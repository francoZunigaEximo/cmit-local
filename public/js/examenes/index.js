$(function(){

    let opciones = $('#opciones')
        opcionesClass = $('.opciones'),
        especialidad = $('#especialidad'),
        atributos = $('#atributos'),
        codigoex = $('#codigoex'),
        activo = $('#activo'),
        activoClass = $('.activo'),
        estado = $('#estado'),
        estadoClass = $('.estado'),
        examen = $('#examen'),
        reset = $('#reset'),
        grilla = $('#listaExamenes');

    listaProveedores();
    opcionesClass.hide();
    activoClass.hide();
    estadoClass.hide();

    $(document).on('change', "#atributos", function() {
        const data = $(this).val();
    
        opcionesClass.hide();
        activoClass.hide();
        estadoClass.hide();
        
        activo.val('');
        estado.val('');
        opciones.val('');
    
        switch (data) {
            case 'opciones':
                opcionesClass.show();
                break;
            case 'activo':
                activoClass.show();
                break;
            case 'estado':
                estadoClass.show();
                break;
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
        estado.val('');
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

    $(document).on('click', '#exportar', function(e){
        e.preventDefault();

        let table = grilla.DataTable();
    
        if (!table.data().any() ) {
            table.destroy();
            toastr.warning('No existen registros para exportar', '', {timeOut: 1000});
            return;
        }

        let data = table.rows({ page: 'current' }).data().toArray();
        let ids = data.map(function(row) {
            return row.IdExamen;
        });
        preloader('on')
        $.get(exportarExcel, {Ids: ids})
            .done(function(response){
                createFile("excel", response.filePath, generarCodigoAleatorio() + "_reporte");
                preloader('off')
                toastr.success(response.msg, '', {timeOut: 1000});
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;  
            })
        });

    function listaProveedores(){

        $.get(lstProveedores, function(response){

            especialidad.empty().append('<option value="" selected>Elige una opción...</option>');

            for(let index = 0; index < response; index++) {
                let r = response[index],
                contenido = `<option value="${r.Id}">${r.Nombre}</option>`;
                especialidad.append(contenido);
            }
        });
    }

    
});