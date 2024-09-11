$(document).ready(function(){

    const valAbrir = ['3','4','5'], valCerrar = ['0','1','2'], ID = $('#Id').val(), valCerrarI = 3;
    let cadj = $('#CAdj').val(), CInfo = $('#CInfo').val(), efector = $('#efectores').val(), informador = $('#informadores').val(), provEfector = $('#IdEfector').val(), provInformador = $('#IdInformador').val(), Estado = $('#Estado').val(), EstadoI = $('#EstadoI').val();

    $('.abrir, .cerrar, .asignar, .liberar, .asignarI, .liberarI, .cerrarI, .adjuntarEfector, .adjuntarInformador').hide();
    $('#efectores option[value="0"]').text('Elija una opción...');
    $('#informadores option[value="0"]').text('Elija una opción...');
    
    checkBloq();

    asignar(efector, 'efector');
    asignar(informador, 'informador');
    liberar(cadj, efector, 'efector');
    liberar(CInfo, informador, 'informador');
    abrir(cadj);
    cerrar(cadj, efector, 'efector');
    cerrar(cadj, informador, 'informador');
    optionsGeneral(provEfector, 'efector');
    optionsGeneral(provInformador, 'informador');
    listadoE();
    listadoI();

    $(document).on('click', '.btnAdjEfector, .btnAdjInformador', function (e){
        e.preventDefault();
        
        let who = $(this).hasClass('btnAdjEfector') ? 'efector' : 'informador';

        let obj = {
            efector: ['input[name="fileEfector"]', '[id^="Id_multiAdj_"]:checked', '#DescripcionE', '#efectores'],
            informador: ['input[name="fileInformador"]', '[id^="Id_multiAdjInf_"]:checked', '#DescripcionI', '#informadores']
        }
        
        let archivo = $(obj[who][0])[0].files[0];

        let multi = who === 'efector' ? $('#multi').val() : $('#multiE').val(), ids = [], anexoProfesional = $(obj[who][3]).val();

        $(obj[who][1]).each(function() {
            ids.push($(this).val());
        });

        if(ids.length === 0 && multi == "success"){
            toastr.warning('No hay examenes seleccionados', 'Atención');
            return;
        }
        
        let descripcion = $(obj[who][2]).val(),
            identificacion = (multi == 'success') ? ids : $('#identificacion').val(),
            prestacion = $('#prestacion').val();
        
        who = multi === 'success' && who === 'efector'
                ? 'multiefector'
                : (multi === 'success' && who === 'informador'
                    ? 'multiInformador'
                    : who);
        
        if(verificarArchivo(archivo)){
            preloader('on');
            let formData = new FormData();
            formData.append('archivo', archivo);
            formData.append('Id', ID);
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
                    preloader('off');
                    toastr.success("Se ha cargado el reporte de manera correcta.");
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                },
                error: function (xhr) {
                    preloader('off');
                    let errorData = JSON.parse(jqXHR.responseText);            
                    checkError(jqXHR.status, errorData.msg);
                    return;
                }
            });
        }
    });
    
    

    $(document).on('click', '#abrir', function(e){
        e.preventDefault();
        let lista = {3: 0, 4: 1, 5: 2};

        if(cadj in lista){
            preloader('on');
            $.post(updateItem, {Id : ID, _token: TOKEN, CAdj: lista[cadj], Para: 'abrir' })
                .done(function(){
                    preloader('off');
                    toastr.success('Se ha realizado la acción correctamente', 'Actualizacion realizada');
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

    $('#btnVolver').click(function() {
        location.href = volver;
    }); 

    $(document).on('click', '.deleteAdjunto', function(e){
        e.preventDefault();
        let id = $(this).data('id'), tipo = $(this).data('tipo');

        if(id === '' || tipo === ''){
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
                $.get(deleteIdAdjunto, {Id: id, Tipo: tipo, ItemP: ID})
                    .done(function(response){
                        preloader('off');
                        toastr.success(response.msg);
                        
                        setTimeout(() => {
                            location.reload();
                        }, 3000);
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

    $(document).on('click', '#cerrar, #cerrarI', function(){
        
        let who = $(this).hasClass('cerrar') ? 'cerrar' : 'cerrarI',
            listaE = {0: 3, 2: 5, 1: 4},
            listaI = ['0', '1', '2', '3'];

        if(who === 'cerrar' && cadj in listaE){
            preloader('on');
            $.post(updateItem, {Id : ID, _token: TOKEN, CAdj: listaE[cadj], Para: who })
                .done(function(){
                    preloader('off');
                    toastr.success('Se ha cerrado al efector correctamente', 'Actualizacion realizada');
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
            $.post(updateItem, {Id : ID, _token: TOKEN, CInfo: 3, Para: who })
                .done(function(){
                    preloader('off');
                    toastr.success('Se ha cerrado al informador correctamente', 'Actualizacion realizada');
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

    $(document).on('click', '#asignar, #asignarI', function() {

        let who = $(this).hasClass('asignar') ? 'asignar' : 'asignarI',
            check = (who === 'asignar') ? $('#efectores').val() : $('#informadores').val();

        if(['', null, 0, '0'].includes(check)) {
            toastr.warning("Debe seleccionar un Efector/Informador para poder asignar uno");
            return;
        }

        preloader('on');
        $.post(updateAsignado, { Id: ID, _token: TOKEN, IdProfesional: check, fecha: 1, Para: who})
            .done(function(){
                preloader('off');
                toastr.success('Se ha actualizado la información de manera correcta', 'Actualización realizada');
                setTimeout(() => {
                    location.reload();             
                }, 3000); 
            })
    });

    $(document).on('click', '#liberar, #liberarI', function(e) {
        e.preventDefault();

        let checkEmptyE = $('#efectores').val(), checkEmptyI = $('#informadores').val(), who = $(this).attr('id') === 'liberar' ? checkEmptyE : checkEmptyI;
        
        if (who != '0') {

            $.post(updateAsignado, { Id: ID, _token: TOKEN, IdProfesional: 0, fecha: 0, Para: $(this).attr('id') === 'liberar' ? 'asignar' : 'asignarI'})
            .done(function(response){
                toastr.success(response.msg);
                setTimeout(() => {
                    location.reload();
                }, 3000);
                
            })
        }
    });

    $(document).on('click', '#adjuntos', function() {

        let lista = {1: 2, 4: 5, 2: 1, 5: 4};

        if (cadj === '0' || cadj === null) return;

        if (cadj in lista) {
            $.post(updateAdjunto, {Id: ID, _token: TOKEN, CAdj: lista[cadj]})
                .done(function(response){
                    toastr.success(response.msg);
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                })
        }
            
    });

    $(document).on('click', '#actualizarExamen', function(){

        let ObsExamen = $('#ObsExamen').val(), Profesionales2 = $('#informadores').val(), Obs = $('#Obs').val(), Fecha = $('#Fecha').val();
        
        $.post(updateItemExamen, {Id: ID, _token: TOKEN, ObsExamen: ObsExamen, Profesionales2: Profesionales2, Obs: Obs, Fecha: Fecha})
            .done(function(response) {

                toastr.success(response.msg);
                setTimeout(() => {
                    location.reload();
                }, 3000);
            })
            .fail(function(jqXHR) {
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            });
    });

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
                    preloader('off');
                    toastr.success(response.msg);
                    setTimeout(() => {
                        listadoE();
                        listadoI();
                        $('#replaceAdjunto').modal('hide');
                    }, 3000);
                },
                error: function (jqXHR) {
                    preloader('off');
                    let errorData = JSON.parse(jqXHR.responseText);            
                    checkError(jqXHR.status, errorData.msg);
                    return;
                }
            });
        }

    })

    async function abrir(val){
        let resultado = await (valAbrir.includes(val));
        
        if (resultado) {

            $('.abrir').show();
            $('#informadores').prop('disabled', false);
        
        } else {

            $('.abrir').hide();  
        }
    }

    async function cerrar(val, e, tipo){  

        if (tipo === 'efector') {

            let resultado = await (valCerrar.includes(val) && e !== '0');
            
            if(resultado){
                $('.cerrar').show();
                $('#informadores').prop('disabled', true);

            }
        } else if (tipo === 'informador') {

            let resultado = await (efector !== '0' && informador !== '0') && (CInfo !== '3'),
                final = await (efector !== '0' && informador !== '0') && (Estado === 'Cerrado' && EstadoI === 'Cerrado');

            if(resultado){
   
                $('.cerrarI').show();
                $('.abrir').hide();
                $('.adjuntarInformador').show();

            }else if(final){

                $('.cerrarI').hide();
                $('.abrir').hide();
                $('.liberarI').hide();
                $('.adjuntarInformador').hide();
            }
        }
    }

    async function asignar(e, tipo){

        if (tipo === 'efector') {

            let resultado = await (e === '0' || e === null || e === '');

            if (resultado) {

                $('.asignar').show();
                $('#informadores').prop('disabled', true);
                $('.adjuntarInformador').hide();
            }
        
        } else if (tipo === 'informador') {

            let resultado = await (e === '0' || e === null || e === '') && (efector !== '0');
            
            if (resultado) {
                $('.asignarI').show();
                $('.liberarI').hide();
                $('.abrir').show();
            }
        }
    }
       
    
    async function liberar(val, e, tipo){
        
        if(tipo === 'efector') {

            let resultado = await (!['',null,'0'].includes(e) && valCerrar.includes(val));
            
            if (resultado) {
                $('.liberar').show();
                $('.asignarI').hide();
                $('.adjuntarEfector').show();
            } 
        }else if(tipo === 'informador') {

            let resultado = await (!['',null,'0'].includes(e) && valCerrarI !== val);
        
            if (resultado) {
                $('.liberarI').show();
                $('.asignarI').hide();
                $('.adjuntarInformador').show();
            }  

        }
    }

    function optionsGeneral(id, tipo) {
        
        let etiqueta, valor;

        if (tipo === 'efector') {
            etiqueta = $('#efectores');
            valor = etiqueta.val();

        } else if (tipo === 'informador') {
            etiqueta = $('#informadores');
            valor = etiqueta.val();
        }
    
        if (valor === '0') {

            $.get(listGeneral, { proveedor: id, tipo: tipo })
                .done(function (response) {
                    let data = response.resultados;

                    $.each(data, function (index, d) {
                        let contenido = `<option value="${d.Id}">${d.NombreCompleto}</option>`;
                        etiqueta.append(contenido);
                    });
                });
        }
    }

    function listadoE(){

        $('#listaefectores').empty();
        preloader('on');
        $.get(paginacionGeneral, {Id: ID, tipo: 'efector'})
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
                                    ${(Estado === 'Cerrado') ? `` : `
                                        <div class="remove">
                                            <button data-id="${d.IdE}" data-tipo="efector" class="btn btn-sm iconGeneral deleteAdjunto" title="Eliminar">
                                                <i class="ri-delete-bin-2-line"></i>
                                            </button>
                                        </div>
                                    `}
                                    
                                </div>
                            </td>
                        </tr>
                    `;

                    $('#listaefectores').append(contenido);
                });
            })
    }

    function listadoI(){

        $('#listainformadores').empty();
        preloader('on');
        $.get(paginacionGeneral, {Id: ID, tipo: 'informador'})
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
                                    ${(EstadoI === 'Cerrado') ? `` : `
                                    <div class="remove">
                                        <button data-id="${d.IdI}" data-tipo="informador" class="btn btn-sm iconGeneral deleteAdjunto" title="Eliminar">
                                            <i class="ri-delete-bin-2-line"></i>
                                        </button>
                                    </div>
                                    `}
                                </div>
                            </td>
                        </tr>
                    `;

                    $('#listainformadores').append(contenido);
                });
            })
    }

    function verificarArchivo(archivo){

        if (!archivo || archivo.size === 0) {
            toastr.warning("Debe seleccionar un archivo", "Atención");
            return false;
        }

        if (!archivo.name.includes('.')) {
            toastr.warning("El archivo no tiene extensión o la misma es invalida", "Atención");
            return false;
        }

        let tipoArchivo = archivo.type.toLowerCase();

        if(tipoArchivo !== 'application/pdf' && !tipoArchivo.startsWith('image/')) {
            toastr.warning("Los archivos permitidos son imágenes o PDF", "Atención");
            return false;
        }

        return true

    }

    function checkBloq(){

        $.get(getBloqueoItemPrestacion, {Id: ID})
            .done(async function(response){

                if(await response.prestacion === true){

                    $('#Fecha, #ObsExamen, #efectores, #informadores').prop('disabled', true);
                    $('button').removeClass('asignar liberar abrir cerrar asignarI liberarI cerrarI');
                    $('button').removeAttr('id');
                    $('i.ri-play-list-add-line').removeClass('addPaquete');
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