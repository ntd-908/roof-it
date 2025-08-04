/**
 * Copyright © magebig.com - All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'MageBig_SmartMenu/js/jquery.collapsible',
    'Magento_Theme/js/initScroll',
    'magnificpopup',
    'nanoscroller',
    'jquery-ui-modules/tooltip',
    'domReady!'
], function ($) {
    "use strict";
    $.widget('custom.magebig', {
        options: {
            sticky_header: false
        },
        _create: function () {
            var self = this,
                isStickyAddCart = this.options.sticky_add_cart,
                isProductView = $('.catalog-product-view').length;

            if (this.options.sticky_header) {
                if (!isProductView || (isProductView && !isStickyAddCart)) {
                    setTimeout(function () {
                        self._stickyMenu();
                    }, 500);
                }
            }

            this._minicartSidebar();
            this._goTop();

            this._toggleMobile();
            this._toolTip();
            $('body').on('contentUpdated', function () {
                self._toolTip();
            });

            var currentUrl = window.location.href,
                $url = $('.smartmenu').find('a');

            $url.each(function (index) {
                if (currentUrl.indexOf($(this).attr('href')) === 0) {
                    var $catLevel = $(this).parents('.level-top');

                    $catLevel.each(function (i, level) {
                        $(this).addClass('active');
                    })
                }
            })

            var $menu = $('.magebig-nav').clone();

            if ($menu.length) {
                $menu.find('> .level0 > i, .menu-static-blocks, .mbmenu-block:not(.menu-items)').remove();
                $menu.find('.show-sub, .show-sub-content, .menu-items').contents().unwrap();
                this._toggleNav($menu);
                this._menuCatalog($menu);
            }

            this._userTopbar();
            this._filterBtn();
        },
        _menuCatalog: function (menu) {
            (function (events) {
                const initCatNav = function () {
                    events.forEach(function (eventType) {
                        window.removeEventListener(eventType, initCatNav);
                    });

                    var $category = $('#categories-nav');

                    if ($category.length && !$category.text()) {
                        menu.find('.hide-cat').remove();
                        $category.html(menu.html());
                        $category.parents('.block-nav').removeClass('d-none');
                        $category.MbCollapse({
                            id: '#categories-nav',
                            accordion: true,
                            speed: 400,
                            mouseType: 0,
                            collapsed: 'collapsed mbi mbi-ios-arrow-down',
                            expanded: 'expanded mbi mbi-ios-arrow-up'
                        });
                    }
                }
                events.forEach(function (eventType) {
                    window.addEventListener(eventType, initCatNav, {once: true, passive: true})
                })
            })(['keydown', 'mouseover', 'scroll', 'touchstart', 'wheel']);
        },
        _minicartSidebar: function () {
            $('.action.showcart').magnificPopup({
                preloader: false,
                mainClass: 'mfp-slide-right cart-modal',
                fixedContentPos: true,
                closeOnBgClick: true,
                removalDelay: 300,
                items: {
                    src: '.minicart-slide',
                    type: 'inline'
                },
                callbacks: {
                    open: function () {
                        window.dispatchEvent(new CustomEvent('open_mini_cart'));
                    }
                }
            });
            $('[data-block="minicart"]').on('contentLoading', function () {
                $(this).on('contentUpdated', function () {
                    if ($('.quickview-wrap').length) {
                        if ($.magnificPopup.instance.isOpen) {
                            $.magnificPopup.close();
                        }
                        setTimeout(function () {
                            $('.action.showcart').trigger('click');
                        }, 350);
                    } else {
                        $('.action.showcart').trigger('click');
                    }
                });
            });
        },
        _goTop: function () {
            var $goTop = $('#go-top'),
                stickyActive = false;
            if ($goTop.length) {
                $goTop.hide();
                $(window).scrolled(function () {
                    if ($(this).scrollTop() > 100) {
                        if (!stickyActive) {
                            $goTop.fadeIn();
                            stickyActive = true;
                        }
                    } else {
                        if (stickyActive) {
                            $goTop.fadeOut();
                            stickyActive = false;
                        }
                    }
                });
                $goTop.on('click', function () {
                    $('body,html').animate({
                        scrollTop: 0
                    }, 300);
                    if ($('.sticky-menu').length) {
                        $('.sticky-menu').removeClass('active fadeindown fadeoutup');
                    }
                    return false;
                });
            }
        },
        _stickyMenu: function () {
            var sType = this.options.sticky_type ? this.options.sticky_type : 0,
                sOffset = this.options.sticky_offset ? this.options.sticky_offset : 500,
                $stickyMenu = $('.sticky-menu');

            if ($stickyMenu.length > 0) {
                $stickyMenu.wrap('<div class="sticky-wrap"></div>');

                $stickyMenu.parent().css('min-height', $stickyMenu.outerHeight());

                var threshold = $stickyMenu.height() + $stickyMenu.offset().top + sOffset,
                    stickyActive = false,
                    $win = $(window),
                    wh = $win.height(),
                    lastScrollTop = 0;

                $win.scrolled(function () {
                    var curWinTop = $win.scrollTop();
                    if (sType == 1) {
                        if (curWinTop > threshold && wh > 500 && curWinTop < Math.ceil(lastScrollTop) + 1) {
                            if (!stickyActive) {
                                $stickyMenu.addClass('active fadeindown');
                                stickyActive = true;
                            }
                        } else {
                            if (stickyActive) {
                                $stickyMenu.removeClass('active fadeindown');
                                stickyActive = false;
                            }
                        }
                    } else if (sType == 2) {
                        if (curWinTop > threshold && wh > 500) {
                            if (!stickyActive) {
                                $stickyMenu.addClass('active fadeindown');
                                stickyActive = true;
                            }
                        } else {
                            if (stickyActive) {
                                $stickyMenu.removeClass('active fadeindown');
                                stickyActive = false;
                            }
                        }
                    } else {
                        if (curWinTop > threshold && wh > 500 && curWinTop > Math.ceil(lastScrollTop) - 1) {
                            if (!stickyActive) {
                                $stickyMenu.addClass('active fadeindown');
                                stickyActive = true;
                            }
                        } else {
                            if (stickyActive) {
                                $stickyMenu.removeClass('active fadeindown');
                                stickyActive = false;
                            }
                        }
                    }
                    lastScrollTop = curWinTop;
                });

                var timer = false,
                    lastWidth = $win.width();
                $win.resize(function () {
                    if (timer) {
                        clearTimeout(timer);
                    }
                    timer = setTimeout(function () {
                        if ($win.height() < 500 && stickyActive) {
                            $stickyMenu.removeClass('active fadeindown fadeoutup');
                            stickyActive = false;
                        }
                        if ($win.width() !== lastWidth) {
                            $stickyMenu.parent().css('min-height', $stickyMenu.outerHeight());
                            lastWidth = $win.width();
                        }
                    }, 500);
                });
            }
        },
        _toggleMobile: function () {
            $('.btn-search-mobile > i').magnificPopup({
                items: {
                    src: '.top-search-wrap',
                    type: 'inline'
                },
                focus: 'input#search',
                mainClass: 'mfp-move-from-top search-popup',
                fixedContentPos: true,
                removalDelay: 300
            });

            if (!$('.toggle-mobile').find('.title .mbi').length) {
                $('.toggle-mobile').find('.title').append('<i class="mbi mbi-chevron-down"></i>');
            }
            $('.toggle-mobile').each(function () {
                if ($(this).hasClass('open')) {
                    $(this).find('.title .mbi').removeClass('mbi-chevron-down').addClass('mbi-chevron-up');
                    $(this).find('.content-toggle').show();
                }
            });
            $('.toggle-mobile .title .mbi').on('click', function () {
                $(this).parents('.toggle-mobile').removeClass('open');
                if ($(this).hasClass('mbi-chevron-down')) {
                    $(this).parents('.toggle-mobile').find('.content-toggle').slideDown();
                    $(this).removeClass('mbi-chevron-down').addClass('mbi-chevron-up');
                } else {
                    $(this).parents('.toggle-mobile').find('.content-toggle').slideUp();
                    $(this).removeClass('mbi-chevron-up').addClass('mbi-chevron-down');
                }
            });
        },
        _toolTip: function () {
            if ($(window).width() > 767) {
                $('.mb-tooltip').tooltip({
                    show: null,
                    trigger: 'hover',
                    hide: {
                        delay: 250
                    },
                    position: {
                        my: "center bottom-30",
                        at: "center top",
                        using: function (position, feedback) {
                            $(this).css(position);
                            $(this).addClass("magebig-tooltip");
                        }
                    },
                    open: function (event, ui) {
                        ui.tooltip.addClass('in');
                    },
                    close: function (event, ui) {
                        ui.tooltip.removeClass('in');
                    }
                });
            }
        },
        _toggleNav: function (menu) {
            // button show hide menu mobile
            $('.btn-nav').on('click', function (event) {
                event.preventDefault();
                var $menuMobile = $('#mb-collapsible');
                if (!$menuMobile.text()) {
                    $menuMobile.html(menu.html());
                    $('.nav-collapse').MbCollapse({
                        accordion: true,
                        speed: 300,
                        mouseType: 0,
                        collapsed: 'collapsed mbi mbi-ios-arrow-down',
                        expanded: 'expanded mbi mbi-ios-arrow-up'
                    });
                }
                $('.overlay-contentpush').addClass('open');
                $('.page-wrapper').addClass('overlay-open');
                $('html').addClass('nav-open');
            });
            $('.nav-bar-wrap').on('click', function (event) {
                if (!$(event.target).closest('.nav-bar').length) {
                    $('.overlay-contentpush').removeClass('open');
                    $('.btn-nav').removeClass('active');
                    $('.page-wrapper').removeClass('overlay-open');
                    $('html').removeClass('nav-open');
                }
            });

            var toggles = document.querySelectorAll(".mb-toggle-switch");
            for (var i = toggles.length - 1; i >= 0; i--) {
                var toggle = toggles[i];
                toggle.addEventListener("click", function (e) {
                    e.preventDefault();
                    (this.classList.contains("active") === true) ? this.classList.remove("active") : this.classList.add("active");
                });
            }
        },
        _userTopbar: function () {
            if ($('.header.links').find('a').length > 2) {
                $('.user-topbar > i').on('click', function (event) {
                    event.preventDefault();
                    $.magnificPopup.open({
                        items: {
                            src: '.header.links',
                            type: 'inline'
                        },
                        overflowY: 'auto',
                        fixedContentPos: true,
                        removalDelay: 300,
                        mainClass: 'mfp-slide-right'
                    });
                });
            } else {
                $('.user-topbar > i').on('click', function (event) {
                    event.preventDefault();
                    if ($('.authorization-link a').length) {
                        $('.authorization-link a')[0].click();
                    }
                });
            }
        },
        _filterBtn: function () {
            if ($('.filter-mobile-btn').length) {
                $('#maincontent').append('<button type="button" class="filter-btn d-none"></button>');
                $('.filter-mobile-btn').on('click', function () {
                    $('.filter-btn').trigger('click');
                });
                $('body').on('contentUpdated', function () {
                    $('.filter-mobile-btn').on('click', function () {
                        $('.filter-btn').trigger('click');
                    });
                });
                $('.filter-btn').magnificPopup({
                    preloader: false,
                    mainClass: 'mfp-slide-right',
                    fixedContentPos: true,
                    removalDelay: 300,
                    items: {
                        src: '.sidebar-wrap',
                        type: 'inline'
                    },
                    callbacks: {
                        open: function () {
                            $('.filter-options-item .nano').nanoScroller();
                        }
                    }
                });
            }
        }
    });
    return $.custom.magebig;
});
