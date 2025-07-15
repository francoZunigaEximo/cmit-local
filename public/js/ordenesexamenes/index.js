$(function() {

    $('#fechaHasta, #fechaHastaAsignados, #fechaHastaAdjunto, #fechaHastaInf, #fechaHastaAsignadosInf, #fechaHastaAdjuntoInf, #fechaHastaPres, #fechaHastaEEnviar').val(fechaNow(null, "-", 0)), $('#efectorPres').val('pendientes'),$('#tipoPres').val('todos');

    let checks = {
        '#checkAllAsignar': 'Id_asignar',
        '#checkAllAsignado': 'Id_asignado',
        '#checkAllAdj': 'Id_adjunto',
        '#checkAllAsigInf': 'Id_asigInf',
        '#checkAllAsignadoInf': 'Id_asignadoInf',
        '#checkAllAdjInf': 'Id_adjuntoInf',
        '#checkAllEEnviar': 'Id_EEnviar'
    };
    
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

        if (!profesional|| profesional === '0') { 
            toastr.warning('No se ha seleccionado un Profesional para asignar','',{timeOut: 1000});
            return;
        }
    
        if(ids.length === 0 && checkAll === false){
            toastr.warning('No hay ningun exámen seleccionado para asignar','',{timeOut: 1000});
            return;
        }
        
        swal({
            title: "¿Desea confirmar la operación?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                preloader('on');
                $.post(asignarProfesional, { _token: TOKEN, Ids: ids, IdProfesional: profesional, tipo: seleccion })
                .done(function(response){
                    preloader('off');
                    let data = response.message;
                    toastr.success(data, "Información", { timeOut: 1000 });
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

        if(ids.length === 0 && !checkAll){
            toastr.warning('No hay examenes seleccionados', 'Atención', {timeOut: 1000});
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
                    toastr.success('Se ha realizado la acción correctamente', '', {timeOut: 1000});                 
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
            toastr.warning('No hay examenes seleccionados', 'Atención', {timeOut: 1000});
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
                    toastr.success('Se ha realizado la acción correctamente','', {timeOut: 1000});
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

        if(ids.length === 0 && !checkAll){
            toastr.warning('No hay examenes seleccionados','',{timeOut: 1000});
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
                    toastr.success(response.message,'',{timeOut: 1000});
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

    $(document).on('click', '#resetPres', function(e){
        e.preventDefault();
        $('#form-index :input, #form-index select, #especialidadPres, #efectoresPres').val('');
        $('#examenPres').val([]).trigger('change.select2');
        $('#listaOrdenesPrestaciones').DataTable().clear().destroy();
        $('#fechaHastaPres').val(fechaNow(null, "-", 0));
    });

    $(document).on('click', '#reset', function(e){
        e.preventDefault();
        $('#form-index :input, #form-index select, #especialidad, #efectores').val('');
        ['#examen', '#paciente', '#empresa'].forEach(function(selector) {
            $(selector).val([]).trigger('change.select2');
        });
        $('#listaOrdenesEfectores').DataTable().clear().destroy();
        $('#fechaHasta').val(fechaNow(null, "-", 0));
    });

    $(document).on('click', '#resetInf', function(e){
        e.preventDefault();
        $('#form-index :input, #form-index select, #especialidadInf, #informadores').val('');
        ['#examenInf', '#pacienteInf', '#empresaInf'].forEach(function(selector) {
            $(selector).val([]).trigger('change.select2');
        });
        $('#listaOrdenesInformadores').DataTable().clear().destroy();
        $('#fechaHastaInf').val(fechaNow(null, "-", 0));
    });

    $(document).on('click', '#resetAsignado', function(e){
        e.preventDefault();
        $('#form-index :input, #form-index select, #especialidadAsignados, #efectorAsignado').val('');
        ['#examenAsignados','#pacienteAsignados','#empresaAsignados'].forEach(function(selector) {
            $(selector).val([]).trigger('change.select2');
        });
        $('#listaOrdenesEfectoresAsig').DataTable().clear().destroy();
        $('#fechaHastaAsignado').val(fechaNow(null, "-", 0));
    });

    $(document).on('click', '#resetAsignadoInf', function(e){
        e.preventDefault();    
        $('#form-index :input, #form-index select, #especialidadAsignadosInf, #informadorAsignadoInf').val('');
        ['#examenAsignadosInf','#pacienteAsignadosInf','#empresaAsignadosInf'].forEach(function(selector) {
            $(selector).val([]).trigger('change.select2');
        });
        $('#listaOrdenesInformadoresAsig').DataTable().clear().destroy();
        $('#fechaDesdeAsignadosInf').val(fechaNow(null, "-", 0));
    });

    $(document).on('click', '#resetAdjunto', function(e){
        e.preventDefault();  
        $('#form-index :input, #form-index select, #especialidadAdjunto, #efectorAdjunto').val('');
        ['#empresaAdjunto','#artAdjunto'].forEach(function(selector) {
            $(selector).val([]).trigger('change.select2');
        });
        $('#listaOrdenesEfectoresAdj').DataTable().clear().destroy();
        $('#fechaHastaAdjunto').val(fechaNow(null, "-", 0));
    });

    $(document).on('click', '#resetAdjuntoInf', function(e){
        e.preventDefault();  
        $('#form-index :input, #form-index select, #especialidadAdjuntoInf, #informadorAdjuntoInf').val('');
        ['empresaAdjuntoInf', 'artAdjuntoInf'].forEach(function(selector) {
            $(selector).val([]).trigger('change.select2');
        });
        $('#listaOrdenesInformadoresAdj').DataTable().clear().destroy();
        $('#fechaHastaAdjuntoInf').val(fechaNow(null, "-", 0));
    });


    $.each(checks, function(checkAllId, nombre) {
        $(checkAllId).on('click', function() {
            $('input[type="checkbox"][name="' + nombre + '"]:not(' + checkAllId + ')').prop('checked', this.checked);
        });
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
        let prestacion = $(this).data('prestacion'), paciente = $(this).data('paciente'), idexamen = $(this).data('examenid');
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
                    toastr.success("Se ha cargado el reporte de manera correcta.",'', {timeOut: 1000});
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

        if(ids.length === 0 && !checkAll){
            toastr.warning('No hay examenes seleccionados', 'Atención', {timeOut: 1000});
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
            toastr.warning('No hay examenes para exportar', 'Atención', {timeOut: 1000});
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
                        toastr.success("Se esta generando el reporte",'',{timeOut: 1000});
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
            toastr.warning('No hay prestaciones seleccionados para visualizar', '', {timeOut: 1000});
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

                        let contador = 1
                        $.each(response, function(index, link){
                            
                            setTimeout(() => {
                                console.log(link, contador++)
                                window.open(link, '_blank');
                            }, 2000);
                            
                        });
                        
                        preloader('off');
                        toastr.success("Se ha generado la vista previa", '', {timeOut: 1000});
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
            toastr.warning('No hay prestaciones seleccionados para visualizar', '', {timeOut: 1000});
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
                            r.estado == 'success' ? toastr.success(r.msg,'',{timeOut:1000}) : toastr.warning(r.msg,'',{timeOut:1000});
                        });

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

    $(document).on('click', '.EEnviarEnvios', function(e){
        e.preventDefault();

        let ids = [];

        $('input[name="Id_EEnviar"]:checked').each(function() {
            ids.push($(this).val());
        });

        if(ids.length === 0){
            toastr.warning('No hay prestaciones seleccionados para visualizar', '', {timeOut: 1000});
            return;
        }

        swal({
            title: "¿Esta seguro que desea enviar el eEstudio de las prestaciones seleccionadas?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                preloader('on');
                $.get(enviarEE, { Ids: ids})
                    .done(function(response){
                        preloader('off');
                        $.each(response, function(index, r){
                            r.estado == 'success' ? toastr.success(r.msg,'',{timeOut:1000}) : toastr.warning(r.msg,'',{timeOut:1000});
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
            toastr.warning("El archivo se encuentra vacío o no es PDF",'',{timeOut: 1000});
            return false;
        }

        if (!archivo.name.includes('.')) {
            toastr.warning("El archivo no tiene extensión o la misma es invalida",'',{timeOut: 1000});
            return false;
        }

        let tipoArchivo = archivo.type.toLowerCase();

        if(tipoArchivo !== 'application/pdf') {
            toastr.warning("Los archivos permitidos son PDF",'',{timeOut: 1000});
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

            for(let index = 0; index < response.length; index++) {
                let r = response[index],
                contenido = `<option value="${r.Id}">${r.Nombre}</option>`;
                lstEspecialidades.append(contenido);    

            }
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