$(document).ready(()=>{
    
    let hoy = new Date().toISOString().slice(0, 10);
    let precarga = $('#tipoPrestacionPres').val();
    $('#Fecha').val(hoy);
    
    precargaTipoPrestacion(precarga);

    function precargaTipoPrestacion(val){
        if(val === 'ART'){

            $('.selectMapaPres').show();
        }else{
            $('.selectMapaPres').hide();
        }
    }
    
    getMap();
    $(document).on('change', '#tipoPrestacionPres', function(){
        let estado = $(this).val();
        actualizarFinanciador(estado);
        
    });

    //Cuenta corriente sin medio de pago
    $(document).on('change', '#Pago', function(){

        let pago = $(this).val();
        
        if (pago === 'C'){
            $('.SPago').hide();
        }else{
            $('.SPago').show();
        } 
    });

    //Guardamos la prestación
    $('#guardarPrestacion').click(function(){
    
        let cliente = $('#selectClientes').val(),
            art = $('#selectArt').val(),
            paciente = ID,
            fecha = $('#Fecha').val(),
            tipoPrestacion = $('#tipoPrestacionPres').val(),
            mapa = $('#mapas').val(),
            pago = $('#Pago').val(),
            spago = $('#SPago').val(),
            observaciones = $('#Observaciones').val(),
            autorizado = $('#autorizado').val(),
            nroFactura = $('#NumeroFacturaVta').val(),
            financiador = $('#financiador').val();

        //Validamos la factura
        if(spago === 'E' && nroFactura.length === 0 || nroFactura == null){
            swal('El número de la factura es obligatoria si el medio de pago es "OTRO"');
            return;
        }

        if(tipoPrestacion === ''){
            swal("Atención", "El tipo de prestación no puede ser un campo vacío", "warning");
            return;
        }

        if(pago === 'B' && nroFactura == ''){
            swal("Atención", "El pago es contado, asi que debe agregar el número de factura para continuar.", "info");
            return;
        }

        if(nroFactura.length > 11){
            swal('Atención', 'EL número de factura no puede tener mas de 11 dígitos y debe ser numerico', 'warning');
            return;
        }

        if(financiador === ''){
            swal('Atención', 'El campo financiador es obligatorio', 'warning');
            return;
        }

        $.ajax({
            url: verificarAlta,
            type: 'get',
            data: {
                Id: ID,
                _token: TOKEN
            },
            success: function(response){
                let cliente = response.cliente;
                let clienteArt = response.clienteArt;

                $.ajax({
                    url: savePrestacion,
                    method: 'post',
                    data: {
                        paciente: paciente,
                        tipoPrestacion: tipoPrestacion,
                        mapas: mapa,
                        fecha: fecha,
                        pago: pago,
                        spago: spago,
                        observaciones: observaciones,
                        nroFactura: nroFactura,
                        financiador: financiador,
                        IdART: clienteArt.Id ?? 0,
                        IdEmpresa: cliente.Id ?? 0,
                        _token: TOKEN
                    },
                    success: function(response){
                        console.log(response);
                        swal("¡Alta exitosa!", "Se ha generado la prestación del paciente. Se redireccionará en 3 segundos a edición de prestaciones.", "success");
                        $('#Pago, #SPago, #Observaciones, #NumeroFacturaVta, #Autorizado').val("");
                        setTimeout(function(){
                            $('#altaPrestacionModal').modal('hide');
                            let url = location.href,
                                clearUrl = url.replace(/\/pacientes\/.*/, ''),
                                redireccionar =  clearUrl + '/prestaciones/' + response + '/edit';
                            window.location.href = redireccionar;
                        },3000);
                        
                    },
                    error: function(xhr){
        
                        swal("Error", "No se puedieron almacenar los datos. Consulte con el administrador", "error");
                        console.error(xhr);
                    }
                });
            },
            error: function(xhr){
                swal("Error", "Ha habido un error el la validación del cliente para generar la prestación. Consulte con su administrador", "error");
                console.error(xhr);
            }   
        });
    });

    //Buscador de prestaciones en Pacientes
    $('#buscarPrestPaciente').on('keypress', function(){

        if (event.keyCode === 13){
            event.preventDefault();
            
            let buscar = $(this).val();
            mostrarPreloader('#preloader');

            $.ajax({
                url: searchPrestPacientes,
                type: 'POST',
                data: {
                    _token: TOKEN,
                    buscar: buscar,
                    paciente: ID
                },
                success: function(response){

                    ocultarPreloader('#preloader');
                    let pacientes = response.pacientes;
                    $('#results').hide();

                    $('#listaPacientes tbody').empty();
                    $.each(pacientes.data, function(index, papre) {

                        //Acortadores
                        let prestacionArt = papre.Art,
                            prestacionRz = papre.RazonSocial,
                            prestacionPe = papre.ParaEmpresa,
                            prestacionApe = papre.Apellido;

                        let recorteArt = prestacionArt.substring(0, 15) + "...",
                            recorteRz = prestacionRz.substring(0,15) + "...",
                            recortePe = prestacionPe.substring(0,15) + "...",
                            recorteApe = prestacionApe.substring(0,15) + "...";

                        let row = `<tr>
                                    <td>
                                        <div class="prestacionComentario" data-id="${papre.Id}" data-bs-toggle="modal" data-bs-target="#prestacionModal">
                                            <i class="ri-chat-3-line"></i>
                                        </div>
                                    </td>
                                    <td>${papre.Id}</td>
                                    <td>${fechaNow(papre.FechaAlta,'/',1)}</td>
                                    <td title="${papre.RazonSocial}">${recorteRz}</td>
                                    <td title="${papre.ParaEmpresa}">${recortePe}</td>
                                    <td>${papre.Identificacion}</td>
                                    <td title="${papre.Art}">${recorteArt}</td>
                                    <td>
                                        <span class="badge badge-soft-${(papre.Anulado == 0 ? "success" : "danger")} text-uppercase">${ (papre.Anulado == 0 ? "Habilitado" : "Bloqueado")}</span>
                                    </td>
                                    <td>${(papre.Pago == "B" ? 'Ctdo.' : papre.Pago == "C" ? "CCorriente" : papre.Pago == "C" ? "PCuenta" : "CCorriente")}
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a title="Editar" href="${urlEdicion}">
                                                <button type="button" class="btn btn-sm btn-primary edit-item-btn"><i class="ri-edit-line"></i></button>
                                            </a>
                                            <button type="button" title="Generar examen" id="AddExamenPrestacion" data-idprest="${papre.Id}" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#addExamenModal"><i class="ri-heart-add-line"></i></button>
                                            <div class="bloquear">
                                                <button type="button" id="blockPrestPaciente" data-idprest="${papre.Id}" class="btn btn-sm btn-warning remove-item-btn" title="${(papre.Anulado == 1 ? "Bloqueado" : "Bloquear")}" ${(papre.Anulado == 1 ? "disabled" : "")}><i class="ri-forbid-2-line"></i></button>
                                            </div>
                                            <button type="button" id="downPrestPaciente" data-idprest="${papre.Id}"class="btn btn-sm btn-danger remove-item-btn" ><i class="ri-delete-bin-2-line"></i></button>
                                        </div>
                                    </td>
                                </tr>`;


                        $('#listaPacientes tbody').append(row);
                    });
                }
            });

        
        }
    });

    //Bloqueo de prestación
    $(document).on('click', '#blockPrestPaciente', function() {
        let Id = $(this).data('idprest');

        $.ajax({
            url: blockPrestPaciente,
            type: 'post',
            data: {
                Id: Id,
                _token: TOKEN
            },
            success: function(result) {
                swal('Bloqueo procesado', 'Se ha bloqueado la prestación del paciente de manera correcta. Puede que tarde unos minutos en cargar el cambio.', 'success');
                cambioEstadoBlock();
            },
            error: function(xhr) {
                swal('Error', 'No se ha podido bloquear la prestación. Consulte con el administrador', 'error');
                console.error(xhr);
            }
        });
        
        cambioEstadoBlock();      
    });

    
    //Baja logica de prestación
    $(document).on('click', '#downPrestPaciente', function() {
        let Id = $(this).data('idprest');

        $.ajax({
            url: downPrestPaciente,
            type: 'post',
            data: {
                prestaciones: Id,
                _token: TOKEN
            },
            success: function() {
                swal('Acción realizada', 'Se ha dado de baja la prestación del paciente de manera correcta. Puede que tarde unos minutos en cargar el cambio.', 'success');
                cambioEstadoDown();
            },
            error: function(xhr){
                swal('Error', 'No se ha podido dar de baja la prestación. Consulte con el administrador', 'error');
                console.error(xhr);
            }
        });
    });

    //Mostrar u ocultar autorizados si esta "Sin Cargo"
    $('#SPago').change(function(){
        
        let autorizado = $('.Autorizado');

        return ($(this).val() == 'G')? autorizado.show() : autorizado.hide();
    });

    $(document).on('keydown', function(event) {
        if (event.keyCode === 27) {
            event.preventDefault();

            $('#buscar').val(" ");

            window.location.reload();
        }
    });

    // Cuando la página se haya recargado completamente
    $(window).on('load', function() {
        $('a.nav-link[href="#prestaciones"]').tab('show');
    });

    function mostrarPreloader(arg) {
        $(arg).css({
            opacity: '0.3',
            visibility: 'visible'
        });
    }
    
    function ocultarPreloader(arg) {
        $(arg).css({
            opacity: '0',
            visibility: 'hidden'
        });
    }
    
    //Obtener fechas
    function fechaNow(fechaAformatear, divider, format) {
        let fechaActual;
    
        if (fechaAformatear === null) {
            fechaActual = new Date();
        } else {
            fechaActual = new Date(fechaAformatear);
        }
    
        let dia = fechaActual.getDate(),
            mes = fechaActual.getMonth() + 1
            anio = fechaActual.getFullYear();
    
        dia = dia < 10 ? '0' + dia : dia;
        mes = mes < 10 ? '0' + mes : mes;
    
        return (format === 1) ? dia + divider + mes + divider + anio : anio + divider + mes + divider + dia;
    }

    function cambioEstadoDown() {
        let borrar = $('#filapresId').data('filapres');
        $('tr[data-filapres="' + borrar + '"]').hide();
    }

    function cambioEstadoBlock() {
        $('#blockPrestPaciente').prop('disabled', true);
        $('#estadoBadge').removeClass('badge badge-soft-success').addClass('badge badge-soft-danger');
        $('#estadoBadge').text('Bloqueado');
    }

    function getMap(){

        let empresa = $('#selectClientes').val(),
            art = $('#selectArt').val();
 
        $.get(getMapas, {empresa: empresa, art: art})
            .done(function(response){

                let mapas = response.mapas;
                $('#mapas').empty().append('<option value="" selected>Elija un mapa...</option>');
                
                if(mapas.length === 0)
                {
                    $('#mapas').empty().append('<option title=""value="Sin mapas disponibles para esta ART y Empresa." selected>Sin mapas disponibles.</option>');
                }else{

                    $.each(mapas, function(index, d){

                        let contenido = `<option value="${d.Id}">${d.Nro} | Empresa: ${d.RSE} - ART: ${d.RSArt}</option>`;
    
                        $('#mapas').append(contenido);
                    });
                }
                
            })
    }

    function actualizarFinanciador(estado){
        if (estado !== 'ART') {
            $("#empresaFinanciador").prop("selected", true);
            
        } else if (estado === '') {
            $("#emptyFinanciador").prop("selected", true);
            
        } else {
            $("#artFinanciador").prop("selected", true);
            
        }
    }


});