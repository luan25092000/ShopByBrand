require([
    'jquery',
    'Magenest_ShopByBrand/js/owl.carousel'
], function ($) {
    $(".mn-brand-group .mn-brand-items").owlCarousel({
        nav: true,
        dot: false,
        navText: ['',''],
        mouseDrag: false,
        touchDrag: false,
        pullDrag: false,
        freeDrag: false,
        responsive:{
            0:{
                items:1
            },
            479:{
                items: 2
            },
            958:{
                items: 3
            },
            1437:{
                items: 4
            },
        }
    })
});

