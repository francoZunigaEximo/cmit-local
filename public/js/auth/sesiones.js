$(function(){

    getSesiones(ID);

    function getSesiones(id) {
        $('#lstSesionesUsuario').empty();

        // if([null, undefined, ''].includes(id)) return;

        preloader('on');
        $.get(lstSesionesUs, {Id: id})
            .done(function(response) {
                let contenido = '';
                preloader('off');
  
                console.log(response)

                if(response && response.length > 0){

                    $.each(response, function(index, data) {
                    
                        contenido += `
                        <tr>
                            <td>${data.ip}</td>
                            <td>${data.dispositivo}</td>
                            <td>${data.ingreso}</td>
                            <td>${data.salida}</td>
                        </tr>
                        `;
                    });
                }else{
                    contenido = `
                        <tr>
                            <td>No hay historial de sesiones</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        `;
                }

                $('#lstSesionesUsuario').append(contenido); 
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            }); 
    }

});