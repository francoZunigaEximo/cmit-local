$(document).ready(()=>{

    $("#form-updatePass").off();
    $("#form-updatePass").validate({
        rules: {
            password: {
                required: true,
                remote: checkPassword
            },
            newPass: {
                required: true,
            },
            confirmPass: {
                required: true,
                equalTo: "#newPass"
            },

        },
        messages: {
            password: {
                required: "Debe escribir tu contraseña actual.",
                remote: "No es su contraseña actual"
            },
            newPass: {
                required: "Debe escribir la contraseña nueva"
            },
            confirmPass: {
                required: "Debe confirmar su contraseña",
                equalTo: "La nueva contraseña y su confirmación no coinciden"
            }
        }

    });


    quitarDuplicados("#provincia");
    quitarDuplicados("#cuil");
    quitarDuplicados("#tipoDoc");

    $(document).on('click', '.updateDatos', async function(e){
        e.preventDefault();

        let nombre = $('#nombre').val(),
            apellido = $('#apellido').val(),
            tipoDocumento = $('#tipoDoc').val(),
            documento = $('#numeroDoc').val(),
            tipoIdentificacion = $('#cuil').val(),
            identificacion = $('#numeroCUIL').val(),
            telefono = $('#numTelefono').val(),
            fechaNacimiento = $('#fechaNac').val(),
            provincia = $('#provincia').val(),
            localidad = $('#localidad').val(),
            direccion = $('#direccion').val(),
            cp = $('#codPostal').val(),
            Id = $('#Id').val(),
            email = $('#email').val(),
            name = $('#name').val();

        if([0,null,''].includes(email)) {
            toastr.warning('El correo electrónico no puede estar vacío','',{timeOut: 1000});
            return;
        }

        if([0,null,''].includes(name)) {
            toastr.warning('No identificamos al usuario. Consulte con el administrador','',{timeOut: 1000});
            return;
        }

        if(correoValido(email) === false) {
            toastr.warning("El email no es válido",'',{timeOut: 1000});
            return;
        }

        let result = await checkEmail(name,email);
        if (result && result.estado === 'false') {
            toastr.warning(result.msg, '', {timeOut: 1000});
            return;
        }

        if (result && result.estado === 'true') {
            toastr.success(result.msg, '', {timeOut: 1000});
        }
    
        swal({
            title: "¿Esta seguro que desea confirmar la operación?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {

                preloader('on');
                $.post(actualizarDatos, {_token: TOKEN, Nombre: nombre, Apellido: apellido, TipoDocumento: tipoDocumento, Documento: documento, TipoIdentificacion: tipoIdentificacion, Identificacion: identificacion, Telefono: telefono, FechaNacimiento: fechaNacimiento, Provincia: provincia, IdLocalidad: localidad, CP: cp, Id: Id, email: email, Direccion: direccion, name: name})
                    .done(function(response){
                        preloader('off');
                        toastr.success(response.msg, '', {timeOut: 1000});
                        setTimeout(()=>{
                            location.reload();
                        },2000);
                    })
                    .fail(function(jqXHR){
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);
                        checkError(jqXHR.status, errorData.msg);
                        return;
                    })
            }
        })

    });

    $(document).on('click', '.actPass', function(e){
        e.preventDefault();
        let passw = $('#newPass').val();

        if($('#form-updatePass').valid()) {
            swal({
                title: "¿Esta seguro que desea actualizar su contraseña?",
                icon: "warning",
                buttons: ["Cancelar", "Aceptar"]
            }).then((confirmar) => {
                if(confirmar) {
                    preloader('on');
                    $.post(actualizarPass, {_token: TOKEN, password: passw})
                        .done(function(response) {
                            preloader('off');

                            toastr.success(response.msg, '', {timeOut: 1000});
                            setTimeout(()=>{
                                location.reload();
                            },2000);
                        })
                        .fail(function(jqXHR){
                            preloader('off');
                            let errorData = JSON.parse(jqXHR.responseText);            
                            checkError(jqXHR.status, errorData.msg);
                            return;
                        });
                }
            }); 
        }
    });

    $('#provincia').change(function() {
        let provincia = $(this).val();
        loadProvincia(provincia);
    });

     $('#localidad').change(function() {
        let localidadId = $(this).val();
        // Realizar la solicitud Ajax
        loadLocalidad(localidadId);
    });


    function loadProvincia(valor) {
        $.ajax({
            url: getLocalidad,
            type: "GET",
            data: {
                provincia: valor,
            },
            success: function(response) {
                let localidades = response.localidades;
                $('#localidad').empty();
                $('#localidad').append('<option selected>Elija una opción...</option>');
                localidades.forEach(function(localidad) {
                    $('#localidad').append('<option value="' + localidad.id + '">' + localidad.nombre + '</option>');
                });
            }
        });
    }

    function loadLocalidad(valor){
        
        $.ajax({
            url: getCodigoPostal,
            type: "GET",
            data: {
                localidadId: valor,
            },
            success: function(response) {
                // Actualizar el valor del input de Código Postal
                $('#codPostal').val(response.codigoPostal);
            }
        });
    }

    async function checkEmail(name, email) {
        let response = await $.get(checkCorreo, { email: email, name: name });
        return response;
    }

});