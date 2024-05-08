$(document).ready(()=>{

    //Botón de busqueda de Mapas
    $(document).on('click', '#buscarEEnviar, .completo', function() {

        let fechaDesde = $('#fechaDesdeEEnviar').val(),
            fechaHasta = $('#fechaHastaEEnviar').val(),
            empresa = $('#empresaEEnviar').val(),
            paciente = $('#pacienteEEnviar').val(),
            enviar = $('#eEnviarEEnviar').val(),
            completo = $(this).hasClass('completo') ? "activo" : null;

        

        if (completo !== 'activo' && (fechaDesde === '' || fechaHasta === '')) {
            toastr.warning("Las fechas son obligatorias");
            return;
        }

        $('#listaOrdenesEEnviar').DataTable().clear().destroy();

        new DataTable("#listaOrdenesEEnviar", {

            searching: false,
            ordering: false,
            processing: true,
            lengthChange: false,
            pageLength: 50,
            deferRender: true,
            responsive: true,
            serverSide: true,
            ajax: {
                url: SEARCHEENVIAR,
                data: function(d){
                    d.fechaDesde = fechaDesde;
                    d.fechaHasta = fechaHasta;
                    d.empresa = empresa;
                    d.paciente = paciente;
                    d.eenviar = enviar;
                    d.completo = completo;
                },
            },
            dataType: 'json',
            type: 'POST',
            columnDefs: [
                {
                    data: null,
                    targets: 0,
                    width: "50px",
                    render: function(data){
                        return fechaNow(data.Fecha,'/',0);
                    }
                },
                {
                    data: null,
                    targets: 1,
                    width: "70px",
                    render: function(data) {
                        return `<div class="text-center"><a href="${linkPrestaciones}/${data.IdPrestacion}/edit" target="_blank">${data.IdPrestacion}</a></div>`;
                    } 
                },
                {
                    data: null,
                    targets: 2,
                    render: function(data) {

                        return `<span title="${data.Empresa}">${acortadorTexto(data.Empresa, 12)}</span>`;
                    }
                },
                {
                    data: null,
                    targets: 3,
                    render: function(data) {
                        return  `<span title="${data.NombreCompleto}">${acortadorTexto(data.NombreCompleto, 10)}</span>`; 
                    }
                },
                {
                    data: 'Documento',
                    targets: 4,
                    name: 'Documento',
                },
                {
                    data: null,
                    targets: 5,
                    render: function(data) {
                        return `<span title="${data.Examen}">${acortadorTexto(data.Examen, 12)}</span>`;
                    }
                },
                {
                    data: null,
                    targets: 6,
                    render: function(data) {
                        return [undefined, null, '0000-00-00'].includes(data.FechaEnviado) ? '-' : fechaNow(data.FechaEnviado, "/", 0);
                    }
                },
                {
                    data: null,
                    targets: 7,
                    render: function(data){
                        return `<span title="${data.Correo}">${[undefined, null].includes(data.Correo) ? '-' : acortadorTexto(data.Correo, 15)}</span>`;
                    } 
                },
                {
                    data: null,
                    targets: 8,
                    render: function(data) {
                        return  `<div class="text-center"><span class="${data.Pagado === 0 ? 'rojo' : ''}">${data.Pagado === 0 ? 'X' : ''}</span></div>`;    
                    }
                },
                {
                    data: null,
                    targets: 9,
                    render: function(data) {
                        return `<input type="checkbox" name="Id_EEnviar" value="${data.IdEx}" checked>`       
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

        function acortadorTexto(cadena, nroCaracteres = 10) {
            return cadena.length <= nroCaracteres ? cadena : cadena.substring(0,nroCaracteres);
        }

    });

    
});