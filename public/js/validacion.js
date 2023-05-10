$(document).ready(function() {
    $('#form-change').submit(function(e) {
      e.preventDefault(); 
      
      var passactual = $('#passactual').val();
      var newpass = $('#newpass').val();
      var renewpass = $('#renewpass').val();
      
      // Validación del campo Contraseña Actual
      if (passactual.length == 0) {
        alert('Debe ingresar su contraseña actual.');
        return;
      }
      
      // Validación del campo Nueva Contraseña
      if (newpass.length == 0) {
        alert('Debe ingresar su nueva contraseña.');
        return;
      }
      
      // Validación del campo Repetir Nueva Contraseña
      if (renewpass.length == 0) {
        alert('Debe repetir su nueva contraseña.');
        return;
      }
      
      if (newpass != renewpass) {
        alert('La nueva contraseña y su repetición deben ser iguales.');
        return;
      }
      
      // Si todos los campos son válidos, se puede enviar el formulario
      $(this).unbind('submit').submit();
    });
  });