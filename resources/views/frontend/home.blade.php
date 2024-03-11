@extends('frontend.layouts.master')
@section('meta_title')
@endsection
@section('title')
    {{ env('APP_NAME') }} - Home
@endsection
@push('styles')
@endpush

@section('content')
<section class="banner__slider banner_sec">
    <div class="slider">
        <div class="slide">
            <a href="" tabindex="0">
                <div class="slide__img">
                    <video autoplay="" muted="" loop="" class="video_part">
                        <source src="{{asset('frontend_assets/uploads/2024/02/earth.mp4')}}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <!-- <img src="" alt="" class="full-image d-block d-md-none" /> -->
                    <img src="{{asset('frontend_assets/uploads/2024/02/banner_lion-6.png')}}" alt=""
                        class="full-image overlay-image">
                </div>
            </a>
            <div class="slide__content slide__content__left">
                <div class="slide__content--headings text-left">
                    <h1 class="title">A habitation where supernatural and solution intersects</h1>
                    <p class="top-title"></p>
                    <!--<a class="red_btn slidebottomleft" href=""><span>get started</span></a>-->
                </div>
            </div>
        </div>
    </div>
</section>
<section class="about_sec"
    style="background:url({{asset('frontend_assets/uploads/2023/05/Mask-group-2.jpg')}}); background-repeat: no-repeat; background-size: cover;">
    <div class="v_text right_v">About Us</div>
    <div class="container">
        <div class="row align-items-center justify-content-center">
            <div class="col-xl-5 col-lg-6 mb-4" data-aos="fade-up" data-aos-duration="1000">
                <div class="img_part">
                    <div class="img1">
                        <video controls="" style="width: 100%; height: 100%;">
                            <source src="{{asset('frontend_assets/uploads/2024/02/v3.mp4')}}" type="video/mp4">
                        </video>
                    </div>
                </div>
            </div>
            <div class="col-xl-5 col-lg-6" data-aos="fade-up" data-aos-duration="1000">
                <div class="about_text heading_hp text_white">
                    <h6>about</h6>
                    <h2>Lion Roaring, PMA</h2>
                    <p style="font-weight: 400;">
                        <strong>Lion Roaring Private Members Association’s (PMA)
                            main
                            focus is to bring Heaven’s cultures on earth and to restore nations
                            (communities,
                            cities, states, and countries) through each soul whom the Lord has transformed
                            and
                            chosen. This soul will be given the opportunity to work for the Lion Roaring
                            Innovation Centers and its partners being fully educated through Lion Roaring
                            Foundation as God’s king and priest. The goal for each soul is to create
                            inspired
                            ideas and become self-sufficient to perform good works and bring heaven’s
                            attributes
                            into its environment.</strong>
                    </p>
                </div>
                <a class="red_btn" data-animation-in="fadeInUp"
                    href="https://www.lionroaring.us/about-us/"><span>read more</span></a>
            </div>
        </div>
    </div>
</section>
<section class="after_about after_about_hm">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8">
                <div class="abt-box-1">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="after_abt first_abt first_abt_1">
                                <div class="row align-items-center justify-content-center flex-column">
                                    <div class="col-md-4">
                                        <div class="img_abt flex-fixed">
                                            <img src="{{asset('frontend_assets/uploads/2023/04/after_abt.jpg')}}" alt="">
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="abt_text_white">
                                            <h4 class="flex-fixed">Daud Santosa</h4>
                                            <div class="srl" id="srl_1">
                                                <p><strong>Daud Santosa has over 35 years of experience as a
                                                        transformation leader in the IT industry for
                                                        establishing the corporate technology vision and
                                                        leading
                                                        all the aspects of the corporate technology
                                                        deployment
                                                        and development in both Government and Industry
                                                        sectors.
                                                        Responsibilites included transforming organization,
                                                        people, and technologies to create new products and
                                                        services. These new products and services, including
                                                        modernization of infrastructures, helped establish
                                                        new
                                                        business services in the digital world, create new
                                                        IT
                                                        organizations which helped develop new cost models
                                                        for
                                                        Enterprise Data Inventory Strategy, Shared Services
                                                        Centers, Cloud Computing services, and IT
                                                        methodology.
                                                        This led to a balance of score cards, performance
                                                        metrics/Service Level Agreements, and business
                                                        processes
                                                        automation in areas of Financial, Human Resources,
                                                        Acquisition, Law Enforcement, Telecommunications,
                                                        Research Survey, and IT Hosting Services. Daud has
                                                        held
                                                        many different roles ranging from software engineer,
                                                        Certified Chief IT Lead, IT Executive, and Chief
                                                        Technology Officer. He has managed and had oversight
                                                        over the IT budget within the range of $75,000,000
                                                        up to
                                                        $2,000,000,000.<br>
                                                        The Lord transformed Daud in 2016 after undergoing
                                                        and
                                                        surviving his third brain surgery at the end of
                                                        2015. He
                                                        began seeking the Lord in 2016 by waiting on God
                                                        every
                                                        day from 3:00 to 4:30 AM. He had many spiritual
                                                        encounters through dreams and visions in 2016-2017.
                                                        He
                                                        saw Jesus’s face in the 3rd Dimension when he was on
                                                        Mt.
                                                        Sinai. He also attended the Open Heaven Prophetic
                                                        Conference with Prophet Sadhu Selvaraj in 2017.
                                                        Since
                                                        then, his life has changed as he regularly
                                                        walkiswith
                                                        God. He continues to experience many visions,
                                                        dreams,
                                                        and revelations. The Lord has taught him over the
                                                        past
                                                        two years as a leader under JMK Maryland (a branch
                                                        of
                                                        Jesus My King Church, Shelby, North Carolina) to
                                                        prepare
                                                        his congregation to become a wise Sower with
                                                        Kingship
                                                        and Priesthood anointing.<br>
                                                        Alos, during this time, he received the strategy of
                                                        Lion
                                                        Roaring (Kingship Authority) and established JMK
                                                        Maryland (Priesthood authority) through divine
                                                        intervention of the Holy Spirit as he connected with
                                                        Pastor Michael Widjaya and Dr. Steven Francis. This
                                                        is
                                                        God’s destiny as revealed to Daud and his wife; that
                                                        is
                                                        to establish Kingship and Priesthood authority where
                                                        Heaven is brought on earth with the office of
                                                        Christ,
                                                        which is the office of the everlasting Kingdom of
                                                        Light.<br>
                                                        Currently, he is the elder of JMK Maryland that
                                                        helps
                                                        that facilitate Lion Roaring teachings on Spiritual
                                                        and
                                                        Leadership Development by helping to restore one
                                                        person,
                                                        group, community, and nation at the time as the Lord
                                                        directs him.</strong></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="after_abt">
                                <div class="row align-items-center justify-content-center flex-column">
                                    <div class="col-md-4">
                                        <div class="img_abt flex-fixed">
                                            <img src="{{asset('frontend_assets/uploads/2023/08/IMG_2989-2.jpeg')}}" alt="">
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="abt_text_white">
                                            <h4 class="flex-fixed">Lystia Santosa</h4>
                                            <div class="srl" id="srl_1">
                                                <p><strong>Lystia Santosa has over 35 years of experience
                                                        working in the financial and accounting field for
                                                        International Non-profit Organizations. She has held
                                                        various positions during her career such as an
                                                        auditor
                                                        for a CPA Firm auditing the Federal Government Grant
                                                        Programs, a Field Office Project Accountant and
                                                        Senior
                                                        Accountant, Accounting Manager, Controller, and then
                                                        a
                                                        Director of Finance position. Although Lystia
                                                        obtained
                                                        her CPA (Certified Public Accountant) early in her
                                                        career, she decided not to pursue a career in the
                                                        Public
                                                        Accounting but to devote her career working for an
                                                        international non-profit organization working with
                                                        the
                                                        third world countries. It was her passion to work in
                                                        an
                                                        organization in which the primary mission and the
                                                        vision
                                                        were to help people in Third World countries to
                                                        improve
                                                        their standard of living.<br>
                                                        Prior to her retirement in 2019, she held a position
                                                        as
                                                        the Director of Finance (CFO) working for the
                                                        largest
                                                        U.S. based international worker rights organization.
                                                        She
                                                        helped facilitate the organization’s mission of
                                                        helping
                                                        workers attain safe and healthy workplaces, while
                                                        promoting worker’s equality. She also helped improve
                                                        workers’ standard of living with education and
                                                        collective agreement, and by helping fight
                                                        discrimination, and by pr eventing the exploitation
                                                        of
                                                        systems that entrench poverty. Her 30 plus years’
                                                        experience working with this organization gave her
                                                        solid
                                                        and broad mastery in all areas in financial
                                                        management,
                                                        financial affairs, budgeting, human resources and
                                                        personnel policy and procedures. Furthermore, she
                                                        developed expertise to ensure compliance with U.S.
                                                        Federal Rules &amp; regulations on grant awards, and
                                                        how
                                                        to effectively deal with the the organization’s
                                                        funders
                                                        (U.S. Government, foundation and international
                                                        donors).
                                                        She was responsible for the organization’s annual
                                                        budget
                                                        of about $32,000,000.00 and directed staff in the
                                                        finance and accounting departments at the company’s
                                                        headquarters, and approximately 30 filed offices.
                                                        Additionally, she was a member of the Executive
                                                        Team,
                                                        and she worked closely with the CEO, COO and other
                                                        Directors in the implementation of the
                                                        organization’s
                                                        vision and mission.<br>
                                                        Since retiring in 2019, Lystia has been volunteering
                                                        her
                                                        time in helping JMK Shelby, North Carolina church
                                                        with
                                                        their accounting and financial matters. She is also
                                                        the
                                                        elder for JMK Maryland church alongside her husband,
                                                        Daud Santosa.<br>
                                                        During the pandemic, the Lord brought her into a
                                                        more
                                                        intimate relationship with Him and helped train her
                                                        to
                                                        study His words on a deeper level. This helped shift
                                                        her
                                                        priorities from working from a worldly employer to
                                                        working for God’s Kingdom. She gave up her
                                                        consulting
                                                        work and completely devoted her time to studying and
                                                        working alongside with husband, Daud Santosa in
                                                        serving
                                                        the JMK Maryland church.</strong></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="key_feature_sec">
    <div class="v_text right_v feature_v"></div>
    <div class="key_bg">
        <img src="{{asset('frontend_assets/images/line.png')}}" alt="">
    </div>
    <div class="container">
        <div class="row align-items-center justify-content-center mb-5">
            <div class="col-lg-8">
                <div class="about_text heading_hp text-center">
                    <h2>Our Governance Board</h2>
                    <h6>This Board provides direction and oversight for day-to-day operation of Lion
                        Roaring,
                        PMA.</h6>
                </div>
            </div>
        </div>
        <div class="slid_bh">
            <div class="padding_k">
                <div class="bounce_1">
                    <div class="one_cli">
                        <div class="one_cli_nh">
                            <img src="{{asset('frontend_assets/images/before_n.png')}}" alt="">
                        </div>
                        <div class="clild_box">
                            <div class="clild_sec">
                                <img src="{{asset('frontend_assets/uploads/2023/08/JFS_6593-4-1.png')}}" alt="">
                                <h4>Daud Santosa</h4>

                            </div>
                            <a href="" class="ellipss_right" tabindex="-1">
                                <i class="fa-solid fa-ellipsis"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="padding_k">
                <div class="bounce_2">
                    <div class="one_cli1">
                        <div class="one_cli_nh">
                            <img src="{{asset('frontend_assets/images/before_n1.png')}}" alt="">
                        </div>
                        <div class="clild_box">
                            <div class="clild_sec bg_fir">
                                <img src="{{asset('frontend_assets/uploads/2023/08/IMG_2989-2.jpeg')}}" alt="">
                                <h4>Lystia Santosa</h4>
                            </div>
                            <a href="https://www.lionroaring.us/governance/lystia-santosa/"
                                class="ellipss_right" tabindex="-1">
                                <i class="fa-solid fa-ellipsis"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="padding_k">
                <div class="bounce_1">
                    <div class="one_cli">
                        <div class="one_cli_nh">
                            <img src="{{asset('frontend_assets/images/before_n.png')}}" alt="">
                        </div>
                        <div class="clild_box">
                            <div class="clild_sec">
                                <img src="{{asset('frontend_assets/uploads/2023/08/IRENE-FINAL.jpg')}}" alt="">
                                <h4>Irene Subowo</h4>

                            </div>
                            <a href="https://www.lionroaring.us/governance/irene-subowo/"
                                class="ellipss_right" tabindex="-1"> <i
                                    class="fa-solid fa-ellipsis"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="padding_k">
                <div class="bounce_2">
                    <div class="one_cli1">
                        <div class="one_cli_nh">
                            <img src="{{asset('frontend_assets/images/before_n1.png')}}" alt="">
                        </div>
                        <div class="clild_box">
                            <div class="clild_sec bg_fir">
                                <img src="{{asset('frontend_assets/uploads/2023/08/JFS_6569.jpeg')}}" alt="">
                                <h4>Sanny Subowo</h4>

                            </div>
                            <a href="https://www.lionroaring.us/governance/sanny-subowo/"
                                class="ellipss_right" tabindex="-1"> <i
                                    class="fa-solid fa-ellipsis"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="padding_k">
                <div class="bounce_1">
                    <div class="one_cli">
                        <div class="one_cli_nh">
                            <img src="{{asset('frontend_assets/images/before_n.png')}}" alt="">
                        </div>
                        <div class="clild_box">
                            <div class="clild_sec">
                                <img src="{{asset('frontend_assets/uploads/2023/08/JFS_6572-1-scaled.jpg')}}" alt="">
                                <h4>Robert Hyde</h4>

                            </div>
                            <a href="https://www.lionroaring.us/governance/robert-hyde/" class="ellipss_right"
                                tabindex="0"> <i class="fa-solid fa-ellipsis"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="padding_k">
                <div class="bounce_2">
                    <div class="one_cli1">
                        <div class="one_cli_nh">
                            <img src="{{asset('frontend_assets/images/before_n1.png')}}" alt="">
                        </div>
                        <div class="clild_box">
                            <div class="clild_sec bg_fir">
                                <img src="{{asset('frontend_assets/uploads/2023/08/JFS_6564-scaled-1.png')}}" alt="">
                                <h4>Jasmine Goh</h4>

                            </div>
                            <a href="https://www.lionroaring.us/governance/jasmine-goh/" class="ellipss_right"
                                tabindex="0"> <i class="fa-solid fa-ellipsis"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="padding_k">
                <div class="bounce_1">
                    <div class="one_cli">
                        <div class="one_cli_nh">
                            <img src="{{asset('frontend_assets/images/before_n.png')}}" alt="">
                        </div>
                        <div class="clild_box">
                            <div class="clild_sec">
                                <img src="{{asset('frontend_assets/uploads/2023/08/JFS_6585-1-scaled-1.png')}}" alt="">
                                <h4>Elizabeth Chirwa</h4>

                            </div>
                            <a href="https://www.lionroaring.us/governance/elizabeth-chirwa/"
                                class="ellipss_right" tabindex="0"> <i
                                    class="fa-solid fa-ellipsis"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="real_solution_sec">
    <div class="v_text left_v">Services</div>
    <div class="container">
        <div class="row align-items-center justify-content-center mb-5">
            <div class="col-lg-6">
                <div class="about_text heading_hp text-center text_white">
                    <h6></h6>
                    <h2>OUR ORGANIZATION</h2>
                    <h4>A habitation where supernatural and solution intersects</h4>
                </div>
            </div>
        </div>
        <div class="row g-0 align-items-center justify-content-center row-cols-1 row-cols-lg-3 row-cols-md-2">
            <div class="col" data-aos="fade-up" data-aos-duration="1000">
                <div class="tow_box_j">
                    <div class="row align-items-center justify-content-center">
                        <div class="col-lg-12">
                            <div class="solution_img">
                                <a href="">
                                    <img src="{{asset('frontend_assets/uploads/2023/04/IFCM-CWP-Vakkur-Tamil-Nadu1-scaled.jpg')}}"
                                        alt="">
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="box_solution box_blue">
                                <h4><a href="">Lion
                                        Roaring Education Center</a></h4>
                                <p style="font-weight: 400;"><strong>The mission of Lion Roaring Education
                                        Centers (LREC) is to educate each person to embrace the kingdom of
                                        God
                                        by restoring the soul through the salvation of the Lord Jesus. In
                                        doing
                                        so, LREC will also help develop spiritual maturity through spiritual
                                        growth and transformation. And to nurture those skills according to
                                        that
                                        person’s giftedness within the circle of the Lion Roaring Community
                                        of
                                        interest groups and within the Lion Roaring Habitation and
                                        partnership
                                        around the world.</strong></p>
                                <p>.</p>
                                <a href="" class="ellipss"><i class="fa-solid fa-ellipsis"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col" data-aos="fade-up" data-aos-duration="1000">
                <div class="tow_box_j">
                    <div class="row align-items-center justify-content-center">
                        <div class="col-lg-12">
                            <div class="solution_img">
                                <a href="">
                                    <img src="{{asset('frontend_assets/uploads/2023/04/our_service.jpg')}}" alt="">
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="box_solution box_blue">
                                <h4><a href="">Lion
                                        Roaring Innovation Center</a></h4>
                                <p style="font-weight: 400;"><strong>The mission of Lion Roaring innovation
                                        center is building the future of innovation technologies to support
                                        the
                                        vision of Lion Roaring and to support natural habitation that
                                        follows
                                        Psalm 104:14-18, 24-25 – “God cause grass to grow for the cattle,
                                        herb
                                        for the service of man: bring forth food out of the earth; and wine
                                        that
                                        makes glad the heart of man, and oil to make his face to shine, and
                                        bread which strengthens mean’s heart. In wisdom God made them all:
                                        the
                                        earth full of your riches”. This innovation will be leveraged to
                                        help
                                        restore villages, cities, states, and nations through Lion Roaring
                                        Education Centers.</strong></p>
                                <a href="" class="ellipss"><i class="fa-solid fa-ellipsis"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="testimonial_sec">
    <div class="v_text left_v">TESTIMONIES</div>
    <div class="container">
        <div class="row align-items-center justify-content-center mb-5">
            <div class="col-lg-6">
                <div class="about_text heading_hp text-center text_white">
                    <h6></h6>
                    <h2>TESTIMONIES</h2>
                </div>
            </div>
        </div>
        <div class="row align-items-center justify-content-center mb-5">
            <div class="col-xl-8 col-lg-10">
                <div class="testimonial_slider">
                    <div class="client">
                        <div class="testimonial_box">
                            <div class="client_img">
                                <img src="{{asset('frontend_assets/uploads/2023/04/Ps-Pablo1.png')}}.jpg')}}" alt="">
                            </div>
                            <div class="client-text">
                                <h2>Pastor Pablo and Adriana<span>Pastor from Argentina,
                                        Santa
                                        Fe Capital</span></h2>
                                <div class="srlt" id="">
                                    <p><strong>Dear Pastor David and Lystia Santosa, dear
                                            brothers of Lion Roaring. We send you a great
                                            and
                                            warm brotherly hug in our beloved Lord Jesus,
                                            who is
                                            pleased to connect us, and not with just any
                                            connection, but an eternal one for his purposes
                                            and
                                            plans in our city and nation. </strong></p>
                                    <p><strong>We want to bear witness to everything that
                                            the
                                            Father has done in this time, which is a lot and
                                            Supernatural!! </strong></p>
                                    <p><strong>“Many times we think that God is taking a
                                            while
                                            to answer us, but in reality He is preparing us
                                            for
                                            what is coming.” And it is precisely what the
                                            Eternal planned and allowed on Wednesday,
                                            September
                                            20, 2023, in the middle of an evangelistic
                                            campaign
                                            for children.</strong></p>
                                    <p><strong>That day, Claudio, one of the servants, went
                                            out
                                            to receive all the prophetic company that would
                                            be
                                            helping us evangelize. Something “crazy”
                                            happened…
                                            And Claudio felt a very strong voice that told
                                            him:
                                            “That’s the man” (Claudio looked everywhere… who
                                            said that?? what man??)</strong></p>
                                    <p><strong>Immediately the Face of Pastor David
                                            appeared. He
                                            understood there that God had something in his
                                            hands… A day prepared by the Father where in
                                            seconds
                                            he allowed a very strong spiritual
                                            bond.</strong>
                                    </p>
                                    <p><strong>Since without knowing each other, without
                                            having
                                            ever spoken before, God began to bring words
                                            through
                                            the mouth of Pastor David, and each word was
                                            nothing
                                            more than the promises of God for the territory,
                                            for
                                            our congregation and for an entire generation of
                                            children and young people that God will use to
                                            proclaim his kingdom.</strong></p>
                                    <p><strong>Encouragement and new strength came with each
                                            of
                                            Pastor David’s words about Pastor Pablo. Oh, our
                                            hearts were beating strong! We could only say
                                            “Thank
                                            you God! You do as you please; you have sent
                                            someone
                                            from another place to speak to us, to know that
                                            nothing has been in vain! Thank you God because
                                            your
                                            eyes are on this house!!!” truly God is
                                            Sovereign.
                                            We cannot stop getting excited.</strong></p>
                                    <p><strong>The father put it in our hearts, 5 years ago,
                                            that He would use the church to restore the
                                            vile,
                                            the despised, and the rejected by others.
                                        </strong>
                                    </p>
                                    <p><strong>We embraced that word and our hearts beat
                                            strongly to reach the generations of young
                                            people
                                            and children that we saw getting lost every day.
                                        </strong></p>
                                    <p><strong>God allowed us to find the Jesús Rey Soberano
                                            Revival Center. It is in front of an old train
                                            station, a place where many young people go take
                                            drugs and get drunk. They are homeless and
                                            totally
                                            abandoned, and many of them are lost in crime.
                                            &nbsp;Children are living in sad realities. They
                                            are
                                            alone in their homes. They use the warehouses of
                                            the
                                            station to get their basic needs. They lack of
                                            affection. No one tells them about
                                            Jesus…</strong>
                                    </p>
                                    <p><strong>We take the challenge and seize the territory
                                            for
                                            the Lord.&nbsp; God did not fail to send an
                                            answer.
                                            You Pastor, as we told you before, “You are the
                                            answer to our prayers.</strong></p>
                                    <p><strong>We want to thank you for your support, trust
                                            and
                                            every planting for this place. With each seed,
                                            we
                                            have built a kitchen, a place where we have a
                                            space
                                            and comforts to be able to cook and to provide
                                            snacks and dinner to our children, adolescents
                                            and
                                            young people. The kitchen is equipped with pizza
                                            oven and some furniture. </strong></p>
                                    <p><strong>We also bought new chairs, instruments for
                                            the
                                            altar, and a gazebo so we could preach in the
                                            streets. Part of those seeds have also been used
                                            to
                                            build cement floors at the entrance to the
                                            temple.&nbsp; At the Christmas evangelistic
                                            event,
                                            where we were able to provide food and toys to a
                                            large number of children, to whom we were able
                                            to
                                            talk and introduce them to Jesus. God is doing
                                            amazing things!</strong></p>
                                </div>
                                <div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="client">
                        <div class="testimonial_box testimonial_box_2">
                            <div class="client_img">
                                <img src="{{asset('frontend_assets/uploads/2023/08/mobile-banner.jpg')}}" alt="">
                            </div>
                            <div class="client-text">
                                <h2>TBD<span></span></h2>
                                <div class="srlt" id="">
                                    <p>TBD</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="client">
                        <div class="testimonial_box">
                            <div class="client_img">
                                <img src="{{asset('frontend_assets/uploads/2023/07/Sept-13-food-5.jpg')}}" alt="">
                            </div>
                            <div class="client-text">
                                <h2>Pastor Prabhat<span>Pastor from India</span></h2>
                                <div class="srlt" id="">
                                    <div>
                                        <p><b>Praise the Lord,<br>
                                                Greeting to you in the name of loving Lord
                                                Savior Jesus!<br>
                                                The Lord has chosen you for His royal work
                                                anointed you and filled you with abundant
                                                grace.
                                                God the Father has anointed you with a
                                                special
                                                anointing of word of understanding and word
                                                of
                                                wisdom, word of knowledge and the Holy
                                                Spirit.
                                                He has anointed you to know the truth of the
                                                word quickly and has given many spiritual
                                                gifts,
                                                and God has laid on your hands a fivefold
                                                ministry.<br>
                                                I thank God for you and the ministry, God
                                                has
                                                sent and burdened you to help us both
                                                physically
                                                and spiritually. The villa of Orissa has
                                                benefited from many donations we have
                                                received
                                                from you and your ministry.<br>
                                                1. Monthly donation for Pastors. With your
                                                help,
                                                the Lord’s work is going on in Orissa today.
                                                The
                                                Gospel is being preached, and today 15
                                                pastors/
                                                preachers have received benefit from your
                                                monthly donation. They are preaching the
                                                gospel
                                                in the sparsely populated areas and able to
                                                minister to the villages through Sunday
                                                service
                                                or visitation.</b></p>
                                    </div>
                                    <div>
                                        <p><b>2. Women sewing machine project. By your
                                                helping
                                                hands and generous support, the desperate
                                                and
                                                distressed mothers (widows) have benefited a
                                                lot. The 24 mothers who have received sewing
                                                machines are now able to earn a living and
                                                meet
                                                the dual expenditure needs of their
                                                families. In
                                                addition, they also have given back their
                                                earned
                                                income to the ministry through tithe and
                                                offering. Their offering has enabled our
                                                ministry to serve the elderly with the basic
                                                needs of groceries. On a monthly basis,
                                                10-15
                                                poor families are able to eat every
                                                month.</b>
                                        </p>
                                    </div>
                                    <div>
                                        <p><b>3. Basic groceries (Food items) need due to
                                                rainy/flooding season. We were able to
                                                provide
                                                food items to about 300 families which some
                                                homes have been damaged by the rain and
                                                floods.</b></p>
                                    </div>
                                    <div>
                                        <p><b>4. Christmas project – blankets and sweaters
                                                for
                                                children. We distributed 300 blankets for
                                                elderly family and widows during winter and
                                                about 100 sweaters for the orphan’s
                                                children.</b></p>
                                    </div>
                                    <div>
                                        <p><b>5. Easter project – outreach ministry. With
                                                the
                                                projector, screen, speaker and other
                                                equipment,
                                                we were able to show Jesus movie to several
                                                villages.<br>
                                                You are doing a great and satisfying work in
                                                the
                                                eyes of God, there is a great reward for
                                                you,
                                                the Lord will give it to you.<br>
                                                Thank the Lord for your donation, thank you
                                                for
                                                your generous heart, love and personal help.
                                                Your ministry is doing a great service for
                                                the
                                                Lord!<br>
                                            </b><b><img draggable="false" role="img" class="emoji"
                                                    alt="🙏"
                                                    src="https://s.w.org/images/core/emoji/14.0.0/svg/1f64f.svg"><img
                                                    draggable="false" role="img" class="emoji"
                                                    alt="🙏"
                                                    src="https://s.w.org/images/core/emoji/14.0.0/svg/1f64f.svg"></b><b>
                                                Thank you for your love, constant prayer and
                                                helping hands,</b></p>
                                    </div>
                                    <div>
                                        <p><b>May the Lord keep you more and more with His
                                                Spirit and fill you with His blessings and
                                                always continue in the work of the Lord.
                                                Thank
                                                you. We always pray for you and give thanks
                                                to
                                                God.<br>
                                                Pastor. Prabhatkumar mali<br>
                                                Orissa, India.<br>
                                                Thank you.</b></p>
                                    </div>
                                </div>
                                <div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="client">
                        <div class="testimonial_box testimonial_box_2">
                            <div class="client_img">
                                <img src="{{asset('frontend_assets/uploads/2023/05/German-e1690575420430.jpeg')}}" alt="">
                            </div>
                            <div class="client-text">
                                <h2>Pastor German Ace<span>Pastor from Philippines</span>
                                </h2>
                                <div class="srlt" id="srlt_1">
                                    <p style="font-weight: 400;"><strong>I thank the Lord
                                            for
                                            the partnership with the Lion Roaring for 3
                                            years
                                            ago, I have been doing outreaches and bible
                                            study
                                            that time when servant of God Brother Daud
                                            Santosa
                                            and Family help me in my financial needs, it
                                            almost
                                            lasted for 3 years. The Santosa Family and
                                            ministry
                                            partners supported me that leads to formation of
                                            planting. I have been an independent worker that
                                            time and with the help of our ministry partners
                                            I
                                            was able to in large our border. Thank God for
                                            the
                                            Santosa Family and friends.</strong></p>
                                    <p style="font-weight: 400;"><strong>To God be the
                                            Glory</strong></p>
                                    <p style="font-weight: 400;"><strong>&nbsp;</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="client">
                        <div class="testimonial_box">
                            <div class="client_img">
                                <img src="{{asset('frontend_assets/uploads/2023/04/Ps-Pablo1.png')}}.jpg')}}" alt="">
                            </div>
                            <div class="client-text">
                                <h2>Pastor Pablo and Adriana<span>Pastor from Argentina,
                                        Santa
                                        Fe Capital</span></h2>
                                <div class="srlt" id="">
                                    <p><strong>Dear Pastor David and Lystia Santosa, dear
                                            brothers of Lion Roaring. We send you a great
                                            and
                                            warm brotherly hug in our beloved Lord Jesus,
                                            who is
                                            pleased to connect us, and not with just any
                                            connection, but an eternal one for his purposes
                                            and
                                            plans in our city and nation. </strong></p>
                                    <p><strong>We want to bear witness to everything that
                                            the
                                            Father has done in this time, which is a lot and
                                            Supernatural!! </strong></p>
                                    <p><strong>“Many times we think that God is taking a
                                            while
                                            to answer us, but in reality He is preparing us
                                            for
                                            what is coming.” And it is precisely what the
                                            Eternal planned and allowed on Wednesday,
                                            September
                                            20, 2023, in the middle of an evangelistic
                                            campaign
                                            for children.</strong></p>
                                    <p><strong>That day, Claudio, one of the servants, went
                                            out
                                            to receive all the prophetic company that would
                                            be
                                            helping us evangelize. Something “crazy”
                                            happened…
                                            And Claudio felt a very strong voice that told
                                            him:
                                            “That’s the man” (Claudio looked everywhere… who
                                            said that?? what man??)</strong></p>
                                    <p><strong>Immediately the Face of Pastor David
                                            appeared. He
                                            understood there that God had something in his
                                            hands… A day prepared by the Father where in
                                            seconds
                                            he allowed a very strong spiritual
                                            bond.</strong>
                                    </p>
                                    <p><strong>Since without knowing each other, without
                                            having
                                            ever spoken before, God began to bring words
                                            through
                                            the mouth of Pastor David, and each word was
                                            nothing
                                            more than the promises of God for the territory,
                                            for
                                            our congregation and for an entire generation of
                                            children and young people that God will use to
                                            proclaim his kingdom.</strong></p>
                                    <p><strong>Encouragement and new strength came with each
                                            of
                                            Pastor David’s words about Pastor Pablo. Oh, our
                                            hearts were beating strong! We could only say
                                            “Thank
                                            you God! You do as you please; you have sent
                                            someone
                                            from another place to speak to us, to know that
                                            nothing has been in vain! Thank you God because
                                            your
                                            eyes are on this house!!!” truly God is
                                            Sovereign.
                                            We cannot stop getting excited.</strong></p>
                                    <p><strong>The father put it in our hearts, 5 years ago,
                                            that He would use the church to restore the
                                            vile,
                                            the despised, and the rejected by others.
                                        </strong>
                                    </p>
                                    <p><strong>We embraced that word and our hearts beat
                                            strongly to reach the generations of young
                                            people
                                            and children that we saw getting lost every day.
                                        </strong></p>
                                    <p><strong>God allowed us to find the Jesús Rey Soberano
                                            Revival Center. It is in front of an old train
                                            station, a place where many young people go take
                                            drugs and get drunk. They are homeless and
                                            totally
                                            abandoned, and many of them are lost in crime.
                                            &nbsp;Children are living in sad realities. They
                                            are
                                            alone in their homes. They use the warehouses of
                                            the
                                            station to get their basic needs. They lack of
                                            affection. No one tells them about
                                            Jesus…</strong>
                                    </p>
                                    <p><strong>We take the challenge and seize the territory
                                            for
                                            the Lord.&nbsp; God did not fail to send an
                                            answer.
                                            You Pastor, as we told you before, “You are the
                                            answer to our prayers.</strong></p>
                                    <p><strong>We want to thank you for your support, trust
                                            and
                                            every planting for this place. With each seed,
                                            we
                                            have built a kitchen, a place where we have a
                                            space
                                            and comforts to be able to cook and to provide
                                            snacks and dinner to our children, adolescents
                                            and
                                            young people. The kitchen is equipped with pizza
                                            oven and some furniture. </strong></p>
                                    <p><strong>We also bought new chairs, instruments for
                                            the
                                            altar, and a gazebo so we could preach in the
                                            streets. Part of those seeds have also been used
                                            to
                                            build cement floors at the entrance to the
                                            temple.&nbsp; At the Christmas evangelistic
                                            event,
                                            where we were able to provide food and toys to a
                                            large number of children, to whom we were able
                                            to
                                            talk and introduce them to Jesus. God is doing
                                            amazing things!</strong></p>
                                </div>
                                <div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="client">
                        <div class="testimonial_box testimonial_box_2">
                            <div class="client_img">
                                <img src="{{asset('frontend_assets/uploads/2023/08/mobile-banner.jpg')}}" alt="">
                            </div>
                            <div class="client-text">
                                <h2>TBD<span></span></h2>
                                <div class="srlt" id="srlt_1">
                                    <p>TBD</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="client">
                        <div class="testimonial_box">
                            <div class="client_img">
                                <img src="{{asset('frontend_assets/uploads/2023/07/Sept-13-food-5.jpg')}}" alt="">
                            </div>
                            <div class="client-text">
                                <h2>Pastor Prabhat<span>Pastor from India</span></h2>
                                <div class="srlt" id="">
                                    <div>
                                        <p><b>Praise the Lord,<br>
                                                Greeting to you in the name of loving Lord
                                                Savior Jesus!<br>
                                                The Lord has chosen you for His royal work
                                                anointed you and filled you with abundant
                                                grace.
                                                God the Father has anointed you with a
                                                special
                                                anointing of word of understanding and word
                                                of
                                                wisdom, word of knowledge and the Holy
                                                Spirit.
                                                He has anointed you to know the truth of the
                                                word quickly and has given many spiritual
                                                gifts,
                                                and God has laid on your hands a fivefold
                                                ministry.<br>
                                                I thank God for you and the ministry, God
                                                has
                                                sent and burdened you to help us both
                                                physically
                                                and spiritually. The villa of Orissa has
                                                benefited from many donations we have
                                                received
                                                from you and your ministry.<br>
                                                1. Monthly donation for Pastors. With your
                                                help,
                                                the Lord’s work is going on in Orissa today.
                                                The
                                                Gospel is being preached, and today 15
                                                pastors/
                                                preachers have received benefit from your
                                                monthly donation. They are preaching the
                                                gospel
                                                in the sparsely populated areas and able to
                                                minister to the villages through Sunday
                                                service
                                                or visitation.</b></p>
                                    </div>
                                    <div>
                                        <p><b>2. Women sewing machine project. By your
                                                helping
                                                hands and generous support, the desperate
                                                and
                                                distressed mothers (widows) have benefited a
                                                lot. The 24 mothers who have received sewing
                                                machines are now able to earn a living and
                                                meet
                                                the dual expenditure needs of their
                                                families. In
                                                addition, they also have given back their
                                                earned
                                                income to the ministry through tithe and
                                                offering. Their offering has enabled our
                                                ministry to serve the elderly with the basic
                                                needs of groceries. On a monthly basis,
                                                10-15
                                                poor families are able to eat every
                                                month.</b>
                                        </p>
                                    </div>
                                    <div>
                                        <p><b>3. Basic groceries (Food items) need due to
                                                rainy/flooding season. We were able to
                                                provide
                                                food items to about 300 families which some
                                                homes have been damaged by the rain and
                                                floods.</b></p>
                                    </div>
                                    <div>
                                        <p><b>4. Christmas project – blankets and sweaters
                                                for
                                                children. We distributed 300 blankets for
                                                elderly family and widows during winter and
                                                about 100 sweaters for the orphan’s
                                                children.</b></p>
                                    </div>
                                    <div>
                                        <p><b>5. Easter project – outreach ministry. With
                                                the
                                                projector, screen, speaker and other
                                                equipment,
                                                we were able to show Jesus movie to several
                                                villages.<br>
                                                You are doing a great and satisfying work in
                                                the
                                                eyes of God, there is a great reward for
                                                you,
                                                the Lord will give it to you.<br>
                                                Thank the Lord for your donation, thank you
                                                for
                                                your generous heart, love and personal help.
                                                Your ministry is doing a great service for
                                                the
                                                Lord!<br>
                                            </b><b><img draggable="false" role="img" class="emoji"
                                                    alt="🙏"
                                                    src="https://s.w.org/images/core/emoji/14.0.0/svg/1f64f.svg"><img
                                                    draggable="false" role="img" class="emoji"
                                                    alt="🙏"
                                                    src="https://s.w.org/images/core/emoji/14.0.0/svg/1f64f.svg"></b><b>
                                                Thank you for your love, constant prayer and
                                                helping hands,</b></p>
                                    </div>
                                    <div>
                                        <p><b>May the Lord keep you more and more with His
                                                Spirit and fill you with His blessings and
                                                always continue in the work of the Lord.
                                                Thank
                                                you. We always pray for you and give thanks
                                                to
                                                God.<br>
                                                Pastor. Prabhatkumar mali<br>
                                                Orissa, India.<br>
                                                Thank you.</b></p>
                                    </div>
                                </div>
                                <div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="client">
                        <div class="testimonial_box testimonial_box_2">
                            <div class="client_img">
                                <img src="{{asset('frontend_assets/uploads/2023/05/German-e1690575420430.jpeg')}}" alt="">
                            </div>
                            <div class="client-text">
                                <h2>Pastor German Ace<span>Pastor from Philippines</span>
                                </h2>
                                <div class="srlt" id="">
                                    <p style="font-weight: 400;"><strong>I thank the Lord
                                            for
                                            the partnership with the Lion Roaring for 3
                                            years
                                            ago, I have been doing outreaches and bible
                                            study
                                            that time when servant of God Brother Daud
                                            Santosa
                                            and Family help me in my financial needs, it
                                            almost
                                            lasted for 3 years. The Santosa Family and
                                            ministry
                                            partners supported me that leads to formation of
                                            planting. I have been an independent worker that
                                            time and with the help of our ministry partners
                                            I
                                            was able to in large our border. Thank God for
                                            the
                                            Santosa Family and friends.</strong></p>
                                    <p style="font-weight: 400;"><strong>To God be the
                                            Glory</strong></p>
                                    <p style="font-weight: 400;"><strong>&nbsp;</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="client">
                        <div class="testimonial_box">
                            <div class="client_img">
                                <img src="{{asset('frontend_assets/uploads/2023/04/Ps-Pablo1.png')}}.jpg')}}" alt="">
                            </div>
                            <div class="client-text">
                                <h2>Pastor Pablo and Adriana<span>Pastor from Argentina,
                                        Santa
                                        Fe Capital</span></h2>
                                <div class="srlt" id="">
                                    <p><strong>Dear Pastor David and Lystia Santosa, dear
                                            brothers of Lion Roaring. We send you a great
                                            and
                                            warm brotherly hug in our beloved Lord Jesus,
                                            who is
                                            pleased to connect us, and not with just any
                                            connection, but an eternal one for his purposes
                                            and
                                            plans in our city and nation. </strong></p>
                                    <p><strong>We want to bear witness to everything that
                                            the
                                            Father has done in this time, which is a lot and
                                            Supernatural!! </strong></p>
                                    <p><strong>“Many times we think that God is taking a
                                            while
                                            to answer us, but in reality He is preparing us
                                            for
                                            what is coming.” And it is precisely what the
                                            Eternal planned and allowed on Wednesday,
                                            September
                                            20, 2023, in the middle of an evangelistic
                                            campaign
                                            for children.</strong></p>
                                    <p><strong>That day, Claudio, one of the servants, went
                                            out
                                            to receive all the prophetic company that would
                                            be
                                            helping us evangelize. Something “crazy”
                                            happened…
                                            And Claudio felt a very strong voice that told
                                            him:
                                            “That’s the man” (Claudio looked everywhere… who
                                            said that?? what man??)</strong></p>
                                    <p><strong>Immediately the Face of Pastor David
                                            appeared. He
                                            understood there that God had something in his
                                            hands… A day prepared by the Father where in
                                            seconds
                                            he allowed a very strong spiritual
                                            bond.</strong>
                                    </p>
                                    <p><strong>Since without knowing each other, without
                                            having
                                            ever spoken before, God began to bring words
                                            through
                                            the mouth of Pastor David, and each word was
                                            nothing
                                            more than the promises of God for the territory,
                                            for
                                            our congregation and for an entire generation of
                                            children and young people that God will use to
                                            proclaim his kingdom.</strong></p>
                                    <p><strong>Encouragement and new strength came with each
                                            of
                                            Pastor David’s words about Pastor Pablo. Oh, our
                                            hearts were beating strong! We could only say
                                            “Thank
                                            you God! You do as you please; you have sent
                                            someone
                                            from another place to speak to us, to know that
                                            nothing has been in vain! Thank you God because
                                            your
                                            eyes are on this house!!!” truly God is
                                            Sovereign.
                                            We cannot stop getting excited.</strong></p>
                                    <p><strong>The father put it in our hearts, 5 years ago,
                                            that He would use the church to restore the
                                            vile,
                                            the despised, and the rejected by others.
                                        </strong>
                                    </p>
                                    <p><strong>We embraced that word and our hearts beat
                                            strongly to reach the generations of young
                                            people
                                            and children that we saw getting lost every day.
                                        </strong></p>
                                    <p><strong>God allowed us to find the Jesús Rey Soberano
                                            Revival Center. It is in front of an old train
                                            station, a place where many young people go take
                                            drugs and get drunk. They are homeless and
                                            totally
                                            abandoned, and many of them are lost in crime.
                                            &nbsp;Children are living in sad realities. They
                                            are
                                            alone in their homes. They use the warehouses of
                                            the
                                            station to get their basic needs. They lack of
                                            affection. No one tells them about
                                            Jesus…</strong>
                                    </p>
                                    <p><strong>We take the challenge and seize the territory
                                            for
                                            the Lord.&nbsp; God did not fail to send an
                                            answer.
                                            You Pastor, as we told you before, “You are the
                                            answer to our prayers.</strong></p>
                                    <p><strong>We want to thank you for your support, trust
                                            and
                                            every planting for this place. With each seed,
                                            we
                                            have built a kitchen, a place where we have a
                                            space
                                            and comforts to be able to cook and to provide
                                            snacks and dinner to our children, adolescents
                                            and
                                            young people. The kitchen is equipped with pizza
                                            oven and some furniture. </strong></p>
                                    <p><strong>We also bought new chairs, instruments for
                                            the
                                            altar, and a gazebo so we could preach in the
                                            streets. Part of those seeds have also been used
                                            to
                                            build cement floors at the entrance to the
                                            temple.&nbsp; At the Christmas evangelistic
                                            event,
                                            where we were able to provide food and toys to a
                                            large number of children, to whom we were able
                                            to
                                            talk and introduce them to Jesus. God is doing
                                            amazing things!</strong></p>
                                </div>
                                <div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="client">
                        <div class="testimonial_box testimonial_box_2">
                            <div class="client_img">
                                <img src="{{asset('frontend_assets/uploads/2023/08/mobile-banner.jpg')}}" alt="">
                            </div>
                            <div class="client-text">
                                <h2>TBD<span></span></h2>
                                <div class="srlt" id="">
                                    <p>TBD</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="gallery_sec margin_27">
    <div class="gallery_slider">
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/04/Ilapuram-IFCM-Child-Welfare-Program-5.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/04/IFCM-CWP-Vakkur-Tamil-Nadu1-scaled.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/04/IFCM-CWP-Kayathur-Tamil-Nadu.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/04/IFCM-CWP-Chhatarpur-Madhya-Pradesh.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/04/20220318_150717-scaled.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/04/20220301_161055-scaled.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/04/20220301_160259-scaled.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/04/20220301_155616-scaled.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2024/02/PHOTO-2023-12-17-13-25-04_6.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2024/02/PHOTO-2023-12-17-13-25-03.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2024/02/4b79e079-5670-4d72-9ea8-b719ebde1cc9.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2024/02/3f7977bd-2cac-45c0-b47f-0e412007ab68.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2024/02/WhatsApp-Image-2024-02-19-at-00.43.48_8cb84201.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/08/Nov-17-orphanage-2.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/08/Oct-19-prayer-13.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/08/Nov-22-sweaters-1.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/07/IMG_8269photo.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/07/IMG_8462photo.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/07/IMG_8464photo.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/07/IMG_8317photo.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/07/Nov-17-orphanage-1.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/07/IMG_8440photo.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/07/Sept-13-food-5.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2023-03-31-at-17.43.59-1.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2023-03-31-at-14.48.49.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2023-03-31-at-14.47.53.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2023-03-31-at-14.42.10.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2023-02-02-at-12.16.34.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2023-02-02-at-12.15.20.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2023-02-02-at-12.14.14.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2023-02-02-at-12.14.13.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2022-12-08-at-10.48.49.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2022-12-08-at-10.36.08.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2022-12-08-at-10.36.08-1.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2022-12-08-at-10.36.07.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2022-12-08-at-10.36.07-2.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2022-12-08-at-10.36.07-1.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2022-12-08-at-10.28.45.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/04/WhatsApp-Image-2022-12-08-at-10.28.44.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/04/WhatsApp-Image-2022-12-08-at-10.28.44-1-1.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/04/Ilapuram-IFCM-Child-Welfare-Program-5.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/04/IFCM-CWP-Vakkur-Tamil-Nadu1-scaled.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/04/IFCM-CWP-Kayathur-Tamil-Nadu.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/04/IFCM-CWP-Chhatarpur-Madhya-Pradesh.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/04/20220318_150717-scaled.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/04/20220301_161055-scaled.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/04/20220301_160259-scaled.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/04/20220301_155616-scaled.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2024/02/PHOTO-2023-12-17-13-25-04_6.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2024/02/PHOTO-2023-12-17-13-25-03.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2024/02/4b79e079-5670-4d72-9ea8-b719ebde1cc9.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2024/02/3f7977bd-2cac-45c0-b47f-0e412007ab68.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2024/02/WhatsApp-Image-2024-02-19-at-00.43.48_8cb84201.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/08/Nov-17-orphanage-2.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/08/Oct-19-prayer-13.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/08/Nov-22-sweaters-1.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/07/IMG_8269photo.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/07/IMG_8462photo.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/07/IMG_8464photo.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/07/IMG_8317photo.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/07/Nov-17-orphanage-1.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/07/IMG_8440photo.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/07/Sept-13-food-5.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2023-03-31-at-17.43.59-1.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2023-03-31-at-14.48.49.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2023-03-31-at-14.47.53.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2023-03-31-at-14.42.10.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2023-02-02-at-12.16.34.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2023-02-02-at-12.15.20.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2023-02-02-at-12.14.14.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2023-02-02-at-12.14.13.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2022-12-08-at-10.48.49.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2022-12-08-at-10.36.08.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2022-12-08-at-10.36.08-1.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2022-12-08-at-10.36.07.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2022-12-08-at-10.36.07-2.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2022-12-08-at-10.36.07-1.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/05/WhatsApp-Image-2022-12-08-at-10.28.45.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/04/WhatsApp-Image-2022-12-08-at-10.28.44.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/04/WhatsApp-Image-2022-12-08-at-10.28.44-1-1.jpeg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/04/Ilapuram-IFCM-Child-Welfare-Program-5.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/04/IFCM-CWP-Vakkur-Tamil-Nadu1-scaled.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/04/IFCM-CWP-Kayathur-Tamil-Nadu.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/04/IFCM-CWP-Chhatarpur-Madhya-Pradesh.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/04/20220318_150717-scaled.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/04/20220301_161055-scaled.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/04/20220301_160259-scaled.jpg')}}" alt="">
        </div>
        <div class="gallery_box" style="width: 100%; display: inline-block;">
            <img src="{{asset('frontend_assets/uploads/2023/04/20220301_155616-scaled.jpg')}}" alt="">
        </div>
    </div>
</section>
@endsection

@push('scripts')
@endpush
