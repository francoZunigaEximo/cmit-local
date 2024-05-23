$(document).ready(()=>{

    $('#listaUsuarios').DataTable().clear().destroy();

    new DataTable("#listaUsuarios", {
        
        searching: false,
        ordering: false,
        processing: true,
        lengthChange: false,
        pageLength: 50,
        responsive: true,
        serverSide: true,
        deferRender: true,
        ajax: {
            url: INDEX,
        },
        dataType: 'json',
        type: 'POST',
        columns: [
            {
                data: 'usuario',
                name: 'usuario',
            },
            {
                data: null,
                render: function(data){
                    let nombreCompleto = data.Nombre + ' ' + data.Apellido;
                    return `<span class="text-uppercase" title="${nombreCompleto}">${acortadorTexto(nombreCompleto, 18)}</span>`;
                }
            },
            {
                data: null,
                render: function(data){
                    let arr = [null, undefined].includes(data.RolUsuario) ? [] : (data.RolUsuario).split(',');
                    let result = '';
                    arr.forEach(element => {
                        result += `<span class="custom-badge nuevoAzul m-1">${element}</span>`;
                    });
                    return result;
                    
                },
            },
            {
                data: null,
                render: function(data){
                    return `<p class="text-${data.Inactivo === 0 ? 'success' : 'danget' }">${data.Inactivo === 0 ? 'Activo' : 'Inactivo'}</p>`;
                },
            },
            {
                data: null,
                render: function(data){            
                    
                    let editar = `<a title="Editar" href="${location.href}/${data.IdUser}/edit"><button type="button" class="btn btn-sm iconGeneralNegro"><i class="ri-edit-line"></i></button></a>`,
                        eliminar = `<button data-id="${data.IdUser}" title="Dar de baja al usuario" type="button" class="btn btn-sm iconGeneralNegro baja"><i class="ri-delete-bin-2-line"></i></button>`,
                        bloquear = `<button title="${data.Inactivo === 1 ? 'Activar usuario' : 'Desactivar usuario'}" data-id="${data.IdUser}" class="btn btn-sm iconGeneralNegro bloquear"><i class="ri-lock-2-line"></i></button>`,
                        cambiar = `<button title="Cambiar password" data-id="${data.IdUser}" class="btn btn-sm iconGeneralNegro cambiarPass"><i class="ri-key-2-line"></i></button>`;

                    return `${editar}${bloquear}${cambiar}${eliminar}`;
                }
            }
        ],
        language: {
            processing: "<div style='text-align: center; margin-top: 20px;'><img src='./images/spinner.gif' /><p>Cargando...</p></div>",
            emptyTable: "No hay usuarios con los datos buscados",
            paginate: {
                first: "Primera",
                previous: "Anterior",
                next: "Siguiente",
                last: "Última"
            },
            aria: {
                paginate: {
                    first: "Primera",
                    previous: "Anterior",
                    next: "Siguiente",
                    last: "Última"
                }
            },
            info: "Mostrando _START_ a _END_ de _TOTAL_ de usuarios",
        },
    });


    $(document).on('click', '.buscarUsuario', function() {

        let nombre = $('#nombre').val(), usuario = $('#usua').val(), rol = $('#rol').val(), condiciones = [nombre, usuario, rol];

        if (condiciones.every(condicion => condicion === '' || condicion === null) === true) {
            toastr.warning("Debe seleccionar algun filtro para buscar");
            return;
        }

        $('#listaUsuarios').DataTable().clear().destroy();

        new DataTable("#listaUsuarios", {
            
            searching: false,
            ordering: false,
            processing: true,
            lengthChange: false,
            pageLength: 50,
            responsive: true,
            serverSide: true,
            deferRender: true,
            ajax: {
                url: SEARCHUSUARIO,
                data: function(d){
                    d.nombre = nombre;
                    d.usuario = usuario;
                    d.rol = rol;
                }
            },
            dataType: 'json',
            type: 'POST',
            columns: [
                {
                    data: 'usuario',
                    name: 'usuario',
                },
                {
                    data: null,
                    render: function(data){
                        let nombreCompleto = data.Nombre + ' ' + data.Apellido;
                        return `<span title="${nombreCompleto}">${acortadorTexto(nombreCompleto, 12)}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        let arr = [null, undefined].includes(data.RolUsuario) ? [] : (data.RolUsuario).split(',');
                        let result = '';
                        arr.forEach(element => {
                            result += `<span class="custom-badge nuevoAzul m-1">${element}</span>`;
                        });
                        return result;
                        
                    },
                },
                {
                    data: null,
                    render: function(data){
                        return `<p class="text-${data.Inactivo === 0 ? 'success' : 'danget' }">${data.Inactivo === 0 ? 'Activo' : 'Inactivo'}</p>`;
                    },
                },
                {
                    data: null,
                    render: function(data){            
                        
                        let editar = `<a title="Editar" href="${location.href}/${data.IdUser}/edit"><button type="button" class="btn btn-sm iconGeneralNegro"><i class="ri-edit-line"></i></button></a>`,
                            eliminar = `<button data-id="${data.IdUser}" title="Dar de baja al usuario" type="button" class="btn btn-sm iconGeneralNegro baja"><i class="ri-delete-bin-2-line"></i></button>`,
                            bloquear = `<button title="Bloquear usuario" data-id="${data.IdUser}" class="btn btn-sm iconGeneralNegro bloquear"><i class=" ri-lock-2-line"></i></button>`,
                            cambiar = `<button title="Cambiar password" data-id="${data.IdUser}" class="btn btn-sm iconGeneralNegro cambiarPass"><i class="ri-lock-password-line"></i></button>`;
    
                        return `${editar}${eliminar}${bloquear}${cambiar}`;
                    }
                }
            ],
            language: {
                processing: "<div style='text-align: center; margin-top: 20px;'><img src='./images/spinner.gif' /><p>Cargando...</p></div>",
                emptyTable: "No hay usuarios con los datos buscados",
                paginate: {
                    first: "Primera",
                    previous: "Anterior",
                    next: "Siguiente",
                    last: "Última"
                },
                aria: {
                    paginate: {
                        first: "Primera",
                        previous: "Anterior",
                        next: "Siguiente",
                        last: "Última"
                    }
                },
                info: "Mostrando _START_ a _END_ de _TOTAL_ de usuarios",
            },
        });

    });

    function acortadorTexto(cadena, nroCaracteres = 10) {
        return cadena.length <= nroCaracteres ? cadena : cadena.substring(0,nroCaracteres);
    }

});