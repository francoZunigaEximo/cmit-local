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

    toastr.options = {
        closeButton: true,   
        progressBar: true,    
        timeOut: 3000,        
    };
    quitarDuplicados("#provincia");
    quitarDuplicados("#cuil");
    quitarDuplicados("#tipoDoc");

    $(document).on('click', '.updateDatos', function(e){
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
            Id = $('#Id').val();

        if($('#form-update').valid() && confirm("¿Esta seguro que desea confirmar la operación?")) {

            preloader('on');
            $.post(actualizarDatos, {_token: TOKEN, Nombre: nombre, Apellido: apellido, TipoDocumento: tipoDocumento, Documento: documento, TipoIdentificacion: tipoIdentificacion, Identificacion: identificacion, Telefono: telefono, FechaNacimiento: fechaNacimiento, Provincia: provincia, IdLocalidad: localidad, CP: cp, Id: Id, Direccion: direccion})
                .done(function(){
                    preloader('off');
                    toastr.success("Se han actualizado correctamente los datos del usuario");
                    setTimeout(()=>{
                        location.reload();
                    },2000);
                })
                .fail(function(xhr){
                    console.error(xhr);
                    toastr.error("Ha ocurrido un error. Consulte con el administrador");
                })
        }

    });

    $(document).on('click', '.actPass', function(e){
        e.preventDefault();
        let passw = $('#newPass').val();

        if($('#form-updatePass').valid() && confirm("¿Esta seguro que desea actualizar su contraseña?")){
            preloader('on');
            $.post(actualizarPass, {_token: TOKEN, password: passw})
                .done(function() {
                    preloader('off');
                    toastr.success("Se han actualizado su contraseña correctamente");
                    setTimeout(()=>{
                        location.reload();
                    },2000);
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


    let timer = null;

    $(document).on('input', '#email', function(){
        clearTimeout(timer);

        timer = setTimeout(function() { 
            let email = $('#email').val();
            
            if([null, undefined, ''].includes(email)) {
                toastr.warning("El email no puede estar vacío");
                $('#cambiarEmail').attr('disabled','true');
                return;
            }
    
            $.get(checkEmailUpdate, {email: email})
                .done(function(response){
                    response.estado == 'false' && verificarCorreo === response.correo
                        ? toastr.success("Es su correo actual") 
                        : response.estado == 'false' && verificarCorreo !== response.correo
                            ? toastr.warning(response.msg) 
                            : toastr.success(response.msg)
    
                    response.estado === 'false' && verificarCorreo === response.correo
                        ? $('#cambiarEmail').removeAttr('disabled')
                        : response.estado === 'false' && verificarCorreo !== response.correo
                            ? $('#cambiarEmail').attr('disabled','true') 
                            : $('#cambiarEmail').removeAttr('disabled');
                });
        }, 1000); 
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

    function quitarDuplicados(selector) {
        let seleccion = $(selector).val();
        let countSeleccion = $(selector + " option[value='" + seleccion + "']").length;
    
        if (countSeleccion > 1) {
            $(selector + " option[value='" + seleccion + "']:gt(0)").hide();
        }
    }

    function preloader(opcion) {
        $('#preloader').css({
            opacity: '0.3',
            visibility: opcion === 'on' ? 'visible' : 'hidden'
        });
    }

});