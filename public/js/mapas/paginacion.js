$(document).ready(()=>{

        //Botón de busqueda de Mapas
        $(document).on('click', '#buscar', function() {

            $('#listaMapas').DataTable().clear().destroy();
    
            new DataTable("#listaMapas", {

                searching: false,
                ordering: false,
                processing: true,
                lengthChange: false,
                pageLength: 50,
                deferRender: true,
                responsive: false,
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
                        width: 30,
                        render: function(data){
                            return `<input data-paciente="${data.IdPaciente}" data-prestacion="${data.IdPrestacion}" type="checkbox" name="Id" value="${data.Id}" checked>`;
                        }
                    },
                    {
                        data: 'Nro',
                        name: 'Nro',
                        width: 40,
                    },
                    {
                        data: null,
                        render: function(data){
                            return `<span title="Art: ${data.Art} - ParaEmpresa: ${data.ParaEmpresa_Art} - Alias: ${data.NombreFantasia_Art}">${data.Art}</span>`;
                        }
                    },
                    {
                        data: null,
                        render: function(data){
                            return `<span title="Empresa: ${data.Empresa} - ParaEmpresa: ${data.ParaEmpresa_Empresa} - Alias: ${data.NombreFantasia_Empresa}">${data.Empresa}</span>`;
                        }
                    },
                    {
                        data:null,
                        width: 60,
                        render: function(data){
                            
                            let totalDias = getDias(data.Fecha);
                            let fecha = fechaNow(data.Fecha,'/',0);

                            let contenido = `<div class="text-center">
                                                <span>${fecha === 'NaN/NaN/NaN'? '-' : fecha}</span>
                                                ${(totalDias <= 0) ? '<span class="custom-badge generalNegro">Cerrado</span>' : ''}
                                            </div>`;

                            return contenido;
                            
                        }
                    },
                    {
                        data: null,
                        width: 60,
                        render: function(data){

                            let totalDias = getDias(data.FechaE),
                                fecha = fechaNow(data.FechaE,'/',0);

                            let contenido = `<div class="text-center">
                                                <span>${(fecha === 'NaN/NaN/NaN'? 'Sin fecha' : fecha) }</span>
                                                <span class="custom-badge generalNegro">${(totalDias === NaN || totalDias < 0 ? 0 : totalDias)}</span>
                                            </div>`;
                            return contenido;
                        }
                    },
                    {
                        data: null,
                        width: 50,
                        render: function(data){

                            return `<div class="text-center">${data.contadorPrestaciones}/${data.contadorPacientes === 0 && data.contadorPrestaciones !== 0 ? data.contadorPrestaciones : data.contadorPacientes}</div>`;
                        } 

                    },
                    {
                        data: null,
                        width: 50,
                        render: function(data) {
                            
                            let enviados = data.contadorPrestaciones > 0 && data.contadorPrestaciones === data.cdorEEnviados, 
                                noEnviados = data.contadorPrestaciones > 0 && data.contadorPrestaciones !== data.cdorEEnviados,
                                cerrado = data.contadorPrestaciones > 0 && data.contadorPrestaciones === data.cdorCerrados && data.contadorPrestaciones !== data.cdorFinalizados,
                                abierto = data.contadorPrestaciones > 0 && data.contadorPrestaciones !== data.cdorCerrado && data.contadorPrestaciones !== data.cdorFinalizados,
                                terminado = data.contadorPrestaciones > 0 && data.contadorPrestaciones === data.cdorCerrados && data.contadorPrestaciones ===data.cdorEEnviados;

                                let conteo = '(Total Prestaciones: ' + data.contadorPrestaciones + ') (Total Cerrados: ' + data.cdorCerrados + ') (Total Finalizados: ' + data.cdorFinalizados + ') (Total eEnviados:' + data.cdorEEnviados + ')';

                            if(enviados){
                                return '<span title="' + conteo + '" class="custom-badge generalNegro">eEnviado</span>';
                            }else if(abierto){
                                return '<span title="' + conteo + '" class="custom-badge generalNegro">Abierto</span>';
                            }else if(terminado){
                                return '<span title="' + conteo + '" class="custom-badge generalNegro">Terminado</span>';
                            }else if(cerrado){
                                return '<span title="' + conteo + '" class="custom-badge generalNegro">Cerrado</span>';
                            }else if(noEnviados){
                                return '<span title="' + conteo + '" class="custom-badge generalNegro">No eEnviado</span>';
                            }else if(data.contadorPrestaciones === 0){
                                return '<span title="' + conteo + '" class="custom-badge generalNegro">Vacío</span>';
                            }       
                        }
                    },
                    {
                        data: null,
                        width: 80,
                        render: function(data){
                            
                            let editar = '<a title="Editar" href="'+ location.href + '/' + data.Id + '/edit">' + '<button type="button" class="btn btn-sm iconGeneral edit-item-btn"><i class="ri-edit-line"></i></button>' + '</a>';
                            
                            let baja = `<button data-id="${data.Id}" type="button" class="btn btn-sm iconGeneral deleteMapa" ><i class="ri-delete-bin-2-line"></i></button>`;
        
                            return editar + ' ' + baja;
                        }
                    }
                ],
                language: {
                    processing: "<div style='text-align: center; margin-top: 20px;'><img src='./images/spinner.gif' /><p>Cargando...</p></div>",
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
                },
                createdRow: function (row, data, dataIndex) {

                    let totalDias = getDias(data.FechaE);
                    let resultado;

                    if (totalDias >= 11 && totalDias <= 15 && data.eEnviado === 0) {
                        resultado = $(row).addClass('fondo-amarillo');
                    } else if (totalDias >= 1 && totalDias <= 10 && data.eEnviado === 0) {
                        resultado = $(row).addClass('fondo-naranja');
                    } else if (totalDias <= 0 && data.eEnviado === 0) {
                        resultado = $(row).addClass('fondo-rojo');
                    } else if (data.contadorPrestaciones > 0 && data.contadorPrestaciones === data.cdorEEnviados) {
                        resultado = $(row).addClass('fondo-verde');
                    }
                
                    return resultado;
                }
            });
    
        });

        function getDias(fecha){

            let fechaActual = new Date();
            let fechaLimiteAdmision = new Date(fecha);
            let diff = fechaLimiteAdmision.getTime() - fechaActual.getTime();
           
            return (Math.round(diff/(1000*60*60*24)));
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


});