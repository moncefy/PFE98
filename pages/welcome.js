document.addEventListener('DOMContentLoaded', function() {
    // Image Slider Code
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.slide-dot');
    let currentSlide = 0;
    const slideInterval = 5000;
    let slideTimer;

    // Function to show a specific slide
    function showSlide(index) {
        // Remove active class from all slides and dots
        slides.forEach(slide => {
            slide.classList.remove('active');
            slide.style.opacity = '0';
        });
        dots.forEach(dot => {
            dot.classList.remove('active');
            dot.style.transform = 'scale(1)';
        });

        // Add active class to current slide and dot
        slides[index].classList.add('active');
        slides[index].style.opacity = '1';
        dots[index].classList.add('active');
        dots[index].style.transform = 'scale(1.2)';

        // Update current slide index
        currentSlide = index;
    }

    // Function to show next slide
    function nextSlide() {
        let next = currentSlide + 1;
        if (next >= slides.length) {
            next = 0;
        }
        showSlide(next);
    }

    // Start auto-sliding
    function startSlideTimer() {
        slideTimer = setInterval(nextSlide, slideInterval);
    }

    // Reset timer when manually changing slides
    function resetSlideTimer() {
        clearInterval(slideTimer);
        startSlideTimer();
    }

    // Add click event listeners to dots
    dots.forEach((dot, index) => {
        dot.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (currentSlide !== index) {
                showSlide(index);
                resetSlideTimer();
            }
        });
    });

    // Initialize the slider
    showSlide(0);
    startSlideTimer();

    // Destination Cards Carousel Code
    const destinationCards = document.querySelectorAll('.destination-card');
    const destinationDots = document.querySelectorAll('.destination-dot');
    const prevDestinationBtn = document.getElementById('prevDestination');
    const nextDestinationBtn = document.getElementById('nextDestination');
    let currentDestination = 0;
    const cardInterval = 4000;
    let cardTimer;

    function updateDestinationCards() {
        destinationCards.forEach((card, index) => {
            const offset = index - currentDestination;
            const translateX = offset * 100;
            const scale = offset === 0 ? 1 : 0.8;
            const opacity = offset === 0 ? 1 : 0.5;
            const zIndex = offset === 0 ? 10 : 1;

            card.style.transform = `translateX(${translateX}%) scale(${scale})`;
            card.style.opacity = opacity;
            card.style.zIndex = zIndex;
        });

        destinationDots.forEach((dot, index) => {
            dot.classList.toggle('bg-purple', index === currentDestination);
            dot.classList.toggle('bg-purple/30', index !== currentDestination);
        });
    }

    function nextDestination() {
        currentDestination = (currentDestination + 1) % destinationCards.length;
        updateDestinationCards();
    }

    function prevDestination() {
        currentDestination = (currentDestination - 1 + destinationCards.length) % destinationCards.length;
        updateDestinationCards();
    }

    function startCardTimer() {
        cardTimer = setInterval(nextDestination, cardInterval);
    }

    function resetCardTimer() {
        clearInterval(cardTimer);
        startCardTimer();
    }

    destinationDots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            currentDestination = index;
            updateDestinationCards();
            resetCardTimer();
        });
    });

    if (prevDestinationBtn) {
        prevDestinationBtn.addEventListener('click', () => {
            prevDestination();
            resetCardTimer();
        });
    }

    if (nextDestinationBtn) {
        nextDestinationBtn.addEventListener('click', () => {
            nextDestination();
            resetCardTimer();
        });
    }

    updateDestinationCards();
    startCardTimer();
});