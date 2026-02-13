$(function() {

    let tabla = "#lstBloqueoCliente";

    let informacion = {
            
        searching: false,
        ordering: false,
        processing: true,
        lengthChange: false,
        pageLength: 50,
        responsive: true,
        serverSide: true,
        deferRender: true,
        ajax: {
            url: SEARCHBLOQUEO,
            data: function (d) {
                d.tipo = $('#tipo').val();
            }
        },
        dataType: 'json',
        type: 'POST',
        columns: [
            {
                data: null,
                width: '300px',
                render: function(data){
                    return `<span>${data.empresa} ${data.bloqueado === 1 ? '<p class="badge round-pill text-bg-danger">Bloqueado</p>' : ''}</span>`;
                }
            },
            {
                data: 'cuit',
                width: '100px',
                name: 'cuit'
            },
            {
                data: 'paraEmpresa',
                width: '200px',
                name: 'paraEmpresa'
            },
            {
                data: 'alias',
                width: '200px',
                name: 'alias'
            },
            {
                data: null,
                render: function(data){
                    let eliminado = `<button type="button" class="btn btn-sm botonGeneral btnRestaurar" data-id="${data.id}">Restaurar</button>`;

                    return eliminado;
                }
            }
        ],
        language: {
            emptyTable: "No hay eliminados con los datos buscados",
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
            info: "Mostrando _START_ a _END_ de _TOTAL_ de eliminados",
        },
    };

       let dataTable = new DataTable(tabla, informacion);

       $(document).on('click', '.buscarBloqueo', function (e) {
            e.preventDefault();

            dataTable.ajax.reload(); 
       });

});