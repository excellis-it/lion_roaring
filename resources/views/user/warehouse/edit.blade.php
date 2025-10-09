@extends('user.layouts.master')

@section('title')
    E-Store Warehouse Management
@endsection

@push('styles')
    <!-- bootstrap-select (modern) -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/css/bootstrap-select.min.css" />
    <style>
        /* map sizing */
        .text-danger {
            color: red !important;
            font-size: 0.875em !important;
        }

        .is-invalid {
            border-color: red !important;
        }

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
            color: rgb(83, 83, 83);
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
                    <h4 class="title mb-5">Edit Warehouse</h4>
                    <form action="{{ route('ware-houses.update', $wareHouse->id) }}" method="POST" id="edit-warehouse-form">
                        @csrf
                        @method('PUT')
                        <div class="row">

                            {{-- Warehouse Name --}}
                            <div class="col-md-12 mb-2">
                                <div class="box_label">
                                    <label for="name">Warehouse Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $wareHouse->name) }}" >
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Latitude --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="latitude">Latitude<span class="text-danger">*</span></label>
                                    <input type="text" name="location_lat" id="latitude"
                                        class="form-control @error('location_lat') is-invalid @enderror"
                                        value="{{ old('location_lat', $wareHouse->location_lat) }}">
                                    @error('location_lat')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Longitude --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="longitude">Longitude<span class="text-danger">*</span></label>
                                    <input type="text" name="location_lng" id="longitude"
                                        class="form-control @error('location_lng') is-invalid @enderror"
                                        value="{{ old('location_lng', $wareHouse->location_lng) }}">
                                    @error('location_lng')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Warehouse Address --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="address">Warehouse Address<span class="text-danger">*</span></label>
                                    <input type="text" name="address" id="address"
                                        class="form-control @error('address') is-invalid @enderror"
                                        value="{{ old('address', $wareHouse->address) }}"
                                        placeholder="Enter address to search location">
                                    @error('address')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Map --}}
                            <div class="col-md-12 mb-2">
                                <div id="map"></div>
                            </div>

                            {{-- Country --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="country">Warehouse Country<span class="text-danger">*</span></label>
                                    <select name="country_id" id="country"
                                        class="form-control @error('country_id') is-invalid @enderror">
                                        <option value="">Select Country</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}"
                                                {{ (int) old('country_id', $wareHouse->country_id) === $country->id ? 'selected' : '' }}>
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('country_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Service Range --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="service_range">Warehouse Service Range (In Miles)<span class="text-danger">*</span></label>
                                    <input type="number" step="any" placeholder="Ex: 10.5" name="service_range"
                                        id="service_range" class="form-control @error('service_range') is-invalid @enderror"
                                        value="{{ old('service_range', $wareHouse->service_range) }}">
                                    @error('service_range')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Assign User --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="assign_user">Assign User</label>
                                    <select name="assign_user[]" id="assign_user"
                                        class="selectpicker @error('assign_user') is-invalid @enderror"
                                        data-live-search="true" data-width="100%" data-size="10" multiple>
                                        @foreach ($all_users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ in_array($user->id, old('assign_user', $assignedUserIds ?? [])) ? 'selected' : '' }}>
                                                {{ $user->full_name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assign_user')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Status --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="status">Status</label>
                                    <select name="is_active" id="status"
                                        class="form-control @error('is_active') is-invalid @enderror">
                                        <option value="1"
                                            {{ old('is_active', $wareHouse->is_active) == 1 ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="0"
                                            {{ old('is_active', $wareHouse->is_active) == 0 ? 'selected' : '' }}>Inactive
                                        </option>
                                    </select>
                                    @error('is_active')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        {{-- Buttons --}}
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
    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places"></script>
    <!-- bootstrap-select (modern) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/js/bootstrap-select.min.js"></script>

    <script>
        $(document).ready(function() {
            if ($.fn.selectpicker) {
                $('.selectpicker').selectpicker();
            }

            $("#create-warehouse-form").on("submit", function(e) {
                $('#loading').addClass('loading');
                $('#loading-content').addClass('loading-content');
            });

            // Google Maps initialization with existing warehouse data
            let map, marker, geocoder, autocomplete;
            const $lat = $('#latitude');
            const $lng = $('#longitude');
            const $address = $('#address');
            const $country = $('#country');

            // Build country lookup
            const countryMap = {};
            $country.find('option').each(function() {
                const txt = $(this).text().trim().toLowerCase();
                if (txt) countryMap[txt] = $(this).val();
            });

            function selectCountryByName(name) {
                if (!name) return;
                const n = name.trim().toLowerCase();

                if (countryMap[n]) {
                    $country.val(countryMap[n]).trigger('change');
                    return;
                }

                for (let key in countryMap) {
                    if (key.indexOf(n) !== -1 || n.indexOf(key) !== -1) {
                        $country.val(countryMap[key]).trigger('change');
                        return;
                    }
                }
            }

            function initMap() {
                // Use existing warehouse coordinates or default
                const defaultLat = parseFloat(
                    '{{ old('location_lat', $wareHouse->location_lat) ?? '40.712776' }}');
                const defaultLng = parseFloat(
                    '{{ old('location_lng', $wareHouse->location_lng) ?? '-74.005974' }}');

                map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 13,
                    center: {
                        lat: defaultLat,
                        lng: defaultLng
                    }
                });

                geocoder = new google.maps.Geocoder();

                marker = new google.maps.Marker({
                    position: {
                        lat: defaultLat,
                        lng: defaultLng
                    },
                    map: map,
                    draggable: true
                });

                marker.addListener('dragend', function() {
                    const position = marker.getPosition();
                    updateInputs(position.lat(), position.lng());
                    reverseGeocode(position.lat(), position.lng());
                });

                map.addListener('click', function(event) {
                    const lat = event.latLng.lat();
                    const lng = event.latLng.lng();
                    marker.setPosition(event.latLng);
                    updateInputs(lat, lng);
                    reverseGeocode(lat, lng);
                });

                // Initialize Places Autocomplete
                autocomplete = new google.maps.places.Autocomplete($address[0], {
                    fields: ['place_id', 'geometry', 'name', 'formatted_address', 'address_components']
                });
                autocomplete.bindTo('bounds', map);

                // Handle place selection from autocomplete
                autocomplete.addListener('place_changed', function() {
                    const place = autocomplete.getPlace();

                    if (!place.geometry || !place.geometry.location) {
                        console.log("No details available for input: '" + place.name + "'");
                        return;
                    }

                    // Update map and marker
                    const location = place.geometry.location;
                    const lat = location.lat();
                    const lng = location.lng();

                    map.setCenter(location);
                    map.setZoom(15);
                    marker.setPosition(location);

                    // Update inputs
                    updateInputs(lat, lng);

                    // Update address if formatted_address is available
                    if (place.formatted_address) {
                        $address.val(place.formatted_address);
                    }

                    // Extract and set country
                    if (place.address_components) {
                        const countryComponent = place.address_components.find(component =>
                            component.types.includes('country')
                        );

                        if (countryComponent) {
                            selectCountryByName(countryComponent.long_name);
                        }
                    }
                });
            }

            function updateInputs(lat, lng) {
                $lat.val(lat.toFixed(6));
                $lng.val(lng.toFixed(6));
            }

            function reverseGeocode(lat, lng) {
                const latlng = {
                    lat: lat,
                    lng: lng
                };

                geocoder.geocode({
                    location: latlng
                }, (results, status) => {
                    if (status === 'OK' && results[0]) {
                        $address.val(results[0].formatted_address);

                        const addressComponents = results[0].address_components;
                        const countryComponent = addressComponents.find(component =>
                            component.types.includes('country')
                        );

                        if (countryComponent) {
                            selectCountryByName(countryComponent.long_name);
                        }
                    }
                });
            }

            function geocodeAddress(address) {
                if (!address.trim()) return;

                geocoder.geocode({
                    address: address
                }, (results, status) => {
                    if (status === 'OK' && results[0]) {
                        const location = results[0].geometry.location;
                        const lat = location.lat();
                        const lng = location.lng();

                        map.setCenter(location);
                        map.setZoom(15);
                        marker.setPosition(location);
                        updateInputs(lat, lng);

                        const addressComponents = results[0].address_components;
                        const countryComponent = addressComponents.find(component =>
                            component.types.includes('country')
                        );

                        if (countryComponent) {
                            selectCountryByName(countryComponent.long_name);
                        }
                    }
                });
            }

            // Address input keyup event with debounce (for manual typing)
            let addressTimeout;
            $address.on('keyup', function(e) {
                // Don't trigger geocoding if user is selecting from autocomplete
                if (e.key === 'ArrowDown' || e.key === 'ArrowUp' || e.key === 'Enter') {
                    return;
                }

                clearTimeout(addressTimeout);
                const address = $(this).val();

                addressTimeout = setTimeout(function() {
                    if (address.length > 3) {
                        geocodeAddress(address);
                    }
                }, 1000);
            });

            $lat.add($lng).on('change', function() {
                const lat = parseFloat($lat.val());
                const lng = parseFloat($lng.val());

                if (!isNaN(lat) && !isNaN(lng)) {
                    const position = {
                        lat: lat,
                        lng: lng
                    };
                    marker.setPosition(position);
                    map.setCenter(position);
                    reverseGeocode(lat, lng);
                }
            });

            initMap();
        });
    </script>
@endpush
