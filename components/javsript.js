// add background on scroll
window.addEventListener("scroll", function () {
    const nav = document.querySelector(".custom-navbar");
    nav.classList.toggle("scrolled", window.scrollY > 20);
});

// loading page
window.addEventListener("load", function () {

    const loader = document.getElementById("loader");
    const content = document.getElementById("page-content");

    // fade out loader
    loader.style.opacity = "0";

    // remove loader after fade animation
    setTimeout(() => {
        loader.style.display = "none";
        content.style.display = "block";
    }, 500); // match transition time
});