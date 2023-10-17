$(document).ready(function(){

    //Validaciones
     $("#form-update, #form-create").off();
    $("#form-update, #form-create").validate({
        rules: {
            Nombre: {
                required: true,
                maxlength: 30,
                minlength: 4,
            },
            Apellido: {
                required: true,
                maxlength: 30,
                minlength: 4,
            },
            TipoDocumento: {
                required: true
            },
            Documento: {
                required: true,
                digits: true,
                maxlength: 8,
                minlength: 7
            },
            Identificacion: {
                cuitCuil: true,
                maxlength: 13,
            },
            FechaNacimiento: {
                required: true,
                date: true, //Que sea fecha 
                dateISO: true, //que cumpla el ISO 8601
                max: function () {
                    return new Date().toISOString().split("T")[0];
                },
                min: "1923-01-01"
            },
            Direccion: {
                maxlength: 100,
            },
            NumeroTelefono: {
                required: true,
                maxlength: 13,
                minlength: 13,
                formatoEspecial: true,
            },
            EMail: {
                formatoEmail: true,
            },
            Provincia: {
                required: true
            },
            IdLocalidad: {
                required: true
            },
            CP: {
                maxlength: 8,
                minlength: 4
            }
        },
        messages: {
            Nombre: {
                required: "El nombre es obligatorio.",
                maxlength: "El nombre no debe exceder los 50 caracteres.",
                minlength: "El nombre debe tener un mínimo de 4 caracteres.",
                nombreRequerido: true
            },
            Apellido: {
                required: "El apellido es obligatorio.",
                maxlength: "El apellido no debe exceder los 50 caracteres.",
                minlength: "El apellido debe tener un mínimo de 4 caracteres.",
            },
            TipoDocumento: {
                required: "Debe seleccionar un tipo de documento."
            },
            Documento: {
                required: "El documento es obligatorio",
                digits: "El documento solo debe contener numeros.",
                maxlength: "El documento debe tener un máximo de 8 digitos.",
                minlength: "El documento debe contener un mínimo de 7 digitos."
            },
            Identificacion: {
                cuitCuil: "La Cuit/Cuil debe tener el siguiente formato xx-xxxxxxxx-x",
                maxlength: "El Cuit/Cuil debe tener 13 caracteres, incluida con los guiones correspondientes"
            },
            FechaNacimiento: {
                required: "La fecha de nacimiento es un dato obligatorio",
                date: "La fecha solo puede ser un campo de fecha",
                dateISO: "Utilice el formato especificado en el campo",
                max: "No puede ser superior a la fecha actual",
                min: "La fecha mínima es 01/01/1923"
            },
            Direccion: {
                maxlength: "Puede contener un máximo de 100 caracteres",
            },
            NumeroTelefono: {
                required: "El telefono es un dato obligatorio.",
                maxlength: "El telefono debe tener 10 digitos",
                minlength: "El telefono debe tener 10 digitos",
                formatoEspecial: "Debe completar el telefono en el formato que corresponde y todo numerico",
            },
            EMail: {
                formatoEmail: "El campo acepta los siguientes formatos com/com.ar/ar de cualquier país"
            },
            Provincia: {
                required: "La provincia es un dato obligatorio"
            },
            IdLocalidad: {
                required: "La localidad es un dato obligatorio"
            },
            CP: {
                maxlength: "El maximo de caracteres es de 8",
                minlength: "El campo debe tener un mínimo 4 caracteres"
            }


        }
    });

    //Validación especial para cuit/cuil de JQUERY
    $.validator.addMethod("cuitCuil", function(value, element) {
        return this.optional(element) || /^([0-9]{2}-[0-9]{8}-[0-9])$/.test(value);
    }, "Por favor, ingresa el formato xx-xxxxxxxx-x.");

    //Evitamos los caracteres especiales
    $.validator.addMethod("formatoEspecial", function(value, element) {
        return this.optional(element) || /^\(\d{3}\)\d{3}-\d{4}$/.test(value);
    }, "El formato es (xxx)xxx-xxxx. Los parentesis y guiones se completan automaticamente.");

    $.validator.addMethod("formatoEmail", function(value, element) {
        return this.optional(element) || /^\w+([\.-]?\w+)*@(?:\w+\.)+[a-z]{2,3}$/i.test(value);
    }, "Por favor, ingresa una dirección de correo electrónico válida.");


    $("#form-update, #form-create").on("submit", function(event) {
        if ($(this).valid()) {
            if($(this).attr("id") == "form-create"){
                
                swal('Felicitaciones','¡Se ha creado el paciente de manera correcta. Se habilitara la Ficha Medica, Prestaciones y Examenes!', 'success');
                $(this).unbind("submit").submit();
            }else{
                swal('Cambios realizados','¡Se ha actualizado el paciente de manera correcta.!', 'success');
                $(this).unbind("submit").submit();
            }
        } else {
            swal('Alerta','Por favor, complete todos los campos requeridos correctamente.', 'info');
        }
        
        event.preventDefault();
    });
});