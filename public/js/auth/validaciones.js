$(document).ready(function() {

    $("#form-update, #form-create").off();
    $("#form-update").validate({
        rules: {
            nombre: {
                required: true,
                maxlength: 30,
            },
            apellido: {
                required: true,
                maxlength: 30
            },
            tipoDoc: {
                required: true,
            },
            numeroDoc: {
                required: true,
                digits: true,
                maxlength: 8,
                minlength: 7
            },
            cuil: {
                required: true
            },
            numeroCUIL: {
                required: true,
                cuitCuil: true,
                maxlength: 13,
            },
            direccion: {
                maxlength: 100,
            },
            numTelefono: {
                digits: true,
                maxlength: 11,
            }
        },
        messages: {
            nombre: {
                required: "El nombre es obligatorio."
            },
            apellido: {
                required: "El apellido es obligatorio."
            },
            tipoDoc: {
                required: "Debe seleccionar el tipo de documento"
            },
            cuil: {
                required: "Es obligatorio"
            },
            numeroDoc: {
                required: "El numero de doc es obligatorio.",
                digits: "Solamente se admiten numeros",
                maxlength: "Hasta 8 digitos",
                minlength: "Mínimo  digitos"
            },
            numeroCUIL: {
                required: "Es obligatorio",
                cuitCuil: "Debe ajustarse al formato solicitado. Ej: 20-00000000-3",
                maxlength: "Hasta 13 digitos",
            },
            Direccion: {
                maxlength: "El campo admite un máximo de 100 caracteres.",
            },
            numTelefono: {
                digits: "Solo admite numeros",
                maxlength: "Máximo de 11 digitos"
            }

        }

    });

  

    //Validación especial para cuit/cuil de JQUERY
    $.validator.addMethod("cuitCuil", function(value, element) {
        return this.optional(element) || /^([0-9]{2}-[0-9]{8}-[0-9])$/.test(value);
    }, "Por favor, ingresa el formato xx-xxxxxxxx-x.");


});
