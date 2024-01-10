$(document).ready(function(){

     //Cerramos el modal y reset de comentarios de la DB
     $('#prestacionModal').on('hidden.bs.modal', function(){
        $("#comentario").val("");
    });

    //Limpiamos el ID para evitar bug
    let IdComentario = null;

    //Arrastramos eventos
    $(document).on('click', '.prestacionComentario', function() {
        
        let IdComentario = $(this).data('id');
        $('#IdComentarioEs').text(IdComentario);
        $('.guardarComentario').data('id', IdComentario);

        $.ajax({
            url: getComentarioPres,
            type: 'get',
            data: {
                Id: IdComentario
            },
            success: function(response){
                let getComentario = response.comentario;

                if(getComentario){
                    $('#comentario').val(getComentario);
                }else{
                    $('#comentario').val("...");
                }

            },
            error:function(xhr){

                swal('Error','¡Ha ocurrido un inconveniente y la solicitud no podrá llevarse a cabo. Consulte con el administrador!', 'error');
                console.error(xhr);
            }
        });

        $('.guardarComentario').off('click').on('click', function() {

            event.stopPropagation();
            let IdComentario = $(this).data('id');

            if (IdComentario) {
                let comentario = $('#comentario').val();

                $.ajax({
                    url: setComentarioPres,
                    type: 'Post',
                    data: {
                        _token: TOKEN,
                        Obs: comentario,
                        IdP: IdComentario,
                    },
                    success: function(){
                        
                        swal('Perfecto', 'El comentario de la prestación se ha guardado correctamente', 'success');
                    },

                    error: function(xhr){

                        swal("Error", "¡Ha ocurrido un inconveniente y la solicitud no podrá llevarse a cabo. Consulte con el administrador!", "error");
                        console.error(xhr);
                    }
                });
            }
        });

    });
});