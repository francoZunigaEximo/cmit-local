$(document).ready(function () {

    let fichaLaboralId = (checkFichaLaboral) ? checkFichaLaboral : '',
        pagoLaboral = $('#PagoLaboral').val(),
        changeTipo = $('#TipoPrestacion').val(),
        AntiguedadEmpresa = $('#AntiguedadEmpresa').val();
    
    quitarDuplicados("#Horario");
    quitarDuplicados("#Tipo");
    quitarDuplicados("#TipoPrestacion");
    quitarDuplicados('#PagoLaboral');
    calcularAntiguedad();
    mostrarFull();
    actualizarMensaje();

    const listOpciones = {
        '.TareaRealizar': ['OCUPACIONAL', 'OTRO', 'INGRESO'],
        '.PuestoActual': ['OCUPACIONAL', 'OTRO', 'PERIODICO', 'EGRESO'],
        '.UltimoPuesto': ['OCUPACIONAL', 'OTRO', 'INGRESO'],
        '.SectorActual': ['PERIODICO', 'OCUPACIONAL', 'EGRESO', 'OTRO'],
        '.AntiguedadPuesto': ['PERIODICO', 'OCUPACIONAL', 'EGRESO', 'OTRO'],
        '.AntiguedadEmpresa': ['EGRESO', 'OCUPACIONAL', 'OTRO', 'PERIODICO', ],
        '.FechaIngreso': ['EGRESO', 'OCUPACIONAL', 'OTRO', 'PERIODICO'],
        '.FechaEgreso': ['EGRESO', 'OCUPACIONAL', 'OTRO'],
    };
    
    //Hack para forzar la recarga
    $(document).ready(function () {
        let inputShow = $('#TipoPrestacion').val();
        opcionesFicha(inputShow);
    });
   
    // Tipo de prestaciones muestra opciones
    $('#TipoPrestacion').change(function () {
        let inputShow = $(this).val();
        opcionesFicha(inputShow);
    });

    $('#PagoLaboral').change(function(){
        pagoLaboral = $(this).val(); 
        $('#Pago').val(pagoLaboral);
    });

    $('#TipoPrestacion').change(function(){
        changeTipo = $(this).val(); 
        $('#tipoPrestacionPres').val(changeTipo);
    });

    $('#Pago').val(pagoLaboral);
    $('#tipoPrestacionPres').val(changeTipo);

    $('.full').hide();

    $('#selectClientes').select2({
        placeholder: 'Seleccionar Cliente',
        language: 'es',
        allowClear: true,
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
        language: 'es',
        allowClear: true,
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

    //Ocultamos campos
    $('.TareaRealizar, .UltimoPuesto, .PuestoActual, .SectorActual, .AntiguedadPuesto, .AntiguedadEmpresa, .FechaIngreso, .FechaEgreso, .selectMapaPres, .Autorizado').hide();

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
            tipoPrestacion = $('#TipoPrestacion').val(),
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
        
        if(tipoPrestacion === ''){
            swal('Alerta', 'El campo tipo de prestación es obligatorio', 'warning');
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
                observaciones: observaciones,
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
                swal("Perfecto", "¡Los datos fueron almacenados correctamente.!", "success");

                mostrarFull(fichaLaboralId);
                nextPrestaciones();

            },
            error: function(xhr) {
                swal("Error", "Hubo un problema para procesar la información. Consulte con el administrador del sistema.", "error");
                console.error(xhr);
            }
        });
    });

    //Boton para volver de FichaLaboral
    $('#btnVolverFicha').click(function(){

        $('.tab-pane').removeClass('active show');
        $('#datosPersonales').addClass('active show');
        $('.nav-link[href="#datosPersonales"]').tab('show');
    });

    $(document).on('click','#editFull', function(){
        $('.full').hide();
        
        $('.selectClientes').show();
        $('.selectArt').show(); 
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

    //Generamos los cambios
    function nextPrestaciones(){
    
        $('#pacientePrestacion').removeAttr('disabled').removeAttr('title', 'Botón habilitado').removeAttr('data-toggle', 'tooltip').removeAttr('data-placement', 'top');
        $('#mensajeFichaLaboral').hide();

        $('.tab-pane').removeClass('active show');
        $('#prestaciones').addClass('active show');
        $('.nav-link[href="#prestaciones"]').tab('show');
    }

    //Bloqueo de cliente si existe
    function cargarBloqueo(response) {
        let razonSocial = response.RazonSocial;
        let motivo = response.Motivo;
        let identificacion = response.Identificacion;

        $('#razonSocialModal').text(razonSocial);
        $('#motivoModal').text(motivo);
        $('#identificacionModal').text(identificacion);

        $('#guardarFicha').attr('disabled', 'disabled').attr('title', 'Botón bloqueado').attr('data-toggle', 'tooltip').attr('data-placement', 'top');

        swal("¡Cliente Bloqueado!", "El cliente " +  razonSocial + " | cuit: " + identificacion + " se encuentra bloqueado por el siguiente motivo: " + motivo +  ". No podrá avanzar con el alta. Se ha bloqueado el botón de registro.","info");
    }

    function deshabilitarBloqueo() {
        $('#guardarFicha').removeAttr('disabled').removeAttr('title', 'Botón habilitado').removeAttr('data-toggle', 'tooltip').removeAttr('data-placement', 'top');
    }

    function actualizarMensaje() {
        if (!fichaLaboralId) {
            $('#mensajeFichaLaboral').html(`
                <div class="alert alert-dark" role="alert">
                    <strong> Atención: </strong> ¡Debe registrar una ficha laboral para poder generar prestaciones. Botón 'Nuevo' deshabilitado!
                </div>
            `);
            $('#pacientePrestacion').prop('disabled', true);
        } else {
            $('#mensajeFichaLaboral').empty();
            $('#pacientePrestacion').prop('disabled', false);
        }
     }
     
     function mostrarFull(){

        $.ajax({
            url: verificarAlta,
            type: 'get',
            data: {
                Id: ID,
                _token: TOKEN
            },
            success: function(response){
                let verificar = response.fichaLaboral,
                    cliente = response.cliente,
                    clienteArt = response.clienteArt;

                if(verificar !== undefined && verificar.Id ) {
                    $('.full').show();
                    $('.selectClientes, .selectArt').hide();

                    let contenido = `Empresa: ${cliente.RazonSocial} - ${cliente.Identificacion} | ART: ${clienteArt.RazonSocial}`;
                    $('.fullInput').val(contenido);

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

    let opcion = $('#Pago').val();
    selectMedioPago(opcion);

    $(document).on('change', '#Pago', function(){
        
        let option = $(this).val();
        selectMedioPago(option);
    });

    function selectMedioPago(opcion)
    {

        if(opcion === 'B'){

            let contenido = `
                <option value="" selected>Elija una opción...</option>
                <option value="A">Efectivo</option>
                <option value="B">Débito</option>
            `;

            $('#SPago').empty().append(contenido);
       
        }else if(opcion === 'P') {

            let contenido = `
                <option value="" selected>Elija una opción...</option>
                <option value="C">Crédito</option>
                <option value="D">Cheque</option>
                <option value="E">Otro</option>
                <option value="F">Transferencia</option>
                <option value="G">Sin Cargo</option>
            `;
            
            $('#SPago').empty().append(contenido);
        }else {

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

            $('#SPago').empty().append(contenido);
        }
    }


});