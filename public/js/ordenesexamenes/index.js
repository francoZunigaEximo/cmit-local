$(document).ready(function(){

    $('#fechaHasta, #fechaHastaAsignados, #fechaHastaAdjunto, #fechaHastaInf, #fechaHastaAsignadosInf, #fechaHastaAdjuntoInf, #fechaHastaPres, #fechaHastaEEnviar').val(fechaNow(null, "-", 0)), $('#efectorPres').val('pendientes'),$('#tipoPres').val('todos');
    
    let especialidadVal = $('#especialidad').val(),
        especialidadAsigVal = $('#especialidadAsignados').val(),
        especialidadAdjVal = $('#especialidadAdjunto').val(),
        especialidadInf = $('#especialidadInf').val(),
        especialidadAsigVaInf = $('#especialidadAsignadosInf').val(),
        especialidadAdjValInf = $('#especialidadAdjuntoInf').val(),
        especialidadPres = $('#especialidadPres').val(),
        lstEspecialidades = $('#especialidad, #especialidadAsignados, #especialidadAdjunto, #especialidadInf, #especialidadAsignadosInf, #especialidadAdjuntoInf, #especialidadPres');

    $('#Liberar, #Cerrar, #Abrir, #qrExamen, #LiberarInf').hide();

    listaProveedores();
    optionsGeneral(especialidadVal, "efector", "efector");
    optionsGeneral(especialidadAsigVal, "efectorAsignado", "efector");
    optionsGeneral(especialidadAdjVal, "efectorAdjunto", "efector");
    optionsGeneral(especialidadPres, "efectorPres", "efector");
    optionsGeneral(especialidadInf, "informador", "informador");
    optionsGeneral(especialidadAsigVaInf, "informadorAsignadoInf", "informador");
    optionsGeneral(especialidadAdjValInf, "informadorAdjuntoInf", "informador");
    optionsGeneral(especialidadPres, "informadorPres", "informador");

    $(document).on('change', '.especialidad, .especialidadAsignados, .especialidadAdjunto, .especialidadInf, .especialidadAsignadosInf, .especialidadAdjuntoInf, .especialidadPres', function() {

        let newEspecialidadVal = $('.especialidad').val(),
            newEspecialidadAsigVal = $('.especialidadAsignados').val(),
            newEspecialidadAdjVal = $('.especialidadAdjunto').val(),
            newEspecialidadInf = $('.especialidadInf').val(),
            newEspecialidadAsigValInf = $('.especialidadAsignadosInf').val(),
            newEspecialidadAdjValInf = $('.especialidadAdjuntoInf').val(),
            newEspecialidadEfePres = $('.especialidadPres').val(),
            newEspecialidadInfPres = $('.especialidadPres').val();

        optionsGeneral(newEspecialidadVal, "efector", "efector");
        optionsGeneral(newEspecialidadAsigVal, "efectorAsignado", "efector");
        optionsGeneral(newEspecialidadAdjVal, "efectorAdjunto", "efector");
        optionsGeneral(newEspecialidadInf, "informador", "informador");
        optionsGeneral(newEspecialidadAsigValInf, "informadorAsignadoInf", "informador");
        optionsGeneral(newEspecialidadAdjValInf, "informadorAdjuntoInf", "informador");
        optionsGeneral(newEspecialidadEfePres, "efectorPres", "efector");
        optionsGeneral(newEspecialidadInfPres, "informadorPres", "informador");
    });
    
    $('#empresa, #empresaInf, #empresaAsignados, #empresaAdjunto, #empresaAsignadosInf, #empresaAdjuntoInf, #empresaEEnviar').each(function() {
        $(this).select2({
            language: {
                noResults: function() {
    
                return "No hay empresas con esos datos";        
                },
                searching: function() {
    
                return "Buscando..";
                },
                inputTooShort: function () {
                    return "Por favor, ingrese 2 o más caracteres";
                }
            },
            placeholder: 'Nombre Empresa, Alias o ParaEmpresa',
            allowClear: true,
            ajax: {
                url: getClientes, 
                dataType: 'json',
                data: function(params) {
                    return {
                        buscar: params.term,
                        tipo: 'E'
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.clientes 
                    };
                },
                cache: true
            },
            minimumInputLength: 2 
        });
    });

    $('#artAdjunto, #artAdjuntoInf').select2({
        language: {
            noResults: function() {

            return "No hay art con esos datos";        
            },
            searching: function() {

            return "Buscando..";
            },
            inputTooShort: function () {
                return "Por favor, ingrese 2 o más caracteres";
            }
        },
        placeholder: 'Nombre de la ART',
        allowClear: true,
        ajax: {
            url: getClientes, 
            dataType: 'json',
            data: function(params) {
                return {
                    buscar: params.term,
                    tipo: 'A'
                };
            },
            processResults: function(data) {
                return {
                    results: data.clientes 
                };
            },
            cache: true
        },
        minimumInputLength: 2 
    });

    $('#examen, #examenAsignados, #examenInf, #examenAsignadosInf, #examenPres').each(function(){
        $(this).select2({
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
            placeholder: 'Nombre del exámen',
            allowClear: true,
            ajax: {
                url: searchExamen, 
                dataType: 'json',
                data: function(params) {
                    return {
                        buscar: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.examen 
                    };
                },
                cache: true
            },
            minimumInputLength: 2
        });
    });

    $('#paciente, #pacienteInf, #pacienteAsignados, #pacienteAsignadosInf, #pacienteEEnviar').each(function(){
        $(this).select2({
            language: {
                noResults: function() {
    
                return "No hay pacientes con esos datos";        
                },
                searching: function() {
    
                return "Buscando..";
                },
                inputTooShort: function () {
                    return "Por favor, ingrese 2 o más caracteres";
                }
            },
            placeholder: 'Nombre y/o apellido del paciente',
            allowClear: true,
            ajax: {
                url: getPacientes, 
                dataType: 'json',
                data: function(params) {
                    return {
                        buscar: params.term,
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.pacientes 
                    };
                },
                cache: true
            },
            minimumInputLength: 2 
        });
    });
 
    $(document).on('click', '#asigEfector, #asigInf', function(e){
        e.preventDefault();
        let obj = {
            asigEfector: ['#efectores', '#checkAllAsignar', '#listaOrdenesEfectores', 'Id_asignar'],
            asigInf: ['#informadores', '#checkAllAsigInf', '#listaOrdenesInformadores', 'Id_asigInf']
        }

        let seleccion = $(this).attr('id'), profesional = $(obj[seleccion][0]).val(); 
        
        let ids = [], checkAll = $(obj[seleccion][1]).prop('checked');

        $('input[name="' + obj[seleccion][3] + '"]:checked').each(function() {
            ids.push($(this).val());
        });

        if (profesional == '' || profesional == '0') { 
            toastr.warning('No se ha seleccionado un Profesional para asignar');
            return;
        }
    
        if(ids.length === 0 && checkAll === false){
            toastr.warning('No hay ningun exámen seleccionado para asignar');
            return;
        }
        
        swal({
            title: "¿Desea confirmar la operación?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                $.post(asignarProfesional, { _token: TOKEN, Ids: ids, IdProfesional: profesional, tipo: seleccion })
                .done(function(response){

                    let data = response.message;
                    toastr.info(data, "Información");
                    $(obj[seleccion][2]).DataTable().draw(false)

                })
                .fail(function(jqXHR) {
                    preloader('off');
                    let errorData = JSON.parse(jqXHR.responseText);            
                    checkError(jqXHR.status, errorData.msg);
                    return; 
                });
            }
        });
    });

    $(document).on('click', '.btnAbrir', function(e){

        e.preventDefault();

        let ids = [];

        $('input[name="Id_asignado"]:checked').each(function() {
            ids.push($(this).val());
        });

        let checkAll =$('#checkAllAsignado').prop('checked');

        if(ids.length === 0 && checkAll === false){
            toastr.warning('No hay examenes seleccionados', 'Atención');
            return;
        }
        
        swal({
            title: "¿Desea confirmar la operación?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                preloader('on');
                $.post(updateItem, {Id : ids, _token: TOKEN, Para: 'abrir' })
                .done(function(){
                    preloader('off');
                    toastr.success('Se ha realizado la acción correctamente');                 
                    $('#listaOrdenesEfectoresAsig').DataTable().draw(false);
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

    $(document).on('click', '.btnCerrar', function(e){
        e.preventDefault();

        let ids = [];

        $('input[name="Id_asignado"]:checked').each(function() {
            ids.push($(this).val());
        });

        let checkAll =$('#checkAllAsignado').prop('checked');

        if(ids.length === 0 && checkAll === false){
            toastr.warning('No hay examenes seleccionados', 'Atención');
            return;
        }

        swal({
            title: "¿Desea confirmar la operación?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                preloader('on');
                $.post(updateItem, {Id : ids, _token: TOKEN, Para: 'cerrar' })
                .done(function(){
                    preloader('off');
                    toastr.success('Se ha realizado la acción correctamente');
                    $('#listaOrdenesEfectoresAsig').DataTable().draw(false);
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

    $(document).on('click', '.btnLiberar, .btnLiberarInf', function(e){

        e.preventDefault();

        let seleccion = $(this).hasClass('btnLiberar') ? 'btnLiberar' : 'btnLiberarInf';

        let obj = {
            //El ultimo es para tipo en el controlador
            btnLiberar: ['Id_asignado', '#checkAllAsignado', '#listaOrdenesEfectoresAsig', 'asigEfector'], 
            btnLiberarInf: ['Id_asignadoInf', '#checkAllAsignadoInf', '#listaOrdenesInformadoresAsig', 'asigInf']
        }
        
        let ids = [];

        $('input[name="' + obj[seleccion][0] + '"]:checked').each(function() {
            ids.push($(this).val());
        });

        let checkAll =$(obj[seleccion][1]).prop('checked');

        if(ids.length === 0 && checkAll === false){
            toastr.warning('No hay examenes seleccionados');
            return;
        }
       
        swal({
            title: "¿Desea confirmar la operación?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {

                preloader('on');
                $.post(asignarProfesional, { _token: TOKEN, Ids: ids, IdProfesional: 0, tipo: obj[seleccion][3]})
                .done(function(response){
                    preloader('off');
                    toastr.success(response.message);
                    $(obj[seleccion][2]).DataTable().draw(false);
                })
                .fail(function(jqXHR) {
                    preloader('off');
                    let errorData = JSON.parse(jqXHR.responseText);            
                    checkError(jqXHR.status, errorData.msg);
                    return;  
                });

            }
        });
    });

    $(document).on("select2:open", () => {
        document.querySelector(".select2-container--open .select2-search__field").focus()
    });

    $('#resetPres').click(function(){ 
        $('#form-index :input, #form-index select').val('');
        $('#examenPres').val([]).trigger('change.select2');
        $('#especialidadPres').val('');
        $('#efectoresPres').val('');
        $('#listaOrdenesPrestaciones').DataTable().clear().destroy();
        $('#fechaHastaPres').val(fechaNow(null, "-", 0));
    });

    $('#reset').click(function(){ 
        $('#form-index :input, #form-index select').val('');
        $('#examen').val([]).trigger('change.select2');
        $('#paciente').val([]).trigger('change.select2');
        $('#empresa').val([]).trigger('change.select2');
        $('#especialidad').val('');
        $('#efectores').val('');
        $('#listaOrdenesEfectores').DataTable().clear().destroy();
        $('#fechaHasta').val(fechaNow(null, "-", 0));
    });

    $('#resetInf').click(function(){ 
        $('#form-index :input, #form-index select').val('');
        $('#examenInf').val([]).trigger('change.select2');
        $('#pacienteInf').val([]).trigger('change.select2');
        $('#empresaInf').val([]).trigger('change.select2');
        $('#especialidadInf').val('');
        $('#informadores').val('');
        $('#listaOrdenesInformadores').DataTable().clear().destroy();
        $('#fechaHastaInf').val(fechaNow(null, "-", 0));
    });

    $('#resetAsignado').click(function(){ 
        $('#form-index :input, #form-index select').val('');
        $('#examenAsignados').val([]).trigger('change.select2');
        $('#pacienteAsignados').val([]).trigger('change.select2');
        $('#empresaAsignados').val([]).trigger('change.select2');
        $('#especialidadAsignados').val('');
        $('#efectorAsignado').val('');
        $('#listaOrdenesEfectoresAsig').DataTable().clear().destroy();
        $('#fechaHastaAsignado').val(fechaNow(null, "-", 0));
    });

    $('#resetAsignadoInf').click(function(){ 
        $('#form-index :input, #form-index select').val('');
        $('#examenAsignadosInf').val([]).trigger('change.select2');
        $('#pacienteAsignadosInf').val([]).trigger('change.select2');
        $('#empresaAsignadosInf').val([]).trigger('change.select2');
        $('#especialidadAsignadosInf').val('');
        $('#informadorAsignadoInf').val('');
        $('#listaOrdenesInformadoresAsig').DataTable().clear().destroy();
        $('#fechaDesdeAsignadosInf').val(fechaNow(null, "-", 0));
    });

    $('#resetAdjunto').click(function(){ 
        $('#form-index :input, #form-index select').val('');
        $('#empresaAdjunto').val([]).trigger('change.select2');
        $('#artAdjunto').val([]).trigger('change.select2');
        $('#especialidadAdjunto').val('');
        $('#efectorAdjunto').val('');
        $('#listaOrdenesEfectoresAdj').DataTable().clear().destroy();
        $('#fechaHastaAdjunto').val(fechaNow(null, "-", 0));
    });

    $('#resetAdjuntoInf').click(function(){ 
        $('#form-index :input, #form-index select').val('');
        $('#empresaAdjuntoInf').val([]).trigger('change.select2');
        $('#artAdjuntoInf').val([]).trigger('change.select2');
        $('#especialidadAdjuntoInf').val('');
        $('#informadorAdjuntoInf').val('');
        $('#listaOrdenesInformadoresAdj').DataTable().clear().destroy();
        $('#fechaHastaAdjuntoInf').val(fechaNow(null, "-", 0));
    });

    $('#checkAllAsignar').on('click', function() {

        $('input[type="checkbox"][name="Id_asignar"]:not(#checkAllAsignar)').prop('checked', this.checked);
    });

    $('#checkAllAsignado').on('click', function() {

        $('input[type="checkbox"][name="Id_asignado"]:not(#checkAllAsignado)').prop('checked', this.checked);
    });

    $('#checkAllAdj').on('click', function() {

        $('input[type="checkbox"][name="Id_adjunto"]:not(#checkAllAdj)').prop('checked', this.checked);
    });

    $('#checkAllAsigInf').on('click', function() {

        $('input[type="checkbox"][name="Id_asigInf"]:not(#checkAllAsigInf)').prop('checked', this.checked);
    });

    $('#checkAllAsignadoInf').on('click', function() {

        $('input[type="checkbox"][name="Id_asignadoInf"]:not(#checkAllAsignadoInf)').prop('checked', this.checked);
    });

    $('#checkAllAdjInf').on('click', function() {

        $('input[type="checkbox"][name="Id_adjuntoInf"]:not(#checkAllAdjInf)').prop('checked', this.checked);
    });

    $('#checkAllEEnviar').on('click', function() {

        $('input[type="checkbox"][name="Id_EEnviar"]:not(#checkAllEEnviar)').prop('checked', this.checked);
    });

    $(document).on('change', '#efectores, #informadores', function() {

        const obj = {
            efectores : ['#asigEfector', '#listaOrdenesEfectores'],
            informadores : ['#asigInf', '#listaOrdenesInformadores']
        }

        let seleccion = $(this).attr('id'), botonAsignar = $(obj[seleccion][0]);

        botonAsignar.prop('disabled', true);
        let table = $(obj[seleccion][1]).DataTable();
        table.draw(false);

        //Habilitamos el boton
        table.on('draw.dt', function() {
            botonAsignar.prop('disabled', false);
        });
    });

    $(document).on('click', '.copiarQr', function(e) {

        e.preventDefault();
        let prestacion = $(this).data('prestacion'), paciente = $(this).data('paciente'), examen = $(this).data('examen'), idexamen = $(this).data('examenid');
        let copiarQr = crearQR("A", prestacion, idexamen, paciente);
        navigator.clipboard.writeText(copiarQr)
          .then(() => alert("Se ha copiado el siguiente QR: " + copiarQr))
          .catch(err => console.error("Error al copiar al portapapeles: ", err));
    });


    $(document).on('click', '.uploadFile', function(){
        let id = $(this).data('id'), idprestacion = $(this).data('idprestacion'), tipo = $(this).data('tipo');
        $(this).off('click'); 
        $(this).next('.fileManual')
            .data('id', id)
            .data('idprestacion', idprestacion)
            .data('tipo', tipo)
            .click();
    });

    $(document).on('change', '.fileManual', function(){
        let id = $(this).data('id'), idprestacion = $(this).data('idprestacion'), archivo = $('.fileManual')[0].files[0], who = $(this).data('tipo');
       

        if (verificarArchivo(archivo)) {

            let tabla = ['efector','multiefector'].includes(who) ? "#listaOrdenesEfectoresAdj" : "#listaOrdenesInformadoresAdj";

            let formData = new FormData();
            formData.append('archivo', archivo);
            formData.append('IdEntidad', id);
            formData.append('IdPrestacion', idprestacion)
            formData.append('_token', TOKEN);
            formData.append('who', who);

            preloader('on');

            $.ajax({
                type: 'POST',
                url: fileUpload,
                data: formData,
                processData: false,
                contentType: false,
                success: function() {
                    preloader('off');
                    toastr.success("Se ha cargado el reporte de manera correcta.");
                    $(tabla).DataTable().clear().draw(false);
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

    $(document).on('click', '.automaticUpload, .automaticUploadI, .automaticUploadIC', function(e){

        e.preventDefault();

        let obj= {
            automaticUpload: ['Id_adjunto', '#checkAllAdj', 'archivosAutomatico', '#listaOrdenesEfectoresAdj'],
            automaticUploadI: ['Id_adjuntoInf', '#checkAllAdjInf', 'archivosAutomaticoI', '#listaOrdenesInformadoresAdj'],
            automaticUploadIC: ['Id_adjuntoInf', '#checkAllAdjInf', 'archivosAutomaticoI', '#listaOrdenesInformadoresAdj']
        }

        let ids = [], tipo = $(this).data('forma'), opcion = $(this).hasClass('automaticUpload') ? 'automaticUpload' : 'automaticUploadI', who = $(this).hasClass('automaticUploadI') ? 'multiInformador' : 'multiefector';

        if(tipo === 'individual') {

                ids.push($(this).data('id'));  
        }else{

            $('input[name="' + obj[opcion][0] + '"]:checked').each(function() {
                ids.push($(this).val());
            });

        }
    
        let checkAll = $(obj[opcion][1]).prop('checked');

        if(ids.length === 0 && checkAll === false){
            toastr.warning('No hay examenes seleccionados', 'Atención');
            return;
        }
        preloader('on');

        $.post(obj[opcion][2], { _token: TOKEN, Ids: ids, AutoCerrar: $(this).hasClass('automaticUploadIC') ? true : null, who: who, IdEntidad: $(this).data('id'), IdPrestacion: $(this).data('idprestacion') })
            .done(function(response){
                var estados = [];
                response.forEach(function(msg) {

                    let tipoToastr = msg.estado == 'success' ? 'success' : 'info';

                    toastr[tipoToastr](msg.message, "Atención", { timeOut: 10000 })
                    estados.push(msg.estado);
                });

                if(estados.includes('success')) {
                    $(obj[opcion][3]).DataTable().clear().draw(false);
                }

            })
            .fail(function(jqXHR){
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;  
            })
            .always(function() {   
                preloader('off');
            });
        
    });

    $(document).on('click', '.Exportar', function(e) {
        e.preventDefault();
    
        var ids = [];
        $('#listaOrdenesPrestaciones #listado').each(function() {
            var id = $(this).data('id');
            if (id) {
                ids.push(id);
            }
        });

        if(ids.length === 0) {
            toastr.warning('No hay examenes para exportar');
            return;
        }

        swal({
            title: "¿Estas seguro que deseas generar el reporte de  examenes/prestaciones?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar){
                preloader('on');
                $.get(exportarOrdExa, {Id: ids})
                    .done(function(response){
                        preloader('off');
                        createFile("excel", response.filePath, generarCodigoAleatorio() + '_reporte');
                        toastr.success("Se esta generando el reporte");
                    })
            }
        });
    });

    $(document).on('click', '.vistaPreviaEnvios', function(e){
        e.preventDefault();
        let ids = [];

        $('input[name="Id_EEnviar"]:checked').each(function() {
            ids.push($(this).val());
        });
        
        if(ids.length === 0){
            toastr.warning('No hay prestaciones seleccionados para visualizar');
            return;
        }

        swal({
            title: "¿Esta seguro que desea generar la vista previa de los envios?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar){
                preloader('on');
                $.get(vistaPreviaEnvios, { Ids: ids })

                    .done(function(response){
                        e.preventDefault();

                        $.each(response, function(index, link){
                            window.open(link, '_blank');
                        });
                        
                        preloader('off');
                        toastr.success("Se ha generado la vista previa");
                    })
                    .fail(function(jqXHR){
                        let errorData = JSON.parse(jqXHR.responseText);
                        checkError(jqXHR.status, errorData.msg);
                        return;
                    });
            }
        });
    });

    $(document).on('click', '.avisoEnviosEE', function(e){
        e.preventDefault();
        let ids = [];

        $('input[name="Id_EEnviar"]:checked').each(function() {
            ids.push($(this).val());
        });

        if(ids.length === 0){
            toastr.warning('No hay prestaciones seleccionados para visualizar');
            return;
        }

        swal({
            title: "¿Esta seguro que desea enviar un aviso a las prestaciones seleccionadas?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                preloader('on');
                $.get(avisoEnvios, { Ids: ids})
                    .done(function(response){
                        preloader('off');
            
                        $.each(response, function(index, r){
                            r.estado == 'success' ? toastr.success(r.message) : toastr.warning(r.message);
                        });

                    })
                    .fail(function(jqXHR){
                        let errorData = JSON.parse(jqXHR.responseText);
                        checkError(jqXHR.status, errorData.msg);
                        return;
                    });
            }
        });
        

    });

    function verificarArchivo(archivo){

        if (!archivo || archivo.size === 0) {
            toastr.warning("El archivo se encuentra vacío o no es PDF");
            return false;
        }

        if (!archivo.name.includes('.')) {
            toastr.warning("El archivo no tiene extensión o la misma es invalida");
            return false;
        }

        let tipoArchivo = archivo.type.toLowerCase();

        if(tipoArchivo !== 'application/pdf') {
            toastr.warning("Los archivos permitidos son PDF");
            return false;
        }

        return true

    }

    function crearQR(tipo, prestacion, examen, paciente) {

        prestacionId = prestacion.toString().padStart(9, "0");
        examenId = examen.toString().padStart(5, "0");
        pacienteId = paciente.toString().padStart(7, "0");

        let code = tipo.toUpperCase() + prestacionId + examenId + pacienteId;
        return code;
    }

    function listaProveedores(){

        $.get(lstProveedores, function(response){

            lstEspecialidades.empty().append('<option value="" selected>Elige una opción...</option>');

            $.each(response.result, function(index, r){

                contenido = `<option title="${r.Nombre}" value="${r.Id}">${r.Nombre}</option>`;

                lstEspecialidades.append(contenido);
            });
        });
    }

    async function optionsGeneral(id, ident, tipo) {

        let obj = {
            efector: '#efectores',
            efectorAsignado: '#efectorAsignado',
            efectorAdjunto: '#efectorAdjunto',
            efectorPres: '#profEfePres',
            informador: '#informadores',
            informadorAsignadoInf: '#informadorAsignadoInf',
            informadorAdjuntoInf: '#informadorAdjuntoInf',
            informadorPres: '#profInfPres'
        }
   
        let etiqueta;

        if (ident in obj) {
            etiqueta = $(obj[ident]);
        }

        etiqueta.empty().append('<option value="" selected>Elija una opción...</option>')
        preloader('on');
        $.get(listGeneral, { proveedor: id, tipo: tipo })
            .done(async function (response) {
                preloader('off');
                let data = await response.resultados;
                $.each(data, function (index, d) {
                    let contenido = `<option value="${d.Id}">${d.NombreCompleto}</option>`;
                    etiqueta.append(contenido);
                });
            });
    }

});