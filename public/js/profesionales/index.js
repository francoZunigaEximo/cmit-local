$(document).ready(()=> {

    toastr.options = {
        closeButton: true,   
        progressBar: true,    
        timeOut: 3000,        
    };

    $('#especialidad').select2({
        placeholder: 'Seleccionar especialidad...',
        language: {
            noResults: function() {
                return "No hay especialidades con esos datos";        
            },
            searching: function() {
                return "Buscando..";
            },
            inputTooShort: function () {
                return "Por favor, ingrese 2 o más caracteres";
            }
        },
        allowClear: true,
        ajax: {
           url: getProveedores,
           dataType: 'json',
           delay: 250,
           data: function(params) {
                return {
                    buscar: params.term,
                };
           },
           processResults: function(data) {
                return {
                    results: data.proveedores
                };
           },
           cache: true,
        },
        minimumInputLength: 2
    });


    $(document).on("select2:open", () => {
        document.querySelector(".select2-container--open .select2-search__field").focus()
    });

    $(document).on('click', '.blockProfesional', function(e) {
        e.preventDefault();

        if (confirm("¿Esta seguro que desea bloquear a este profesional?")){
            
            accion("bloquear", $(this).data('id'));
        }
        
    });

    $(document).on('click', '.deleteProfesional', function(e) {
        e.preventDefault();

        if (confirm("¿Esta seguro que desea eliminar a este profesional?")){

            accion("eliminar", $(this).data('id'));
        }
    });

    $(document).on('click', '.multipleBProf, .multipleDProf', function(e) {
        e.preventDefault();
        
            let capturado = $(this).hasClass('multipleBProf') ? 'multipleBProf' : 'multipleDProf',
            ids = [];

        $('input[name="Id"]:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            toastr.warning("Debe seleccionar al menos un profesional para la baja múltiple", "warning");
            return; 
        }
        if (confirm("¿Esta seguro que desea realizar esta acción?")){
            accion(capturado, ids);
        }
    });

   

    function accion(tipo, id){

        let listAccion = {
            'bloquear': {
                0: 'bloqueado',
                1: 'Bloquear'
            },   
            'eliminar': {
                0: 'eliminado',
                1: 'Eliminar'
            },
            'multipleBProf': {
                0: 'bloqueado, todos los profesionales',
                1: 'Bloqueo multiple de'
            },
            'multipleDProf': {
                0: 'eliminado, todos los profesionales',
                1: 'Eliminación multiple de'
            }
        };

        $.post(estadoProfesional, {_token: TOKEN, Id: id, tipo: tipo})

            .done(function(){
                toastr.options = {
                    closeButton: true,   
                    progressBar: true,    
                    timeOut: 3000,        
                };
                toastr.success(`Se ha/han ${listAccion[tipo][0]} correctamente`, `${listAccion[tipo][1]} profesional/es`);
                $('#listaProf').DataTable();
                $('#listaProf').DataTable().draw(false);

            })
            .fail(function(xhr){
                console.error(xhr);
                toastr.error("Ha ocurrido un error. Actualice la página y si el problema persiste, consulte con el administrador","Error");
            })
    }

});