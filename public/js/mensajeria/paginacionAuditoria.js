$(document).ready(function(){

    let paginacion = {
        
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
                d.FechaDesde = $('#fechaDesde').val();
                d.FechaHasta = $('#fechaHasta').val();
            }
        },
        dataType: 'json',
        type: 'POST',
        columns: [

            {
                width: "20px",
                data: null,
                render: function(data){
                    return ajustarFecha(data.Fecha);
                }
            },
            {
                width: "40px",
                data: null,
                render: function(data){
                    return `<span title="${data.Asunto}">${acortadorTexto(data.Asunto, 15)}</span>`
                }
            },
            {      
                data: null,
                width: "200px",
                render: function(data){
                    return `<span class="text-break" title="${data.Destinatarios}">${acortadorTexto(saltoLinea(data.Destinatarios), 650)}</span>`;
                }
            },
            {
                width: "50px",
                data: null,
                render: function(data){            
                    return `
                    <button data-id="${data.Id}" data-bs-toggle="modal" data-bs-target="#modalDestinatarios" class="btn btn-sm botonGeneral destinatarios p-1" title="Ver todos los destinatarios"><i class="ri-eye-line"></i></button>
                    <button data-id="${data.Id}" data-bs-toggle="modal" data-bs-target="#modalHistorial" class="btn btn-sm botonGeneral historiaMensaje p-1" title="Ver mensaje"><i class=" ri-file-text-line p-1"></i></button>`;
                }
            }
        ],
        language: {
            processing: "<div style='text-align: center; margin-top: 20px;'><img src='../images/spinner.gif' /><p>Cargando...</p></div>",
            emptyTable: "No hay auditorias con los datos buscados",
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
            info: "Mostrando _START_ a _END_ de _TOTAL_ de auditorias",
        }
    }

    const selector = '#listaAuditorias';

    $(selector).DataTable().clear().destroy();
    new DataTable(selector, paginacion);


    $(document).on('click', '.buscar', function(e){
        e.preventDefault();
        let fechaDesde = $('#fechaDesde').val(), fechaHasta = $('#fechaHasta').val();

        if([null, undefined, ''].includes(fechaDesde) || [null, undefined, ''].includes(fechaHasta)) {
            toastr.warning("Debe seleccionar un rango de fechas");
            return;
        }
        $(selector).DataTable().clear().destroy();
        new DataTable(selector, paginacion);
    
    });


});