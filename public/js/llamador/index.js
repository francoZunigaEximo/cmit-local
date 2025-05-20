$(function(){

    const principal = {
        grillaEfector: $('#listaLlamadaEfector'),
        grillaExamenes: $('#tablasExamenes'),
        atenderPaciente: $('.atenderPaciente')
    };

    const variables = {
        fechaHasta: $('#fechaHasta'),
        estado: $('#estado'),
        profesionalEfector: $('#profesionalEfector'),
        prestacionEfector: $('#prestacionEfector'),
        tipoEfector: $('#tipoEfector'),
        artEfector: $('#artEfector'),
        empresaEfector: $('#empresaEfector'),
        paraEmpresaEfector: $('#paraEmpresaEfector'),
        pacienteEfector: $('#pacienteEfector'),
        edadEfector: $('#edadEfector'),
        fechaEfector: $('#fechaEfector'),
        fotoEfector: $('#fotoEfector'),
        profesional: $('#profesional')
    };

    variables.fechaHasta.val(fechaNow(null, "-", 0));
    variables.estado.val('abierto');

    principal.atenderPaciente.hide();

    $(document).on('click', '.verPrestacion', function(e){
        e.preventDefault();

        let prestacion = $(this).data('prestacion');
        window.open(lnkPrestaciones.replace('__item__', prestacion), '_blank');
    });

    $(document).on('click','.exportar, .detalles', function(e){
        e.preventDefault();

        let opcion = $(this).hasClass('exportar') ? 'exportar' : 'detalles';

        let lista = principal.grillaEfector.DataTable();

        if(!lista.data().any()){
            lista.clear().destroy();
            toastr.warning('No hay datos para exportar');
            return;
        }

        //lista.rows().data().toArray(); 
        let data = lista.rows({page: 'current'}).data().toArray(),
            ids = data.map(function(row) {
            return row.prestacion;
        });

        preloader('on');
        $.get(printExportar, {Ids: ids, tipo: 'efector', modo: opcion === 'exportar' ? 'basico' : 'full'})
            .done(function(response){
                createFile("xlsx", response.filePath, generarCodigoAleatorio() + '_reporte');
                preloader('off')
                toastr.success(response.msg)
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            })
    });

    $(document).on('click', '.atenderPaciente', function(e){
        e.preventDefault();

        let id = $(this).data('id'), 
            profesional =  
            especialidades = $(this).data('especialidades');

        variables.profesionalEfector
            .add(variables.prestacionEfector)
            .add(variables.tipoEfector)
            .add(variables.artEfector)
            .add(variables.empresaEfector)
            .add(variables.paraEmpresaEfector)
            .add(variables.pacienteEfector)
            .add(variables.edadEfector)
            .add(variables.fechaEfector)
            .empty();

        variables.fotoEfector.attr('src', '');

        preloader('on')
        $.get(dataPaciente, {Id: id, IdProfesional: variables.profesional.val(), Especialidades: especialidades})
            .done(function(response){
                const prestacion = response.prestacion;

                let paciente = prestacion.paciente.Apellido + ' ' + prestacion.paciente.Nombre,
                    edad = calcularEdad(prestacion.paciente.FechaNacimiento),
                    fecha = fechaNow(prestacion.Fecha,'/',0);

                preloader('off');
                let nombreProfesional = variables.profesional.find(':selected').text();
                variables.prestacionEfector.val(prestacion.Id);
                variables.profesionalEfector.val(nombreProfesional);
                variables.tipoEfector.val(prestacion.TipoPrestacion);
                variables.artEfector.val(prestacion.art.RazonSocial);
                variables.empresaEfector.val(prestacion.empresa.RazonSocial);
                variables.paraEmpresaEfector.val(prestacion.empresa.ParaEmpresa);
                variables.pacienteEfector.val(paciente);
                variables.edadEfector.val(edad);
                variables.fechaEfector.val(fecha);
                variables.fotoEfector.attr('src', FOTO + prestacion.paciente.Foto);

                tablasExamenes(response.itemsprestaciones);

            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            });
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
        
        $.get(addAtencion, {prestacion: $(this).data('id'), profesional: variables.profesional.val()})
            .done(function(){
                toastr.success('Cambio de estado realizado correctamente','',{timeOut: 1000})
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            });
    });

    function tablasExamenes(data) { 
        principal.grillaExamenes.empty();
        preloader('on');

        const categoria = {};
        data.forEach(function (item) {
            if (!categoria[item.NombreEspecialidad]) {
                categoria[item.NombreEspecialidad] = [];
            }
            categoria[item.NombreEspecialidad].push(item);
        });
    
        // Recorre cada grupo de especialidades
        for (const especialidad in categoria) {
            if (categoria.hasOwnProperty(especialidad)) {
                const examenes = categoria[especialidad];
    
                // Crea la tabla para la especialidad actual
                let contenido = `
                    <div class="especialidad-grilla mb-2">
                        <h4>${especialidad}</h4>
                        <table class="table table-bordered no-footer dataTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 250px">Examen</th>
                                    <th style="width: 90px">Estado</th>
                                    <th style="width: 120px">Adjunto</th>
                                    <th>Observaciones</th>
                                    <th style="width: 150px">Efector</th>
                                    <th style="width: 150px">Informador</th>
                                    <th style="width: 50px">
                                        <input type="checkbox" class="checkAllExamenes" name="Id_examenes">
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                `;
    
       
                examenes.forEach(function (examen) {
                    contenido += `
                        <tr>
                            <td>${examen.NombreExamen}</td>
                            <td>${estado(examen.CAdj)}</td>
                            <td>${checkAdjunto(examen.NoImprime, examen.Adjunto, examen.Archivo)}</td>
                            <td>${[null, undefined, ''].includes(examen.ObsExamen) ? '' : examen.ObsExamen}</td>
                            <td>Efector</td>
                            <td>Informador</td>
                            <td>
                                <input type="checkbox" name="Id_examenes" value="${examen.IdItem}">
                            </td>
                        </tr>
                    `;
                });
    
                contenido += `
                            </tbody>
                        </table>
                    </div>
                `;
                preloader('off');
                principal.grillaExamenes.append(contenido);
            }
        }
    }

    function estado(data) {
        
        if([0,1,2].includes(data)){
            return `<span class="rojo">Abierto <i class="fs-6 ri-lock-unlock-line"></i><span>`;
        
        }else if([3,4,5].includes(data)){
            return `<span class="verde">Cerrado <i class="fs-6 ri-lock-2-line"></i><span>`;
        }          
    }

    //No Imprime: saber si es fisico o digital / adjunto: si acepta o no adjuntos / condicion: pendiente o adjuntado
    function checkAdjunto(noImprime, adjunto, condicion) {
        // console.log(noImprime, adjunto, condicion)
        if (adjunto === 0) {
            return ``;
        }else if(adjunto === 1 && condicion > 0 && noImprime === 0) {
            return `<span class="verde">Adjuntado <i class="fs-6 ri-map-pin-line"></i><span>`;
        }else if(adjunto === 1 && condicion === 0 && noImprime === 0) {
            return `<span class="rojo d-flex align-items-center justify-content-between w-100">
                        <span class="me-auto">Pendiente</span>
                        <i class="fs-6 ri-map-pin-line mx-auto"></i>
                        <i class="fs-6 ri-folder-add-line ms-auto"></i>
                    </span>`;
        }else if(adjunto === 1 && noImprime === 1){
            return `<span class="mx-auto"><i class="gris fs-6 ri-map-pin-line"></i><span>`;
        }else{
            return ``;
        }
    }



});