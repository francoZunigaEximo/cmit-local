$(document).ready(() => {
    cargarTabla();

    $('#check-todos').on('change', function () {
        const check = this.checked;
        $('.fila-checkbox').prop('checked', check);
        $('.grupo-checkbox').prop('checked', check);
    });

    $("#buscarItemsAnulados").on('click', function () {
        cargarTabla();
    });
});

function cargarTabla() {
    $('#tablaItemsAnulados').DataTable().clear().destroy();

    const tabla = new DataTable("#tablaItemsAnulados", {
        searching: false,
        ordering: false,
        processing: true,
        lengthChange: false,
        pageLength: 150,
        responsive: false,
        serverSide: true,
        deferRender: true,
        scrollCollapse: true,
        autoWidth: false,
        select: {
            style: 'multi'
        },
        ajax: {
            url: items_facturas,
            data: function (e) {
                e.IdEmpresa = parseInt(idCliente);
                e.fechaDesde = $('#fechaDesde').val();
                e.fechaHasta = $('#fechaHasta').val();
                e.NroFactura = $('#nroFactura').val();
                e.IdPrestacion = $('#nroPrestacion').val();
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
                    return `<input type="checkbox" class="fila-checkbox" data-factura="${row.NroFactura}" value="${data.Id}" />`;
                }
            },
            {
                data: null,
                name: 'Fecha Anulado',
                orderable: true,
                targets: 1,
                render: function (data) {
                    return `<div class="text-start"><span>${data.FechaAnulado}</span></div>`;
                }

            },
            {
                data: null,
                name: 'Prestacion',
                orderable: true,
                targets: 2,
                render: function (data) {
                    return `<div class="text-start"><span>${data.Prestacion}</span></div>`;
                }

            },
            {
                data: null,
                name: 'Factura',
                orderable: true,
                targets: 3,
                render: function (data) {
                    return `<div class="text-start"><span>${data.Tipo}-${String(data.Sucursal).padStart(4, '0')}-${String(data.NroFactura).padStart(8, '0')}</span></div>`;
                }

            },
            {
                data: null,
                name: 'Examen',
                orderable: true,
                targets: 4,
                render: function (data) {

                    return `<div class="text-center">${data.Examen}</div>`;
                }

            },
            {
                data: null,
                name: 'Paciente',
                orderable: true,
                targets: 5,
                render: function (data) {

                    return `<div class="text-center">${data.Paciente}</div>`;
                }

            },
            {
                data: null,
                name: 'Acciones',
                targets: 6,
                render: function (data) {
                    let editar = '<button class="btn btn-sm iconGeneral edit-item-btn" onclick="reactivar(' + data.Id + ')" type="button"><i class="ri-arrow-up-circle-fill" style="font-size: 2em;"></i></button>';
                    editar += `<button class="btn btn-sm iconGeneral edit-item-btn" onclick="altaModalTabla('${data.Id}')" type="button"><i class="ri-file-text-fill" style="font-size: 2em;"></i></button>`;
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
        rowGroup: {
            dataSrc: 'NroFactura',
            startRender: function (rows, group) {
                let data = rows.data()[0];
                return `
                <tr class="grupo">
                    <td><input type="checkbox" class="grupo-checkbox" data-grupo="${group}" /></td>
                    <td colspan="3"><strong>Factura ${data.Tipo}-${String(data.Sucursal).padStart(4, '0')}-${String(data.NroFactura).padStart(8, '0')}</strong></td>
                </tr>
                `;
            }
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
                url: SEARCH,
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


    tabla.on('draw', function () {
        $('.grupo-checkbox').on('change', function () {
            let grupo = $(this).data('grupo');
            console.log(grupo);
            let check = this.checked;

            $(".fila-checkbox[data-factura='" + grupo + "']").prop('checked', check);

        });
    });
}

function reactivar(id) {
    swal({
        title: "¿Está seguro que desea reactivar el examen?",
        icon: "warning",
        buttons: ["Cancelar", "Aceptar"]
    }).then((confirmar) => {
        if (confirmar) {
            preloader('on');
            $.ajax({
                url: reactivarItem,
                type: 'POST',
                data: {
                    id: id,
                    _token: TOKEN
                },
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message, '', { timeOut: 1000 });
                        cargarTabla();
                    } else {
                        toastr.warning(response.message, '', { timeOut: 1000 });
                    }
                    preloader('off');
                },
                error: function (xhr, status, error) {
                    preloader('off');
                    toastr.error("Error al reactivar el item.");
                }
            });
        }
    })

}

function reactivarMasivo() {
    swal({
        title: "¿Está seguro que desea reactivar los examenes?",
        icon: "warning",
        buttons: ["Cancelar", "Aceptar"]
    }).then((confirmar) => {
        if (confirmar) {
            let seleccionados = $(".fila-checkbox:checked").map(function () {
                return this.value;
            }).get();

            seleccionados.forEach(seleccionar => {
                reactivar(seleccionar);
            });
        }
    });
}

let itemsSeleccionados;

function altaModalTabla(idItem) {
    itemsSeleccionados = [idItem];
    $("#modalNuevaNC").modal("show")
}

function altaModalMasivo() {
    itemsSeleccionados = $(".fila-checkbox:checked").map(function () {
        return this.value;
    }).get();
    if (itemsSeleccionados.length === 0) {
        toastr.warning("Debe seleccionar al menos un item para crear una nota de crédito.", '', { timeOut: 1000 });
        return;
    }
    $("#modalNuevaNC").modal("show");
}

function altaNotaCredito() {
    let tipo = $('#tipo').val();
    let sucursal = $('#sucursal').val();
    let nroNotaCredito = $('#nroNotaCredito').val();
    let fechaNotaCredito = $('#fechaNotaCredito').val();
    let observacion = $('#observacionNotaCredito').val();

    if (nroNotaCredito === '' || fechaNotaCredito === '') {
        toastr.error("Por favor, complete todos los campos requeridos.");
        return;
    }
    if (itemsSeleccionados.length === 0) {
        toastr.error("Por favor, seleccione al menos un item.");
        return;
    }
    crearNotaCredito(tipo, sucursal, nroNotaCredito, fechaNotaCredito, observacion, itemsSeleccionados.map(id => parseInt(id)));
}

function crearNotaCredito(tipo, sucursal, nroNotaCredito, fechaNotaCredito, observacion, idItems) {
    preloader('on');
    let cliente = parseInt(idCliente);

    $.ajax({
        url: crearNotaCreditoUrl,
        type: 'POST',
        data: {
            Tipo: tipo,
            Sucursal: sucursal,
            NroNotaCredito: nroNotaCredito,
            Fecha: fechaNotaCredito,
            Observacion: observacion,
            items: idItems,
            IdCliente: cliente,
            _token: TOKEN
        },
        success: function (response) {
            if (response.success) {
                toastr.success(response.message, '', { timeOut: 1000 });
                cargarTabla();
            } else {
                toastr.warning(response.message, '', { timeOut: 1000 });
            }
            preloader('off');
        },
        error: function (xhr, status, error) {
            preloader('off');
            toastr.error("Error al crear la nota de crédito.");
        }
    });
}
