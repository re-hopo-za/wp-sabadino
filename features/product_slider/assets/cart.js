'use strict';
(function ($) {
    jQuery(window).on('elementor/frontend/init', function(){
        elementorFrontend.hooks.addAction('frontend/element_ready/products-slider.default', function ($scope, $ ) {
            let products_wraper = $scope.find('.za-products-wraper');

            let display_dots    = products_wraper.data('display-dots');
            if( display_dots == 'yes' ){
                display_dots = true;
            }else{
                display_dots = false;
            }

            let autoplay = products_wraper.data('autoplay');
            if( autoplay == 'yes' ){
                autoplay = true;
            }else{
                autoplay = false;
            }

            let autoplaySpeed = 3000;
            if( autoplay == true ){
                autoplaySpeed = products_wraper.data('autoplay-speed');
            }

            let slideSpeed = products_wraper.data('slide-speed');
            if( slideSpeed <= 0 ){
                slideSpeed = 1000;
            }

            let slides_to_show  = products_wraper.data('slide-to-show');
            if( slides_to_show > 0 ){
                slides_to_show  = products_wraper.data('slide-to-show');
            }else{
                slides_to_show = 4
            }

            let slides_to_scroll  = products_wraper.data('slides-to-scroll');
            if( slides_to_scroll > 0 ){
                slides_to_scroll  = products_wraper.data('slides-to-scroll');
            }else{
                slides_to_scroll = 4
            }

            /*var pauseOnFocus = $scope.find('.wpce_slider_wrapper').data('pause-on-focus');
            if( pauseOnFocus == 'yes' ){
                pauseOnFocus = true;
            }else{
                pauseOnFocus = false;
            }*/

            let pauseOnHover = products_wraper.data('pause-on-hover');
            if( pauseOnHover == 'yes' ){
                pauseOnHover = true;
            }else{
                pauseOnHover = false;
            }

            let pauseOnDotsHover = products_wraper.data('pause-on-dots-hover');
            if( pauseOnDotsHover == 'yes' ){
                pauseOnDotsHover = true;
            }else{
                pauseOnDotsHover = false;
            }

            let prev_arrow = $scope.find('.wb-arrow-prev');
            let next_arrow = $scope.find('.wb-arrow-next');


            products_wraper.slick({
                infinite: false,
                slidesToShow: 4,
                slidesToScroll: 4,
                autoplay: autoplay,
                arrows: true,
                rows:1,
                rtl:true ,
                prevArrow: prev_arrow,
                nextArrow: next_arrow,
                dots: display_dots,
                draggable: true,
                focusOnSelect: false,
                swipe: true,
                adaptiveHeight: true,
                speed: slideSpeed,
                autoplaySpeed: autoplaySpeed,
                // pauseOnFocus : pauseOnFocus,
                pauseOnHover : pauseOnHover,
                pauseOnDotsHover : pauseOnDotsHover,
                responsive: [
                    {
                        breakpoint: 1224,
                        settings: {
                            slidesToShow: 4,
                            slidesToScroll: 4,
                            infinite: true,
                            dots: true
                        }
                    },
                    {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 3
                        }
                    },
                    {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 2
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 2
                        }
                    }
                ]
            });
        });
    });


})(jQuery);





