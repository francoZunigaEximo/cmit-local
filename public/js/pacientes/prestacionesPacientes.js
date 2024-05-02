$(document).ready(()=>{
    
    var empresaInput = $('#selectClientes').val(), artInput = $('#selectArt').val();
    let hoy = new Date().toISOString().slice(0, 10), precarga = $('#tipoPrestacionPres').val();
    $('#Fecha').val(hoy);

    $('#siguienteExCta, .seleccionExCta').hide();

    precargaTipoPrestacion(precarga);
    getMap(empresaInput, artInput);
    getListado(null);
    listadoConSaldos(empresaInput);
    cantidadDisponibles(empresaInput);
    listadoFacturas(empresaInput, null);
    
    toastr.options = {
        closeButton: true,   
        progressBar: true,    
        timeOut: 3000,        
    };

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

    $(document).on('change', '#financiador', function(){
        let updateFinanciador = $('#financiador').val();
        getUltimasFacturadas(updateFinanciador);
        listadoConSaldos(updateFinanciador);
        cantidadDisponibles(updateFinanciador);
        listadoFacturas(updateFinanciador, null);
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
            pago = $('#Pago').val(),
            spago = $('#SPago').val(),
            observaciones = $('#Observaciones').val(),
            autorizado = $('#Autorizado').val(),
            tipo = $('#Tipo').val();
            sucursal = $('#Sucursal').val();
            nroFactura = $('#NroFactura').val(),
            financiador = $('#financiador').val();

        //Validamos la factura
        if (spago === 'G' && autorizado === ''){
            toastr.warning('Si el medio de pago es gratuito, debe seleccionar quien autoriza.', 'Alerta');
            return;
        }

        if (['', null].includes(tipoPrestacion)) {
            toastr.warning('El tipo de prestación no puede ser un campo vacío', 'Alerta');
            return;
        }

        if (tipoPrestacion === 'ART' && (mapa == '0' || mapa === '')){
            toastr.warning('Debe seleccionar un mapa vigente para continuar si su prestacion es ART', 'Alerta');
            return;
        }

        if (pago === 'B' && spago === '') {
            toastr.warning('Debe seleccionar un "medio de pago" cuando la "forma de pago" es "contado"', 'Alerta');
            return;
        }

        if (['',null,undefined].includes(pago)) {
            toastr.warning('Debe seleccionar una "forma de pago"', 'Alerta');
            return;
        }

        if (pago === 'B' && (tipo == '' || sucursal === '' || nroFactura === '')){
            toastr.warning('El pago es contado, asi que debe agregar el número de factura para continuar.', 'Alerta');
            return;
        }

        if (financiador === ''){
            toastr.warning('El campo financiador es obligatorio', 'Alerta');
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
                        financiador: financiador,
                        IdART: clienteArt.Id ?? 0,
                        IdEmpresa: cliente.Id ?? 0,
                        examenCuenta: ids,
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
                    error: function(xhr){
                        
                        toastr.error('No se puedieron almacenar los datos. Consulte con el administrador', 'Error');
                        console.error(xhr);
                    }
                });
            },
            error: function(xhr){
                toastr.error('Ha habido un error el la validación del cliente para generar la prestación. Consulte con su administrador', 'Error');
                console.error(xhr);
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
    $(document).on('click', '#blockPrestPaciente', function() {
        let Id = $(this).data('idprest');

        $.ajax({
            url: blockPrestacion,
            type: 'GET',
            data: {
                Id: Id,
            },
            success: function() {
                toastr.success('Se ha bloqueado la prestación del paciente de manera correcta. Puede que tarde unos minutos en cargar el cambio.', 'Bloqueo procesado');
                cambioEstadoBlock();
            },
            error: function(xhr) {
                toastr.error('No se ha podido bloquear la prestación. Consulte con el administrador');
                console.error(xhr);
            }
        });
        
        cambioEstadoBlock();      
    });

    //Baja logica de prestación
    $(document).on('click', '#downPrestPaciente', function() {
        let Id = $(this).data('idprest');

        $.ajax({
            url: downPrestaActiva,
            type: 'GET',
            data: {
                Id: Id,
            },
            success: function() {
                toastr.success('Se ha dado de baja la prestación del paciente de manera correcta. Puede que tarde unos minutos en cargar el cambio.');
                cambioEstadoDown();
                getListado(null);
            },
            error: function(xhr){
                toastr.error('No se ha podido dar de baja la prestación. Consulte con el administrador');
                console.error(xhr);
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

    $(document).on('change', '#Pago', function(){

        let pago = $(this).val();
        if (pago != 'B') {
            $('#SPago, #Tipo, #Sucursal, #NroFactura').val(" ");
        }
    });

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
    }

    function getMap(x, y){

        let empresa = x, art = y;

        $.get(getMapas, {empresa: empresa, art: art})
            .done(function(response){

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

                    //Acortadores
                    let prestacionArt = papre.Art,
                        prestacionRz = papre.Empresa,
                        prestacionPe = papre.ParaEmpresa;

                    let recorteArt = prestacionArt.substring(0, 10),
                        recorteRz = prestacionRz.substring(0,10),
                        recortePe = prestacionPe.substring(0,10);

                    let cerradoAdjunto = papre.CerradoAdjunto || 0,
                        total = papre.Total || 1,
                        calculo = parseFloat(((cerradoAdjunto / total) * 100).toFixed(2)),
                        resultado = (calculo === 100) ? 'fondo-blanco' : (calculo >= 86 && calculo <= 99) ? 'fondo-verde' : (calculo >= 51 && calculo <= 85) ? 'fondo-amarillo' : (calculo >= 1 && calculo <= 50) ? 'fondo-naranja' : 'fondo-rojo';

                    let row = `<tr class="${resultado}">
                                <td>
                                    <input type="checkbox" name="Id" value=${papre.Id} checked="">
                                </td>
                                <td>
                                    ${parseFloat(((cerradoAdjunto / total) * 100).toFixed(2)) + `%`}
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
                                <td title="${papre.RazonSocial}">
                                    ${recorteRz}
                                </td>
                                <td title="${papre.ParaEmpresa}">
                                    ${recortePe}
                                </td>
                                <td title="${papre.Art}">
                                    ${recorteArt}
                                </td>
                                <td>
                                    <span class="text-uppercase">${ (papre.Anulado == 0 ? "Habilitado" : "Bloqueado")}</span>
                                </td>
                                <td>
                                    ${(`<div class="text-center"><i class="${papre.eEnviado === 1 ? `ri-checkbox-circle-fill negro` : `ri-close-circle-line negro`}"></i></div>`)}
                                </td>
                                <td>
                                    ${(papre.Incompleto === 1 ? `<div class="text-center"><i class="ri-check-line"></i></div>` : `-`)}
                                </td>
                                <td>
                                    ${(papre.Ausente === 1 ? `<div class="text-center"><i class="ri-check-line"></i></div>` : `-`)}
                                </td>
                                <td>
                                    ${(papre.Forma === 1 ? `<div class="text-center"><i class="ri-check-line"></i></div>` : `-`)}
                                </td>
                                <td>
                                    ${(papre.Devol === 1 ? `<div class="text-center"><i class="ri-check-line"></i></div>` : `-`)}
                                </td>
                                <td>
                                    ${(papre.Pago == "B" ? 'Ctdo.' : papre.Pago == "C" ? "CCorriente" : papre.Pago == "C" ? "PCuenta" : "CCorriente")}
                                </td>
                                <td>
                                    ${(papre.Facturado === 1 ? `<div class="text-center"><i class="ri-check-line"></i></div>` : `-`)}
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

    async function listadoFacturas(id, idexamen){
        $('#lstEx').empty();
        preloader('on');

        $.get(lstExClientes, {Id: id})
            .done(async function(response){
                preloader('off');
                let promises = response.map(async function(r) {
                    if(response && response.length) {
                        let suc = r.Suc ? r.Suc.toString().padStart(4, '0') : '-', numero = r.Nro ? r.Nro.toString().padStart(8, '0') : '-', moduloResult = await vistaDni(r.Id,idexamen);
                        let contenido = `
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
                        
                        return contenido;
                    }else{
                        return '<tr class="mb-2"><td>No hay historial de facturas disponible</td></tr>';
                    } 
                });
                let contents = await Promise.all(promises);
                contents.forEach(content => $('#lstEx').append(content));
            });
    }

    async function vistaDni(id,idexamen) {
        return new Promise((resolve, reject) => {
            preloader('on');

            $.get(listPrecarga, {Id: id, IdExamen: idexamen})
                .done(async function(response){
                    preloader('off');
                    
                    if (response && response.length) {
                        let result = '';
                        for (let r of response) {
                            detallesResult = await detalles(r.Documento, r.IdPago); 
                            result += `
                            <tr class="mb-1">   
                                <tr class="fondo-grisClaro mb-2">
                                    <td colspan="4" class="fw-bolder"><span class="fw-bolder">${[0,''].includes(r.Documento) ? '' : 'DNI Precargado: '}</span> ${[0,''].includes(r.Documento) ? 'Sin precarga' : r.Documento}</td>          
                                    ${detallesResult}
                                </tr>
                            </tr>
                            `;
                        }
                        resolve(result);
                    }
                })
                .fail(function(error){
                    reject(error);
                });
        });
    }

    async function detalles(documento, idpago) {
        return new Promise((resolve, reject) => {
            preloader('on');
            $.get(listExCta, {Id: documento, IdPago: idpago})
                .done(async function(response){
                    preloader('off');
                    if (response && response.length) {
                        let result =  ``;
                        for (let r of response) {
                            result += `
                            <tr>  
                                <td>${r.Cantidad}</td>
                                <td>${r.NombreExamen}</td>
                                <td><input type="checkbox" class="form-check-input" value="${r.IdEx}"></td>
                            </tr>
                            `;
                        }
                        resolve(result);
                    }
                })
                .fail(function(error){
                    reject(error);
                });
        });
        
    }

    function preloader(opcion) {
        $('#preloader').css({
            opacity: '0.3',
            visibility: opcion === 'on' ? 'visible' : 'hidden'
        });
    }
    


});