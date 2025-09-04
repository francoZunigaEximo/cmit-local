function mostrarBotonesPago(valor) {

    if(valor === 'pago') {
        $('.quitarPago').show();
        $('.botonPagar').hide();
    
    }else if(valor === '') {
        $('.quitarPago').hide();
        $('.botonPagar').show();
    }else{
        $('.botonPagar, .quitarPago').hide();
    }
}

function habilitarMasivo(arg) {

    if (arg === 1) {
        $('.quitarPago').prop('disabled', false);
        $('.botonPagar').prop('disabled', true);

    } else if(arg === 0) {
        $('.quitarPago').prop('disabled', true);
        $('.botonPagar').prop('disabled', false);
        
    }else{
        $('.botonPagar, .quitarPago').prop('disabled', true);
    }
}


function obtenerFormato(date) {
    return date.toISOString().slice(0, 10);
};


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


    });
}


$(function(){

    $('.botonPagar, .quitarPago').prop('disabled', true);
    mostrarBotonesPago($('#estado').val());

    $('th.sort').off("click");

    var tableIndex = new DataTable("#listadoExamenesCuentas", {

        searching: false,
        ordering: true,
        order: [[0, 'desc'], [1, 'desc'], [2, 'desc'], [3, 'desc'], [4, 'desc'], [5, 'desc'], [6, 'desc'], [7, 'desc'], [8, 'desc']],
        fixedColumns: true,
        processing: true,
        lengthChange: false,
        pageLength: 7,
        deferRender: true,
        responsive: true,
        serverSide: true,
        ajax: {
            url: INDEX,
        },
        dataType: 'json',
        type: 'POST',
        columnDefs: [
            {
                data: null,
                name: 'selectId',
                orderable: false,
                targets: 0,
                render: function(data){
               
                    return `<div class="text-center"><input type="checkbox" name="Id" value="${data.IdEx}" disabled></div>`;
                }
            },
            {
                data: null,
                name: 'IdEx',
                orderable: true,
                targets: 1,
                render: function(data){
                    return ("000000" + data.IdEx).slice(-6);
                }
            },
            {
                data: null,
                name: 'Numero',
                orderable: true,
                targets: 2,
                render: function(data){
                    return data.Tipo + ("0000" + data.Sucursal).slice(-4) + '-' + ("00000000" + data.Numero).slice(-8);
                }
            },
            {
                data: null,
                name: 'Fecha',
                orderable: true,
                targets: 3,
                render: function(data){
                    return fechaNow(data.Fecha,'/',0);
                }
            },
            {
                data: null,
                name: 'Empresa',
                orderable: true,
                targets: 4,
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
                targets: 5,
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
                targets: 6,
                render: function(data){

                    return data.FechaPagado === '0000-00-00' ? '-' : fechaNow(data.FechaPagado,'/',0);
                }
            },
            {
                className: 'details-control',
                orderable: false,
                data: null,
                defaultContent: '',
                targets: 7
            },
            {
                data: null,
                name: 'ParaEmpresa',
                orderable: false,
                targets: 8,
                render: function(data) {
                    let nroFactura = data.Tipo + ("0000" + data.Sucursal).slice(-4) + '-' + ("00000000" + data.Numero).slice(-8);
                    let empresa = data.Empresa;

                    let editar = `<a title="Editar" href="${location.href}/${data.IdEx}/edit"><button type="button" class="btn btn-sm iconGeneralNegro"><i class="ri-edit-line"></i></button></a>`,
                
                        baja = `<button data-id="${data.IdEx}" title="Dar de baja" type="button" class="btn btn-sm iconGeneralNegro deleteExamen" ><i class="ri-delete-bin-2-line"></i></button>`,
                        
                        pago = `<button type="button" data-id="${data.IdEx}" data-nro="${nroFactura}" data-empresa="${empresa}" class="btn btn-sm botonGeneral cambiarBoton">${data.FechaPagado === '0000-00-00' ? 'Pagar' : 'Quitar pago'}</button>`;

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
        drawCallback: function() {
            $(document).on('change', '#estado', function() {
                let valor = $(this).val();
                mostrarBotonesPago(valor);
                
            });

            $('.botonPagar').prop('disabled', true);

            $('#listadoExamenesCuentas tbody').off('click', 'td.details-control').on('click', 'td.details-control', function(){
                let tr = $(this).closest('tr'), row = tableIndex.row(tr);
            
                if(row.child.isShown()){
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    format(row.data()).then(function(div){
                        row.child(div).show();
                        tr.addClass('shown');
                    }).catch(function(error){
                        console.error('Error al cargar detalles:', error);
                    });
                }
            });
        
            $(document).on('click', '#btn-show-all-children', function(){
                tableIndex.rows().every(function(){
                    if(!this.child.isShown()){
                        this.child(format(this.data())).show();
                        $(this.node()).addClass('shown');
                    }
                });
            });
        
            $(document).on('click', '#btn-hide-all-children', function(){
                tableIndex.rows().every(function(){
                    if(this.child.isShown()){
                        this.child.hide();
                        $(this.node()).removeClass('shown');
                    }
                });
            });
        

        }
    });


    //Botón de busqueda de Mapas
    $(document).on('click', '#buscar, .sieteDias, .tresMeses', function() {
        
        let fechaHasta = $(this).hasClass('sieteDias') || $(this).hasClass('tresMeses') 
            ? new Date() 
            : $('#fechaHasta').val(); 
        
        let fechaDesde = $(this).hasClass('sieteDias') || $(this).hasClass('tresMeses')
            ? new Date(fechaHasta) 
            : $('#fechaDesde').val();
        
        $(this).hasClass('sieteDias') 
            ? fechaDesde.setDate(fechaHasta.getDate() - 7) 
            : $(this).hasClass('tresMeses') 
                ? fechaDesde.setDate(fechaHasta.getDate() - 90) 
                : $('#fechaDesde').val();
        
        $(this).hasClass('sieteDias') || $(this).hasClass('tresMeses') ? fechaDesde = obtenerFormato(fechaDesde) : $('#fechaDesde').val();
        $(this).hasClass('sieteDias') || $(this).hasClass('tresMeses') ? fechaHasta = obtenerFormato(fechaHasta) : $('#fechaHasta').val();

        /*console.log(fechaDesde, fechaHasta); 
        console.log(typeof(fechaDesde), typeof(fechaHasta));return; */

        if (fechaDesde === '' || fechaHasta === '') {
            toastr.warning("Las fechas son obligatorias", "", {timeOut: 1000});
            return;
        }

        $('#listadoExamenesCuentas').DataTable().clear().destroy();
        //$('#listadoExamenesCuentas').DataTable().clear().draw();

        var table = new DataTable("#listadoExamenesCuentas", {

            searching: false,
            ordering: true,
            order: [[0, 'desc'], [1, 'desc'], [2, 'desc'], [3, 'desc'], [4, 'desc'], [5, 'desc'], [6, 'desc'], [7, 'desc'], [8, 'desc']],
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
                    d.fechaDesde = fechaDesde;
                    d.fechaHasta = fechaHasta;
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
                    name: 'selectId',
                    orderable: false,
                    targets: 0,
                    render: function(data){
                   
                        return `<div class="text-center"><input type="checkbox" name="Id" value="${data.IdEx}"></div>`;
                    }
                },
                {
                    data: null,
                    name: 'IdEx',
                    orderable: true,
                    targets: 1,
                    render: function(data){
                        return ("000000" + data.IdEx).slice(-6);
                    }
                },
                {
                    data: null,
                    name: 'Numero',
                    orderable: true,
                    targets: 2,
                    render: function(data){
                        return data.Tipo + ("0000" + data.Sucursal).slice(-4) + '-' + ("00000000" + data.Numero).slice(-8);
                    }
                },
                {
                    data: null,
                    name: 'Fecha',
                    orderable: true,
                    targets: 3,
                    render: function(data){
                        return fechaNow(data.Fecha,'/',0);
                    }
                },
                {
                    data: null,
                    name: 'Empresa',
                    orderable: true,
                    targets: 4,
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
                    targets: 5,
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
                    targets: 6,
                    render: function(data){

                        return data.FechaPagado === '0000-00-00' ? '-' : fechaNow(data.FechaPagado,'/',0);
                    }
                },
                {
                    className: 'details-control',
                    orderable: false,
                    data: null,
                    defaultContent: '',
                    targets: 7
                },
                {
                    data: null,
                    name: 'ParaEmpresa',
                    orderable: false,
                    targets: 8,
                    render: function(data) {
                        let nroFactura = data.Tipo + ("0000" + data.Sucursal).slice(-4) + '-' + ("00000000" + data.Numero).slice(-8);
                        let empresa = data.Empresa;
                        habilitarMasivo(data.Pagado);

                        let editar = `<a title="Editar" href="${location.href}/${data.IdEx}/edit"><button type="button" class="btn btn-sm iconGeneralNegro"><i class="ri-edit-line"></i></button></a>`,
                    
                            baja = `<button data-id="${data.IdEx}" title="Dar de baja" type="button" class="btn btn-sm iconGeneralNegro deleteExamen" ><i class="ri-delete-bin-2-line"></i></button>`,
                            
                            pago = `<button type="button" data-id="${data.IdEx}" data-nro="${nroFactura}" data-empresa="${empresa}" class="btn btn-sm botonGeneral cambiarBoton">${data.FechaPagado === '0000-00-00' ? 'Pagar' : 'Quitar pago'}</button>`;

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
            drawCallback: function() {
                $(document).on('change', '#estado', function() {
                    let valor = $(this).val();
                    mostrarBotonesPago(valor);
                    
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
                $(document).off('click', '#btn-show-all-children').on('click', '#btn-show-all-children', function(){
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
                $(document).off('click', '#btn-hide-all-children').on('click', '#btn-hide-all-children', function(){
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

            }
        });
        


        
    });

});