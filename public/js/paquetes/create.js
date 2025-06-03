let examenes = [];
let estudiosRenderizar = [];

let tabla = new DataTable("#listaExamenesPaquetes");
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

                result.Estudios.forEach(x => {
                    cargarExamen(x.IdExamen);
                });
                examenes = examenes.concat(estudiosRenderizar);
                renderizarEstudios(estudiosRenderizar);
                estudiosRenderizar = [];
                preloader('off');

            })
            .fail(function (jqXHR) {
                let errorData = JSON.parse(jqXHR.responseText);
                checkError(jqXHR.status, errorData.msg);
                return;
            });
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

    $('#btnRegistrar').on('click', function (e) {
        e.preventDefault();
        preloader('on');
        let nombre = $("#nombre").val();
        let descripcion = $("#descripcion").val();
        let alias = $("#alias").val();

        if (nombre && descripcion) {
            $.post(postPaquetesEstudios, {
                _token: TOKEN,
                Nombre: nombre,
                Empresas: clientes
            })
                .done(function () {
                    preloader('off');
                    toastr.success('Se ha editado al paquete correctamente', '', { timeOut: 1000 });

                })
                .fail(function (jqXHR) {
                    preloader('off');
                    let errorData = JSON.parse(jqXHR.responseText);
                    checkError(jqXHR.status, errorData.msg);
                    return;
                });

        } else {
            toastr.warning("Tiene que ingresar nombre, descricpion y seleccionar al menos un examen", '', { timeOut: 1000 });
        }
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
