"use strict";

if ($('.content').find('#agentCreate').length) {
    var hasPhoneError = false;
    var hasEmailError = false;

    var pwd1 = document.getElementById("password"), 
    pwd2 = document.getElementById("confirm_password");
    function confirmPwd() {
    if (pwd1.value != pwd2.value) {
        pwd2.setCustomValidity("Passwords Don't Match");
    } else {
        pwd2.setCustomValidity('');
    }
    }
    pwd1.onchange = confirmPwd;
    pwd2.onkeyup = confirmPwd;

    function enableDisableButton() {
        if (!hasPhoneError && !hasEmailError) {
            $('form').find("button[type='submit']").prop('disabled', false);
        } else {
            $('form').find("button[type='submit']").prop('disabled', true);
        }
    }

    function formattedPhone() {
        if ($('#phone').val != '') {
            var p = $('#phone').intlTelInput("getNumber").replace(/-|\s/g, "");
            $("#formattedPhone").val(p);
        }
    }

    function validateEmail(email) {
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }

    function emptyEmail() {
        $('#email_error').remove();
    }

    $(function() {
        $(".select2").select2({});
    });

    // initialise plugin
    $(document).ready(function() {

        $("#phone").intlTelInput({
            separateDialCode: true,
            nationalMode: true,
            preferredCountries: [countryShortCode],
            autoPlaceholder: "polite",
            placeholderNumberType: "MOBILE",
            initialCountry: "auto",
            geoIpLookup: function(callback) {
                $.get('https://ipinfo.io', function() {}, "jsonp").always(function(resp) {
                    var countryCode = (resp && resp.country) ? resp.country : countryShortCode;
                    callback(countryCode);
                });
            },
            utilsScript: SITE_URL + "/public/backend/intl-tel-input-13.0.0/intl-tel-input-13.0.0/build/js/utils.js"
        });

        var countryData = $("#phone").intlTelInput("getSelectedCountryData");
        $('#defaultCountry').val(countryData.iso2);
        $('#carrierCode').val(countryData.dialCode);

        $("#phone").on("countrychange", function(e, countryData) {

            formattedPhone();

            $('#defaultCountry').val(countryData.iso2);
            $('#carrierCode').val(countryData.dialCode);

            if ($.trim($(this).val()) !== '') {

                if (!$(this).intlTelInput("isValidNumber") || !isValidPhoneNumber($.trim($(this).val()))) {
                    $('#tel-error').addClass('error').html(intPhnMgs).css("font-weight","bold");
                    hasPhoneError = true;
                    enableDisableButton();
                    $('#phone-error').hide();
                } else {
                    $('#tel-error').html('');
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        method: "POST",
                        url: ajaxUrl,
                        dataType: "json",
                        cache: false,
                        data: {
                            'phone': $.trim($(this).val()),
                            'carrierCode': $.trim(countryData.dialCode),
                        }
                    })
                    .done(function(response) {
                        if (response.status == true) {
                            $('#tel-error').html('');
                            $('#phone-error').show();

                            $('#phone-error').addClass('error').html(response.fail).css(
                                "font-weight", "bold");
                            hasPhoneError = true;
                            enableDisableButton();
                        } else if (response.status == false) {
                            $('#tel-error').show();
                            $('#phone-error').html('');

                            hasPhoneError = false;
                            enableDisableButton();
                        }
                    });
                }
            } else {
                $('#tel-error').html('');
                $('#phone-error').html('');
                hasPhoneError = false;
                enableDisableButton();
            }
        });
    });

    //Invalid Number Validation - admin create
    $(document).ready(function() {
        $("input[name=phone]").on('blur', function(e) {

            formattedPhone();

            if ($.trim($(this).val()) !== '') {

                if (!$(this).intlTelInput("isValidNumber") || !isValidPhoneNumber($.trim($(this).val()))) {
                    $('#tel-error').addClass('error').html(intPhnMgs).css("font-weight","bold");
                    hasPhoneError = true;
                    enableDisableButton();
                    $('#phone-error').hide();
                } else {
                    var phone = $(this).val().replace(/-|\s/g,"");
                    var phone = $(this).val().replace(/^0+/,"");

                    var pluginCarrierCode = $('#phone').intlTelInput('getSelectedCountryData').dialCode;

                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        method: "POST",
                        url: ajaxUrl,
                        dataType: "json",
                        data: {
                            'phone': phone,
                            'carrierCode': pluginCarrierCode,
                        }
                    }).done(function(response) {
                        if (response.status == true) {
                            if (phone.length == 0) {
                                $('#phone-error').html('');
                            } else {
                                $('#phone-error').addClass('error').html(response.fail).css("font-weight", "bold");
                                hasPhoneError = true;
                                enableDisableButton();
                            }
                        } else if (response.status == false) {
                            $('#phone-error').html('');
                            hasPhoneError = false;
                            enableDisableButton();
                        }
                    });
                    $('#tel-error').html('');
                    $('#phone-error').show();
                    hasPhoneError = false;
                    enableDisableButton();
                }
            } else {
                $('#tel-error').html('');
                $('#phone-error').html('');
                hasPhoneError = false;
                enableDisableButton();
            }
        });
    });

    // Validate Emal via Ajax
    $(document).ready(function() {
        $("#email").on('blur', function(e) {
            var email = $('#email').val();
            emptyEmail();

            if (!validateEmail(email)) {
                $('#email').parent('div').append('<span id="email_error" style="font-weight: bold" class="error">Please enter a valid email.</span>');
                hasEmailError = false;
                enableDisableButton();
                return false;
            }
            
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: emailCheckAjaxUrl,
                dataType: "json",
                data: {
                    'email': email,
                }
            }).done(function(response) {
                if (response.status == true) {
                    $('#email').parent('div').append('<span id="email_error" style="font-weight: bold" class="error">' + response.fail + '</span>');
                    hasEmailError = true;
                    enableDisableButton();
                } else if (response.status == false) {
                    emptyEmail();
                    hasEmailError = false;
                    enableDisableButton();
                }
            });
        });
    });

    //Only go back by back button, if submit button is not clicked
    $(document).on('click', '.admin-user-deposit-confirm-back-btn', function (e)
    {
        e.preventDefault();
        window.history.back();
    });
}

if ($('.content').find('#agentEdit').length) {
    // flag for button disable/enable
    var hasPhoneError = false;
    var hasEmailError = false;

    function enableDisableButton() {
        if (!hasPhoneError && !hasEmailError) {
            $('form').find("button[type='submit']").prop('disabled', false);
        } else {
            $('form').find("button[type='submit']").prop('disabled', true);
        }
    }

    function formattedPhone() {
        if ($('#phone').val != '') {
            var p = $('#phone').intlTelInput("getNumber").replace(/-|\s/g, "");
            $("#formattedPhone").val(p);
        }
    }

    function validateEmail(email) {
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }

    function emptyEmail() {
        $('#email_error').remove();
    }

    $(function () {
        $(".select2").select2({
        });

        $("#phone").intlTelInput({
            separateDialCode: true,
            nationalMode: true,
            preferredCountries: ["us"],
            autoPlaceholder: "polite",
            placeholderNumberType: "MOBILE",
            formatOnDisplay: false,
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/13.0.4/js/utils.js"
        })
        .done(function()
        {
            if (formatted !== null && carrierCode !== null && defaultCountry !== null) {
                $("#phone").intlTelInput("setNumber", formatted);
                $('#defaultCountry').val(defaultCountry);
                $('#carrierCode').val(carrierCode);
            }
        });
    });

    function checkInvalidAndDuplicatePhoneNumberForUserProfile(phoneVal, phoneData, agentId)
    {
        var that = $("input[name=phone]");
        if ($.trim(that.val()) !== '')
        {
            if (!that.intlTelInput("isValidNumber") || !isValidPhoneNumber($.trim(that.val())))
            {
                // alert('invalid');
                $('#tel-error').addClass('error').html('Please enter a valid International Phone Number.').css("font-weight", "bold");
                hasPhoneError = true;
                enableDisableButton();
                $('#phone-error').hide();
            }
            else
            {
                $('#tel-error').html('');

                var id = $('#id').val();
                $.ajax({
                    headers:
                    {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: "POST",
                    url: ajaxUrl,
                    dataType: "json",
                    cache: false,
                    data: {
                        'phone': phoneVal,
                        'carrierCode': phoneData,
                        'id': agentId,
                    }
                })
                .done(function(response)
                {
                    if (response.status == true)
                    {
                        $('#tel-error').html('');
                        $('#phone-error').show();

                        $('#phone-error').addClass('error').html(response.fail).css("font-weight", "bold");
                        hasPhoneError = true;
                        enableDisableButton();
                    }
                    else if (response.status == false)
                    {
                        $('#tel-error').show();
                        $('#phone-error').html('');

                        hasPhoneError = false;
                        enableDisableButton();
                    }
                });
            }
        }
        else
        {
            $('#tel-error').html('');
            $('#phone-error').html('');
            hasPhoneError = false;
            enableDisableButton();
        }
    }

    var countryData = $("#phone").intlTelInput("getSelectedCountryData");
    $('#defaultCountry').val(countryData.iso2);
    $('#carrierCode').val(countryData.dialCode);

    $("#phone").on("countrychange", function(e, countryData)
    {
        $('#defaultCountry').val(countryData.iso2);
        $('#carrierCode').val(countryData.dialCode);
        formattedPhone();
        var id = $('#id').val();
        //Invalid Phone Number Validation
        checkInvalidAndDuplicatePhoneNumberForUserProfile($.trim($(this).val()), $.trim(countryData.dialCode), id);
    });

    //Duplicated Phone Number Validation
    $("#phone").on('blur', function(e)
    {
        formattedPhone();
        var id = $('#id').val();
        var phone = $(this).val().replace(/-|\s/g,""); //replaces 'whitespaces', 'hyphens'
        var phone = $(this).val().replace(/^0+/,"");  //replaces (leading zero - for BD phone number)
        var pluginCarrierCode = $(this).intlTelInput('getSelectedCountryData').dialCode;
        checkInvalidAndDuplicatePhoneNumberForUserProfile(phone, pluginCarrierCode, id);
    });

    // Validate Emal via Ajax
    $(document).ready(function() {

        $("#email").on('blur', function(e) {

            var email = $(this).val();
            var id = $('#id').val();

            emptyEmail();

            if (!validateEmail(email)) {
                $('#email').parent('div').append('<span id="email_error" style="font-weight: bold" class="error">Please enter a valid email.</span>');
                hasEmailError = false;
                enableDisableButton();
                return false;
            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: emailCheckAjaxUrl,
                dataType: "json",
                data: {
                    'email': email,
                    'agent_id': id,
                }
            }).done(function(response) {
                if (response.status == true) {
                    $('#email').parent('div').append('<span id="email_error" style="font-weight: bold" class="error">' + response.fail + '</span>');
                    hasEmailError = true;
                    enableDisableButton();
                } else if (response.status == false) {
                    emptyEmail();
                    hasEmailError = false;
                    enableDisableButton();
                }
            });
        });
    });

    var pwd1 = document.getElementById("password"), 
    pwd2 = document.getElementById("confirm_password");
    function confirmPwd() {
    if (pwd1.value != pwd2.value) {
        pwd2.setCustomValidity("Passwords Don't Match");
    } else {
        pwd2.setCustomValidity('');
    }
    }
    pwd1.onchange = confirmPwd;
    pwd2.onkeyup = confirmPwd;
}

if ($('.content').find('#deposit').length) {
    $(".select2").select2({});

    function restrictNumberToPrefdecimalOnInput(e)
    {
        var type = $('select#currency_id').find(':selected').data('type')
        restrictNumberToPrefdecimal(e, type);
    }

    function determineDecimalPoint() {
        
        var currencyType = $('select#currency_id').find(':selected').data('type')

        if (currencyType == 'fiat') {
            
            $('.pFees, .fFees, .total_fees').text(FIATDP);
            $("#amount").attr('placeholder', FIATDP);
        }
    }

    $(window).on('load', function (e) {
        determineDecimalPoint();
        checkAmountLimitAndFeesLimit();
    });

    $(document).on('input', '.amount', function (e) {
        checkAmountLimitAndFeesLimit();
    });

    $(document).on('change', '.wallet', function (e) {
        determineDecimalPoint();
        checkAmountLimitAndFeesLimit();
    });

    function checkAmountLimitAndFeesLimit() {
        var token = $('input[name = _token]').val();
        var amount = $('#amount').val();
        var currency_id = $('#currency_id').val();
        var payment_method_id = $('#payment_method').val();

        $.ajax({
            method: "POST",
            url: ajaxUrl,
            dataType: "json",
            data: {
                "_token": token,
                'amount': amount,
                'currency_id': currency_id,
                'payment_method_id': payment_method_id,
                'agent_id': agentId,
                'transaction_type_id': transactionTypeId
            }
        }).done(function(response) {
            if (response.success.status == 200) {
                $("#percentage_fee").val(response.success.feesPercentage);
                $("#fixed_fee").val(response.success.feesFixed);
                $(".percentage_fees").html(response.success.feesPercentage);
                $(".fixed_fees").html(response.success.feesFixed);
                $(".total_fees").val(response.success.totalFees);
                $('.total_fees').html(response.success.totalFeesHtml);
                $('.pFees').html(response.success.pFeesHtml);
                $('.fFees').html(response.success.fFeesHtml);

                $('.amountLimit').text('');
                $("#deposit-create").attr("disabled", false);
                return true;
            } else {
                if (amount == '') {
                    $('.amountLimit').text('');
                } else {
                    $('.amountLimit').text(response.success.message);
                    $("#deposit-create").attr("disabled", true);
                    return false;
                }
            }
        });
    }
}

if ($('.content').find('#revenue').length) {
    $(".select2").select2({});

    var sDate;
    var eDate;

    //Date range as a button
    $('#daterange-btn').daterangepicker({
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate: moment()
    },
        function (start, end) {
            var sessionDate = dateFormateType;;
            var sessionDateFinal = sessionDate.toUpperCase();

            sDate = moment(start, 'MMMM D, YYYY').format(sessionDateFinal);
            $('#startfrom').val(sDate);

            eDate = moment(end, 'MMMM D, YYYY').format(sessionDateFinal);
            $('#endto').val(eDate);

            $('#daterange-btn span').html('&nbsp;' + sDate + ' - ' + eDate + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
        }
    )

    $(document).ready(function () {
        $("#daterange-btn").mouseover(function () {
            $(this).css('background-color', 'white');
            $(this).css('border-color', 'grey !important');
        });

        var startDate = formDate;
        var endDate = toDate;
        if (startDate == '') {
            $('#daterange-btn span').html('<i class="fa fa-calendar"></i>'+ ' ' + pickDateRange + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
        } else {
            $('#daterange-btn span').html(startDate + ' - ' + endDate + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
        }
    });

    // csv
    $(document).ready(function () {
        $('#csv').on('click', function (event) {
            event.preventDefault();
            var startfrom = $('#startfrom').val();
            var endto = $('#endto').val();
            var currency = $('#currency').val();
            var type = $('#type').val();
            var user_id = $('#user_id').val();
            window.location = SITE_URL + "/admin/agents/revenues/csv?startfrom=" + startfrom +
                "&endto=" + endto +
                "&currency=" + currency +
                "&type=" + type +
                "&user_id=" + user_id;
        });
    });

    // pdf
    $(document).ready(function () {
        $('#pdf').on('click', function (event) {
            event.preventDefault();
            var startfrom = $('#startfrom').val();
            var endto = $('#endto').val();
            var currency = $('#currency').val();
            var type = $('#type').val();
            var user_id = $('#user_id').val();
            window.location = SITE_URL + "/admin/agents/revenues/pdf?startfrom=" + startfrom +
                "&endto=" + endto +
                "&currency=" + currency +
                "&type=" + type +
                "&user_id=" + user_id;
        });
    });

    $("#user_input").on('keyup keypress', function (e) {
        if (e.type == "keyup" || e.type == "keypress") {
            var user_input = $('form').find("input[type='text']").val();
            if (user_input.length === 0) {
                $('#user_id').val('');
                $('#error-user').html('');
                $('form').find("button[type='submit']").prop('disabled', false);
            }
        }
    });

    $('#user_input').autocomplete({
        source: function (req, res) {
            if (req.term.length > 0) {
                $.ajax({
                    url: ajaxUrl,
                    dataType: 'json',
                    type: 'get',
                    data: {
                        search: req.term
                    },
                    success: function (response) {
                        $('form').find("button[type='submit']").prop('disabled', true);
                        if (response.status == 'success') {
                            res($.map(response.data, function (item) {
                                return {
                                    id: item.agent_id,
                                    first_name: item.first_name,
                                    last_name: item.last_name,
                                    value: item.first_name + ' ' + item.last_name
                                }
                            }));
                        } else if (response.status == 'fail') {
                            $('#error-user').addClass('text-danger').html(userDoesntExist);
                        }
                    }
                })
            } else {
                console.log(req.term.length);
                $('#user_id').val('');
            }
        },
        select: function (event, ui) {
            var e = ui.item;
            $('#error-user').html('');
            $('#user_id').val(e.id);
            $('form').find("button[type='submit']").prop('disabled', false);
        },
        minLength: 0,
        autoFocus: true
    });
}

if ($('.content').find('#transaction').length) {
    $(".select2").select2({});

    var sDate;
    var eDate;

    //Date range as a button
    $('#daterange-btn').daterangepicker({
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
                    'month')]
            },
            startDate: moment().subtract(29, 'days'),
            endDate: moment()
        },
        function(start, end) {
            var sessionDate = dateFormateType;
            var sessionDateFinal = sessionDate.toUpperCase();

            sDate = moment(start, 'MMMM D, YYYY').format(sessionDateFinal);
            $('#startfrom').val(sDate);

            eDate = moment(end, 'MMMM D, YYYY').format(sessionDateFinal);
            $('#endto').val(eDate);

            $('#daterange-btn span').html('&nbsp;' + sDate + ' - ' + eDate + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
        }
    )

    $(document).ready(function() {
        $("#daterange-btn").mouseover(function() {
            $(this).css('background-color', 'white');
            $(this).css('border-color', 'grey !important');
        });

        var startDate = formDate;
        var endDate = toDate;

        if (startDate == '') {
            $('#daterange-btn span').html('<i class="fa fa-calendar"></i> ' + ' ' + pickDateRange);
        } else {
            $('#daterange-btn span').html(startDate + ' - ' + endDate + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
        }
    });
}
if ($('.content').find('#wallet').length) {
    $(function() {
        $("#eachagentwallet").DataTable({
            "order": [],
            "language": language,
            "pageLength": pageLength
        });
    });
}