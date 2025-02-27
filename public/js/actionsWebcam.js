$(function(){
  
    $(document).on('click', '#deleteButton', function(){

        let id = $(this).data('id'), checkImagen = $('#profile-image-preview').css('background-image');

        if(checkImagen.match(/foto-default\.png/)) {
            toastr.warning('No hay imagen para eliminar','',{timeOut: 1000});
            return;
        }
        
        swal({
            title: '¿Estas seguro que deseas eliminar la fotografía?',
            icon: 'warning',
            buttons: ['Cancelar', 'Sí, quiero eliminarla']
        }).then((confirmar) => {

            if(confirmar){
                $.post(deletePicture, {Id: id, _token: TOKEN})
                    .done(function(response){
                        toastr.success(response.msg,'',{timeOut: 1000});
                        setTimeout(() => {
                            location.reload();
                        }, 3000);
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