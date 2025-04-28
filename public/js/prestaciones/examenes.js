$(function(){

    let idExamen = [];

    const valAbrir = [3, 4, 5], 
          valCerrar = [0, 1, 2], 
          valCerrarI = 3;

    cargarExamen();
    contadorExamenes(ID);

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

    $(document).off('click', '.adPaquete').on('click', '.addPaquete', function(e){
        e.preventDefault();
        
        let paquete = $('#paquetes').val();
        
        if([null, undefined, ''].includes(paquete)){
            toastr.warning("Debe seleccionar un paquete para poder añadirlo en su totalidad",'',{timeOut: 1000});
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

        let ids = [], id = $(this).data('delete'), checkAll ='';

        if ($(this).hasClass('deleteExamenes')) {

            $('input[name="Id_examenes"]:checked').each(function() {
               ids.push($(this).val());
            });
    
            checkAll = $('#checkAllExamenes').prop('checked');

        } else if($(this).hasClass('deleteExamen')) {

            ids.push(id);
        }

        if(ids.length === 0 && checkAll === false){
            toastr.warning('No hay examenes seleccionados','',{timeOut: 1000});
            return;
        }  
    
        swal({
            title: "Confirme la eliminación de los examenes",
            icon: "warning",
            buttons: ["Cancelar", "Eliminar"],
        }).then((confirmar) => {
            if (confirmar){

                preloader('on');
                $.post(deleteItemExamen, {Id: ids,  _token: TOKEN})
                    .done(function(response){
                        preloader('off');
                        for (let i = 0; i < response.length; i++) {
                            let data = response[i];
                            toastr[data.status](data.msg, "", { timeOut: 1000 });

                        }

                        principal.listaExamenes.empty();
                        variables.exam.val([]).trigger('change.select2');
                        variables.paquetes.val([]).trigger('change.select2');
                        cargarExamen();
                        contadorExamenes(IdNueva);
                        tablasExamenes(variables.selectClientes.val(), true, '#lstEx');
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
            toastr.warning('No hay examenes seleccionados','',{timeOut: 1000});
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
                        for(let index = 0; index < response.length; index++){
                            let msg = response[index],
                                tipoRespuesta = {
                                    success: 'success',
                                    fail: 'info'
                                }
                           
                            toastr[tipoRespuesta[msg.estado]](msg.message, "Atención", { timeOut: 10000 });
                            estados.push(msg.estado);
                        }
                        
                        if(estados.includes('success')) {
                            $('#listaExamenes').empty();
                            $('#exam').val([]).trigger('change.select2');
                            $('#addPaquete').val([]).trigger('change.select2');
                            cargarExamen();
                            contadorExamenes(ID);
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
            toastr.warning('No hay examenes seleccionados','',{timeOut: 1000});
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

                        for(let index=0; index < response.length; index++){
                            let msg = response[index],
                                tipoRespuesta = {
                                    success: 'success',
                                    fail: 'info'
                                }
                           
                            toastr[tipoRespuesta[msg.estado]](msg.message, "Atención", { timeOut: 10000 });
                            estados.push(msg.estado);
                        }

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
            toastr.warning('No hay examenes seleccionados','',{timeOut: 1000});
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

                        for(let index = 0; index < response.length; index++){
                            let msg = response[index],
                                tipoRespuesta = {
                                    success: 'success',
                                    fail: 'info'
                                }

                                toastr[tipoRespuesta[msg.estado]](msg.message, "Atención", { timeOut: 10000 });
                                estados.push(msg.estado);
                        }

                        if(estados.includes('success')) {
                            $('#listaExamenes').empty();
                            $('#exam').val([]).trigger('change.select2');
                            $('#addPaquete').val([]).trigger('change.select2');
                            cargarExamen();
                            contadorExamenes(ID);
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
            toastr.warning('No hay examenes seleccionados','',{timeOut: 1000});
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
                            contadorExamenes(ID);
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
            toastr.warning("Debe seleccionar un examen para poder añadirlo a la lista",'',{timeOut: 1000});
            return;
        }
        saveExamen(id);
    });

    $(window).on('popstate', function() {
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
        contadorExamenes(ID);
    });

    
    function saveExamen(id){

        idExamen = [];
        if (Array.isArray(id)) {
            for(let index = 0; index < id.length; index++) {
                idExamen.push(id[index]);
            }
        }else{
            idExamen.push(id);
        }

        if (idExamen.length === 0) {
            toastr.warning("No existe el exámen o el paquete no contiene examenes",'',{timeOut: 1000});
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
                contadorExamenes(ID);
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
                toastr.success(response.msg,'',{timeOut: 1000});

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
            let examenes = result;
    
            if (examenes.length > 0) {
                preloader('on');
                const response = await $.ajax({
                    url: getItemExamenes,
                    type: 'GET',
                    data: {
                        IdExamen: examenes,
                        Id: ID,
                        tipo: 'listado'
                    },
                });

                const cargaEfector = await primeraCarga(ID, "efector");
                const cargaInformador = await primeraCarga(ID, "informador");
                
                let registros = response;
                checkExamenes(ID);
                contadorExamenes(ID);
                
                let filas = '';

                preloader('off');
                for (let i = 0; i < registros.length; i++) {
                    const examen = registros[i];

                    let titleEfector = examen.RegHis === 1 
                        ? ![undefined,null,0].includes(examen.EfectorFullName) && (examen.EfectorFullName).length > 0 
                            ? examen.EfectorFullName 
                            : (examen.IdEfector  === 0 ? '' : examen.DatosEfectorFullName)
                        : ![undefined,null,0].includes(examen.DatosEfectorFullName) && (examen.DatosEfectorFullName).length > 0 
                            ? (examen.IdEfector === 0 ? '' : examen.DatosEfectorFullName)
                            : '',
                        
                        fullNameEfector = examen.RegHis === 1 
                            ? ![undefined,null,0].includes(examen.EfectorApellido) && (examen.EfectorApellido).length > 0 
                                ? examen.EfectorApellido 
                                : (examen.IdEfector === 0 ? '' : examen.DatosEfectorApellido)
                            : ![undefined,null,0].includes(examen.DatosEfectorApellido) && (examen.DatosEfectorApellido).length > 0 
                                ? (examen.IdEfector === 0 ? '' : examen.DatosEfectorApellido)
                                : '';

                    let titleInformador = examen.Informe === 1 
                        ? examen.RegHis === 1 
                            ? ![undefined,null,0].includes(examen.InformadorFullName) && (examen.InformadorFullName).length > 0 
                                ? examen.InformadorFullName
                                : (examen.IdInformador === 0 ? '' : examen.DatosInformadorFullName)
                            : ![undefined,null,0].includes(examen.DatosInformadorFullName) && (examen.DatosInformadorFullName).length > 0 ? (examen.IdInformador === 0 ? '' : examen.DatosInformadorFullName) : ''
                        : '',

                        fullNameInformador = examen.Informe === 1
                        ? examen.RegHis === 1
                            ? ![undefined,null,0].includes(examen.InformadorApellido) && (examen.InformadorApellido).length > 0 
                                ? examen.InformadorApellido
                                : (examen.IdInformador === 0 ? '' : examen.DatosInformadorApellido)
                            : ![undefined,null,0].includes(examen.DatosInformadorApellido) && (examen.DatosInformadorApellido).length > 0 ? (examen.IdInformador === 0 ?  '' : examen.DatosInformadorApellido) : ''
                        : '';

                    filas += `
                        <tr ${examen.Anulado === 1 ? 'class="filaBaja"' : ''}>
                            <td><input type="checkbox" name="Id_examenes" value="${examen.IdItem}" checked></td>
                            <td data-idexam="${examen.IdExamen}" id="${examen.IdItem}" style="text-align:left">${examen.Nombre} ${examen.Anulado === 1 ? '<span class="custom-badge rojo">Bloqueado</span>' : ''} ${cargaEfector.includes(examen.IdItem) ? '<i title="Carga multiple, efector, desde este exámen" class="ri-file-mark-line verde"></i>' : ''} ${cargaInformador.includes(examen.IdItem) ? '<i title="Carga multiple, informador, desde este exámen" class="ri-file-mark-line naranja"></i>' : ''}</td>
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
                            <td class="date text-center capitalize" title="${titleEfector}">${fullNameEfector}
                                <span class="badge badge-soft-${([0,1,2].includes(examen.CAdj) ? 'danger': ([3,4,5].includes(examen.CAdj) ? 'success' : ''))}">
                                    ${([0,1,2].includes(examen.CAdj) ? 'Abierto': ([3,4,5].includes(examen.CAdj) ? 'Cerrado' : ''))}
                                </span>
                                ${examen.ExaAdj === 1 ? `<i class="ri-attachment-line ${examen.archivos > 0 ? 'verde' : 'gris'}"></i>`: ``}    
                            </td>
                            <td class="date text-center capitalize" title="${titleInformador}">${fullNameInformador}
                                <span class="badge badge-soft-${(examen.CInfo === 0 ? 'dark' :(examen.CInfo === 3 ? 'success' : ([1,2].includes(examen.CInfo)) ? 'danger' : ''))}">${(examen.CInfo === 0 || examen.InfAdj === 0 ? '' : (examen.CInfo === 3 ? 'Cerrado' : (examen.CInfo == 2 ? 'Borrador' : (examen.CInfo === 1 ? 'Pendiente': ''))))}</span>
                                ${examen.InfAdj === 1 ? (examen.CInfo !== 0 ? `<i class="ri-attachment-line ${examen.archivosI > 0 ? 'verde' : 'gris'}"></i>`: ``) : ''}   
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
                    } else {
                        console.warn('No hay registros disponibles');
                    }
                } catch (error) {
            preloader('off');
            console.error('Error en la carga del examen:', error);
        }
    }
    
    

    async function checkExamenes(id) {

        $.get(await buscarEx, {Id: id}, function(response){

            response === 0 
                ? $('.auditoria, .autorizados, .evaluacion, .banderas').hide()
                : $('.auditoria, .autorizados, .evaluacion, .banderas').show()  
        })
    }


    function loadModalExamen(id) {
        preloader('on');
        $.get(editModal, {Id: id})
            .done(function(response){

                const estadoAbierto = [0, 1, 2], 
                      estadoCerrado = [3, 4, 5], 
                      itemprestaciones = response.itemprestacion, 
                      pacientes = response.paciente.paciente,
                      examenes = itemprestaciones.examenes,
                      factura = itemprestaciones.facturadeventa,
                      notaCreditoEx = itemprestaciones?.notaCreditoIt?.notaCredito;

                const eventos = [
                    eventoAbrir,
                    eventoCerrar,
                    eventoAsignar,
                    eventoLiberar,
                    eventoActualizar
                ];

                const reactivarEstilos = [
                    '#ex-Fecha', 
                    '#ex-ObsExamen', 
                    '#ex-efectores', 
                    '#ex-informadores'
                ];

                preloader('off');
                let paciente = pacientes.Nombre + ' ' + pacientes.Apellido,
                    anulado = itemprestaciones.Anulado === 1 ? '<span class="custom-badge rojo">Bloqueado</span>' : '',
                    estado = estadoAbierto.includes(itemprestaciones.CAdj) ? 'Abierto' : estadoCerrado.includes(itemprestaciones.CAdj) ? 'Cerrado' : '';
                    estadoColor = estadoAbierto.includes(itemprestaciones.CAdj) ? {'color': 'red'} : estadoCerrado.includes(itemprestaciones.CAdj) ? {'color' : 'green'} : '{}',
                    colorAdjEfector = examenes.Adjunto === 1 && response.adjuntoEfector === 0 ? {"color" : 'red'} : examenes.Adjunto === 1 && response.adjuntoEfector === 1 ? {"color" : "green"} : '{}',
                    estadoColorI = [0,1,2].includes(itemprestaciones.CInfo) ? {"color": "red"} : itemprestaciones.CInfo === 3 ? {"color": "green"} : '{}',
                    estadoI = itemprestaciones.CInfo === 1 ? 'Pendiente' : itemprestaciones.CInfo === 2 ? 'Borrador' : itemprestaciones.CInfo === 3 ? 'Cerrado' : '',
                    colorAdjInformador = itemprestaciones.profesionales2.InfAdj === 1 && response.adjuntoInformador === 0 ? {"color": "red"} : itemprestaciones.profesionales2.InfAdj === 1 && response.adjuntoInformador === 1 ? {"color": "green"} : '{}',
                    tipo = factura.Tipo || '',
                    sucursal = factura.Sucursal || '',
                    nroFactura = factura.NroFactura || '',
                    tipoNc = notaCreditoEx?.Tipo || '',
                    sucursalNc = notaCreditoEx?.Sucursal || '',
                    nroNc = notaCreditoEx?.Nro || '',
                    notaCEx = tipoNc + sucursalNc + nroNc;
                
                $('.ex-abrir').hide();
                
                $('#ex-qr').empty().text(response.qrTexto);
                $('#ex-paciente').empty().text(paciente);
                $('#ex-anulado').empty().html(anulado);
                $('#ex-identificacion').val(itemprestaciones.Id || '');
                $('#ex-prestacion').val(itemprestaciones.IdPrestacion || '');
                $('#ex-idExamen').val(itemprestaciones.IdExamen || '')
                $('#ex-prestacionTitulo').empty().text(itemprestaciones.IdPrestacion || '');
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
                $('#ex-Estado').val(adjuntosEfector(examenes, response));
                $('#ex-Estado').empty().css(colorAdjEfector || '');

                itemprestaciones.CInfo !== 0 ? $('.visualizarInformador').show() : $('.visualizarInformador').hide();

                $('#ex-EstadoI').val(estadoI)
                $('#ex-EstadoI').empty().css(estadoColorI);
                $('#ex-FechaPagado2').val(itemprestaciones.FechaPagado2 || '');
                
                itemprestaciones.Anulado === 1 ? $('#ex-asignarI, #ex-abrirI, #ex-cerrarI').hide() : $('#ex-asignar, #ex-abrir, #ex-cerrar').show();

                $('#ex-CInfo').val(itemprestaciones.CInfo || '');

                $('#ex-EstadoInf').val(adjuntosInformador(itemprestaciones, response));
                $('#ex-EstadoInf').empty().css(colorAdjInformador);
                $('#ex-Obs').val(stripTags(itemprestaciones?.itemsInfo?.Obs));

                checkearFacturas(); //Verifica si es Examen a cuenta o Factura de Venta

                $('#ex-FechaNC').val(notaCreditoEx?.Fecha);
                $('#ex-NumeroNC').val(notaCEx);

                $('#ex-efectores').empty().append('<option selected value="' + response.efectores.id + '">' + response.efectores.NombreCompleto + '</option>');
          
                $('#ex-informadores').empty().append('<option selected value="' + response.informadores.id + '">' + response.informadores.NombreCompleto + '</option>');

                let efector = $('#ex-efectores').val(), informador = $('#ex-informadores').val();
                
                asignarModal(itemprestaciones.IdProfesional, 'efector');
                asignarModal(itemprestaciones.IdProfesional2, 'informador');
                liberarModal(itemprestaciones.CAdj, efector, 'efector');
                liberarModal(itemprestaciones.CInfo, informador, 'informador');
                abrirModal(itemprestaciones.CAdj);
                cerrarModal(itemprestaciones.CAdj, efector, 'efector', itemprestaciones.Id);
                cerrarModal(itemprestaciones.CInfo, informador, 'informador', itemprestaciones.Id);
                optionsGeneralModal(examenes.IdProveedor, 'efector').then(response => {
                    return response;
                });
                optionsGeneralModal(examenes.IdProveedor2, 'informador').then(response => {
                    return response;
                });
                listadoEModal(itemprestaciones.Id);
                listadoIModal(itemprestaciones.Id);

                

                if (itemprestaciones.CInfo === 3 && adjuntosInformador(itemprestaciones, response) === 'Pendiente' && itemprestaciones.Anulado === 0) {
                    borrarCache();
                    $('.ex-adjuntarInformador').show();
                }

                ocultarCampos();
                $.each(eventos, function(index, evento) {
                    evento(itemprestaciones.Id);
                });

                eventoAdjuntar(itemprestaciones.Id, "editExamen");

                // pasamos el id de la prestacion, multi efector y multi informador para contemplar la eliminación multiple de los 2 casos
                eventoEliminar(itemprestaciones.Id, itemprestaciones.examenes.proveedor1.Multi, itemprestaciones.examenes.proveedor2.MultiE);

                multiExEfector(itemprestaciones); //Activa el multiefector en el listado de Archivos
                listaExEfector(response.multiEfector); // Listado de los examenes MultiEfector
                
                $('#ex-multi').val(itemprestaciones.examenes.proveedor1.Multi === 1 ? 'success' : 'fail');

                multiExInformador(itemprestaciones); //Activa el multiinformador en el listado de Archivos
                listaExInformador(response.multiInformador); // Listado de los examenes MultiInformador

                $('#ex-multiE').val(itemprestaciones.examenes.proveedor2.MultiE == 1 && itemprestaciones.profesionales2.InfAdj === 1 ? 'success' : 'fail');

                

                checkBloq(itemprestaciones.Anulado, reactivarEstilos);
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;  
            });
    }

    /*******************************Modal Examen ************************************/
    function eventoAbrir(id) {
        $(document).off('click', '.ex-abrir').on('click', '.ex-abrir', function(e){

            e.preventDefault();
            let lista = {3: 0, 4: 1, 5: 2}, cadj = $('#ex-CAdj').val();

            if(parseInt(cadj, 10) in lista){
                preloader('on');
                $.post(updateItem, {Id : id, _token: TOKEN, CAdj: lista[cadj], Para: 'abrir' })
                    .done(function(response){
                        preloader('off');
                        toastr.success('Se ha realizado la acción correctamente','',{timeOut: 1000});
                        loadModalExamen(response.data.Id);
                        
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
        $(document).off('click', '#ex-cerrar, #ex-cerrarI').on('click', '#ex-cerrar, #ex-cerrarI', function(e){
            e.preventDefault();
            let who = $(this).hasClass('ex-cerrar') ? 'cerrar' : 'cerrarI',
                listaE = {0: 3, 2: 5, 1: 4},
                listaI = ["0", "1", "2", "3"],
                cadj = $('#ex-CAdj').val(), 
                CInfo = $('#ex-CInfo').val();

            if(who === 'cerrar' && parseInt(cadj, 10) in listaE) {
                
                swal({
                    title: "¿Esta seguro que desea cerrar el exámen efector?",
                    icon: "warning",
                    buttons: ["Cancelar", "Aceptar"]
                }).then((confirmar) => {
                    if(confirmar) {
                        preloader('on');
                        $.post(updateItem, {Id : id, _token: TOKEN, CAdj: listaE[cadj], Para: who })
                            .done(function(response){
                                preloader('off');
                                toastr.success('Se ha cerrado al efector correctamente','',{timeOut: 1000});
                                loadModalExamen(response.data.Id);
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
            
            if(who === 'cerrarI' && listaI.includes(CInfo)) {
                swal({
                    title: "¿Esta seguro que desea cerrar el examen informador?",
                    icon: "warning",
                    buttons: ["Cancelar", "Aceptar"]
                }).then((confirmar) => {
                    if(confirmar) {
                        preloader('on');
                        $.post(updateItem, {Id : id, _token: TOKEN, CInfo: 3, Para: who })
                            .done(function(response){
                                preloader('off');
                                let query = response.data;

                                toastr.success('Se ha cerrado al informador correctamente','',{timeOut: 1000});
                                loadModalExamen(query.Id);
                                
                            })
                            .fail(function(jqXHR){
                                preloader('off');
                                let errorData = JSON.parse(jqXHR.responseText);            W
                                checkError(jqXHR.status, errorData.msg);
                                return;
                            });
                    }
                });
            }
        });
    }

    function eventoAsignar(id) {
        $(document).on('click', '#ex-asignar, #ex-asignarI', function(e) {
            e.preventDefault();

            let who = $(this).hasClass('ex-asignar') ? 'asignar' : 'asignarI',
                check = (who === 'asignar') ? $('#ex-efectores').val() : $('#ex-informadores').val();
    
            if(['', null, 0, '0'].includes(check)) {
                toastr.warning("Debe seleccionar un Efector/Informador para poder asignar uno",'',{timeOut: 1000});
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
                        .done(function(response){
                            preloader('off');
                            const itemprestaciones = response.data;
                            toastr.success(response.msg,'',{timeOut: 1000});

                            if (who === 'asignar') {

                                preloader('on');
                                loadModalExamen(itemprestaciones.Id);
                                preloader('off');


                            }else if(who === 'asignarI') {

                                preloader('on');
                                loadModalExamen(itemprestaciones.Id);
                                preloader('off');
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
    }

    function eventoLiberar(id) {
        $(document).on('click', '#ex-liberar, #ex-liberarI', function(e) {
            e.preventDefault();
    
            let checkEmptyE = $('#ex-efectores').val(), checkEmptyI = $('#ex-informadores').val(), who = $(this).attr('id') === 'ex-liberar' ? checkEmptyE : checkEmptyI;
            
            if (who != '0') {
    
                swal({
                    title: "¿Esta seguro que desea liberar el exámen?",
                    icon: "warning",
                    buttons: ["Cancelar", "Aceptar"]
                }).then((confirmar) => {
                    if (confirmar) {
                        
                        preloader('on');
                        $.post(updateAsignado, { Id: id, _token: TOKEN, IdProfesional: 0, fecha: 0, Para: $(this).attr('id') === 'ex-liberar' ? 'asignar' : 'asignarI'})
                        .done(function(response){
                            preloader('off');
                            toastr.success(response.msg,'',{timeOut: 1000});
                            loadModalExamen(response.data.Id);
                            
                        })
                        .fail(function(jqXHR){
                            preloader('off');
                            let errorData = JSON.parse(jqXHR.responseText);            
                            checkError(jqXHR.status, errorData.msg);
                            return;
                        });
                    }
                })   
            }
        });
    }

    function eventoAdjuntar(id) {
        $(document).off('click', '.ex-btnAdjEfector, .ex-btnAdjInformador').on('click', '.ex-btnAdjEfector, .ex-btnAdjInformador', function (e){
            e.preventDefault();
            
            let who = $(this).hasClass('ex-btnAdjEfector') ? 'efector' : 'informador';

            let obj = {
                efector: ['input[name="fileEfector"]', '[id^="Id_multiAdj_"]:checked', '#DescripcionE', '#ex-efectores'],
                informador: ['input[name="fileInformador"]', '[id^="Id_multiAdjInf_"]:checked', '#DescripcionI', '#ex-informadores']
            }
            
            let archivo = $(obj[who][0])[0].files[0];
    
            let multi = who === 'efector' ? $('#ex-multi').val() : $('#ex-multiE').val(), ids = [], anexoProfesional = $(obj[who][3]).val();
    
            $(obj[who][1]).each(function() {
                ids.push($(this).val());
            });

            if(ids.length === 0 && multi == "success"){
                toastr.warning('No hay examenes seleccionados','',{timeOut: 1000});
                return;
            }
            
            let descripcion = $(obj[who][2]).val(),
                identificacion = (multi == 'success') ? ids : $('#ex-identificacion').val(),
                prestacion = $('#ex-prestacion').val();
            
            who = multi === 'success' && who === 'efector'
                    ? 'multiefector'
                    : (multi === 'success' && who === 'informador'
                        ? 'multiInformador'
                        : who);

            if(verificarArchivo(archivo)){
                preloader('on');
                let formData = new FormData();
                formData.append('archivo', archivo);
                formData.append('Id', id);
                formData.append('Descripcion', descripcion);
                formData.append('IdEntidad', identificacion);
                formData.append('IdPrestacion', prestacion);
                formData.append('who', who);
                formData.append('anexoProfesional', anexoProfesional);
                formData.append('multi', multi);
                formData.append('_token', TOKEN);
       
                $.ajax({
                    type: 'POST',
                    url: fileUpload,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function() {
                        
                        borrarCache();
                        ocultarCampos();
                        $('#modalEfector, #modalInformador').removeClass('show');
                        $('.fileA').val('');
                        loadModalExamen(id);
                        preloader('off');
                        toastr.success("Se ha cargado el reporte de manera correcta.");
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
    
    }

    function eventoEliminar(idItem, multiE, multiI) {
        $(document).on('click', '.deleteAdjunto', function(e){
            e.preventDefault();
            let id = $(this).data('id'), 
                tipo = $(this).data('tipo'),
                idExOriginal = $('#ex-identificacion').val();
    
            if(id === '' || tipo === ''){
                toastr.warning("Hay un problema porque no podemos identificar el tipo o la id a eliminar");
                return;
            }
            
            swal({
                title: "¿Está seguro que desea eliminar?",
                icon: "warning",
                buttons: {
                    cancel: {
                        text: "Cancelar",
                        value: null,
                        visible: true,
                        className: "",
                        closeModal: true
                    },
                    aceptar: {
                        text: "Eliminar Único",
                        value: true,
                        visible: true,
                        className: "",
                        closeModal: true
                    },
                    custom: {
                        text: "Eliminar Multi",
                        value: "multiple",
                        visible: tipo === 'efector' ? multiE === 1 : tipo === 'informador' ? multiI === 1 : false, // solo visible si multi es 1
                        className: "fondo-rojo",
                        closeModal: true
                    }
                }
            }).then((confirmar) =>{

                if (confirmar === null || (typeof confirmar === 'object' && Object.keys(confirmar).length === 0)) {
                    return; 
                }

                preloader('on');

                $.get(deleteIdAdjunto, {Id: id, Tipo: tipo, ItemP: idItem, multi: confirmar === 'multiple'})
                    .done(function(response){
                        borrarCache();
                        loadModalExamen(idExOriginal);
                        preloader('off');
                        toastr.success(response.msg);  
                    })
                    .fail(function(jqXHR){
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return;
                    })
            });
        });
    }

    function eventoActualizar(id) {
        $(document).off('click', '#actExamenModal').on('click', '#actExamenModal', function(e){
            e.preventDefault();
    
            let ObsExamen = $('#ex-ObsExamen').val(), Profesionales2 = $('#ex-informadores').val(), Obs = $('#ex-Obs').val(), Fecha = $('#ex-Fecha').val();
            
            preloader('on');
            $.post(updateItemExamen, {Id: id, _token: TOKEN, ObsExamen: ObsExamen, Profesionales2: Profesionales2, Obs: Obs, Fecha: Fecha})
                .done(function(response) {
    
                    borrarCache();
                    loadModalExamen(response.data.Id);
                    preloader('off');
                    toastr.success(response.msg);
                })
                .fail(function(jqXHR) {
                    preloader('off');
                    let errorData = JSON.parse(jqXHR.responseText);            
                    checkError(jqXHR.status, errorData.msg);
                    return;
                });
        });
    }

    $(document).on('click', '.replaceAdjunto', function() {

        let replaceId = $(this).data('id');
        let replaceTipo = $(this).data('tipo');
        
        $('#replaceId').val(replaceId);
        $('#replaceTipo').val(replaceTipo);
        
    });

    $(document).on('click', '.btnReplaceAdj', function() {

        let archivo = $('input[name="fileReplace"]')[0].files[0], 
            replace_Id = $('input[name="replaceId"]').val(), 
            replace_Tipo = $('input[name="replaceTipo"]').val();

        if (verificarArchivo(archivo)) {

            let formData = new FormData();
            formData.append('archivo', archivo);
            formData.append('Id', replace_Id);
            formData.append('who', replace_Tipo);
            formData.append('_token', TOKEN);
            preloader('on');
            $.ajax({
                type: 'POST',
                url: replaceIdAdjunto,
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    borrarCache();
                    listadoEModal(response.data.Id);
                    listadoIModal(response.data.Id);
                    $('#replaceAdjunto').removeClass('show');
                    loadModalExamen(response.data.Id);
                    preloader('off');
                    toastr.success(response.msg);

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
  
    async function asignarModal(e, tipo){

        let efector = $('#ex-efectores').val();

        if (tipo === 'efector') {
            
            let resultado = await [0, null, '', undefined].includes(parseInt(e, 10));
            
            if (resultado) {
                await borrarCache();
                $('.ex-asignar').show();
                $('#ex-informadores').prop('disabled', true);  
            }
        
        } else if (tipo === 'informador') {

            let resultado = await ([0, null, '', undefined].includes(parseInt(e, 10))) && (parseInt(efector, 10) !== 0);

            if (resultado) {
                
                await borrarCache();
                $('.ex-asignarI').show();
            }
        }
    }

    async function liberarModal(val, e, tipo){

        let number = parseInt(e, 10);

        if(tipo === 'efector') {
                                    
            let resultado = await (![null,undefined,'',0].includes(number) && valCerrar.includes(parseInt(val, 10)));

            if (resultado) {
                await borrarCache();
                $('.ex-liberar, .ex-cerrar').show();    
            }

        }else if(tipo === 'informador') {

            let resultado = await (![null,undefined,'',0].includes(number) && valCerrarI !== parseInt(val, 10));

            if (resultado) {
                await borrarCache();
                $('.ex-liberarI, .ex-adjuntarInformador').show();
                $('.ex-asignarI').hide();
            }  

        }
    }

    async function abrirModal(val){

        let informador = $('#ex-informadores').val();
        let resultado = await (valAbrir.includes(parseInt(val, 10)) && parseInt(informador,10) !== 3 || parseInt(informador,10) !== 0);

        if (resultado) {

            await borrarCache();
            $('.ex-abrir').show();
            $('#ex-informadores').prop('disabled', false);
        
        } else {

            await borrarCache();
            $('.ex-abrir').hide();  
        }
    }

    async function cerrarModal(val, e, tipo, idItemprestacion){ 

        let efector = parseInt($('#ex-efectores').val(), 10),
            informador = parseInt($('#ex-informadores').val(),10),
            CInfo = parseInt($('#ex-CInfo').val(), 10),
            Estado = $('#ex-EstadoEx').val(),
            EstadoI = $('#ex-EstadoI').val(),
            AdjEfector = $('#ex-Estado').val(),
            AdjInformador = $('#ex-EstadoInf').val(),
            num = parseInt(e, 10);
        
        if (tipo === 'efector') {
            let valido = ![null,undefined,'',0].includes(num);
            let resultado = await (valCerrar.includes(parseInt(val, 10)) && valido);

            if(resultado){
                await borrarCache();
                $('.ex-liberar, .ex-cerrar').show();   
            
                checkAdjunto(idItemprestacion, 'efector').then(response => {
                    if (response === true) {
                        $('.ex-adjuntarEfector').show();
                    } else {
                        $('.ex-adjuntarEfector').hide();
                    }
                });
            }
        } else if (tipo === 'informador') {

            let resultado = await (efector !== 0 && informador !== 0) && (CInfo !== 3),
                errorEfector = await (efector !== 0) && (AdjEfector === 'Pendiente'),
                errorInformador = await (informador !== 0) && (AdjInformador === 'Pendiente'),
                final = await (efector !== 0 && informador !== 0) && (Estado === 'Cerrado' && EstadoI === 'Cerrado');

            if(resultado){
   
                await borrarCache();
                $('.ex-cerrarI').show();
                $('.ex-abrir').hide();
                $('.ex-adjuntarInformador').show();

                checkAdjunto(idItemprestacion, 'informador').then(response => {
                    response === true ? $('.ex-adjuntarInformador').show() : $('.ex-adjuntarInformador').hide();
                });

            }else if(errorEfector) {
                await borrarCache();
                $('.ex-adjuntarEfector').show();
                
            }else if(errorInformador) {
                await borrarCache();
                $('.ex-adjuntarInformador').show();

            }else if(final){

                await borrarCache();
                $('.ex-cerrarI, .ex-abrir, .ex-liberarI, .ex-adjuntarInformador').hide();

                if (CInfo === 3 && EstadoI === 'Pendiente' && anulado === 1) {
                    $('.ex-adjuntarInformador').show();
                }
            }
        }
    }

    async function optionsGeneralModal(id, tipo) {
        
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

                    for(let i = 0; i < data.length; i++) {
                        let d = data[i],
                            contenido = `<option value="${d.Id}">${d.NombreCompleto}</option>`;
                            etiqueta.append(contenido);
                    }
                });
        }
    }

    async function listadoEModal(id){
        
        $('#listaefectores').empty();
        
        preloader('on');
        $.get(await paginacionGeneral, {Id: id, tipo: 'efector'})
            .done(function(response){
                let data = response.resultado;
                preloader('off');

                for(let index = 0; index < data.length; index++) {
                    let d = data[index],
                        Estado = $('#ex-EstadoEx').val();

                    let contenido = `
                        <tr>
                            <td>${d.Nombre}</td>
                            <td>${(![null, undefined, ''].includes(d.DescripcionE) ? d.DescripcionE : '')}</td>
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
                                        <button data-id="${d.IdE}" data-tipo="efector" class="btn btn-sm iconGeneral replaceAdjunto" data-bs-toggle="offcanvas" data-bs-target="#replaceAdjunto" title="Reemplazar archivo">
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
                }
            })
    }

    async function listadoIModal(id){

        $('#listainformadores').empty();
        let EstadoI = $('#ex-EstadoI').val()
        preloader('on');
        $.get(await paginacionGeneral, {Id: id, tipo: 'informador'})
            .done(function(response){
                let data = response.resultado;
                preloader('off');

                for(let index = 0; index < data.length; index++) {
                    let d = data[index],
                        contenido = `
                    <tr>
                        <td>${d.Nombre}</td>
                        <td>${(![null, undefined, ''].includes(d.DescripcionI) ? d.DescripcionI : '')}</td>
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
                                    <button data-id="${d.IdI}" data-tipo="informador" data-bs-toggle="offcanvas" data-bs-target="#replaceAdjunto" class="btn btn-sm iconGeneral replaceAdjunto" title="Reemplazar archivo">
                                        <i class="ri-file-edit-line"></i>
                                    </button> 
                                </div>
                                    `:``}
                                ${(EstadoI === 'Cerrado' || d.Anulado === 1) ? `
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
                }     
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            });
    }

    function ocultarCampos() {
        const elements = document.querySelectorAll('.ex-abrir, .ex-cerrar, .ex-asignar, .ex-liberar, .ex-asignarI, .ex-liberarI, .ex-cerrarI, .ex-adjuntarEfector, .ex-adjuntarInformador');

        for(let i = 0; i < elements.length; i++) {
            elements[i].style.display = 'none';
        }
    }

    function multiExEfector(data) {
        return data.examenes.proveedor1.Multi === 1 ? $('.multiEf').show() : $('.multiEf').hide();
    }

    function listaExEfector(data) {

        if([null, undefined, ''].includes(data)) return;
        $('.listaGrupoEfector').empty();

        for(let index = 0; index < data.length; index++) {
            let examen = data[index],
                contenido = `
                <label class="list-group-item">
                    <input class="form-check-input me-1" type="checkbox" id="Id_multiAdj_${ examen.Id }" value="${examen.Id}" ${ examen.archivos_count > 0 ? 'disabled' : 'checked' }> 
                    ${ examen.archivos_count > 0 ? examen.NombreExamen + ' <i title="Con archivo adjunto" class="ri-attachment-line verde"></i>' : examen.NombreExamen}
                </label>
            `;
            $('.listaGrupoEfector').append(contenido);
        }
    }

    function multiExInformador(data) {
        return data.examenes.proveedor2.MultiE === 1 && data.profesionales2.InfAdj === 1 ? $('.multiInf').show() : $('.multiInf').hide();
    }

    function listaExInformador(data) {
        if([null, undefined, ''].includes(data)) return;
        
        $('.listaGrupoInformador').empty();
        for(let index = 0; index < data.length; index++) {
            let examen = data[index],
                contenido = `
                    <label class="list-group-item">
                        <input class="form-check-input me-1" type="checkbox" id="Id_multiAdjInf_${ examen.Id }" value="${ examen.Id}" ${ examen.archivos_count > 0 ? 'disabled' : 'checked' }> 
                        ${ examen.archivos_count > 0 ? examen.NombreExamen + ' (' + examen.NombreProveedor + ') <i title="Con archivo adjunto" class="ri-attachment-line verde"></i>' : examen.NombreExamen + ' (' + examen.NombreProveedor + ')'}
                    </label>
                `;
                $('.listaGrupoInformador').append(contenido);
        }
        
    }

    function adjuntosEfector(examenes, data) {

        switch(true) {
            case (examenes.Adjunto === 0):
                return 'No lleva adjuntos';

            case (examenes.Adjunto === 1 && data.adjuntoEfector === 0):
                return 'Pendiente';

            case (examenes.Adjunto === 1 && data.adjuntoEfector === 1):
                return 'Adjuntado';

            default:
                return '';
        }
    }

    function adjuntosInformador(item, data) {

        switch(true) {
            case (item.profesionales2.InfAdj === 0):
                return 'No lleva Adjuntos';

            case (item.profesionales2.InfAdj === 1 && data.adjuntoInformador === 0):
                return 'Pendiente';

            case (item.profesionales2.InfAdj === 1 && data.adjuntoInformador === 1):
                return 'Adjuntado';

            default:
                return '';
        }
    }

    function borrarCache() {
        $.post(cacheDelete, {_token: TOKEN}, function(){});
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

    function checkBloq(anulado, estilos) {
        
        if(anulado === 1) {
            $('#ex-Fecha, #ex-ObsExamen, #ex-efectores, #ex-informadores, #actExamenModal').prop('disabled', true);
            $('button').removeClass('ex-asignar ex-abrir ex-cerrar ex-asignarI ex-cerrarI');
            //p$('button').removeAttr('id');
            $('#ex-liberarI, #ex-liberar').show();
        }else{
            $('#ex-Fecha, #ex-ObsExamen, #ex-efectores, #ex-informadores, #actExamenModal').prop('disabled', false);
            $.each(estilos, function(index, estilo){
                let clase = estilo.replace('#', '');
                $(estilo).hasClass(clase) ? $(estilo).addClass(clase) : '';
            })
        }
    }

    async function primeraCarga(id, who) {
        let resultado = await $.ajax({
            url: checkFirst,
            type: 'GET',
            data: {
                Id: id, 
                who: who
            }
        });

        return resultado;
    }

    function contadorExamenes(idPrestacion) {
        $.get(contadorEx, {Id: idPrestacion}, function(response){
            $('#countExamenes').empty().text(response);
        });
    }

    function checkearFacturas() {

        if(['', 0, null].includes($('#ex-identificacion').val())) return;

        $.get(checkFacturas, {IdPrestacion: $('#ex-prestacion').val(), IdExamen: $('#ex-idExamen').val()})
            .done(function(response){

                switch (response.tipo) {
                    case 'examenCuenta':
                         $('#ex-NroFacturaVta').val(response.data.NroFactura);
                         $('#ex-FechaFacturaVta').val(response.data.Fecha);
                        return;
                    case 'facturaDeVenta':
                         $('#ex-NroFacturaVta').val(response.data.NroFactura);
                         $('#ex-FechaFacturaVta').val(response.data.Fecha);
                        return 
                    default:
                        $('#ex-NroFacturaVta').val();
                        $('#ex-FechaFacturaVta').val();
                        return;
                }
            })
            .fail(function(jqXHR){
                let errorData = JSON.parse(jqXHR.responseText);
                checkError(jqXHR.status, errorData.msg);
                return;
            })
    }

});