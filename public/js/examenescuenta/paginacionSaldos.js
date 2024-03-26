$(document).ready(()=>{

    $('th.sort').off("click");

    $(document).on('click', '#buscarSaldo', function(e){

        let empresa = $('#empresaSaldo').val(),
        examen = $('#examenSaldo').val();

        if ($.fn.DataTable.isDataTable('#listadoExCtasSaldos')) {
            $('#listadoExCtasSaldos').DataTable().clear().destroy();
        }

        new DataTable('#listadoExCtasSaldos', {

            searching: false,
            ordering: true,
            order: [[0, 'desc'], [1, 'desc'], [2, 'desc']],
            fixedColumns: true,
            processing: true,
            lengthChange: false,
            pageLength: 50,
            deferRender: true,
            responsive: true,
            serverSide: true,
            ajax: {
                url: SALDOS,
                data: function(d){
                    d.empresa = empresa;
                    d.examen = examen;
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
                        let recorte = (data.Empresa).substring(0,35) + "...";
                        return recorte.length >= 35 ? `<span title="${data.Empresa}">${recorte}</span>` : data.Empresa;
                    }
                },
                {
                    data: null,
                    name: 'Examen',
                    orderable: true,
                    targets: 1,
                    render: function(data){
                        let recorte = (data.Examen).substring(0,35) + "...";
                        return recorte.length >= 35 ? `<span title="${data.Examen}">${recorte}</span>` : data.Examen;
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


});