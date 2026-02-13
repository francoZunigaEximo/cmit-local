$(document).on('click', '#altaPrestacionArtModal', function (e) {
    $('#altaPrestacionModal').modal('show');
    $(".checkPrestacion").hide();
    $("#ART").prop('checked', true).trigger("change");
    
});