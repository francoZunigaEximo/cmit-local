$(document).ready(()=>{

    //Formato automático de Identificacion para create y edit
    $('#Identificacion').on('input', function() {
        let input = $(this),
            cleanedValue = input.val().replace(/\D/g, ''),
            formattedValue = cleanedValue.replace(/^(\d{2})(\d{8})(\d{1})$/, '$1-$2-$3');
        input.val(formattedValue);
    });

    // Contador y máximo de caracteres en Remito de Clientes/Opciones
    let maxCaracteres = 150;

    function updateContador() {
        let longitud = $('#ObsCE').val(), caracteres = longitud.length;
        $('#contadorObsCE').text(caracteres + '/' + maxCaracteres);
    }

    function blockExcedente(e) {
        let longitud = $('#ObsCE').val(), caracteres = longitud.length;
        if (caracteres >= maxCaracteres) {
            e.preventDefault();
        }
    }

    $('#ObsCE').on('input', function() {
        updateContador();
    });

    $('#ObsCE').on('keydown', function(e) {
        blockExcedente(e);
    });
    /****************Fin contador *****************************/


     //Ajuste automatico de tamaño de observacion
     $('.auto-resize').click(function() {
        $(this).height('auto');
        $(this).height(this.scrollHeight);
    });
    
    $('#Provincia').change(function() {
        let provincia = $(this).val();
        $.ajax({
            url: getLocalidad,
            type: "GET",
            data: {
                provincia: provincia,
            },
            success: function(response) {
                let localidades = response.localidades;
                $('#IdLocalidad').empty();
                $('#IdLocalidad').append('<option selected>Elija una opción...</option>');
                localidades.forEach(function(localidad) {
                    $('#IdLocalidad').append('<option value="' + localidad.id + '">' + localidad.nombre + '</option>');
                });
            }
        });
    });
     $('#IdLocalidad').change(function() {
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
                $('#CP').val(response.codigoPostal);
            }
        });
    });

});