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

                let texto = data.status === 'llamado' ? 'red' : 'green',
                    fila = $(`tr[data-id="${data.prestacion}"]`);
                
                if (fila.length > 0) {
                    fila.css('color', texto);
                    
                     let boton = fila.find(principal.llamarExamen + ', ' + principal.liberarExamen);

                    if (data.status === 'llamado') {
                        boton.removeClass(principal.llamarExamen)
                            .addClass(principal.liberarExamen)
                            .html('<i class="ri-edit-line"></i> Liberar');
                    } else {
                        boton.removeClass(principal.liberarExamen)
                            .addClass(principal.llamarExamen)
                            .html('<i class="ri-edit-line"></i> Llamar');
                    }
                }

                let result = await $.get(checkLlamado, { id: data.prestacion });

                let botones = $('button[data-id]');

                for (let i = 0; i < botones.length; i++) {
                    let boton = $(botones[i]),
                        botonId = boton.data('id'),
                        fila = boton.closest('tr, div.row, div.fila');

                    if (botonId == result.prestacion_id) {
                        if (parseInt(USERACTIVO) !== parseInt(result.profesional_id)) {

                            const botones = $(principal.llamarExamen + ',' + principal.liberarExamen + ',' + principal.atenderPaciente + ',' + fila);
                            botones.hide();

                            if (!fila.find(principal.mensajeOcupado).length) {
                                botones.last().after('<span class="mensaje-ocupado rojo text-center fs-bolder">Ocupado</span>');
                                fila.find('td').css('color', 'red')
                            }
                        }
                    } else {
                        fila.find(principal.mensajeOcupado).remove();
                        $(principal.llamarExamen + ',' + principal.liberarExamen + ',' + principal.atenderPaciente + ',' + fila).show();
                        fila.find('td').css('color', 'green')
                    }
                }
            });

});
