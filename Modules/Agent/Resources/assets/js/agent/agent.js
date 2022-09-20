"use strict";

if ($('.main-content').find('#profile').length) {
    //start - ajax image upload
    
    $('#file').change(function() {
        if ($(this).val() != '') {
            upload(this);
        }
    });

    function upload(img) {
        var form_data = new FormData();
        form_data.append('file', img.files[0]);
        form_data.append('_token', csrfToken);
        $('#loading').css('display', 'block');
        $.ajax({
            url: imageUploadUrl,
            data: form_data,
            type: 'POST',
            contentType: false,
            processData: false,
            cache: false,
            success: function(data) {
                if (data.fail) {
                    $('#profileImage').attr('src', profileImagePath);
                    $('#file-error').show().addClass('error').html(data.errors.file).css({
                        'color': 'red !important',
                        'font-size': '14px',
                        'font-weight': '800',
                        'padding-top': '5px',
                    });
                } else {
                    $('#file-error').hide();
                    $('#file_name').val(data);
                    $('#profileImage').attr('src', uploadImagePath + '/' + data);
                    $('#profileImageHeader').attr('src', uploadImagePath + '/' + data);
                }
                $('#loading').css('display', 'none');
            },
            error: function(xhr, status, error) {
                alert(xhr.responseText);
                $('#profileImage').attr('src', profileImagePath);
            }
        });
    }
    //end - ajax image upload
}
if ($('.main-content').find('#deposit').length) {
    //on load
    $(window).on('load', function() {
        $('#amount').val('')
    });

    $("#amount").on('input', function(e) {
        if ($(this).val() != '' && !(isNaN($(this).val()))) {
            getDepositFeesLimit();
        }
    });

    //Fees Limit check on currencies change
    $('#currencies').on('change', function(e) {
        getDepositFeesLimit();
    });

    //User ajax search 
    $('#user').select2({
        placeholder: "Search User",
        minimumInputLength: 1,
        ajax: {
            method: "POST",
            url: SITE_URL + "/agent/search-user",
            delay: 500,
            processResults: function(data) {
                return {
                    results: data
                };
            }
        }
    });

    function getDepositFeesLimit() {
        var token = csrfToken;
        var amount = $('#amount').val();
        var currency_id = $('#currencies').val();
        var user_id = $("#user").val();
        $.ajax({
            method: "POST",
            url: feesLimitCheckUrl,
            dataType: "json",
            data: {
                "token": token,
                'amount': amount,
                'currency_id': currency_id,
                'transaction_type_id': transactionType,
                'user_id': user_id,
            }
        }).done(function(response) {
            if (response.success.status == 200) {
                $("#percentage_fee").val(response.success.feesPercentage);

                $("#fixed_fee").val(response.success.feesFixed);

                $("#agent_p_fee").val(response.success.agentFee);

                $("#total_fees").val(response.success.totalFees);

                $("#total_amount").val(response.success.totalAmount);

                $(".fee").val(response.success.totalFees);

                $(".total_fees").html(response.success.totalFeesHtml);

                $('.pFees').html(response.success.pFeesHtml);

                $('.aFees').html(response.success.aFeesHtml);

                $('.fFees').html(response.success.fFeesHtml);

                $('#payment_method').val(response.success.payment_method);

                $('.amountLimit').text('');

                if (response.success.code == 402) {
                    $('#feesLimitError').html(response.success.mgs);
                    $("#send_money").attr('disabled', true);
                    $(".spinner").show();
                } else {
                    if ((response.success.totalAmount - response.success.agentFee) >= response.success.agentbalance) {
                        $('.amountLimit').html(errorTxt);
                        $('#send_money').attr('disabled', true);
                    } else {
                        $('.amountLimit').html('');
                        $('#send_money').removeAttr('disabled');
                    }
                }
            } else {
                if (amount == '') {
                    $('.amountLimit').text('');
                    $('#send_money').attr('disabled', false);
                } else {
                    $('.amountLimit').text(response.success.message);
                    $('#send_money').attr('disabled', true);
                    return false;
                }
            }
        });
    }
}
if ($('.main-content').find('#depositConfirm').length) {
    function depositPaymentBack()
    {
        window.history.back();
    }

    $('#cashPayment').validate({
        submitHandler: function(form) {
            $("#send_money").attr("disabled", true);
            $(".spinner").show();
            var pretext=$("#send_text").text();
            $("#send_text").text('Processing...');
            form.submit();

            setTimeout(function(){
                $("#send_money").removeAttr("disabled");
                $(".spinner").hide();
                $("#send_text").text(pretext);
            },10000);
        }
    });
}
if ($('.main-content').find('#depositSuccess').length) {
    $(document).ready(function() {
        window.history.pushState(null, "", window.location.href);
        window.onpopstate = function() {
            window.history.pushState(null, "", window.location.href);
        };
    });

    //disabling F5
    function disable_f5(e) {
        if ((e.which || e.keyCode) == 116) {
            e.preventDefault();
        }
    }

    $(document).ready(function() {
        $(document).bind("keydown", disable_f5);
    });

    //disabling ctrl+r
    function disable_ctrl_r(e) {
        if (e.keyCode == 82 && e.ctrlKey) {
            e.preventDefault();
        }
    }

    $(document).ready(function() {
        $(document).bind("keydown", disable_ctrl_r);
    });
}
if ($('.agent-login-bg').find('#forget').length) {
    $.validator.setDefaults({
        highlight: function (t) {
            $(t).parent("div").addClass("has-error");
        },
        unhighlight: function (t) {
            $(t).parent("div").removeClass("has-error");
        },
    }),
    $("#forget-password-form").validate({
        errorClass: "has-error",
        rules: { email: { required: !0, email: !0 } },
        submitHandler: function (t) {
            $("#agent-forget-password-submit-btn")
                .attr("disabled", !0)
                .click(function (t) {
                    t.preventDefault();
                }),
                $(".fa-spin").show(),
                $("#agent-forget-password-submit-btn-text").text("Submitting.."),
                t.submit();
        },
    });
}
if ($('.agent-login-bg').find('#password').length) {
    $.validator.setDefaults({
        highlight: function(element) {
            $(element).parent('div').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).parent('div').removeClass('has-error');
        },
    });
    
    $('#forget-password-form').validate({
        errorClass: "has-error",
        rules: {
            new_password: {
                required: true
            },
            confirm_new_password: {
                required: true
            }
        }
    });
}
if ($('.main-content').find('#transaction').length) {
    $(window).on('load', function() {
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
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                },
                startDate: moment().subtract(29, 'days'),
                endDate: moment(),
    
            },
            function(start, end) {
                sDate = moment(start, 'MMMM D, YYYY').format('DD-MM-YYYY');
                $('#startfrom').val(sDate);
                eDate = moment(end, 'MMMM D, YYYY').format('DD-MM-YYYY');
                $('#endto').val(eDate);
                $('#daterange-btn span').html(sDate + ' - ' + eDate);
            }
        )
    
        var startDate = formDate;
        var endDate = toDate;
        if (startDate == '') {
            $('#daterange-btn span').html('<i class="fa fa-calendar"></i>' + ' ' + pickDateRange);
        } else {
            $('#daterange-btn span').html(startDate + ' - ' + endDate);
        }
    });
}
if ($('.main-content').find('#addUser').length) {
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

    $(document).ready(function()
    {
        $("#phone").intlTelInput({
            separateDialCode: true,
            nationalMode: true,
            preferredCountries: ["us"],
            autoPlaceholder: "polite",
            placeholderNumberType: "MOBILE",
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/13.0.4/js/utils.js"
        });

        var countryData = $("#phone").intlTelInput("getSelectedCountryData");
        $('#defaultCountry').val(countryData.iso2);
        $('#carrierCode').val(countryData.dialCode);

        $("#phone").on("countrychange", function(e, countryData)
        {
            formattedPhone();
            $('#defaultCountry').val(countryData.iso2);
            $('#carrierCode').val(countryData.dialCode);

            if ($.trim($(this).val()) !== '') {
                if (!$(this).intlTelInput("isValidNumber")) {
                    $('#tel-error').addClass('error').html('Please enter a valid International Phone Number.').css("font-weight");
                    hasPhoneError = true;
                    enableDisableButton();
                    $('#phone-error').hide();
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
    $(document).ready(function()
    {
        $("input[name=phone]").on('blur', function(e)
        {
            formattedPhone();
            var errorPhoneSpan = document.getElementById('validatorPhoneError');
            if(errorPhoneSpan) {
                errorPhoneSpan.innerHTML = "";
            }

            if ($.trim($(this).val()) !== '') {
                if (!$(this).intlTelInput("isValidNumber")) {
                    $('#tel-error').addClass('error').html('Please enter a valid International Phone Number.').css("font-weight");
                    hasPhoneError = true;
                    enableDisableButton();
                    $('#phone-error').hide();
                } else {
                    var phone = $(this).val().replace(/-|\s/g,"");
                    var phone = $(this).val().replace(/^0+/,"");

                    var pluginCarrierCode = $('#phone').intlTelInput('getSelectedCountryData').dialCode;
                    var myFormattedPhone = '+'+pluginCarrierCode+phone;
                    $.ajax({
                        headers:
                        {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        method: "POST",
                        url: ajaxUrlPhoneCheck,
                        dataType: "json",
                        data: {
                            'phone': phone,
                            'carrierCode': pluginCarrierCode,
                            'formattedPhone': myFormattedPhone,
                        }
                    }) .done(function(response) {
                        if (response.status == false) {
                            if(phone.length == 0) {
                                $('#phone-error').html('');
                            } else {
                                $('#phone-error').addClass('error').html(response.message).css("font-weight");
                                hasPhoneError = true;
                                enableDisableButton();
                            }
                        } else if (response.status == true) {
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


    function formattedPhone()
    {
        if ($('#phone').val != '') {
            var p = $('#phone').intlTelInput("getNumber").replace(/-|\s/g,"");
            $("#formattedPhone").val(p);
        }
    }

        // Validate Email via Ajax
    $(document).ready(function()
    {
        $("#email").on('input', function(e)
        {
            var email = $('#email').val();
            var errorEmailSpan = document.getElementById('validatorEmailError');
            if(errorEmailSpan) {
                errorEmailSpan.innerHTML = "";
            }
            $.ajax({
                headers:
                {
                    'X-CSRF-TOKEN': csrfToken
                },
                method: "POST",
                url: ajaxUrlEmailCheck,
                dataType: "json",
                data: {
                    'email': email,
                }
            })
            .done(function(response)
            {
                if (response.status == false) {
                    emptyEmail();
                    if (validateEmail(email)) {
                        $('#email_error').addClass('error').html(response.message).css("font-weight");
                        $('#email_ok').html('');
                        hasEmailError = true;
                        enableDisableButton();
                    } else {
                        $('#email_error').html('');
                    }
                } else if (response.status == true) {
                    emptyEmail();
                    if (validateEmail(email)) {
                        $('#email_error').html('');
                    } else {
                        $('#email_ok').html('');
                    }
                    hasEmailError = false;
                    enableDisableButton();
                }

                /**
                 * [validateEmail description]
                 * @param  {null} email [regular expression for email pattern]
                 * @return {null}
                 */
                function validateEmail(email) {
                var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(email);
                }

                /**
                 * [checks whether email value is empty or not]
                 * @return {void}
                 */
                function emptyEmail() {
                    if( email.length === 0 ) {
                        $('#email_error').html('');
                        $('#email_ok').html('');
                    }
                }
            });
        });
    });

    /**
     * [check submit button should be disabled or not]
     * @return {void}
     */
    function enableDisableButton()
    {
        if (!hasPhoneError && !hasEmailError) {
            $('form').find("button[type='submit']").prop('disabled',false);       
        } else {
            $('form').find("button[type='submit']").prop('disabled',true);
        }
    }
}
if ($('.main-content').find('#deleteUser').length) {
    // delete script for href
    $(document).on('click', '.delete-warning', function(e) {
        e.preventDefault();
        var url = ajaxUrl;
        $('#delete-modal-yes').attr('href', url);
        $('#delete-warning-modal').modal('show');
    });
}
if ($('.main-content').find('#editUser').length) {
    // flag for button disable/enable
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

    $(function () {
        $("#phone").intlTelInput({
            separateDialCode: true,
            nationalMode: true,
            preferredCountries: ["us"],
            autoPlaceholder: "polite",
            placeholderNumberType: "MOBILE",
            formatOnDisplay: false,
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/13.0.4/js/utils.js"
        })
        .done(function () {
            let formattedPhone = formatted;
            let carrierCode = userCarrierCode;
            let defaultCountry = defaultCountryCode;
            if (formattedPhone !== null && carrierCode !== null && defaultCountry !== null) {
                $("#phone").intlTelInput("setNumber", formattedPhone);
                $('#user_defaultCountry').val(defaultCountry);
                $('#user_carrierCode').val(carrierCode);
            }
        });
    });

    /**
     * [check submit button should be disabled or not]
     * @return {void}
    */
    function enableDisableButton() {
        if (!hasPhoneError && !hasEmailError) {
            $('form').find("button[type='submit']").prop('disabled', false);
        } else {
            $('form').find("button[type='submit']").prop('disabled', true);
        }
    }

    function formattedPhone() {
        if ($('#phone').val != '') {
            let p = $('#phone').intlTelInput("getNumber").replace(/-|\s/g, "");
            $("#formattedPhone").val(p);
        }
    }

    /*
    intlTelInput
    */

    function checkInvalidAndDuplicatePhoneNumberForUserProfile(phoneVal, phoneData, userId) {
        var that = $("input[name=phone]");
        if ($.trim(that.val()) !== '') {
            if (!that.intlTelInput("isValidNumber") || !isValidPhoneNumber($.trim(that.val()))) {
                $('#tel-error').addClass('error').html('Please enter a valid International Phone Number.').css("font-weight", "bold");
                hasPhoneError = true;
                enableDisableButton();
                $('#phone-error').hide();
            }
            else {
                $('#tel-error').html('');
                $.ajax({
                    headers:
                    {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: "POST",
                    url: ajaxUrlPhoneCheck,
                    dataType: "json",
                    cache: false,
                    data: {
                        'phone': phoneVal,
                        'carrierCode': phoneData,
                        'id': userId,
                    }
                })
                    .done(function (response) {
                        if (response.status == true) {
                            $('#tel-error').html('');
                            $('#phone-error').show();

                            $('#phone-error').addClass('error').html(response.fail).css("font-weight", "bold");
                            hasPhoneError = true;
                            enableDisableButton();
                        }
                        else if (response.status == false) {
                            $('#tel-error').show();
                            $('#phone-error').html('');

                            hasPhoneError = false;
                            enableDisableButton();
                        }
                    });
            }
        }
        else {
            $('#tel-error').html('');
            $('#phone-error').html('');
            hasPhoneError = false;
            enableDisableButton();
        }
    }

    var countryData = $("#phone").intlTelInput("getSelectedCountryData");
    $('#user_defaultCountry').val(countryData.iso2);
    $('#user_carrierCode').val(countryData.dialCode);

    $("#phone").on("countrychange", function (e, countryData) {
        $('#user_defaultCountry').val(countryData.iso2);
        $('#user_carrierCode').val(countryData.dialCode);
        formattedPhone();
        var id = $('#id').val();
        //Invalid Phone Number Validation
        checkInvalidAndDuplicatePhoneNumberForUserProfile($.trim($(this).val()), $.trim(countryData.dialCode), id);
    });

    //Duplicated Phone Number Validation
    $("#phone").on('blur', function (e) {
        formattedPhone();
        var id = $('#id').val();
        var phone = $(this).val().replace(/-|\s/g, ""); //replaces 'whitespaces', 'hyphens'
        var phone = $(this).val().replace(/^0+/, "");  //replaces (leading zero - for BD phone number)
        var pluginCarrierCode = $(this).intlTelInput('getSelectedCountryData').dialCode;
        checkInvalidAndDuplicatePhoneNumberForUserProfile(phone, pluginCarrierCode, id);
    });
    /*
    intlTelInput
    */

    // Validate email via Ajax
    $(document).ready(function () {
        $("#email").on('input', function (e) {
            var email = $(this).val();
            var id = $('#id').val();
            $.ajax({
                headers:
                {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: ajaxUrlEmailCheck,
                dataType: "json",
                data: {
                    'email': email,
                    'user_id': id,
                }
            })
                .done(function (response) {
                    emptyEmail(email);
                    if (response.status == true) {

                        if (validateEmail(email)) {
                            $('#emailError').addClass('error').html(response.fail).css("font-weight", "bold");
                            $('#email-ok').html('');
                            hasEmailError = true;
                            enableDisableButton();
                        } else {
                            $('#emailError').html('');
                        }
                    }
                    else if (response.status == false) {
                        hasEmailError = false;
                        enableDisableButton();
                        if (validateEmail(email)) {
                            $('#emailError').html('');
                        } else {
                            $('#email-ok').html('');
                        }
                    }

                    /**
                     * [validateEmail description]
                     * @param  {null} email [regular expression for email pattern]
                     * @return {null}
                     */
                    function validateEmail(email) {
                        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                        return re.test(email);
                    }

                    /**
                     * [checks whether email value is empty or not]
                     * @return {void}
                     */
                    function emptyEmail(email) {
                        if (email.length === 0) {
                            $('#emailError').html('');
                            $('#email-ok').html('');
                        }
                    }
                });
        });
    });
}
if ($('.main-content').find('#withdrawal').length) {
        //on load
    $(window).on('load', function() {
        $('#amount').val('')
    });

    $("input[name=amount]").on('input', function(e) {
        if ($(this).val() != '' && !(isNaN($(this).val()))) {
            getPayoutFeesLimit();
        }
    });

    //User ajax search 
    $('.select2user').select2({
        placeholder: userTxt,
        minimumInputLength: 1,
        ajax: {
            method: "POST",
            url: searchUserkUrl,
            delay: 500,
            processResults: function(data) {
                return {
                    results: data
                };
            }
        }
    });

    function getPayoutFeesLimit() {
        var token = csrfToken;
        var amount = $('#amount').val();
        var currency_id = $('#currencies').val();
        var user_id = $("#user").val();

        $.ajax({
            method: "POST",
            url: feesLimitCheckUrl,
            dataType: "json",
            data: {
                "token": token,
                'amount': amount,
                'currency_id': currency_id,
                'transaction_type_id': transactionType,
                'user_id': user_id
            }
        }).done(function(response) {
            if (response.success.status == 200) {

                $("#percentage_fee").val(response.success.feesPercentage);

                $("#fixed_fee").val(response.success.feesFixed);

                $("#agent_p_fee").val(response.success.agentFee);

                $("#total_fees").val(response.success.totalFees);

                $("#total_amount").val(response.success.totalAmount);

                $(".fee").val(response.success.totalFees);

                $(".total_fees").html(response.success.totalFeesHtml);

                $('.pFees').html(response.success.pFeesHtml);

                $('.aFees').html(response.success.aFeesHtml);

                $('.fFees').html(response.success.fFeesHtml);

                $('#payment_method').val(response.success.payment_method);

                $('.amountLimit').text('');

                //checking balance
                if (response.success.code == 401) {
                    $('#withdrawMoney').attr('disabled', true);
                    $(".spinner").show();
                } else {
                    if (response.success.totalAmount > response.success.balance) {
                        $('#amountLimit').html(errorTxt);
                        $('#withdrawMoney').attr('disabled', true);
                    } else {
                        $('#amountLimit').html('');
                        $('#withdrawMoney').removeAttr('disabled');
                    }
                }
            } else {
                if (amount == '') {
                    $('.amountLimit').text('');
                    $('#withdrawMoney').attr('disabled', false);
                } else {
                    $('.amountLimit').text(response.success.message);
                    $('#withdrawMoney').attr('disabled', true);
                    return false;
                }
            }
        });
    }

    //Fees Limit check on currencies change
    $('#currencies').on('change', function(e) {
        getPayoutFeesLimit();
    });

    // As user need to have valid wallet
    $('.select2user').on('select2:select', function(e) {

        $('#currencies').empty();

        $('#amount').val("");
        $('.amountLimit').text('');
        $('#withdrawMoney').attr('disabled', false);

        var user_id = e.params.data.id;
        var token = csrfToken;

        $.ajax({
            method: "POST",
            url: withdrawCurrencylistUrl,
            dataType: "json",
            data: {
                "token": token,
                'user_id': user_id
            }
        }).done(function(response) {
            if (response.currencyList.length != 0) {
                $.each(response.currencyList, function(key, value) {
                    var newOption = new Option(value.code, value.id);
                    $("#currencies").append(newOption);
                });
            } else {
                $('#noAvailableCurrency').html(currencyNotFoundTxt);
                $('#withdrawMoney').attr('disabled', true);
            }
        });
    });
}
if ($('.main-content').find('#withdrawalConfirm').length) {
    function payoutPaymentBack() {
        window.history.back();
    }

    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();   
    });

    $("input[name=payout_verify_code]").on('input', function(e) {
        if ($(this).val().length == 6) {
            payoutVerification();
        }
    });

    function payoutVerification() {
        var verification_code = $("#payout_verify_code").val();
        var token = csrfToken;
        var user_id = $("#user_id").val();
        $.ajax({
            method: "POST",
            url: verifyCodeCheckUrl,
            dataType: "json",
            data: {
                "token": token,
                'user_id': user_id,
                'verification_code': verification_code
            }
        }).done(function(response) {

            if (response.success.status == 200) {
                $('#withdrawMoneyConfirm').attr('disabled', false);
                $("#verification_message").text(" ");
            } else {
                $("#withdrawMoneyConfirm").attr("disabled", true);
                $("#verification_message").text(errorTxt);
            }
        });

    }

    var isNumberOrDecimalPointKey = function(value, e) {

        var charCode = (e.which) ? e.which : e.keyCode;

        if (charCode == 46) {
            if (value.value.indexOf('.') === -1) {
                return true;
            } else {
                return false;
            }
        } else {
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
        }
        return true;
    }

    $('button[type=submit]').click(function() {
        $(this).attr('disabled', 'disabled');
        $(this).parents('form').submit();
    });

    function startTimer(duration, display) {
        var timer = duration, minutes, seconds;
        setInterval(function () {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            display.text(minutes + ":" + seconds);

            if (--timer < 0) {
                timer = duration;
            }
        }, 1000);
    }

    jQuery(function ($) {
        var fiveMinutes = 60 * 5,
            display = $('#time');
        startTimer(fiveMinutes, display);
    });
}
if ($('.main-content').find('#withdrawalSuccess').length) {
    $(document).ready(function() {
        window.history.pushState(null, "", window.location.href);
        window.onpopstate = function() {
            window.history.pushState(null, "", window.location.href);
        };
    });

    //disabling F5
    function disable_f5(e) {
        if ((e.which || e.keyCode) == 116) {
            e.preventDefault();
        }
    }
    $(document).ready(function() {
        $(document).bind("keydown", disable_f5);
    });

    //disabling ctrl+r
    function disable_ctrl_r(e) {
        if (e.keyCode == 82 && e.ctrlKey) {
            e.preventDefault();
        }
    }

    $(document).ready(function() {
        $(document).bind("keydown", disable_ctrl_r);
    });
}
