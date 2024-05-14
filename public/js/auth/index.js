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


    $(document).on('click', '.buscarUsuario', function(e){
        e.preventDefault();

        


        
    })

});