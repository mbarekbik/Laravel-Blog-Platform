let slideIndex = 1;
let myTimer;

window.currentSlide = function(n) {
    clearInterval(myTimer);
    showSlides(slideIndex = n);
    startTimer();
}

function showSlides(n) {
    let i;
    let slides = document.getElementsByClassName("post-highlighted");
    let dots = document.getElementsByClassName("dot");
    if (n > slides.length) {slideIndex = 1}
    if (n < 1) {slideIndex = slides.length}
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    for (i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace("fa-solid", "fa-regular");
    }
    slides[slideIndex-1].style.display = "flex";
    dots[slideIndex-1].className = dots[slideIndex-1].className.replace("fa-regular", "fa-solid");
}

function animateSlides() {
    let slides = document.getElementsByClassName("post-highlighted");
    slideIndex++;
    if (slideIndex > slides.length) {slideIndex = 1}
    showSlides(slideIndex);
}

showSlides(slideIndex);

startTimer();

function startTimer() {
    myTimer = setInterval(animateSlides, 10000);
}
