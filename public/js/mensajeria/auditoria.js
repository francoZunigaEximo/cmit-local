$(function(){

    let hoy = new Date().toLocaleDateString('en-CA');
    $('#fechaHasta').val(hoy);

    $(document).on('click', '.destinatarios, .historiaMensaje', function(){
        let tipo = $(this).hasClass('destinatarios') ? 'destinatario' : 'mensaje', id = $(this).data('id');

        let obj = {
            destinatario: '.verDestinatarios',
            mensaje: '.verMensaje'
        };

        if(!id) {
            toastr.warning("La existe el identificador", "", {timeOut: 1000});
            return;
        }
        preloader('on');
        $.get(verAuditoria, {Id: id, Tipo: tipo})
            .done(function(data){
                preloader('off');
                $(obj[tipo]).empty().html(data);
            });
    });

    $(document).on('click', '.reiniciarAuditoria', function(e){
        e.preventDefault();
        $('#listaAuditorias').DataTable();
        $('#listaAuditorias').DataTable().draw(false);
    });

});