$(function(){

    const tabla = "#listadoExamenesCuentas";

    $('#fechaHasta, #FechaCreate').val(fechaNow(null, "-", 0));
    $('#estado').val("todos");

    $('#empresaSaldo, #empresaPago, #empresaCreate').each(function() {
        $(this).select2({
            language: {
                noResults: function() {
    
                return "No hay empresas con esos datos";        
                },
                searching: function() {
    
                return "Buscando..";
                },
                inputTooShort: function () {
                    return "Por favor, ingrese 2 o más caracteres";
                }
            },
            placeholder: 'Nombre Empresa, Alias o ParaEmpresa',
            allowClear: true,
            ajax: {
                url: getClientes, 
                dataType: 'json',
                data: function(params) {
                    return {
                        buscar: params.term,
                        tipo: 'E'
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.clientes 
                    };
                },
                cache: true
            },
            minimumInputLength: 2 
        });
    });

    $('#examenSaldo').each(function(){
        $(this).select2({
            language: {
                noResults: function() {
    
                return "No hay examenes con esos datos";        
                },
                searching: function() {
    
                return "Buscando..";
                },
                inputTooShort: function () {
                    return "Por favor, ingrese 2 o más caracteres";
                }
            },
            placeholder: 'Nombre del exámen',
            allowClear: true,
            ajax: {
                url: searchExamen, 
                dataType: 'json',
                data: function(params) {
                    return {
                        buscar: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.examen 
                    };
                },
                cache: true
            },
            minimumInputLength: 2
        });
    });

    Inputmask.extendAliases({
        'rango': {
            mask: 'a-9999-99999999',
            placeholder: "X-0000-00000000",
            clearMaskOnLostFocus: true,
            onBeforePaste: function (pastedValue, opts) {
                
                return pastedValue.charAt(0).toUpperCase() + pastedValue.slice(1);
            },
            definitions: {
                'a': {
                    validator: "[A-Za-z]",
                    cardinality: 1,
                    casing: "upper"
                }
            }
        }
    });

    $("#rangoDesde, #rangoHasta, #FacturaCreate").inputmask('rango');
    
    $(document).on('click', '.cambiarBoton', function(e) {
        
        e.preventDefault();
        let id = $(this).data('id'),
            nroFactura = $(this).data('nro'),
            textBoton = $(this).text().trim(),
            hoy = new Date().toISOString().slice(0, 10);
            empresa = $(this).data('empresa'),
            title = "¿Estas seguro que deseas marcar como pagado el examen?",
            contenido = $('<div>').html(`
                <b>Empresa:</b> ${empresa}<br /> 
                <b>Nro Factura:</b> ${nroFactura}<br />
                <b class="mt-2 fechaExamen">Fecha:</b> <input type="date" id="fecha" name="fecha" value="${hoy}">    
            `);

        if(textBoton === 'Quitar pago') {
            $('#fecha').val('');
            contenido.find('#fecha').hide();
            contenido.find('.fechaExamen').hide();
            title = "¿Estas seguro que deseas quitar el pago del examen?";
        }

        swal({
            title: title,
            content: contenido[0],
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                let fechaSeleccionada = $('#fecha').val();

                preloader('on');
                $.post(cambiarPago, {Id: id, fecha: fechaSeleccionada, _token: TOKEN})
                    .done(function(response){
                        preloader('off');
                        let data = response.resultados
                        toastr.success(data.msg, { timeOut: 1000 })
                        $(tabla).DataTable().draw(false);
                    })
                    .fail(function(jqXHR){
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return;  
                    })
            }
        })

            
        
    });

    $(document).on('click', '.deleteExamen', function(e){
        e.preventDefault();

        let id = $(this).data('id');
        
        swal({
            title: "¿Estas seguro que deseas eliminar el examen a cuenta?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                preloader('on');
                $.post(eliminarExCuenta, {Id: id, _token: TOKEN})
                    .done(function(response){

                        preloader('off');
                        let tipoToastr = response.estado == 'success' ? ['success', 'Perfecto'] : ['info', 'Atención'];

                        toastr[tipoToastr[0]](response.message, [tipoToastr[1]], { timeOut: 10000 })
                        let table = $(tabla).DataTable();
                        table.draw(false);

                    })
            }
        });    
    });
    
    $(document).on('click', '.botonPagar, .quitarPago', function(e){
        e.preventDefault();

        let ids = [], datosEmpresa = [], hoy = new Date().toISOString().slice(0, 10), tipo = $(this).hasClass('botonPagar') ? 'pagoMasivo' : null, textBoton = $(this).text().trim();

        $('input[name="Id"]:checked').each(function() {
            let checkbox = $(this),
                fila = checkbox.closest('tr'),
                empresa = fila.find('td:eq(4)').text().trim(),
                factura = fila.find('td:eq(2)').text().trim();
            
            ids.push(checkbox.val());
            datosEmpresa.push({
                factura: factura,
                empresa: empresa
            });

        });

        if (ids.length === 0) {
            toastr.warning('Debes seleccionar al menos un item para realizar la operación.','',{timeOut: 1000});
            return;
        }

        let filtro = datosEmpresa.filter(item => item.factura !== '');
        let listaHtml = '<div><ul>';
        listaHtml += `<b class="mb-2 mt-1 fechaExamen">Fecha:</b> <input type="date" id="fecha" name="fecha" value="${hoy}"><br>`;
        $.each(filtro, function(index, data){
            listaHtml += `<li class="fs-6">${data.empresa} | Factura: ${data.factura}</li>`;
        });

        listaHtml += '</ul></div>';

        let contenido = $(listaHtml);

        if(textBoton === 'Quitar pago masivo') {
    
            $('#fecha').val('');
            contenido.find('#fecha').hide();
            contenido.find('.fechaExamen').hide();
            title = "¿Estas seguro que deseas quitar los pagos de los examenes?";
        }

        swal({
            title: "¿Esta seguro que desea realizar esta operación?",
            content: contenido[0],
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {

                let fechaSeleccionada = $('#fecha').val();
                preloader('on');
                $.post(cambiarPago, {Id: ids, _token: TOKEN, fecha: fechaSeleccionada, tipo: tipo})
                    .done(function(response){
                        preloader('off');
                        let data = response.resultados,
                            reporte = response.reporte;

                        if(reporte) {
                            createFile("excel", reporte.original.filePath, generarCodigoAleatorio() + '_reporte');
                        }

                        toastr.success(data.msg, { timeOut: 10000 })
                        $(tabla).DataTable().draw(false);

                    })
                    .fail(function(jqXHR){
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return;  
                    })
            }
        });
    });

    $(document).on('click', '.facturasHoy', function(e){
        e.preventDefault();
        /*let table = $(tabla).DataTable();
        table.ajax.url(INDEX).page.len(7).load();*/
        location.reload();
    });

    $(".btnExcelSimple").on("click", function (e) {
        preloader('on');
        var idsSeleccionados = obtenerIdsSeleccionados();
        var allSelected = $("#checkAll").is(":checked");
        $.get(exportSimple,
            {
                fechaDesde : $('#fechaDesde').val(),
                fechaHasta : $('#fechaHasta').val(),
                rangoDesde : $('#rangoDesde').val(),
                rangoHasta : $('#rangoHasta').val(),
                empresa : $('#empresa').val(),
                paciente : $('#paciente').val(),
                examen : $('#examen').val(),
                estado : $('#estado').val(),
                ids: idsSeleccionados,
                all: allSelected
            })
            .done(function (response) {
                preloader('off');
                createFile("excel", response.filePath, generarCodigoAleatorio() + "_examenes_cuenta_simple");
                preloader('off');
                toastr.success(response.msg);
                return;
            })
            .fail(function (jqXHR) {
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);
                checkError(jqXHR.status, errorData.msg);
                return;
            });
    });

    $(".btnExcelCompleto").on("click", function (e) {
        preloader('on');
        var idsSeleccionados = obtenerIdsSeleccionados();
        var allSelected = $("#checkAll").is(":checked");

        $.get(exportCompleto,
            {
                fechaDesde : $('#fechaDesde').val(),
                fechaHasta : $('#fechaHasta').val(),
                rangoDesde : $('#rangoDesde').val(),
                rangoHasta : $('#rangoHasta').val(),
                empresa : $('#empresa').val(),
                paciente : $('#paciente').val(),
                examen : $('#examen').val(),
                estado : $('#estado').val(),
                ids: idsSeleccionados,
                all: allSelected
            })
            .done(function (response) {
                preloader('off');
                createFile("excel", response.filePath, generarCodigoAleatorio() + "_examenes_cuenta_completo");
                preloader('off');
                toastr.success(response.msg);
                return;
            })
            .fail(function (jqXHR) {
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);
                checkError(jqXHR.status, errorData.msg);
                return;
            });
    });


});

function obtenerIdsSeleccionados(){
        itemsSeleccionados = $(".fila-checkbox:checked").map(function () {
            return this.value;
        }).get();
        return itemsSeleccionados;
    }