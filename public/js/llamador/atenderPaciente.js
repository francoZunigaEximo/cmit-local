$(function(){

    const variables = {
        profesional: $('#profesional'),
        prestacion: $('#prestacion_var'),
        fileA: $('.fileA'),
        DescripcionE: $('#DescripcionE'),
        prestacion: $('#prestacion_var'),
        especialidad: $('#especialidad'),
        especialidadSelect: $('#especialidadSelect'),
        ComentarioPriv: $("#ComentarioPriv"),
        privadoPrestaciones: $('#privadoPrestaciones')
    };

    const principal = {
        efector: 'Efector',
        tabla: $('#listaLlamadaEfector'),
        atenderEfector: $('#atenderEfector'),
        cargarArchivo: $('#cargarArchivo'),
        mensajeMulti: $('.mensajeMulti'),
        modalArchivo: $('#modalArchivo'),
        multi: $('#multi'),
        btnAdjEfector: $('.btnAdjEfector'),
        adjuntosEfectores: $('#adjuntosEfectores'),
        openObsPriv: $('#openObsPriv'),
    };

    const checkPaciente = checkerRolUser();

    principal.mensajeMulti.hide();

    $(document).on('click', 'input[type="checkbox"][name^="Id_"]', function () {

        let checkbox = $(this); 
            chequeado = checkbox.is(':checked'),
            idCheck = checkbox.val();

        if(!idCheck) return;

        preloader('on')
        $.get(asignacionProfesional, {Id: idCheck, Profesional: variables.profesional.val(), estado: chequeado})
            .done(function(response) {
                preloader('off')
                toastr.success(response.msg);
            })
            .fail(function(jqXHR) {
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);
      
                errorData.noCheck && checkbox.prop('checked', true);
                      
                checkError(jqXHR.status, errorData.msg);
                return;
            });
    });

    $(document).on('click', '.abrir, .cerrar', function(e){
        e.preventDefault();
        accion = $(this).hasClass('abrir') ? 'abrir' : 'cerrar';

        if(!accion) return;

        let fila = $(this).closest('tr.listadoAtencion'),
             id = fila.data('id'); 

        swal({
            title: "¿Esta seguro que desea cambiar el estado?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                preloader('on')
                $.get(itemPrestacionEstado, {Id: id, accion: accion, tipo: principal.efector})
                    .done(async function(response){
                        preloader('off');
                        toastr.success(response.msg);
                        await estado(response.CAdj, response.IdItem, 'edicion');
                        principal.tabla.DataTable().draw(false);
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

    $(document).on('click', '.terminarAtencion', function(e){
        e.preventDefault();

        preloader('on');
        $.get(addAtencion, {prestacion: variables.prestacion.val(), Tipo: (principal.efector).toUpperCase(), profesional: variables.profesional.val(), especialidad: variables.especialidad.data('id') || variables.especialidadSelect.val()})
            .done(function(){
                preloader('off');
                principal.atenderEfector.modal('hide');
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            });

    });

    $(document).on('click', '#modalArchivo', async function(e){
        e.preventDefault();

        principal.mensajeMulti.hide();
        principal.multi.val('fail');

        let id = $(this).data('id'),
            etiqueta = $('.list-group-item.listExamenes'),
            response = await $.get(getItemPrestacion, {Id: id});

        principal.btnAdjEfector.attr('data-iden', id);
        
        etiqueta.empty();
        variables.DescripcionE
            .add(variables.fileA)
            .val('');

        cargarArchivosEfector(variables.prestacion.val(), variables.profesional.val(), variables.especialidad.data('id') || variables.especialidadSelect.val());
        principal.cargarArchivo.modal('show');
        
        if(response.proveedores.Multi === 1) {
            principal.mensajeMulti.show();
            response.proveedores.Multi === 1 ? principal.multi.val('success') : principal.multi.val('fail');
        }

        if(response.multiEfector) {
            $.each(response.multiEfector, function(index, examen){
                let contenido = `
                    <input class="form-check-input me-1" type="checkbox" id="Id_multiAdj_${examen.Id}" value="${examen.Id}" ${examen.archivos_count > 0 ? 'disabled' : 'checked' }> 
                        ${examen.archivos_count > 0 ? examen.NombreExamen + ' <i title="Con archivo adjunto" class="ri-attachment-line verde"></i>' : examen.NombreExamen}
                `;
                etiqueta.append(contenido);
            });
        }

    });

    $(document).on('click', '.btnAdjEfector, .btnAdjInformador', async function (e){
        e.preventDefault();

        let id = $(this).data('iden');
            response = await $.get(getItemPrestacion, {Id: id}),
            obj = {
                efector: ['input[name="fileEfector"]', '[id^="Id_multiAdj_"]:checked', '#DescripcionE', '#efectores'],
                informador: ['input[name="fileInformador"]', '[id^="Id_multiAdjInf_"]:checked', '#DescripcionI', '#informadores']
            };
            
        if(!response.itemprestacion.IdProfesional) {
            toastr.warning('No puede adjuntar porque no posee profesional asignado', '', {timeOut: 1000});
            return;
        }

        let who = $(this).hasClass('btnAdjEfector') ? 'efector' : 'informador',
            archivo = $(obj[who][0])[0].files[0],
            multi = who === 'efector' ? $('#multi').val() : $('#multiE').val(), ids = [], anexoProfesional = parseInt(IDPROFESIONAL);

        $(obj[who][1]).each(function() {
            ids.push($(this).val());
        });

        if(ids.length === 0 && multi == "success"){
            toastr.warning('No hay examenes seleccionados');
            return;
        }
        
        let descripcion = $(obj[who][2]).val(),
            identificacion = (multi == 'success') ? ids : id,
            prestacion = $('#prestacion_var').val();
        
        who = multi === 'success' && who === 'efector'
                ? 'multiefector'
                : (multi === 'success' && who === 'informador'
                    ? 'multiInformador'
                    : who);
        
        if(verificarArchivo(archivo)){
            preloader('on');
            let formData = new FormData();
            formData.append('archivo', archivo);
            formData.append('Id', parseInt($(this).data('id')));
            formData.append('Descripcion', descripcion);
            formData.append('IdEntidad', parseInt(identificacion));
            formData.append('IdPrestacion', parseInt(prestacion));
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
                    
                    preloader('off');
                    toastr.success("Se ha cargado el reporte de manera correcta.");
                    principal.cargarArchivo.modal('hide');
                    cargarArchivosEfector(variables.prestacion.val(), variables.profesional.val(), variables.especialidad.data('id') || variables.especialidadSelect.val());
                    actualizarEstadoAdj(response.itemprestacion.examenes.Adjunto, 1, response.itemprestacion.Id);
                    
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

    $(document).on('click', '.deleteAdjunto', function(e){
        e.preventDefault();
        let id = $(this).data('id'), tipo = $(this).data('tipo'), itemprestacion = $(this).data('itempres');

        if(!id || !tipo){
            toastr.warning("Hay un problema porque no podemos identificar el tipo o la id a eliminar");
            return;
        }
        
        swal({
            title: "¿Está seguro que desea eliminar?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) =>{
            if(confirmar){
                preloader('on');
                $.get(deleteIdAdjunto, {Id: id, Tipo: tipo, ItemP: itemprestacion})
                    .done(function(response){
                        preloader('off');
                        toastr.success(response.msg);
                        cargarArchivosEfector(variables.prestacion.val(), variables.profesional.val(), variables.especialidad.data('id') || variables.especialidadSelect.val());
                        actualizarEstadoAdj(1, 0, itemprestacion)   
                    })
                    .fail(function(jqXHR){
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return;
                    })
            }
        });
    });

    $(document).on('click', '.addObs', function(e) {
        principal.openObsPriv.modal('show');
    });

    $(document).on('click', '.confirmarComentarioPriv', function(e){
        e.preventDefault();
        let comentario = variables.ComentarioPriv.val();

        if(!comentario){
            toastr.warning('La observación no puede estar vacía', 'Atención');
            return;
        }

        let profesional = PROFESIONAL[0].toUpperCase() + PROFESIONAL.slice(1).toLowerCase();

        swal({
            'title': '¿Estas seguro que quieres guardar el comentario privado?',
            'icon': 'warning',
            'buttons': ['Cancelar', 'Aceptar']
        }).then((confirmar) => {
            if(confirmar){

                preloader('on');
                $.post(savePrivComent, {_token: TOKEN, Comentario: comentario, IdEntidad: variables.prestacion.val(), obsfasesid: 2, Rol: profesional})
                    .done(function(){
                        preloader('off');
                        toastr.success('Perfecto', 'Se ha registrado la observacion correctamente');

                        setTimeout(() => {
                            variables.privadoPrestaciones.empty();
                            principal.openObsPriv.modal('hide');
                            variables.ComentarioPriv.val("");
                            comentariosPrivados(variables.prestacion.val());
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

    async function estado(CAdj, itemId) {

        let fila = $('.listadoAtencion[data-id="' + itemId + '"]'),
            td = fila.find('td').eq(1),
            html = '';

        const [isAdmin, esUsuarioPermitido] = await checkPaciente;
        let permiso = isAdmin || (esUsuarioPermitido && !isAdmin);

        if ([0, 1, 2].includes(CAdj)) {
            html = `<span class="rojo">Abierto ${permiso ? '' : '<i class="fs-6 ri-lock-unlock-line cerrar"></i>'}</span>`;
        } else if ([3, 4, 5].includes(CAdj)) {
            html = `<span class="verde">Cerrado ${permiso ? '' : '<i class="fs-6 ri-lock-2-line abrir"></i>'}</span>`;
        } else {
            html = '';
        }

        td.html(html);
    }

    function actualizarEstadoAdj(adjunto, condicion, idItem) {

        let campoAdjunto = $('tr.listadoAtencion td').eq(2);
        campoAdjunto.empty();

        let contenido = checkAdjunto(adjunto, condicion, idItem);

        campoAdjunto.html(contenido);

    }

    function checkAdjunto(adjunto, condicion, idItem) {
        switch (true) {
            case adjunto === 0:
                return '';

            case adjunto === 1 && condicion === 1:
                return `<span class="verde">Adjuntado <i class="fs-6 ri-map-pin-line"></i><span>`;

            case adjunto === 1 && condicion === 0:
                return `<span class="rojo d-flex align-items-center justify-content-between w-100">
                            <span class="me-auto">Pendiente</span>
                            <i class="fs-6 ri-map-pin-line mx-auto"></i>
                            <i class="fs-6 ri-folder-add-line ms-auto" id="modalArchivo" data-id="${idItem}"></i> 
                        </span>`;

            case adjunto === 0:
                return `<span class="mx-auto"><i class="gris fs-6 ri-map-pin-line"></i><span>`;

            default:
                return '';
        }
    }

    
        

});