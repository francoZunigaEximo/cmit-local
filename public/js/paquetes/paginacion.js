$(document).ready(()=>{

    $('.buscarPaquetesExamenes').on('click', function(e) {
        e.preventDefault();
        
        buscar = $('#paqueteEstudioSelect2').val();
        //alias = $('#aliaspaquete').val();
        id = $('#codigopaquete').val();

        $('#listaPaquetesExamenes').DataTable().clear().destroy();

        new DataTable("#listaPaquetesExamenes", {
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
                url: SEARCH_EXAMENES,
                data: function(e) {
                    e.buscar = buscar;
                    e.id = id;
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
                    name: 'Examenes',
                    orderable: true,
                    targets: 1,
                    render: function(data){
                        return `<div class="text-start"><span>${data.CantidadExamenes}</span></div>`;
                    }
                },
                {
                    data: null,
                    name: 'Nombre',
                    orderable: true,
                    targets: 2,
                    render: function(data){
                        return `<div class="text-start"><span>${data.Nombre}</span></div>`;
                    }

                },
                {
                    data: null,
                    name: 'Alias',
                    orderable: true,
                    targets: 3,
                    render: function(data){
                        return `<div class="text-start"><span>${data.Alias == null ? "" : data.Alias}</span></div>`;
                    }

                },
                {
                    data: null,
                    name: 'Acciones',
                    targets: 4,
                    render: function(data){
                        
                        let editar = '<a title="Editar" href="'+ location.href + '/editPaqueteExamen/'+data.Id +'">' + '<button type="button" class="btn btn-sm iconGeneral edit-item-btn"> <i class="ri-edit-line"></i> </button>' + '</a>';
                        editar += '<button class="btn btn-sm iconGeneral edit-item-btn" onclick="eliminarPaqueteEstudio('+data.Id+')" type="button"><i class="ri-delete-bin-2-line"></i></button>'
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

    $('.buscarPaquetesFacturacion').on('click', function(e) {
        e.preventDefault();
        
        let codigo = $("#codigoPaquete").val();
        let idPaquete = $("#paqueteFacturacionSelect2").val();
        let idGrupo = $("#grupoSelect2").val();
        let idEmpresa = $("#empresaSelect2").val();

        $('#listaPaquetesFacturacion').DataTable().clear().destroy();

        new DataTable("#listaPaquetesFacturacion", {
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
                url: search_paquetes_studio,
                data: function(e) {
                    e.IdPaquete = idPaquete;
                    e.Codigo = codigo;
                    e.IdGrupo = idGrupo;
                    e.IdEmpresa = idEmpresa;
                }
            },
            dataType: 'json',
            type: 'POST',
            columnDefs: [
                {
                    data: null,
                    name: 'Nombre',
                    orderable: true,
                    targets: 0,
                    render: function(data){

                        return `<div class="text-center">${data.Nombre}</div>`;
                    }

                },
                {
                    data: null,
                    name: 'CantExamenes',
                    orderable: true,
                    targets: 1,
                    render: function(data){
                        return `<div class="text-start"><span>${data.CantExamenes}</span></div>`;
                    }

                },
                {
                    data: null,
                    name: 'NombreEmpresa',
                    orderable: true,
                    targets: 2,
                    render: function(data){
                        return `<div class="text-start"><span>${data.NombreEmpresa == null ? "" : data.NombreEmpresa}</span></div>`;
                    }

                },
                {
                    data: null,
                    name: 'NombreGrupo',
                    orderable: true,
                    targets: 3,
                    render: function(data){
                        return `<div class="text-start"><span>${data.NombreGrupo == null ? "" : data.NombreGrupo}</span></div>`;
                    }

                },
                {
                    data: null,
                    name: 'Codigo',
                    orderable: true,
                    targets: 4,
                    render: function(data){
                        return `<div class="text-start"><span>${data.Codigo == null ? "" : data.Codigo}</span></div>`;
                    }

                },
                {
                    data: null,
                    name: 'Acciones',
                    targets: 5,
                    render: function(data){
                        
                        let editar = '<a title="Editar" href="'+ location.href + '/editPaqueteFacturacion/'+data.Id +'">' + '<button type="button" class="btn btn-sm iconGeneral edit-item-btn"> <i class="ri-edit-line"></i> </button>' + '</a>';
                        editar += '<button class="btn btn-sm iconGeneral edit-item-btn" onclick="eliminarPaqueteFacturacion('+data.Id+')" type="button"><i class="ri-delete-bin-2-line"></i></button>'
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