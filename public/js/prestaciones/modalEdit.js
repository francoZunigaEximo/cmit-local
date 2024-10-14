$(document).ready(function(){

    $(document).on('click', '.verExamen', function(e){
        e.preventDefault();

        let id = $(this).data('id');
        loadModalExamen(id, ID);
    });

    $('#modalExamen').on('hidden.bs.modal', function () {
        $('#listaExamenes').empty();
        cargarExamen();
    });

    function loadModalExamen(id, idPrestacion) {
        preloader('on');
        $.get(editModal, {Id: id})
            .done(function(response){
                preloader('off');

                const estadoAbierto = [0, 1, 2], 
                      estadoCerrado = [3, 4, 5], 
                      itemprestaciones = response.itemprestacion, 
                      pacientes = response.paciente.paciente,
                      examenes = itemprestaciones.examenes,
                      factura = itemprestaciones.facturadeventa,
                      notaCreditoEx = itemprestaciones?.notaCreditoIt?.notaCredito;

                let paciente = pacientes.Nombre + ' ' + pacientes.Apellido,
                    anulado = itemprestaciones.Anulado === 1 ? '<span class="custom-badge rojo">Bloqueado</span>' : '',
                    estado = estadoAbierto.includes(itemprestaciones.CAdj) ? 'Abierto' : estadoCerrado.includes(itemprestaciones.CAdj) ? 'Cerrado' : '';
                    estadoColor = estadoAbierto.includes(itemprestaciones.CAdj) ? {'color': 'red'} : estadoCerrado.includes(itemprestaciones.CAdj) ? {'color' : 'green'} : '{}',
                    estadoAdjEfector = null,
                    colorAdjEfector = examenes.Adjunto === 1 && response.adjuntoEfector === 0 ? {"color" : 'red'} : examenes.Adjunto === 1 && response.adjuntoEfector === 0 ? {"color" : "green"} : '{}',
                    estadoColorI = [0,1,2].includes(itemprestaciones.CInfo) ? {"color": "red"} : itemprestaciones.CInfo === 3 ? {"color": "green"} : '{}',
                    estadoI = itemprestaciones.CInfo === 1 ? 'Pendiente' : itemprestaciones.CInfo === 1 ? 'Borrador' : itemprestaciones.CInfo === 3 ? 'Cerrado' : '',
                    colorAdjInformador = itemprestaciones.profesionales2.InfAdj === 1 && response.adjuntoInformador === 0 ? {"color": "red"} : itemprestaciones.profesionales2.InfAdj === 1 && response.adjuntoInformador === 0 ? {"color": "green"} : '{}',
                    estadoAdjInformador = null,
                    tipo = factura.Tipo || '',
                    sucursal = factura.Sucursal || '',
                    nroFactura = factura.NroFactura || '',
                    facturaExamen = tipo + sucursal + nroFactura,
                    tipoNc = notaCreditoEx?.Tipo || '',
                    sucursalNc = notaCreditoEx?.Sucursal || '',
                    nroNc = notaCreditoEx?.Nro || '',
                    notaCEx = tipoNc + sucursalNc + nroNc;

                switch(true) {
                    case (examenes.Adjunto === 0):
                        estadoAdjEfector = 'No lleva adjuntos';
                        break;
                    case (examenes.Adjunto === 1 && response.adjuntoEfector === 0):
                        estadoAdjEfector = 'Pendiente';
                        break;
                    case (examenes.Adjunto === 1 && response.adjuntoEfector === 1):
                        estadoAdjEfector = 'Adjuntado';
                        break;
                    default:
                        estadoAdjEfector = '';
                        break;
                }

                switch(true) {
                    case (itemprestaciones.profesionales2.InfAdj === 0):
                        estadoAdjInformador = 'No lleva Adjuntos';
                        break;
                    case (itemprestaciones.profesionales2.InfAdj === 1 && itemprestaciones.adjuntoInformador === 0):
                        estadoAdjInformador = 'Pendiente';
                        break;
                    case (itemprestaciones.profesionales2.InfAdj === 1 && itemprestaciones.adjuntoInformador === 1):
                        estadoAdjInformador = 'Adjuntado';
                        break;
                    default:
                        estadoAdjInformador = '';
                        break;
                }
                
                $('#ex-prestacion').empty().text(idPrestacion);
                $('#ex-qr').empty().text(response.qrTexto);
                $('#ex-paciente').empty().text(paciente);
                $('#ex-anulado').empty().html(anulado);
                $('#ex-identificacion').val(itemprestaciones.Id || '');
                $('#ex-prestacion').val(itemprestaciones.IdPrestacion || '');
                $('#ex-fecha').val(itemprestaciones.prestaciones.Fecha || '');
                $('#ex-examen').val(examenes.Nombre || '');
                $('#ex-provEfector').val(examenes.proveedor1.Nombre || '');
                $('#ex-IdEfector').val(examenes.proveedor1.Id || '');
                $('#ex-provInformador').val(examenes.proveedor2.Nombre || '');
                $('#ex-IdInformador').val(examenes.proveedor2.Id || '');
                $('#ex-ObsExamen').val(stripTags(itemprestaciones.ObsExamen) || '');
                $('#ex-FechaAsignado').val(itemprestaciones.FechaAsignado || '');
                
                $('#ex-EstadoEx').val(estado);
                $('#ex-EstadoEx').empty().css(estadoColor);

                $('#ex-FechaPagado').val(itemprestaciones.FechaPagado || '')
                $('#ex-CAdj').val(itemprestaciones.CAdj || '')

                itemprestaciones.Anulado === 1 ? $('#ex-asignar, #ex-abrir, #ex-cerrar').hide() : $('#ex-asignar, #ex-abrir, #ex-cerrar').show();
                $('#ex-Estado').val(estadoAdjEfector);
                $('#ex-Estado').empty().css(colorAdjEfector || '');

                itemprestaciones.CInfo !== 0 ? $('.visualizarInformador').show() : $('.visualizarInformador').hide();

                $('#ex-EstadoI').val(estadoI)
                $('#ex-EstadoI').empty().css(estadoColorI);
                $('#ex-FechaPagado2').val(itemprestaciones.FechaPagado2 || '');
                
                itemprestaciones.Anulado === 1 ? $('#ex-asignarI, #ex-abrirI, #ex-cerrarI').hide() : $('#ex-asignar, #ex-abrir, #ex-cerrar').show();

                $('#ex-CInfo').val(itemprestaciones.CInfo || '');

                $('#ex-EstadoInf').val(estadoAdjInformador);
                $('#ex-EstadoInf').empty().css(colorAdjInformador);
                $('#ex-Obs').val(stripTags(itemprestaciones?.itemsInfo?.Obs));

                $('#ex-FechaFacturaVta').val(factura?.Fecha);
                $('#ex-NroFacturaVta').val(facturaExamen);

                $('#ex-FechaNC').val(notaCreditoEx?.Fecha);
                $('#ex-NumeroNC').val(notaCEx);


            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;  
            });
    }

});