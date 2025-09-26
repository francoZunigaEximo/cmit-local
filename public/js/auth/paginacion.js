$(function() {
    let nombre = $('#nombre').val(), 
        usuario = $('#usua').val(), 
        rol = $('#rol').val();
    
    let condiciones = [nombre, usuario, rol],
        idSesion = $('#ID').val(),
        tabla = "#listaUsuarios";

    let informacion = {
            
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
            data: function (d) {
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
                    return `<span title="${data.nombreCompleto}">${data.nombreCompleto}</span>`;
                }
            },
            {
                data: null,
                render: function(data){
                    let arr = !data.RolUsuario ? [] : (data.RolUsuario).split(',');
                    let result = '';
                    arr.forEach(element => {
                        result += `<span class="custom-badge nuevoAzul">${element}</span> `;
                    });
                    return result;
                    
                },
            },
            {
                data: null,
                render: function(data){
                    return `<p title="${data.Inactivo === 0 ? 'Habilitado' : 'Inhabilitado'}" class="badge round-pill text-bg-${data.Inactivo === 0 ? 'success' : 'danger' }">${data.Inactivo === 0 ? 'Activo' : 'Inactivo'}</p>`;
                },
            },
            {
                data: null,
                render: function(data) {
                    return `<span class="badge badge-label ${data.status === 'online' ? 'bg-success' : 'bg-danger'}"><i class="mdi mdi-circle-medium"></i>${data.status}</span>`;
                }
            },
            {
                data: null,
                data: null,
                render: function(data){            
                    
                    let editar = `<a class="btn btn-sm botonGeneral small p-1" title="Editar" href="${location.href}/${data.IdUser}/edit"><i class="ri-edit-line p-1"></i></a>`,
                        eliminar = `<button data-id="${data.IdUser}" title="Eliminar" type="button" class="btn btn-sm botonGeneral baja small p-1"><i class="ri-delete-bin-2-line p-1"></i></button>`,
                        bloquear = `<button title="${data.Inactivo === 1 ? 'Activar usuario' : 'Desactivar usuario'}" data-id="${data.IdUser}" class="btn btn-sm p-1 botonGeneral bloquear small"><i class="ri-lock-unlock-line p-1"></i></button>`,
                        cambiar = `<button title="Restablecer password" data-id="${data.IdUser}" class="btn btn-sm botonGeneral p-1 cambiarPass"><i class="ri-key-2-line p-1"></i></button>`,
                        expulsar = `<button title="Forzar cierre de sesión" data-id="${data.IdUser}" class="btn btn-sm botonGeneral p-1 forzarCierre"><i class="ri-logout-box-line p-1"></i></button>`;
    


                    return `${editar} ${bloquear} ${cambiar} ${eliminar} ${(parseInt(idSesion) !== data.IdUser) && data.status === 'online' ? expulsar : ''}`;
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
    };

       let dataTable = new DataTable(tabla, informacion);

       $(document).on('click', '.buscarUsuario', function (e) {
            e.preventDefault();

            /*if (condiciones.every(condicion => !condicion)) {
                toastr.warning("Debe seleccionar algún filtro para buscar",'',{timeOut: 1000});
                return;
            }*/
            nombre = $('#nombre').val();
            usuario = $('#usua').val();
            rol = $('#rol').val();

            informacion.ajax.data.nombre = nombre;
            informacion.ajax.data.usuario = usuario;
            informacion.ajax.data.rol = rol;
   
            dataTable.clear().destroy();
            dataTable = new DataTable(tabla, informacion);
       });

});