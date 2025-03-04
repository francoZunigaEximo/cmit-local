$(function(){

    let doc = localStorage.getItem('insertDoc');
    
    if(doc){
        $('#documento').val(doc);
        localStorage.removeItem('insertDoc');
    }

    //Hacemos los cambios cuando hay un cambio en el campo
    $('#documento').on('change blur', function () {
        let documento = $(this).val();

        $.ajax({
            url: verify,
            type: "POST",
            data: {
                documento: documento,
                _token: TOKEN
            },
            success: function (response) {
                if (response.existe) {
                    let paciente = response.paciente,
                        url = editUrl.replace('__paciente__', paciente.Id);
                    $('#editLink').attr('href', url);

                    $('#myModal').modal('show');
                    $('#btnRegistrar').prop('disabled', true);
                }else{
                    $('#btnRegistrar').prop('disabled', false);
                }
            }
         });
    });


    $('#provincia').change(function() {
        let provincia = $(this).val();

        $.ajax({
            url: getLocalidades,
            type: "GET",
            data: {
                provincia: provincia,
            },
            success: function(response) {
                let localidades = response.localidades;
                $('#localidad').empty().append('<option selected>Elija una opción...</option>');

                localidades.forEach(function(localidad) {
                    $('#localidad').append('<option value="' + localidad.id + '">' + localidad.nombre + '</option>');
                });
            }
        });
    });


    $('#localidad').change(function() {
        let localidadId = $(this).val();

        // Realizar la solicitud Ajax
        $.ajax({
            url: getCodigoPostal,
            type: "GET",
            data: {
                localidadId: localidadId,
            },
            success: function(response) {
                // Actualizar el valor del input de Código Postal
                $('#codigoPostal').val(response.codigoPostal);
            }
        });
    });

});