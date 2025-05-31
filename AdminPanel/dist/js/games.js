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
                'from': 'get_games',
            }
        },
        'columns': [{
                data: 'id'
            },
            {
                data: 'action'
            },
            {
                data: 'name'
            },
            {
                data: 'image'
            },
            {
                data: 'how_to_get_id'
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


    // SHOW EDIT GAME MODAL
    $('#data_rable').on('click', '.editGame', function(e) {
        var getGameId = $(this).data('id');
        var getName = $(this).data('name');
        var getImage = $(this).data('image');
        var getHowtoGetId = $(this).data('how_to_get_id');

        $("#game_id").val(getGameId);
        $("#game_name").val(getName);
        $("#preview").attr("src", getImage);
        $("#how_to_get_game_id").val(getHowtoGetId);

        $("#edit_game_modal").modal('show');

    });
    
    // SHOW DELETE CONFIRMATION DIALOG
    $('#data_rable').on('click', '.deleteGame', function(e) {
        
        $("#deleteGameBtn").val($(this).data('id'));
        $("#delete_game_modal").modal('show');
        
    });

    // EDIT GAME
    $("#update_game_form").submit(function(event) {

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

                // enable submit btn while sending data to server
                $("#submitBtn").attr("disabled", false);

                // hide progress bar and make button normal
                $("#submitBtn").html('Submit');

                $("#edit_game_modal").modal('hide');

                $('#data_rable').DataTable().ajax.reload();

                $("#game_image").val('');


            },
            cache: false,
            contentType: false,
            processData: false
        });
    });
    
    $("#deleteGameBtn").click(function() {

        $("#deleteGameBtn").attr("disabled", true);
        $("#deleteGameBtn").html('<span class="spinner-border" role="status"></span> Please wait...');

        $.ajax({
            url: 'APIs/index.php',
            type: 'POST',
            data: {
                'from': 'delete_game',
                'game_id': $(this).val(),
            },
            dataType: "json",
            success: function(data) {

                $("#delete_game_modal").modal('hide');

                $("#deleteGameBtn").attr("disabled", false);
                $("#deleteGameBtn").html('Yes');

                showToastMessage(data.msg, data.status);

                // reload data table
                $('#data_rable').DataTable().ajax.reload();
            }
        });
    });
});