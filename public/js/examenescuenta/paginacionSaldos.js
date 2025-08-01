$(document).ready(()=>{

    $('th.sort').off("click");

    $(document).on('click', '#buscarSaldo', function(e){

        let empresa = $('#empresaSaldo').val(),
        examen = $('#examenSaldo').val(),
        examen2 = $('#examenSaldo2').val();

        if ($.fn.DataTable.isDataTable('#listadoExCtasSaldos')) {
            $('#listadoExCtasSaldos').DataTable().clear().destroy();
        }

        new DataTable('#listadoExCtasSaldos', {

            searching: false,
            ordering: true,
            order: [[0, 'desc'], [1, 'desc'], [2, 'desc'], [3, 'desc']],
            fixedColumns: true,
            processing: true,
            lengthChange: false,
            pageLength: 50,
            deferRender: true,
            responsive: false,
            serverSide: true,
            ajax: {
                url: SALDOS,
                data: function(d){
                    d.empresa = empresa;
                    d.examen = examen;
                    d.examen2 = examen2;
                }
            },
            dataType: 'json',
            type: 'POST',
            columnDefs: [
                {
                    data: null,
                    name: 'Empresa',
                    orderable: true,
                    targets: 0,
                    render: function(data){
                        return `<span title="${data.Empresa}">${data.Empresa}</span>`;
                    }
                },
                {
                    data: null,
                    name: 'Examen',
                    orderable: true,
                    targets: 1,
                    render: function(data){
                        return `<span title="${data.Examen}">${data.Examen}</span>`;
                    }
                },
                {
                    data: null,
                    name: 'contadorSaldos',
                    orderable: false,
                    targets: 2,
                    render: function(data){
                        return data.contadorSaldos;
                    }
                },
                {
                    data: null,
                    name: 'acciones',
                    orderable: false,
                    targets: 3,
                    render: function(data){
                
                        //detalle = `<button data-id="${data.IdEmpresa}" title="Detalles" type="button" class="btn btn-sm botonGeneral detalles"><i class="ri-file-list-2-line"></i> Detalle</button>`,
                        
                        saldo = `<button type="button" data-id="${data.IdEmpresa}" class="btn btn-sm botonGeneral saldo"><i class="ri-file-list-2-line"></i> Saldo</button>`;

                    return saldo;  
                    }
                },
            ],
            language: {
                processing: "<div style='text-align: center; margin-top: 20px;'><img src='./images/spinner.gif' /><p>Cargando...</p></div>",
                emptyTable: "No hay saldos con los datos buscados",
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
                info: "Mostrando _START_ a _END_ de _TOTAL_ de saldos",
            },
        });

    });
    $(document).on('click', '.excelDetalleSaldos', function(e){

        let empresa = $('#empresaSaldo').val(),
        examen = $('#examenSaldo').val(),
        examen2 = $('#examenSaldo2').val();

        swal({
            title: "¿Estas seguro que deseas generar el reporte?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                $.get(exportarDetalle, {
                    empresa : empresa,
                    examen : examen,
                    examen2 : examen2
                })
                .done(function(response){

                    createFile("xlsx", response.filePath, "reporte");
                    toastr.success("Se esta generando el reporte", '', {timeOut: 1000});
                })
                .fail(function(jqXHR){
                    preloader('off');
                    let errorData = JSON.parse(jqXHR.responseText);            
                    checkError(jqXHR.status, errorData.msg);
                    return;  
                });
            }
        });
    });

     $(document).on('click', '.excelExcel', function(e){

        let empresa = $('#empresaSaldo').val(),
        examen = $('#examenSaldo').val(),
        examen2 = $('#examenSaldo2').val();

        swal({
            title: "¿Estas seguro que deseas generar el reporte?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                $.get(exportGeneral, {
                    empresa : empresa,
                    examen : examen,
                    examen2 : examen2
                })
                .done(function(response){

                    createFile("xlsx", response.filePath, "reporte");
                    toastr.success("Se esta generando el reporte", '', {timeOut: 1000});
                })
                .fail(function(jqXHR){
                    preloader('off');
                    let errorData = JSON.parse(jqXHR.responseText);            
                    checkError(jqXHR.status, errorData.msg);
                    return;  
                });
            }
        });
    });
});