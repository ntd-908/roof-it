/**
 * Copyright © magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'mage/translate',
    'Magento_Theme/js/initScroll'
], function ($, $t) {
    'use strict';

    function productViewMore(config)
    {
        var viewMore = $t('View more'),
            viewLess = $t('View less'),
            productDesc = $('.product.attribute.description');

        if (productDesc.length) {
            var temp = '<div class="btn-desc d-none"><div class="btn-more"><button type="button">'+viewMore+'</button></div><div class="btn-less"><button type="button">'+viewLess+'</button></div></div>';
            productDesc.append(temp);

            var desc = $('.btn-desc'),
                more = $('.btn-more'),
                less = $('.btn-less'),
                tabDesc = $('#tab-label-description'),
                height = parseInt(config.maxHeight);

            tabDesc.on('afterOpen', function (e) {
                if (productDesc.find('.value').height() > height) {
                    desc.removeClass('d-none');
                    more.show();
                    less.hide();
                }
            })

            more.on('click', function () {
                more.hide();
                less.show();
                productDesc.css('max-height', '100%');
            })

            less.on('click', function () {
                less.hide();
                more.show();
                productDesc.css('max-height', height + 'px');

                $('html, body').animate({
                    scrollTop: tabDesc.offset().top - 80
                }, 300);
            })
        }
    }

    function stickyAddCart ()
    {
        var $stickyAddCart = $('#product_addtocart_form .box-tocart');

        if ($stickyAddCart.length > 0) {
            $stickyAddCart.wrap('<div class="sticky-addcart-wrap"><div class="sticky-addcart"></div></div>');

            $('.sticky-addcart-wrap').css('min-height', $stickyAddCart.outerHeight());

            var $sAddCartChild = $('.sticky-addcart'),
                $stickWrap = $('.sticky-addcart-wrap').parent(),
                pageTitle = $('.page-title-wrapper.product').clone(),
                stickyActive = false,
                oneActive = false,
                $win = $(window),
                wh = $win.height(),
                $productTabTitle = $('.product.data.items').find('.switch');

            $win.scrolled(function () {
                var threshold = $stickWrap.offset().top + 60,
                    curWinTop = $win.scrollTop();

                if (curWinTop > threshold && wh > 500) {
                    if (!stickyActive) {
                        $sAddCartChild.addClass('active fadeindown');
                        $stickyAddCart.addClass('container');
                        stickyActive = true;
                    }

                    if (!oneActive) {
                        if (!$stickyAddCart.find('.page-title-wrapper').length) {
                            $stickyAddCart.prepend(pageTitle);
                            if (!$('.stick-info').length) {
                                pageTitle.append('<div class="stick-info"></div>');

                                $productTabTitle.each(function (index, item) {
                                    var id = 'stick-info' + index,
                                        elm = $(item),
                                        data = elm.clone().attr('id', id);
                                    $('.stick-info').append(data);
                                    $('#' + id).on('click', function (e) {
                                        e.preventDefault();
                                        if (!elm.parent().hasClass('opened')) {
                                            elm.trigger('click');
                                        }
                                        $('html,body').animate({
                                            scrollTop: elm.offset().top - 80
                                        }, 300);
                                    })
                                })
                            }
                        }
                        oneActive = true;
                    }
                } else {
                    if (stickyActive) {
                        $sAddCartChild.removeClass('active fadeindown');
                        $stickyAddCart.removeClass('container');
                        stickyActive = false;
                    }
                }
            });

            var timer = false,
                lastWidth = $win.width();
            $win.resize(function () {
                if (timer) {
                    clearTimeout(timer);
                }
                timer = setTimeout(function () {
                    if ($win.height() < 500 && stickyActive) {
                        $sAddCartChild.removeClass('active fadeindown');
                        $stickyAddCart.removeClass('container');
                        stickyActive = false;
                    }
                    if ($win.width() !== lastWidth) {
                        $('.sticky-addcart-wrap').css('min-height', $stickyAddCart.outerHeight());
                        lastWidth = $win.width();
                    }
                }, 500);
            });
        }
    }

    return function (config) {
        let isStickyAddCart = config.stickyAddCart;

        if (isStickyAddCart) {
            setTimeout(function () {
                stickyAddCart();
            }, 500);
        }

        if (config.isViewMore) {
            productViewMore(config);
        }

        if (!location.hash) {
            return
        }

        let hashId = encodeURIComponent(location.hash);
        hashId = hashId.replaceAll('%', '');
        let hashElm = $('#' + hashId);

        if (hashElm.length) {
            let tabHash = hashElm.prev();

            tabHash.on('afterOpen', function () {
                setTimeout(function () {
                    $('html, body').animate({
                        scrollTop: tabHash.offset().top - 80
                    }, 300);
                }, 500)
            })
        }
    };
});
