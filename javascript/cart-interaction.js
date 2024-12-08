// Open the cart by adding 'active' class to both the overlay and cart panel
function openCart() {
    document.getElementById("overlay").classList.add("active");
    document.getElementById("cartPanel").classList.add("active");
}

// Close the cart by removing 'active' class from both the overlay and cart panel
function closeCart() {
    document.getElementById("overlay").classList.remove("active");
    document.getElementById("cartPanel").classList.remove("active");
}

// Close the cart when clicking outside of it (on the overlay)
document.getElementById("overlay").addEventListener("click", function(event) {
    // Check if the clicked element is the overlay, and if so, close the cart
    if (event.target === document.getElementById("overlay")) {
        closeCart();
    }
});

// Prevent clicks inside the cart panel from closing the cart
document.getElementById("cartPanel").addEventListener("click", function(event) {
    // Stop the event from propagating to the overlay (this prevents the cart from closing)
    event.stopPropagation();
});
