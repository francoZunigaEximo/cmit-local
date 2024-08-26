$(document).ready(()=>{
    
    var empresaInput = $('#selectClientes').val(), artInput = $('#selectArt').val();
    let hoy = new Date().toLocaleDateString('en-CA'), precarga = $('#tipoPrestacionPres').val();
    $('#Fecha').val(hoy);

    $('#siguienteExCta, .seleccionExCta').hide();
    precargaTipoPrestacion(precarga);
    getMap(empresaInput, artInput);
    getListado(null);
    listadoConSaldos(empresaInput);
    cantidadDisponibles(empresaInput);
    listadoFacturas(empresaInput, null);
    getUltimasFacturadas(empresaInput);
    selectorPago(pagoInput);

    $(document).on('change', '#tipoPrestacionPres', function(){
        let actPrecarga = $('#tipoPrestacionPres').val();
        precargaTipoPrestacion(actPrecarga);
    });

    $(document).on('change', '#selectClientes, #selectArt', function(){
        let empresaCap = $('#selectClientes').val();
        let artCap = $('#selectArt').val();
        
        getMap(empresaCap, artCap);
        getUltimasFacturadas(empresaCap);
        listadoConSaldos(empresaCap);
        cantidadDisponibles(empresaCap);
        listadoFacturas(empresaCap, null);
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
                toastr.warning("Debe seleccionar un examen para registrar la prestación");
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
            autorizado = $('#Autorizado').val(),
            tipo = $('#Tipo').val();
            sucursal = $('#Sucursal').val();
            nroFactura = $('#NroFactura').val(),
            NroFactProv = $('#NroFactProv').val();
            
        if (['', null].includes(tipoPrestacion)) {
            toastr.warning('El tipo de prestación no puede ser un campo vacío');
            return;
        }

        if (tipoPrestacion === 'ART' && (mapa == '0' || mapa === '')){
            toastr.warning('Debe seleccionar un mapa vigente para continuar si su prestacion es ART');
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
                let cliente = response.cliente;
                let clienteArt = response.clienteArt;
                
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
                        
                        let nuevoId = response.nuevoId;
                        toastr.success('Se ha generado la prestación del paciente. Se redireccionará en 3 segundos a edición de prestaciones.', '¡Alta exitosa!');
                        $('#Pago, #SPago, #Observaciones, #NumeroFacturaVta, #Autorizado').val("");
                        setTimeout(function(){
                            let url = location.href,
                                clearUrl = url.replace(/\/pacientes\/.*/, ''),
                                redireccionar =  clearUrl + '/prestaciones/' + nuevoId + '/edit';
                            window.location.href = redireccionar;
                        },3000);
                        
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
    $('#buscarPrestPaciente').on('keypress', function(event){

        if (event.keyCode === 13){
            event.preventDefault();
            
            let buscar = $(this).val();
            $('#grillaPacientes').empty();
            $('#grillaPacientes').append(getListado(buscar));
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
                        toastr.success(response.msg);
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
                        toastr.success(response.msg);
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

    $(document).on('keydown', function(event) {
        if (event.keyCode === 27) {
            event.preventDefault();

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
            
            $('.ultimasFacturadas, .examenesDisponibles').hide();
            $('#siguienteExCta').hide();
            $('#guardarPrestacion').show();
        }else{
            preloader('on');
            $.get(cantTotalDisponibles, {Id: $('#selectClientes').val()})
            .done(function(response){
                preloader('off');
                if(response > 0) {
                    $('.ultimasFacturadas, .examenesDisponibles').show();
                    $('#siguienteExCta').show();
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
            
            toastr.warning('Debe seleccionar un exámen');
            return;
        }

        listadoFacturas(empIn, examen);
        examen.remove();
    });

    $(document).on('click', '.reiniciarExamen', function(e){
        e.preventDefault();

        let empIn = $('#selectClientes').val();

        listadoFacturas(empIn, null);

    });

    $(document).on('click', '.precargaExamenes', function(e){
        e.preventDefault();

        let ids = [], checkAll =$('#checkAllEx').prop('checked');

        $('#lstEx input[type="checkbox"]:checked').each(function(){
            ids.push($(this).val());
        });

        if(ids.length === 0 && checkAll === false) {
            toastr.warning("Debe seleccionar un examen para añadirlo a la prestación");
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
            toastr.warning("Debe seleccionar un exámen para sacarlo del listado");
            return;
        }
    
        ids.each(function() {
            $(this).closest('tr').remove();
        });

        toastr.success("Se elimino todo correctamente");
    });
    

    $('#checkAllEx').on('click', function() {
        $('input[type="checkbox"][name="Id_exa"]:not(#checkAllEx)').prop('checked', this.checked);
    });
  

    function cargaPreExamenes(valor) {
        preloader('on');
        $.get(preExamenes, {Id: valor})
            .done(function(response){
                $.each(response, function(index, r){

                    var existe = false;
                    $('#listEdicion tr').each(function() {
                        var nombreExamen = $(this).find('td:first').text();
                        if (nombreExamen === r.NombreExamen) {
                            preloader('off');
                            existe = true;
                            toastr.warning("No pueden haber dos examenes iguales en una prestación")
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
                });
            })
    }

    function selectorPago(pago) {
        
        if (pago != 'B') {
            $('#SPago, #Tipo, #Sucursal, #NroFactura').val(" ");
        }

        if(['B', ''].includes(pago)) {
            
            $('.ultimasFacturadas, .examenesDisponibles').hide();
            $('#siguienteExCta').hide();
            $('#guardarPrestacion').show();
        }else{
            preloader('on');
            $.get(cantTotalDisponibles, {Id: $('#selectClientes').val()})
            .done(function(response){
                preloader('off');
                if(response > 0) {
                    $('.ultimasFacturadas, .examenesDisponibles').show();
                    $('#siguienteExCta').show();
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

    async function getMap(x, y){

        let empresa = x, art = y;
 
        $.get(getMapas, {empresa: empresa, art: art})
            .done(await function(response){
   
                let mapas = response.mapas;
                $('#mapas').empty().append('<option value="" selected>Elija un mapa...</option>');
                
                if(mapas.length === 0)
                {
                    $('#mapas').empty().append('<option title="Sin mapas disponibles para esta ART y Empresa." value="0" selected>Sin mapas disponibles.</option>');
                }else{

                    $.each(mapas, function(index, d){

                        let contenido = `<option value="${d.Id}">${d.Nro} | Empresa: ${d.RSE} - ART: ${d.RSArt}</option>`;
    
                        $('#mapas').append(contenido);
                    });
                } 
            })
    }

    function precargaTipoPrestacion(val){
        if(val === 'ART'){
            $('.selectMapaPres').show();
        }else if(val !== 'ART'){
            $('.selectMapaPres').hide();
        }
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

                    let row = `<tr>
                                <td>
                                    <input type="checkbox" name="Id" value=${papre.Id} checked="">
                                </td>
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
                                    ${acortadorTexto(papre.Empresa, 10)}
                                </td>
                                <td title="${papre.ParaEmpresa}">
                                    ${acortadorTexto(papre.ParaEmpresa, 10)}
                                </td>
                                <td title="${papre.Art}">
                                    ${acortadorTexto(papre.Art, 10)}
                                </td>
                                <td>
                                    <span>${comprobarEstado(papre.Cerrado, papre.Finalizado, papre.Entregado)}</span>
                                </td>
                                <td>
                                    ${(`<div class="text-center"><i class="${papre.eEnviado === 1 ? `ri-checkbox-circle-fill negro` : `ri-close-circle-line negro`}"></i></div>`)}
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
                                    ${(papre.Pago == "B" ? 'Ctdo.' : papre.Pago == "C" ? "CCorriente" : papre.Pago == "C" ? "PCuenta" : "CCorriente")}
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
                
                $.each(response, function(index, r){
                    let contenido = `
                        <tr>
                            <td>${r.contadorSaldos}</td>
                            <td>${r.Examen}</td>
                        </tr>
                    `;
                    $('#disponiblesExamenes').append(contenido);

                });
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

    function listadoFacturas(id, idexamen) {
        $('#lstEx').empty();
        preloader('on');
    
        $.get(lstExClientes, { Id: id })
            .done(async function(response) {
                preloader('off');
                if (response && response.length) {
                    // Utiliza Promise.all para manejar todas las promesas a la vez
                    let promises = response.map(async function(r) {
                        let suc = [null, 0, undefined, ''].includes(r.Suc) ? '' : (r.Suc ? r.Suc.toString().padStart(4, '0') : '-');
                        let numero = [null, 0, undefined, ''].includes(r.Nro) ? '' : (r.Nro ? r.Nro.toString().padStart(8, '0') : '-');
                        let moduloResult = await vistaDni(r.Id, idexamen);
                        
                        return `
                        <tr class="fondo-gris">
                            <td colspan="3"><span class="fw-bolder text-capitalize">fact </span> ${r.Tipo ?? '-'}${suc}-${numero}</td>
                            <td>
                                <tr>
                                    <td colspan="4">
                                        <span class="fw-bolder text-capitalize">Observación: </span><span>${r.Obs}</span>
                                    </td>
                                </tr>
                            </td> 
                        </tr>
                        ${moduloResult}
                        `;
                    });
    
                    let contents = await Promise.all(promises);
                    $('#lstEx').append(contents.join(''));
                } else {
                    $('#lstEx').append('<tr><td>No hay historial de facturas disponible</td></tr>');
                }
            })
            .fail(function(error) {
                preloader('off');
                console.error('Error fetching facturas:', error);
            });
    }
    
    async function vistaDni(id, idexamen) {
        return new Promise((resolve, reject) => {
            preloader('on');
    
            $.get(listPrecarga, { Id: id, IdExamen: idexamen })
                .done(async function(response) {
                    preloader('off');
                    if (response && response.length) {
                        let promises = response.map(async function(r) {
                            let detallesResult = await detalles(r.Documento, r.IdPago);
                            return `
                            <tr class="fondo-grisClaro mb-2">
                                <td colspan="4" class="fw-bolder"><span class="fw-bolder">${[0, ''].includes(r.Documento) ? '' : 'DNI Precargado: '}</span> ${[0, ''].includes(r.Documento) ? 'Sin precarga' : r.Documento}</td>          
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
    
    async function detalles(documento, idpago) {
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
                            <td><input type="checkbox" class="form-check-input" value="${r.IdEx}"></td>
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
    



});