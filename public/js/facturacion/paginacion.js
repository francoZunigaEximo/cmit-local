$(document).ready(function(){

    $(document).on('click', '.Hoy', function(e){
        e.preventDefault();
        let base = $('#listaFacturas');
        let hoy = new Date().toISOString().slice(0, 10);
        let fechaDesde = hoy,
            fechaHasta = hoy;
            
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
                url: SEARCH,
                data: function(d){
                    d.FechaDesde = fechaDesde;
                    d.FechaHasta = fechaHasta;
                    d.Tipo = "facturas";
                }
            },
            dataType: 'json',
            type: 'POST',
            columns: [
                {
                    data: null,
                    render: function(data){            
                        return `<div class="text-center"><input type="checkbox" name="Id_factura" value="${data.Id}" data-opcion="${data.Opcion}"></div>`;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        let id = parseInt(data.Id), total = parseInt(data.Total);
                        let html = (id.toString().padStart(6, '0')) + ' ';
        
                        if (total > 0) {
                            html += '<span class="badge bg-success">Nota de Crédito</span>';
                        }
        
                        return html;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        return data.Tipo + (data.Sucursal).toString().padStart(4, '0') + (data.NroFactura).toString().padStart(8, '0');
                    }
                },
                {
                    data: null,
                    render: function(data){
                        return fechaNow(data.Fecha, '/', 0);
                    }
                },
                {
                    data: null,
                    render: function(data){
                        return `<span title="${data.RazonSocial}">${acortadorTexto(data.RazonSocial, 18)}</span>`;
                    }
                },
                {
                    data: 'Identificacion',
                    name: 'Identificacion',
                },
                {
                    data: null,
                    width: "50px",
                    render: function(data){            
                        
                        return `<button data-id="${data.Id}" data-opcion="${data.Opcion}" class="btn btn-sm iconoGeneral editar" title="Editar"><i class="ri-edit-line p-1"></i></button>
                        <button data-id="${data.Id}" data-opcion="${data.Opcion}" class="btn btn-sm iconoGeneral anular" title="Anular"><i class="ri-forbid-2-line p-1"></i></button>
                        <button data-id="${data.Id}" data-opcion="${data.Opcion}" class="btn btn-sm iconoGeneral eliminar" title="Eliminar"><i class="ri-delete-bin-2-line p-1"></i></button>
                        `;
                    }
                },
            ],
            language: {
                processing: "<div style='text-align: center; margin-top: 20px;'><img src='./images/spinner.gif' /><p>Cargando...</p></div>",
                emptyTable: "No hay facturas con los datos buscados",
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
                info: "Mostrando _START_ a _END_ de _TOTAL_ de facturas",
            },
            createdRow(row, data, dataIndex){
                if(data.Pagado === 0 || data.Pagado === '0') {
                    $(row).addClass('fondo-rojo');
                }
            }
        });
    });

    $(document).on('click', '.buscar', function(e){
        e.preventDefault();
        let base = $('#listaFacturas');
        let facturaDesde = $('#facturaDesde').val(),
            facturaHasta = $('#facturaHasta').val(),
            tabla = $('#tabla').val(),
            empresa = $('#empresa').val(),
            art = $('#art').val(),
            fechaDesde = $('#fechaDesde').val(),
            fechaHasta = $('#fechaHasta').val();
        
        if([null, undefined, ''].includes(tabla)){
            toastr.warning('Seleccione un Ver Tabla de factura. El campo es obligatorio');
            return;
        }

        if(fechaDesde !== '' && fechaHasta === ''){
            toastr.warning('Seleccione una fecha hasta. El campo es obligatorio si agrega una fecha desde');
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
                url: SEARCH,
                data: function(d){
                    d.FechaDesde = fechaDesde;
                    d.FechaHasta = fechaHasta;
                    d.Tipo = tabla;
                    d.FacturaDesde = facturaDesde;
                    d.FacturaHasta = facturaHasta;
                    d.Empresa = empresa;
                    d.Art = art;
                }
            },
            dataType: 'json',
            type: 'POST',
            columns: [
                {
                    data: null,
                    render: function(data){            
                        return `<div class="text-center"><input type="checkbox" name="Id_factura" value="${data.Id}" data-opcion="${data.Opcion}"></div>`;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        let id = parseInt(data.Id), total = parseInt(data.Total);
                        let html = (id.toString().padStart(6, '0')) + ' ';
        
                        if (total > 0) {
                            html += '<span class="badge bg-success">Nota de Crédito</span>';
                        }
        
                        return html;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        return data.Tipo + (data.Sucursal).toString().padStart(4, '0') + (data.NroFactura).toString().padStart(8, '0');
                    }
                },
                {
                    data: null,
                    render: function(data){
                        return fechaNow(data.Fecha, '/', 0);
                    }
                },
                {
                    data: null,
                    render: function(data){
                        return `<span title="${data.RazonSocial}">${acortadorTexto(data.RazonSocial, 18)}</span>`;
                    }
                },
                {
                    data: 'Identificacion',
                    name: 'Identificacion',
                },
                {
                    data: null,
                    width: "50px",
                    render: function(data){            
                        
                        return `<button data-id="${data.Id}" data-opcion="${data.Opcion}" class="btn btn-sm iconoGeneral editar" title="Editar"><i class="ri-edit-line p-1"></i></button>
                        <button data-id="${data.Id}" data-opcion="${data.Opcion}" class="btn btn-sm iconoGeneral anular" title="Anular"><i class="ri-forbid-2-line p-1"></i></button>
                        <button data-id="${data.Id}" data-opcion="${data.Opcion}" class="btn btn-sm iconoGeneral eliminar" title="Eliminar"><i class="ri-delete-bin-2-line p-1"></i></button>
                        `;
                    }
                },
            ],
            language: {
                processing: "<div style='text-align: center; margin-top: 20px;'><img src='./images/spinner.gif' /><p>Cargando...</p></div>",
                emptyTable: "No hay facturas con los datos buscados",
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
                info: "Mostrando _START_ a _END_ de _TOTAL_ de facturas",
            },
            createdRow(row, data, dataIndex){
                if(data.Pagado === 0 || data.Pagado === '0') {
                    $(row).addClass('fondo-rojo');
                }
            }
        });
    });

    $(document).on('click', '.FacturasSN', function(e){
        e.preventDefault();
        let base = $('#listaFacturas');

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
                url: SEARCH,
                data: function(d){
                    d.Cero = true;
                    d.Tipo = null;
                }
            },
            dataType: 'json',
            type: 'POST',
            columns: [
                {
                    data: null,
                    render: function(data){            
                        return `<div class="text-center"><input type="checkbox" name="Id_factura" value="${data.Id}" data-opcion="${data.Opcion}"></div>`;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        let id = parseInt(data.Id), total = parseInt(data.Total);
                        let html = (id.toString().padStart(6, '0')) + ' ';
        
                        if (total > 0) {
                            html += '<span class="badge bg-success">Nota de Crédito</span>';
                        }
        
                        return html;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        return data.Tipo + (data.Sucursal).toString().padStart(4, '0') + (data.NroFactura).toString().padStart(8, '0');
                    }
                },
                {
                    data: null,
                    render: function(data){
                        return fechaNow(data.Fecha, '/', 0);
                    }
                },
                {
                    data: null,
                    render: function(data){
                        return `<span title="${data.RazonSocial}">${acortadorTexto(data.RazonSocial, 18)}</span>`;
                    }
                },
                {
                    data: 'Identificacion',
                    name: 'Identificacion',
                },
                {
                    data: null,
                    width: "50px",
                    render: function(data){            
                        
                        return `<button data-id="${data.Id}" data-opcion="${data.Opcion}" class="btn btn-sm iconoGeneral editar" title="Editar"><i class="ri-edit-line p-1"></i></button>
                        <button data-id="${data.Id}" data-opcion="${data.Opcion}" class="btn btn-sm iconoGeneral anular" title="Anular"><i class="ri-forbid-2-line p-1"></i></button>
                        <button data-id="${data.Id}" data-opcion="${data.Opcion}" class="btn btn-sm iconoGeneral eliminar" title="Eliminar"><i class="ri-delete-bin-2-line p-1"></i></button>
                        `;
                    }
                },
            ],
            language: {
                processing: "<div style='text-align: center; margin-top: 20px;'><img src='./images/spinner.gif' /><p>Cargando...</p></div>",
                emptyTable: "No hay facturas con los datos buscados",
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
                info: "Mostrando _START_ a _END_ de _TOTAL_ de facturas",
            },
            createdRow(row, data, dataIndex){
                if(data.Pagado === 0 || data.Pagado === '0') {
                    $(row).addClass('fondo-rojo');
                }
            }
        });
        
    })

});