$(document).ready(function(){

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
            preloader('on');
            $.ajax({
                url: multipleDown,
                type: 'POST',
                data: {
                    _token: TOKEN,
                    ids: ids
                },
                success: function(response) {
                    preloader('off');
                    toastr.success(response.msg);
                    setTimeout(()=>{
                        $('#listaClientes').DataTable().draw(false);
                    }, 3000);
                   
                },
                error: function(jqXHR, xhr) {
                    preloader('off');            
                    let errorData = JSON.parse(jqXHR.responseText);            
                    checkError(jqXHR.status, errorData.msg);
                    return;    
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
                preloader('on');
                $.ajax({
                    url: exportExcelClientes,
                    type: "GET",
                    data: {
                        Id: ids
                    },
                    success: function(response) {
                        preloader('off');
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
                    error: function(xhr,jqXHR) {
                        preloader('off');            
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return; 
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
            preloader('on');
            $.post(baja, {_token: TOKEN, Id: cliente})
            .done(function(response){
                preloader('off');
                toastr.success(response.msg);
                setTimeout(()=>{
                    $('#listaClientes').DataTable();
                    $('#listaClientes').DataTable().draw(false);
                },3000);
            })
            .fail(function(jqXHR, xhr){
                preloader('off');            
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;                
            });
        }
       
    });


});