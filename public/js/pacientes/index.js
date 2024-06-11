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
            toastr.warning("Debe seleccionar al menos un paciente para la baja múltiple", "Atención");
            return; 
        }

        swal({
            title: "¿Estás seguro de que deseas realizar la baja múltiple de los pacientes seleccionados?",
            icon: "warning",
            buttons: ["No", "Sí"],
        }).then((confirmar) => {
            if(confirmar) {
                preloader('on');
                $.post(down, {_token: TOKEN, ids: ids})
                .done(function(response) {
                    preloader('off');
                    let tipo = {
                        200: 'success',
                        409: 'warning'
                    };

                    response.forEach(function(data) {
                        toastr[tipo[data.status]](data.msg);
                    });
                    
                    setTimeout(() => {
                        $('#listaPac').DataTable();
                        $('#listaPac').DataTable().draw(false); 
                    }, 3000);
                })
                .fail(function(jqXHR) {
                    preloader('off');
                    checkError(jqXHR.status, errorData.msg);
                    return;
                });
            } 
        });
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
                preloader('on');
                $.post(down, {_token: TOKEN, ids: paciente})
                .done(function(response){
                    preloader('off');
                    let tipo = {
                        200: 'success',
                        409: 'warning'
                    };

                    response.forEach(function(data) {
                        toastr[tipo[data.status]](data.msg);
                    });

                    $('#listaPac').DataTable();
                    $('#listaPac').DataTable().draw(false);
                })
                .fail(function(jqXHR){
                    preloader('off');
                    let errorData = JSON.parse(jqXHR.responseText);            
                    checkError(jqXHR.status, errorData.msg);
                    return;
                    
                })
            } 
        });
  
    });

});