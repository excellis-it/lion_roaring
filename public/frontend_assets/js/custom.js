$('#toggle').click(function() {
  $(this).toggleClass('active');
  $('#overlay').toggleClass('open');
 });




$(document).ready(function(){       
   $('#onload_popup').modal('show');
}); 


/*----- slier --------*/




$('.slider').slick({
  autoplay: true,
  speed: 2000,
  lazyLoad: 'progressive',
  arrows: false,
  dots: false,
  fade:true,
	prevArrow: '<div class="slick-nav prev-arrow"><i></i><svg><use xlink:href="#circle"></svg></div>',
	nextArrow: '<div class="slick-nav next-arrow"><i></i><svg><use xlink:href="#circle"></svg></div>',
  responsive: [
   
    {
      breakpoint: 768,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1,
        dots: false
      }
    }
  ]
});

$('.slick-nav').on('click touch', function(e) {

    e.preventDefault();

    var arrow = $(this);

    if(!arrow.hasClass('animate')) {
        arrow.addClass('animate');
        setTimeout(() => {
            arrow.removeClass('animate');
        }, 2000);
    }

});


/*----- slier --------*/

$('.slid_bh').slick({
  dots: false,
  arrows:false,
  autoplay: true,
  infinite: false,
  speed: 300,
  slidesToShow: 3,
  slidesToScroll: 1,
  prevArrow: '<div class="slick-nav prev-arrow"><i class="fa fa-arrow-left"></i></div>',
  nextArrow: '<div class="slick-nav next-arrow"><i class="fa fa-arrow-right"></i></div>',
  responsive: [
    {
      breakpoint: 1025,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 1,
        infinite: true,
        dots: false
      }
    },
    {
      breakpoint: 991,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 1
      }
    },
    {
      breakpoint: 768,
      settings: {
        slidesToShow: 1,
        centerPadding: '50px',
        arrows:false,
        slidesToScroll: 1
      }
    }
  ]
});


$('.testimonial_slider').slick({
  dots: true,
  arrows:false,
  autoplay: false,
  infinite: true,
  speed: 500,
  slidesToShow: 2,
  slidesToScroll: 2,
  prevArrow: '<div class="slick-nav prev-arrow"><i class="fa fa-arrow-left"></i></div>',
  nextArrow: '<div class="slick-nav next-arrow"><i class="fa fa-arrow-right"></i></div>',
  responsive: [
    {
      breakpoint: 1025,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 2,
        infinite: true,
        dots: false
      }
    },
    {
      breakpoint: 991,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 2
      }
    },
    {
      breakpoint: 768,
      settings: {
        slidesToShow: 1,
        centerPadding: '50px',
        arrows:false,
        slidesToScroll: 1
      }
    }
  ]
});


$('.gallery_slider').slick({
  autoplay: true,
  speed: 2000,
  lazyLoad: 'progressive',
  arrows: false,
  dots: false,
  slidesToShow: 7,
  slidesToScroll: 1,
  centerMode: true,
	prevArrow: '<div class="slick-nav prev-arrow"><i></i><svg><use xlink:href="#circle"></svg></div>',
	nextArrow: '<div class="slick-nav next-arrow"><i></i><svg><use xlink:href="#circle"></svg></div>',
  responsive: [
   
    {
      breakpoint: 1025,
      settings: {
        slidesToShow: 5,
        slidesToScroll: 1,
        dots: false
      }
    },
    {
      breakpoint: 768,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1,
        dots: false
      }
    }
  ]
});






var timeout;

function hide() {
  timeout = setTimeout(function () {
    $('.map-location-details').fadeOut(400);
  }, 400);
};

$('.map-point').mouseover(function () {
  clearTimeout(timeout);
  $(this).siblings('.map-location-details').fadeIn(400);
}).mouseout(hide);

$('.map-location-details').mouseover(function () {
  clearTimeout(timeout);
}).mouseout(hide);













AOS.init();
