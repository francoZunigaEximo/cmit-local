$(function() { 

    let hoy = new Date().toLocaleDateString('en-CA'), 
        maxCaracteres = 100;

    quitarDuplicados("#Estado");
    getPrestaMapas();
    verificacionNro();
    getCerrarMapas();
    getFinalMapa();
    getEnMapa();
    listaComentariosPrivados(IDMAPA, 'prestaciones','mapa');
    listaComentariosPrivados(IDMAPA, 'cerrado','mapa');
    listarRemitos(IDMAPA);

    $('#remitoFechaE').val(hoy);
    $('.comentarioObsEstado').hide();
    $('#EstadoCerrar').val('abierto');

    $('#verPrestacionModal').on('shown.bs.modal', function () {
        $('.comentarioObsEstado').hide();
        $(document).off('click', '.mostrarObsEstado, .cerrarObsEstado').on('click', '.mostrarObsEstado, .cerrarObsEstado', function(){

        $(this).hasClass('mostrarObsEstado') ? $('.comentarioObsEstado').show() : $('.comentarioObsEstado').hide();

        });
    });


    $('#verPrestacionModa').on('hide.bs.modal', function () {
        $(".ComObsEstado").val("");
        $('.comentarioObsEstado').hide();
    });
    
    //Exportar
    $(document).on('click', '.excel, .pdf', function(e){
        e.preventDefault();

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

        if (ids.length === 0) {
            toastr.warning('Debes seleccionar al menos un mapa para exportar.');
            return;
        }
            
        swal({
            title: '¿Estás seguro de que deseas generar el reporte?',
            icon: 'warning',
            buttons: ['Cancelar', 'Generar']
        }).then((confirmar) => {
            if(confirmar){
                $.ajax({
                    url: fileExport,
                    type: "GET",
                    data: arr[tipo].datos,
                    success: function(response) {
                        createFile(arr[tipo].archivo, response.filePath, generarCodigoAleatorio + "_reporte");
                        toastr.success(response.msg);
                    },
                    error: function(jqXHR) {
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return;
                    }
                });
            }
        });
    });

    $(document).on('click', '#updateMapa', function(e) {
        e.preventDefault();

        let Nro = $('#Nro').val(),
            IdART =$('#IdART').val(),
            IdEmpresa = $('#IdEmpresa').val(),
            FechaEdicion = $('#FechaEdicion').val(),
            FechaEEdicion = $('#FechaEEdicion').val(),
            Estado = $('#Estado').val(),
            Cpacientes = $('#Cpacientes').val(),
            Cmapeados = $('#Cmapeados').val(),
            Obs = $('#Obs').val(),
            IdMap = $('#Id').val();
        
            if($('#form-update').valid()) {
                preloader('off');
                $.post(updateMapa, {_token: TOKEN, Nro:Nro, IdART: IdART, IdEmpresa: IdEmpresa, FechaEdicion: FechaEdicion, FechaEEdicion: FechaEEdicion, Estado: Estado, Cpacientes: Cpacientes, Obs: Obs, Id: IdMap, Cmapeados: Cmapeados})
                .done(function(response){
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
                });
            }
             
    });

    $('#remitoObs').on('input', function() {
        updateContador();
    });

    $('#remitoObs').on('keydown', function(e) {
        blockExcedente(e);
    });

    $(document).on('click', '.entregarRemito', function(e){
        e.preventDefault();

        let remito = $(this).data('remito');
        $('#IdRemito').text(remito);
        $('.confirmarEntrega').attr('data-id', remito);
    });

    $('#entregarModal').on('hidden.bs.modal', function(){
        $("#remitoObs").val("");
    });

    $(document).on('click', '.confirmarEntrega', function(e){
        e.preventDefault();
    
        $(this).prop('disabled', true);
    
        let nroRemito =  $('#IdRemito').text(),
            remitoObs = $('#remitoObs').val(),
            remitoFechaE = $('#remitoFechaE').val();

        if(['', null, 0].includes(nroRemito)) {
            toastr.warning('No se puede realizar la entrega porque no posee numero de remito. Debe Finalizar la Prestación.');
            $(this).prop('disabled', false);
            return;
        }
    
        if(['', null].includes(remitoFechaE)) {
            toastr.warning('Debe especificar una fecha de entrega');
            $(this).prop('disabled', false);
            return;
        }

        if(['', null].includes(remitoObs)){
            toastr.warning('Debe escribir una observación');
            $(this).prop('disabled', false);
            return;
        }

        swal({
            title: "¿Está seguro que desea registrar la entrega?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                preloader('on');
                $.post(saveRemitos, {_token: TOKEN, Obs: remitoObs, FechaE: remitoFechaE, Id: nroRemito})
                .done(function(response){
                    preloader('off');
                    toastr.success(response.msg);
                    setTimeout(()=>{
                        $('#remitoObs').val('');
                        $('#entregarModal').modal('hide');
                        listarRemitos(IDMAPA);
                        
                        $('.confirmarEntrega').prop('disabled', false);
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
    
    
    $(document).on('click', '.revertirEntrega', function(e){
        e.preventDefault();

        let remito = $(this).data('remito');
        $(this).prop('disabled', true);

        swal({
            title: "¿Está seguro que desea revertir la entrega?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                preloader('on');
                $.post(reverseRemito, {_token: TOKEN, Id: remito})
                    .done(function(response){
                        preloader('off');   
                        toastr.success(response.msg);
                        setTimeout(()=>{
                            listarRemitos(IDMAPA);
                            $('.revertirEntrega').prop('disabled', false);
                        }, 3000);  
                    })
                    .fail(function(jqXHR){
                        preloader('off');            
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return;  
                    });
            }else{
                $(this).prop('disabled', false);
            }
        })
    });

    $(document).on('click', '.buscarPresMapa', function() {

        let NroPrestacion = $('#NroPrestacion').val(),
            NroRemito = $('#NroRPrestacion').val(),
            Etapa = $('#etapaPrestacion').val(),
            Estado = $('#estadoPrestacion').val(),
            mapa = MAPA,
            condiciones = [NroRemito, NroPrestacion, Etapa, Estado];

        if(condiciones.every(condicion => condicion === '')){
            toastr.warning('Debe utilizar algun filtro', 'Atención');
            return;
        }

        preloader('on');
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
                preloader('off');
                let data = response.result;

                if(data === ''){
                    toastr.warning('No se han encontrado resultados', 'Atención');
                }

                $('#prestaMapa').empty();

                $.each(data, function(index, dat) {

                    let contenido = `
                        <tr>
                            <td>${dat.IdPrestacion}</td>
                            <td>${fecha(dat.Fecha)}</td>
                            <td>${dat.Apellido} ${dat.Nombre}</td>
                            <td>${dat.NroCEE}</td>
                            <td class="text-center">
                                <span class="custom-badge ${dat.Etapa === 'Completo' ? 'verde' : 'rojo'}">${dat.Etapa}</span>
                            </td>
                            <td><span style="text-align=center" class="custom-badge pequeno">${dat.estado}</span></td>
                            <td class="text-center">
                                ${dat.eEnviado === 1 ? `<span style="text-align=center" class="btn btn-sm iconGeneral"><i class="ri-check-line"></i></span>`: `` }
                            </td>
                            <td class="text-center">
                                ${dat.Facturado === 1 ? `<span style="text-align=center" class="btn btn-sm iconGeneral"><i class="ri-check-line"></i></span>` : ``}
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
                                <button title="Observaciones privadas" type="button" class="iconGeneral btn btn-sm comentarioPrivado" data-bs-toggle="modal" data-bs-target="#comentarioPrivado" data-id="${dat.IdPrestacion}" data-nombre="${dat.Apellido} ${dat.Nombre}" data-fase="prestaciones">
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
                $('.eEstudioModal').attr('data-id', prestacion);
                getObsEstado(prestacion);
            })
            .fail(function(jqXHR) {
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            });
            
        preloader('on');
        $.get(getExamenMapa, { prestacion: prestacion})
            .done(function(response) {
                preloader('off');
                let examen = response;
                $('#examenMapa').empty();
                
                $.each(examen, function(index, e) {
                    
                    let arrCompleto = [3,4,5,6], efectuadoOk = arrCompleto.includes(e.CAdj), arrCerrado = [3,4,5], cerradoOk = arrCerrado.includes(e.CAdj);
                    
                    let efectorCompleto = e.ApellidoEfector + ' ' + e.NombreEfector,
                        informadorCompleto = e.ApellidoInformador + ' ' + e.NombreInformador;

                    let contenido = `
                        <tr>
                            <td>
                                <span>${e.NombreExamen}</span>
                                <span>${(e.Anulado === 1 ? '<span style="display:block" class="custom-badge rojo">Bloqueado</span>' : '')}</span>
                            </td>
                            <td title="${e.NombreProveedor}">${e.NombreProveedor}</td>
                            <td>
                                <span>${e.NombreEfector === null || e.ApellidoEfector == null ? '-' : efectorCompleto}</span>
                                <span>${(cerradoOk ? '<span style="display:block" class="custom-badge verde">Completo</span>' : '')}</span>
                            </td>
                            <td><span class="custom-badge pequeno">${arrCerrado.includes(e.CAdj) ? `cerrado`:`abierto`}</span></td>
                            <td><span class="custom-badge pequeno">${e.ExamenAdjunto === 0 ? `No lleva adjuntos` : e.ExamenAdjunto === 1 && e.adjuntados === 'sadjunto' ? `pendiente` : e.ExamenAdjunto === 1 && e.adjuntados === 'adjunto' ? `Adjuntado` : `-`}</span></td>
                            <td>
                                <span>${e.Informe === 0 ? 'Sin informador' : informadorCompleto }</span>
                                <span>${(e.Informe === 0 ? '' : e.CInfo === 3 ? '<span style="display:block" class="custom-badge verde">Completo</span>' : '')}</span>
                            </td>
                            <td>${e.Informe === 0 ? '' : `<span class="custom-badge pequeno">${e.CInfo === 3 ? `cerrado`: `abierto`}</span>`}</td>
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
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            })
    });
    
    $(document).on('change', '#NroPresCerrar, #NroRemitoCerrar, #EstadoCerrar', function() {

        let prestacion = $('#NroPresCerrar').val(),
            estado = $('#EstadoCerrar').val(),
            remito = $('#NroRemitoCerrar').val();
        preloader('on');

        $.get(serchInCerrar, { prestacion: prestacion, remito: remito, estado: estado, mapa: MAPA})
        .done(function(response) {
            preloader('off');
            $('#cerrarMapa').empty();

            let data = response.result;

            $.each(data, function(index, d) {
                
                let contenido = `
                    <tr>
                        <td>${d.IdPrestacion}</td>
                        <td>${fecha(d.Fecha)}</td>
                        <td>${d.ApellidoPaciente} ${d.NombrePaciente}</td>
                        <td>${d.dni}</td>
                        <td><span style="text-align=center" class="custom-badge pequeno">${d.Cerrado === 1 ? `Cerrado` : `Abierto`}</span></td>
                        <td>
                            <button data-id="${d.IdPrestacion}" class="btn btn-sm iconGeneral verPrestacion" title="Ver"  data-bs-toggle="modal" data-bs-target="#verPrestacionModal"><i class="ri-search-eye-line"></i></button>
                            <button title="Observaciones privadas" type="button" class="iconGeneral btn btn-sm comentarioPrivado" data-bs-toggle="modal" data-bs-target="#comentarioPrivado" data-id="${d.IdPrestacion}" data-nombre="${d.ApellidoPaciente} ${d.NombrePaciente}" data-fase="cerrar">
                                <i class="ri-chat-quote-line"></i>
                            </button>
                        </td>
                        <td>${d.Cerrado === 1 ? '<input type="checkbox" disabled>' : `<input type="checkbox" name="Id_cerrar" value="${d.IdPrestacion}" checked>`}</td>
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
        .fail(function(jqXHR) {
            preloader('off');
            let errorData = JSON.parse(jqXHR.responseText);            
            checkError(jqXHR.status, errorData.msg);
            return;
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
        $('input[name="Id_cerrar"]:checked').each(function() {
            ids.push($(this).val());
        });

        let checkAll =$('#checkAllCerrar').prop('checked');

        if(ids.length === 0 && checkAll === false){
            toastr.warning('No hay ninguna prestación seleccionada para cerrar', 'Atención');
            return;
        }
        preloader('on');
        $.post(saveEstado, { ids: ids, estado: 'Cerrado', _token: TOKEN })
            .done(function(response){
                preloader('off');
                response.forEach(function(data){
                    tipoToastr = data.estado === 'success' ? 'success' : 'warning';
                    toastr[tipoToastr](data.msg, {timeOut: 10000});
                });

                $('#cerrarMapa').empty();
                getEnMapa();
                getFinalMapa();
                getCerrarMapas();
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            })
    });

    $(document).on('change', '#NroPresFinal, #NroRemitoFinal, #estadosFinalizar', function() {

        let prestacionf = $('#NroPresFinal').val(),
            remitof = $('#NroRemitoFinal').val(),
            estadoFinalizar = $('#estadosFinalizar').val();
        preloader('on');
        $.get(searchInFinalizar, { prestacion: prestacionf, remito: remitof, estadoFinalizar: estadoFinalizar, mapa: MAPA })
            .done(function(response){
                preloader('off');
                $('#finalizarMapa').empty();
                let data = response.result;

                $.each(data, function(index, f){
                
                let contenido = `
                    <tr>
                        <td>${f.NroRemito == 0 ? '-' : f.NroRemito}</td>
                        <td>${fecha(f.Fecha)}</td>
                        <td>${f.IdPrestacion}</td>
                        <td>${f.ApellidoPaciente} ${f.NombrePaciente}</td>
                        <td>${f.Documento}</td>
                        <td><span style="text-align=center" class="custom-badge pequeno">${f.Finalizado === 1 ? `Finalizado` : `Cerrado`}</span></td>
                        <td><button data-id="${f.IdPrestacion}" class="btn btn-sm iconGeneral verPrestacion" title="Ver"  data-bs-toggle="modal" data-bs-target="#verPrestacionModal"><i class="ri-search-eye-line"></i></button></td>
                        <td>${f.Finalizado === 1 ? '<input type="checkbox" disabled>' : `<input type="checkbox" name="Id_finalizar" value="${f.IdPrestacion}" checked>`}</td>
                        
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
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            });
    });

    $(document).on('click', '.finalizarMap', function(e){
        e.preventDefault();

        let ids = [];
        $('input[name="Id_finalizar"]:checked').each(function() {
            ids.push($(this).val());
        });

        let checkAll =$('#checkAllFinalizar').prop('checked');

        if(ids.length === 0 && checkAll === false){
            toastr.warning('No hay ninguna prestación seleccionada para finalizar', 'Atención');
            return;
        }
        preloader('on');
        $.post(saveFinalizar, { ids: ids, _token: TOKEN })
            .done(function(response){
                preloader('off');
                response.forEach(function(data){

                    let tipoToastr = data.estado === 'success' ? 'success' : 'warning';
                    toastr[tipoToastr](data.msg, {timeOut: 10000});
                });

                $('#finalizarMapa').empty();
                getPrestaMapas();
                getEnMapa();
                getFinalMapa();
                getCerrarMapas();
                listarRemitos(IDMAPA);

            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            })
    });

    $(document).on('click', '#buscarEnviar', function(e){

        e.preventDefault();

        let fDesde = $('#fDesde').val(),
            fHasta = $('#fHasta').val(),
            NroPresEnviar = $('#NroPresEnviar').val(),
            eEnviadoEnviar = $('#eEnviadoEnviar').val(),
            NroPresRemito = $('#NroPresRemito').val();

        let arr = [fDesde, fHasta, NroPresEnviar, eEnviadoEnviar, NroPresRemito];

        if(arr.every(condicion => condicion === '')) {
            toastr.warning("No hay un filtro seleccionado");
            return;
        }
        
        preloader('on');
        $.get(searchInEnviar, {desde: fDesde, hasta: fHasta, prestacion: NroPresEnviar, eEnviado: eEnviadoEnviar, mapa: MAPA, NroRemito: NroPresRemito})
            .done(function(response){

                let data = response.result;
                preloader('off');
                $('#eenviarMapa').empty();

                $.each(data, function(index, en){
                    
                    let contenido = `
                        <tr>
                            <td>${en.NroRemito}</td>
                            <td>${fechaNow(en.Fecha,"/",1)}</td>
                            <td>${en.IdPrestacion} ${en.EmpresaSinEnvio === 1 ? `<i title="La empresa no tiene habilitado los envios de Emails" class="ri-mail-forbid-line rojo"></i>` : ``} ${en.ArtSinEnvio === 1 ? `<i title="La ART no tiene habilitado los envios de Emails" class="ri-mail-forbid-line rojo"></i>` : ``}</td>
                            <td>${en.ApellidoPaciente} ${en.NombrePaciente}</td>
                            <td>${en.Documento}</td>
                            <td><span class="custom-badge pequeno">${(en.eEnviado === 1 ? 'eEnviado':'No eEnviado')}</span></td>
                            <td>
                                <button data-id="${en.IdPrestacion}" class="btn btn-sm iconGeneral verPrestacion" title="Ver"  data-bs-toggle="modal" data-bs-target="#verPrestacionModal"><i class="ri-search-eye-line"></i></button>
                                <button data-id="${en.IdPrestacion}" class="btn btn-sm iconGeneral vistaPreviaEnviar" title="Vista previa"><i class="ri-file-search-line"></i></button>
                            </td>
                            <td>${en.eEnviado === 1 ? '' : en.eEnviado === 0 && en.Finalizado === 1 && en.Cerrado === 1 && en.Etapa === 'Completo' ? `<input type="checkbox" name="Id_enviar" value="${en.IdPrestacion}" checked>` : '<span class="custom-badge pequeno">Bloqueado</span>'}</td>     
                        </tr>
                    `;
                    $('#eenviarMapa').append(contenido);
                    
                });

                $("#listaeenviar").fancyTable({
                    pagination: true,
                    perPage: 10,
                    searchable: false,
                    globalSearch: false,
                    sortable: false, 
                });

                $('#fDesde, #fHasta, #NroPresEnviar, #NroPresRemito').val('');
                $('#eEnviadoEnviar option[value=""]').prop('selected', true);
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            });
    });

    $(document).on('click', '.eArt, .eEmpresa', function(e){

        e.preventDefault();

        $('.eTipo').val($(this).hasClass('eArt') ? "eArt" : "eEmpresa");
    });

    $(document).on('click', '.saveEnviar', function(e){
        e.preventDefault();

        let ids = [];
        $('input[name="Id_enviar"]:checked').each(function() {
            ids.push($(this).val());
        });

        let checkAll =$('#checkAllEnviar').prop('checked');

        if (ids.length === 0 && checkAll === false) {
            toastr.warning('No hay ninguna prestación seleccionada para eEnviar');
            return;
        }

        let enviarMail= $('.enviarMail').prop('checked'),
            exportarInforme = $('.exportarInforme').prop('checked'),
            eTipo = $('.eTipo').val();
            condiciones = [enviarMail, exportarInforme],
            contarTrue = condiciones.filter(condicion => condicion === true).length;

        if (contarTrue !== 1){
            toastr.warning("Debe seleccionar una opción");
            return;
        }

        if (condiciones.every(condicion => condicion === false)) {
            toastr.warning("Debe seleccionar una de las opciones de envío");
            return;
        }

        preloader('on');
        $.post(saveEnviar, { ids: ids, _token: TOKEN, eTipo: eTipo, exportarInforme: exportarInforme,  enviarMail: enviarMail})
            .done(function(response){
                preloader('off');
               
                $.each(response, function(index, r){
                    if(r.icon === 'art-impresion' || r.icon === 'empresa-impresion') {
                        toastr.success(r.msg);
                        createFile("pdf", r.filePath, r.name);
                    
                    }else if(r.icon === 'art-email') {
                        toastr.success(r.msg);
                    
                    }else if(r.icon === 'empresa-email') {
                        toastr.success(r.msg);
                    }
                });
               
                setTimeout(()=>{
                    $('#eenviarMapa').empty();
                    $('#eEnviarModal').modal('hide');
                    getEnMapa();
                    getFinalMapa();
                    getCerrarMapas();
                    $('.saveEnviar').prop('disabled', false);
                }, 3000);
                
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            });
    });

    $(document).on('click', '.verItemPrestacion', function(){

        let id = $(this).data('id');
        window.open(lnkItemsprestaciones.replace('__item__', id), '_blank');
    });

    $(document).on('click', '.cambiarEstado', function(e){

        e.preventDefault();
        preloader('on');
        let id = $(this).data('id');
        let status = $(this).data('estado');

        checkEstado(id, status);
        preloader('off');
    });

    $(document).on('click', '.comentarioPrivado', function(){

        let id = $(this).data('id'), nombre = $(this).data('nombre'), fase = $(this).data('fase');

        $('#mostrarIdPrestacion').text(id);
        $('#mostrarNombre').text(nombre);
        $('#fase').text(fase);
    });

    $(document).on('click', '.confirmarComentarioPriv', function(e){
        e.preventDefault();

        let comentario = $('#Comentario').val(), idprest = $('#mostrarIdPrestacion').text(), fase = $('#fase').text();

        let lstFases = {
            prestaciones: 2,
            cerrar: 3
        }

        if(comentario === ''){
            toastr.warning('La observación no puede estar vacía', 'Atención');
            return;
        }
        preloader('on');
        $.post(savePrivComent, {_token: TOKEN, Comentario: comentario, IdEntidad: idprest, obsfasesid: lstFases[fase]})
            .done(function(response){
                preloader('off');
                toastr.success(response.msg);

                setTimeout(() => {
                    $('#privadoPrestaciones, #privadoCerrar').empty();
                    $('#comentarioPrivado').modal('hide');
                    $("#Comentario").val("");
                    listaComentariosPrivados(IDMAPA, 'prestaciones','mapa');
                    listaComentariosPrivados(IDMAPA, 'cerrado','mapa');
                }, 3000);
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            });

    });

    $(document).off('click','.eEstudioModal').on('click', '.eEstudioModal', function(e){
        e.preventDefault();

        let id = $('#IdPrestacion').text()

        if ([null, '', undefined, 0].includes(id)) return;
        preloader('on');
        $.get(vistaPrevia, {Id: id})
            .done(function(response){
                preloader('off');
                toastr.success("Generando vista previa");
                window.open(response, '_blank');
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            });
    });

    $('#comentarioPrivado').on('hidden.bs.modal', function(){
        $("#Comentario, .ComObsEstado").val("");
    });

    $('#closeModalButton').click(function() {
        $('.ComObsEstado').val('');
    });

    $(document).off('click', '.saveComObsEstado').on('click', '.saveComObsEstado', function(){
        let prestacion = $('#IdPrestacion').text(), observacion = $('.ComObsEstado').val();

        if(observacion === ''){
            toastr.warning('Debe escribir un observación de la prestación');
            return;
        }
        preloader('on');
        $.post(setComentarioPres, {_token: TOKEN, Id: prestacion, observacion: observacion})
            .done(function(response){
                preloader('off');
                toastr.success(response.msg);
                setTimeout(()=>{
                    $('.comentarioObsEstado').hide();
                }, 2000);
            })
            fail(function(jqXHR) {
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return; 
            });
    });

    $('#checkAllCerrar').on('click', function() {

        $('input[type="checkbox"][name="Id_cerrar"]:not(#checkAllCerrar)').prop('checked', this.checked);
    });

    $('#checkAllFinalizar').on('click', function() {

        $('input[type="checkbox"][name="Id_finalizar"]:not(#checkAllFinalizar)').prop('checked', this.checked);
    });

    $('#checkAllEnviar').on('click', function() {

        $('input[type="checkbox"][name="Id_enviar"]:not(#checkAllEnviar)').prop('checked', this.checked);
    });

    $(document).on('click', '.reiniciarPresMapa', function(e){
        e.preventDefault();
        preloader('on');
        getPrestaMapas();
        preloader('off');
    });

    $(document).on('click', '.multiVolver', function(e) {
        e.preventDefault();
        window.history.back();
    });


    $(document).on('click', '.vistaPreviaEnviar', function(e){
        e.preventDefault();

        let id = $(this).data('id');
        if([0, null, ''].includes(id)) return;

        preloader('on');
        $.get(vistaPrevia, {Id: id})
            .done(function(response){
                preloader('off');
                toastr.success("Generando vista previa");
                window.open(response, '_blank');
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            });
    });

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
        preloader('on');

        $.get(getPrestaciones, {mapa: MAPA })
            .done(function(presta){
                preloader('off');
                $.each(presta, function(index, d) {

                   let estado = (d.Cerrado === 1 && d.Finalizado === 1) ? 'Finalizado' : (d.Cerrado === 1 && d.Finalizado === 0) ? 'Cerrado' : (d.Cerrado === 0 && d.Finalizado === 0) ? 'Abierto' : '-';

                    let contenido = `
                        <tr>
                            <td>${d.IdPrestacion}</td>
                            <td>${fecha(d.Fecha)}</td>
                            <td>${d.Apellido} ${d.Nombre}</td>
                            <td>${d.NroCEE}</td>
                            <td class="text-center">
                                <span class="custom-badge ${d.Etapa === 'Completo' ? 'verde': 'rojo'}">${d.Etapa}</span>
                            </td>
                            <td class="text-center"><span style="text-align=center" class="custom-badge pequeno">${estado}</span></td>
                            <td class="text-center">
                                ${d.eEnviado === 1 ? `<span style="text-align=center" class="btn btn-sm iconGeneral"><i class="ri-check-line"></i></span>`: ``}
                            </td>
                            <td class="text-center">
                                ${d.Facturado === 1 ? `<span style="text-align=center" class="btn btn-sm iconGeneral"><i class=" ri-check-line"></i></span>` : ``}
                            </td>
                            <td><span data-id="${d.IdPrestacion}" data-estado="prestacion" title="${d.Incompleto === 1 ? `Incompleto` : `Completo`}" class="cambiarEstado custom-badge ${d.Incompleto === 1 ? `rojo` : `verde`}"><i class="ri-lightbulb-line"></i></span></td>
                            <td>
                                <button data-id="${d.IdPrestacion}" class="btn btn-sm iconGeneral verPrestacion" title="Ver"  data-bs-toggle="modal" data-bs-target="#verPrestacionModal">
                                    <i class="ri-search-eye-line"></i>
                                </button>
                                <button title="Observaciones privadas" type="button" class="iconGeneral btn btn-sm comentarioPrivado" data-bs-toggle="modal" data-bs-target="#comentarioPrivado"  data-id="${d.IdPrestacion}"data-nombre="${d.Apellido} ${d.Nombre}" data-fase="prestaciones">
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
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return; 
            })
    }

    function getCerrarMapas(){

        $('#cerrarMapa').empty();
        preloader('on');
        $.get(getCerrar, {mapa: MAPA})
            .done(function(response){
                preloader('off');
                let data = response.result;

                $.each(data, function(index, c) {
 
                    let contenido = `
                        <tr>
                            <td>${fecha(c.Fecha)}</td>
                            <td>${c.IdPrestacion}</td>
                            <td>${c.ApellidoPaciente} ${c.NombrePaciente}</td>
                            <td>${c.dni}</td>
                            <td><span style="text-align=center" class="custom-badge pequeno">${c.Cerrado === 1 ? `Cerrado`: `Abierto`}</span></td>
                            <td>
                                <button data-id="${c.IdPrestacion}" class="btn btn-sm iconGeneral verPrestacion" title="Ver"  data-bs-toggle="modal" data-bs-target="#verPrestacionModal"><i class="ri-search-eye-line"></i></button>
                                <button title="Observaciones privadas" type="button" class="iconGeneral btn btn-sm comentarioPrivado" data-bs-toggle="modal" data-bs-target="#comentarioPrivado" data-id="${c.IdPrestacion}" data-nombre="${c.ApellidoPaciente} ${c.NombrePaciente}" data-fase="cerrar">
                                <i class="ri-chat-quote-line"></i>
                            </button>
                            </td>
                            <td>${c.Cerrado === 1 ? '<input type="checkbox" disabled>' : `<input type="checkbox" name="Id_cerrar" value="${c.IdPrestacion}" checked>`} </td>
                           
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
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return; 
            });
    }

    function getFinalMapa(){

        $('#cerrarMapa').empty();
        preloader('on');

        $.get(getFMapa, {mapa: MAPA})
        .done(function(response){
            preloader('off');
            $('#finalizarMapa').empty();
            let data = response.result;

            $.each(data, function(index, f){
            
            let contenido = `
                <tr>
                    <td>${fecha(f.Fecha)}</td>
                    <td>${f.NroRemito == 0 ? '-' : f.NroRemito}</td>
                    <td>${f.IdPrestacion}</td>
                    <td>${f.ApellidoPaciente} ${f.NombrePaciente}</td>
                    <td>${f.Documento}</td>
                    <td><span style="text-align=center" class="custom-badge pequeno">${f.Finalizado === 1 ? `Finalizado` : `Cerrado`}</span></td>
                    <td><button data-id="${f.IdPrestacion}" class="btn btn-sm iconGeneral verPrestacion" title="Ver"  data-bs-toggle="modal" data-bs-target="#verPrestacionModal"><i class="ri-search-eye-line"></i></button></td>
                    <td>${f.Finalizado === 1 ? `<input type="checkbox" disabled>` : `<input type="checkbox" name="Id_finalizar" value="${f.IdPrestacion}" checked>`}</td>
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
        .fail(function(jqXHR){
            preloader('off');
            let errorData = JSON.parse(jqXHR.responseText);            
            checkError(jqXHR.status, errorData.msg);
            return; 
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
        preloader('on');

        $.get(enviarMapa, { mapa: MAPA})
            .done(function(response){
                preloader('off');
                let data = response.result;

                $.each(data, function(index, en){

                    let contenido = `
                        <tr>
                            <td> ${en.NroRemito}</td>
                            <td>${fecha(en.Fecha)}</td>
                            <td>${en.IdPrestacion} ${en.EmpresaSinEnvio === 1 ? `<i title="La empresa no tiene habilitado los envios de Emails" class="ri-mail-forbid-line rojo"></i>` : ``} ${en.ArtSinEnvio === 1 ? `<i title="La ART no tiene habilitado los envios de Emails" class="ri-mail-forbid-line rojo"></i>` : ``}</td>
                            <td>${en.ApellidoPaciente} ${en.NombrePaciente}</td>
                            <td>${en.Documento}</td>
                            <td><span class="custom-badge pequeno">${(en.eEnviado === 1 ? 'eEnviado':'No eEnviado')}</span></td>
                            <td>
                                <button data-id="${en.IdPrestacion}" class="btn btn-sm iconGeneral verPrestacion" title="Ver"  data-bs-toggle="modal" data-bs-target="#verPrestacionModal"><i class="ri-search-eye-line"></i></button>
                                <button data-id="${en.IdPrestacion}" class="btn btn-sm iconGeneral vistaPreviaEnviar" title="Vista previa"><i class="ri-file-search-line"></i></button>
                            </td>
                            <td>${en.eEnviado === 1 ? '' : en.eEnviado === 0 && en.Finalizado === 1 && en.Cerrado === 1 && en.Etapa === 'Completo' ? `<input type="checkbox" name="Id_enviar" value="${en.IdPrestacion}" checked>` : `<span class="custom-badge pequeno">Bloqueado</span>`}
                            </td> 
                        </tr>
                    `;
                    $('#eenviarMapa').append(contenido);
                    
                });

                $("#listaeenviar").fancyTable({
                    pagination: true,
                    perPage: 10,
                    searchable: false,
                    globalSearch: false,
                    sortable: false, 
                });

            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return; 
            })
    }

    function checkEstado(id, who) {
        let btn = $('.cambiarEstado[data-id="' + id + '"]');
        btn.prop('disabled', true);
    
        let arr = { _token: TOKEN, Id: id, estado: who };

        $.post(changeEstado, arr)
            .done(function(response) {
                let data = response.result;

                btn.prop('title', data.Incompleto === 1 ? 'Incompleto' : 'Completo')
                   .removeClass()
                   .addClass('cambiarEstado custom-badge ' + (data.Incompleto === 1 ? 'rojo' : 'verde'));
            })
            .always(function() {
                btn.prop('disabled', false);
            });

    }

    function listaComentariosPrivados(idmapa, opcionFase, tipo){

        $('#privadoPrestaciones').empty();
        let listaFases = {
            prestaciones: {
                id: 2,
                bodyTable: '#privadoPrestaciones',
                table: '#lstPrivPrestaciones'
            },
            cerrado: {
                id: 3,
                bodyTable: '#privadoCerrar',
                table: '#lstPrivCerrados'
            },

        }

        $.get(privateComment, {Id: idmapa, obsfasesid: listaFases[opcionFase].id, tipo: tipo})
            .done(async function(response){

                let data = await response.result;

                $.each(data, function(index, d){
 
                    let contenido =  `
                        <tr>
                            <td>${d.Fecha}</td>
                            <td>${d.IdEntidad}</td>
                            <td>${d.IdUsuario}</td>
                            <td>${d.nombre_perfil}</td>
                            <td>${d.Comentario}</td>
                        </tr>
                    `;

                    $(listaFases[opcionFase].bodyTable).append(contenido);
                });

                $(listaFases[opcionFase].table).fancyTable({
                    pagination: true,
                    perPage: 15,
                    searchable: false,
                    globalSearch: false,
                    sortable: false, 
                });
            })
    }

    function fecha(fe){

        if(['', undefined, null].includes(fe)) return;

        let partesFecha = fe.split("-");
        return  partesFecha[2] + "/" + partesFecha[1] + "/" + partesFecha[0];
    }

    function listarRemitos(idmapa){

        $('#remitoMapa').empty();
        preloader('on');

        $.get(getRemito, {Id: idmapa})
            .done(function(response){
                preloader('off');
                let data = response.result;

                $.each(data, function(index, r){

                    let contenido = `
                        <tr>
                            <td>${r.NroCEE == 0 ? '-' : r.NroCEE}</td>
                            <td>${r.contadorRemitos}</td>
                            <td>
                                <span style="text-align=center" class="custom-badge ${r.Entregado === 1 ? 'verde':'rojo'}">${r.Entregado === 1 ? 'Entregado':'Sin Entregar'}</span>
                            </td>
                            <td>${r.constanciases && r.constanciases.length > 0 && r.constanciases[0].Obs !== null && r.constanciases[0].Obs !== undefined ? r.constanciases[0].Obs : '-'}</td>
                            <td>
                                <button data-remito="${r.NroCEE}" type="button" class="btn botonGeneral ${r.Entregado === 1 ? 'revertirEntrega' : 'entregarRemito'}" ${r.Entregado === 1 ? '' : 'data-bs-toggle="modal" data-bs-target="#entregarModal"'}>${r.Entregado === 1 ? 'Revertir Entrega':'Entregar'}</button> 
                            </td>
                            <td>
                                <button data-remito="${r.NroCEE}" type="button" class="pdf btn iconGeneral" title="Generar reporte en Pdf">
                                    <i class="ri-file-pdf-line"></i>
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

    function getObsEstado(id){
        preloader('on');
        $('.ComObsEstado').val("");
        $.get(getComentarioPres, {Id: id})    
            .done(async function(response){
                preloader('off');
                let rs = await response.comentario;

                $('.ComObsEstado').val(rs);

            })
    }


});