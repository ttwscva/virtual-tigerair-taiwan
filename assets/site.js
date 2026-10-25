// 1. 修正導航列錨點平滑滾動（相容 index.html#about 與 #about）
document.querySelectorAll('a[href*="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (!href || href === '#' || href.startsWith('http')) return;

        // 取得 # 後面的 id 名稱
        const hashIndex = href.indexOf('#');
        if (hashIndex === -1) return;
        
        const targetId = href.substring(hashIndex);
        const pathPart = href.substring(0, hashIndex);

        // 如果點擊的是當前頁面內的錨點
        const currentPage = window.location.pathname.split('/').pop() || 'index.html';
        if (pathPart === '' || pathPart === currentPage || (currentPage === '' && pathPart === 'index.html')) {
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
                // 更新網址 hash
                history.pushState(null, null, targetId);
            }
        }
    });
});

// 2. 主視覺 Banner 自動輪播控制
document.addEventListener("DOMContentLoaded", function() {
    const slides = document.querySelectorAll('.hero-slider .slide');
    if (slides.length === 0) return;
    
    let currentSlide = 0;
    const slideInterval = 5000; // 每 5 秒切換一次背景

    function nextSlide() {
        slides[currentSlide].classList.remove('active');
        currentSlide = (currentSlide + 1) % slides.length;
        slides[currentSlide].classList.add('active');
    }

    setInterval(nextSlide, slideInterval);
});

console.log('Virtual Tigerair Taiwan Website Loaded.');
