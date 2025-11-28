// Sidebar toggle for mobile (jQuery version)
$(document).ready(function () {
    const $sideNav = $(".sideNav2");
    const $userLists = $(".user-list");
    const $groupLists = $(".group-data");
    const $userListSection = $(".user-list-section");

    // When a user is clicked → hide sidebar
    $userLists.on("click", function () {
        $sideNav.addClass("hidden"); // hide sidebar
    });
    $groupLists.on("click", function () {
        $sideNav.addClass("hidden"); // hide sidebar
    });

    // When back button is clicked → show sidebar and scroll to user list section
    $(document).on("click", ".backButton", function () {
        $sideNav.removeClass("hidden"); // show sidebar again

        // Smooth scroll to user list section (if it exists)
        if ($userListSection.length) {
            $("html, body").animate(
                {
                    scrollTop: $userListSection.offset().top,
                },
                600
            ); // 600ms = smooth scroll speed
        }
    });
});

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
    // Download with progress - global handler
    $(document).on("click", "a.file-download", function (e) {
        e.preventDefault();
        var $link = $(this);
        if ($link.data("downloading")) {
            return; // prevent concurrent downloads
        }
        var url = $link.data("download-url") || $link.attr("href");
        var fileName =
            $link.data("file-name") || url.split("/").pop() || "file";
        if (!url) return (window.location.href = $link.attr("href"));

        var modalEl = document.getElementById("downloadProgressModal");
        var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        $("#downloadFileName").text(fileName);
        $("#downloadProgressModal .progress-bar")
            .css("width", "0%")
            .attr("aria-valuenow", 0)
            .text("0%");
        modal.show();
        // Ensure focus moves out of the modal before it is hidden (e.g., user clicks close)
        try {
            $(modalEl).one("hide.bs.modal", function () {
                try {
                    var activeEl = document.activeElement;
                    if (modalEl && activeEl && modalEl.contains(activeEl)) {
                        if (
                            typeof triggerElement !== "undefined" &&
                            triggerElement &&
                            typeof triggerElement.focus === "function"
                        ) {
                            triggerElement.focus();
                        } else if (
                            typeof document !== "undefined" &&
                            document.body &&
                            typeof document.body.focus === "function"
                        ) {
                            document.body.focus();
                        } else if (
                            activeEl &&
                            typeof activeEl.blur === "function"
                        ) {
                            activeEl.blur();
                        }
                    }
                } catch (err) {
                    /* ignore */
                }
            });
        } catch (err) {
            /* ignore */
        }

        var xhr = new XMLHttpRequest();
        // helper: hide modal and remove backdrop reliably
        var hideModalClean = function () {
            // Ensure focus is moved out of the modal before hiding so aria-hidden won't be set
            try {
                var activeEl = document.activeElement;
                if (modalEl && activeEl && modalEl.contains(activeEl)) {
                    try {
                        if (
                            typeof triggerElement !== "undefined" &&
                            triggerElement &&
                            typeof triggerElement.focus === "function"
                        ) {
                            triggerElement.focus();
                        } else if (
                            typeof document !== "undefined" &&
                            document.body &&
                            typeof document.body.focus === "function"
                        ) {
                            document.body.focus();
                        } else if (
                            activeEl &&
                            typeof activeEl.blur === "function"
                        ) {
                            activeEl.blur();
                        }
                    } catch (e) {
                        /* ignore */
                    }
                }
            } catch (err) {
                /* ignore */
            }
            try {
                modal.hide();
            } catch (err) {
                /* ignore */
            }
            // Attach an event listener to cleanup on hidden (Bootstrap trigger), helps avoid race conditions
            try {
                var onHidden = function () {
                    try {
                        document
                            .querySelectorAll(".modal-backdrop")
                            .forEach(function (el) {
                                el.remove();
                            });
                        document.body.classList.remove("modal-open");
                    } catch (err) {}
                    try {
                        modalEl.removeEventListener(
                            "hidden.bs.modal",
                            onHidden
                        );
                    } catch (err) {}
                };
                modalEl.addEventListener("hidden.bs.modal", onHidden);
            } catch (err) {
                /* ignore */
            }
            // Fallback cleanup in case the above event doesn't fire (older bootstrap versions / race conditions)
            setTimeout(function () {
                try {
                    document
                        .querySelectorAll(".modal-backdrop")
                        .forEach(function (el) {
                            el.remove();
                        });
                    document.body.classList.remove("modal-open");
                } catch (err) {
                    /* ignore */
                }
            }, 500);
        };
        xhr.open("GET", url, true);
        xhr.responseType = "blob";
        $link.data("downloading", true);
        // store trigger element for accessibility focus restore
        var triggerElement = $link.get(0);

        xhr.onprogress = function (e) {
            if (e.lengthComputable) {
                var percent = Math.round((e.loaded / e.total) * 100);
                $("#downloadProgressModal .progress-bar")
                    .css("width", percent + "%")
                    .attr("aria-valuenow", percent)
                    .text(percent + "%");
            } else {
                $("#downloadProgressModal .progress-bar")
                    .css("width", "50%")
                    .attr("aria-valuenow", 50)
                    .text("Loading...");
            }
        };
        xhr.onload = function (e) {
            if (xhr.status >= 200 && xhr.status < 300) {
                var blob = xhr.response;
                var downloadUrl = window.URL.createObjectURL(blob);
                var a = document.createElement("a");
                a.href = downloadUrl;
                a.download = fileName;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(downloadUrl);
                try {
                    if (
                        triggerElement &&
                        typeof triggerElement.focus === "function"
                    ) {
                        triggerElement.focus();
                    } else if (
                        typeof document !== "undefined" &&
                        document.body
                    ) {
                        document.body.focus();
                    }
                } catch (err) {
                    /* ignore */
                }
                // Try to hide modal and clean backdrop immediately and once more after a short delay.
                hideModalClean();
                // In some browsers/'bootstrap' setups modal may sometimes not close correctly; attempt again and remove backdrop.
                setTimeout(function () {
                    // Use the unified cleanup which also moves focus safely
                    hideModalClean();
                    try {
                        var backdrops =
                            document.querySelectorAll(".modal-backdrop");
                        backdrops.forEach(function (el) {
                            el.remove();
                        });
                        document.body.classList.remove("modal-open");
                    } catch (err) {
                        /* ignore */
                    }
                }, 350);
                if (typeof toastr !== "undefined") {
                    toastr.success("Download complete");
                    // Extra ensure modal closure after the toast is shown
                    setTimeout(function () {
                        hideModalClean();
                    }, 350);
                }
            } else {
                try {
                    if (
                        triggerElement &&
                        typeof triggerElement.focus === "function"
                    ) {
                        triggerElement.focus();
                    } else if (
                        typeof document !== "undefined" &&
                        document.body
                    ) {
                        document.body.focus();
                    }
                } catch (err) {
                    /* ignore */
                }
                hideModalClean();
                setTimeout(function () {
                    hideModalClean();
                    try {
                        document
                            .querySelectorAll(".modal-backdrop")
                            .forEach(function (el) {
                                el.remove();
                            });
                        document.body.classList.remove("modal-open");
                    } catch (err) {
                        /* ignore */
                    }
                }, 350);
                alert("Download failed. Please try again.");
            }
        };
        xhr.onerror = function () {
            try {
                if (
                    triggerElement &&
                    typeof triggerElement.focus === "function"
                ) {
                    triggerElement.focus();
                } else if (typeof document !== "undefined" && document.body) {
                    document.body.focus();
                }
            } catch (err) {
                /* ignore */
            }
            hideModalClean();
            setTimeout(function () {
                hideModalClean();
                try {
                    document
                        .querySelectorAll(".modal-backdrop")
                        .forEach(function (el) {
                            el.remove();
                        });
                    document.body.classList.remove("modal-open");
                } catch (err) {
                    /* ignore */
                }
            }, 350);
            alert("An error occurred while downloading the file.");
        };
        // Ensure the modal will hide once the request fully ends, even if onload didn't fire
        xhr.onloadend = function () {
            try {
                if (
                    triggerElement &&
                    typeof triggerElement.focus === "function"
                ) {
                    triggerElement.focus();
                } else if (typeof document !== "undefined" && document.body) {
                    document.body.focus();
                }
            } catch (err) {
                /* ignore */
            }
            hideModalClean();
            setTimeout(function () {
                hideModalClean();
                try {
                    document
                        .querySelectorAll(".modal-backdrop")
                        .forEach(function (el) {
                            el.remove();
                        });
                    document.body.classList.remove("modal-open");
                } catch (err) {
                    /* ignore */
                }
            }, 350);
            try {
                $link.data("downloading", false);
            } catch (ex) {
                /* ignore */
            }
        };
        // Abort support from user
        $("#downloadCancelBtn")
            .off("click")
            .on("click", function () {
                xhr.abort();
                try {
                    if (
                        triggerElement &&
                        typeof triggerElement.focus === "function"
                    ) {
                        triggerElement.focus();
                    } else if (
                        typeof document !== "undefined" &&
                        document.body
                    ) {
                        document.body.focus();
                    }
                } catch (err) {
                    /* ignore */
                }
                hideModalClean();
                setTimeout(function () {
                    hideModalClean();
                    try {
                        document
                            .querySelectorAll(".modal-backdrop")
                            .forEach(function (el) {
                                el.remove();
                            });
                        document.body.classList.remove("modal-open");
                    } catch (err) {
                        /* ignore */
                    }
                }, 350);
                if (typeof toastr !== "undefined")
                    toastr.info("Download canceled");
                try {
                    $link.data("downloading", false);
                } catch (ex) {
                    /* ignore */
                }
            });
        xhr.send();
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
