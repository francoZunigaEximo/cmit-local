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



    async function listadoEspecialidades() {
    preloader('on');

        try {
            const response = await $.get(searchEspecialidad, {
                IdProfesional: variables.profesional.val(),
                Tipo: variables.efector
            });

            variables.especialidadSelect.empty();

            for (let index = 0; index < response.length; index++) {
                let data = response[index];
                let contenido = `<option value="${data.Id}">${data.Nombre}</option>`;
                variables.especialidadSelect.append(contenido);
            }

        } catch (jqXHR) {
            let errorData = JSON.parse(jqXHR.responseText);
            checkError(jqXHR.status, errorData.msg);
        } finally {
            preloader('off');
        }
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