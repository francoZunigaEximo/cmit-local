$(document).ready(()=> {

    let fecha = $('#FechaVto').val(), opcion = $('#pago').val(), opcionPago = $('#SPago').val(), empresa = $('#empresa').val(),art = $('#art').val();

    toastr.options = {
        closeButton: true,   
        progressBar: true,     
        timeOut: 3000,        
    };
    
    quitarDuplicados("#tipoPrestacion");
    quitarDuplicados("#pago");
    quitarDuplicados("#SPago");
    quitarDuplicados("#Evaluacion");
    quitarDuplicados("#Calificacion");
    quitarDuplicados("#RxPreliminar");
    quitarDuplicados("#TipoPrestacion");
    quitarDuplicados("#Financiador");

    precargaMapa();
    cargarFinanciador($("#tipoPrestacion").val());
    cambiosVencimiento(fecha);
    selectMedioPago(opcion);
    getMap(empresa, art);
    getFact();

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

    $('#actualizarPrestacion').on('click', function(event) {
        event.preventDefault();

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
            ObsExamenes = $('#ObsExamenes').val();

            
         //Validamos la factura
        if(spago === 'G' && autorizado === ''){
            toastr.warning('Si el medio de pago es gratuito, debe seleccionar quien autoriza.', 'Alerta');
            return;
        }

        if(pago === 'B' && spago === '') {
            toastr.warning('Debe seleccionar un "medio de pago" cuando la "forma de pago" es "contado"', 'Alerta');
            return;
        }

        if(pago === '' || spago === null || pago === undefined) {
            toastr.warning('Debe seleccionar una "forma de pago"', 'Alerta');
            return;
        }

        if(pago === 'B' && (tipo == '' || sucursal === '' || nroFactura === '')){
            toastr.warning('El pago es contado, asi que debe agregar el número de factura para continuar.', 'Alerta');
            return;
        }

        if(tipoPrestacion === ''){
            swal("Atención", "El tipo de prestación no puede ser un campo vacío", "warning");
            return;
        }

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
                _token: TOKEN
            },
            success: function(){
                toastr.success("La prestación se ha actualizado correctamente. Se recargaran los datos.", "Actualización realizada");
                setTimeout(function(){
                    location.reload();
                }, 3000);  
            },
            error: function(xhr){
                console.error(xhr);
            }
        });

    });

    $("#btnVolver").on("click", function() {
        let location = UBICACION;
        
        if(location === 'prestaciones'){
            window.location.replace(GOPRESTACIONES);
        }else{
            window.location.replace(GOPACIENTES);
        }
    });

    $(document).on('change', '#empresa', function(){

        let empresa = $(this).val();
        
        if(empresa === null) return;

        $.get(checkParaEmpresa, {empresa: empresa})
            .done(function(response){

                let data = response.cliente;

                $('#paraEmpresa').val(data.ParaEmpresa);
            })
            .fail(function(xhr){

                console.log(xhr);
                toastr.error('Ha ocurrido un error. Consulte con el administrador', 'Error');
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

        $.ajax({    
            url: actualizarEstados, 
            type: 'POST',
            data: {
                _token: TOKEN,
                Id: ID,
                Tipo: tipo
            },
            success: function(response){

                let t = response.tipo,
                    e = response.estado;
    
                switch (tipo) {
                    
                    case 'cerrar':
                        if(e.Cerrado === 1 && e.Finalizado === 0 && e.Entregado === 0) {
                            
                            $('.cerrar').html('<i class="ri-lock-line"></i>&nbsp;Cerrado');
                            $('.FechaFinalizado').find('span').removeAttr('title').removeClass().addClass('input-group-text finalizar');
                            $('#cerrar').val(fechaNow(new Date, '/', 1)).prop('readonly', true);
                            
                        } else {
                            
                            if(e.Cerrado === 0 && e.Finalizado === 0 && e.Entregado === 0){
                                $('.cerrar').html('<i class="ri-lock-unlock-line"></i>&nbsp;Cerrar');
                                $('.FechaFinalizado').find('span').removeAttr('title').removeClass().addClass('input-group-text');
                                $('#cerrar').val('').prop('readonly', false);
                            }
                        }
                        
                        break;

                    case 'finalizar':
                        if(e.Cerrado === 1 && e.Finalizado === 1 && e.Entregado === 0 ){
                            $('.finalizar').html('<i class="ri-lock-line"></i>&nbsp;Finalizado');
                            $('#finalizar').val(fechaNow(new Date, '/', 1)).prop('readonly', true);
                            $('.FechaEntrega').find('span').removeAttr('title').removeClass().addClass('input-group-text entregar');
                            

                        }else{
                            if(e.Entregado !== 1){
                                $('.finalizar').html('<i class="ri-lock-unlock-line"></i>&nbsp;Finalizar');
                                $('#finalizar').val('').prop('readonly', false);
                                $('.FechaEntrega').find('span').removeAttr('title').removeClass().addClass('input-group-text');
                                
                            }
                        }
                        break;

                    case 'entregar':
                        if(e.Cerrado === 1 && e.Finalizado === 1 && e.Entregado === 1){
                            $('.entregar').html('<i class="ri-lock-line"></i>&nbsp;Entregado');
                            $('#entregar').val(fechaNow(new Date, '/', 1)).prop('readonly', true);
                        
                        }else{
                            if(e.eEnviado !== 1){
                                $('.entregar').html('<i class="ri-lock-unlock-line"></i>&nbsp;Entregar');
                                $('#entregar').val('').prop('readonly', false);
                            }  
                        }
                        break;
                    
                    case 'eEnviar':
                        if(e.Cerrado === 1 && e.eEnviado === 1){
                            $('.eEnviar').html('<i class="ri-lock-line"></i>&nbsp;eEnviado');
                            $('#eEnviar').val(fechaNow(new Date, '/', 1)).prop('readonly', true);
                        }else{
                            if(e.Cerrado !== 0){
                                $('.eEnviar').html('<i class="ri-lock-unlock-line"></i>&nbsp;eEnviar');
                                $('#eEnviar').val('').prop('readonly', false);
                            }
                            
                        }
                            
                        break;
                }
              
            },
            error: function(xhr){
                console.error(xhr);
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
        
        if (pago === 'C'){
            $('.SPago').hide();
            }else{
            $('.SPago').show();
            } 
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

 
    function precargaMapa(){
        let val = $('#TipoPrestacion').val();
        if(val === 'ART'){

            $('.mapas').show();
        }else{
            $('.mapas').hide();
        }
    }

    function fechaNow(fechaAformatear, divider, format) {
        let dia, mes, anio;
    
        if (fechaAformatear === null || !(fechaAformatear instanceof Date)) {
            let fechaHoy = new Date();
    
            dia = fechaHoy.getDate().toString().padStart(2, '0');
            mes = (fechaHoy.getMonth() + 1).toString().padStart(2, '0');
            anio = fechaHoy.getFullYear();
        } else {
            dia = fechaAformatear.getDate().toString().padStart(2, '0');
            mes = (fechaAformatear.getMonth() + 1).toString().padStart(2, '0');
            anio = fechaAformatear.getFullYear();
        }
    
        let fechaCadena = `${anio}${divider}${mes}${divider}${dia}`;
    
        return (format === 1) ? `${dia}${divider}${mes}${divider}${anio}` : fechaCadena;
    }
    
    function quitarDuplicados(selector) {
        let seleccion = $(selector).val();
        let countSeleccion = $(selector + " option[value='" + seleccion + "']").length;
    
        if (countSeleccion > 1) {
            $(selector + " option[value='" + seleccion + "']:gt(0)").hide();
        }
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

        let hoy = new Date().toISOString().slice(0, 10);
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
            $('.Autoriza').hide();
       
        }else {

            $('.SPago').hide();
            $('.ObsPres').hide();
            $('.Factura').hide();
            $('.Autoriza').hide();
        }
    }

    function getMap(empresaIn, artIn){

        $.get(getMapas, {empresa: empresaIn, art: artIn})
            .done(function(response){

                let mapas = response.mapas;
                $('#mapas').empty().append('<option value="" selected>Elija un mapa...</option>');
                
                if(mapas.length === 0)
                {
                    $('#mapas').empty().append('<option title="" value="" selected>Sin mapas disponibles.</option>');
                }else{

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

        if(pago === 'G'){
            $('.Autoriza').show();
        }else{
            $('.Autoriza').hide();
        }
    }
    
});