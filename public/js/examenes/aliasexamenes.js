$(document).ready(function(){

    cargarListado().then(response => {
        return response;
    });

    cargarOptions().then(response => {
        return response;
    });

    $(document).on('click', '.agregarItem', function(e){
        e.preventDefault();

        let nombre = $('#nombreAlias').val(), descripcion = $('#descripcionAlias').val();
        console.log(nombre, descripcion)
        
        if(nombre === '') {
            toastr.warning("El nombre del alias del exámen es obligatorio");
            return;
        }

        swal({
            title: "¿Estás seguro que deseas crear el alias?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar){
                preloader('on');
                $.post(agregarAlias, {_token: TOKEN, Nombre: nombre, Descripcion: descripcion})
                .done(function(response){
                    cargarListado().then(response => {
                        return response;
                    });
                    cargarOptions().then(response => {
                        return response;
                    });
                    preloader('off');
                    toastr.success(response.msg);
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

    $(document).on('click', '.eliminarAlias', function(e){
        e.preventDefault();
        let alias = $(this).data('id');

        if(['', 0, undefined].includes(alias)) {
            toastr.warning("No se ha encontrado el alias que desea eliminar");
            return;
        }

        swal({
            title: "¿Estás seguro que deseas eliminar el alias",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                preloader('on');
                $.get(delAlias, {Id: alias})
                    .done(function(response){
                        cargarListado().then(response => {
                            return response;
                        });
                        cargarOptions().then(response => {
                            return response;
                        });
                        preloader('off');
                        toastr.success(response.msg);
                    })
                    .fail(function(jqXHR) {
                        let errorData = JSON.parse(jqXHR.responseText);
                        checkError(jqXHR.status, errorData.msg);
                        return;
                    })
            }
        });
    });

    async function cargarListado() {

        $('#lstAliasExamenes').empty();
        const result = await $.get(cargar);

        $.each(result, function(index, r){
            let contenido = `
            <tr>
                <td>${r.Nombre}</td>
                <td>${r.Descripcion}</td>
                <td
                    <div class="remove">
                        <button data-id="${r.Id}" class="btn btn-sm iconGeneral eliminarAlias" title="Eliminar">
                            <i class="ri-delete-bin-2-line"></i>
                        </button>
                    </div>  
                </td>
            `;
            $('#lstAliasExamenes').append(contenido);
        });

        $("#lstAliasExamenes").fancyTable({
            pagination: true,
            perPage: 50,
            searchable: false,
            globalSearch: false,
            sortable: false, 
        });
    }

    async function cargarOptions() {
        

        const opcion = typeof ID === 'undefined' ? false : await optionExamen(ID);

        if (opcion && opcion.alias_examen && opcion.alias_examen.Id) {
            $('#aliasexamenes').empty().append(`<option selected value="${opcion.alias_examen.Id}">${opcion.alias_examen.Nombre}</option>`);
        }else{
            $('#aliasexamenes').empty().append('<option selected value="">Elija una opción...</option>');
        }
        
        const result = await $.get(cargar);

        $.each(result, function(index, r) {
            let contenido = `
                <option value="${r.Id}">${r.Nombre}</option>
            `;
            $('#aliasexamenes').append(contenido);
        });

        quitarDuplicados('#aliasexamenes');
    }

    async function optionExamen(id) {
        if ([0, '', null, undefined].includes(id)) return;
    
        try {
            const response = await $.get(optionEx, { Id: id });
            console.log(response); // Muestra el resultado
            return response; // Devuelve el resultado
        } catch (jqXHR) {
            let errorData = JSON.parse(jqXHR.responseText);
            checkError(jqXHR.status, errorData.msg);
            return null; // Maneja el error adecuadamente
        }
    }
    
    

});