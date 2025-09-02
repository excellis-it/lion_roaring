$(document).on("click", "div.showcity", function (e) {
    $(this).find('.citylist').show();

    $(this).find('.citysearch').focus();
    e.stopPropagation();
});



$(".citylist").click(function (e) {
    e.stopPropagation();
});



$(document).mouseup(function (e) {
    var container = $(".citylist");

    // if the target of the click isn't the container nor a descendant of the container
    if (!container.is(e.target) && container.has(e.target).length === 0) {
        container.hide();
    }
});





// 22/12/2021 for class passenger update


$(document).on("click", "a.passenger_class_update_click", function (e) {
    $('.class_passeger_update').show();
    e.stopPropagation();
});

$(document).mouseup(function (e) {
    var container = $(".class_passeger_update");
    if (!container.is(e.target) && container.has(e.target).length === 0) {
        container.hide();
    }
});




$(document).on("click", "div.showreturncity", function (e) {
    $(this).find('.returncitylist').show();

    $(this).find('.returncitysearch').focus();
    e.stopPropagation();
});

$(".returncitylist").click(function (e) {
    e.stopPropagation();
});

/*$(document).click(function () {
    $(".returncitylist").hide();
});*/


$(document).mouseup(function (e) {
    var container = $(".returncitylist");

    // if the target of the click isn't the container nor a descendant of the container
    if (!container.is(e.target) && container.has(e.target).length === 0) {
        container.hide();
    }
});




$('.departure_date').datepicker({
    format: 'dd.mm.yyyy',
    numberOfMonths: 2,
    minDate: new Date(),
    autoclose: true
}).on('changeDate', function (e) {
    //on change of date on start datepicker, set end datepicker's date
    $('.departure_date').datepicker('setStartDate', e.date)

});

$(document).ready(function () {
    $(".mobile_menu_btn").click(function () {
        $("#navbar").toggleClass("menu_open");
    });
});







$(document).ready(function () {
    $(".addCF").click(function () {
        var multicitydiv = $(".multicity_row").length;

        if (multicitydiv < 5) {


            $("#customFields").append('<div class="multicity_row"> <div class="from_to_area mr-3"> <div class="onway_from showcity ps-3 pe-3"> <label for="fromCity"> <span class="flight_label">From</span> <input id="fromCity" type="text" placeholder="" value="Kolkata"/> <p>CCU,Netaji Bose Subhas chandra Bose Internation Airport</p></label> <div class="citylist city_show" style="display: none"> <div class="city_list_search"> <input type="text" autocomplete="off" class="citysearch" placeholder="From"/> </div><div class="city_list"> <h4>Popular City</h4> <li> <img src="img/ic-flight-onward.png"/> <div class="city_name"> <p>Kolkata, India</p><h5>Netaji Subhas Chandra Internation Airport</h5> </div><div class="city_code">CCU</div></li></div></div></div><span class="swap_flight"><i class="ph ph-arrows-left-right"></i></span> <div class="onway_from ps-3 showreturncity"> <label for="toCity"> <span class="flight_label">To</span> <input id="toCity" type="text" placeholder="" value="Select City"/> <p>Airport Name</p></label> <div class="returncitylist city_show" style="display: none"> <div class="city_list_search"> <input type="text" autocomplete="off" class="returncitysearch"/> </div><div class="city_list"> <h4>Popular City</h4> <li> <img src="img/ic-flight-onward.png"/> <div class="city_name"> <p>Kolkata, India</p><h5>Netaji Subhas Chandra Internation Airport</h5> </div><div class="city_code">CCU</div></li></div></div></div></div><div class="onway_calender_area"> <div class="date_select mr-3 ps-3 pe-3"> <label for="selectcal" class="fm_datepicker" data-provide="datepicker"> <span class="flight_label">Departure</span> <p> <span class="shortdate">28 </span> <span class="shortmonth">Nov</span> <span class="shortYear">21</span> </p><input class="departure_date fm_datepicker" data-provide="datepicker" id="selectcal" type="text" placeholder="" value="Kolkata"/> <p class="fulldayname">Monday</p></label> </div></div><div class="add_multi_row"> <button class="remCF"><i class="ph ph-x"></i></button> </div></div>');


        } else {

        }





    });




    $("#customFields").on('click', '.remCF', function () {
        $(this).parent().parent().remove();
    });
});









$(document).ready(function () {
    $(".addMC").click(function () {
        $("#mulcity_field_add").append('<div class="oneway_search_enq_details mt-3"> <div class="enq_from_to"> <div class="enq_from showcity"> <label><i class="ph-airplane-tilt"></i> From</label> <input type="text" value="Kolkata"/> <div class="citylist city_show" style="display: none"> <div class="city_list_search"> <input type="text" autocomplete="off" class="citysearch" placeholder="From"/> </div><div class="city_list"> <h4>Popular City</h4> <li> <div class="city_name"> <p>Kolkata, India</p><h5>Netaji Subhas Chandra Internation Airport</h5> </div><div class="city_code">CCU</div></li><li> <div class="city_name"> <p>Kolkata, India</p><h5>Netaji Subhas Chandra Internation Airport</h5> </div><div class="city_code">CCU</div></li><li> <div class="city_name"> <p>Kolkata, India</p><h5>Netaji Subhas Chandra Internation Airport</h5> </div><div class="city_code">CCU</div></li><li> <div class="city_name"> <p>Kolkata, India</p><h5>Netaji Subhas Chandra Internation Airport</h5> </div><div class="city_code">CCU</div></li><li> <div class="city_name"> <p>Kolkata, India</p><h5>Netaji Subhas Chandra Internation Airport</h5> </div><div class="city_code">CCU</div></li><li> <div class="city_name"> <p>Kolkata, India</p><h5>Netaji Subhas Chandra Internation Airport</h5> </div><div class="city_code">CCU</div></li><li> <div class="city_name"> <p>Kolkata, India</p><h5>Netaji Subhas Chandra Internation Airport</h5> </div><div class="city_code">CCU</div></li></div></div></div><div class="enq_flight_rev"> <span><i class="ph-swap"></i></span> </div><div class="enq_from showreturncity mr-3"> <label><i class="ph-airplane-tilt"></i> To</label> <input type="text" value="Delhi"/> <div class="returncitylist city_show" style="display: none"> <div class="city_list_search"> <input type="text" autocomplete="off" class="citysearch" placeholder="From"/> </div><div class="city_list"> <h4>Popular City</h4> <li> <div class="city_name"> <p>Kolkata, India</p><h5>Netaji Subhas Chandra Internation Airport</h5> </div><div class="city_code">CCU</div></li><li> <div class="city_name"> <p>Kolkata, India</p><h5>Netaji Subhas Chandra Internation Airport</h5> </div><div class="city_code">CCU</div></li><li> <div class="city_name"> <p>Kolkata, India</p><h5>Netaji Subhas Chandra Internation Airport</h5> </div><div class="city_code">CCU</div></li><li> <div class="city_name"> <p>Kolkata, India</p><h5>Netaji Subhas Chandra Internation Airport</h5> </div><div class="city_code">CCU</div></li><li> <div class="city_name"> <p>Kolkata, India</p><h5>Netaji Subhas Chandra Internation Airport</h5> </div><div class="city_code">CCU</div></li><li> <div class="city_name"> <p>Kolkata, India</p><h5>Netaji Subhas Chandra Internation Airport</h5> </div><div class="city_code">CCU</div></li><li> <div class="city_name"> <p>Kolkata, India</p><h5>Netaji Subhas Chandra Internation Airport</h5> </div><div class="city_code">CCU</div></li></div></div></div></div><div class="enq_flight_other"> <div class="enq_from mr-3"> <label><i class="ph-calendar-blank"></i> Departure</label> <input class="fm_datepicker departure_date" data-provide="datepicker" type="text" value="23, Nov"/> </div></div><div class="add_multi_row"><button class="remMC"><i class="ph-trash"></i> Remove</button></div></div>');
    });
    $("#mulcity_field_add").on('click', '.remMC', function () {
        $(this).parent().parent().remove();
    });
});




$(document).ready(function () {
    var carousel = $("#flight_matrix");
    carousel.owlCarousel({
        nav: true,
        autoplay: true,
        dots: false,
        items: 8,
        navText: [
      "<i class='ph-caret-left'></i>",
      "<i class='ph-caret-right'></i>"
    ],
        responsive: {
            0: {
                items: 2
            },
            600: {
                items: 2,
                nav: false,
                dots: false
            },
            800: {
                items: 4,
                nav: false,
                dots: false
            },
            1024: {
                items: 8
            }
        }
    });
});


$(document).ready(function () {
    $('input[name="update_flight"]').click(function () {
        $(this).tab('show');
        $(this).removeClass('active');
    });
})


$(function () {
    $("#sof").click(function () {

        if ($(".sof_sec").is(":visible")) {
            $(".sof_sec").hide();
        } else {
            $(".sof_sec").show();
        }


    });

});


// $(document).ready(function () {
//     $('#payment_table').DataTable({
//         "paging": false,
//         "ordering": false,
//         "info": true
//     });
// });

$(document).ready(function () {
    $("select").change(function () {
        $(this).find("option:selected").each(function () {
            var optionValue = $(this).attr("value");
            if (optionValue) {
                $(".box").not("." + optionValue).hide();
                $("." + optionValue).show();
            } else {
                $(".box").hide();
            }
        });
    }).change();
});


$(document).ready(function () {
    $('.trigger').click(function () {
        $('.modal-open1').toggleClass('open');
        return false;
    });
});

function toggledisplay(elementID) {
    (function (style) {
        style.display = style.display === 'block' ? '' : 'block';
    })(document.getElementById(elementID).style);
}





// tabbed content
// http://www.entheosweb.com/tutorials/css/tabs.asp
$(".tab_content").hide();
$(".tab_content:first").show();

/* if in tab mode */
$("ul.tabs li").click(function () {

    $(".tab_content").hide();
    var activeTab = $(this).attr("rel");
    $("#" + activeTab).fadeIn();

    $("ul.tabs li").removeClass("active");
    $(this).addClass("active");

    $(".tab_drawer_heading").removeClass("d_active");
    $(".tab_drawer_heading[rel^='" + activeTab + "']").addClass("d_active");

    /*$(".tabs").css("margin-top", function(){ 
       return ($(".tab_container").outerHeight() - $(".tabs").outerHeight() ) / 2;
    });*/
});
$(".tab_container").css("min-height", function () {
    return $(".tabs").outerHeight() + 50;
});
/* if in drawer mode */
$(".tab_drawer_heading").click(function () {

    $(".tab_content").hide();
    var d_activeTab = $(this).attr("rel");
    $("#" + d_activeTab).fadeIn();

    $(".tab_drawer_heading").removeClass("d_active");
    $(this).addClass("d_active");

    $("ul.tabs li").removeClass("active");
    $("ul.tabs li[rel^='" + d_activeTab + "']").addClass("active");
});

// $(document).ready(function () {
//     $('#payment_table').DataTable({
//         "paging": false,
//         "ordering": false,
//         "info": true
//     });
// });

/* Extra class "tab_last" 
   to add border to bottom side
   of last tab 
$('ul.tabs li').last().addClass("tab_last");*/


 // Range
 let min = 10;
 let max = 100;

 const calcLeftPosition = value => 100 / (100 - 10) * (value - 10);

 $('.rangeMin').on('input', function (e) {
     const newValue = parseInt(e.target.value);
     if (newValue > max) return;
     min = newValue;
     $('.thumbMin').css('left', calcLeftPosition(newValue) + '%');
     $('.min').html(newValue);
     $('.line-1').css({
         'left': calcLeftPosition(newValue) + '%',
         'right': (100 - calcLeftPosition(max)) + '%'
     });
 });

 $('.rangeMax').on('input', function (e) {
     const newValue = parseInt(e.target.value);
     if (newValue < min) return;
     max = newValue;
     $('.thumbMax').css('left', calcLeftPosition(newValue) + '%');
     $('.max').html(newValue);
     $('.line-1').css({
         'left': calcLeftPosition(min) + '%',
         'right': (100 - calcLeftPosition(newValue)) + '%'
     });
 });

 

 
