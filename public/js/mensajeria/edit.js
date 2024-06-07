$(document).ready(function() {

    toastr.options = {
        closeButton: true,   
        progressBar: true,    
        timeOut: 3000,        
    };

    $(document).on('click', '.actualizar', function(e) {
        e.preventDefault();

        let EMailResultados = $('#EMailResultados').val(),
            EMailFactura = $('#EMailFactura').val(),
            EMailInformes = $('#EMailInformes').val(),
            Id = $('#Id').val(),
            controlEjecucion = true;

        if (!verificarCorreos(EMailResultados) || !verificarCorreos(EMailFactura) || !verificarCorreos(EMailInformes)) {
            controlEjecucion = false;
        }

        if (controlEjecucion) {  
            preloader('on');

            $.post(updateEmail, {_token: TOKEN, EMailResultados: EMailResultados, EMailFactura: EMailFactura, EMailInformes: EMailInformes, Id: Id}) 
                .done(function(response){
                    preloader('off');
                    toastr.success(response.msg);
                })
                .fail(function(jqXHR, xhr){
                    preloader('off');
                    if(jqXHR.status === 403) {
                        toastr.warning("No tiene permisos para realizar esta acción");
                        return;
                    }else {
                        toastr.error("Ha ocurrido un error. Consulte con el administrador del sistema.");
                        console.error(xhr);
                    }
                });

        }
    });



    $(document).on('click', '.volver', function(e){
        e.preventDefault();

        window.location.href = VOLVER;
    });

    function verificarCorreos(emails) {
        
        let emailRegex = /^[\w.-]+(\.[\w.-]+)*@[\w.-]+\.[A-Za-z]{2,}$/;
        let correosInvalidos = [];
        let emailsArray = emails.split(',');

        for (let i = 0; i < emailsArray.length; i++) {
            let email = emailsArray[i].trim();

            if (email !== "" && !emailRegex.test(email)) {
                correosInvalidos.push(email);
            }
        }

        if (correosInvalidos.length > 0) {
            swal("Atención", "Estos correos tienen formato inválido. Verifique por favor: " + correosInvalidos.join(", "), "warning");
            return false; 
        }

        return true; 
    }

    function preloader(opcion) {
        $('#preloader').css({
            opacity: '0.3',
            visibility: opcion === 'on' ? 'visible' : 'hidden'
        });
    }

});
