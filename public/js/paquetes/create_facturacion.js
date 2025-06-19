let examenes = [];
let estudiosRenderizar = [];

let tabla = new DataTable("#listaExamenesPaquetes",{
    searching: false,
    lengthChange: false,
});
const params = new URLSearchParams(window.location.search);

$(function () {
    if (params.get("id")) {
        preloader('on');

        $.get(getPaquete, {
            id: params.get("id")
        })
            .done(function (result) {
                console.log(result);
                $("#nombre").val(result.Paquete.Nombre);
                $("#descripcion").val(result.Paquete.Descripcion);
                $("#alias").val(result.Paquete.Alias);
                $("#codigo").val(result.Paquete.Cod);

                $("#grupoSelect2").val();
                $("#empresaSelect2").val();

                cargarGrupo(result.Paquete.IdGrupo);
                cargarEmpresa(result.Paquete.IdEmpresa);

                cargarExamenes(params.get("id"));

                preloader('off');

            })
            .fail(function (jqXHR) {
                let errorData = JSON.parse(jqXHR.responseText);
                checkError(jqXHR.status, errorData.msg);
                return;
            });
    }

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

                return "No hay estudio con esos datos";
            },
            searching: function () {

                return "Buscando..";
            },
            inputTooShort: function () {
                return "Por favor, ingrese 2 o más caracteres";
            }
        },
        placeholder: 'Nombre Estudio',
        allowClear: true,
        ajax: {
            url: getExamenes,
            dataType: 'json',
            data: function (params) {
                return {
                    buscar: params.term
                }
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    $('#paqueteSelect2').select2({
        language: {
            noResults: function () {

                return "No hay paquetes de estudios  con esos datos";
            },
            searching: function () {

                return "Buscando..";
            },
            inputTooShort: function () {
                return "Por favor, ingrese 2 o más caracteres";
            }
        },
        placeholder: 'Nombre paquete estudio',
        allowClear: true,
        ajax: {
            url: getPaquetes,
            dataType: 'json',
            data: function (params) {
                return {
                    buscar: params.term
                }
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

    $('.agregarExamen').on('click', function (e) {
        e.preventDefault();
        let id = $("#examenSelect2").val();

        cargarExamen(id);
        renderizarEstudios(estudiosRenderizar);
        examenes = examenes.concat(estudiosRenderizar);
        estudiosRenderizar = [];
    })

    tabla.on('click', 'button.remove-item-btn', function () {
        let id = $(this).data().id;
        index = examenes.indexOf(examenes.find(x => x.Id == id));
        examenes.splice(index, 1);
        tabla
            .row($(this).parents('tr'))
            .remove()
            .draw();
    });

    $('.agregarPaquete').on('click', function (e) {
        e.preventDefault();
        //buscamos los examenes del paquete seleccionado
        let id = $("#paqueteSelect2").val();
        preloader('on');

        $.ajax({
            url: paqueteId,
            type: 'POST',
            data: {
                _token: TOKEN,
                IdPaquete: id,
            },

            success: function (response) {
                let data = response.examenes;
                data.forEach(examen => {
                    cargarExamen(examen.Id);
                });
                examenes = examenes.concat(estudiosRenderizar);
                renderizarEstudios(estudiosRenderizar);
                estudiosRenderizar = [];
                preloader('off');
            },
            error: function (jqXHR) {
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);
                checkError(jqXHR.status, errorData.msg);
                return;
            }
        });
    });
});

function cargarExamenes(id) {
    $.ajax({
        url: getExamenesByIdPaquete,
        type: 'get',
        data: {
            _token: TOKEN,
            id: id,
        },

        success: function (response) {

            examenes = response;
            estudiosRenderizar = examenes;
            renderizarEstudios(estudiosRenderizar);
            estudiosRenderizar = [];

            preloader('off');
        },
        error: function (jqXHR) {
            preloader('off');
            let errorData = JSON.parse(jqXHR.responseText);
            checkError(jqXHR.status, errorData.msg);
            return;
        }
    });
}

function cargarGrupo(grupo) {
    if (grupo != '0') {
        $.ajax({
            url: getGrupo,
            type: 'GET',
            data: {
                _token: TOKEN,
                id: grupo,
            },

            success: function (response) {
                console.log(response);
                let newOption = new Option(response.Nombre, response.Id, true, true);
                $('#grupoSelect2').append(newOption).trigger('change');
                preloader('off');
            },
            error: function (jqXHR) {
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);
                checkError(jqXHR.status, errorData.msg);
                return;
            }
        });
    }
}

function cargarEmpresa(empresa) {
    if (empresa != '0') {
        $.ajax({
            url: getCliente,
            type: 'GET',
            data: {
                _token: TOKEN,
                id: empresa,
            },

            success: function (response) {
                console.log(response);
                let newOption = new Option(response.ParaEmpresa, response.Id, true, true);
                $('#empresaSelect2').append(newOption).trigger('change');
                preloader('off');
            },
            error: function (jqXHR) {
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);
                checkError(jqXHR.status, errorData.msg);
                return;
            }
        });
    }
}

function cargarExamen(idExamen) {
    preloader('on');
    $.ajax({
        url: getExamenId,
        method: 'GET',
        async: false,
        data: { Id: idExamen }
    })
        .done(function (result) {
            //preloader('off');
            //toastr.success('Se ha realizado la acción correctamente', '', {timeOut: 1000});
            console.log(result);
            if (!examenes.find(x => x.Id == result.Id)) {
                estudiosRenderizar.push(result);
            } else {
                toastr.warning('No puede cargar un examen ya cargado.', '', { timeOut: 1000 });
            }
            preloader('off');
        })
        .fail(function (jqXHR) {
            preloader('off');
            let errorData = JSON.parse(jqXHR.responseText);
            checkError(jqXHR.status, errorData.msg);
            return;
        });
}

function renderizarEstudios(estudios) {
    preloader('on');
    estudios.forEach(x => {
        tabla.row.add([
            x.Cod,
            x.Nombre,
            x.Descripcion,
            "<button class=\"btn btn-sm iconGeneral remove-item-btn\" type=\"button\" data-id=\"" + x.Id + "\" ><i class=\"ri-delete-bin-2-line\"></i></button>"
        ]).draw();
    });
    preloader('off');
}

$('#btnRegistrar').on('click', function (e) {
    e.preventDefault();
    preloader('on');
    //registramos el paquete de facturacion
    let nombre = $("#nombre").val();
    let descricpion = $("#descripcion").val();
    let alias = $("#alias").val();
    let codigo = $("#codigo").val();
    let idGrupo = $("#grupoSelect2").val();
    let idEmpresa = $("#empresaSelect2").val();

    if ( validaciones()) {
        if (!(idGrupo && idEmpresa)) {
            $.ajax({
                url: postPaqueteFacturacionCreate,
                type: 'POST',
                data: {
                    _token: TOKEN,
                    Nombre: nombre,
                    Descripcion: descricpion,
                    Alias: alias,
                    Codigo: codigo,
                    Examenes: examenes,
                    IdGrupo: idGrupo,
                    IdEmpresa: idEmpresa
                },
                success: function (response) {
                    preloader('off');
                    toastr.success('Se ha cargado al paquete correctamente', '', { timeOut: 1000 });
                },
                error: function (jqXHR) {
                    preloader('off');
                    let errorData = JSON.parse(jqXHR.responseText);
                    checkError(jqXHR.status, errorData.msg);
                    return;
                }
            });
        }
    }
});


function validaciones(){
    let mensaje = "";
    if (!$("#nombre").val()) {
        mensaje += "Debe ingresar un nombre para el paquete.\n";
    }
    if (!$("#alias").val()) {
        mensaje += "Debe ingresar un alias para el paquete.\n";
    }
    if (!$("#codigo").val()) {
        mensaje += "Debe ingresar un código para el paquete.\n";
    }
    if (!$("#grupoSelect2").val() && !$("#empresaSelect2").val()) {
        mensaje += "Debe seleccionar un grupo o una empresa.\n";
    }
    if (examenes.length === 0) {
        mensaje += "Debe agregar al menos un examen al paquete.\n";
    }
    if (mensaje) {
        preloader('off');
        toastr.warning(mensaje, '', { timeOut: 3000 });
        return false;
    }
    return true;
}