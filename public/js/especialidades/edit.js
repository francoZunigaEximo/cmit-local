$(document).ready(function(){

    toastr.options = {
        closeButton: true,   
        progressBar: true,    
        timeOut: 3000,        
    };

    quitarDuplicados("#Externo");
    quitarDuplicados("#Inactivo");
    quitarDuplicados("#Provincia");

    $(document).on('click', '#updateBasico, #updateOpciones', function(){

        let Nombre = $('#Nombre').val(), Id = $('#Id').val(), Externo = $('#Externo').val(), Inactivo = $('#Inactivo').val(), Telefono = $('#Telefono').val(), Direccion = $('#Direccion').val(), IdLocalidad = $('#IdLocalidad').val(), Obs = $('#Obs').val(), Multi = $('#Multi').prop('checked'), MultiE = $('#MultiE').prop('checked'), Min = $('#Min').val(), PR = $('#PR').val(), InfAdj = $('#InfAdj').val();
        
        if(Nombre === ''){
            toastr.warning('El campo Nombre es obligatorio', 'Atención');
            return;
        }

        if (PR < 0 || PR > 120){
            toastr.warning('El máximo debe estar entre 0 y 60 y no ser negativo.', 'Atención');
            return;
        }

        if (Min < 0 || Min > 60){
            toastr.warning('La duración debe estar entre 0 y 60 y no ser negativo.', 'Atención');
            return;
        }

        $.post(updateProveedor, { _token: TOKEN, Id: Id, Nombre: Nombre, Externo: Externo, Inactivo: Inactivo, Telefono: Telefono, Direccion: Direccion, IdLocalidad: IdLocalidad, Obs: Obs, Multi: Multi, MultiE, Min: Min, PR: PR, InfAdj: InfAdj })
            .done(function(){
                toastr.success('Se han cargado los datos de manera correcta', 'Perfecto');
            })
            .fail(function(xhr){
                toastr.error('Ha ocurrido un error. Consulte con el administrador.', 'Error');
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
            type: "GET",
            data: {
                provincia: id,
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