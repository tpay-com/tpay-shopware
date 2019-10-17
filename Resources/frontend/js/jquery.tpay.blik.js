;(function ($, window) {
    'use strict';
    $.plugin('tpayBlik', {


        defaults: {
            url: '/TpayPaymentBlik/ajax',
            check: '/TpayPaymentBlik/check',
            buttonSelector: '.blik__button',
            inputSelector: '#blik',
            sAgbSelector: '#sAGB',
            sAgbLabelSelector: 'label[for="sAGB"]',
            tosSelector: '#tpayTOS',
            tosLabelSelector: '.tpay__tos--label'
        },

        init: function () {
            var me = this;

            me.applyDataAttributes();

            me.$button = me.$el.find(me.opts.buttonSelector);
            me.$input = me.$el.find(me.opts.inputSelector);
            me.$sAGB = $(me.opts.sAgbSelector);
            me.$sAGBLabel = $(me.opts.sAgbLabelSelector);
            me.$tos = $(me.opts.tosSelector);
            me.$tosLabel = $(me.opts.tosLabelSelector);

            me.registerEventListener();
        },

        registerEventListener: function () {
            var me = this;
            me._on(me.$button, 'click', $.proxy(me.onSend, me));
        },

        onSend: function (e) {
            e.preventDefault();
            var me = this;
            if (!me.validate()) {
                me.enableBuyButton();
                return;
            }
            var code = me.$input.val();

            me.modal = $.modal.open($('#blikModal').html(), {closeOnOverlay: false, showCloseButton: false});

            $.ajax({
                type: "POST",
                url: me.opts.url,
                data: {code: code},
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        me.checkResult(data.number);
                        me.changeModalState('wait');
                    } else {
                        me.changeModalState('error');
                    }
                },
                error: function () {
                    me.changeModalState('error');
                }
            });
        },

        changeModalState: function (state) {
            var me = this,
                $error = me.modal._$content.find('.blikmodal__text-error'),
                $confirm = me.modal._$content.find('.blikmodal__text-confirm'),
                $wait = me.modal._$content.find('.blikmodal__text-wait'),
                $success = me.modal._$content.find('.blikmodal__text-success');
            $error.addClass('is--hidden');
            $confirm.addClass('is--hidden');
            $wait.addClass('is--hidden');
            $success.addClass('is--hidden');

            switch (state) {
                case 'error':
                    $error.removeClass('is--hidden');
                    me._on($error.find('.btn'), 'click', $.proxy(me.onModalExit, me));
                    break;
                case 'confirm':
                    $confirm.removeClass('is--hidden');
                    break;
                case 'wait':
                    $wait.removeClass('is--hidden');
                    break;
                case 'success':
                    me.modal._$content.find('.blikmodal').addClass('blikmodal--success');
                    $success.removeClass('is--hidden');
                    break;
            }
        },

        checkResult: function (ordernumber) {
            var me = this;
            $.ajax({
                type: "POST",
                url: me.opts.check,
                data: {number: ordernumber},
                dataType: 'json',
                success: function (data) {
                    if (data.waiting) {
                        setTimeout(function () {
                            me.checkResult(ordernumber);
                        }, 500);
                    } else {
                        if(data.success) {
                            me.changeModalState('success');
                        }
                        window.location.replace(data.redirect);
                    }
                },
            });
        },

        onModalExit: function () {
            this.modal.close();
            this.enableBuyButton();
        },

        enableBuyButton: function () {
            var me = this;
            setTimeout(function () {
                me.$button.removeAttr("disabled");
                me.$button.find('.js--loading').remove();
            }, 150);
        },

        validate: function () {
            var me = this,
                valid = true,
                value = me.$input.val();

            if (me.$sAGB.is(':checked')) {
                me.$sAGBLabel.removeClass('has--error');
            } else {
                me.$sAGBLabel.addClass('has--error');
                valid = false;
            }

            if (me.$tos.is(':checked')) {
                me.$tosLabel.removeClass('has--error');
            } else {
                me.$tosLabel.addClass('has--error');
                valid = false;
            }

            if (me.isNumeric(value) && value.length === 6) {
                me.$input.removeClass('has--error');
            } else {
                me.$input.addClass('has--error');
                valid = false;
            }

            return valid;

        },

        isNumeric: function (value) {
            return !isNaN(parseFloat(value)) && isFinite(value);
        }
    });

    window.StateManager.addPlugin('*[data-blik="true"]', 'tpayBlik');


})(jQuery, window);
