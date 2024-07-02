$(document).ready(()=>{

    toastr.options = {
        closeButton: true,   
        progressBar: true,    
        timeOut: 3000,        
    };

    $("#form-update, #form-create").off();
    $("#form-update, #form-create").validate({
        rules: {
            Nro: {
                required: true,
                maxlength: 8,
                noNegative: true,
            },
            IdART: {
                required: true,
            },
            IdEmpresa: {
                required: true,
            },
            Fecha: {
                date:true,
                fechaPosterior: true,
            },
            FechaE: {
                date:true,
                fechaPosterior: true,
            },
            Cpacientes: {
                required: true,   
            }
        },
        messages: {
            Nro: {
                required: "Este campo es obligatorio",
                maxlength: "El máximo de caracteres es de 8",
            },
            IdART: {
                required: "El ART es obligatorio",
            },
            IdEmpresa: {
                required: "La Empresa es obligatoria",
            },
            Fecha: {
                date: "El campo debe ser de fechas",
            },
            FechaE: {
                date: "El campo debe ser de fechas",
            },
            Cpacientes: {
                required: "Debe especificar la cantidad de pacientes",
            }
        }
    });

    $.validator.addMethod("fechaPosterior", function(value, element) {
        var fechaActual = new Date();
        var fechaIngresada = new Date(value);
        return fechaIngresada > fechaActual;
    }, "La fecha debe ser posterior a la fecha de hoy");

    $.validator.addMethod("noNegative", function(value, element) {
        return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
    }, "Solo se permiten letras o números sin caracteres especiales");


    $("#form-create").on("submit", function(event) {
        // Verificar si el formulario es válido
        if ($(this).valid()) {
            if($(this).attr("id") == "form-create"){
                toastr.success('¡Se ha generado el mapa de manera correcta.', 'Felicitaciones');
                setTimeout(() => {
                    $(this).unbind("submit").submit();
                }, 3000);   
            }

        } else {
            toastr.warning('Por favor, complete todos los campos requeridos correctamente.', 'Atención');
        }
        
        event.preventDefault();
     });


});