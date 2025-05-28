$(function(){

    const tabla = $('#listaUsuarios');

    $('#nombre').select2({
        language: {
            noResults: function() {

            return "No hay usuarios con esos datos";        
            },
            searching: function() {

            return "Buscando..";
            },
            inputTooShort: function () {
                return "Por favor, ingrese 2 o más caracteres";
            }
        },
        placeholder: 'Apellido y nombre de usuario',
        allowClear: true,
        ajax: {
            url: searchNombreUsuario, 
            dataType: 'json',
            data: function(params) {
                return {
                    buscar: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: data.result 
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });

    $('#usua').select2({
        language: {
            noResults: function() {

            return "No hay usuarios con esos datos";        
            },
            searching: function() {

            return "Buscando..";
            },
            inputTooShort: function () {
                return "Por favor, ingrese 2 o más caracteres";
            }
        },
        placeholder: 'Nombre de usuario',
        allowClear: true,
        ajax: {
            url: searchUsuario, 
            dataType: 'json',
            data: function(params) {
                return {
                    buscar: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: data.result 
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });


    $('#rol').select2({
        language: {
            noResults: function() {

            return "No hay roles con esos datos";        
            },
            searching: function() {

            return "Buscando..";
            },
            inputTooShort: function () {
                return "Por favor, ingrese 2 o más caracteres";
            }
        },
        placeholder: 'Rol de usuario',
        allowClear: true,
        ajax: {
            url: searchRol, 
            dataType: 'json',
            data: function(params) {
                return {
                    buscar: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: data.result 
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });


    $(document).on('click', '.baja', function(e){
        let id = $(this).data('id');

        if([null, undefined, 0].includes(id)) return;

        swal({
            title: "¿Estas seguro que deseas eliminar al usuario?",
            icon: "warning",
            buttons: ["Cancelar", "Eliminar"],
        }).then((result) => {
            if(result){

                $('#listaUsuarios tbody').hide();
                $('.dataTables_processing').show();

                $.get(bajaUsuario, {Id: id})
                    .done(function(response){

                        let tipoToastr = response.estado === 'success' ? 'success' : 'warning';

                        toastr[tipoToastr](response.msg);
                        $('#listaUsuarios').DataTable().ajax.reload(function(){
                            $('#listaUsuarios tbody').show();
                            $('.dataTables_processing').hide();
                        }, false);
                    })
                    .fail(function(jqXHR){

                        let errorData = JSON.parse(jqXHR.responseText);
                        checkError(jqXHR.status, errorData.msg);
                        return;
                    });
            }
        });            
    });

    $(document).on('click', '.bloquear', function(e){
        let id = $(this).data('id');

        if([null, undefined, 0].includes(id)) return;

        swal({
            title: "¿Estas seguro que deseas realizar la acción?",
            icon: "warning",
            buttons: ["No", "Si"]
        }).then((result) => {
            if(result){

                $('#listaUsuarios tbody').hide();
                $('.dataTables_processing').show();

                $.get(bloquearUsuario, {Id: id})
                    .done(function(response){
                        toastr.success(response.msg);
                        $('#listaUsuarios').DataTable().ajax.reload(function(){
                            $('#listaUsuarios tbody').show();
                            $('.dataTables_processing').hide();
                        }, false);
                        
                    })
                    .fail(function(jqXHR){

                        let errorData = JSON.parse(jqXHR.responseText);
                        checkError(jqXHR.status, errorData.msg);
                        return;
                    });
            }
        });      
    });

    $(document).on('click', '.cambiarPass', function(e){
        let id = $(this).data('id');

        if([null, undefined, 0].includes(id)) return;

        swal({
            title: "¿Estas seguro que deseas resetear la contraseña?",
            icon: "warning",
            buttons: ["Cancelar", "Resetear"]
        }).then((result) => {
            if(result){
                preloader('on');
                $.get(cambiarPassUsuario, {Id: id})
                    .done(function(response){
                        preloader('off');
                        toastr.success(response.msg);
                    }); 
            }
        });      
    });

    $(document).on('click', '#reiniciar', function(e){
        e.preventDefault();
        $('#listaUsuarios tbody').hide();
        $('.dataTables_processing').show();

        $('#listaUsuarios').DataTable().ajax.reload(function(){
            $('#listaUsuarios tbody').show();
            $('.dataTables_processing').hide();
        }, false);

    });

    $(document).on('click', '.forzarCierre', function(e){
        e.preventDefault();

        let id = $(this).data('id');

        if([null, 0, undefined, ''].includes(id)) return;

        swal({
            title: "¿Esta seguro que deseas cerrar la sesion del usuario?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) =>{
            if(confirmar){

                preloader('on');
                $.get(btnCerrarSesion, {Id: id, forzar: true})
                    .done(function(response){
                        preloader('off');
                        toastr.success(response.msg);
                        tabla.DataTable().draw(false);

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