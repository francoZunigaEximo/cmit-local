$(function(){

    const variables = {
        usuario: $('#usuario')
    };

    cargarAdmins();

    async function cargarAdmins() {

        const response = await $.get(loadAdmins);
        
        variables.usuario.empty().append('<option selected value="">Elija una opción</option>');

        $.each(response, function(index, data){
            let contenido = `
                <option value="${data.usuario}">${data.usuario} (${data.NombreCompleto})</option>
            `;

            variables.usuario.append(contenido);
        })
    }

});