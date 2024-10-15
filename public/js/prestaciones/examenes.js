$(document).ready(()=>{

    let idExamen = [];

    const valAbrir = ['3','4','5'], valCerrar = ['0','1','2'], valCerrarI = 3;

    let cadj = $('#ex-CAdj').val(), CInfo = $('#ex-CInfo').val(), efector = $('#ex-efectores').val(), informador = $('#ex-informadores').val(), provEfector = $('#IdEfector').val(), provInformador = $('#IdInformador').val(), Estado = $('#ex-Estado').val(), EstadoI = $('#ex-EstadoI').val();

    $('.ex-abrir, .ex-cerrar, .ex-asignar, .ex-liberar, .ex-asignarI, .ex-liberarI, .ex-cerrarI, .ex-adjuntarEfector, .ex-adjuntarInformador').hide();

    cargarExamen();

    $('#exam').select2({
        placeholder: 'Seleccionar exámen...',
        language: {
            noResults: function() {

            return "No hay examenes con esos datos";        
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
           url: searchExamen,
           dataType: 'json',
           delay: 250,
           data: function(params) {
                return {
                    buscar: params.term,
                };
           },
           processResults: function(data) {
                return {
                    results: data.examen
                };
           },
           cache: true,
        },
        minimumInputLength: 2
    });

    $(document).on("select2:open", () => {
        document.querySelector(".select2-container--open .select2-search__field").focus()
    });

    $(document).on('click', '.addPaquete', function(e){
        e.preventDefault();
        
        let paquete = $('#paquetes').val();
        
        if([null, undefined, ''].includes(paquete)){
            toastr.warning("Debe seleccionar un paquete para poder añadirlo en su totalidad");
            return;
        }
        preloader('on');
       $.ajax({
            url: paqueteId,
            type: 'POST',
            data: {
                _token: TOKEN,
                IdPaquete: paquete,
            },

            success:function(response){

                preloader('off');
                let data = response.examenes,
                    ids = data.map(function(item) {
                    return item.Id;
                  });
                saveExamen(ids);  
                $('.addPaquete').val([]).trigger('change.select2');
            },
            error: function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return; 
            }
       });
       
    });

    $(document).on('click', '.deleteExamenes, .deleteExamen', function(e){

        e.preventDefault();

        let ids = [], tieneAdjunto = false, id = $(this).data('delete'), adjunto, archivos;
        var checkAll ='';

        if ($(this).hasClass('deleteExamenes')) {

            $('input[name="Id_examenes"]:checked').each(function() {
                adjunto = $(this).data('adjunto'), archivos = $(this).data('archivo');
                adjunto == 1 && archivos > 0 ? tieneAdjunto = true : ids.push($(this).val());
            });
    
            checkAll = $('#checkAllExamenes').prop('checked');

        } else if($(this).hasClass('deleteExamen')) {

            adjunto = $(this).data('adjunto'), archivos = $(this).data('archivo');
            adjunto == 1 && archivos > 0 ? tieneAdjunto = true : ids.push(id);
        }

        if (tieneAdjunto) {
            toastr.warning('El o los examenes seleccionados tienen un reporte adjuntado. El mismo no se podrá eliminar.');
            return;
        }

        if(ids.length === 0 && checkAll === false){
            toastr.warning('No hay examenes seleccionados');
            return;
        }  
    
        swal({
            title: "Confirme la eliminación de los examenes",
            icon: "warning",
            buttons: ["Cancelar", "Eliminar"],
        }).then((confirmar) => {
            if (confirmar){

                preloader('on');
                $.ajax({
                    url: deleteItemExamen,
                    type: 'POST',
                    data: {
                        Id: ids,
                        _token: TOKEN
                    },
                    success: function(response){
                        preloader('off');
                        var estados = [];
                        
                        response.forEach(function(msg) {
                            
                            let tipoRespuesta = {
                                success: 'success',
                                fail: 'info'
                            }
                            toastr[tipoRespuesta[msg.estado]](msg.message, "Atención", { timeOut: 10000 });
                            estados.push(msg.estado);
        
                        });

                        if(estados.includes('success')) {
                            $('#listaExamenes').empty();
                            $('#exam').val([]).trigger('change.select2');
                            $('#addPaquete').val([]).trigger('change.select2');
                            cargarExamen();
                            checkExamenes(ID);
                        }

                    }
                });
            }
            
        });
    });

    $(document).on('click', '.bloquearExamenes, .bloquearExamen', function(e){

        e.preventDefault();

        let ids = [], id = $(this).data('bloquear');
        var checkAll ='';

        if ($(this).hasClass('bloquearExamenes')) {

            $('input[name="Id_examenes"]:checked').each(function() {
                ids.push($(this).val());
        
            });
    
            checkAll =$('#checkAllExamenes').prop('checked');
        
        }else if($(this).hasClass('bloquearExamen')){

            ids.push(id);
        }

        if(ids.length === 0 && checkAll === false){
            toastr.warning('No hay examenes seleccionados');
            return;
        }
    
        swal({
            title: "Confirme el bloqueo de los examenes",
            icon: "warning",
            buttons: ["Cancelar", "Bloquear"],
        }).then((confirmar) => {
            if(confirmar){

                preloader('on');
                $.ajax({    
                    url: bloquearItemExamen,
                    type: 'POST',
                    data: {
                        Id: ids,
                        _token: TOKEN
                    },
                    success: function(response){
                        var estados = [];
                        
                        preloader('off')
                        response.forEach(function(msg) {

                            let tipoRespuesta = {
                                success: 'success',
                                fail: 'info'
                            }
                            
                            toastr[tipoRespuesta[msg.estado]](msg.message, "Atención", { timeOut: 10000 })
                            
                            estados.push(msg.estado)
                            
                        });
                        
                        if(estados.includes('success')) {
                            $('#listaExamenes').empty();
                            $('#exam').val([]).trigger('change.select2');
                            $('#addPaquete').val([]).trigger('change.select2');
                            cargarExamen();
                        }
                    }
                });
            }
        });     
    });

    $(document).on('click', '.abrirExamenes', function(e) {
        e.preventDefault();

        let ids = [];

        $('input[name="Id_examenes"]:checked').each(function() {
            ids.push($(this).val());
        });

        let checkAll = $('#checkAllExamenes').prop('checked');

        if(ids.length === 0 && checkAll === false){
            toastr.warning('No hay examenes seleccionados');
            return;
        }

        swal({
            title: "Confirme la apertura de los examenes",
            icon: "warning",
            buttons: ["Cancelar", "Abrir"],
        }).then((confirmar) => {
            if(confirmar){

                preloader('on');
                $.ajax({
                    url: updateEstadoItem,
                    type: 'POST',
                    data: {
                        Ids: ids,
                        _token: TOKEN
                    },
                    success: function(response){
                        var estados = [];
                        preloader('off');
                        response.forEach(function(msg) {
                            
                            let tipoRespuesta = {
                                success: 'success',
                                fail: 'info'
                            }
                           
                            toastr[tipoRespuesta[msg.estado]](msg.message, "Atención", { timeOut: 10000 });
                            estados.push(msg.estado);
                        });

                        if(estados.includes('success')) {
                            $('#listaExamenes').empty();
                            $('#exam').val([]).trigger('change.select2');
                            $('#addPaquete').val([]).trigger('change.select2');
                            cargarExamen();
                        }

                    }
                });
            }
        });

            

    });

    $(document).on('click', '.adjuntoExamenes', function(e){
        e.preventDefault();

        let ids = [];

        $('input[name="Id_examenes"]:checked').each(function() {
            ids.push($(this).val());
        });

        let checkAll = $('#checkAllExamenes').prop('checked');

        if(ids.length === 0 && checkAll === false){
            toastr.warning('No hay examenes seleccionados');
            return;
        }

        swal({
            title: "Confirme la marca de adjunto de los examenes",
            icon: "warning",
            buttons: ["Cancelar", "Adjuntar"],
        }).then((confirmar) => {
            if(confirmar){

                preloader('on');
                $.ajax({
                    url: marcarExamenAdjunto,
                    type: 'POST',
                    data: {
                        Ids: ids,
                        _token: TOKEN
                    },
                    success: function(response){
                        var estados = [];
                        preloader('off');
                        response.forEach(function(msg) {
                            
                            let tipoRespuesta = {
                                success: 'success',
                                fail: 'info'
                            }
                            
                            toastr[tipoRespuesta[msg.estado]](msg.message, "Atención", { timeOut: 10000 });
                            estados.push(msg.estado);
        
                        });

                        if(estados.includes('success')) {
                            $('#listaExamenes').empty();
                            $('#exam').val([]).trigger('change.select2');
                            $('#addPaquete').val([]).trigger('change.select2');
                            cargarExamen();
                        }

                    }
                });
            }
        });    
    });

    $(document).on('click', '.liberarExamenes', function(e){

        e.preventDefault();

        let ids = [];

        $('input[name="Id_examenes"]:checked').each(function() {
            ids.push($(this).val());
        });

        let checkAll = $('#checkAllExamenes').prop('checked');

        if(ids.length === 0 && checkAll === false){
            toastr.warning('No hay examenes seleccionados');
            return;
        }

        swal({
            title: "Confirme la liberación de los examenes",
            icon: "warning",
            buttons: ["Cancelar", "Liberar"],
        }).then((confirmar) => {
            if(confirmar) {

                preloader('on');
                $.ajax({
                    url: liberarExamen,
                    type: 'POST',
                    data: {
                        Ids: ids,
                        _token: TOKEN
                    },
                    success: function(response){
                        var estados = [];
                        preloader('off');
                        response.forEach(function(msg) {
                            
                            let tipoRespuesta = {
                                success: 'success',
                                fail: 'info'
                            }
                            
                            toastr[tipoRespuesta[msg.estado]](msg.message, "Atención", { timeOut: 10000 });
                            estados.push(msg.estado);
                        });

                        if(estados.includes('success')) {
                            $('#listaExamenes').empty();
                            $('#exam').val([]).trigger('change.select2');
                            $('#addPaquete').val([]).trigger('change.select2');
                            cargarExamen();
                        }

                    }
                });
            }
        });     
    });

    $(document).on('click', '.addExamen', function(e){
        e.preventDefault();

        let id = $("#exam").val();
        
        if(['', null, undefined].includes(id)) {
            toastr.warning("Debe seleccionar un examen para poder añadirlo a la lista");
            return;
        }
        saveExamen(id);
    });

    $(window).on('popstate', function(event) {
        location.reload();
    });

    $('#checkAllExamenes').on('click', function() {

        $('input[type="checkbox"][name="Id_examenes"]:not(#checkAllExamenes)').prop('checked', this.checked);
    });
  
    $(document).on('click', '.incompleto, .ausente, .forma, .sinesc, .devol', function() {
        let classes = $(this).attr('class').split(' '),
            //item = $(this).closest('tr').find('td:first').attr('id');
            item = $(this).closest('tr').find('td:eq(1)').attr('id');

            const opcionesClasses = {
                'incompleto': 'Incompleto',
                'ausente': 'Ausente',
                'forma': 'Forma',
                'sinesc': 'SinEsc',
                'devol': 'Devol'
              };

            let buscarClasse = classes.find(clase => opcionesClasses.hasOwnProperty(clase));

            if(buscarClasse){
                opcionesExamenes(item, opcionesClasses[buscarClasse]);
            }
    
    });

    $(document).on('click', '.verExamen', function(e){
        e.preventDefault();

        let id = $(this).data('id');
        loadModalExamen(id, ID);
    });

    $('#modalExamen').on('hidden.bs.modal', function () {
        $('#listaExamenes').empty();
        cargarExamen(ID);
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
                    colorAdjEfector = examenes.Adjunto === 1 && response.adjuntoEfector === 0 ? {"color" : 'red'} : examenes.Adjunto === 1 && response.adjuntoEfector === 1 ? {"color" : "green"} : '{}',
                    estadoColorI = [0,1,2].includes(itemprestaciones.CInfo) ? {"color": "red"} : itemprestaciones.CInfo === 3 ? {"color": "green"} : '{}',
                    estadoI = itemprestaciones.CInfo === 1 ? 'Pendiente' : itemprestaciones.CInfo === 1 ? 'Borrador' : itemprestaciones.CInfo === 3 ? 'Cerrado' : '',
                    colorAdjInformador = itemprestaciones.profesionales2.InfAdj === 1 && response.adjuntoInformador === 0 ? {"color": "red"} : itemprestaciones.profesionales2.InfAdj === 1 && response.adjuntoInformador === 1 ? {"color": "green"} : '{}',
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
                    case (itemprestaciones.profesionales2.InfAdj === 1 && response.adjuntoInformador === 0):
                        estadoAdjInformador = 'Pendiente';
                        break;
                    case (itemprestaciones.profesionales2.InfAdj === 1 && response.adjuntoInformador === 1):
                        estadoAdjInformador = 'Adjuntado';
                        break;
                    default:
                        estadoAdjInformador = '';
                        break;
                }

                checkAdjunto(itemprestaciones.Id, 'informador').then(response => {
                    if (response === true) {
                        $('.ex-adjuntarInformador').show();
                    } else {
                        $('.ex-adjuntarInformador').hide();
                    }
                });
            
                checkAdjunto(itemprestaciones.Id, 'efector').then(response => {
                    if (response === true) {
                        $('.ex-adjuntarEfector').show();
                    } else {
                        $('.ex-adjuntarEfector').hide();
                    }
                });
                
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

                $('#ex-efectores').empty().append('<option selected value="' + itemprestaciones.IdProfesional + '">' + itemprestaciones.profesionales1.Apellido + ' ' + itemprestaciones.profesionales1.Nombre + '</option>');

                $('#ex-informadores').empty().append('<option selected value="' + itemprestaciones.IdProfesional2 + '">' + itemprestaciones.profesionales2.Apellido + ' ' + itemprestaciones.profesionales2.Nombre + '</option>');

                asignar(efector, 'efector');
                asignar(informador, 'informador');
                liberar(cadj, efector, 'efector');
                liberar(CInfo, informador, 'informador');
                abrir(cadj);
                cerrar(cadj, efector, 'efector');
                cerrar(cadj, informador, 'informador');
                optionsGeneral(examenes.proveedor1.Id, 'efector');
                optionsGeneral(examenes.proveedor2.Id, 'informador');
                listadoE(itemprestaciones.Id);
                listadoI(itemprestaciones.Id);

                eventoAbrir(itemprestaciones.Id);
                eventoCerrar(itemprestaciones.Id);
                eventoAsignar(itemprestaciones.Id);

            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;  
            });
    }

    function saveExamen(id){

        idExamen = [];
        if (Array.isArray(id)) {
            $.each(id, function(index, item) {
                idExamen.push(item);
              });
        }else{
            idExamen.push(id);
        }

        if (idExamen.length === 0) {
            toastr.warning("No existe el exámen o el paquete no contiene examenes");
            return;
        }
        preloader('on');
        $.ajax({

            url: saveItemExamenes,
            type: 'post',
            data: {
                _token: TOKEN,
                idPrestacion: ID,
                idExamen: idExamen
            },
            success: function(){
                preloader('off');
                $('#listaExamenes').empty();
                $('#exam').val([]).trigger('change.select2');
                $('#addPaquete').val([]).trigger('change.select2');
                cargarExamen();
        },
            error: function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;  
            }
        });

    }

    function opcionesExamenes(item, opcion){
        preloader('on');
        $.ajax({
            url: itemExamen,
            type: 'Post',
            data: {
                _token: TOKEN,
                Id: item,
                opcion: opcion
            },
            success: function(response){
                preloader('off');
                toastr.success(response.msg);

                let fila = $('td#' + item).closest('tr'), 
                    span= fila.find('span#' + opcion.toLowerCase()),
                    clase = span.attr('class'),
                    contenido = (clase === 'badge badge-soft-dark' ? 'custom-badge rojo' : 'badge badge-soft-dark');
            
                span.removeClass().addClass(contenido);
                checkerIncompletos(ID);
            },
            error: function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;  
            }
        });
    }

    async function checkerIncompletos(idPrestacion)
    {
        if([null,'',0].includes(idPrestacion)) return;

        $.get(await checkInc, { Id: idPrestacion }, function(response) {

            let ids = ['Incompleto', 'Ausente', 'Forma', 'SinEsc', 'Devol'];
            let propiedades = ['inc', 'aus', 'forma', 'sin', 'devo'];
        
            ids.forEach((id, index) => {
                let propiedad = propiedades[index];
                let className = response[propiedad] === 'Completo' ? 'grisClaro' : 'grisFuerte';
        
                $('#' + id).removeClass().addClass('form-control ' + className);
            });
        });
    }

    async function cargarExamen() {
        preloader('on');
    
        try {
            const result = await $.ajax({
                url: checkItemExamen,
                method: 'GET',
                data: { Id: ID },
            });
    
            preloader('off');
            let estado = result.respuesta;
            let examenes = result.examenes;
    
            if (estado) {
                preloader('on');
                const response = await $.ajax({
                    url: getItemExamenes,
                    type: 'POST',
                    data: {
                        _token: TOKEN,
                        IdExamen: examenes,
                        Id: ID,
                        tipo: 'listado'
                    },
                });
    
                let registros = response.examenes;
                checkExamenes(ID);
    
                let filas = '';
                const responseEfector = await checkMultiId(ID, "efector");
                const responseInformador = await checkMultiId(ID, "informador");
                const firstE = responseEfector !== undefined ? responseEfector : null;
                const firstI = responseInformador !== undefined ? responseInformador : null;
                preloader('off');
                for (let i = 0; i < registros.length; i++) {
                    const examen = registros[i];

                    filas += `
                        <tr ${examen.Anulado === 1 ? 'class="filaBaja"' : ''}>
                            <td><input type="checkbox" name="Id_examenes" value="${examen.IdItem}" checked data-adjunto="${examen.ExaAdj}" data-archivo="${examen.archivos}"></td>
                            <td data-idexam="${examen.IdExamen}" id="${examen.IdItem}" style="text-align:left">${examen.Nombre} ${examen.Anulado === 1 ? '<span class="custom-badge rojo">Bloqueado</span>' : ''} ${firstE.IdEntidad === examen.IdItem ? '<i title="Carga multiple, efector, desde este exámen" class="ri-file-mark-line verde"></i>' : ''} ${firstI.IdEntidad === examen.IdItem ? '<i title="Carga multiple, informador, desde este exámen" class="ri-file-mark-line naranja"></i>' : ''}</td>
                            <td>
                                <span id="incompleto" class="${(examen.Incompleto === 0 || examen.Incompleto === null ? 'badge badge-soft-dark' : 'custom-badge rojo')}">
                                    <i class="ri-flag-2-line ${examen.Anulado === 0 ? 'incompleto' : ''}"></i>
                                </span>
                            </td>
                            <td>
                                <span id="ausente" class="${(examen.Ausente === 0 || examen.Ausente === null ? 'badge badge-soft-dark' : 'custom-badge rojo')}">
                                    <i class="ri-flag-2-line ${examen.Anulado === 0 ? 'ausente' : ''}"></i>
                                </span>
                            </td>
                            <td>
                                <span id="forma" class="${(examen.Forma === 0 || examen.Forma === null ? 'badge badge-soft-dark' : 'custom-badge rojo')}">
                                    <i class="ri-flag-2-line ${examen.Anulado === 0 ? 'forma' : ''}"></i>
                                </span>
                            </td>
                            <td>
                                <span id="sinesc" class="${(examen.SinEsc === 0 || examen.SinEsc === null ? 'badge badge-soft-dark' : 'custom-badge rojo')}">
                                    <i class="ri-flag-2-line ${examen.Anulado === 0 ? 'sinesc' : ''}"></i>
                                </span>
                            </td>
                            <td>
                                <span id="devol" class="${(examen.Devol === 0 || examen.Devol === null ? 'badge badge-soft-dark' : 'custom-badge rojo')}">
                                    <i class="ri-flag-2-line ${examen.Anulado === 0 ? 'devol' : ''}"></i>
                                </span>
                            </td>
                            <td class="date text-center" title="${examen.ApellidoE} ${examen.NombreE}">${examen.ApellidoE}
                                <span class="badge badge-soft-${([0,1,2].includes(examen.CAdj) ? 'danger': ([3,4,5].includes(examen.CAdj) ? 'success' : ''))}">
                                    ${([0,1,2].includes(examen.CAdj) ? 'Abierto': ([3,4,5].includes(examen.CAdj) ? 'Cerrado' : ''))}
                                </span>
                                ${examen.ExaAdj === 1 ? `<i class="ri-attachment-line ${examen.archivos > 0 ? 'verde' : 'gris'}"></i>`: ``}    
                            </td>
                            <td class="date text-center" title="${examen.Informe === 0 ? examen.ApellidoI : ''} ${examen.Informe === 1 ? examen.NombreI : ''}">${examen.Informe === 1 ? examen.ApellidoI : ''}
                                <span class="badge badge-soft-${(examen.CInfo === 0 ? 'dark' :(examen.CInfo === 3 ? 'success' : ([0,1,2].includes(examen.CInfo)) ? 'danger' : ''))}">${(examen.CInfo === 0 ? '' : (examen.CInfo === 3 ? 'Cerrado' : (examen.CInfo == 2 ? 'Borrador' : ([0,1].includes(examen.CInfo) ? 'Pendiente': ''))))}</span>
                                ${examen.CInfo !== 0 ? `<i class="ri-attachment-line ${examen.archivosI > 0 ? 'verde' : 'gris'}"></i>`: ``}   
                            </td>
                            <td class="phone"><span class="${examen.Facturado === 1 ? 'badge badge-soft-success' : 'custom-badge rojo'}"><i class="ri-check-line"></i></span></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <div class="edit">
                                        <button data-id="${examen.IdItem}" type="button" class="btn btn-sm iconGeneral verExamen" title="Ver" data-bs-toggle="modal" data-bs-target="#modalExamen"><i class="ri-search-eye-line"></i></button>
                                    </div>
                                    ${examen.Anulado === 0 ? `
                                        <div class="bloquear">
                                            <button data-bloquear="${examen.IdItem}" class="btn btn-sm iconGeneral bloquearExamen" title="Baja">
                                                <i class="ri-forbid-2-line"></i>
                                            </button>
                                        </div>
                                    ` : ''}
                                    <div class="remove">
                                        <button data-delete="${examen.IdItem}" class="btn btn-sm iconGeneral deleteExamen" title="Eliminar">
                                            <i class="ri-delete-bin-2-line"></i>
                                        </button>
                                    </div>  
                                </div>
                            </td>
                        </tr>`;
                }
    
                $('#listaExamenes').append(filas);
    
                $("#listado").fancyTable({
                    pagination: true,
                    perPage: 50,
                    searchable: false,
                    globalSearch: false,
                    sortable: false, 
                });
            }
        } catch (jqXHR) {
            preloader('off');
            let errorData = JSON.parse(jqXHR.responseText);            
            checkError(jqXHR.status, errorData.msg);
        }
    }
    

    async function checkExamenes(id) {

        $.get(await buscarEx, {Id: id}, function(response){

            response === 0 
                ? $('.auditoria, .autorizados, .evaluacion, .banderas').hide()
                : $('.auditoria, .autorizados, .evaluacion, .banderas').show()  
        })
    }

    async function checkMultiId(idPrestacion, tipo) {
    
        return new Promise((resolve, reject) => {
            $.get(checkFirst, { Id: idPrestacion, who: tipo })
                .done(function(response) { 
                    resolve(response);      
                })
                .fail(function(error) {
                    reject(error);
                });
        });
    }

    async function checkAdjunto(id, tipo) {
        if (['', 0, null].includes(id)) return;

        return new Promise((resolve, reject) => {
            $.get(checkAdj, { Id: id, Tipo: tipo })
                .done(function(response) { 
                    resolve(response);      
                })
                .fail(function(error) {
                    console.error("Error:", error);
                    reject(error);
                });
        });
    }

    /*******************************Modal Examen ************************************/

    function eventoAbrir(id) {
        $(document).on('click', '#ex-abrir', function(e){
            e.preventDefault();
            let lista = {3: 0, 4: 1, 5: 2};
    
            if(cadj in lista){
                preloader('on');
                $.post(updateItem, {Id : id, _token: TOKEN, CAdj: lista[cadj], Para: 'abrir' })
                    .done(function(){
                        preloader('off');
                        toastr.success('Se ha realizado la acción correctamente');
                        setTimeout(() => {
                            location.reload();
                        }, 3000);
                        
                    })
                    .fail(function(jqXHR){
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return;
                    });
            }
        });
    }

    function eventoCerrar(id) {
        $(document).on('click', '#ex-cerrar, #ex-cerrarI', function(){
            let who = $(this).hasClass('ex-cerrar') ? 'cerrar' : 'cerrarI',
                listaE = {0: 3, 2: 5, 1: 4},
                listaI = ['0', '1', '2', '3'];
    
            if(who === 'cerrar' && cadj in listaE){
                preloader('on');
                $.post(updateItem, {Id : id, _token: TOKEN, CAdj: listaE[cadj], Para: who })
                    .done(function(){
                        preloader('off');
                        toastr.success('Se ha cerrado al efector correctamente');
                        setTimeout(() => {
                            location.reload();
                        }, 3000);
                        
                    })
                    .fail(function(jqXHR){
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return;
                    });
    
            }else if(who === 'cerrarI' && listaI.includes(CInfo)){
                preloader('on');
                $.post(updateItem, {Id : id, _token: TOKEN, CInfo: 3, Para: who })
                    .done(function(){
                        preloader('off');
                        toastr.success('Se ha cerrado al informador correctamente');
                        setTimeout(() => {
                            location.reload();
                        }, 3000);
                        
                    })
                    .fail(function(jqXHR){
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return;
                    });
            }
        });
    }

    function eventoAsignar(id) {
        $(document).on('click', '#asignar, #asignarI', function(e) {
            e.preventDefault();
    
            let who = $(this).hasClass('asignar') ? 'asignar' : 'asignarI',
                check = (who === 'asignar') ? $('#efectores').val() : $('#informadores').val();
    
            if(['', null, 0, '0'].includes(check)) {
                toastr.warning("Debe seleccionar un Efector/Informador para poder asignar uno");
                return;
            }
    
            swal({
                title: "¿Esta seguro que desea asignar?",
                icon: "warning",
                buttons: ["Cancelar", "Aceptar"]
            }).then((confirmar) => {
                if(confirmar) {
    
                    preloader('on');
                    $.post(updateAsignado, { Id: id, _token: TOKEN, IdProfesional: check, fecha: 1, Para: who})
                        .done(function(){
                            preloader('off');
                            toastr.success('Se ha actualizado la información de manera correcta');
                            setTimeout(() => {
                                location.reload();             
                            }, 3000); 
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
    } 
    

    async function asignar(e, tipo){

        if (tipo === 'efector') {

            let resultado = await (e === '0' || e === null || e === '');

            if (resultado) {

                $('.ex-asignar').show();
                $('#ex-informadores').prop('disabled', true);
                $('.ex-adjuntarInformador').hide();
            }
        
        } else if (tipo === 'informador') {

            let resultado = await (e === '0' || e === null || e === '') && (efector !== '0');
            
            if (resultado) {
                $('.ex-asignarI').show();
                $('.ex-liberarI').hide();
                $('.ex-abrir').show();
            }
        }
    }

    async function liberar(val, e, tipo){
        
        if(tipo === 'efector') {

            let resultado = await (!['',null,'0'].includes(e) && valCerrar.includes(val));
            
            if (resultado) {
                $('.ex-liberar').show();
                $('.ex-asignarI').hide();
                $('.ex-adjuntarEfector').show();
            } 
        }else if(tipo === 'informador') {

            let resultado = await (!['',null,'0'].includes(e) && valCerrarI !== val);
        
            if (resultado) {
                $('.ex-liberarI').show();
                $('.ex-asignarI').hide();
                $('.ex-adjuntarInformador').show();
            }  

        }
    }

    async function abrir(val){
        let resultado = await (valAbrir.includes(val));
        
        if (resultado) {

            $('.ex-abrir').show();
            $('#ex-informadores').prop('disabled', false);
        
        } else {

            $('.ex-abrir').hide();  
        }
    }

    async function cerrar(val, e, tipo){  

        if (tipo === 'efector') {

            let resultado = await (valCerrar.includes(val) && e !== '0');
            
            if(resultado){
                $('.ex-cerrar').show();
                $('#ex-informadores').prop('disabled', true);

            }
        } else if (tipo === 'informador') {

            let resultado = await (efector !== '0' && informador !== '0') && (CInfo !== '3'),
                final = await (efector !== '0' && informador !== '0') && (Estado === 'Cerrado' && EstadoI === 'Cerrado');

            if(resultado){
   
                $('.ex-cerrarI').show();
                $('.ex-abrir').hide();
                $('.ex-adjuntarInformador').show();

            }else if(final){

                $('.ex-cerrarI').hide();
                $('.ex-abrir').hide();
                $('.ex-liberarI').hide();
                $('.ex-adjuntarInformador').hide();
            }
        }
    }

    async function optionsGeneral(id, tipo) {
        
        let etiqueta, valor;

        if (tipo === 'efector') {
            etiqueta = $('#ex-efectores');
            valor = etiqueta.val();

        } else if (tipo === 'informador') {
            etiqueta = $('#ex-informadores');
            valor = etiqueta.val();
        }

        if (valor === '0') {

            $.get(await listGeneral, { proveedor: id, tipo: tipo })
                .done(function (response) {
                    let data = response.resultados;

                    etiqueta.empty().append('<option value="">Elija una opción</option>');

                    $.each(data, function (index, d) {
                        let contenido = `<option value="${d.Id}">${d.NombreCompleto}</option>`;
                        etiqueta.append(contenido);
                    });
                });
        }
    }

    async function listadoE(id){

        $('#listaefectores').empty();

        preloader('on');
        $.get(await paginacionGeneral, {Id: id, tipo: 'efector'})
            .done(function(response){
                preloader('off');
                let data = response.resultado;
   
                $.each(data, function(index, d){

                    let contenido = `
                        <tr>
                            <td>${d.Nombre}</td>
                            <td>${(d.DescripcionE !== null && d.DescripcionE !== undefined  && d.DescripcionE !== '' ? d.DescripcionE : ' ')}</td>
                            <td>${(d.Adjunto === 0 ? 'Físico' : 'Digital')}</td>
                            <td>${(d.MultiE === 0 ? 'Simple' : 'Multi')}</td>
                            <td>
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <div class="edit">
                                        <a href="${descargaE}/${d.RutaE}" target="_blank">
                                            <button type="button" class="btn btn-sm iconGeneral" title="Ver"><i class="ri-search-eye-line"></i></button>
                                        </a>
                                    </div>
                                    <div class="download">
                                        <a href="${descargaE}/${d.RutaE}" target="_blank" download>
                                            <button type="button" class="btn btn-sm iconGeneral" title="Descargar"><i class="ri-download-2-line"></i></button>
                                        </a>
                                    </div>
                                    ${(Estado === 'Cerrado') ? `
                                    <div class="replace">
                                        <button data-id="${d.IdE}" data-tipo="efector" class="btn btn-sm iconGeneral replaceAdjunto" data-bs-toggle="modal" data-bs-target="#replaceAdjunto" title="Reemplazar archivo">
                                            <i class="ri-file-edit-line"></i>
                                        </button>
                                    </div>
                                    ` : ``}
                                    ${(Estado === 'Cerrado') || (d.Anulado === 1) ? `
                                    <div class="remove">
                                        <button data-id="${d.IdE}" data-tipo="efector" class="btn btn-sm iconGeneral deleteAdjunto" title="Eliminar">
                                            <i class="ri-delete-bin-2-line"></i>
                                        </button>
                                    </div>
                                    ` : ``}
                                    
                                </div>
                            </td>
                        </tr>
                    `;

                    $('#listaefectores').append(contenido);
                });
            })
    }

    async function listadoI(id){

        $('#listainformadores').empty();

        preloader('on');
        $.get(await paginacionGeneral, {Id: id, tipo: 'informador'})
            .done(function(response){
                preloader('off');
                let data = response.resultado;

                $.each(data, function(index, d){

                    let contenido = `
                        <tr>
                            <td>${d.Nombre}</td>
                            <td>${(d.DescripcionI !== null && d.DescripcionI !== undefined && d.DescripcionI !== '' ? d.DescripcionI : '')}</td>
                            <td>
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <div class="edit">
                                        <a href="${descargaI}/${d.RutaI}" target="_blank">
                                            <button type="button" class="btn btn-sm iconGeneral" title="Ver"><i class="ri-search-eye-line"></i></button>
                                        </a>
                                    </div>
                                    <div class="download">
                                        <a href="${descargaI}/${d.RutaI}" target="_blank" download>
                                            <button type="button" class="btn btn-sm iconGeneral" title="Descargar"><i class="ri-download-2-line"></i></button>
                                        </a>
                                    </div>
                                    ${(EstadoI === 'Cerrado') ? `
                                    <div class="replace">
                                        <button data-id="${d.IdI}" data-tipo="informador" data-bs-toggle="modal" data-bs-target="#replaceAdjunto" class="btn btn-sm iconGeneral replaceAdjunto" title="Reemplazar archivo">
                                            <i class="ri-file-edit-line"></i>
                                        </button> 
                                    </div>
                                        `:``}
                                    ${(EstadoI === 'Cerrado') ? `
                                    <div class="remove">
                                        <button data-id="${d.IdI}" data-tipo="informador" class="btn btn-sm iconGeneral deleteAdjunto" title="Eliminar">
                                            <i class="ri-delete-bin-2-line"></i>
                                        </button>
                                    </div>
                                    ` : ''}
                                </div>
                            </td>
                        </tr>
                    `;

                    $('#listainformadores').append(contenido);
                });
            })
    }

});