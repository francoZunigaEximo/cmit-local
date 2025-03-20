$(document).ready(function(){

    $(document).on('click', '#buscar', function(){
        
        let especialidad = $('#especialidad').val(), opciones = $('#opciones').val();

        if(especialidad === '' && opciones === ''){

            toastr.warning("Debe escribir que desea buscar o bien seleccionar un filtro", "Atención");
            return;
        } 
 
        $('#listaEspecialidades').DataTable().clear().destroy();

        $('#especialidad, #opciones').val("");

        new DataTable("#listaEspecialidades", {

            searching: false,
            ordering: false,
            processing: true,
            lengthChange: false,
            deferRender: true,
            pageLength: 100,
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
                        return `<div class="text-center"><input type="checkbox" name="Id" value="${data.IdEspecialidad}"></div>`;
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
                                    <button class="btn btn-sm iconGeneral" title="Editar"><i class="ri-edit-line"></i></button>
                                </a>
                            </div>
                            <div class="bloquear">
                                <button data-id="${data.IdEspecialidad}" class="blockEsp btn btn-sm iconGeneral" title="Inhabilitar">
                                    <i class="ri-forbid-2-line"></i>
                                </button>
                            </div>
                        </div>
                        `;
                    }
                    
                }
            ],
            language: {
                processing: "<div style='text-align: center; margin-top: 20px;'><img src='./images/spinner.gif' /><p>Cargando...</p></div>",
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