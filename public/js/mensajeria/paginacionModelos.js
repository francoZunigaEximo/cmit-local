$(document).ready(function(){

    $('#listadoModeloMsj').DataTable().clear().destroy();

    new DataTable("#listadoModeloMsj", {
        
        searching: false,
        ordering: false,
        processing: true,
        lengthChange: false,
        pageLength: 50,
        responsive: true,
        serverSide: true,
        deferRender: true,
        ajax: {
            url: SEARCHMODELO
        },
        dataType: 'json',
        type: 'POST',
        columns: [

            {
                data: 'Nombre',
                name: 'Nombre',
                width: "100px",
            },
            {
                data: null,
                width: "50px",
                render: function(data){            
                    
                    return `<button data-id="${data.Id}" class="btn btn-sm iconGeneral editar" title="Editar"><i class="ri-edit-line p-1"></i></button>
                    <button data-id="${data.Id}" class="btn btn-sm iconGeneral eliminar" title="Eliminar"><i class="ri-delete-bin-2-line p-1"></i></button>`;
                }
            }
        ],
        language: {
            processing: "<div style='text-align: center; margin-top: 20px;'><img src='../images/spinner.gif' /><p>Cargando...</p></div>",
            emptyTable: "No hay modelos con los datos buscados",
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
            info: "Mostrando _START_ a _END_ de _TOTAL_ de modelos",
        }
    });

});