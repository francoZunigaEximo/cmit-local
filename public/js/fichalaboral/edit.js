$(function () {
 
    quitarDuplicados("#Horario");
    quitarDuplicados("#Tipo");
    quitarDuplicados("#TipoPrestacion");
    quitarDuplicados('#PagoLaboral');
    calcularAntiguedad();

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

    $('#selectClientes').select2({
        placeholder: 'Seleccionar Cliente',
        dropdownParent: $('#fichaLaboral'),
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
        dropdownParent: $('#fichaLaboral'),
        placeholder: 'Seleccionar ART',
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
        return pago === 'G' ? $('.Autoriza').show() : $('.Autoriza').hide();
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

        let paciente = $('#IdPaciente').val(),
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
            antiguedadEmpresa = $('#AntiguedadEmpresa').val(),
            Id = $('#IdFichaLaboral').val(),
            idPrestacion = $('#idPrestacion').val();

        if(tipoPrestacion === 'OTRO' && tipoPrestacionPresOtros) {
            tipoPrestacion = tipoPrestacionPresOtros;
            $('#tipoPrestacionHidden').val(tipoPrestacion);
        }

        if([0, null, undefined, ''].includes(tipoPrestacion)){
            toastr.warning('¡El campo tipo de prestación es obligatorio!', '', {timeOut: 1000});
            return;
        }

        if([0, null, undefined, ''].includes(cliente) && [0, null, undefined, ''].includes(art)){
            toastr.warning('¡Debe seleccionar una empresa o una art!','', {timeOut: 1000});
            return;
        }
        
        if(tipoPrestacion === 'ART' && ([0, null, undefined, ''].includes(art))){
            toastr.warning('¡Debe seleccionar una ART para el tipo de prestación ART!','', {timeOut: 1000});
            return;
        }

        if(tipoPrestacion === 'ART' && (![0, null, undefined, ''].includes(art)) && ([0, null, undefined, ''].includes(cliente))){
            toastr.warning('¡Debe seleccionar una Empresa para el tipo de prestación ART y la ART seleccionada!','', {timeOut: 1000});
            return;
        }
        
        if(tipoPrestacion !== 'ART' && ([0, null, undefined, ''].includes(cliente))){
            toastr.warning('¡Debe seleccionar una empresa para el tipo de prestación seleccionado!','',{timeOut: 1000});
            return;
        }

        preloader('on');
        $.post(saveFichaAlta, {
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
            fechaPreocupacional: fechaPreocupacional,
            fechaUltPeriod: fechaUltPeriod,
            fechaExArt: fechaExArt,
            Id: Id,
            idPrestacion: idPrestacion,
            _token: TOKEN,
            }) 
            .done(function(response) {
                preloader('off');
                toastr.success(response.msg,'', {timeOut: 1000});
                swal('Atención', 'Se actualizará la pantalla en segundos...', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            })
            .fail(function(jqXHR) {
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);
                checkError(jqXHR.status, errorData.msg);
                return;
                
            });
    });

    //Calcular Antiguedad en la Empresa en FichaLaboral
    $('#FechaIngreso, #FechaEgreso').change(function(){
        calcularAntiguedad();
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

    function filtrarTipoPrestacion(idFinanciador, estado) {
        preloader('on');
        $.ajax({
            url: getTipoPrestacion,
            type: "GET",
            data: {
                financiador: idFinanciador,
                _token: TOKEN
            },
            success: function(response) {
                preloader('off');
                let tiposPrestacion = response.tiposPrestacion;
                
                if(tiposPrestacion) {
                    $('#tipoPrestacionPres').empty().append('<option value="" selected>Elija una opción...</option>');

                    for(let index = 0; index < tiposPrestacion.length; index++){
                        let tipoPrestacion = tiposPrestacion[index];
                        $('#tipoPrestacionPres').append('<option value="' + tipoPrestacion.nombre + '">' + tipoPrestacion.nombre + '</option>');
                    }
                    
                    if(estado) {    
                        $('#tipoPrestacionPres').val(estado);
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
        let dateEgreso = egreso ? new Date(egreso) : new Date();

        let diff = dateEgreso.getFullYear() - dateIngreso.getFullYear();

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

    
});