$(function() {

    $(document).on('click', '#excel', function(e) {
        e.preventDefault();

        let ids = [];
        $('input[name="Id"]:checked').each(function() {
            ids.push($(this).val());
        });

        if (!ids) {
            toastr.warning("Debe seleccionar alguna especialidad para generar el reporte", "", {timeOut: 1000});
            return;
        }

        swal({
            title: "¿Estás seguro de que deseas generar el reporte de Excel con todos los items seleccionados?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                preloader('on');
                $.ajax({
                    url: especialidadExcel,
                    type: "GET",
                    data: {
                        Id: ids
                    },
                    success: function(response) {
                        preloader('off');
                        createFile("xlsx", response.filePath, generarCodigoAleatorio() + '_reporte');
                        toastr.success(response.msg, '', {timeOut: 1000});
                    
                    },                    
                    error: function(jqXHR) {
                        preloader('off');            
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return; 
                    }
                });

            }
        });       
    });

    $(document).on('click', '#baja, #multiple', function(e) {
        e.preventDefault();

        let ids = [],
            tipo = $(this).is('#baja') ? 'baja' : 'multiple';


        if(tipo === 'multiple') {

            $('input[name="Id"]:checked').each(function() {
                ids.push($(this).val());
            });
        
        }else{
            ids.push($(this).data('id'));
        }

        if (!ids) {
            toastr.warning("Debe seleccionar al menos una especialidad para la baja múltiple", "", {timeOut: 1000});
            return; 
        }

        let ids_cleans = ids.filter(item => !isNaN(item) && isFinite(item));

        swal({
            title: "¿Estás seguro de que deseas realizar la baja de o las especialidades seleccionadas?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar => {
            if(confirmar) {
                preloader('on')
                $.ajax({
                    url: multiDownEspecialidad,
                    type: "POST",
                    data: {
                        _token: TOKEN,
                        ids: ids_cleans
                    },
                    success: function(response) {
                        preloader('off');
                        toastr.success(response.msg, '', {timeOut: 1000});
                        $('#listaEspecialidades').DataTable().draw(false);
                    },
                    error: function(jqXHR) {
                        preloader('off');            
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return; 
                    }
                });
            
            }
        }));  
    });


});