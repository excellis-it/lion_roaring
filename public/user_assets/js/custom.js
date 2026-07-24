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
    // Prefer natural aspect: only cap size — never force width×height (that squash EXIF / wrong sources)
    window.fitChatMediaElement = function (el, maxW, maxH) {
        if (!el) {
            return;
        }
        el.style.setProperty("width", "auto", "important");
        el.style.setProperty("height", "auto", "important");
        el.style.setProperty("max-width", maxW + "px", "important");
        el.style.setProperty("max-height", maxH + "px", "important");
        el.style.setProperty("object-fit", "contain", "important");
    };

    window.fitChatBubbleImages = function (root) {
        var scope = root && root.querySelectorAll ? root : document;
        scope.querySelectorAll("img.chat-image-attachment").forEach(function (img) {
            var apply = function () {
                window.fitChatMediaElement(img, 280, 360);
            };
            if (img.complete && img.naturalWidth) {
                apply();
            } else {
                img.addEventListener("load", apply, { once: true });
            }
        });
    };

    // Capture-phase load: works for dynamically inserted chat images
    document.addEventListener(
        "load",
        function (e) {
            var t = e.target;
            if (t && t.tagName === "IMG" && t.classList.contains("chat-image-attachment")) {
                window.fitChatMediaElement(t, 280, 360);
            }
        },
        true
    );

    $(function () {
        window.fitChatBubbleImages(document);
    });

    // BUG-054: open chat images in preview modal (do not auto-download)
    $(document).on("click", "a.chat-image-preview", function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $link = $(this);
        var url =
            $link.data("image-url") ||
            $link.data("download-url") ||
            $link.attr("href");
        var fileName =
            $link.data("file-name") ||
            (url ? url.split("/").pop() : "") ||
            "image";
        if (!url) {
            return;
        }
        var $img = $("#chatImagePreviewImg");
        var $download = $("#chatImagePreviewDownload");
        var modalEl = document.getElementById("chatImagePreviewModal");
        if (!$img.length || !modalEl) {
            window.open(url, "_blank");
            return;
        }
        var imgEl = $img.get(0);
        // Reset before load so previous locked size doesn't distort the next image
        imgEl.removeAttribute("width");
        imgEl.removeAttribute("height");
        imgEl.style.removeProperty("width");
        imgEl.style.removeProperty("height");
        imgEl.style.removeProperty("max-width");
        imgEl.style.removeProperty("max-height");
        imgEl.style.removeProperty("object-fit");

        var applyPreviewSize = function () {
            // Height-first: tall images show fully; width shrinks → dark gaps L/R
            var stage = imgEl.parentElement;
            var stageW = stage ? stage.clientWidth - 48 : window.innerWidth * 0.88;
            var maxW = Math.max(120, Math.min(stageW, window.innerWidth * 0.88, 680));
            var maxH = Math.min(window.innerHeight * 0.78, 720);
            window.fitChatMediaElement(imgEl, maxW, maxH);
        };

        $img.off("load.chatPreview").on("load.chatPreview", applyPreviewSize);
        $img.attr("src", url).attr("alt", fileName || "Photo");
        if (imgEl.complete && imgEl.naturalWidth) {
            applyPreviewSize();
        }

        // Keep a short friendly title — avoid dumping raw storage filenames
        var title = "Photo";
        if (fileName && !/^Screenshot_\d+/i.test(fileName) && !/^[a-f0-9]{16,}/i.test(fileName)) {
            title = String(fileName).length > 40
                ? String(fileName).slice(0, 37) + "…"
                : String(fileName);
        }
        $("#chatImagePreviewModalLabel").text(title);
        $download
            .attr("href", url)
            .attr("data-download-url", url)
            .attr("data-file-name", fileName)
            .data("download-url", url)
            .data("file-name", fileName);
        var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        $(modalEl)
            .off("shown.bs.modal.chatPreview")
            .on("shown.bs.modal.chatPreview", function () {
                applyPreviewSize();
            });
        modal.show();
    });

    // Open chat videos in player modal (thumbnail click)
    $(document).on("click", ".chat-video-preview", function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $btn = $(this);
        var url = $btn.data("video-url") || $btn.attr("href");
        var fileName =
            $btn.data("file-name") ||
            (url ? String(url).split("/").pop() : "") ||
            "video";
        var mime = $btn.data("mime") || "video/mp4";
        if (!url) {
            return;
        }
        var player = document.getElementById("chatVideoPreviewPlayer");
        var modalEl = document.getElementById("chatVideoPreviewModal");
        var $download = $("#chatVideoPreviewDownload");
        if (!player || !modalEl) {
            window.open(url, "_blank");
            return;
        }
        $("#chatVideoPreviewModalLabel").text("Video");
        $download
            .attr("href", url)
            .attr("data-download-url", url)
            .attr("data-file-name", fileName)
            .data("download-url", url)
            .data("file-name", fileName);
        player.pause();
        player.removeAttribute("src");
        while (player.firstChild) {
            player.removeChild(player.firstChild);
        }
        var source = document.createElement("source");
        source.src = url;
        source.type = mime;
        player.appendChild(source);
        player.load();
        var sizeVideo = function () {
            var stage = player.parentElement;
            var stageW = stage ? stage.clientWidth - 48 : window.innerWidth * 0.88;
            var maxW = Math.max(120, Math.min(stageW, window.innerWidth * 0.88, 680));
            var maxH = Math.min(window.innerHeight * 0.78, 720);
            if (typeof window.fitChatMediaElement === "function") {
                window.fitChatMediaElement(player, maxW, maxH);
            }
        };
        player.addEventListener("loadedmetadata", sizeVideo, { once: true });
        var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        var playWhenShown = function () {
            modalEl.removeEventListener("shown.bs.modal", playWhenShown);
            sizeVideo();
            var playPromise = player.play();
            if (playPromise && typeof playPromise.catch === "function") {
                playPromise.catch(function () {});
            }
        };
        modalEl.addEventListener("shown.bs.modal", playWhenShown);
        modal.show();
    });

    $("#chatVideoPreviewModal").on("hidden.bs.modal", function () {
        var player = document.getElementById("chatVideoPreviewPlayer");
        if (!player) {
            return;
        }
        player.pause();
        player.removeAttribute("src");
        while (player.firstChild) {
            player.removeChild(player.firstChild);
        }
        player.load();
    });

    // Download with progress - global handler
    $(document).on("click", "a.file-download", function (e) {
        // BUG-049: let nested video/audio controls play instead of forcing download
        if ($(e.target).closest("video, audio").length) {
            return;
        }
        // Bubble video thumbnails open the player modal, not download
        if ($(this).closest(".chat-video-preview").length) {
            return;
        }
        // BUG-054: image preview links are handled separately
        if ($(this).hasClass("chat-image-preview")) {
            return;
        }
        e.preventDefault();
        e.stopPropagation();
        var $link = $(this);
        if ($link.data("downloading")) {
            return; // prevent concurrent downloads
        }
        // Prefer attr over .data() — jQuery caches initial empty data-download-url=""
        var url =
            $link.attr("data-download-url") ||
            $link.data("download-url") ||
            $link.attr("href");
        var fileName =
            $link.attr("data-file-name") ||
            $link.data("file-name") ||
            (url ? String(url).split("/").pop() : "") ||
            "file";
        if (!url || url === "#") {
            return;
        }

        var modalEl = document.getElementById("downloadProgressModal");
        if (!modalEl) {
            window.open(url, "_blank");
            return;
        }
        var modal = bootstrap.Modal.getOrCreateInstance(modalEl, {
            backdrop: true,
            keyboard: true,
            focus: true,
        });
        $("#downloadFileName").text(fileName);
        var $bar = $("#downloadProgressBar");
        if (!$bar.length) {
            $bar = $("#downloadProgressModal .progress-bar");
        }
        $bar
            .css("width", "8%")
            .attr("aria-valuenow", 0)
            .addClass("progress-bar-striped progress-bar-animated")
            .text("0%");
        $("#downloadProgressPercent").text("0%");

        var setDownloadProgress = function (percent, label) {
            percent = Math.max(0, Math.min(100, parseInt(percent, 10) || 0));
            var width = percent === 0 ? 8 : percent; // keep a visible stub at 0%
            $bar
                .css("width", width + "%")
                .attr("aria-valuenow", percent)
                .text(label || percent + "%");
            $("#downloadProgressPercent").text(label || percent + "%");
            if (percent >= 100) {
                $bar.removeClass("progress-bar-animated");
            }
        };

        // Stack above open image/video lightbox (Bootstrap does not stack nested modals by default)
        var elevateDownloadModal = function () {
            modalEl.classList.add("download-progress-on-top");
            modalEl.style.zIndex = "2100";
            var backdrops = document.querySelectorAll(".modal-backdrop");
            if (backdrops.length) {
                var bd = backdrops[backdrops.length - 1];
                bd.classList.add("download-progress-backdrop");
                bd.style.zIndex = "2090";
            }
        };

        $(modalEl)
            .off("shown.bs.modal.downloadStack")
            .one("shown.bs.modal.downloadStack", elevateDownloadModal);
        modal.show();
        setTimeout(elevateDownloadModal, 50);

        var triggerElement = $link.get(0);

        try {
            $(modalEl).one("hide.bs.modal", function () {
                try {
                    var activeEl = document.activeElement;
                    if (modalEl && activeEl && modalEl.contains(activeEl)) {
                        if (triggerElement && typeof triggerElement.focus === "function") {
                            triggerElement.focus();
                        } else if (activeEl && typeof activeEl.blur === "function") {
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
        // Hide ONLY the download modal — never wipe lightbox backdrop/state
        var hideModalClean = function () {
            try {
                var activeEl = document.activeElement;
                if (modalEl && activeEl && modalEl.contains(activeEl)) {
                    if (triggerElement && typeof triggerElement.focus === "function") {
                        triggerElement.focus();
                    } else if (activeEl && typeof activeEl.blur === "function") {
                        activeEl.blur();
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
            try {
                modalEl.classList.remove("download-progress-on-top");
                modalEl.style.zIndex = "";
                document
                    .querySelectorAll(".modal-backdrop.download-progress-backdrop")
                    .forEach(function (el) {
                        el.remove();
                    });
                // Keep body.modal-open if lightbox (or any other modal) is still visible
                if (document.querySelector(".modal.show:not(#downloadProgressModal)")) {
                    document.body.classList.add("modal-open");
                    document.body.style.removeProperty("overflow");
                    document.body.style.overflow = "hidden";
                }
            } catch (err) {
                /* ignore */
            }
        };

        xhr.open("GET", url, true);
        xhr.responseType = "blob";
        $link.data("downloading", true);

        xhr.onprogress = function (ev) {
            if (ev.lengthComputable && ev.total > 0) {
                var percent = Math.round((ev.loaded / ev.total) * 100);
                setDownloadProgress(percent);
            } else if (ev.loaded > 0) {
                setDownloadProgress(50, "Loading...");
            }
        };
        xhr.onload = function () {
            if (xhr.status >= 200 && xhr.status < 300) {
                setDownloadProgress(100);
                var blob = xhr.response;
                var downloadUrl = window.URL.createObjectURL(blob);
                var a = document.createElement("a");
                a.href = downloadUrl;
                a.download = fileName;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(downloadUrl);
                setTimeout(hideModalClean, 250);
                if (typeof toastr !== "undefined") {
                    toastr.success("Download complete");
                }
            } else {
                hideModalClean();
                if (typeof toastr !== "undefined") {
                    toastr.error("Download failed");
                } else {
                    alert("Download failed. Please try again.");
                }
            }
            $link.data("downloading", false);
        };
        xhr.onerror = function () {
            hideModalClean();
            $link.data("downloading", false);
            if (typeof toastr !== "undefined") {
                toastr.error("Download failed");
            } else {
                alert("An error occurred while downloading the file.");
            }
        };
        xhr.onabort = function () {
            hideModalClean();
            $link.data("downloading", false);
        };
        $("#downloadCancelBtn")
            .off("click")
            .on("click", function () {
                xhr.abort();
                hideModalClean();
                $link.data("downloading", false);
                if (typeof toastr !== "undefined") {
                    toastr.info("Download canceled");
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
