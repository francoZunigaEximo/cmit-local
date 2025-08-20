async function cargarArchivosEfector(idPrestacion, idProfesional, idEspecialidad){

        if(!idPrestacion || !idProfesional || !idEspecialidad) return;

        $('#adjuntosEfectores').empty();
        preloader('on');
        $.get(await paginacionByPrestacion, {Id: idPrestacion, tipo: 'efector', especialidad: idEspecialidad, IdProfesional: idProfesional})
            .done(async function(response){
                preloader('off');
                let data = response.resultado;

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
                                    ${[3,4,5].includes(d.CAdj) || (d.Anulado === 1) ? `
                                    <div class="remove">
                                        <button data-id="${d.IdE}" data-tipo="efector" data-itempres="${d.IdItem}" class="btn btn-sm iconGeneral deleteAdjunto" title="Eliminar">
                                            <i class="ri-delete-bin-2-line"></i>
                                        </button>
                                    </div>
                                    ` : ``}
                                    
                                </div>
                            </td>
                        </tr>
                    `;

                   $('#adjuntosEfectores').append(contenido);
                }); 
            });
    }

async function comentariosPrivados(id){

    $('#privadoPrestaciones').empty();
    preloader('on');

    let lstRoles = await $.get(getRoles, function(response){
        return response;
    });

    $.get(privateComment, {Id: id,  tipo: 'prestacion'})
        .done(async function(response){ 
            let data = await response.result,
                comentarios = data.filter(comentario => comentario.nombre_perfil && comentario.nombre_perfil.toLowerCase() !== sessionName && comentario.Rol === 'Efector');
                roles = lstRoles.map(rol => rol.nombre),
                dataFiltrada = roles.includes('Administrador') ? data : comentarios;

            $.each(dataFiltrada, function(index, d){
                preloader('off');
                let contenido =  `
                    <tr>
                        <td>${fechaCompleta(d.Fecha)}</td>
                        <td class="text-capitalize">${d.IdUsuario}</td>
                        <td class="text-uppercase">${d.nombre_perfil}</td>
                        <td class="text-start">${d.Comentario}</td>
                    </tr>
                `;
                $('#privadoPrestaciones').append(contenido);
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            });

            

            $('#lstPrivPrestaciones').fancyTable({
                pagination: true,
                perPage: 15,
                searchable: false,
                globalSearch: false,
                sortable: false, 
            });
        })   
}
