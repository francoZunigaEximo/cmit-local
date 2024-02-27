$(document).ready(()=>{

    //Botón de busqueda de Mapas
    $(document).on('click', '#buscarAdjuntoInf', function() {

        let fechaDesde = $('#fechaDesdeAdjuntoInf').val(),
            fechaHasta = $('#fechaHastaAdjuntoInf').val();

        if (fechaDesde === '' || fechaHasta === '') {
            toastr.warning("Las fechas son obligatorias", "Atención");
            return;
        }

        $('#listaOrdenesInformadoresAdj').DataTable().clear().destroy();

        new DataTable("#listaOrdenesInformadoresAdj", {

            searching: false,
            ordering: false,
            processing: true,
            lengthChange: false,
            pageLength: 50,
            responsive: true,
            serverSide: true,
            ajax: {
                url: SEARCHADJINF,
                data: function(d){
                    d.fechaDesde = $('#fechaDesdeAdjuntoInf').val();
                    d.fechaHasta = $('#fechaHastaAdjuntoInf').val();
                    d.especialidad = $('#especialidadAdjuntoInf').val();
                    d.empresa = $('#empresaAdjuntoInf').val();
                    d.efectores = $('#informadorAdjuntoInf').val();
                    d.art = $('#artAdjuntoInf').val();
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
                        let recorte = (data.Especialidad).substring(0,10) + "...";
                        return recorte.length >= 10 ? `<span title="${data.Especialidad}">${recorte}</span>` : data.Especialidad;
                    }
                },
                {
                    data: 'IdPrestacion',
                    name: 'IdPrestacion',
                },
                {
                    data: null,
                    render: function(data) {

                        let recorte = (data.Empresa).substring(0,10) + "...";
                        return recorte.length >= 10 ? `<span title="${data.Empresa}">${recorte}</span>` : data.Empresa;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        let recorte = (data.NombreCompleto).substring(0,10) + "...";
                        return recorte.length >= 10 ? `<span title="${data.NombreCompleto}">${recorte}</span>` : data.NombreCompleto;
                    }
                },
                {
                    data: 'Documento',
                    name: 'Documento',
                },
                {
                    data: null,
                    render: function(data) {
                        let recorte = (data.Examen).substring(0,10) + "...";
                        return recorte.length >= 10 ? `<span title="${data.Examen}">${recorte}</span>` : data.Examen;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        let recorte = (data.NombreProfesional).substring(0, 10) + "...";
                        return recorte.length >= 10 ? `<span title="${data.NombreProfesional}">${recorte}</span>` : data.NombreProfesional;
                    }
                },
                {
                    data: null,
                    render: function(data) {

                        let abierto = [0, 1, 2], cerrado = [3, 4, 5], estatus = data.Estado;

                        let mostrar = abierto.includes(estatus)
                                        ? 'Abierto'
                                        : cerrado.includes(estatus)
                                            ? 'Cerrado'
                                            : 'sin datos';

                        return `<span class="custom-badge pequeno">${mostrar}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data) {
                        
                        return  `<input type="checkbox" name="Id_adjuntoInf" value="${data.IdItem}" checked>`;      
                    }
                },
                {
                    data: null,
                    render: function(data){

                        let masivo = `<span title="Subir automáticamente el reporte" class="custom-badge iconGeneral"><i class="ri-file-upload-line"></i></span>`,
                            individual = `<span data-id="${data.IdItem}" data-idprestacion="${data.IdPrestacion}" data-tipo="informador" title="Subir manualmente el reporte" class="custom-badge iconGeneral uploadFile"><i class="ri-folder-line"></i></span><input type="file" class="fileManual" style="display: none;">`;

                        return `${masivo} ${individual}`;
                    }
                }
            ],
            language: {
                processing: "Cargando listado de examenes de CMIT",
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