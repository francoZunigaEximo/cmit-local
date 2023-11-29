$(document).ready(()=>{
    
    let hoy = new Date().toISOString().slice(0, 10), precarga = $('#tipoPrestacionPres').val();
    $('#Fecha').val(hoy);
    
    precargaTipoPrestacion(precarga);
    getMap();

    toastr.options = {
        closeButton: true,   
        progressBar: true,    
        timeOut: 3000,        
    };

    //Guardamos la prestación
    $('#guardarPrestacion').click(function(){
    
        let paciente = ID,
            fecha = $('#Fecha').val(),
            tipoPrestacion = $('#tipoPrestacionPres').val(),
            mapa = $('#mapas').val(),
            pago = $('#Pago').val(),
            spago = $('#SPago').val(),
            observaciones = $('#Observaciones').val(),
            autorizado = $('#Autorizado').val(),
            tipo = $('#Tipo').val();
            sucursal = $('#Sucursal').val();
            nroFactura = $('#NroFactura').val(),
            financiador = $('#financiador').val();

        //Validamos la factura
        if(spago === 'G' && autorizado === ''){
            toastr.warning('Si el medio de pago es gratuito, debe seleccionar quien autoriza.', 'Alerta');
            return;
        }

        if(tipoPrestacion === ''){
            toastr.warning('El tipo de prestación no puede ser un campo vacío', 'Alerta');
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

        if(financiador === ''){
            toastr.warning('El campo financiador es obligatorio', 'Alerta');
            return;
        }

        $.ajax({
            url: verificarAlta,
            type: 'get',
            data: {
                Id: ID
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
                        tipo: tipo,
                        autorizado: autorizado,
                        sucursal: sucursal,
                        nroFactura: nroFactura,
                        financiador: financiador,
                        IdART: clienteArt.Id ?? 0,
                        IdEmpresa: cliente.Id ?? 0,
                        _token: TOKEN
                    },
                    success: function(response){

                        let nuevoId = response.nuevoId;
                        toastr.success('Se ha generado la prestación del paciente. Se redireccionará en 3 segundos a edición de prestaciones.', '¡Alta exitosa!');
                        $('#Pago, #SPago, #Observaciones, #NumeroFacturaVta, #Autorizado').val("");
                        setTimeout(function(){
                            let url = location.href,
                                clearUrl = url.replace(/\/pacientes\/.*/, ''),
                                redireccionar =  clearUrl + '/prestaciones/' + nuevoId + '/edit';
                            window.location.href = redireccionar;
                        },3000);
                        
                    },
                    error: function(xhr){
                        
                        toastr.error('No se puedieron almacenar los datos. Consulte con el administrador', 'Error');
                        console.error(xhr);
                    }
                });
            },
            error: function(xhr){
                toastr.error('Ha habido un error el la validación del cliente para generar la prestación. Consulte con su administrador', 'Error');
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
            url: blockPrestacion,
            type: 'get',
            data: {
                Id: Id,
            },
            success: function() {
                toastr.success('Se ha bloqueado la prestación del paciente de manera correcta. Puede que tarde unos minutos en cargar el cambio.', 'Bloqueo procesado');
                cambioEstadoBlock();
            },
            error: function(xhr) {
                toastr.error('No se ha podido bloquear la prestación. Consulte con el administrador', 'Error');
                console.error(xhr);
            }
        });
        
        cambioEstadoBlock();      
    });

    
    //Baja logica de prestación
    $(document).on('click', '#downPrestPaciente', function() {
        let Id = $(this).data('idprest');

        $.ajax({
            url: downPrestaActiva,
            type: 'get',
            data: {
                Id: Id,
            },
            success: function() {
                toastr.success('Se ha dado de baja la prestación del paciente de manera correcta. Puede que tarde unos minutos en cargar el cambio.', 'Acción realizada');
                cambioEstadoDown();
            },
            error: function(xhr){
                swal('Error', 'No se ha podido dar de baja la prestación. Consulte con el administrador', 'error');
                console.error(xhr);
            }
        });
    });

    $(document).on('keydown', function(event) {
        if (event.keyCode === 27) {
            event.preventDefault();

            $('#buscar').val(" ");

            window.location.reload();
        }
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

    function precargaTipoPrestacion(val){
        if(val === 'ART'){

            $('.selectMapaPres').show();
        }else{
            $('.selectMapaPres').hide();
        }
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