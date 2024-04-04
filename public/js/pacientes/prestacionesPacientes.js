$(document).ready(()=>{
    
    let hoy = new Date().toISOString().slice(0, 10), precarga = $('#tipoPrestacionPres').val();
    $('#Fecha').val(hoy);
    
    precargaTipoPrestacion(precarga);
    getMap();
    getListado(null);

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

        if(pago === '' || pago === null || pago === undefined) {
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
            type: 'GET',
            data: {
                Id: ID
            },
            success: function(response){
                let cliente = response.cliente;
                let clienteArt = response.clienteArt;
                console.log(response)
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
    $('#buscarPrestPaciente').on('keypress', function(event){

        if (event.keyCode === 13){
            event.preventDefault();
            
            let buscar = $(this).val();
            $('#grillaPacientes').empty();
            $('#grillaPacientes').append(getListado(buscar));
        }
    });



    //Bloqueo de prestación
    $(document).on('click', '#blockPrestPaciente', function() {
        let Id = $(this).data('idprest');

        $.ajax({
            url: blockPrestacion,
            type: 'GET',
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
            type: 'GET',
            data: {
                Id: Id,
            },
            success: function() {
                toastr.success('Se ha dado de baja la prestación del paciente de manera correcta. Puede que tarde unos minutos en cargar el cambio.', 'Acción realizada');
                cambioEstadoDown();
                getListado(null);
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

    $(document).on('change', '#Pago', function(){
        
        let pago = $(this).val();
        if (pago != 'B') {
            $('#SPago, #Tipo, #Sucursal, #NroFactura').val(" ");
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
        let dia, mes, anio; 
    
        if (fechaAformatear === null) {
            let fechaHoy = new Date();
    
            dia = fechaHoy.getDate().toString().padStart(2, '0');
            mes = (fechaHoy.getMonth() + 1).toString().padStart(2, '0');
            anio = fechaHoy.getFullYear();
        } else {
            let nuevaFecha = fechaAformatear.split("-"); 
            dia = nuevaFecha[0]; 
            mes = nuevaFecha[1]; 
            anio = nuevaFecha[2];
        }
    
        return (format === 1) ? `${dia}${divider}${mes}${divider}${anio}` : `${anio}${divider}${mes}${divider}${dia}`;
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

    function getListado(buscar) {

        mostrarPreloader('#preloader');
        $.ajax({
            url: searchPrestPacientes,
            type: 'GET',
            data: {
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
                        prestacionRz = papre.Empresa,
                        prestacionPe = papre.ParaEmpresa;

                    let recorteArt = prestacionArt.substring(0, 7) + "...",
                        recorteRz = prestacionRz.substring(0,7) + "...",
                        recortePe = prestacionPe.substring(0,7) + "...";

                    let cerradoAdjunto = papre.CerradoAdjunto || 0,
                        total = papre.Total || 1,
                        calculo = parseFloat(((cerradoAdjunto / total) * 100).toFixed(2));
                
                    if (calculo === 100) {
                        resultado = 'fondo-blanco';
                    } else if (calculo >= 86 && calculo <= 99) {
                        resultado ='fondo-verde';
                    } else if (calculo >= 51 && calculo <= 85) {
                        resultado = 'fondo-amarillo';
                    } else if (calculo >= 1 && calculo <= 50) {
                        resultado = 'fondo-naranja';
                    } else {
                        resultado = 'fondo-rojo';
                    }

                    let row = `<tr class="${resultado}">
                                <td>
                                    <input type="checkbox" name="Id" value=${papre.Id} checked="">
                                </td>
                                <td>
                                    ${parseFloat(((cerradoAdjunto / total) * 100).toFixed(2)) + `%`}
                                </td>
                                <td>
                                    ${fechaNow(papre.FechaAlta,'/',0)}
                                </td>
                                <td>
                                    ${papre.Id}
                                </td>
                                <td>
                                    ${papre.Tipo}
                                </td>
                                <td title="${papre.RazonSocial}">
                                    ${recorteRz}
                                </td>
                                <td title="${papre.ParaEmpresa}">
                                    ${recortePe}
                                </td>
                                <td title="${papre.Art}">
                                    ${recorteArt}
                                </td>
                                <td>
                                    <span class="iconGeneralNegro text-uppercase">${ (papre.Anulado == 0 ? "Habilitado" : "Bloqueado")}</span>
                                </td>
                                <td>
                                    ${(`<div class="text-center"><i class="${papre.eEnviado === 1 ? `ri-checkbox-circle-fill negro` : `ri-close-circle-line negro`}"></i></div>`)}
                                </td>
                                <td>
                                    ${(papre.Incompleto === 1 ? `<div class="text-center"><i class="ri-check-line"></i></div>` : `-`)}
                                </td>
                                <td>
                                    ${(papre.Ausente === 1 ? `<div class="text-center"><i class="ri-check-line"></i></div>` : `-`)}
                                </td>
                                <td>
                                    ${(papre.Forma === 1 ? `<div class="text-center"><i class="ri-check-line"></i></div>` : `-`)}
                                </td>
                                <td>
                                    ${(papre.Devol === 1 ? `<div class="text-center"><i class="ri-check-line"></i></div>` : `-`)}
                                </td>
                                <td>
                                    ${(papre.Pago == "B" ? 'Ctdo.' : papre.Pago == "C" ? "CCorriente" : papre.Pago == "C" ? "PCuenta" : "CCorriente")}
                                </td>
                                <td>
                                    ${(papre.Facturado === 1 ? `<div class="text-center"><i class="ri-check-line"></i></div>` : `-`)}
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a title="Editar" href="${urlEdicion}">
                                            <button type="button" class="btn btn-sm iconGeneralNegro"><i class="ri-edit-line"></i></button>
                                        </a>
                                        <div class="bloquear">
                                            <button type="button" id="blockPrestPaciente" data-idprest="${papre.Id}" class="btn btn-sm iconGeneralNegro" title="${(papre.Anulado == 1 ? "Bloqueado" : "Bloquear")}" ${(papre.Anulado == 1 ? "disabled" : "")}><i class="ri-forbid-2-line"></i></button>
                                        </div>
                                        <button type="button" id="downPrestPaciente" data-idprest="${papre.Id}"class="btn btn-sm iconGeneralNegro"><i class="ri-delete-bin-2-line"></i></button>
                                    </div>
                                </td>
                            </tr>`;


                    $('#listaPacientes tbody').append(row);
                });
            }
        });
    }


});