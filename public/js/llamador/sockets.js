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
        llamarExamen: $('.llamarExamen')
    };

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
          .listen(socket.grillaEfectores.canal, (response) => {
                const data = response.grilla;
                
                console.log(USERACTIVO, profesional.val())

                let texto = data.status === 'llamado' ? 'red' : 'black';
            
                let fila = $(`tr[data-id="${data.prestacion}"]`);
                if (fila.length > 0) {
                    fila.css('color', texto);
                }

                if(parseInt(USERACTIVO) !== profesional.val()) {
                    llamarExamen.prop('disabled', true);
                }
    });

});
