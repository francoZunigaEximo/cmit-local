$(document).ready(()=>{

    quitarDuplicados("#provincia");
    quitarDuplicados("#cuil");
    quitarDuplicados("#tipoDoc");
    listadoRoles();

    $(document).on('click', '.updateDatos', function(e){
        e.preventDefault();

        let nombre = $('#nombre').val(),
            apellido = $('#apellido').val(),
            tipoDocumento = $('#tipoDoc').val(),
            documento = $('#numeroDoc').val(),
            tipoIdentificacion = $('#cuil').val(),
            identificacion = $('#numeroCUIL').val(),
            telefono = $('#numTelefono').val(),
            fechaNacimiento = $('#fechaNac').val(),
            provincia = $('#provincia').val(),
            localidad = $('#localidad').val(),
            direccion = $('#direccion').val(),
            cp = $('#codPostal').val(),
            Id = $('#Id').val(),
            email = $('#email').val();

        if($('#form-update').valid() && confirm("¿Esta seguro que desea confirmar la operación?")) {
            preloader('on');
            $.post(actualizarDatos, {_token: TOKEN, Nombre: nombre, Apellido: apellido, TipoDocumento: tipoDocumento, Documento: documento, TipoIdentificacion: tipoIdentificacion, Identificacion: identificacion, Telefono: telefono, FechaNacimiento: fechaNacimiento, Provincia: provincia, IdLocalidad: localidad, CP: cp, Id: Id, email: email, Direccion: direccion})
                .done(function(){
                    preloader('off');
                    toastr.success("Se han actualizado correctamente los datos del usuario");
                    setTimeout(()=>{
                        location.reload();
                    },2000);
                })
                .fail(function(jqXHR){
                    let errorData = JSON.parse(jqXHR.responseText);            
                    checkError(jqXHR.status, errorData.msg);
                    return;
                })
        }

    });

    $('#provincia').change(function() {
        let provincia = $(this).val();
        loadProvincia(provincia);
    });

     $('#localidad').change(function() {
        let localidadId = $(this).val();
        // Realizar la solicitud Ajax
        loadLocalidad(localidadId);
    });

    $(document).on('click', '#volver', function(e){
        e.preventDefault();
        window.location.href = INDEX;
    });

    let timer = null;

    $(document).on('click', '.cambiarEmail', function(){

        let email = $('#email').val(), name = $('#usuario').val();
        
        if([null, undefined, ''].includes(email)) {
            toastr.warning("El email no puede estar vacío");
            return;
        }

        if(correoValido(email) === false) {
            toastr.warning("El email no es válido");
            return;
        }

        preloader('on');
        $.get(checkEmailUpdate, {email: email, name: name})
            .done(function(response){
                preloader('off');
                $tipo = response.estado === 'true' ? 'success' : 'warning';
                toastr[$tipo](response.msg);
            });

    });

    $(document).on('click', '.agregarRol', function(e){
        let rol = $('#listaRoles').val(), usuario = $(this).data('id'),  errores = [null, undefined, ''];
        if(errores.includes(rol) || errores.includes(usuario)) return;

        swal({
            title: "¿Estas seguro que deseas agregar el rol?",
            icon: "warning",
            buttons: ["Cancelar", "Agregar"],
        }).then((result) => {
            if(result){

                preloader('on');
                $.post(addRol, {_token: TOKEN, user: usuario, role: rol})
                    .done(function(response){
                        preloader('off');
                        if(response.estado === 'false'){
                            toastr.warning(response.msg);
                        }else if(response.estado === 'true'){
                            toastr.success(response.msg);
                            setTimeout(() => {
                                listadoRoles();
                            }, 2000);
                        }
                    })
            }
        });

           
        
    });

    $(document).on('click', '.eliminar', function(e){

        let usuario = $(this).data('user'), rol = $(this).data('rol'), errores = [null, undefined, '', 0];
        if(errores.includes(rol) || errores.includes(usuario)) return;

        swal({
            title: "¿Estas seguro que deseas eliminar el rol?",
            icon: "warning",
            buttons: ["Cancelar", "Eliminar"],
        }).then((result) => {
            if(result){

                preloader('on');
                $.get(deleteRol, {user: usuario, role: rol})
                    .done(function(response){
                        preloader('off');
                        toastr.success(response.msg);
                        setTimeout(() => {
                            listadoRoles();
                        }, 2000);
                    })
            }
        });  
    });
    
    function listadoRoles() {
        preloader('on');
        $.get(lstRolAsignados, {Id: ID})
            .done(function(response){
                preloader('off');
                $('#lstRolesAsignados').empty();

                $.each(response, function(index, r){
                    let arr = (r.Descripcion).split(', ');
    
                    let badges = '';
                    // Crea un badge para cada descripción
                    arr.forEach((descripcion, i) => {
                        badges += `<span class="badge bg-primary">${descripcion}</span> `;
                        if ((i + 1) % 7 === 0) {
                            badges += '<br>';  // Inserta un salto de línea cada 7 badges
                        }
                    });
                
                    let contenido = `
                    <tr>
                        <td>${r.Nombre}</td>
                        <td>${badges}</td>
                        <td>
                            <button data-rol="${r.IdRol}" data-user="${r.IdUser}" title="Eliminar rol" type="button" class="btn btn-sm iconGeneralNegro eliminar"><i class="ri-delete-bin-2-line"></i></button>
                        </td>
                    </tr>
                    `;
                    $('#lstRolesAsignados').append(contenido);
                });

            });
    }

    function loadProvincia(valor) {
        $.ajax({
            url: getLocalidad,
            type: "GET",
            data: {
                provincia: valor,
            },
            success: function(response) {
                let localidades = response.localidades;
                $('#localidad').empty();
                $('#localidad').append('<option selected>Elija una opción...</option>');
                localidades.forEach(function(localidad) {
                    $('#localidad').append('<option value="' + localidad.id + '">' + localidad.nombre + '</option>');
                });
            }
        });
    }

    function loadLocalidad(valor){
        
        $.ajax({
            url: getCodigoPostal,
            type: "GET",
            data: {
                localidadId: valor,
            },
            success: function(response) {
                // Actualizar el valor del input de Código Postal
                $('#codPostal').val(response.codigoPostal);
            }
        });
    }

});