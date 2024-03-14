@extends('frontend.layouts.master')
@section('meta_title')
@endsection
@section('title')
    {{ env('APP_NAME') }} - Details
@endsection
@push('styles')
@endpush

@section('content')
    <section class="inner_banner_sec"
        style="background-image: url({{ asset('frontend_assets/uploads/2023/07/inner_banner.jpg') }}); background-position: center; background-repeat: no-repeat; background-size: cover">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="inner_banner_ontent text-center">
                        <h1>Details</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="about_sec">
        <div class="container">
            @if (count($details) > 0)
                @foreach ($details as $key => $item)
                <div class="row align-items-center justify-content-center mb-5">
                    <div class="col-xl-7 col-lg-7 {{($key % 2 == 0) ? 'order-2 order-lg-1' : ''}}" data-aos="fade-up" data-aos-duration="500">
                        <div class="about_text heading_hp text_white">
                            <p>{{ $item->description }}

                            </p>
                        </div>
                    </div>
                    <div class="col-xl-5 col-lg-5 {{($key+1 % 2 == 0) ? 'order-1 order-lg-2' : ''}}" data-aos="fade-up" data-aos-duration="1000">
                        <div class="single_img_lion">
                            <img src="{{ Storage::url($item->image) }}" alt="" />
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="col-md-12">
                    <div class="alert alert-danger" role="alert">
                        No Details Found!
                    </div>
                </div>
            @endif
            {{-- <div class="row align-items-center justify-content-center mb-5">
                <div class="col-xl-7 col-lg-7" data-aos="fade-up" data-aos-duration="500">
                    <div class="about_text heading_hp text_white">
                        <p>This is the story behind this logo. When I encountered the Lord Jesus in 2017, I saw in the
                            vision when I was lying in the stone in Mt. Sinai. I saw the face of the Lord Jesus just like
                            the man in this picture. His eyes were blue but the hair was a dark color and expanded beyond
                            this picture. I choose this picture from the little girl name : Akiane Kramarik. See her bio:
                            https://godtv.com/portrait-of-jesus-akiane-kramarik/. Her painting is the closest to the face
                            that I saw on Mt. Sinai. God will raise up His people to have these four different faces as
                            shown in the logo.

                            The face of a Man signifies God’s King and priest. God created man in His image and His
                            likeness. This man has thus united with Christ and has the character of Christ. He is no longer
                            of the mindset of “I live in me” but Christ lives in me. This man, with character quality of
                            eagle, Lion, and Ox, will rule the earth. God continues to create and He will restore man, like
                            God, to reign the earth.

                        </p>
                    </div>
                </div>
                <div class="col-xl-5 col-lg-5" data-aos="fade-up" data-aos-duration="1000">
                    <div class="single_img_lion">
                        <img src="https://www.lionroaring.us/wp-content/uploads/2023/08/jesus.png" alt="" />
                    </div>
                </div>
            </div>
            <div class="row align-items-center justify-content-center mb-5">
                <div class="col-xl-5 col-lg-5 order-2 order-lg-1" data-aos="fade-up" data-aos-duration="500">
                    <div class="single_img_lion">
                        <img src="https://www.lionroaring.us/wp-content/uploads/2023/08/lion.png" alt="" />
                    </div>
                </div>
                <div class="col-xl-7 col-lg-7 order-1 order-lg-2" data-aos="fade-up" data-aos-duration="1000">
                    <div class="about_text heading_hp text_white">
                        <p>

                            The Face of Lion signifies Authority, Strength and Power. The character of lion demonstrates
                            tremendous strength, very strong in the spirit by abiding in God. The lion does not budge, he
                            stands strong, no matter what happens around him. The lion anointing exhibits boldness and
                            territorial protection, it protects its territory from the darkness.</p>
                    </div>
                </div>
            </div>
            <div class="row align-items-center justify-content-center mb-5">
                <div class="col-xl-7 col-lg-7" data-aos="fade-up" data-aos-duration="500">
                    <div class="about_text heading_hp text_white">
                        <p>The face of Eagle signifies the eagle soaring during a thunderstorm. During the darkness days,
                            people of God who have been given the eagle’s anointing will soar high with the Holy Spirit to
                            navigate through the darkness. The eagle has incredible sight. It has two sets of eye lids. The
                            manifested Christian eagle has the ability to see in different views, or the ability to see
                            things that other do not see. These individuals will be able to see and move in the realm of the
                            spirit, not flesh.</p>
                    </div>
                </div>
                <div class="col-xl-5 col-lg-5" data-aos="fade-up" data-aos-duration="1000">
                    <div class="single_img_lion">
                        <img src="https://www.lionroaring.us/wp-content/uploads/2023/08/egal.png" alt="" />
                    </div>
                </div>
            </div>
            <div class="row align-items-center justify-content-center mb-5">
                <div class="col-xl-5 col-lg-5 order-2 order-lg-1" data-aos="fade-up" data-aos-duration="500">
                    <div class="single_img_lion">
                        <img src="https://www.lionroaring.us/wp-content/uploads/2023/09/lion.jpg" alt="" />
                    </div>
                </div>
                <div class="col-xl-7 col-lg-7 order-1 order-lg-2" data-aos="fade-up" data-aos-duration="1000">
                    <div class="about_text heading_hp text_white">
                        <p>The face of an Ox signifies with the characteristic of hard work. Our logo the Ox appeared as
                            flame that came out from these three characters. This fire signifies the Holy Spirit power for
                            those remnants who has the character of Christ, Eagle and Lion will serve His kingdom in this
                            last day ministry. The ox for ploughs, hauls, and performs manual labor. The is the beast of
                            work and burden. The work of the Lord has to be done. In 2 Cor 6:1 “As God's fellow-workers we
                            also urge you not to receive his grace and then do nothing with it.” We will labor together with
                            God. The ox has to work. Its function is labor and to serve, working for God. There are two
                            levels here; the first level is where we begin by learning to be workers for God. The second
                            level is where we learn to Minister with God (partner). 2 Cor 6:1, 1 Cor 3:9. It means we labor
                            together with God. Working with God! (as a partner).

                        </p>
                    </div>
                </div>
            </div> --}}
        </div>
    </section>
@endsection

@push('scripts')
@endpush
