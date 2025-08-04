/**
 * Copyright © magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery'
], function ($) {
    'use strict';

    /**
     * @param {String} url
     * @param {*} fromPages
     */
    function processReviews(url, fromPages)
    {
        $.ajax({
            url: url,
            cache: true,
            dataType: 'html',
            showLoader: false,
            loaderContext: $('.product.data.items')
        }).done(function (data) {
            $('#product-review-container').html(data).trigger('contentUpdated');
            $('[data-role="product-review"] .pages a').each(function (index, element) {
                $(element).on('click', function (event) {
                    processReviews($(element).attr('href'), true);
                    event.preventDefault();
                });
            });
        });
    }

    return function (config) {
        var reviewTab = $(config.reviewsTabSelector);

        reviewTab.on('afterOpen', function () {
            processReviews(config.productReviewUrl);
        });

        reviewTab.one('beforeOpen', function () {
            processReviews(config.productReviewUrl);
        });

        $(function () {
            $('.product-info-main .reviews-actions a').on('click', function (event) {
                event.preventDefault();

                if (!reviewTab.hasClass('opened')) {
                    reviewTab.trigger('click');
                }

                $('html, body').animate({
                    scrollTop: reviewTab.offset().top - 80
                }, 300);
            });
        });
    };
});
