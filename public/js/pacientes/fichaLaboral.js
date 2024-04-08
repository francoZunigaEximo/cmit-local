$(document).ready(function () {

    let pagoLaboral = $('#PagoLaboral').val(), changeTipo = $('input[name="TipoPrestacion"]:checked').val();
    $('.nuevaPrestacionModal, .observacionesModal, .nuevaPrestacion, .ObBloqueoEmpresa, .ObBloqueoArt, .ObEmpresa, .ObsPaciente, .ObsPres, .Factura, .TareaRealizar, .UltimoPuesto, .PuestoActual, .SectorActual, .AntiguedadPuesto, .AntiguedadEmpresa, .FechaIngreso, .FechaEgreso, .selectMapaPres, .Autoriza, .listadoExCta, #alertaExCta').hide();

    quitarDuplicados("#Horario");
    quitarDuplicados("#Tipo");
    quitarDuplicados("#TipoPrestacion");
    quitarDuplicados('#PagoLaboral');
    calcularAntiguedad();
    mostrarFinanciador();
    checkExamenesCuenta($('selectClientes').val());
    
    toastr.options = {
        closeButton: true,   
        progressBar: true,    
        timeOut: 3000,        
    };
 
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
        $('.nuevaPrestacion').hide();
        $('.observacionesModal').hide();
        $('.fichaLaboralModal').show();
    });

    $('.seguirAl').on('click', function(){
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

    $('#PagoLaboral').change(function(){
        let valor = $(this).val(); 
        $('#Pago').val(valor).find('option[value="' + valor + '"]').prop('selected', true);
    });

    $('input[name="TipoPrestacion"]').change(function(){
        changeTipo = $(this).val(); 
        $('#tipoPrestacionHidden').val(changeTipo);

        if(changeTipo === 'OTRO') {
            $("#divtipoPrestacionPresOtros").show();
        }
        else {
            $("#divtipoPrestacionPresOtros").hide();
        }
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

        if(pago === 'G'){
            $('.Autoriza').show();
        }else{
            $('.Autoriza').hide();
        }
    });
    
    $('#selectClientes').on('select2:select', function (e) {
        if (!confirm('¿Está seguro que desea seleccionar esta empresa para el paciente?')) {
            $(this).val(null).trigger('change.select2');
            e.stopPropagation();
        }
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

                if (data.Bloqueado === 0) {
                    deshabilitarBloqueo();
                } else {
                    cargarBloqueo(data);
                }
            }
        });
    });

    $(document).on('change', '#Pago', function(){
        
        selectMedioPago();
    });

    //Habilitamos el botón de guardar
    $('#selectClientes').on('change', function(){
        
        if($(this).select2('data').map(option => option.id).length === 0){
            $('#guardarFicha').removeAttr('disabled').removeAttr('title', 'Botón habilitado').removeAttr('data-toggle', 'tooltip').removeAttr('data-placement', 'top');
        }
    });

    //Guardar FichaLaboral
    $(document).on('click', '#guardarFicha', function(e){
        e.preventDefault();

        let paciente = ID,
            cliente = $('#selectClientes').val(),
            art = $('#selectArt').val(),
            tipoPrestacion =  $('input[name="TipoPrestacion"]:checked').val(),
            tipoPrestacionPresOtros =  $('#tipoPrestacionPresOtros').val(),
            tareaRealizar = $('#TareaRealizar').val(),
            tipo = $('#TipoJornada').val(),
            pago = $('#PagoLaboral').val(),
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
            antiguedadEmpresa = $('#AntiguedadEmpresa').val();

        if(tipoPrestacion === 'OTRO' && tipoPrestacionPresOtros) {
            tipoPrestacion = tipoPrestacionPresOtros;
            $('#tipoPrestacionHidden').val(tipoPrestacion);
        }

        if(tipoPrestacion === '' || tipoPrestacion === undefined){
            toastr.warning('¡El campo tipo de prestación es obligatorio!', 'Alerta');
            return;
        }
        if(cliente == '0' && art == '0' || cliente === '' && art === '' || cliente === null && art === null){
            toastr.warning('¡Debe seleccionar una empresa o una art!', 'Alerta');
            return;
        }
        
        if(tipoPrestacion === 'ART' && (art === '0' || art === '')){
            toastr.warning('¡Debe seleccionar una ART para el tipo de prestación ART!', 'Alerta');
            return;
        }
        
        if(tipoPrestacion != 'ART' && (cliente === '0' || cliente === '')){
            toastr.warning('¡Debe seleccionar una empresa para el tipo de prestación seleccionado!', 'Alerta');
            return;
        }

        preloader('on');
        $.post(saveFichaAlta, {paciente: paciente,
            cliente: cliente,
            art: art,
            tareaRealizar: tareaRealizar,
            tipoPrestacion: tipoPrestacion,
            tipo: tipo,
            pago: pago,
            horario: horario,
            observaciones: observaciones,
            ultimoPuesto: ultimoPuesto,
            puestoActual: puestoActual,
            sectorActual: sectorActual,
            ccosto: ccosto,
            antiguedadPuesto: antiguedadPuesto,
            fechaIngreso: fechaIngreso,
            fechaEgreso: fechaEgreso,
            antiguedadEmpresa: antiguedadEmpresa,
            fechaPreocupacional: fechaPreocupacional,
            fechaUltPeriod: fechaUltPeriod,
            fechaExArt: fechaExArt,
            _token: TOKEN,
            }) 
            .done(function() {
                preloader('off');
                toastr.success('¡Los datos se han actualizado. Nos redirigimos a la nueva prestación.!', 'Perfecto');
                mostrarFinanciador();
                selectMedioPago();
                setTimeout(() => {
                    checkObservaciones();
                }, 2000);

            })
            .fail(function(xhr) {
                preloader('off');
                toastr.error('Hubo un problema para procesar la información. Consulte con el administrador del sistema.', 'Error');
                console.error(xhr);
            });
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

    //Bloqueo de cliente si existe
    function cargarBloqueo(response) {
        let razonSocial = response.RazonSocial,
            motivo = response.Motivo,
            identificacion = response.Identificacion;

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

        $.ajax({
            url: verificarAlta,
            type: 'GET',
            data: {
                Id: ID,
            },
            success: function(response){
                let verificar = response.fichaLaboral, cliente = response.cliente, clienteArt = response.clienteArt;

                if(verificar !== undefined && verificar.Id ) {

                    $('.updateFinanciador').empty();
    
                    let prestacion = `<select class="form-select" name="financiador" id="financiador">
                                        <option id="emptyFinanciador" value="" selected="">Elija una opción...</option>
                                        <option id="artFinanciador" value="${clienteArt.Id}">ART:  ${clienteArt.RazonSocial} - ${clienteArt.Identificacion}</option>
                                        <option id="empresaFinanciador" value="${cliente.Id}">EMPRESA: ${cliente.RazonSocial} - ${cliente.Identificacion}</option>
                                    </select>`;

                    $('.updateFinanciador').append(prestacion);

                    $('#financiador').change(function() {
                        filtrarTipoPrestacion($('#financiador').val(), null);
                    });

                    let estado = $('#tipoPrestacionHidden').val();
                    
                    if (estado !== 'ART') {
                        $("#empresaFinanciador").prop("selected", true);
                        $('.selectMapaPres').hide();
                    } else if (estado === '') {
                        $("#emptyFinanciador").prop("selected", true);
                        $('.selectMapaPres').hide();
                    } else {
                        $("#artFinanciador").prop("selected", true);
                        $('.selectMapaPres').show();
                    }

                    filtrarTipoPrestacion($('#financiador').val(), estado);

                 }

            }
        });   
     }

    function filtrarTipoPrestacion(idFinanciador, estado) {

        $.ajax({
            url: getTipoPrestacion,
            type: "GET",
            data: {
                financiador: idFinanciador,
                _token: TOKEN
            },
            success: function(response) {
                let tiposPrestacion = response.tiposPrestacion;
                
                if(tiposPrestacion) {
                    $('#tipoPrestacionPres').empty();
                    $('#tipoPrestacionPres').append('<option selected>Elija una opción...</option>');

                    tiposPrestacion.forEach(function(tipoPrestacion) {
                        $('#tipoPrestacionPres').append('<option value="' + tipoPrestacion.nombre + '">' + tipoPrestacion.nombre + '</option>');
                    });
                    
                    if(estado) {    
                        $('#tipoPrestacionPres').val(estado);
                    }
                }
            }
        });
    }

    function activarMapas(estado){
        if (estado === 'ART') {
            $("#artFinanciador").prop("selected", true);
            $("empresaFinanciador").prop("selected", false);
            $('.selectMapaPres').show();
        }else{
            $("#artFinanciador").prop("selected", false);
            $("#empresaFinanciador").prop("selected", true);
            $('.selectMapaPres').hide();
        }
    }

    //Creamos función para trabajar cambios en fechas automaticamente
    function calcularAntiguedad(){
        
        let ingreso = $('#FechaIngreso').val();
        let egreso = $('#FechaEgreso').val();

        let dateIngreso = new Date(ingreso);
        let dateEgreso = egreso ? new Date(egreso) : new Date();

        let diff = dateEgreso.getFullYear() - dateIngreso.getFullYear();

        if (dateEgreso.getMonth() < dateIngreso.getMonth() || (dateEgreso.getMonth() === dateIngreso.getMonth() && dateEgreso.getDate() < dateIngreso.getDate())) {
            diff--;
        }

        $('#AntiguedadEmpresa').val(diff);
    }

    function quitarDuplicados(selector) {
        let seleccion = $(selector).val();
        let countSeleccion = $(selector + " option[value='" + seleccion + "']").length;
    
        if (countSeleccion > 1) {
            $(selector + " option[value='" + seleccion + "']:gt(0)").hide();
        }
    }

    function opcionesFicha(option) {
        Object.entries(listOpciones).forEach(([campo, opciones]) => {
            if (opciones.includes(option)) {
                $(campo).show();
            } else {
                $(campo).hide();
            }
        });
    }

    function selectMedioPago()
    {
        let opcion = $('#Pago').val()
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
            $('#SPago').empty().append(contenido);
       
        }else{

            $('.SPago').hide();
            $('.ObsPres').hide();
            $('.Factura').hide();
            $('.Autoriza').hide();

        }
    }

    async function checkObservaciones() {

        toastr.options = {
            closeButton: true,   
            progressBar: true,     
            timeOut: 3000,        
        };
     
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
                        <strong> No existen observaciones para generar la prestación. </strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;

                $('.fichaLaboralModal, .observacionesModal').hide();
                $('.nuevaPrestacion').show();

                $(".messagePrestacion").html(alerta);
            
            }else{

                $('.fichaLaboralModal').hide();
                $('.observacionesModal').show();

                if(obsArt.Motivo !== '' && obsArt.Motivo !== null) {
                    $('.ObBloqueoArt').show();
                    $('.ObBloqueoArt p').text(obsArt.Motivo);
                    $('.seguirAl').prop('disabled', true).attr('title', 'Boton bloqueado');
                }

                if(obsArt.Observaciones !== '' && obsArt.Observaciones !== null) {
                    $('.ObArt').show();
                    $('.ObArt p').text(obsArt.Observaciones);
                }
                
                if(obsEmpresa.Observaciones !== '' && obsEmpresa.Observaciones !== null) {
                    $('.ObEmpresa').show();
                    $('.ObEmpresa p').text(obsEmpresa.Observaciones);
                }

                if(obsEmpresa.Motivo !== '' && obsEmpresa.Motivo !== null) {
                    $('.ObBloqueoEmpresa').show();
                    $('.ObBloqueoEmpresa p').text(obsEmpresa.Motivo);
                    $('.seguirAl').prop('disabled', true).attr('title', 'Boton bloqueado');
                }
                
                if(obsPaciente.Observaciones !== '' && obsPaciente.Observaciones !== null) {
                    $('.ObPaciente').show();
                    $('.ObPaciente p').text(obsPaciente.Observaciones);
                }
            }
            
        } catch (error) {
            console.error(error);
            toastr.warning('Se ha producido un error. Consulte con el administrador', 'Error');
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
                    
                        contenido = `
                        <tr>
                            <td>${r.Precarga === '' ? '-' : r.Precarga}</td>
                            <td>${r.NombreExamen}</td>
                        </tr>
                        `;
                        $('#lstSaldos').append(contenido);  
                    });
                }else{
                    contenido = `
                        <tr>
                            <td>No hay registros de examenes a cuenta</td>
                            <td></td>
                            <td></td>
                        </tr>
                        `;
                        $('#lstSaldos').append(contenido); 
                }

                
                
            });    
    }

    function checkExamenesCuenta(id){

        $.get(lstExDisponibles, {Id: id})
            .done(function(response){
                if(response && response.length > 0) {
                    console.log('check: ' + response)
                    $('#alertaExCta').show();
                    $('#PagoLaboral').val('P');
                } else {
                    $('#alertaExCta').hide();
                    $('#PagoLaboral').val('');
                }
            })
    }

    function preloader(opcion) {
        $('#preloader').css({
            opacity: '0.3',
            visibility: opcion === 'on' ? 'visible' : 'hidden'
        });
    }
    
});