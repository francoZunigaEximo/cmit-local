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

        let llamado = $(this),
            prestacion = llamado.data('prestacion'),
            profesional = llamado.data('profesional');

        swal({
            title: "Cierre forzado de llamado a paciente",
            text: "Este cierre no tiene en cuenta el estado del examen. Cierra de manera forzada la atención de la misma",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {

            if(confirmar) {
                preloader('on');
                $.post(cierreForzadoLlamado, {_token: TOKEN, prestacion: prestacion, profesional: profesional})
                    .done(function(response){
                        preloader('off');
                        swal("Cierre realizado de manera correcta", {
                            icon: "success",
                        });

                    })
                    .fail(function(jqXHR){
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return;
                    })

                
            }
        });

    });



});