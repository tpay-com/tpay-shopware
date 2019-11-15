;(function($, window) {
    'use strict';
    $.plugin('tpayBank', {


        defaults: {
            url: '/TpayBankSelection',
            disable: '',
            error: ''
        },

        init: function () {
            var me = this;

            me.applyDataAttributes();
            me.$inputs = me.$el.find('.tpay__list-item');
            me.changeButtonState();
            me.registerEventListener();
        },

        registerEventListener: function () {
            var me = this;
            me._on(me.$inputs, 'click', $.proxy(me.onChangeBank, me));
        },

        onChangeBank: function (event) {
            var me = this,
                currentTarget = $(event.currentTarget),
                bankData = {};

            me.$inputs.removeClass('tpay__list-item--active');
            me.$inputs.addClass('tpay__list-item--busy');
            currentTarget.addClass('tpay__list-item--active');

            bankData = {
                id: currentTarget.data('bank-id'),
                name: currentTarget.data('bank-name'),
                img: currentTarget.data('bank-img')
            };

            $.ajax({
                type: "POST",
                url: me.opts.url,
                data: bankData,
                success: function (data) {
                    currentTarget.removeClass('tpay__list-item--busy');
                    if(data.success){
                        currentTarget.addClass('tpay__list-item--active');
                        me.changeButtonState();
                        var tPayInput = $('#' + currentTarget.data('payment-selector'));

                        if(!tPayInput.is(':checked')) {
                            tPayInput.click();
                        } else {
                            dispatchScroll();
                        }
                    }
                },
                dataType: 'json'
            });
        },

        changeButtonState: function () {
            var me = this,
                tPayBankListActive = me.$el.hasClass('tpay__list__selected'),
                hasSelectedBank = (me.$el.find('.tpay__list-item--active').length === 1),
                isOk = false,
                $button = $('.main--actions');

            if(!tPayBankListActive) {
                isOk = true
            }else if(hasSelectedBank && tPayBankListActive) {
                isOk = true;
            }

            if(isOk){
                $button.attr("disabled", false);
                $button.removeAttr('title');
            }else {
                $button.attr("disabled", true);
                $button.attr("title", me.opts.disable);
            }
        }
    });

    window.StateManager.addPlugin('*[data-tpay-bank="true"]', 'tpayBank');

    $.subscribe('plugin/swShippingPayment/onInputChanged', function() {
        window.StateManager.addPlugin('*[data-tpay-bank="true"]', 'tpayBank');
    });

    $.subscribe('plugin/swShippingPayment/onInputChanged', function () {
        var listTpay = $('.tpay__list__selected');

        if(listTpay.length > 0) {
            if(listTpay.find('.tpay__list-item--active').length > 0) {
                dispatchScroll();
            }
        } else {
            dispatchScroll();
        }
    });

    function dispatchScroll() {
        var $headline = $('.dispatch--method-headline');
        if($headline.length > 0) {
            $("html, body").animate({
                scrollTop: $headline.offset().top-100
            });
        }
    }

})(jQuery, window);
