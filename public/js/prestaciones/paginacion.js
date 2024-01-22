$(document).ready(()=>{

    $('th.sort').off("click"); // Se coloca provisoriamente, cuando se defina el modelo de ordenado a utilizar se puede quitar.

    $('.buscarPrestaciones, .hoyPrestaciones').on('click', function(event) {

        event.preventDefault();
        let nroprestacion = $('#nroprestacion').val(),
            pacienteSearch = $('#pacienteSearch').val(),
            empresaSearch = $('#empresaSearch').val(),
            artSearch = $('#artSearch').val(),
            empresaSelect2 = $('#empresaSelect2').val(),
            pacienteSelect2 = $('#pacienteSelect2').val(),
            artSelect2 = $('#artSelect2').val(),
            tipoPrestacion = $('#TipoPrestacion').val(),
            fechaDesde = $('#fechaDesde').val(),
            fechaHasta = $('#fechaHasta').val(),
            estado = $('#Estado').val();

        let dataTableConfig = {
            searching: false,
            ordering: true,
            order: [[0, 'desc'], [1, 'desc'], [2, 'desc'], [3, 'desc'], [4, 'desc'], [5, 'desc'], [6, 'desc'], [7, 'desc'], [8, 'desc'], [9, 'desc'], [10, 'desc'], [11, 'desc'], [12, 'desc'], [13, 'desc'], [14, 'desc'], [15, 'desc'], [16, 'desc'], [17, 'desc']],
            fixedColumns: true,
            processing: true,
            lengthChange: false,
            pageLength: 150,
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
                   
                        return `<input type="checkbox" name="Id" value="${data.Id}" checked>`;
                    }
                },
                {
                    data: null,
                    name: 'Id',
                    orderable: true,
                    targets: 1,
                    render: function(data){

                        let cerradoAdjunto = data.CerradoAdjunto || 0,
                            total = data.Total || 1;

                        let resultado = data.Anulado === 0 
                            ? ((cerradoAdjunto/total)*100).toFixed(2) + '%'
                            : 'Anul';

                        return resultado;
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
                    name: 'Id',
                    target: 3,
                    orderable: true,
                    render: function(data){
                        return `<div class="text-center"><span>${data.Id}</span></div>`;
                    }
                },
                {
                    data: null,
                    name: 'Apellido',
                    orderable: true,
                    targets: 4,
                    width: 80,
                    render: function(data){
                        let nombreCompleto = data.Apellido + ' ' + data.Nombre;
                        let recorteNom = nombreCompleto.substring(0,7) + "...";
                        return `<span title="${nombreCompleto}">${recorteNom}</span>`;
                    }
                },
                {
                    data: null,
                    name: 'TipoPrestacion',
                    orderable: true,
                    targets: 5,
                    render: function(data){
                        return data.TipoPrestacion; 
                    }
                },
                {
                    data: null,
                    name: 'Empresa',
                    orderable: true,
                    targets: 6,
                    render: function(data){
                        let prestacionEmp = data.Empresa ===  null ? '-' : data.Empresa;
                        let recorteEm = prestacionEmp.substring(0,7) + "...";
                        return `<span title="${data.Empresa}">${recorteEm}</span>`;
                    }  
                },  
                {
                    data: null,
                    name: 'ParaEmpresa',
                    orderable: true,
                    targets: 7,
                    render: function(data){
                        let prestacionPe = data.ParaEmpresa === null ? '-' : data.ParaEmpresa;
                        let recortePe = prestacionPe.substring(0,7) + "...";
                        return `<span title="${data.ParaEmpresa}">${recortePe}</span>`;
                    }
                }, 
                {
                    data: null,
                    name: 'Art',
                    orderable: true,
                    targets: 8,
                    render: function(data){
                        let prestacionArt = data.Art === null ? '-' : data.Art;
                        let recorteArt = prestacionArt.substring(0, 7) + "...";
                        return `<span title="${data.Art}">${recorteArt}</span>`;
                    }
                },
                {
                    data: null,
                    name: 'Id',
                    orderable: true,
                    targets: 9,
                    render: function(data){

                        let situacion;

                        if(data.Cerrado === 1){
                            situacion = "Cerrado";
                        }else if(data.Finalizado === 1){
                            situacion = "Finalizado";
                        }else if(data.Entregado === 1){
                            situacion = "Entregado";
                        }else if(data.Cerrado === 0 && data.Finalizado === 0){
                            situacion = "Abierto";
                        }

                        return '<span class="iconGeneralNegro">' + situacion + '</span>';
                    }
                },
                {
                    data: null,
                    name: 'eEnviado',
                    orderable: false,
                    targets: 10,
                    render: function(data){
                        return `<div class="text-center"><i class="${data.eEnviado === 1 ? `ri-checkbox-circle-fill negro` : `ri-close-circle-line negro`}"></i></div>`;
                    }
                },
                {
                    data: null,
                    name: 'INC',
                    orderable: false, 
                    targets: 11,
                    width: "10px",
                    render: function(data){
                        return data.Incompleto === 1 ? `<div class="text-center"><i class="ri-check-line"></i></div>` : `-`;
                    }
                },
                {
                    data: null,
                    name: 'AUS',
                    orderable: false, 
                    targets: 12,
                    render: function(data){
                        return data.Ausente === 1 ? `<div class="text-center"><i class="ri-check-line"></i></div>` : `-`;
                    }
                },
                {
                    data: null,
                    name: 'FOR',
                    orderable: false, 
                    targets: 13,
                    render: function(data){
                        return data.Forma === 1 ? `<div class="text-center"><i class="ri-check-line"></i></div>` : `-`;
                    }
                },
                {
                    data: null,
                    name: 'DEV',
                    orderable: false, 
                    targets: 14,
                    render: function(data){
                        return data.Devol === 1 ? `<div class="text-center"><i class="ri-check-line"></i></div>` : `-`;
                    }
                },
                {
                    data: null,
                    name: 'FP',
                    orderable: false, 
                    targets: 15,
                    render: function(data){

                        let pagos = {
                            'B': 'Ctdo',
                            'P': 'ExCta',
                            'C': 'CC'
                        };
                        return pagos[data.Pago] === undefined || 
                               pagos[data.Pago] === null || 
                               pagos[data.Pago] === '' 
                               ? '-' 
                               : pagos[data.Pago];
                    }
                },
                {
                    data: null,
                    name: 'Factura',
                    orderable: false,
                    targets: 16,
                    render: function(data){
                        return data.Facturado === 1 
                                ? `<div class="text-center"><i class="ri-check-line"></i></div>` 
                                : `-`;
                    }
                },
                {
                    data: null,
                    name: 'Id',
                    orderable: false,
                    targets: 17,
                    render: function(data){
                        let editar = `<a title="Editar" href="${location.href}/${data.Id}/edit"><button type="button" class="btn btn-sm iconGeneralNegro"><i class="ri-edit-line"></i></button></a>`,
                        
                        bloquear = `<button type="button" data-id="${data.Id}" class="btn btn-sm iconGeneralNegro bloquearPrestacion" title="${(data.Anulado == 1 ? "Bloqueado" : "Bloquear")}" ${ (data.Anulado == 1 ? "disabled" : "")}><i class="ri-forbid-2-line"></i></button>`,

                        baja = `<button data-id="${data.Id}" title="Dar de baja" type="button" class="btn btn-sm iconGeneralNegro downPrestacion" ><i class="ri-delete-bin-2-line"></i></button>`;

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
            },
            createdRow: function (row, data, dataIndex) {

                let cerradoAdjunto = data.CerradoAdjunto || 0,
                    total = data.Total || 1,
                    calculo = parseFloat(((cerradoAdjunto / total) * 100).toFixed(2)),
                    resultado;
            
                if (calculo === 100) {
                    resultado = $(row).addClass('fondo-blanco');
                } else if (data.Anulado === 0 && calculo >= 86 && calculo <= 99) {
                    resultado = $(row).addClass('fondo-verde');
                } else if (data.Anulado === 0 && calculo >= 51 && calculo <= 85) {
                    resultado = $(row).addClass('fondo-amarillo');
                } else if (data.Anulado === 0 && calculo >= 1 && calculo <= 50) {
                    resultado = $(row).addClass('fondo-naranja');
                } else if(data.Anulado === 0) {
                    resultado = $(row).addClass('fondo-rojo');
                } else if(data.Anulado === 1) {
                    resultado = $(row).addClass('rojo');
                }
            
                return resultado;
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
                    e.pacienteSearch = pacienteSearch;
                    e.empresaSearch = empresaSearch;
                    e.artSearch = artSearch;
                    e.pacienteSelect2 = pacienteSelect2;
                    e.artSelect2 = artSelect2;
                    e.empresaSelect2 = empresaSelect2;
                    e.nroprestacion = nroprestacion;
                    e.tipoPrestacion = tipoPrestacion;
                    e.fechaDesde = fechaDesde;
                    e.fechaHasta = fechaHasta;
                    e.estado = estado;
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