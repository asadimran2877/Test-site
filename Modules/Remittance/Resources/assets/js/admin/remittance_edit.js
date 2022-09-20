'use strict';

$(".select2").select2({});

// disabling submit and cancel button after form submit
$(document).ready(function() {
    $('form').submit(function() {
        $("#remittances_edit").attr("disabled", true);

        $('#cancel_anchor').attr("disabled", "disabled");

        $(".fa-spin").show();

        $("#remittances_edit_text").text('Updating...');

        // Click False
        $('#remittances_edit').click(false);
        $('#cancel_anchor').click(false);
    });
});