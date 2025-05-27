let tabla = new DataTable("#listaExamenesPaquetes");

let examenes = [];
let examenesNuevos = [];
let examenesEliminar = [];

let estudiosRenderizar = [];

$(document).ready(() => {

    let id = $("#idPaquete").val();
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
                cargarEstudio(examen.Id);
            });
            examenes = response.examenes;
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
})

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
            if (!enExamenesNuevos && !enExamenes) {
                estudiosRenderizar.push(result);
            } else {
                toastr.warning("El examen ya se encuentra cargado", "", { timeout: 1000 });
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
});

tabla.on('click', 'button.remove-item-btn', function () {
    let id = $(this).data().id;
    index = examenes.indexOf(examenes.find(x => x.Id == id));
    if (index) {
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
    let nombre = $("#nombre").val();
    let descripcion = $("#descripcion").val();
    let alias = $("#alias").val();
    let id = $("#idPaquete").val();

    if (nombre && descripcion) {
        $.post(postPaquetesEstudiosEditar, {
            _token: TOKEN,
            id: id,
            nombre: nombre,
            descripcion: descripcion,
            alias: alias,
            estudios: examenesNuevos,
            estudiosEliminar: examenesEliminar
        })
            .done(function () {
                preloader('off');
                toastr.success('Se ha cargado el paquete correctamente', '', { timeOut: 1000 });

            })
            .fail(function (jqXHR) {
                let errorData = JSON.parse(jqXHR.responseText);
                checkError(jqXHR.status, errorData.msg);
                return;
            });

    } else {
        preloader('off');
        toastr.warning("Tiene que ingresar nombre, descricpion y seleccionar al menos un examen", '', { timeOut: 1000 });
    }
});