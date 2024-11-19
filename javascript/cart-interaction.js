function openCart() {
    document.getElementById("overlay").classList.add("active");
    document.getElementById("cartPanel").classList.add("active");
}

function closeCart() {
    document.getElementById("overlay").classList.remove("active");
    document.getElementById("cartPanel").classList.remove("active");
}

// Close the cart when clicking outside of it
document.getElementById("overlay").addEventListener("click", closeCart);