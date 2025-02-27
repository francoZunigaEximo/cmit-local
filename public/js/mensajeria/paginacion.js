$(function(){
 
    $(document).on('click', '.buscar', function(e){

        e.preventDefault();
        let nroDesde = $('#nroDesde').val(),
            nroHasta = $('#nroHasta').val(),
            tipo = $('#tipo').val(),
            pago = $('#pago').val(),
            estado = $('#estado').val(),
            fechaDesde = $('#fechaDesde').val(),
            fechaHasta = $('#fechaHasta').val(),
            bloqueado = $('#bloqueado').val();

        if([null, '', 0].includes(fechaHasta) || (![null, '', 0].includes(fechaDesde) && [null, '', 0].includes(fechaHasta))) {
            toastr.warning("La fecha hasta es obligatoria. Tampoco puede faltar si la fecha desde esta incluida", "", {timeOut: 1000});
            return;
        }

        $('#listaMensaje').DataTable().clear().destroy();

        new DataTable("#listaMensaje", {
        
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
                    d.NroDesde = nroDesde;
                    d.NroHasta = nroHasta;
                    d.Tipo = tipo;
                    d.Estado = estado;
                    d.Pago = pago;
                    d.FechaDesde = fechaDesde;
                    d.FechaHasta = fechaHasta;
                    d.Bloqueado = bloqueado;
                }
            },
            dataType: 'json',
            type: 'POST',
            columns: [
                {
                    data: null,
                    width: "30px",
                    render: function(data){
                        let codigo = (data.Id).toString().padStart(6, '0');
                        return codigo;
                    },
                    style: {
                        fontSize: '1px'
                    }
                },
                {
                    data: null,
                    width: "80px",
                    render: function(data){
                        let nombre = data.RazonSocial;
                        return `<span class="text-uppercase" title="${nombre}">${acortadorTexto(nombre, 15)}</span>`;
                    }
                },
                {
                    data: null,
                    width: "80px",
                    render: function(data){
                        let nombre = data.ParaEmpresa;
                        return `<span class="text-uppercase" title="${nombre}">${acortadorTexto(nombre, 15)}</span>`;
                    }
                },
                {
                    data: 'Identificacion',
                    name: 'Identificacion',
                    width: "100px",
                },
                {
                    data: null,
                    width: "100px",
                    render: function(data){
                        let tipo = {
                            A: "ART",
                            E: "Empresa"
                        };
                        let valor = data.TipoCliente;
                    return `<div class="text-center"><span>${tipo[valor]}</span></div>`;
                    }
                },
                {
                    data: null,
                    width: "120px",
                    render: function(data){
                        let tipo = {
                            A: "CC",
                            B: "Ctdo",
                            C: "Ctdo(CC Bloq)",
                        };
                        let valor = data.FPago;
                    return `<div class="text-center"><span>${[undefined, null, ''].includes(valor) ? 'CC' : tipo[valor]}</span></div>`;
                    }
                },
                {
                    data: null,
                    width: "100px",
                    render: function(data){
                        return [null, undefined,''].includes(data.EMailFactura) ? `<div class="fondo-rojo">&nbsp;</div>` : `<span title="${data.EMailFactura}">${acortadorTexto(data.EMailFactura, 14)}</span>`;
                    }
                },
                {
                    data: null,
                    width: "100px",
                    render: function(data){
                        return [null, undefined,''].includes(data.EmailMasivo) ? `<div class="fondo-rojo">&nbsp;</div>` : `<span title="${data.EmailMasivo}">${acortadorTexto(data.EmailMasivo, 14)}</span>`;
                    }
                },
                {
                    data: null,
                    width: "100px",
                    render: function(data){
                        return [null, undefined, ''].includes(data.EMailInformes) ? '<div class="fondo-rojo">&nbsp;</div>': `<span>${acortadorTexto(data.EMailInformes, 14)}</span>`;
                    }
                },
                {
                    data: null,
                    width: "50px",
                    render: function(data){            
                        
                        return `<button data-id="${data.Id}" class="btn btn-sm iconoGeneral editar" title="Editar"><i class="ri-edit-line p-1"></i></button>
                        <button data-id="${data.Id}" class="btn btn-sm iconoGeneral envioIndivivual" title="Enviar al cliente mensaje" data-bs-toggle="modal" data-bs-target="#envioIndividual"><i class="ri-mail-send-line p-1"></i></button>
                        `;
                    }
                },
                {
                    data: null,
                    width: "50px",
                    render: function(data){            
                        
                        return `<input type="checkbox" name="Id_masivo" value="${data.Id}">`;
                    }
                }
            ],
            language: {
                processing: "<div style='text-align: center; margin-top: 20px;'><img src='./images/spinner.gif' /><p>Cargando...</p></div>",
                emptyTable: "No hay clientes con los datos buscados",
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
                info: "Mostrando _START_ a _END_ de _TOTAL_ de clientes",
            },
            createdRow: function(row, data, dataIndex){
                data.Bloqueado == '1' ? $(row).addClass('rojo') : '';
            }
        });

    });
});