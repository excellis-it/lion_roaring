@extends('user.layouts.master')

@section('title')
    E-Store Warehouse Management
@endsection

@push('styles')
    <!-- Leaflet + Geocoder CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <!-- bootstrap-select (modern) -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/css/bootstrap-select.min.css" />


    <style>
        /* small map sizing */
        #map {
            height: 320px;
            width: 100%;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
        }

        .dropdown-menu.show {
            z-index: 999999;
        }

        .bootstrap-select>.dropdown-toggle.bs-placeholder {
            border: 1px solid #ced4da;
            color: rgb(55, 54, 54);
        }

        .bootstrap-select .dropdown-menu li {
            padding: 5px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <div class="row">
                <div class="col-md-12">
                    <h4 class="title mb-5">Create New Warehouse</h4>
                    <form action="{{ route('ware-houses.store') }}" method="POST" id="create-warehouse-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <div class="box_label">
                                    <label for="name">Warehouse Name</label>
                                    <input type="text" name="name" id="name" class="form-control">

                                </div>

                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="latitude">Latitude</label>
                                    <input type="text" name="location_lat" id="latitude" class="form-control">

                                </div>

                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="name">Longitude</label>
                                    <input type="text" name="location_lng" id="longitude" class="form-control">

                                </div>

                            </div>



                            <div class="col-md-12 mb-2">
                                <div class="box_label">
                                    <label for="name">Warehouse Location</label>


                                </div>

                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="address">Warehouse Address</label>
                                    <input type="text" name="address" id="address" class="form-control">

                                </div>

                            </div>

                            <!-- Map container -->
                            <div class="col-md-12 mb-2">
                                <div id="map"></div>
                            </div>




                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="country">Warehouse Country</label>
                                    <select name="country_id" id="country" class="form-control">
                                        <option value="">Select Country</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>

                                </div>

                            </div>



                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="service_range">Warehouse Service Range (In kilometers)</label>
                                    <input type="number" step="0.1" placeholder="Ex: 10.5" name="service_range"
                                        id="service_range" class="form-control">

                                </div>

                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="assign_user">Assign User</label>
                                    <select name="assign_user[]" id="assign_user" class="selectpicker"
                                        data-live-search="true" data-width="100%" data-size="10" multiple>
                                        <option value="">Select User</option>
                                        @foreach ($all_users as $user)
                                            <option value="{{ $user->id }}">
                                                {{ $user->full_name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>

                                </div>

                            </div>


                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="status">Status</label>
                                    <select name="is_active" id="status" class="form-control">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>

                                </div>

                            </div>



                        </div>



                        <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                            <button type="submit" class="print_btn me-2">Save</button>
                            <a href="{{ route('ware-houses.index') }}" class="print_btn print_btn_vv">Cancel</a>
                        </div>
                    </form>
                </div>

            </div>



        </div>
    </div>
@endsection


@push('scripts')
    <!-- Leaflet + Geocoder JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <!-- bootstrap-select (modern) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/js/bootstrap-select.min.js"></script>


    <script>
        $(document).ready(function() {

            if ($.fn.selectpicker) {
                $('.selectpicker').selectpicker(); // activate plugin on selects with .selectpicker
            }
            // existing loader behaviour
            $("#create-warehouse-form").on("submit", function(e) {
                // e.preventDefault();
                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');
            });

            // Map + geocoding logic
            var $lat = $('#latitude'),
                $lng = $('#longitude'),
                $address = $('#address'),
                $country = $('#country');

            // build quick lookup from option text -> value
            var countryMap = {};
            $country.find('option').each(function() {
                var txt = $(this).text().trim().toLowerCase();
                if (txt) countryMap[txt] = $(this).val();
            });

            function selectCountryByName(name) {
                if (!name) return;
                var n = name.trim().toLowerCase();
                // direct match
                if (countryMap[n]) {
                    $country.val(countryMap[n]).trigger('change');
                    return;
                }
                // try partial matches (e.g. "comoros" in "Union of the Comoros")
                for (var key in countryMap) {
                    if (key.indexOf(n) !== -1 || n.indexOf(key) !== -1) {
                        $country.val(countryMap[key]).trigger('change');
                        return;
                    }
                }
                // fallback: take last token if comma-separated display_name -> often country at the end
                var parts = name.split(',');
                if (parts.length) {
                    var last = parts[parts.length - 1].trim().toLowerCase();
                    if (countryMap[last]) {
                        $country.val(countryMap[last]).trigger('change');
                        return;
                    }
                }
            }

            // default center (New York) — override if inputs already have values
            var defaultLat = 40.712776,
                defaultLng = -74.005974;
            var initLat = parseFloat($lat.val()) || defaultLat;
            var initLng = parseFloat($lng.val()) || defaultLng;

            var map = L.map('map').setView([initLat, initLng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors',
                // dark mode
                className: 'leaflet-dark'
            }).addTo(map);

            var marker = L.marker([initLat, initLng], {
                draggable: true
            }).addTo(map);

            function setInputs(lat, lng) {
                $lat.val(lat.toFixed(6));
                $lng.val(lng.toFixed(6));
            }

            function reverseGeocode(lat, lng) {
                // Nominatim reverse geocode
                fetch('https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + lat + '&lon=' + lng, {
                    headers: {
                        'Accept': 'application/json'
                    }
                }).then(function(res) {
                    return res.json();
                }).then(function(data) {
                    if (data && data.display_name) {
                        $address.val(data.display_name);
                    }
                    // if Nominatim returned structured address, extract country
                    if (data && data.address && data.address.country) {
                        selectCountryByName(data.address.country);
                    }
                }).catch(function() {
                    // ignore errors
                });
            }

            // on marker drag
            marker.on('dragend', function(e) {
                var p = e.target.getLatLng();
                setInputs(p.lat, p.lng);
                reverseGeocode(p.lat, p.lng);
            });

            // click on map to move marker
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                setInputs(e.latlng.lat, e.latlng.lng);
                reverseGeocode(e.latlng.lat, e.latlng.lng);
            });

            // if lat/lng inputs change manually, update marker
            $lat.add($lng).on('change', function() {
                var la = parseFloat($lat.val()),
                    lo = parseFloat($lng.val());
                if (!isNaN(la) && !isNaN(lo)) {
                    marker.setLatLng([la, lo]);
                    map.setView([la, lo], 15);
                    reverseGeocode(la, lo);
                }
            });

            // add search control (uses Nominatim via leaflet-control-geocoder)
            if (L.Control && L.Control.Geocoder) {
                var geocoder = L.Control.geocoder({
                    defaultMarkGeocode: false
                }).on('markgeocode', function(e) {
                    var center = e.geocode.center;
                    map.setView(center, 15);
                    marker.setLatLng(center);
                    setInputs(center.lat, center.lng);
                    // e.geocode.name often contains useful address text
                    if (e.geocode && e.geocode.name) {
                        $address.val(e.geocode.name);
                        // try to pick country from the returned name
                        selectCountryByName(e.geocode.name);
                    } else {
                        reverseGeocode(center.lat, center.lng);
                    }
                }).addTo(map);
            }

            // initial reverse geocode if address empty
            if (!$address.val()) {
                reverseGeocode(initLat, initLng);
            }
        });
    </script>
@endpush
