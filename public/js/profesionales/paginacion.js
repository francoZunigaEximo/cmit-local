$(document).ready(() => {

    $('#buscar, #especialidad, #tipo, #opciones').on('change', function() {
        
        $('#listaProf').DataTable().clear().destroy();

        new DataTable("#listaProf", {
            
            searching: false,
            ordering: false,
            processing: true,
            lengthChange: false,
            pageLength: 15,
            responsive: true,
            serverSide: true,
            ajax: {
                url: SEARCH,
                data: function(d){
                    d.tipo = $('#tipo').val();
                    //d.filtro = $('#filtro').select2('data').map(option => option.id);
                    d.opciones = $('#opciones').val();
                    d.especialidad = $('#especialidad').val();
                    d.buscar = $('#buscar').val();
                }
            },       
            dataType: 'json',
            type: 'POST',
            columns: [
                {
                    data: null,
                    render: function(data){
                        return `<input type="checkbox" name="Id" value="${data.IdProfesional}" checked>`;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        return `${data.Apellido} ${data.Nombre}`;
                    }
                },
                {
                    name: 'Documento',
                    data: 'Documento',
                },
                {
                    data: null,
                    render: function(data){
                        
                        let style = (data.TMP === 1 ? 'success' : 'danger');
                        let tipo = (data.TMP === 1 ? 'Múltiples' : data.Proveedor);
                        return `<span class="badge badge-soft-${style} text-uppercase">${tipo}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data){

                        let efector = (data.Efector === 1 ? '<span class="badge badge-soft-success text-uppercase" style="margin-right: 3px; display:block">Efector</span>': ''),
                            informador = (data.Informador === 1 ? '<span class="badge badge-soft-warning text-uppercase" style="margin-right: 3px; display:block">Informador</span>': ''),
                            evaluador = (data.Evaluador === 1 ? '<span class="badge badge-soft-primary text-uppercase" style="margin-right: 3px; display:block">Evaluador</span>': ''),
                            combinado = (data.Combinado === 1 ? '<span class="badge badge-soft-success text-uppercase" style="margin-right: 3px; display:block">Combinado</span>': '');

                        return efector + informador + evaluador + combinado;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        let estados = {
                            1: ['SI', 'success'], 
                            0: ['NO', 'danger']
                        };
                        return `<div style="text-align: center"><span class="badge badge-soft-${estados[data.TMP][1]} text-uppercase">${estados[data.TMP][0]}</span></div>`;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        let estados = {
                            1: ['SI', 'success'], 
                            0: ['NO', 'danger']
                        };
                        return `<div style="text-align: center"><span class="badge badge-soft-${estados[data.Login][1]} text-uppercase">${estados[data.Login][0]}</span></div>`;
                    }
                },
                {
                    data: null,
                    render: function(data){

                        let estados = {1: 'Por Hora', 0: 'Por Prestación'};
                        return `<span class="badge badge-soft-success text-uppercase">${estados[data.Pago]}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data){
                    
                        let estados = { 0: 'Activo', 1: 'Inactivo', 2: 'Baja'};
                        return `<span class="badge badge-soft-success text-uppercase">${estados[data.Estado]}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        return `
                        <div class="d-flex gap-2">
                            <div class="edit">
                                <a title="Editar" href="${location.href}/${data.IdProfesional}/edit">
                                    <button class="btn btn-sm btn-primary" title="Editar"><i class="ri-edit-line"></i></button>
                                </a>
                            </div>
                            <div class="bloquear">
                                <button data-id="${data.IdProfesional}" class="blockProfesional btn btn-sm btn-warning remove-item-btn" title="Inhabilitar">
                                    <i class="ri-forbid-2-line"></i>
                                </button>
                            </div>
                            <div class="remove">
                                <button data-id="${data.IdProfesional}" class="deleteProfesional btn btn-sm btn-danger remove-item-btn" title="Dar de baja">
                                    <i class="ri-delete-bin-2-line"></i>
                                </button>
                            </div>
                        </div>
                        `;
                    }
                }
            ],
            language: {
                processing: "Cargando listado de profesionales de CMIT",
                emptyTable: "No hay profesionales con los datos buscados",
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
                info: "Mostrando _START_ a _END_ de _TOTAL_ de profesionales",
            }
        });

    });

});