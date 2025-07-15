const errores = {
    400: ['Solicitud incorrecta', 'warning'],
    401: ['No autorizado', 'warning'],
    403: ['Acceso prohibido', 'warning'],
    404: ['No encontrado', 'error'],
    405: ['Método no permitido', 'error'],
    409: ['Conflicto', 'warning'],
    500: ['Error interno en el servidor', 'error'],
    503: ['Servicio Inhabilitado', 'error'],
    504: ['Tiempo de espera agotado', 'error']
};

function checkError(status, msg = 'Ha ocurrido un error inesperado') {

    if (status in errores) {
        let [titulo, tipo] = errores[status];
        toastr[tipo](msg, titulo, { timeOut: 1000 });
    }
}