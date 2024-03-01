$(document).ready(()=>{

    //Botón de busqueda de Mapas
    $(document).on('click', '#buscarAsignadosInf', function() {

        $('#LiberarInf').hide();

        let fechaDesde = $('#fechaDesdeAsignadosInf').val(),
            fechaHasta = $('#fechaHastaAsignadosInf').val(),
            nroPrestacion = $('#prestacionAsignados').val();

        if ((fechaDesde === '' || fechaHasta === '') && nroPrestacion === '') {
            toastr.warning("Las fechas son obligatorias");
            return;
        }

        $('#LiberarInf').show();

        $('#listaOrdenesInformadoresAsig').DataTable().clear().destroy();

        new DataTable("#listaOrdenesInformadoresAsig", {

            searching: false,
            ordering: false,
            processing: true,
            lengthChange: false,
            pageLength: 50,
            deferRender: true,
            responsive: true,
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
                    data: 'Especialidad',
                    name: 'Especialidad',
                },
                {
                    data: 'IdPrestacion',
                    name: 'IdPrestacion',
                },
                {
                    data: null,
                    render: function(data) {

                        let recorte = (data.Empresa).substring(0,20) + "...";
                        return recorte.length >= 20 ? `<span title="${data.Empresa}">${recorte}</span>` : data.Empresa;
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
                        let recorte = (data.Examen).substring(0,20) + "...";
                        return recorte.length >= 20 ? `<span title="${data.Examen}">${recorte}</span>` : data.Examen;
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