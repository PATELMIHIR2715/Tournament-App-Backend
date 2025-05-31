$(function() {
    
    'use strict';
    
    // LOGIN USER
    // consol.log("mihir")
    $("#login_user").submit(function(event) {

        event.preventDefault();

        // create FormData object
        var formData = new FormData(this);

        // disable submit btn while sending data to server
        $("#sign_in_btn").attr("disabled", true);

        // show progresss bar on submit button while sending data to server
        $("#sign_in_btn").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Checking');

        $.ajax({
            url: 'APIs/index.php',
            type: 'POST',
            data: formData,
            dataType: "json",
            success: function(data) {

                // show toast message
                showToastMessage(data.msg, data.status);

                // check status. status = 1 means data added
                if (data.status == 1) {
                    window.location.href = 'dashboard.php';
                } else {
                    // enable submit btn while sending data to server
                    $("#sign_in_btn").attr("disabled", false);

                    // hide progress bar and make button normal
                    $("#sign_in_btn").html('Sign In');
                }

            },
            error: function(data,status){
                console.log(data)
                console.log(status)
                // window.location.href = 'dashboard.php';
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });
});