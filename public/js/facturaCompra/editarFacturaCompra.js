
$(document).ready(function () {

    cargarTablaExamenesEfector();
    cargarTablaExamenesInformador();

    $('#checkAllEfector').on('change', function () {
        const check = this.checked;
        $('.fila-checkbox-efector').prop('checked', check);
    });

    $('#checkAllInformador').on('change', function () {
        const check = this.checked;
        $('.fila-checkbox-informador').prop('checked', check);
    });
});


$("#editarFactura").on("click", function () {
    swal({
            title: "¿Está seguro que desea editar la factura?",
            icon: "warning",
            buttons: ["Cancelar", "Eliminar"],
        }).then((aceptar) => {
            if (aceptar) {
                editarFactura();
            }
        });
});

function cargarTablaExamenesEfector() {


    $('#listadoExamenesEfector').DataTable().clear().destroy();

    const tabla = new DataTable("#listadoExamenesEfector", {
        searching: false,
        ordering: false,
        processing: true,
        lengthChange: false,
        pageLength: 50,
        responsive: false,
        serverSide: true,
        deferRender: true,
        scrollCollapse: true,
        autoWidth: false,
        select: {
            style: 'multi'
        },
        ajax: {
            url: rutaListarExamenesEfector,
            data: function (e) {
                e.idFactura = ID;
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
                    var check = $("#checkAllEfectores").is(":checked") ? "checked" : "";
                    return `<input type="checkbox" class="fila-checkbox-efector" value="${data.Id}" ${check} />`;
                }
            },
            {
                data: null,
                name: 'Paciente',
                orderable: true,
                targets: 1,
                render: function (data) {
                    return `<div class="text-start"><span>${data.idPrestacion == null ? "" : data.idPrestacion}</span></div>`;
                }

            },
            {
                data: null,
                name: 'Fecha',
                targets: 2,
                render: function (data) {
                    return `<div class="text-start"><span>${data.Fecha == null ? "" : data.Fecha}</span></div>`;
                }
            },
            {
                data: null,
                name: 'Examen',
                targets: 3,
                render: function (data) {
                    return `<div class="text-start"><span>${data.Examen == null ? "" : data.Examen}</span></div>`;
                }
            }, {
                data: null,
                name: 'Paciente',
                targets: 4,
                render: function (data) {
                    return `<div class="text-start"><span>${data.Paciente == null ? "" : data.Paciente}</span></div>`;
                }
            },
            {
                data: null,
                name: 'Acciones',
                targets: 5,
                render: function (data) {
                    var adjuntado = "";
                    if(data.Adjunto == 1){
                        adjuntado = data.Adjuntado == 1 ? "<i title='Adjuntado' class='ri-attachment-line verde'></i>" : `<i title='No Adjuntado' class='ri-attachment-line rojo'></i>`;
                    }
                    var bloquear = data.Anulado == 1 ? "<i title='Bloqueado' class='ri-forbid-2-line rojo'></i>" : "";

                    return `<button type="button" class="btn btn-sm iconGeneral remove-item-btn" onclick="eliminarItemEfector(${data.Id})"><i class="ri-delete-bin-2-fill" style="font-size: 2em;"></i></button>${adjuntado} ${bloquear}`;
                }
            }
        ],
        fnCreatedRow: function( row, data, dataIndex ) {
            console.log(data);
            if ( data.Anulado == 1  ) {
                $(row).addClass('table-danger border'); // Agrega una clase personalizada
            }
        },
        language: {
            processing:
                `<div class="text-center p-2">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>`,
            emptyTable: "No hay efectores con los datos buscados",
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
            info: "Mostrando _START_ a _END_ de _TOTAL_ de efectores",
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

        },
    });

}

function cargarTablaExamenesInformador() {


    $('#listadoExamenesInformador').DataTable().clear().destroy();

    const tabla = new DataTable("#listadoExamenesInformador", {
        searching: false,
        ordering: false,
        processing: true,
        lengthChange: false,
        pageLength: 50,
        responsive: false,
        serverSide: true,
        deferRender: true,
        scrollCollapse: true,
        autoWidth: false,
        select: {
            style: 'multi'
        },
        ajax: {
            url: rutaListarExamenesInformador,
            data: function (e) {
                e.idFactura = ID;
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
                    var check = $("#checkAllInformador").is(":checked") ? "checked" : "";
                    return `<input type="checkbox" class="fila-checkbox-informador" value="${data.Id}" ${check} />`;
                }
            },
            {
                data: null,
                name: 'Paciente',
                orderable: true,
                targets: 1,
                render: function (data) {
                    return `<div class="text-start"><span>${data.IdPrestacion == null ? "" : data.IdPrestacion}</span></div>`;
                }

            },
            {
                data: null,
                name: 'Fecha',
                targets: 2,
                render: function (data) {
                    return `<div class="text-start"><span>${data.Fecha == null ? "" : data.Fecha}</span></div>`;
                }
            },
            {
                data: null,
                name: 'Examen',
                targets: 3,
                render: function (data) {
                    return `<div class="text-start"><span>${data.Examen == null ? "" : data.Examen}</span></div>`;
                }
            }, {
                data: null,
                name: 'Paciente',
                targets: 4,
                render: function (data) {
                    return `<div class="text-start"><span>${data.ApPac == null ? "" : data.ApPac}, ${data.NomPac == null ? "" : data.NomPac}</span></div>`;
                }
            },
            {
                data: null,
                name: 'Acciones',
                targets: 5,
                render: function (data) {
                    var adjuntado = "";
                    if(data.Adjunto == 1){
                        adjuntado = data.Adjuntado == 1 ? "<i title='Adjuntado' class='ri-attachment-line verde'></i>" : `<i title='No Adjuntado' class='ri-attachment-line rojo'></i>`;
                    }
                    var bloquear = data.Anulado == 1 ? "<i title='Bloqueado' class='ri-forbid-2-line rojo'></i>" : "";

                    let editar = `<button type="button" class="btn btn-sm iconGeneral edit-item-btn" onclick="eliminarItemInformador(${data.Id})"> <i class="ri-delete-bin-5-line"></i> </button>${adjuntado} ${bloquear}`;
                    return editar;
                }
            }
        ],
        fnCreatedRow: function( row, data, dataIndex ) {
            console.log(data);
            if ( data.Anulado == 1  ) {
                $(row).addClass('table-danger border'); // Agrega una clase personalizada
            }
        },
        language: {
            processing:
                `<div class="text-center p-2">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>`,
            emptyTable: "No hay efectores con los datos buscados",
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
            info: "Mostrando _START_ a _END_ de _TOTAL_ de efectores",
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

        },
    });

}

function editarFactura() {
    var idFactura = ID;
    var tipo = $('#tipo').val();
    var sucursal = $('#sucursal').val();
    var nroFactura = $('#nroFactura').val();
    var obs = $('#observaciones').val();
    var fecha = $('#fecha').val();
    preloader('on');
    $.ajax({
        url: rutaEditarFactura,
        type: 'POST',
        data: {
            idFactura: idFactura,
            TipoFactura: tipo,
            SucursalFactura: sucursal,
            NroFactura: nroFactura,
            Obs: obs,
            Fecha: fecha,
            _token: TOKEN
        },
        success: function (response) {
            if (response.success) {
                toastr.success(response.message, '', { timeOut: 1000 });
            } else {
                toastr.warning(response.message, '', { timeOut: 1000 });
            }
        },
        complete: function () {
            preloader('off');
        },
        error: function (xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al editar la factura.'
            });
        }
    });
}

function eliminarItemEfector(id) {
    swal({
            title: "¿Está seguro que desea eliminar el item de la factura compra?",
            icon: "warning",
            buttons: ["Cancelar", "Eliminar"],
        }).then((aceptar) => {
            if (aceptar) {
               eliminarItemEfectorAjax(id);
            }
        });
}

function eliminarItemEfectorAjax(id) {
    preloader('on');
    $.ajax({
        url: eliminarItemFacturaCompraEfector,
        type: 'POST',
        data: {
            idItemFactura: id,
            _token: TOKEN
        },
        success: function (response) {
            if (response.success) {
                toastr.success(response.message, '', { timeOut: 1000 });
                cargarTablaExamenesEfector();
            } else {
                toastr.warning(response.message, '', { timeOut: 1000 });
            }
        },
        complete: function () {
            preloader('off');
        },
        error: function (xhr, status, error) {
            toastr.error('Ocurrió un error al eliminar el item de la factura.', '', { timeOut: 1000 });
            preloader('off');
        }
    });
}

function eliminarItemInformador(id) {
    swal({
            title: "¿Está seguro que desea eliminar el item de la factura compra?",
            icon: "warning",
            buttons: ["Cancelar", "Eliminar"],
        }).then((aceptar) => {
            if (aceptar) {
               eliminarItemInformadorAjax(id);
            }
        });
}

function eliminarItemInformadorAjax(id){
    preloader('on');
    $.ajax({
        url: eliminarItemFacturaCompraInformador,
        type: 'POST',
        data: {
            idItemFactura: id,
            _token: TOKEN
        },
        success: function (response) {
            if (response.success) {
                toastr.success(response.message, '', { timeOut: 1000 });
                cargarTablaExamenesEfector();
            } else {
                toastr.warning(response.message, '', { timeOut: 1000 });
            }
        },
        complete: function () {
            preloader('off');
        },
        error: function (xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al eliminar el item de la factura.'
            });
        }
    });
}

function eliminarItemsEfectorMasivo(){
    var ids = [];
    $(".fila-checkbox-efector:checked").each(function(){
        let id = $(this).val();
        ids.push(id);
    });
    eliminarItemsEfectorMasivoAjax(ids);
}

function eliminarItemsEfectorMasivoAjax(ids){
    preloader('on');
    $.ajax({
        url: eliminarItemsFacturaCompraEfectorMasivo,
        type: 'POST',
        data: {
            idsItemsFactura: ids,
            _token: TOKEN
        },
        success: function (response) {
            if (response.success) {
                toastr.success(response.message, '', { timeOut: 1000 });
                cargarTablaExamenesEfector();
            } else {
                toastr.warning(response.message, '', { timeOut: 1000 });
            }
        },
        complete: function () {
            preloader('off');
        },
        error: function (xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al eliminar los items de la factura.'
            });
        }
    });
}

function eliminarItemsEfectorInformador(){
    var ids = [];
    $(".fila-checkbox-informador:checked").each(function(){
        let id = $(this).val();
        ids.push(id);
    });
    eliminarItemsInformadorMasivoAjax(ids);
}


function eliminarItemsInformadorMasivoAjax(ids){
    preloader('on');
    $.ajax({
        url: eliminarItemsFacturaCompraInformadorMasivo,
        type: 'POST',
        data: {
            idsItemsFactura: ids,
            _token: TOKEN
        },
        success: function (response) {
            if (response.success) {
                toastr.success(response.message, '', { timeOut: 1000 });
                cargarTablaExamenesInformador();
            } else {
                toastr.warning(response.message, '', { timeOut: 1000 });
            }
        },
        complete: function () {
            preloader('off');
        },
        error: function (xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al eliminar los items de la factura.'
            });
        }
    });
}


 $("#btnExportarPdf").on("click", function (e) {
        preloader('on');
        
        $.get(imprimirReporte,
            {
                idFactura: ID,
                
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

$("#btnreporteexcel").on("click", function (e) {
    preloader('on');
    
    $.get(imprimirExcel,
        {
            idFactura: ID,
            _token: TOKEN
        })
        .done(function (response) {
            preloader('off');
            createFile("excel", response.filePath, generarCodigoAleatorio() + "_facturas_compra.xlsx");
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