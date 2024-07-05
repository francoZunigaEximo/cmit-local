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
 
    $(document).on('click', '#asigEfector, #asigInf', function(){

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
            toastr.warning('No se ha seleccionado un Profesional para asignar', 'Atención');
            return;
        }
    
        if(ids.length === 0 && checkAll === false){
            toastr.warning('No hay ningun exámen seleccionado para asignar', 'Atención');
            return;
        }
        
        if (confirm("¿Desea confirmar la operación?")) {
        
            $.post(asignarProfesional, { _token: TOKEN, Ids: ids, IdProfesional: profesional, tipo: seleccion })
            .done(function(response){

                let data = response.message;
                toastr.info(data, "Información");
                $(obj[seleccion][2]).DataTable().draw(false)

            })
            .fail(function(xhr) {

                console.error(xhr);
                toastr.error("Ha ocurrido un error en la asignación. Consulte con el administrador");
            });
        }
        
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
        
        if (confirm("¿Desea confirmar la operación?")) {
            
            $.post(updateItem, {Id : ids, _token: TOKEN, Para: 'abrir' })
            .done(function(){
                toastr.success('Se ha realizado la acción correctamente', 'Actualizacion realizada');
                
                let table = $('#listaOrdenesEfectoresAsig').DataTable();
                table.draw(false); 
            })
            .fail(function(xhr){
                toastr.success('Ha ocurrido un error. Consulte con el administrador', 'Error');
                console.error(xhr);
            });
        
        }
        
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

        if (confirm("¿Desea confirmar la operación?")) {
            
            $.post(updateItem, {Id : ids, _token: TOKEN, Para: 'cerrar' })
            .done(function(){
                toastr.success('Se ha realizado la acción correctamente', 'Actualizacion realizada');
                
                let table = $('#listaOrdenesEfectoresAsig').DataTable();
                table.draw(false); 
            })
            .fail(function(xhr){
                toastr.success('Ha ocurrido un error. Consulte con el administrador', 'Error');
                console.error(xhr);
            });
        
        }
        
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
            toastr.warning('No hay examenes seleccionados', 'Atención');
            return;
        }
       
        if (confirm("¿Desea confirmar la operación?")) {

            $.post(asignarProfesional, { _token: TOKEN, Ids: ids, IdProfesional: 0, tipo: obj[seleccion][3]})
            .done(function(response){

                let data = response.message;
                toastr.info(data, "Información");
                let table = $(obj[seleccion][2]).DataTable();
                table.draw(false);

            })
            .fail(function(xhr) {

                console.error(xhr);
                toastr.error("Ha ocurrido un error en la desasignación. Consulte con el administrador");
            });

        }
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
                    toastr.success("Se ha cargado el reporte de manera correcta.", "Perfecto");
                    let table = $(tabla).DataTable();
                    table.clear().draw(false);

                },
                error: function (xhr) {
                    preloader('off');
                    console.error(xhr);
                    toastr.error("Ha ocurrido un error. Consulte con el administrador", "Atención");
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
                    
                    let table = $(obj[opcion][3]).DataTable();
                    table.clear().draw(false);
                }

            })
            .fail(function(xhr){
                console.error(xhr)
                toastr.error("Ha ocurrido un error. Consulte con el administrador", "Error");
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
                        createFile(response.filePath);
                        toastr.success("Se esta generando el reporte");
                    })
            }
        });
        

            
        
    });
 
    function createFile(array){
        let fecha = new Date(),
            dia = fecha.getDay(),
            mes = fecha.getMonth() + 1,
            anio = fecha.getFullYear();

        let filePath = array,
            pattern = /storage(.*)/,
            match = filePath.match(pattern),
            path = match ? match[1] : '';

        let url = new URL(location.href),
            baseUrl = url.origin,
            fullPath = baseUrl + '/cmit/storage' + path;

        let link = document.createElement('a');
        link.href = fullPath;
        link.download = "reporte-"+ dia + "-" + mes + "-" + anio +".xlsx";
        link.style.display = 'none';

        document.body.appendChild(link);
        link.click();
        setTimeout(function() {
            document.body.removeChild(link);
        }, 100);
    }

    function verificarArchivo(archivo){

        if (!archivo || archivo.size === 0) {
            toastr.warning("El archivo se encuentra vacío o no es PDF", "Atención");
            return false;
        }

        if (!archivo.name.includes('.')) {
            toastr.warning("El archivo no tiene extensión o la misma es invalida", "Atención");
            return false;
        }

        let tipoArchivo = archivo.type.toLowerCase();

        if(tipoArchivo !== 'application/pdf') {
            toastr.warning("Los archivos permitidos son PDF", "Atención");
            return false;
        }

        return true

    }

    function infoQr(idPrestacion, qr, examen) {
        $('.generarQr').empty();

        let contenido = `
            <span>Prestacion: ${idPrestacion}. Examen: ${examen} 
                <span id="qr">${qr}</span>
            </span>
            <button type="button" class="btn btn-sm botonGeneral copiarQr"><i class="ri-file-copy-line"></i>Copiar</button>
        `;

        $('#qrExamen').show();
        $('.generarQr').append(contenido);

        
    }

    function crearQR(tipo, prestacion, examen, paciente) {

        prestacionId = prestacion.toString().padStart(9, "0");
        examenId = examen.toString().padStart(5, "0");
        pacienteId = paciente.toString().padStart(7, "0");

        let code = tipo.toUpperCase() + prestacionId + examenId + pacienteId;
        return code;
    }

    function fechaNow(fechaAformatear, divider, format) {
        let dia, mes, anio; 
    
        if (fechaAformatear === null) {
            let fechaHoy = new Date();
    
            dia = fechaHoy.getDate().toString().padStart(2, '0');
            mes = (fechaHoy.getMonth() + 1).toString().padStart(2, '0');
            anio = fechaHoy.getFullYear();
        } else {
            let nuevaFecha = fechaAformatear.split("-"); 
            dia = nuevaFecha[0]; 
            mes = nuevaFecha[1]; 
            anio = nuevaFecha[2];
        }
    
        return (format === 1) ? `${dia}${divider}${mes}${divider}${anio}` : `${anio}${divider}${mes}${divider}${dia}`;
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
        
        $.get(listGeneral, { proveedor: id, tipo: tipo })
            .done(async function (response) {
                let data = await response.resultados;
                $.each(data, function (index, d) {
                    let contenido = `<option value="${d.Id}">${d.NombreCompleto}</option>`;
                    etiqueta.append(contenido);
                });
            });
    }

    function preloader(opcion) {
        $('#preloader').css({
            opacity: '0.3',
            visibility: opcion === 'on' ? 'visible' : 'hidden'
        });
    }

});