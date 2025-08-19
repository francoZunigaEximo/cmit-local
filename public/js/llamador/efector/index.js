$(function(){

    const profesionales = ['EFECTOR', 'INFORMADOR', 'COMBINADO'];

    const principal = {
        grillaEfector: $('#listaLlamadaEfector'),
        grillaExamenes: $('#tablasExamenes'),
        atenderPaciente: $('.atenderPaciente'),
        chekAllExamenes: $('.checkAllExamenes'),
        buscar: $('#buscar'),
    };

    const variables = {
        fechaHasta: $('#fechaHasta'),
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

    variables.fechaHasta.val(fechaNow(null, "-", 0));
    variables.estado.val('abierto');
    
    habilitarBoton(sessionProfesional);
    listadoEspecialidades();


    $(document).on('click', '.verPrestacion', function(e){
        e.preventDefault();

        let prestacion = $(this).data('prestacion');
        window.open(lnkPres.replace('__item__', prestacion), '_blank');
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
        $.get(printExportar, {Ids: ids, modo: opcion === 'exportar' ? 'basico' : 'full'})
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
            especialidades = $(this).data('especialidades');

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

                comentariosPrivados(parseInt(prestacion.Id));
                tablasExamenes(response.itemsprestaciones);
                cargarArchivosEfector(parseInt(prestacion.Id), parseInt(variables.profesional.val()), variables.especialidad.data('id') || variables.especialidadSelect.val());

            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            });
    });

    variables.profesional.change(function(){
        listadoEspecialidades();
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
        
        $.get(addAtencion, {prestacion: $(this).data('id'), profesional: variables.profesional.val(), Tipo: $(this).data('tipo'), especialidad: variables.especialidad.data('id') || variables.especialidadSelect.val()})
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

    function listadoEspecialidades() {

        preloader('on');
        $.get(searchEspecialidad, {IdProfesional: variables.profesional.val(), Tipo: variables.efector})
            .done(function(response){
                preloader('off');
                
                variables.especialidadSelect.empty();

                for(let index = 0; index < response.length; index++) {
                    let data = response[index],
                        contenido = `
                            <option value="${data.Id}">${data.Nombre}</option>
                        `;
                    variables.especialidadSelect.append(contenido);
                }
                
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            });
    }

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
                                        <input type="checkbox" class="checkAllExamenes" name="Id_${limpiarAcentosEspacios(especialidad)}">
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                `;
    
                

                examenes.forEach(function (examen) {

                    contenido += `
                        <tr class="listadoAtencion" data-id="${examen.IdItem}">
                            <td>${examen.NombreExamen}</td>
                            <td>${estado(examen.CAdj)}</td>
                            <td title="Adjunto: ${examen.Adjunto} | Archivo: ${examen.Archivo}">${checkAdjunto(examen.Adjunto, examen.Archivo, examen.IdItem)}</td>
                            <td>${!examen.ObsExamen ? '' : examen.ObsExamen}</td>
                            <td>${examen.efectorId === 0 ? '' : verificarProfesional(examen, "efector")}</td>
                            <td>${examen.informadorId === 0 ? '' : verificarProfesional(examen, "informador")}</td>
                            <td>
                                <input type="checkbox" name="Id_${limpiarAcentosEspacios(especialidad)}_${examen.IdExamen}" value="${examen.IdItem}"  ${checkboxCheck(examen)}>
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
        switch (true) {
            case [0, 1, 2].includes(data):
                return `<span class="rojo">Abierto <i class="fs-6 ri-lock-unlock-line cerrar"></i></span>`;

            case [3, 4, 5].includes(data):
                return `<span class="verde">Cerrado <i class="fs-6 ri-lock-2-line abrir"></i></span>`;

            default:
                return '';
        }
    }

    //No Imprime: saber si es fisico o digital / adjunto: si acepta o no adjuntos / condicion: pendiente o adjuntado
    function checkAdjunto(adjunto, condicion, idItem) {
        switch (true) {
            case adjunto === 0:
                return '';

            case adjunto === 1 && condicion === 1:
                return `<span class="verde">Adjuntado <i class="fs-6 ri-map-pin-line"></i><span>`;

            case adjunto === 1 && condicion === 0:
                return `<span class="rojo d-flex align-items-center justify-content-between w-100">
                            <span class="me-auto">Pendiente</span>
                            <i class="fs-6 ri-map-pin-line mx-auto"></i>
                            <i class="fs-6 ri-folder-add-line ms-auto" id="modalArchivo" data-id="${idItem}"></i> 
                        </span>`;

            case adjunto === 0:
                return `<span class="mx-auto"><i class="gris fs-6 ri-map-pin-line"></i><span>`;

            default:
                return '';
        }
    }

    function checkboxCheck(data) {

        switch (true) {
            case data.informadorId !== 0:
                return 'disabled';
        
            case parseInt(data.efectorId) !== 0 && parseInt(data.efectorId) === parseInt(USERACTIVO):
                return 'checked';

            case parseInt(data.efectorId) !== 0 && parseInt(data.efectorId) !== parseInt(USERACTIVO):
                return 'checked disabled';
         
            default:
                return '';
        }
    }

    function verificarProfesional(data, tipoProfesional) {
        if(data.length === 0) return;

        switch (true) {
            case tipoProfesional === 'efector':
                return data.EfectorHistorico || data.Efector || '';
            
            case tipoProfesional === 'informador':
                return data.InformadorHistorico || data.Informador || '';
            
            default:
                return '';
        }
    }


    function habilitarBoton(profesional) {

        let usuarios = ROLESUSER.map(u => u.nombre),
            administradores = ['Administrador', 'Admin SR', 'Recepcion SR'],
            admin = usuarios.some(item => administradores.includes(item));

        if(!profesional && !admin) return principal.buscar.hide();

        return (profesionales[0] === profesional || admin) 
            ? principal.buscar.show()
            : principal.buscar.hide();
    }



});