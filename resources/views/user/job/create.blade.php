@extends('user.layouts.master')
@section('title')
    Job - {{ env('APP_NAME') }}
@endsection
@push('styles')

@endpush
@section('content')
    <div class="container-fluid">
        <div class="bg_white_border">

            <!--  Row 1 -->
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('jobs.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="heading_box mb-5">
                                    <h3>Job Details</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="name"> Job Title* </label>
                                    <input type="text" name="job_title" id="job_title" class="form-control"
                                        value="{{ old('job_title') }}" placeholder="Enter Job Title">
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
                                        value="{{ old('job_type') }}" placeholder="Enter Job Type">
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
                                        value="{{ old('job_location') }}" placeholder="Enter Job Location">
                                    @if ($errors->has('job_location'))
                                        <span class="text-danger"
                                            style="color:red !important;">{{ $errors->first('job_location') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- job_experience --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="job_experience"> Job Experience (Year) </label>
                                    <input type="number" name="job_experience" id="job_experience" class="form-control"
                                        value="{{ old('job_experience') }}" placeholder="Enter Job Experience">
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
                                            <option value="$" @if (old('currency') == '$') selected @endif>USD ($)
                                            </option>
                                            <option value="€" @if (old('currency') == '€') selected @endif>EUR (€)
                                            </option>
                                            <option value="£" @if (old('currency') == '£') selected @endif>GBP (£)
                                            </option>
                                            <option value="₹" @if (old('currency') == '₹') selected @endif>INR (₹)
                                            </option>
                                            <option value="A$" @if (old('currency') == 'A$') selected @endif>AUD
                                                (A$)</option>
                                            <option value="C$" @if (old('currency') == 'C$') selected @endif>CAD
                                                (C$)</option>
                                            <option value="S$" @if (old('currency') == 'S$') selected @endif>SGD
                                                (S$)</option>
                                            <option value="د.إ" @if (old('currency') == 'د.إ') selected @endif>AED
                                                (د.إ)</option>
                                            <option value="﷼" @if (old('currency') == '﷼') selected @endif>SAR
                                                (﷼)</option>
                                            <option value="QR" @if (old('currency') == 'QR') selected @endif>QAR
                                                (QR)</option>
                                            <option value="ر.ع." @if (old('currency') == 'ر.ع.') selected @endif>OMR
                                                (ر.ع.)</option>
                                            <option value="د.ك" @if (old('currency') == 'د.ك') selected @endif>KWD
                                                (د.ك)</option>
                                            <option value="BD" @if (old('currency') == 'BD') selected @endif>BHD
                                                (BD)</option>
                                            <option value="¥" @if (old('currency') == '¥') selected @endif>JPY
                                                (¥)</option>
                                            <option value="¥" @if (old('currency') == '¥') selected @endif>CNY
                                                (¥)</option>
                                            <option value="RM" @if (old('currency') == 'RM') selected @endif>MYR
                                                (RM)</option>
                                            <option value="฿" @if (old('currency') == '฿') selected @endif>THB
                                                (฿)</option>
                                            <option value="Rp" @if (old('currency') == 'Rp') selected @endif>IDR
                                                (Rp)</option>
                                            <option value="₱" @if (old('currency') == '₱') selected @endif>PHP
                                                (₱)</option>
                                            <option value="₫" @if (old('currency') == '₫') selected @endif>VND
                                                (₫)</option>
                                            <option value="₨" @if (old('currency') == '₨') selected @endif>PKR
                                                (₨)</option>
                                            <option value="৳" @if (old('currency') == '৳') selected @endif>BDT
                                                (৳)</option>
                                            <option value="₨" @if (old('currency') == '₨') selected @endif>LKR
                                                (₨)</option>
                                            <option value="₨" @if (old('currency') == '₨') selected @endif>NPR
                                                (₨)</option>
                                            <option value="Rf" @if (old('currency') == 'Rf') selected @endif>MVR
                                                (Rf)</option>
                                            <option value="₨" @if (old('currency') == '₨') selected @endif>MUR
                                                (₨)</option>
                                            <option value="R" @if (old('currency') == 'R') selected @endif>ZAR
                                                (R)</option>
                                            <option value="₦" @if (old('currency') == '₦') selected @endif>NGN
                                                (₦)</option>
                                            <option value="KSh" @if (old('currency') == 'KSh') selected @endif>KES
                                                (KSh)</option>
                                            <option value="GH₵" @if (old('currency') == 'GH₵') selected @endif>GHS
                                                (GH₵)</option>
                                            <option value="E£" @if (old('currency') == 'E£') selected @endif>EGP
                                                (E£)</option>
                                            <option value="USh" @if (old('currency') == 'USh') selected @endif>UGX
                                                (USh)</option>
                                            <option value="TSh" @if (old('currency') == 'TSh') selected @endif>TZS
                                                (TSh)</option>
                                            <option value="ZK" @if (old('currency') == 'ZK') selected @endif>ZMW
                                                (ZK)</option>
                                        </select>
                                        <input type="text" name="job_salary" id="job_salary" class="form-control"
                                            value="{{ old('job_salary') }}" placeholder="Enter Job Salary">
                                    </div>
                                    @if ($errors->has('job_salary'))
                                        <span class="text-danger"
                                            style="color:red !important;">{{ $errors->first('job_salary') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- list_of_values --}}
                            <div class="col-md-6 mb-2">
                                <div class="box_label">
                                    <label for="list_of_values"> List of Values </label>
                                    <select name="list_of_values" id="list_of_values" class="form-control">
                                        <option value="">Select List of Values</option>
                                        <option value="Hourly" @if (old('list_of_values') == 'Hourly') selected @endif>Hourly
                                        </option>
                                        <option value="Weekly" @if (old('list_of_values') == 'Weekly') selected @endif>Weekly
                                        </option>
                                        <option value="Monthly" @if (old('list_of_values') == 'Monthly') selected @endif>Monthly
                                        </option>
                                        <option value="Annually" @if (old('list_of_values') == 'Annually') selected @endif>
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
                                        value="{{ old('contact_person') }}" placeholder="Enter Contact Person">
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
                                        value="{{ old('contact_email') }}" placeholder="Enter Contact Email">
                                    @if ($errors->has('contact_email'))
                                        <span class="text-danger"
                                            style="color:red !important;">{{ $errors->first('contact_email') }}</span>
                                    @endif
                                </div>
                            </div>
                            {{-- job_description --}}
                            <div class="col-md-12 mb-2">
                                <div class="box_label">
                                    <label for="description"> Job Description* </label>
                                    <textarea name="job_description" id="description" class="form-control" placeholder="Enter Job Description">{{ old('job_description') }}</textarea>
                                    @if ($errors->has('job_description'))
                                        <span class="text-danger"
                                            style="color:red !important;">{{ $errors->first('job_description') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="w-100 text-end d-flex align-items-center justify-content-end mt-3">
                                <button type="submit" class="print_btn me-2">Add</button>
                                <a href="{{ route('jobs.index') }}" class="print_btn print_btn_vv">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script src='https://cdn.ckeditor.com/ckeditor5/28.0.0/classic/ckeditor.js'></script>

        <script>
            // ClassicEditor.create(document.querySelector("#description"));
            // remove heading1, heading2, heading3, heading4, heading5, heading6 from toolbar
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
