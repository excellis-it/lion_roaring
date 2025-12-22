@extends('user.layouts.master')
@section('title')
    Job Edit - {{ env('APP_NAME') }}
@endsection
@push('styles')
@endpush
@section('content')
    <section id="loading">
        <div id="loading-content"></div>
    </section>
    <div class="container-fluid">
        <div class="bg_white_border">

            <!--  Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('jobs.update', $job->id) }}" method="POST" enctype="multipart/form-data"
                        id="uploadForm">
                        @method('PUT')
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Job Details</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                              @if (auth()->user()->user_type == 'Global')
                                <div class="col-md-6 mb-2">
                                    <div class="box_label">
                                        <label for="country_id">Country*</label>

                                        <select name="country_id" id="country_id" class="form-control">
                                            <option value="">Select Country</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}"
                                                    {{ $job->country_id == $country->id ? 'selected' : '' }}>
                                                    {{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('country_id'))
                                            <span class="error">{{ $errors->first('country_id') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            {{-- job_title --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="name"> Job Title* </label>
                                    <input type="text" name="job_title" id="job_title" class="form-control"
                                        placeholder="Enter Job Title" value="{{ $job->job_title }}">
                                    @if ($errors->has('job_title'))
                                        <span class="text-danger"
                                            style="color:red !important;">{{ $errors->first('job_title') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- job_type --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="job_type"> Job Type* </label>
                                    <input type="text" name="job_type" id="job_type" class="form-control"
                                        placeholder="Enter Job Type" value="{{ $job->job_type }}">
                                    @if ($errors->has('job_type'))
                                        <span class="text-danger"
                                            style="color:red !important;">{{ $errors->first('job_type') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- job_location --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="job_location"> Job Location* </label>
                                    <input type="text" name="job_location" id="job_location" class="form-control"
                                        placeholder="Enter Job Location" value="{{ $job->job_location }}">
                                    @if ($errors->has('job_location'))
                                        <span class="text-danger"
                                            style="color:red !important;">{{ $errors->first('job_location') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- job_experience --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="job_experience"> Job Experience </label>
                                    <input type="text" name="job_experience" id="job_experience" class="form-control"
                                        placeholder="Enter Job Experience" value="{{ $job->job_experience }}">
                                    @if ($errors->has('job_experience'))
                                        <span class="text-danger"
                                            style="color:red !important;">{{ $errors->first('job_experience') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- job_salary --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="job_salary">Job Salary</label>
                                    <div class="input-group">
                                        <select name="currency" id="currency" class="form-control"
                                            style="max-width: 100px;">
                                            <option value="$" @if ($job->currency == '$') selected @endif>USD ($)
                                            </option>
                                            <option value="€" @if ($job->currency == '€') selected @endif>EUR (€)
                                            </option>
                                            <option value="£" @if ($job->currency == '£') selected @endif>GBP (£)
                                            </option>
                                            <option value="₹" @if ($job->currency == '₹') selected @endif>INR (₹)
                                            </option>
                                            <option value="A$" @if ($job->currency == 'A$') selected @endif>AUD
                                                (A$)</option>
                                            <option value="C$" @if ($job->currency == 'C$') selected @endif>CAD
                                                (C$)</option>
                                            <option value="S$" @if ($job->currency == 'S$') selected @endif>SGD
                                                (S$)</option>
                                            <option value="د.إ" @if ($job->currency == 'د.إ') selected @endif>AED
                                                (د.إ)</option>
                                            <option value="﷼" @if ($job->currency == '﷼') selected @endif>SAR
                                                (﷼)</option>
                                            <option value="QR" @if ($job->currency == 'QR') selected @endif>QAR
                                                (QR)</option>
                                            <option value="ر.ع." @if ($job->currency == 'ر.ع.') selected @endif>OMR
                                                (ر.ع.)</option>
                                            <option value="د.ك" @if ($job->currency == 'د.ك') selected @endif>KWD
                                                (د.ك)</option>
                                            <option value="BD" @if ($job->currency == 'BD') selected @endif>BHD
                                                (BD)</option>
                                            <option value="¥" @if ($job->currency == '¥') selected @endif>JPY
                                                (¥)</option>
                                            <option value="¥" @if ($job->currency == '¥') selected @endif>CNY
                                                (¥)</option>
                                            <option value="RM" @if ($job->currency == 'RM') selected @endif>MYR
                                                (RM)</option>
                                            <option value="฿" @if ($job->currency == '฿') selected @endif>THB
                                                (฿)</option>
                                            <option value="Rp" @if ($job->currency == 'Rp') selected @endif>IDR
                                                (Rp)</option>
                                            <option value="₱" @if ($job->currency == '₱') selected @endif>PHP
                                                (₱)</option>
                                            <option value="₫" @if ($job->currency == '₫') selected @endif>VND
                                                (₫)</option>
                                            <option value="₨" @if ($job->currency == '₨') selected @endif>PKR
                                                (₨)</option>
                                            <option value="৳" @if ($job->currency == '৳') selected @endif>BDT
                                                (৳)</option>
                                            <option value="₨" @if ($job->currency == '₨') selected @endif>LKR
                                                (₨)</option>
                                            <option value="₨" @if ($job->currency == '₨') selected @endif>NPR
                                                (₨)</option>
                                            <option value="Rf" @if ($job->currency == 'Rf') selected @endif>MVR
                                                (Rf)</option>
                                            <option value="₨" @if ($job->currency == '₨') selected @endif>MUR
                                                (₨)</option>
                                            <option value="R" @if ($job->currency == 'R') selected @endif>ZAR
                                                (R)</option>
                                            <option value="₦" @if ($job->currency == '₦') selected @endif>NGN
                                                (₦)</option>
                                            <option value="KSh" @if ($job->currency == 'KSh') selected @endif>KES
                                                (KSh)</option>
                                            <option value="GH₵" @if ($job->currency == 'GH₵') selected @endif>GHS
                                                (GH₵)</option>
                                            <option value="E£" @if ($job->currency == 'E£') selected @endif>EGP
                                                (E£)</option>
                                            <option value="USh" @if ($job->currency == 'USh') selected @endif>UGX
                                                (USh)</option>
                                            <option value="TSh" @if ($job->currency == 'TSh') selected @endif>TZS
                                                (TSh)</option>
                                            <option value="ZK" @if ($job->currency == 'ZK') selected @endif>ZMW
                                                (ZK)</option>
                                        </select>
                                        <input type="text" name="job_salary" id="job_salary" class="form-control"
                                            placeholder="Enter Job Salary" value="{{ $job->job_salary }}">
                                    </div>
                                    @if ($errors->has('currency'))
                                        <span class="text-danger">{{ $errors->first('currency') }}</span>
                                    @endif
                                    @if ($errors->has('job_salary'))
                                        <span class="text-danger">{{ $errors->first('job_salary') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- list_of_values --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="list_of_values"> List of Values </label>
                                    <select name="list_of_values" id="list_of_values" class="form-control">
                                        <option value="">Select List of Values</option>
                                        <option value="Hourly" @if ($job->list_of_values == 'Hourly') selected @endif>Hourly
                                        </option>
                                        <option value="Weekly" @if ($job->list_of_values == 'Weekly') selected @endif>Weekly
                                        </option>
                                        <option value="Monthly" @if ($job->list_of_values == 'Monthly') selected @endif>Monthly
                                        </option>
                                        <option value="Annually" @if ($job->list_of_values == 'Annually') selected @endif>
                                            Annually</option>
                                    </select>
                                    @if ($errors->has('list_of_values'))
                                        <span class="text-danger"
                                            style="color:red !important;">{{ $errors->first('list_of_values') }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- contact_person --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="contact_person"> Contact Person </label>
                                    <input type="text" name="contact_person" id="contact_person" class="form-control"
                                        placeholder="Enter Contact Person" value="{{ $job->contact_person }}">
                                    @if ($errors->has('contact_person'))
                                        <span class="text-danger"
                                            style="color:red !important;">{{ $errors->first('contact_person') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- contact_email --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="contact_email"> Contact Email </label>
                                    <input type="email" name="contact_email" id="contact_email" class="form-control"
                                        placeholder="Enter Contact Email" value="{{ $job->contact_email }}">
                                    @if ($errors->has('contact_email'))
                                        <span class="text-danger"
                                            style="color:red !important;">{{ $errors->first('contact_email') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- job_description --}}
                            <div class="col-md-12 mb-2">
                                <div class="box_label">
                                    <label for="description"> Job Description </label>
                                    <textarea name="job_description" id="description" class="form-control" placeholder="Enter Job Description">{{ $job->job_description }}</textarea>
                                    @if ($errors->has('job_description'))
                                        <span class="text-danger"
                                            style="color:red !important;">{{ $errors->first('job_description') }}</span>
                                    @endif
                                </div>
                            </div>

                        </div>
                        <div class="row">

                            <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="print_btn me-2">Update</button>
                                <a href="{{ route('jobs.index') }}" class="print_btn print_btn_vv">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            $(document).ready(function() {
                $("#uploadForm").on("submit", function(e) {
                    // e.preventDefault();
                    $('#loading').addClass('loading');
                    $('#loading-content').addClass('loading-content');
                });
            });
        </script>
        <script src='https://cdn.ckeditor.com/ckeditor5/28.0.0/classic/ckeditor.js'></script>

        <script>
            ClassicEditor.create(document.querySelector("#description"), {
                toolbar: {
                    items: [
                        'bold',
                        'italic',
                        'underline',
                        'link',
                        'bulletedList',
                        'numberedList',
                        'blockQuote',
                        'undo',
                        'redo'
                    ]
                },
            });
        </script>
    @endpush
