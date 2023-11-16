$(document).ready(function(){

    toastr.options = {
        closeButton: true,   
        progressBar: true,    
        timeOut: 3000,        
    };
    
    $(document).on('click', '#deleteButton', function(){

        let id = $(this).data('id'), checkImagen = $('#profile-image-preview').css('background-image');

        if(checkImagen.match(/foto-default\.png/)) {
            toastr.warning('No hay imagen para eliminar', 'Atención');
            return;
        }
        
        swal({
            title: '¿Estas seguro que deseas eliminar la fotografía?',
            icon: 'warning',
            buttons: ['Cancelar', 'Sí, quiero eliminarla']
        }).then((confirmar) => {

            if(confirmar){
                $.post(deletePicture, {Id: id, _token: TOKEN})
                    .done(function(){
                        toastr.success('Se ha eliminado la imagen correctamente', 'Perfecto');
                        setTimeout(() => {
                            location.reload();
                        }, 3000);
                    })
                    .fail(function(xhr){
                        toastr.warning('No se ha podido eliminar la imagen. Consulte con el administrador', 'Atención');
                    });
            }
        });
    
    });
    
});