$(document).ready(function() {

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
                    let errorData = JSON.parse(jqXHR.responseText);            
                    checkError(jqXHR.status, errorData.msg);
                    return;
                });

        }
    });



    $(document).on('click', '.volver', function(e){
        e.preventDefault();

        window.location.href = VOLVER;
    });

});
