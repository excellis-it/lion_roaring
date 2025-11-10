

$("#toggle").click(function () {
    $(this).toggleClass("active");
    $("#overlay").toggleClass("open");
});

/*----- slier --------*/

$(".slider").slick({
    autoplay: true,
    speed: 2000,
    lazyLoad: "progressive",
    arrows: false,
    dots: false,
    fade: true,
    prevArrow:
        '<div class="slick-nav prev-arrow"><i></i><svg><use xlink:href="#circle"></svg></div>',
    nextArrow:
        '<div class="slick-nav next-arrow"><i></i><svg><use xlink:href="#circle"></svg></div>',
    responsive: [
        {
            breakpoint: 768,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1,
                dots: false,
            },
        },
    ],
});

$(".slick-nav").on("click touch", function (e) {
    e.preventDefault();

    var arrow = $(this);

    if (!arrow.hasClass("animate")) {
        arrow.addClass("animate");
        setTimeout(() => {
            arrow.removeClass("animate");
        }, 2000);
    }
});

/*----- slier --------*/

$(".slid_bh").slick({
    dots: false,
    arrows: false,
    autoplay: true,
    infinite: false,
    speed: 300,
    slidesToShow: 4,
    slidesToScroll: 1,
    prevArrow:
        '<div class="slick-nav prev-arrow"><i class="fa fa-arrow-left"></i></div>',
    nextArrow:
        '<div class="slick-nav next-arrow"><i class="fa fa-arrow-right"></i></div>',
    responsive: [
        {
            breakpoint: 1025,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 1,
                infinite: true,
                dots: false,
            },
        },
        {
            breakpoint: 991,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 1,
            },
        },
        {
            breakpoint: 768,
            settings: {
                slidesToShow: 1,
                centerPadding: "50px",
                arrows: false,
                slidesToScroll: 1,
            },
        },
    ],
});

$(".testimonial_slider").slick({
    dots: false,
    arrows: true,
    autoplay: false,
    infinite: true,
    speed: 500,
    slidesToShow: 5,
    slidesToScroll: 1,
    centerMode: true,
    prevArrow:
        '<div class="slick-nav prev-arrow"><i class="fa fa-arrow-left"></i></div>',
    nextArrow:
        '<div class="slick-nav next-arrow"><i class="fa fa-arrow-right"></i></div>',
    responsive: [
        {
            breakpoint: 1025,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 2,
                infinite: true,
                dots: false,
            },
        },
        {
            breakpoint: 991,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 2,
            },
        },
        {
            breakpoint: 768,
            settings: {
                slidesToShow: 1,
                centerPadding: "50px",
                arrows: false,
                slidesToScroll: 1,
            },
        },
    ],
});

$(".gallery_slider").slick({
    autoplay: true,
    speed: 2000,
    lazyLoad: "progressive",
    arrows: false,
    dots: false,
    slidesToShow: 7,
    slidesToScroll: 1,
    centerMode: true,
    prevArrow:
        '<div class="slick-nav prev-arrow"><i></i><svg><use xlink:href="#circle"></svg></div>',
    nextArrow:
        '<div class="slick-nav next-arrow"><i></i><svg><use xlink:href="#circle"></svg></div>',
    responsive: [
        {
            breakpoint: 1025,
            settings: {
                slidesToShow: 5,
                slidesToScroll: 1,
                dots: false,
            },
        },
        {
            breakpoint: 768,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1,
                dots: false,
            },
        },
    ],
});

var timeout;

function hide() {
    timeout = setTimeout(function () {
        $(".map-location-details").fadeOut(400);
    }, 400);
}

$(".map-point")
    .mouseover(function () {
        clearTimeout(timeout);
        $(this).siblings(".map-location-details").fadeIn(400);
    })
    .mouseout(hide);

$(".map-location-details")
    .mouseover(function () {
        clearTimeout(timeout);
    })
    .mouseout(hide);

// register agreement check
$(document).ready(function () {
    // check if pma_register_check1 is checked, if checked then close registerModalFirst step and open registerModalSecond step

    $(".register_next_first").on("click", function () {
        if ($("#pma_register_check1").is(":checked")) {
            $("#registerModalFirst").modal("hide");
            $("#registerModalSecond").modal("show");
            console.log("Checkbox 1 is checked");
        } else {
            toastr.error("Please check the agreement");
            console.log("Checkbox 1 is not checked");
        }
    });

    // check if pma_register_check2 is checked, if checked then redirect to register route
    $(".register_next_second").on("click", function () {
        if ($("#pma_register_check2").is(":checked")) {
            window.location.href = register_page_route;
        } else {
            toastr.error("Please check the agreement");
            console.log("Checkbox 2 is not checked");
        }
    });
});

AOS.init();




  var pages = document.getElementsByClassName('page');
  for(var i = 0; i < pages.length; i++)
    {
      var page = pages[i];
      if (i % 2 === 0)
        {
          page.style.zIndex = (pages.length - i);
        }
    }

  document.addEventListener('DOMContentLoaded', function(){
    for(var i = 0; i < pages.length; i++)
      {
        //Or var page = pages[i];
        pages[i].pageNum = i + 1;
        pages[i].onclick=function()
          {
            if (this.pageNum % 2 === 0)
              {
                this.classList.remove('flipped');
                this.previousElementSibling.classList.remove('flipped');
              }
            else
              {
                this.classList.add('flipped');
                this.nextElementSibling.classList.add('flipped');
              }
           }
        }
  })