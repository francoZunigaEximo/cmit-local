$(document).ready(function(){
    let hoy = new Date().toISOString().slice(0, 10);
    $('#fechaHastaA').val(hoy);

    listadoPrestaciones();

    $('#empresa').select2({
        language: {
            noResults: function() {

            return "No hay empresas con esos datos";        
            },
            searching: function() {

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
            data: function(params) {
                return {
                    buscar: params.term,
                    tipo: 'E'
                };
            },
            processResults: function(data) {
                return {
                    results: data.clientes 
                };
            },
            cache: true
        },
        minimumInputLength: 2 
    });

    Inputmask.extendAliases({
        'rango': {
            mask: 'a-9999-99999999',
            placeholder: "X-0000-00000000",
            clearMaskOnLostFocus: true,
            onBeforePaste: function (pastedValue, opts) {
                
                return pastedValue.charAt(0).toUpperCase() + pastedValue.slice(1);
            },
            definitions: {
                'a': {
                    validator: "[A-Za-z]",
                    cardinality: 1,
                    casing: "upper"
                }
            }
        }
    });

    $("#nroFactura").inputmask('rango');


    function listadoPrestaciones()
    {
        $('#tipo').empty();
        preloader('on');
        $.get(lstTipoPrestacion)
            .done(function(response){
                preloader('off');
                $('#tipo').append(`<option value="">Elija una opción...</option>`);
                $.each(response, function(index, data){
                    $('#tipo').append(`<option value="${data.Nombre}">${data.Nombre}</option>`);
                });
            
            })
    }

});