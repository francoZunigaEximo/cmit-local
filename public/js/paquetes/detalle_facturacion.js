$(function () {
    $('#empresaSelect2').select2({
        language: {
            noResults: function () {

                return "No hay empresas con esos datos";
            },
            searching: function () {

                return "Buscando..";
            },
            inputTooShort: function () {
                return "Por favor, ingrese 2 o más caracteres";
            }
        },
        placeholder: 'Nombre Empresa, Alias o ParaEmpresa',
        allowClear: true,
        ajax: {
            url: getClientes,
            dataType: 'json',
            data: function (params) {
                return {
                    buscar: params.term,
                    tipo: 'E'
                };
            },
            processResults: function (data) {
                return {
                    results: data.clientes
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    $('#grupoSelect2').select2({
        language: {
            noResults: function () {

                return "No hay grupos con esos datos";
            },
            searching: function () {

                return "Buscando..";
            },
            inputTooShort: function () {
                return "Por favor, ingrese 2 o más caracteres";
            }
        },
        placeholder: 'Nombre grupo clientes',
        allowClear: true,
        ajax: {
            url: getGrupos,
            dataType: 'json',
            data: function (params) {
                return {
                    buscar: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data.grupo
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    $('#examenSelect2').select2({
        language: {
            noResults: function () {

                return "No hay especialidades con esos datos";
            },
            searching: function () {

                return "Buscando..";
            },
            inputTooShort: function () {
                return "Por favor, ingrese 2 o más caracteres";
            }
        },
        placeholder: 'Nombre Especialidad',
        allowClear: true,
        ajax: {
            url: getExamenes,
            dataType: 'json',
            data: function (params) {
                return {
                    buscar: params.term,
                    tipo: 'E'
                };
            },
            processResults: function (data) {
                return {
                    results: data.examen
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    $('#paqueteSelect2').select2({
        language: {
            noResults: function () {

                return "No hay especialidades con esos datos";
            },
            searching: function () {

                return "Buscando..";
            },
            inputTooShort: function () {
                return "Por favor, ingrese 2 o más caracteres";
            }
        },
        placeholder: 'Nombre Paquete Facturacion',
        allowClear: true,
        ajax: {
            url: getPaquetesFact,
            dataType: 'json',
            data: function (params) {
                return {
                    buscar: params.term,
                    tipo: 'E'
                };
            },
            processResults: function (data) {
                return {
                    results: data.paquete
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    $('.buscarDetallesEstudios').on('click', function(e) {
        e.preventDefault();
        
        paquete = $('#paqueteSelect2').val();
        examen = $('#examenSelect2').val();
        empresa = $('#empresaSelect2').val();
        grupo = $('#grupoSelect2').val();

        $('#listaPaquetesEstudiosDetalle').DataTable().clear().destroy();

        new DataTable("#listaPaquetesEstudiosDetalle", {
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
                url: searchDetalleFacturacion,
                data: function(e) {
                    e.examen = examen;
                    e.paquete = paquete;
                    e.empresa = empresa;
                    e.grupo = grupo;
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
                        return `<div class="text-start"><span>${data.Nombre}</span></div>`;
                    }

                },
                {
                    data: null,
                    name: 'Especialidad',
                    orderable: true,
                    targets: 2,
                    render: function(data){
                        return `<div class="text-start"><span>${data.Especialidad == null ? "" : data.Especialidad}</span></div>`;
                    }

                },
                {
                    data: null,
                    name: 'NombreExamen',
                    targets: 3,
                    render: function(data){
                        return `<div class="text-start"><span>${data.NombreExamen == null ? "" : data.NombreExamen}</span></div>`;
                    }
                },
                {
                    data: null,
                    name: 'Empresa',
                    targets: 4,
                    render: function(data){
                        return `<div class="text-start"><span>${data.Empresa == null ? "" : data.Empresa}</span></div>`;
                    }
                },
                {
                    data: null,
                    name: 'Grupo',
                    targets: 5,
                    render: function(data){
                        return `<div class="text-start"><span>${data.Grupo == null ? "" : data.Grupo}</span></div>`;
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

    $(".btnExcelEstudios").on("click", function (e) {
        preloader('on');
        paquete = $('#paqueteSelect2').val();
        examen = $('#examenSelect2').val();
        empresa = $('#empresaSelect2').val();
        grupo = $('#grupoSelect2').val();
        
        $.get(exportarExcel,
            {
                examen: examen,
                paquete: paquete,
                empresa: empresa,
                grupo: grupo
            })
            .done(function (response) {
                preloader('off');
                createFile("excel", response.filePath, generarCodigoAleatorio() + "_paquetes_estudios");
                preloader('off');
                toastr.success(response.msg);
                return;
            })
            .fail(function (jqXHR) {
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);
                checkError(jqXHR.status, errorData.msg);
                return;
            });
    });

})