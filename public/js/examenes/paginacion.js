$(document).ready(()=>{

    $(document).on('click', '#buscar', function() {

        $('#listaExamenes').DataTable().clear().destroy();

        new DataTable("#listaExamenes", {
            
            searching: false,
            ordering: false,
            processing: true,
            lengthChange: false,
            pageLength: 50,
            responsive: true,
            serverSide: true,
            ajax: {
                url: SEARCH,
                data: function(d){
                    d.examen = $('#examen').val();
                    d.especialidad = $('#especialidad').val();
                    d.atributos = $('#atributos').val();
                    d.estado = $('#estado').val();
                    d.opciones = $('#opciones').val();
                    d.codigoex = $('#codigoex').val();
                    d.activo = $('#activo').val();
                }
            },
            dataType: 'json',
            type: 'POST',
            columns: [
                {
                    data: 'Estudio',
                    name: 'Estudio',
                },
                {
                    data: null,
                    render: function(data){
                        return `<span title="${data.NombreExamen}">${(data.NombreExamen).length <= 30 ? data.NombreExamen : (data.NombreExamen).substring(0,12) + "..."}</span>`;
                    }
                },
                {
                    data: 'ProvEfector',
                    name: 'ProvEfector',
                },
                {
                    data: 'ProvInformador',
                    name: 'ProvInformador',
                },
                {
                    data: 'Vto',
                    name: 'Vto',
                },
                {
                    data: 'CodigoExamen',
                    name: 'CodigoExamen',
                },
                {
                    data: 'CodigoEfector',
                    name: 'CodigoEfector',
                },
                {
                    data: null,
                    render: function(data){
                        return `<span title="${data.NombreReporte}">${(data.NombreReporte).length <= 30 ? data.NombreReporte : (data.NombreReporte).substring(0,12) + "..."}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        
                        let editar = '<a title="Editar" href="'+ location.href + '/' + data.IdExamen + '/edit">' + '<button type="button" class="btn btn-sm iconGeneral edit-item-btn"><i class="ri-edit-line"></i></button>' + '</a>';
    
                        return editar;
                    }
                }
            ],
            language: {
                processing: "Cargando listado de examenes de CMIT",
                emptyTable: "No hay mapas con los datos buscados",
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
                info: "Mostrando _START_ a _END_ de _TOTAL_ de examenes",
            },
            createdRow: function (row, data, dataIndex) {

                console.log(data);
                if (data && data.prioridadImpresion === 1) {
                    $(row).addClass('fondo-celeste');
                }
            
            }
        });

    });


});