$(document).ready(function(){
    
    $('#Provincia').val('NEUQUEN');
    $('#IdLocalidad').val(3); //Elije la ciudad de Neuquen como default
    $('.Telefono, .Direccion, .Provincia, .IdLocalidad, .Obs').hide();
    
    $(document).on('change', '#Externo', function(){

        let externo = $(this).val();
        console.log(externo);
        if(externo === '1'){
            $('.Telefono, .Direccion, .Provincia, .IdLocalidad, .Obs').show();
        
        } else if(externo === '0' || externo === ''){
            $('.Telefono, .Direccion, .Provincia, .IdLocalidad, .Obs').hide();
        }
    });

    $('#Provincia').change(function() {
        let provincia = $(this).val();
        changeProvincia(provincia);
    });

    $(document).on('click', '#saveBasico', function(){

        let Nombre = $('#Nombre').val(), Externo = $('#Externo').val(), Inactivo = $('#Inactivo').val(), Telefono = $('#Telefono').val(), Direccion = $('#Direccion').val(), IdLocalidad = $('#IdLocalidad').val(), Obs = $('#Obs').val();

        if(Nombre === ''){
            swal('Atención', 'El campo Nombre es obligatorio', 'warning');
            return;
        }

        $.post(saveBasico, {_token: TOKEN, Nombre: Nombre, Externo: Externo, Inactivo: Inactivo, Telefono: Telefono, Direccion: Direccion, IdLocalidad: IdLocalidad, Obs: Obs})
            .done(function(response){

                let data = response.especialidad;

                swal('Perfecto', 'Se ha registrado la nueva especialidad de manera correcta', 'success');
                setTimeout(() => {
                    let nuevo = location.href.replace("create", "");
                    let lnk = nuevo + "/" + data.Id + "/edit";
                    window.location.href = lnk;
                }, 3000);
            })
            .fail(function(xhr){
                swal('Error', 'Ha ocurrido un error. Consulte con el administrador', 'error');
                console.error(xhr);
            });
    });

    $(document).on('change', '#Nombre', function(){

        let nombre = $(this).val();

        $.get(checkProveedor, {Nombre: nombre})
            .done(function(response){

                if (response.existe) {
                    let especialidad = response.especialidades, url = editUrl.replace('__especialidad__', especialidad.Id);
                    
                    $('#editLink').attr('href', url);
                    $('#advertencia').modal('show');
                    $('#saveBasico').prop('disabled', true);
                }else{
                    $('#saveBasico').prop('disabled', false);
                }
            })
            
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