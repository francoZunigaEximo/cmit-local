$(function(){

    const tabla = "#listadoExamenesCuentas";

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
        let nroFactura = $(this).data('nro');
        let empresa = $(this).data('empresa');

        swal({
            title: "Empresa: "+empresa + " Nro Factura: " + nroFactura + " \n ¿Está seguro que desea realizar la operación?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                preloader('on');
                $.post(cambiarPago, {Id: id, _token: TOKEN})
                    .done(function(response){
                        preloader('off');
                        let tipoToastr = response.estado == 'success' ? ['success', 'Perfecto'] : ['info', 'Atención'];

                        toastr[tipoToastr[0]](response.message, [tipoToastr[1]], { timeOut: 10000 })
                        $(tabla).DataTable().draw(false);
                    })
                    .fail(function(jqXHR){
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return;  
                    })
            }
        })

            
        
    });

    $(document).on('click', '.deleteExamen', function(e){
        e.preventDefault();

        let id = $(this).data('id');
        
        swal({
            title: "¿Estas seguro que deseas eliminar el examen a cuenta?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
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

        if (ids.length === 0) {
            toastr.warning('Debes seleccionar al menos un item para realizar la operación.','',{timeOut: 1000});
            return;
        }

        swal({
            title: "¿Esta seguro que desea realizar esta operación?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                preloader('on');
                $.post(cambiarPago, {Id: ids, _token: TOKEN})
                    .done(function(response){

                        preloader('off');
                        let tipoToastr = response.estado == 'success' ? ['success', 'Perfecto'] : ['info', 'Atención'];

                        toastr[tipoToastr[0]](response.message, [tipoToastr[1]], { timeOut: 10000 })
                        $(tabla).DataTable().draw(false);

                    })
                    .fail(function(jqXHR){
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return;  
                    })
            }
        });
    });

    $(document).on('click', '.facturasHoy', function(e){
        e.preventDefault();
        /*let table = $(tabla).DataTable();
        table.ajax.url(INDEX).page.len(7).load();*/
        location.reload();
    });


});