$(document).ready(function(){


    let resizing = false, startWidth, startHeight, startX, startY; //variables de ancho de imagen

    toastr.options = {
        closeButton: true,   
        progressBar: true,    
        timeOut: 3000,        
    };

    $("#Apellido, #Nombre").on("input", function() {
        $(this).val($(this).val().toUpperCase());
    });

    $(document).on('change', '#Documento', function(){

        let documento = $(this).val();

        if(documento === '') return '';
        
        $.post(checkDocumento, { _token: TOKEN, Documento: documento})
            .done(function(response){

                let resultado = response.check;

                if(resultado){
    
                $('#docModal').text(documento); 
                    let url = editUrl.replace('__profesionale__', resultado.Id);
                    $('#editLink').attr('href', url);
                
                $('#alertaModal').modal('show');
                $('.saveProfesional').prop('disabled', true);
    
                }else{
    
                    $('.saveProfesional').prop('disabled', false); 
                }
                
            })
            .fail(function(xhr){
                console.error(xhr);
                toastr.error("Ha ocurrido un error. Actualice la página y si el problema persiste, consulte con el administrador", "Error");
            });
    });

    $(document).on('click', '#volverProfesionales', function(){

        window.location.href = GOINDEX;
    });

    $('#imagenModal').mousedown(function (e) {
        resizing = true;
        startWidth = $('#imagenModal').width();
        startHeight = $('#imagenModal').height();
        startX = e.clientX;
        startY = e.clientY;
    });

    $(document).mousemove(function (e) {
        if (resizing) {
            let newWidth = startWidth + (e.clientX - startX);
            let newHeight = startHeight + (e.clientY - startY);

            // Aplica nuevas dimensiones
            $('#imagenModal').width(newWidth);
            $('#imagenModal').height(newHeight);

            $('#wImagen').val(newWidth);
            $('#hImagen').val(newHeight);
        }
    });

    $(document).mouseup(function () {
        resizing = false;
    });

});