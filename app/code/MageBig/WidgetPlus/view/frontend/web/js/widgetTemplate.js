define(['jquery', 'mage/apply/main', 'jquery-ui-modules/widget'], function ($, main) {
    'use strict';

    $.widget('mage.widgetTemplate', {
        _create: function () {
            let self = this;
            self._loadByEvent();
        },

        _loadByEvent: function () {
            let self = this;

            (function (events) {
                const initLoadWidget = function () {
                    events.forEach(function (eventType) {
                        window.removeEventListener(eventType, initLoadWidget);
                    });

                    if (!!window.IntersectionObserver) {
                        self._lazyAjax();
                    } else {
                        self._callAjax();
                    }
                }
                events.forEach(function (eventType) {
                    window.addEventListener(eventType, initLoadWidget, {once: true, passive: true})
                })
            })(['keydown', 'mouseover', 'scroll', 'touchstart', 'wheel']);
        },

        _lazyAjax: function () {
            let self = this, offset = this.options.offset + "px 0px",
                lazyElm = document.getElementById(this.element.attr('id'));

            let lazyAjax = new IntersectionObserver(function (entries, observer) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        let lazyElm = entry.target;
                        self._callAjax();
                        lazyAjax.unobserve(lazyElm);
                    }
                });
            }, {rootMargin: offset});

            lazyAjax.observe(lazyElm);
        },

        _callAjax: function () {
            let self = this;

            $.ajax({
                url: self.options.loadUrl,
                type: 'GET',
                dataType: 'html',
                data: {parameters: JSON.stringify(self.options.parameters)},
                success: function (content) {
                    self.element.replaceWith(content);
                    main.apply();
                    $('body').trigger('contentUpdated');
                },
                error: function (content) {
                    self.element.remove();
                }
            });
        }
    });

    return $.mage.widgetTemplate;
});
