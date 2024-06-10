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

function saltoLinea(cadena) {
    let textoConSaltos = '';
    for (var i = 0; i < cadena.length; i++) {
        textoConSaltos += cadena[i];
        if ((i + 1) % 130 === 0) {
            textoConSaltos += '<br>';
        }
    }
    return textoConSaltos;
}

function ajustarFecha(fecha) {
    let fechaArray = fecha.split(' '), cortar = fechaArray[0].split('-'), nuevaFecha = `${cortar[2]}/${cortar[1]}/${cortar[0]}`;
    return `${nuevaFecha} ${fechaArray[1]} `;
}

