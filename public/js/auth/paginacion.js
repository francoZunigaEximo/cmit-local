let nombre = $('#nombre').val(), usuario = $('#usua').val(), rol = $('#rol').val(), condiciones = [nombre, usuario, rol];

$(document).ready(()=>{

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
                let nombre = $('#nombre').val();
                let usuario = $('#usua').val();
                let rol = $('#rol').val();

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
                data: null,
                render: function(data){            
                    
                    let editar = `<a class="btn btn-sm botonGeneral small p-1" title="Editar" href="${location.href}/${data.IdUser}/edit"><i class="ri-edit-line p-1"></i>Editar</a>`,
                        eliminar = `<button data-id="${data.IdUser}" title="Dar de baja al usuario" type="button" class="btn btn-sm botonGeneral baja small p-1"><i class="ri-delete-bin-2-line p-1"></i>Eliminar</button>`,
                        bloquear = `<button title="${data.Inactivo === 1 ? 'Activar usuario' : 'Desactivar usuario'}" data-id="${data.IdUser}" class="btn btn-sm p-1 botonGeneral bloquear small"><i class="ri-lock-unlock-line p-1"></i>${data.Inactivo === 1 ? 'Activar' : 'Desactivar'}</button>`,
                        cambiar = `<button title="Restablecer password" data-id="${data.IdUser}" class="btn btn-sm botonGeneral p-1 cambiarPass"><i class="ri-key-2-line p-1"></i>Reset</button>`;
    
                    return `${editar} ${bloquear} ${cambiar} ${eliminar}`;
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

       let dataTable = new DataTable("#listaUsuarios", informacion);

       $(document).on('click', '.buscarUsuario', function () {
           let nombre = $('#nombre').val();
           let usuario = $('#usua').val();
           let rol = $('#rol').val();
   
           if (nombre === '' && usuario === '' && rol === '') {
               toastr.warning("Debe seleccionar algún filtro para buscar");
               return;
           }
   
           informacion.ajax.data.nombre = nombre;
           informacion.ajax.data.usuario = usuario;
           informacion.ajax.data.rol = rol;
   
           dataTable.clear().destroy();
           dataTable = new DataTable("#listaUsuarios", informacion);
       });

});