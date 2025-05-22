$(function(){

    const socket = {
        selectEfectores: {
            echo: window.Echo.channel('listado-efectores'),
            canal: '.LstProfesionalesEvent'
        },
        grillaEfectores:  {
            echo: window.Echo.channel('grilla-efectores'),
            canal: '.GrillaEfectoresEvent'
        }
    };

    const variables = {
        profesional: $('#profesional'),
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

                if (!checkRoles && usuarios.includes(parseInt(USERACTIVO))) {

                    variables.profesional.append(
                        `<option value="${efectores[0].Id}" selected>${efectores[0].NombreCompleto}</option>`
                    );
                } else if(checkRoles) {

                    toastr.info('Se ha actualizado el listado de profesionales');

                    variables.profesional.append('<option value="" selected>Elija una opción...</option>');
                    $.each(efectores, function(index, value){
                        let contenido = `<option value="${value.Id}">${value.NombreCompleto}</option>`;

                        variables.profesional.append(contenido);
                    });

                } else {
                    variables.profesional.append(
                        `<option value="" selected>No hay efectores</option>`
                    );
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

});
