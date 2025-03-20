$(document).ready(function(){
    
    $('#Provincia').val('NEUQUEN');
    $('#IdLocalidad').val(3); //Elije la ciudad de Neuquen como default
    $('.Telefono, .Direccion, .Provincia, .IdLocalidad, .Obs').hide();

    $(document).on('change', '#Externo', function() {

        let externo = $(this).val();
    
        if (externo === '1') {
            $('.Telefono, .Direccion, .Provincia, .IdLocalidad, .Obs').show();
        } else if (['', '0'].includes(externo)) {
            $('.Telefono, .Direccion, .Provincia, .IdLocalidad, .Obs').hide();
        }
    });

    $('#Provincia').change(function() {
        let provincia = $(this).val();
        changeProvincia(provincia);
    });

    $(document).on('click', '#saveBasico', function(e){
        e.preventDefault();

        let Nombre = $('#Nombre').val(), Externo = $('#Externo').val(), Inactivo = 1, Telefono = $('#Telefono').val(), Direccion = $('#Direccion').val(), IdLocalidad = $('#IdLocalidad').val(), Obs = $('#Obs').val();

        if([0,null,''].includes(Nombre)) {
            toastr.warning('El campo Nombre es obligatorio');
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

        swal({
            title: "¿Estas seguro que deseas realizar la operación?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                preloader('on');
                $.post(saveBasico, {_token: TOKEN, Nombre: Nombre, Externo: Externo, Inactivo: Inactivo, Telefono: Telefono, Direccion: Direccion, IdLocalidad: IdLocalidad, Obs: Obs})
                    
                    .done(function(response){
                        preloader('off');
                        let data = response.especialidad;

                        toastr.success(response.msg);
                        setTimeout(() => {
                            let nuevo = location.href.replace("create", "");
                            let lnk = nuevo + "" + data + "/edit";
                            window.location.href = lnk;
                        }, 3000);
                    })
                    .fail(function(jqXHR){
                        preloader('off');            
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return; 
                    });
            }   
        }) 
    });

    $(document).on('change', '#Nombre', function(){

        let nombre = $(this).val();

        $.get(checkProveedor, {Nombre: nombre})
            .done(function(response){

                if (response.existe) {
                    let especialidad = response.especialidades, url = editUrl.replace('__especialidades__', especialidad.Id);
                    
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