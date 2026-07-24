window.formatNotificationBadgeCount = function (count) {
    count = parseInt(count, 10) || 0;
    if (count <= 0) {
        return "0";
    }
    return count > 99 ? "99+" : String(count);
};

window.setNotificationBadgeCount = function (userId, count) {
    count = parseInt(count, 10) || 0;
    var $el = $("#show-notification-count-" + userId);
    if (!$el.length) {
        return;
    }
    $el.attr("data-count", count).text(
        window.formatNotificationBadgeCount(count)
    );
    var $wrap = $el.hasClass("round-note") ? $el : $el.closest(".round-note");
    if (count > 0) {
        $wrap.css("display", "flex");
    } else {
        $wrap.hide();
    }
};

window.incrementNotificationBadgeCount = function (userId, by) {
    by = by == null ? 1 : by;
    var $el = $("#show-notification-count-" + userId);
    if (!$el.length) {
        return;
    }
    var current = parseInt($el.attr("data-count"), 10);
    if (isNaN(current)) {
        current = parseInt($el.text(), 10) || 0;
    }
    window.setNotificationBadgeCount(userId, current + by);
};

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
                    if (typeof window.setNotificationBadgeCount === "function") {
                        window.setNotificationBadgeCount(authUserId, 0);
                    } else {
                        $notificationCount.attr("data-count", 0).html("0");
                        $notificationCount.closest(".round-note").hide();
                    }
                    $notificationDropdownContent.html("");
                    $notificationDropdown.removeClass("show");
                    notification_page = 1;
                    toastr.success(data.message);
                }
            },
        });
    });
});
