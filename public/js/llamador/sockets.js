$(function(){

    const socket = {
        selectEfectores: {
            echo: window.Echo.channel('listado-efectores'),
            canal: '.LstProfesionalesUpdateEvent'
        },
        grillaEfectores:  {
            echo: window.Echo.channel('grilla-efectores'),
            canal: '.GrillaEfectoresEvent'
        },
        onlineEfectores: {
            echo: window.Echo.channel('listado-efectores-online'),
            canal: '.LstProfEfectoresEvent'
        }
    };

    const variables = {
        profesional: $('#profesional')
    };

    socket.selectEfectores
          .echo
          .listen(socket.selectEfectores.canal, (response) => {
                const efectores = response.efectores;

                variables.profesional.empty();

                toastr.info('Se ha actualizado la lista de profesionales');

                if (efectores.length === 1) {

                    variables.profesional.append(
                        `<option value="${efectores[0].Id}" selected>${efectores[0].NombreCompleto}</option>`
                    );
                } else if(efectores.length > 1) {

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
                // console.log(data);
                let texto = data.status === 'llamado' ? 'red' : 'black';
            
                let fila = $(`tr[data-id="${data.prestacion}"]`);
                if (fila.length > 0) {
                    fila.css('color', texto);
                }
    });

    socket.onlineEfectores
    .echo
    .listen(socket.onlineEfectores.canal, (response) => {
          const efectores = response.efectores;
            console.log(efectores);
          variables.profesional.empty();

          toastr.info('Un profesional ha cambiado su estado');

          if (efectores.length === 1) {

              variables.profesional.append(
                  `<option value="${efectores[0].Id}" selected>${efectores[0].NombreCompleto}</option>`
              );
          } else if(efectores.length > 1) {

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

});
