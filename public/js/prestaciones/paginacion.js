$(document).ready(()=>{

    $('th.sort').off("click"); // Se coloca provisoriamente, cuando se defina el modelo de ordenado a utilizar se puede quitar.

    $('.buscarPrestaciones, .hoyPrestaciones').on('click', function(event) {

        event.preventDefault();
        let nroprestacion = $('#nroprestacion').val();
        let pacempart = $('#pacempart').val();

        //Filtros basicos
        let tipoPrestacion = $('#TipoPrestacion').val();
        let pago = $('#Pago').val();
        let formaPago = $('#Spago').val();
        let fechaDesde = $('#fechaDesde').val();
        let fechaHasta = $('#fechaHasta').val();
        let estado = $('#Estado').val();
        let eEnviado = $('#eEnviado').val();

        //Filtros avanzados
        let finalizado = $('#Finalizado').val();
        let facturado = $('#Facturado').val();
        let entregado = $('#Entregado').val();

        var dataTableConfig = {
            searching: false,
            ordering: true,
            order: [[0, 'desc'], [1, 'desc'], [2, 'desc'], [3, 'desc'], [4, 'desc'], [5, 'desc'], [6, 'desc'], [7, 'desc'], [8, 'desc'], [9, 'desc'], [10, 'desc']],
            processing: true,
            lengthChange: false,
            pageLength: 50,
            responsive: true,
            serverSide: true,
            dataType: 'json',
            type: 'POST',
            columnDefs: [
                {
                    data: null,
                    name: 'selectId',
                    orderable: false,
                    targets: 0,
                    render: function(data){
                        let id = data.Id;
                        return `<input type="checkbox" name="Id" value="${id}" checked>`;
                    }
                },
                {
                    data: null,
                    name: 'Id',
                    orderable: true,
                    targets: 1,
                    render: function(data){
                        return '<span>' + data.Id + '</span>';
                    }

                },
                {
                    data: null,
                    name: 'FechaAlta',
                    targets: 2,
                    orderable: true,
                    render: function(data){
                        return fechaNow(data.FechaAlta,'/',0);
                    }
                },
                {
                    data: null,
                    name: 'Empresa',
                    orderable: true,
                    targets: 3,
                    render: function(data){
                        let prestacionRz = data.Empresa ===  null ? '-' : data.Empresa;
                        let recorteRz = prestacionRz.substring(0,15) + "...";
                        return `<span title="${data.Empresa}">${recorteRz}</span>`; 
                    }
                },
                {
                    data: null,
                    name: 'ParaEmpresa',
                    orderable: true,
                    targets: 4,
                    render: function(data){
                        let prestacionPe = data.ParaEmpresa === null ? '-' : data.ParaEmpresa;
                        let recortePe = prestacionPe.substring(0,15) + "...";
                        return `<span title="${data.ParaEmpresa}">${recortePe}</span>`;
                    }
                },
                {
                    data: 'Identificacion',
                    name: 'Identificacion',
                    targets: 5,
                    orderable: true,
                },
                {
                    data: null,
                    name: 'nombreCompleto',
                    orderable: true,
                    targets: 6,
                    render: function(data){
                        let prestacionNom = data.nombreCompleto === null ? '-' : data.nombreCompleto;
                        let recorteNom = prestacionNom.substring(0,15) + "...";
                        return `<span title="${data.nombreCompleto}">${recorteNom}</span>`;
                    }
                },
                {
                    data: null,
                    name: 'Art',
                    orderable: true,
                    targets: 7,
                    render: function(data){
                        let prestacionArt = data.Art === null ? '-' : data.Art;
                        let recorteArt = prestacionArt.substring(0, 15) + "...";
                        return `<span title="${data.Art}">${recorteArt}</span>`;
                    }
                },
                {
                    data: null,
                    name: 'Anulado',
                    orderable: true,
                    targets: 8,
                    render: function(data){
                        return '<span class="badge badge-soft-' + (data.Anulado == 0 ? "success" : "danger") + ' text-uppercase">' + (data.Anulado == 0 ? "Habilitado" : "Anulado") + '</span>';
                    }
                },
                {
                    data: null,
                    name: 'Pago',
                    orderable: true,
                    targets: 9,
                    render: function(data){
                        
                        let pagos = {
                            'B': 'Ctdo.',
                            'C': 'CCorriente',
                            'P': 'ExCuenta'
                        };
                        
                        return pagos[data.Pago] === undefined ||  pagos[data.Pago] === null ? '-' : pagos[data.Pago];     
                    
                    }
                },
                {
                    data: null,
                    name: 'Id',
                    orderable: false,
                    targets: 10,
                    render: function(data){
                        let editar = `<a title="Editar" href="${location.href}/${data.Id}/edit"><button type="button" class="btn btn-sm iconGeneral"><i class="ri-edit-line"></i></button></a>`,
                        
                        bloquear = `<button type="button" data-id="${data.Id}" class="btn btn-sm iconGeneral bloquearPrestacion" title="${(data.Anulado == 1 ? "Bloqueado" : "Bloquear")}" ${ (data.Anulado == 1 ? "disabled" : "")}><i class="ri-forbid-2-line"></i></button>`,

                        baja = `<button data-id="${data.Id}" type="button" class="btn btn-sm iconGeneral downPrestacion" ><i class="ri-delete-bin-2-line"></i></button>`;

                        return editar + ' ' + bloquear + ' ' + baja;
                    }
                }
            ],
            language: {
                processing: "Cargando listado de prestaciones de CMIT",
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
            }
        };

        if ($(this).hasClass('hoyPrestaciones')) {

            dataTableConfig.ajax = {
                url: ROUTE,
            };

        } else {
            
            if((fechaDesde == '' || fechaHasta == '') && nroprestacion == ''){
                swal('Alerta','La fecha "Desde" y "Hasta" son obligatorias.', 'warning');
                return;
            }

            dataTableConfig.ajax = {
                url: SEARCH,
                data: function(e) {
                    e.pacempart = pacempart;
                    e.nroprestacion = nroprestacion;
                    e.tipoPrestacion = tipoPrestacion;
                    e.pago = pago;
                    e.formaPago = formaPago;
                    e.fechaDesde = fechaDesde;
                    e.fechaHasta = fechaHasta;
                    e.estado = estado;
                    e.eEnviado = eEnviado;
                    e.finalizado = finalizado;
                    e.facturado = facturado;
                    e.entregado = entregado;
                }
            };
        }

        

        $('#listaPrestaciones').DataTable().clear().destroy();

        new DataTable("#listaPrestaciones", dataTableConfig);

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
    
        return (format === 1) ? `${dia}${divider}${mes}${divider}${anio}` : `${anio}${divider}${mes}${divider}${dia}`;
    }


});