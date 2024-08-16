$(document).ready(()=>{

    //Fix de presionar enter sobre los campos
    $('#fechaDesde, #fechaHasta, #TipoPrestacion, #Estado, #nroprestacion').on('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
        }
    });

    let IdComentario = null;

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

                        if(response.estado === 'true') {
                            toastr.success(response.msg);
                            $('#listaPrestaciones').DataTable().clear().draw(false);
                        }else{
                            toastr.warning(response.msg);
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
    $(document).on('click', '.prestacionComentario', function() {
        
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
    
    /*$('#checkAll').on('click', function() {
        $('input[type="checkbox"][name="Id"]:not(#checkAll)').prop('checked', this.checked);
    });*/

});