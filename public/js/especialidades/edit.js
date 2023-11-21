$(document).ready(function(){

    quitarDuplicados("#Externo");
    quitarDuplicados("#Inactivo");
    quitarDuplicados("#Provincia");

    $(document).on('click', '#updateBasico, #updateOpciones', function(){

        let Nombre = $('#Nombre').val(), Id = $('#Id').val(), Externo = $('#Externo').val(), Inactivo = $('#Inactivo').val(), Telefono = $('#Telefono').val(), Direccion = $('#Direccion').val(), IdLocalidad = $('#IdLocalidad').val(), Obs = $('#Obs').val(), Multi = $('#Multi').prop('checked'), MultiE = $('#MultiE').prop('checked'), Min = $('#Min').val(), PR = $('#PR').val(), InfAdj = $('#InfAdj').val();
        
        if(Nombre === ''){
            swal('Atención', 'El campo Nombre es obligatorio', 'warning');
            return;
        }

        if (PR < 0 || PR > 120){
            swal('Atención', 'El máximo debe estar entre 0 y 60 y no ser negativo.', 'warning');
            return;
        }

        if (Min < 0 || Min > 60){
            swal('Atención', 'La duración debe estar entre 0 y 60 y no ser negativo.', 'warning');
            return;
        }

        $.post(updateProveedor, { _token: TOKEN, Id: Id, Nombre: Nombre, Externo: Externo, Inactivo: Inactivo, Telefono: Telefono, Direccion: Direccion, IdLocalidad: IdLocalidad, Obs: Obs, Multi: Multi, MultiE, Min: Min, PR: PR, InfAdj: InfAdj })
            .done(function(){
                swal('Perfecto', 'Se han cargado los datos de manera correcta', 'success');
            })
            .fail(function(xhr){
                swal('Error', 'Ha ocurrido un error. Consulte con el administrador.', 'error');
                console.error(xhr);
            });

    });

    $('#Provincia').change(function() {
        let provincia = $(this).val();
        changeProvincia(provincia);
    });

    $('#btnVolverEspe').click(function(){
        window.location.href = GOINDEX;
    });
    
    function changeProvincia(id){
        $.ajax({
            url: getLocalidad,
            type: "POST",
            data: {
                provincia: id,
                _token: TOKEN
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
    }

    function quitarDuplicados(selector) {
        let seleccion = $(selector).val();
        let countSeleccion = $(selector + " option[value='" + seleccion + "']").length;
    
        if (countSeleccion > 1) {
            $(selector + " option[value='" + seleccion + "']:gt(0)").hide();
        }
    }
});