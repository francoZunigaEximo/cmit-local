$(function(){

    const socket = {
        selectEfectores: {
            echo: window.Echo.channel('listado-efectores'),
            canal: '.LstProfesionalesEvent'
        },
        selectInformadores: {
            echo: window.Echo.channel('listado-informadores'),
            canal: '.LstProfInformadorEvent'
        },
        selectCombinado: {
            echo: window.Echo.channel('listado-combinado'),
            canal: '.LstProfCombinadoEvent'
        },
        grillaEfectores:  {
            echo: window.Echo.channel('grilla-efectores'),
            canal: '.GrillaEfectoresEvent'
        },
        liberarAtencion: {
            echo: window.Echo.channel('liberar-atencion'),
            canal: '.LiberarPacientesEvent'
        },
        asignarProfesional: {
            echo: window.Echo.channel('asignar-profesional'),
            canal: '.AsignarProfesionalEvent'
        }
    };

    const variables = {
        profesional: $('#profesional'),
        profesionalInf: $('#profesionalInf'),
        profesionalComb: $('#profesionalComb'),
        profesionalEva: $('#profesionalEva'),
        especialidadSelect: $('#especialidadSelect'),
        especialidad: $('#especialidad')
    };

    const principal = {
        atenderPaciente: $('.atenderPaciente'),
        llamarExamen: $('.llamarExamen'),
        liberarExamen: $('.liberarExamen'),
        mensajeOcupado: $('.mensaje-ocupado')
    }

    const ADMIN = [
        'Administrador', 
        'Admin SR', 
        'Recepcion SR'
    ];

    socket.selectEfectores
          .echo
          .listen(socket.selectEfectores.canal, (response) => {
                const efectores = response.efectores;
                variables.profesional.empty();

                let roles = ROLESUSER.map(r => r.nombre)
                    checkRoles = roles.some(rol => ADMIN.includes(rol)),
                    usuarios = efectores.map(e => e.Id);

                console.log(efectores);
                
                if(!checkRoles && usuarios.includes(parseInt(USERACTIVO))) {

                    variables.profesional.append(
                        `<option value="${efectores[0].Id}" selected>${efectores[0].NombreCompleto}</option>`
                    );
                } else if(checkRoles) {

                    toastr.info('Se ha actualizado el listado de profesionales');
                    variables.profesional.append('<option value="" selected>Elija una opción...</option>');

                    for(let index = 0; index < efectores.length; index++) {
                        let value = efectores[index],
                            contenido = `<option value="${value?.Id}">${value?.NombreCompleto || ''}</option>`;

                        variables.profesional.append(contenido);
                    }

                    if(efectores.length === 0) {
                        variables.especialidad.add(variables.especialidadSelect).empty(); 
                    }

                } else {
                    variables.profesional.append(
                        `<option value="" selected>No hay efectores</option>`
                    );
                    variables.especialidad.add(variables.especialidadSelect).empty(); //limpiamos la especialidad del efector o admin si no hay efectores
                }
    });

   
    socket.grillaEfectores
        .echo
        .listen(socket.grillaEfectores.canal, async(response) => {
        
            const data = response.grilla;

        let fila = $(`tr[data-id="${data.prestacion}"]`);

        if (fila.length === 0) return;

        let botonLlamada = fila.find('.llamarExamen, .liberarExamen'),
            botonAtender = fila.find('.atenderPaciente'),
            mensajeOcupado = fila.find('.mensaje-ocupado'),
            result = await $.get(checkLlamado, { id: data.prestacion });

        if (data.status === 'llamado') {

            botonLlamada.removeClass(principal.llamarExamen)
                    .addClass(principal.liberarExamen)
                    .html('<i class="ri-edit-line"></i> Liberar');
            botonAtender.show();
            fila.find('td').css('color', 'red');

            if (parseInt(USERACTIVO) !== parseInt(result.profesional_id)) {
                botonLlamada.add(botonAtender)
                    .hide();
                
                if (!mensajeOcupado.length) {
                    botonLlamada.last().after('<span class="mensaje-ocupado rojo text-center fs-bolder">Ocupado</span>');
                }
            } else {
                mensajeOcupado.remove();
                botonLlamada.show();
            }

        } else {

            botonLlamada.removeClass(principal.liberarExamen)
                    .addClass(principal.llamarExamen)
                    .html('<i class="ri-edit-line"></i> Llamar');

            mensajeOcupado.remove();
            botonAtender.hide();
            fila.find('td').css('color', 'green');
            botonLlamada.show();
        }
    });

    socket.liberarAtencion
        .echo
        .listen(socket.liberarAtencion.canal, async function(response) {

            let idsAtencion = response.liberar;

            idsAtencion = [...new Set(idsAtencion)];

            for (let index = 0; index < idsAtencion.length; index++) {

                let id = idsAtencion[index],
                    fila = $(`tr[data-id="${id}"]`),
                    botonLlamada = fila.find('.llamarExamen, .liberarExamen'),
                    botonAtender = fila.find('.atenderPaciente'),
                    mensajeOcupado = fila.find('.mensaje-ocupado');

                     botonLlamada.removeClass(principal.liberarExamen)
                        .addClass(principal.llamarExamen)
                        .html('<i class="ri-edit-line"></i> Llamar');
                    
                    botonAtender.hide();
                    fila.find('td').css('color', 'green');
                    mensajeOcupado.remove();
                    botonLlamada.show();
            }
        });

    socket.asignarProfesional
        .echo
        .listen(socket.asignarProfesional.canal, async function(response) {
            let data = response.profesional,
                fila = $(`tr.listadoAtencion[data-id="${data.itemprestacion}"]`);

            if(!fila.length) return;

            let celda = fila.find('td').eq(4);
            celda.empty().text(data.profesional);
        });

});