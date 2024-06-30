//Pop-up Navbar Menu

document.addEventListener('DOMContentLoaded', function () {
    const menuIcon = document.querySelector('.menu-icon');
    const popupMenu = document.querySelector('.popup-menu');

    menuIcon.addEventListener('click', function () {
        popupMenu.style.display = popupMenu.style.display === 'block' ? 'none' : 'block';
    });
});

// Services Carousel

document.addEventListener("DOMContentLoaded", function() {
    const prevBtn = document.querySelector(".prev-btn");
    const nextBtn = document.querySelector(".next-btn");
    const imagesContainer = document.querySelector(".images-container");
    const images = document.querySelectorAll(".image");

    let scrollAmount = 0;

    const step = images[0].offsetWidth + 40; 

    prevBtn.addEventListener("click", function() {
        scrollAmount = Math.max(scrollAmount - step, 0);
        imagesContainer.scroll({
            left: scrollAmount,
            behavior: "smooth"
        });
    });

    nextBtn.addEventListener("click", function() {
        scrollAmount = Math.min(scrollAmount + step, imagesContainer.scrollWidth - imagesContainer.clientWidth);
        imagesContainer.scroll({
            left: scrollAmount,
            behavior: "smooth"
        });
    });
});

// FAQ Pop-up

document.addEventListener('DOMContentLoaded', function () {
    var faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach(function (item) {
        var question = item.querySelector('.question');
        question.addEventListener('click', function () {
            var answer = item.querySelector('.answer');
            var arrow = question.querySelector('.arrow');
            
            // Toggle active class on question
            question.classList.toggle('active');

            if (answer.style.display === 'block') {
                answer.style.display = 'none';
                arrow.innerHTML = '&#9660;';
            } else {
                answer.style.display = 'block';
                arrow.innerHTML = '&#9650;';
            }
        });
    });
});




















