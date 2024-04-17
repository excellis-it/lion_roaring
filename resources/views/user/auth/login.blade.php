
<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>{{ env('APP_NAME') }} - Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">
      <!-- Bootstrap core CSS -->
      <link href="{{asset('user_assets/css/bootstrap.min.css')}}" rel="stylesheet">
      <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css">
      <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.3/animate.min.css">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.css"
          integrity="sha512-Woz+DqWYJ51bpVk5Fv0yES/edIMXjj3Ynda+KWTIkGoynAMHrqTcDUQltbipuiaD5ymEo9520lyoVOo9jCQOCA=="
          crossorigin="anonymous" referrerpolicy="no-referrer">
      <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
      <link href="{{asset('user_assets/css/menu.css')}}" rel="stylesheet">
      <link rel="stylesheet" href="{{asset('user_assets/css/style.min.')}}css">
      <link href="{{asset('user_assets/css/style.css')}}" rel="stylesheet">
      <link href="{{asset('user_assets/css/responsive.css')}}" rel="stylesheet">
</head>

<body style="background: #643271">
    <main>
        <section class="log-main">
            <div class="container">
                <div class="row justify-content-center align-items-center">
                    <div class="col-lg-5">
                        <div class="login_bg_sec border-top-0">
                            <div class="heading_hp">
                                <h2 id="greeting">Good Afternoon</h2>
                                <h4>Sign on to enter Lion Roaring PMA Private Member area.</h4>
                                <div class="admin-form">
                                    <form name="login-form" id="login-form" action="" method="post">
                                        <p class="login-username">
                                            <label for="user_login">Username or Email Address</label>
                                            <input type="text" name="log" id="user_login" autocomplete="username"
                                                class="input" value="" size="20">
                                        </p>
                                        <p class="login-password">
                                            <label for="user_password">Password</label>
                                            <input type="password" name="pwd" id="user_password"
                                                autocomplete="current-password" spellcheck="false" class="input"
                                                value="" size="20">
                                        </p>
                                        <div class="check-main">
                                            <div class="form-group">
                                                <input type="checkbox" id="pma_check">
                                                <label for="pma_check">Remember Me</label>
                                            </div>
                                        </div>
                                        <p class="login-submit mt-lg-4 mt-2">
                                            <input type="submit" name="wp-submit" id="login-submit"
                                                class="button button-primary w-100" value="Log In">
                                            <input type="hidden" name="redirect_to" value="">
                                        </p>
                                    </form>
                                </div>
                                <div class="join-text">
                                    <a href="javascrip:void(0);" data-bs-toggle="modal"
                                        data-bs-target="#staticBackdrop">Join Lion
                                        Roaring Member</a> | <a href="">Forgot username or password
                                    </a>
                                </div>
                                <div class="join-text join-text-1">
                                    <a href="">Privacy,
                                        Cookies, and Legal </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5> -->
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="">
                            <div class="logo-admin">
                                <img src="{{asset('user_assets/images/logo.png')}}" alt="">
                            </div>
                            <div class="heading_hp">
                                <h2 id="greeting">Lion Roaring PMA (Private Members Association) Agreement</h2>
                            </div>
                            <div class="member-text-div admin-srl" id="admin-srl_1">
                                <div class="member-text">
                                    <p>It is the responsibility of the members to read and review the Articles of
                                        Association of Lion Roaring PMA in its entirety and agree to adopt and comply to
                                        its belief, foundation and purpose of the Lion Roaring PMA. <a href="">Click
                                            here to read the full document XXXXXXXX</a></p>
                                </div>
                                <div class="member-text">
                                    <h4>Each member agrees to the following excerpt taken from the Articles of
                                        Association of PMA:</h4>
                                </div>
                                <div class="member-text">
                                    <ul>
                                        <li>1. Lion Roaring PMA is a Private Members Association protected under the
                                            Constitution of the United States of America and the original constitution
                                            for these united States of America and the Maryland Constitution</li>
                                        <li>2. Member agrees and is supportive to the mission and vision of the Lion
                                            Roaring</li>
                                        <li>3. Member strives to contribute to the purpose of the PMA to fulfill the God
                                            given call to the founding members as it is written in Section 4 through 15
                                            in the Article of Association</li>
                                        <li>4. Member will not hold Lion Roaring PMA liable for any materials or
                                            contents posted in the website or any paperwork, written articles, education
                                            materials or others created within the PMA for its members’ benefits and
                                            private usage</li>
                                        <li>5. Member’s agreement does not entitle a member to any financial or other
                                            interest in the Private Members Association or management thereof</li>
                                        <li>6. Information regarding details of the association, any materials produced
                                            or created by Lion Roaring PMA including all paperwork, agreements,
                                            articles, PowerPoints presentations, word parchments, coaching, and
                                            education materials are private intellectual property of the PMA and will
                                            not be shared, replicated, dispersed or distributed with anyone outside the
                                            PMA without explicit written permission from the founder</li>
                                        <li>7. Member’s due diligence is expected and member will hold harmless any
                                            member or founder of Lion Roaring PMA and any dispute shall be handled by
                                            the founder(s) with final decision for remedy made by the founder(s) and
                                            shall be accepted as a settled matter. (Article III for disputes resolution
                                            & Article IV for Sovereignty in the Private)</li>
                                        <li>8. As a private member of the Lion Roaring PMA, member is invoking its
                                            united States constitutional rights specifically the 1st, 4th, 5th, 9th and
                                            10th and the Maryland Constitutional rights included in the Maryland
                                            Declaration of Rights Sections 1, 2, 6, 10, 24, 26, 36, 40 and 45 and as
                                            such take full responsibility for his or her behavior, such that his or her
                                            actions shall never constitute anything that can be determined to be of a
                                            “clear and present danger of a substantial evil.”   </li>
                                        <li>9. Any actions by the member which are not consistent with the values of the
                                            PMA can result in the founder’s decision to ask the member to leave the PMA
                                        </li>
                                        <li>10. Member is connected with each other and the actions affect one another,
                                            therefore, the Lion Roaring PMA encourages and supports one another as a
                                            family and community</li>
                                        <li>11. Member and those who are included in this member’s agreement and
                                            contract are solely responsible for member’s own outcome or results from
                                            participating or receiving any education materials, counsel, coaching,
                                            training, mentoring or other services provided by Lion Roaring PMA through
                                            its websites or any other resources made available to the members</li>
                                        <li>12. The terminology used in these articles of organization and member’s
                                            agreement is used solely for clarification of the various usages for Private
                                            Members Association under universal contract law by and between free,
                                            spiritually free men and women, creations of nature and Natures God, whose
                                            lives and rights derive from God Almighty and unique Covenant of the man
                                            and/or woman with the Creator</li>
                                        <li>13.  Any reference within the Articles of Association to the man shall also
                                            include the woman and any reference to one people may include many people.
                                            This PMA shall be construed and interpreted in the private and all decisions
                                            or disputes will be final as settled by the founders in accordance with
                                            Article III  </li>
                                        <li>14. Member agrees that the elimination of one Item or segment of this
                                            Agreement does not eliminate the entirety of the Agreement but the Agreement
                                            will remain as Agreed</li>
                                    </ul>
                                </div>
                                <div class="check-main">
                                    <div class="form-group">
                                        <input type="checkbox" id="pma_check1">
                                        <label for="pma_check1">I have read and agreed to the Lion Roaring PMA
                                            Agreement</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-end">
                                <div class="col-lg-4">
                                    <div class="login-submit mt-lg-4 mt-2 text-end">
                                        <a href="private-mem-act.html" class="button button-primary w-100"> Next</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    </main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
    <script src="{{asset('user_assets/js/bootstrap.bundle.min.js')}}"></script>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="{{asset('user_assets/js/custom.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox-plus-jquery.js"
        integrity="sha512-0rYcJjaqTGk43zviBim8AEjb8cjUKxwxCqo28py38JFKKBd35yPfNWmwoBLTYORC9j/COqldDc9/d1B7dhRYmg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>

</html>
