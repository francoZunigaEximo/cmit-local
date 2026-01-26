let tabla = new DataTable("#listaExamenesPaquetes", {
    searching: false,
    lengthChange: false,
});

let examenes = [];
let examenesNuevos = [];
let examenesEliminar = [];

let estudiosRenderizar = [];

$(document).ready(() => {
    cargarEmpresa();
    cargarGrupo();

    let id = $("#idPaquete").val();
    preloader('on');

    $.ajax({
        url: getPaqueteFacturacionId,
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

    //verificaciones extras
    $('#grupoSelect2').on('change', verificarSelectGrupo);
    $('#empresaSelect2').on('change', verificarSelectEmpresa);


    // Función para verificar el valor
    function verificarSelectGrupo() {
        const valor = $('#grupoSelect2').val();
        if (valor === null || valor === '') {
            $('#empresaSelect2').prop("disabled", false);
        } else {
            $('#empresaSelect2').prop("disabled", true);
        }
    }

    function verificarSelectEmpresa() {
        const valor = $('#empresaSelect2').val();
        if (valor === null || valor === '') {
            $('#grupoSelect2').prop("disabled", false);
        } else {
            $('#grupoSelect2').prop('disabled', true);
        }
    }

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
})

function cargarGrupo() {
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

function cargarEmpresa() {
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

function cargarEstudio(idExamen) {
    $.ajax({
        url: getExamenId,
        method: 'GET',
        async: false,
        data: { Id: idExamen }
    })
        .done(function (result) {
            let enExamenes = examenes.find(x => x.Id == result.Id);
            let enExamenesNuevos = examenesNuevos.find(x => x.Id == result.Id);
            let enEliminado = examenesEliminar.indexOf(examenesEliminar.find(x => x == result.Id));

            if (!enExamenesNuevos && !enExamenes) {
                estudiosRenderizar.push(result);
            } else {
                if (enEliminado >= 0 && enExamenes) {
                    estudiosRenderizar.push(result);
                    renderizarEstudios(estudiosRenderizar);
                    estudiosRenderizar = [];
                    examenesEliminar.splice(enEliminado, 1);
                } else {
                    toastr.warning("El examen ya se encuentra cargado", "", { timeout: 1000 });
                }
            }
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

$('.agregarExamen').on('click', function (e) {
    e.preventDefault();
    let id = $("#examenSelect2").val();

    cargarEstudio(id);
    examenesNuevos = examenesNuevos.concat(estudiosRenderizar);
    renderizarEstudios(estudiosRenderizar);
    estudiosRenderizar = [];

});

$('.agregarPaquete').on('click', function (e) {
    e.preventDefault();
    //buscamos los examenes del paquete seleccionado
    let id = $("#paqueteSelect2").val();
    if(id){
        preloader('on');

        $.ajax({
            url: paqueteId,
            type: 'POST',
            data: {
                _token: TOKEN,
                IdPaquete: id,
            },
            async: false,
            success: function (response) {
                let data = response.examenes;
                data.forEach(examen => {
                    cargarEstudio(examen.Id);
                });
                examenesNuevos = examenesNuevos.concat(estudiosRenderizar);
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
    } else {
        toastr.warning('Debe seleccionar un paquete de estudios para agregar.', '', { timeOut: 1000 });
    }
});

tabla.on('click', 'button.remove-item-btn', function () {
    let id = $(this).data().id;
    index = examenes.indexOf(examenes.find(x => x.Id == id));
    if (index != -1) {
        examenesEliminar.push(id);
    } else {
        examenesNuevos.splice(index, 1);
    }
    tabla
        .row($(this).parents('tr'))
        .remove()
        .draw();
});

$("#btnRegistrar").on('click', function (e) {
    e.preventDefault();
    preloader('on');
    let id = $("#idPaquete").val();
    let nombre = $("#nombre").val();
    let descricpion = $("#descripcion").val();
    let alias = $("#alias").val();
    let codigo = $("#codigo").val();
    let idGrupo = $("#grupoSelect2").val();
    let idEmpresa = $("#empresaSelect2").val();

    if (validaciones()) {
        $.post(postEditPaqueteFactutacion, {
            _token: TOKEN,
            id: id,
            nombre: nombre,
            descripcion: descricpion,
            alias: alias,
            codigo: codigo,
            IdGrupo: idGrupo,
            IdEmpresa: idEmpresa,
            estudios: examenesNuevos,
            estudiosEliminar: examenesEliminar
        })
            .done(function () {

                toastr.success('Se ha cargado el paquete correctamente', '', { timeOut: 1000 });
                setTimeout(function () {
                    history.back();
                }, 1000);
            })
            .fail(function (jqXHR) {

                // Manejo de errores
                let errorData = JSON.parse(jqXHR.responseText);
                checkError(jqXHR.status, errorData.error || errorData.msg);
                return;
            })
            .always(function () {
                preloader('off');
            });

    }
});


function validaciones() {
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
    
    if (examenes.length == examenesEliminar.length && examenesNuevos.length === 0) {
        mensaje += "Debe agregar al menos un examen al paquete.\n";
    }

    if (mensaje) {
        preloader('off');
        toastr.warning(mensaje, '', { timeOut: 3000 });
        return false;
    }
    return true;
}