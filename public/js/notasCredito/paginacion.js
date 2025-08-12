$(function () {
    //cargamos los combos de lientes
    $('#cliente').select2({
        language: {
            noResults: function () {

                return "No hay empresas con esos datos";
            },
            searching: function () {

                return "Buscando..";
            },
            inputTooShort: function () {
                return "Por favor, ingrese 2 o más caracteres";
            }
        },
        placeholder: 'Nombre Empresa, Alias o ParaEmpresa',
        allowClear: true,
        ajax: {
            url: getClientesSelect,
            dataType: 'json',
            data: function (params) {
                return {
                    buscar: params.term,
                    tipo: 'E'
                };
            },
            processResults: function (data) {
                return {
                    results: data.clientes
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    $("#clienteNota").select2({
        language: {
            noResults: function () {

                return "No hay empresas con esos datos";
            },
            searching: function () {

                return "Buscando..";
            },
            inputTooShort: function () {
                return "Por favor, ingrese 2 o más caracteres";
            }
        },
        placeholder: 'Nombre Empresa, Alias o ParaEmpresa',
        allowClear: true,
        ajax: {
            url: getClientesSelect,
            dataType: 'json',
            data: function (params) {
                return {
                    buscar: params.term,
                    tipo: 'E'
                };
            },
            processResults: function (data) {
                return {
                    results: data.clientes
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    $("#buscarCliente").on('click', function () {
        cargarTablaClientes();
    });

    $("#buscarNotas").on('click', function () {
        cargarTablaNotas();
    });

    $('#check-todas-notas').on('change', function () {
        const check = this.checked;
        $('.fila-checkbox').prop('checked', check);
    });
})

function cargarTablaClientes() {
    $('#listaClientes').DataTable().clear().destroy();

    new DataTable("#listaClientes", {
        searching: false,
        ordering: false,
        processing: true,
        lengthChange: false,
        pageLength: 100,
        responsive: false,
        serverSide: true,
        deferRender: true,
        scrollCollapse: true,
        autoWidth: false,
        select: {
            style: 'multi'
        },
        ajax: {
            url: getClientes,
            data: function (e) {
                e.IdEmpresa = $('#cliente').val();
                e.CUIT = $('#cuit').val();
                e.fechaDesde = $('#fechaDesde').val();
                e.fechaHasta = $('#fechaHasta').val();
            }
        },
        dataType: 'json',
        type: 'POST',
        columnDefs: [

            {
                data: null,
                name: 'Cliente',
                orderable: true,
                targets: 0,
                render: function (data) {
                    return `<div class="text-start"><span>${data.Cliente}</span></div>`;
                }

            },
            {
                data: null,
                name: 'CUIT',
                orderable: true,
                targets: 1,
                render: function (data) {
                    return `<div class="text-start"><span>${data.CUIT == null ? "" : data.CUIT}</span></div>`;
                }

            },
            {
                data: null,
                name: 'Acciones',
                targets: 2,
                render: function (data) {

                    let editar = '<a title="Items Anulados" href="' + location.href + '/itemsanulados/' + data.Id + '">' + '<button type="button" class="btn btn-sm iconGeneral edit-item-btn"> <i class="ri-edit-line"></i> </button>' + '</a>';
                    return editar;
                }
            }
        ],
        language: {
            processing: "<div style='text-align: center; margin-top: 20px;'><img src='./images/spinner.gif' /><p>Cargando...</p></div>",
            emptyTable: "No hay paquetes con los datos buscados",
            paginate: {
                first: "Primera",
                previous: "Anterior",
                next: "Siguiente",
                last: "Última"
            },
            aria: {
                paginate: {
                    first: "Primera",
                    previous: "Anterior",
                    next: "Siguiente",
                    last: "Última"
                }
            },
            info: "Mostrando _START_ a _END_ de _TOTAL_ de paquetes",
        },
        stateLoadCallback: function (settings, callback) {
            $.ajax({
                url: SEARCH,
                dataType: 'json',
                success: function (json) {

                    // Pasar el objeto json a callback
                    callback(json);
                }
            });
        },
        stateSaveCallback: function (settings, data) {
            $.ajax({
                url: getClientes,
                type: 'POST',
                data: {

                },
                dataType: "json",
                success: function (response) { },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error("Error: ", textStatus, errorThrown);
                }
            });
        },
    });
}

function cargarTablaNotas() {

    $('#listaNotas').DataTable().clear().destroy();

    const tabla = new DataTable("#listaNotas", {
        searching: false,
        ordering: false,
        processing: true,
        lengthChange: false,
        pageLength: 100,
        responsive: false,
        serverSide: true,
        deferRender: true,
        scrollCollapse: true,
        autoWidth: false,
        select: {
            style: 'multi'
        },
        ajax: {
            url: getNotas,
            data: function (e) {
                e.IdEmpresa = $('#clienteNota').val();
                e.fechaDesde = $('#fechaNotaDesde').val();
                e.fechaHasta = $('#fechaNotaHasta').val();
                e.NroDesde = $('#NroDesde').val();
                e.NroHasta = $('#NroHasta').val();
            }
        },
        dataType: 'json',
        type: 'POST',
        columnDefs: [
            {
                data: null,
                orderable: false,
                targets: 0,
                className: 'select-checkbox',
                defaultContent: '',
                render: function (data, type, row) {
                    return `<input type="checkbox" class="fila-checkbox" value="${data.Id}" />`;
                }
            },
            {
                data: null,
                name: 'Id',
                orderable: true,
                targets: 1,
                render: function (data) {
                    return `<div class="text-start"><span>${data.Id == null ? "" : data.Id}</span></div>`;
                }

            },
            {
                data: null,
                name: 'NC',
                targets: 2,
                render: function (data) {
                    var nroFactura = `${data.Tipo}-${String(data.Sucursal).padStart(4, '0')}-${String(data.NroNotaCredito).padStart(8, '0')}`;
                    return `<div class="text-start"><span>${nroFactura == null ? "" : nroFactura}</span></div>`;
                }
            },
            {
                data: null,
                name: 'Fecha',
                targets: 3,
                render: function (data) {
                    return `<div class="text-start"><span>${data.Fecha == null ? "" : data.Fecha}</span></div>`;
                }
            },
            {
                data: null,
                name: 'Empresa',
                targets: 4,
                render: function (data) {
                    return `<div class="text-start"><span>${data.Empresa == null ? "" : data.Empresa}</span></div>`;
                }
            },
            {
                data: null,
                name: 'CUIT',
                targets: 5,
                render: function (data) {
                    return `<div class="text-start"><span>${data.CUIT == null ? "" : data.CUIT}</span></div>`;
                }
            },
            {
                data: null,
                name: 'Observaciones',
                targets: 6,
                render: function (data) {
                    return `<div class="text-start"><span>${data.Observacion == null ? "" : data.Observacion}</span></div>`;
                }
            },
            {
                data: null,
                name: 'Acciones',
                targets: 7,
                render: function (data) {

                    let editar = '<a title="Items Anulados" href="' + location.href + '/editarNotaCredito/' + data.Id + '">' + '<button type="button" class="btn btn-sm iconGeneral edit-item-btn"> <i class="ri-edit-line"></i> </button>' + '</a>';
                    editar += '<button class="btn btn-sm iconGeneral remove-item-btn" onclick="eliminarNotaCredito(' + data.Id + ')" type="button"><i class="ri-delete-bin-5-fill"></i></button>';
                    return editar;
                }
            }
        ],
        language: {
            processing: "<div style='text-align: center; margin-top: 20px;'><img src='./images/spinner.gif' /><p>Cargando...</p></div>",
            emptyTable: "No hay paquetes con los datos buscados",
            paginate: {
                first: "Primera",
                previous: "Anterior",
                next: "Siguiente",
                last: "Última"
            },
            aria: {
                paginate: {
                    first: "Primera",
                    previous: "Anterior",
                    next: "Siguiente",
                    last: "Última"
                }
            },
            info: "Mostrando _START_ a _END_ de _TOTAL_ de paquetes",
        },
        stateLoadCallback: function (settings, callback) {
            $.ajax({
                url: SEARCH,
                dataType: 'json',
                success: function (json) {

                    // Pasar el objeto json a callback
                    callback(json);
                }
            });
        },
        stateSaveCallback: function (settings, data) {
            $.ajax({
                url: getClientes,
                type: 'POST',
                data: {

                },
                dataType: "json",
                success: function (response) { },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error("Error: ", textStatus, errorThrown);
                }
            });
        },
    });


    
}

function eliminarNotaCredito(id) {
        swal({
            title: "¿Está seguro que desea eliminar esta nota de crédito?",
            icon: "warning",
            buttons: ["Cancelar", "Eliminar"],
        }).then((aceptar) => {
            if (aceptar) {
                preloader('on');
                $.ajax({
                    url: eliminarNotaCreditoPost,
                    type: 'POST',
                    data: {
                        id: id,
                        _token: TOKEN
                    },
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message, '', { timeOut: 1000 });
                            setTimeout(function () {
                                cargarTablaNotas();
                            }, 1000);
                            
                        } else {
                            toastr.error(response.message, '', { timeOut: 1000 });
                        }
                        preloader('off');
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        toastr.error(jqXHR.responseJSON.message, '', { timeOut: 1000 });
                        console.error("Error: ", textStatus, errorThrown);
                    }
                });
            }
        });

}

 $(".btnExcelNotas").on("click", function (e) {
        preloader('on');
        
        $.get(exportarNotaCreditoExcel,
            {
                IdEmpresa : $('#clienteNota').val(),
                fechaDesde: $('#fechaNotaDesde').val(),
                fechaHasta: $('#fechaNotaHasta').val(),
                NroDesde: $('#NroDesde').val(),
                NroHasta: $('#NroHasta').val()
            })
            .done(function (response) {
                preloader('off');
                createFile("excel", response.filePath, generarCodigoAleatorio() + "_paquetes_estudios");
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

$(".btnExcelClientesItemsAnulados").on("click", function (e) {
    preloader('on');
    $.get(exportClientesItemsAnuladosExcel,{
        IdEmpresa : $('#cliente').val(),
        CUIT: $('#cuit').val(),
        fechaDesde: $('#fechaDesde').val(),
        fechaHasta: $('#fechaHasta').val()
    })
    .done(function (response) {
        preloader('off');
        createFile("excel", response.filePath, generarCodigoAleatorio() + "_clientes_items_anulados");
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

function obtenerIdsSeleccionados(){
    itemsSeleccionados = $(".fila-checkbox:checked").map(function () {
        return this.value;
    }).get();
    return itemsSeleccionados;
}

function eliminarNotaCreditoMasivo(){
    const ids = obtenerIdsSeleccionados(); // Implementa esta función para obtener los IDs seleccionados
    if (ids.length === 0) {
        toastr.warning("No se han seleccionado notas de crédito para eliminar.");
        return;
    }

    swal({
        title: "¿Está seguro que desea eliminar estas notas de crédito?",
        icon: "warning",
        buttons: ["Cancelar", "Eliminar"],
    }).then((aceptar) => {
        if (aceptar) {
            $.ajax({
                url: eliminarNotaCreditoMasivoPost,
                type: 'POST',
                data: {
                    ids: ids,
                    _token: TOKEN
                },
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        if(response.result.eliminadas > 0) toastr.success("Notas de crédito eliminadas: " + response.result.eliminadas, '', { timeOut: 1000 });
                        if(response.result.no_encontradas > 0) toastr.warning("Notas de crédito no encontradas: " + response.result.no_encontradas, '', { timeOut: 1000 });
                        if(response.result.no_eliminadas > 0) toastr.error("Notas de crédito no eliminadas: " + response.result.no_eliminadas, '', { timeOut: 1000 });
                        setTimeout(function () {
                            cargarTablaNotas();
                        }, 1000);
                    } else {
                        toastr.error(response.message, '', { timeOut: 1000 });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    toastr.error(jqXHR.responseJSON.message, '', { timeOut: 1000 });
                    console.error("Error: ", textStatus, errorThrown);
                }
            });
        }
    });
}

