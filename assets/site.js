// 1. 控制導航列錨點平滑滾動
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        if(targetId === '#') return;
        
        const targetElement = document.querySelector(targetId);
        if(targetElement) {
            targetElement.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});

// 2. Hero Banner 自動輪播圖片控制
document.addEventListener("DOMContentLoaded", function() {
    const slides = document.querySelectorAll('.hero-slider .slide');
    if(slides.length === 0) return;
    
    let currentSlide = 0;
    const slideInterval = 5000; // 每 5 秒切換一次圖片

    function nextSlide() {
        slides[currentSlide].classList.remove('active');
        currentSlide = (currentSlide + 1) % slides.length;
        slides[currentSlide].classList.add('active');
    }

    setInterval(nextSlide, slideInterval);
});

console.log('Virtual Tigerair Taiwan Website Loaded with Slideshow.');
