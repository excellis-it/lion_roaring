@php
    /** @var \Knuckles\Camel\Output\OutputEndpointData $endpoint */

    // Helper: map PHP/scalar to Dart types
    $mapType = function ($val) use (&$mapType, &$generatedClasses, &$classDefs) {
        if (is_int($val)) {
            return 'int';
        }
        if (is_float($val)) {
            return 'double';
        }
        if (is_bool($val)) {
            return 'bool';
        }
        if (is_string($val)) {
            return 'String';
        }
        if (is_null($val)) {
            return 'dynamic';
        }
        if (is_array($val)) {
            // associative -> object, indexed -> list
            $isAssoc = array_keys($val) !== range(0, count($val) - 1);
            if (!$isAssoc) {
                // List: infer type from first element if exists
                if (count($val) === 0) {
                    return 'List<dynamic>';
                }
                return 'List<' . $mapType($val[0]) . '>';
            } else {
                // object -> class
                return 'Map<String, dynamic>'; // fallback; actual class created elsewhere
            }
        }
        return 'dynamic';
    };

    // Recursively generate Dart classes from PHP array/object
    $classDefs = [];
    $generatedClasses = [];

    $generateClass = function (string $className, $data) use (
        &$generateClass,
        &$mapType,
        &$classDefs,
        &$generatedClasses,
    ) {
        if (isset($generatedClasses[$className])) {
            return;
        }
        $generatedClasses[$className] = true;

        $props = [];
        if (is_array($data)) {
            // associative array => object
            $isAssoc = array_keys($data) !== range(0, count($data) - 1);
            if (!$isAssoc) {
                // shouldn't happen here
            $classDefs[$className] = "// $className is a list, no class generated.";
            return;
        }

        foreach ($data as $key => $value) {
            $propName = $key;
            $type = null;
            if (is_array($value)) {
                $isAssocChild = array_keys($value) !== range(0, count($value) - 1);
                if ($isAssocChild) {
                    // nested object -> create nested class
                    $childClass = ucfirst(camel_case($propName));
                    $generateClass($childClass, $value);
                    $type = $childClass;
                } else {
                    // list: element type inference
                    if (count($value) === 0) {
                        $type = 'List<dynamic>';
                    } else {
                        $first = $value[0];
                        if (is_array($first) && array_keys($first) !== range(0, count($first) - 1)) {
                            $childClass = ucfirst(camel_case($propName)) . 'Item';
                            $generateClass($childClass, $first);
                            $type = "List<$childClass>";
                        } else {
                            $type = 'List<' . $mapType($first) . '>';
                        }
                    }
                }
            } else {
                $type = $mapType($value);
            }
            $props[$propName] = $type;
        }
    }

    // Build Dart class string
    $buf = "class $className {\n";
    // fields
    foreach ($props as $pname => $ptype) {
        $buf .= "  final $ptype? $pname;\n";
    }
    $buf .= "\n  $className({";
    foreach ($props as $pname => $_) {
        $buf .= "this.$pname, ";
    }
    $buf .= "});\n\n";

    // fromJson
    $buf .= "  factory $className.fromJson(Map<String, dynamic> json) => $className(\n";
    foreach ($props as $pname => $ptype) {
        if (str_starts_with($ptype, 'List<') && preg_match('/List<(.+)>/', $ptype, $m)) {
            $elemType = $m[1];
            if ($elemType === 'dynamic' || in_array($elemType, ['int', 'double', 'String', 'bool'])) {
                $buf .= "    $pname: List<$elemType>.from(json['$pname'] ?? []),\n";
            } else {
                $buf .= "    $pname: (json['$pname'] as List<dynamic>?)?.map((e) => $elemType.fromJson(e as Map<String, dynamic>)).toList(),\n";
            }
        } elseif (in_array($ptype, ['int', 'double', 'String', 'bool', 'dynamic', 'Map<String, dynamic>'])) {
            $buf .= "    $pname: json['$pname'],\n";
        } else {
            // nested object
            $buf .= "    $pname: json['$pname'] != null ? $ptype.fromJson(json['$pname'] as Map<String, dynamic>) : null,\n";
        }
    }
    $buf .= "  );\n\n";

    // toJson
    $buf .= "  Map<String, dynamic> toJson() => {\n";
    foreach ($props as $pname => $ptype) {
        if (str_starts_with($ptype, 'List<') && preg_match('/List<(.+)>/', $ptype, $m)) {
            $elemType = $m[1];
            if ($elemType === 'dynamic' || in_array($elemType, ['int', 'double', 'String', 'bool'])) {
                $buf .= "    '$pname': $pname,\n";
            } else {
                $buf .= "    '$pname': $pname?.map((e) => e.toJson()).toList(),\n";
            }
        } elseif (in_array($ptype, ['int', 'double', 'String', 'bool', 'dynamic', 'Map<String, dynamic>'])) {
            $buf .= "    '$pname': $pname,\n";
        } else {
            $buf .= "    '$pname': $pname?.toJson(),\n";
        }
    }
    $buf .= "  };\n";

    $buf .= "}\n";
    $classDefs[$className] = $buf;
};

// Utility to create CamelCase from snake/camel
if (!function_exists('camel_case')) {
    function camel_case($str)
    {
        // Use Laravel's Str::studly for more robust studly/camel casing
            return \Illuminate\Support\Str::studly($str);
        }
    }

    // Decide source JSON:
    // Prefer example response (2xx) if present, else cleanBodyParameters (request body), else empty
    $jsonData = null;

    // Try example responses (Scribe usually exposes sample responses through endpoint->responses or endpoint->exampleResponses)
    if (!empty($endpoint->responses ?? [])) {
        // try to pick first 2xx JSON response
        foreach ($endpoint->responses as $resp) {
            if (
                isset($resp['status']) &&
                intval($resp['status']) >= 200 &&
                intval($resp['status']) < 300 &&
                !empty($resp['content'])
            ) {
                $decoded = @json_decode($resp['content'], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $jsonData = $decoded;
                    break;
                }
            }
        }
    }

    // Fallback: cleanBodyParameters -> convert to a sample array
    if (is_null($jsonData) && !empty($endpoint->cleanBodyParameters)) {
        $sample = [];
        foreach ($endpoint->cleanBodyParameters as $k => $v) {
            $sample[$k] = $v['example'] ?? ($v['value'] ?? null);
        }
        $jsonData = $sample;
    }

    // If still null, try first response from exampleResponses (older Scribe versions)
    if (is_null($jsonData) && !empty($endpoint->exampleResponses ?? [])) {
        foreach ($endpoint->exampleResponses as $ex) {
            if (!empty($ex->content)) {
                $decoded = @json_decode($ex->content, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $jsonData = $decoded;
                    break;
                }
            }
        }
    }

    if (is_null($jsonData)) {
        $jsonData = new \stdClass(); // empty
    }

    // Build classes: root model named using endpoint's boundUri or 'AutoModel'
$rootName = 'AutoModel';
if (!empty($endpoint->boundUri)) {
    // sanitize and transform to CamelCase
    $parts = preg_split('/[\/{}\-:_]+/', $endpoint->boundUri);
    $parts = array_filter($parts);
    if (count($parts)) {
        $rootName = ucfirst(camel_case(end($parts)));
    }
}

// If top-level is a list, use Item as root element
if (is_array($jsonData) && array_keys($jsonData) === range(0, count($jsonData) - 1)) {
    // list -> pick first element to build class
    $first = $jsonData[0] ?? [];
    $generateClass($rootName . 'Item', $first);
    // also provide wrapper type List<RootItem>
    $classOutput = "/* Root is a List<$rootName" . "Item> */\n";
    // append class defs
} else {
    $generateClass($rootName, $jsonData);
    $classOutput = '';
    }

    // Collect classes in reverse order (nested classes first)
    $result = $classOutput . implode("\n\n", array_reverse($classDefs));
@endphp

```dart
{!! $result !!}
