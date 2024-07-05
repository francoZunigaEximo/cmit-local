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

    function checkProvincia(){

        let provincia = $('#provincia').val();
        let localidad = $('#localidad').val();

        if (provincia === 0)
        {
            $.ajax({
                url: checkP,
                type: 'GET',
                data: {
                    localidad: localidad,
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
                    preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);
                        checkError(jqXHR.status, errorData.msg);
                        return;
                }
            });
        }
    }

    //Fix  TABS
    function tabActivo(){
        $('.tab-pane').removeClass('active show');
        $('#datosPersonales').addClass('active show');
        $('.nav-link[href="datosPersonales"]').tab('show');
    }

    $('a[href="#datosPersonales"]').click(function() {
        $('.tab-pane').removeClass('active show');
        $('#datosPersonales').addClass('active show');
        $('.nav-link[href="datosPersonales"]').tab('show');
    });

    $('a[href="#fichaLaboral"]').click(function() {
        $('.tab-pane').removeClass('active show');
        $('#fichaLaboral').addClass('active show');
        $('.nav-link[href="fichaLaboral"]').tab('show');
    });

    $('a[href="#prestaciones"]').click(function() {
        $('.tab-pane').removeClass('active show');
        $('#prestaciones').addClass('active show');
        $('.nav-link[href="prestaciones"]').tab('show');
    });



});