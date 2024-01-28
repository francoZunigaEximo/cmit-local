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
            }
       });
       
    });

    $(document).on('click', '.delete-examen', function() {

        let idItem = $(this).data('delete');
        $(this).closest('tr').remove();

        if (idItem !== undefined) {
            if(confirm("Confirme la eliminación del exámen:")){

                $.ajax({
                    url: deleteItemExamen,
                    type: 'Post',
                    data: {
                        Id: idItem,
                        _token: TOKEN
                    },
                    success: function(){
                        toastr.info('Se ha eliminado el exámen.', 'Eliminar');
                    }
                });
            }
            
        }
    });

    $(document).on('click', '.bloquear-examen', function() {

        let idItem = $(this).data('bloquear');

        if (idItem !== undefined) {
            if(confirm('Confirme la baja del exámen:')){

                $.ajax({
                    url: bloquearItemExamen,
                    type: 'Post',
                    data: {
                        Id: idItem,
                        _token: TOKEN
                    },
                    success: function(){
                        toastr.info('Se ha dado de baja el exámen', 'Baja realizada');
                        $('#listaExamenes').empty();
                        $('#exam').val([]).trigger('change.select2');
                        $('#addPaquete').val([]).trigger('change.select2');
                        cargarExamen();
                    }
                });
            }   
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
                                            <td data-idexam="${examenId}" id="${examen.IdItem}">${examen.Nombre}</td>
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
                                                <span class="badge badge-soft-${(examen.CAdj === 0 || examen.CAdj === 1 || examen.CAdj === 2 ? 'danger': (examen.CAdj === 3 || examen.CAdj === 4 || examen.CAdj === 5 ? 'success' : ''))}">${(examen.CAdj === 0 || examen.CAdj === 1 || examen.CAdj === 2 ? 'Abierto': (examen.CAdj === 3 || examen.CAdj === 4 || examen.CAdj === 5 ? 'Cerrado' : ''))}</span>
                                                ${(examen.CAdj === 2 || examen.CAdj === 5) && examen.ExaAdj === 1 ? `<i class="ri-attachment-line ${(examen.CAdj === 2 || examen.CAdj === 5  ? 'verde' : '')}"></i>`: ``}    
                                            </td>
                                    <!-- muestra el apellido + nombre del efector y debajo el estado (campo CAdj Abierto = 0 - 1 - 2 Cerrado = 3 - 4 - 5)  y al lado el icono de archivo (campo cAdj) gris si no hay archivo o verde si hay adjunto (NA = 0 - 3 Pendiente = 1 - 4  Adjunto = 2 - 5)  -->
                                            <td class="date text-center" title="${examen.ApellidoI} ${examen.NombreI}">${examen.ApellidoI}
                                                <span class="badge badge-soft-${(examen.CInfo === 3 ? 'success' : (examen.CInfo === 2 ? 'danger' : (examen.CInfo === 1 || examen.CInfo === 0 ? 'danger': '')))}">${(examen.CInfo === 3 ? 'Cerrado' : (examen.CInfo === 2 ? 'Borrador' : (examen.CInfo === 1 || examen.CInfo === 0 ? 'Pendiente': '')))}</span>
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
                                                    <button data-bloquear="${examen.IdItem}" class="btn btn-sm iconGeneral bloquear-examen" title="Baja">
                                                        <i class="ri-forbid-2-line"></i>
                                                    </button>
                                                </div>
                                                <div class="remove">
                                                    <button data-delete="${examen.IdItem}"  class="btn btn-sm iconGeneral delete-examen" title="Eliminar">
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
});