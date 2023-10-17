$(document).ready(()=>{

    $("#form-update, #form-create").off();
    $("#form-update, #form-create").validate({
        rules: {
            Nro: {
                required: true,
                maxlength: 12,
                noNegative: true,
            },
            Fecha: {
                date:true,
                fechaPosterior: true,
            },
            FechaE: {
                date:true,
                fechaPosterior: true,
            },
            Obs: {
                required: true,
            }
        },
        messages: {
            Nro: {
                required: "Este campo es obligatorio",
                maxlength: "El máximo de caracteres es de 12",
            },
            Fecha: {
                date: "El campo debe ser de fechas",
            },
            FechaE: {
                date: "El campo debe ser de fechas",
            },
            Obs: {
                required: "Este campo es obligatorio",
            }
        }
    });

    $.validator.addMethod("fechaPosterior", function(value, element) {
        var fechaActual = new Date();
        var fechaIngresada = new Date(value);
        return fechaIngresada > fechaActual;
    }, "La fecha debe ser posterior a la fecha de hoy");

    $.validator.addMethod("noNegative", function(value, element) {
        return this.optional(element) || parseFloat(value) >= 0;
      }, "No se permiten números mapas negativos o símbolos");


    $("#form-create").on("submit", function(event) {
        // Verificar si el formulario es válido
        if ($(this).valid()) {
            if($(this).attr("id") == "form-create"){
                swal('Felicitaciones', '¡Se ha generado el mapa de manera correcta.', 'success');
                setTimeout(() => {
                    $(this).unbind("submit").submit();
                }, 4000);   
            }

        } else {
            swal('Atención', 'Por favor, complete todos los campos requeridos correctamente.', 'warning');
        }
        
        event.preventDefault();
     });


});