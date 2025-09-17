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
        grillaInformadores: {
            echo: window.Echo.channel('listado-informadores'),
            canal: '.GrillaInformadoresEvent'
        },
        liberarAtencion: {
            echo: window.Echo.channel('liberar-atencion'),
            canal: '.LiberarPacientesEvent'
        },
        asignarProfesional: {
            echo: window.Echo.channel('asignar-profesional'),
            canal: '.AsignarProfesionalEvent'
        },
        tablaExamenes: {
            echo: window.Echo.channel('actualizar-tablaExamenes'),
            canal: '.TablaExamenesEvent'
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
        mensajeOcupado: $('.mensaje-ocupado'),
        atenderEfector: $('#atenderEfector')
    }

    const ADMIN = [
        'Administrador', 
        'Admin SR', 
        'Recepcion SR'
    ];

    const profesionales = ['EFECTOR', 'INFORMADOR', 'COMBINADO'];

    // socket.selectEfectores
    //       .echo
    //       .listen(socket.selectEfectores.canal, (response) => {
    //             const efectores = response.efectores;
    //             variables.profesional.empty();

    //             let roles = ROLESUSER.map(r => r.nombre)
    //                 checkRoles = roles.some(rol => ADMIN.includes(rol)),
    //                 usuarios = efectores.map(e => e.Id);
                
    //             if(!checkRoles && usuarios.includes(parseInt(USERACTIVO))) {

    //                 variables.profesional.append(
    //                     `<option value="${efectores[0].Id}" selected>${efectores[0].NombreCompleto}</option>`
    //                 );
    //             } else if(checkRoles) {

    //                 toastr.info('Se ha actualizado el listado de profesionales');
    //                 variables.profesional.append('<option value="" selected>Elija una opción...</option>');

    //                 for(let index = 0; index < efectores.length; index++) {
    //                     let value = efectores[index],
    //                         contenido = `<option value="${value?.Id}">${value?.NombreCompleto || ''}</option>`;

    //                     variables.profesional.append(contenido);
    //                 }

    //                 if(efectores.length === 0) {
    //                     variables.especialidad.add(variables.especialidadSelect).empty(); 
    //                 }

    //             } else {
    //                 variables.profesional.append(
    //                     `<option value="" selected>No hay efectores</option>`
    //                 );
    //                 variables.especialidad.add(variables.especialidadSelect).empty(); //limpiamos la especialidad del efector o admin si no hay efectores
    //             }
    // });

   
    socket.grillaEfectores
        .echo
        .listen(socket.grillaEfectores.canal, async(response) => {
        
        const data = response.grilla;

        let fila = $(`tr[data-id="${data.prestacion}"]`),
            idFila = fila.find('.badge-atencion').data('prestacion-id');

        if (fila.length === 0) return;

        let botonLlamada = fila.find('.llamarExamen, .liberarExamen'),
            botonAtender = fila.find('.atenderPaciente'),
            mensajeOcupado = fila.find('.mensaje-ocupado'),
            vistaAdmin = fila.find('.vista-admin'),
            cerrarAtencion = fila.find('.cerrar-atencion')
            result = await $.get(checkLlamado, { id: data.prestacion, tipo: 'EFECTOR' });

        if (data.status === 'llamado' && profesionales[0] === 'EFECTOR') {

            botonLlamada.removeClass(principal.llamarExamen)
                    .addClass(principal.liberarExamen)
                    .html('<i class="ri-edit-line"></i> Liberar');
            botonAtender.show();
            fila.find('td').css('color', 'red');

            let check = await $.get(checkAtencion, {Id: idFila});
        
            if(check) {
                fila.find('.badge-atencion[data-prestacion-id="' + idFila +'"]')
                         .addClass('custom-badge generalNegro px-2')
                         .text(check.profesional);
            }

            let lstRoles = await $.get(getRoles),
                roles = lstRoles.map(rol => rol.nombre),
                tienePermiso = ADMIN.some(rol => roles.includes(rol));

            

            if (parseInt(USERACTIVO) !== parseInt(result.profesional_id)) {
                botonLlamada.add(botonAtender)
                    .hide();
                
                if (!mensajeOcupado.length) {

                    if(tienePermiso) {
                        botonLlamada.last().after(`<span title="Liberar atencion" id="clickCierreForzado" data-profesional="${result.profesional_id}" data-prestacion="${data.prestacion}" class="cerrar-atencion"><i class="ri-logout-box-line"></i></span>`);
                        botonLlamada.last().after(`<span title="Visualizar actividad" id="clickAtencion" class="vista-admin px-2" data-id="${data.prestacion}"><i class="ri-search-eye-line"></span>`);
                    }
                    
                    botonLlamada.last().after('<span class="mensaje-ocupado rojo text-center fs-bolder">Ocupado</span>');
                }
            } else {
                
                badgeSpan.removeClass('custom-badge generalNegro px-2').text('');
                mensajeOcupado
                    .add(vistaAdmin)
                    .add(cerrarAtencion)
                    .remove();
                botonLlamada.show();
            }

        } else {

            botonLlamada.removeClass(principal.liberarExamen)
                    .addClass(principal.llamarExamen)
                    .html('<i class="ri-edit-line"></i> Llamar');

            mensajeOcupado.remove();
            vistaAdmin.remove();
            cerrarAtencion.remove();
            botonAtender.hide();
            fila.find('td').css('color', 'green');
            botonLlamada.show();

            badgeSpan.removeClass('custom-badge generalNegro px-2').text('');

            //usamos JS Puro para evitar problemas de compatibilidad con otros navegadores xD
            let modalAtender = document.getElementById('atenderEfector');

            if (modalAtender && modalAtender.classList.contains('show')) {
                const modalInstancia = bootstrap.Modal.getOrCreateInstance(modalAtender);
                modalInstancia.hide();
            }

        }
    });

    socket.liberarAtencion
        .echo
        .listen(socket.liberarAtencion.canal, async function(response) {

            let idsAtencion = response.liberar;
            console.log(idsAtencion);

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
            
            if(fila.length === 0) return;

            let celda = fila.find('td').eq(4);
            console.log("Contenido anterior de la celda:", celda.text());
            celda.empty().text(data.profesional);
            console.log("Contenido actual de la celda:", celda.text());
        });

    socket.tablaExamenes
        .echo
        .listen(socket.tablaExamenes.canal, async function(response) {

            let data = response.tablaExamenes;
            tablasExamenes(data.itemsprestaciones, data.profesional);
            toastr.info('Actualizando tabla de examenes...','',{timeOut: 1000});
        });

});