$.plugin('tpayForm', {
    defaults: {
        url: ''
    },
    init: function() {
        var me = this,
            parentFunction = window.changeBank,
            blikCodeInput = $('#blik_code'),
            tpayRegulationsCheckbox = $('#tpay-accept-regulations-checkbox');

        const TRIGGER_EVENTS = 'input change blur';

        me.applyDataAttributes();
        window.changeBank = function (bankID) {
            $('a.tpay-select').addClass('is--hidden');
            $('button.tpay-select').removeClass('is--hidden');
            $.post( me.opts.url, { id: bankID } ).done(function( data ) {
                $('a.tpay-select').removeClass('is--hidden');
                $('button.tpay-select').addClass('is--hidden');
            });
            parentFunction(bankID);
        };
        blikCodeInput.on(TRIGGER_EVENTS, isBlikFormValid);
        tpayRegulationsCheckbox.on(TRIGGER_EVENTS, isBlikFormValid);

        function isBlikFormValid() {
            if (blikCodeInput.val().length === 6 && tpayRegulationsCheckbox.is(':checked')) {
                $('a.tpay-select').removeClass('is--hidden');
                $('button.tpay-select').addClass('is--hidden');
                $.post( me.opts.url, { blikCode: blikCodeInput.val() } ).done(function( data ) {
                    $('a.tpay-select').removeClass('is--hidden');
                    $('button.tpay-select').addClass('is--hidden');
                })
            } else {
                $('a.tpay-select').addClass('is--hidden');
                $('button.tpay-select').removeClass('is--hidden');
            }
        }

    }
});

StateManager.addPlugin('.sw-tpay-form', 'tpayForm');
