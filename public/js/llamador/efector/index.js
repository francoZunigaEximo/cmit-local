$(function(){

    const profesionales = ['EFECTOR', 'INFORMADOR', 'COMBINADO'];

    const principal = {
        grillaEfector: $('#listaLlamadaEfector'),
        grillaExamenes: $('#tablasExamenes'),
        atenderPaciente: $('.atenderPaciente'),
        chekAllExamenes: $('.checkAllExamenes'),
        buscar: $('#buscar'),
        atenderEfector: $('#atenderEfector')
    };

    const variables = {
        fechaHasta: $('#fechaHasta'),
        fechaDesde: $('#fechaDesde'),
        estado: $('#estado'),
        profesional: $('#profesional'),
        prestacion: $('#prestacion'),
        prestacionVar: $('#prestacion_var'),
        profesionalEfector: $('#profesional_var'),
        tipoEfector: $('#tipo_var'),
        artEfector: $('#art_var'),
        empresaEfector: $('#empresa_var'),
        paraEmpresaEfector: $('#paraEmpresa_var'),
        pacienteEfector: $('#paciente_var'),
        edadEfector: $('#edad_var'),
        fechaEfector: $('#fecha_var'),
        fotoEfector: $('#foto_var'),
        profesional: $('#profesional'),
        descargaFoto: $('#descargaFoto'),
        efector: 'Efector',
        especialidadSelect: $('#especialidadSelect'),
        especialidad: $('#especialidad'),
    };

    variables.fechaHasta
        .add(variables.fechaDesde)
        .val(fechaNow(null, "-", 0));
        
    variables.estado.val('abierto');
    
    habilitarBoton(sessionProfesional, profesionales[0]);
    listadoEspecialidades(variables.efector);


    $(document).on('click', '.verPrestacion', function(e){
        e.preventDefault();

        let prestacion = $(this).data('prestacion');
        window.open(lnkPres.replace('__item__', prestacion), '_blank');
    });

    $(document).on('click','.exportar, .detalles', async function(e){
        e.preventDefault();

        let opcion = $(this).hasClass('exportar') ? 'exportar' : 'detalles';

        let lista = principal.grillaEfector.DataTable();

        if(!lista.data().any()){
            lista.clear().destroy();
            toastr.warning('No hay datos para exportar', '', {timeOut: 1000});
            return;
        }

        //lista.rows().data().toArray(); 
        let data = lista.rows({page: 'current'}).data().toArray(),
            ids = data.map(function(row) {
            return row.prestacion;
        });

        preloader('on');
        try {
            let response = await $.get(printExportar, {Ids: ids, modo: opcion === 'exportar' ? 'basico' : 'full'});

            if(response){
                createFile("xlsx", response.filePath, generarCodigoAleatorio() + '_reporte');
                preloader('off')
                toastr.success(response.msg, '', {timeOut: 1000});
            }
            
        }catch(jqXHR) {
            let errorData = JSON.parse(jqXHR.responseText);            
            checkError(jqXHR.status, errorData.msg);
            return;

        }finally {
            preloader('off');
        }      
    });

    $(document).on('click', '.atenderPaciente', async function(e){
        e.preventDefault();
    
        let id = $(this).data('id'),  
            especialidades = $(this).data('especialidades'),
            arrayEspecialidades = especialidades;

        if(especialidades === 'todos') {
            arrayEspecialidades = await $.get(searchEspecialidad, {IdProfesional: variables.profesional.val(), Tipo: 'Efector'});
            arrayEspecialidades = arrayEspecialidades.map(id => id.Id);
        }

        variables.profesionalEfector
            .add(variables.prestacionVar)
            .add(variables.tipoEfector)
            .add(variables.artEfector)
            .add(variables.empresaEfector)
            .add(variables.paraEmpresaEfector)
            .add(variables.pacienteEfector)
            .add(variables.edadEfector)
            .add(variables.fechaEfector)
            .empty();

        principal.atenderEfector.removeAttr('data-itemp');

        variables.fotoEfector.attr('src', '');
        preloader('on');
        
        try {
            let response =  await $.get(dataPaciente, {Id: id, IdProfesional: variables.profesional.val(), Especialidades: arrayEspecialidades});

            const prestacion = response.prestacion;


            let paciente = prestacion.paciente.Apellido + ' ' + prestacion.paciente.Nombre,
                edad = calcularEdad(prestacion.paciente.FechaNacimiento),
                fecha = fechaNow(prestacion.Fecha,'/',0);

            let nombreProfesional = variables.profesional.find(':selected').text();
            variables.prestacionVar.val(prestacion.Id);
            variables.profesionalEfector.val(nombreProfesional);
            variables.tipoEfector.val(prestacion.TipoPrestacion);
            variables.artEfector.val(prestacion.art.RazonSocial);
            variables.empresaEfector.val(prestacion.empresa.RazonSocial);
            variables.paraEmpresaEfector.val(prestacion.empresa.ParaEmpresa);
            variables.pacienteEfector.val(paciente);
            variables.edadEfector.val(edad);
            variables.fechaEfector.val(fecha);
            variables.fotoEfector.attr('src', FOTO + prestacion.paciente.Foto);
            variables.descargaFoto.attr('href', FOTO + prestacion.paciente.Foto);

            principal.atenderEfector.attr('data-itemp', response.itemsprestaciones.IdItem);

            comentariosPrivados(parseInt(prestacion.Id));
            tablasExamenes(response.itemsprestaciones, parseInt(variables.profesional.val()));
            cargarArchivosEfector(parseInt(prestacion.Id), parseInt(variables.profesional.val()), variables.especialidad.data('id') || variables.especialidadSelect.val());

        }catch (jqXHR){
            let errorData = JSON.parse(jqXHR.responseText);            
            checkError(jqXHR.status, errorData.msg);
            return;
        
        }finally {
            preloader('off');
        }  
    });

    variables.profesional.change(function(){
        listadoEspecialidades(variables.efector);
    });

    variables.fotoEfector.hover(
        function() {
            $(this).addClass('zoomed');
        },
        function() {
            $(this).removeClass('zoomed');
        }
    );

    $(document).on('click', '.llamarExamen, .liberarExamen',function(e){
        e.preventDefault();

        const accion = $(this).hasClass('llamarExamen') ? 'llamado' : 'liberar',
              boton = {
                    llamado: {
                        texto: '<i class="ri-edit-line"></i> Liberar',
                        remover: 'llamarExamen',
                        agregar: 'liberarExamen',
                        textoFila: 'red'

                    },
                    liberar: {
                        texto: '<i class="ri-edit-line"></i> Llamar',
                        remover: 'liberarExamen',
                        agregar: 'llamarExamen',
                        textoFila: 'green'
                    }
            };

        let fila = $(this).closest('tr');
        fila.css('color', boton[accion].textoFila);

        accion === 'liberar' ? principal.atenderPaciente.show() : principal.atenderPaciente.hide();

        $(this).empty()
            .html(boton[accion].texto)
            .removeClass(boton[accion].remover)
            .addClass(boton[accion].agregar);
        preloader('on');
        
        $.get(addAtencion, {prestacion: $(this).data('id'), profesional: variables.profesional.val(), Tipo: $(this).data('tipo'), especialidad: variables.especialidad.data('id') || variables.especialidadSelect.val()})
            .done(function(){
                preloader('off')
                toastr.success('Cambio de estado realizado correctamente','',{timeOut: 1000})
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            });
    });

    $(document).on('change', '.checkAllExamenes', function(){
        let nombreCheckAll = $(this).attr('name');
        if(!nombreCheckAll || !nombreCheckAll.startsWith('Id_')) return;

        let grupo = nombreCheckAll.replace('Id_', ''),
            seleccion = `input[type="checkbox"][name^="Id_${grupo}_"]`,
            isChecked = $(this).prop('checked');

        $(seleccion).each(function () {
            let $checkbox = $(this);

            if ($checkbox.prop('checked') !== isChecked) {
                $checkbox.prop('checked', isChecked);

                // $checkbox.prop('checked', isChecked).trigger('click');

                // if ($checkbox.prop('checked') !== isChecked) {
                //     $checkbox.prop('checked', isChecked);
                //     $checkbox[0].click(); //Disparo el evento
                // }

                //console.log($checkbox[0])

                //Usar JS en lugar de Jquery
                $checkbox[0].dispatchEvent(new Event('click', { bubbles: true }));
            }
        });
    });

    




});