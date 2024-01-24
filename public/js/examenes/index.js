$(document).ready(function(){

    let opciones = $('#opciones')
        opcionesClass = $('.opciones'),
        especialidad = $('#especialidad'),
        atributos = $('#atributos'),
        codigoex = $('#codigoex'),
        activo = $('#activo'),
        activoClass = $('.activo'),
        examen = $('#examen'),
        reset = $('#reset');

    listaProveedores();
    opcionesClass.hide();
    activoClass.hide();

    $(document).on('change', "#atributos", function(){

        if($(this).val() === 'opciones'){
            opcionesClass.show();
            activoClass.hide();
            activo.val('');

        }else if($(this).val() === 'activo'){
            activoClass.show();
            opcionesClass.hide();
            opciones.val('');
        
        }else{
            activoClass.hide();
            opcionesClass.hide();
            activo.val('');
            opciones.val('');
        }
    });

    codigoex.on('input', function() {

        let textoInput = codigoex.val(), textoEnMayusculas = textoInput.toUpperCase();
        codigoex.val(textoEnMayusculas);
    });

    reset.click(function(){ 
        $('#form-index :input, #form-index select').val('');
        examen.val([]).trigger('change.select2');
        activoClass.hide();
        opcionesClass.hide();
        activo.val('');
        opciones.val('');
        atributos.val('');
        especialidad.val('');
        $('#listaExamenes').DataTable().clear().destroy();
    });

    examen.select2({
        language: {
            noResults: function() {

            return "No hay examenes con esos datos";        
            },
            searching: function() {

            return "Buscando..";
            },
            inputTooShort: function () {
                return "Por favor, ingrese 2 o más caracteres";
            }
        },
        placeholder: 'Nombre del exámen',
        allowClear: true,
        ajax: {
            url: searchExamen, 
            dataType: 'json',
            data: function(params) {
                return {
                    buscar: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: data.examen 
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    function listaProveedores(){

        $.get(lstProveedores, function(response){

            especialidad.empty().append('<option value="" selected>Elige una opción...</option>');

            $.each(response.result, function(index, r){

                contenido = `<option value="${r.Id}">${r.Nombre}</option>`;

                especialidad.append(contenido);
            });
        });
    }

    
});