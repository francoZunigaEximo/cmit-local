$(function(){

    $(document).on('click', '.btnBajaMultiple, .downCliente', function(e) {
        e.preventDefault();

        let ids = [];

        if( $(this).hasClass('btnBajaMultiple')) {
            $('input[name="Id"]:checked').each(function() {
                ids.push($(this).val());
            });
        }else{
            ids.push($(this).data('id'));
        }
        
        if (!ids) {
            toastr.warning('Debe seleccionar al menos un cliente para la baja múltiple', '', { timeOut: 1000 });
            return; 
        }

        swal({
            title: "¿Estás seguro de que deseas realizar la baja de él o los clientes seleccionados?",
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
                        preloader('off');
                        toastr.success(response.msg, '', { timeOut: 2000 });

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



    $(document).on('click', '#excelTodo', function(e) {
        e.preventDefault();

        let tipo = $('#tipoCliente').val(),
            filtro = $('#filtro').val(),
            formaPago = $('#Fpago').val(),
            buscar = $('#buscar').val();

        preloader('on')
        $.get(excelTodo, {tipo: tipo, filtro: filtro, formaPago: formaPago, buscar: buscar, base: 'excelTodo'})
            .done(function(response) {
                preloader('off');
                let tipoToastr = response.estado === 'success' ? 'success' : 'warning';
                createFile("excel", response.filePath, generarCodigoAleatorio() + '_reporte');
                toastr[tipoToastr](response.msg);
                return;
            })
            .fail(function(jqXHR){
                preloader('off');            
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            })
        

    });

    //Exportar Excel a clientes
    $(document).on('click', '#excel', function(e){
        e.preventDefault();

        let ids = [];

         $('input[name="Id"]:checked').each(function() {
               ids.push($(this).val());
            });


        if(ids.length === 0) {
            toastr.warning('Debes seleccionar al menos un cliente para exportar.', '', { timeOut: 1000 });
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
                        Id: id,
                        base: 'excel'
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

});