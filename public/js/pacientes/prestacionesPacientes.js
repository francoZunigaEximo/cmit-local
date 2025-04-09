$(function(){

    const principal = {
        Fecha: $('#Fecha'),
        FechaN: $('#FechaN'),
        siguienteExCta: $('#siguienteExCta'),
        seleccionExCta: $('.seleccionExCta'),
        editarComentario: $('.editarComentario'),
    }

    const variables = {
        ElPago: $('#ElPago'),
        ElSPago: $('#ElSPago'),
        ElTipo: $('#ElTipo'),
        ElSucursal: $('#ElSucursal'),
        ElNroFactura: $('#ElNroFactura'),
        ElNroFactProv: $('#ElNroFactProv'),
        ElAutorizado: $('#ElAutorizado'),
        tipoPrestacionPres: $('#tipoPrestacionPres'),
    };

    variables.ElPago
        .add(variables.ElSPago)
        .add(variables.ElTipo)
        .add(variables.ElSucursal)
        .add(variables.ElNroFactura)
        .add(variables.ElNroFactProv)
        .add(variables.ElAutorizado)
        .prop('disabled', true);

    let IdNueva, empresaInput = $('#selectClientes').val(), artInput = $('#selectArt').val(), hoy = new Date().toLocaleDateString('en-CA');
    
    principal.Fecha
        .add(principal.FechaN)
        .val(hoy);

    principal.siguienteExCta
        .add(principal.seleccionExCta)
        .add(principal.editarComentario)
        .hide();

    
    precargaTipoPrestacion(variables.tipoPrestacionPres.val());
    getMap(empresaInput, artInput);
    getListado(null);
    listadoConSaldos(empresaInput);
    cantidadDisponibles(empresaInput);
    listadoFacturas(empresaInput, null, true, '#lstEx');
    listadoFacturas(empresaInput, null, false, '#lstEx2');
    getUltimasFacturadas(empresaInput);
    // selectorPago(pagoInput);

    variables.tipoPrestacionPres.on('change', function(){
        precargaTipoPrestacion(variables.tipoPrestacionPres.val());
    });

    $(document).on('change', '#selectClientes, #selectArt, input[type=radio][name=TipoPrestacion]', function() {

        let empresaCap = $('#selectClientes').val(), artCap = $('#selectArt').val(), tipoPrestacion = $('input[type=radio][name=TipoPrestacion]:checked').val();

        getUltimasFacturadas(empresaCap);
        listadoConSaldos(empresaCap);
        cantidadDisponibles(empresaCap);
        listadoFacturas(empresaCap, null, true, '#lstEx');
        listadoFacturas(empresaCap, null, false, '#lstEx2');

        if (tipoPrestacion === 'ART') {  
            getMap(empresaCap, artCap);  
        }
    });

     //Guardamos la prestación
    $(document).on('click', '.cargarExPrestacion, #guardarPrestacion', function(e){
        e.preventDefault();

        // Agregamos los eamenes a cuenta
        if ($(this).hasClass('cargarExPrestacion')) {

            var ids = [];

            $('#listEdicion input[type="checkbox"]').each(function(){
                var id = $(this).data('id'); 
                ids.push(id);
            });

            if(ids.length === 0) {
                toastr.warning("Debe seleccionar un examen para registrar la prestación",'',{timeOut: 1000});
                return;
            }
        }
        let paciente = ID,
            fecha = $('#Fecha').val(),
            tipoPrestacion = $('#tipoPrestacionPres').val(),
            mapa = $('#mapas').val(),
            pago = $('#PagoLaboral').val(),
            spago = $('#SPago').val(),
            observaciones = $('#Observaciones').val(),
            autorizado = $('#ElAutorizado').val(),
            tipo = $('#ElTipo').val();
            sucursal = $('#ElSucursal').val();
            nroFactura = $('#ElNroFactura').val(),
            NroFactProv = $('#ElNroFactProv').val();
            
        if (['', null].includes(tipoPrestacion)) {
            toastr.warning('El tipo de prestación no puede ser un campo vacío','', {timeOut: 1000});
            return;
        }

        if (tipoPrestacion === 'ART' && (mapa == '0' || mapa === '')){
            toastr.warning('Debe seleccionar un mapa vigente para continuar si su prestacion es ART','', {timeOut: 1000});
            return;
        }
        
        preloader('on');
        $.ajax({
            url: verificarAlta,
            type: 'GET',
            data: {
                Id: ID
            },
            success: function(response){
                preloader('off');
                let cliente = response.cliente, clienteArt = response.clienteArt;
                
                $.ajax({
                    url: savePrestacion,
                    method: 'post',
                    data: {
                        paciente: paciente,
                        tipoPrestacion: tipoPrestacion,
                        mapas: mapa,
                        fecha: fecha,
                        pago: pago,
                        spago: spago,
                        observaciones: observaciones,
                        tipo: tipo,
                        autorizado: autorizado,
                        sucursal: sucursal,
                        nroFactura: nroFactura,
                        IdART: clienteArt.Id ?? 0,
                        IdEmpresa: cliente.Id ?? 0,
                        examenCuenta: ids,
                        NroFactProv: NroFactProv,
                        _token: TOKEN
                    },
                    success: function(response){
                        toastr.success(response.msg,'',{timeOut: 1000});
                        $('.nuevaPrestacion').hide();
                        $('.volverPrestacionLimpia').trigger('click');

                        IdNueva = response.nuevoId;
                        cargarExamen(response.nuevoId);
                        contadorExamenes(response.nuevoId);
                        $('#idPrestacion').val(response.nuevoId);
                    },
                    error: function(jqXHR){
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return; 
                    }
                });
            },
            error: function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return; 
            }   
        });
    });

    //Buscador de prestaciones en Pacientes
    $('#buscarPrestPaciente').on('keypress', function(e){

        if (e.keyCode === 13){
            e.preventDefault();
            let buscar = $(this).val();
            $('#grillaPacientes').empty().append(getListado(buscar));
        }
    });

    //Bloqueo de prestación
    $(document).on('click', '#blockPrestPaciente', function(e) {
        e.preventDefault();
        let Id = $(this).data('idprest');

        swal({
            title: "¿Esta seguro que desea bloquear la prestación?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                preloader('on');
                $.ajax({
                    url: blockPrestacion,
                    type: 'GET',
                    data: {
                        Id: Id,
                    },
                    success: function(response) {
                        preloader('off');
                        toastr.success(response.msg,'',{timeOut: 1000});
                        cambioEstadoBlock();
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

    //Baja logica de prestación
    $(document).on('click', '#downPrestPaciente', function(e) {
        e.preventDefault();
        let Id = $(this).data('idprest');
        
        swal({
            title: "¿Esta seguro que desea eliminar la prestación?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                preloader('on');
                $.ajax({
                    url: downPrestaActiva,
                    type: 'GET',
                    data: {
                        Id: Id,
                    },
                    success: function(response) {
                        preloader('off');
                        toastr.success(response.msg,'',{timeOut: 1000});
                        cambioEstadoDown();
                        getListado(null);
                    },
                    error: function(xhr){
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return; 
                    }
                });
            }
        }); 
    });

    $(document).on('keydown', function(e) {
        if (e.keyCode === 27) {
            e.preventDefault();
            $('#buscar').val(" ");
            window.location.reload();
        }
    });

    $(document).on('change', '#PagoLaboral', function(){

        let pago = $(this).val();
        selectorPago(pago);
    });

    function selectorPago(pago) {
        
        if (pago != 'B') {
            $('#SPago, #Tipo, #Sucursal, #NroFactura').val(" ");
        }

        if(['B','C', ''].includes(pago)) {
            
            $('.ultimasFacturadas, .examenesDisponibles, #siguienteExCta').hide();
            $('#guardarPrestacion').show();
        }else{
            preloader('on');
            $.get(cantTotalDisponibles, {Id: $('#selectClientes').val()})
            .done(function(response){
                preloader('off');
                if(response > 0) {
                    $('.ultimasFacturadas, .examenesDisponibles, #siguienteExCta').show();
                    $('#guardarPrestacion').hide();
                }
            });
        }

    }

    $(document).on('click', '#siguienteExCta', function(e){
        e.preventDefault();
        $('.seleccionExCta').show();
        $('.nuevaPrestacion').hide();
    });

    $(document).on('click', '.volverPrestacion', function(e){
        e.preventDefault();
        $('.seleccionExCta').hide();
        $('.nuevaPrestacion').show();
    });

    $('#examen').select2({
        placeholder: 'Seleccionar exámen...',
        dropdownParent: $('#altaPrestacionModal'),
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
        allowClear: true,
        ajax: {
           url: searchExamen,
           dataType: 'json',
           delay: 250,
           data: function(params) {
                return {
                    buscar: params.term,
                };
           },
           processResults: function(data) {
                return {
                    results: data.examen
                };
           },
           cache: true,
        },
        minimumInputLength: 2
    });

    $(document).on("select2:open", () => {
        document.querySelector(".select2-container--open .select2-search__field").focus()
    });

    $(document).on('click', '.buscarExamen', function(e){
        e.preventDefault();

        let examen = $('#examen').val(), empIn = $('#selectClientes').val();

        if([null,''].includes(examen)) {
            
            toastr.warning('Debe seleccionar un exámen','',{timeOut: 1000});
            return;
        }

        listadoFacturas(empIn, examen, true, '#lstEx');
        listadoFacturas(empIn, examen, false, '#lstEx2');
        examen.remove();
    });

    $(document).on('click', '.reiniciarExamen', function(e){
        e.preventDefault();

        let empIn = $('#selectClientes').val();

        listadoFacturas(empIn, null, true, '#lstEx');
        listadoFacturas(empIn, null, false, '#lstEx2');

    });

    $(document).on('click', '.precargaExamenes', function(e){
        e.preventDefault();

        let ids = [], checkAll =$('#checkAllEx').prop('checked');

        $('#lstEx input[type="checkbox"]:checked').each(function(){
            ids.push($(this).val());
        });

        if(ids.length === 0 && checkAll === false) {
            toastr.warning("Debe seleccionar un examen para añadirlo a la prestación",'',{timeOut: 1000});
            return;
        }

        cargaPreExamenes(ids);
        $('#lstEx input[type="checkbox"]:checked').prop('checked', false);
    });

    $(document).on('click', '.deleteEx', function(e){
        e.preventDefault();
        $(this).closest('tr').remove();
        
    });

    $(document).on('click', '.deleteMasivo', function(e){
        e.preventDefault();
    
        let ids = $('#listEdicion input[name="Id_exa"]:checked'), checkAll = $('#checkAllEx').prop('checked');
    
        if(ids.length === 0 && checkAll === false) {
            toastr.warning("Debe seleccionar un exámen para sacarlo del listado",'',{timeOut: 1000});
            return;
        }
    
        ids.each(function() {
            $(this).closest('tr').remove();
        });

        toastr.success("Se elimino todo correctamente",'',{timeOut: 1000});
    });
    

    $('#checkAllEx').on('click', function() {
        $('input[type="checkbox"][name="Id_exa"]:not(#checkAllEx)').prop('checked', this.checked);
    });
  

    function cargaPreExamenes(valor) {
        preloader('on');
        
        $.get(preExamenes, {Id: valor})
            .done(function(response){

                for(let index = 0; index < response.length; index++) {
                    let r = response[index], existe = false;

                    $('#listEdicion tr').each(function() {
                        var nombreExamen = $(this).find('td:first').text();
                        if (nombreExamen === r.NombreExamen) {
                            preloader('off');
                            existe = true;
                            toastr.warning("No pueden haber dos examenes iguales en una prestación",'',{timeOut: 1000});
                            return false; 
                        }

                    });
    
                    if (!existe) {
                        let contenido = `
                            <tr>
                                <td>${r.NombreExamen}</td>
                                <td>${r.Especialidad}</td>
                                <td>${r.diasVencer}</td>
                                <td><input type="checkbox" name="Id_exa" data-id="${r.IdEx}"></td>
                                <td>
                                    <button type="button" class="btn iconGeneral deleteEx"><i class="ri-delete-bin-2-line"></i></button>
                                </td>
                            </tr>
                        `;
                        $('#listEdicion').append(contenido);
                        preloader('off');
                    }
                }

                
            })
    }

    function selectorPago(pago) {
        
        if(['B', 'A', ''].includes(pago)) {
            
            $('.ultimasFacturadas, .examenesDisponibles, #siguienteExCta').hide();
            $('#guardarPrestacion').show();
        }else{
            preloader('on');
            $.get(cantTotalDisponibles, {Id: $('#selectClientes').val()})
            .done(function(response){
                preloader('off');
                if(response > 0) {
                    $('.ultimasFacturadas, .examenesDisponibles, #siguienteExCta').show();
                    $('#guardarPrestacion').hide();
                }
            });
        }

    }

    //Obtener fechas
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

    function cambioEstadoDown() {
        let borrar = $('#filapresId').data('filapres');
        $('tr[data-filapres="' + borrar + '"]').hide();
    }

    function cambioEstadoBlock() {
        $('#blockPrestPaciente').prop('disabled', true);
        $('#estadoBadge').removeClass('badge badge-soft-success').addClass('badge badge-soft-danger');
        $('#estadoBadge').text('Bloqueado');
        getListado(null);
    }

    async function getMap(idEmpresa, idArt){

        let empresa = idEmpresa, art = idArt;
 
        $.get(getMapas, {empresa: empresa, art: art})
            .done(await function(response){
   
                let mapas = response.mapas;
                $('#mapas, #mapasN').empty();
                
                if(mapas.length === 0)
                {
                    $('#mapas, #mapasN').empty().append('<option title="Sin mapas disponibles para esta ART y Empresa." value="0" selected>Sin mapas disponibles.</option>');
                }else{

                    for(let index = 0; index < mapas.length; index++) {
                        let d = mapas[index],
                                contenido = `<option value="${d.Id}">${d.Nro} | Empresa: ${d.RSE} - ART: ${d.RSArt}</option>`;
    
                        $('#mapas, #mapasN').append(contenido);
                    }
                } 
            })
    }

    function precargaTipoPrestacion(val){
        return val === 'ART' ? $('.selectMapaPres').show() : $('.selectMapaPres').hide();
    }

    function getListado(buscar) {

        preloader('on');
        $.ajax({
            url: searchPrestPacientes,
            type: 'GET',
            data: {
                buscar: buscar,
                paciente: ID
            },
            success: function(response){

                preloader('off');
                let pacientes = response.pacientes;
                $('#results').hide();

                $('#listaPacientes tbody').empty();
                $.each(pacientes.data, function(index, papre) {

                    let cerradoAdjunto = papre.CerradoAdjunto || 0,
                        total = papre.Total || 1,
                        calculo = parseFloat(((cerradoAdjunto / total) * 100).toFixed(2)),
                        resultado = (calculo === 100) ? 'fondo-blanco' : (calculo >= 86 && calculo <= 99) ? 'fondo-verde' : (calculo >= 51 && calculo <= 85) ? 'fondo-amarillo' : (calculo >= 1 && calculo <= 50) ? 'fondo-naranja' : 'fondo-rojo';
                    
                    let porcentaje = parseFloat(((cerradoAdjunto / total) * 100).toFixed(2)) === 0 ? '0.00' : parseFloat(((cerradoAdjunto / total) * 100).toFixed(2));

                    let row = `<tr data-id="${papre.Id}">
                                <td>
                                    <div class="${papre.Anulado == 0 ? resultado : "rojo"}">${papre.Anulado == 0 ? porcentaje + `%` : '0.00%'}</div>
                                </td>
                                <td>
                                    ${fechaNow(papre.FechaAlta,'/',0)}
                                </td>
                                <td>
                                    ${papre.Id}
                                </td>
                                <td>
                                    ${papre.Tipo}
                                </td>
                                <td title="${papre.Empresa}">
                                    ${papre.Empresa}
                                </td>
                                <td title="${papre.ParaEmpresa}">
                                    ${papre.ParaEmpresa}
                                </td>
                                <td title="${papre.Art}">
                                    ${papre.Art}
                                </td>
                                <td>
                                    <span>${comprobarEstado(papre.Cerrado, papre.Finalizado, papre.Entregado)}</span>
                                </td>
                                <td>
                                    ${(`<div class="text-center"><i class="${papre.eEnviado === 1 ? `ri-checkbox-circle-fill verde` : ``}"></i></div>`)}
                                </td>
                                <td>
                                    <div class="text-center">${(papre.Incompleto === 1 ? `<i class="ri-check-line"></i>` : `-`)}</div>
                                </td>
                                <td>
                                    <div class="text-center">${(papre.Ausente === 1 ? `<i class="ri-check-line"></i>` : `-`)}</div>
                                </td>
                                <td>
                                    <div class="text-center">${(papre.Forma === 1 ? `<i class="ri-check-line"></i>` : `-`)}</div>
                                </td>
                                <td>
                                    <div class="text-center">${(papre.Devol === 1 ? `<i class="ri-check-line"></i>` : `-`)}</div>
                                </td>
                                <td>
                                    ${(papre.Pago == "B" 
                                        ? 'Ctdo.' 
                                        : papre.Pago == "C" 
                                            ? "Ctdo" 
                                            : papre.Pago == "P" 
                                                ? "ExCta" 
                                                : "CC")}
                                </td>
                                <td>
                                   <div class="text-center"> ${(papre.Facturado === 1 ? `<i class="ri-check-line"></i>` : `-`)}</div>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a title="Editar" href="${url.replace('__prestacion__', papre.Id)}">
                                            <button type="button" class="btn btn-sm iconGeneralNegro"><i class="ri-edit-line"></i></button>
                                        </a>
                                        <div class="bloquear">
                                            <button type="button" id="blockPrestPaciente" data-idprest="${papre.Id}" class="btn btn-sm iconGeneralNegro" title="${(papre.Anulado == 1 ? "Bloqueado" : "Bloquear")}" ${(papre.Anulado == 1 ? "disabled" : "")}><i class="ri-forbid-2-line"></i></button>
                                        </div>
                                        <button type="button" id="downPrestPaciente" data-idprest="${papre.Id}"class="btn btn-sm iconGeneralNegro"><i class="ri-delete-bin-2-line"></i></button>
                                    </div>
                                </td>
                            </tr>`;


                    $('#listaPacientes tbody').append(row);
                });
            }
        });
    }
    
    function comprobarEstado(cerrado, finalizado, entregado) {

        return (cerrado === 1 && finalizado === 0 && entregado === 0)
        ? "Cerrado"
        : (cerrado === 1 && finalizado === 1 && entregado === 0)
            ? "Finalizado"
            : (cerrado === 1 && finalizado === 1 && entregado === 1)
                ? "Entregado"
                : (cerrado === 0 && finalizado === 0 && entregado === 0)
                    ? "Abierto"
                    : "-"
    }

    async function getUltimasFacturadas(id) {

        $('#grillaFacturadas').empty();
        preloader('on');
        $.get(lstFacturadas, {Id: id})
            .done(async function(response){
                preloader('off');
                let promises = response.map(async function(r){
                    let listadoResultado = await lstFacturados(r.NroPrestacion);
                    
                    // Divide el texto en trozos de 147 caracteres
                    let resultadoDividido = listadoResultado.match(/.{1,147}/g);
                    
                    // Une los trozos con un salto de línea
                    let resultadoFormateado = resultadoDividido.join('<br>');
                    
                    let contenido = `
                        <tr>
                            <td><span class="rojo fw-bolder">${r.NroPrestacion}</span> - ${r.TipoPrestacion} - ${r.Apellido} ${r.Nombre}</td>
                        </tr>
                        <tr class="borde-inferior">
                            <td class="text-break small">${resultadoFormateado}</td>
                        </tr>
                    `;
                    return contenido;
                });
    
                let mostrar = await Promise.all(promises);
                mostrar.forEach(ver => $('#grillaFacturadas').append(ver));
            });
    }
    

    async function lstFacturados(id)
    {
        return new Promise((resolve, reject) => {
            preloader('on');
            $.get(lstExamenes, {Id: id})
                .done(async function(response){
                    preloader('off');
                    let result = '';
                    if(response && response.length) {

                        for (let r of response) {
                            result += r.NombreExamen + ' - ';
                        }
                        
                    }else{
                        result = "No hay examenes";
                    }
                    resolve(result);
                })
                .fail(function(error){
                    reject(error);
                })
        });
    }

    function listadoConSaldos(id) //Desde financiador tomo empresa
    {
        $('#disponiblesExamenes').empty();
     
        preloader('on');
        $.get(saldoNoDatatable, {Id: id})
            .done(function(response){
                preloader('off');

                for(let index = 0; index < response.length; index++) {
                    let r = response[index],
                    contenido = `
                        <tr>
                            <td>${r.contadorSaldos}</td>
                            <td>${r.Examen}</td>
                        </tr>
                    `;
                    $('#disponiblesExamenes').append(contenido);
                }
            });
    }

    async function cantidadDisponibles(id)
    {
        $('#totalCantidad').empty();

        $.get(cantTotalDisponibles, {Id: id})
            .done(await function(response){
                $('#totalCantidad').text(response);
                if(response === 0) {
                    $('#siguienteExCta').hide();
                    $('#guardarPrestacion').show();
                }else if(response > 0) {
                    $('#siguienteExCta').show();
                    $('#guardarPrestacion').hide();
                }
            })
    }

    function listadoFacturas(id, idexamen, checkVisible, etiquetaId) {
        //$('#lstEx').empty();
        $(etiquetaId).empty();
        preloader('on');
    
        $.get(lstExClientes, { Id: id })
            .done(async function(response) {
                preloader('off');
                if (response && response.length) {
                    // Utiliza Promise.all para manejar todas las promesas a la vez
                    let promises = response.map(async function(r) {
                        let suc = [null, 0, undefined, ''].includes(r.Suc) ? '' : (r.Suc ? r.Suc.toString().padStart(4, '0') : '-');
                        let numero = [null, 0, undefined, ''].includes(r.Nro) ? '' : (r.Nro ? r.Nro.toString().padStart(8, '0') : '-');
                        let moduloResult = await vistaDni(r.Id, idexamen, checkVisible);
                        
                        return `
                        <tr class="fondo-gris">
                            <td colspan="6">
                                <span class="fw-bolder text-capitalize">fact </span> ${r.Tipo ?? '-'}${suc}-${numero}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5">
                                <span class="fw-bolder text-capitalize">Observación: </span><span>${r.Obs}</span>
                            </td>
                        </tr>
                        ${moduloResult}
                        `;
                    });
    
                    let contents = await Promise.all(promises);
                    $(etiquetaId).append(contents.join(''));
                } else {
                    $(etiquetaId).append('<tr><td>No hay historial de facturas disponible</td></tr>');
                }
            })
            .fail(function(error) {
                preloader('off');
                console.error('Error fetching facturas:', error);
            });
    }
    
    async function vistaDni(id, idexamen, checkVisible) {
        return new Promise((resolve, reject) => {
            preloader('on');
    
            $.get(listPrecarga, { Id: id, IdExamen: idexamen })
                .done(async function(response) {
                    preloader('off');
                    if (response && response.length) {
                        let promises = response.map(async function(r) {
                            let detallesResult = await detalles(r.Documento, r.IdPago, checkVisible);
                            return `
                            <tr class="fondo-grisClaro mb-2">
                                <td colspan="5" class="fw-bolder"><span class="fw-bolder">${[0, ''].includes(r.Documento) ? '' : 'DNI Precargado: '}</span> ${[0, ''].includes(r.Documento) ? 'Sin precarga' : r.Documento}</td>          
                                ${detallesResult}
                            </tr>
                            `;
                        });
    
                        let result = await Promise.all(promises);
                        resolve(result.join(''));
                    } else {
                        resolve('<tr><td>No hay detalles disponibles</td></tr>');
                    }
                })
                .fail(function(error) {
                    preloader('off');
                    reject(error);
                });
        });
    }
    
    async function detalles(documento, idpago, checkVisible) {
        return new Promise((resolve, reject) => {
            preloader('on');
            $.get(listExCta, { Id: documento, IdPago: idpago })
                .done(async function(response) {
                    preloader('off');
                    if (response && response.length) {
                        let result = response.map(r => `
                        <tr>  
                            <td>${r.Cantidad}</td>
                            <td>${r.NombreExamen}</td>
                            ${checkVisible === true ? `<td style="width:5px"><input type="checkbox" class="form-check-input" value="${r.IdEx}"></td>` : ''}
                        </tr>
                        `).join('');
                        resolve(result);
                    } else {
                        resolve('<tr><td>No hay detalles disponibles</td></tr>');
                    }
                })
                .fail(function(error) {
                    preloader('off');
                    reject(error);
                });
        });
    }


    /******************************Nueva pantalla de prestaciones **************************************/
    
    $('.prestacionLimpia, .resultadosPaciente, .reportesPacientes').hide();

    $(document).on('click', '.cargarExPrestacion, #guardarPrestacion', function(e){
        e.preventDefault();
        cargarExamen($('#idPrestacion').val());
        contadorExamenes($('#idPrestacion').val());
        $('#listaExamenes').empty();
        
    });

    $(document).on('click', '.resulPaciente', function(e){
        e.preventDefault();
        $('.prestacionLimpia, .reportesPacientes,  .editarComentario').hide();
        $('.resultadosPaciente').show();
    });

    $(document).on('click', '.volverPrestacionLimpia', function(e){
        e.preventDefault();
        $('.resultadosPaciente, .reportesPacientes, .editarComentario, .seleccionExCta').hide();
        $('.prestacionLimpia').show();
        $('#ComentarioEditar').val('');
    });

    $(document).on('click', '.imprimirReportes', function(e){
        e.preventDefault();
        $('.resultadosPaciente, .prestacionLimpia, .editarComentario').hide();
        $('.reportesPacientes').show();
    }); 

    $(document).on('click', '.editarComentarioBtn', async function(e){
        e.preventDefault();

        let id = $(this).data('id'), data  = await await $.get(getComentario,{Id: id});
        $('.resultadosPaciente, .prestacionLimpia, .reportesPacientes').hide();
        $('.editarComentario').show();
        $('#ComentarioEditar').empty().val(data[0].Comentario);
        $('#IdObservacion').empty().val(data[0].Id);
    });

    $(document).on('click', '.imprimirRepo', function(e){

        let idP = $('#idPrestacion').val(),
            infInternos = $('#infInternos').prop('checked'),
            pedProveedores = $('#pedProveedores').prop('checked'),
            conPaciente = $('#conPaciente').prop('checked'),
            resAdmin = $('#resAdmin').prop('checked'),
            caratula = $('#caratula').prop('checked'),
            consEstDetallado = $('#consEstDetallado').prop('checked'),
            consEstSimple = $('#consEstSimple').prop('checked');

        let verificar = [
            infInternos,
            pedProveedores,
            conPaciente,
            resAdmin,
            caratula,
            consEstDetallado,
            consEstSimple
        ];

        if (verificar.every(val => !val || (Array.isArray(val) && val.length === 0) || (typeof val === 'object' && Object.keys(val).length === 0))) {
            toastr.warning('Debe seleccionar alguna opción para poder imprimir los reportes','',{timeOut: 1000});
            return;
        }
        preloader('on');
        $.get(impRepo, {Id: idP, infInternos: infInternos, pedProveedores: pedProveedores, conPaciente: conPaciente, resAdmin: resAdmin, caratula: caratula, consEstDetallado: consEstDetallado, consEstSimple: consEstSimple})
            .done(function(response){
                preloader('off');
                createFile("pdf", response.filePath, response.name);
                toastr.success(response.msg,'',{timeOut: 1000});
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return; 
            });
    });


    $('#exam').select2({
        dropdownParent: $('#altaPrestacionModal'),
        placeholder: 'Seleccionar exámen...',
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
        allowClear: true,
        ajax: {
           url: searchExamen,
           dataType: 'json',
           delay: 250,
           data: function(params) {
                return {
                    buscar: params.term,
                };
           },
           processResults: function(data) {
                return {
                    results: data.examen
                };
           },
           cache: true,
        },
        minimumInputLength: 2
    });

    $('#paquetes').select2({
        dropdownParent: $('#altaPrestacionModal'),
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

    $(document).on("select2:open", () => {
        document.querySelector(".select2-container--open .select2-search__field").focus()
    });

    $(document).on('click', '.addPaquete', function(e){
        e.preventDefault();
        
        let paquete = $('#paquetes').val();
        
        if([null, undefined, ''].includes(paquete)){
            toastr.warning("Debe seleccionar un paquete para poder añadirlo en su totalidad",'',{timeOut: 1000});
            return;
        }
        preloader('on');
       $.ajax({
            url: paqueteId,
            type: 'POST',
            data: {
                _token: TOKEN,
                IdPaquete: paquete,
            },

            success:function(response){

                preloader('off');
                let data = response.examenes,
                    ids = [];
 
                for (let i = 0; i < data.length; i++) {
                    ids.push(data[i].Id);
                }
                saveExamen(ids, $('#idPrestacion').val());  
                $('.addPaquete').val([]).trigger('change.select2');
            },
            error: function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return; 
            }
       });
       
    });

    $(document).on('click', '.deleteExamenes, .deleteExamen', function(e){
        e.preventDefault();

        let ids = [], tieneAdjunto = false, id = $(this).data('delete'), adjunto, archivos; checkAll ='';
        if ($(this).hasClass('deleteExamenes')) {

            $('input[name="Id_examenes"]:checked').each(function() {
                adjunto = $(this).data('adjunto'), archivos = $(this).data('archivo');
                adjunto == 1 && archivos > 0 ? tieneAdjunto = true : ids.push($(this).val());
            });
            checkAll = $('#checkAllExa').prop('checked');

        } else if($(this).hasClass('deleteExamen')) {

            adjunto = $(this).data('adjunto'), archivos = $(this).data('archivo');
            adjunto == 1 && archivos > 0 ? tieneAdjunto = true : ids.push(id);
        }

        if (tieneAdjunto) {
            toastr.warning('El o los examenes seleccionados tienen un reporte adjuntado. El mismo no se podrá eliminar.', 'Atención', { timeOut: 10000 });
            return;
        }

        if(ids.length === 0 && checkAll === false){
            toastr.warning('No hay examenes seleccionados', 'Atención', { timeOut: 10000 });
            return;
        }  
    
        swal({
            title: "Confirme la eliminación de los examenes",
            icon: "warning",
            buttons: ["Cancelar", "Eliminar"],
        }).then((confirmar) => {
            if (confirmar){

                preloader('on');
                $.ajax({
                    url: deleteItemExamen,
                    type: 'POST',
                    data: {
                        Id: ids,
                        _token: TOKEN
                    },
                    success: function(response){
                        preloader('off');
                        let estados = [];
                        
                        for (let i = 0; i < response.length; i++) {
                            let msg = response[i];
                            let tipoRespuesta = {
                                success: 'success',
                                fail: 'info'
                            };
                            toastr[tipoRespuesta[msg.estado]](msg.message, "Atención", { timeOut: 10000 });
                            estados.push(msg.estado);
                        }

                        if(estados.includes('success')) {
                            $('#listaExamenes').empty();
                            $('#exam').val([]).trigger('change.select2');
                            $('#addPaquete').val([]).trigger('change.select2');
                            cargarExamen(IdNueva);
                            contadorExamenes(IdNueva)
                        }

                    }
                });
            }
            
        });
    });

    $(document).on('click', '.addExamen', function(e){
        e.preventDefault();

        let id = $("#exam").val();
        
        if(['', null, undefined].includes(id)) {
            toastr.warning("Debe seleccionar un examen para poder añadirlo a la lista", "Atención", { timeOut: 1000 });
            return;
        }
        saveExamen(id, $('#idPrestacion').val());
    });

    $('#altaPrestacionModal').on('hidden.bs.modal', function () {
        $('.prestacionLimpia, .observacionesModal, .nuevaPrestacion, .reportesPacientes').hide();
        $('.fichaLaboralModal').show();
        checkExamenesCuenta(IDFICHA);
    });

    $('#checkAllExa').on('click', function() {
        $('input[type="checkbox"][name="Id_examenes"]:not(#checkAllExa)').prop('checked', this.checked);
    });

    function saveExamen(id, idPrestacion){

        idExamen = [];
        if (Array.isArray(id)) {
              for(let i = 0; i < id.length; i++){
                idExamen.push(id[i]);
              }
        }else{
            idExamen.push(id);
        }

        if (idExamen.length === 0) {
            toastr.warning("No existe el exámen o el paquete no contiene examenes", "Atención", { timeOut: 1000 });
            return;
        }
        preloader('on');
        $.ajax({

            url: saveItemExamenes,
            type: 'post',
            data: {
                _token: TOKEN,
                idPrestacion: idPrestacion,
                idExamen: idExamen
            },
            success: function(){
                preloader('off');
                $('#listaExamenes').empty();
                $('#exam').val([]).trigger('change.select2');
                $('#addPaquete').val([]).trigger('change.select2');
                cargarExamen(idPrestacion);
                contadorExamenes(idPrestacion);
        },
            error: function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;  
            }
        });
    }
    
    $(document).on('click', '#finalizarWizzard', function(e){
        e.preventDefault();

        let idP = $('#idPrestacion').val(),
            ObservacionesPres = $('#ObservacionesPresN').val(),
            ObsExamenes = $('#ObsExamenesN').val(),
            Obs = $('#ObsN').val();

        preloader('on');
        $.post(obsNuevaPrestacion, {_token: TOKEN, IdPrestacion: idP, Observaciones: ObservacionesPres, ObsExamenes: ObsExamenes, Obs: Obs})
            .done(function(response){
                preloader('off');

                toastr.success(response.msg,'',{timeOut: 1000});
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;  
            });
    });
    

    $(document).on('click', '#SalirWizzard', function(e){
        e.preventDefault();
        location.reload();
    });

    async function cargarExamen(id) {
        try {
            preloader('on');

            let result = await $.ajax({
                url: checkItemExamen,
                method: 'GET',
                data: { Id: id }
            });

            let estado = result.respuesta, examenes = result.examenes;
            
            if (estado === true) {

                let response = await $.ajax({
                    url: getItemExamenes,
                    method: 'GET',
                    data: {
                        IdExamen: examenes,
                        Id: id,
                        tipo: 'listado'
                    }
                });
    
                preloader('off');
                let registros = response;

                for(let index = 0; index < registros.length; index++){
                    let examen = registros[index],
                    fila = `
                        <tr ${examen.Anulado === 1 ? 'class="filaBaja"' : ''}>
                            <td>
                                <input type="checkbox" name="Id_examenes" value="${examen.IdItem}" checked ${examen.Anulado === 1 ? 'disabled' : ''}>
                            </td>
                            <td data-idexam="${examen.IdExamen}" id="${examen.IdItem}" style="text-align:left;">${examen.Nombre} ${examen.Anulado === 1 ? '<span class="custom-badge rojo">Bloqueado</span>' : ''}</td>
                            <td style="width: 100px">    
                                <div class="d-flex gap-2">
                                     <div class="bloquear">
                                        <button data-id="${examen.IdItem}" class="btn btn-sm iconGeneral openExamen" title="Ver">
                                            <i class="ri-zoom-in-line"></i>
                                        </button>
                                    </div>
                                    ${examen.Anulado === 0 ? `
                                         <div class="bloquear">
                                                    <button data-bloquear="${examen.IdItem}" class="btn btn-sm iconGeneral bloquearExamen" title="Baja">
                                                        <i class="ri-forbid-2-line"></i>
                                                    </button>
                                                </div>
                                    ` : ''}
                                    <div class="remove">
                                        <button data-delete="${examen.IdItem}" class="btn btn-sm iconGeneral deleteExamen" title="Eliminar">
                                            <i class="ri-delete-bin-2-line"></i>
                                        </button>
                                    </div>  
                                    
                                </div>
                            </td>
                        </tr>`;
    
                    $('#listaExamenes').append(fila);
                }
    
                $("#listado").fancyTable({
                    pagination: true,
                    perPage: 50,
                    searchable: false,
                    globalSearch: false,
                    sortable: false, 
                });
            }
            
    
        } catch (error) {
            preloader('off');
            let errorData = JSON.parse(error.responseText);
            checkError(error.status, errorData.msg);
        } finally {
            preloader('off');
        }
    }

    $(document).on('click', '.openExamen', function(e){
        e.preventDefault();

        let id = $(this).data('id'),
            url = location.href,
            open = url.replace(/\/pacientes\/.*/, ''),
            redireccionar = open + '/itemsprestaciones/' + id + '/edit';

        window.open(redireccionar, '_blank');
    });

    $(document).on('click', '.bloquearExamenes, .bloquearExamen', function(e){

        e.preventDefault();

        let ids = [], id = $(this).data('bloquear'), checkAll ='';

        if ($(this).hasClass('bloquearExamenes')) {

            $('input[name="Id_examenes"]:checked').each(function() {
                ids.push($(this).val());
            });
    
            checkAll =$('#checkAllExa').prop('checked');
        
        }else if($(this).hasClass('bloquearExamen')){
            ids.push(id);
        }

        if(ids.length === 0 && checkAll === false){
            toastr.warning('No hay examenes seleccionados', 'Atención', { timeOut: 10000 });
            return;
        }
    
        swal({
            title: "Confirme el bloqueo de los examenes",
            icon: "warning",
            buttons: ["Cancelar", "Bloquear"],
        }).then((confirmar) => {
            if(confirmar){
                preloader('on');
                $.ajax({    
                    url: bloquearItemExamen,
                    type: 'POST',
                    data: {
                        Id: ids,
                        _token: TOKEN
                    },
                    success: function(response){
                        let estados = [];
                        preloader('off')

                        for(let index = 0; index < response.length; index++) {
                            let msg = response[index],
                                tipoRespuesta = {
                                    success: 'success',
                                    fail: 'info'
                                }
                            toastr[tipoRespuesta[msg.estado]](msg.message, "Atención", { timeOut: 10000 })
                            estados.push(msg.estado)
                        }
                        
                        if(estados.includes('success')) {
                            $('#listaExamenes').empty();
                            $('#exam').val([]).trigger('change.select2');
                            $('#addPaquete').val([]).trigger('change.select2');
                            cargarExamen(IdNueva);
                            contadorExamenes(IdNueva);
                        }
                    }
                });
            }
        });     
    });
    

    async function checkExamenesCuenta(id){

        $.get(lstExDisponibles, {Id: id})
            .done(await function(response){
                let data = selectorPago(pagoInput);

                if(response && response.length > 0) {

                    $('#alertaExCta, .examenesDisponibles, .ultimasFacturadas, #siguienteExCta').show();
                    $('#PagoLaboral, #Pago ').val('P');
                    $('#guardarPrestacion').hide();
                } else {

                    $('examenesDisponibles, .ultimasFacturadas, #siguienteExCta, #alertaExCta').hide();
                    $('#PagoLaboral').val(data);
                    $('#guardarPrestacion').show();
                }
            })
    }

    $(document).on('click', '.confirmarComentarioPriv', function(e){
        e.preventDefault();
        let comentario = $('#Comentario').val(), idp =  $('#idPrestacion').val();

        if(comentario === ''){
            toastr.warning('La observación no puede estar vacía','',{timeOut: 1000});
            return;
        }

        preloader('on');
        $.post(savePrivComent, {_token: TOKEN, Comentario: comentario, IdEntidad: idp, obsfasesid: 2})
            .done(function(){
                preloader('off');
                toastr.success('Se ha generado la observación correctamente','',{timeOut: 1000});

                setTimeout(() => {
                    $('#privadoPrestaciones').empty();
                    $("#Comentario").val("");
                    comentariosPrivados();
                }, 3000);
            })
    });

    $(document).on('click', '.deleteComentario', function(e){
        e.preventDefault;

        let id = $(this).data('id');

        swal({
            title: "¿Está seguro que desea eliminar el comentario privado?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {

                preloader('on')
                $.get(eliminarComentario, {Id: id})
                    .done(function(response){
                        preloader('off');
                        toastr.success(response.msg,'',{timeOut: 1000});
                        comentariosPrivados();
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

    
    $(document).on('click', '.confirmarEdicion', function(e){
        e.preventDefault();

        let id = $('#IdObservacion').val(), comentario = $('#ComentarioEditar').val();

        preloader('on')
        $.get(editarComentario, {Id: id, Comentario: comentario})
            .done(function(response){
                $('.volverPrestacionLimpia').trigger('click');
                $('#comentarioPrivado').modal('hide');
                preloader('off')
                toastr.success(response.msg,'',{timeOut: 1000});
                comentariosPrivados();
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            });
    });


    async function comentariosPrivados(){

        $('#privadoPrestaciones').empty();
        preloader('on');
        let idp =  $('#idPrestacion').val();
        $.get(await privateComment, {Id: idp,  tipo: 'prestacion'})
            .done(function(response){
                preloader('off');
                let data = response.result;

                for(let index = 0; index < data.length; index++){
                    let d = data[index],
                        contenido =  `
                            <tr>
                                <td style="width: 120px">${fechaCompleta(d.Fecha)}</td>
                                <td style="width: 120px" class="text-capitalize">${d.IdUsuario}</td>
                                <td style="width: 120px" class="text-uppercase">${d.nombre_perfil}</td>
                                <td>${d.Comentario}</td>
                                <td style="width: 60px">${USER === d.IdUsuario ? `
                                    <button type="button" data-id="${d.Id}" data-comentario="${d.Comentario}" class="btn btn-sm iconGeneralNegro editarComentarioBtn"><i class="ri-edit-line"></i></button>
                                    <button title="Eliminar" type="button" data-id="${d.Id}"  class="btn btn-sm iconGeneralNegro deleteComentario"><i class="ri-delete-bin-2-line"></i></button>` : ''}</td>
                            </tr>
                        `;
                        $('#privadoPrestaciones').append(contenido);
                }

                $('#lstPrivPrestaciones').fancyTable({
                    pagination: true,
                    perPage: 15,
                    searchable: false,
                    globalSearch: false,
                    sortable: false, 
                });
            })   
    }

    function borrarCache() {
        $.post(cacheDelete, {_token: TOKEN}, function(){});
    }

    function contadorExamenes(idPrestacion) {
        $.get(contadorEx, {Id: idPrestacion}, function(response){
            $('#countExamenes').empty().text(response);
        });
    }

});