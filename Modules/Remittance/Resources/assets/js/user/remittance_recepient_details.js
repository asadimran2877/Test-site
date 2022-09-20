'use strict';

$('#remittanceDetails').validate({
    rules: {
        recepient_f_name: {
            required: true
        },
        recepient_l_name: {
            required: true
        },
        recepient_email: {
            required: true
        },
        recepient_phone: {
            required: true
        },
        recepient_city: {
            required: true
        },
        recepient_street: {
            required: true
        },
        recepient_country: {
            required: true
        },
    },
    submitHandler: function(form) {
        $("#remittance-submit-btn").attr("disabled", true);
        $(".spinner").show();
        $("#submit_text").text("{{__('Submitting...')}}");
        form.submit();
    }
});

$(document).on('input', ".receiver", function(e) {
    let emailOrPhone = $('#receiver').val().trim();
    if (emailOrPhone != null) {
        checkReceiverEmailorPhone();
    }
});

function checkReceiverEmailorPhone() {
    var token = $('#token').val();
    var receiver = $('#receiver').val().trim();
    if (receiver != '') {
        $.ajax({
                method: "POST",
                url: SITE_URL + "/remittance/recepient-email-validation-check",
                dataType: "json",
                data: {
                    '_token': token,
                    'receiver': receiver
                }
            })
            .done(function(response) {
                if (response.status == true) {
                    $('.receiverError').html(response.message).css({
                        'color': 'red',
                        'font-size': '14px',
                        'font-weight': '800',
                        'padding-top': '5px',
                    });
                } else {
                    $('.receiverError').html('');
                }
            });
    } else {
        $('.receiverError').html('');
    }
}