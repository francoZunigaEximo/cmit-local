function getFecha(fecha){

    let fechaActual = new Date();
    let fechaLimiteAdmision = new Date(fecha);
    let diff = fechaLimiteAdmision.getTime() - fechaActual.getTime();
   
    return (Math.round(diff/(1000*60*60*24)));
}


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



$(document).ready(()=>{

        //Botón de busqueda de Mapas
        $(document).on('click', '#buscar', function() {

            $('#listaMapas').DataTable().clear().destroy();
    
            new DataTable("#listaMapas", {

                searching: false,
                ordering: false,
                processing: true,
                lengthChange: false,
                pageLength: 15,
                responsive: true,
                serverSide: true,
                ajax: {
                    url: SEARCH,
                    data: function(d){
                        d.Nro = $('#Nro').val();
                        d.Art = $('#ART').val();
                        d.Empresa = $('#Empresa').val();
                        d.Estado = $('#Estado').val();
                        d.corteDesde = $('#corteDesde').val();
                        d.corteHasta = $('#corteHasta').val();
                        d.entregaDesde = $('#entregaDesde').val();
                        d.entregaHasta = $('#entregaHasta').val();
                        d.Vencimiento = $('#Vencimiento').val();
                        d.Ver = $('#Ver').val();
                    }
                },
                dataType: 'json',
                type: 'POST',
                columns: [
                    {
                        data: null,
                        render: function(data){
                            return `<input data-paciente="${data.IdPaciente}" data-prestacion="${data.IdPrestacion}" type="checkbox" name="Id" value="${data.Id}" checked>`;
                        }
                    },
                    {
                        data: 'Nro',
                        name: 'Nro',
                    },
                    {
                        data: 'Art',
                        name: 'Art',
                    },
                    {
                        data: 'Empresa',
                        name: 'Empresa',
                    },
                    {
                        data:null,
                        render: function(data){
                            
                            let totalDias = getFecha(data.Fecha);
                            let fecha = fechaNow(data.Fecha,'/',1);

                            let style = (totalDias <= 0 ? 'rojo' : 'verde');

                            let contenido = `<div style="text-align: center">
                                                <span style="display:block">${fecha === 'NaN/NaN/NaN'? 'Sin fecha' : fecha}</span>
                                                <span class="custom-badge ${style}">${(totalDias === 'NaN'? 0 : totalDias)}</span>
                                            </div>`;

                            return contenido;
                            
                        }
                    },
                    {
                        data: null,
                        render: function(data){

                            let totalDias = getFecha(data.FechaE);
                            let fecha = fechaNow(data.FechaE,'/',1);

                            let style = (totalDias <= 0 && data.eEnviado === 1 ? 'violeta' : (totalDias <= 0 && data.eEnviado === 0? 'verde' : (totalDias <= 10 && totalDias >= 1 ? 'rojo' : (totalDias > 10 ? 'amarillo': 'violeta'))))

                            let contenido = `<div style="text-align: center">
                                                <span style="display:block">${(fecha === 'NaN/NaN/NaN'? 'Sin fecha' : fecha) }</span>
                                                <span class="custom-badge ${style}">${(totalDias === 'NaN'? 0 : totalDias)}</span>
                                            </div>`;
                            return contenido;
                        }
                    },
                    {
                        data: null,
                        render: function(data){
                            return `<div style="text-align: center">${data.contadorPrestaciones}</div>`;
                        } 

                    },
                    {
                        data: null,
                        render: function(data){
                            
                            let total = data.contadorPacientes - data.contadorPrestaciones;
                            return `<div style="text-align: center">${total}</div>`;
                        }
                    },
                    {
                        data: null,
                        render: function(data) {
                               
                                if(data.estado !== undefined){
                                    listaMapas
                                    return '<span class="badge badge-soft-success text-uppercase">' + data.estado + '</span>';
                                }else{

                                    if(data.eEnviado === 1){
                                        return '<span class="badge badge-soft-success text-uppercase">eEnviado</span>';
                                    }else if(data.Cerrado === 0 && data.Finalizado === 0){
                                        return '<span class="badge badge-soft-success text-uppercase">Abierto</span>';
                                    }else if(data.Cerrado === 1 && data.eEnviado === 1 && data.Entregado === 1){
                                        return '<span class="badge badge-soft-success text-uppercase">Terminado</span>';
                                    }else if(data.Cerrado === 1){
                                        return '<span class="badge badge-soft-success text-uppercase">Cerrado</span>';
                                    }else if(data.eEnviado === 0){
                                        return '<span class="badge badge-soft-success text-uppercase">No eEnviado</span>';
                                    }else{
                                        return '<span class="badge badge-soft-success text-uppercase">En proceso</span>';
                                    }
                                }
                                
                        }
                    },
                    {
                        data: null,
                        render: function(data){
                            
                            let editar = '<a title="Editar" href="'+ location.href + '/' + data.Id + '/edit">' + '<button type="button" class="btn btn-sm btn-primary edit-item-btn"><i class="ri-edit-line"></i></button>' + '</a>';
                            
                            let baja = `<button data-id="${data.Id}" type="button" class="btn btn-sm btn-danger deleteMapa" ><i class="ri-delete-bin-2-line"></i></button>`;
        
                            return editar + ' ' + baja;
                        }
                    }
                ],
                language: {
                    processing: "Cargando listado de mapas de CMIT",
                    emptyTable: "No hay mapas con los datos buscados",
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
                    info: "Mostrando _START_ a _END_ de _TOTAL_ de mapas",
                }
            });
    
        });


});