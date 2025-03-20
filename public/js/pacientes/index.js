$(document).ready(function(){

    $('#btnBajaMultiple').click(function(e) {
        e.preventDefault();

        let ids = [];
        $('input[name="Id"]:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            toastr.warning("Debe seleccionar al menos un paciente para la baja múltiple",'',{timeOut: 1000});
            return; 
        }

        swal({
            title: "¿Estás seguro de que deseas realizar la baja múltiple de los pacientes seleccionados?",
            icon: "warning",
            buttons: ["No", "Sí"],
        }).then((confirmar) => {
            if(confirmar) {

                $('#listaPac tbody').hide();
                $(".dataTables_processing").show();

                $.post(down, {_token: TOKEN, ids: ids})
                .done(function(response) {

                    let tipo = {
                        200: 'success',
                        409: 'warning'
                    };

                    response.forEach(function(data) {
                        toastr[tipo[data.status]](data.msg);
                    });
                    
                    $('#listaPac').DataTable().ajax.reload(function() {
                        $('#listaPac tbody').show();
                        $(".dataTables_processing").hide();
                    }, false);

                })
                .fail(function(jqXHR) {
                    preloader('off');
                    checkError(jqXHR.status, errorData.msg);
                    return;
                });
            } 
        });
    });


    $(document).on('click', '#excel', function(e) {
        e.preventDefault();

        let ids = [];
        $('input[name="Id"]:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            toastr.warning('Debes seleccionar al menos un paciente para exportar.','',{timeOut: 1000});
            return;
        }

        swal({
            title: "¿Estás seguro de que deseas generar el reporte de Excel con todos los items seleccionados?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"],
        }).then((confirmar) => {
            if(confirmar){
                preloader('on');
                $.ajax({
                    url: exportExcel,
                    type: "GET",
                    data: {
                        Id: ids
                    },
                    success: function(response) {
                        preloader('off');
                        let tipoToastr = response.estado === 'success' ? 'success' : 'warning';
                        createFile("excel", response.filePath, generarCodigoAleatorio() + "_reporte");
                        toastr[tipoToastr](response.msg);
                        return;
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

    $(document).on('click', '.downPaciente', function(e){
        e.preventDefault();
        let paciente = $(this).data('id'), fullName = $(this).data('nombrecompleto');

        swal({
            title: `¿Está seguro que desea dar de baja a este paciente ${fullName}?`,
            icon: "warning",
            buttons: ["Cancelar", "Dar de baja"],
        }).then((confirmar) => {
            if(confirmar) {
                
                $('#listaPac tbody').hide();
                $(".dataTables_processing").show();

                $.post(down, {_token: TOKEN, ids: paciente})
                .done(function(response){
              

                    let tipo = {
                        200: 'success',
                        409: 'warning'
                    };

                    response.forEach(function(data) {
                        toastr[tipo[data.status]](data.msg);
                    });
                    $('#listaPac').DataTable().ajax.reload(function() {
                        $('#listaPac tbody').show();
                        $(".dataTables_processing").hide();
                    }, false);

                })
                .fail(function(jqXHR){
      
                    let errorData = JSON.parse(jqXHR.responseText);            
                    checkError(jqXHR.status, errorData.msg);
                    return;
                    
                })
            } 
        });
  
    });

});