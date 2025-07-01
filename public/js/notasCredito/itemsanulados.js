$(document).ready(() => {
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
                    return `<div class="text-start"><span>${data.NroFactura}</span></div>`;
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
                    let editar = '<button class="btn btn-sm iconGeneral edit-item-btn" onclick="restaurarExamen(' + data.Id + ')" type="button"><i class="ri-arrow-up-circle-fill" style="font-size: 2em;"></i></button>';
                    editar += `<button class="btn btn-sm iconGeneral edit-item-btn" onclick="crearNotaCreadito('${data.Id}')" type="button"><i class="ri-file-text-fill" style="font-size: 2em;"></i></button>`;
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
                return `
                <tr class="grupo">
                    <td><input type="checkbox" class="grupo-checkbox" data-grupo="${group}" /></td>
                    <td colspan="3"><strong>Factura ${group}</strong></td>
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

    $('#check-todos').on('change', function () {
        const check = this.checked;
        $('.fila-checkbox').prop('checked', check);
        $('.grupo-checkbox').prop('checked', check);
    });


});
