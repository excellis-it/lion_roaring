(function ($) {
    $.fn.menumaker = function (options) {
        var cssmenu = $(this),
            settings = $.extend(
                {
                    title: "Menu",
                    format: "dropdown",
                    sticky: false,
                },
                options
            );

        return this.each(function () {
            cssmenu.prepend(
                '<div id="menu-button">' + settings.title + "</div>"
            );
            $(this)
                .find("#menu-button")
                .on("click", function () {
                    $(this).toggleClass("menu-opened");
                    var mainmenu = $(this).next("ul");
                    if (mainmenu.hasClass("open")) {
                        mainmenu.hide().removeClass("open");
                    } else {
                        mainmenu.show().addClass("open");
                        if (settings.format === "dropdown") {
                            mainmenu.find("ul").show();
                        }
                    }
                });

            cssmenu.find("li ul").parent().addClass("has-sub");

            multiTg = function () {
                cssmenu
                    .find(".has-sub")
                    .prepend('<span class="submenu-button"></span>');
                cssmenu.find(".submenu-button").on("click", function () {
                    $(this).toggleClass("submenu-opened");
                    if ($(this).siblings("ul").hasClass("open")) {
                        $(this).siblings("ul").removeClass("open").hide();
                    } else {
                        $(this).siblings("ul").addClass("open").show();
                    }
                });
            };

            if (settings.format === "multitoggle") multiTg();
            else cssmenu.addClass("dropdown");

            if (settings.sticky === true) cssmenu.css("position", "fixed");

            resizeFix = function () {
                if ($(window).width() > 768) {
                    cssmenu.find("ul").show();
                }

                if ($(window).width() <= 768) {
                    cssmenu.find("ul").hide().removeClass("open");
                }
            };
            resizeFix();
            return $(window).on("resize", resizeFix);
        });
    };
})(jQuery);

(function ($) {
    $(document).ready(function () {
        $("#cssmenu").menumaker({
            title: "",
            format: "multitoggle",
        });
    });
})(jQuery);

/*----- slier --------*/

$(".slider").slick({
    autoplay: false,
    speed: 2000,
    lazyLoad: "progressive",
    arrows: false,
    dots: false,
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

$(".featured_slider").slick({
    autoplay: false,
    speed: 2000,
    slidesToShow: 4,
    slidesToScroll: 1,
    lazyLoad: "progressive",
    arrows: true,
    dots: false,
    prevArrow:
        '<div class="slick-nav prev-arrow"><i class="fa-solid fa-arrow-right-long"></i><svg><use xlink:href="#circle"></svg></div>',
    nextArrow:
        '<div class="slick-nav next-arrow"><i class="fa-solid fa-arrow-right-long"></i><svg><use xlink:href="#circle"></svg></div>',
    responsive: [
        {
            breakpoint: 1367,
            settings: {
                slidesToShow: 4,
                slidesToScroll: 1,
                dots: false,
            },
        },
        {
            breakpoint: 1025,
            settings: {
                slidesToShow: 3,
                slidesToScroll: 1,
                dots: false,
            },
        },
        {
            breakpoint: 769,
            settings: {
                slidesToShow: 2,
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

$(".slider-for").slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: false,
    fade: true,
    asNavFor: ".slider-nav",
});
$(".slider-nav").slick({
    slidesToShow: 4,
    slidesToScroll: 1,
    asNavFor: ".slider-for",
    dots: true,
    centerMode: false,
    centerPadding: "60px",
    focusOnSelect: true,
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
                range.style.right =
                    100 - (maxPrice / rangeInput[1].max) * 100 + "%";
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

// Update cart count function
function updateCartCount() {
    $.ajax({
        url: window.cartRoutes.cartCount,
        type: "GET",
        success: function (response) {
            if (response.status) {
                $(".cart_count").text(response.cartCount);
            }
        },
    });
}

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

        if (isNaN(qty) || qty < qtyMin) {
            $this.val(qtyMin);
            $minusBtn.attr("disabled", true);
        } else {
            $minusBtn.attr("disabled", false);

            if (qty >= qtyMax) {
                $this.val(qtyMax);
                $addBtn.attr("disabled", true);
            } else {
                $this.val(qty);
                $addBtn.attr("disabled", false);
            }
        }

        // Handle cart update on manual input change
        handleCartUpdate($this);
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

        // Handle cart update on button click
        handleCartUpdate($input);
    });

    // Function to handle cart updates
    function handleCartUpdate($input) {
        var cartId = $input.data("cart-id");
        var productId = $input.data("product-id");
        var quantity = parseInt($input.val());

        // Only proceed if we have a cart item or product
        if (!cartId && !productId) return;

        if (cartId) {
            // Update existing cart item
            if (quantity === 0) {
                // Remove item from cart
                removeCartItem(cartId, $input);
            } else {
                // Update cart quantity
                updateCartQuantity(cartId, quantity);
            }
        } else if (productId && quantity > 0) {
            // Add new item to cart
            addToCartFromDetails(productId, quantity, $input);
        }
    }

    // Remove cart item
    function removeCartItem(cartId, $input) {
        $.ajax({
            url: window.cartRoutes.removeFromCart,
            type: "POST",
            data: {
                id: cartId,
                _token: window.csrfToken,
            },
            success: function (response) {
                if (response.status) {
                    toastr.success("Item removed from cart");
                    updateCartCount();

                    // Reset UI to "Add to Cart" state
                    $input.data("cart-id", "");
                    $input.val(1);
                    $(".view-cart-btn").replaceWith(`
                        <div class="addtocart" data-id="${$input.data(
                            "product-id"
                        )}">
                            <a href="javascript:void(0);" class="red_btn w-100 text-center"><span>Add to Cart</span></a>
                        </div>
                    `);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function () {
                toastr.error("Failed to remove item from cart");
            },
        });
    }

    // Update cart quantity
    function updateCartQuantity(cartId, quantity) {
        $.ajax({
            url: window.cartRoutes.updateCart,
            type: "POST",
            data: {
                id: cartId,
                quantity: quantity,
                _token: window.csrfToken,
            },
            success: function (response) {
                if (response.status) {
                    toastr.success("Cart updated");
                    updateCartCount();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function () {
                toastr.error("Failed to update cart");
            },
        });
    }

    // Add to cart from details page
    function addToCartFromDetails(productId, quantity, $input) {
        $.ajax({
            url: window.cartRoutes.addToCart,
            type: "POST",
            data: {
                product_id: productId,
                quantity: quantity,
                _token: window.csrfToken,
            },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    updateCartCount();

                    // Get the cart ID from response or make another call to get it
                    getCartItemId(productId, $input);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function () {
                toastr.error("Failed to add item to cart");
            },
        });
    }

    // Get cart item ID after adding to cart
    function getCartItemId(productId, $input) {
        $.ajax({
            url: window.cartRoutes.checkProductInCart,
            type: "GET",
            data: {
                product_id: productId,
            },
            success: function (response) {
                if (response.status && response.inCart && response.cartItem) {
                    $input.data("cart-id", response.cartItem.id);

                    // Update UI to "View Cart" state
                    $(".addtocart").replaceWith(`
                        <div class="view-cart-btn">
                            <a href="{{route('e-store.cart')}}" class="red_btn w-100 text-center"><span>View Cart</span></a>
                        </div>
                    `);
                }
            },
        });
    }
})();

/*---- qty ---------*/

AOS.init();

/*---- Cart Handling --------*/
$(document).ready(function () {
    // Add to cart functionality
    $(document).on("click", ".addtocart", function (e) {
        e.preventDefault();

        var $button = $(this);
        var productId = $button.data("id");
        var buttonText = $button.find("a").text().trim();

        // If button shows "View Cart", redirect to cart
        if (buttonText === "View Cart") {
            window.location.href = window.cartRoutes.viewCart;
            return;
        }

        var quantity = 1;

        // Check if there's a quantity input nearby (for product details page)
        var qtyInput = $button.closest(".feature_box").find(".product-qty");
        if (qtyInput.length === 0) {
            qtyInput = $(".product-qty");
        }

        if (qtyInput.length > 0) {
            quantity = parseInt(qtyInput.val()) || 1;
        }

        // Show loading state
        var originalText = $button.find("a").text();
        $button.find("a").text("Adding...");
        $button.addClass("loading");

        $.ajax({
            url: window.cartRoutes.addToCart,
            type: "POST",
            data: {
                product_id: productId,
                quantity: quantity,
                _token: window.csrfToken,
            },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    updateCartCount();

                    // Update button text to "View Cart" if product was added/updated
                    if (
                        response.action === "added" ||
                        response.action === "updated"
                    ) {
                        $button.find("a").text("View Cart");

                        // For product details page, update the quantity input data
                        if (
                            qtyInput.length > 0 &&
                            qtyInput.data("product-id")
                        ) {
                            // Get cart item ID for the product details page
                            $.ajax({
                                url: window.cartRoutes.checkProductInCart,
                                type: "GET",
                                data: {
                                    product_id: productId,
                                },
                                success: function (checkResponse) {
                                    if (
                                        checkResponse.status &&
                                        checkResponse.inCart &&
                                        checkResponse.cartItem
                                    ) {
                                        qtyInput.data(
                                            "cart-id",
                                            checkResponse.cartItem.id
                                        );
                                        qtyInput.val(
                                            checkResponse.cartItem.quantity
                                        );
                                    }
                                },
                            });
                        }
                    }
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                var errorMessage = "Something went wrong!";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                toastr.error(errorMessage);
            },
            complete: function () {
                // Restore button state if not successful
                if (!$button.find("a").text().includes("View Cart")) {
                    $button.find("a").text(originalText);
                }
                $button.removeClass("loading");
            },
        });
    });

    // Update cart from product details page
    $(document).on("click", ".addtocart-update", function (e) {
        e.preventDefault();

        var $button = $(this);
        var cartId = $button.data("cart-id");
        var quantity = parseInt($(".product-qty").val()) || 1;

        // Show loading state
        var originalText = $button.find("a span").text();
        $button.find("a span").text("Updating...");
        $button.addClass("loading");

        $.ajax({
            url: window.cartRoutes.updateCart,
            type: "POST",
            data: {
                id: cartId,
                quantity: quantity,
                _token: window.csrfToken,
            },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    updateCartCount();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                toastr.error("Failed to update cart");
            },
            complete: function () {
                // Restore button state
                $button.find("a span").text(originalText);
                $button.removeClass("loading");
            },
        });
    });

    // Check product in cart on page load for product listing pages
    function checkProductsInCart() {
        $(".addtocart").each(function () {
            var $button = $(this);
            var productId = $button.data("id");

            $.ajax({
                url: window.cartRoutes.checkProductInCart,
                type: "GET",
                data: {
                    product_id: productId,
                },
                success: function (response) {
                    if (response.status && response.inCart) {
                        $button.find("a").text("View Cart");
                    }
                },
            });
        });
    }

    // Check products in cart on page load
    checkProductsInCart();

    // Remove from cart functionality
    $(document).on("click", ".remove-from-cart", function (e) {
        e.preventDefault();

        var cartId = $(this).data("id");
        var cartItem = $(this).closest(".cart-item");

        $.ajax({
            url: window.cartRoutes.removeFromCart,
            type: "POST",
            data: {
                id: cartId,
                _token: window.csrfToken,
            },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    cartItem.fadeOut(300, function () {
                        $(this).remove();
                    });
                    updateCartCount();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                toastr.error("Failed to remove item from cart");
            },
        });
    });

    // Update cart quantity
    $(document).on("change", ".cart-quantity", function () {
        var cartId = $(this).data("id");
        var quantity = $(this).val();

        $.ajax({
            url: window.cartRoutes.updateCart,
            type: "POST",
            data: {
                id: cartId,
                quantity: quantity,
                _token: window.csrfToken,
            },
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                    updateCartCount();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                toastr.error("Failed to update cart");
            },
        });
    });

    // Clear cart
    $(document).on("click", ".clear-cart", function (e) {
        e.preventDefault();

        if (confirm("Are you sure you want to clear your cart?")) {
            $.ajax({
                url: window.cartRoutes.clearCart,
                type: "POST",
                data: {
                    _token: window.csrfToken,
                },
                success: function (response) {
                    if (response.status) {
                        toastr.success(response.message);
                        $(".cart-items").empty();
                        updateCartCount();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    toastr.error("Failed to clear cart");
                },
            });
        }
    });

    // Load cart list functionality
    $(document).on("click", ".view-cart", function (e) {
        e.preventDefault();

        $.ajax({
            url: window.cartRoutes.cartList,
            type: "GET",
            success: function (response) {
                if (response.status) {
                    // Handle cart list display here
                    console.log(response.cartItems);
                } else {
                    toastr.error("Failed to load cart items");
                }
            },
            error: function (xhr, status, error) {
                toastr.error("Failed to load cart");
            },
        });
    });
});

// toggle wishlist
$(document).on("click", ".wishlist_icon", function (e) {
    e.preventDefault();

    var $button = $(this);
    var productId = $button.data("id");

    // Show loading state
    $button.addClass("loading");

    $.ajax({
        url: window.cartRoutes.addToWishlist,
        type: "POST",
        data: {
            product_id: productId,
            _token: window.csrfToken,
        },
        success: function (response) {
            if (response.status) {
                toastr.success(response.message);
                // Toggle button text based on response
                if (response.action === "added") {
                    // wishlist_icon have a tag with i class fa-solid fa-heart then set text-danger class
                    $button.find("i").addClass("text-danger");
                } else {
                    $button.find("i").removeClass("text-danger");
                }
            } else {
                toastr.error(response.message);
            }
        },
        error: function (xhr, status, error) {
            toastr.error("Failed to toggle wishlist");
        },
        complete: function () {
            $button.removeClass("loading");
        },
    });
});

// Remove from wishlist
$(document).on("click", ".remove-from-wishlist", function (e) {
    e.preventDefault();

    var $button = $(this);
    var productId = $button.data("id");

    // Show loading state
    $button.addClass("loading");

    $.ajax({
        url: window.cartRoutes.removeFromWishlist,
        type: "POST",
        data: {
            product_id: productId,
            _token: window.csrfToken,
        },
        success: function (response) {
            if (response.status) {
                toastr.success(response.message);
                // Remove the wishlist item from the UI
                $button.closest(".wishlist-item").fadeOut(300, function () {
                    $(this).remove();
                });
            } else {
                toastr.error(response.message);
            }
        },
        error: function (xhr, status, error) {
            toastr.error("Failed to remove from wishlist");
        },
        complete: function () {
            $button.removeClass("loading");
        },
    });
});
