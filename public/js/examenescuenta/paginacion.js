
$(document).ready(()=>{

    $('th.sort').off("click");
    //Botón de busqueda de Mapas
    $(document).on('click', '#buscar', function() {

        let fechaDesde = $('#fechaDesde').val(),
            fechaHasta = $('#fechaHasta').val();

        if (fechaDesde === '' || fechaHasta === '') {
            toastr.warning("Las fechas son obligatorias");
            return;
        }

        $('#listadoExamenesCuentas').DataTable().clear().destroy();
        //$('#listadoExamenesCuentas').DataTable().clear().draw();

        var table = new DataTable("#listadoExamenesCuentas", {

            searching: false,
            ordering: true,
            order: [[0, 'desc'], [1, 'desc'], [2, 'desc'], [3, 'desc'], [4, 'desc'], [5, 'desc'], [6, 'desc'], [7, 'desc']],
            fixedColumns: true,
            processing: true,
            lengthChange: false,
            pageLength: 50,
            deferRender: true,
            responsive: true,
            serverSide: true,
            ajax: {
                url: SEARCH,
                data: function(d){
                    d.fechaDesde = $('#fechaDesde').val();
                    d.fechaHasta = $('#fechaHasta').val();
                    d.rangoDesde = $('#rangoDesde').val();
                    d.rangoHasta = $('#rangoHasta').val();
                    d.empresa = $('#empresa').val();
                    d.paciente = $('#paciente').val();
                    d.examen = $('#examen').val();
                    d.estado = $('#estado').val();
                }
            },
            dataType: 'json',
            type: 'POST',
            columnDefs: [
                {
                    data: null,
                    name: 'IdEx',
                    orderable: true,
                    targets: 0,
                    render: function(data){
                        return ("000000" + data.IdEx).slice(-6);
                    }
                },
                {
                    data: null,
                    name: 'Numero',
                    orderable: true,
                    targets: 1,
                    render: function(data){
                        return data.Tipo + ("0000" + data.Sucursal).slice(-4) + '-' + ("00000000" + data.Numero).slice(-8);
                    }
                },
                {
                    data: null,
                    name: 'Fecha',
                    orderable: true,
                    targets: 2,
                    render: function(data){
                        return fechaNow(data.Fecha,'/',0);
                    }
                },
                {
                    data: null,
                    name: 'Empresa',
                    orderable: true,
                    targets: 3,
                    render: function(data){
                        let EmpresaCompleto = data.Empresa + ' - ' + data.Cuit;
                        let recorte = (EmpresaCompleto).substring(0,25) + "...";
                        return recorte.length >= 25 ? `<span title="${EmpresaCompleto}">${recorte}</span>` : EmpresaCompleto;
                    }
                },
                {
                    data: null,
                    name: 'ParaEmpresa',
                    orderable: true,
                    targets: 4,
                    render: function(data){
                        let ParaEmpresa = data.ParaEmpresa;
                        let recorte = (ParaEmpresa).substring(0,25) + "...";
                        return recorte.length >= 25 ? `<span title="${ParaEmpresa}">${recorte}</span>` : ParaEmpresa;
                    }
                },
                {
                    data: null,
                    name: 'FechaPagado',
                    orderable: true,
                    targets: 5,
                    render: function(data){

                        return data.FechaPagado === '0000-00-00' ? '-' : fechaNow(data.FechaPagado,'/',0);
                    }
                },
                {
                    className: 'details-control',
                    orderable: false,
                    data: null,
                    defaultContent: '',
                    targets: 6
                },
                {
                    data: null,
                    name: 'ParaEmpresa',
                    orderable: false,
                    targets: 7,
                    render: function(data) {
                        
                        let editar = `<a title="Editar" href="${location.href}/${data.IdEx}/edit"><button type="button" class="btn btn-sm iconGeneralNegro"><i class="ri-edit-line"></i></button></a>`,
                    
                            baja = `<button data-id="${data.Id}" title="Dar de baja" type="button" class="btn btn-sm iconGeneralNegro downExamen" ><i class="ri-delete-bin-2-line"></i></button>`,
                            
                            pago = `<button type="button" data-id="${data.IdEx}" class="btn btn-sm botonGeneral cambiarBoton">${data.FechaPagado === '0000-00-00' ? 'Pagar' : 'Quitar pago'}</button>`;

                        return editar + ' ' + baja + ' ' + pago;  
                    }
                }
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
                info: "Mostrando _START_ a _END_ de _TOTAL_ de facturas",
            },
        });
        
        $('#listadoExamenesCuentas tbody').off('click', 'td.details-control').on('click', 'td.details-control', function(){
            var tr = $(this).closest('tr');
            var row = table.row(tr);
        
            if(row.child.isShown()){
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            } else {
                // Open this row
                format(row.data()).then(function(div){
                    row.child(div).show();
                    tr.addClass('shown');
                }).catch(function(error){
                    console.error('Error al cargar detalles:', error);
                });
            }
        });

        // Handle click on "Expand All" button
        $(document).on('click', '#btn-show-all-children', function(){
            // Enumerate all rows
            table.rows().every(function(){
                // If row has details collapsed
                if(!this.child.isShown()){
                    // Open this row
                    this.child(format(this.data())).show();
                    $(this.node()).addClass('shown');
                }
            });
        });
    
        // Handle click on "Collapse All" button
        $(document).on('click', '#btn-hide-all-children', function(){
            // Enumerate all rows
            table.rows().every(function(){
                // If row has details expanded
                if(this.child.isShown()){
                    // Collapse row details
                    this.child.hide();
                    $(this.node()).removeClass('shown');
                }
            });
        });

        function format(rowData) {
            return new Promise((resolve, reject) => {
                var div = `<div class="table-responsive table-card mt-3 mb-1 mx-auto">
                                <table id="detalles" class="display table table-bordered mx-auto" style="width:70%">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Examen</th>
                                            <th>Prestación</th>
                                            <th>Paciente</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">`;
                preloader('on');
                $.get(detallesExamenes, {Id: (rowData.IdEx === '' || rowData.IdEx === undefined ? 0 : rowData.IdEx)})
                    .done(async function(response){
                        let data = await response.result;
                        preloader('off');
                        $.each(data, function(index, d){
                           let nombreCompleto =  d.ApellidoPaciente + ' ' + d.NombrePaciente;

                            div += `<tr>
                                        <td>${d.NombreExamen === undefined ? '-' : d.NombreExamen}</td>
                                        <td>${d.IdPrestacion === undefined || d.IdPrestacion === 0 ? '-' : d.IdPrestacion}</td>
                                        <td>${nombreCompleto}</td>
                                    </tr>`;
                        });
                        div += `</tbody>
                                </table>
                            </div>`;

                       

                        resolve(div);
                    })
                    .fail(function(error){
                        preloader('off');
                        reject(error);
                    });

                $("#detalles").fancyTable({
                    pagination: true,
                    perPage: 5,
                    searchable: false,
                    globalSearch: false,
                    sortable: false, 
                });
            });
        }


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
        
            return (format === '0') ? `${dia}${divider}${mes}${divider}${anio}` : `${anio}${divider}${mes}${divider}${dia}`;
        }

        function preloader(opcion) {
            $('#preloader').css({
                opacity: '0.3',
                visibility: opcion === 'on' ? 'visible' : 'hidden'
            });
        }

    });

});