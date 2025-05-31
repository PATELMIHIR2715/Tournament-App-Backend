$(function() {

    'use strict';

    $('#data_rable').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": false,
        "scrollX": true,
        "processing": true,
        "order": [
            [0, "desc"]
        ],
        "serverSide": true,
        "serverMethod": 'post',
        "ajax": {
            'url': 'APIs/index.php',
            'data': {
                'from': 'get_transactions',
                'user_id': userId,
            }
        },
        'columns': [{
                data: 'id'
            },
            {
                data: 'fullname'
            },
            {
                data: 'amount'
            },
            {
                data: 'date_time'
            },
            
            {
                data: 'reciept_no'
            },
            {
                data: 'title'
            },
            {
                data: 'message'
            }
        ],
        dom: 'Blfrtip',
        "lengthChange": true,
        "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ],

        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],

    }).buttons().container().appendTo('#data_rable_wrapper .col-md-6:eq(0)');

    $("#main_data_form").submit(function(event) {

        event.preventDefault();

        // create FormData object
        var formData = new FormData(this);

        // disable submit btn while sending data to server
        $("#save").attr("disabled", true);

        // show progresss bar on submit button while sending data to server
        $("#save").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting');

        $.ajax({
            url: 'APIs/index.php',
            type: 'POST',
            data: formData,
            dataType: "text",
            success: function(data) {

                // show toast message
                showToastMessage(data.msg, data.status);

                $("#push_noti_modal").modal('hide');

                // enable submit btn while sending data to server
                $("#save").attr("disabled", false);

                // hide progress bar and make button normal
                $("#save").html('Submit');

            },
            cache: false,
            contentType: false,
            processData: false
        });
    });

});