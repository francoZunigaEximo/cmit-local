$(function() {

    let Id = $('#Id').val(),
        tabla = "#lstAuditoriaCliente";

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
            url: SEARCHAUDITORIA,
            data: function (d) {
                d.usuario = $('#usuario').val();
                d.fecha = $('#fecha').val();
                d.Id = $('#Id').val();
            }
        },
        dataType: 'json',
        type: 'POST',
        columns: [
            {
                data: 'fecha',
                width: '100px',
                name: 'fecha',
            },
            {
                data: 'usuario',
                width: '100px',
                name: 'usuario'
            },
            {
                data: 'accion',
                width: '100px',
                name: 'accion'
            },
            {
                data: 'observacion',
                name: 'observacion'
            }
        ],
        language: {
            emptyTable: "No hay auditoria con los datos buscados",
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
            info: "Mostrando _START_ a _END_ de _TOTAL_ de usuarios",
        },
    };

       let dataTable = new DataTable(tabla, informacion);

       $(document).on('click', '.buscarAuditoria', function (e) {
            e.preventDefault();

            dataTable.ajax.reload(); 
       });

});