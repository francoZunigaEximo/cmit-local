$(document).ready(()=>{

    $('.buscarPaquetesExamenes').on('click', function(e) {
        e.preventDefault();
        buscar = $('#nombrepaquete').val();
        $('#listaPaquetesExamenes').DataTable().clear().destroy();

        new DataTable("#listaPaquetesExamenes", {
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
                url: SEARCH_EXAMENES,
                data: function(e) {
                    e.buscar = buscar
                }
            },
            dataType: 'json',
            type: 'POST',
            columnDefs: [
                {
                    data: null,
                    name: 'Id',
                    orderable: true,
                    targets: 0,
                    render: function(data){

                        return `<div class="text-center">${data.Id}</div>`;
                    }

                },
                {
                    data: null,
                    name: 'Nombre',
                    orderable: true,
                    targets: 1,
                    render: function(data){
                        return `<div class="text-center"><span>${data.Nombre}</span></div>`;
                    }

                },
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
            stateLoadCallback: function(settings, callback) {
                $.ajax({
                    url: SEARCH,
                    dataType: 'json',
                    success: function(json) {

                        // Pasar el objeto json a callback
                        callback(json);
                    }
                });
            },
            stateSaveCallback: function(settings, data) {
                $.ajax({
                    url: SEARCH,
                    type: 'POST',
                    data: {
                        
                    },
                    dataType: "json",
                    success: function(response) {},
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("Error: ", textStatus, errorThrown);
                    }
                });
            },
        });
    });

})