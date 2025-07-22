$("#Reporte").select2({
    placeholder: 'Seleccionar un reporte',
    language: 'es',
    allowClear: true,
    containerCssClass: 'form-control',
    language: {
        noResults: function () {
            return "No hay clientes con esos datos";
        },
        searching: function () {
            return "Buscando..";
        },
        inputTooShort: function () {
            return "Por favor, ingrese 2 o más caracteres";
        }
    },
    ajax: {
        url: getReportes,
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                buscar: params.term
            };
        },
        processResults: function (data) {
            return {
                results: data
            };
        },
        cache: true,
    },
    minimumInputLength: 2
});

if (reporte) {
    let option = new Option(reporte.Nombre, reporte.Id, true, true);
    $("#Reporte").append(option).trigger('change');
}

$(document).on('click', '#vistaPrevia', function (e) {
    let id = $("#Reporte").val();
    if (id) {
        preloader('on');

        $.get(getReporte, {
            Id: 0,
            Examen: parseInt(id),
            vistaPrevia: true
        }).done(function (response) {
            preloader('off');
            if (response) {
                let vistaPrevia = convertToUrl(response, "temp");
                window.open(vistaPrevia, '_blank');
            }
        }).fail(function (err) {
            preloader('off');
            toastr.error('Error al generar la vista previa del reporte', 'Error', {
                timeOut: 3000
            });
        })
    } else {
        toastr.error('Debe seleccionar un reporte para generar la vista previa', 'Error', {
            timeOut: 3000
        });
    }
})
