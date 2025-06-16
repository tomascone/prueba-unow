$(function() {
    let icon = $(".wishlist-button-product i");
    let wishlistButton = $(".wishlist-button-product");

    if (icon.text() === "favorite_border") {
        wishlistButton.prepend($("#wishlist_button_add"));
        $("#wishlist_button_add").show();
        icon.appendTo("#wishlist_button_add");
    } else {
        wishlistButton.prepend($("#wishlist_button_remove"));
        $("#wishlist_button_remove").show();
        icon.appendTo("#wishlist_button_remove");
    }
});