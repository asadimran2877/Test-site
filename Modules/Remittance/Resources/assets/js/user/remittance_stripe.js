'use strict';

var pretext = $("#deposit-stripe-submit-btn-txt").text();
var paymentIntendId = null;
var paymentMethodId = null;

function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}

function depositBack() {
    window.localStorage.setItem("depositConfirmPreviousUrl", document.URL);
    window.history.back();
}

//Only go back by back button, if submit button is not clicked
$(document).on('click', '.deposit-confirm-back-btn', function(e) {
    e.preventDefault();
    depositBack();
});

$('#payment-form').validate({
    rules: {
        cardNumber: {
            required: true,
        },
        month: {
            required: true,
            maxlength: 2
        },
        year: {
            required: true,
            maxlength: 2
        },
        cvc: {
            required: true,
            maxlength: 4
        },
    },
    submitHandler: function(form) {
        confirmPayment();
    }
});

function makePayment() {
    var promiseObj = new Promise(function(resolve, reject) {
        var cardNumber = $("#cardNumber").val().trim();
        var month = $("#month").val().trim();
        var year = $("#year").val().trim();
        var cvc = $("#cvc").val().trim();
        $("#stripeError").html('');
        if (cardNumber && month && year && cvc) {
            $.ajax({
                type: "POST",
                url: SITE_URL + "/remittance/stripe-make-payment",
                data: {
                    "_token": token,
                    'cardNumber': cardNumber,
                    'month': month,
                    'year': year,
                    'cvc': cvc
                },
                dataType: "json",
                beforeSend: function(xhr) {
                    $("#deposit-stripe-submit-btn").attr("disabled", true);
                },
            }).done(function(response) {
                if (response.data.status != 200) {
                    $("#stripeError").html(response.data.message);
                    $("#deposit-stripe-submit-btn").attr("disabled", true);
                    reject(response.data.status);
                    return false;
                } else {
                    resolve(response.data);
                    $("#deposit-stripe-submit-btn").attr("disabled", false);
                }
            });
        }
    });
    return promiseObj;
}

function confirmPayment() {
    makePayment().then(function(result) {
        $.ajax({
            type: "POST",
            url: SITE_URL + "/remittance/stripe-confirm-payment",
            data: {
                "_token": token,
                'paymentIntendId': result.paymentIntendId,
                'paymentMethodId': result.paymentMethodId,
            },
            dataType: "json",
            beforeSend: function(xhr) {
                $("#deposit-stripe-submit-btn").attr("disabled", true);
                $(".fa-spin").show();
                $("#deposit-stripe-submit-btn-txt").text(submitText);
            },
        }).done(function(response) {
            $("#deposit-stripe-submit-btn-txt").text(pretext);
            $(".fa-spin").hide();
            if (response.data.status != 200) {
                $("#deposit-stripe-submit-btn").attr("disabled", true);
                $("#stripeError").html(response.data.message);
                return false;
            } else {
                $("#deposit-stripe-submit-btn").attr("disabled", false);
            }
            window.location.replace(SITE_URL + '/remittance/stripe-payment/success');
        });
    });
}
$("#month").change(function() {
    $("#deposit-stripe-submit-btn").attr("disabled", true);
    makePayment();
});
$("#year, #cvc").on('keyup', $.debounce(500, function() {
    $("#deposit-stripe-submit-btn").attr("disabled", true);
    makePayment();
}));
$("#cardNumber").on('keyup', $.debounce(1000, function() {
    $("#deposit-stripe-submit-btn").attr("disabled", true);
    makePayment();
}));
// For card number design
document.getElementById('cardNumber').addEventListener('input', function(e) {
    var target = e.target,
        position = target.selectionEnd,
        length = target.value.length;
    target.value = target.value.replace(/[^\d]/g, '').replace(/(.{4})/g, '$1 ').trim();
    target.selectionEnd = position += ((target.value.charAt(position - 1) === ' ' && target.value.charAt(length - 1) === ' ' && length !== target.value.length) ? 1 : 0);
});

$(document).ready(function() {
    $("#deposit-stripe-submit-btn").attr("disabled", true);

    window.history.pushState(null, "", window.location.href);
    window.onpopstate = function() {
        window.history.pushState(null, "", window.location.href);
    };
});