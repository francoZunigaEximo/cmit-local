$(document).ready(function(){

    toastr.options = {
        closeButton: true,   
        progressBar: true,    
        timeOut: 3000,        
    };

    $('#fechaHasta, #fechaHastaAsignados, #fechaHastaAdjunto, #fechaHastaInf, #fechaHastaAsignadosInf, #fechaHastaAdjuntoInf').val(fechaNow(null, "-", 0));
    
    let especialidadVal = $('#especialidad').val(),
        especialidadAsigVal = $('#especialidadAsignados').val(),
        especialidadAdjVal = $('#especialidadAdjunto').val(),
        especialidadInf = $('#especialidadInf').val(),
        especialidadAsigVaInf = $('#especialidadAsignadosInf').val(),
        especialidadAdjValInf = $('#especialidadAdjuntoInf').val(),
        lstEspecialidades = $('#especialidad, #especialidadAsignados, #especialidadAdjunto, #especialidadInf, #especialidadAsignadosInf, #especialidadAdjuntoInf');

    $('#Liberar, #Cerrar, #Abrir, #qrExamen, #LiberarInf').hide();

    listaProveedores();
    optionsGeneral(especialidadVal, "efector", "informador");
    optionsGeneral(especialidadAsigVal, "efectorAsignado", "informador");
    optionsGeneral(especialidadAdjVal, "efectorAdjunto", "informador");
    optionsGeneral(especialidadInf, "informador", "informador");
    optionsGeneral(especialidadAsigVaInf, "informadorAsignadoInf", "informador");
    optionsGeneral(especialidadAdjValInf, "informadorAdjuntoInf", "informador");

    $(document).on('change', '.especialidad, .especialidadAsignados, .especialidadAdjunto, .especialidadInf, .especialidadAsignadosInf, .especialidadAdjuntoInf', function() {

        let newEspecialidadVal = $('.especialidad').val(),
            newEspecialidadAsigVal = $('.especialidadAsignados').val(),
            newEspecialidadAdjVal = $('.especialidadAdjunto').val(),
            newEspecialidadInf = $('.especialidadInf').val(),
            newEspecialidadAsigValInf = $('.especialidadAsignadosInf').val(),
            newEspecialidadAdjValInf = $('.especialidadAdjuntoInf').val();

        optionsGeneral(newEspecialidadVal, "efector", "efector");
        optionsGeneral(newEspecialidadAsigVal, "efectorAsignado", "efector");
        optionsGeneral(newEspecialidadAdjVal, "efectorAdjunto", "efector");
        optionsGeneral(newEspecialidadInf, "informador", "informador");
        optionsGeneral(newEspecialidadAsigValInf, "informadorAsignadoInf", "informador");
        optionsGeneral(newEspecialidadAdjValInf, "informadorAdjuntoInf", "informador");
    });
    
    $('#empresa, #empresaInf, #empresaAsignados, #empresaAdjunto, #empresaAsignadosInf, #empresaAdjuntoInf').each(function() {
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

    $('#examen, #examenAsignados, #examenInf, #examenAsignadosInf').each(function(){
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

    $('#paciente, #pacienteInf, #pacienteAsignados, #pacienteAsignadosInf').each(function(){
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

        $.post(asignarProfesional, { _token: TOKEN, Ids: ids, IdProfesional: profesional, tipo: seleccion })
            .done(function(response){

                let data = response.message;
                toastr.info(data, "Información");
                let table = $(obj[seleccion][2]).DataTable();
                table.draw(false);

            })
            .fail(function(xhr) {

                console.error(xhr);
                toastr.error("Ha ocurrido un error en la asignación. Consulte con el administrador");
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

        let checkAll =$(obj[seleccion[1]]).prop('checked');

        if(ids.length === 0 && checkAll === false){
            toastr.warning('No hay examenes seleccionados', 'Atención');
            return;
        }

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


    });

    $(document).on("select2:open", () => {
        document.querySelector(".select2-container--open .select2-search__field").focus()
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

    $('#resetAdjunto').click(function(){ 
        $('#form-index :input, #form-index select').val('');
        $('#empresaAdjunto').val([]).trigger('change.select2');
        $('#especialidadAdjunto').val('');
        $('#efectorAdjunto').val('');
        $('#listaOrdenesEfectoresAdj').DataTable().clear().destroy();
        $('#fechaHastaAdjunto').val(fechaNow(null, "-", 0));
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

    $('#checkAllAsignadoInf').on('click', function() {

        $('input[type="checkbox"][name="Id_asignadoInf"]:not(#checkAllAsignadoInf)').prop('checked', this.checked);
    });

    $('#checkAllAdjInf').on('click', function() {

        $('input[type="checkbox"][name="Id_adjuntoInf"]:not(#checkAllAdjInf)').prop('checked', this.checked);
    });

    $(document).on('change', '#efectores, #informadores', function() {

        const obj = {
            efectores : ['#asigEfector', '#listaOrdenesEfectores'],
            informadores : ['#asigInf', '#listaOrdenesInformadores']
        }

        let seleccion = $(this).attr('id'), botonAsignar = $(obj[seleccion][0]);

        botonAsignar.prop('disabled', true);
        debugger;
        let table = $(obj[seleccion][1]).DataTable();
        table.draw(false);

        //Habilitamos el boton
        table.on('draw.dt', function() {
            botonAsignar.prop('disabled', false);
        });
    });

    $(document).on('click', '.copiarQr', function(e) {

        e.preventDefault();
        let copiarQr = $("#qr").text();
        navigator.clipboard.writeText(copiarQr)
          .then(() => alert("Se ha copiado el siguiente QR: " + copiarQr))
          .catch(err => console.error("Error al copiar al portapapeles: ", err));
    });

    let temporizador;
    $(document).on('click', '.mostrarQr', function(e){

        $('#qrExamen').hide();

        let prestacion = $(this).data('prestacion'), paciente = $(this).data('paciente'), examen = $(this).data('examen'), idexamen = $(this).data('examenid');
        let codigoqr = crearQR("A", prestacion, idexamen, paciente);

        infoQr(prestacion, codigoqr, examen);

        if (temporizador) {
            clearTimeout(temporizador);
        }
    
        temporizador = setTimeout(() => {
            $('#qrExamen').hide();
        }, 10000);
        
    });

    $(document).on('click', '.uploadFile', function(){
        let id = $(this).data('id'), idprestacion = $(this).data('idprestacion');
        $(this).off('click'); 
        $(this).next('.fileManual')
            .data('id', id)
            .data('idprestacion', idprestacion)
            .click();
    });

    $(document).on('change', '.fileManual', function(){
        let id = $(this).data('id'), idprestacion = $(this).data('idprestacion'), archivo = $('.fileManual')[0].files[0];
        debugger;
        if (verificarArchivo(archivo)) {
            debugger;
            let formData = new FormData();
            formData.append('archivo', archivo);
            formData.append('IdEntidad', id);
            formData.append('IdPrestacion', idprestacion)
            formData.append('_token', TOKEN);
            formData.append('who', 'efector');

            $.ajax({
                type: 'POST',
                url: fileUpload,
                data: formData,
                processData: false,
                contentType: false,
                success: function() {
                    toastr.success("Se ha cargado el reporte de manera correcta.", "Perfecto");
                    let table = $('#listaOrdenesEfectoresAdj').DataTable();
                    table.draw(false);

                },
                error: function (xhr) {
                    console.error(xhr);
                    toastr.error("Ha ocurrido un error. Consulte con el administrador", "Atención");
                }
            });
           
        }
    });

    $(document).on('click', '.automaticUpload', function(e){

        e.preventDefault();

        $('#preloader-overlay').show();
        
        let ids = [], tipo = $(this).data('forma');

        if(tipo === 'individual') {

                ids.push($(this).data('id'));  
        }else{

            $('input[name="Id_adjunto"]:checked').each(function() {
                ids.push($(this).val());
            });

        }
    
        let checkAll = $("#checkAllAdj").prop('checked');

        if(ids.length === 0 && checkAll === false){
            toastr.warning('No hay examenes seleccionados', 'Atención');
            return;
        }
        $.post(archivosAutomatico, { _token: TOKEN, Ids: ids })
            .done(function(response){

                response.forEach(function(msg) {

                    let tipoToastr = msg.estado == 'success' ? 'success' : 'info';
                    toastr[tipoToastr](msg.message, "Atención", { timeOut: 10000 })

                    if(msg.estado == 'success') {
                        let table = $('#listaOrdenesEfectoresAdj').DataTable();
                        table.draw(false);
                    }
                });

            })
            .fail(function(xhr){
                console.error(xhr)
                toastr.error("Ha ocurrido un error. Consulte con el administrador", "Error");
            })
            .always(function() {
                // Ocultar el preloader después de completar la solicitud
                $('#preloader-overlay').hide();
            });
        
    });

    function verificarArchivo(archivo){

        if (!archivo || archivo.size === 0) {
            toastr.warning("Debe seleccionar un archivo valido PDF", "Atención");
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

    function optionsGeneral(id, ident, tipo) {

        let obj = {
            efector: '#efectores',
            efectorAsignado: '#efectorAsignado',
            efectorAdjunto: '#efectorAdjunto',
            informador: '#informadores',
            informadorAsignadoInf: '#informadorAsignadoInf',
            informadorAdjuntoInf: '#informadorAdjuntoInf'
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

});