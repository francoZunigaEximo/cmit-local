$(document).ready(function() {

    $("#form-create, #form-update").off();
    $("#form-create, #form-update").validate({

        rules: {
            Nombre: {
                required: true,
                maxlength: 60,
            },
            Asunto: {
                required: true,
                maxlength: 60,
                minlength: 10,
            }
        },
        messages: {
            Nombre: {
                required: "El nombre del mensaje es obligatorio",
                maxlength: "Solo se admiten 60 caracteres",
            },
            Asunto: {
                required: "El asunto es es obligatorio",
                maxlength: "Máximo 100 caracteres",
                minlength: "Mínimo 10 caracteres"
            }
        }
    });

   
});