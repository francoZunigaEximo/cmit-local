$(document).ready(function() {
    toastr.options = {
        closeButton: true,   
        progressBar: true,    
        timeOut: 3000,        
    };
});

function preloader(opcion) {
    $('#preloader').css({
        opacity: '0.3',
        visibility: opcion === 'on' ? 'visible' : 'hidden'
    });
}

function verificarCorreos(emails) {

    if(emails.length === 0) return false;
        
    let emailRegex = /^[\w.-]+(\.[\w.-]+)*@[\w.-]+\.[A-Za-z]{2,}$/;
    let correosInvalidos = [];
    let emailsArray = emails.split(',');

    for (let i = 0; i < emailsArray.length; i++) {
        let email = emailsArray[i].trim();

        if (email !== "" && !emailRegex.test(email)) {
            correosInvalidos.push(email);
        }
    }

    if (correosInvalidos.length > 0) {
        swal("Atención", "Estos correos tienen formato inválido. Verifique por favor: " + correosInvalidos.join(", "), "warning");
        return false; 
    }

    return true; 
}

function acortadorTexto(cadena, nroCaracteres = 10) {
    return cadena.length <= nroCaracteres ? cadena : cadena.substring(0,nroCaracteres);
}

function saltoLinea(cadena, caracteres = 110) {
    let textoConSaltos = '';
    for (var i = 0; i < cadena.length; i++) {
        textoConSaltos += cadena[i];
        if ((i + 1) % caracteres === 0) {
            textoConSaltos += '<br>';
        }
    }
    return textoConSaltos;
}

function ajustarFecha(fecha) {
    let fechaArray = fecha.split(' '), cortar = fechaArray[0].split('-'), nuevaFecha = `${cortar[2]}/${cortar[1]}/${cortar[0]}`;
    return `${nuevaFecha} ${fechaArray[1]} `;
}

function fechaNow(fechaAformatear, divider, format) {
    let dia, mes, anio; 

    if (fechaAformatear === null) {
        let fechaHoy = new Date();

        dia = fechaHoy.getDate().toString().padStart(2, '0');
        mes = (fechaHoy.getMonth() + 1).toString().padStart(2, '0');
        anio = fechaHoy.getFullYear();
    } else {
        let nuevaFecha = fechaAformatear.split("-"); 
        dia = nuevaFecha[0]; 
        mes = nuevaFecha[1]; 
        anio = nuevaFecha[2];
    }

    return (format === 1) ? `${dia}${divider}${mes}${divider}${anio}` : `${anio}${divider}${mes}${divider}${dia}`;
}

function quitarDuplicados(selector) {
    let seleccion = $(selector).val();
    let countSeleccion = $(selector + " option[value='" + seleccion + "']").length;

    if (countSeleccion > 1) {
        $(selector + " option[value='" + seleccion + "']:gt(0)").hide();
    }
}

function createFile(tipo, array, name){
    let filePath = array,
        pattern = /storage(.*)/,
        match = filePath.match(pattern),
        path = match ? match[1] : '';

    let url = new URL(location.href),
        baseUrl = url.origin,
        fullPath = baseUrl + '/cmit/storage' + path;

    let link = document.createElement('a');
    link.href = fullPath;
    link.download = tipo === 'pdf' ? name : name + ".xlsx";
    link.style.display = 'none';

    document.body.appendChild(link);
    link.click();
    setTimeout(function() {
        document.body.removeChild(link);
    }, 100);
}

function generarCodigoAleatorio() {

    let codigo = Math.floor(Math.random() * 9000000) + 1000000;
    return codigo.toString(); 
}

function tipoPagoPrestacion(tipo) {
    switch (tipo) {
        case "C":
            return "Cuenta Corriente";
        
        case "P":
            return "Examen a Cuenta";

        case "B":
            return "Contado";

        default:
            return "-";
    }
}

function tipoSPagoPrestacion(tipo) {
    switch (tipo) {
        case "A":
            return "Efectivo";
        case "B":
            return "Débito";
        case "C":
            return "Crédito";
        case "D":
            return "Cheque";
        case "G":
            return "Sin cargo";
        case "F":
            return "Transferencia";
        case "E":
            return "Otro";
        default:
            return "-";
    }
}

function correoValido(correo) {
    let comprobar = /^[\w.-]+(\.[\w.-]+)*@[\w.-]+\.[A-Za-z]{2,}$/;
    return comprobar.test(correo);
}

function verificarUsuario(usuario) {
    let validar = /^[A-Za-z0-9]{1,25}$/;
    return validar.test(usuario);
}

function fechaCompleta(fecha) {
    const date = new Date(fecha.replace(' ', 'T'));

    // Obtén el día, mes y año
    const dia = String(date.getDate()).padStart(2, '0');
    const mes = String(date.getMonth() + 1).padStart(2, '0'); // Los meses van de 0 a 11
    const anio = date.getFullYear();

    const hora = String(date.getHours()).padStart(2, '0');
    const minutos = String(date.getMinutes()).padStart(2, '0');
    const segundos = String(date.getSeconds()).padStart(2, '0');

    const formatoCompleto = `${dia}/${mes}/${anio} ${hora}:${minutos}:${segundos}`;

    return formatoCompleto;
}

function getDias(fecha){

    let fechaActual = new Date(), fechaLimiteAdmision = new Date(fecha), diff = fechaLimiteAdmision.getTime() - fechaActual.getTime();
   
    return (Math.round(diff/(1000*60*60*24)));
}

function stripTags(html) {
    if (html === null || html === undefined) {
        return;
    }
    return html.replace(/<\/?[^>]+(>|$)/g, "");
}

function verificarArchivo(archivo){

    if (!archivo || archivo.size === 0) {
        toastr.warning("Debe seleccionar un archivo", "Atención");
        return false;
    }

    if (!archivo.name.includes('.')) {
        toastr.warning("El archivo no tiene extensión o la misma es invalida", "Atención");
        return false;
    }

    let tipoArchivo = archivo.type.toLowerCase();

    if(tipoArchivo !== 'application/pdf') {
        toastr.warning("Solo se admite archivos PDF", "Atención");
        return false;
    }

    return true
}

function calculoAvance(data) {

    let cerradoAdjunto = data.CerradoAdjunto || 0, total = data.Total || 1;
    return data.Anulado === 0 ? ((cerradoAdjunto/total)*100).toFixed(0) : '0';

}

function obtenerFormato(date) {
    return date.toISOString().slice(0, 10);
};

function calcularEdad(fechaNacimiento) {
    let fechaNac = new Date(fechaNacimiento), fechaActual = new Date();

    let edad = fechaActual.getFullYear() - fechaNac.getFullYear();

    let mesActual = fechaActual.getMonth(), diaActual = fechaActual.getDate(), mesNacimiento = fechaNac.getMonth(), diaNacimiento = fechaNac.getDate();

    if (mesActual < mesNacimiento || (mesActual === mesNacimiento && diaActual < diaNacimiento)) {
        edad--;
    }

    return edad;
}

function limpiarUserAgent(data) {
    let navegador = 'Desconocido',
        version = '',
        sistema = 'Desconocido';

    const sistemaMatch = data.match(/\((.*?)\)/);
    
    if(sistemaMatch && sistemaMatch[1]) {
        sistema = sistemaMatch[1]; // Ej: "X11; Linux x86_64"
    }

    // Navegador y versión
    if (data.includes('Edg/')) {
        navegador = 'Edge';
        version = data.match(/Edg\/([\d\.]+)/)?.[1] || '';
    } else if (data.includes('Chrome/')) {
        navegador = 'Chrome';
        version = data.match(/Chrome\/([\d\.]+)/)?.[1] || '';
    } else if (data.includes('Firefox/')) {
        navegador = 'Firefox';
        version = data.match(/Firefox\/([\d\.]+)/)?.[1] || '';
    } else if (data.includes('Safari/') && !data.includes('Chrome')) {
        navegador = 'Safari';
        version = data.match(/Version\/([\d\.]+)/)?.[1] || '';
    }

    return `Navegador: ${navegador} | Sistema Operativo: ${sistema}`;

}