(function ($) {
    var debug = true,
        endpointUrl = 'event_atc.php';

    var inputz = {
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

    var cbDataSender = (function () {
        return {
            stateData: null,

            addState: function (data) {
                if (!data) {
                    debug && console.log("CartBoss", "State data is empty, skipping");
                    return;
                }

                clearTimeout(this.tid);

                this.stateData = data;
                this._send(1000);
            },

            onEdit: function () {
                clearTimeout(this.tid);
                debug && console.log("CartBoss", "🛑 Edit detected");

                if (this.stateData) {
                    this._send(1500);
                }
            },

            _send: function (delay) {
                var self = this;

                if (!this.stateData) {
                    debug && console.log("CartBoss", "🚨 Nothing to send");
                }

                if (!self.isSendingInProgress) {
                    debug && console.log("CartBoss", "⏳ Sending scheduled in", delay, "ms");

                    self.tid = setTimeout(function () {
                        $.ajax({
                            url: endpointUrl,
                            type: "POST",
                            data: self.stateData,
                            cache: false,
                            async: true,
                            global: false,
                            timeout: 10000,
                            beforeSend: function (xhr) {
                                self.isSendingInProgress = true;
                                self.stateData = null;
                                debug && console.log("CartBoss", "✈️ Sending started with data");
                            },
                            complete: function (a, b) {
                                self.isSendingInProgress = false;
                                debug && console.log("CartBoss", "✅ Sending completed with response:", b);


                                if (self.stateData) {
                                    debug && console.log("CartBoss", "🙊 State changed while previous sending, send again...");
                                    self._send(0);
                                }
                            },
                        });
                    }, delay);
                }
            },
        };
    })();

    var foo = {};
    var sendCartBossData = function () {
        var data = {};
        $.each(foo, function (name, el) {
            if (el.is(':checkbox')) {
                data[name] = !!el.is(':checked');
            } else {
                data[name] = el.val();
            }
        });
        cbDataSender.addState(data);
    };


    $(document).ready(function () {
        debug && console.log("CartBoss", "Script initialized");

        $.each(inputz, function (name, selector) {
            var el = $(selector);

            if (el && el.length > 0) {
                el.keyup(function () {
                    cbDataSender.onEdit();
                });

                el.change(sendCartBossData);

                foo[name] = el;

                debug && console.log("CartBoss", "Field listener attached to field", name);
            } else {
                debug && console.log("CartBoss", "Field not found", name);
            }
        });
    });
})(jQuery);
