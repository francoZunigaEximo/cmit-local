$(document).ready(()=>{

    let idExamen = []; 
 
    toastr.options = {
        closeButton: true,   
        progressBar: true,     
        timeOut: 3000,        
    };

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

    $(document).on('click', '.addPaquete', function(){
        
        let paquete = $('#paquetes').val();
        
        if(paquete === '' || paquete === null){
            toastr.warning("Debe seleccionar un paquete para poder añadirlo en su totalidad", "Atención");
            return;
        }
        mostrarPreloader('#preloader');
       $.ajax({
            url: paqueteId,
            type: 'POST',
            data: {
                _token: TOKEN,
                IdPaquete: paquete,
            },

            success:function(response){

                let data = response.examenes,
                    ids = data.map(function(item) {
                    return item.Id;
                  });
                saveExamen(ids);  
                ocultarPreloader('#preloader');
                $('.addPaquete').val([]).trigger('change.select2');
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

                if (adjunto == 1 && archivos > 0) {
                    tieneAdjunto = true;
                } else {
                    ids.push($(this).val());
                }
            });
    
            checkAll = $('#checkAllExamenes').prop('checked');

        } else if($(this).hasClass('deleteExamen')) {

            adjunto = $(this).data('adjunto'), archivos = $(this).data('archivo');

            if (adjunto == 1 && archivos > 0) {
                tieneAdjunto = true;
            } else {
                ids.push(id);
            }
        }
        if (tieneAdjunto) {
            toastr.warning('El o los examenes seleccionados tienen un reporte adjuntado. El mismo no se podrá eliminar.', 'Atención');
            return;
        }

        if(ids.length === 0 && checkAll === false){
            toastr.warning('No hay examenes seleccionados', 'Atención');
            return;
        }  
    
        if(confirm("Confirme la eliminación de los examenes")){
            mostrarPreloader('#preloader');
            $.ajax({
                url: deleteItemExamen,
                type: 'POST',
                data: {
                    Id: ids,
                    _token: TOKEN
                },
                success: function(response){
                    var estados = [];

                    response.forEach(function(msg) {
                        
                        let tipoRespuesta = {
                            success: 'success',
                            fail: 'info'
                        }
                        ocultarPreloader('#preloader');
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
            toastr.warning('No hay examenes seleccionados', 'Atención');
            return;
        }
    
        if(confirm("Confirme el bloqueo de los examenes")){
            
            mostrarPreloader('#preloader');
            $.ajax({    
                url: bloquearItemExamen,
                type: 'POST',
                data: {
                    Id: ids,
                    _token: TOKEN
                },
                success: function(response){
                    var estados = [];
                    
                    
                    response.forEach(function(msg) {

                        let tipoRespuesta = {
                            success: 'success',
                            fail: 'info'
                        }
                        ocultarPreloader('#preloader');
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

    $(document).on('click', '.abrirExamenes', function(e) {
        e.preventDefault();

        let ids = [];

        $('input[name="Id_examenes"]:checked').each(function() {
            ids.push($(this).val());
        });

        let checkAll = $('#checkAllExamenes').prop('checked');

        if(ids.length === 0 && checkAll === false){
            toastr.warning('No hay examenes seleccionados', 'Atención');
            return;
        }
        if(confirm("Confirme la apertura de los examenes")){

            mostrarPreloader('#preloader');
            $.ajax({
                url: updateEstadoItem,
                type: 'POST',
                data: {
                    Ids: ids,
                    _token: TOKEN
                },
                success: function(response){
                    var estados = [];

                    response.forEach(function(msg) {
                        
                        let tipoRespuesta = {
                            success: 'success',
                            fail: 'info'
                        }
                        ocultarPreloader('#preloader');
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

    $(document).on('click', '.adjuntoExamenes', function(e){
        e.preventDefault();

        let ids = [];

        $('input[name="Id_examenes"]:checked').each(function() {
            ids.push($(this).val());
        });

        let checkAll = $('#checkAllExamenes').prop('checked');

        if(ids.length === 0 && checkAll === false){
            toastr.warning('No hay examenes seleccionados', 'Atención');
            return;
        }

        if(confirm("Confirme la marca de adjunto de los examenes")){

            mostrarPreloader('#preloader');
            $.ajax({
                url: marcarExamenAdjunto,
                type: 'POST',
                data: {
                    Ids: ids,
                    _token: TOKEN
                },
                success: function(response){
                    var estados = [];

                    response.forEach(function(msg) {
                        
                        let tipoRespuesta = {
                            success: 'success',
                            fail: 'info'
                        }
                        ocultarPreloader('#preloader');
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

    $(document).on('click', '.liberarExamenes', function(e){

        e.preventDefault();

        let ids = [];

        $('input[name="Id_examenes"]:checked').each(function() {
            ids.push($(this).val());
        });

        let checkAll = $('#checkAllExamenes').prop('checked');

        if(ids.length === 0 && checkAll === false){
            toastr.warning('No hay examenes seleccionados', 'Atención');
            return;
        }

        if(confirm("Confirme la marca de adjunto de los examenes")){
        
            mostrarPreloader('#preloader');

            $.ajax({
                url: liberarExamen,
                type: 'POST',
                data: {
                    Ids: ids,
                    _token: TOKEN
                },
                success: function(response){
                    var estados = [];

                    response.forEach(function(msg) {
                        
                        let tipoRespuesta = {
                            success: 'success',
                            fail: 'info'
                        }
                        ocultarPreloader('#preloader');
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

    $(document).on('click', '.addExamen', function(){

        let id = $("#exam").val();
        
        if(id === '' || id === null) {
            toastr.warning("Debe seleccionar un examen para poder añadirlo a la lista", "Atención");
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
            toastr.warning("No existe el exámen o el paquete no contiene examenes", "Atención");
            return;
        }

        $.ajax({

            url: saveItemExamenes,
            type: 'post',
            data: {
                _token: TOKEN,
                idPrestacion: ID,
                idExamen: idExamen
            },
            success: function(){

                $('#listaExamenes').empty();
                $('#exam').val([]).trigger('change.select2');
                $('#addPaquete').val([]).trigger('change.select2');
                cargarExamen();
        },
            error: function(xhr){
                toastr.error("Ha ocurrido un error. Consulte con el administrador", "Error");
                console.error(xhr);
            }
        });

    }
    
    $(document).on('click', '.incompleto, .ausente, .forma, .sinesc, .devol', function() {
        let classes = $(this).attr('class').split(' '),
            item = $(this).closest('tr').find('td:first').attr('id');

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

    function opcionesExamenes(item, opcion){

        $.ajax({
            url: itemExamen,
            type: 'Post',
            data: {
                _token: TOKEN,
                Id: item,
                opcion: opcion
            },
            success: function(){

                toastr.success('Cambio realizado correctamente', 'Perfecto');

                let fila = $('td#' + item).closest('tr'), 
                    span= fila.find('span#' + opcion.toLowerCase()),
                    clase = span.attr('class'),
                    contenido = (clase === 'badge badge-soft-dark' ? 'custom-badge rojo' : 'badge badge-soft-dark');
            
                span.removeClass().addClass(contenido);
            },
            error: function(xhr){
                console.error(xhr);
                toastr.error("Ha ocurrido un error. Consulte con el administrador", "Error");
            }
        });

    }

    function cargarExamen(){
        
        $.ajax({

            url: checkItemExamen, 
            method: 'GET',
            data: { 
                Id: ID,
            },
            success: function(result) {
                let estado = result.respuesta;
                let examenes = result.examenes;
            
                if(estado === true){

                    $.ajax({
                        
                        url: getItemExamenes,
                        type: 'post',
                        data: {
                            _token: TOKEN,
                            IdExamen: examenes,
                            Id: ID,
                            tipo: 'listado'
                        },
                        success: function(response){
                            
                            let registros = response.examenes;

                            registros.forEach(function(examen) {
                                let examenId = examen.IdExamen;

                                let url = editUrl.replace('__examen__', examen.IdItem);

                                let fila = `
                                        <tr ${examen.Anulado === 1 ? 'class="filaBaja"' : ''}>
                                            <td><input type="checkbox" name="Id_examenes" value="${examen.IdItem}" checked data-adjunto="${examen.ExaAdj}" data-archivo="${examen.archivos}"></td>
                                            <td data-idexam="${examenId}" id="${examen.IdItem}" style="text-align:left">${examen.Nombre}</td>
                                            <td>
                                                <span id="incompleto" class="${(examen.Incompleto === 0 ||  examen.Incompleto === null ? 'badge badge-soft-dark' : 'custom-badge rojo')}">
                                                    <i class="ri-flag-2-line ${examen.Anulado === 0 ? 'incompleto' : ''}"></i>
                                                </span>
                                            </td>  <!-- este botón marca o desmarca el campo incompleto - debe ser rojo si es que el valor del campo es 1 -->
                                            <td>
                                                <span id="ausente" class="${(examen.Ausente === 0 || examen.Ausente === null ? 'badge badge-soft-dark' : 'custom-badge rojo')}">
                                                    <i class=" ri-flag-2-line ${examen.Anulado === 0 ? 'ausente' : '' }"></i>
                                                </span>
                                            </td><!-- este botón marca o desmarca el campo ausente - debe ser rojo si es que el valor del campo es 1 -->
                                            <td>
                                                <span id="forma" class="${(examen.Forma === 0 || examen.Forma === null ? 'badge badge-soft-dark' : 'custom-badge rojo')}">
                                                    <i class=" ri-flag-2-line ${examen.Anulado === 0 ? 'forma' : ''}"></i>
                                                </span>
                                            </td><!-- este botón marca o desmarca el campo fomra - debe ser rojo si es que el valor del campo es 1 -->
                                            <td>
                                                <span id="sinesc" class="${(examen.SinEsc === 0 || examen.SinEsc === null ? 'badge badge-soft-dark' : 'custom-badge rojo')}">
                                                    <i class=" ri-flag-2-line ${examen.Anulado === 0 ? 'sinesc' : '' }"></i>
                                                </span>
                                            </td><!-- este botón marca o desmarca el campo sinesc - debe ser rojo si es que el valor del campo es 1 -->
                                            <td>
                                                <span id="devol" id="${examen.IdItem}" class="${(examen.Devol === 0 || examen.Devol === null ? 'badge badge-soft-dark' : 'custom-badge rojo')}">
                                                    <i class=" ri-flag-2-line ${examen.Anulado === 0 ? 'devol' : '' }"></i>
                                                </span>
                                            </td><!-- este botón marca o desmarca el campo devolucion - debe ser rojo si es que el valor del campo es 1 -->
        
                                            <td class="date text-center" title="${examen.ApellidoE} ${examen.NombreE}">${examen.ApellidoE}
                                                <span class="badge badge-soft-${([0,1,2].includes(examen.CAdj) ? 'danger': ([3,4,5].includes(examen.CAdj) ? 'success' : ''))}">${([0,1,2].includes(examen.CAdj) ? 'Abierto': ([3,4,5].includes(examen.CAdj) ? 'Cerrado' : ''))}</span>
                                                ${examen.ExaAdj === 1 ? `<i class="ri-attachment-line ${[2,5].includes(examen.CAdj) ? 'verde' : [1,4].includes(examen.CAdj) ? 'gris' : ''}"></i>`: ``}    
                                            </td>
                                            <td class="date text-center" title="${examen.ApellidoI} ${examen.NombreI}">${examen.ApellidoI}
                                                <span class="badge badge-soft-${(examen.CInfo === 3 ? 'success' : ([0,1,2].includes(examen.CInfo)) ? 'danger' : '')}">${(examen.CInfo === 3 ? 'Cerrado' : (examen.CInfo == 2 ? 'Borrador' : ([0,1].includes(examen.CInfo) ? 'Pendiente': '')))}</span>
                                                ${examen.InfAdj === 1 ? `<i class="ri-attachment-line ${[2,3].includes(examen.CInfo) ? 'verde' : [1].includes(examen.CInfo) ? 'gris' : ''}"></i>`: ``}   
                                            </td>
                                    <!-- muestra el apellido + nombre del informador y debajo el estado (campo CInfo - Cerrado = 3, Borrador = 2 o pendiente = 0 y 1)   -->
                                    <td class="phone"><span class="${examen.Facturado === 1 ? 'badge badge-soft-success' : 'custom-badge gris'}"><i class="ri-check-line"></i></span></td> <!-- > campo Facturado gris si el campo tiene valor 0 verde si el campo tiene valor 1</!-->
                                    <td>
                                        <div class="d-flex gap-2">
                                            <div class="edit">
                                                <a href="${url}" id="editLink">
                                                    <button type="button" class="btn btn-sm iconGeneral" title="Ver"><i class="ri-search-eye-line"></i></button>
                                                </a>
                                            </div>
                                            ${examen.Anulado === 0 ? `
                                                <div class="bloquear">
                                                    <button data-bloquear="${examen.IdItem}" class="btn btn-sm iconGeneral bloquearExamen" title="Baja">
                                                        <i class="ri-forbid-2-line"></i>
                                                    </button>
                                                </div>
                                                <div class="remove">
                                                    <button data-delete="${examen.IdItem}"  class="btn btn-sm iconGeneral deleteExamen" title="Eliminar">
                                                        <i class="ri-delete-bin-2-line"></i>
                                                    </button>
                                                </div>    
                                                ` : ''}
                                            
                                        </div>
                                    </td>
                                    </tr>`;
            
                                $('#listaExamenes').append(fila);
                            });

                            $("#listado").fancyTable({
                                pagination: true,
                                perPage: 50,
                                searchable: false,
                                globalSearch: false,
                                sortable: false, 
                            });
                        }
                    });
                }
            },
            error: function(xhr){
                console.error(xhr);
                toastr.error("Ha ocurrido un error. Consulte con el administrador", "Error");
            }
        });
    }

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
});