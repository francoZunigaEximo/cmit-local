$(document).ready(function(){

    quitarDuplicados("#Externo");
    quitarDuplicados("#Inactivo");
    quitarDuplicados("#Provincia");

    $(document).on('click', '#updateBasico, #updateOpciones', function(){

        let Nombre = $('#Nombre').val(), Id = $('#Id').val(), Externo = $('#Externo').val(), Inactivo = $('#Inactivo').val(), Telefono = $('#Telefono').val(), Direccion = $('#Direccion').val(), IdLocalidad = $('#IdLocalidad').val(), Obs = $('#Obs').val(), Multi = $('#Multi').prop('checked'), MultiE = $('#MultiE').prop('checked'), Min = $('#Min').val(), PR = $('#PR').val(), InfAdj = $('#InfAdj').val();
        
        if(Nombre === ''){
            toastr.warning('El campo Nombre es obligatorio', 'Atención');
            return;
        }

        if([0,null,''].includes(Externo)) {
            toastr.warning('Debe especificar si es externo');
            return;
        }

        if([0,null,''].includes(Inactivo)) {
            toastr.warning('Debe especificar si el campo es inactivo o no');
            return;
        }

        if (PR < 0 || PR > 120){
            toastr.warning('El máximo debe estar entre 0 y 60 y no ser negativo.');
            return;
        }

        if (Min < 0 || Min > 60){
            toastr.warning('La duración debe estar entre 0 y 60 y no ser negativo.');
            return;
        }

        preloader('on');
        $.post(updateProveedor, { _token: TOKEN, Id: Id, Nombre: Nombre, Externo: Externo, Inactivo: Inactivo, Telefono: Telefono, Direccion: Direccion, IdLocalidad: IdLocalidad, Obs: Obs, Multi: Multi, MultiE, Min: Min, PR: PR, InfAdj: InfAdj })
            .done(function(response){
                preloader('off');
                toastr.success(response.msg, 'Perfecto');
            })
            .fail(function(jqXHR){
                preloader('off');            
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return; 
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

});