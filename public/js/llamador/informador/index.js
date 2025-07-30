$(function(){

    const profesionales = ['EFECTOR', 'INFORMADOR', 'COMBINADO'];

    const principal = {
        buscar: $('#buscar')
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


    function habilitarBoton(profesional) {
        let usuarios = ROLESUSER.map(u => u.nombre),
            administradores = ['Administrador', 'Admin SR', 'Recepcion SR'],
            admin = usuarios.some(item => administradores.includes(item));

        if(!profesional && !admin) return principal.buscar.hide();

        return (profesionales[1] === profesional || admin) 
            ? principal.buscar.show()
            : principal.buscar.hide();
    }

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