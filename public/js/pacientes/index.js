$(document).ready(function(){

    $(document).on('keydown', function(event) {
        if (event.keyCode === 27) {

            window.location.href = GOINDEX;
        }
    });

    

    $('#btnBajaMultiple').click(function(e) {
        e.preventDefault();

        let ids = [];
        $('input[name="Id"]:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            swal("Atención", "Debe seleccionar al menos un paciente para la baja múltiple", "info");
            return; 
        }

        if (confirm("¿Estás seguro de que deseas realizar la baja múltiple de los pacientes seleccionados?")) {
            $.ajax({
                url: multipleDown,
                type: "POST",
                data: {
                    _token: TOKEN,
                    ids: ids
                },
                success: function() {
                    swal("Éxito", "¡Se ha dado de baja a los pacientes correctamente!", "success");
                    location.reload();
                },
                error: function(xhr) {
                    swal("Error", "¡Ha ocurrido un inconveniente. Consulte con el administrador!", "error");
                    console.error(xhr);
                }
            });
        }
    });


    $('#excel').click(function(e) {
        e.preventDefault();

        let ids = [];
        $('input[name="Id"]:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length > 0) {
            if (confirm("¿Estás seguro de que deseas generar el reporte de Excel con todos los items seleccionados?")) {
                $.ajax({
                    url: exportExcel,
                    type: "POST",
                    data: {
                        _token: TOKEN,
                        Id: ids
                    },
                    success: function(response) {
                        let filePath = response.filePath,
                            pattern = /storage(.*)/,
                            match = filePath.match(pattern),
                            path = match ? match[1] : '';

                        let url = new URL(location.href), baseUrl = url.origin;

                        let fullPath = baseUrl + '/cmit/storage' + path, link = document.createElement('a');
                        
                        link.href = fullPath;
                        
                        let currentDate = new Date();
                        link.download = "pacientes.xlsx";
                        link.style.display = 'none';

                        document.body.appendChild(link);
                        link.click();
                        setTimeout(function() {
                            document.body.removeChild(link);
                        }, 100);
                    
                    },                    
                    error: function(xhr) {
                        console.error(xhr);
                    }
                });
            }
        } else {
            swal('Error', 'Debes seleccionar al menos un paciente para exportar.', 'error');
        }

    });

    $(document).on('click', '.downPaciente', function(){

        let paciente = $(this).data('id');
        
        if(confirm("¿Está seguro que desea dar de baja a este paciente?")){

            $.post(down, {_token: TOKEN, Id: paciente})

                .done(function(){
                    swal('Perfecto', 'Se ha realizado de manera correcta la baja', 'success');
                    $('#listaPac').DataTable();
                    $('#listaPac').DataTable().draw(false);
                })
                .fail(function(xhr){
                    swal('Error', 'Ha ocurrido un error, consulte con el administrador', 'error');
                    console.error(xhr);
                })
        }
    });
    
});