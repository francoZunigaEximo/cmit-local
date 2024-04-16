$(document).ready(()=>{

    //Botón de busqueda de Mapas
    $(document).on('click', '#buscar', function() {

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

        $('#listaOrdenesEfectores').DataTable().clear().destroy();
        let currentDraw = 1;

        new DataTable("#listaOrdenesEfectores", {

            searching: false,
            ordering: false,
            processing: true,
            lengthChange: false,
            pageLength: 50,
            deferRender: true,
            responsive: true,
            serverSide: true,
            ajax: {
                url: SEARCH,
                data: function(d){
                    d.fechaDesde = $('#fechaDesde').val();
                    d.fechaHasta = $('#fechaHasta').val();
                    d.especialidad = $('#especialidad').val();
                    d.prestacion = $('#prestacion').val();
                    d.empresa = $('#empresa').val();
                    d.paciente = $('#paciente').val();
                    d.examen = $('#examen').val();
                    d.page = d.start / d.length + 1;
                },
                dataSrc: function (response) {
                    let data = {
                        draw: currentDraw,
                        recordsTotal: response.total,
                        recordsFiltered: response.total,
                        data: response.data,
                    };
    
                    currentDraw++;
    
                    return data.data;
                },
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
                        let recorte = (data.Especialidad).substring(0.15) + "...";
                        return recorte.length >=15 ? `<span title="${data.Especialidad}">${recorte}</span>` : data.Especialidad;
                        
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

                        let recorte = (data.Empresa).substring(0,15) + "...";
                        return recorte.length >= 15 ? `<span title="${data.Empresa}">${recorte}</span>` : data.Empresa;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        let NombreCompleto = data.pacNombre + ' ' + data.pacApellido;
                        let recorte = (NombreCompleto).substring(0,15) + "...";
                        return recorte.length >= 15 ? `<span title="${NombreCompleto}">${recorte}</span>` : NombreCompleto;
                    }
                },
                {
                    data: 'Documento',
                    name: 'Documento',
                },
                {
                    data: null,
                    render: function(data) {
                        let recorte = (data.Examen).substring(0,20) + "...";
                        return recorte.length >= 20 ? `<span title="${data.Examen}">${recorte}</span>` : data.Examen;
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