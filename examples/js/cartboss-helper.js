(function ($) {
    var cbDebug = false, // disable in production
        cbATCEventHandlerUrl = 'event_atc.php', // path to script that generates add to cart event
        cbApiTimeout = 10000,
        cbSendDelay = 1500;

    // customize ajax:selector mapping
    var cbInputFields = {
        billing_phone: '#billing_phone',
        billing_email: '#billing_email',
        billing_first_name: '#billing_first_name',
        billing_last_name: '#billing_last_name',
        billing_country: '#billing_country',
        billing_city: '#billing_city',
        billing_company: '#billing_company',
        billing_address_1: '#billing_address_1',
        billing_address_2: '#billing_address_2',
        billing_state: '#billing_state',
        billing_postcode: '#billing_zip',

        shipping_first_name: '#shipping_first_name',
        shipping_last_name: '#shipping_last_name',
        shipping_country: '#shipping_country',
        shipping_city: '#shipping_city',
        shipping_company: '#shipping_company',
        shipping_address_1: '#shipping_address_1',
        shipping_address_2: '#shipping_address_2',
        shipping_state: '#shipping_state',
        shipping_postcode: '#shipping_postcode',

        accepts_marketing: '#accepts_marketing'
    }

    ////
    // don't edit beyond this line
    ////
    var cbDataSender = (function () {
        return {
            stateData: null,

            addState: function (data) {
                if (!data) {
                    cbDebug && console.log("CartBoss", "State data is empty, skipping");
                    return;
                }

                clearTimeout(this.tid);

                this.stateData = data;
                this._send(cbSendDelay);
            },

            onEdit: function () {
                clearTimeout(this.tid);
                cbDebug && console.log("CartBoss", "🛑 Edit detected");

                if (this.stateData) {
                    this._send(cbSendDelay);
                }
            },

            _send: function (delay) {
                var self = this;

                if (!this.stateData) {
                    cbDebug && console.log("CartBoss", "🚨 Nothing to send");
                    return;
                }
                if (!this.stateData.billing_phone || this.stateData.billing_phone.length < 5) {
                    cbDebug && console.log("CartBoss", "🚨 Nothing to send");
                    return;
                }

                if (!self.isSendingInProgress) {
                    cbDebug && console.log("CartBoss", "⏳ Sending scheduled in", delay, "ms");

                    self.tid = setTimeout(function () {
                        $.ajax({
                            url: cbATCEventHandlerUrl,
                            type: "POST",
                            data: self.stateData,
                            cache: false,
                            async: true,
                            global: false,
                            timeout: cbApiTimeout,
                            beforeSend: function (xhr) {
                                self.isSendingInProgress = true;
                                self.stateData = null;
                                cbDebug && console.log("CartBoss", "✈️ Sending started with data");
                            },
                            complete: function (a, b) {
                                self.isSendingInProgress = false;
                                cbDebug && console.log("CartBoss", "✅ Sending completed with response:", b);


                                if (self.stateData) {
                                    cbDebug && console.log("CartBoss", "🙊 State changed while previous sending, send again...");
                                    self._send(0);
                                }
                            },
                        });
                    }, delay);
                }
            },
        };
    })();

    var cbElements = {};
    var cbListener = function () {
        var data = {};
        $.each(cbElements, function (name, el) {
            if (el.is(':checkbox')) {
                data[name] = !!el.is(':checked');
            } else {
                data[name] = el.val();
            }
        });
        cbDataSender.addState(data);
    };


    $(document).ready(function () {
        cbDebug && console.log("CartBoss", "Script initialized");

        $.each(cbInputFields, function (name, selector) {
            var el = $(selector);

            if (el && el.length > 0) {
                el.keyup(function () {
                    cbDataSender.onEdit();
                });

                el.change(cbListener);

                cbElements[name] = el;

                cbDebug && console.log("CartBoss", "Field listener attached to field", name);
            } else {
                cbDebug && console.log("CartBoss", "Field not found", name);
            }
        });
    });
})(jQuery);
