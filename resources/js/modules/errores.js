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

export function checkError(status, msg = 'Ha ocurrido un error') {

    switch(status) {
        case 400:
            msg = 
            toastr[errores[status][1]](msg, errores[status][0]);
            break;
        case 401:
            toastr[errores[status][1]](msg, errores[status][0]);
            break;
        case 403:
            toastr[errores[status][1]](msg, errores[status][0]);
            break;
        case 404:
            toastr[errores[status][1]](msg, errores[status][0]);
            break;
        case 405:
            toastr[errores[status][1]](msg, errores[status][0]);
            break;
        case 409:
            toastr[errores[status][1]](msg, errores[status][0]);
            break;
        case 500:
            toastr[errores[status][1]](msg, errores[status][0]);
            break;
        case 503:
            toastr[errores[status][1]](msg, errores[status][0]);
            break;
        case 504:
            toastr[errores[status][1]](msg, errores[status][0]);
            break;
        default:
            toastr.error('Ha ocurrido un error inesperado');
    }
}