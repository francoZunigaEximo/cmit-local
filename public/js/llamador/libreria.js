async function checkerRolUser() {

    let roles = await $.get(getRoles),
        lista = roles.map(rol => rol.nombre),
        administradores = ['Administrador', 'Admin SR', 'Recepcion SR'],
        admin = lista.some(item => administradores.includes(item));

        return [admin, parseInt(USERACTIVO)];
}

const check = checkerRolUser();


async function cargarArchivosEfector(idPrestacion, idProfesional, idEspecialidad){

        if(!idPrestacion || !idProfesional || !idEspecialidad) return;

        $('#adjuntosEfectores, #adjuntosEfectoresVista').empty();

        $.get(await paginacionByPrestacion, {Id: idPrestacion, tipo: 'efector', especialidad: idEspecialidad, IdProfesional: idProfesional})
            .done(async function(response){
               
                let data = response.resultado;

                const [isAdmin, esUsuarioPermitido] = await check;
                let permiso = !isAdmin && (esUsuarioPermitido === idProfesional);

                $.each(data, function(index, d){

                    let contenido = `
                        <tr>
                            <td>${d.NombreExamen}</td>
                            <td>${(d.DescripcionE ? d.DescripcionE : '')}</td>
                            <td>${d.RutaE}</td>
                            <td>${(d.MultiE === 0 ? '' : '<i class="ri-check-line verde"></i>')}</td>
                            <td>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                    <div class="edit">
                                        <a href="${descargaE}/${d.RutaE}" target="_blank">
                                            <button type="button" class="btn btn-sm iconGeneral" title="Ver"><i class="ri-search-eye-line"></i></button>
                                        </a>
                                    </div>
                                    <div class="download">
                                        <a href="${descargaE}/${d.RutaE}" target="_blank" download>
                                            <button type="button" class="btn btn-sm iconGeneral" title="Descargar"><i class="ri-download-2-line"></i></button>
                                        </a>
                                    </div>
                                ${permiso ? 
                                `
                                    ${[3,4,5].includes(d.CAdj) || (d.Anulado === 1) ? `
                                    <div class="remove">
                                        <button data-id="${d.IdE}" data-tipo="efector" data-itempres="${d.IdItem}" class="btn btn-sm iconGeneral deleteAdjunto" title="Eliminar">
                                            <i class="ri-delete-bin-2-line"></i>
                                        </button>
                                    </div>
                                    ` : ``}
                                ` : ''
                            }
                                
                                    
                                </div>
                            </td>
                        </tr>
                    `;

                   $('#adjuntosEfectores, #adjuntosEfectoresVista').append(contenido);
             
                }); 
            });
    }

async function comentariosPrivados(id) {
    $('#privadoPrestaciones, #privadoPrestacionesVista').empty();
    preloader('on');

    try {

        const lstRoles = await $.get(getRoles);
        const response = await $.get(privateComment, { Id: id, tipo: 'prestacion' });
        
        let data = await response.result;
        let comentarios = data.filter(comentario => comentario.IdUsuario && comentario.IdUsuario.toLowerCase() === sessionName.toLowerCase() && comentario.Rol === 'Efector');
        let roles = lstRoles.map(rol => rol.nombre);
        let dataFiltrada = roles.includes('Administrador') ? data : comentarios;

        if (dataFiltrada.length === 0) {
            preloader('off');
            return; 
        }

        $.each(dataFiltrada, function(index, d) {
            let contenido = `
                <tr>
                    <td>${fechaCompleta(d.Fecha)}</td>
                    <td class="text-capitalize">${d.IdUsuario}</td>
                    <td class="text-uppercase">${d.nombre_perfil}</td>
                    <td class="text-start">${d.Comentario}</td>
                </tr>
            `;
            $('#privadoPrestaciones, #privadoPrestacionesVista').append(contenido);
        });

        preloader('off');

        $('#lstPrivPrestaciones, #lstPrivPrestacionesVista').fancyTable({
            pagination: true,
            perPage: 15,
            searchable: false,
            globalSearch: false,
            sortable: false,
        });

    } catch (jqXHR) {
        preloader('off');
        let errorMsg = 'Ocurrió un error de red o el servidor no está disponible.';

        if (jqXHR.responseText) {
            try {
                const errorData = JSON.parse(jqXHR.responseText);
                
                if (errorData && errorData.msg) {
                    errorMsg = errorData.msg;
                }
            } catch (e) {
                console.error("La respuesta de error no era JSON:", jqXHR.responseText);
            }
        }
        
        checkError(jqXHR.status, errorMsg);
    }
}

async function tablasExamenes(data, usuarioVisita) {
    const [isAdmin, esUsuarioPermitido] = await check;
    let permiso = isAdmin || (esUsuarioPermitido === parseInt(usuarioVisita));

   $('#tablasExamenes, #tablasExamenesVista').empty();
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
                <div class="especialidad-grilla mb-2" data-name="${especialidad}">
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

            // examenes.forEach(async function (examen) {
            for (const examen of examenes) {
                const estadoCheck = await estado(examen.CAdj, usuarioVisita);
                const adjunto = await checkAdjunto(examen.Adjunto, examen.Archivo, examen.IdItem, usuarioVisita);

                contenido += `
                    <tr class="listadoAtencion" data-id="${examen.IdItem}">
                        <td>${examen.NombreExamen}</td>
                        <td>${estadoCheck}</td>
                        <td>${adjunto}</td>
                        <td>${!examen.ObsExamen ? '' : examen.ObsExamen}</td>
                        <td>${examen.efectorId === 0 ? '' : verificarProfesional(examen, "efector")}</td>
                        <td>${examen.informadorId === 0 ? '' : verificarProfesional(examen, "informador")}</td>
                        <td>
                            ${permiso ? `<input type="checkbox" name="Id_${limpiarAcentosEspacios(especialidad)}_${examen.IdExamen}" value="${examen.IdItem}"  ${checkboxCheck(examen)}>`: ''}
                        </td>
                    </tr>
                `;
            // });
            }

            contenido += `
                        </tbody>
                    </table>
                </div>
            `;
            preloader('off');
           $('#tablasExamenes, #tablasExamenesVista').append(contenido);
        }
    }
}

async function estado(data, usuarioVisita) {

    const [isAdmin, esUsuarioPermitido] = await check;
    let permiso = isAdmin || (esUsuarioPermitido === parseInt(usuarioVisita));

    switch (true) {
        case [0, 1, 2].includes(data):
            return `<span class="rojo">Abierto ${permiso ? '<i class="fs-6 ri-lock-unlock-line cerrar"></i>' : ''}</span>`;

        case [3, 4, 5].includes(data):
            return `<span class="verde">Cerrado ${permiso ? '<i class="fs-6 ri-lock-2-line abrir"></i>' : ''}</span>`;

        default:
            return '';
    }
}



//No Imprime: saber si es fisico o digital / adjunto: si acepta o no adjuntos / condicion: pendiente o adjuntado
async function checkAdjunto(adjunto, condicion, idItem, usuarioVisita) {
    const [isAdmin, esUsuarioPermitido] = await check;
    let permiso = isAdmin || (esUsuarioPermitido === parseInt(usuarioVisita));

    switch (true) {
        case adjunto === 0:
            return '';

        case adjunto === 1 && condicion === 1:
            return `<span class="verde">Adjuntado <i class="fs-6 ri-map-pin-line"></i><span>`;

        case adjunto === 1 && condicion === 0:
            return `<span class="rojo d-flex align-items-center justify-content-between w-100">
                        <span class="me-auto">Pendiente</span>
                        <i class="fs-6 ri-map-pin-line mx-auto"></i>
                        ${permiso ? `<i class="fs-6 ri-folder-add-line ms-auto" id="modalArchivo" data-id="${idItem}"></i>` : ''} 
                    </span>`;

        case adjunto === 0:
            return `<span class="mx-auto"><i class="gris fs-6 ri-map-pin-line"></i><span>`;

        default:
            return '';
    }
}

function verificarProfesional(data, tipoProfesional) {
    if(data.length === 0) return;

    switch (true) {
        case tipoProfesional === 'efector':
            return data.efectorId === 0 ? '' : data.Efector; // data.EfectorHistorico || data.Efector || '';
        
        case tipoProfesional === 'informador':
            return data.informadorId === 0 ? '' : data.Informador; // data.InformadorHistorico || data.Informador || '';
        
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

async function habilitarBoton(profesional, tipo) {

    let roles = await $.get(getRoles),
        usuarios = roles.map(u => u.nombre),a
        administradores = ['Administrador', 'Admin SR', 'Recepcion SR'],
        admin = usuarios.some(item => administradores.includes(item)),
        mespecialidad = await $.get(multiespecialidad);

    let esProfesional = (tipo === profesional);
    let esMultiespecialidad = (mespecialidad === "1");

    if(esProfesional || admin || esMultiespecialidad) {
        $('#buscar').show();
    } else {
        $('#buscar').hide();
    }
}

async function listadoEspecialidades(tipo) {
    preloader('on');
    try {
        let response =  await $.get(searchEspecialidad, 
            {    
                IdProfesional: $('#profesional').val(), 
                Tipo: tipo
            });
        $('#especialidadSelect').empty().append('<option selected value="">Elija una opción</option>');

        if(response) {
            $('#especialidadSelect').append('<option value="todos">TODO</option>');
        }

        for(let index = 0; index < response.length; index++) {
            let data = response[index],
                contenido = `<option value="${data.Id}">${data.Nombre}</option>`;
            $('#especialidadSelect').append(contenido);
        }

    }catch(jqXHR) {
        let errorData = JSON.parse(jqXHR.responseText);            
        checkError(jqXHR.status, errorData.msg);
        return;

    }finally {
        preloader('off');
    }
}
