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

    $('#paqueteFacturacionSelect2').select2({
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
        placeholder: 'Nombre paquete facturacion',
        allowClear: true,
        ajax: {
            url: getPaqueteFact,
            dataType: 'json',
            data: function (params) {
                return {
                    buscar: params.term
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

    $(".btnExcelEstudios").on("click", function (e) {
        preloader('on');
        buscar = $('#nombrepaquete').val();
        alias = $('#aliaspaquete').val();
        id = $('#codigopaquete').val();
        $.get(exportarExcel,
            {
                buscar: buscar,
                id: id,
                alias: alias
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

function eliminarPaqueteEstudio(id) {
    swal({
        title: "¿Está seguro que desea eliminar el paquete de estudios?",
        icon: "warning",
        buttons: ["Cancelar", "Eliminar"],
    }).then((aceptar) => {
        if (aceptar) {
            preloader('on');
            $.post(eliminarPaqueteEstudioRoute,{
                _token: TOKEN,
                id: id
            })
            .done(function(){
                preloader('off');
                $('.buscarPaquetesExamenes').trigger("click");

            }).fail(function (jqXHR) {
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);
                checkError(jqXHR.status, errorData.msg);
                return;
            });
        };
    });
}

function eliminarPaqueteFacturacion(id) {
    swal({
        title: "¿Está seguro que desea eliminar el paquete facturacion?",
        icon: "warning",
        buttons: ["Cancelar", "Eliminar"],
    }).then((aceptar) => {
        if (aceptar) {
            preloader('on');
            $.post(eliminarPaqueteFacturacionRoute,{
                _token: TOKEN,
                id: id
            })
            .done(function(){
                preloader('off');
                $('.buscarPaquetesFacturacion').trigger("click");

            }).fail(function (jqXHR) {
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);
                checkError(jqXHR.status, errorData.msg);
                return;
            });
        };
    });
}