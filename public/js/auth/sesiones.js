$(function(){

    getSesiones(ID);

    function getSesiones(id) {
        $('#lstSesionesUsuario').empty();

        // if([null, undefined, ''].includes(id)) return;

        preloader('on');
        $.get(cargarSessiones, {Id: id})
            .done(function(response) {
                let contenido = '';
                preloader('off');

                if(response && response.length > 0){

                    $.each(response, function(index, data) {
                    
                        contenido += `
                        <tr>
                            <td>${data.ip}</td>
                            <td>${limpiarUserAgent(data.dispositivo)}</td>
                            <td>${fechaCompleta(data.ingreso)}</td>
                            <td>${fechaCompleta(data.salida)}</td>
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

                $("#listaSesionesUsuario").fancyTable({
                    pagination: true,
                    perPage: 15,
                    searchable: false,
                    globalSearch: false,
                    sortable: false, 
                });
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            }); 
    }

});