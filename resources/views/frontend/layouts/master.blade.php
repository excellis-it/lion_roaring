<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Swarnadwip Nath">
    <meta name="generator" content="Hugo 0.84.0">
    @yield('meta_title')
    <title>@yield('title')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,500;1,600;1,700;1,800&amp;display=swap"
        rel="stylesheet">
    <!-- Bootstrap core CSS -->
    <link href="{{ asset('frontend_assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.3/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.css"
        integrity="sha512-Woz+DqWYJ51bpVk5Fv0yES/edIMXjj3Ynda+KWTIkGoynAMHrqTcDUQltbipuiaD5ymEo9520lyoVOo9jCQOCA=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="{{ asset('frontend_assets/css/menu.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend_assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend_assets/css/responsive.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css" rel="stylesheet"
        type="text/css" />
    <style>
        .goog-te-banner-frame.skiptranslate {
            display: none !important;
        }

        body {
            top: 0px !important;
        }

        .goog-logo-link {
            display: none !important;
        }

        .trans-section {
            margin: 100px;
        }
    </style>
    @stack('styles')
</head>

<body>
    <main>
        @include('frontend.includes.header')

        <!--=====================================-->
        <!--=       Hero Inner Page Banner Area Start =-->
        <!--=====================================-->
        @yield('content')


        <!--=====================================-->
        <!--=        Footer Area Start       	=-->
        <!--=====================================-->
        <!-- Start Footer Area  -->
        @include('frontend.includes.footer')

        <!-- Custom styles for this template -->
        <div class="modal fade modal_code" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body login_bg_sec border-top-0">
                        <div class="heading_hp">
                            <h2 id="greeting">Good Afternoon</h2>
                            <h4>Sign on to enter Lion Roaring PMA Private Member area.</h4>
                            <form name="login-form" id="login-form" action="" method="post">
                                <p class="login-username">
                                    <label for="user_login">Username</label>
                                    <input type="text" name="log" id="user_login" autocomplete="username"
                                        class="input" value="" size="20">
                                </p>
                                <p class="login-password">
                                    <label for="user_password">Password</label>
                                    <input type="password" name="pwd" id="user_password"
                                        autocomplete="current-password" spellcheck="false" class="input" value=""
                                        size="20">
                                </p>
                                <p class="login-submit">
                                    <input type="submit" name="wp-submit" id="login-submit"
                                        class="button button-primary" value="Log In">
                                    <input type="hidden" name="redirect_to" value="">
                                </p>
                            </form>
                            <p>
                                <a href="" data-bs-toggle="modal" data-bs-target="#join_member"
                                    class="text-dark">Join
                                    Lion
                                    Roaring Member</a> | <a href="" class="text-dark">Forgot username or
                                    password
                                </a>
                            </p>
                            <a href="" class="text-dark">
                            </a>
                            <a href="" class="login_privacy">Privacy,
                                Cookies, and Legal </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade donate_bg_sec" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel1"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="heading_hp">
                            <div class="asp_product_item">
                                <div class="asp_product_item_top">
                                    <div class="asp_product_item_thumbnail">

                                    </div>
                                    <div class="asp_product_name">
                                        Lion Roaring Donate
                                    </div>
                                </div>
                                <div style="clear:both;"></div>
                                <div class="asp_product_description">
                                    <p>Lion Roaring is a Private Member Association,thus any donations will not be
                                        eligible for tax deductible purposes under the IRS Code. Please consult with
                                        your tax advisor should there be any questions related to your donation.</p>

                                </div>
                                <div class="asp_price_container">
                                    <span class="asp_price_amount"></span> <span class="asp_new_price_amount"></span>
                                    <span class="asp_quantity"></span>
                                    <div class="asp_under_price_line"></div>
                                </div>
                                <div class="asp_product_buy_button">
                                    <div class="asp-processing-cont" style="display:none;"><span
                                            class="asp-processing">Processing <i>.</i><i>.</i><i>.</i></span></div>
                                    <form id="asp_ng_form_065e81241e506b" class="asp-stripe-form" action=""
                                        method="POST"> <input type="hidden" name="asp_product_id" value="861">
                                        <div class="asp-child-hidden-fields" style="display: none !important;">
                                        </div>
                                    </form>
                                    <div id="asp-all-buttons-container-065e81241e506b"
                                        class="asp_all_buttons_container">
                                        <div class="asp_product_buy_btn_container"><button
                                                id="asp_ng_button_065e81241e506b" type="submit"
                                                class="asp_product_buy_btn blue"><span>NEXT</span></button></div>
                                        <noscript>Stripe Payments requires Javascript to be supported by the browser
                                            in
                                            order to operate.</noscript>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade modal_code" id="join_member" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body login_bg_sec border-top-0">
                        <div class="join_member_box">
                            <div class="ftr_logo_sec text-center">
                                <a href="https://www.lionroaring.us" class="ftr_logo d-inline-block">
                                    <img src="uploads/2024/02/Group-2029.png" alt="">
                                </a>
                            </div>
                            <h2>Lion Roaring PMA (Private Members Association) Agreement</h2>
                            <p>It is the responsibility of the members to read and review the Articles of
                                Association of
                                Lion Roaring PMA in its entirety and agree to adopt and comply to its belief,
                                foundation
                                and purpose of the Lion Roaring PMA. </p>
                            <p> Click here to read the full document <a
                                    href="uploads/2024/01/Articles-of-Association-1.pdf">
                                    ARTICLES OF ASSOCIATION</a> (provide link to bring up the PMA to the screen)</p>
                            <p>Each member agrees to the following excerpt taken from the Articles of Association of
                                PMA:</p>
                            <ul>
                                <li>Lion Roaring PMA is a Private Members Association protected under the
                                    Constitution
                                    of the United States of America and the original constitution for these united
                                    States of America and the Maryland Constitution</li>
                                <li>Member agrees and is supportive to the mission and vision of the Lion Roaring
                                </li>
                                <li>Member strives to contribute to the purpose of the PMA to fulfill the God given
                                    call
                                    to the founding members as it is written in Section 4 through 15 in the Article
                                    of
                                    Association</li>
                                <li>Member will not hold Lion Roaring PMA liable for any materials or contents
                                    posted in
                                    the website or any paperwork, written articles, education materials or others
                                    created within the PMA for its members’ benefits and private usage</li>
                                <li>Member’s agreement does not entitle a member to any financial or other interest
                                    in
                                    the Private Members Association or management thereof</li>
                                <li>Information regarding details of the association, any materials produced or
                                    created
                                    by Lion Roaring PMA including all paperwork, agreements, articles, PowerPoints
                                    presentations, word parchments, coaching, and education materials are private
                                    intellectual property of the PMA and will not be shared, replicated, dispersed
                                    or
                                    distributed with anyone outside the PMA without explicit written permission from
                                    the
                                    founder</li>
                                <li>Member’s due diligence is expected and member will hold harmless any member or
                                    founder of Lion Roaring PMA and any dispute shall be handled by the founder(s)
                                    with
                                    final decision for remedy made by the founder(s) and shall be accepted as a
                                    settled
                                    matter. (Article III for disputes resolution &amp; Article IV for Sovereignty in
                                    the
                                    Private)</li>
                                <li>As a private member of the Lion Roaring PMA, member is invoking its united
                                    States
                                    constitutional rights specifically the 1st, 4th, 5th, 9th and 10th and the
                                    Maryland
                                    Constitutional rights included in the Maryland Declaration of Rights Sections 1,
                                    2,
                                    6, 10, 24, 26, 36, 40 and 45 and as such take full responsibility for his or her
                                    behavior, such that his or her actions shall never constitute anything that can
                                    be
                                    determined to be of a “clear and present danger of a substantial evil.” </li>
                                <li>Any actions by the member which are not consistent with the values of the PMA
                                    can
                                    result in the founder’s decision to ask the member to leave the PMA</li>
                                <li>Member is connected with each other and the actions affect one another,
                                    therefore,
                                    the Lion Roaring PMA encourages and supports one another as a family and
                                    community
                                </li>
                                <li>Member and those who are included in this member’s agreement and contract are
                                    solely
                                    responsible for member’s own outcome or results from participating or receiving
                                    any
                                    education materials, counsel, coaching, training, mentoring or other services
                                    provided by Lion Roaring PMA through its websites or any other resources made
                                    available to the members</li>
                                <li>The terminology used in these articles of organization and member’s agreement is
                                    used solely for clarification of the various usages for Private Members
                                    Association
                                    under universal contract law by and between free, spiritually free men and
                                    women,
                                    creations of nature and Natures God, whose lives and rights derive from God
                                    Almighty
                                    and unique Covenant of the man and/or woman with the Creator</li>
                                <li>Any reference within the Articles of Association to the man shall also include
                                    the
                                    woman and any reference to one people may include many people. This PMA shall be
                                    construed and interpreted in the private and all decisions or disputes will be
                                    final
                                    as settled by the founders in accordance with Article III </li>
                                <li>Member agrees that the elimination of one Item or segment of this Agreement does
                                    not
                                    eliminate the entirety of the Agreement but the Agreement will remain as Agreed
                                </li>
                            </ul>

                            <form action="#" method="post">
                                <input type="checkbox" id="checkbox" name="checkbox" value="checked">
                                <label for="checkbox">I have read and agreed to the Lion Roaring PMA Agreement
                                </label>
                                <!-- Remove the submit button -->
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="onload_popup" class="modal fade" data-bs-keyboard="false" data-bs-backdrop="static"
            style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header text-center justify-content-center">
                        <!--         <button type="button" class="close" data-bs-dismiss="modal">&times;</button> -->
                        <h4 class="modal-title">LION ROARING PRIVATE MEMBERS ASSOCIATION (PMA)</h4>
                    </div>
                    <form action="{{ route('session.store') }}" method="POST">
                        @csrf
                        <div class="modal-body text-center">
                            <h3>DISCLAIMER</h3>
                            <p>Lion Roaring PMA is a private members association of the participants for educational and
                                informational purposes only. None of the postings of education and information materials
                                can
                                be construed as advice, directive, opinion or recommendation for the members to initiate
                                actions. The contents of the Lion Roaring (LR) website reflects the mission, vision,
                                strategies, and partners’ testimonies of LR PMA. The educational teaching modules are
                                based
                                on our belief and faith in Jesus Christ, biblical foundation and personal experiences
                                which
                                we choose to share for education purposes and spiritual journeys of our faith.</p>
                            <p>
                                In order to protect all members from any adverse interference or action brought on by
                                local,
                                county, state, or federal regulatory, administrative or licensing agencies, and to
                                receive
                                full benefits from participation in Lion Roaring Private Member Association, (hereafter
                                referred to as PMA) all visitors accessing the Lion Roaring website consent to the
                                following:
                            </p>
                            <ul>
                                <li>
                                    All member’s information and postings displayed in the website are privileged
                                    information for the members’ benefit and access only. Any unauthorized use,
                                    collection,
                                    storing or sharing of the content or information from the website is prohibited</li>
                                <li>Visitors will not hold Lion Roaring PMA liable for any materials or contents posted
                                    in
                                    the website</li>
                                <li>Any disclosure, reproduction, distribution, forwarding or other use of the LR PMA
                                    website content or any attachments to an individual or entity other than the
                                    intended
                                    recipient is prohibited</li>
                            </ul>
                            <div class="modal_checkbox">
                                <div class="form-group">
                                    <input type="checkbox" id="pma_check" name="is_checked">
                                    <label for="pma_check">I have read and agreed to the above requirements to access
                                        Lion
                                        Roaring PMA website.</label>
                                </div>
                            </div>
                            <button type="submit" class="continue-btn changed" data-bs-dismiss="modal"
                                id="myButton">Continue</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>


    </main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
    <script src="{{ asset('frontend_assets/js/bootstrap.bundle.min.js') }}"></script>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="{{ asset('frontend_assets/js/custom.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox-plus-jquery.js"
        integrity="sha512-0rYcJjaqTGk43zviBim8AEjb8cjUKxwxCqo28py38JFKKBd35yPfNWmwoBLTYORC9j/COqldDc9/d1B7dhRYmg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en'
            }, 'google_translate_element');
        }
    </script>

    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit">
    </script>

    <script>
        @if (Session::has('message'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.success("{{ session('message') }}");
        @endif

        @if (Session::has('error'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.error("{{ session('error') }}");
        @endif

        @if (Session::has('info'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.info("{{ session('info') }}");
        @endif

        @if (Session::has('warning'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.warning("{{ session('warning') }}");
        @endif
    </script>
    <script>
        $(document).ready(function() {
            $(document).on('submit', '#submit-newsletter', function(e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');
                $.ajax({
                    type: "POST",
                    url: url,
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status === true) {
                            $('.text-danger').html('');
                            $('#newsletter_email').val('');
                            $('#newsletter_name').val('');
                            $('#newsletter_message').val('');
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                showConfirmButton: true,
                                timer: 3000
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message,
                                showConfirmButton: true,
                                timer: 3000
                            });
                        }
                    },
                    error: function(xhr) {
                        $('.text-danger').html('');
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            if (key.includes('.')) {
                                var fieldName = key.split('.')[0];
                                // Display errors for array fields
                                var num = key.match(/\d+/)[0];
                                $('#' + fieldName + '_' + num).html(value[0]);
                            } else {
                                // after text danger span
                                $('#' + key + '_error').html(value[0]);
                            }
                        });
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            @if (Session::has('agree'))
            @else
                $('#onload_popup').modal('show');
            @endif
        });
    </script>
    @stack('scripts')
</body>

</html>
