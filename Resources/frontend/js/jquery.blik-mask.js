;(function ($, window) {

    $.plugin('tpayBlikMask', {
        defaults: {},

        init: function () {
            var me = this;
            // me.registerEvents();
            me.validateBlik();
        },

        registerEvents: function () {
            var me = this;

            me._on(me.$el.find('select'), 'change', $.proxy(me.validateBlik, me));
        },

        validateBlik: function () {
            var me = this,
                l,
                blikInput = me.$el,
                blikMask = [
                    new RegExp('^[0-9]$'),
                    new RegExp('^[0-9][0-9]$'),
                    new RegExp('^[0-9][0-9][0-9]$'),
                    new RegExp('^[0-9][0-9][0-9]+\\s$'),
                    new RegExp('^[0-9][0-9][0-9]+\\s[0-9]$'),
                    new RegExp('^[0-9][0-9][0-9]+\\s[0-9][0-9]$'),
                    new RegExp('^[0-9][0-9][0-9]+\\s[0-9][0-9][0-9]$'),
                ];

            l = blikInput.val().length;
            if (l < 0) {
                blikInput.val('');
            }

            if (l <= 7 && l > 0) {
                if (l === 3) {
                    blikInput.val(blikInput.val() + ' ');
                    l = blikInput.val().length;
                }

                while (!blikMask[l - 1].test(blikInput.val())) {
                    blikInput.val('');
                    l = blikInput.val().length;
                }
            } else {
                blikInput.val('');
            }

            blikInput.on('keyup paste', function () {
                l = blikInput.val().length;

                if (l <= 7 && l > 0) {
                    if (l === 3) {
                        blikInput.val(blikInput.val() + ' ');
                        l = blikInput.val().length;
                    }

                    while (!blikMask[l - 1].test(blikInput.val())) {
                        blikInput.val(blikInput.val().slice(0, -1));
                        l = blikInput.val().length;
                    }
                } else {
                    blikInput.val(blikInput.val().slice(0, -1));
                }
            });

        },
    });

    window.StateManager.addPlugin('.blik__input', 'tpayBlikMask');

})(jQuery, window);

