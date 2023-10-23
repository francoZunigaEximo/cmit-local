$(document).ready(function(){

    

    $('#Provincia').change(function() {
        let provincia = $(this).val();
        changeProvincia(provincia);
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
});