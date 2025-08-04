define([
    'jquery',
    'domReady!'
], function ($) {
    "use strict";

    return function (config, element) {
        var newVal,
            defaultValue = parseFloat($('#qty').val()),
            increment = config.increment ? config.increment : 1;

        $(".btn-qty").click(function (event) {
            event.preventDefault();
            var $button = $(this),
                $input = $button.closest('.control').find("input#qty"),
                oldValue = parseFloat($input.val());

            if (!oldValue || oldValue < increment) {
                oldValue = 0;
            }

            if ($button.hasClass('plus')) {
                newVal = oldValue + increment;
            } else {
                if (oldValue > defaultValue && oldValue - increment > 0) {
                    newVal = oldValue - increment;
                } else {
                    newVal = defaultValue;
                }
            }
            newVal = parseFloat(newVal.toFixed(10));
            $input.val(newVal);
        });
    }
});
