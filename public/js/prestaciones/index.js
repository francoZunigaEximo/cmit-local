$(function() {

    //Fix de presionar enter sobre los campos
    $('#fechaDesde, #fechaHasta, #TipoPrestacion, #Estado, #nroprestacion').on('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
        }
    });

    //Fechas en filtros
    $('#fechaHasta').val(fechaNow(null, "-", 0));
    
    $('#paciente').select2({
        dropdownParent: $('#offcanvasTop'),
        language: {
            noResults: function() {

            return "No hay pacientes con esos datos";        
            },
            searching: function() {

            return "Buscando..";
            },
            inputTooShort: function () {
                return "Por favor, ingrese 2 o más caracteres";
            }
        },
        placeholder: 'Apellido y nombre del paciente',
        allowClear: true,
        ajax: {
            url: getPacientes, 
            dataType: 'json',
            data: function(params) {
                return {
                    buscar: params.term,
                };
            },
            processResults: function(data) {
                return {
                    results: data.pacientes 
                };
            },
            cache: true
        },
        minimumInputLength: 2 
    });

    $('#pacienteSelect2').select2({
        language: {
            noResults: function() {

            return "No hay pacientes con esos datos";        
            },
            searching: function() {

            return "Buscando..";
            },
            inputTooShort: function () {
                return "Por favor, ingrese 2 o más caracteres";
            }
        },
        placeholder: 'Nombre y/o apellido del paciente',
        allowClear: true,
        ajax: {
            url: getPacientes, 
            dataType: 'json',
            data: function(params) {
                return {
                    buscar: params.term,
                };
            },
            processResults: function(data) {
                return {
                    results: data.pacientes 
                };
            },
            cache: true
        },
        minimumInputLength: 2 
    });

    $('#empresaSelect2').select2({
        language: {
            noResults: function() {

            return "No hay empresas con esos datos";        
            },
            searching: function() {

            return "Buscando..";
            },
            inputTooShort: function () {
                return "Por favor, ingrese 2 o más caracteres";
            }
        },
        placeholder: 'Nombre Empresa, Alias o ParaEmpresa',
        allowClear: true,
        ajax: {
            url: getClientes, 
            dataType: 'json',
            data: function(params) {
                return {
                    buscar: params.term,
                    tipo: 'E'
                };
            },
            processResults: function(data) {
                return {
                    results: data.clientes 
                };
            },
            cache: true
        },
        minimumInputLength: 2 
    });

    $('#artSelect2').select2({
        language: {
            noResults: function() {

            return "No hay art con esos datos";        
            },
            searching: function() {

            return "Buscando..";
            },
            inputTooShort: function () {
                return "Por favor, ingrese 2 o más caracteres";
            }
        },
        placeholder: 'Nombre de la ART',
        allowClear: true,
        ajax: {
            url: getClientes, 
            dataType: 'json',
            data: function(params) {
                return {
                    buscar: params.term,
                    tipo: 'A'
                };
            },
            processResults: function(data) {
                return {
                    results: data.clientes 
                };
            },
            cache: true
        },
        minimumInputLength: 2 
    });

    $(document).on("select2:open", () => {
        document.querySelector(".select2-container--open .select2-search__field").focus()
    });

    // Checkeamos paciente y enviamos a edición para crear la prestación
    $('#checkPaciente').on('click', function() {
        let paciente = $('#paciente').select2('data').map(option => option.id);

        if (paciente.length == '') {
            toastr.warning("El campo no puede estar vacío. Si su usuario no existe, pruebe con crearlo con el botón nuevo.");
            return;
        }

        window.location = GOPACIENTES.replace('__paciente__', paciente);
    });

    //Cerramos el modal y reset de comentarios de la DB
    $('#prestacionModal').on('hidden.bs.modal', function(){
        $("#comentario").val("");
    });

    $(document).on('click', '.downPrestacion', function(){

        let prestacion = $(this).data('id');
        
        if(prestacion === '') return;

        swal({
            title: "¿Está seguro que desea dar de baja esta prestación?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"],
        }).then((aceptar) => {
            if(aceptar){
                preloader('on');

                $.get(downPrestaActiva, {Id: prestacion})
                    .done(function(response){
                        preloader('off');
                        let tipoToastr = response.estado === 'true' ? 'success' : 'warning';
                        if(response.estado === 'true') {
                            
                            toastr[tipoToastr](response.msg);
                            $('#listaPrestaciones').DataTable().clear().draw(false);
                        }else{
                            toastr[tipoToastr](response.msg);
                        }
                    })
                    .fail(function(jqXHR) {
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return; 
                    });
            }
        });
    });

    //Arrastramos eventos
    $(document).on('click', '.prestacionComentario', function(e) {
        e.preventDefault();
        
        let IdComentario = $(this).data('id');
        $('#IdComentarioEs').text(IdComentario);
        $('.guardarComentario').data('id', IdComentario);
        preloader('on');
        $.ajax({
            url: getComentarioPres,
            type: 'GET',
            data: {
                Id: IdComentario
            },
            success: function(response){
                preloader('off');
                let getComentario = response.comentario;
                getComentario ? $('#comentario').val(getComentario) : $('#comentario').val("...");

            },
            error:function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return; 
            }
        });
    });


    $('.guardarComentario').off('click').on('click', function(e) {
        e.preventDefault();
        let IdComentario = $(this).data('id');

        if (IdComentario) {
            let comentario = $('#comentario').val();

            $.ajax({
                url: setComentarioPres,
                type: 'Post',
                data: {
                    _token: TOKEN,
                    Obs: comentario,
                    IdP: IdComentario,
                },
                success: function(){
                    
                    toasrt.success("El comentario de la prestación se ha guardado correctamente", "Perfecto");
                },

                error: function(jqXHR){
                    preloader('off');
                    let errorData = JSON.parse(jqXHR.responseText);            
                    checkError(jqXHR.status, errorData.msg);
                    return; 
                }
            });
        }
    });

    $(document).on('click', '.bloquearPrestacion', function(e){
        e.preventDefault();
        let id = $(this).data('id');
        
        swal({
            title: "¿Está seguro que desea bloquear esta prestación?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"],
        }).then((aceptar) => {
            if(aceptar){
                $('#listaPrestaciones tbody').hide();
                $('.dataTables_processing').show();

                $.get(blockPrestacion, {Id: id})
                    .done(function(){
                 
                        toastr.success("Se ha bloqueado correctamente", "Perfecto");
                        $('#listaPrestaciones').DataTable().ajax.reload(function(){
                            $('#listaPrestaciones tbody').show();
                            $('.dataTables_processing').hide();
                        }, false);
                        
                    })
                    .fail(function(jqXHR) {
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return; 
                    }); 
            }
        });       
    });

    $(document).on('click', '.exportExcel', function(e){
        e.preventDefault();

        const listaPrestaciones = $('#listaPrestaciones').DataTable();

        let tipo = $(this).data('id'), nroprestacion = $('#nroprestacion').val(), fechaDesde = $('#fechaDesde').val(), fechaHasta = $('#fechaHasta').val();
        
        if (!listaPrestaciones.data().any() ) {
            $('#listaPrestaciones').DataTable().destroy();
            toastr.info('No existen registros para exportar', 'Atención');
            return;
        }
    
        filters = "";
        length  = $('input[name="Id"]:checked').length;
    
        let data = listaPrestaciones.rows({ page: 'current' }).data().toArray();
        let ids = data.map(function(row) {
            return row.Id;
        });
    
        if(!['',0, null].includes(ids)) {
            filters += "nroprestacion:" + nroprestacion + ",";
            filters += "paciente:" + $('#pacienteSearch').val() + ",";
            filters += "empresa:" + $('#empresaSearch').val() + ",";
            filters += "art:" + $('#artSearch').val() + ",";
            filters += "tipoPrestacion:" + $('#TipoPrestacion').val() + ",";
            filters += "fechaDesde:" + fechaDesde + ",";
            filters += "fechaHasta:" + fechaHasta + ",";
            filters += "estado:" + $('#Estado').val() + ",";
    
            if((fechaDesde == '' || fechaHasta == '') && nroprestacion == ''){
                swal('Alerta','La fecha "Desde" y "Hasta" son obligatorias.', 'warning');
                return;
            }
        }

        swal({
            title: "¿Está seguro que desea exportar la lista de prestaciones?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"],
        }).then((aceptar) => {
            if(aceptar) {
                preloader('on');
                $.get(sendExcel, {ids: ids, filters: filters, tipo: tipo})
                .done(function(response){
                    preloader('off');
                    createFile("excel", response.filePath, generarCodigoAleatorio() + "_reporte_" + tipo);
                        preloader('off');
                        toastr.success(response.msg);
                        return;
                })
                .fail(function(jqXHR) {
                    preloader('off');
                    let errorData = JSON.parse(jqXHR.responseText);            
                    checkError(jqXHR.status, errorData.msg);
                    return; 
                });

            };
        });

        
    });

});