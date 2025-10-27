$(document).ready(() => {
    $('#Profesional').select2({
        language: {
            noResults: function() {

            return "No hay profesionales con ese nombre o apellido";        
            },
            searching: function() {

            return "Buscando..";
            },
            inputTooShort: function () {
                return "Por favor, ingrese 2 o más caracteres";
            }
        },
        placeholder: 'Rol de usuario',
        allowClear: true,
        ajax: {
            url: buscarProfesional, 
            dataType: 'json',
            data: function(params) {
                return {
                    buscar: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: data.profesionales 
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });
})

$("#buscarEfectores").on('click', function () {
    cargarTablaEfectores();
});

$("#buscarFacturas").on('click', function () {
    cargarTablaFacturas();
});

function cargarTablaEfectores() {

    var fechaDesde = $('#fechaDesdeEfector').val();
    var fechaHasta = $('#fechaHastaEfector').val();
    var tipo = $('#tipo').val();
    if (fechaDesde === "" && fechaHasta === "") {
        toastr.error('Por favor, complete los campos de fecha.', '', { timeOut: 1000 });
        return;
    } else {


        $('#listadoEfectores').DataTable().clear().destroy();

        const tabla = new DataTable("#listadoEfectores", {
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
                url: buscarEfectoresUrl,
                data: function (e) {
                    e.fechaDesde = fechaDesde;
                    e.fechaHasta = fechaHasta;
                    e.tipo = tipo;
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
                    name: 'Profesional',
                    orderable: true,
                    targets: 1,
                    render: function (data) {
                        return `<div class="text-start"><span>${data.Apellido == null ? "" : data.Apellido}, ${data.Nombre == null ? "" : data.Nombre}</span></div>`;
                    }

                },
                {
                    data: null,
                    name: 'Pago',
                    targets: 2,
                    render: function (data) {
                        return `<div class="text-start"><span>${data.Pago == null ? "" : data.Pago}</span></div>`;
                    }
                },
                {
                    data: null,
                    name: 'Cantidad',
                    targets: 3,
                    render: function (data) {
                        return `<div class="text-start"><span>${data.Cantidad == null ? "" : data.Cantidad}</span></div>`;
                    }
                },
                {
                    data: null,
                    name: 'Acciones',
                    targets: 4,
                    render: function (data) {
                        let ruta = rutaCrearFacturaCompra.replace('ID_PROFESIONAL', data.Id);
                        let editar = `<a title="Items Anulados" href="${ruta}"><button type="button" class="btn btn-sm iconGeneral edit-item-btn"> <i class="ri-edit-line"></i> </button></a>`;
                        return editar;
                    }
                }
            ],
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

}

function cargarTablaFacturas() {

    var fechaDesde = $('#fechaDesde').val();
    var fechaHasta = $('#fechaHasta').val();
    var idProfesional = $('#Profesional').val();
    var nroDesde = $('#nroDesde').val();
    var nroHasta = $('#nroHasta').val();

    if (fechaDesde === "" && fechaHasta === "") {
        toastr.error('Por favor, complete los campos de fecha.', '', { timeOut: 1000 });
        return;
    } else {


        $('#listadoFacturas').DataTable().clear().destroy();

        const tabla = new DataTable("#listadoFacturas", {
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
                url: buscarFacturas,
                data: function (e) {
                    e.fechaDesde = fechaDesde;
                    e.fechaHasta = fechaHasta;
                    e.idProfesional = idProfesional,
                        e.nroDesde = nroDesde;
                    e.nroHasta = nroHasta;
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
                    name: 'Numero',
                    orderable: true,
                    targets: 1,
                    render: function (data) {
                        var nroFactura = data.Tipo + '-' + data.Sucursal.toString().padStart(5, '0') + '-' + data.NroFactura.toString().padStart(8, '0');
                        return `<div class="text-start"><span>${nroFactura == null ? "" : nroFactura}</span></div>`;
                    }

                },
                {
                    data: null,
                    name: 'Fecha',
                    orderable: true,
                    targets: 2,
                    render: function (data) {
                        var fecha = new Date(data.Fecha);
                        return `<div class="text-start"><span>${fecha.toLocaleDateString() == null ? "" : fecha.toLocaleDateString()}</span></div>`;
                    }

                },
                {
                    data: null,
                    name: 'Factura',
                    orderable: true,
                    targets: 3,
                    render: function (data) {
                        return `<div class="text-start"><span>${data.NroFactura == null ? "" : data.NroFactura}</span></div>`;
                    }

                },
                {
                    data: null,
                    name: 'Especialidad',
                    targets: 4,
                    render: function (data) {
                        return `<div class="text-start"><span>${data.Especialidad == null ? "" : data.Especialidad}</span></div>`;
                    }
                },
                {
                    data: null,
                    name: 'Profesional',
                    targets: 5,
                    render: function (data) {
                        return `<div class="text-start"><span>${data.Profesional == null ? "" : data.Profesional}</span></div>`;
                    }
                },
                {
                    data: null,
                    name: 'Acciones',
                    targets: 6,
                    render: function (data) {
                        let ruta = rutaEditarFacturaCompra.replace('ID_FACTURA', data.Id);
                        let eliminar = `<button type="button" class="btn btn-sm iconGeneral delete-item-btn" onclick="eliminarFacturaCompraConfirmacion(${data.Id})"> <i class="ri-delete-bin-2-line"></i> </button>`;

                        let editar = `<a title="Items Anulados" href="${ruta}"><button type="button" class="btn btn-sm iconGeneral edit-item-btn"> <i class="ri-edit-line"></i> </button></a> ${eliminar}`;
                        return editar;
                    }
                }
            ],
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

}
function eliminarFacturaCompraConfirmacion(id) {
    swal({
        title: "¿Está seguro que desea eliminar la factura?",
        icon: "warning",
        buttons: ["Cancelar", "Eliminar"],
    }).then((aceptar) => {
        if (aceptar) {
            eliminarFacturaCompra(id);
        }
    });
}

function eliminarFacturaCompra(id) {
    $.ajax({
        url: rutaEliminarFacturaCompra,
        type: 'POST',
        data: {
            idFactura: id,
            _token: TOKEN
        },
        success: function (response) {
            if (response.success) {
                // Actualizar la tabla o realizar alguna acción
                toastr.success('Factura de compra eliminada correctamente.', '', { timeOut: 2000 });
                cargarTablaFacturas();
            } else {
                // Mostrar mensaje de error
                toastr.error(response.message, '', { timeOut: 2000 });
            }
        },
        error: function (xhr, status, error) {
            // Manejar errores
            toastr.error('Error al eliminar la factura de compra.', '', { timeOut: 2000 });
        }
    });
}

$("#btnreporte").on("click", function (e) {
    preloader('on');
    var fechaDesde = $('#fechaDesde').val();
    var fechaHasta = $('#fechaHasta').val();
    var idProfesional = $('#Profesional').val();
    var nroDesde = $('#nroDesde').val();
    var nroHasta = $('#nroHasta').val();
    
    $.get(imprimirReporte,
        {
            fechaDesde: fechaDesde,
            fechaHasta: fechaHasta,
            idProfesional: idProfesional,
            nroDesde: nroDesde,
            nroHasta: nroHasta,
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