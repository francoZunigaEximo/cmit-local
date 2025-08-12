$(document).ready(() => {
    cargarTabla();
})

let itemsEliminar = [];

function cargarTabla() {
    $('#itemsNotaCredito').DataTable().clear().destroy();

    const tabla = new DataTable("#itemsNotaCredito", {
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
            url: getItemsNotaCredito,
            data: function (e) {
                e.IdNC = parseInt(idNota);
            }
        },
        dataType: 'json',
        type: 'POST',
        columnDefs: [
            {
                data: null,
                orderable: false,
                targets: 0,
                className: 'select-checkbox text-center',
                defaultContent: '',
                render: function (data, type, row) {
                    return `<input type="checkbox" class="fila-checkbox" value="${data.Id}" />`;
                }
            },
            {
                data: null,
                name: 'Examen',
                orderable: true,
                targets: 1,
                render: function (data) {
                    return `<div class="text-start"><span>${data.Examen}</span></div>`;
                }

            },
            {
                data: null,
                name: 'Fecha',
                orderable: true,
                targets: 2,
                render: function (data) {
                    return `<div class="text-start"><span>${data.Fecha}</span></div>`;
                }

            },

            {
                data: null,
                name: 'Cliente',
                orderable: true,
                targets: 3,
                render: function (data) {
                    return `<div class="text-start"><span>${data.Cliente}</span></div>`;
                }

            },
            {
                data: null,
                name: 'Factura',
                orderable: true,
                targets: 4,
                render: function (data) {
                    return `<div class="text-start"><span>${data.Tipo}-${String(data.Sucursal).padStart(4, '0')}-${String(data.NroFactura).padStart(8, '0')}</span></div>`;
                }

            },
            {
                data: null,
                name: 'Prestacion',
                orderable: true,
                targets: 5,
                render: function (data) {
                    return `<div class="text-start"><span>${data.IdPrestacion}</span></div>`;
                }

            },
            {
                data: null,
                name: 'Acciones',
                className: 'text-center',
                targets: 6,
                render: function (data) {
                    let editar = '<button class="btn btn-sm iconGeneral remove-item-btn" data-id="' + data.Id + '" type="button"><i class="ri-delete-bin-5-fill" style="font-size: 2em;"></i></button>';
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


    tabla.on('click', 'button.remove-item-btn', function () {
        swal({
            title: "¿Está seguro que desea eliminar el item de la nota de crédito?",
            icon: "warning",
            buttons: ["Cancelar", "Eliminar"],
        }).then((aceptar) => {
            if (aceptar) {
                let id = $(this).data().id;
                itemsEliminar.push(id);
                row = $(this).closest('tr');
                row.hide();
            }
        });
    });

    $('#guardarCambios').on('click', function () {
        editarNotaCredito();
    });

    function editarNotaCredito() {
        let tipo = $('#tipo').val();
        let sucursal = $('#sucursal').val();
        let nroNotaCredito = $('#nroNotaCredito').val();
        let fecha = $('#fecha').val();
        let id = parseInt(idNota);
        let observaciones = $('#descripcion').val();


        if (validarCampos()) {
            preloader('on');
            $.ajax({
                url: editarNotasCreditoPost,
                type: 'POST',
                data: {
                    id: id,
                    Tipo: tipo,
                    Sucursal: sucursal,
                    NroNotaCredito: nroNotaCredito,
                    Fecha: fecha,
                    Observacion: observaciones,
                    itemsEliminar: itemsEliminar,
                    _token: TOKEN
                },
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message, '', { timeOut: 1000 });
                        setTimeout(function () {
                            history.back();
                        }, 1000);
                    } else {
                        toastr.error(response.message, '', { timeOut: 1000 });
                    }
                    preloader('off');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error("Error: ", textStatus, errorThrown);
                    preloader('off');
                }
            });
        }
    }

    function validarCampos() {
        let tipo = $('#tipo').val();
        let sucursal = $('#sucursal').val();
        let nroNotaCredito = $('#nroNotaCredito').val();
        let fecha = $('#fecha').val();
        let observaciones = $('#descripcion').val();

        let mensaje = '';
        if (!tipo) {
            mensaje += 'Debe seleccionar un tipo.\n';
        }

        if (!sucursal) {
            mensaje += 'Debe seleccionar una sucursal.\n';
        }

        if (!nroNotaCredito) {
            mensaje += 'Debe ingresar un número de nota de crédito.\n';
        }
        if (!fecha) {
            mensaje += 'Debe ingresar una fecha.\n';
        }
        if (!observaciones) {
            mensaje += 'Debe ingresar una observación.\n';
        }

        if (mensaje) {
            toastr.error(mensaje, '', { timeOut: 1000 });
            return false;
        }

        return true;
    }
}

$('#check-todos').on('change', function () {
    const check = this.checked;
    $('.fila-checkbox').prop('checked', check);
});

function eliminarMasivo() {
    var obtenerIdsSeleccionados = $(".fila-checkbox:checked").map(function () {
        return parseInt($(this).val());
    }).get();
    swal({
        title: "¿Está seguro que desea eliminar los item de la nota de crédito?",
        icon: "warning",
        buttons: ["Cancelar", "Eliminar"],
    }).then((aceptar) => {
        if (aceptar) {
           itemsEliminar = itemsEliminar.concat(obtenerIdsSeleccionados);
            obtenerIdsSeleccionados.forEach(function (id) {
                let row = $(`.fila-checkbox[value="${id}"]`).closest('tr');
                row.hide();
            });
            toastr.success("Items eliminados correctamente", '', { timeOut: 1000 });
        }
    });
}