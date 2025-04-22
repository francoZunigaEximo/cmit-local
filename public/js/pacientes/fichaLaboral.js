$(function () {

    const principal = {
        lstSaldos: $('#lstSaldos'),
        seguirAl: $('.seguirAl'),
        eventDelete: $('.eventDelete'),
        mapas: $('#mapas'),
        mapasN: $('#mapasN'),
        alertaExCta: $('#alertaExCta'),
        SPago: $('.SPago'),
        Autoriza: $('.Autoriza'),
        ObsPres: $('.ObsPres'),
        Factura: $('.Factura'),
        NroFactProv: $('.NroFactProv'),
        nuevaPrestacion: $('.nuevaPrestacion'),
        observacionesModal: $('.observacionesModal'),
        altaPrestacionModal: $('#altaPrestacionModal'),
        guardarFicha: $('#guardarFicha'),
        mensajeFichaLaboral: $('#mensajeFichaLaboral'),
        verListadoExCta: $('.verListadoExCta'),
        listadoExCta: $('.listadoExCta'),
        fichaLaboralModal: $('.fichaLaboralModal'),
        cerrarlstExCta: $('.cerrarlstExCta'),
        prestacionLimpia: $('.prestacionLimpia'),
        ultimasFacturadas: $('.ultimasFacturadas'),
        examenesDiponibles: $('examenesDisponibles'),
        siguienteExCta: $('#siguienteExCta'),
        guardarPrestacion: $('#guardarPrestacion'),
        selectMapaPres: $('.selectMapaPres'),
        selectMapaPresN: $('.selectMapaPresN'),
    };

    const variables = {
        PagoLaboral: $('#PagoLaboral'),
        Pago: $('#Pago'),
        SPago: $('#SPago'),
        Tipo: $('#Tipo'),
        Sucursal: $('#Sucursal'),
        NroFactura: $('#NroFactura'),
        FechaIngreso: $('#FechaIngreso'),
        FechaEgreso: $('#FechaEgreso'),
        TipoPrestacion: $('input[name="TipoPrestacion"]'),
        selectClientes: $('#selectClientes'),
        selectArt: $('#selectArt'),
        tipoPrestacionHidden: $('#tipoPrestacionHidden'),
        tipoPrestacionPres: $('#tipoPrestacionPres'),
        tipoPrestacionPresN: $('#tipoPrestacionPresN'),
        divtipoPrestacionPresOtros: $("#divtipoPrestacionPresOtros"),
        selectClientesPres: $('#selectClientesPres'),
        selectArtPres: $('#selectArtPres'),
        selectClientesPresN: $('#selectClientesPresN'),
        selectArtPresN: $('#selectArtPresN'),
        tipoPrestacionPresOtros: $('#tipoPrestacionPresOtros'),
        Autorizado: $('#Autorizado'),
        NroFactProv: $('#NroFactProv'),
        ElPago: $('#ElPago'),
        ElSPago: $('#ElSPago'),
        ElTipo: $('#ElTipo'),
        ElSucursal: $('#ElSucursal'),
        ElNroFactura: $('#ElNroFactura'),
        ElAutorizado: $('#ElAutorizado'),
        ElNroFactProv: $('#ElNroFactProv'),
    };

    let changeTipo = $('input[name="TipoPrestacion"]:checked').val(), e;
    
    $('.nuevaPrestacionModal, .observacionesModal, .nuevaPrestacion, .ObBloqueoEmpresa, .ObBloqueoArt, .ObEmpresa, .ObsPaciente, .ObsPres, .Factura, .TareaRealizar, .UltimoPuesto, .PuestoActual, .SectorActual, .AntiguedadPuesto, .AntiguedadEmpresa, .FechaIngreso, .FechaEgreso, .selectMapaPres, .Autoriza, .listadoExCta, #examenesDisponibles, #ultimasFacturadas, #alertaExCta, .NroFactProv, #divtipoPrestacionPresOtros, .verListadoExCta').hide();

    let IDficha = ['', null, undefined].includes(variables.selectClientes.val()) ? IDFICHA : variables.selectClientes.val();
    
    removerCeroSelect2(variables.selectArt);
    quitarDuplicados('#Horario');
    quitarDuplicados('#Tipo');
    quitarDuplicados('#TipoPrestacion');
    quitarDuplicados("#PagoLaboral");
    quitarDuplicados('#Tipo');
    quitarDuplicados('#Autorizado');
    calcularAntiguedad();
    mostrarFinanciador();
    marcarPago(variables.PagoLaboral.val());
    selectMedioPago(variables.PagoLaboral.val());

    variables.selectClientes.add(variables.selectArt).on('change', function(){
        getMap(variables.selectClientes.val(), variables.selectArt.val());
    });

    const listOpciones = {
        '.TareaRealizar': ['OCUPACIONAL', 'OTRO', 'INGRESO', 'ART'],
        '.PuestoActual': ['OCUPACIONAL', 'OTRO', 'PERIODICO', 'EGRESO', 'ART'],
        '.UltimoPuesto': ['OCUPACIONAL', 'OTRO', 'INGRESO', 'ART'],
        '.SectorActual': ['PERIODICO', 'OCUPACIONAL', 'EGRESO', 'OTRO', 'ART'],
        '.AntiguedadPuesto': ['PERIODICO', 'OCUPACIONAL', 'EGRESO', 'OTRO', 'ART'],
        '.AntiguedadEmpresa': ['EGRESO', 'OCUPACIONAL', 'OTRO', 'PERIODICO', 'ART', ],
        '.FechaIngreso': ['EGRESO', 'OCUPACIONAL', 'OTRO', 'PERIODICO', 'ART', 'INGRESO'],
        '.FechaEgreso': ['EGRESO', 'OCUPACIONAL', 'OTRO', 'ART'],
        '.CCosto': ['INGRESO', 'S/C_OCUPACIO', 'RECMED', 'CARNET', 'OTRO', 'ART', 'EGRESO', 'OCUPACIONAL', 'PERIODICO']
    };

    principal.eventDelete.on('click', function() {
        location.reload();
    });

    principal.seguirAl.on('click', function(e){
        e.preventDefault();
        principal.nuevaPrestacion.show();
        principal.observacionesModal.hide();
    });

    variables.tipoPrestacionPres.on('change', function(){
        activarMapas(variables.tipoPrestacionPres.val());
    });
    
    //Hack para forzar la recarga
    $(document).ready(function () {
        opcionesFicha(variables.TipoPrestacion.filter(':checked').val());
    });
   
    // Tipo de prestaciones muestra opciones
    variables.TipoPrestacion.change(function () {
        opcionesFicha(variables.TipoPrestacion.filter(':checked').val());
        limpiezaInputsPagos();
    });

    variables.PagoLaboral.change(function(){
        let valor = $(this).val();
        selectorPago(valor);
        // checkExamenesCuenta(IDficha);
    });

    variables.TipoPrestacion.change(function(){
        changeTipo = $(this).val(); 
        variables.tipoPrestacionHidden.val(changeTipo);
        changeTipo === 'OTRO' ? variables.divtipoPrestacionPresOtros.show() : variables.divtipoPrestacionPresOtros.hide();
        checkExamenesCuenta(variables.selectClientes.val());
    });

    variables.TipoPrestacion.change(function(){
        return variables.TipoPrestacion.filter(':checked').val() === 'ART'
            ? variables.PagoLaboral.find('option[value="P"]').remove()
            : variables.PagoLaboral.empty().append(`<option selected="" value="">Elija una opción...</option>
                <option value="B">Contado</option>
                <option value="A">Cuenta Corriente</option>
                <option value="P">Exámen a Cuenta</option>`);
    });

    variables.Pago.val(variables.PagoLaboral.val());
    // variables.tipoPrestacionPres.val(changeTipo);
    variables.tipoPrestacionHidden.val(changeTipo);

    variables.selectClientes.select2({
        dropdownParent: principal.altaPrestacionModal,
        placeholder: 'Seleccionar Cliente',
        language: 'es',
        allowClear: true,
        language: {
            noResults: function() {
                return "No hay clientes con esos datos";        
            },
            searching: function() {
                return "Buscando..";
            },
            inputTooShort: function () {
                return "Por favor, ingrese 2 o más caracteres";
            }
        },
        ajax: {
           url: getClientes,
           dataType: 'json',
           delay: 250,
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
           cache: true,
        },
        minimumInputLength: 2
    });

    $('#selectArt').select2({
        placeholder: 'Seleccionar ART',
        dropdownParent: principal.altaPrestacionModal,
        language: 'es',
        allowClear: true,
        language: {
            noResults: function() {
                return "No hay clientes con esos datos";        
            },
            searching: function() {
                return "Buscando..";
            },
            inputTooShort: function () {
                return "Por favor, ingrese 2 o más caracteres";
            }
        },
        ajax: {
           url: getClientes,
           dataType: 'json',
           delay: 250,
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
           cache: true,
        },
        minimumInputLength: 2
    });

    variables.SPago.on('change', function(){
        let pago = $(this).val();
        return pago === 'G' 
            ? principal.Autoriza.show() 
            : principal.Autoriza.hide();
    });

    //Alerta - verificacion de clientes bloqueados
    variables.selectClientes.on('select2:select', function (e) {
        let cliente = e.params.data.id;

        $.ajax({
            url: verifyBlock,
            type: "POST",
            data: {
                cliente: cliente,
                _token: TOKEN
            },
            success: function (response) {
                let data = response.cliente;
                data.Bloqueado === 0 
                    ? deshabilitarBloqueo() 
                    : cargarBloqueo(data);
            }
        });
    });

    variables.PagoLaboral.on('change', function() {
        selectMedioPago(variables.PagoLaboral.val());
    });

    //Habilitamos el botón de guardar
    variables.selectClientes.on('change', function(){
        if($(this).select2('data').map(option => option.id).length === 0){
            principal.guardarFicha
                .removeAttr('disabled')
                .removeAttr('title', 'Botón habilitado')
                .removeAttr('data-toggle', 'tooltip')
                .removeAttr('data-placement', 'top');
        }
    });

    //Guardar FichaLaboral
    principal.guardarFicha.on('click', function(e){
        e.preventDefault();

        let data = {
            paciente: ID,
            cliente: variables.selectClientes.val(),
            art: variables.selectArt.val(),
            tareaRealizar: $('#TareaRealizar').val(),
            tipoPrestacion: variables.TipoPrestacion.filter(':checked').val(),
            tipo: $('#TipoJornada').val(),
            pago: variables.PagoLaboral.val(),
            horario: $('#Horario').val(),
            observaciones: $('#ObservacionesFicha').val(),
            ultimoPuesto: $('#UltimoPuesto').val(),
            puestoActual: $('#PuestoActual').val(),
            sectorActual: $('#SectorActual').val(),
            ccosto: $('#CCostos').val(),
            antiguedadPuesto: $('#AntiguedadPuesto').val(),
            fechaIngreso: variables.FechaIngreso.val(),
            fechaEgreso: variables.FechaEgreso.val(),
            antiguedadEmpresa: $('#AntiguedadEmpresa').val(),
            fechaPreocupacional: $('#FechaPreocupacional').val(),
            fechaUltPeriod: $('#FechaUltPeriod').val(),
            fechaExArt: $('#FechaExArt').val(),
            Id: $('#IdFichaLaboral').val(),
            Spago: variables.SPago.val(),
            TipoF: variables.Tipo.val(),
            SucursalF: variables.Sucursal.val(),
            NumeroF: variables.NroFactura.val(),
            NumeroProvF: $('#NroFactProv').val(),
            Autoriza: $('#Autorizado').val()
        };
    

        //Validamos la factura
        if (data.Spago === 'G' && data.Autoriza === ''){
            toastr.warning('Si el medio de pago es gratuito, debe seleccionar quien autoriza.', '', {timeOut: 1000});
            return;
        }

        if (data.pago === 'B' && data.Spago === '') {
            toastr.warning('Debe seleccionar un "medio de pago" cuando la "forma de pago" es "contado"', '', {timeOut: 1000});
            return;
        }

        if (['',null,undefined].includes(data.pago)) {
            toastr.warning('Debe seleccionar una "forma de pago"','',{timeOut: 1000});
            return;
        }

        if (data.pago === 'B' && (data.TipoF == '' || data.SucursalF === '' || data.NumeroF === '')){
            toastr.warning('El pago es contado, asi que debe agregar el número de factura para continuar.','',{timeOut: 1000});
            return;
        }

        if(data.tipoPrestacion === 'OTRO' && variables.tipoPrestacionPresOtros.val() === '') {
            toastr.warning('¡El campo tipo de prestación OTRO necesita una opción de tipo!','',{timeOut: 1000});
            return;
        }


        if([0, null, undefined, ''].includes(data.tipoPrestacion)){
            toastr.warning('¡El campo tipo de prestación es obligatorio!','',{timeOut: 1000});
            return;
        }

        if(["0", null, undefined, ''].includes(data.cliente) && ["0", null, undefined, ''].includes(data.art)){
            toastr.warning('¡Debe seleccionar una empresa o una art!','',{timeOut: 1000});
            return;
        }
        
        if(data.tipoPrestacion === 'ART' && (["0", null, undefined, ''].includes(data.art))){
            toastr.warning('¡Debe seleccionar una ART para el tipo de prestación ART!','',{timeOut: 1000});
            return;
        }
        
        if(data.tipoPrestacion !== 'ART' && (["0", null, undefined, ''].includes(data.cliente))){
            toastr.warning('¡Debe seleccionar una empresa para el tipo de prestación seleccionado!','',{timeOut: 1000});
            return;
        }

        //ejecutamos la verificación de disponibilidad
        if(
            ((["0", null, undefined, ''].includes(data.art) || !["0", null, undefined, ''].includes(data.art)) && data.tipoPrestacion !== 'ART')
        ) {
            verificarDisponibilidad(data.cliente, data.pago, data, e);
            variables.Pago.find('option[value="' + data.pago + '"]').prop('selected', true);
        }else{
            saveFichaLaboral(data);
            variables.Pago.find('option[value="' + data.pago + '"]').prop('selected', true);
        }

    });

    //Calcular Antiguedad en la Empresa en FichaLaboral
    variables.FechaIngreso.add(variables.FechaEgreso).on('change', function () {
        calcularAntiguedad();
    });

    //Actualizamos
    principal.mensajeFichaLaboral.change(function() {
        actualizarMensaje();
    });

    $(document).on("select2:open", () => {
        document.querySelector(".select2-container--open .select2-search__field").focus()
    });
    
    principal.verListadoExCta.on('click', function(e){
        e.preventDefault();
        principal.listadoExCta.show();
        principal.fichaLaboralModal.hide();

        examenesCta(variables.selectClientes.val());
    });

    principal.cerrarlstExCta.on('click', function(e){
        e.preventDefault();
        principal.listadoExCta.hide();
        principal.fichaLaboralModal.show();
    });

    variables.selectClientes.on('change', function(){
        let id = $(this).val();
        checkExamenesCuenta(id);
    });

    principal.altaPrestacionModal.on('hidden.bs.modal', function () {
        principal.prestacionLimpia.add(principal.observacionesModal).add(principal.nuevaPrestacion).hide();
        principal.fichaLaboralModal.show();
        checkExamenesCuenta(variables.selectClientes.val());
        getMap(variables.selectClientes.val(), variables.selectArt.val());
      });

    //Bloqueo de cliente si existe
    function cargarBloqueo(response){
        let razonSocial = response.RazonSocial, motivo = response.Motivo, identificacion = response.Identificacion;

        $('#razonSocialModal').text(razonSocial);
        $('#motivoModal').text(motivo);
        $('#identificacionModal').text(identificacion);

        principal.guardarFicha.attr('disabled', 'disabled').attr('title', 'Botón bloqueado').attr('data-toggle', 'tooltip').attr('data-placement', 'top');

        swal("¡Cliente Bloqueado!", "El cliente " +  razonSocial + " | cuit: " + identificacion + " se encuentra bloqueado por el siguiente motivo: " + motivo +  ". No podrá avanzar con el alta. Se ha bloqueado el botón de registro.","info");
    };

    function deshabilitarBloqueo(){
        principal.guardarFicha
            .removeAttr('disabled')
            .removeAttr('title', 'Botón habilitado')
            .removeAttr('data-toggle', 'tooltip')
            .removeAttr('data-placement', 'top');
    };

    function mostrarFinanciador(){
        preloader('on');
        $.ajax({
            url: verificarAlta,
            type: 'GET',
            data: { Id: ID },
            success: function(response){
                preloader('off');
                let verificar = response.fichaLaboral, cliente = response.cliente, clienteArt = response.clienteArt;

                if(verificar !== undefined && verificar.Id ) {

                    variables.selectClientesPres
                        .add(variables.selectArtPres)
                        .add(variables.selectClientesPresN)
                        .add(variables.selectArtPresN)
                        .empty();

                    variables.selectClientesPres
                        .add(variables.selectClientesPresN)
                        .val(cliente.RazonSocial);
                    variables.selectArtPres
                        .add(variables.selectArtPresN)
                        .val(clienteArt.RazonSocial);

                    filtrarTipoPrestacion(variables.tipoPrestacionHidden.val());
                }
            }
        });   
    };

    function filtrarTipoPrestacion(estado){
        preloader('on');
        $.ajax({
            url: getTipoPrestacion,
            type: "GET",

            success: function(response) {
                preloader('off');
                let tiposPrestacion = response.tiposPrestacion;
                
                if(tiposPrestacion) {
                    variables.tipoPrestacionPres
                        .add(variables.tipoPrestacionPresN)
                        .empty()
                        .append('<option value="" selected>Elija una opción...</option>');

                    if(estado === 'ART') {

                        variables.tipoPrestacionPres
                            .add(variables.tipoPrestacionPresN)
                            .empty()
                            .append('<option value="ART" selected>ART</option>');

                    }else {

                        tiposPrestacion.forEach(function(tipoPrestacion) {
                            variables.tipoPrestacionPres
                                .append('<option value="' + tipoPrestacion.nombre + '">' + tipoPrestacion.nombre + '</option>');
    
                            variables.tipoPrestacionPresN
                                .append('<option value="' + tipoPrestacion.nombre + '">' + tipoPrestacion.nombre + '</option>');
                        });

                        variables.tipoPrestacionPres.add(variables.tipoPrestacionPresN).find('option[value="ART"]').remove();
                    }                    
                    
                    if(estado) { 
                        
                        let result = estado === 'OTRO' ? variables.tipoPrestacionPresOtros.val() : estado;
                        
                        variables.tipoPrestacionPres
                            .add(variables.tipoPrestacionPresN)
                            .val(result);
                    }

                    if (estado !== 'ART' || estado === '') {
                        principal.selectMapaPres
                            .add(principal.selectMapaPresN)
                            .hide();
                    } else {
                        principal.selectMapaPres
                            .add(principal.selectMapaPresN)
                            .show();
                    }
                }
            }
        });
    }

    function activarMapas(estado){
        return estado === 'ART' 
            ? principal.selectMapaPres.show() 
            :  principal.selectMapaPres.hide();
    };

    function calcularAntiguedad(){
    
        let ingreso = variables.FechaIngreso.val(), egreso = variables.FechaEgreso.val();
        let dateIngreso = new Date(ingreso), dateEgreso = egreso ? new Date(egreso) : new Date();

        let diff =  dateEgreso.getFullYear() - dateIngreso.getFullYear();

        if (dateEgreso.getMonth() < dateIngreso.getMonth() || (dateEgreso.getMonth() === dateIngreso.getMonth() && dateEgreso.getDate() < dateIngreso.getDate())) {
            diff--;
        }

        $('#AntiguedadEmpresa').val(diff);
    };

    function opcionesFicha(option){
        Object.entries(listOpciones).forEach(([campo, opciones]) => {
            opciones.includes(option) 
                ? $(campo).show() 
                : $(campo).hide();
        });
    };

    function selectMedioPago(opcion){

        if(opcion === 'B'){

            const contenido = `
                <option value="" selected>Elija una opción...</option>
                <option value="A">Efectivo</option>
                <option value="B">Débito</option>
                <option value="C">Crédito</option>
                <option value="D">Cheque</option>
                <option value="E">Otro</option>
                <option value="F">Transferencia</option>
                <option value="G">Sin Cargo</option>
            `;

            principal.SPago
                .add(principal.Factura)
                .add(principal.NroFactProv)
                .add(principal.ObsPres)
                .show();
                
            variables.SPago
                .empty()
                .append(contenido);
       
        }else if(opcion === 'C') {
            principal.ObsPres
                .add(principal.Factura)
                .add(principal.NroFactProv)
                .add(principal.SPago)
                .hide();
            
            principal.Autoriza.show();
        }else{
            principal.SPago
                .add(principal.Autoriza)
                .add(principal.ObsPres)
                .add(principal.Factura)
                .add(principal.NroFactProv)
                .hide();
        }
    };

    async function checkObservaciones() {

        const elementos = {
            ObBloqueoEmpresa: $('.ObBloqueoEmpresa'),
            ObBloqueoArt: $('.ObBloqueoArt'),
            ObArt: $('.ObArt'),
            ObEmpresa: $('.ObEmpresa'),
            ObPaciente: $('.ObPaciente'),
            messagePrestacion: $('.messagePrestacion')
        };

        let alerta = `
        <!-- Warning Alert -->
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong> No existen observaciones para mostrar en la prestación. </strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
     
        if (ID === '') return;
        
        elementos.ObBloqueoArt.hide();
        elementos.ObBloqueoEmpresa.hide();
        elementos.ObArt.hide();
        elementos.ObEmpresa.hide();
        elementos.ObPaciente.hide();

        principal.seguirAl
            .prop('disabled', false)
            .removeAttr('title');

        try {
            const response = await $.get(checkObs, { Id: ID });
            let obsArt = response.obsArt, obsEmpresa = response.obsEmpresa, obsPaciente = response.obsPaciente;

            if([obsArt.Motivo, obsArt.Observaciones, obsEmpresa.Motivo, obsEmpresa.Observaciones, obsPaciente.Observaciones].every(value => value === '' || value === null)){

                principal.fichaLaboralModal
                    .add(principal.observacionesModal)
                    .hide();
                principal.nuevaPrestacion.show();

                elementos.messagePrestacion.html(alerta);
                setTimeout(()=>{
                    elementos.messagePrestacion.fadeOut();
                }, 10000);
            
            }else{

                principal.fichaLaboralModal.hide();
                principal.observacionesModal.show();

                if(![null, undefined, ''].includes(obsArt.Motivo)) {
                    elementos.ObBloqueoArt.show();
                    elementos.ObBloqueoArt
                        .find('p')
                        .text(obsArt.Motivo);
                    principal.seguirAl
                        .prop('disabled', true)
                        .attr('title', 'Boton bloqueado');
                }

                if(![null, undefined, ''].includes(obsArt.Observaciones)) {
                    elementos.ObArt.show();
                    elementos.ObArt
                        .find('p')
                        .text(obsArt.Observaciones);
                }
                
                if(![null, undefined, ''].includes(obsEmpresa.Observaciones)) {
                    elementos.ObEmpresa.show();
                    elementos.ObEmpresa
                        .find('p')
                        .text(obsEmpresa.Observaciones);
                }

                if(![null,undefined, ''].includes(obsEmpresa.Motivo)) {
                    elementos.ObBloqueoEmpresa.show();
                    elementos.ObBloqueoEmpresa
                        .find('p')
                        .text(obsEmpresa.Motivo);
                    //$('.seguirAl').prop('disabled', true).attr('title', 'Boton bloqueado');
                }
                
                if(![null, undefined, ''].includes(obsPaciente.Observaciones)) {
                    elementos.ObPaciente.show();
                    elementos.ObPaciente
                        .find('p')
                        .text(obsPaciente.Observaciones);
                }
            } 
        } catch (jqXHR) {
            let errorData = JSON.parse(jqXHR.responseText);
            checkError(jqXHR.status, errorData.msg);
            return;
        }
    };

    function examenesCta(id) {

        principal.lstSaldos.empty();
        preloader('on');

        $.get(lstExDisponibles, {Id: id})
            .done(function(response) {
                let contenido = '';
                preloader('off');

                if(response && response.length > 0){

                    for(let index = 0; index < response.length; index++) {
                        let r = response[index];

                        contenido += `
                        <tr>
                            <td>${r.Precarga === '' ? '-' : r.Precarga}</td>
                            <td>${r.NombreExamen}</td>
                        </tr>
                        `;
                    }
                }else{
                    contenido = `
                        <tr>
                            <td>No hay registros de examenes a cuenta</td>
                            <td></td>
                            <td></td>
                        </tr>
                        `;
                }
                principal.lstSaldos.append(contenido); 
            });    
    };

    function selectorPago(pago) {

        if (pago != 'B') {

            variables.SPago.val('');
            variables.Tipo.val('');
            variables.Sucursal.val('');
            variables.NroFactura.val('');
        }

        if(['B','A', ''].includes(pago)) {
            
            principal.ultimasFacturadas
                .add(principal.examenesDiponibles)
                .add(principal.siguienteExCta)
                .hide();
            principal.guardarPrestacion.show();
        
        }else if(pago === 'P') {

            preloader('on');
            $.get(lstExDisponibles, {Id: variables.selectClientes.val()})
            .done(function(response){
                preloader('off');

                if(response.length > 0) {
                    principal.ultimasFacturadas
                        .add(principal.examenesDiponibles)
                        .add(principal.siguienteExCta)
                        .show();
                    principal.guardarPrestacion.hide();
                    checkExamenesCuenta(variables.selectClientes.val())
                }
            });
        }
    };

    async function checkExamenesCuenta(id) {

        if([null, 0,''].includes(id)) {
            principal.alertaExCta
                    .add(principal.verListadoExCta)
                    .hide();
                return;
        }

        try {
            const response = await $.get(lstExDisponibles, { Id: id });
    
            if (
                response &&
                response.length > 0 &&
                !['ART', '', undefined, 0].includes($("input[name='TipoPrestacion']:checked").val())
            ) {

                principal.alertaExCta.add(principal.verListadoExCta).show();
                variables.PagoLaboral.val('P');
                variables.Pago.val('P');
                limpiezaInputsPagos();
                return true;

            } else {
                principal.alertaExCta
                    .add(principal.verListadoExCta)
                    .hide();
                return;
            }
        } catch (jqXHR) {
            preloader('off');
            let errorData = JSON.parse(jqXHR.responseText);
            checkError(jqXHR.status, errorData.msg);
            return;
        }
    };

    function saveFichaLaboral(data){
        preloader('on');

        $.post(saveFichaAlta, {
            IdPaciente: data.paciente,
            IdEmpresa: data.cliente === '' ? 0 : data.cliente,
            IdART: data.art === '' ? 0 : data.art,
            tareaRealizar: data.tareaRealizar === '' ? '' : data.tareaRealizar,
            TipoPrestacion: data.tipoPrestacion === '' ? '' : data.tipoPrestacion,
            TipoJornada: data.tipo === '' ? '' : data.tipo,
            Pago: data.pago === '' ? '' : data.pago,
            Jornada: data.horario === '' ? '' : data.horario,
            Observaciones: data.observaciones === '' ? '' : data.observaciones,
            TareasEmpAnterior: data.ultimoPuesto === '' ? '' : data.ultimoPuesto,
            Puesto: data.puestoActual === '' ? '' : data.puestoActual,
            Sector: data.sectorActual === '' ? '' : data.sectorActual,
            CCosto: data.ccosto === '' ? '' : data.ccosto,
            AntigPuesto: data.antiguedadPuesto === '' ? '' : data.antiguedadPuesto,
            FechaIngreso: data.fechaIngreso === '' ? '' : data.fechaIngreso,
            FechaEgreso: data.fechaEgreso === '' ? '' : data.fechaEgreso,
            antiguedadEmpresa: data.antiguedadEmpresa,
            FechaPreocupacional: data.fechaPreocupacional === '' ? '' : data.fechaPreocupacional,
            FechaUltPeriod: data.fechaUltPeriod === '' ? '' : data.fechaUltPeriod,
            FechaExArt: data.fechaExArt === '' ? '' : data.fechaExArt,
            Id: data.Id,
            SPago: data.Spago === '' ? '' : data.Spago,
            Tipo: data.TipoF === '' ? '' : data.TipoF,
            Sucursal: data.SucursalF === '' ? '' : data.SucursalF,
            NroFactura: data.NumeroF === '' ? '' : data.NumeroF,
            NroFactProv: data.NumeroProvF === '' ? '' : data.NumeroProvF,
            Autorizado: data.Autoriza === '' ? '' :data.Autoriza,   
            _token: TOKEN,
            }) 
            .done(function(response) {
                preloader('off');
                toastr.success(response.msg,'',{timeOut: 1000});

                mostrarFinanciador();
                selectMedioPago(variables.PagoLaboral.val());

                variables.PagoLaboral.val() === 'A' ? variables.ElPago.val("C") : variables.ElPago.val(variables.PagoLaboral.val());
                variables.ElSPago.val(data.Spago);
                variables.ElTipo.val(data.TipoF);
                variables.ElSucursal.val(data.SucursalF);
                variables.ElNroFactura.val(data.NumeroF);
                variables.ElAutorizado.val(data.Autoriza);
                variables.ElNroFactura.val(data.NumeroProvF);
                variables.ElNroFactProv.val(data.NumeroProvF);
                
                selectorPago(data.pago);
                getMap(data.cliente, data.art);
                setTimeout(() => {
                    checkObservaciones();
                }, 2000);

            })
            .fail(function(jqXHR) {
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);
                checkError(jqXHR.status, errorData.msg);
                return;
                
            });
    };

    async function checkExCuentaDisponible(id) {
        $data = await $.get(lstExDisponibles, {Id: id});
        return $data.length > 0 ? true : false;
    }

    function marcarPago(pago) {

        switch (pago) {
            case 'P':
                variables.PagoLaboral.val(pago);
                break;
            case 'A':
                variables.PagoLaboral.val(pago);
                break;
            case 'B':
                $variables.PagoLaboral.val(pago);
                break;
            default:
                variables.PagoLaboral.val('');
                break;
        }
    }

    function getMap(idEmpresa, idArt){

        const mapas = [principal.mapas, principal.mapasN];
 
        $.get(getMapas, {empresa: idEmpresa, art: idArt})
            .done(async function(response){

                let dataMap = await response.mapas;
            
                mapas.forEach(mapa => {
                    mapa.empty();
                });

                if(dataMap.length === 0)
                {
                    
                    mapas.forEach(mapa => {
                        mapa.empty().append(`
                            <option title="Sin mapas disponibles para esta ART y Empresa." value="0" selected>
                                Sin mapas disponibles.
                            </option>
                        `);
                    });

                }else{
                    for(let index = 0; index < dataMap.length; index++) {
                        let d = dataMap[index],
                            contenido = `<option value="${d.Id}">${d.Nro} | Empresa: ${d.RSE} - ART: ${d.RSArt}</option>`;
    
                        mapas.forEach(mapa => {
                            mapa.append(contenido);
                        });
                    }
                } 
            })
    }

    async function verificarDisponibilidad(cliente, pago, data, e) {

        let disponibilidad = await checkExCuentaDisponible(cliente);
        allData = disponibilidad === true && pago !== 'P';

        preloader('on')
        if (allData) {
            preloader('off');
            if (confirm('Tienes examenes a cuenta disponibles. ¿Estás seguro que deseas continuar?')) {
                
                saveFichaLaboral(data);
            }else{
                e.stopPropagation();
            }
        }else{
            preloader('off');
            saveFichaLaboral(data);
        }
    }

    function limpiezaInputsPagos() {
        variables.SPago.val('');
        variables.Tipo.val('');
        variables.Sucursal.val('');
        variables.NroFactura.val('');
        variables.Autorizado.val('');
        variables.NroFactProv.val('');

        principal.SPago
            .add(principal.Factura)
            .add(principal.NroFactProv)
            .add(principal.Autoriza)
            .hide();
    }


});