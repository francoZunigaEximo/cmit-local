$(function(){

    $(document).on('click', '#crear', async function(e) {
        e.preventDefault();
        let usuario = $('#usuario').val();
        let email = $('#email').val();

        if(usuario === '') {
            toastr.warning('El usuario no puede estar vacío','',{timeOut: 1000});
            return;
        }

        if(verificarUsuario(usuario) === false) {
            toastr.warning("El usuario no puede contener espacios vacíos, caracteres especiales y solo hasta 25 caracteres",'',{timeOut: 1000});
            return;
        }
      
        if(email === '') {
            toastr.warning('El email no se puede encontrar vacío','',{timeOut: 1000});
            return;
        }

        if(correoValido(email) === false) {
            toastr.warning('El email no es válido','',{timeOut: 1000});
            return;
        }

        let emailValido = await comprobarEmail(email);
        let usuarioExiste = await comprobarUsuario(usuario);
    
        if(usuarioExiste && usuarioExiste.exists) {
            toastr.warning('El usuario ya se encuentra registrado en la base de datos','',{timeOut: 1000});
            return;
        }

        if(emailValido && emailValido.exists) {
            toastr.warning('El email ya se encuentra registrado en la base de datos','',{timeOut: 1000});
            return;
        }

        swal({
            title: "¿Está seguro que desea confirmar la operación?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if (confirmar) {
                preloader('on');
                $.post(register, {_token: TOKEN, name: usuario, email: email})
                    .done(function(response) {
                        preloader('off');
                        toastr.success(response.msg,'',{timeOut: 1000});
                        $('#crear').attr('disabled', true);
                        setTimeout(function() {
                            let url = location.href;
                            let clearUrl = url.replace('create', '');
                            let redireccionar =  clearUrl + response.id + '/edit';
                            window.location.href = redireccionar;
                        }, 3000);
                    })
                    .fail(function(jqXHR) {
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return;
                    });
            }
        });
    });

    $(document).on('click', '#volver', function(e){
        e.preventDefault();
        history.back();
    });

    async function comprobarEmail(email) {
        preloader('on');
        let response = await $.get(checkMail, { email: email });
        preloader('off');
        return response;
    }
    
    async function comprobarUsuario(usuario) {
        preloader('on');
        let response = await $.get(checkUsuario, { usuario: usuario });
        preloader('off');
        return response;
    }
});