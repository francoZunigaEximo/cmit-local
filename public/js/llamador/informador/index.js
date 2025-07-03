$(function(){

    const profesionales = ['EFECTOR', 'INFORMADOR', 'COMBINADO'];

    const principal = {
        buscar: $('#buscar')
    };

    const variables = {
        fechaHasta: $('#fechaHasta'),
        estado: $('#estado')
    };

    variables.fechaHasta.val(fechaNow(null, "-", 0));
    variables.estado.val('abierto');

    habilitarBoton(sessionProfesional);


    function habilitarBoton(profesional) {
        if(!profesional) return principal.buscar.hide();

        return (profesionales[1] === profesional) 
            ? principal.buscar.show()
            : principal.buscar.hide();
    }
});