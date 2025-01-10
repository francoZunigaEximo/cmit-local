$(document).ready(()=>{

    //Botón de busqueda de Mapas
    $(document).on('click', '#buscarEEnviar, .completo, .abiertoEE, .cerradoEE, .impagoEE', function() {

        let fechaDesde = $('#fechaDesdeEEnviar').val(),
            fechaHasta = $('#fechaHastaEEnviar').val(),
            empresa = $('#empresaEEnviar').val(),
            paciente = $('#pacienteEEnviar').val(),
            eenviar = $('#eEnviarEEnviar').val(),
            completo = $(this).hasClass('completo') ? "activo" : null,
            abierto = $(this).hasClass('abiertoEE') ? "activo" : null,
            cerrado = $(this).hasClass('cerradoEE') ? "activo" : null,
            impago = $(this).hasClass('impagoEE') ? "activo" : null;

        if ((completo !== 'activo' || abierto !== 'activo' || cerrado !== 'activo' || impago !== 'activo') && (fechaDesde === '' || fechaHasta === '')) {
            toastr.warning("Las fechas son obligatorias");
            return;
        }
        console.log(eenviar)
        $('#listaOrdenesEEnviar').DataTable().clear().destroy();

        new DataTable("#listaOrdenesEEnviar", {

            searching: false,
            ordering: false,
            processing: true,
            lengthChange: false,
            pageLength: 50,
            deferRender: true,
            responsive: false,
            serverSide: true,
            ajax: {
                url: SEARCHEENVIAR,
                data: function(d){
                    d.fechaDesde = fechaDesde;
                    d.fechaHasta = fechaHasta;
                    d.empresa = empresa;
                    d.paciente = paciente;
                    d.eenviar = eenviar;
                    d.completo = completo;
                    d.abierto = abierto;
                    d.cerrado = cerrado;
                    d.impago = impago;
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
                        console.log(data);
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

                        return `<span title="${data.Empresa}">${data.Empresa}</span>`;
                    }
                },
                {
                    data: null,
                    targets: 3,
                    render: function(data) {
                        return  `<span title="${data.NombreCompleto}">${data.NombreCompleto}</span>`; 
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
                        return [undefined, null, '0000-00-00'].includes(data.FechaEnviado) ? '-' : fechaNow(data.FechaEnviado, "/", 0);
                    }
                },
                {
                    data: null,
                    targets: 6,
                    render: function(data){
                        return `<span title="${data.Correo}">${[undefined, null].includes(data.Correo) ? '-' : data.Correo}</span>`;
                    } 
                },
                {
                    data: null,
                    targets: 7,
                    render: function(data) {
                        let subqueryData = null;
        
                        $.ajax({
                            url: getPagado,
                            method: 'GET',
                            data: { Id: data.IdPrestacion }, 
                            async: false, 
                            success: function(response) {
                                subqueryData = response; 
                            }
                        });
                        return  `<div class="text-center"><span class="${subqueryData.Pagado === 0 ? 'rojo' : ''}">${subqueryData.Pagado === 0 ? 'X' : ''}</span></div>`;    
                    }
                },
                {
                    data: null,
                    targets: 8,
                    render: function(data) {
                        return `<input type="checkbox" name="Id_EEnviar" value="${data.IdPrestacion}" checked>`       
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

    });

    
});