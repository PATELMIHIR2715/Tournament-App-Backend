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
                'from': 'get_tournaments',
            }
        },
        'columns': [{
                data: 'id'
            },
            {
                data: 'action'
            },
            {
                data: 'image'
            },
            {
                data: 'win_ss'
            },
            {
                data: 'name'
            },
            {
                data: 'gameName'
            },
            {
                data: 'map'
            },
            {
                data: 'type'
            },
            {
                data: 'mode'
            },
            {
                data: 'entry_fees'
            },
            {
                data: 'prize_pool'
            },
            {
                data: 'per_kill'
            },
            {
                data: 'from_bonus'
            },
            {
                data: 'schedule'
            },
            {
                data: 'total_players'
            },
            {
                data: 'joined_players'
            },
            {
                data: 'details'
            },
            {
                data: 'room_id'
            },
            {
                data: 'message'
            },
            {
                data: 'youtube_video'
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

    // SEE JOINED PLAYERS In JOINED PLAYERS MODAL
    $('#data_rable').on('click', '.seeJoinedPlayers', function(e) {

        var getTournamentId = $(this).data('id');
        $("#joined_players_modal").modal('show');

        // getting joined players
        $.ajax({
            url: 'APIs/index.php',
            type: 'POST',
            data: {
                'from': 'get_joined_players',
                'tournament_id': getTournamentId
            },
            dataType: "json",
            success: function(data) {
                
                $("#joined_players_table").html('');
                var tr = "";

                for (var i = 0; i < data.length; i++) {
                    
                    var action = "<button class='btn btn-sm btn-danger' onclick = 'showDeleteModal(" + data[i].user_id + ", " + getTournamentId + ")'; data-user_id='" + data[i].user_id + "'; data-tournament_id='" + getTournamentId + "'><i class='fa fa-trash'></i></button>";
                    
                    tr += '<tr><td>' + (i + 1) + '.</td>';
                    tr += '<td>' + action + '</td>';
                    tr += '<td>' + data[i].date_time + '</td>';
                    tr += '<td>' + data[i].fullname + '</td>';
                    tr += '<td>' + data[i].game_username + '</td></tr>';
                }
                $("#joined_players_table").append(tr);
            }
        });
    });

    // SEE TOURNAMENT DETAILS IN A MODAL
    $('#data_rable').on('click', '.seeDetails', function(e) {

        var getTournamentId = $(this).data('id');
        var getDetails = $(this).data('details');

        $("#updateDetailsBtn").val(getTournamentId);
        $('#t_details').val(getDetails);
        $("#details_modal").modal('show');

    });

    // EDIT TOURNAMENT
    $('#data_rable').on('click', '.editTournament', function(e) {
        var getTournamentId = $(this).data('id');
        window.location.href = "edit-tournament.php?id=" + getTournamentId;
    });

    // TOURNAMENT ACTIONS Like Room Id, Prize Distributions, Tournament Cancel
    $('#data_rable').on('click', '.tournamentActions', function(e) {
        var getTournamentId = $(this).data('id');

        $("#roomIdBtn").val(getTournamentId);
        $("#prizeDistributeBtn").val(getTournamentId);
        $("#cancelTournamentBtn").val(getTournamentId);

        $("#actions_modal").modal('show');
    });

    // SHOW CANCEL TOURNAMENT MODAL
    $("#cancelTournamentBtn").click(function() {
        var getTournamentId = $(this).val();
        $("#yesBtn").val(getTournamentId);
        $("#cancel_tournament_modal").modal('show');
    });

    // CANCEL TOURNAMENT
    $("#yesBtn").click(function() {

        var getTournamentId = $(this).val();
        var getReason = $("#cancel_reason").val();
        $("#yesBtn").attr("disabled", true);

        // show progresss bar on submit button while sending data to server
        $("#yesBtn").html('<span class="spinner-border" role="status"></span> Please wait...');

        $.ajax({
            url: 'APIs/index.php',
            type: 'POST',
            data: {
                'from': 'cancel_tournament',
                'tournament_id': getTournamentId,
                'reason': getReason,
            },
            dataType: "json",
            success: function(data) {
                showToastMessage(data.msg, data.status);

                $("#cancel_tournament_modal").modal('hide');
                $("#actions_modal").modal('hide');

                $('#data_rable').DataTable().ajax.reload();

                $("#yesBtn").attr("disabled", false);
                $("#yesBtn").html('Yes');
            }
        });

    });

    // SHOW PRIZE DISTRIBUTIONS MODAL
    $("#prizeDistributeBtn").click(function() {

        var getTournamentId = $(this).val();
        $("#distributeNowBtn").val(getTournamentId);
        $("#distribute_prize_modal").modal('show');

        $.ajax({
            url: 'APIs/index.php',
            type: 'POST',
            data: {
                'from': 'get_joined_players',
                'tournament_id': getTournamentId,
                'type': 'distribute',
            },
            dataType: "json",
            success: function(data) {
                
                $("#prizes_table").html('');
                var tr = "";

                for (var i = 0; i < data.length; i++) {
                    
                    var disabledRank = "";
                    var disabledKills = "";
                    
                    if(data[i].won_amount > 0){
                        disabledRank = "disabled";
                    }
                    
                    if(data[i].kills > 0){
                        disabledKills = "disabled";
                    }
                    
                    var rank = "<input style='width:100px;' type='number' "+disabledRank+" class='form-control' id='rank_" + (i + 1) + "' value = '" + data[i].rank + "'>";
                    rank += "<input type='hidden' id='id_" + (i + 1) + "' value = '" + data[i].id + "'>";
                    
                    var kills = "<input style='width:100px;' type='number' "+disabledKills+" class='form-control' id='kills_" + (i + 1) + "' value = '" + data[i].kills + "'>";

                    tr += '<tr><td>' + (i + 1) + '.</td>';
                    tr += '<td>' + rank + '</td>';
                    tr += '<td>' + kills + '</td>';
                    tr += '<td>' + data[i].won_amount + '</td>';
                    tr += '<td>' + data[i].fullname + '</td>';
                    tr += '<td>' + data[i].game_username + '</td></tr>';
                }
                
                $("#prizes_table").append(tr);
            }
        });
    });

    // DISTRIBUTE PRIZE
    $("#distributeNowBtn").click(function() {

        var tournamentId = $(this).val();
        
        var generateRanks = [];
        //var generateKills = [];

        var rankCount = 1;
        //var killsCount = 1;
        
        while(true){
            if($("#rank_" + rankCount).length !== 0 && $("#id_" + rankCount).length !== 0) {
                
                var rankDetails = {id : $("#id_" + rankCount).val(), rank : $("#rank_" + rankCount).val(), kills : $("#kills_" + rankCount).val()};
                generateRanks[rankCount - 1] = rankDetails;
                
                // var killDetails = {id : $("#id_" + rankCount).val(), rank : $("#kills_" + rankCount).val()};
                // generateKills[rankCount - 1] = killDetails;
                
                rankCount++;
            }
            else{
                break;
            }
        }
        
        // while(true){
        //     if($("#kills_" + killsCount).length !== 0 && $("#id_" + killsCount).length !== 0) {
                
        //         var killDetails = {id : $("#id_" + killsCount).val(), rank : $("#kills_" + killsCount).val()};
        //         generateKills[killsCount - 1] = killDetails;
                
        //         killsCount++;
        //     }
        //     else{
        //         break;
        //     }
        // }
        
        $("#distributeNowBtn").attr("disabled", true);
        $("#distributeNowBtn").html('<span class="spinner-border" role="status"></span> Please wait...');

        $.ajax({
            url: 'APIs/index.php',
            type: 'POST',
            data: {
                'from': 'distribute_prize',
                'tournament_id': tournamentId,
                'ranks': generateRanks,
            },
            dataType: "json",
            success: function(data) {

                showToastMessage(data.msg, data.status);

                if (data.logs !== "") {
                    alert(data.logs);
                }
                if (data.status == 1) {
                    location.reload();
                }
            }
        });
    });

    // SHOW ROOM ID MODAL
    $("#roomIdBtn").click(function() {
        var getTournamentId = $(this).val();
        $("#sendRoomIdBtn").val(getTournamentId);
        $("#room_id_modal").modal('show');
    });

    // SEND ROOM ID
    $("#sendRoomIdBtn").click(function() {

        var getTournamentId = $(this).val();
        var getRoomId = $("#t_room_id").val();
        var getMessage = $("#t_message").val();
        var getYoutubeLink = $("#t_youtube_link").val();

        $("#sendRoomIdBtn").attr("disabled", true);
        // show progresss bar on submit button while sending data to server
        $("#sendRoomIdBtn").html('<span class="spinner-border" role="status"></span> Please wait...');

        $.ajax({
            url: 'APIs/index.php',
            type: 'POST',
            data: {
                'from': 'send_room_id',
                'tournament_id': getTournamentId,
                'room_id': getRoomId,
                'message': getMessage,
                'youtube': getYoutubeLink,
            },
            dataType: "json",
            success: function(data) {
                showToastMessage(data.msg, data.status);
                $('#data_rable').DataTable().ajax.reload();

                $("#sendRoomIdBtn").attr("disabled", false);
                $("#sendRoomIdBtn").html('Send Room Id');

                $("#room_id_modal").modal('hide');
                $("#actions_modal").modal('hide');
            }
        });
    });

    // UPDATE TOURNAME DETAILS
    $("#updateDetailsBtn").click(function() {

        var getTournamentId = $(this).val();
        var getDetails = $('#t_details').val();

        $("#updateDetailsBtn").attr("disabled", true);

        // show progresss bar on submit button while sending data to server
        $("#updateDetailsBtn").html('<span class="spinner-border" role="status"></span> Please wait...');

        $.ajax({
            url: 'APIs/index.php',
            type: 'POST',
            data: {
                'from': 'update_details',
                'details': getDetails,
                'tournament_id': getTournamentId,
            },
            dataType: "json",
            success: function(data) {
                showToastMessage(data.msg, data.status);
                $('#data_rable').DataTable().ajax.reload();

                $("#updateDetailsBtn").attr("disabled", false);
                $("#updateDetailsBtn").html('Update Details');
            }
        });

    });

});

// REMOVE JOINED PLAYER
function removePlayer(giveRefund) {

    var userId = $('#user_id').val();
    var tournamentId = $('#tournament_id').val();
    var getMessage = $('#remove_message').val();

    // disable submit btn while sending data to server
    $("#removeNoRefund").attr("disabled", true);
    $("#removeRefund").attr("disabled", true);

    // show progresss bar on submit button while sending data to server
    $("#removeNoRefund").html('<span class="spinner-border" role="status"></span> Please wait...');
    $("#removeRefund").html('<span class="spinner-border" role="status"></span> Please wait...');

    $.ajax({
        url: 'APIs/index.php',
        type: 'POST',
        data: {
            'from': 'remove_joined_user',
            'tournament_id': tournamentId,
            'user_id': userId,
            'message': getMessage,
            'give_refund': giveRefund,
        },
        dataType: "json",
        success: function(data) {

            // enable submit btn while sending data to server
            $("#removeNoRefund").attr("disabled", false);
            $("#removeRefund").attr("disabled", false);

            // hide progress bar and make button normal
            $("#removeNoRefund").html("Remove & Don't Refund");
            $("#removeRefund").html('Remove & Refund');

            if (data.status == 1) {
                $("#joined_players_modal").modal('hide');
                $("#delete_player_modal").modal('hide');
            }
            
            if(data.logs !== ""){
                alert(data.logs);
            }
            showToastMessage(data.msg, data.status);
        }
    });
}

// SHOW TOURNAMENT DETAILS MODAL
function showDeleteModal(userId, tournamentId) {

    $('#user_id').val(userId);
    $('#tournament_id').val(tournamentId);

    $("#delete_player_modal").modal('show');
}