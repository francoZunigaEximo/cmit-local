$(function() {

    let fecha = $('#FechaVto').val(), 
        opcion = $('#pago').val(), 
        opcionPago = $('#SPago').val(), 
        empresa = $('#empresa').val(), 
        art = $('#art').val(),
        paraEmpresa =$('#paraEmpresa').val();

    precargaMapa(empresa, art);
    examenesCta(empresa);
    checkExamenes(ID);
    checkerIncompletos(ID);
    checkEstadoEnviar(ID);
    loadListAdjPrestacion();
    lstResultadosPrest(IDPACIENTE);

    quitarDuplicados("#tipoPrestacion");
    quitarDuplicados("#pago");
    quitarDuplicados("#SPago");
    quitarDuplicados("#Evaluacion");
    quitarDuplicados("#Calificacion");
    quitarDuplicados("#TipoPrestacion");
    quitarDuplicados("#mapas");
    quitarDuplicados("#Tipo");

    cambiosVencimiento(fecha);
    selectMedioPago(opcion, ID);
    getFact();
    checkBloq();
    comentariosPrivados();
    cargarAutorizados();

    $('#modificar').hide();

    $('#addObs').on('hide.bs.modal', function () {
        $('#confirmar').show();
        $('#modificar').hide();
    });

    if(cerrado === '1') {
        $('.bloquearExamenes').prop('disabled', true);
    }

    //Hack de carga
    $(document).ready(function(){
        getAutoriza(opcionPago);
    });
    
    $(document).on('change', '#empresa, #art, #TipoPrestacion', function(){
        let emp = $('#empresa').val(), art = $('#art').val();
        examenesCta(emp);
        precargaMapa(emp, art);
    });

    $('.alert').hide();

    $(document).on('change', '#pago', function(){
        selectMedioPago();
    });

    $(document).on('change', '#SPago', function(){
        let data = $(this).val();
        getAutoriza(data);
    });

    $(document).on('click', '#actualizarPrestacion', function(e){
        e.preventDefault();

        let tipoPrestacion = $('#TipoPrestacion').val(),
            pago = $('#pago').val(),
            fecha = $('#Fecha').val();
            empresa = $('#empresa').val(),
            art = $('#art').val(),
            IdPaciente = $('#IdPaciente').val(),
            spago = $('#SPago').val(),
            observaciones = $('#Observaciones').val(),
            tipo = $('#Tipo').val(),
            sucursal = $('#Sucursal').val(),
            nroFactura = $('#NroFactura').val(),
            mapas = $('#mapas').val(),
            autorizado = $('#Autorizado').val();
            IdEvaluador = $('#IdEvaluador').val(),
            Evaluacion = $('#Evaluacion').val(),
            Calificacion = $('#Calificacion').val(),
            SinEval =$('#SinEval').prop('checked');
            ObsExamenes = $('#ObsExamenes').val(),
            FechaAnul = $('#FechaAnul').val(),
            Obs = $('#Obs').val();
            NroFactProv = $('#NroFactProv').val();
 
        let factura = [tipo, sucursal, nroFactura];

         //Validamos la factura
        if (spago === 'G' && !autorizado){ 
            toastr.warning('Si el medio de pago es gratuito, debe seleccionar quien autoriza.', '', {timeOut: 1000});
            return;
        }

        if (pago === 'B' && !spago) {
            toastr.warning('Debe seleccionar un "medio de pago" cuando la "forma de pago" es "contado"','', {timeOut: 1000});
            return;
        }
       
        if (!pago) {
            toastr.warning('Debe seleccionar una "forma de pago"','', {timeOut: 1000});
            return;
        }

        if (pago === 'B' && (factura.some(condicion => !condicion))){
            toastr.warning('El pago es contado, asi que debe agregar el número de factura para continuar.','', {timeOut: 1000});
            return;
        }

        if (!tipoPrestacion){
            toastr.warning("Atención", "El tipo de prestación no puede ser un campo vacío", "warning", {timeOut: 1000});
            return;
        }
        
        if (!art && tipoPrestacion === 'ART') {
            toastr.warning("Debe seleccionar un cliente ART si el tipo de prestación es ART",'', {timeOut: 1000});
            return;
        }
        
        if ((!art || art === '0') && tipoPrestacion === 'ART' && !mapas) {
            toastr.warning("Debe seleccionar un mapa vigente si la prestación es ART y tiene un cliente ART cargado",'', {timeOut: 1000});
            return;
        }

        if (tipoPrestacion === 'ART' && !mapas) {
            toastr.warning("Debe seleccionar un mapa si la prestación es ART",'', {timeOut: 1000});
            return;
        }
   
        preloader('on');
        $.ajax({
            url: updatePrestacion,
            type: 'Post',
            data: {
                Id: ID,
                TipoPrestacion: tipoPrestacion,
                Pago: pago,
                Fecha: fecha,
                SPago: spago,
                Mapas: mapas,
                Observaciones: observaciones,
                Empresa: empresa,
                IdPaciente: IdPaciente,
                Art: art,
                IdEvaluador: IdEvaluador,
                Evaluacion: Evaluacion,
                Calificacion: Calificacion,
                SinEval: SinEval,
                ObsExamenes: ObsExamenes,
                tipo: tipo,
                sucursal: sucursal,
                nroFactura: nroFactura,
                FechaAnul: FechaAnul,
                Obs: Obs,
                NroFactProv: NroFactProv,
                _token: TOKEN
            },
            success: function(response){
                preloader('off');
                toastr.success(response.msg,'', {timeOut: 1000});
                setTimeout(function(){
                    location.reload();
                }, 3000);  
            },
            error: function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;  
            }
        });

    });

    $(document).on('click', '#btnVolver', function(e){
        e.preventDefault();
        let location = UBICACION;
        return location === 'prestaciones' ? window.location.replace(GOPRESTACIONES) : window.location.replace(GOPACIENTES);
    });

    $(document).on('change', '#empresa', function(){

        let empresa = $(this).val();
        
        if(!empresa) return;

        $.get(checkParaEmpresa, {empresa: empresa})
            .done(function(response){
                let data = response.cliente;
                $('#paraEmpresa').val(data.ParaEmpresa);
            })
            .fail(function(jqXHR){
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;  
            });
    });

    $(document).on('click','.cerrar, .finalizar, .entregar, .eEnviar', function(e) {
        e.preventDefault();
        $(this).prop('readonly', false);

        let tipo = 
            $(this).hasClass('cerrar') ? 'cerrar' : ($(this).hasClass('finalizar') ? 'finalizar' : ($(this).hasClass('entregar') ? 'entregar' : ($(this).hasClass('eEnviar') ? 'eEnviar' : '')));
        preloader('on')
        $.ajax({    
            url: actualizarEstados, 
            type: 'POST',
            data: {
                _token: TOKEN,
                Id: ID,
                Tipo: tipo
            },
            success: function(response){
                preloader('off');
                switch (tipo) {
                    
                    case 'cerrar':
                        if(response.Cerrado === 1 && response.Finalizado === 0 && response.Entregado === 0) {
                            
                            $('.cerrar').html('<i class="ri-lock-line"></i>&nbsp;Cerrado');
                            $('.FechaFinalizado').find('span').removeAttr('title').removeClass().addClass('input-group-text finalizar');
                            $('#cerrar').val(fechaNow(response.FechaCierre, '/', 0)).prop('readonly', true);
                            window.location.reload();
                        } else {
                            
                            if(response.Cerrado === 0 && response.Finalizado === 0 && response.Entregado === 0){
                                $('.cerrar').html('<i class="ri-lock-unlock-line"></i>&nbsp;Cerrar');
                                $('.FechaFinalizado').find('span').removeAttr('title').removeClass().addClass('input-group-text');
                                $('#cerrar').val('').prop('readonly', false);
                                //recargamos la tabla de examenes
                                window.location.reload();
                            }
                        }
                        break;

                    case 'finalizar':
                        if(response.Cerrado === 1 && response.Finalizado === 1 && response.Entregado === 0 ){
                            $('.finalizar').html('<i class="ri-lock-line"></i>&nbsp;Finalizado');
                            $('#finalizar').val(fechaNow(response.FechaFinalizado, '/', 0)).prop('readonly', true);
                            $('.FechaEntrega').find('span').removeAttr('title').removeClass().addClass('input-group-text entregar');
                            

                        }else{
                            if(response.Entregado !== 1){
                                $('.finalizar').html('<i class="ri-lock-unlock-line"></i>&nbsp;Finalizar');
                                $('#finalizar').val('').prop('readonly', false);
                                $('.FechaEntrega').find('span').removeAttr('title').removeClass().addClass('input-group-text');
                                
                            }
                        }
                        break;

                    case 'entregar':
                        if(response.Cerrado === 1 && response.Finalizado === 1 && response.Entregado === 1){
                            $('.entregar').html('<i class="ri-lock-line"></i>&nbsp;Entregado');
                            $('#entregar').val(fechaNow(response.FechaFinalizado, '/', 0)).prop('readonly', true);
                        
                        }else{
                            if(response.eEnviado !== 1){
                                $('.entregar').html('<i class="ri-lock-unlock-line"></i>&nbsp;Entregar');
                                $('#entregar').val('').prop('readonly', false);
                            }  
                        }
                        break;
                    
                    case 'eEnviar':
                        if(response.Cerrado === 1 && response.eEnviado === 1){
                            $('.eEnviar').html('<i class="ri-lock-line"></i>&nbsp;eEnviado');
                            $('#eEnviar').val(fechaNow(response.FechaEnviado, '/', 0)).prop('readonly', true);
                        }else{
                            if(response.Cerrado !== 0){
                                $('.eEnviar').html('<i class="ri-lock-unlock-line"></i>&nbsp;eEnviar');
                                $('#eEnviar').val('').prop('readonly', false);
                            }
                            
                        }    
                        break;
                }
              
            },
            error: function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;  
            }
        });

    });

    $('#IdEvaluador').select2({
        placeholder: 'Seleccionar evaluador',
        language: {
            noResults: function() {

            return "No hay evaluadores T3 con esos datos";        
            },
            searching: function() {

            return "Buscando..";
            },
            inputTooShort: function () {
                return "Por favor, ingrese 2 o más caracteres";
            }
        },
        language: 'es',
        allowClear: true,
        ajax: {
           url: getEvaluador,
           dataType: 'json',
           delay: 250,
           data: function(params) {
                return {
                    buscar: params.term,
                };
           },
           processResults: function(data) {
                return {
                    results: data.evaluadores
                };
           },
           cache: true,
        },
        minimumInputLength: 2
    });

    $('#paquetes').select2({
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

    $('#art').select2({
        placeholder: 'Seleccionar ART',
        language: {
            noResults: function() {

            return "No hay ART con esos datos";        
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

    $('#empresa').select2({
        placeholder: 'Seleccionar empresa',
        language: {
            noResults: function() {

            return "No hay Empresa con esos datos";        
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

    $(document).on("select2:open", () => {
        document.querySelector(".select2-container--open .select2-search__field").focus()
    });

    $(document).on('change', '#Pago', function(){
        let pago = $(this).val();
        return pago === 'C' ? $('.SPago').hide() : $('.SPago').show();
    });

    $(document).on('change', '#pago', function(){
        selectMedioPago($(this).val());
    });

    $('#TipoPrestacion').change(function(empresa, art){
        precargaMapa(empresa, art);
    });

    $(document).on("select2:open", () => {
        document.querySelector(".select2-container--open .select2-search__field").focus()
    });

     $(document).on('click', '.confirmarComentarioPriv', function(e){
        e.preventDefault();
        let comentario = $('#Comentario').val();

        if(comentario === ''){
            toastr.warning('La observación no puede estar vacía', 'Atención', {timeOut: 1000});
            return;
        }

        let profesional = PROFESIONAL ? PROFESIONAL[0].toUpperCase() + PROFESIONAL.slice(1).toLowerCase() : '-';

        preloader('on');
        $.post(savePrivComent, {_token: TOKEN, Comentario: comentario, IdEntidad: ID, obsfasesid: 2, Rol: profesional})
            .done(function(){
                preloader('off');
                toastr.success('Perfecto', 'Se ha generado la observación correctamente', {timeOut: 1000});

                setTimeout(() => {
                    $('#privadoPrestaciones').empty();
                    $('#addObs').modal('hide');
                    $("#Comentario").val("");
                    comentariosPrivados();
                }, 3000);
            })
    });

    $(document).on('click', '.multiVolver', function(e) {
        window.history.back();
    });
    
    $(document).on('click', '.imprimirReporte', function(e){
        e.preventDefault();

        let evaluacion = $('#evaluacion').prop('checked'),
            eEstudio = $('#eEstudio').prop('checked'),
            eEnvio = $('#eEnvio').prop('checked'),
            adjDigitales = $('#adjDigitales').prop('checked'),
            adjFisicos = $('#adjFisicos').prop('checked'),
            adjFisicosDigitales = $('#adjFisicosDigitales').prop('checked'),
            adjGenerales = $('#adjGenerales').prop('checked'),
            adjAnexos = $('#eAnexo').prop('checked'),
            infInternos = $('#infInternos').prop('checked'),
            pedProveedores = $('#pedProveedores').prop('checked'),
            conPaciente = $('#conPaciente').prop('checked'),
            resAdmin = $('#resAdmin').prop('checked'),
            caratula = $('#caratula').prop('checked'),
            consEstDetallado = $('#consEstDetallado').prop('checked'),
            consEstSimple = $('#consEstSimple').prop('checked'),
            audioCmit = $('#audioCmit').prop('checked'),
            estudios = [];

        let verificar = [
            evaluacion,
            eEstudio,
            eEnvio,
            adjDigitales,
            adjFisicosDigitales,
            adjGenerales,
            adjAnexos,
            infInternos,
            pedProveedores,
            conPaciente,
            resAdmin,
            caratula,
            consEstDetallado,
            consEstSimple,
            audioCmit,
            adjFisicos,
            estudios
        ];
            
        if (verificar.every(val => !val)) {
            toastr.warning('Debe seleccionar algun check para obtener el reporte', '', {timeOut: 1000});
            return;
        }

        $('input[data-nosend]:checked').each(function() {
            estudios.push($(this).attr('id'));
        });
        
        preloader('on');
        $.get(exportPdf, {Id: ID, evaluacion: evaluacion, eEstudio: eEstudio, eEnvio: eEnvio, adjDigitales: adjDigitales, adjFisicos: adjFisicos, adjFisicosDigitales: adjFisicosDigitales, adjGenerales: adjGenerales, adjAnexos: adjAnexos, infInternos: infInternos, pedProveedores: pedProveedores, conPaciente: conPaciente, resAdmin: resAdmin, caratula: caratula, consEstDetallado: consEstDetallado, consEstSimple: consEstSimple, audioCmit: audioCmit, estudios: estudios})
            .done(function(response){
                createFile("pdf", response.filePath, response.name);
                preloader('off')
                toastr.success(response.msg, '', {timeOut: 1000});
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            });    
    });

    $(document).on('click', '.resumenTotal', function(e){
        e.preventDefault();

        swal({
            title: "¿Desea generar el resumen?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar){
                preloader('on');
                $.get(exportXls, {Id: ID})
                    .done(function(response){
                        
                        createFile("excel", response.filePath, generarCodigoAleatorio() + '_reporte');
                        preloader('off');
                        toastr.success(response.msg, '', {timeOut: 1000});
                    })
                    .fail(function(jqXHR){
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return;
                    });
            }
        });
    });

    $(document).on('click', '.eAnexo', function(e){
        e.preventDefault();
        
        swal({
            title: "¿Desea generar el anexo?",
            icon: "warning",
            //text: `Destinatarios: ${emailsInforme}`,
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar){
                preloader('on');
                $.get(exportPdf, {Id: ID, adjAnexos: 'true', buttonEA: 'true'})
                    .done(function(response){
                        createFile("pdf", response.filePath, response.name);
                        preloader('off')
                        toastr.success(response.msg, '', {timeOut: 1000});
                    })
                    .fail(function(jqXHR){
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return;
                    }); 
            }
        });
    });

    $(document).on('click', '.EnviarAviso', function(e){
        e.preventDefault();

        swal({
            title: "¿Desea enviar el aviso de reporte?",
            icon: "warning",
            text: `Destinatarios: ${emailsInforme}`,
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar){

                preloader('on');
                $.get(eEnviarAviso, {Id: ID})
                    .done(function(response){
                        preloader('off');
                        toastr.success(response.msg, '', {timeOut: 1000});
                    })
                    .fail(function(jqXHR){
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);
                        checkError(jqXHR.status, errorData.msg);
                        return;
                    });
            }
        });
    });

    $(document).on('click', '.eEnviarReporte', function(e){
        e.preventDefault();

        swal({
            title: "¿Desea enviar el reporte eEstudio, eAnexos y Adjuntos Generales de la prestación?",
            icon: "warning",
            text: `Destinatarios: ${emailsInforme}`,
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar){

                preloader('on');
                $.get(eEnviarEspecial, {Id: ID})
                    .done(function(response){
                        preloader('off');
                        toastr.success(response.msg, '', {timeOut: 1000});
                    })
                    .fail(function(jqXHR){
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);
                        checkError(jqXHR.status, errorData.msg);
                        return;
                    });
            }
        });

        
    });

    $(document).on('click', '.eEstudio', function(e){
        e.preventDefault();

        swal({
            title: "¿Desea enviar el estudio?",
            icon: "warning",
            //text: `Destinatarios: ${emailsInforme}`,
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {

                preloader('on');
                $.get(exportPdf, {Id: ID, eEstudio: 'true', buttonEE: 'true'})
                    .done(function(response){
                        createFile("pdf", response.filePath, response.name);
                        preloader('off')
                        toastr.success(response.msg, '', {timeOut: 1000});
                    })
                    .fail(function(jqXHR){
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return;
                    });  
            }
        })   
    });

    $(document).on('click', '.enviarReporte', function(e){
        e.preventDefault();

        let eEstudio = $('#eEstudio').prop('checked'),
            eEnvio = $('#eEnvio').prop('checked'),
            adjFisicosDigitales = $('#adjFisicosDigitales').prop('checked'),
            infInternos = $('#infInternos').prop('checked'),
            pedProveedores = $('#pedProveedores').prop('checked'),
            conPaciente = $('#conPaciente').prop('checked'),
            caratula = $('#caratula').prop('checked');

        let evaluacion = $('#evaluacion').prop('checked'),
            eAnexo = $('#eAnexo').prop('checked'),
            adjDigitales = $('#adjDigitales').prop('checked'),
            adjFisicos = $('#adjFisicos').prop('checked'),
            adjPrestacion = $('#adjPrestacion').prop('checked'),
            resAdmin = $('#resAdmin').prop('checked'),
            consEstDetallado = $('#consEstDetallado').prop('checked'),
            consEstSimple = $('#consEstSimple').prop('checked'),
            EMailInformes = $('#EMailInformes').val();
        
        let estudios = $('input[data-nosend]:checked').map(function() {
            return $(this).data('examen');
        }).get();

        let verificar = [
            eEstudio,
            eEnvio,
            adjFisicosDigitales,
            infInternos,
            pedProveedores,
            conPaciente,
            caratula,
            ...estudios
        ];

       let conReportesInternos = verificar.some(val => val);

        let soloEnvios = {evaluacion: evaluacion, eAnexo: eAnexo, adjDigitales: adjDigitales, adjFisicos: adjFisicos, adjPrestacion: adjPrestacion, resAdmin: resAdmin, consEstDetallado: consEstDetallado, consEstSimple: consEstSimple, EMailInformes: EMailInformes, Id: ID };

        let conInternos = {evaluacion: evaluacion, eAnexo: eAnexo, adjDigitales: adjDigitales, adjFisicos: adjFisicos, adjPrestacion: adjPrestacion, resAdmin: resAdmin, consEstDetallado: consEstDetallado, consEstSimple: consEstSimple, EMailInformes: EMailInformes, Id: ID, eEstudio: eEstudio, eEnvio: eEnvio, adjFisicosDigitales: adjFisicosDigitales, infInternos: infInternos, pedProveedores: pedProveedores, conPaciente: conPaciente, caratula: caratula, estudios: estudios };

        if (!verificarCorreos(EMailInformes)) {
            toastr.warning('Alguno de los correos no es válido o el mismo se encuentra vacío', '', {timeOut: 1000});
            return;
        }

        swal({
            title: '¿Desea realizar en envio de los reportes?',
            icon: 'warning',
            text: `Destinatarios: ${emailsInforme}`,
            buttons: ['Cancelar', 'Aceptar']
        }).then((confirmar) => {
            if(confirmar) {
                if(conReportesInternos) {

                    swal({
                        'title': 'Esta por enviar reportes internos ¿Desea Continuar?',
                        'icon': 'warning',
                        'buttons':  ['Cancelar', 'Enviar']
                    }).then((sendEnvio) => {
                        if(sendEnvio) {
                            ajaxEnviarReporte(conInternos);
                        }
                    });
                }else{
                    ajaxEnviarReporte(soloEnvios);
                }
            }
        });
    });

    $(document).on('click', '.btnTodo', function(e){
        e.preventDefault();

        let tipoPrestacion = $('#TipoPrestacion').val(),
            pago = $('#pago').val(),
            fecha = $('#Fecha').val();
            empresa = $('#empresa').val(),
            art = $('#art').val(),
            IdPaciente = $('#IdPaciente').val();
            spago = $('#SPago').val(),
            observaciones = $('#Observaciones').val(),
            tipo = $('#Tipo').val(),
            sucursal = $('#Sucursal').val(),
            nroFactura = $('#NroFactura').val(),
            mapas = $('#mapas').val(),
            autorizado = $('#Autorizado').val();
            IdEvaluador = $('#IdEvaluador').val(),
            Evaluacion = $('#Evaluacion').val(),
            Calificacion = $('#Calificacion').val(),
            SinEval =$('#SinEval').prop('checked');
            ObsExamenes = $('#ObsExamenes').val(),
            FechaAnul = $('#FechaAnul').val(),
            Obs = $('#Obs').val();
            NroFactProv = $('#NroFactProv').val();

       swal({
        title: "¿Desea enviar el Informe, grabar, cerrar e imprimir?",
        icon: "warning",
        buttons: ["Cancelar", "Aceptar"]
       }).then((confirmar) => {
            if(confirmar) {

                let data = {Id: ID, TipoPrestacion: tipoPrestacion, Pago: pago, Fecha: fecha, SPago: spago, Mapas: mapas, Observaciones: observaciones, Empresa: empresa, IdPaciente: IdPaciente, Art: art, IdEvaluador: IdEvaluador,Evaluacion: Evaluacion, Calificacion: Calificacion, SinEval: SinEval, ObsExamenes: ObsExamenes, tipo: tipo, sucursal: sucursal, nroFactura: nroFactura, FechaAnul: FechaAnul, Obs: Obs, NroFactProv: NroFactProv, _token: TOKEN}

                preloader('on'); 
                $.get(CmdTodo, data)
                .done(function(response){
                    preloader('off');
                    
                    if(response.icon === 'success') {
                        createFile("pdf", response.filePath, response.name);
                        toastr.success(response.msg, '', {timeOut: 1000});
                    }else{
                        toastr.success(response.msg, '', {timeOut: 1000});
                    }
                })
                .fail(function(jqXHR){
                    preloader('off');
                    let errorData = JSON.parse(jqXHR.responseText);            
                    checkError(jqXHR.status, errorData.msg);
                    return;
                });  
            }
       });
    });

    $('#opciones').on('show.bs.modal', function () {
        cargarEstudiosImp();
    });

    $(document).on('click', '.btnAdjFilePres', function(e){
        e.preventDefault();

        let archivo = $('input[name="fileAdjPrestacion"]')[0].files[0],
            descripcion = $('#DescripcionAdjPrestacion').val();

        if(verificarArchivo(archivo)){
        
            preloader('on');
            let formData = new FormData();
            formData.append('archivo', archivo);
            formData.append('Descripcion', descripcion);
            formData.append('IdEntidad', ID);
            formData.append('_token', TOKEN);

            $.ajax({
                type: 'POST',
                url: fileUploadPres,
                data: formData,
                processData: false,
                contentType: false,
                success: function() {
                    loadListAdjPrestacion();
                    $('#addAdjPres').modal('hide');
                    $('#DescripcionAdjPrestacion, input[name="fileAdjPrestacion"]').val('');
                    preloader('off');
                    toastr.success("Se ha cargado el adjunto de manera correcta.", '', {timeOut: 1000});
                },
                error: function (jqXHR) {
                    preloader('off');
                    let errorData = JSON.parse(jqXHR.responseText);            
                    checkError(jqXHR.status, errorData.msg);
                    return;
                }
            });
        }

    });

    $(document).on('click', '.deleteAdjuntoPres', function(e){
        e.preventDefault();

        let id = $(this).data('id');
        
        if(!id) return;

        swal({
            title: "¿Estás seguro que deseas eliminar?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                preloader('on');
                $.get(deleteAdjPrest, {Id: id})
                    .done(function(response){
                        loadListAdjPrestacion();
                        preloader('off');
                        toastr.success(response.msg,'', {timeOut: 1000});
                    })
                    .fail(function(jqXHR){
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return;
                    });
            }
        });
    });

    $(document).on('click', '.verPrestacion', function(e){
        e.preventDefault();

        let link = urlPrestacion.replace('__prestacion__', $(this).data('id'));
        window.open(link, '_blank');

    });

    $(document).on('click', '.exportSimple, .exportDetallado', function(e){
        e.preventDefault();

        let id = $(this).data('id'), tipo = $(this).hasClass('exportSimple') ? 'exportSimple' : 'exportDetallado';

        if(!id) return;

        preloader('on');
        $.get(exResultado, {IdPaciente: id, Tipo: tipo})
            .done(function(response){
                createFile("excel", response.filePath, generarCodigoAleatorio() + '_reporte');
                preloader('off');
                toastr.success('Se ha generado el archivo correctamente','', {timeOut: 1000});
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            });
    });

    $(document).on('click', '.deleteComentario', function(e){
        e.preventDefault;

        let id = $(this).data('id');

        if(!id) return;

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

    $(document).off('click', '.editarComentario').on('click', '.editarComentario', async function(){
        borrarCache();

        let id = $(this).data('id'),
            data  = await $.get(getComentario,{Id: id});

        $('#addObs').modal('show');
        $('#addObs').attr('aria-hidden', 'false');
        $('#confirmar').hide();
        $('#modificar').show();
        $('#IdComentarioFase').empty().val(data[0].Id)
        $('#Comentario').val(data[0].Comentario);
    });

    $(document).off('click', '.editarComentarioPriv').on('click', '.editarComentarioPriv', function(e){
        e.preventDefault();
        let comentario = $('#Comentario').val(), id = $('#IdComentarioFase').val();

        preloader('on')
        $.get(editarComentario, {Id: id, Comentario: comentario})
            .done(function(response){
                $('#confirmar').show();
                $('#modificar').hide();
                $('#addObs').modal('hide');
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

    $(document).on('click', '.agregarComentario', function(e){
        $('#addObs').modal('show');
        $('#confirmar').show();
        $('#modificar').hide();
        $('#Comentario').val('');
    });

    function loadListAdjPrestacion() {

        $('#adjPrestacion').empty();

        $.get(loadlistadoAdjPres, {Id: ID})
            .done(function(response){

                for (let index = 0; index < response.length; index++) {
                    let r = response[index];
                    
                    let contenido = `
                        <tr>
                            <td>${r.Descripcion}</td>
                            <td>
                                 <div class="d-flex justify-content-center align-items-center gap-2">
                                     <div class="edit">
                                        <a href="${descarga}/${r.Ruta}" target="_blank">
                                            <button type="button" class="btn btn-sm iconGeneral" title="Ver"><i class="ri-search-eye-line"></i></button>
                                        </a>
                                    </div>
                                    <div class="remove">
                                        <button data-id="${r.Id}" class="btn btn-sm iconGeneral deleteAdjuntoPres" title="Eliminar">
                                            <i class="ri-delete-bin-2-line"></i>
                                        </button>
                                    </div>
                                 </div>
                            </td>
                        </tr>
                    `;
                
                    $('#adjPrestacion').append(contenido);
                }
                
                
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            });
    }

    async function cargarEstudiosImp()
    {
        $('#estudios').empty();

        if(!ID) return;
        
        preloader('on');
        $.get(await listadoEstudiosImp, {Id: ID})

          .done(function(response){
                preloader('off');
                for(let index = 0; index < response.length; index++) {
                    let data = response[index],
                        forNombre = (data.NombreExamen).replace(" ", "-"),
                        contenido = `
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="${data.IdReporte}" data-examen="${data.IdExamen}" data-nosend>
                                <label class="form-check-label" for="${forNombre}">
                                    ${data.NombreExamen}
                                </label>
                            </div>
                        `;

                    $('#estudios').append(contenido);
                }
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            });

    }

    function precargaMapa(empresa, art){
        
        let val = $('#TipoPrestacion').val(), val2 = $('#art').val();

        if (val === 'ART' && (val2)) {
            $('.mapas').show();
            getMap(empresa, art)
        } else {
            $('#mapas').empty();
            $('.mapas').hide();
        }    
    }

    function cambiosVencimiento(actual){

        if(!actual) return;

        let hoy = new Date().toLocaleDateString('en-CA');
        if(hoy > actual){
            
            $.ajax({
                url: actualizarVto,
                type: 'POST',
                data: {
                    _token: TOKEN,
                    Id: ID,
                },
                success: function(){
                    $('.alert').show().html('Se ha actualizado el Vto en la base de datos por ser la fecha actual posterior a la fecha de vigente <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>');
                }
            });
        }
    }

    function selectMedioPago(opcion, id = null)
    {
         $('.SPago, .ObsPres, .Factura, .NroFactProv, .Autoriza, .NroFactExCta').hide();
        
         switch (opcion) {
            case 'B':
                $('.SPago, .Factura, .NroFactProv').show();
                $('.Autoriza').hide();
                break;
            case 'A':
                $('.Factura').show();
                break;
            case 'P':
                
                $.get(checkTipoFactExCta, {Id: id}, function(response) {
                     $('.NroFactExCta').show();

                    if(response.length > 1) {
                        $('#NroFactExCta').val("Multi Examen");

                    }else if(response.length === 1){
                        
                        let factura = `${response[0].Tipo}` +
                            `${response[0].Sucursal}`.padStart(4, '0') +
                            `${response[0].NroFactura}`.padStart(8, '0');

                            $('#NroFactExCta').val(factura);
                    }
                })
                break;
        }
    }

    async function getMap(empresaIn, artIn){

        $('#mapas').empty();

        $.get(await mapaPrestacion, {Id: ID})
            .done(function(response){
                let item = '';

                if(![undefined, null].includes(response)) {
                    item = `<option selected value="${response.IdMapa}">${response.mapa.Nro} | Empresa: ${response.mapa.empresa_mapa.RazonSocial} - ART: ${response.mapa.art_mapa.RazonSocial}</option>`;
                    
                }else{
                    item = `<option selected value="">Elija una opción disponible...</option>`;
                }

                $('#mapas').append(item);
            });

        $.get(await getMapas, {empresa: empresaIn, art: artIn})
            .done(function(response){
                let mapas = response.mapas;
                
                if(![undefined, null].includes(mapas))
                {
                    for(let index = 0; index < mapas.length; index++) {
                        let d = mapas[index],
                        contenido = `<option value="${d.Id}">${d.Nro} | Empresa: ${d.RSE} - ART: ${d.RSArt}</option>`;
                        
                        $('#mapas').append(contenido);
                    }
                } 
            });
    }

    function getFact(){
        $.get(getFactura, {Id: ID})
            .done(function(response){
                let data = response.factura;

                if(data){
                    $('#Tipo').val(data.Tipo);
                    $('#Sucursal').val(data.Sucursal);
                    $('#NroFactura').val(data.NroFactura);
                }
            })
    }

    function getAutoriza(pago){
        return pago === 'G' ? $('.Autoriza').show() : $('.Autoriza').hide();
    }

    function checkBloq(){
        preloader('on');
        $.get(getBloqueoPrestacion, {Id: ID})
            .done(async function(response){
                preloader('off');
                if(await response.prestacion === true){

                    $('#art, #empresa, #paraEmpresa, #Fecha, #TipoPrestacion, #mapas, #cerrar, #finalizar, #entregar, #eEnviar, #pago, #SPago, #Tipo, #Autorizado, #IdEvaluador, #Evaluacion, #Calificacion, #Observaciones, #SinEval, #ObsExamenes, #Obs, #actualizarPrestacion, #paquetes, #exam, #Sucursal, #NroFactura').prop('disabled', true);
                    $('span.input-group-text').removeClass('cerrar finalizar entregar eEnviar');
                    $('i.ri-add-circle-line').removeClass('addExamen');
                    $('i.ri-play-list-add-line').removeClass('addPaquete');
                    }
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;  
            });
    }

    function comentariosPrivados(){

        $('#privadoPrestaciones').empty();
        preloader('on');
        $.get(privateComment, {Id: ID,  tipo: 'prestacion'})
            .done(async function(response){
                preloader('off');
                let data = await response.result;

                for(let index = 0; index < data.length; index++){
                    let d = data[index];

                    let contenido =  `
                        <tr>
                            <td style="width: 120px">${fechaCompleta(d.Fecha)}</td>
                            <td style="width: 120px" class="text-capitalize">${d.IdUsuario}</td>
                            <td style="width: 120px" class="text-uppercase">${d.nombre_perfil}</td>
                            <td class="text-start">${d.Comentario}</td>
                            <td style="width: 60px">${USER === d.IdUsuario ? `
                                <button type="button" data-id="${d.Id}" class="btn btn-sm iconGeneralNegro editarComentario"><i class="ri-edit-line"></i></button>
                                <button title="Eliminar" data-id="${d.Id}" type="button" class="btn btn-sm iconGeneralNegro deleteComentario"><i class="ri-delete-bin-2-line"></i></button>` : ''}</td>
                        </tr>
                    `;
                    $('#privadoPrestaciones').append(contenido);
                }

                $('#lstPrivPrestaciones').fancyTable({
                    pagination: true,
                    perPage: 10,
                    searchable: false,
                    globalSearch: false,
                    sortable: false, 
                });
            })   
    }

    function cargarAutorizados() {

        $.ajax({
            url: getAutorizados,
            type: 'GET',
            data: {
                Id: IDEMPRESA,
            },
            success: function(response) {
                let autorizados = response;

                $('#autorizadosPres').empty();

                if (autorizados.length == 0) {
                    let contenido = '<p> Sin datos de Autorizados </p>';
                    $('.body-autorizado').append(contenido);

                }else{
                    for(let index = 0; index < autorizados.length; index++){

                        let autorizado = autorizados[index];

                        let contenido = `
                        <tr>
                            <td>${autorizado.Nombre} ${autorizado.Apellido}</td>
                            <td>${autorizado.DNI}</td>
                            <td>${autorizado.Derecho}</td>
                        </tr>
                        `;
                        $('#autorizadosPres').append(contenido);
                    }

                    $("#lstAutorizados").fancyTable({
                        pagination: true,
                        perPage: 10,
                        searchable: false,
                        globalSearch: false,
                        sortable: false, 
                    });
                }
            },
            error: function(jqXHR){
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return; 
            }
        });
    }

    function examenesCta(id) {

        $('#lstSaldos').empty();
        preloader('on');

        $.get(lstExDisponibles, {Id: id})
            .done(function(response) {
                let contenido = '';
                preloader('off');

                if(response && response.length > 0){

                    for(let index = 0; index < response.length; index++){
                        let r = response[index],
                            contenido = `
                            <tr>
                                <td>${r.Precarga === '' ? '-' : r.Precarga}</td>
                                <td>${r.NombreExamen}</td>
                            </tr>
                        `;
                        $('#lstSaldos').append(contenido);
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
                $('#lstSaldos').append(contenido); 
            });    
    }

    async function checkExamenes(id) {
        $.get(await buscarEx, {Id: id}, function(response){
            !response
                ? $('.auditoria, .autorizados, .evaluacion, .banderas').hide()
                : $('.auditoria, .autorizados, .evaluacion, .banderas').show()  
        })
    }
    
    async function checkerIncompletos(idPrestacion)
    {
        if([null,'',0].includes(idPrestacion)) return;
        $.get(await checkInc, {Id: idPrestacion});
    }

    function checkEstadoEnviar(id) {
        
        if(!id) return;

        $.get(btnVisibleEnviar, {Id: id})
            .done(function(response){
                let arr = ['pagado', 'completos', 'evaluado', 'pagado'], allTrue = arr.every(key => response[key] === true);
                return allTrue === true ? $('#eEnviarReporte').show() : $('#eEnviarReporte').hide();
            });
    }

    async function lstResultadosPrest(idPaciente){

        if(!idPaciente) return;

        $('#lstResultadosPrestacion').empty();
        preloader('on');
        $.get(await loadResultadosPres, {IdPaciente: idPaciente})
            .done(function(response){
                
                preloader('off');
                for(let index = 0; index < response.length; index++){
                    let r = response[index],
                        icon = r.Evaluacion === 0 ? `<span class="custom-badge generalNegro">Antiguo</span>` : '',
                        evaluacion = r.Evaluacion ? r.Evaluacion.slice(2) : '',
                        calificacion = r.Calificacion ? r.Calificacion.slice(2) : '',
                        boton = r.Evaluacion !== 0 ? `<button data-id="${r.Id}" class="btn btn-sm iconGeneral verPrestacion" title="Ver">
                                    <i class="ri-search-eye-line"></i>
                                </button>` : '';

                    let contenido = `
                        <tr>
                            <td style="width: 50px">${fechaNow(r.Fecha, "/", 0)}</td>
                            <td style="width: 50px">${r.Id} ${icon}</td>
                            <td style="width: 160px">${r.Empresa}</td>
                            <td style="width: 100px">${r.Tipo}</td>
                            <td style="width: 120px">${evaluacion}</td>
                            <td style="width: 120px">${calificacion}</td>
                            <td>${r.Obs}</td>
                            <td style="width: 30px">
                                ${boton}
                            </td>
                        </tr>
                    `;

                    $('#lstResultadosPrestacion').append(contenido);
                }

                $("#listadoResultadosPrestacion").fancyTable({
                    pagination: true,
                    perPage: 15,
                    searchable: false,
                    globalSearch: false,
                    sortable: false, 
                });
            });
    }

    
    function borrarCache() {
        $.post(cacheDelete, {_token: TOKEN}, function(){});
    }

    function ajaxEnviarReporte(paquete) {
        preloader('on');
        $.get(enviarReporte, paquete)
            .done(function(response){
                preloader('off');
                toastr.success(response.msg, '', {timeOut: 1000});
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return; 
            });
    }

  
});