$(document).ready(function(){

    $('#identificacion').on('input', function() {
        let input = $(this),
            cleanedValue = input.val().replace(/\D/g, ''),
            formattedValue = cleanedValue.replace(/^(\d{2})(\d{8})(\d{1})$/, '$1-$2-$3');
            
        input.val(formattedValue);
    });
    

    //Calculo de Edad
    let fechaNacimiento = $('#fecha');

    fechaNacimiento.change(function() {
      let fechaNacimientoValor = new Date($(this).val()),
          fechaActual = new Date(),
          añoActual = fechaActual.getFullYear(),
          edad = añoActual - fechaNacimientoValor.getFullYear();

      $('#edad').val(edad);
    });

});