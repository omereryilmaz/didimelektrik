// Initialize when jQuery and Owl Carousel are ready
function initializeSliders() {
    // Check if jQuery and Owl Carousel are loaded
    if (typeof jQuery === 'undefined' || typeof $.fn.owlCarousel === 'undefined') {
        setTimeout(initializeSliders, 100);
        return;
    }

    // Initialize main slider with proper configuration
    if ($('.owl-one').length > 0 && !$('.owl-one').hasClass('owl-loaded')) {
        try {
            $('.owl-one').owlCarousel({
                loop: true,
                margin: 0,
                autoplay: true,
                autoplayTimeout: 5000,
                autoplaySpeed: 800,
                autoplayHoverPause: true,
                nav: true,
                navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
                dots: true,
                dotsSpeed: 400,
                dragEndSpeed: 400,
                animateIn: 'fadeIn',
                animateOut: 'fadeOut',
                touchDrag: true,
                touchDragIOS: true,
                mouseDrag: true,
                rewindSpeed: 600,
                startPosition: 0,
                items: 1,
                responsive: {
                    0: { items: 1 },
                    600: { items: 1 },
                    1000: { items: 1 },
                    1200: { items: 1 },
                    1400: { items: 1 },
                    1600: { items: 1 },
                    1800: { items: 1 }
                },
                onInitialize: function(event) {
                    var $captions = $('.owl-item.active .slider-captions');
                    $captions.stop(true, true).css('opacity', 0).fadeIn(400);
                },
                onChange: function(event) {
                    var $captions = $('.owl-item.active .slider-captions');
                    $captions.stop(true, true).fadeOut(100).fadeIn(600);
                    var $images = $('.owl-item.active .slider-img img');
                    $images.stop(true, true).css({ 'transform': 'scale(1.05)' }).animate({ 'opacity': 1 }, 800, function() {
                        $(this).css('transform', 'scale(1)');
                    });
                }
            });
        } catch(e) {
            console.error('Error initializing owl carousel:', e);
        }
    }

    // Initialize gallery carousel
    if ($('.gallery-owl').length > 0 && !$('.gallery-owl').hasClass('owl-loaded')) {
        try {
            $('.gallery-owl').owlCarousel({
                loop: true,
                margin: 0,
                autoplay: true,
                autoplayTimeout: 3000,
                dots: false,
                nav: true,
                navText: ['<i class="fa fa-long-arrow-left"></i>', '<i class="fa fa-long-arrow-right"></i>'],
                responsive: {
                    0: { items: 1 },
                    600: { items: 1 },
                    1000: { items: 1 }
                }
            });
        } catch(e) {
            console.error('Error initializing gallery carousel:', e);
        }
    }

    // Initialize post gallery carousel
    if ($('.owl-post-gallery').length > 0 && !$('.owl-post-gallery').hasClass('owl-loaded')) {
        try {
            $('.owl-post-gallery').owlCarousel({
                loop: true,
                margin: 0,
                autoplay: true,
                autoplayTimeout: 3000,
                nav: true,
                navText: ['<i class="icon-back-arrow-circular-symbol"></i>', '<i class="icon-right-arrow-circular-button"></i>'],
                responsive: {
                    0: { items: 1 },
                    600: { items: 1 },
                    1000: { items: 1 }
                }
            });
        } catch(e) {
            console.error('Error initializing post gallery carousel:', e);
        }
    }

    // Initialize owl-Four carousel
    if ($('.owl-Four').length > 0 && !$('.owl-Four').hasClass('owl-loaded')) {
        try {
            $('.owl-Four').owlCarousel({
                loop: true,
                margin: 0,
                autoplay: true,
                autoplayTimeout: 3000,
                nav: false,
                navText: ['<i class="icon-back-2"></i>', '<i class="icon-next-4"></i>'],
                responsive: {
                    0: { items: 1 },
                    600: { items: 1 },
                    1000: { items: 1 }
                }
            });
        } catch(e) {
            console.error('Error initializing owl-Four carousel:', e);
        }
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeSliders);
} else {
    initializeSliders();
}

// Also initialize on jQuery ready if available
if (typeof jQuery !== 'undefined') {
    jQuery(document).ready(function($) {
        initializeSliders();
    });
}
