$(function(){

    const principal = {
        Fecha: $('#Fecha'),
        FechaN: $('#FechaN'),
        siguienteExCta: $('#siguienteExCta'),
        seleccionExCta: $('.seleccionExCta'),
        editarComentario: $('.editarComentario'),
        nuevaPrestacion: $('.nuevaPrestacion'),
        volverPrestacionLimpia: $('.volverPrestacionLimpia'),
        buscarPrestPaciente: $('#buscarPrestPaciente'),
        grillaPacientes: $('#grillaPacientes'),
        selectMapaPres: $('.selectMapaPres'),
        ultimasFacturadas: $('.ultimasFacturadas'),
        examenesDisponibles: $('.examenesDisponibles'),
        guardarPrestacion: $('#guardarPrestacion'),
        cargarExPrestacion: $('.cargarExPrestacion'),
        blockPrestPaciente: $('#blockPrestPaciente'),
        downPrestPaciente: $('#downPrestPaciente'),
        buscarExamen: $('.buscarExamen'),
        reiniciarExamen: $('.reiniciarExamen'),
        precargaExamenes: $('.precargaExamenes'),
        checkAllEx: $('#checkAllEx'),
        countExamenes: $('#countExamenes'),
        privadoPrestaciones: $('#privadoPrestaciones'),
        finalizarWizzard: $('#finalizarWizzard'),
        lstPrivPrestaciones: $('#lstPrivPrestaciones'),
        confirmarEdicion: $('.confirmarEdicion'),
        disponiblesExamenes: $('#disponiblesExamenes'),
        totalCantidad: $('#totalCantidad'),
        grillaFacturadas: $('#grillaFacturadas'),
        listaPacientes: $('#listaPacientes'),
        estadoBadge: $('#estadoBadge'),
        filapresId: $('#filapresId'),
        comentarioPrivado: $('#comentarioPrivado'),
        listEdicion: $('#listEdicion'),
        deleteMasivo: $('.deleteMasivo'),
        deleteEx: $('.deleteEx'),
        listaExamenes: $('#listaExamenes'),
        prestacionLimpia: $('.prestacionLimpia'),
        resultadosPaciente: $('.resultadosPaciente'),
        reportesPacientes: $('.reportesPacientes'),
        resulPaciente: $('.resulPaciente'),
        imprimirRepo: $('.imprimirRepo'),
        addPaquete: $('.addPaquete'),
        altaPrestacionModal: $('#altaPrestacionModal'),
        addExamen: $('.addExamen'),
        fichaLaboralModal: $('.fichaLaboralModal'),
        deleteExamenes: $('.deleteExamenes'),
        deleteExamen: $('.deleteExamen'),
        SalirWizzard: $('#SalirWizzard'),
        openExamen: $('.openExamen'),
        bloquearExamenes: $('.bloquearExamenes'),
        bloquearExamen: $('.bloquearExamen'),
        listado: $("#listado"),
        alertaExCta: $('#alertaExCta'),
        confirmarComentarioPriv: $('.confirmarComentarioPriv'),
        deleteComentario: $('.deleteComentario'),
        results: $('#results'),
        lstEx: $('#lstEx'),
        lstEx2: $('#lstEx2'),
        observacionesModal: $('.observacionesModal'),
        checkAllExa: $('#checkAllExa'),
        paqueteExamen: $('.paqueteExamen'),
        tituloPrestacion: $('.tituloPrestacion'),
        paqueteExCta: $('.paqueteExCta'),
        btnExamen: $('.btnExamen'),
        examenesCantidad: $('#examenesCantidad'),
        lstExamenesCtd: $('#lstExamenesCtd'),
        listadoExamenesCtd: $('#listadoExamenesCtd')
    };

    const variables = {
        ElPago: $('#ElPago'),
        ElSPago: $('#ElSPago'),
        ElTipo: $('#ElTipo'),
        ElSucursal: $('#ElSucursal'),
        ElNroFactura: $('#ElNroFactura'),
        ElNroFactProv: $('#ElNroFactProv'),
        ElAutorizado: $('#ElAutorizado'),
        tipoPrestacionPres: $('#tipoPrestacionPres'),
        selectClientes: $('#selectClientes'),
        selectArt: $('#selectArt'),
        Fecha: $('#Fecha'),
        TipoPrestacion: $('#tipoPrestacionPres'),
        IdPaciente: ID,
        IdMapa: $('#mapas'),
        IdMapaN: $('#mapasN'),
        PagoLaboral: $('#PagoLaboral'),
        SPago: $('#SPago'),
        Observaciones: $('#Observaciones'),
        AutorizaSC: $('#ElAutorizado'),
        Sucursal: $('#ElSucursal'),
        NumeroFacturaVta: $('#ElNroFactura'),
        NroFactProv: $('#ElNroFactProv'),
        idPrestacion: $('#idPrestacion'),
        buscar: $('#buscar'),
        examen: $('#examen'),
        Comentario: $("#Comentario"),
        ObsN: $('#ObsN'),
        IdObservacion: $('#IdObservacion'),
        ComentarioEditar: $('#ComentarioEditar'),
        infInternos: $('#infInternos'),
        pedProveedores: $('#pedProveedores'),
        conPaciente: $('#conPaciente'),
        resAdmin: $('#resAdmin'),
        caratula: $('#caratula'),
        consEstDetallado: $('#consEstDetallado'),
        consEstSimple: $('#consEstSimple'),
        paquetes: $('#paquetes'),
        exam: $("#exam"),
        ObsExamenesN: $('#ObsExamenesN'),
        ObservacionesPresN: $('#ObservacionesPresN'),
        NroFactPrestacion: $('#NroFactPrestacion'),
        hoy: new Date().toLocaleDateString('en-CA'),
        facturacion_id: $('#facturacion_id'),
        ElTipo: $('#ElTipo'),
        ElSucursal: $('#ElSucursal'),
        ElNroFactura: $('#ElNroFactura'),
        inputExCtd: $('#inputExCtd'),
        buscarExCtd: $('#buscarExCtd'),
        examenCheck: $('#examenCheck'),
        addExaCtd: $('#addExaCtd')
    };

    variables.ElPago
        .add(variables.ElSPago)
        .add(variables.ElTipo)
        .add(variables.ElSucursal)
        .add(variables.ElNroFactura)
        .add(variables.ElNroFactProv)
        .add(variables.ElAutorizado)
        .prop('disabled', true);

    let IdNueva;
    
    principal.Fecha
        .add(principal.FechaN)
        .val(variables.hoy);

    principal.seleccionExCta
        .add(principal.editarComentario)
        .add(principal.NroFactPrestacion)
        .hide();

    
    precargaTipoPrestacion(variables.tipoPrestacionPres.val());
    getMap(variables.selectClientes.val(), variables.selectArt.val());
    getListado(null);
    listadoConSaldos(variables.selectClientes.val());
    cantidadDisponibles(variables.selectClientes.val());
    // listadoFacturas(variables.selectClientes.val(), null);
    getUltimasFacturadas(variables.selectClientes.val());
    selectorPago(pagoInput);
    

    variables.tipoPrestacionPres.on('change', function(){
        precargaTipoPrestacion(variables.tipoPrestacionPres.val());
    });

    $(document).on('change', '#selectClientes, #selectArt, input[type=radio][name=TipoPrestacion]', function() {

        let tipoPrestacion = $('input[type=radio][name=TipoPrestacion]:checked').val(), empresa = variables.selectClientes.val(), art = variables.selectArt.val();

        getUltimasFacturadas(variables.selectClientes.val());
        listadoConSaldos(variables.selectClientes.val());
        cantidadDisponibles(variables.selectClientes.val());
        tablasExamenes(variables.selectClientes.val(), false, '#lstEx2');
        tablasExamenes(variables.selectClientes.val(), true, '#lstEx');

        if(tipoPrestacion === 'ART') {  
            getMap(empresa, art);  
        }
    });

    principal.cargarExPrestacion.on('click', function(e){
        e.preventDefault();

        let ids = [];

        principal.lstEx.find('.item-checkbox:checked').each(function(){
            ids.push($(this).val());
        });

        if(ids.length === 0) {
            toastr.warning("Debe seleccionar un examen para registrar la prestación",'',{timeOut: 1000});
            return;
        }
        preloader('on');
          $.post(cargarExCta, {Ids: ids, IdPrestacion: variables.idPrestacion.val(), _token: TOKEN})
            .done(function(response){

                principal.listaExamenes.empty();
                cargarExamen(variables.idPrestacion.val());
                contadorExamenes(variables.idPrestacion.val());
                tablasExamenes(variables.selectClientes.val(), true, '#lstEx');  
          
                preloader('off');
                toastr[response[0].status](response[0].msg,'',{timeOut: 1000});
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return; 
            });  

    });

     //Guardamos la prestación
    principal.guardarPrestacion.on('click', async function(e){
        e.preventDefault();

        if (!variables.TipoPrestacion.val()) {
            toastr.warning('El tipo de prestación no puede ser un campo vacío','', {timeOut: 1000});
            return;
        }

        if (variables.TipoPrestacion.val() === 'ART' && (!variables.IdMapa.val() || variables.IdMapa.val() === '0')){
            toastr.warning('Debe seleccionar un mapa vigente para continuar si su prestacion es ART','', {timeOut: 1000});
            return;
        }

        preloader('on');
        const alta = await $.get(verificarAlta, {Id: ID})
        console.log(alta);

        if (alta && Object.keys(alta).length > 0) {

            $.get(savePrestacion, {
                IdPaciente: variables.IdPaciente ?? '',
                TipoPrestacion: variables.TipoPrestacion.val() ?? '',
                IdMapa: variables.IdMapa.val() ?? 0,
                Pago: variables.PagoLaboral.val() ?? '',
                SPago: variables.ElSPago.val() ?? '',
                Observaciones: variables.Observaciones.val() ?? '',
                AutorizaSC: variables.AutorizaSC.val() ?? '',
                IdART: alta.clienteArt.Id ?? 0,
                IdEmpresa: alta.cliente.Id ?? 0,
                datos_facturacion_id: ['B', 'A'].includes(variables.PagoLaboral.val()) ? variables.facturacion_id.val(): 0,
                Tipo: variables.ElTipo.val(),
                Sucursal: variables.ElSucursal.val(),
                NroFactura: variables.ElNroFactura.val(),
                NroFactProv: variables.ElNroFactProv.val()

            })
            .done(function(response){
                toastr.success(response.msg,'',{timeOut: 1000});
                principal.nuevaPrestacion.hide();
                principal.volverPrestacionLimpia.trigger('click');
                principal.listaExamenes.empty();
                IdNueva = response.nuevoId;
                cargarExamen(response.nuevoId);
                contadorExamenes(response.nuevoId);
                variables.idPrestacion.val(IdNueva);
                verificarExamenCuenta();
                preloader('off');
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return; 
            });

        }
        
    });

    //Buscador de prestaciones en Pacientes
    principal.buscarPrestPaciente.on('keypress', function(e){

        if (e.keyCode === 13){
            e.preventDefault();
            principal.grillaPacientes
                .empty()
                .append(getListado($(this).val()));
        }
    });
    
    principal.btnExamen.on('click', function(e){
        e.preventDefault();
        principal.examenesCantidad.modal('show');
    });

    variables.buscarExCtd.on('click', function(e){
        e.preventDefault();

        if(!variables.inputExCtd.val()) {
            toastr.warning("El campo se encuentra vacío o contiene datos incorrectos");
            return;
        }
        preloader('on')
        $.get(searchExamen, {buscar: variables.inputExCtd.val()})
            .done(function(response){
                preloader('off');
                tablaExaCantidad(response);
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return; 
            })
    });


    variables.examenCheck.on('click', function(e){
        e.preventDefault();

        let checkboxes = $('input[name="itemCheckbox"]:not(#examenCheck)'),
            total = checkboxes.length,
            checked = checkboxes.filter(':checked').length;

        if (checked < total) {
            checkboxes.prop('checked', true);
        } else {
            checkboxes.prop('checked', false);
        }
    });

    variables.addExaCtd.on('click', function(e){

        let examenes = [],
            idPrestacion = variables.idPrestacion.val();

        $('input[name="itemCheckbox"][type="checkbox"]:checked').each(function(){
            examenes.push($(this).val());
        })

        if(examenes.length === 0) {
            toastr.warning("Debe seleccionar algun examen para añadirlo a la prestación");
            return;
        }

        preloader('on');
        $.post(saveItemExamenes, {idPrestacion: idPrestacion, idExamen: examenes, _token: TOKEN})
            .done(function(){
                principal.listaExamenes.empty();
                $('#estudios').empty();
                cargarEstudiosImp();
                cargarExamen(idPrestacion);
                contadorExamenes(idPrestacion);
                preloader('off');
                toastr.success('Examenes cargados a la prestación correctamente','',{timeOut: 1000});

            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return; 
            });
    });

    

    //Bloqueo de prestación
    $(document).on('click', '#blockPrestPaciente', function(e){

        e.preventDefault();
        let Id = $(this).data('idprest');

        swal({
            title: "¿Esta seguro que desea bloquear la prestación?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                preloader('on');
                $.get(blockPrestacion, {Id:Id})
                    .done(function(response){
                        preloader('off');
                        toastr.success(response.msg,'',{timeOut: 1000});
                        cambioEstadoBlock();
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

    //Baja logica de prestación
    $(document).on('click', '#downPrestPaciente', function(e){

        e.preventDefault();
        let Id = $(this).data('idprest');
        
        swal({
            title: "¿Esta seguro que desea eliminar la prestación?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                preloader('on');
                $.get(downPrestaActiva, {Id:Id})
                    .done(function(response){
                        preloader('off');
                        toastr.success(response.msg,'',{timeOut: 1000});
                        cambioEstadoDown();
                        getListado(null);
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

    $(document).on('keydown', function(e) {
        if (e.keyCode === 27) {
            e.preventDefault();
            variables.buscar.val(" ");
            window.location.reload();
        }
    });

    $(document).on('change', '#PagoLaboral', function(){
        selectorPago($(this).val());
    });

    function selectorPago(pago) {
        
        if(['B','C', ''].includes(pago)) {
            
            principal.ultimasFacturadas
                .add(principal.siguienteExCta)
                .hide();
            
            principal.guardarPrestacion.show();

        }else if(variables.TipoPrestacion.val() !== 'ART') {
            preloader('on');
            $.get(lstExDisponibles, {Id: variables.selectClientes.val()})
            .done(function(response){
                preloader('off');
                if(response.length > 0) {
                    principal.ultimasFacturadas
                    .add(principal.siguienteExCta)
                    .show();
                    
                    principal.guardarPrestacion.hide();
                }
            });
        }
    }

    $(document).on('click', '#siguienteExCta', function(e){
        e.preventDefault();
        principal.nuevaPrestacion.hide();
        principal.prestacionLimpia.show();

        principal.guardarPrestacion.trigger('click');

        verificarExamenCuenta();
    });

    $(document).on('click', '.volverPrestacion', function(e){
        e.preventDefault();
        principal.seleccionExCta.hide();
        principal.nuevaPrestacion.show();
    });

    variables.examen.select2({
        placeholder: 'Seleccionar exámen...',
        dropdownParent: principal.altaPrestacionModal,
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

    $(document).on('click','.buscarExamen', function(e){
        e.preventDefault();

        if(!variables.examen.val()) {
            
            toastr.warning('Debe seleccionar un exámen','',{timeOut: 1000});
            return;
        }

        tablasExamenes(variables.selectClientes.val(), true, '#lstEx', variables.examen.val());
        variables.examen.val('');
        variables.examen.remove();
    });

    $(document).on('click', '.reiniciarExamen', function(e){
        e.preventDefault();
        tablasExamenes(variables.selectClientes.val(), true, '#lstEx');
    });

    $(document).on('click', '.deleteMasivo, .deleteEx', function(e){
        e.preventDefault();

        if($(this).hasClass('deleteEx')) {
            $(this).closest('tr').remove();
            return;
        }
    
        let ids = principal.listEdicion.find('input[name="Id_exa"]:checked'), checkAll = principal.checkAllEx.prop('checked');
    
        if(ids.length === 0 && checkAll === false) {
            toastr.warning("Debe seleccionar un exámen para sacarlo del listado",'',{timeOut: 1000});
            return;
        }
    
        ids.each(function() {
            $(this).closest('tr').remove();
        });

        toastr.success("Se elimino todo correctamente",'',{timeOut: 1000});
    });
    

    principal.checkAllEx.on('click', function() {
        $('input[type="checkbox"][name="Id_exa"]:not(#checkAllEx)').prop('checked', this.checked);
    });
  

    function cambioEstadoDown() {
        let borrar = principal.filapresId.data('filapres');
        $('tr[data-filapres="' + borrar + '"]').hide();
    }

    function cambioEstadoBlock() {
        principal.blockPrestPaciente.prop('disabled', true);
        
        principal.estadoBadge
            .removeClass('badge badge-soft-success')
            .addClass('badge badge-soft-danger')
            .text('Bloqueado');
        
        getListado(null);
    }

    async function getMap(idEmpresa, idArt){

        $.get(getMapas, {empresa: idEmpresa, art: idArt})
            .done(await function(response){
   
                let mapas = response.mapas;

                variables.IdMapa
                    .add(variables.IdMapaN)
                    .empty();
                
                if(mapas.length === 0)
                {
                    variables.IdMapa
                        .add(variables.IdMapaN)
                        .empty()
                        .append('<option title="Sin mapas disponibles para esta ART y Empresa." value="0" selected>Sin mapas disponibles.</option>');

                }else{

                    for(let index = 0; index < mapas.length; index++) {
                        let d = mapas[index],
                                contenido = `<option value="${d.Id}">${d.Nro} | Empresa: ${d.RSE} - ART: ${d.RSArt}</option>`;
    
                        variables.IdMapa
                            .variables.IdMapaN
                            .append(contenido);

                    }
                } 
            })
    }

    function precargaTipoPrestacion(val){
        return val === 'ART' ? principal.selectMapaPres.show() : principal.selectMapaPres.hide();
    }

    function getListado(buscar) {

        preloader('on');
        $.ajax({
            url: searchPrestPacientes,
            type: 'GET',
            data: {
                buscar: buscar,
                paciente: ID
            },
            success: function(response){

                preloader('off');
                let pacientes = response.pacientes;
                principal.results.hide();

                principal.listaPacientes.find('tbody').empty();
                $.each(pacientes.data, function(index, papre) {

                    let cerradoAdjunto = papre.CerradoAdjunto || 0,
                        total = papre.Total || 1,
                        calculo = parseFloat(((cerradoAdjunto / total) * 100).toFixed(2)),
                        resultado = (calculo === 100) ? 'fondo-blanco' : (calculo >= 86 && calculo <= 99) ? 'fondo-verde' : (calculo >= 51 && calculo <= 85) ? 'fondo-amarillo' : (calculo >= 1 && calculo <= 50) ? 'fondo-naranja' : 'fondo-rojo';
                    
                    let porcentaje = parseFloat(((cerradoAdjunto / total) * 100).toFixed(2)) === 0 ? '0.00' : parseFloat(((cerradoAdjunto / total) * 100).toFixed(2));

                    let row = `<tr data-id="${papre.Id}">
                                <td>
                                    <div class="${papre.Anulado == 0 ? resultado : "rojo"}">${papre.Anulado == 0 ? porcentaje + `%` : '0.00%'}</div>
                                </td>
                                <td>
                                    ${fechaNow(papre.FechaAlta,'/',0)}
                                </td>
                                <td>
                                    ${papre.Id}
                                </td>
                                <td>
                                    ${papre.Tipo}
                                </td>
                                <td title="${papre.Empresa}">
                                    ${papre.Empresa}
                                </td>
                                <td title="${papre.ParaEmpresa}">
                                    ${papre.ParaEmpresa}
                                </td>
                                <td title="${papre.Art}">
                                    ${papre.Art}
                                </td>
                                <td>
                                    <span>${comprobarEstado(papre.Cerrado, papre.Finalizado, papre.Entregado)}</span>
                                </td>
                                <td>
                                    ${(`<div class="text-center"><i class="${papre.eEnviado === 1 ? `ri-checkbox-circle-fill verde` : ``}"></i></div>`)}
                                </td>
                                <td>
                                    <div class="text-center">${(papre.Incompleto === 1 ? `<i class="ri-check-line"></i>` : `-`)}</div>
                                </td>
                                <td>
                                    <div class="text-center">${(papre.Ausente === 1 ? `<i class="ri-check-line"></i>` : `-`)}</div>
                                </td>
                                <td>
                                    <div class="text-center">${(papre.Forma === 1 ? `<i class="ri-check-line"></i>` : `-`)}</div>
                                </td>
                                <td>
                                    <div class="text-center">${(papre.Devol === 1 ? `<i class="ri-check-line"></i>` : `-`)}</div>
                                </td>
                                <td>
                                    ${(["B","C"].includes(papre.Pago)
                                        ? "Ctdo" 
                                        : papre.Pago == "P" 
                                            ? "ExCta" 
                                            : "CC")}
                                </td>
                                <td>
                                   <div class="text-center"> ${(papre.Facturado === 1 ? `<i class="ri-check-line"></i>` : `-`)}</div>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a title="Editar" href="${url.replace('__prestacion__', papre.Id)}">
                                            <button type="button" class="btn btn-sm iconGeneralNegro"><i class="ri-edit-line"></i></button>
                                        </a>
                                        <div class="bloquear">
                                            <button type="button" id="blockPrestPaciente" data-idprest="${papre.Id}" class="btn btn-sm iconGeneralNegro" title="${(papre.Anulado == 1 ? "Bloqueado" : "Bloquear")}" ${(papre.Anulado == 1 ? "disabled" : "")}><i class="ri-forbid-2-line"></i></button>
                                        </div>
                                        <button type="button" id="downPrestPaciente" data-idprest="${papre.Id}"class="btn btn-sm iconGeneralNegro"><i class="ri-delete-bin-2-line"></i></button>
                                    </div>
                                </td>
                            </tr>`;


                    principal.listaPacientes.find('tbody').append(row);
                });
            }
        });
    }

    function comprobarEstado(cerrado, finalizado, entregado) {
        let estado = `${cerrado}${finalizado}${entregado}`;
    
        switch (estado) {
            case "100":
                return "Cerrado";
            case "110":
                return "Finalizado";
            case "111":
                return "Entregado";
            case "000":
                return "Abierto";
            default:
                return "-";
        }
    }

    async function getUltimasFacturadas(id) {

        principal.grillaFacturadas.empty();
        preloader('on');
        $.get(lstFacturadas, {Id: id})
            .done(async function(response){
                preloader('off');
                let promises = response.map(async function(r){
                    let listadoResultado = await lstFacturados(r.NroPrestacion);
                    
                    // Divide el texto en trozos de 147 caracteres
                    let resultadoDividido = listadoResultado.match(/.{1,147}/g);
                    
                    // Une los trozos con un salto de línea
                    let resultadoFormateado = resultadoDividido.join('<br>');
                    
                    let contenido = `
                        <tr>
                            <td><span class="rojo fw-bolder">${r.NroPrestacion}</span> - ${r.TipoPrestacion} - ${r.Apellido} ${r.Nombre}</td>
                        </tr>
                        <tr class="borde-inferior">
                            <td class="text-break small">${resultadoFormateado}</td>
                        </tr>
                    `;
                    return contenido;
                });
    
                let mostrar = await Promise.all(promises);
                mostrar.forEach(ver => principal.grillaFacturadas.append(ver));
            });
    }
    

    async function lstFacturados(id)
    {
        return new Promise((resolve, reject) => {
            preloader('on');
            $.get(lstExamenes, {Id: id})
                .done(async function(response){
                    preloader('off');
                    let result = '';
                    if(response && response.length) {

                        for (let r of response) {
                            result += r.NombreExamen + ' - ';
                        }
                        
                    }else{
                        result = "No hay examenes";
                    }
                    resolve(result);
                })
                .fail(function(error){
                    reject(error);
                })
        });
    }

    function listadoConSaldos(id) //Desde financiador tomo empresa
    {
        principal.disponiblesExamenes.empty();
        preloader('on');
        $.get(saldoNoDatatable, {Id: id})
            .done(function(response){
                preloader('off');

                for(let index = 0; index < response.length; index++) {
                    let r = response[index],
                    contenido = `
                        <tr>
                            <td>${r.contadorSaldos}</td>
                            <td>${r.Examen}</td>
                        </tr>
                    `;
                    principal.disponiblesExamenes.append(contenido);
                }
            });
    }

    async function cantidadDisponibles(id)
    {
        principal.totalCantidad.empty();

        $.get(lstExDisponibles, {Id: id})
            .done(await function(response){
                principal.totalCantidad.text(response.length);
                if(response.length === 0) {
                    principal.siguienteExCta.hide();
                    principal.guardarPrestacion.show();
                }else if(response.length > 0) {
                    principal.siguienteExCta.show();
                    principal.guardarPrestacion.hide();
                }
            })
    }

    /******************************Nueva pantalla de prestaciones **************************************/
    
    principal.prestacionLimpia
        .add(principal.resultadosPaciente)
        .add(principal.reportesPacientes)
        .hide();

    principal.resulPaciente.on('click', function(e){
        e.preventDefault();

        principal.prestacionLimpia
            .add(principal.resultadosPaciente)
            .add(principal.editarComentario)
            .hide();
        principal.resultadosPaciente.show();
    });

    principal.volverPrestacionLimpia.on('click', function(e){

        e.preventDefault();
        principal.resultadosPaciente
            .add(principal.reportesPacientes)
            .add(principal.editarComentario)
            .add(principal.seleccionExCta)
            .hide();
        principal.prestacionLimpia.show();
        variables.ComentarioEditar.val('');
    });

    $(document).on('click', '.imprimirReportes', function(e){
        e.preventDefault();

        principal.resultadosPaciente
            .add(principal.prestacionLimpia)
            .add(principal.editarComentario)
            .hide();

        principal.reportesPacientes.show();
    }); 

    $(document).on('click', '.editarComentarioBtn', async function(e){
        e.preventDefault();

        let id = $(this).data('id'), data  = await await $.get(getComentario,{Id: id});

        principal.prestacionLimpia
        .add(principal.resultadosPaciente)
        .add(principal.reportesPacientes)
        .hide();

        principal.editarComentario.show();

        variables.ComentarioEditar
            .empty()
            .val(data[0].Comentario);
        
        variables.IdObservacion
            .empty()
            .val(data[0].Id);
    });

    principal.imprimirRepo.on('click', function(e){
        e.preventDefault();

        let infInternos = variables.infInternos.prop('checked'),
            pedProveedores = variables.pedProveedores.prop('checked'),
            conPaciente = variables.conPaciente.prop('checked'),
            resAdmin = variables.resAdmin.prop('checked'),
            caratula = variables.caratula.prop('checked'),
            consEstDetallado = variables.consEstDetallado.prop('checked'),
            consEstSimple = variables.consEstSimple.prop('checked');

        let verificar = [
                infInternos,
                pedProveedores,
                conPaciente,
                resAdmin,
                caratula,
                consEstDetallado,
                consEstSimple
            ];

        if (verificar.every(val => !val || (Array.isArray(val) && val.length === 0) || (typeof val === 'object' && Object.keys(val).length === 0))) {
            toastr.warning('Debe seleccionar alguna opción para poder imprimir los reportes','',{timeOut: 1000});
            return;
        }
        preloader('on');
        $.get(impRepo, {
            Id: variables.idPrestacion.val(), 
            infInternos: infInternos, 
            pedProveedores: pedProveedores, 
            conPaciente: conPaciente, 
            resAdmin: resAdmin, 
            caratula: caratula, 
            consEstDetallado: consEstDetallado, 
            consEstSimple: consEstSimple
        })
            .done(function(response){
                preloader('off');
                createFile("pdf", response.filePath, response.name);
                toastr.success(response.msg,'',{timeOut: 1000});
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return; 
            });
    });


    variables.exam.select2({
        dropdownParent: principal.altaPrestacionModal,
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

    variables.paquetes.select2({
        dropdownParent: principal.altaPrestacionModal,
        placeholder: 'Seleccionar paquete...',
        language: {
            noResults: function() {

            return "No hay paquete con esos datos";        
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
           url: getPaquetes,
           dataType: 'json',
           delay: 250,
           data: function(params) {
                return {
                    buscar: params.term,
                };
           },
           processResults: function(data) {
                return {
                    results: data.paquete
                };
           },
           cache: true,
        },
        minimumInputLength: 2
    });

    $(document).on("select2:open", () => {
        document.querySelector(".select2-container--open .select2-search__field").focus()
    });
  
    principal.addPaquete.on('click', function(e){
        e.preventDefault();
        
        if(!variables.paquetes.val()){
            toastr.warning("Debe seleccionar un paquete para poder añadirlo en su totalidad",'',{timeOut: 1000});
            return;
        }

        let idPrestacion = variables.idPrestacion.val();

        preloader('on');

       $.post(paqueteId,{_token: TOKEN, IdPaquete: variables.paquetes.val(), IdPrestacion: idPrestacion})
            .done(function(){
                principal.listaExamenes.empty();
                variables.exam.val([]).trigger('change.select2');
                variables.paquetes.trigger('change.select2');
                cargarExamen(idPrestacion);
                contadorExamenes(idPrestacion);
                preloader('off');
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return; 
            });
       
    });

    $(document).on('click', '.deleteExamenes, .deleteExamen', function(e){

        e.preventDefault();

        let ids = [], tieneAdjunto = false, id = $(this).data('delete'), adjunto, archivos; checkAll ='';
        if ($(this).hasClass('deleteExamenes')) {

            $('input[name="Id_examenes"]:checked').each(function() {
                adjunto = $(this).data('adjunto'), archivos = $(this).data('archivo');
                adjunto == 1 && archivos > 0 ? tieneAdjunto = true : ids.push($(this).val());
            });

        } else if($(this).hasClass('deleteExamen')) {

            adjunto = $(this).data('adjunto'), archivos = $(this).data('archivo');
            adjunto == 1 && archivos > 0 ? tieneAdjunto = true : ids.push(id);
        }

        if (tieneAdjunto) {
            toastr.warning('El o los examenes seleccionados tienen un reporte adjuntado. El mismo no se podrá eliminar.', 'Atención', { timeOut: 1000 });
            return;
        }

        if(ids.length === 0 && principal.checkAllExa.prop('checked') === false){
            toastr.warning('No hay examenes seleccionados', 'Atención', { timeOut: 1000 });
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
                        cargarExamen(IdNueva);
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

    principal.addExamen.on('click', function(e){
        e.preventDefault();

        let id = [];

        if(!variables.exam.val()) {
            toastr.warning("Debe seleccionar un examen para poder añadirlo a la lista", "Atención", { timeOut: 1000 });
            return;
        }

        id.push(variables.exam.val());
        saveExamen(id, variables.idPrestacion.val());
    });

    principal.altaPrestacionModal.on('hidden.bs.modal', function () {
        principal.prestacionLimpia
            .add(principal.observacionesModal)
            .add(principal.nuevaPrestacion)
            .add(principal.reportesPacientes)
            .hide();

        principal.fichaLaboralModal.show();
        checkExamenesCuenta(IDFICHA);
    });

    principal.checkAllExa.on('click', function(){
        $('input[type="checkbox"][name="Id_examenes"]:not(#checkAllExa)').prop('checked', this.checked);
    });

    principal.finalizarWizzard.on('click', function(e){
        e.preventDefault();

        preloader('on');
        $.post(obsNuevaPrestacion, {
            _token: TOKEN, 
            IdPrestacion: variables.idPrestacion.val() ?? '', 
            Observaciones: variables.ObservacionesPresN.val() ?? '', 
            ObsExamenes: variables.ObsExamenesN.val() ?? '',
            Obs: variables.ObsN.val() ?? ''})
            .done(function(response){
                preloader('off');
                toastr.success(response.msg,'',{timeOut: 1000});
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;  
            });
    });
    
    principal.SalirWizzard.on('click', function(e){
        e.preventDefault();
        location.reload();
    });

    $(document).on('click','.openExamen', function(e) {
        e.preventDefault();

        let id = $(this).data('id'),
            url = location.href,
            open = url.replace(/\/pacientes\/.*/, ''),
            redireccionar = open + '/itemsprestaciones/' + id + '/edit';

        window.open(redireccionar, '_blank');
    });

    $(document).on('click', '.bloquearExamenes, .bloquearExamen', function(e){

        e.preventDefault();

        let ids = [], id = $(this).data('bloquear'), checkAll ='';

        if ($(this).hasClass('bloquearExamenes')) {

            $('input[name="Id_examenes"]:checked').each(function() {
                ids.push($(this).val());
            });
        
        }else if($(this).hasClass('bloquearExamen')){
            ids.push(id);
        }
        
        if (ids.length === 0 && principal.checkAllExa.prop('checked') === false) {
            toastr.warning('No hay examenes seleccionados', 'Atención', { timeOut: 1000 });
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
                        let estados = [];
                        preloader('off')

                        for(let index = 0; index < response.length; index++) {
                            let msg = response[index],
                                tipoRespuesta = {
                                    success: 'success',
                                    fail: 'info'
                                }
                            toastr[tipoRespuesta[msg.estado]](msg.message, "Atención", { timeOut: 1000 })
                            estados.push(msg.estado)
                        }
                        
                        if(estados.includes('success')) {
                            principal.listaExamenes.empty();
                            variables.exam.val([]).trigger('change.select2');
                            variables.paquetes.val([]).trigger('change.select2');
                            cargarExamen(IdNueva);
                            contadorExamenes(IdNueva);
                        }
                    }
                });
            }
        });     
    });

    principal.deleteComentario.on('click', function(e){
        e.preventDefault;

        let id = $(this).data('id');

        swal({
            title: "¿Está seguro que desea eliminar el comentario privado?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {

                preloader('on')
                $.get(eliminarComentario, {Id: id})
                    .done(function(response){
                        preloader('off');
                        toastr.success(response.msg,'',{timeOut: 1000});
                        comentariosPrivados();
                    })
                    .fail(function(jqXHR){
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return;
                    })
            }
        })
    });

    principal.confirmarEdicion.on('click', function(e){
        e.preventDefault();

        preloader('on')
        $.get(editarComentario, {Id: variables.IdObservacion.val(), Comentario: variables.ComentarioEditar.val()})
            .done(function(response){
                principal.volverPrestacionLimpia.trigger('click');
                principal.comentarioPrivado.modal('hide');
                preloader('off')
                toastr.success(response.msg,'',{timeOut: 1000});
                comentariosPrivados();
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            });
    });

    principal.confirmarComentarioPriv.on('click', function(e){
        e.preventDefault();

        if(variables.Comentario.val() === ''){
            toastr.warning('La observación no puede estar vacía','',{timeOut: 1000});
            return;
        }

        preloader('on');
        $.post(savePrivComent, {
            _token: TOKEN, 
            Comentario: variables.Comentario.val(), 
            IdEntidad: variables.idPrestacion.val(), 
            obsfasesid: 2})

            .done(function(){
                preloader('off');
                toastr.success('Se ha generado la observación correctamente','',{timeOut: 1000});

                setTimeout(() => {
                    principal.privadoPrestaciones.empty();
                    variables.Comentario.val("");
                    comentariosPrivados();
                }, 3000);
            })
    });

    function saveExamen(id, idPrestacion){

        idExamen = [];

        for(let i = 0; i < id.length; i++){
        idExamen.push(id[i]);
        }

        if (idExamen.length === 0) {
            toastr.warning("No existe el exámen o el paquete no contiene examenes", "Atención", { timeOut: 1000 });
            return;
        }
        preloader('on');
        $.post(saveItemExamenes,{_token: TOKEN, idPrestacion: idPrestacion, idExamen: idExamen})
            .done(function(){
                principal.listaExamenes.empty();
                $('#estudios').empty()
                cargarEstudiosImp();
                variables.exam.val([]).trigger('change.select2');
                variables.paquetes.trigger('change.select2');
                cargarExamen(idPrestacion);
                contadorExamenes(idPrestacion);
                preloader('off');
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;  
            })
    }

    async function cargarExamen(id) {
        try {
            preloader('on');

            let response = await $.get(getExamenesEstandar,{Id: id, tipo: 'listado'});

            preloader('off');
            for(let index = 0; index < response.length; index++){
                let examen = response[index],
                fila = `
                    <tr ${examen.Anulado === 1 ? 'class="filaBaja"' : ''}>
                        <td>
                            <input type="checkbox" name="Id_examenes" value="${examen.IdItem}" checked ${examen.Anulado === 1 ? 'disabled' : ''}>
                        </td>
                        <td data-idexam="${examen.IdExamen}" id="${examen.IdItem}" style="text-align:left;">${examen.Nombre} ${examen.Anulado === 1 ? '<span class="custom-badge rojo">Bloqueado</span>' : ''}</td>
                        <td style="width: 100px">    
                            <div class="d-flex gap-2">
                                    <div class="bloquear">
                                    <button data-id="${examen.IdItem}" class="btn btn-sm iconGeneral openExamen" title="Ver">
                                        <i class="ri-zoom-in-line"></i>
                                    </button>
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

                principal.listaExamenes.append(fila);
            }

            principal.listado.fancyTable({
                pagination: true,
                perPage: 50,
                searchable: false,
                globalSearch: false,
                sortable: false, 
            });
            
    
        } catch (error) {
            preloader('off');
            let errorData = JSON.parse(error.responseText);
            checkError(error.status, errorData.msg);
        } finally {
            preloader('off');
        }
    }

    async function checkExamenesCuenta(id){

        $.get(await lstExDisponibles, {Id: id})
            .done(function(response){
                // let data = selectorPago(pagoInput);

                if(response && response.length > 0) {

                    principal.alertaExCta
                        .add(principal.ultimasFacturadas)
                        .add(principal.siguienteExCta)
                        .show();

                    variables.PagoLaboral.val('P');
                    
                    principal.guardarPrestacion.hide();


                } else {
                    principal.ultimasFacturadas
                        .add(principal.alertaExCta)
                        .add(principal.siguienteExCta)
                        .hide();

                    variables.PagoLaboral.val(data);
                    
                    principal.guardarPrestacion.show();
                }
            })
    }

    async function comentariosPrivados() {

        principal.privadoPrestaciones.empty();
        preloader('on');

        $.get(await privateComment, {Id: variables.idPrestacion.val(),  tipo: 'prestacion'})
            .done(function(response){
                preloader('off');
                let data = response.result;

                for(let index = 0; index < data.length; index++){
                    let d = data[index],
                        contenido =  `
                            <tr>
                                <td style="width: 120px">${fechaCompleta(d.Fecha)}</td>
                                <td style="width: 120px" class="text-capitalize">${d.IdUsuario}</td>
                                <td style="width: 120px" class="text-uppercase">${d.nombre_perfil}</td>
                                <td>${d.Comentario}</td>
                                <td style="width: 60px">${USER === d.IdUsuario ? `
                                    <button type="button" data-id="${d.Id}" data-comentario="${d.Comentario}" class="btn btn-sm iconGeneralNegro editarComentarioBtn"><i class="ri-edit-line"></i></button>
                                    <button title="Eliminar" type="button" data-id="${d.Id}"  class="btn btn-sm iconGeneralNegro deleteComentario"><i class="ri-delete-bin-2-line"></i></button>` : ''}</td>
                            </tr>
                        `;
                        principal.privadoPrestaciones.append(contenido);
                }

                principal.lstPrivPrestaciones.fancyTable({
                    pagination: true,
                    perPage: 15,
                    searchable: false,
                    globalSearch: false,
                    sortable: false, 
                });
            })   
    }

    function contadorExamenes(idPrestacion) {
        $.get(contadorEx, {Id: idPrestacion}, function(response){
            principal.countExamenes
                .empty()
                .text(response);
        });
    }

    function verificarExamenCuenta()
    {
        if(variables.PagoLaboral.val() === 'P') {
            principal.paqueteExamen.hide();
            principal.paqueteExCta.show();
            principal.tituloPrestacion.text('Alta Prestación con Ex. a Cuenta');

        }else{
            principal.paqueteExamen.show();
            principal.paqueteExCta.hide();
            principal.tituloPrestacion.text('Alta Prestación');
        }
    }

    async function tablasExamenes(idCliente, checkVisible, etiquetaId, filtroId = null) {
        preloader('on');

        try {
            let data = await $.get(getListaExCta, { Id: idCliente });

            $(etiquetaId).empty();

            const factura = {};
            data.forEach(function (item) {
                if (!factura[item.Factura]) {
                    factura[item.Factura] = [];
                }
                factura[item.Factura].push(item);
            });

            let tablaCompleta = '';

            for (const grupoFactura in factura) {
                if (factura.hasOwnProperty(grupoFactura)) {
                    const examenes = factura[grupoFactura];
                    const documentos = {};

                    examenes.forEach((examen) => {
                        const documentoKey = examen.Documento || "Sin precarga";
                        if (!documentos[documentoKey]) {
                            documentos[documentoKey] = [];
                        }
                        documentos[documentoKey].push(examen);
                    });

                    let contenidoFactura = `
                        <tr class="fondo-gris">
                            <td colspan="5">
                                <span class="fw-bolder text-capitalize">fact </span> ${grupoFactura}
                            </td>
                            ${checkVisible ? `<td style="width:5px"><input type="checkbox" class="form-check-input" id="checkAll-${grupoFactura}"></td>` : ''}
                        </tr>
                    `;

                    for (const documentoKey in documentos) {
                        if (documentos.hasOwnProperty(documentoKey)) {
                            const examenesPorDocumento = documentos[documentoKey];
                            const idSubgrupo = documentoKey === "Sin precarga" ? `sin_precarga-${grupoFactura}` : documentoKey;

                            contenidoFactura += `
                                <tr class="fondo-grisClaro mb-2">
                                    <td colspan="5" class="fw-bolder">
                                        <span class="fw-bolder precarga">${documentoKey === "Sin precarga" ? "" : "DNI Precargado: "}</span> 
                                        ${documentoKey}
                                    </td>
                                    ${checkVisible ? `<td style="width:5px"><input type="checkbox" class="form-check-input" id="checkAll-${idSubgrupo}"></td>` : ''}
                                </tr>
                            `;

                            const listaParaRenderizar = filtroId !== null 
                                ? examenesPorDocumento.filter((examen) => examen.IdFiltro === parseInt(filtroId, 10))
                                : examenesPorDocumento;

                            listaParaRenderizar.forEach((examen) => {
                                contenidoFactura += `
                                    <tr>
                                        <td>${examen.CantidadExamenes}</td>
                                        <td colspan="4">${examen.NombreExamen}</td>
                                        ${checkVisible ? `
                                        <td style="width:5px">
                                            <input type="checkbox" 
                                                class="form-check-input item-checkbox" 
                                                data-factura="${grupoFactura}" 
                                                data-subgrupo="${idSubgrupo}"
                                                value="${examen.IdEx}">
                                        </td>
                                        ` : ''}
                                    </tr>`;
                            });
                        }
                    }
                    tablaCompleta += contenidoFactura;
                }
            }
            $(etiquetaId).append(tablaCompleta);

        } catch (error) {
            let errorData = JSON.parse(error.responseText);
            checkError(error.status, errorData.msg);
        } finally {
            preloader('off');
        }
    }

     principal.lstEx.on('click', 'input.form-check-input[id^="checkAll"]', function () {
        let checkAll = $(this), 
            isChecked = checkAll.is(':checked'), 
            fila = checkAll.closest('tr');

        if(fila.hasClass('fondo-gris')) {
            
            let rangoFactura = fila.nextUntil('tr.fondo-gris'),
                checkboxesHijos = rangoFactura.find('input.form-check-input');
            
            checkboxesHijos.prop('checked', isChecked);
        } 

        else if(fila.hasClass('fondo-grisClaro')) {

            let rangoSubgrupo = fila.nextUntil('tr.fondo-gris, tr.fondo-grisClaro'),
                checkboxesHijos = rangoSubgrupo.find('input.item-checkbox');
            
            checkboxesHijos.prop('checked', isChecked);
        }
    });

    function tablaExaCantidad(data)
    {
        principal.lstExamenesCtd.empty();

        const examenes = data.examen;

        for(let index = 1; index < examenes.length; index++) {
            let examen = examenes[index],
                contenido = `
                <tr>
                    <td>${examen.text}</td>
                    <td><input type="checkbox" name="itemCheckbox" value="${examen.id}"></td>
                </tr>
                `;

                principal.lstExamenesCtd.append(contenido);
        }

        principal.listadoExamenesCtd.fancyTable({
            pagination: true,
            perPage: 15,
            searchable: false,
            globalSearch: false,
            sortable: false, 
        });


    }

        async function cargarEstudiosImp()
    {
        $('#estudios').empty();

        if(!ID) return;
        
        preloader('on');
        $.get(await listadoEstudiosImp, {Id: ID})

          .done(function(response){
                preloader('off');
                for(let index = 0; index < response.length; index++) {
                    let data = response[index],
                        forNombre = (data.NombreExamen).replace(" ", "-"),
                        contenido = `
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="${data.IdReporte}" data-examen="${data.IdExamen}" data-nosend checked>
                                <label class="form-check-label" for="${forNombre}">
                                    ${data.NombreExamen}
                                </label>
                            </div>
                        `;

                    $('#estudios').append(contenido);
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