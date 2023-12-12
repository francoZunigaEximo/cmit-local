$(document).ready(()=>{

    let hoy = new Date().toISOString().slice(0, 10), 
        maxCaracteres = 100,
        totalEnProceso = parseInt($('#totalEnProceso').text()),
        totalCerradas = parseInt($('#totalCerradas').text()),
        totalFinalizados = parseInt($('#totalFinalizados').text()),
        totalEntregados = parseInt($('#totalEntregados').text()),
        totalCompleta = parseInt($('#totalCompleta').text()),
        totalConEstados = parseInt($('#totalConEstados').text());

    toastr.options = {
        closeButton: true,   
        progressBar: true,    
        timeOut: 3000,        
    };

    quitarDuplicados("#Estado");
    getPrestaMapas();
    actualizarTotales(totalEnProceso, totalCerradas, totalFinalizados, totalEntregados, totalCompleta, totalConEstados);
    verificacionNro();
    getCerrarMapas();
    getFinalMapa();
    getEnMapa();

    $('#remitoFechaE').val(hoy)
   
    //Exportar Excel
    $('.excel').click(function() {
        
        let ids = [];
        ids.push($(this).data('remito'));
        

        if (ids.length > 0) {
            if (confirm("¿Estás seguro de que deseas generar el reporte de Excel?")) {
                $.ajax({
                    url: exportExcelMapas,
                    type: "POST",
                    data: {
                        _token: TOKEN,
                        modulo: 'remito',
                        tipo: 'excel',
                        mapa: MAPA,
                        Id: ids
                    },
                    success: function(response) {
                        let filePath = response.filePath,
                            pattern = /storage(.*)/,
                            match = filePath.match(pattern),
                            path = match ? match[1] : '';

                        let url = new URL(location.href),
                            baseUrl = url.origin,
                            fullPath = baseUrl + '/cmit/storage' + path;

                        let link = document.createElement('a');
                        link.href = fullPath;
                        link.download = "informe.xlsx";
                        link.style.display = 'none';

                        document.body.appendChild(link);
                        link.click();
                        setTimeout(function() {
                            document.body.removeChild(link);
                        }, 100);

                        toastr.options = {
                            closeButton: true,   
                            progressBar: true,    
                            timeOut: 3000,        
                        };
                        toastr.success('Se esta generando el informe. Aguarde', 'Informe Excel');
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });
            }
        } else {
            toastr.error('Debes seleccionar al menos un mapa para exportar.', 'Error');
        }

    });

    //Exportar PDF
    $('.pdf').click(function() {

        let ids = [];

        ids.push($(this).data('remito'));

        if (ids.length > 0) {
            
            if (confirm("¿Estás seguro de que deseas generar el reporte en PDF?")) {
                console.log(ids);
                $.ajax({
                    url: mapasPdf,
                    type: "POST",
                    data: {
                        _token: TOKEN,
                        Id: ids,
                        mapa: MAPA
                    },
                    success: function(response) {
                        toastr.options = {
                            closeButton: true,   
                            progressBar: true,    
                            timeOut: 3000,        
                        };
                        toastr.success('Se esta generando el informe. Aguarde', 'Informe PDF');

                        let filePath = response.filePath,
                            pattern = /storage(.*)/,
                            match = filePath.match(pattern),
                            path = match ? match[1] : '';

                        let url = new URL(location.href),
                            baseUrl = url.origin,
                            fullPath = baseUrl + '/cmit/storage' + path;

                        let link = document.createElement('a');
                        link.href = fullPath;
                        link.download = "remito.pdf";
                        link.style.display = 'none';

                        document.body.appendChild(link);
                        link.click();
                        setTimeout(function() {
                            document.body.removeChild(link);
                        }, 100);
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });
            }
        } else {

            toastr.info('No hay remitos para imprimir el pdf', 'Atención');
        }
        
    });

    
    $('#updateMapa').click(function(){

        let Nro = $('#Nro').val(),
            IdART =$('#IdART').val(),
            IdEmpresa = $('#IdEmpresa').val(),
            Fecha = $('#Fecha').val(),
            FechaE = $('#FechaE').val(),
            Estado = $('#Estado').val(),
            Cpacientes = $('#Cpacientes').val(),
            Obs = $('#Obs').val(),
            IdMap = $('#Id').val();

            if($('#form-update').valid()) {
                
                $.post(updateMapa, {_token: TOKEN, Nro:Nro, IdART: IdART, IdEmpresa: IdEmpresa, Fecha: Fecha, FechaE: FechaE, Estado: Estado, Cpacientes: Cpacientes, Obs: Obs, Id: IdMap})
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
        $('.confirmarEntrega').data('id', remito);
    });

    $('#entregarModal').on('hidden.bs.modal', function(){
        $("#remitoObs").val("");
    });

    $(document).on('click', '.confirmarEntrega', function(){

        let remito = $(this).data('id'),
            remitoObs =$('#remitoObs').val(),
            remitoFechaE = $('#remitoFechaE').val();

        if(remitoFechaE === '' || remitoFechaE === null){
            toastr.warning('Debe especificar una fecha de entrega', 'Atención');
            return;
        }

        $.ajax({
            url: saveRemitos,
            type: 'Post',
            data: {
                _token: TOKEN,
                Obs: remitoObs,
                FechaE: remitoFechaE,
                Id: remito
            },
            success: function(){

                toastr.success('Se han registrado las fechas de entrega en los remitos correspondientes', 'Perfecto');
                $('#remitoObs').val('');
                $('#entregarModal').modal('hide');
            },
            error: function(xhr){
                console.error(xhr);
                toastr.error('Ha ocurrido un error. Consulte con el administrador', 'Error');
            }
            
        });

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

                    let etapa = (dat.Etapa === 'Completo' ? '<span style="text-align=center" class="custom-badge verde">Completo</span>' : '<span style="text-align=center" class="custom-badge rojo">Incompleto</span>');

                    let estado;
                    
                   if(dat.Finalizado === 1){
                        estado = '<span style="text-align=center" class="custom-badge verde">Finalizado</span>';
                  
                   } else if(dat.Cerrado === 1){
                        estado = '<span style="text-align=center" class="custom-badge azul">Cerrado</span>';
                   
                   } else if(dat.Cerrado === 0 && dat.Finalizado === 0) {
                        estado = '<span style="text-align=center" class="custom-badge gris">Abierto</span>';
                   }

                   let eEnviado = (dat.eEnviado === 1 ? '<span style="text-align=center" class="custom-badge verde"><i class="ri-checkbox-circle-line"></i></span>' : '<span style="text-align=center" class="custom-badge gris"><i class="ri-checkbox-circle-line"></i></span>');

                   let facturado = (dat.Facturado === 1 ? '<span style="text-align=center" class="custom-badge verde"><i class="ri-checkbox-circle-line"></i></span>' : '<span style="text-align=center" class="custom-badge amarillo"><i class="ri-information-line"></i></span>');
                
                   let fechaOriginal = dat.Fecha;
                   let partesFecha = fechaOriginal.split("-");
                   let nuevaFecha = partesFecha[2] + "/" + partesFecha[1] + "/" + partesFecha[0];

                    let contenido = `
                        <tr>
                            <td>${dat.IdPrestacion}</td>
                            <td>${nuevaFecha}</td>
                            <td>${dat.Apellido} ${dat.Nombre}</td>
                            <td>${dat.NroCEE}</td>
                            <td>${etapa}</td>
                            <td>${estado}</td>
                            <td>${eEnviado}</td>
                            <td>${facturado}</td>
                            <td><button data-id="${dat.IdPrestacion}" class="btn btn-sm btn-soft-primary edit-item-btn verPrestacion" title="Ver"  data-bs-toggle="modal" data-bs-target="#verPrestacionModal"><i class="ri-search-eye-line"></i></button></td>
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
                    let isComplete = (efectuadoOk && e.CInfo === 3 ? '<span class="custom-badge verde">Completo</span>' : '<span class="custom-badge rojo">Incompleto</span>');

                    let arrCerrado = [3,4,5], cerradoOk = arrCerrado.includes(e.CAdj);
       
                    let efectorCompleto = e.ApellidoEfector + ' ' + e.NombreEfector;

                    let contenido = `
                        <tr>
                            <td>${e.NombreExamen}</td>
                            <td>${isComplete}</td>
                            <td>${e.NombreProveedor}</td>
                            <td>
                                <span>${(e.NombreEfector === null || e.ApellidoEfector == null ? '-' : efectorCompleto )}</span>
                                <span>${(cerradoOk ? '<span style="display:block" class="custom-badge verde">Completo</span>' : '')}</span>
                            </td>
                            <td>
                                <span>${e.ApellidoInformador ? e.ApellidoInformador : '-'} ${e.NombreInformador ? e.NombreInformador : ''}</span>
                                <span>${(e.CInfo === 3 ? '<span style="display:block" class="custom-badge verde">Completo</span>' : '')}</span>
                            </td>
                            <td><button data-id="${e.IdExamen}" class="btn btn-sm btn-soft-primary edit-item-btn" title="Ver"><i class="ri-search-eye-line"></i></button></td>
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
            
        $.get(getCerrarMapa, { prestacion: prestacion, remito: remito, estado: estado, mapa: MAPA})
        .done(function(cerrar) {

            $('#cerrarMapa').empty();

            $.each(cerrar, function(index, d) {

                let estado;
                
                if(d.Finalizado === 1){
                    estado = '<span style="text-align=center" class="custom-badge verde">Finalizado</span>';
                
                } else if(d.Cerrado === 1){
                    estado = '<span style="text-align=center" class="custom-badge azul">Cerrado</span>';
                
                } else if(d.Cerrado === 0 && d.Finalizado === 0) {
                    estado = '<span style="text-align=center" class="custom-badge gris">Abierto</span>';
                
                }else if(d.eEnviado === 1) {
                    estado = '<span style="text-align=center" class="custom-badge gris">eEnviado</span>';
                
                }else if(d.Anulado === 1) {
                    estado = '<span style="text-align=center" class="custom-badge gris">Anulado</span>';
                
                }else if(d.Entregado === 1) {
                    estado = '<span style="text-align=center" class="custom-badge gris">Entregado</span>';
                
                }else if(d.Incompleto === 1) {
                    estado = '<span style="text-align=center" class="custom-badge gris">Incompleto</span>';
                }

                let fechaOriginal = d.Fecha,
                    partesFecha = fechaOriginal.split("-"),
                    nuevaFecha = partesFecha[2] + "/" + partesFecha[1] + "/" + partesFecha[0];
                
                let contenido = `
                    <tr>
                        <td>${d.IdPrestacion}</td>
                        <td>${nuevaFecha}</td>
                        <td>${d.ApellidoPaciente} ${d.NombrePaciente}</td>
                        <td>${estado}</td>
                        <td><button data-id="${d.IdPrestacion}" class="btn btn-sm btn-soft-primary edit-item-btn verPrestacion" title="Ver"  data-bs-toggle="modal" data-bs-target="#verPrestacionModal"><i class="ri-search-eye-line"></i></button></td>
                        <td><input type="checkbox" name="Id" value="${d.IdPrestacion}" checked></td>
                    </tr>
                `;

                $('#cerrarMapa').append(contenido);
                $('#NroPresCerrar, #EtapaCerrar, #NroRemitoCerrar').val('');
                
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

    $(document).on('change', '#NroPresFinal, #NroRemitoFinal', function() {

        let prestacionf = $('#NroPresFinal').val(),
            remitof = $('#NroRemitoFinal').val();

        $.get(getFinalizarMapa, { prestacion: prestacionf, remito: remitof, mapa: MAPA })
            .done(function(finalizar){

                $('#finalizarMapa').empty();

                $.each(finalizar, function(index, f){

                    let estado;
                
                if(f.Finalizado === 1){
                    estado = '<span style="text-align=center" class="custom-badge verde">Finalizado</span>';
                
                } else if(f.Cerrado === 1){
                    estado = '<span style="text-align=center" class="custom-badge azul">Cerrado</span>';
                
                } else if(f.Cerrado === 0 && d.Finalizado === 0) {
                    estado = '<span style="text-align=center" class="custom-badge gris">Abierto</span>';
                
                }else if(f.eEnviado === 1) {
                    estado = '<span style="text-align=center" class="custom-badge gris">eEnviado</span>';
                
                }else if(f.Anulado === 1) {
                    estado = '<span style="text-align=center" class="custom-badge gris">Anulado</span>';
                
                }else if(f.Entregado === 1) {
                    estado = '<span style="text-align=center" class="custom-badge gris">Entregado</span>';
                
                }else if(f.Incompleto === 1) {
                    estado = '<span style="text-align=center" class="custom-badge gris">Incompleto</span>';
                }

                let fechaOriginal = f.Fecha,
                    partesFecha = fechaOriginal.split("-"),
                    nuevaFecha = partesFecha[2] + "/" + partesFecha[1] + "/" + partesFecha[0];
                
                let contenido = `
                    <tr>
                        <td>${f.IdPrestacion}</td>
                        <td>${nuevaFecha}</td>
                        <td>${f.ApellidoPaciente} ${f.NombrePaciente}</td>
                        <td>${estado}</td>
                        <td><button data-id="${f.IdPrestacion}" class="btn btn-sm btn-soft-primary edit-item-btn verPrestacion" title="Ver"  data-bs-toggle="modal" data-bs-target="#verPrestacionModal"><i class="ri-search-eye-line"></i></button></td>
                        <td>${f.Finalizado === 1 ? '<input type="checkbox" disabed>' : `<input type="checkbox" name="Id" value="${f.IdPrestacion}" checked>`}</td>
                        
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
                getEnMapa();
                getFinalMapa();
                getCerrarMapas();
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
            eEnviadoEnviar = $('#eEnviadoEnviar').val();

        
            $.get(getEnviarMapa, {desde: fDesde, hasta: fHasta, prestacion: NroPresEnviar, eEnviado: eEnviadoEnviar, mapa: MAPA})
                .done(function(enviar){

                    $('#eenviarMapa').empty();

                    $.each(enviar, function(index, en){
                        
                        let tipo = (en.TipoPrestacion ? '<span class="custom-badge nuevoAzulInverso">' + en.TipoPrestacion +'</span>' : '');

                        let fechaOriginal = en.Fecha,
                        partesFecha = fechaOriginal.split("-"),
                        nuevaFecha = partesFecha[2] + "/" + partesFecha[1] + "/" + partesFecha[0];

                        let contenido = `
                            <tr>
                                <td>${en.IdPrestacion}</td>
                                <td>${nuevaFecha}</td>
                                <td>${tipo}</td>
                                <td>${en.ApellidoPaciente} ${en.NombrePaciente}</td>
                                <td>${en.Documento}</td>
                                <td><button data-id="${en.IdPrestacion}" class="btn btn-sm btn-soft-primary edit-item-btn verPrestacion" title="Ver"  data-bs-toggle="modal" data-bs-target="#verPrestacionModal"><i class="ri-search-eye-line"></i></button></td>
                                <td>${en.eEnviado === 1 ? '<input type="checkbox" disabled>' : `<input type="checkbox" name="Id" value="${en.IdPrestacion}" checked>`}</td>
                                
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

    function quitarDuplicados(selector) {
        let seleccion = $(selector).val();
        let countSeleccion = $(selector + " option[value='" + seleccion + "']").length;
    
        if (countSeleccion > 1) {
            $(selector + " option[value='" + seleccion + "']:gt(0)").hide();
        }
    }

    function total(...args) {
        return args.reduce((sum, current) => sum + current, 0);
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

    function actualizarTotales(totalEnProceso, totalConEstados, totalCerradas, totalFinalizados, totalEntregados, totalCompleta) {
        let totales = total(totalEnProceso, totalConEstados, totalCerradas, totalFinalizados, totalEntregados, totalCompleta);
    
        if (isNaN(totales)) {
            totales = 0;
        }
    
        $('#total').text(totales);
    }

    function getPrestaMapas(){

        $('#prestaMapa').empty();

        $.get(getPrestaciones, {mapa: MAPA })
            .done(function(presta){

                $.each(presta, function(index, d) {

                    let etapa = (d.Etapa === 'Completo' ? '<span style="text-align=center" class="custom-badge verde">Completo</span>' : '<span style="text-align=center" class="custom-badge rojo">Incompleto</span>');

                    let estado;
                    
                   if(d.Finalizado === 1){
                        estado = '<span style="text-align=center" class="custom-badge verde">Finalizado</span>';
                  
                   } else if(d.Cerrado === 1){
                        estado = '<span style="text-align=center" class="custom-badge azul">Cerrado</span>';
                   
                   } else if(d.Cerrado === 0 && d.Finalizado === 0) {
                        estado = '<span style="text-align=center" class="custom-badge gris">Abierto</span>';
                   }


                   let eEnviado = (d.eEnviado === 1 ? '<span style="text-align=center" class="custom-badge verde"><i class="ri-checkbox-circle-line"></i></span>' : '<span style="text-align=center" class="custom-badge gris"><i class="ri-checkbox-circle-line"></i></span>');

                   let facturado = (d.Facturado === 1 ? '<span style="text-align=center" class="custom-badge verde"><i class="ri-checkbox-circle-line"></i></span>' : '<span style="text-align=center" class="custom-badge amarillo"><i class="ri-information-line"></i></span>');
                
                   let fechaOriginal = d.Fecha;
                   let partesFecha = fechaOriginal.split("-");
                   let nuevaFecha = partesFecha[2] + "/" + partesFecha[1] + "/" + partesFecha[0];

                    let contenido = `
                        <tr>
                            <td>${d.IdPrestacion}</td>
                            <td>${nuevaFecha}</td>
                            <td>${d.Apellido} ${d.Nombre}</td>
                            <td>${d.NroCEE}</td>
                            <td>${etapa}</td>
                            <td>${estado}</td>
                            <td>${eEnviado}</td>
                            <td>${facturado}</td>
                            <td><button data-id="${d.IdPrestacion}" class="btn btn-sm btn-soft-primary edit-item-btn verPrestacion" title="Ver"  data-bs-toggle="modal" data-bs-target="#verPrestacionModal"><i class="ri-search-eye-line"></i></button></td>
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
            .done(function(close){

                $.each(close, function(index, c) {

                    let estado;
                    
                    if(c.Finalizado === 1){
                        estado = '<span style="text-align=center" class="custom-badge verde">Finalizado</span>';
                    
                    } else if(c.Cerrado === 1){
                        estado = '<span style="text-align=center" class="custom-badge azul">Cerrado</span>';
                    
                    } else if(c.Cerrado === 0 && c.Finalizado === 0) {
                        estado = '<span style="text-align=center" class="custom-badge gris">Abierto</span>';
                    
                    }else if(c.eEnviado === 1) {
                        estado = '<span style="text-align=center" class="custom-badge gris">eEnviado</span>';
                    
                    }else if(c.Anulado === 1) {
                        estado = '<span style="text-align=center" class="custom-badge gris">Anulado</span>';
                    
                    }else if(c.Entregado === 1) {
                        estado = '<span style="text-align=center" class="custom-badge gris">Entregado</span>';
                    
                    }else if(c.Incompleto === 1) {
                        estado = '<span style="text-align=center" class="custom-badge gris">Incompleto</span>';
                    }
    
                    let fechaOriginal = c.Fecha,
                        partesFecha = fechaOriginal.split("-"),
                        nuevaFecha = partesFecha[2] + "/" + partesFecha[1] + "/" + partesFecha[0];
                    
                    let contenido = `
                        <tr>
                            <td>${c.IdPrestacion}</td>
                            <td>${nuevaFecha}</td>
                            <td>${c.ApellidoPaciente} ${c.NombrePaciente}</td>
                            <td>${estado}</td>
                            <td><button data-id="${c.IdPrestacion}" class="btn btn-sm btn-soft-primary edit-item-btn verPrestacion" title="Ver"  data-bs-toggle="modal" data-bs-target="#verPrestacionModal"><i class="ri-search-eye-line"></i></button></td>
                            <td>${c.Cerrado === 1 ? '<input type="checkbox" disabled>' : `<input type="checkbox" name="Id" value="${c.IdPrestacion}" checked>`} </td>
                           
                        </tr>
                    `;
    
                    $('#cerrarMapa').append(contenido);
                    
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
        .done(function(data){

            $('#finalizarMapa').empty();

            $.each(data, function(index, f){

                let estado;
            
            if(f.Finalizado === 1){
                estado = '<span style="text-align=center" class="custom-badge verde">Finalizado</span>';
            
            } else if(f.Cerrado === 1){
                estado = '<span style="text-align=center" class="custom-badge azul">Cerrado</span>';
            
            } else if(f.Cerrado === 0 && d.Finalizado === 0) {
                estado = '<span style="text-align=center" class="custom-badge gris">Abierto</span>';
            
            }else if(f.eEnviado === 1) {
                estado = '<span style="text-align=center" class="custom-badge gris">eEnviado</span>';
            
            }else if(f.Anulado === 1) {
                estado = '<span style="text-align=center" class="custom-badge gris">Anulado</span>';
            
            }else if(f.Entregado === 1) {
                estado = '<span style="text-align=center" class="custom-badge gris">Entregado</span>';
            
            }else if(f.Incompleto === 1) {
                estado = '<span style="text-align=center" class="custom-badge gris">Incompleto</span>';
            }

            let fechaOriginal = f.Fecha,
                partesFecha = fechaOriginal.split("-"),
                nuevaFecha = partesFecha[2] + "/" + partesFecha[1] + "/" + partesFecha[0];
            
            let contenido = `
                <tr>
                    <td>${f.IdPrestacion}</td>
                    <td>${nuevaFecha}</td>
                    <td>${f.ApellidoPaciente} ${f.NombrePaciente}</td>
                    <td>${estado}</td>
                    <td><button data-id="${f.IdPrestacion}" class="btn btn-sm btn-soft-primary edit-item-btn verPrestacion" title="Ver"  data-bs-toggle="modal" data-bs-target="#verPrestacionModal"><i class="ri-search-eye-line"></i></button></td>
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
            .done(function(enviar){

                $.each(enviar, function(index, en){
                        
                    let tipo = (en.TipoPrestacion ? '<span class="custom-badge nuevoAzulInverso">' + en.TipoPrestacion +'</span>' : '');

                    let fechaOriginal = en.Fecha,
                    partesFecha = fechaOriginal.split("-"),
                    nuevaFecha = partesFecha[2] + "/" + partesFecha[1] + "/" + partesFecha[0];

                    let contenido = `
                        <tr>
                            <td>${en.IdPrestacion}</td>
                            <td>${nuevaFecha}</td>
                            <td>${tipo}</td>
                            <td>${en.ApellidoPaciente} ${en.NombrePaciente}</td>
                            <td>${en.Documento}</td>
                            <td><button data-id="${en.IdPrestacion}" class="btn btn-sm btn-soft-primary edit-item-btn verPrestacion" title="Ver"  data-bs-toggle="modal" data-bs-target="#verPrestacionModal"><i class="ri-search-eye-line"></i></button></td>
                            <td>${en.eEnviado === 1 ? '<input type="checkbox" disabled>' : `<input type="checkbox" name="Id" value="${en.IdPrestacion}" checked>`}</td>
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

});