$(document).ready(function(){

    $(document).on('click', '#buscar', function(){
        
        let especialidad = $('#especialidad').val(), opciones = $('#opciones').val();

        if(especialidad === '' && opciones === '') return;
 
        $('#listaEspecialidades').DataTable().clear().destroy();

        new DataTable("#listaEspecialidades", {

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
                    d.especialidad = especialidad;
                    d.opciones = opciones;
                }
            },     
            dataType: 'json',
            type: 'POST',
            columns:  [
                {
                    data: null,
                    render: function(data){
                        return `<input type="checkbox" name="Id" value="${data.IdEspecialidad}" checked>`;
                    }
                },
                {
                    name: 'Nombre',
                    data: 'Nombre'
                },
                {
                    data: null,
                    render: function(data){
                        return `<span class="badge badge-soft-success text-uppercase">${(data.Ubicacion === 0) ? 'Interno' : 'Externo' }</span>`;
                    }
                },
                {
                    name: 'Telefono',
                    data: 'Telefono'
                },
                {
                    name: 'Direccion',
                    data: 'Direccion'
                },
                {
                    name: 'NombreLocalidad',
                    data: 'NombreLocalidad'
                },      
                {
                    data: null,
                    render: function(data){
                        return `<span class="badge badge-soft-warning text-uppercase">${(data.Adjunto === 0) ? 'Simple' : 'Multiple'}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        return `<span class="badge badge-soft-success text-uppercase">${(data.Examen === 0) ? 'Simple' : 'Multiple' }</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        return `<span class="badge badge-soft-warning text-uppercase">${(data.Informe === 0) ? 'Simple' : 'Multiple'}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data){

                        return `
                        <div class="d-flex gap-2">
                            <div class="edit">
                                <a title="Editar" href="${location.href}/${data.IdEspecialidad}/edit">
                                    <button class="btn btn-sm btn-primary" title="Editar"><i class="ri-edit-line"></i></button>
                                </a>
                            </div>
                            <div class="bloquear">
                                <button data-id="${data.IdEspecialidad}" class="blockEsp btn btn-sm btn-warning remove-item-btn" title="Inhabilitar">
                                    <i class="ri-forbid-2-line"></i>
                                </button>
                            </div>
                        </div>
                        `;
                    }
                    
                }
            ],
            language: {
                processing: "Cargando listado de especialidades de CMIT",
                emptyTable: "No hay especialidades con los datos buscados",
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
                info: "Mostrando _START_ a _END_ de _TOTAL_ de especialidades",
            }
        });
    });
});