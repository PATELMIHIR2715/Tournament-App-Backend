var prizesCount = 2;

$(function() {

    'use strict';

    // DATE & TIME RANGE PICKER
    $('#t_schedule').daterangepicker({
        timePicker: true,
        timePickerIncrement: 1,
        locale: {
            format: 'YYYY-MM-DD HH:mm:ss'
        }
    });
    
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

    // ADDING NEW TOURNAMENT
    $("#add_tournament_form").submit(function(event) {

        event.preventDefault();

        // create FormData object
        var formData = new FormData(this);

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
                    prizesCount = 0;
                    location.reload();
                }

            },
            error: function(data,status){
                console.log("hereerror")
                console.log(data)
                console.log(status)

                // enable submit btn while sending data to server
                $("#submitBtn").attr("disabled", false);

                // hide progress bar and make button normal
                $("#submitBtn").html('Submit');
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });

});

// ADD NEW PRICE FIELD
function addNewPrize() {

    var tr = '<tr  id = "im' + prizesCount + '">';
    
    tr += '<td><input required type="number" class="form-control" name="start_rank_'+prizesCount+'" placeholder = "Start Rank"></td>';
    tr += '<td><input required type="number" class="form-control" name="end_rank_'+prizesCount+'" placeholder = "End Rank"></td>';
    tr += '<td><input required type="number" class="form-control" name="amount_'+prizesCount+'" placeholder = "Amount"></td>';
    tr += '<td><a type="button" onclick= "removeRow(' + prizesCount + ')" class="fas fa-times-circle"><i class="fa fa-close"></i></a></td>';
    tr += '</tr>';
   
    $("#prizes_count").val(prizesCount);
    $("#prize_details").append(tr);

    prizesCount++;
}

// REMOVE PRICE ROW
function removeRow(id) {
    $("#im" + id).remove();
}