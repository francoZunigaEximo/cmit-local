$(document).ready(()=>{

    toastr.options = {
        closeButton: true,   
        progressBar: true,    
        timeOut: 3000,        
    };

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

        if(confirm("¿Esta seguro que desea eliminar al usuario?")) {

            preloader('on');
            $.get(bajaUsuario, {Id: id})
                .done(function(){
                    preloader('off');
                    toastr.success("Se ha dado de baja al usuario correctamente");
                    setTimeout(()=> {
                        $('#listaUsuarios').DataTable();
                        $('#listaUsuarios').DataTable().draw(false);
                    }, 2000);
                });

        }
    });

    $(document).on('click', '.bloquear', function(e){
        let id = $(this).data('id');

        if([null, undefined, 0].includes(id)) return;

        if(confirm("¿Esta seguro que desea bloquear al usuario")){

            preloader('on');
            $.get(bloquearUsuario, {Id: id})
                .done(function(response){
                    preloader('off');
                    toastr.success(response.msg);
                    setTimeout(()=> {
                        $('#listaUsuarios').DataTable();
                        $('#listaUsuarios').DataTable().draw(false);
                    }, 2000);
                });

        }
        
    });

    $(document).on('click', '.cambiarPass', function(e){
        let id = $(this).data('id');

        if([null, undefined, 0].includes(id)) return;

        if(confirm("¿Esta seguro que desea resetear la contraseña?")) {

            preloader('on');
            $.get(cambiarPassUsuario, {Id: id})
                .done(function(){
                    preloader('off');
                    toastr.success("Se ha dado de cambiado la contraseña a 'cmit1234' correctamente");
                });

        }    
    });

    function preloader(opcion) {
        $('#preloader').css({
            opacity: '0.3',
            visibility: opcion === 'on' ? 'visible' : 'hidden'
        });
    }

});