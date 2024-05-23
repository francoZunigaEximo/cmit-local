$(document).ready(()=> {

    const tabla = "#listadoExamenesCuentas";

    toastr.options = {
        closeButton: true,   
        progressBar: true,    
        timeOut: 3000,        
    };

    $('#fechaHasta, #FechaCreate').val(fechaNow(null, "-", 0));
    $('#estado').val("todos");

    $('#empresa, #empresaSaldo, #empresaPago, #empresaCreate').each(function() {
        $(this).select2({
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
    });

    $('#examen, #examenSaldo').each(function(){
        $(this).select2({
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
    });

    $('#paciente').each(function(){
        $(this).select2({
            language: {
                noResults: function() {
    
                return "No hay pacientes con esos datos";        
                },
                searching: function() {
    
                return "Buscando..";
                },
                inputTooShort: function () {
                    return "Por favor, ingrese 2 o más caracteres";
                }
            },
            placeholder: 'Apellido y/o nombre del paciente',
            allowClear: true,
            ajax: {
                url: getPacientes, 
                dataType: 'json',
                data: function(params) {
                    return {
                        buscar: params.term,
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.pacientes 
                    };
                },
                cache: true
            },
            minimumInputLength: 2 
        });
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
    
    $("#rangoDesde, #rangoHasta, #FacturaCreate").inputmask('rango');
    
    $(document).on('click', '.cambiarBoton', function(e) {
        
        e.preventDefault();
        let id = $(this).data('id');

        if(confirm('¿Está seguro que desea realizar la operación?')){

            preloader('on');
            $.post(cambiarPago, {Id: id, _token: TOKEN})
                .done(function(response){

                    preloader('off');
                    let tipoToastr = response.estado == 'success' ? ['success', 'Perfecto'] : ['info', 'Atención'];

                    toastr[tipoToastr[0]](response.message, [tipoToastr[1]], { timeOut: 10000 })
                    let table = $(tabla).DataTable();
                    table.draw(false);
                })
                .fail(function(xhr){

                    preloader('off');
                    toastr.error('Ha ocurrido un error. Consulte con el administrador', 'Error');
                })
        }
        
    });

    $(document).on('click', '.deleteExamen', function(e){
        e.preventDefault();

        let id = $(this).data('id');
        
        if (confirm("¿Estas seguro que deseas eliminar el examen a cuenta?")) {
            preloader('on');
            $.post(eliminarExCuenta, {Id: id, _token: TOKEN})
                .done(function(response){

                    preloader('off');
                    let tipoToastr = response.estado == 'success' ? ['success', 'Perfecto'] : ['info', 'Atención'];

                    toastr[tipoToastr[0]](response.message, [tipoToastr[1]], { timeOut: 10000 })
                    let table = $(tabla).DataTable();
                    table.draw(false);

                })
        }
    });

    $('#checkAll').on('click', function() {
        $('input[type="checkbox"][name="Id"]:not(#checkAll)').prop('checked', this.checked);
    });

    $(document).on('click', '.botonPagar, .quitarPago', function(e){
        e.preventDefault();

        let ids = [];
        $('input[name="Id"]:checked').each(function() {
            ids.push($(this).val());
        });

        if(ids.length > 0) {
            if (confirm('¿Esta seguro que desea realizar esta operación?')) {

                preloader('on');
                $.post(cambiarPago, {Id: ids, _token: TOKEN})
                    .done(function(response){

                        preloader('off');
                        let tipoToastr = response.estado == 'success' ? ['success', 'Perfecto'] : ['info', 'Atención'];

                        toastr[tipoToastr[0]](response.message, [tipoToastr[1]], { timeOut: 10000 })
                        let table = $(tabla).DataTable();
                        table.draw(false);
                    })
                    .fail(function(xhr){

                        preloader('off');
                        toastr.error('Ha ocurrido un error. Consulte con el administrador', 'Error');
                    })
           }
        
        }else{
            toastr.warning('Debes seleccionar al menos un item para realizar la operación.','Alerta');
        }
    });

    $(document).on('click', '.sieteFacturas', function(e){
        e.preventDefault();
        /*let table = $(tabla).DataTable();
        table.ajax.url(INDEX).page.len(7).load();*/
        location.reload();
    });

    $(document).on('click', '.detalles, .saldo', function(e){
        e.preventDefault();

        let id = $(this).data('id'), tipo = $(this).hasClass('detalles') ? 'detalles' : 'saldo';

        if([null, undefined, ''].includes(id)) {
            toastr.warning("No posee identificador para iniciar el proceso");
            return;
        }
        if(confirm("¿Estas seguro que deseas generar el reporte de " + tipo)) {

            $.get(exportGeneral, {Id: id, Tipo: tipo})
            .done(function(response){

                createFile(response.filePath);
                toastr.success("Se esta generando el reporte");
            })

        }
    });

    function fechaNow(fechaAformatear, divider, format) {
        let dia, mes, anio; 
    
        if (fechaAformatear === null) {
            let fechaHoy = new Date();
    
            dia = fechaHoy.getDate().toString().padStart(2, '0');
            mes = (fechaHoy.getMonth() + 1).toString().padStart(2, '0');
            anio = fechaHoy.getFullYear();
        } else {
            let nuevaFecha = fechaAformatear.split("-"); 
            dia = nuevaFecha[0]; 
            mes = nuevaFecha[1]; 
            anio = nuevaFecha[2];
        }
    
        return (format === 1) ? `${dia}${divider}${mes}${divider}${anio}` : `${anio}${divider}${mes}${divider}${dia}`;
    }

    function preloader(opcion) {
        $('#preloader').css({
            opacity: '0.3',
            visibility: opcion === 'on' ? 'visible' : 'hidden'
        });
    }

    function createFile(array){
        let fecha = new Date(),
            dia = fecha.getDay(),
            mes = fecha.getMonth() + 1,
            anio = fecha.getFullYear();

        let filePath = array,
            pattern = /storage(.*)/,
            match = filePath.match(pattern),
            path = match ? match[1] : '';

        let url = new URL(location.href),
            baseUrl = url.origin,
            fullPath = baseUrl + '/cmit/storage' + path;

        let link = document.createElement('a');
        link.href = fullPath;
        link.download = "reporte-"+ dia + "-" + mes + "-" + anio +".xlsx";
        link.style.display = 'none';

        document.body.appendChild(link);
        link.click();
        setTimeout(function() {
            document.body.removeChild(link);
        }, 100);
    }

});