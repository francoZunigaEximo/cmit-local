$(function() {

    //Botón de busqueda de Mapas
    $(document).on('click', '#buscar', function(e) {
        e.preventDefault();

        let fechaDesde = $('#fechaDesde').val(),
            fechaHasta = $('#fechaHasta').val(),
            prestacion = $('#prestacion').val();

        if ((fechaDesde === '' || fechaHasta === '') && prestacion === '') {
            toastr.warning("Las fechas son obligatorias", "Atención");
            return;
        }

        var especialidad = $('#especialidad').val();

        $(document).on('change', '#especialidad', function(){
            var nuevoValor = $(this).val();
            
            if (nuevoValor !== especialidad) {

                especialidad = nuevoValor;

            }
        });

        if (especialidad === '') {
            toastr.warning("Debe seleccionar una especialidad");
            return;
        }

        $('#listaOrdenesEfectores').DataTable().clear().destroy();

        new DataTable("#listaOrdenesEfectores", {

            searching: false,
            ordering: false,
            processing: true,
            lengthChange: false,
            pageLength: 50,
            deferRender: true,
            responsive: false,
            scrollCollapse: true,
            serverSide: true,
            ajax: {
                url: SEARCH,
                data: function(d){
                    d.fechaDesde = fechaDesde;
                    d.fechaHasta = fechaHasta;
                    d.especialidad = $('#especialidad').val();
                    d.prestacion = prestacion;
                    d.empresa = $('#empresa').val();
                    d.paciente = $('#paciente').val();
                    d.examen = $('#examen').val();
                }
            },
            dataType: 'json',
            type: 'POST',
            columns: [
                {
                    data: null,
                    render: function(data){
                        return fechaNow(data.Fecha,'/',0);
                    }
                },
                {
                    data: null,
                    render: function(data){
                        return `<span title="${data.Especialidad}">${data.Especialidad}</span>`;
                        
                    }
                },
                {
                    data: null,
                    render: function(data) {
                        return `<div class="text-center"><a href="${linkPrestaciones}/${data.IdPrestacion}/edit" target="_blank">${data.IdPrestacion}</a></div>`;
                    } 
                },
                {
                    data: null,
                    render: function(data) {
                        return `<span title="${data.Empresa}">${data.Empresa}</span>`}
                },
                {
                    data: null,
                    render: function(data){
                        let NombreCompleto = data.pacNombre + ' ' + data.pacApellido;
                        return `<span title="${NombreCompleto}">${NombreCompleto}</span>`;
                    }
                },
                {
                    data: 'Documento',
                    name: 'Documento',
                },
                {
                    data: null,
                    render: function(data) {
                        return `<span title="${data.Examen}">${data.Examen}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data) {
                        
                        return  especialidad == 0 || especialidad == ''
                            ? `<input type ="checkbox" disabled>`
                            : data.IdEspecialidad == especialidad
                                ? `<input type="checkbox" name="Id_asignar" value="${data.IdItem}" checked>`
                                : `<input type ="checkbox" disabled>`;      
                    }
                },
            ],
            language: {
                processing: "<div style='text-align: center; margin-top: 20px;'><img src='./images/spinner.gif' /><p>Cargando...</p></div>",
                emptyTable: "No hay examenes con los datos buscados",
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
                info: "Mostrando _START_ a _END_ de _TOTAL_ de examenes",
            }
        });

        function fechaNow(fechaAformatear, divider, format) {
            let dia, mes, anio; 
        
            if (fechaAformatear === null) {
                let fechaHoy = new Date();
        
                dia = fechaHoy.getDate().toString().padStart(2, '0');
                mes = (fechaHoy.getMonth() + 1).toString().padStart(2, '0');
                anio = fechaHoy.getFullYear();
            } else {
                let nuevaFecha = fechaAformatear.split("-"); 
                dia = nuevaFecha[0]; 
                mes = nuevaFecha[1]; 
                anio = nuevaFecha[2];
            }
        
            return (format === '0') ? `${dia}${divider}${mes}${divider}${anio}` : `${anio}${divider}${mes}${divider}${dia}`;
        }

    });
});