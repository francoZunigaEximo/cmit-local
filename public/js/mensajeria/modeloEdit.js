$(function(){

    $('.Cuerpo').richText(atributos);

    $(document).on('click', '.actualizar', function(e){
        e.preventDefault();

        let id = $('#Id').val();

        if([null, undefined, ''].includes(id)) {
            toastr.warning("No hay ningún modelo para actualizar", "", {timeOut: 1000});
            return;
        }

        if($('#form-update').valid() && confirm("¿Está seguro que desea actualizar el modelo de mensaje?")) {

            let data = {
                Nombre: $('#Nombre').val(),
                Asunto: $('#Asunto').val(),
                Cuerpo: $('.Cuerpo').val(),
                Id: id,
                _token: TOKEN
            };

            preloader('on');
            $.post(actualizarModelo, data)
                .done(function(response){
                    preloader('off');
                    toastr.success(response.msg, '', {timeOut: 1000});
                    $('#form-create').trigger('reset');
                    $('.Cuerpo').html('');
                    setTimeout(() => {
                        window.location.href = listadoModelo;
                    }, 2000);
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