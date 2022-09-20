<script type="text/javascript">
    $(document).on('click', '.show_area', function(e) {
        e.preventDefault();

        var trans_id = $(this).attr('trans-id');
        var row_id = $(this).attr('id');
        var preRowId = (parseInt(row_id) - 1);

        $.ajax({
            method: "POST",
            url: SITE_URL + "/agent/get_transaction",
            dataType: "json",
            data: {
                id: trans_id
            },
            beforeSend: function() {
                $('.preloader').show();
            },
        })
        .complete(function() {
            $('.preloader').hide();
        })
        .done(function(response) {
            $("#total_" + row_id).html(response.total);
            $("#html_" + row_id).html(response.html);
        })
        .fail(function(error) {
            console.log(error);
        });

        var totalClick = parseInt($(this).attr('click')) + 1;
        $(this).attr('click', totalClick);
        var nowClick = parseInt($(this).attr('click')) % 2;

        if (nowClick == 0) {
            $("#icon_" + row_id).removeClass("fa-arrow-circle-down").addClass("fa-arrow-circle-right");
        } else {
            $("#icon_" + row_id).removeClass('fa-arrow-circle-right').addClass("fa-arrow-circle-down");
        }
    });
</script>
