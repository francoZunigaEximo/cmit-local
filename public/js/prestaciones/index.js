$(document).ready(()=>{

    //Fechas en filtros
    $('#fechaHasta').val(fechaNow(null, "-", 2));
    //Limpiamos el ID para evitar bug
    let IdComentario = null;

    scrollListado();

    $('#paciente').select2({
        dropdownParent: $('#offcanvasTop'),
        language: {
            noResults: function() {

            return "No hay pacientes con esos datos";        
            },
            searching: function() {

            return "Buscando..";
            }
        },
        placeholder: 'Nombre y apellido del paciente',
        language: "es",
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

    $(document).on("select2:open", () => {
        document.querySelector(".select2-container--open .select2-search__field").focus()
    });

    // Checkeamos paciente y enviamos a edición para crear la prestación
    $('#checkPaciente').on('click', function() {
        let paciente = $('#paciente').select2('data').map(option => option.id);

        if (paciente.length == '') {
            swal("Atención", "El campo no puede estar vacío. Si su usuario no existe, pruebe con crearlo con el botón nuevo.", "warning");
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

            $.post(downPrestaActiva, {_token: TOKEN, Id: prestacion})
                .done(function(){
                    swal('Perfecto', 'Se ha dado de baja la prestación', 'success');
                    $('#listaPrestaciones').DataTable();
                    $('#listaPrestaciones').DataTable().draw(false);
                })
                .fail(function(xhr){
                    swal('Error', 'Ha ocurrido un error. Consulte con el administrador', 'error');
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
            type: 'Post',
            data: {
                _token: TOKEN,
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

                swal('Error', '¡Ha ocurrido un inconveniente y la solicitud no podrá llevarse a cabo. Consulte con el administrador!', 'error');
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
                    
                    swal('Perfecto', 'El comentario de la prestación se ha guardado correctamente', 'success');
                },

                error: function(xhr){

                    swal("Error", "¡Ha ocurrido un inconveniente y la solicitud no podrá llevarse a cabo. Consulte con el administrador!", "error");
                    console.error(xhr);
                }
            });
        }
    });
    

    function scrollListado(){
        $('html, body').animate({
            scrollTop: $('#listaPrestaciones').offset().top
        }, 800);
    }

    function mostrarPreloader(arg) {
        $(arg).css({
            opacity: '0.3',
            visibility: 'visible'
        });
    }
    
    function ocultarPreloader(arg) {
        $(arg).css({
            opacity: '0',
            visibility: 'hidden'
        });
    }

    //Obtener fechas
    function fechaNow(fechaAformatear, divider, format) {
        let fechaActual;

        if (fechaAformatear === null) {
            fechaActual = new Date();
        } else {
            fechaActual = new Date(fechaAformatear);
        }

        let dia = fechaActual.getDate();
        let mes = fechaActual.getMonth() + 1;
        let anio = fechaActual.getFullYear();

        dia = dia < 10 ? '0' + dia : dia;
        mes = mes < 10 ? '0' + mes : mes;

        return (format === 1) ? dia + divider + mes + divider + anio : anio + divider + mes + divider + dia;
    }

});