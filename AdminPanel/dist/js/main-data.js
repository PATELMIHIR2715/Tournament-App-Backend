$(function() {

    'use strict';

    // UPDATE MAIN DATA
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
            dataType: "json",
            success: function(data) {

                // show toast message
                showToastMessage(data.msg, data.status);
                
                if(data.logs !== ""){
                    alert(data.logs);
                }
                
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
    
    $('#payment_gateway').on('change', function() {
        
        // hide previous selected payment gateway fields
        $("#payment_div_"+gatewayId).css("display", "none");
        
        gatewayId = this.value;
        
        $("#payment_div_"+gatewayId).css("display", "initial");
    });
});