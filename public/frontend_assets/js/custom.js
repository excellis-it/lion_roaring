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
            breakpoint: 1440,
            settings: {
                slidesToShow: 4,
                slidesToScroll: 1,
                infinite: true,
                dots: false,
            },
        },
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

$(".reviews_slider").slick({
    autoplay: true,
    speed: 2000,
    lazyLoad: "progressive",
    arrows: false,
    dots: false,
    slidesToShow: 1,
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

    function computeInitials(name) {
        if (!name) return "";
        const parts = name.trim().split(/\s+/).filter(Boolean);
        return parts
            .map((p) => (p[0] || "").toUpperCase())
            .join("")
            .slice(0, 4);
    }

    function updateInitialLabel() {
        const name = $("#pma_register_signer_name").val() || "";
        const initials = computeInitials(name);
        if (initials) {
            $("#pma_register_initial_label").text(
                "I confirm my initials: " + initials
            );
        } else {
            $("#pma_register_initial_label").text("I confirm my initials");
        }
    }

    $(document).on("input", "#pma_register_signer_name", function () {
        updateInitialLabel();
        $("#pma_register_initial_check").prop("checked", false);
    });

    // Step 2: generate filled agreement PDF and show preview modal
    $(".register_next_second").on("click", function () {
        const signerName = ($("#pma_register_signer_name").val() || "").trim();
        if (!signerName) {
            toastr.error("Please enter your full name");
            return;
        }

        updateInitialLabel();

        if (!$("#pma_register_initial_check").is(":checked")) {
            toastr.error("Please confirm your initials");
            return;
        }

        const btn = $(this);
        btn.addClass("disabled");

        $.ajax({
            url:
                typeof register_agreement_preview_route !== "undefined"
                    ? register_agreement_preview_route
                    : "/register-agreement/preview",
            method: "POST",
            headers: {
                "X-CSRF-TOKEN":
                    typeof csrf_token !== "undefined" ? csrf_token : undefined,
            },
            data: {
                signer_name: signerName,
            },
        })
            .done(function (res) {
                if (!res || res.status !== true || !res.pdf_url) {
                    toastr.error(
                        "Could not generate agreement preview. Please try again."
                    );
                    return;
                }

                $("#register_agreement_pdf_iframe").attr("src", res.pdf_url);
                $("#pma_register_check3").prop("checked", false);

                $("#registerModalSecond").modal("hide");
                $("#registerAgreementPreviewModal").modal("show");
            })
            .fail(function (xhr) {
                const msg =
                    (xhr &&
                        xhr.responseJSON &&
                        (xhr.responseJSON.message || xhr.responseJSON.error)) ||
                    "Could not generate agreement preview. Please try again.";
                toastr.error(msg);
            })
            .always(function () {
                btn.removeClass("disabled");
            });
    });

    // Preview step: require agreement checkbox then go to register page
    $(".register_next_preview").on("click", function () {
        if (!$("#pma_register_check3").is(":checked")) {
            toastr.error("Please check the agreement");
            return;
        }
        window.location.href = register_page_route;
    });
});

AOS.init();

document.addEventListener("DOMContentLoaded", function () {
    const pages = document.querySelectorAll(".page");

    /* Set z-index */
    pages.forEach((page, index) => {
        if (index % 2 === 0) {
            page.style.zIndex = pages.length - index;
        }
        page.pageNum = index + 1;
    });

    /* 🔥 OPEN BOOK BY DEFAULT */
    pages[0].classList.add("flipped");
    pages[1].classList.add("flipped");

    /* Helper: check if book is fully closed */
    function isBookClosed() {
        return (
            !pages[0].classList.contains("flipped") &&
            !pages[1].classList.contains("flipped")
        );
    }

    /* Click logic */
    pages.forEach((page) => {
        page.addEventListener("click", function () {
            /* ✅ FIRST PAGE CLICK — reopen book if closed */
            if (this.pageNum === 1) {
                if (isBookClosed()) {
                    pages[0].classList.add("flipped");
                    pages[1].classList.add("flipped");
                }
                return;
            }

            /* EVEN pages → close */
            if (this.pageNum % 2 === 0) {
                this.classList.remove("flipped");
                if (this.previousElementSibling) {
                    this.previousElementSibling.classList.remove("flipped");
                }
            } else {
            /* ODD pages → open */
                this.classList.add("flipped");
                if (this.nextElementSibling) {
                    this.nextElementSibling.classList.add("flipped");
                }
            }
        });
    });
});
