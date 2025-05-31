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
    
    // ADDING NEW GAME
    $("#add_game_form").submit(function(event) {

        event.preventDefault();

        // create FormData object
        var formData = new FormData(this);
        console.log("heyyyyy");
        // disable submit btn while sending data to server
        $("#submitBtn").attr("disabled", true);

        // show progresss bar on submit button while sending data to server
        $("#submitBtn").html('<span class="spinner-border" role="status"></span> Please wait...');

        $.ajax({
            url: 'APIs/index.php',
            type: 'POST',
            data: formData,
            dataType: "json",
            success: function(data) {

                showToastMessage(data.msg, data.status);
                
                // show logs if any
                if(data.logs !== ""){
                    alert(data.logs);
                }
                
                // enable submit btn while sending data to server
                $("#submitBtn").attr("disabled", false);

                // hide progress bar and make button normal
                $("#submitBtn").html('Submit');

                if (data.status == 1) {
                    location.reload();
                }

            },
            error: function(data,status){
                console.log("hereerror")
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