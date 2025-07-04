function toggleTheme(a) {
    $(".preloader").show(),
        (document.getElementById("themeColors").href = a),
        $(".preloader").fadeOut();
}
$(function () {
    "use strict";
    [].slice
        .call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        .map(function (a) {
            return new bootstrap.Tooltip(a);
        }),
        [].slice
            .call(document.querySelectorAll('[data-bs-toggle="popover"]'))
            .map(function (a) {
                return new bootstrap.Popover(a);
            });
    $(".minus,.add").on("click", function () {
        var a = $(this).closest("div").find(".qty"),
            e = parseInt(a.val()),
            t = $(this).hasClass("add");
        !isNaN(e) && a.val(t ? ++e : e > 0 ? --e : e);
    }),
        $('a[data-action="collapse"]').on("click", function (a) {
            a.preventDefault(),
                $(this)
                    .closest(".card")
                    .find('[data-action="collapse"] i')
                    .toggleClass("ti-minus ti-plus"),
                $(this)
                    .closest(".card")
                    .children(".card-body")
                    .collapse("toggle");
        }),
        $('a[data-action="expand"]').on("click", function (a) {
            a.preventDefault(),
                $(this)
                    .closest(".card")
                    .find('[data-action="expand"] i')
                    .toggleClass("ti-arrows-maximize ti-arrows-maximize"),
                $(this).closest(".card").toggleClass("card-fullscreen");
        }),
        $('a[data-action="close"]').on("click", function () {
            $(this).closest(".card").removeClass().slideUp("fast");
        }),
        $(window).scroll(function () {
            $(window).scrollTop() >= 60
                ? $(".app-header").addClass("fixed-header")
                : $(".app-header").removeClass("fixed-header");
        }),
        $(function () {
            $(".billing-address").click(function () {
                $(".billing-address-content").hide();
            }),
                $(".billing-address").click(function () {
                    $(".payment-method-list").show();
                });
        });
}),
    $(".full-width").click(function () {
        $(".container-fluid").addClass("mw-100"),
            $(".full-width i").addClass("text-primary"),
            $(".boxed-width i").removeClass("text-primary");
    }),
    $(".boxed-width").click(function () {
        $(".container-fluid").removeClass("mw-100"),
            $(".full-width i").removeClass("text-primary"),
            $(".boxed-width i").addClass("text-primary");
    }),
    $(".light-logo").hide(),
    $(".dark-theme").click(function () {
        $("nav.navbar-light").addClass("navbar-dark"),
            $(".dark-theme i").addClass("text-primary"),
            $(".light-theme i").removeClass("text-primary"),
            $(".light-logo").show(),
            $(".dark-logo").hide();
    }),
    $(".light-theme").click(function () {
        $("nav.navbar-light").removeClass("navbar-dark"),
            $(".dark-theme i").removeClass("text-primary"),
            $(".light-theme i").addClass("text-primary"),
            $(".light-logo").hide(),
            $(".dark-logo").show();
    }),
    $(".cardborder").click(function () {
        $("body").addClass("cardwithborder"),
            $(".cardshadow i").addClass("text-dark"),
            $(".cardborder i").addClass("text-primary");
    }),
    $(".cardshadow").click(function () {
        $("body").removeClass("cardwithborder"),
            $(".cardborder i").removeClass("text-primary"),
            $(".cardshadow i").removeClass("text-dark");
    }),
    $(".change-colors li a").click(function () {
        $(".change-colors li a").removeClass("active-theme"),
            $(this).addClass("active-theme");
    }),
    $(".preloader").fadeOut();





