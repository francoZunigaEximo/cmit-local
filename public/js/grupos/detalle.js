$(document).ready(() => {
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

    $('.buscarGrupos').on('click', function (e) {
        e.preventDefault();

        $('#listaGrupoClientes').DataTable().clear().destroy();
        let idGrupo = $("#grupoSelect2").val();
        let idCliente = $("#empresaSelect2").val();
        let nroCliente = $("#nrocliente").val();
        
        new DataTable("#listaGrupoClientes", {
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
                url: search,
                data: function (e) {
                    e.NroCliente = nroCliente;
                    e.IdCliente = idCliente;
                    e.IdGrupo = idGrupo;
                }
            },
            dataType: 'json',
            type: 'POST',
            columnDefs: [
                {
                    data: null,
                    name: 'NombreGrupo',
                    orderable: true,
                    targets: 0,
                    render: function (data) {

                        return `<div class="text-center">${data.NombreGrupo}</div>`;
                    }

                },
                {
                    data: null,
                    name: 'NroCliente',
                    orderable: true,
                    targets: 1,
                    render: function (data) {
                        return `<div class="text-start"><span>${data.NroCliente}</span></div>`;
                    }

                },
                {
                    data: null,
                    name: 'RazonSocial',
                    targets: 2,
                    render: function (data) {
                        return `<div class="text-start"><span>${data.RazonSocial}</span></div>`;
                    }
                },
                {
                    data: null,
                    name: 'ParaEmpresa',
                    targets: 3,
                    render: function (data) {
                        return `<div class="text-start"><span>${data.ParaEmpresa}</span></div>`;
                    }
                },
                {
                    data: null,
                    name: 'CUIT',
                    targets: 4,
                    render: function (data) {
                        return `<div class="text-start"><span>${data.CUIT}</span></div>`;
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
            stateLoadCallback: function (settings, callback) {
                $.ajax({
                    url: SEARCH,
                    dataType: 'json',
                    success: function (json) {

                        // Pasar el objeto json a callback
                        callback(json);
                    }
                });
            },
            stateSaveCallback: function (settings, data) {
                $.ajax({
                    url: SEARCH,
                    type: 'POST',
                    data: {

                    },
                    dataType: "json",
                    success: function (response) { },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.error("Error: ", textStatus, errorThrown);
                    }
                });
            },
        });
    });

    $(".btnExcel").on("click", function (e) {
        preloader('on');
        //buscar = $('#nombregrupo').val();
        let idGrupo = $("#grupoSelect2").val();
        let idCliente = $("#empresaSelect2").val();
        let nroCliente = $("#nrocliente").val();

        $.get(exportarExcel,
            {
                NroCliente: nroCliente,
                IdCliente: idCliente,
                IdGrupo: idGrupo
            })
            .done(function (response) {
                preloader('off');
                createFile("excel", response.filePath, generarCodigoAleatorio() + "_grupo_clientes");
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