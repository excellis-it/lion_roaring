$(document).ready(function () {
    var notification_page = 1;
    var loading = false; // Prevents multiple simultaneous AJAX requests
    var authUserId = window.Laravel.authUserId;

    // remove notification dropdown when clicked outside
    $(document).on("click", function (e) {
        if ($("#show-notification-" + authUserId + " .showing").length > 0) {
            if (
                !$(e.target).closest("#show-notification-" + authUserId).length
            ) {
                $(".notification-dropdown").removeClass("show");
                $("#show-notification-" + authUserId).html(""); // Clear the notifications
                notification_page = 1;
            }
        }
    });

    $(document).on("click", "#drop2", function () {
        var $dropdown = $(".notification-dropdown");
        if ($dropdown.hasClass("show")) {
            // If the dropdown is already shown, hide it
            $dropdown.removeClass("show");
            $("#show-notification-" + authUserId).html(""); // Clear the notifications
            notification_page = 1;
        } else {
            $dropdown.addClass("show");
            loadMoreNotification(notification_page, true);
        }
    });

    $("#show-notification-" + authUserId).on("scroll", function () {
        loadingNotification();
    });

    function loadingNotification() {
        if (loading) return; // Exit if a load is already in progress

        var $container = $("#show-notification-" + authUserId);
        var lastItem = $(".message-body").last();
        var lastItemOffset = lastItem.offset().top + lastItem.outerHeight();
        var containerOffset = $container.scrollTop() + $container.innerHeight();

        if (containerOffset >= lastItemOffset) {
            loading = true;
            notification_page++;
            loadMoreNotification(notification_page, false);
        }
    }

    function loadMoreNotification(page, initialLoad) {
        loading = true;
        if (!initialLoad) {
            $("#show-notification-" + authUserId).append(
                '<div class="loader-topbar"></div>'
            );
        }
        $.ajax({
            url: window.Laravel.routes.notificationList,
            data: {
                page: page,
            },
            success: function (data) {
                if (page === 1) {
                    $("#show-notification-" + authUserId).html(data.view);
                } else {
                    $("#show-notification-" + authUserId).append(data.view);
                }

                if (data.count < 8) {
                    // Stop loading if there are fewer items than the threshold
                    $("#show-notification-" + authUserId).off("scroll");
                } else {
                    $("#show-notification-" + authUserId).on(
                        "scroll",
                        function () {
                            loadingNotification();
                        }
                    );
                }

                loading = false;
                $(".loader-topbar").remove();
            },
            error: function () {
                loading = false;
                $(".loader-topbar").remove();
            },
        });
    }
    // clear-all-notification
    $(document).on("click", ".clear-all-notification", function () {
        var $this = $(this);
        var $notification = $("#show-notification-" + authUserId);
        var $notificationCount = $("#show-notification-count-" + authUserId);
        var $notificationDropdown = $(".notification-dropdown");
        var $notificationDropdownContent =
            $notificationDropdown.find(".message-body");

        $.ajax({
            url: window.Laravel.routes.notificationClear,
            success: function (data) {
                if (data.status === true) {
                    $notification.html("");
                    $notificationCount.html("0");
                    $notificationDropdownContent.html("");
                    $notificationDropdown.removeClass("show");
                    notification_page = 1;
                    toastr.success(data.message);
                }
            },
        });
    });
});
