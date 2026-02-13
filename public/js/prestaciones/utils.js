$(function(){

    $('th.sort').click(function() {
        let table = $(this).closest('table');
        let columnIndex = $(this).index();
        let rows = table.find('tbody tr').toArray().sort(comparer(columnIndex));
        this.asc = !this.asc;
        if (!this.asc) {
            rows.reverse();
        }
        for (let i = 0; i < rows.length; i++) {
            table.append(rows[i]);
        }
    });
    
    function comparer(index) {
        return function(a, b) {
            let valA = getCellValue(a, index).toUpperCase();
            let valB = getCellValue(b, index).toUpperCase();
    
            // Si una de las celdas es vacía, mover al final
            if (valA === '') {
                return 1;
            } else if (valB === '') {
                return -1;
            }
    
            // Comparar los valores de las celdas
            if (valA < valB) {
                return -1;
            } else if (valA > valB) {
                return 1;
            }
            return 0;
        };
    }
    
    function getCellValue(row, index) {
        return $(row).find('td').eq(index).text().trim();
    }
    
    //Unificador
    function visualizarOpcionesFecha(elemento, valor) {
        visualizarOpciones(elemento, valor);
    }
    
    //Visualizar opciones dentro del filtro avanzado
    function visualizarOpciones(objeto, condicion) {
        if (condicion) {
            objeto.show();
        } else {
            objeto.hide();
        }
    }
    
    
    
    function mostrarPreloader(arg) {
        $(arg).css({
            opacity: '0.3',
            visibility: 'visible'
        });
    }
    
    function ocultarPreloader(arg) {
        $(arg).css({
            opacity: '0',
            visibility: 'hidden'
        });
    }
      
    //Mostrar campos ocultos
    function showCampo(id, lista, mostrar){
        let result = lista.includes(id);
    
        return (result) ? $(mostrar).show() : $(mostrar).hide();
    }
      
      
    $('#btnVolver').click(function() {
        history.back();
    }); 
    
    
    //Verificamos datos
    function verificacion(condicion, opciones = {}) {
        const { mensaje = 'Error', onConfirm = null } = opciones;
        if (condicion) {
          if (onConfirm) {
            if (confirm(mensaje)) {
              onConfirm();
            }
          } else {
            alert(mensaje);
            return;
          }
        }
      }
    
      
      $(function() {
        $('[data-toggle="tooltip"]').tooltip();
      });
    
      //Resetear busqueda
      $('#buscarReset').click(function(){
        mostrarPreloader('#preloader');
        location.reload();
    });

});

