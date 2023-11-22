$(document).ready(()=>{

    toastr.options = {
        closeButton: true,   
        progressBar: true,    
        timeOut: 3000,        
    };

    $("#form-update,e, #form-create").validate({
        
        rules: {
            Documento: {
                required: true,
                digits: true,
                maxlength: 8,
                minlength: 7
            },
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
            Direccion: {
                maxlength: 100,
            },
            Telefono: {
                maxlength: 10,
                minlength: 10,
                //formatoEspecial: true,
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
            },
            estado: {
                required: true,
            }
        },
        messages: {
            Documento: {
                required: "El documento es obligatorio",
                digits: "El documento solo debe contener numeros.",
                maxlength: "El documento debe tener un máximo de 8 digitos.",
                minlength: "El documento debe contener un mínimo de 7 digitos."
            },
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
            Direccion: {
                maxlength: "Puede contener un máximo de 100 caracteres",
            },
            Telefono: {
                maxlength: "El telefono debe tener 10 digitos",
                minlength: "El telefono debe tener 10 digitos",
                //formatoEspecial: "Debe completar el telefono en el formato que corresponde y todo numerico",
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
            },
            estado: {
                required: "Debe seleccionar un estado para el profesional"
            }
        }
    
    });

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
                toastr.success("¡Se ha creado el profesional de manera correcta. Se habilitará las Opciones y el Seguro!", "Felicitaciones");
                setTimeout(() => {
                    $(this).unbind("submit").submit();
                }, 5000);
            }else{
                toastr.success("¡Se ha actualizado el profesional de manera correcta.!", "Cambios realizados");
                setTimeout(() => {
                    $(this).unbind("submit").submit();
                }, 5000);
            }
        } else {
            toastr.warning("Por favor, complete todos los campos requeridos correctamente.", "Alerta");
        }
        
        event.preventDefault();
    });

});