$(function() {

    $("#form-update, #form-create").off();
    $("#form-update, #form-create").validate({
        rules: {
            TipoCliente: {
                required: true
            },
            Identificacion: {
                required: true,
                cuitCuil: true,
                maxlength: 13,
            },
            RazonSocial: {
                required: true,
                maxlength: 50,
                minlength: 4
            },
            ParaEmpresa: {
                required: true,
                maxlength: 50,
                minlength: 4
            },
            NombreFantasia: {
                maxlength: 40,
                minlength: 4
            },
            Telefono: {
                required: true,
                maxlength: 13,
                minlength: 10,
                formatoEspecial: true,
            },
            EMail: {
                email: true,
            },
            Direccion: {
                maxlength: 100,
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
            },
            prefijoExtra: {
                maxlength: 3,
            },
            numeroExtra: {
                maxlength: 7,
            },
            obsExtra: {
                maxlength: 50,
            },
            Descuento: {
                percentage: true
            }
        },
        messages: {
            TipoCliente: {
                required: "El tipo de cliente es obligatorio."
            },
            Identificacion: {
                required: "El cuit es obligatorio",
                cuitCuil: "La identificacion es obligatoria con el formato correspondiente.",
                maxlength: "Este campo admite hasta 13 caracteres.",
            },
            RazonSocial: {
                required: "La razón social es obligatoria.",
                maxlength: "Este campo admite hasta 50 caracteres.",
                minlength: "El campo admite un mínimo de 4 caracteres."
            },
            ParaEmpresa: {
                required: "El para empresa es obligatorio.",
                maxlength: "Este campo admite hasta 50 caracteres.",
                minlength: "El campo admite un mínimo de 4 caracteres."
            },
            NombreFantasia: {
                maxlength: "El campo admite un mínimo de 40 caracteres.",
                minlength: "El campo admite un mínimo de 4 caracteres."
            },
            Telefono: {
                required: "El teléfono del cliente es obligatorio",
                maxlength: "El campo admite un máximo de 13 digitos.",
                minlength: "El campo admite un mínimo de 10 digitos.",
                formatoEspecial: "Debe completar el teléfono en el formato que corresponde y todo numerico.",
            },
            EMail: {
                email: "El formato del correo electrónico es incorrecto."
            },
            Direccion: {
                maxlength: "El campo admite un mínimo de 100 caracteres.",
            },
            Provincia: {
                required: "La provincia es un dato obligatorio"
            },
            IdLocalidad: {
                required: "La localidad es un dato obligatorio"
            },
            CP: {
                maxlength: "El máximo de caracteres es de 8",
                minlength: "El campo debe tener un mínimo 4 caracteres"
            },
            prefijoExtra: {
                maxlength: "El prefijo debe tener solo 3 digitos."
            },
            numeroExtra: {
                maxlength: "El número debe tener solo 7 digitos."
            },
            obsExtra: {
                maxlength: "La Observacion debe tener un máximo de 50 caracteres.",
            },
            Descuento: {
                percentage: "El descuento debe estar entre 0 y 100 y ser un número entero."
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

    $.validator.addMethod("percentage", function(value, element) {
        return this.optional(element) || (value >= 0 && value <= 100 && Number.isInteger(parseFloat(value)));
    }, "Por favor, ingrese un valor entre 0 y 100.");
     

    $("#form-update, #form-create").on("submit", function(event) {
        // Verificar si el formulario es válido
        if ($(this).valid()) {
            if($(this).attr("id") == "form-create"){
                
                toastr.success('¡Se ha creado el paciente de manera correcta. Se habilitarán la Opciones, Emails, Autorizados, Observaciones y Para Empresa!', 'Felicitaciones', {timeOut: 1000});
                setTimeout(()=> {
                    $(this).unbind("submit").submit();
                }, 3000);
                

                
            }else{
                toastr.success('¡Se han actualizado los datos de manera correcta. Se actualizará el navegador!', 'Perfecto', {timeOut: 1000});
                setTimeout(() => {
                    $(this).unbind("submit").submit();
                }, 3000);
                
                
            }  

        } else {
            toastr.error('Por favor, complete todos los campos requeridos correctamente.', 'Atención', {timeOut: 1000});
        }
        
        event.preventDefault();
     });
});
