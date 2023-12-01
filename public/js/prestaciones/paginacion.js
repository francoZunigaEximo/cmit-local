$(document).ready(()=>{

    $('th.sort').off("click"); // Se coloca provisoriamente, cuando se defina el modelo de ordenado a utilizar se puede quitar.

    new DataTable("#listaPrestaciones", {

        searching: false,
        ordering: false,
        processing: true,
        lengthChange: false,
        pageLength: 15,
        responsive: true,
        serverSide: true,
        ajax: ROUTE,       
        dataType: 'json',
        type: 'POST',
        columns: [
            {
                data: null,
                render: function(data){
                    return `<div class="prestacionComentario" data-id="${ data.Id }" data-bs-toggle="modal" data-bs-target="#prestacionModal">
                            <i class="ri-chat-3-line"></i>
                    </div>`;
                }
            },
            {
                data: null,
                render: function(data){
                    return '<span ' + (data.Ausente === 1 ? 'style="padding: 0.5em; padding: 0.5em; background-color: red; color: white;" title="Ausente"' : (data.Incompleto === 1 ? 'style="padding: 0.5em; background-color: orange; color: black;" title="Incompleto"' : (data.Devol === 1 ? 'style="padding: 0.5em; background-color: blue; color: white;" title="Devol"' : (data.Forma === 1 ? 'style="padding: 0.5em; background-color: #0cb7f2; color: black;" title="Forma"' : (data.SinEsc === 1 ? 'style="padding: 0.5em; background-color: yellow; color: black;" title="Sin Esc"': ''))))) + '>' + data.Id + '</span>';
                }
            },
            {
                data: null,
                render: function(data){
                    return fechaNow(data.FechaAlta,'/',1);
                }
            },
            {
                data: null,
                render: function(data){
                    let prestacionRz = data.RazonSocial;
                    let recorteRz = prestacionRz.substring(0,15) + "...";
                    return `<span title="${data.RazonSocial}">${recorteRz}</span>`; 
                }
            },
            {
                data: null,
                render: function(data){
                    let prestacionPe = data.ParaEmpresa;
                    let recortePe = prestacionPe.substring(0,15) + "...";
                    return `<span title="${data.ParaEmpresa}">${recortePe}</span>`;
                }
            },
            {
                data: 'Identificacion',
                name: 'Identificacion',
            },
            {
                data: null,
                render: function(data){
                    let prestacionNom = data.Nombre;
                    let recorteNom = prestacionNom.substring(0,15) + "...";
                    return `<span title="${data.Apellido} ${data.Nombre}">${data.Apellido} ${recorteNom}</span>`;
                }
            },
            {
                data: null,
                render: function(data){
                    let prestacionArt = data.Art;
                    let recorteArt = prestacionArt.substring(0, 15) + "...";
                    return `<span title="${data.Art}">${recorteArt}</span>`;
                }
            },
            {
                data: null,
                render: function(data){
                    return '<span class="badge badge-soft-' + (data.Anulado == 0 ? "success" : "danger") + ' text-uppercase">' + (data.Anulado == 0 ? "Habilitado" : "Anulado") + '</span>';
                }
            },
            {
                data: null,
                    render: function(data){
                        
                        let pago;
                        if(data.Pago == "B"){
                            pago = 'Ctdo.';
                        }else if(data.Pago == "C"){
                            pago = 'CCorriente';
                        }else if (data.Pago == "P"){
                            pago = 'PCuenta';
                        }else{
                            pago = "-"
                        }
                        
                        return pago;
                    }  
            },
            {
                data: null,
                render: function(data){
                    let editar = `<a title="Editar" href="${location.href}/${data.Id}/edit">
                                    <button type="button" class="btn btn-sm btn-primary edit-item-btn">
                                        <i class="ri-edit-line"></i>
                                    </button>
                                  </a>`;
                    
                    let bloquear = '<a ' + (data.Anulado == 1 ? 'onclick="return false;"' : 'href="' + rutaBlock + data.Id + '"  onclick="return confirm(\'¿Estás seguro de que deseas Inhabilitar esta prestación?\')"') + '">' +
                    '<button type="button" class="btn btn-sm btn-warning remove-item-btn" title="' + (data.Anulado == 1 ? "Anulado" : "Anular") + '" ' + (data.Anulado == 1 ? "disabled" : "") + '><i class="ri-forbid-2-line"></i></button>' +
                '</a>';

                    let baja = `<button data-id="${data.Id}" type="button" class="btn btn-sm btn-danger downPrestacion"><i class="ri-delete-bin-2-line"></i></button>`;

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
        
    });


    $('#buscarPrestaciones').on('click', function(event) {

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
        let finalizado = $('Finalizado').val();
        let facturado = $('#Facturado').val();
        let entregado = $('#Entregado').val();

        if((fechaDesde == '' || fechaHasta == '') && nroprestacion == ''){
            swal('Alerta','La fecha "Desde" y "Hasta" son obligatorias.', 'warning');
            return;
        }

        $('#listaPrestaciones').DataTable().clear().destroy();

        new DataTable("#listaPrestaciones", {

            searching: false,
            ordering: true,
            order: [[0, 'desc'],[1, 'desc'],[2, 'desc'],[3, 'desc'], [4, 'desc'], [5, 'desc'], [6, 'desc'], [7, 'desc'], [8, 'desc'], [9, 'desc'], [10, 'desc']],
            processing: true,
            lengthChange: false,
            pageLength: 15,
            responsive: true,
            serverSide: true,
            ajax: {
                url: SEARCH,
                data: function(e){
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
            },
            dataType: 'json',
            type: 'POST',
            columnDefs: [
                {
                    data: null,
                    name: 'Id',
                    orderable: false,
                    targets: 0,
                    render: function(data){
                        return `<div class="prestacionComentario" data-id="${ data.Id }" data-bs-toggle="modal" data-bs-target="#prestacionModal">
                                <i class="ri-chat-3-line"></i>
                        </div>`;
                    }
                },
                {
                    data: null,
                    name: 'Id',
                    orderable: true,
                    targets: 1,
                    render: function(data){
                        return '<span ' + (data.Ausente === 1 ? 'style="padding: 0.5em; background-color: red; color: white;" title="Ausente"' : (data.Incompleto === 1 ? 'style="padding: 0.5em; background-color: orange; color: black;" title="Incompleto"' : (data.Devol === 1 ? 'style="padding: 0.5em; background-color: blue; color: white;" title="Devol"' : (data.Forma === 1 ? 'style="padding: 0.5em; background-color: #0cb7f2; color: black;" title="Forma"' : (data.SinEsc === 1 ? 'style="padding: 0.5em; background-color: yellow; color: black;" title="Sin Esc"': ''))))) + '>' + data.Id + '</span>';
                    }

                },
                {
                    data: null,
                    name: 'FechaAlta',
                    targets: 2,
                    orderable: true,
                    render: function(data){
                        return fechaNow(data.FechaAlta,'/',1);
                    }
                },
                {
                    data: null,
                    name: 'empresa',
                    orderable: true,
                    targets: 3,
                    render: function(data){
                        let prestacionRz = data.empresa;
                        let recorteRz = prestacionRz.substring(0,15) + "...";
                        return `<span title="${data.empresa}">${recorteRz}</span>`; 
                    }
                },
                {
                    data: null,
                    name: 'ParaEmpresa',
                    orderable: true,
                    targets: 4,
                    render: function(data){
                        let prestacionPe = data.ParaEmpresa;
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
                        let prestacionNom = data.Nombre;
                        let recorteNom = prestacionNom.substring(0,15) + "...";
                        return `<span title="${data.Apellido} ${data.Nombre}">${data.Apellido} ${recorteNom}</span>`;
                    }
                },
                {
                    data: null,
                    name: 'Art',
                    orderable: true,
                    targets: 7,
                    render: function(data){
                        let prestacionArt = data.Art;
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
                        
                        let pago;
                        if(data.Pago == "B"){
                            pago = 'Ctdo.';
                        }else if(data.Pago == "C"){
                            pago = 'CCorriente';
                        }else if (data.Pago == "P"){
                            pago = 'PCuenta';
                        }else{
                            pago = "-"
                        }
                        
                        return pago;     
                    
                    }
                },
                {
                    data: null,
                    name: 'Id',
                    orderable: false,
                    targets: 10,
                    render: function(data){
                        let editar = `<a title="Editar" href="${location.href}/${data.Id}/edit"><button type="button" class="btn btn-sm btn-primary edit-item-btn"><i class="ri-edit-line"></i></button></a>`;
                        
                        let bloquear = '<a ' + (data.Anulado == 1 ? 'onclick="return false;"' : 'href="' + rutaBlock + data.Id + '"  onclick="return confirm(\'¿Estás seguro de que deseas Inhabilitar esta prestación?\')"') + '">' +
                        '<button type="button" class="btn btn-sm btn-warning remove-item-btn" title="' + (data.Anulado == 1 ? "Anulado" : "Anular") + '" ' + (data.Anulado == 1 ? "disabled" : "") + '><i class="ri-forbid-2-line"></i></button>' +
                    '</a>';

                    let baja = `<button data-id="${data.Id}" type="button" class="btn btn-sm btn-danger downPrestacion" ><i class="ri-delete-bin-2-line"></i></button>`;
    
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
        });

    });


    function fechaNow(fechaAformatear, divider, format) {
        let fechaActual;
    
        if (fechaAformatear === null) {
            fechaActual = new Date();
        } else {
            fechaActual = new Date(fechaAformatear);
        }
    
        let dia = fechaActual.getDate();
        let mes = fechaActual.getMonth() + 1;
        let anio = fechaActual.getFullYear();
    
        dia = dia < 10 ? '0' + dia : dia;
        mes = mes < 10 ? '0' + mes : mes;
    
        return (format === 1) ? dia + divider + mes + divider + anio : anio + divider + mes + divider + dia;
    }


});