$(document).ready(function(){

    toastr.options = {
        closeButton: true,   
        progressBar: true,    
        timeOut: 3000,        
    };

    $('#btnBajaMultiple').click(function(e) {
        e.preventDefault();

        let ids = [];
        $('input[name="Id"]:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            toastr.warning('Debe seleccionar al menos un cliente para la baja múltiple', 'Atención');
            return; 
        }

        if (confirm("¿Estás seguro de que deseas realizar la baja múltiple de los clientes seleccionados?")) {
            $.ajax({
                url: multipleDown,
                type: 'POST',
                data: {
                    _token: TOKEN,
                    ids: ids
                },
                success: function() {
                    toastr.success('¡Se ha dado de baja a los clientes correctamente!', 'Acción realizada');
                    setTimeout(()=>{
                        $('#listaClientes').DataTable().draw(false);
                    }, 3000);
                   
                },
                error: function(xhr) {
                    toastr.error('¡Ha ocurrido un inconveniente y la solicitud no podrá llevarse a cabo. Consulte con el administrador!', 'Error');
                    console.error(xhr);
                }
            });
        }
    });


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
                    url: exportExcelClientes,
                    type: "GET",
                    data: {
                        Id: ids
                    },
                    success: function(response) {
                        let filePath = response.filePath,
                            pattern = /storage(.*)/,
                            match = filePath.match(pattern),
                            path = match ? match[1] : '';

                        let url = new URL(location.href), baseUrl = url.origin; // Obtener la URL base (por ejemplo, http://localhost)

                        let fullPath = baseUrl + '/cmit/storage' + path;

                        let link = document.createElement('a');
                        link.href = fullPath;
                        link.download = "clientes.xlsx";
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
            toastr.error('Debes seleccionar al menos un cliente para exportar.', 'Error');
        }

    });


    //Reset de busquedas
    $(document).on('keydown', function(event) {
        if (event.keyCode === 27) {

            window.location.href = GOINDEX;
        }
    });

    $(document).on('click', '.downCliente', function(){

        let cliente = $(this).data('id');
        
        if(cliente === '') return;

        if(confirm("¿Está seguro que desea dar de baja al cliente?")){

            $.post(baja, {_token: TOKEN, Id: cliente})
            .done(function(){
                toastr.success('Se ha dado de baja al cliente de manera correcta', 'Perfecto');
                setTimeout(()=>{
                    $('#listaClientes').DataTable();
                    $('#listaClientes').DataTable().draw(false);
                },3000);
            })
            .fail(function(xhr){
                toastr('Ha ocurrido un error. Consulte con el administrador', 'Error');
                console.error(xhr);
            });
        }
       
    });


});