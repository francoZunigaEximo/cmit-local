$(function() {

    $(document).on('click', '#btnWizardPrestacion', function(){

        let dniPrestacion = $('#dniPrestacion').val();

        if(!dniPrestacion){
            toastr.info('El campo del DNI es obligatorio', 'Atención');
            return;
        }

        if(dniPrestacion.length > 8 || dniPrestacion.length < 7){
            toastr.info('El DNI no debe contener mas de 8 digitos o menos de 7', 'Atención');
            return;
        }

        preloader('on');
        $.get(verifyWizard, { Documento: dniPrestacion })
            .done(function(response){
                preloader('off');
                if(response){
                    window.location.href = lnkExistePaciente.replace('__paciente__', response.Id);
                }else{
                    localStorage.setItem('insertDoc', dniPrestacion);
                    window.location.href = lnkNuevoPaciente;
                }

            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            });

    });
});