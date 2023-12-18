$(document).ready(function(){

    
    $('#btnBajaMultiple').click(function(e) {
        e.preventDefault();

        let ids = [];
        $('input[name="Id"]:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            swal('Atención', 'Debe seleccionar al menos un cliente para la baja múltiple', 'warning');
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
                    swal('Acción realizada', '¡Se ha dado de baja a los clientes correctamente!', 'success');
                    $('#listaClientes').DataTable().draw(false);
                },
                error: function(xhr) {
                    swal('error', '¡Ha ocurrido un inconveniente y la solicitud no podrá llevarse a cabo. Consulte con el administrador!', 'error');
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
                    type: "POST",
                    data: {
                        _token: TOKEN,
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
            swal('Error', 'Debes seleccionar al menos un paciente para exportar.', 'error');
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
                swal('Perfecto', 'Se ha dado de baja al cliente de manera correcta', 'success');
                $('#listaClientes').DataTable();
                $('#listaClientes').DataTable().draw(false);

            })
            .fail(function(xhr){
                swal('Error', 'Ha ocurrido un error. Consulte con el administrador', 'error');
                console.error(xhr);
            });
        }
       
    });


});