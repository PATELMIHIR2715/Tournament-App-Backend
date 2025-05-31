$(function() {
    
    'use strict';
    
    // BROWSE IMAGE      
    $(document).on("click", ".browse", function() {
        var file = $(this).parents().find(".file");
        file.trigger("click");
    });

    // GETTING AND SETTING IMAGE DETAILS
    $('input[type="file"]').change(function(e) {
        var fileName = e.target.files[0].name;
        $("#file").val(fileName);

        var reader = new FileReader();
        reader.onload = function(e) {
            // get loaded data and render thumbnail.
            document.getElementById("preview").src = e.target.result;
        };
        // read the image file as a data URL.
        reader.readAsDataURL(this.files[0]);
    });
    
    // GETTING USERS FROM SERVER AND SHOW IN DATA TABLE
    $('#users_data_rable').DataTable({
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
                'from': 'get_users',
                'type': type,
                'user_id': userId,
            }
        },
        'columns': [{
                data: 'id'
            },
            {
                data: 'action'
            },
            {
                data: 'fullname'
            },
            {
                data: 'transactions'
            },
            {
                data: 'profile_pic'
            },
            {
                data: 'email'
            },
            {
                data: 'mobile'
            },
            {
                data: 'password'
            },
            {
                data: 'register_date'
            },
            {
                data: 'login_date'
            },
            {
                data: 'referral_code'
            },
            {
                data: 'sponsor'
            },
            {
                data: 'bonus_amount'
            },
            {
                data: 'deposit_amount'
            },
            {
                data: 'win_amount'
            },
            {
                data: 'lifetime_winning'
            },
            {
                data: 'played_tournaments'
            },
            {
                data: 'won_tournaments'
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


    // SHOW PUSH NOTIFICATION MODAL
    $('#users_data_rable').on('click', '#sendNotification', function(e) {
        var getUserId = $(this).data('user_id');

        $("#user_id").val(getUserId);
        $("#push_noti_modal").modal('show');

    });

    // CHAGE USER STATUS (Block or Unblock User)
    $('#users_data_rable').on('click', '#userStatus', function(e) {
        var getUserId = $(this).data('user_id');

        $.ajax({
            url: 'APIs/index.php',
            type: 'POST',
            data: {
                'from': 'change_user_status',
                'user_id': getUserId,
            },
            dataType: "json",
            success: function(data) {

                // show toast message
                showToastMessage(data.msg, data.status);

                // reload data table
                $('#users_data_rable').DataTable().ajax.reload();
            }
        });

    });
    
    // Update User Wallet
    $('#users_data_rable').on('click', '#updateWalletBtn', function(e) {
        
        var getUserId = $(this).data('user_id');
        var depositAmount = $(this).data('deposit_amount');
        var winningAmount = $(this).data('winning_amount');
        var bonusAmount = $(this).data('bonus_amount');
        
        $("#updateWalletNowBtn").val(getUserId);
        $("#deposit_amount").val(depositAmount);
        $("#winning_amount").val(winningAmount);
        $("#bonus_amount").val(bonusAmount);
        
        $("#update_wallet_modal").modal('show');

    });
    
    // Accept Payment
    $("#updateWalletNowBtn").click(function() {

        var userId = $("#updateWalletNowBtn").val();
        var depositAmount = $("#deposit_amount").val();
        var winningAmount = $("#winning_amount").val();
        var bonusAmount = $("#bonus_amount").val();

        $("#updateWalletNowBtn").attr("disabled", true);
        
        // show progresss bar on submit button while sending data to server
        $("#updateWalletNowBtn").html('<span class="spinner-border" role="status"></span> Please wait...');
        
        $.ajax({
            url: 'APIs/index.php',
            type: 'POST',
            data: {
                'from': 'update_user_wallet',
                'deposit_amount': depositAmount,
                'winning_amount': winningAmount,
                'bonus_amount': bonusAmount,
                'user_id': userId,
            },
            dataType: "json",
            success: function(data) {
                
                $("#update_wallet_modal").modal('hide');
                
                showToastMessage(data.msg, data.status);
                
                $('#users_data_rable').DataTable().ajax.reload();
                
                $("#updateWalletNowBtn").attr("disabled", false);
                $("#updateWalletNowBtn").html('Update Now');
            }
        });

    });
    
    // SEND PUSH NOTIFICATION
    $("#send_notification_form").submit(function(event) {

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