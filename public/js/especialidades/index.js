$(document).ready(()=> {

    $('#excel').click(function(e) {
        e.preventDefault();

        let ids = [];
        $('input[name="Id"]:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length == 0) {
            toastr.warning("Debe seleccionar alguna especialidad para generar el reporte");
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
                        createFile("excel", response.filePath, generarCodigoAleatorio() + '_reporte');
                        toastr.success(response.msg);
                    
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

    $('#btnBajaMultiple').click(function(e) {
        e.preventDefault();

        let ids = [];
        $('input[name="Id"]:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            toastr.warning("Debe seleccionar al menos una especialidad para la baja múltiple");
            return; 
        }

        swal({
            title: "¿Estás seguro de que deseas realizar la baja múltiple de las especialidades seleccionadas?",
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
                        ids: ids
                    },
                    success: function(response) {
                        preloader('off');
                        toastr.success(response.msg);
                        $('#listaEspecialidades').DataTable().draw(false);
                    },
                    error: function(xhr) {
                        preloader('off');            
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return; 
                    }
                });
            
            }
        }));  
    });

    $(document).on('click', '.blockEsp', function(){

        let especialidad = $(this).data('id');
        
        if(especialidad === '') return;


        swal({
            title: "¿Está seguro que desea dar de baja la especialidad?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar){
                preloader('on');
                $.post(bajaEspecialidad, {_token: TOKEN, Id: especialidad})
                .done(function(response){
                    preloader('off');
                    toastr.success(response.msg);
                    setTimeout(()=>{
                        $('#listaEspecialidades').DataTable().draw(false);
                    },3000);
                })
                .fail(function(jqXHR){
                    preloader('off');            
                    let errorData = JSON.parse(jqXHR.responseText);            
                    checkError(jqXHR.status, errorData.msg);
                    return; 
                });
            }
        });

            
    });

});