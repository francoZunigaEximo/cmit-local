$(function () {
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

                    let editar = '<a title="Items Anulados" href="' + location.href +'/itemsanulados/' + data.Id + '">' + '<button type="button" class="btn btn-sm iconGeneral edit-item-btn"> <i class="ri-edit-line"></i> </button>' + '</a>';
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
})