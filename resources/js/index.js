import Swiper from 'swiper/bundle';

var swiper = new Swiper(".index-default-carousel", {
    loop: true,

    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },

    autoplay: {
        delay: 3000,
    },
});
