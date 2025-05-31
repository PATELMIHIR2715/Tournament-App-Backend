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
    
    // SEND NOTIFICATION
    $("#send_notification").submit(function(event) {

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