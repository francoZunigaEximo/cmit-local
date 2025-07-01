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
        if(id){
            cargarExamen(id);
            renderizarEstudios(estudiosRenderizar);
            examenes = examenes.concat(estudiosRenderizar);
            estudiosRenderizar = [];
        }else{
            toastr.warning('Debe seleccionar un examen para agregar.', '', { timeOut: 1000 });
        }
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

        if (validaciones()) {
            $.post(postPaquetesEstudios, {
                _token: TOKEN,
                nombre: nombre,
                alias: alias,
                descripcion: descripcion,
                estudios: examenes
            })
                .done(function () {
                    preloader('off');
                    toastr.success('Se ha editado al paquete correctamente', '', { timeOut: 1000 });
                    setTimeout(function() {
                        history.back();
                    }, 1000);
                })
                .fail(function (jqXHR) {
                    preloader('off');
                    let errorData = JSON.parse(jqXHR.responseText);
                    checkError(jqXHR.status, errorData.error || errorData.msg);
                    return;
                });

        }
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
                async: false,
                data: {
                    _token: TOKEN,
                    IdPaquete: id,
                },

                success: function (response) {
                    console.log(response);
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
        }else{
            toastr.warning('Debe seleccionar un paquete de estudios para agregar.', '', { timeOut: 1000 });
        }
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

function validaciones(){
    let mensaje = "";
    if (!$("#nombre").val()) {
        mensaje += "Debe ingresar un nombre para el paquete.\n";
    }

    if(!$("#alias").val()){
        mensaje += "Debe ingresar un alias para el paquete.\n";
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