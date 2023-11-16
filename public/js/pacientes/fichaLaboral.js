$(document).ready(function () {

    let pagoLaboral = $('#PagoLaboral').val(), changeTipo = $('input[name="TipoPrestacion"]:checked').val();
    $('.nuevaPrestacionModal, .observacionesModal, .nuevaPrestacion, .ObBloqueoEmpresa, .ObBloqueoArt, .ObEmpresa, .ObsPaciente, .ObsPres, .Factura, .TareaRealizar, .UltimoPuesto, .PuestoActual, .SectorActual, .AntiguedadPuesto, .AntiguedadEmpresa, .FechaIngreso, .FechaEgreso, .selectMapaPres, .Autoriza').hide();

    quitarDuplicados("#Horario");
    quitarDuplicados("#Tipo");
    quitarDuplicados("#TipoPrestacion");
    quitarDuplicados('#PagoLaboral');
    calcularAntiguedad();
    mostrarFinanciador();
    
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
        '.AntiguedadEmpresa': ['EGRESO', 'OCUPACIONAL', 'OTRO', 'PERIODICO', 'ART'],
        '.FechaIngreso': ['EGRESO', 'OCUPACIONAL', 'OTRO', 'PERIODICO', 'ART'],
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
        pagoLaboral = $(this).val(); 
        $('#Pago').val(pagoLaboral);
    });

    $('input[name="TipoPrestacion"]').change(function(){
        changeTipo = $(this).val(); 
        $('#tipoPrestacionPres').val(changeTipo);
    });

    $('#Pago').val(pagoLaboral);
    $('#tipoPrestacionPres').val(changeTipo);

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
                if (response.Bloqueado == 0) {
                    deshabilitarBloqueo();
                } else {
                    cargarBloqueo(response);
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
    $(document).on('click', '#guardarFicha', function(){

        let paciente = ID;
            cliente = $('#selectClientes').val(),
            art = $('#selectArt').val(),
            tipoPrestacion =  $('input[name="TipoPrestacion"]:checked').val(),
            tareaRealizar = $('#TareaRealizar').val(),
            tipo = $('#Tipo').val(),
            pago = $('#PagoLaboral').val(),
            horario = $('#Horario').val(),
            observaciones = $('#Observaciones').val(),
            ultimoPuesto = $('#UltimoPuesto').val(),
            puestoActual = $('#PuestoActual').val(),
            sectorActual = $('#SectorActual').val(),
            ccosto = $('#CCostos').val(),
            antiguedadPuesto = $('#AntiguedadPuesto').val(),
            fechaIngreso = $('#FechaIngreso').val(),
            fechaEgreso = $('#FechaEgreso').val(),
            antiguedadEmpresa = $('#AntiguedadEmpresa').val();

        if(tipoPrestacion === '' || tipoPrestacion === undefined){
            toastr.warning('¡El campo tipo de prestación es obligatorio!', 'Alerta');
            return;
        }

        if(cliente === '0' && art === '0'){
            toastr.warning('¡Debe seleccionar una empresa o una art!', 'Alerta');
            return;
        }

        $.ajax({
            url: saveFichaAlta,
            type: 'post',
            data: {
                paciente: paciente,
                cliente: cliente,
                art: art,
                tareaRealizar: tareaRealizar,
                tipoPrestacion: tipoPrestacion,
                tipo: tipo,
                pago: pago,
                horario: horario,
                observaciones: ObservacionesFicha,
                ultimoPuesto: ultimoPuesto,
                puestoActual: puestoActual,
                sectorActual: sectorActual,
                ccosto: ccosto,
                antiguedadPuesto: antiguedadPuesto,
                fechaIngreso: fechaIngreso,
                fechaEgreso: fechaEgreso,
                antiguedadEmpresa: antiguedadEmpresa,
                _token: TOKEN,
            },
            success: function(){
                toastr.success('¡Los datos se han actualizado. Nos redirigimos a la nueva prestación.!', 'Perfecto');
                mostrarFinanciador();
                selectMedioPago();
                setTimeout(() => {
                    checkObservaciones();
                }, 2000);
            },
            error: function(xhr) {
                toastr.danger('Hubo un problema para procesar la información. Consulte con el administrador del sistema.', 'Error');
                console.error(xhr);
            }
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
            type: 'get',
            data: {
                Id: ID,
                _token: TOKEN
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
                    let estado = $('#tipoPrestacionPres').val();
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
        let dateEgreso = new Date(egreso);

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
            console.log("la opcion dentro funcion: " + opcion)
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

        }
    }

    async function checkObservaciones() {
        if (ID === '') return;
        $('.ObBloqueoEmpresa, .ObBloqueoArt, .ObEmpresa, .ObsPaciente').hide();
        $('.seguirAl').prop('disabled', false).removeAttr('title');

        try {
            const response = await $.get(checkObs, { Id: ID });
            
            let obsArt = response.obsArt, obsEmpresa = response.obsEmpresa, obsPaciente = response.obsPaciente;


            if([obsArt.Motivo, obsArt.Observaciones, obsEmpresa.Motivo, obs.Observaciones, obsPaciente.Observaciones].every(value => value === '')){

                $('.fichaLaboralModal, .observacionesModal').hide();
                $('.nuevaPrestacion').show();
            
            }else{

                $('.fichaLaboralModal').hide();
                $('.observacionesModal').show();

                if(obsArt.Motivo !== '') {
                    $('.ObBloqueoArt').show();
                    $('.seguirAl').prop('disabled', true).attr('title', 'Boton bloqueado');
                }

                if(obsArt.Observaciones !== '') {
                    $('.ObArt').show();
                }

                if(obsEmpresa.Observaciones !== '') {
                    $('.ObEmpresa').show();
                }

                if(obsEmpresa.Motivo !== '') {
                    $('.ObEmpresa').show();
                    $('.seguirAl').prop('disabled', true).attr('title', 'Boton bloqueado');
                }

                if(obsPaciente.Observaciones !== '') {
                    $('.ObPaciente').show();
                }
            }
            
        } catch (error) {
            console.error(error);
            toastr.danger('Se ha producido un error. Consulte con el administrador', 'Error');
        }
    }
    

    function stopPrestacion(campo) {

        if(campo !== ''){
            $('.seguirAl').attr('disabled', 'disabled').attr('title', 'Boton bloqueado');
        }
    }

    
});