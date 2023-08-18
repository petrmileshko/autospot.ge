$(document).ready(function () {
    $('.slick-slider').slick({
        slidesToShow: 6,
        slidesToScroll: 1,
        autoplay: false,
        autoplaySpeed: 2000,
		rows: 2, // Установить значение rows равным 2
        infinite: false, // Отключение бесконечной прокрутки
        centerMode: false, // Отключение центрирования слайдов
        responsive: [
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 4
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 2
                }
            },
            {
                breakpoint: 576,
                settings: {
                    slidesToShow: 2
                }
            }
        ],
        prevArrow: '<span class="init-slider-grid-prev slick-arrow slick-disabled" style="left: 0px; margin-top: -30px;" aria-disabled="true"><i class="las la-arrow-left"></i></span>',
        nextArrow: '<span class="init-slider-grid-next slick-arrow" style="right: 0px; margin-top: -30px;" aria-disabled="false"><i class="las la-arrow-right"></i></span>'
    });
});

