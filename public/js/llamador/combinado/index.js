$(function(){

    const profesionales = ['EFECTOR', 'INFORMADOR', 'COMBINADO'];

    const principal = {
        buscar: $('#buscar'),
        profesional: $('#profesional')
    };

    const variables = {
        fechaHasta: $('#fechaHasta'),
        fechaDesde: $('#fechaDesde'),
        estado: $('#estado'),
        combinado: 'Combinado',
        especialidadSelect: $('#especialidadSelect')
    };

    variables.fechaHasta
        .add(variables.fechaDesde)
        .val(fechaNow(null, "-", 0));


    variables.estado.val('abierto');

    habilitarBoton(sessionProfesional);
    listadoEspecialidades();



    function listadoEspecialidades() {

        preloader('on');
        $.get(searchEspecialidad, {IdProfesional: variables.profesional.val(), Tipo: variables.efector})
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


    function habilitarBoton(profesional) {

        let usuarios = ROLESUSER.map(u => u.nombre),
            administradores = ['Administrador', 'Admin SR', 'Recepcion SR'],
            admin = usuarios.some(item => administradores.includes(item));

        if(!profesional && !admin) return principal.buscar.hide();

        return (profesionales[2] === profesional || admin) 
            ? principal.buscar.show()
            : principal.buscar.hide();
    }

});