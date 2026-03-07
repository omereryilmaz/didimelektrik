$(document).ready(function(){
    // Modern Slider with fade and animation effects
    $('.owl-one').owlCarousel({
        loop: true,
        margin: 0,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplaySpeed: 800,
        autoplayHoverPause: true,
        itemWidth: 100,
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
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 1
            },
            1000: {
                items: 1
            },
            1200: {
                items: 1
            },
            1400: {
                items: 1
            },
            1600: {
                items: 1
            },
            1800: {
                items: 1
            }
        },
        onChanged: function(event) {
            // Add fade-in animation to captions on slide change
            var $captions = $('.owl-item.active .slider-captions');
            $captions.stop(true, true).fadeOut(0).fadeIn(600);
            var $images = $('.owl-item.active .slider-img img');
            $images.stop(true, true).css({
                'transform': 'scale(1.05)'
            }).animate({
                'opacity': 1
            }, 800, function() {
                $(this).css('transform', 'scale(1)');
            });
        }
    });

    $('.gallery-owl').owlCarousel({

    loop:true,
    margin:0,
    autoplay:true,
    autoplayTimeout:3000,
    dots:false,

    nav:true,
    navText:['<i class="fa fa-long-arrow-left"></i>', '<i class="fa fa-long-arrow-right"></i>'],
        responsive:{
            0:{
                items:1
            },
            600:{
                items:1
            },
            1000:{
                items:1
            }
        }
    });

 $('.owl-post-gallery').owlCarousel({

       loop:true,
    margin:0,
    autoplay:true,
    autoplayTimeout:3000,

    nav:true,
    navText:['<i class="icon-back-arrow-circular-symbol"></i>', '<i class="icon-right-arrow-circular-button"></i>'],
        responsive:{
            0:{
                items:1
            },
            600:{
                items:1
            },
            1000:{
                items:1
            }
        }
    });
 $('.owl-Four').owlCarousel({

       loop:true,
    margin:0,
    autoplay:true,
    autoplayTimeout:3000,

    nav:false,
    navText:['<i class="icon-back-2"></i>', '<i class="icon-next-4"></i>'],
        responsive:{
            0:{
                items:1
            },
            600:{
                items:1
            },
            1000:{
                items:1
            }
        }
    });






    
});