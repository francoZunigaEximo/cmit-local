$(document).ready(()=>{

    let hoy = new Date().toISOString().slice(0, 10), 
        maxCaracteres = 100;

    toastr.options = {
        closeButton: true,   
        progressBar: true,    
        timeOut: 3000,        
    };

    quitarDuplicados("#Estado");
    getPrestaMapas();
    verificacionNro();
    getCerrarMapas();
    getFinalMapa();
    getEnMapa();
    listaComentariosPrivados(IDMAPA);
    listarRemitos(IDMAPA);

    $('#remitoFechaE').val(hoy);
    $('.comentarioObsEstado').hide();
    $('#EstadoCerrar').val('abierto');

    $('#verPrestacionModal').on('shown.bs.modal', function () {
        $(document).off('click', '.mostrarObsEstado, .cerrarObsEstado');
        $(document).on('click', '.mostrarObsEstado, .cerrarObsEstado', function(){

            if ($(this).hasClass('mostrarObsEstado')) {
                $('.comentarioObsEstado').show();
            } else {
                $('.comentarioObsEstado').hide();
            }
        });
    });


    $('#verPrestacionModa').on('hide.bs.modal', function () {
        console.log("Modal ocultado");
        $(".ComObsEstado").val("");
        $('.comentarioObsEstado').hide();
    });
    
    //Exportar
    $(document).on('click', '.excel, .pdf', function(){
        
        let ids = [];
        ids.push($(this).data('remito'));

        let arr = {
            excel: 
                {
                    datos:  {modulo: 'remito', tipo: 'excel', mapa: MAPA, Id: ids, archivo: 'csv'},
                    archivo: 'csv'    
                },
            pdf:
                {
                    datos:  {Id: ids, mapa: MAPA, archivo: 'pdf'},
                    archivo: 'pdf'  
                }
        };
        
        tipo = $(this).hasClass('pdf') ? 'pdf' : 'excel';

        if (ids.length > 0) {
            if (confirm("¿Estás seguro de que deseas generar el reporte?")) {
                $.ajax({
                    url: fileExport,
                    type: "GET",
                    data: arr[tipo].datos,
                    success: function(response) {
                        createFile(arr[tipo].archivo, response.filePath);
                        toastr.success('Se esta generando el reporte. Aguarde', 'Generando reporte');
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            }
        } else {
            toastr.error('Debes seleccionar al menos un mapa para exportar.', 'Error');
        }

    });

    $('#updateMapa').click(function(){

        let Nro = $('#Nro').val(),
            IdART =$('#IdART').val(),
            IdEmpresa = $('#IdEmpresa').val(),
            FechaEdicion = $('#FechaEdicion').val(),
            FechaEEdicion = $('#FechaEEdicion').val(),
            Estado = $('#Estado').val(),
            Cpacientes = $('#Cpacientes').val(),
            Obs = $('#Obs').val(),
            IdMap = $('#Id').val();

            if($('#form-update').valid()) {
                
                $.post(updateMapa, {_token: TOKEN, Nro:Nro, IdART: IdART, IdEmpresa: IdEmpresa, FechaEdicion: FechaEdicion, FechaEEdicion: FechaEEdicion, Estado: Estado, Cpacientes: Cpacientes, Obs: Obs, Id: IdMap})
                .done(function(){
                    toastr.success('Se ha actualizado el mapa correctamente', 'Perfecto');
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                    
                })
                .fail(function(xhr){
                    toastr.error('No se ha podido guardar la actualización. Recargue la página y si el problema persiste, consulte con el administrador', 'Error');
                });
            }
             
    });


    $('#remitoObs').on('input', function() {
        updateContador();
    });

    $('#remitoObs').on('keydown', function(e) {
        blockExcedente(e);
    });

    $(document).on('click', '.entregarRemito', function(){
        let remito = $(this).data('remito');
        $('#IdRemito').text(remito);
        $('.confirmarEntrega').attr('data-id', remito);
    });

    $('#entregarModal').on('hidden.bs.modal', function(){
        $("#remitoObs").val("");
    });

    $(document).on('click', '.confirmarEntrega', function(){

        let nroRemito =  $('#IdRemito').text(),
            remitoObs =$('#remitoObs').val(),
            remitoFechaE = $('#remitoFechaE').val();

        if(remitoFechaE === '' || remitoFechaE === null){
            toastr.warning('Debe especificar una fecha de entrega', 'Atención');
            return;
        }
        $.post(saveRemitos, {_token: TOKEN, Obs: remitoObs, FechaE: remitoFechaE, Id: nroRemito})
            .done(function(){
                toastr.success('Se han registrado las fechas de entrega en los remitos correspondientes', 'Perfecto');
                setTimeout(()=>{
                    $('#remitoObs').val('');
                    $('#entregarModal').modal('hide');
                    listarRemitos(IDMAPA);
                }, 3000);
            })
            .fail(function(xhr){
                console.error(xhr);
                toastr.error('Ha ocurrido un error. Consulte con el administrador', 'Error');
            })

    });

    $(document).on('click', '.revertirEntrega', function(){
        let remito = $(this).data('remito');

        $.post(reverseRemito, {_token: TOKEN, Id: remito})
            .done(function(){   
                toastr.success('Se revertirá la entrega en unos segundos...', 'Perfecto');
                setTimeout(()=>{
                    listarRemitos(IDMAPA);
                }, 3000);  
            })
    });

    $(document).on('click', '.buscarPresMapa', function() {

        let NroPrestacion = $('#NroPrestacion').val(),
            NroRemito = $('#NroRPrestacion').val(),
            Etapa = $('#etapaPrestacion').val(),
            Estado = $('#estadoPrestacion').val(),
            mapa = MAPA;

        if(NroRemito === '' && NroPrestacion === '' && Etapa === '' && Estado ===''){
            toastr.warning('Debe utilizar algun filtro', 'Atención');
            return;
        }

        $.ajax({
            url: searchMapaPres,
            type: 'POST',
            data: {
                _token: TOKEN,
                NroPrestacion: NroPrestacion,
                NroRemito: NroRemito,
                Etapa: Etapa,
                Estado: Estado,
                mapa: mapa
            },
            success: function(response){

                let data = response.result;

                if(data === ''){
                    toastr.warning('No se han encontrado resultados', 'Atención');
                }

                $('#prestaMapa').empty();

                $.each(data, function(index, dat) {

                   let estado;
                    
                   if(dat.Finalizado === 1){
                        estado = '<span style="text-align=center" class="custom-badge verde">Finalizado</span>';
                  
                   } else if(dat.Cerrado === 1){
                        estado = '<span style="text-align=center" class="custom-badge azul">Cerrado</span>';
                   
                   } else if(dat.Cerrado === 0 && dat.Finalizado === 0) {
                        estado = '<span style="text-align=center" class="custom-badge gris">Abierto</span>';
                   }
                
                   let nuevaFecha = fecha(dat.Fecha);

                    let contenido = `
                        <tr>
                            <td>${dat.IdPrestacion}</td>
                            <td>${nuevaFecha}</td>
                            <td>${dat.Apellido} ${dat.Nombre}</td>
                            <td>${dat.NroCEE}</td>
                            <td class="text-center">
                                <span class="custom-badge ${dat.Etapa === 'Completo' ? 'verde' : 'rojo'}">${dat.Etapa}</span>
                            </td>
                            <td>${estado}</td>
                            <td class="text-center">
                                <span style="text-align=center" class="custom-badge original"><i class="${dat.eEnviado === 1 ? 'ri-check-line' : 'ri-close-line'}"></i></span>
                            </td>
                            <td class="text-center">
                                <span style="text-align=center" class="custom-badge original"><i class="${dat.Facturado === 1 ? 'ri-check-line' : 'ri-close-line'}"></i></span>
                            </td>
                            </td>
                            <td>
                                <span data-id="${dat.IdPrestacion}" data-estado="prestacion" title="${dat.Incompleto === 1 ? `Incompleto` : `Completo`}" class="cambiarEstado custom-badge ${dat.Incompleto === 1 ? `rojo` : `verde`}">
                                    <i class="ri-lightbulb-line"></i>
                                </span>
                            </td>
                            <td>
                                <button data-id="${dat.IdPrestacion}" class="btn btn-sm iconGeneral verPrestacion" title="Ver" data-bs-toggle="modal" data-bs-target="#verPrestacionModal">
                                    <i class="ri-search-eye-line"></i>
                                </button>
                                <button title="Observaciones privadas" type="button" class="iconGeneral btn btn-sm comentarioPrivado" data-bs-toggle="modal" data-bs-target="#comentarioPrivado" data-id="${dat.IdPrestacion}" data-nombre="${dat.Apellido} ${dat.Nombre}">
                                    <i class="ri-chat-quote-line"></i>
                                </button>
                            </td>
                        </tr>`;

                    $('#prestaMapa').append(contenido);
                    $('#NroPrestacion, #NroRPrestacion, #etapaPrestacion, #estadoPrestacion').val("");

                    });

                $("#listaPresMapa").fancyTable({
                    pagination: true,
                    perPage: 15,
                    searchable: false,
                    globalSearch: false,
                    sortable: false, 
                });
                
            }
        });

    });

    $(document).on('click', '.verPrestacion', function(){

        let prestacion = $(this).data('id');
        $('#IdPrestacion').text(prestacion);

        $.get(getPacienteMapa, { prestacion: prestacion })
            .done(function(data) {
                $('#nomPaciente').text(data.Nombre);
                $('#apePaciente').text(data.Apellido);
                $('#tipoDocPaciente').text(data.TipoDocumento);
                $('#documentoPaciente').text(data.Documento);
                $('.mostrarObsEstado').attr('data-id', prestacion);
                getObsEstado(prestacion);
            })
            .fail(function(xhr) {
                console.error(xhr);
            });
        
        $.get(getExamenMapa, { prestacion: prestacion})
            .done(function(response) {

                let examen = response;
                $('#examenMapa').empty();

                $.each(examen, function(index, e) {
                    
                    let arrCompleto = [3,4,5,6], efectuadoOk = arrCompleto.includes(e.CAdj);

                    let arrCerrado = [3,4,5], cerradoOk = arrCerrado.includes(e.CAdj);
                    
                    let efectorCompleto = e.ApellidoEfector + ' ' + e.NombreEfector,
                        informadorCompleto = e.ApellidoInformador + ' ' + e.NombreInformador;

                    let contenido = `
                        <tr>
                            <td>${e.NombreExamen}</td>
                            <td>${e.NombreProveedor}</td>
                            <td>
                                <span>${(e.NombreEfector === null || e.ApellidoEfector == null ? '-' : efectorCompleto )}</span>
                                <span>${(cerradoOk ? '<span style="display:block" class="custom-badge verde">Completo</span>' : '')}</span>
                            </td>
                            <td><span class="custom-badge pequeno">${arrCerrado.includes(e.CAdj) ? `cerrado`:`abierto`}</span></td>
                            <td><span class="custom-badge pequeno">${e.ExamenAdjunto === 0 ? `No lleva adjuntos` : e.ExamenAdjunto === 1 && e.adjuntados === 'sadjunto' ? `pendiente` : e.ExamenAdjunto === 1 && e.adjuntados === 'adjunto' ? `Adjuntado` : `-`}</span></td>
                            <td>
                                <span>${e.NombreInformador === null || e.ApellidoInformador == null ? '-' : informadorCompleto }</span>
                                <span>${(e.CInfo === 3 ? '<span style="display:block" class="custom-badge verde">Completo</span>' : '')}</span>
                            </td>
                            <td><span class="custom-badge pequeno">${e.CInfo === 3 ? `cerrado`: `abierto`}</span></td>
                            <td><span data-id="${e.IdItemPrestacion}" data-estado="examen" title="${e.Incompleto === 1 ? `Incompleto` : `Completo`}" class="cambiarEstado custom-badge ${e.Incompleto === 1 ? `rojo` : `verde`}"><i class="ri-lightbulb-line"></i></span></td>
                            <td>
                                <button type="button" data-id="${e.IdItemPrestacion}" class="btn btn-sm iconGeneral verItemPrestacion" title="Ver exámen"><i class="ri-search-eye-line"></i></button>
                            </td>
                        </tr>
                    `;
                    
                    $('#examenMapa').append(contenido);

                });

                $("#listaExaMapa").fancyTable({
                    pagination: true,
                    perPage: 10,
                    searchable: false,
                    globalSearch: false,
                    sortable: false, 
                });

            })
            .fail(function(xhr){
                console.error(xhr);
                toastr.error('Ha ocurrido un error, consulte con el administrador', 'Error');
            })
    });
    
    $(document).on('change', '#NroPresCerrar, #NroRemitoCerrar, #EstadoCerrar', function() {

        let prestacion = $('#NroPresCerrar').val(),
            estado = $('#EstadoCerrar').val(),
            remito = $('#NroRemitoCerrar').val();
            
        $.get(serchInCerrar, { prestacion: prestacion, remito: remito, estado: estado, mapa: MAPA})
        .done(function(response) {

            $('#cerrarMapa').empty();

            let data = response.result;

            $.each(data, function(index, d) {

                let estado;

                if(d.Cerrado === 1){
                    estado = '<span style="text-align=center" class="custom-badge verde">Cerrado</span>';
                
                } else if(d.Cerrado === 0){
                    estado = '<span style="text-align=center" class="custom-badge rojo">Abierto</span>';
                
                } 
                
                let contenido = `
                    <tr>
                        <td>${d.IdPrestacion}</td>
                        <td>${fecha(d.Fecha)}</td>
                        <td>${d.ApellidoPaciente} ${d.NombrePaciente}</td>
                        <td>${d.dni}</td>
                        <td>${estado}</td>
                        <td><button data-id="${d.IdPrestacion}" class="btn btn-sm iconGeneral verPrestacion" title="Ver"  data-bs-toggle="modal" data-bs-target="#verPrestacionModal"><i class="ri-search-eye-line"></i></button></td>
                        <td>${d.Cerrado === 1 ? '<input type="checkbox" disabled>' : `<input type="checkbox" name="Id" value="${d.IdPrestacion}" checked>`}</td>
                    </tr>
                `;

                $('#cerrarMapa').append(contenido);
                $('#NroPresCerrar, #EstadoCerrar').val('');
                
            });

            $("#listaCerrar").fancyTable({
                pagination: true,
                perPage: 10,
                searchable: false,
                globalSearch: false,
                sortable: false, 
            });
        })
        .fail(function(xhr) {
            console.error(xhr);
            toastr.error('Ha ocurrido un error, por favor, consulte con un administrador', 'Error');
        });
    });

    $(document).on('change', '#Nro, #verificador', function(){

        let nro = $(this).val(),
            verificador = $('#verificador').val();

        verificacionNro();
        if(verificador !== nro){

            $.get(checkMapa, {Nro: nro })
            .done(function(response){
    
                if(response){
    
                    let contenido = `
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong> Atención </strong> El numero de mapa ya existe en la base de datos. Se ha deshabilitado el botón de actualizar.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    
                    `;
                $('#messageMapas').html(contenido)
                $('#updateMapa').prop('disabled', true);
    
                }else{
    
                    $('#updateMapa').prop('disabled', false); 
                }
                
            });
        }

    });

    $(document).on('click', '.cerrarMapa', function(){

        let ids = [];
        $('input[name="Id"]:checked').each(function() {
            ids.push($(this).val());
        });

        let checkAll =$('#checkAll').prop('checked');

        if(ids.length === 0 && checkAll === false){
            toastr.warning('No hay ninguna prestación seleccionada para cerrar', 'Atención');
            return;
        }

        $.post(saveEstado, { ids: ids, estado: 'Cerrado', _token: TOKEN })
            .done(function(){
                toastr.success('Se han cerrado todos los mapas seleccionados','Perfecto');
                $('#cerrarMapa').empty();
                getEnMapa();
                getFinalMapa();
                getCerrarMapas();
            })
            .fail(function(xhr){
                console.error(xhr);
                toastr.error('Ha ocurrido un error. Consulte con el administrador','Error');
            })
    });

    $(document).on('change', '#NroPresFinal, #NroRemitoFinal, #estadosFinalizar', function() {

        let prestacionf = $('#NroPresFinal').val(),
            remitof = $('#NroRemitoFinal').val(),
            estadoFinalizar = $('#estadosFinalizar').val();

        $.get(searchInFinalizar, { prestacion: prestacionf, remito: remitof, estadoFinalizar: estadoFinalizar, mapa: MAPA })
            .done(function(response){

                $('#finalizarMapa').empty();
                let data = response.result;

                $.each(data, function(index, f){

                let estado;

                if(f.Finalizado === 1){
                    estado = '<span style="text-align=center" class="custom-badge verde">Finalizado</span>';
                
                } else if(f.Finalizado === 0){
                    estado = '<span style="text-align=center" class="custom-badge azul">Abierto</span>';
                } 
                
                let contenido = `
                    <tr>
                        <td>${f.NroRemito}</td>
                        <td>${fecha(f.Fecha)}</td>
                        <td>${f.IdPrestacion}</td>
                        <td>${f.ApellidoPaciente} ${f.NombrePaciente}</td>
                        <td>${f.Documento}</td>
                        <td>${estado}</td>
                        <td><button data-id="${f.IdPrestacion}" class="btn btn-sm iconGeneral verPrestacion" title="Ver"  data-bs-toggle="modal" data-bs-target="#verPrestacionModal"><i class="ri-search-eye-line"></i></button></td>
                        <td>${f.Finalizado === 1 ? '<input type="checkbox" disabled>' : `<input type="checkbox" name="Id" value="${f.IdPrestacion}" checked>`}</td>
                        
                    </tr>
                `;

                    $('#finalizarMapa').append(contenido);
                    $('#NroPresFinal, #NroRemitoFinal').val("");
                });

                $("#listaFinalizar").fancyTable({
                    pagination: true,
                    perPage: 10,
                    searchable: false,
                    globalSearch: false,
                    sortable: false, 
                });

            })
            .fail(function(xhr){
                console.error(xhr);
                toastr.error('Ha ocurrido un error. Consulte con el administrador', 'Error');
            });
    });

    $(document).on('click', '.finalizarMap', function(){

        let ids = [];
        $('input[name="Id"]:checked').each(function() {
            ids.push($(this).val());
        });

        let checkAll =$('#checkAll').prop('checked');

        if(ids.length === 0 && checkAll === false){
            toastr.warning('No hay ninguna prestación seleccionada para finalizar', 'Atención');
            return;
        }

        $.post(saveFinalizar, { ids: ids, _token: TOKEN })
            .done(function(){
                toastr.success('Se han finalizado todos los mapas seleccionados','Perfecto');
                $('#finalizarMapa').empty();
                getPrestaMapas();
                getEnMapa();
                getFinalMapa();
                getCerrarMapas();
                listarRemitos(IDMAPA);

            })
            .fail(function(xhr){
                console.error(xhr);
                toastr.error('Ha ocurrido un error. Consulte con el administrador','Error');
            })
    });

    $(document).on('click', '#buscarEnviar', function(){

        let fDesde = $('#fDesde').val(),
            fHasta = $('#fHasta').val(),
            NroPresEnviar = $('#NroPresEnviar').val(),
            eEnviadoEnviar = $('#eEnviadoEnviar').val(),
            NroPresRemito = $('#NroPresRemito').val();

        
            $.get(searchInEnviar, {desde: fDesde, hasta: fHasta, prestacion: NroPresEnviar, eEnviado: eEnviadoEnviar, mapa: MAPA, NroRemito: NroPresRemito})
                .done(function(response){

                    let data = response.result;

                    $('#eenviarMapa').empty();

                    $.each(data, function(index, en){
                        
                        let nuevaFecha = fecha(en.Fecha);;

                        let contenido = `
                            <tr>
                                <td>${en.NroRemito}</td>
                                <td>${nuevaFecha}</td>
                                <td>${en.IdPrestacion}</td>
                                <td>${en.ApellidoPaciente} ${en.NombrePaciente}</td>
                                <td>${en.Documento}</td>
                                <td><span class="custom-badge original">${(en.eEnviado === 1 ? 'eEnviado':'No eEnviado')}</span></td>
                                <td><button data-id="${en.IdPrestacion}" class="btn btn-sm iconGeneral verPrestacion" title="Ver"  data-bs-toggle="modal" data-bs-target="#verPrestacionModal"><i class="ri-search-eye-line"></i></button></td>
                                <td>${en.eEnviado === 1 ? '' : en.eEnviado === 0 && en.Finalizado === 1 && en.Cerrado === 1 && en.CInfo === 3 && [3,4,5].includes(en.CAdj) ? `<input type="checkbox" name="Id" value="${en.IdPrestacion}" checked>` : '<span class="custom-badge pequeno">Bloqueado</span>'}</td>     
                            </tr>
                        `;
                        $('#eenviarMapa').append(contenido);
                        $('#fDesde, #fHasta, #NroPresEnviar, #eEnviadoEnviar').val('');
                    });
                })
                .fail(function(xhr){

                    console.error(xhr);
                    toastr.error('Ha ocurrido un error. Consulte con el administrador', 'Error');
                });
    });

    $(document).on('click', '.saveEnviar', function(){

        let ids = [];
        $('input[name="Id"]:checked').each(function() {
            ids.push($(this).val());
        });

        let checkAll =$('#checkAll').prop('checked');

        if(ids.length === 0 && checkAll === false){
            toastr.warning('No hay ninguna prestación seleccionada para eEnviar', 'Atención');
            return;
        }

        $.post(saveEnviar, { ids: ids, _token: TOKEN })
            .done(function(){
                toastr.success('Se han eEnviado todos los mapas seleccionados','Perfecto');
                $('#eenviarMapa').empty();
                $('#eEnviarModal').modal('hide');
                getEnMapa();
                getFinalMapa();
                getCerrarMapas();
            })
            .fail(function(xhr){
                console.error(xhr);
                toastr.error('Ha ocurrido un error. Consulte con el administrador','Error');
            })
    });

    $(document).on('click', '.eEnviarDatos', function(){

        let remito = $(this).data('remito');
        $('#verIdRemito').text(remito);

    });

    $(document).on('click', '.verItemPrestacion', function(){

        let id = $(this).data('id');
        window.open(lnkItemsprestaciones.replace('__item__', id), '_blank');
    });

    $(document).on('click', '.cambiarEstado', function(){

        let id = $(this).data('id');
        let status = $(this).data('estado');
        checkEstado(id, status);
    });

    $(document).on('click', '.comentarioPrivado', function(){

        let id = $(this).data('id'), nombre = $(this).data('nombre');

        $('#mostrarIdPrestacion').text(id);
        $('#mostrarNombre').text(nombre);
    });


    $(document).on('click', '.confirmarComentarioPriv', function(){

        let comentario = $('#Comentario').val(), idprest = $('#mostrarIdPrestacion').text();

        if(comentario === ''){
            toastr.warning('La observación no puede estar vacía', 'Atención');
            return;
        }

        $.post(savePrivComent, {_token: TOKEN, Comentario: comentario, IdEntidad: idprest})
            .done(function(){

                toastr.success('Perfecto', 'Se ha generado la observación correctamente');

                setTimeout(() => {
                    $('#privadoPrestaciones').empty();
                    $('#comentarioPrivado').modal('hide');
                    $("#Comentario").val("");
                    listaComentariosPrivados(IDMAPA);
                }, 3000);
            })

    });

    $('#comentarioPrivado').on('hidden.bs.modal', function(){
        $("#Comentario").val("");
    });

    $(document).on('click', '.saveComObsEstado', function(){

    });

    function quitarDuplicados(selector) {
        let seleccion = $(selector).val();
        let countSeleccion = $(selector + " option[value='" + seleccion + "']").length;
    
        if (countSeleccion > 1) {
            $(selector + " option[value='" + seleccion + "']:gt(0)").hide();
        }
    }

    function updateContador() {
        let longitud = $('#remitoObs').val();
        let caracteres = longitud.length;
        $('#contadorRemitoObs').text(caracteres + '/' + maxCaracteres);
    }

    function blockExcedente(e) {
        let longitud = $('#remitoObs').val();
        let caracteres = longitud.length;
        if (caracteres >= maxCaracteres) {
            e.preventDefault();
        }
    }

    function getPrestaMapas(){

        $('#prestaMapa').empty();

        $.get(getPrestaciones, {mapa: MAPA })
            .done(function(presta){

                $.each(presta, function(index, d) {

                    let estado;
                    
                   if(d.Finalizado === 1){
                        estado = '<span style="text-align=center" class="custom-badge verde">Finalizado</span>';
                  
                   } else if(d.Cerrado === 1){
                        estado = '<span style="text-align=center" class="custom-badge azul">Cerrado</span>';
                   
                   } else if(d.Cerrado === 0 && d.Finalizado === 0) {
                        estado = '<span style="text-align=center" class="custom-badge gris">Abierto</span>';
                   }
                
                   let nuevaFecha = fecha(d.Fecha);

                    let contenido = `
                        <tr>
                            <td>${d.IdPrestacion}</td>
                            <td>${nuevaFecha}</td>
                            <td>${d.Apellido} ${d.Nombre}</td>
                            <td>${d.NroCEE}</td>
                            <td class="text-center">
                                <span class="custom-badge ${d.Etapa === 'Completo' ? 'verde': 'rojo'}">${d.Etapa}</span>
                            </td>
                            <td class="text-center">${estado}</td>
                            <td class="text-center">
                                <span style="text-align=center" class="custom-badge original"><i class="${d.eEnviado === 1 ? 'ri-check-line' : 'ri-close-line'}"></i></span>
                            </td>
                            <td class="text-center">
                                <span style="text-align=center" class="custom-badge original"><i class="${d.Facturado === 1 ? 'ri-check-line' : 'ri-close-line'}"></i></span>
                            </td>
                            <td><span data-id="${d.IdPrestacion}" data-estado="prestacion" title="${d.Incompleto === 1 ? `Incompleto` : `Completo`}" class="cambiarEstado custom-badge ${d.Incompleto === 1 ? `rojo` : `verde`}"><i class="ri-lightbulb-line"></i></span></td>
                            <td>
                                <button data-id="${d.IdPrestacion}" class="btn btn-sm iconGeneral verPrestacion" title="Ver"  data-bs-toggle="modal" data-bs-target="#verPrestacionModal">
                                    <i class="ri-search-eye-line"></i>
                                </button>
                                <button title="Observaciones privadas" type="button" class="iconGeneral btn btn-sm comentarioPrivado" data-bs-toggle="modal" data-bs-target="#comentarioPrivado"  data-id="${d.IdPrestacion}"data-nombre="${d.Apellido} ${d.Nombre}">
                                    <i class="ri-chat-quote-line"></i>
                                </button>
                            </td>
                        </tr>`;

                    $('#prestaMapa').append(contenido);

                    });

                    $("#listaPresMapa").fancyTable({
                        pagination: true,
                        perPage: 15,
                        searchable: false,
                        globalSearch: false,
                        sortable: false, 
                    });
            })
            .fail(function(xhr){
                toastr.error('Ha ocurrido un error. Actualice la página y si el problema persiste, consulte con el administrador', 'Error');
                console.error(xhr)
            })
    }

    function getCerrarMapas(){

        $('#cerrarMapa').empty();

        $.get(getCerrar, {mapa: MAPA})
            .done(function(response){

                let data = response.result;

                $.each(data, function(index, c) {

                    let estado;

                    if(c.Cerrado === 1){
                        estado = '<span style="text-align=center" class="custom-badge verde">Cerrado</span>';
                    
                    } else if(c.Cerrado === 0){
                        estado = '<span style="text-align=center" class="custom-badge azul">Abierto</span>';
                    
                    } 
                    
                    let contenido = `
                        <tr>
                            <td>${fecha(c.Fecha)}</td>
                            <td>${c.IdPrestacion}</td>
                            <td>${c.ApellidoPaciente} ${c.NombrePaciente}</td>
                            <td>${c.dni}</td>
                            <td>${estado}</td>
                            <td><button data-id="${c.IdPrestacion}" class="btn btn-sm iconGeneral verPrestacion" title="Ver"  data-bs-toggle="modal" data-bs-target="#verPrestacionModal"><i class="ri-search-eye-line"></i></button></td>
                            <td>${c.Cerrado === 1 ? '<input type="checkbox" disabled>' : `<input type="checkbox" name="Id" value="${c.IdPrestacion}" checked>`} </td>
                           
                        </tr>
                    `;
    
                    $('#cerrarMapa').append(contenido);        
                });

                $("#listaCerrar").fancyTable({
                    pagination: true,
                    perPage: 15,
                    searchable: false,
                    globalSearch: false,
                    sortable: false, 
                });
                
            })
            .fail(function(xhr){
                console.error(xhr);
                toastr.error('Ha ocurrido un error. Actualice la página y si el problema persiste, consulte al administrador', 'Error');
            });
    }

    function getFinalMapa(){

        $('#cerrarMapa').empty();

        $.get(getFMapa, {mapa: MAPA})
        .done(function(response){

            $('#finalizarMapa').empty();
            let data = response.result;

            $.each(data, function(index, f){

            let estado;

            if(f.Finalizado === 1){
                estado = '<span style="text-align=center" class="custom-badge verde">Finalizado</span>';
            
            } else if(f.Finalizado === 0){
                estado = '<span style="text-align=center" class="custom-badge azul">Abierto</span>';
            } 
            
            let contenido = `
                <tr>
                    <td>${f.NroRemito}</td>
                    <td>${fecha(f.Fecha)}</td>
                    <td>${f.IdPrestacion}</td>
                    <td>${f.ApellidoPaciente} ${f.NombrePaciente}</td>
                    <td>${f.Documento}</td>
                    <td>${estado}</td>
                    <td><button data-id="${f.IdPrestacion}" class="btn btn-sm iconGeneral verPrestacion" title="Ver"  data-bs-toggle="modal" data-bs-target="#verPrestacionModal"><i class="ri-search-eye-line"></i></button></td>
                    <td>${f.Finalizado === 1 ? `<input type="checkbox" disabled>` : `<input type="checkbox" name="Id" value="${f.IdPrestacion}" checked>`}</td>
                </tr>
            `;

                $('#finalizarMapa').append(contenido);
                $('#NroPresFinal, #NroRemitoFinal').val("");
                
            });

            $("#listaFinalizar").fancyTable({
                pagination: true,
                perPage: 10,
                searchable: false,
                globalSearch: false,
                sortable: false, 
            });

        })
        .fail(function(xhr){
            console.error(xhr);
            toastr.error('Ha ocurrido un error. Actualice la página y si el problema persiste, consulte con el administrador', 'Error');
        })
    }

    function verificacionNro(){
        let nro = $('#Nro').val(),
            verificador = $('#verificador').val();

        if(nro === verificador){
            $('#updateMapa').prop('disabled', false);
        }
    }

    function getEnMapa(){

        $('#eenviarMapa').empty();

        $.get(enviarMapa, { mapa: MAPA})
            .done(function(response){

                let data = response.result;

                $.each(data, function(index, en){
                        
                    let tipo = (en.TipoPrestacion ? '<span class="custom-badge nuevoAzulInverso">' + en.TipoPrestacion +'</span>' : '');

                    let nuevaFecha = fecha(en.Fecha);

                    let contenido = `
                        <tr>
                            <td>${en.NroRemito}</td>
                            <td>${nuevaFecha}</td>
                            <td>${en.IdPrestacion}</td>
                            <td>${en.ApellidoPaciente} ${en.NombrePaciente}</td>
                            <td>${en.Documento}</td>
                        <td><span class="custom-badge original">${(en.eEnviado === 1 ? 'eEnviado':'No eEnviado')}</span></td>
                            <td><button data-id="${en.IdPrestacion}" class="btn btn-sm iconGeneral verPrestacion" title="Ver"  data-bs-toggle="modal" data-bs-target="#verPrestacionModal"><i class="ri-search-eye-line"></i></button></td>
                            <td>${en.eEnviado === 1 ? '' : en.eEnviado === 0 && en.Finalizado === 1 && en.Cerrado === 1 && en.CInfo === 3 && [3,4,5].includes(en.CAdj) ? `<input type="checkbox" name="Id" value="${en.IdPrestacion}" checked>` : '<span class="custom-badge pequeno">Bloqueado</span>'}</td> 
                        </tr>
                    `;
                    $('#eenviarMapa').append(contenido);
                    
                });

            })
            .fail(function(xhr){
                console.error(xhr);
                toastr.error('Ha ocurrido un error. Actualice la página y el caso de que el problema persista, consulte con el administrador.', 'Error');
            })
    }

    function checkEstado(id, who) {
        let btn = $('.cambiarEstado[data-id="' + id + '"]');
        btn.prop('disabled', true);
    
        let arr = { _token: TOKEN, Id: id, estado: who };

        $.post(changeEstado, arr)
            .done(async function(response) {
                let data = await response.result;

                btn.prop('title', data.Incompleto === 1 ? 'Incompleto' : 'Completo')
                   .removeClass()
                   .addClass('cambiarEstado custom-badge ' + (data.Incompleto === 1 ? 'rojo' : 'verde'));
            })
            .always(function() {
                btn.prop('disabled', false);
            });
    }

    function listaComentariosPrivados(idmapa){

        $('#privadoPrestaciones').empty();

        $.get(privateComment, {Id: idmapa})
            .done(async function(response){

                let data = await response.result;

                $.each(data, function(index, d){
 
                    let contenido =  `
                        <tr>
                            <td>${fecha(d.Fecha)}</td>
                            <td>${d.IdEntidad}</td>
                            <td>${d.IdUsuario}</td>
                            <td>${d.nombre_perfil}</td>
                            <td>${d.Comentario}</td>
                        </tr>
                    `;

                    $('#privadoPrestaciones').append(contenido);
                });

                $("#lstPrivPrestaciones").fancyTable({
                    pagination: true,
                    perPage: 15,
                    searchable: false,
                    globalSearch: false,
                    sortable: false, 
                });
            })
    }

    function fecha(fe){

        if(fe === '' || fe === undefined) return;

        let partesFecha = fe.split("-");
        return  partesFecha[2] + "/" + partesFecha[1] + "/" + partesFecha[0];
    }

    function listarRemitos(idmapa){

        $('#remitoMapa').empty();

        $.get(getRemito, {Id: idmapa})
            .done(function(response){

                let data = response.result;

                $.each(data, function(index, r){

                    let contenido = `
                        <tr>
                            <td>${r.NroCEE}</td>
                            <td>${r.contadorRemitos}</td>
                            <td>
                                <span style="text-align=center" class="custom-badge ${r.Entregado === 1 ? 'verde':'rojo'}">${r.Entregado === 1 ? 'Entregado':'Sin Entregar'}</span>
                            </td>
                            <td>${r.constanciases && r.constanciases.length > 0 && r.constanciases[0].Obs !== undefined ? r.constanciases[0].Obs : '-'}</td>
                            <td>
                                <button data-remito="${r.NroCEE}" type="button" class="btn botonGeneral ${r.Entregado === 1 ? 'revertirEntrega' : 'entregarRemito'}" ${r.Entregado === 1 ? '' : 'data-bs-toggle="modal" data-bs-target="#entregarModal"'}>${r.Entregado === 1 ? 'Revertir Entrega':'Entregar'}</button> 
                            </td>
                            <td>
                                <button data-remito="${r.NroCEE}" type="button" class="pdf btn iconGeneral" title="Generar reporte en Pdf">
                                    <i class="ri-file-pdf-line"></i>
                                </button>
                                <button data-remito="${r.NroCEE }" type="button" class="excel btn iconGeneral" title="Generar reporte en Excel">
                                    <i class="ri-file-excel-line"></i>
                                </button>
                            </td>
                        </tr>
                    `;

                    $('#remitoMapa').append(contenido);
                });

                $("#listaRemito").fancyTable({
                    pagination: true,
                    perPage: 15,
                    searchable: false,
                    globalSearch: false,
                    sortable: false, 
                });
            })
    }

    function createFile(tipo, array){
        let filePath = array,
            pattern = /storage(.*)/,
            match = filePath.match(pattern),
            path = match ? match[1] : '';

        let url = new URL(location.href),
            baseUrl = url.origin,
            fullPath = baseUrl + '/cmit/storage' + path;

        let link = document.createElement('a');
        link.href = fullPath;
        link.download = tipo === 'pdf' ? "reporte.pdf" : "reporte.csv";
        link.style.display = 'none';

        document.body.appendChild(link);
        link.click();
        setTimeout(function() {
            document.body.removeChild(link);
        }, 100);
    }

    function getObsEstado(id){

        $.get(getComentarioPres, {Id: id})
            .done(async function(response){

                let rs = await response.comentario;

                $('.ComObsEstado').val(rs);

            })
    }

});