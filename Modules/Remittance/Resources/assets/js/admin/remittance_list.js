'use strict';

$(".select2").select2({});
  
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
function(start, end) {
   
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

// alert(startDate);
if (startDate == '') {
    $('#daterange-btn span').html('<i class="fa fa-calendar"></i> &nbsp;&nbsp; Pick a date range &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
} else {
    $('#daterange-btn span').html(startDate + ' - ' + endDate + '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
}

$("#user_input").on('keyup keypress', function(e) {
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
    source: function(req, res) {
        if (req.term.length > 0) {
            $.ajax({
                url: url,
                dataType: 'json',
                type: 'get',
                data: {
                    search: req.term
                },
                success: function(response) {
                    console.log(response);
                    // console.log(req.term.length);

                    $('form').find("button[type='submit']").prop('disabled', true);

                    if (response.status == 'success') {
                        res($.map(response.data, function(item) {
                            return {
                                id: item.sender_id, //user_id is defined
                                first_name: item.first_name, //first_name is defined
                                last_name: item.last_name, //last_name is defined
                                value: item.first_name + ' ' + item.last_name //don't change value
                            }
                        }));
                    } else if (response.status == 'fail') {
                        $('#error-user').addClass('text-danger').html(userNotExistError);
                    }
                }
            })
        } else {
            console.log(req.term.length);
            $('#user_id').val('');
        }
    },
    select: function(event, ui) {
        var e = ui.item;

        $('#error-user').html('');

        $('#user_id').val(e.id);

        $('form').find("button[type='submit']").prop('disabled', false);
    },
    minLength: 0,
    autoFocus: true
});
});

// csv
$(document).ready(function() {
$('#csv').on('click', function(event) {
    event.preventDefault();

    var startfrom = $('#startfrom').val();
    var endto = $('#endto').val();

    var status = $('#status').val();

    var currency = $('#currency').val();

    var payment_methods = $('#payment_methods').val();

    var user_id = $('#user_id').val();

    window.location = SITE_URL + "/" + ADMIN_PREFIX + "/remittances/csv?startfrom=" + startfrom +
        "&endto=" + endto +
        "&status=" + status +
        "&currency=" + currency +
        "&payment_methods=" + payment_methods +
        "&user_id=" + user_id;
});
});

// pdf
$(document).ready(function() {
$('#pdf').on('click', function(event) {

    event.preventDefault();

    var startfrom = $('#startfrom').val();

    var endto = $('#endto').val();

    var status = $('#status').val();

    var currency = $('#currency').val();

    var payment_methods = $('#payment_methods').val();

    var user_id = $('#user_id').val();

    window.location = SITE_URL + "/" + ADMIN_PREFIX + "/remittances/pdf?startfrom=" + startfrom +
        "&endto=" + endto +
        "&status=" + status +
        "&currency=" + currency +
        "&payment_methods=" + payment_methods +
        "&user_id=" + user_id;
});
});