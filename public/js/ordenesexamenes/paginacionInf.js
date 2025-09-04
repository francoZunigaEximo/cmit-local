$(function() {

    //Botón de busqueda de Mapas
    $(document).on('click', '#buscarInf', function() {

        let fechaDesde = $('#fechaDesdeInf').val(),
            fechaHasta = $('#fechaHastaInf').val(),
            nroPrestacion = $('#prestacionInf').val();

        if ((!fechaDesde || !fechaHasta) && !nroPrestacion) {
            toastr.warning("Las fechas son obligatorias",'',{timeOut: 1000});
            return;
        }

        var especialidad = $('#especialidadInf').val();

        if (!especialidad && !nroPrestacion) {
            toastr.warning('Debe seleccionar una especialidad para continuar', 'Atención',{timeOut: 1000});
            return;
        }

        $(document).on('change', '#especialidadInf', function(){
            let nuevoValor = $(this).val();

            if(!nuevoValor) return; //evitamos la recarga vacia
            
            if (nuevoValor !== especialidad) {
                especialidad = nuevoValor;
            }
        });

        $('#listaOrdenesInformadores').DataTable().clear().destroy();
        let currentDraw = 1;

        new DataTable("#listaOrdenesInformadores", {

            searching: false,
            ordering: false,
            processing: true,
            lengthChange: false,
            pageLength: 50,
            deferRender: true,
            responsive: false,
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
                        return `<span title="${data.Empresa}">${data.Empresa}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        return `<span title="${data.NombreCompleto}">${ data.NombreCompleto}</span>`;
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
                                ? `<input type="checkbox" name="Id_asigInf" value="${data.IdItem}" checked>`
                                : `<input type ="checkbox" disabled>`;      
                    }
                },
            ],
            language: {
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