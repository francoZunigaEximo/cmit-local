$(function() {
    $('[data-toggle="tooltip"]').tooltip();
});

$(document).ready(function() {

    let sortDirection = 1; // Inicialmente, ordenar en dirección ascendente

    $('th.sort').click(function() {
        let table = $(this).closest('table'),
            columnIndex = $(this).index(),
            rows = table.find('tbody tr').toArray();
    
        // Invertir la dirección de la ordenación
        sortDirection = -sortDirection;
    
        // Ordenar las filas en función del comparador
        rows.sort(comparer(columnIndex, sortDirection));
    
        // Eliminar las filas existentes de la tabla
        table.find('tbody tr').remove();
    
        // Agregar las filas ordenadas nuevamente a la tabla
        for (let i = 0; i < rows.length; i++) {
            table.find('tbody').append(rows[i]);
        }
    });
    
    function comparer(index, direction) {
        return function(a, b) {
            let valA = getCellValue(a, index).toUpperCase(),
                valB = getCellValue(b, index).toUpperCase();
    
            if (valA === '') {
                return 1;
            } else if (valB === '') {
                return -1;
            }
    
            if (valA < valB) {
                return -direction;
            } else if (valA > valB) {
                return direction;
            }
            return 0;
        };
    }
    
    function getCellValue(row, index) {
        return $(row).find('td').eq(index).text().trim();
    }
    
  

    //Calculo de Edad
    let fechaNacimiento = $('#fecha');

    fechaNacimiento.change(function() {
      let fechaNacimientoValor = new Date($(this).val()),
          fechaActual = new Date();
    
          let diff = fechaActual.getFullYear() - fechaNacimientoValor.getFullYear();

          if (fechaActual.getMonth() < fechaNacimientoValor.getMonth() || (fechaActual.getMonth() === fechaNacimientoValor.getMonth() && fechaActual.getDate() < fechaNacimientoValor.getDate())) {
              diff--;
          }

      $('#edad').val("Edad: " + diff);
    });

    let ingreso = $('#FechaIngreso').val();
        let egreso = $('#FechaEgreso').val();

        let dateIngreso = new Date(ingreso);
        let dateEgreso = egreso ? new Date(egreso) : new Date();

        let diff = dateEgreso.getFullYear() - dateIngreso.getFullYear();

        if (dateEgreso.getMonth() < dateIngreso.getMonth() || (dateEgreso.getMonth() === dateIngreso.getMonth() && dateEgreso.getDate() < dateIngreso.getDate())) {
            diff--;
        }

        $('#AntiguedadEmpresa').val(diff);


    $('#identificacion').on('input', function() {
        let input = $(this),
            cleanedValue = input.val().replace(/\D/g, ''),
            formattedValue = cleanedValue.replace(/^(\d{2})(\d{8})(\d{1})$/, '$1-$2-$3');
            
        input.val(formattedValue);
    });


    $('#checkAll').on('click', function() {

        $('input[type="checkbox"][name="Id"]:not(#checkAll)').prop('checked', this.checked);
    });

    $('#btnVolver').click(function() {
        history.back();
    }); 

});



