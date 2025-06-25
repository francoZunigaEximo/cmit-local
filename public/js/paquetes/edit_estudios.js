let tabla = new DataTable("#listaExamenesPaquetes",{
    searching: false,
    lengthChange: false,
});

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
            let enEliminado = examenesEliminar.indexOf(examenesEliminar.find(x => x == result.Id));

            console.log(enExamenes, enExamenesNuevos, enEliminado);
            if (!enExamenesNuevos && !enExamenes) {
                estudiosRenderizar.push(result);
            } else {
                if(enEliminado >= 0 && enExamenes){
                    estudiosRenderizar.push(result);
                    renderizarEstudios(estudiosRenderizar);
                    estudiosRenderizar = [];
                    examenesEliminar.splice(enEliminado, 1);
                }else{
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
});

tabla.on('click', 'button.remove-item-btn', function () {
    let id = $(this).data().id;
    index = examenes.indexOf(examenes.find(x => x.Id == id));
    console.log(index);
    if (index != null) {
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

    if (validaciones()) {
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

    }
});

function validaciones(){
    let mensaje = "";
    if (!$("#nombre").val()) {
        mensaje += "Debe ingresar un nombre para el paquete.\n";
    }

    if(!$("#alias").val()){
        mensaje += "Debe ingresar un alias para el paquete.\n";
    }
    
    if ( examenes.length == examenesEliminar.length && examenesNuevos.length === 0) {
        mensaje += "Debe agregar al menos un examen al paquete.\n";
    }
    if (mensaje) {
        preloader('off');
        toastr.warning(mensaje, '', { timeOut: 3000 });
        return false;
    }
    return true;
}