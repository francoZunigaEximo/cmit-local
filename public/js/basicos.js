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
    link.download = tipo === 'pdf' ? name+".pdf" : name+".xlsx";
    link.style.display = 'none';

    document.body.appendChild(link);
    link.click();
    setTimeout(function() {

        fetch(fullPath, {
            method: 'DELETE'
        }).then(response => {

            if (response.ok) {
                console.log('Archivo eliminado correctamente.');
            } else {
                console.error('Error al intentar eliminar el archivo.');
            }
        }).catch(error => {
            console.error('Error en la solicitud de eliminación:', error);
        });

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
            break;
        
        case "P":
            return "Examen a Cuenta";
            breack;

        case "B":
            return "Contado";
            break;

        default:
            return "-";
            break;
    }
}

function tipoSPagoPrestacion(tipo) {
    switch (tipo) {
        case "A":
            return "Efectivo";
            break;
        case "B":
            return "Débito";
            break;
        case "C":
            return "Crédito";
            break;
        case "D":
            return "Cheque";
            break;
        case "G":
            return "Sin cargo";
            break;
        case "F":
            return "Transferencia";
            break;
        case "E":
            return "Otro";
            break;
        default:
            return "-";
            break;
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

function formatoCeldaDataTable(texto, maxCaracteres) {
    let resultado = "";
    let palabras = texto.split(/\s+/); // Divide el texto en palabras
    let lineaActual = "";

    palabras.forEach(palabra => {
        // Si la longitud de la línea actual más la nueva palabra excede maxCaracteres
        if ((lineaActual.length + palabra.length + 1) > maxCaracteres) {
            // Añadir la línea actual al resultado con un salto de línea
            resultado += (lineaActual.trim() + '<br>');
            // Comenzar una nueva línea con la palabra actual
            lineaActual = palabra + ' ';
        } else {
            // Añadir la palabra a la línea actual
            lineaActual += palabra + ' ';
        }
    });

    // Añadir la última línea (si no está vacía)
    if (lineaActual.trim().length > 0) {
        resultado += lineaActual.trim();
    }

    return resultado;
}

function fechaCompleta(fecha) {
    const date = new Date(fecha.replace(' ', 'T'));

    // Obtén el día, mes y año
    const dia = String(date.getDate()).padStart(2, '0');
    const mes = String(date.getMonth() + 1).padStart(2, '0'); // Los meses van de 0 a 11
    const anio = date.getFullYear();

    const hora = String(date.getHours()).padStart(2, '0');
    const minuto = String(date.getMinutes()).padStart(2, '0');
    const segundos = String(date.getSeconds()).padStart(2, '0');

    const formatoCompleto = `${dia}/${mes}/${anio} ${hora}:${minutos}:${segundos}`;

    return formatoCompleto;
}
