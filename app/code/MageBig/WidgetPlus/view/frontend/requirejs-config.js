var config = {
    map: {
        "*": {
            'owlWidget': 'MageBig_WidgetPlus/js/owl.carousel-set',
            'owlCarousel': 'MageBig_WidgetPlus/js/owl.carousel',
            'widgetTemplate': 'MageBig_WidgetPlus/js/widgetTemplate'
        }
    },
    shim: {
        'owlWidget': {
            deps: ['jquery', 'owlCarousel']
        },
        'owlCarousel': {
            deps: ['jquery']
        }
    }
};
