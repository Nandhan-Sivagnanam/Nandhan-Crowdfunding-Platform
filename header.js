let slideIndex = 0;
showSlide(slideIndex);

function changeSlide(n) {
    showSlide(slideIndex += n);
}

function showSlide(index) {
    let slides = document.getElementsByClassName("slide");
    if (index >= slides.length) {
        slideIndex = 0;
    }
    if (index < 0) {
        slideIndex = slides.length - 1;
    }
    for (let i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    slides[slideIndex].style.display = "block";
}

// Automatic Slide
function autoSlide() {
    changeSlide(1);
    setTimeout(autoSlide, 3000); // Change image every 5 seconds
}

// Start automatic sliding
autoSlide();
