$(document).ready(()=> {

    let fecha = $('#FechaVto').val(), opcion = $('#pago').val(), opcionPago = $('#SPago').val();
    var empresa = $('#empresa').val(), art = $('#art').val();

    precargaMapa();
    examenesCta(empresa);
    checkExamenes(ID);
    checkerIncompletos(ID);
    
    $(document).on('change', '#empresa, #art, #TipoPrestacion', function(){
        let emp = $('#empresa').val(), art = $('#art').val();
        getMap(emp, art);
        examenesCta(emp);
    });
    
    quitarDuplicados("#tipoPrestacion");
    quitarDuplicados("#pago");
    quitarDuplicados("#SPago");
    quitarDuplicados("#Evaluacion");
    quitarDuplicados("#Calificacion");
    quitarDuplicados("#RxPreliminar");
    quitarDuplicados("#TipoPrestacion");
    quitarDuplicados("#Financiador");
    quitarDuplicados("#mapas");

    cargarFinanciador($("#tipoPrestacion").val());
    cambiosVencimiento(fecha);
    selectMedioPago(opcion);
    getFact();
    checkBloq();
    comentariosPrivados();
    cargarAutorizados();

    $(document).on('change', '#empresa, #art', function() {
        precargaMapa();
    });

    //Hack de carga
    $(document).ready(function(){
        getAutoriza(opcionPago);
    });
    
    $('.alert').hide();

    $(document).on('change', '#pago', function(){
        selectMedioPago();
    });

    $(document).on('change', '#SPago', function(){
        let data = $(this).val();
        getAutoriza(data);
    });

    $('#actualizarPrestacion').on('click', function(e) {
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
            RxPreliminar = $('#RxPreliminar').prop('checked'),
            ObsExamenes = $('#ObsExamenes').val(),
            FechaAnul = $('#FechaAnul').val(),
            Obs = $('#Obs').val();
            NroFactProv = $('#NroFactProv').val();
 
         //Validamos la factura
        if (spago === 'G' && autorizado === ''){ 
            toastr.warning('Si el medio de pago es gratuito, debe seleccionar quien autoriza.');
            return;
        }

        if (pago === 'B' && spago === '') {
            toastr.warning('Debe seleccionar un "medio de pago" cuando la "forma de pago" es "contado"');
            return;
        }
       
        if (['', null, undefined].includes(pago)) {
            toastr.warning('Debe seleccionar una "forma de pago"');
            return;
        }

        if (pago === 'B' && (tipo == '' || sucursal === '' || nroFactura === '')){
            toastr.warning('El pago es contado, asi que debe agregar el número de factura para continuar.');
            return;
        }

        if (tipoPrestacion === ''){
            toa("Atención", "El tipo de prestación no puede ser un campo vacío", "warning");
            return;
        }
        
        if (tipoPrestacion === 'ART' && ['', 0, null].includes(mapas)) {
            toastr.warning("Debe seleccionar un mapa si la prestación es ART");
            return;
        }
        
        if ([0, null, '', '0'].includes(art) && tipoPrestacion === 'ART') {
            toastr.warning("Debe seleccionar un cliente ART si el tipo de prestación es ART");
            return;
        }
        
        if (![0, null, undefined, '0'].includes(art) && tipoPrestacion === 'ART' && (['', null, 0].includes(mapas))) {
            toastr.warning("Debe seleccionar un mapa vigente si la prestación es ART y tiene un cliente ART cargado");
            return;
        }

        if(![null, undefined, '0', 0].includes(art) && tipoPrestacion !== 'ART') {
            toastr.warning("Si hay un cliente ART la prestación debe ser de tipo ART");
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
                RxPreliminar: RxPreliminar,
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
                toastr.success(response.msg);
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

    $("#btnVolver").on("click", function() {
        
        let location = UBICACION;
        return location === 'prestaciones' ? window.location.replace(GOPRESTACIONES) : window.location.replace(GOPACIENTES);
    });

    $(document).on('change', '#empresa', function(){

        let empresa = $(this).val();
        
        if(empresa === null) return;

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

    $(document).on('change', '#tipoPrestacion', function() {
        cargarFinanciador($(this).val());
    });


    $("#TipoPrestacion").val(selectTipoPrestacion);
 
    $(document).on('click','.cerrar, .finalizar, .entregar, .eEnviar', function() {

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
                            
                        } else {
                            
                            if(response.Cerrado === 0 && response.Finalizado === 0 && response.Entregado === 0){
                                $('.cerrar').html('<i class="ri-lock-unlock-line"></i>&nbsp;Cerrar');
                                $('.FechaFinalizado').find('span').removeAttr('title').removeClass().addClass('input-group-text');
                                $('#cerrar').val('').prop('readonly', false);
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
        
        let option = $(this).val();
        selectMedioPago(option);
    });

    $('#TipoPrestacion').change(function(){
        precargaMapa();
    });

    $(document).on("select2:open", () => {
        document.querySelector(".select2-container--open .select2-search__field").focus()
    });

     $(document).on('click', '.confirmarComentarioPriv', function(){

        let comentario = $('#Comentario').val();

        if(comentario === ''){
            toastr.warning('La observación no puede estar vacía', 'Atención');
            return;
        }

        $.post(savePrivComent, {_token: TOKEN, Comentario: comentario, IdEntidad: ID, obsfasesid: 2})
            .done(function(){

                toastr.success('Perfecto', 'Se ha generado la observación correctamente');

                setTimeout(() => {
                    $('#privadoPrestaciones').empty();
                    $('#addObs').modal('hide');
                    $("#Comentario").val("");
                    comentariosPrivados();
                }, 3000);
            })
    });

    function precargaMapa(){
        
        let val = $('#TipoPrestacion').val(), val2 = $('#art').val();
        return (val === 'ART' && (!['','0', null].includes(val2)))
            ? $('.mapas').show()
            : $('.mapas').hide();
    }
    
    function cargarFinanciador(estado) {
        if (estado !== 'ART') {
            $("#empresaFinanciador").prop("selected", true);
        } else if (estado === '') {
            $("#emptyFinanciador").prop("selected", true);
        } else {
            $("#artFinanciador").prop("selected", true);
        }
    }

    function cambiosVencimiento(actual){

        if(actual == '') return;

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

    function selectMedioPago(opcion)
    {
        if(opcion === 'B'){
            $('.SPago').show();
            $('.Factura').show();
            $('.NroFactProv').show();
            $('.Autoriza').hide();
        }else {
            $('.SPago').hide();
            $('.ObsPres').hide();
            $('.Factura').hide();
            $('.NroFactProv').hide();
            $('.Autoriza').hide();
        }
    }

    function getMap(empresaIn, artIn){

        $('#mapas').empty();

        $.get(getMapas, {empresa: empresaIn, art: artIn})
            .done(function(response){

                let mapas = response.mapas;
                
                if(mapas.length !== 0)
                {
                    $.each(mapas, function(index, d){

                        let contenido = `<option value="${d.Id}">${d.Nro} | Empresa: ${d.RSE} - ART: ${d.RSArt}</option>`;
    
                        $('#mapas').append(contenido);
                    });
                }
                
            })
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

                    $('#art, #empresa, #paraEmpresa, #Fecha, #TipoPrestacion, #mapas, #cerrar, #finalizar, #entregar, #eEnviar, #pago, #SPago, #Tipo, #Autorizado, #IdEvaluador, #Evaluacion, #Calificacion, #Observaciones, #RxPreliminar, #SinEval, #ObsExamenes, #Obs, #actualizarPrestacion, #paquetes, #exam, #Sucursal, #NroFactura').prop('disabled', true);
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

                $.each(data, function(index, d){
 
                    let contenido =  `
                        <tr>
                            <td>${fechaNow(d.Fecha, '/', 1)}</td>
                            <td>${d.IdUsuario}</td>
                            <td>${d.nombre_perfil}</td>
                            <td>${d.Comentario}</td>
                        </tr>
                    `;
                    $('#privadoPrestaciones').append(contenido);
                });

                $('#lstPrivPrestaciones').fancyTable({
                    pagination: true,
                    perPage: 15,
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

                    $.each(autorizados, function(index, autorizado) {
                        let contenido = `
                        <tr>
                            <td>${autorizado.Nombre} ${autorizado.Apellido}</td>
                            <td>${autorizado.DNI}</td>
                            <td>${autorizado.Derecho}</td>
                        </tr>
                        `;
                        $('#autorizadosPres').append(contenido);
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

    async function checkExamenes(id) {

        $.get(await buscarEx, {Id: id}, function(response){

            response === 0 
                ? $('.auditoria, .autorizados, .evaluacion, .banderas').hide()
                : $('.auditoria, .autorizados, .evaluacion, .banderas').show()  
        })
    }
    
    async function checkerIncompletos(idPrestacion)
    {
        if([null,'',0].includes(idPrestacion)) return;

        $.get(await checkInc, {Id: idPrestacion}, function(){
            console.log("Actualizados los estados");
        });
    }

});