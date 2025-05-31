$(function() {

    'use strict';

    // GETTING WITHDRW REQUESTS FROM SERVER AND SHOW IN DATA TABLE
    $('#data_rable').DataTable({
        "paging": true,
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
                'from': 'withdraw_requests',
            }
        },
        'columns': [{
                data: 'id'
            },
            {
                data: 'status'
            },
            {
                data: 'profileName'
            },
            {
                data: 'type'
            },
            {
                data: 'amount'
            },
            {
                data: 'mobile'
            },
            {
                data: 'account_no'
            },
            {
                data: 'fullname'
            },
            {
                data: 'bank_name'
            },
            {
                data: 'ifsc'
            },
            {
                data: 'date_time'
            },
        ],
        dom: 'Blfrtip',
        "lengthChange": true,
        "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ],

        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],

    }).buttons().container().appendTo('#data_rable_wrapper .col-md-6:eq(0)');


    // SHOW UPDATE WITHDRAW REQUEST MODAL
    $('#data_rable').on('click', '#changeStatus', function(e) {
        var getId = $(this).data('id');

        $("#acceptBtn").val(getId);
        $("#declineBtn").val(getId);

        $("#withdraw_action_modal").modal('show');

    });

    // ACCEPT WITHDRAW REQUEST
    $("#acceptBtn").click(function() {

        var withdrawId = $(this).val();
        var message = $("#message").val();

        $("#acceptBtn").attr("disabled", true);
        $("#acceptBtn").html('<span class="spinner-border" role="status"></span> Please wait...');

        $.ajax({
            url: 'APIs/index.php',
            type: 'POST',
            data: {
                'from': 'withdraw_action',
                'withdraw_id': withdrawId,
                'status': 'accepted',
                'message': message,
                'refund': 'false',
            },
            dataType: "json",
            success: function(data) {

                $("#withdraw_action_modal").modal('hide');

                $("#acceptBtn").attr("disabled", false);
                $("#acceptBtn").html('Accept');
                
                if(data.logs !== ""){
                    alert(data.logs);
                }
                
                showToastMessage(data.msg, data.status);

                // reload data table
                $('#data_rable').DataTable().ajax.reload();
            }
        });
    });

    // DECLINE WITHDRAW REQUEST
    $("#declineBtn").click(function() {

        var withdrawId = $(this).val();
        var message = $("#message").val();
        var refundStatus = $("#refund_status").val();

        $("#declineBtn").attr("disabled", true);
        $("#declineBtn").html('<span class="spinner-border" role="status"></span> Please wait...');

        $.ajax({
            url: 'APIs/index.php',
            type: 'POST',
            data: {
                'from': 'withdraw_action',
                'withdraw_id': withdrawId,
                'status': 'declined',
                'message': message,
                'refund': refundStatus,
            },
            dataType: "json",
            success: function(data) {

                $("#withdraw_action_modal").modal('hide');

                $("#declineBtn").attr("disabled", false);
                $("#declineBtn").html('Decline');

                showToastMessage(data.msg, data.status);

                // reload data table
                $('#data_rable').DataTable().ajax.reload();
            }
        });
    });

});