$(document).ready(function(){
    
    toastr.options = {
        closeButton: true,   
        progressBar: true,    
        timeOut: 3000,        
    };

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
            toastr.warning("Debe seleccionar al menos un paciente para la baja múltiple", "Atención");
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

                    toastr.success('¡Se ha dado de baja a los pacientes correctamente!', 'Éxito');
                    setTimeout(() => {
                        $('#listaPac').DataTable();
                        $('#listaPac').DataTable().draw(false); 
                    }, 3000);
                },
                error: function(xhr) {
                    toastr.error("¡Ha ocurrido un inconveniente. Consulte con el administrador!", "Error");
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
            toastr.error('Debes seleccionar al menos un paciente para exportar.', 'Error');
        }

    });

    $(document).on('click', '.downPaciente', function(){

        let paciente = $(this).data('id'), fullName = $(this).data('nombrecompleto')
        
        swal({
            title: `¿Está seguro que desea dar de baja a este paciente ${fullName}?`,
            icon: "warning",
            buttons: ["Cancelar", "Dar de baja"],
        }).then((confirmar) => {
            if(confirmar) {
                
                $.post(down, {_token: TOKEN, Id: paciente})
                .done(function(){
                   
                    toastr.success('Se ha realizado de manera correcta la baja', 'Perfecto');

                    $('#listaPac').DataTable();
                    $('#listaPac').DataTable().draw(false);
                })
                .fail(function(xhr){
                    toastr.error('Ha ocurrido un error, consulte con el administrador', 'Error');
                    console.error(xhr);
                })
            } 
        });
  
    });

});