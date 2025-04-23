$(function(){

    $(document).on('click', '.btnBuscar', function(e){
        e.preventDefault();

        let buscar = $('#buscar').val();

        if (buscar === '') {
            toastr.warning('Debe escribir un nombre, apellido o dni','Atención',{timeOut: 1000});
            return;
        }

        $('#listaPac').DataTable().clear().destroy();

        new DataTable('#listaPac', {
            lengthMenu: [
                [25, 50, 100, 500,],
                [25, 50, 100, 500,]
            ],
            pageLength: 500,
            searching: false,
            ordering: false,
            processing: true,
            lengthChange: true,
            deferRender: true,
            responsive: false,
            serverSide: true,
            stateSave: false,
            dataType: 'json',
            type: 'POST',
            ajax: {
                url: ROUTE,
                data: function(d){
                    d.buscar = buscar;
                }
            },     
            columns: [
                {
                    data: null,
                    render: function(data){
                        return `<div class="text-center"><input type="checkbox" name="Id" value="${data.Id}" checked></div>`;
                    }
                },
                {
                    data: 'Id',
                    name: 'Id'
                },
                {
                    data: null,
                    render: function(data){
                        return `<strong>${(data.Apellido).toUpperCase()} ${(data.Nombre).toUpperCase()}</strong>`;
                    }
                },
                {
                    data: 'Documento',
                    name: 'Documento',
                },
                {
                    data: null,
                    render: function(data){
                        return (data.Cp === '') ? data.Telefono : '(' + data.Cp + ') ' + data.Telefono;
                    },
                },
                {
                    data: null,
                    render: function(data){

                        let editar = `<a href="${location.href}/${data.Id}/edit/"><button type="button" class="btn btn-sm iconGeneral edit-item-btn"><i class="ri-search-eye-line"></i></button></a>`;
                        let eliminar = `
                        <button data-id="${data.Id}" data-nombrecompleto="${data.NombreCompleto}" type="button" class="btn btn-sm downPaciente iconGeneral" title="Baja a paciente"><i class="ri-delete-bin-2-line"></i></button>
                        `;
    
                        return editar + ' ' + eliminar;
                    },
                }
            ],
            language: {
                processing: "<div style='text-align: center; margin-top: 20px;'><img src='./images/spinner.gif' /><p>Cargando...</p></div>",
                emptyTable: "No hay pacientes con los datos buscados",
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
                info: "Mostrando _START_ a _END_ de _TOTAL_ de pacientes",
            },
            
            initComplete: function(settings, json) {
                $("#listaPac").show();
                $(".dataTables_processing").hide();
            },
            xhr: function(settings, json, xhr) {
                $("#listaPac").hide();
                $(".dataTables_processing").show();
            },
            drawCallback: function(settings) {
                $("#listaPac").show();
                $(".dataTables_processing").hide();
            },
        });
    });
            

});

