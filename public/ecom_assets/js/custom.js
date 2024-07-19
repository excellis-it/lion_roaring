(function($) {

  $.fn.menumaker = function(options) {
      
      var cssmenu = $(this), settings = $.extend({
        title: "Menu",
        format: "dropdown",
        sticky: false
      }, options);

      return this.each(function() {
        cssmenu.prepend('<div id="menu-button">' + settings.title + '</div>');
        $(this).find("#menu-button").on('click', function(){
          $(this).toggleClass('menu-opened');
          var mainmenu = $(this).next('ul');
          if (mainmenu.hasClass('open')) { 
            mainmenu.hide().removeClass('open');
          }
          else {
            mainmenu.show().addClass('open');
            if (settings.format === "dropdown") {
              mainmenu.find('ul').show();
            }
          }
        });

        cssmenu.find('li ul').parent().addClass('has-sub');

        multiTg = function() {
          cssmenu.find(".has-sub").prepend('<span class="submenu-button"></span>');
          cssmenu.find('.submenu-button').on('click', function() {
            $(this).toggleClass('submenu-opened');
            if ($(this).siblings('ul').hasClass('open')) {
              $(this).siblings('ul').removeClass('open').hide();
            }
            else {
              $(this).siblings('ul').addClass('open').show();
            }
          });
        };

        if (settings.format === 'multitoggle') multiTg();
        else cssmenu.addClass('dropdown');

        if (settings.sticky === true) cssmenu.css('position', 'fixed');

        resizeFix = function() {
          if ($( window ).width() > 768) {
            cssmenu.find('ul').show();
          }

          if ($(window).width() <= 768) {
            cssmenu.find('ul').hide().removeClass('open');
          }
        };
        resizeFix();
        return $(window).on('resize', resizeFix);

      });
  };
})(jQuery);

(function($){
$(document).ready(function(){

$("#cssmenu").menumaker({
   title: "",
   format: "multitoggle"
});

});
})(jQuery);


/*----- slier --------*/

$('.slider').slick({
  autoplay: false,
  speed: 2000,
  lazyLoad: 'progressive',
  arrows: false,
  dots: false,
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

$('.featured_slider').slick({
  autoplay: false,
  speed: 2000,
  slidesToShow: 4,
  slidesToScroll: 1,
  lazyLoad: 'progressive',
  arrows: true,
  dots: false,
  prevArrow: '<div class="slick-nav prev-arrow"><i class="fa-solid fa-arrow-right-long"></i><svg><use xlink:href="#circle"></svg></div>',
  nextArrow: '<div class="slick-nav next-arrow"><i class="fa-solid fa-arrow-right-long"></i><svg><use xlink:href="#circle"></svg></div>',
  responsive: [
    {
      breakpoint: 1367,
      settings: {
        slidesToShow: 4,
        slidesToScroll: 1,
        dots: false
      }
    },
    {
      breakpoint: 1025,
      settings: {
        slidesToShow: 3,
        slidesToScroll: 1,
        dots: false
      }
    },
    {
      breakpoint: 769,
      settings: {
        slidesToShow: 2,
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

$('.slider-for').slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  arrows: false,
  fade: true,
  asNavFor: '.slider-nav'
});
$('.slider-nav').slick({
  slidesToShow: 4,
  slidesToScroll: 1,
  asNavFor: '.slider-for',
  dots: true,
  centerMode: false,
  centerPadding: '60px',
  focusOnSelect: true
});
/*----- slier --------*/
const rangeInput = document.querySelectorAll(".range-input input"),
  priceInput = document.querySelectorAll(".price-input input"),
  range = document.querySelector(".slider .progress");
let priceGap = 1000;

priceInput.forEach((input) => {
  input.addEventListener("input", (e) => {
    let minPrice = parseInt(priceInput[0].value),
      maxPrice = parseInt(priceInput[1].value);

    if (maxPrice - minPrice >= priceGap && maxPrice <= rangeInput[1].max) {
      if (e.target.className === "input-min") {
        rangeInput[0].value = minPrice;
        range.style.left = (minPrice / rangeInput[0].max) * 100 + "%";
      } else {
        rangeInput[1].value = maxPrice;
        range.style.right = 100 - (maxPrice / rangeInput[1].max) * 100 + "%";
      }
    }
  });
});

rangeInput.forEach((input) => {
  input.addEventListener("input", (e) => {
    let minVal = parseInt(rangeInput[0].value),
      maxVal = parseInt(rangeInput[1].value);

    if (maxVal - minVal < priceGap) {
      if (e.target.className === "range-min") {
        rangeInput[0].value = maxVal - priceGap;
      } else {
        rangeInput[1].value = minVal + priceGap;
      }
    } else {
      priceInput[0].value = minVal;
      priceInput[1].value = maxVal;
      range.style.left = (minVal / rangeInput[0].max) * 100 + "%";
      range.style.right = 100 - (maxVal / rangeInput[1].max) * 100 + "%";
    }
  });
});




/*---- qty ---------*/
var QtyInput = (function () {
	var $qtyInputs = $(".qty-input");

	if (!$qtyInputs.length) {
		return;
	}

	var $inputs = $qtyInputs.find(".product-qty");
	var $countBtn = $qtyInputs.find(".qty-count");
	var qtyMin = parseInt($inputs.attr("min"));
	var qtyMax = parseInt($inputs.attr("max"));

	$inputs.change(function () {
		var $this = $(this);
		var $minusBtn = $this.siblings(".qty-count--minus");
		var $addBtn = $this.siblings(".qty-count--add");
		var qty = parseInt($this.val());

		if (isNaN(qty) || qty <= qtyMin) {
			$this.val(qtyMin);
			$minusBtn.attr("disabled", true);
		} else {
			$minusBtn.attr("disabled", false);
			
			if(qty >= qtyMax){
				$this.val(qtyMax);
				$addBtn.attr('disabled', true);
			} else {
				$this.val(qty);
				$addBtn.attr('disabled', false);
			}
		}
	});

	$countBtn.click(function () {
		var operator = this.dataset.action;
		var $this = $(this);
		var $input = $this.siblings(".product-qty");
		var qty = parseInt($input.val());

		if (operator == "add") {
			qty += 1;
			if (qty >= qtyMin + 1) {
				$this.siblings(".qty-count--minus").attr("disabled", false);
			}

			if (qty >= qtyMax) {
				$this.attr("disabled", true);
			}
		} else {
			qty = qty <= qtyMin ? qtyMin : (qty -= 1);
			
			if (qty == qtyMin) {
				$this.attr("disabled", true);
			}

			if (qty < qtyMax) {
				$this.siblings(".qty-count--add").attr("disabled", false);
			}
		}

		$input.val(qty);
	});
})();

/*---- qty ---------*/

AOS.init();
