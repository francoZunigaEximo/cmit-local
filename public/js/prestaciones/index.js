$(document).ready(()=>{

    toastr.options = {
        closeButton: true,   
        progressBar: true,     
        timeOut: 3000,        
    };

    //scrollListado();

    //Limpiamos el ID para evitar bug
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
        placeholder: 'Nombre y apellido del paciente',
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
            url: getOnlyClientes, 
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
            url: getOnlyClientes, 
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
            toastr.warning("El campo no puede estar vacío. Si su usuario no existe, pruebe con crearlo con el botón nuevo.", "Atención");
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

        if(confirm("¿Está seguro que desea dar de baja esta prestación?")){

            $.get(downPrestaActiva, {Id: prestacion})
                .done(function(){
                    toastr.success("Se ha dado de baja la prestación", "Perfecto");
                    $('#listaPrestaciones').DataTable();
                    $('#listaPrestaciones').DataTable().draw(false);
                })
                .fail(function(xhr){
                    toastr.error("Ha ocurrido un error. Consulte con el administrador", "Error");
                    console.error(xhr);
                });
        }
    });

    //Arrastramos eventos
    $(document).on('click', '.prestacionComentario', function() {
        
        let IdComentario = $(this).data('id');
        $('#IdComentarioEs').text(IdComentario);
        $('.guardarComentario').data('id', IdComentario);

        $.ajax({
            url: getComentarioPres,
            type: 'get',
            data: {
                Id: IdComentario
            },
            success: function(response){
                let getComentario = response.comentario;

                if(getComentario){
                    $('#comentario').val(getComentario);
                }else{
                    $('#comentario').val("...");
                }

            },
            error:function(xhr){

                toasrt.error("¡Ha ocurrido un inconveniente y la solicitud no podrá llevarse a cabo. Consulte con el administrador!", "Error");
                console.error(xhr);
            }
        });
    });


    $('.guardarComentario').off('click').on('click', function() {

        event.stopPropagation();
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

                error: function(xhr){

                    toasrt.error("¡Ha ocurrido un inconveniente y la solicitud no podrá llevarse a cabo. Consulte con el administrador!", "Error");
                    console.error(xhr);
                }
            });
        }
    });

    $(document).on('click', '.bloquearPrestacion', function(){

        let id = $(this).data('id');
        
        $.get(blockPrestacion, {Id: id})
            .done(function(){
                toastr.success("Se ha bloqueado correctamente", "Perfecto");
                $('#listaPrestaciones').DataTable();
                $('#listaPrestaciones').DataTable().draw(false);
            })
            .fail(function(xhr){
                toastr.error("Se ha producido un error. Consulte con el administrador", "Error");
                console.error(xhr);
            });
    });
    

    /*function scrollListado(){
        $('html, body').animate({
            scrollTop: $('#listaPrestaciones').offset().top
        }, 800);
    }*/

    function fechaNow(fechaAformatear, divider, format) {
        let dia, mes, anio; 
    
        if (fechaAformatear === null) {
            let fechaHoy = new Date();
    
            dia = fechaHoy.getDate().toString().padStart(2, '0');
            mes = (fechaHoy.getMonth() + 1).toString().padStart(2, '0');
            anio = fechaHoy.getFullYear();
        } else {
            let nuevaFecha = fechaAformatear.split("-"); 
            dia = nuevaFecha[0]; 
            mes = nuevaFecha[1]; 
            anio = nuevaFecha[2];
        }
    
        return (format === 1) ? `${dia}${divider}${mes}${divider}${anio}` : `${anio}${divider}${mes}${divider}${dia}`;
    }

    $('#checkAll').on('click', function() {
        $('input[type="checkbox"][name="Id"]:not(#checkAll)').prop('checked', this.checked);
    });

});