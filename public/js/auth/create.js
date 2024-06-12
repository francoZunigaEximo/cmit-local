$(document).ready(()=>{

    $("#form-create").validate({
        rules: {
            usuario: {
                required: true,
                maxlength: 15,
                remote: checkUsuario,
            },
            email: {
                required: true,
                email: true,
                remote: checkCorreo,
            }
        },
        messages: {
            usuario: {
                required: "El usuario es obligatorio.",
                maxlength: "El máximo de caracteres es de 15",
                remote: "El usuario ya se encuentra en la base de datos"
            },
            email: {
                required: "El correo es obligatorio.",
                email: "Debe tener un formato de correo electronico",
                remote: "El email ya se encuentra en la base de datos"
            },
        }

    });

    $(document).on('click', '#crear', function(e){
        e.preventDefault();
        let usuario = $('#usuario').val(), email = $('#email').val();

        if($('#form-create').valid() && confirm("¿Esta seguro que desea confirmar la operación?")) {
            preloader('on');
            $.post(register, {_token: TOKEN, name: usuario, email: email})
                .done(function(response){
                    preloader('off');
                    toastr.success('Se ha realizado los cambios de manera correcta');
                    setTimeout(function(){
                        let url = location.href,
                        clearUrl = url.replace('create', ''),
                        redireccionar =  clearUrl + response + '/edit';
                        window.location.href = redireccionar;
                    },3000);
                });
        }

    });

    $(document).on('click', '#volver', function(e){
        e.preventDefault();
        history.back();
    });


});