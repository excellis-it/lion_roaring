@php
    use App\Helpers\Helper;
@endphp
<footer class="footer_sec">
    <div class="container">
        <div class="copyright">
            <div class="row justify-content-between">
                <div class="col-lg-5">
                    <div class="ftr_logo_sec">
                        <div class="d-flex align-items-center">
                            <div>
                                <a class="ftr_logo">
                                    @if (isset(Helper::getFooter()['footer_logo']))
                                        <img src="{{ Storage::url(Helper::getFooter()['footer_logo']) }}" alt="">
                                    @else
                                        <img src="{{ asset('frontend_assets/uploads/2024/02/Group-2029.png') }}"
                                            alt="">
                                    @endif
                                </a>
                            </div>
                            <div>
                                <a class="ftr_logo_right">
                                    @if (isset(Helper::getFooter()['footer_flag']))
                                        <img src="{{ Storage::url(Helper::getFooter()['footer_flag']) }}"
                                            alt="">
                                    @else
                                        <img src="{{ asset('frontend_assets/uploads/2024/02/Group-2029.png') }}"
                                            alt="">
                                    @endif
                                </a>
                            </div>
                        </div>

                        <p>
                            {!! Helper::getFooter()['footer_title'] ??
                                'Our main focus is to restore our various communities, villages, cities, states,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            and
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            our nation by restoring the condition of a person in both the spiritual and the
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            physical.' !!}
                        </p>
                        <div class="col-lg-12">
                            <div class="d-flex align-items-center">
                                <a href="{{ Helper::getFooter()['footer_playstore_link'] ?? 'javascript:void(0);' }}"
                                    class="me-2">
                                    @if (isset(Helper::getFooter()['footer_playstore_link']))
                                        <img src="{{ Storage::url(Helper::getFooter()['footer_playstore_image']) }}"
                                            alt="">
                                    @else
                                        <img src="{{ asset('frontend_assets/uploads/2024/01/playstore.png') }}"
                                            alt="">
                                    @endif
                                </a>
                                <a href="{{ Helper::getFooter()['footer_appstore_link'] ?? 'javascript:void(0);' }}">
                                    @if (isset(Helper::getFooter()['footer_appstore_link']))
                                        <img src="{{ Storage::url(Helper::getFooter()['footer_appstore_image']) }}"
                                            alt="">
                                    @else
                                        <img src="{{ asset('frontend_assets/uploads/2024/01/appstore.png') }}"
                                            alt="">
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="find-us">
                        <h4>{!! Helper::getFooter()['footer_newsletter_title'] ?? 'Don’t miss our newsletter! Get in touch today!' !!}</h4>
                        <div class="ftr-frm">
                            <div class="wpcf7 js" id="wpcf7-f52-o1" lang="en-US" dir="ltr">
                                <div class="screen-reader-response">
                                    <p role="status" aria-live="polite" aria-atomic="true"></p>
                                    <ul></ul>
                                </div>
                                <form action="{{ route('newsletter') }}" method="post" id="submit-newsletter">
                                    @csrf
                                    <div class="row">
                                        <div class="form-group col-lg-6 col-md-12">
                                            <input size="40" class="form-control" id="newsletter_name"
                                                placeholder="Full Name" value="" type="text"
                                                name="newsletter_name">
                                            <span class="text-danger" id="newsletter_name_error"></span>
                                        </div>
                                        <div class="form-group col-lg-6 col-md-12">
                                            <input class="form-control" placeholder="Email ADDRESS"
                                                id="newsletter_email" value="" type="email"
                                                name="newsletter_email">
                                            <span class="text-danger" id="newsletter_email_error"></span>
                                        </div>
                                        <div class="form-group col-12">
                                            <textarea cols="40" rows="10" class="form-control" id="newsletter_message" placeholder="Message"
                                                name="newsletter_message"></textarea>
                                            <span class="text-danger" id="newsletter_message_error"></span>
                                        </div>
                                    </div>
                                    <div class="main-btn">
                                        <input class="red_btn" type="submit" value="Submit">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
    <div class="copy_1">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="quick_links_ul">
                        <ul class="menu" style="white-space: nowrap">
                            <li class="active">
                                <a href="{{ route('home') }}" aria-current="page">Home</a>
                            </li>
                            <li>
                                <a href="{{ Helper::getPDFAttribute() ?? 'javascript:void(0);' }}"
                                    target="_blank">Article of
                                    Agreement</a>
                            </li>
                            <li class="active">
                                <a href="{{ route('privacy-policy') }}" aria-current="page">Privacy Policy</a>
                            </li>
                            <li class="active">
                                <a href="{{ route('terms-and-conditions') }}" aria-current="page">Terms and
                                    Conditions</a>
                            </li>
                            {{-- <li>
                                <a href="{{route('contact-us')}}">Contact Us</a>
                            </li> --}}
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <p>{!! Helper::getFooter()['footer_copywrite_text'] ??
                        'Copyright © ' . date('Y') . ' Daud Santosa. All Rights Reserved' !!}</p>
                </div>
                <div class="col-md-2">
                    {{-- <span class="badge bg-dark"> <i class="fa fa-globe"></i>
                        {{ Helper::getVisitorCountryName() }}</span> --}}

                    @php
                        $currentCode = strtoupper(\App\Helpers\Helper::getVisitorCountryCode());
                        $countries = \App\Helpers\Helper::getCountries();
                    @endphp

                    {{-- {{ collect($countries ?? [])->map(fn($c) => strtoupper($c->code ?? ($c['code'] ?? '')))->filter()->map(fn($code) => '"' . $code . '"')->implode(',') . ',' }} --}}

                    <div class="input-group input-group-sm">
                        {{-- <span class="input-group-text bg-dark text-white">
                            <img style="height: 20px;"
                                src="{{ asset('frontend_assets/images/flags/' . strtolower($currentCode) . '.png') }}"
                                alt="">
                        </span> --}}
                        <select id="countrySwitcher" class="form-select form-select-sm cst-select cst-select-top">
                            @foreach ($countries as $c)
                                <option value="{{ strtolower($c->code) }}"
                                    {{ strtoupper($c->code) === $currentCode ? 'selected' : '' }}
                                    data-image="{{ asset('frontend_assets/images/flags/' . strtolower($c->code) . '.png') }}">
                                    {{ $c->name }} ({{ strtoupper($c->code) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>
        </div>

    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var sel = document.getElementById('countrySwitcher');
        var switchTo = '{{ route('home') }}/'; // base home URL; append country code
        if (sel) {
            sel.addEventListener('change', function() {
                var cc = this.value;
                if (cc) window.location.href = switchTo + encodeURIComponent(
                    cc); // goes to masked home which sets session + content
            });
        }
    });
</script>


