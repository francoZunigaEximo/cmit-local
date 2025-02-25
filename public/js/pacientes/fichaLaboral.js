$(document).ready(function () {

    let pagoLaboral = $('#PagoLaboral').val(), changeTipo = $('input[name="TipoPrestacion"]:checked').val(), empresaInput = $('#selectClientes').val(), artInput = $('#selectArt').val();
    $('.nuevaPrestacionModal, .observacionesModal, .nuevaPrestacion, .ObBloqueoEmpresa, .ObBloqueoArt, .ObEmpresa, .ObsPaciente, .ObsPres, .Factura, .TareaRealizar, .UltimoPuesto, .PuestoActual, .SectorActual, .AntiguedadPuesto, .AntiguedadEmpresa, .FechaIngreso, .FechaEgreso, .selectMapaPres, .Autoriza, .listadoExCta, #examenesDisponibles, #ultimasFacturadas, #alertaExCta, .NroFactProv').hide();
    let IDficha = ['', null, undefined].includes(empresaInput) ? IDFICHA : empresaInput;
    
    quitarDuplicados('#Horario');
    quitarDuplicados('#Tipo');
    quitarDuplicados('#TipoPrestacion');
    quitarDuplicados('#PagoLaboral');
    quitarDuplicados('#Tipo');
    quitarDuplicados('#Autorizado');
    calcularAntiguedad();
    mostrarFinanciador();
    selectorPago(pagoLaboral, IDFICHA);
    checkExamenesCuenta(IDficha);
    marcarPago(pagoLaboral);
    selectMedioPago();

    $(document).on('change', '#selectClientes, #selectArt', function(){
        let empresaInput = $('#selectClientes').val(), artInput = $('#selectArt').val();
        getMap(empresaInput, artInput);
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

    $('.eventDelete').on('click', function() {
        //$('.nuevaPrestacion').hide();
        //$('.observacionesModal').hide();
        //$('.fichaLaboralModal').show();
        location.reload();
    });

    $('.seguirAl').on('click', function(e){
        e.preventDefault();
        $('.nuevaPrestacion').show();
        $('.observacionesModal').hide();
    });

    $(document).on('change', '#tipoPrestacionPres', function(){
        let estado = $('#tipoPrestacionPres').val();
        activarMapas(estado);
    });
    
    //Hack para forzar la recarga
    $(document).ready(function () {
        let inputShow = $('input[name="TipoPrestacion"]:checked').val();
        opcionesFicha(inputShow);
    });
   
    // Tipo de prestaciones muestra opciones
    $('input[name="TipoPrestacion"]').change(function () {
        let inputShow = $('input[name="TipoPrestacion"]:checked').val();
        opcionesFicha(inputShow);
    });

    $('#PagoLaboral').change(function(IDFICHA){
        let valor = $(this).val(); 
        $('#Pago').val(valor).find('option[value="' + valor + '"]').prop('selected', true);
        selectorPago(valor, IDFICHA)
    });

    $('input[name="TipoPrestacion"]').change(function(){
        changeTipo = $(this).val(); 
        $('#tipoPrestacionHidden').val(changeTipo);
        changeTipo === 'OTRO' ? $("#divtipoPrestacionPresOtros").show() : $("#divtipoPrestacionPresOtros").hide();
    });

    $('#Pago').val(pagoLaboral);
    $('#tipoPrestacionPres').val(changeTipo);
    $('#tipoPrestacionHidden').val(changeTipo);

    $('#selectClientes').select2({
        dropdownParent: $('#altaPrestacionModal'),
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
        dropdownParent: $('#altaPrestacionModal'),
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

    $(document).on('change', '#SPago', function(){
        let pago = $(this).val();
        pago === 'G' ? $('.Autoriza').show() : $('.Autoriza').hide();
    });

    //Alerta - verificacion de clientes bloqueados
    $('#selectClientes').on('select2:select', function (e) {
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
                data.Bloqueado === 0 ? deshabilitarBloqueo() : cargarBloqueo(data);
            }
        });
    });

    $(document).on('change', '#PagoLaboral', function(){
        selectMedioPago();
    });

    //Habilitamos el botón de guardar
    $('#selectClientes').on('change', function(){
        
        if($(this).select2('data').map(option => option.id).length === 0){
            $('#guardarFicha').removeAttr('disabled').removeAttr('title', 'Botón habilitado').removeAttr('data-toggle', 'tooltip').removeAttr('data-placement', 'top');
        }
    });

    //Guardar FichaLaboral
    $(document).on('click', '#guardarFicha', function(){

        let paciente = ID,
            cliente = $('#selectClientes').val(),
            art = $('#selectArt').val(),
            tipoPrestacion =  $('input[name="TipoPrestacion"]:checked').val(),
            tipoPrestacionPresOtros =  $('#tipoPrestacionPresOtros').val(),
            tareaRealizar = $('#TareaRealizar').val(),
            tipo = $('#TipoJornada').val(),
            pago = $('#PagoLaboral').val(),
            spago = $('#SPago').val(),
            tipoF = $('#Tipo').val(),
            sucursalF = $('#Sucursal').val(),
            numeroF = $('#NroFactura').val(),
            numeroProvF = $('#NroFactProv').val(),
            autoriza = $('#Autorizado').val(),
            horario = $('#Horario').val(),
            observaciones = $('#ObservacionesFicha').val(),
            ultimoPuesto = $('#UltimoPuesto').val(),
            puestoActual = $('#PuestoActual').val(),
            sectorActual = $('#SectorActual').val(),
            ccosto = $('#CCostos').val(),
            antiguedadPuesto = $('#AntiguedadPuesto').val(),
            fechaIngreso = $('#FechaIngreso').val(),
            fechaEgreso = $('#FechaEgreso').val(),
            fechaPreocupacional = $('#FechaPreocupacional').val(),
            fechaUltPeriod =$('#FechaUltPeriod').val(),
            fechaExArt = $('#FechaExArt').val(),
            antiguedadEmpresa = $('#AntiguedadEmpresa').val(),
            Id = $('#IdFichaLaboral').val();

        //Validamos la factura
        if (spago === 'G' && autoriza === ''){
            toastr.warning('Si el medio de pago es gratuito, debe seleccionar quien autoriza.');
            return;
        }

        if (pago === 'B' && spago === '') {
            toastr.warning('Debe seleccionar un "medio de pago" cuando la "forma de pago" es "contado"');
            return;
        }

        if (['',null,undefined].includes(pago)) {
            toastr.warning('Debe seleccionar una "forma de pago"');
            return;
        }

        if (pago === 'B' && (tipoF == '' || sucursalF === '' || numeroF === '')){
            toastr.warning('El pago es contado, asi que debe agregar el número de factura para continuar.');
            return;
        }

        if(tipoPrestacion === 'OTRO' && tipoPrestacionPresOtros) {
            tipoPrestacion = tipoPrestacionPresOtros;
            $('#tipoPrestacionHidden').val(tipoPrestacion);
        }

        if([0, null, undefined, ''].includes(tipoPrestacion)){
            toastr.warning('¡El campo tipo de prestación es obligatorio!');
            return;
        }

        if([0, null, undefined, ''].includes(cliente) && [0, null, undefined, ''].includes(art)){
            toastr.warning('¡Debe seleccionar una empresa o una art!');
            return;
        }
        
        if(tipoPrestacion === 'ART' && ([0, null, undefined, ''].includes(art))){
            toastr.warning('¡Debe seleccionar una ART para el tipo de prestación ART!');
            return;
        }
        
        if(tipoPrestacion !== 'ART' && ([0, null, undefined, ''].includes(cliente))){
            toastr.warning('¡Debe seleccionar una empresa para el tipo de prestación seleccionado!');
            return;
        }

        async function verificarDisponibilidad(cliente, pago) {
            let disponibilidad = await checkExCuentaDisponible(cliente);
            data = disponibilidad === true && pago !== 'P';
            preloader('on')
            if (data) {
                preloader('off');
                if (confirm('Tienes examenes a cuenta disponibles. ¿Estás seguro que deseas continuar?')) {
                    
                    saveFichaLaboral(paciente, cliente, art, tareaRealizar, tipoPrestacion, tipo, pago, horario, observaciones, ultimoPuesto, puestoActual, sectorActual, ccosto, antiguedadPuesto, fechaIngreso, fechaEgreso, antiguedadEmpresa, fechaPreocupacional, fechaUltPeriod, fechaExArt, Id, spago, tipoF, sucursalF, numeroF, numeroProvF, autoriza);
                }else{
                    e.stopPropagation();
                }
            }else{
                preloader('off');
                saveFichaLaboral(paciente, cliente, art, tareaRealizar, tipoPrestacion, tipo, pago, horario, observaciones, ultimoPuesto, puestoActual, sectorActual, ccosto, antiguedadPuesto, fechaIngreso, fechaEgreso, antiguedadEmpresa, fechaPreocupacional, fechaUltPeriod, fechaExArt, Id, spago, tipoF, sucursalF, numeroF, numeroProvF, autoriza);
            }
        }

        verificarDisponibilidad(cliente, pago);  
    });

    //Calcular Antiguedad en la Empresa en FichaLaboral
    $('#FechaIngreso, #FechaEgreso').change(function(){
        calcularAntiguedad();
    });

    //Actualizamos
    $('#mensajeFichaLaboral').change(function() {
        actualizarMensaje();
    });

    $(document).on("select2:open", () => {
        document.querySelector(".select2-container--open .select2-search__field").focus()
    });
    
    $(document).on('click', '.verListadoExCta', function(e){
        e.preventDefault();
        $('.listadoExCta').show();
        $('.fichaLaboralModal').hide();

        let id = $('#selectClientes').val();
        examenesCta(id);
    });

    $(document).on('click', '.cerrarlstExCta', function(e){
        e.preventDefault();
        $('.listadoExCta').hide();
        $('.fichaLaboralModal').show();
    });

    $(document).on('change', '#selectClientes', function(){
        let id = $(this).val();
        checkExamenesCuenta(id);
    });

    $('#altaPrestacionModal').on('hidden.bs.modal', function () {
        $('.prestacionLimpia, .observacionesModal, .nuevaPrestacion').hide();
        $('.fichaLaboralModal').show();
        checkExamenesCuenta(IDficha);
        getMap(empresaInput, artInput);
      });

    //Bloqueo de cliente si existe
    function cargarBloqueo(response) {
        let razonSocial = response.RazonSocial, motivo = response.Motivo, identificacion = response.Identificacion;

        $('#razonSocialModal').text(razonSocial);
        $('#motivoModal').text(motivo);
        $('#identificacionModal').text(identificacion);

        $('#guardarFicha').attr('disabled', 'disabled').attr('title', 'Botón bloqueado').attr('data-toggle', 'tooltip').attr('data-placement', 'top');

        swal("¡Cliente Bloqueado!", "El cliente " +  razonSocial + " | cuit: " + identificacion + " se encuentra bloqueado por el siguiente motivo: " + motivo +  ". No podrá avanzar con el alta. Se ha bloqueado el botón de registro.","info");
    }

    function deshabilitarBloqueo() {
        $('#guardarFicha').removeAttr('disabled').removeAttr('title', 'Botón habilitado').removeAttr('data-toggle', 'tooltip').removeAttr('data-placement', 'top');
    }

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

                    $('#selectClientesPres, #selectArtPres, #selectClientesPresN, #selectArtPresN').empty();

                    $('#selectClientesPres, #selectClientesPresN').val(cliente.RazonSocial);
                    $('#selectArtPres, #selectArtPresN').val(clienteArt.RazonSocial);

                    $('#selectClientesPres').change(function() {
                        filtrarTipoPrestacion($('#selectClientesPres, #tipoPrestacionPresN').val(), null);
                    });

                    let estado = $('#tipoPrestacionHidden').val();
                    
                    if (estado !== 'ART' || estado === '') {
                        $('.selectMapaPres, .selectMapaPresN').hide();
                    } else {
                        $('.selectMapaPres, .selectMapaPresN').show();
                    }

                    filtrarTipoPrestacion(estado);
                }
            }
        });   
    }

    function filtrarTipoPrestacion(estado) {
        preloader('on');
        $.ajax({
            url: getTipoPrestacion,
            type: "GET",

            success: function(response) {
                preloader('off');
                let tiposPrestacion = response.tiposPrestacion;
                
                if(tiposPrestacion) {
                    $('#tipoPrestacionPres, #tipoPrestacionPresN').empty();
                    $('#tipoPrestacionPres, #tipoPrestacionPresN').append('<option value="" selected>Elija una opción...</option>');

                    tiposPrestacion.forEach(function(tipoPrestacion) {
                        $('#tipoPrestacionPres').append('<option value="' + tipoPrestacion.nombre + '">' + tipoPrestacion.nombre + '</option>');

                        $('#tipoPrestacionPresN').append('<option value="' + tipoPrestacion.nombre + '">' + tipoPrestacion.nombre + '</option>');
                    });
                    
                    if(estado) {    
                        $('#tipoPrestacionPres, #tipoPrestacionPresN').val(estado);
                    }
                }
            }
        });
    }

    function activarMapas(estado){
        if (estado === 'ART') {
            $('.selectMapaPres').show();
        }else{
            $('.selectMapaPres').hide();
        }
    }

    //Creamos función para trabajar cambios en fechas automaticamente
    function calcularAntiguedad(){
        
        let ingreso = $('#FechaIngreso').val(), egreso = $('#FechaEgreso').val();
        let dateIngreso = new Date(ingreso), dateEgreso = egreso ? new Date(egreso) : new Date();

        let diff = dateIngreso.getFullYear() - dateEgreso.getFullYear();

        if (dateEgreso.getMonth() < dateIngreso.getMonth() || (dateEgreso.getMonth() === dateIngreso.getMonth() && dateEgreso.getDate() < dateIngreso.getDate())) {
            diff--;
        }

        $('#AntiguedadEmpresa').val(diff);
    }

    function opcionesFicha(option) {
        Object.entries(listOpciones).forEach(([campo, opciones]) => {
            opciones.includes(option) ? $(campo).show() : $(campo).hide();
        });
    }

    function selectMedioPago()
    {
        let opcion = $('#PagoLaboral').val()
        if(opcion === 'B'){

            let contenido = `
                <option value="" selected>Elija una opción...</option>
                <option value="A">Efectivo</option>
                <option value="B">Débito</option>
                <option value="C">Crédito</option>
                <option value="D">Cheque</option>
                <option value="E">Otro</option>
                <option value="F">Transferencia</option>
                <option value="G">Sin Cargo</option>
            `;

            $('.SPago').show();
            $('.ObsPres').show();
            $('.Factura').show();
            $('.NroFactProv').show();
            $('#SPago').empty().append(contenido);
       
        }else if(opcion === 'C') {

            $('.SPago').hide();
            $('.ObsPres').hide();
            $('.Factura').hide();
            $('.NroFactProv').hide();
            $('.Autoriza').show();

        }else{
            $('.SPago').hide();
            $('.ObsPres').hide();
            $('.Factura').hide();
            $('.NroFactProv').hide();
            $('.Autoriza').hide();
        }
    }

    async function checkObservaciones() {
     
        if (ID === '') return;
        $('.ObBloqueoEmpresa, .ObBloqueoArt, .ObArt, .ObEmpresa, .ObPaciente').hide();
        $('.seguirAl').prop('disabled', false).removeAttr('title');

        try {

            const response = await $.get(checkObs, { Id: ID });

            let obsArt = response.obsArt, obsEmpresa = response.obsEmpresa, obsPaciente = response.obsPaciente;

            if([obsArt.Motivo, obsArt.Observaciones, obsEmpresa.Motivo, obsEmpresa.Observaciones, obsPaciente.Observaciones].every(value => value === '' || value === null)){

                let alerta = `
                <!-- Warning Alert -->
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong> No existen observaciones para mostrar en la prestación. </strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;

                $('.fichaLaboralModal, .observacionesModal').hide();
                $('.nuevaPrestacion').show();

                $(".messagePrestacion").html(alerta);
                setTimeout(()=>{
                    $(".messagePrestacion").hide();
                }, 10000);
            
            }else{

                $('.fichaLaboralModal').hide();
                $('.observacionesModal').show();

                if(![null, undefined, ''].includes(obsArt.Motivo)) {
                    $('.ObBloqueoArt').show();
                    $('.ObBloqueoArt p').text(obsArt.Motivo);
                    $('.seguirAl').prop('disabled', true).attr('title', 'Boton bloqueado');
                }

                if(![null, undefined, ''].includes(obsArt.Observaciones)) {
                    $('.ObArt').show();
                    $('.ObArt p').text(obsArt.Observaciones);
                }
                
                if(![null, undefined, ''].includes(obsEmpresa.Observaciones)) {
                    $('.ObEmpresa').show();
                    $('.ObEmpresa p').text(obsEmpresa.Observaciones);
                }

                if(![null,undefined, ''].includes(obsEmpresa.Motivo)) {
                    $('.ObBloqueoEmpresa').show();
                    $('.ObBloqueoEmpresa p').text(obsEmpresa.Motivo);
                    //$('.seguirAl').prop('disabled', true).attr('title', 'Boton bloqueado');
                }
                
                if(![null, undefined, ''].includes(obsPaciente.Observaciones)) {
                    $('.ObPaciente').show();
                    $('.ObPaciente p').text(obsPaciente.Observaciones);
                }
            } 
        } catch (jqXHR) {
            let errorData = JSON.parse(jqXHR.responseText);
            checkError(jqXHR.status, errorData.msg);
            return;
        }
    }

    function examenesCta(id) {

        $('#lstSaldos').empty();
        preloader('on');

        $.get(lstExDisponibles, {Id: id})
            .done(function(response) {
                var contenido = '';
                preloader('off');

                if(response && response.length > 0){

                    $.each(response, function(index, r) {
                    
                        contenido += `
                        <tr>
                            <td>${r.Precarga === '' ? '-' : r.Precarga}</td>
                            <td>${r.NombreExamen}</td>
                        </tr>
                        `;
                    });
                }else{
                    contenido = `
                        <tr>
                            <td>No hay registros de examenes a cuenta</td>
                            <td></td>
                            <td></td>
                        </tr>
                        `;
                }
                $('#lstSaldos').append(contenido); 
            });    
    }

    

    function selectorPago(pago, id) {

        if (pago != 'B') {
            $('#SPago, #Tipo, #Sucursal, #NroFactura').val(" ");
        }

        if(['B','A', ''].includes(pago)) {
            
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
                    checkExamenesCuenta(id)
                }
            });
        }
    }

    async function checkExamenesCuenta(id){

        $.get(lstExDisponibles, {Id: id})
            .done(await function(response){

                if(response && response.length > 0) {

                    $('#alertaExCta').show();
                    $('#PagoLaboral, #Pago ').val('P');
                    return true;
                } else {
                    $('#alertaExCta').hide();
                    return false;
                }
            })
    }

    function saveFichaLaboral(...params) {
        preloader('on');

        $.post(saveFichaAlta, {
            paciente: params[0],
            cliente: params[1],
            art: params[2],
            tareaRealizar: params[3],
            tipoPrestacion: params[4],
            tipo: params[5],
            pago: params[6],
            horario: params[7],
            observaciones: params[8],
            ultimoPuesto: params[9],
            puestoActual: params[10],
            sectorActual: params[11],
            ccosto: params[12],
            antiguedadPuesto: params[13],
            fechaIngreso: params[14],
            fechaEgreso: params[15],
            antiguedadEmpresa: params[16],
            fechaPreocupacional: params[17],
            fechaUltPeriod: params[18],
            fechaExArt: params[19],
            Id: params[20],
            Spago: params[21],
            TipoF: params[22],
            SucursalF: params[23],
            NumeroF: params[24],
            NumeroProvF: params[25],
            Autoriza: params[26],
            _token: TOKEN,
            }) 
            .done(function(response) {
                preloader('off');
                toastr.success(response.msg);
                mostrarFinanciador();
                selectMedioPago();
                selectorPago(params[6], IDFICHA);
                getMap(params[1], params[2]);
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
    }

    async function checkExCuentaDisponible(id) {
        $data = await $.get(checkDisponibilidad, {Id: id});
        console.log("funcion: " + $data);
        return $data > 0 ? true : false;
    }

    function marcarPago(pago) {

        switch (pago) {
            case 'P':
                $('#PagoLaboral').val('P');
                break;
            case 'A':
                $('#PagoLaboral').val('A');
                break;
            case 'B':
                $('#PagoLaboral').val('B');
                break;
            default:
                $('#PagoLaboral').val('');
                break;
        }
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

                    $.each(mapas, function(index, d){

                        let contenido = `<option value="${d.Id}">${d.Nro} | Empresa: ${d.RSE} - ART: ${d.RSArt}</option>`;
    
                        $('#mapas, #mapasN').append(contenido);
                    });
                } 
            })
    }
    
});