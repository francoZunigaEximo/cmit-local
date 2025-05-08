let examenes = [];
let tabla =  new DataTable("#listaExamenesPaquetes");

$(function () {

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

        $.get(getExamenId, { Id: id })
            .done(function (result) {
                //preloader('off');
                //toastr.success('Se ha realizado la acción correctamente', '', {timeOut: 1000});
                console.log(result);
                if(!examenes.find(x => x.Id == result.Id)){
                    examenes.push(result);
                    tabla.row.add([
                        result.Cod,
                        result.Nombre,
                        result.Descripcion,
                        "<button class=\"btn btn-sm iconGeneral remove-item-btn\" type=\"button\" data-id=\""+result.Id+"\" >x</button>"
                    ]).draw();
                }else{

                }
            })
            .fail(function (jqXHR) {
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);
                checkError(jqXHR.status, errorData.msg);
                return;
            });
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

})