$(document).ready(()=>{

    $('#buscar').on('keypress', function(){

        if (event.keyCode === 13){

            $('#listaPac').DataTable().clear().destroy();
            
            new DataTable("#listaPac", {

                searching: false,
                ordering: false,
                processing: true,
                lengthChange: false,
                pageLength: 50,
                deferRender: true,
                responsive: true,
                serverSide: true,
                dataType: 'json',
                type: 'POST',
                ajax: {
                    url: SEARCH,
                    data: function(d){
                        d.buscar = $('#buscar').val();
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
                        searching: false,
                        ordering: true,
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
                }
            });

        }
    });
            


});

