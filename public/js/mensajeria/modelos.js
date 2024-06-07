$(document).ready(function(){

    $(document).on('click', '.editar', function(){

        let id = $(this).data('id');
        console.log(id);
        if([null, undefined, ''].includes(id)) {
            toastr.warning("No hay ningún modelo para editar");
            return;
        }

        window.location.href = linkModelo.replace('__modelo__', id);

    });

    $(document).on('click', '.eliminar', function(){
        let id = $(this).data('id');

        if([null, undefined, ''].includes(id)) {
            toastr.warning("No hay ningún modelo para eliminar");
            return;
        }

        if(confirm("¿Está seguro que desea eliminar el modelo?")) {
            preloader('on');
            $.get(eliminarModelo, {Id: id})
                .done(function(response){
                    preloader('off');
                    toastr.success(response.msg);
                    $('#listadoModeloMsj').DataTable().clear().draw(false);
                    
                })
                .fail(function(jqXHR){
                    preloader('off');
                    if(jqXHR.status === 403){
                        toastr.warning("No tiene permisos para realizar esta acción");
                        return;
                    
                    }else if(jqXHR.status === 500){
                        toastr.warning(jqXHR.responseJSON.msg);
                        return;
                    }else{
                        toastr.error("Error: Consulte con el administrador");
                        console.error(jqXHR);
                    }   
                });
        }
    });

    function preloader(opcion) {
        $('#preloader').css({
            opacity: '0.3',
            visibility: opcion === 'on' ? 'visible' : 'hidden'
        });
    }

});