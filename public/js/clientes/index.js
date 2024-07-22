$(document).ready(function(){

    const tabla = "#listaClientes";

    $('#btnBajaMultiple').click(function(e) {
        e.preventDefault();

        let ids = [];
        $('input[name="Id"]:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            toastr.warning('Debe seleccionar al menos un cliente para la baja múltiple');
            return; 
        }

        swal({
            title: "¿Estás seguro de que deseas realizar la baja múltiple de los clientes seleccionados?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar)=> {
            if(confirmar){
                
                $('#listaClientes tbody').hide();
                $(".dataTables_processing").show();

                $.ajax({
                    url: multipleDown,
                    type: 'POST',
                    data: {
                        _token: TOKEN,
                        ids: ids
                    },
                    success: function(response) {
                        toastr.success(response.msg);
                        $('#listaClientes').DataTable().ajax.reload(function() {
                            $('#listaClientes tbody').show();
                            $(".dataTables_processing").hide();
                        }, false);
                    
                    },
                    error: function(jqXHR) {
                        preloader('off');            
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return;    
                    }
                });
            }
        });
    });


    //Exportar Excel a clientes
    $('#excel').click(function(e) {
        e.preventDefault();

        let ids = [];
        $('input[name="Id"]:checked').each(function() {
            ids.push($(this).val());
        });

        if(ids.length === 0) {
            toastr.warning('Debes seleccionar al menos un cliente para exportar.');
            return;
        }

        swal({
            title: "¿Estás seguro de que deseas generar el reporte de Excel con todos los items seleccionados?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar){
                preloader('on');
                $.ajax({
                    url: exportExcelClientes,
                    type: "GET",
                    data: {
                        Id: ids
                    },
                    success: function(response) {
                        preloader('off');
                        let tipoToastr = response.estado === 'success' ? 'success' : 'warning';
                        createFile("excel", response.filePath, generarCodigoAleatorio() + '_reporte');
                        toastr[tipoToastr](response.msg);
                        return;
                    },
                    error: function(xhr,jqXHR) {
                        preloader('off');            
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return; 
                    }
                });
            }
        });
    });

    //Reset de busquedas
    $(document).on('keydown', function(event) {
        if (event.keyCode === 27) {
            window.location.href = GOINDEX;
        }
    });

    $(document).on('click', '.downCliente', function(e){
        e.preventDefault();
        let cliente = $(this).data('id');
        
        if(cliente === '') return;

        swal({
            title: "¿Está seguro que desea dar de baja al cliente?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar)=>{
            if(confirmar){

                $('#listaClientes tbody').hide();
                $(".dataTables_processing").show();

                $.post(baja, {_token: TOKEN, Id: cliente})
                .done(function(response){
                    toastr.success(response.msg);
                    
                    $('#listaClientes').DataTable().ajax.reload(function(){
                        $('#listaClientes tbody').show();
                        $(".dataTables_processing").hide();
                    }, false);

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


});