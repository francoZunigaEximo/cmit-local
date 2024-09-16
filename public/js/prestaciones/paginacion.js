$(document).ready(()=>{

    $('th.sort').off("click"); // Se coloca provisoriamente, cuando se defina el modelo de ordenado a utilizar se puede quitar.

    $('.buscarPrestaciones, .hoyPrestaciones').on('click', function(e) {
        e.preventDefault();

        let fechaDesde = $('#fechaDesde').val(),
            fechaHasta = $('#fechaHasta').val(),
            nroprestacion = $('#nroprestacion').val(),
            pacienteSearch = $('#pacienteSearch').val(),
            empresaSearch = $('#empresaSearch').val(),
            artSearch = $('#artSearch').val(),
            empresaSelect2 = $('#empresaSelect2').val(),
            pacienteSelect2 = $('#pacienteSelect2').val(),
            artSelect2 = $('#artSelect2').val(),
            tipoPrestacion = $('#TipoPrestacion').val(),
            estado = $('#Estado').val();

        let hoy = new Date().toLocaleDateString('en-CA');

        if($(this).hasClass('hoyPrestaciones')) {

                fechaDesde = hoy,
                fechaHasta = hoy,
                nroprestacion = null,
                pacienteSearch = null,
                empresaSearch = null,
                artSearch = null,
                empresaSelect2 = null,
                pacienteSelect2 = null,
                artSelect2 = null,
                tipoPrestacion = null,
                estado = null;
        }

        if((fechaDesde == '' || fechaHasta == '') && nroprestacion == ''){
            swal('Alerta','La fecha "Desde" y "Hasta" son obligatorias.', 'warning');
            return;
        }

        $('#listaPrestaciones').DataTable().clear().destroy();

        new DataTable("#listaPrestaciones", {
            searching: false,
            ordering: true,
            order: [[0, 'desc'], [1, 'desc'], [2, 'desc'], [3, 'desc'], [4, 'desc'], [5, 'desc'], [6, 'desc'], [7, 'desc'], [8, 'desc'], [9, 'desc'], [10, 'desc'], [11, 'desc'], [12, 'desc'], [13, 'desc'], [14, 'desc'], [15, 'desc'], [16, 'desc']],
            processing: true,
            lengthChange: false,
            pageLength: 150,
            responsive: false,
            serverSide: true,
            deferRender: true,
            scrollCollapse: true,
            autoWidth: false,
            select: {
                style: 'multi'
            },
            ajax: {
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
            },
            dataType: 'json',
            type: 'POST',
            columnDefs: [
                {
                    data: null,
                    name: 'Id',
                    orderable: true,
                    targets: 0,
                    render: function(data){

                        let cerradoAdjunto = data.CerradoAdjunto || 0,
                            total = data.Total || 1;

                        let resultado = data.Anulado === 0 
                            ? ((cerradoAdjunto/total)*100).toFixed(0)
                            : '0';

                        return `<div class="text-center ${indicador(data)}">${resultado}</div>`;
                    }

                },
                {
                    data: null,
                    name: 'FechaAlta',
                    targets: 1,
                    orderable: true,
                    render: function(data){
                        return fechaNow(data.FechaAlta,'/',0);
                    }
                },
                {
                    data: null,
                    name: 'Id',
                    target: 2,
                    orderable: true,
                    render: function(data){
                        return `<div class="text-center"><span>${data.Id}</span></div>`;
                    }
                },
                {
                    data: null,
                    name: 'Apellido',
                    orderable: true,
                    targets: 3,
                    render: function(data){
                        let nombreCompleto = data.Apellido + ' ' + data.Nombre;
                        return `<span title="${nombreCompleto}">${nombreCompleto}</span>`;
                    }
                },
                {
                    data: null,
                    name: 'Tipo',
                    orderable: true,
                    targets: 4,
                    render: function(data){
                        return data.Tipo; 
                    }
                },
                {
                    data: null,
                    name: 'Empresa',
                    orderable: true,
                    targets: 5,
                    render: function(data){
                        let prestacionEmp = data.Empresa ===  null ? '-' : data.Empresa;
                        return `<span title="${data.Empresa}">${prestacionEmp}</span>`;
                    }  
                },  
                {
                    data: null,
                    name: 'ParaEmpresa',
                    orderable: true,
                    targets: 6,
                    render: function(data){
                        let prestacionPe = data.ParaEmpresa === null ? '-' : data.ParaEmpresa;
                        return `<span title="${data.ParaEmpresa}">${prestacionPe}</span>`;
                    }
                }, 
                {
                    data: null,
                    name: 'Art',
                    orderable: true,
                    targets: 7,
                    render: function(data){
                        let prestacionArt = data.Art === null ? '-' : data.Art;
                        return `<span title="${data.Art}">${prestacionArt}</span>`;
                    }
                },
                {
                    data: null,
                    name: 'Id',
                    orderable: true,
                    targets: 8,
                    render: function(data){

                        return (data.Cerrado === 1 && data.Finalizado === 0 && data.Entregado === 0)
                                ? "Cerrado"
                                : (data.Cerrado === 1 && data.Finalizado === 1 && data.Entregado === 0)
                                    ? "Finalizado"
                                    : (data.Cerrado === 1 && data.Finalizado === 1 && data.Entregado === 1)
                                        ? "Entregado"
                                        : (data.Cerrado === 0 && data.Finalizado === 0 && data.Entregado === 0)
                                            ? "Abierto"
                                            : "-";
                    }
                },
                {
                    data: null,
                    name: 'eEnviado',
                    orderable: false,
                    targets: 9,
                    render: function(data){
                        return `<div class="text-center"><i class="${data.eEnviado === 1 ? `ri-checkbox-circle-fill negro` : `ri-close-circle-line negro`}"></i></div>`;
                    }
                },
                {
                    data: null,
                    name: 'INC',
                    orderable: false, 
                    targets: 10,
                    render: function(data){
                        return data.Incompleto === 1 ? `<div class="text-center"><i class="ri-check-line"></i></div>` : `-`;
                    }
                },
                {
                    data: null,
                    name: 'AUS',
                    orderable: false, 
                    targets: 11,
                    render: function(data){
                        return data.Ausente === 1 ? `<div class="text-center"><i class="ri-check-line"></i></div>` : `-`;
                    }
                },
                {
                    data: null,
                    name: 'FOR',
                    orderable: false, 
                    targets: 12,
                    render: function(data){
                        return data.Forma === 1 ? `<div class="text-center"><i class="ri-check-line"></i></div>` : `-`;
                    }
                },
                {
                    data: null,
                    name: 'DEV',
                    orderable: false, 
                    targets: 13,
                    render: function(data){
                        return data.Devol === 1 ? `<div class="text-center"><i class="ri-check-line"></i></div>` : `-`;
                    }
                },
                {
                    data: null,
                    name: 'FP',
                    orderable: false, 
                    targets: 14,
                    render: function(data){

                        let pagos = { 'B': 'Ctdo', 'C': 'Ctdo', 'P': 'ExCta', 'A': 'CC'};
                        return [null, '', undefined].includes(pagos[data.Pago]) ? 'CC' : pagos[data.Pago];      
                    }
                },
                {
                    data: null,
                    name: 'Factura',
                    orderable: false,
                    targets: 15,
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
                    width: 100,
                    targets: 16,
                    render: function(data){
                        let editar = `<a title="Editar" href="${location.href}/${data.Id}/edit"><button type="button" class="btn btn-sm iconGeneralNegro"><i class="ri-edit-line"></i></button></a>`,
                        
                        bloquear = `<button type="button" data-id="${data.Id}" class="btn btn-sm iconGeneralNegro bloquearPrestacion" title="${(data.Anulado == 1 ? "Bloqueado" : "Bloquear")}" ${ (data.Anulado == 1 ? "disabled" : "")}><i class="ri-forbid-2-line"></i></button>`,

                        baja = `<button data-id="${data.Id}" title="Dar de baja" type="button" class="btn btn-sm iconGeneralNegro downPrestacion" ><i class="ri-delete-bin-2-line"></i></button>`;

                        return editar + ' ' + bloquear + ' ' + baja;
                    }
                }
            ],
            language: {
                processing: "<div style='text-align: center; margin-top: 20px;'><img src='./images/spinner.gif' /><p>Cargando...</p></div>",
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
        });

        $('#checkAll').on('change', function() {
            let isChecked = $(this).is(':checked');
            if (isChecked) {
                table.rows().select();
            } else {
                table.rows().deselect();
            }
        });

        $('#listaPrestaciones tbody').on('change', '.row-select', function() {
            let allChecked = $('#listaPrestaciones .row-select').length === $('#listaPrestaciones .row-select:checked').length;
            $('#checkAll').prop('checked', allChecked);
        });

    });
});

function indicador(data) {
    let cerradoAdjunto = data.CerradoAdjunto || 0,
        total = data.Total || 1,
        calculo = parseFloat(((cerradoAdjunto / total) * 100).toFixed(2));

        return (calculo === 100) 
                ? 'fondo-blanco'
                : (data.Anulado === 0 && calculo >= 86 && calculo <= 99)
                    ? 'fondo-verde'
                    : (data.Anulado === 0 && calculo >= 51 && calculo <= 85)
                        ? 'fondo-amarillo'
                        : (data.Anulado === 0 && calculo >= 1 && calculo <= 50)
                            ? 'fondo-naranja'
                            : (data.Anulado === 0)
                                ? 'fondo-rojo'
                                : (data.Anulado === 1)
                                    ? 'rojo negrita'
                                    : '';
}
