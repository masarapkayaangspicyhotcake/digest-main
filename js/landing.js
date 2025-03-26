 // Carousel functionality
 let currentSlide = 0;

 function updateSlidePosition() {
     const carouselInner = document.querySelector('.carousel-inner');
     const slideWidth = document.querySelector('.carousel-item').clientWidth;
     carouselInner.style.transform = `translateX(-${currentSlide * slideWidth}px)`;
 }
 
 function moveSlide(n) {
     const slides = document.querySelectorAll('.carousel-item');
     currentSlide = (currentSlide + n + slides.length) % slides.length;
     updateSlidePosition();
     updateIndicators();
 }
 
 function goToSlide(n) {
     currentSlide = n;
     updateSlidePosition();
     updateIndicators();
 }
 
 function updateIndicators() {
     const indicators = document.querySelectorAll('.indicator');
     indicators.forEach((indicator, index) => {
         indicator.classList.toggle('active', index === currentSlide);
     });
 }
 
 document.addEventListener('DOMContentLoaded', () => {
     const indicators = document.querySelectorAll('.indicator');
     indicators.forEach((indicator, index) => {
         indicator.addEventListener('click', () => goToSlide(index));
     });
 
     document.querySelector('.prev').addEventListener('click', () => moveSlide(-1));
     document.querySelector('.next').addEventListener('click', () => moveSlide(1));
 
     updateSlidePosition();
     updateIndicators();
 
     // Automatically move to the next slide every 5 seconds
     setInterval(() => moveSlide(1), 5000);
 });
 
 
 // USER DASHBOARD 
 loadCarousel();
 loadAnnouncements();
 loadArticles();
 loadMagazines();
 loadTejidos();

 // Load carousel images
 function loadCarousel() {
     $.ajax({
         url: "./ajax/fetch_carousel.php",
         method: "GET",
         success: function (data) {
             $("#carousel-images").html(data.images);
             $("#carousel-indicators").html(data.indicators);
             // Reinitialize carousel indicators after loading new content
             updateIndicators();
         },
         dataType: "json"
     });
 }
