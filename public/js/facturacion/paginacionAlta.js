$(document).ready(function() {

    $(document).on('click', '.buscarAlta', function(e) {
        e.preventDefault();
        const base = "#listaFacturasAlta";
        let fechaDesde = $('#fechaDesdeA').val(),
            fechaHasta = $('#fechaHastaA').val(),
            empresa = $('#empresa').val(),
            pago = $('#pago').val(),
            tipo = $('#tipo').val();
    
        if(fechaDesde === '' || fechaHasta === ''){
            toastr.warning('Seleccione un rango de fecha');
            return;
        }

        if([null, undefined, '', 0].includes(empresa)) {
            toastr.warning("Debe seleccionar una empresa para listar las prestaciones");
            return;
        }

        $(base).DataTable().clear().destroy();
        new DataTable(base, {
    
            searching: false,
            ordering: false,
            processing: true,
            lengthChange: false,
            pageLength: 50,
            responsive: true,
            serverSide: true,
            deferRender: true,
            ajax: {
                url: SEARCHALTA,
                data: function(d){
                    d.FechaDesde = fechaDesde;
                    d.FechaHasta = fechaHasta;
                    d.Tipo = tipo;
                    d.Empresa = empresa;
                    d.Pago = pago;
                }
            },
            dataType: 'json',
            type: 'POST',
            columns: [
                {
                    data: null,
                    render: function(data){            
                        return `<div class="text-center"><input type="checkbox" name="Id_factura" value="${data.Id}"></div>`;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        return fechaNow(data.Fecha, "/", 0);
                    }
                },
                {
                    data: null,
                    render: function(data){
                        return `<div class="text-center">${data.Id}</div>`;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        return `<div class="text-center" title="${data.Empresa}">${acortadorTexto(data.Empresa, 10)}</div>`;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        return `<div class="text-center" title="${data.Art}">${acortadorTexto(data.Art, 10)}</div>`;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        return `<div class="text-center">${data.TipoPrestacion}</div>`;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        return `<div class="text-center">${data.Nombre} ${data.Apellido}</div>`;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        return `<div class="text-center">${tipoSPagoPrestacion(data.SPago)}</div>`;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        return `<div class="text-center">${[0,null,'',undefined].includes(data.CCosto) ? '-' : data.CCosto}</div>`;
                    }
                },
                {
                    data: null,
                    width: "50px",
                    render: function(data){            
                        
                        return `<button data-id="${data.Id}" class="btn btn-sm iconoGeneral ver" title="Ver"><i class="ri-eye-line p-1"></i></button>`;
                    }
                },
            ],
            language: {
                processing: "<div style='text-align: center; margin-top: 20px;'><img src='../images/spinner.gif' /><p>Cargando...</p></div>",
                emptyTable: "No hay prestaciones con los datos buscados",
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
                info: "Mostrando _START_ a _END_ de _TOTAL_ de prestaciones",
            },
        });
        
    });
});