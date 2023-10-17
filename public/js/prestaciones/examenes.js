$(document).ready(()=>{
 

    $('#exam').select2({
        placeholder: 'Seleccionar exámen...',
        language: 'es',
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
            swal("Atención", "Debe seleccionar un paquete para poder añadirlo en su totalidad", "warning");
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
                setTimeout(() => {
                    location.reload();
                }, 3000);     
            }
       });
       
    });

    $(document).on('click', '.delete-examen', function() {

        let idItem = $(this).data('delete');
        $(this).closest('tr').remove();

        if (idItem !== undefined) {
            
            $.ajax({
                url: deleteExamen,
                type: 'Post',
                data: {
                    Id: idItem,
                    _token: TOKEN
                },
                success: function(){

                    toastr.options = {
                        closeButton: true,   
                        progressBar: true,    
                        timeOut: 3000,        
                    };
                    toastr.info('Se ha eliminado el examen de manera correcta de la BD', 'Eliminación realizada');
                }
            });
        }
    });

    $(document).on('click', '.bloquear-examen', function() {

        let idItem = $(this).data('bloquear');
        $(this).closest('tr').remove();

        if (idItem !== undefined) {
            
            $.ajax({
                url: bloquearExamen,
                type: 'Post',
                data: {
                    Id: idItem,
                    _token: TOKEN
                },
                success: function(){

                    toastr.options = {
                        closeButton: true,   
                        progressBar: true,    
                        timeOut: 3000,        
                    };
                    toastr.info('Se ha bloquead el examen de manera correcta en la BD', 'Bloqueo realizado');
                }
            });
        }
    });

    $(document).on('click', '.addExamen', function(){

        let id = $("#exam").val();
        
        if(id === '' || id === null) {
            swal("Atención", "Debe seleccionar un examen para poder añadirlo a la lista", "warning");
            return;
        }

        saveExamen(id);
    });

    let idExamen = []; 
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
            swal('Atención','No existe el exámen o el paquete no contiene examenes','warning');
            return;
        }

        $.ajax({

            url: saveExamenes,
            type: 'post',
            data: {
                _token: TOKEN,
                idPrestacion: Id,
                idExamen: idExamen
            },
            success: function(){

                $('#listaExamenes').empty();
                $('#exam').val([]).trigger('change.select2');
                $('#addPaquete').val([]).trigger('change.select2');
                cargarExamen();
                location.reload();
        },
            error: function(xhr){
                swal('Error', 'Ha ocurrido un error. Consulte con el administrador', 'error');
                console.error(xhr);
            }
        });

    }

    cargarExamen();
    
    $(document).on('click', '.incompleto, .ausente, .forma, .sinesc, .devol', function() {
        let classes = $(this).attr('class').split(' '),
            item = $(this).closest('tr').find('td:first').attr('id');
    
            if (classes.includes('incompleto')) {

                opcionesExamenes(item, 'Incompleto');

            } else if (classes.includes('ausente')) {

                opcionesExamenes(item, 'Ausente');

            } else if(classes.includes('forma')) {

                opcionesExamenes(item, 'Forma');
            
            } else if(classes.includes('sinesc')) {

                opcionesExamenes(item, 'SinEsc');

            } else if(classes.includes('devol')) {

                opcionesExamenes(item, 'Devol');
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

                toastr.options = {
                    closeButton: true,   
                    progressBar: true,    
                    timeOut: 3000,        
                };
                toastr.success('Cambio realizado correctamente', 'Perfecto');

                let fila = $('td#' + item).closest('tr'), 
                    span= fila.find('span#' + opcion.toLowerCase()),
                    clase = span.attr('class'),
                    contenido = (clase === 'badge badge-soft-dark' ? 'custom-badge rojo' : 'badge badge-soft-dark');
            
                span.removeClass().addClass(contenido);
            },
            error: function(xhr){
                console.error(xhr);
                swal('Error', 'Ha ocurrido un error', 'error');
            }
        });

    }

    function cargarExamen(){
        
        $.ajax({

            url: checkExamen, 
            method: 'Post',
            data: { 
                Id: Id,
                _token: TOKEN,
            },
            success: function(result) {
                let estado = result.respuesta;
                let examenes = result.examenes;
            
                if(estado === true){

                    $.ajax({
                        
                        url: getExamenes,
                        type: 'post',
                        data: {
                            _token: TOKEN,
                            IdExamen: examenes,
                            Id: Id,
                            tipo: 'listado'
                        },
                        success: function(response){
                            
                            let registros = response.examenes;

                            registros.forEach(function(examen) {
                                let examenId = examen.IdExamen;

                                let url = editUrl.replace('__examen__', examen.IdItem);

                                let fila = `
                                        <tr>
                                            <td data-idexam="${examenId}" id="${examen.IdItem}">${examen.Nombre}</td>
                                            <td>
                                                <span id="incompleto" class="${(examen.Incompleto === 0 ||  examen.Incompleto === null ? 'badge badge-soft-dark' : 'custom-badge rojo')}">
                                                    <i class="ri-flag-2-line incompleto"></i>
                                                </span>
                                            </td>  <!-- este botón marca o desmarca el campo incompleto - debe ser rojo si es que el valor del campo es 1 -->
                                            <td>
                                                <span id="ausente" class="${(examen.Ausente === 0 || examen.Ausente === null ? 'badge badge-soft-dark' : 'custom-badge rojo')}">
                                                    <i class=" ri-flag-2-line ausente"></i>
                                                </span>
                                            </td><!-- este botón marca o desmarca el campo ausente - debe ser rojo si es que el valor del campo es 1 -->
                                            <td>
                                                <span id="forma" class="${(examen.Forma === 0 || examen.Forma === null ? 'badge badge-soft-dark' : 'custom-badge rojo')}">
                                                    <i class=" ri-flag-2-line forma"></i>
                                                </span>
                                            </td><!-- este botón marca o desmarca el campo fomra - debe ser rojo si es que el valor del campo es 1 -->
                                            <td>
                                                <span id="sinesc" class="${(examen.SinEsc === 0 || examen.SinEsc === null ? 'badge badge-soft-dark' : 'custom-badge rojo')}">
                                                    <i class=" ri-flag-2-line sinesc"></i>
                                                </span>
                                            </td><!-- este botón marca o desmarca el campo sinesc - debe ser rojo si es que el valor del campo es 1 -->
                                            <td>
                                                <span id="devol" id="${examen.IdItem}" class="${(examen.Devol === 0 || examen.Devol === null ? 'badge badge-soft-dark' : 'custom-badge rojo')}">
                                                    <i class=" ri-flag-2-line devol"></i>
                                                </span>
                                            </td><!-- este botón marca o desmarca el campo devolucion - debe ser rojo si es que el valor del campo es 1 -->
        
                                            <td class="date text-center">${examen.ApellidoE} ${examen.NombreE} <br>
                                                <span class="badge badge-soft-danger">${(examen.CAdj === 0 || examen.CAdj === 1 || examen.CAdj === 2 ? 'Abierto': (examen.CAdj === 3 || examen.CAdj === 4 || examen.CAdj === 5 ? 'Cerrado' : 'Sin datos'))}</span>
                                                    <i class="ri-attachment-line ${(examen.CAdj === 0 || examen.CAdj === 3 || examen.CAdj === 1 || examen.CAdj === 4 ? 'rojo' : 'verde')}"></i>
                                            </td>
                                    <!-- muestra el apellido + nombre del efector y debajo el estado (campo CAdj Abierto = 0 - 1 - 2 Cerrado = 3 - 4 - 5)  y al lado el icono de archivo (campo cAdj) gris si no hay archivo o verde si hay adjunto (NA = 0 - 3 Pendiente = 1 - 4  Adjunto = 2 - 5)  -->
                                            <td class="date text-center">${examen.ApellidoI} ${examen.NombreI}<br>
                                                <span class="badge badge-soft-danger">${(examen.CInfo === 3 ? 'Cerrado' : (examen.CInfo === 2 ? 'Borrador' : (examen.CInfo === 1 || examen.CInfo === 0 ? 'Pendiente': 'Sin datos')))}</span>
                                            </td>
                                    <!-- muestra el apellido + nombre del informador y debajo el estado (campo CInfo - Cerrado = 3, Borrador = 2 o pendiente = 0 y 1)   -->
                                    <td class="phone"><span class="${examen.Facturado === 1 ? 'badge badge-soft-success' : 'custom-badge gris'}"><i class="ri-check-line"></i></span></td> <!-- > campo Facturado gris si el campo tiene valor 0 verde si el campo tiene valor 1</!-->
                                    <td>
                                        <div class="d-flex gap-2">
                                            <div class="edit">
                                                <a href="${url}" id="editLink">
                                                    <button type="button" class="btn btn-sm btn-soft-primary edit-item-btn" title="Ver"><i class="ri-search-eye-line"></i></button>
                                                </a>
                                            </div>
                                            <div class="bloquear">
                                                <button data-bloquear="${examen.IdItem}" class="btn btn-sm btn-warning remove-item-btn bloquear-examen" title="Inhabilitar">
                                                    <i class="ri-forbid-2-line"></i>
                                                </button>
                                            </div>
                                            <div class="remove">
                                                <button data-delete="${examen.IdItem}"  class="btn btn-sm btn-danger delete-examen" title="Eliminar">
                                                    <i class="ri-delete-bin-2-line"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    </tr>`;
            
                                $('#listaExamenes').append(fila);
                            });

                        }
                    });
                }
            },
            error: function(xhr){
                console.error(xhr);
                swal('Error', 'Ha ocurrido un error. Consulte con el administrador', 'error');
            }
        });
    }
});