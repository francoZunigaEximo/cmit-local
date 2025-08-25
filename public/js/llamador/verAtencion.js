$(function(){

    let principal = {
        clickAtencion: $('#clickAtencion'),
        verAtencion: $('#verAtencion')
    }

    $(document).on('click', '#clickAtencion', function(e){
        e.preventDefault();
        principal.verAtencion.modal('show');
    });

    $(document).on('click', '#clickCierreForzado', function(e){
        e.preventDefault();

        swal({
            title: "Cierre forzado de llamado a paciente",
            text: "Este cierre no tiene en cuenta el estado del examen. Cierra de manera forzada la atención de la misma",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {

            if(confirmar) {

                

                swal("Cierre realizado de manera correcta", {
                    icon: "success",
                })
            }
        });

    });



});