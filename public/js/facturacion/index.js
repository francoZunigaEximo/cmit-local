$(document).ready(function(){
    
    let hoy = new Date().toISOString().slice(0, 10);
    $('#fechaHasta').val(hoy);
    $('#tabla').val('facturas');

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

    $('#art').select2({
        language: {
            noResults: function() {

            return "No hay art con esos datos";        
            },
            searching: function() {

            return "Buscando..";
            },
            inputTooShort: function () {
                return "Por favor, ingrese 2 o más caracteres";
            }
        },
        placeholder: 'Nombre de la ART',
        allowClear: true,
        ajax: {
            url: getClientes, 
            dataType: 'json',
            data: function(params) {
                return {
                    buscar: params.term,
                    tipo: 'A'
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

    $("#facturaDesde, #facturaHasta").inputmask('rango');

    $('#checkAllFactura').on('click', function() {
        $('input[type="checkbox"][name="Id_factura"]:not(#checkAllFactura)').prop('checked', this.checked);
    });

    $(document).on('click', '.editar', function(e){
        e.preventDefault();
        let id = $(this).data('id'), opcion = $(this).data('opcion');

        if(opcion === 1){
            window.location.href = lnkFactura.replace('__id__', id);
        }else if(opcion === 2){
            window.location.href = lnkExamenCuenta.replace('__id__', id);
        }   
    });

    $(document).on('click', '.eliminar, .eliminarMultiple', function(e){

        e.preventDefault();

        let ids = [], arrOpciones = [];

        if($(this).hasClass('eliminar')){

            arrOpciones.push($(this).data('opcion'));
            ids.push($(this).data('id'));
        
        }else if($(this).hasClass('eliminarMultiple')){

            $('input[name="Id_factura"]:checked').each(function() {
                var opcion = $(this).attr('data-opcion');
                arrOpciones.push(opcion);
                ids.push($(this).val());
            });
        }

        if (ids.length === 0) {
            toastr.warning('Debe seleccionar al menos una factura para la eliminar');
            return; 
        }

        swal({
            title: "¿Estás seguro que desea eliminar?",
            icon: "warning",
            buttons: ['Cancelar', 'Aceptar'],
        }).then((confirmar)=>{
            if(confirmar) {
                preloader('on');
                $.get(eliminarFactura, {Ids: ids, Tipo: arrOpciones})
                    .done(function(response){
                        preloader('off');
                        console.log(response); // Verificar la respuesta recibida del servidor
                        response.forEach(function(item){
                            let tipoToastr = item.tipo === 'success' ? 'success' : 'warning';
                            toastr[tipoToastr](item.msg, {timeOut: 1000});

                            if(item.tipo === 'success'){
                                $('#listaFacturas').DataTable().draw(false);
                            }
                        });
                    })
                    .fail(function(jqXHR){
                        preloader('off');            
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return;
                    });
            }   
        });
    });

    $(document).on('click', '.imprimir', function(e){
        e.preventDefault();

        let ids = [], arrOpciones = [];

        $('input[name="Id_factura"]:checked').each(function() {
            var opcion = $(this).attr('data-opcion');
            arrOpciones.push(opcion);
            ids.push($(this).val());
        });

        if(ids.length === 0){
            toastr.warning('Debe seleccionar al menos una factura para imprimir');
            return;
        }

        swal({
            title: "¿Estas seguro que desea imprimir?",
            icon: "warning",
            buttons: ['Cancelar', 'Aceptar'],
        }).then((confirmar)=>{
            if(confirmar){
                preloader('on');
                $.get(exportar, {Ids: ids, Tipo: 'imprimir', Opcion: arrOpciones})

                    .done(function(response){

                        preloader('off');
                        //console.log(response); return;
                        response.forEach(function(item){
                            let tipoToastr = item.original.icon === 'success' ? 'success' : 'warning';
                            createFile("pdf", item.original.filePath, item.original.name);
                            toastr[tipoToastr](item.original.msg, {timeOut: 1000});
                        });
                    })
                    .fail(function(jqXHR){
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);
                        checkError(jqXHR.status, errorData.msg);
                        return;
                    });
            }
        });
    });


});