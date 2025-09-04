$(function() {

    $("#form-update, #form-create").off();
    $("#form-update, #form-create").validate({

        rules: {
            Examen: {
                required: true,
                maxlength: 50,
            },
            Estudio: {
                required: true,
                notZero: true
            },
            CodigoEx: {
                maxlength: 10
            },
            ProvEfector: {
                required: true,
                notZero: true
            },
            ProvInformador: {
                required: true,
                notZero: true
            }
        },
        messages: {
            Examen: {
                required: "El nombre del exámen es obligatorio",
                maxlength: "Solo se admiten 50 caracteres",
                notZero: "El nombre del exámen es obligatorio"
            },
            Estudio: {
                required: "El estudio es obligatorio",
                notZero: "El nombre del estudio es obligatorio"
            },
            CodigoEx: {
                maxlength: "Solo puede tener hasta 10 caracteres"
            },
            ProvEfector: {
                required: "La especialidad del efector es obligatorio",
                notZero: "El nombre de la especialidad es obligatoria"
            },
            ProvInformador: {
                required: "La especialidad del informador es obligatorio",
                notZero: "El nombre de la especialidad es obligatoria"
            }
        }
    });

    $.validator.addMethod("notZero", function(value, element) {
        return value !== "0";
    }, "Por favor, selecciona un valor distinto de 0");

    $("#form-create").on("submit", function(e) {
        // Verificar si el formulario es válido
        if ($(this).valid()) {
            if($(this).attr("id") == "form-create"){
                toastr.success('¡Se ha generado el exámen de manera correcta.', 'Felicitaciones', {timeOut: 1000});
                setTimeout(() => {
                    $(this).unbind("submit").submit();
                }, 3000);   
            }

        } else {
            toastr.warning('Por favor, complete todos los campos requeridos correctamente.', 'Atención', {timeOut: 1000});
        }
        
        e.preventDefault();
     });
});