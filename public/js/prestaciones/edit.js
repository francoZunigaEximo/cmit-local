$(document).ready(()=> {

    let fecha = $('#FechaVto').val();
    let opcion = $('#pago').val();
    
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

    $('.alert').hide();

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
            nroFactura = $('#NumeroFacturaVta').val(),
            mapas = $('#mapas').val(),
            IdEvaluador = $('#IdEvaluador').val(),
            Evaluacion = $('#Evaluacion').val(),
            Calificacion = $('#Calificacion').val(),
            SinEval =$('#SinEval').prop('checked');
            RxPreliminar = $('#RxPreliminar').prop('checked'),
            ObsExamenes = $('#ObsExamenes').val();

            
         //Validamos la factura
         if(spago === 'E' && nroFactura.length === 0 || nroFactura == null){
            swal('Atención', 'El número de la factura es obligatoria', 'warning');
            return;
        }

        if(tipoPrestacion === ''){
            swal("Atención", "El tipo de prestación no puede ser un campo vacío", "warning");
            return;
        }

        if(pago === 'B' && nroFactura == ''){
            swal("Atención", "El pago es contado, asi que debe agregar el número de factura para continuar.", "warning");
            return;
        }

        if(nroFactura.length > 11){
            swal('Atención', 'EL número de factura no puede tener mas de 11 dígitos y debe ser numerico', 'warning');
            return;
        }

        $.ajax({
            url: updatePrestacion,
            type: 'Post',
            data: {
                Id: Id,
                TipoPrestacion: tipoPrestacion,
                Pago: pago,
                Fecha: fecha,
                SPago: spago,
                Mapas: mapas,
                Observaciones: observaciones,
                Empresa: empresa,
                IdPaciente: IdPaciente,
                Art: art,
                NumeroFacturaVta: nroFactura,
                IdEvaluador: IdEvaluador,
                Evaluacion: Evaluacion,
                Calificacion: Calificacion,
                RxPreliminar: RxPreliminar,
                SinEval: SinEval,
                ObsExamenes: ObsExamenes,
                _token: TOKEN
            },
            success: function(){
                swal("Actualización realizada","La prestación se ha actualizado correctamente. Se recargaran los datos.", "success");
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

    $(document).on('change', '#tipoPrestacion', function() {
        cargarFinanciador($(this).val());
    });


    $("#TipoPrestacion").val(selectTipoPrestacion);
 
    $('#mapas').select2({
        placeholder: 'Seleccionar mapa',
        language: 'es',
        allowClear: true,
        ajax: {
           url: getMapas,
           dataType: 'json',
           delay: 250,
           data: function(params) {
                return {
                    buscar: params.term,
                    empresa: $('#empresa').val(),
                    art: $('#art').val()
                };
           },
           processResults: function(data) {
                return {
                    results: data.mapas
                };
           },
           cache: true,
        },
        minimumInputLength: 2
    });


    $(document).on('click','.cerrar, .finalizar, .entregar, .eEnviar', function() {

        $(this).prop('readonly', false);

        let tipo = 
            $(this).hasClass('cerrar') ? 'cerrar' : ($(this).hasClass('finalizar') ? 'finalizar' : ($(this).hasClass('entregar') ? 'entregar' : ($(this).hasClass('eEnviar') ? 'eEnviar' : '')));

        $.ajax({    
            url: actualizarEstados, 
            type: 'POST',
            data: {
                _token: TOKEN,
                Id: Id,
                Tipo: tipo
            },
            success: function(response){

                let t = response.tipo,
                    e = response.estado;
                console.log(t);
                console.log(e.Cerrado);

                switch (tipo) {

                    case 'cerrar':
                        if(e.Cerrado === 1 && e.Entregado === 0 && e.eEnviado === 0){
                            $('.cerrar').html('<i class="ri-lock-line"></i> Cerrado');
                            $('.FechaFinalizado').find('span').removeAttr('title').removeClass().addClass('badge text-bg-warning finalizar');
                            $('#cerrar').val(fechaNow(new Date, '/', 1)).prop('readonly', true);
                            
                        } else {
                            if(e.Finalizado !== 1 ){
                                $('.cerrar').html('Cerrar');
                                $('.FechaFinalizado').find('span').removeAttr('title').removeClass().addClass('badge text-bg-dark');
                                $('#cerrar').val('').prop('readonly', false);
                            }
                        }
                        break;

                    case 'finalizar':
                        if(e.Cerrado === 1 && e.Finalizado === 1 && e.Entregado === 0 ){
                            $('.finalizar').html('<i class="ri-lock-line"></i> Finalizado');
                            $('#finalizar').val(fechaNow(new Date, '/', 1)).prop('readonly', true);
                            $('.FechaEntrega').find('span').removeAttr('title').removeClass().addClass('badge text-bg-success entregar');

                        }else{
                            if(e.Entregado !== 1){
                                $('.finalizar').html('Finalizar');
                                $('#finalizar').val('').prop('readonly', false);
                                $('.FechaEntrega').find('span').removeAttr('title').removeClass().addClass('badge text-bg-dark');
                            }
                        }
                        break;

                    case 'entregar':
                        if(e.Cerrado === 1 && e.Finalizado === 1 && e.Entregado === 1){
                            $('.entregar').html('<i class="ri-lock-line"></i> Entregado');
                            $('#entregar').val(fechaNow(new Date, '/', 1)).prop('readonly', true);
                        
                        }else{
                            if(e.eEnviado !== 1){
                                $('.entregar').html('Entregar');
                                $('#entregar').val('').prop('readonly', false);
                            }  
                        }
                        break;
                    
                    case 'eEnviar':
                        if(e.Cerrado === 1 && e.eEnviado === 1){
                            $('.eEnviar').html('<i class="ri-lock-line"></i> eEnviado');
                            $('#eEnviar').val(fechaNow(new Date, '/', 1)).prop('readonly', true);
                        }else{
                            if(e.Cerrado !== 0){
                                $('.eEnviar').html('eEnviar');
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
        language: 'es',
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

    $('#empresa').select2({
        placeholder: 'Seleccionar empresa',
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
        let fechaActual;
    
        if (fechaAformatear === null) {
            fechaActual = new Date();
        } else {
            fechaActual = new Date(fechaAformatear);
        }
    
        let dia = fechaActual.getDate(), mes = fechaActual.getMonth() + 1, anio = fechaActual.getFullYear();
    
        dia = dia < 10 ? '0' + dia : dia;
        mes = mes < 10 ? '0' + mes : mes;
    
        return (format === 1) ? dia + divider + mes + divider + anio : anio + divider + mes + divider + dia;
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
                    Id: Id,
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