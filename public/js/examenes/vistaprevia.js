$("#Reporte").select2({
        placeholder: 'Seleccionar un reporte',
        language: 'es',
        allowClear: true,
        containerCssClass : 'form-control',
        language: {
            noResults: function() {
                return "No hay clientes con esos datos";        
            },
            searching: function() {
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
           data: function(params) {
                return {
                    buscar: params.term
                };
           },
           processResults: function(data) {
                return {
                    results: data
                };
           },
           cache: true,
        },
        minimumInputLength: 2
    });

$(document).on('click', '#vistaPrevia', function(e){
    let id = $("#Reporte").val();
    preloader('on');

    $.get('/prestaciones/pdfPrueba', {
        Id: 0,
        Examen:  parseInt(id),
        vistaPrevia: true
    }).done(function(response){
        preloader('off');
        if(response){
            vistaPrevia = convertToUrl(response);
            window.open(vistaPrevia, '_blank');
        }
    }).fail(function(err){
        console.error(err)
    })
})
