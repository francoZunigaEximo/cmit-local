$(document).ready(function(){

    let hoy = new Date().toISOString().slice(0, 10);
    $('#fechaHasta').val(hoy);

    $(document).on('click', '.destinatarios, .historiaMensaje', function(){
        let tipo = $(this).hasClass('destinatarios') ? 'destinatario' : 'mensaje', id = $(this).data('id');

        let obj = {
            destinatario: '.verDestinatarios',
            mensaje: '.verMensaje'
        };

        if([null, undefined, ''].includes(id)) {
            toastr.warning("No se hay id");
            return;
        }
        preloader('on');
        $.get(verAuditoria, {Id: id, Tipo: tipo})
            .done(function(data){
                preloader('off');
                $(obj[tipo]).empty().html(data);
            });
    });


});