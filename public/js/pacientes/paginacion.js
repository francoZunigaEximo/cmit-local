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
                            let id = data.Id;
                            return `<input type="checkbox" name="Id" value="${id}" checked>`;
                        }
                    },
                    {
                        data: null,
                        render: function(data){
                            return `<a href="${location.href}/${data.Id}/edit/"><strong>${data.NombreCompleto}</strong></a>`;
                        }
                    },
                    {
                        data: 'Documento',
                        name: 'Documento',
                    },
                    {
                        data: null,
                        render: function(data){
                            let cp = data.Cp;
                            let numero = data.Telefono;
        
                            let completo = (cp === '')? numero : '(' + cp + ') ' + numero;
        
                            return completo;
                        },
                        searching: false,
                        ordering: true,
                    },
                    {
                        data: null,
                        render: function(data){
                            let eliminar = `
                            <button data-id="${data.Id}" data-nombrecompleto="${data.NombreCompleto}" type="button" class="btn btn-sm downPaciente iconGeneral" title="Baja a paciente"><i class="ri-delete-bin-2-line"></i></button>
                            `;
        
                            return eliminar;
                        },
                    }
                ],
                language: {
                    processing: "Cargando listado de pacientes de CMIT",
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
                }

            });

        }
    });
            


});

