$(document).ready(()=> {

    $(document).on('click', '#btnWizardPrestacion', function(){

        let dniPrestacion = $('#dniPrestacion').val();

        toastr.options = {
            closeButton: true,   
            progressBar: true,    
            timeOut: 3000,        
        };

        if(dniPrestacion === ''){

            toastr.info('El campo del DNI es obligatorio', 'Atención');
            return;
        }

        if(dniPrestacion.length > 8 || dniPrestacion.length < 7){

            toastr.info('El DNI no debe contener mas de 8 digitos o menos de 7', 'Atención');
            return;
        }

        $.get(checkDNI, { Documento: dniPrestacion })
            .done(function(response){
                let paciente = response.paciente;

                if(response.existe){

                    window.location.href = lnkExistePaciente.replace('__paciente__', paciente.Id);
                }else{
                    window.location.href = lnkNuevoPaciente.replace('__paciente__', dniPrestacion);
                }

            })
            .fail(function(xhr){
                console.error(xhr);
                toastr.danger('Ha ocurrido un error. Consulte con el administrador', 'Error');
            });

    });
});