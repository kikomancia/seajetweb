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

// scroll event to add class to navbar
const navbar = document.querySelector('.custom-navbar');

window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});



document.addEventListener("DOMContentLoaded", function () {

    const animatedElements = document.querySelectorAll(".animate-on-scroll");

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const animation = entry.target.getAttribute("data-animate");

                entry.target.classList.add(
                    "animate__animated",
                    animation
                );

                entry.target.style.opacity = 1;
                observer.unobserve(entry.target); // animate once
            }
        });
    }, {
        threshold: 0.2
    });

    animatedElements.forEach(el => observer.observe(el));

});


// scroll effect sa image
// window.addEventListener("scroll", () => {
//   const excelence = document.querySelector(".hero-section");
//   const scrollPos = window.scrollY;
//   excelence.style.backgroundPosition = `center ${scrollPos * .5}px`;
// });




// COUNTING EFFECT
document.addEventListener("DOMContentLoaded", () => {
    const counters = document.querySelectorAll(".counter");

    const animateCounter = (counter) => {
        const target = +counter.getAttribute("data-target");
        const suffix = counter.getAttribute("data-suffix") || "";
        const duration = 1500; // total animation time (ms)
        const startTime = performance.now();

        const update = (currentTime) => {
            const progress = Math.min((currentTime - startTime) / duration, 1);
            const value = Math.floor(progress * target);
            counter.textContent = value + suffix;

            if (progress < 1) {
                requestAnimationFrame(update);
            } else {
                counter.textContent = target + suffix;
            }
        };

        requestAnimationFrame(update);
    };

    const observer = new IntersectionObserver(
        (entries, obs) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    obs.unobserve(entry.target); // run once
                }
            });
        },
        { threshold: 0.6 }
    );

    counters.forEach(counter => observer.observe(counter));
});


// 