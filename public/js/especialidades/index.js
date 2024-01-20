$(document).ready(()=> {

    toastr.options = {
        closeButton: true,   
        progressBar: true,    
        timeOut: 3000,        
    };

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
                    type: "GET",
                    data: {
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
                        toastr.success('Se ha generado el reporte excel de manera correcta', 'Perfecto');
                    
                    },                    
                    error: function(xhr) {
                        console.error(xhr);
                        toastr.warning("Verifique si ha seleccionado alguna especialidad", "Atención");
                    }
                });
            }
        } else {
            toastr.warning("Verifique si ha seleccionado alguna especialidad. Caso contrario, consulte con el administrador", "Atención");
        }

    });

    $('#btnBajaMultiple').click(function(e) {
        e.preventDefault();

        let ids = [];
        $('input[name="Id"]:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            toastr.warning("Debe seleccionar al menos una especialidad para la baja múltiple", "Atención");
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
                    toastr.success("¡Se ha dado de baja a las especialidades correctamente!", "Éxito");
                    $('#listaEspecialidades').DataTable();
                    $('#listaEspecialidades').DataTable().draw(false);
                },
                error: function(xhr) {
                    toastr.error("¡Ha ocurrido un inconveniente. Consulte con el administrador!", "Error");
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
                toastr.success("Se ha dado de baja la especialidad de manera correcta", "Perfecto");
                setTimeout(()=>{
                    $('#listaEspecialidades').DataTable();
                $('#listaEspecialidades').DataTable().draw(false);
                },3000);
            })
            .fail(function(xhr){
                toastr.error("Ha ocurrido un error. Consulte con el administrador", "Error");
                console.error(xhr);
            });
        }
       
    });

});