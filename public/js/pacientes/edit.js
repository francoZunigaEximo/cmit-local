$(document).ready(function(){

    quitarDuplicados("#tipoDocumento");
    quitarDuplicados("#provincia");
    quitarDuplicados("#tipoIdentificacion");
    checkProvincia();
    tabActivo();

    
    $('#provincia').change(function() {
        let provincia = $(this).val();

        $.ajax({
            url: getLocalidades,
            type: "POST",
            data: {
                provincia: provincia,
                _token: TOKEN
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
            type: "POST",
            data: {
                localidadId: localidadId,
                _token: TOKEN
            },
            success: function(response) {
                // Actualizar el valor del input de Código Postal
                $('#codigoPostal').val(response.codigoPostal);
            }
        });
    });

    function checkProvincia(){

        let provincia = $('#provincia').val();
        let localidad = $('#localidad').val();

        if (provincia === 0)
        {
            $.ajax({
                url: checkP,
                type: 'POST',
                data: {
                    localidad: localidad,
                    _token: TOKEN
                },
                success: function(response){
                    
                    let provinciaNombre = response.fillProvincia;
                        
                    let nuevoOption = $('<option>', {
                        value: provinciaNombre,
                        text: provinciaNombre,
                        selected: true,
                    });

                    $('#provincia').append(nuevoOption);
                },
                error: function(xhr){
                    swal('Error', 'No se pudo autocompletar la provincia. Debe cargarlo manualmente.', 'error');
                    console.error(xhr);
                }
            });
        }
    }

    function quitarDuplicados(selector) {
        let seleccion = $(selector).val();
        let countSeleccion = $(selector + " option[value='" + seleccion + "']").length;
    
        if (countSeleccion > 1) {
            $(selector + " option[value='" + seleccion + "']:gt(0)").hide();
        }
    }

    function tabActivo(){
        $('.tab-pane').removeClass('active show');
        $('#datosPersonales').addClass('active show');
        $('.nav-link[href="datosPersonales"]').tab('show');
    }

});