$(document).ready(function(){

    listado();

    $('#examen').each(function(){
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

    $('.volverPrincipal').click(function(){
        window.location.href = INDEX;
    });

    $('#paquete').select2({
        placeholder: 'Seleccionar paquete...',
        language: {
            noResults: function() {

            return "No hay paquete con esos datos";        
            },
            searching: function() {

            return "Buscando..";
            },
            inputTooShort: function () {
                return "Por favor, ingrese 2 o más caracteres";
            }
        },
        allowClear: true,
        ajax: {
           url: getPaquetes,
           dataType: 'json',
           delay: 250,
           data: function(params) {
                return {
                    buscar: params.term,
                };
           },
           processResults: function(data) {
                return {
                    results: data.paquete
                };
           },
           cache: true,
        },
        minimumInputLength: 2
    });

    $('#facturacion').select2({
        placeholder: 'Seleccionar paquete...',
        language: {
            noResults: function() {

            return "No hay paquete con esos datos";        
            },
            searching: function() {

            return "Buscando..";
            },
            inputTooShort: function () {
                return "Por favor, ingrese 2 o más caracteres";
            }
        },
        allowClear: true,
        ajax: {
           url: getPaqueteFact,
           dataType: 'json',
           delay: 250,
           data: function(params) {
                return {
                    buscar: params.term,
                };
           },
           processResults: function(data) {
                return {
                    results: data.paquete
                };
           },
           cache: true,
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
  
    $("#Factura").inputmask('rango');

    $(document).on('click', '.aplicar', function(e){
        e.preventDefault();

        let dni = $('#dni').val(), 
            examen = $('#examen').val(), 
            paquete = $('#paquete').val(), 
            facturacion = $('#facturacion').val(), 
            cantidad = $('#cantidad').val(), 
            contador = [examen, paquete, facturacion].filter(conteo => conteo !== null).length;

        if (contador !== 1) {
            toastr.warning("Solo puede elegir un paquete o examen para aplicar a la vez. No puede seleccionar más de uno", {timeOut: 2000});
            return;
        }

        if  (cantidad <= 0 || cantidad === '') {
            toastr.warning("Debe seleccionar una cantidad");
            return;
        }

        let valor = examen !== null ? examen : paquete !== null ? paquete : facturacion !== null ? facturacion : 0;
        let tipo = examen !== null ? 'examen' : paquete !== null ? 'paquete' : facturacion !== null ? 'facturacion' : 0;

        if(valor === 0 || tipo === 0) { toastr.warning("Hay un problema en la selección del paquete. Verifique la selección"); return; }

        preloader('on');

        $.post(savePaquete, {_token: TOKEN, Id: ID, Tipo: tipo, examen: valor, precarga: dni, cantidad: cantidad})
            .done(function(){
                preloader('off');
                toastr.success('Se ha realizado ha cargado el o los examenes.')
                setTimeout(()=> {
                    listado();
                }, 3000);
                $('#dni').val("");
                $('#cantidad').val(1);
                $("#examen option").remove();
                $("#paquete option").remove();
                $("#facturacion option").remove();
            })
    });

    $(document).on('click', '.actualizarPagoCuenta', function(e){

        e.preventDefault();

        let empresa = $('#empresa').val(), Fecha = $('#Fecha').val(), Factura = $('#Factura').val(), FechaPago = $('#FechaPago').val(), Obs = $('#Obs').val(),
            condiciones = [empresa, Fecha, Factura],
            partes = Factura.split('-');

        if (condiciones.some(condicion => condicion === '' || condicion === null) === true) {
            toastr.warning("Los campos marcados con astericos son obligatorios");
            return;
        }

        swal({
            title: "¿Esta segudo que desea actualizar el examen a cuenta?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                preloader('on');
                $.post(updateExamenCuenta, {_token: TOKEN, IdEmpresa: empresa, Fecha: Fecha, Tipo: partes[0], Suc: parseInt(partes[1], 10), Nro: parseInt(partes[2], 10), Obs: Obs, FechaP: FechaPago, Id: ID})
                    .done(function(response){
                        preloader('off');
                        toastr.success('Se ha actualizado el examen a cuenta correctamente');
                    })
            }
         });

        
    });

    function listado() {
        preloader('on');
        $('#lstSaldos').empty();
    
        $.get(listadoExCta, {Id: ID})
            .done(function(response){
                preloader('off');                
                let dniAnterior = '';
                $.each(response, function(index, r){
                    
                    let nombreCompleto = r.ApellidoPaciente + ' ' + r.NombrePaciente;
                    let contenido = `
                        <tr>
                            <td><div class="text-center"><input type="checkbox" name="Id" value="${r.IdEx}"></div></td>
                            <td>${r.Precarga === '' ? '-' : r.Precarga}</td>
                            <td title="${r.Examen}">${acortadorTexto(r.Examen, 10)}</td>
                            <td>${r.Prestacion}</td>
                            <td title="${nombreCompleto}">${acortadorTexto(nombreCompleto)}</td>
                            <td>
                                <button data-id="${r.IdEx}" type="button" class="btn iconGeneral editarDNI" title="Agregar/Editar DNI" data-bs-toggle="modal" data-bs-target="#editarDNI">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <button data-id="${r.IdEx}" type="button" class="btn iconGeneral deleteItem" title="Eliminar examen">
                                    <i class="ri-delete-bin-2-line"></i>
                                </button>
                            </td>
                        </tr>
                    `;
    
                    $('#lstSaldos').append(contenido);
    
                    // Si el DNI actual es diferente al DNI anterior, agrega la clase al registro anterior
                    if (dniAnterior && dniAnterior !== r.Precarga) {
                        $('#lstSaldos tr:last-child').prev().addClass('border-grueso');
                    }
    
                    dniAnterior = r.Precarga;
                });
    
                // Agrega la clase al último registro
                $('#lstSaldos tr:last-child').addClass('border-grueso');
    
                $("#listadoSaldos").fancyTable({
                    pagination: true,
                    perPage: 15,
                    searchable: false,
                    globalSearch: false,
                    sortable: false, 
                });
            })
    }
    

    $(document).on('click', '.editarDNI, .editarMasivo', function(e){
        e.preventDefault();

        var id, ids = [];
        $(".saveCambiosEdit").show();
        $("#dniNuevo").attr("disabled", false);

        if($(this).hasClass('editarDNI') === true) {

            id = $(this).data('id');
            $('#cargarId').val(id);
        
        }else if($(this).hasClass('editarMasivo') === true) {

            $('input[name="Id"]:checked').each(function() {
                ids.push($(this).val());
            });
    
            let checkAll =$('#checkAll').prop('checked');
    
            if(ids.length === 0 && checkAll === false){
                $(".saveCambiosEdit").hide();
                $("#dniNuevo").attr("disabled", true);
                toastr.warning('No hay items seleccionados');
                return;
            }

            var carga = ids.join(',');
            $('#cargarId').val(carga);
        }
    });

    $(document).on('click', '.saveCambiosEdit', function(e){
        e.preventDefault();
        let id = $('#cargarId').val(), dniNuevo = $('#dniNuevo').val();
        
        if(dniNuevo.length > 8 || dniNuevo.length === 0) {
            toastr.warning("El dni debe llevar un máximo de 8 digitos");
            return;
        }

        preloader('on');
        $.post(savePrecarga, {_token: TOKEN, Id: id.includes(',') ? id = id.split(",") : id, Precarga: dniNuevo})
            .done(function(){
                preloader('off');
                toastr.success('Se ha cambiado el dni de la precarga');
                setTimeout(()=>{
                    $('#editarDNI').modal('hide');
                    listado();
                    $('#dniNuevo').val("");
                },2000);
            })
    });

    $(document).on('click', '.liberarItemMasivo', function(e){
        e.preventDefault();

        let id = [], checkAll = $('#checkAll').prop('checked');

        $('input[name="Id"]:checked').each(function() {
            id.push($(this).val());
        });

        if(id.length === 0 && checkAll === false){
            toastr.warning('No hay items seleccionados', 'Atención');
            return;
        }

        swal({
            title: "¿Esta seguro que desea liberar esos items del examen a cuenta?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {

                preloader('on');
                $.get(liberarItemExCta, {Id: id})
                    .done(function(){
                        preloader('off');
                        toastr.success("Se ha realizado la liberación de los items correctamente");
                        setTimeout(()=>{
                            listado()
                        },2000);
                    });
            }
        });
    });

    $(document).on('click', '.deleteItem, .deleteItemMasivo', function(e){
        e.preventDefault();
         
        var id;

        if ($(this).hasClass('deleteItem') === true ){

            id = $(this).data('id');
            if(id === null || id === '') return;
        
        } else {

            id = [];

            $('input[name="Id"]:checked').each(function() {
                id.push($(this).val());
            });

            let checkAll =$('#checkAll').prop('checked');

            if(id.length === 0 && checkAll === false){
                toastr.warning('No hay items seleccionados', 'Atención');
                return;
            }
        }

        swal({
            title: "¿Esta seguro que desea eliminar el item del exámen?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar)=>{
            if(confirmar){
                preloader('on');
                $.get(deleteItemExCta, {Id: id})
                    .done(function(){
                        preloader('off');
                        toastr.success("Se ha realizado la eliminación");
                        setTimeout(()=>{
                            listado()
                        },2000);
                    });
            }
        });
    });

    //Exportar Excel a clientes
    $(document).on('click', '.exportar, .imprimir', function(e) {
        e.preventDefault();

        let id = $(this).data('id'), tipo = $(this).hasClass('exportar') ? 'excel' : 'pdf';

        if([null, undefined, ""].includes(id)) {
            toastr.warning('No hay datos para exportar');
            return;
        }

        swal({
            title: "¿Estás seguro de que deseas generar el reporte de " + tipo.charAt(0).toUpperCase() + tipo.slice(1) + "?",
            icon: "warning",
            buttons: ['Cancelar', 'Aceptar'],
        }).then((confirmar)=>{
            if(confirmar){
                preloader('on')
                $.ajax({
                    url: tipo === 'excel' ? exportExcel : exportPDF,
                    type: "GET",
                    data: {
                        Id: id
                    },
                    success: function(response) {
                        preloader('off');
                        const jsonPattern = /{.*}/s;
                        const match = response.match(jsonPattern);

                        if(match) {
                            const jsonResponse = match[0];
                            const data = JSON.parse(jsonResponse);

                            createFile(tipo, data.filePath, data.name);
                            toastr.success(data.msg);
                        }
                    },
                    error: function(jqXHR) {
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return;  
                    }
                });
    
            }
        });
            
    });


});