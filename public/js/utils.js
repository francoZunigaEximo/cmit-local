export function fechaNow(fechaAformatear, divider, format) {
    let fechaActual;

    if (fechaAformatear === null) {
        fechaActual = new Date();
    } else {
        fechaActual = new Date(fechaAformatear);
    }

    let dia = fechaActual.getDate(), mes = fechaActual.getMonth() + 1, anio = fechaActual.getFullYear();

    dia = dia < 10 ? '0' + dia : dia;
    mes = mes < 10 ? '0' + mes : mes;

    return (format === 1) ? dia + divider + mes + divider + anio : anio + divider + mes + divider + dia;
}


export function quitarDuplicados(selector) {
    let seleccion = $(selector).val();
    let countSeleccion = $(selector + " option[value='" + seleccion + "']").length;

    if (countSeleccion > 1) {
        $(selector + " option[value='" + seleccion + "']:gt(0)").hide();
    }
}