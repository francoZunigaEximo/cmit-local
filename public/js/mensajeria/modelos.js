$(function(){

    $(document).on('click', '.editar', function(){

        let id = $(this).data('id');
   
        if([null, undefined, ''].includes(id)) {
            toastr.warning("No hay ningún modelo para editar", "", {timeOut: 1000});
            return;
        }

        window.location.href = linkModelo.replace('__modelo__', id);

    });

    $(document).on('click', '.eliminar', function(){
        let id = $(this).data('id');

        if([null, undefined, ''].includes(id)) {
            toastr.warning("No hay ningún modelo para eliminar", "", {timeOut: 1000});
            return;
        }

        swal({
            title: "¿Está seguro que desea eliminar el modelo?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {

                preloader('on');
                $.get(eliminarModelo, {Id: id})
                    .done(function(response){
                        preloader('off');
                        toastr.success(response.msg, '', {timeOut: 1000});
                        $('#listadoModeloMsj').DataTable().clear().draw(false);
                        
                    })
                    .fail(function(jqXHR){
                        preloader('off');            
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return;
                    });
            }
        });
            
    });

    
});