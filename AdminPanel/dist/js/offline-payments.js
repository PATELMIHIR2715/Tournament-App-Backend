$(function() {

    'use strict';

    // FETCH TOURNAMENT INTO DATA TABLE
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
                'from': 'get_offline_payments',
            }
        },
        'columns': [{
                data: 'id'
            },
            {
                data: 'action'
            },
            {
                data: 'status'
            },
            {
                data: 'fullname'
            },
            {
                data: 'screenshot'
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

    $('#data_rable').on('click', '.acceptBtn', function(e) {
        
        var getPaymentId = $(this).data('id');
        var userId = $(this).data('user_id');
        
        $("#acceptNowBtn").val(getPaymentId);
        $("#accepted_user_id").val(userId);
        
        $("#payment_accept_modal").modal('show');
        
        
    });
    
    $('#data_rable').on('click', '.declineBtn', function(e) {
        
        var getPaymentId = $(this).data('id');
        var userId = $(this).data('user_id');
        
        $("#declinedNowBtn").val(getPaymentId);
        $("#declined_user_id").val(userId);
        
        $("#decline_details").modal('show');
        
        
    });

    // Decline Payment
    $("#declinedNowBtn").click(function() {

        var paymentId = $("#declinedNowBtn").val();
        var reason = $("#declined_resson").val();
        var userId = $("#declined_user_id").val();

        $("#declinedNowBtn").attr("disabled", true);
        // show progresss bar on submit button while sending data to server
        $("#declinedNowBtn").html('<span class="spinner-border" role="status"></span> Please wait...');
        
        $.ajax({
            url: 'APIs/index.php',
            type: 'POST',
            data: {
                'from': 'update_payment_status',
                'payment_id': paymentId,
                'reason': reason,
                'user_id': userId,
                'status': 'declined',
            },
            dataType: "json",
            success: function(data) {
                
                $("#decline_details").modal('hide');
                
                showToastMessage(data.msg, data.status);
                
                $('#data_rable').DataTable().ajax.reload();
                
                $("#declinedNowBtn").attr("disabled", false);
                $("#declinedNowBtn").html('Decline Now');
            }
        });

    });
    
    // Accept Payment
    $("#acceptNowBtn").click(function() {

        var paymentId = $("#acceptNowBtn").val();
        var depositAmount = $("#deposit_amount").val();
        var userId = $("#accepted_user_id").val();

        $("#acceptNowBtn").attr("disabled", true);
        // show progresss bar on submit button while sending data to server
        $("#acceptNowBtn").html('<span class="spinner-border" role="status"></span> Please wait...');
        
        $.ajax({
            url: 'APIs/index.php',
            type: 'POST',
            data: {
                'from': 'update_payment_status',
                'payment_id': paymentId,
                'deposit_amount': depositAmount,
                'user_id': userId,
                'reason': '',
                'status': 'approved',
            },
            dataType: "json",
            success: function(data) {
                
                $("#payment_accept_modal").modal('hide');
                
                showToastMessage(data.msg, data.status);
                
                $('#data_rable').DataTable().ajax.reload();
                
                $("#acceptNowBtn").attr("disabled", false);
                $("#acceptNowBtn").html('Accept Now');
            }
        });

    });

});