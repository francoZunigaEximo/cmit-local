$(document).ready(()=>{

    //Botón reset en el buscador
    $('#reset').click(function(){ 
        $('#form-index :input, #form-index select').val('');
        $('#Ver').val('activo');
        $('#Estado').val('NOeEnviado');
    });

    //Datos Default
    $('#Ver').val('activo');
    $('#Estado').val('NOeEnviado');


    //Exportar Excel a clientes
    $('#excel').click(function(e) {
        e.preventDefault();

        let ids = [];
        $('input[name="Id"]:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length > 0) {
            if (confirm("¿Estás seguro de que deseas generar el reporte de Excel con todos los items seleccionados?")) {
                $.ajax({
                    url: exportExcelMapas,
                    type: "POST",
                    data: {
                        _token: TOKEN,
                        Id: ids
                    },
                    success: function(response) {
                        let filePath = response.filePath;
                        let pattern = /storage(.*)/;
                        let match = filePath.match(pattern);
                        let path = match ? match[1] : '';

                        let url = new URL(location.href);
                        let baseUrl = url.origin; // Obtener la URL base (por ejemplo, http://localhost)

                        let fullPath = baseUrl + '/cmit/storage' + path;

                        let link = document.createElement('a');
                        link.href = fullPath;
                        link.download = "mapas.xlsx";
                        link.style.display = 'none';

                        document.body.appendChild(link);
                        link.click();
                        setTimeout(function() {
                            document.body.removeChild(link);
                        }, 100);
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });
            }
        } else {
            swal('Error', 'Debes seleccionar al menos un mapa para exportar.', 'error');
        }

    });


    //$(document).off('click', '.deleteMapa');
    $(document).on('click','.deleteMapa', function(){
        let id = $(this).data('id');
        
        $.ajax({
            url: deleteMapa,
            type: 'POST',
            data: {
                _token: TOKEN, 
                Id: id
            },
            success: function(){
                toastr.options = {
                    closeButton: true,   
                    progressBar: true,    
                    timeOut: 3000,        
                };
                toastr.success(`Se ha elimnado correctamente el mapa`, `Eliminar mapa`);
                $('#listaMapas').DataTable();
                $('#listaMapas').DataTable().draw(false);
            },
            error: function(xhr){
                console.error(xhr);
                swal('Error', 'Se ha producido un error. Actualice la página y si el problema persiste, consulte con el administrador', 'error');
            }
        });

    });

    

       

});