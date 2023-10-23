$(document).ready(()=> {

    $('#excel').click(function(e) {
        e.preventDefault();

        let ids = [];
        $('input[name="Id"]:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length > 0) {
            if (confirm("¿Estás seguro de que deseas generar el reporte de Excel con todos los items seleccionados?")) {
                $.ajax({
                    url: especialidadExcel,
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
                        link.download = "especialidades.xlsx";
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

    $('#btnBajaMultiple').click(function(e) {
        e.preventDefault();

        let ids = [];
        $('input[name="Id"]:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            swal("Atención", "Debe seleccionar al menos una especialidad para la baja múltiple", "info");
            return; 
        }

        if (confirm("¿Estás seguro de que deseas realizar la baja múltiple de las especialidades seleccionadas?")) {
            $.ajax({
                url: multiDownEspecialidad,
                type: "POST",
                data: {
                    _token: TOKEN,
                    ids: ids
                },
                success: function() {
                    swal("Éxito", "¡Se ha dado de baja a las especialidades correctamente!", "success");
                    $('#listaEspecialidades').DataTable();
                    $('#listaEspecialidades').DataTable().draw(false);
                },
                error: function(xhr) {
                    swal("Error", "¡Ha ocurrido un inconveniente. Consulte con el administrador!", "error");
                    console.error(xhr);
                }
            });
        }
    });

    $(document).on('click', '.blockEsp', function(){

        let especialidad = $(this).data('id');
        
        if(especialidad === '') return;

        if(confirm("¿Está seguro que desea dar de baja la especialidad?")){

            $.post(bajaEspecialidad, {_token: TOKEN, Id: especialidad})
            .done(function(){
                swal('Perfecto', 'Se ha dado de baja la especialidad de manera correcta', 'success');
                $('#listaEspecialidades').DataTable();
                $('#listaEspecialidades').DataTable().draw(false);

            })
            .fail(function(xhr){
                swal('Error', 'Ha ocurrido un error. Consulte con el administrador', 'error');
                console.error(xhr);
            });
        }
       
    });

});