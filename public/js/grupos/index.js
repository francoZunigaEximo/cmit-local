$(function(){
    $(".btnExcel").on("click", function (e) {
        preloader('on');
        buscar = $('#nombregrupo').val();
       
        $.get(exportarExcel,
            {
                buscar: buscar
            })
            .done(function (response) {
                preloader('off');
                createFile("excel", response.filePath, generarCodigoAleatorio() + "_grupo_clientes");
                preloader('off');
                toastr.success(response.msg);
                return;
            })
            .fail(function (jqXHR) {
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);
                checkError(jqXHR.status, errorData.msg);
                return;
            });
    });
});

function eliminarGrupoEstudio(id) {
    swal({
        title: "¿Está seguro que desea eliminar el grupo de clientes?",
        icon: "warning",
        buttons: ["Cancelar", "Eliminar"],
    }).then((aceptar) => {
        if (aceptar) {
            preloader('on');
            $.post(deleteGrupoCliente,{
                _token: TOKEN,
                id: id
            })
            .done(function(){
                preloader('off');
                $('.buscarGrupos').trigger("click");

            }).fail(function (jqXHR) {
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);
                checkError(jqXHR.status, errorData.msg);
                return;
            });
        };
    });
}