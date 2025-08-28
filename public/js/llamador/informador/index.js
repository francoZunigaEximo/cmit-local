$(function(){

    const profesionales = ['EFECTOR', 'INFORMADOR', 'COMBINADO'];

    const principal = {
        buscar: $('#buscar'),
        atenderInformador: $('#atenderInformador')
    };

    const variables = {
        fechaHasta: $('#fechaHasta'),
        estado: $('#estado'),
        especialidadSelect: $('#especialidadSelect'),
        profesional: $('#profesional'),
        informador: 'Informador',
    };

    variables.fechaHasta.val(fechaNow(null, "-", 0));
    variables.estado.val('abierto');

    habilitarBoton(sessionProfesional);
    listadoEspecialidades();

    variables.profesional.change(function(){
        listadoEspecialidades();
    });

    $(document).on('click', '.atenderExamen', function(e){
        e.preventDefault();

        $(principal.atenderInformador).modal('show');
    });

    function listadoEspecialidades() {

        preloader('on');
        $.get(searchEspecialidad, {IdProfesional: variables.profesional.val(), Tipo: variables.informador})
            .done(function(response){
                preloader('off');
                
                variables.especialidadSelect.empty();

                for(let index = 0; index < response.length; index++) {
                    let data = response[index],
                        contenido = `
                            <option value="${data.Id}">${data.Nombre}</option>
                        `;
                    variables.especialidadSelect.append(contenido);
                }
                
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            });
    }
});