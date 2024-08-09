$(document).ready(()=>{

    let IdProfesional = $('#IdProfesional').val();
    let resizing = false, startWidth, startHeight, startX, startY; //variables de ancho de imagen
    $('.verOpciones').hide();
    $('.verAlerta').show();

    quitarDuplicados("#provincia");
    quitarDuplicados("#cuil");
    quitarDuplicados("#tipoDoc");
    listadoRoles();
    cargarPerfiles();
    perfiles(IdProfesional);
    checkRol(IdProfesional);

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
            email = $('#email').val(),
            userId = $('#UserId').val();

        if($('#form-update').valid()){ 
            swal({
                title: "¿Esta seguro que desea confirmar la operación?",
                icon: "warning",
                buttons: ["Cancelar", "Aceptar"]
            }).then((confirmar) => {
                if(confirmar) {
                    preloader('on');
                    $.post(actualizarDatos, {_token: TOKEN, Nombre: nombre, Apellido: apellido, TipoDocumento: tipoDocumento, Documento: documento, TipoIdentificacion: tipoIdentificacion, Identificacion: identificacion, Telefono: telefono, FechaNacimiento: fechaNacimiento, Provincia: provincia, IdLocalidad: localidad, CP: cp, Id: Id, email: email, Direccion: direccion, UserId: userId})
                        .done(function(response){
                            preloader('off');
                            toastr.success(response.msg);
                            setTimeout(()=>{
                                location.reload();
                            },2000);
                        })
                        .fail(function(jqXHR){
                            preloader('off');
                            let errorData = JSON.parse(jqXHR.responseText);            
                            checkError(jqXHR.status, errorData.msg);
                            return;
                        });
                }
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
                            checkRol(IdProfesional);
                            perfiles(IdProfesional);
                            setTimeout(() => {
                                listadoRoles();
                            }, 2000);
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
                        checkRol(IdProfesional);
                        perfiles(IdProfesional);
                        cargarPerfiles();
                        setTimeout(() => {
                            listadoRoles();
                        }, 2000);
                    })
            }
        });  
    });

    $('#imagenModal').mousedown(function (e) {
        resizing = true;
        startWidth = $('#imagenModal').width();
        startHeight = $('#imagenModal').height();
        startX = e.clientX;
        startY = e.clientY;
    });

    $(document).on('click', '.saveOpciones', function(e){
        e.preventDefault();
        
        let Pago = $('#Pago').prop('checked') ? 1 : 0,
            InfAdj = $('#InfAdj').prop('checked') ? 1 : 0,
            Firma = $('#Firma').val(),
            Foto = $('#Foto')[0].files[0],
            wImage = $('#wImage').val(),
            hImage = $('#hImage').val(),
            Id = $('#IdProfesional').val();

        let formData = new FormData();
        formData.append('_token', TOKEN);
        formData.append('Pago', Pago);
        formData.append('InfAdj', InfAdj);
        formData.append('Firma', Firma);
        if (Foto) formData.append('Foto', Foto);
        formData.append('wImage', wImage);
        formData.append('hImage', hImage);
        formData.append('Id', Id);

        swal({
            title: "¿Está seguro que desea actualizar los datos",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar){
                
                preloader('on');
                $.ajax({
                    url: datosProf,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        preloader('off');
                        toastr.success(response.msg);
                    },
                    error: function(xhr, status, error) {
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return;  
                    }
                });
            }
        });
    });

    $(document).on('click', '.saveSeguro', function(e){
        e.preventDefault();

        let mn = $('#MN').val(),
            mp = $('#MP').val(),
            seguroMP = $('#SeguroMP').val(),
            Id = $('#IdProfesional').val();

        if(mn < 0) {
            toastr.warning("Matricula no acepta números negativos");
            return;
        }
        preloader('on');
        $.post(seguroProf, {_token: TOKEN, MN: mn, MP: mp, SeguroMP: seguroMP, Id: Id})
            
            .done(function(response){
                preloader('off');
                toastr.success(response.msg);
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            })
    });

    $(document).mousemove(function (e) {
        if (resizing) {
            let newWidth = startWidth + (e.clientX - startX);
            let newHeight = startHeight + (e.clientY - startY);

            // Aplica nuevas dimensiones
            $('#imagenModal').width(newWidth);
            $('#imagenModal').height(newHeight);

            $('#wImage').val(newWidth);
            $('#hImage').val(newHeight);
        }
    });

    $(document).mouseup(function () {
        resizing = false;
    });

    $(document).on('click', '.addPerfilProf', function(e){
        e.preventDefault();

        let perfil = $('#perfiles').val(), especialidad = $('#listaEspecialidad').val();

        if(perfil === '' || especialidad === '') {
            toastr.warning("Debe seleccionar una especialidad y un perfil para poder añadirlo a la lista");
            return;
        }
        preloader('on');
        $.post(setPerfiles, {_token: TOKEN, perfil: perfil, especialidad: especialidad, Id: IDPROF})
            
            .done(function(response){
                preloader('off');
                toastr.success(response.msg);
                cargarPerfiles(); 
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return; 
            });
    });

    $(document).on('click', '.eliminarPerfil',  function(e){
        e.preventDefault();
        let IdProf = $(this).data('prof'),
            IdProv = $(this).data('prov');

        swal({
            title: "¿Está seguro que desea eliminar?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                preloader('on');
                $.post(delPerfil, {_token: TOKEN, IdProf: IdProf, IdProv: IdProv})
                .done(function(){
                    preloader('off')
                    toastr.success(`Se ha añadido el perfil de manera correcta`);
                    cargarPerfiles();
                })
                .fail(function(jqXHR){
                    preloader('off');
                    let errorData = JSON.parse(jqXHR.responseText);            
                    checkError(jqXHR.status, errorData.msg);
                    return; 
                });
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

    function perfiles(id)
    {
        if([0, null, undefined, ''].includes(id)) return;

        $("#perfiles").empty();
        $("#perfiles").append('<option value="" selected>Elija una opción...</option>');

        $.get(choisePerfil, {Id: id})
            .done(function(response){

                const perfiles = new Map([
                    ['T1', 'Efector'],
                    ['T2', 'Informador'],
                    ['T3', 'Evaluador'],
                    ['T4', 'Combinado'],
                    ['T5', 'Evaluador ART']
                ]);

                let select = $("#perfiles");
                
                perfiles.forEach((descripcion, clave) => {
                    if (response[clave] === 1) {
                        select.append(`<option value="${clave.toLowerCase()}">${descripcion}</option>`);
                    }
                });
              
            });
    }

    function checkRol(id) {
        $.get(checkRoles, { Id: id }, function(response) {

            let arr = ['Efector', 'Informador', 'Evaluador', 'Combinado', 'Evaluador ART'];
            let buscados = response.map(item => item.nombre);
            
            let resultados = buscados.some(e => arr.includes(e));

            if (resultados) {
                $('.verOpciones').css('display', ''); 
                $('.verAlerta').hide();
            } else {
                $('.verOpciones').css('display', 'none');
                $('.verAlerta').show();
            }
        });
    }

    function cargarPerfiles(){
        preloader('on');
        $('#listaProfesionales').empty();

        $.get(getPerfiles, {Id: IDPROF})
            .done(function(response){
                preloader('off')
                let data = response.data;
                data.forEach(function (d) {

                    if(d.Tipos) {
                        let arr = d.Tipos.split(',');
                    
                        const perfiles = {
                            't1': ['Efector'],
                            't2': ['Informador'],
                            't3': ['Evaluador'],
                            't4': ['Combinado'],
                            't5': ['Evaluador ART']
                        }
                    
                        let imprimir = arr.map(e => {
                            if (perfiles[e]) {
                                return `<span class="badge custom-badge pequeno text-uppercase" style="margin-right: 3px; display:inline-block">${perfiles[e][0]}</span><br>`;
                            }
                            return ''; 
                        }).join(''); 
                    
                        let contenido = `
                            <tr style="text-align: center">
                                <td>${d.especialidad}</td>
                                <td style="margin-right: 3px;">
                                    ${imprimir}
                                </td>
                                <td>
                                    <div class="remove">
                                        <button data-prof="${d.IdProf}" data-prov="${d.IdProv}" class="btn btn-sm iconGeneral eliminarPerfil" title="Dar de baja"><i class="ri-delete-bin-2-line"></i></button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    
                        $('#listaProfesionales').append(contenido);
                        $('#perfiles').val(''),
                        $('#listaEspecialidad').val('');
                    }
                });
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return; 
            });
    }

    

});