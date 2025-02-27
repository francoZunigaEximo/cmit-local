$(function() {

    //Botón de busqueda de Mapas
    $(document).on('click', '#buscarAsignadosInf', function() {

        $('#LiberarInf').hide();

        let fechaDesde = $('#fechaDesdeAsignadosInf').val(),
            fechaHasta = $('#fechaHastaAsignadosInf').val(),
            nroPrestacion = $('#prestacionAsignados').val()
            /*especialidad = $('#especialidadAsignadosInf').val()*/;

        if ((fechaDesde === '' || fechaHasta === '') && nroPrestacion === '') {
            toastr.warning("Las fechas son obligatorias",'',{timeOut: 1000});
            return;
        }

        /*if (especialidad === '') {
            toastr.warning('Debe seleccionar una especialidad para continuar', 'Atención');
            return;
        }*/

        $('#LiberarInf').show();

        $('#listaOrdenesInformadoresAsig').DataTable().clear().destroy();
        let currentDraw = 1;

        new DataTable("#listaOrdenesInformadoresAsig", {

            searching: false,
            ordering: false,
            processing: true,
            lengthChange: false,
            pageLength: 50,
            deferRender: true,
            responsive: false,
            serverSide: true,
            ajax: {
                url: SEARCHASIGINF,
                data: function(d){
                    d.fechaDesde = $('#fechaDesdeAsignadosInf').val();
                    d.fechaHasta = $('#fechaHastaAsignadosInf').val();
                    d.especialidad = $('#especialidadAsignadosInf').val();
                    d.prestacion = $('#prestacionAsignadosInf').val();
                    d.empresa = $('#empresaAsignadosInf').val();
                    d.paciente = $('#pacienteAsignadosInf').val();
                    d.examen = $('#examenAsignadosInf').val();
                    d.informadores = $('#informadorAsignadoInf').val();
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
                    data: 'Especialidad',
                    name: 'Especialidad',
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
                    data: 'NombreCompleto',
                    name: 'NombreCompleto',
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
                        
                        return `<span class="custom-badge pequeno">Asignado</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data) {
                    
                        return  data.IdItem == 0 || data.IdItem == ''
                            ? `<input type ="checkbox" disabled>`
                            : `<input type="checkbox" name="Id_asignadoInf" value="${data.IdItem}" checked>`;     
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

        function generarCodigo(idprest, idex, idpac) {
            return 'A' + ('000000000' + idprest).slice(-9) + ('00000' + idex).slice(-5) + ('0000000' + idpac).slice(-7) + '.pdf';
        }

    });

    
});