$(document).on('click', '#vistaPrevia', function(e){
    let id = $("#Reporte").val();
    $("#imagenVistaPrevia").attr("src", "");
    preloader('on');
    $(".alertaModal").css("display", "none");
    $.get('../reporte/vistaprevia', {Id: parseInt(id)}).done(function(response){
        preloader('off');
        if(response.VistaPrevia){
            $("#imagenVistaPrevia").attr("src", "/archivos/reportes/"+response.VistaPrevia);
        }else{
            $(".alertaModal").css("display", "block");
        }
    }).fail(function(err){
        console.error(err)
    })
})
