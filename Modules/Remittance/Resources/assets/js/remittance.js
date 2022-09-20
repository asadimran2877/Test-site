var token = $("input[name=_token]").val();

// Main function for Calculation
function getCalculatedValue(sendCurrencyId, receivedCurrencyId, sendAmount, receivedAmount = null, payWith) {
    if (sendCurrencyId != null && receivedCurrencyId != null) {

        $.ajax({
                url: SITE_URL + "/remittance/get-calculated-values",
                method: "GET",
                data: {
                    'send_currency_id': sendCurrencyId,
                    'received_currency_id': receivedCurrencyId,
                    'send_amount': sendAmount,
                    'received_amount': receivedAmount,
                    'pay_with': payWith,
                    'token': token
                },
                dataType: 'json',
            })
            .done(function (data) {
                if (receivedAmount !== null && (receivedAmount.event_from_receivedAmount == true)) {
                    $('#sendAmount').val(data.success.sendAmount);
                    $('#totalFee').text(data.success.totalFee);
                    $('#totalAmount').text(data.success.totalPaymentAmount);
                    $('#subTotalAmount').text(data.success.subTotalAmount);
                    $('.fee').val(data.success.totalFee);
                    $('.totalAmount').val(data.success.totalPaymentAmount);
                } else {
                    $('#receivedAmount').val(data.success.receivedAmount);
                    $('#totalFee').text(data.success.totalFee);
                    $('#totalAmount').text(data.success.totalPaymentAmount);
                    $('#subTotalAmount').text(data.success.subTotalAmount);
                    $('.fee').val(data.success.totalFee);
                    $('.totalAmount').val(data.success.totalPaymentAmount);
                }
                $('#exchangeRate').text(data.success.exchangeRate);

                $('#rate').val(data.success.exchangeRate);

                $('#feeCurrencySymbol').text(data.success.sendCurrencySymbol);
                $('#totalAmountCurrencySymbol').text(data.success.sendCurrencySymbol);
                $('#subTotalAmountCurrencySymbol').text(data.success.sendCurrencySymbol);
            })
            .fail(function (err) {
                console.log(err);
                err.responseText.hasOwnProperty('message') == true ? alert(JSON.parse(err.responseText).message) : alert(err.responseText);
            });
    } else {
        console.log('Payment Method or Fees Limit is Inactive');
    }
}

// On Ready Get Currency Related Data
function getCurrencyRelatedData(sendCurrencyId, receivedCurrencyId) {
    if (sendCurrencyId != null && receivedCurrencyId != null) {

        let promiseObj = new Promise(function (resolve, reject) {
            $.ajax({
                    url: SITE_URL + "/remittance/get-currency-related-data",
                    method: "GET",
                    data: {
                        'send_currency_id': sendCurrencyId,
                        'received_currency_id': receivedCurrencyId,
                        'token': token
                    },
                    dataType: 'json',
                })
                .done(function (data) {
                    // Send Currency Related data
                    $option = '';
                    $.each(data.success.sendCurrencyPaymentMethods, function (index, value) {
                        $option += `<option value="${value.id}">${value.name}</option>`;
                    });
                    $('#pay_with').html($option);
                    $('#sendAmount').val(data.success.sendCurrencyMinLimit);


                    // Send Currency Related data
                    $option = '';
                    $.each(data.success.receivedCurrencyPaymentMethods, function (index, value) {
                        $option += `<option value="${value.id}">${value.payout_type}</option>`;
                    });
                    $('#delivered_to').html($option);

                    // For exchage rate
                    $('#sendCurrencyCode').text(data.success.sendCurrencyCode);
                    $('#receivedCurrencyCode').text(data.success.receivedCurrencyCode);

                    resolve();
                })
                .fail(function (err) {
                    console.log(err);
                    err.responseText.hasOwnProperty('message') == true ? alert(JSON.parse(err.responseText).message) : alert(err.responseText);
                    reject();
                });
        });
        return promiseObj;
    } else {
        console.log('Payment Method or Fees Limit is Inactive');
    }
}

// On Change Send Currency
function getSendCurrencyRelatedData(sendCurrencyId) {
    if (sendCurrencyId != null) {

        let promiseObj = new Promise(function (resolve, reject) {
            $.ajax({
                    url: SITE_URL + "/remittance/get-send-currency-related-data",
                    method: "GET",
                    data: {
                        'send_currency_id': sendCurrencyId,
                        'token': token
                    },
                    dataType: 'json',
                })
                .done(function (data) {
                    $option = '';
                    $.each(data.success.sendCurrencyPaymentMethods, function (index, value) {
                        $option += `<option value="${value.id}">${value.name}</option>`;
                    });
                    $('#pay_with').html($option);

                    $('#sendCurrencyCode').text(data.success.sendCurrencyCode);

                    resolve();
                })
                .fail(function (err) {
                    console.log(err);
                    err.responseText.hasOwnProperty('message') == true ? alert(JSON.parse(err.responseText).message) : alert(err.responseText);
                    reject();
                });
        });

        return promiseObj;
    } else {
        console.log('Payment Method or Fees Limit is Inactive');
    }
}

// On Change Received Currency
function getReceivedCurrencyRelatedData(receivedCurrencyId) {
    if (receivedCurrencyId != null) {

        let promiseObj = new Promise(function (resolve, reject) {
            $.ajax({
                    url: SITE_URL + "/remittance/get-received-currency-related-data",
                    method: "GET",
                    data: {
                        'received_currency_id': receivedCurrencyId,
                        'token': token
                    },
                    dataType: 'json',
                })
                .done(function (data) {
                    $option = '';
                    $.each(data.success.receivedCurrencyPaymentMethods, function (index, value) {
                        $option += `<option value="${value.id}">${value.payout_type}</option>`;
                    });
                    $('#delivered_to').html($option);


                    $('#receivedCurrencyCode').text(data.success.receivedCurrencyCode);
                    resolve();
                })
                .fail(function (err) {
                    console.log(err);
                    err.responseText.hasOwnProperty('message') == true ? alert(JSON.parse(err.responseText).message) : alert(err.responseText);
                    reject();
                });
        });

        return promiseObj;
    } else {
        console.log('Payment Method or Fees Limit is Inactive');
    }
}

// On Change Send Currency get The min limit for send Currency payment method
function getSendAmountMinMaxLimit(paymentMethodId, currencyId) {
    let promiseObj = new Promise(function (resolve, reject) {
        $.ajax({
            url: SITE_URL + "/remittance/get-send-min-max-amount",
            method: "GET",
            data: {
                'payment_method_id': paymentMethodId,
                'currency_id': currencyId,
                'token': token

            },
            dataType: 'json',
            success: function (data) {
                $('#sendAmount').val(data.success.amountLimit.min_limit);
                resolve();
            },
            error: function (error) {
                console.log(error);
                reject();
            }
        });
    });

    return promiseObj;
}

// On Ready
$(document).on('ready', function () {

    let sendCurrencyId = $('#send_currency').val();
    let receivedCurrencyId = $('#received_currency').val();
    if (sendCurrencyId != null && receivedCurrencyId != null) {
        getCurrencyRelatedData(sendCurrencyId, receivedCurrencyId)
            .then(() => {

                let sendAmount = $('#sendAmount').val();
                let payWith = $('#pay_with').val();

                getCalculatedValue(sendCurrencyId, receivedCurrencyId, sendAmount, null, payWith);

            });
    } else {
        console.log('Payment Method or Fees Limit is Inactive');
    }
});

// On Change send currency
$('#send_currency').on('change', function () {

    let sendCurrencyId = $(this).val();

    getSendCurrencyRelatedData(sendCurrencyId)
        .then(() => {
            let receivedCurrencyId = $('#received_currency').val();
            let sendAmount = $('#sendAmount').val();
            let payWith = $('#pay_with').val();

            getCalculatedValue(sendCurrencyId, receivedCurrencyId, sendAmount, null, payWith);
        });

});

// On Change received currency
$('#received_currency').on('change', function () {

    let receivedCurrencyId = $(this).val();

    getReceivedCurrencyRelatedData(receivedCurrencyId)
        .then(() => {
            let sendCurrencyId = $('#send_currency').val();
            let sendAmount = $('#sendAmount').val();
            let payWith = $('#pay_with').val();

            getCalculatedValue(sendCurrencyId, receivedCurrencyId, sendAmount, null, payWith);
        });

});

// On chagne Payment method
$('#pay_with').on('change', function () {

    let payWith = $(this).val();
    let sendCurrencyId = $('#send_currency').val();
    let receivedCurrencyId = $('#received_currency').val();
    let sendAmount = $('#sendAmount').val();

    getCalculatedValue(sendCurrencyId, receivedCurrencyId, sendAmount, null, payWith);

});

// On blur Send Amount
$('#sendAmount').on('input', $.debounce(1000, function () {

    let sendCurrencyId = $('#send_currency').val();
    let receivedCurrencyId = $('#received_currency').val();
    let sendAmount = $(this).val();
    let payWith = $('#pay_with').val();

    if (!isNaN(sendAmount)) {
        getCalculatedValue(sendCurrencyId, receivedCurrencyId, sendAmount, null, payWith);
    }

}));

// On blur Received Amount
$('#receivedAmount').on('input', $.debounce(1000, function () {

    let sendCurrencyId = $('#send_currency').val();
    let receivedCurrencyId = $('#received_currency').val();
    let sendAmount = $('#sendAmount').val();
    let payWith = $('#pay_with').val();
    let receivedAmount = {
        received_amount: $(this).val(),
        event_from_receivedAmount: true,
    };

    if (!isNaN(receivedAmount.received_amount)) {
        getCalculatedValue(sendCurrencyId, receivedCurrencyId, sendAmount, receivedAmount, payWith);
    }
}));