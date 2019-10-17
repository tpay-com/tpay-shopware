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


            bankData = {
                id: currentTarget.data('bank-id'),
                name: currentTarget.data('bank-name'),
                img: currentTarget.data('bank-img')
            };

            currentTarget.addClass('tpay__list-item--busy');

            $.ajax({
                type: "POST",
                url: me.opts.url,
                data: bankData,
                success: function (data) {
                    currentTarget.removeClass('tpay__list-item--busy');
                    if(data.success){
                        currentTarget.addClass('tpay__list-item--active');
                        me.changeButtonState();
                    }
                },
                dataType: 'json'
            });
        },

        changeButtonState: function () {
            var me = this,
                hasSelectedBank = (me.$el.find('.tpay__list-item--active').length === 1),
                isSelectedMethod = !me.$el.parents('.method--bankdata').hasClass('is--hidden'),
                isOk = false,
                $button = $('.main--actions');

            if(!isSelectedMethod) {
                isOk = true
            }else if(hasSelectedBank && isSelectedMethod) {
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
    })

})(jQuery, window);
