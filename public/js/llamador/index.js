$(function(){

    $('#fechaHasta').val(fechaNow(null, "-", 0));
    $('#estado').val('abierto');

    let echo = window.Echo.channel('listado-efectores');

    echo.listen('.ListadoProfesionalesEvent', (response) => {
        console.log("Respuesta cruda:", response);

        const efectores = response.efectores;

        $('#profesional').empty();

        toastr.info('Se ha actualizado la lista de profesionales');

        if (efectores.length === 1) {

            $('#profesional').append(
                `<option value="${efectores[0].Id}" selected>${efectores[0].NombreCompleto}</option>`
            );
        } else if(efectores.length > 1) {

            $('#profesional').append('<option value="" selected>Elija una opción...</option>');
            $.each(efectores, function(index, value){
                let contenido = `<option value="${value.Id}">${value.NombreCompleto}</option>`;

                $('#profesional').append(contenido);
            });

        } else {

            $('#profesional').append(
                `<option value="" selected>No hay efectores</option>`
            );
        }
    });

});