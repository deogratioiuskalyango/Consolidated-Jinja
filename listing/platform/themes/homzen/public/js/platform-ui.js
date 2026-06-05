(function () {
    'use strict';

    var revealSelectors = [
        '.homeya-box',
        '.bb-product-item',
        '.bb-store-item',
        '.flat-blog-item',
        '.platform-hub-card',
        '.platform-quote-form',
        '.platform-quote-steps > div',
        '.single-detail > *',
        '.ck-content > *',
        '.cart-area',
        '.bb-empty-state',
        '.box-title-listing',
        '.flat-title-page .container'
    ];

    var targets = Array.prototype.slice.call(document.querySelectorAll(revealSelectors.join(',')));

    targets.forEach(function (element, index) {
        element.classList.add('jcp-reveal');
        element.style.setProperty('--jcp-reveal-delay', Math.min(index % 8, 7) * 70 + 'ms');
    });

    if (!('IntersectionObserver' in window)) {
        targets.forEach(function (element) {
            element.classList.add('jcp-in-view');
        });

        return;
    }

    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('jcp-in-view');
                observer.unobserve(entry.target);
            }
        });
    }, {
        rootMargin: '0px 0px -8% 0px',
        threshold: 0.08
    });

    targets.forEach(function (element) {
        observer.observe(element);
    });
})();
