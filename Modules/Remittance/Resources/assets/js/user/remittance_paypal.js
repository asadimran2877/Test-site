'use strict';

paypal.Buttons({
    createOrder: function(data, actions) {
        return actions.order.create({
            purchase_units: [{
                amount: {
                    value: amount
                }
            }]
        });
    },
    onApprove: function(data, actions) {
        return actions.order.capture().then(function(details) {
            window.location.replace(SITE_URL + "/remittance/paypal-payment/success/" + btoa(details.purchase_units[0].amount.value));
        });
    }
}).render('#paypal-button-container');