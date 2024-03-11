$(document).ready(()=>{

    //Botón de busqueda de Mapas
    $(document).on('click', '#buscarInf', function() {

        let fechaDesde = $('#fechaDesdeInf').val(),
            fechaHasta = $('#fechaHastaInf').val(),
            nroPrestacion = $('#prestacionInf').val();

        if ((fechaDesde === '' || fechaHasta === '') && nroPrestacion === '') {
            toastr.warning("Las fechas son obligatorias");
            return;
        }

        var especialidad = $('#especialidadInf').val();

        if (especialidad === '') {
            toastr.warning('Debe seleccionar una especialidad para continuar', 'Atención');
            return;
        }

        $(document).on('change', '#especialidadInf', function(){
            var nuevoValor = $(this).val();
            
            if (nuevoValor !== especialidad) {

                especialidad = nuevoValor;

            }
        });

        $('#listaOrdenesInformadores').DataTable().clear().destroy();

        new DataTable("#listaOrdenesInformadores", {

            searching: false,
            ordering: false,
            processing: true,
            lengthChange: false,
            pageLength: 50,
            deferRender: true,
            responsive: true,
            serverSide: true,
            ajax: {
                url: SEARCHINF,
                data: function(d){
                    d.fechaDesde = $('#fechaDesdeInf').val();
                    d.fechaHasta = $('#fechaHastaInf').val();
                    d.especialidad = $('#especialidadInf').val();
                    d.prestacion = $('#prestacionInf').val();
                    d.empresa = $('#empresaInf').val();
                    d.paciente = $('#pacienteInf').val();
                    d.examen = $('#examenInf').val();
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
                        let recorte = (data.Especialidad).substring(0.15) + "...";
                        return recorte.length >=15 ? `<span title="${data.Especialidad}">${recorte}</span>` : data.Especialidad;
                        
                    }
                },
                {
                    data: 'IdPrestacion',
                    name: 'IdPrestacion',
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
                        let recorte = (data.NombreCompleto).substring(0,15) + "...";
                        return recorte.length >= 15 ? `<span title="${data.NombreCompleto}">${recorte}</span>` : data.NombreCompleto;
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
                                ? `<input type="checkbox" name="Id_asigInf" value="${data.IdItem}" checked>`
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