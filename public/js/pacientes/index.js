$(function(){

    const principal = {
        btnBajaMultiple: $('#btnBajaMultiple'),
        listaPac: $('#listaPac '),
        dataTables_processing: $(".dataTables_processing"),
        excel: $('#excel'),
        downPaciente: $('.downPaciente')
    };

    principal.btnBajaMultiple.click(function(e) {
        e.preventDefault();

        let ids = [];
        $('input[name="Id"]:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            toastr.warning("Debe seleccionar al menos un paciente para la baja múltiple",'',{timeOut: 1000});
            return; 
        }

        swal({
            title: "¿Estás seguro de que deseas realizar la baja múltiple de los pacientes seleccionados?",
            icon: "warning",
            buttons: ["No", "Sí"],
        }).then((confirmar) => {
            if(confirmar) {

                principal.listaPac
                .find('tbody')
                .hide();
                principal.dataTables_processing.show();

                $.post(down, {_token: TOKEN, ids: ids})
                .done(function(response) {

                    let tipo = {
                        200: 'success',
                        409: 'warning'
                    };

                    response.forEach(function(data) {
                        toastr[tipo[data.status]](data.msg);
                    });
                    
                    principal.listaPac.DataTable().ajax.reload(function() {
                        principal.listaPac
                            .find('tbody')
                            .show();
                        principal.dataTables_processing.hide();
                    }, false);

                })
                .fail(function(jqXHR) {
                    preloader('off');
                    checkError(jqXHR.status, errorData.msg);
                    return;
                });
            } 
        });
    });

    principal.excel.on('click', function(e){
        e.preventDefault();
        
        let ids = [], 
            table = $('#listaPac').DataTable();
        
        let pageInfo = table.page.info();
        let totalPages = pageInfo.pages; // Número total de páginas
        let currentPage = table.page(); // Guardar la página actual para restaurarla después

        console.log("Total de páginas:", totalPages); // Verificar el número total de páginas

        // Función recursiva para procesar todas las páginas
        function processAllPages(pageIndex) {
            if (pageIndex >= totalPages) {
                // Cuando se procesan todas las páginas, restaurar la página original
                table.page(currentPage).draw(false);
                console.log("Todos los IDs capturados:", ids); // Mostrar los IDs capturados
                return;
            }

            console.log("Procesando página:", pageIndex); // Verificar qué página se está procesando

            // Ir a la página actual y forzar una solicitud al servidor
            table.page(pageIndex).draw(true);

            // Esperar a que los datos se carguen antes de continuar
            setTimeout(() => {
                // Iterar sobre las filas de la página actual
                table.rows({ page: 'current' }).every(function () {
                    let rowData = this.data();
                    console.log("Datos de fila:", rowData); // Verificar los datos de cada fila
                    let id = rowData.Id; // Acceder al campo Id
                    ids.push(id); // Agregar el ID al array
                });

                // Procesar la siguiente página
                processAllPages(pageIndex + 1);
            }, 500); // Pequeño retraso para permitir que los datos se carguen
        }

        // Iniciar el procesamiento desde la primera página
        processAllPages(0);

        if (ids.length === 0) {
            toastr.warning('Debes seleccionar al menos un paciente para exportar.','',{timeOut: 1000});
            return;
        }

        swal({
            title: "¿Estás seguro de que deseas generar el reporte de Excel con todos los items seleccionados?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"],
        }).then((confirmar) => {
            if(confirmar){
                preloader('on');

                $.get(exportExcel, {Id: ids})
                    .done(function(response){
                        preloader('off');
                        let tipoToastr = response.estado === 'success' ? 'success' : 'warning';
                        createFile("excel", response.filePath, generarCodigoAleatorio() + "_reporte");
                        toastr[tipoToastr](response.msg);
                        return;
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

    principal.downPaciente.on('click', function(e){
        e.preventDefault();
        let paciente = $(this).data('id'), fullName = $(this).data('nombrecompleto');

        swal({
            title: `¿Está seguro que desea dar de baja a este paciente ${fullName}?`,
            icon: "warning",
            buttons: ["Cancelar", "Dar de baja"],
        }).then((confirmar) => {
            if(confirmar) {
                
                principal.listaPac
                    .find('tbody')
                    .show();
                
                principal.dataTables_processing.show();

                $.post(down, {_token: TOKEN, ids: paciente})
                .done(function(response){
              

                    let tipo = {
                        200: 'success',
                        409: 'warning'
                    };

                    response.forEach(function(data) {
                        toastr[tipo[data.status]](data.msg);
                    });
                    
                    principal.listaPac.DataTable().ajax.reload(function() {
                        principal.listaPac
                            .find('tbody')
                            .show();
                        principal.dataTables_processing.hide();
                    }, false);

                })
                .fail(function(jqXHR){
      
                    let errorData = JSON.parse(jqXHR.responseText);            
                    checkError(jqXHR.status, errorData.msg);
                    return;
                    
                })
            } 
        });
  
    });




});