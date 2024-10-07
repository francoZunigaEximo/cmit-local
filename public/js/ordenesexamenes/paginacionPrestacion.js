function obtenerFormato(date) {
    return date.toISOString().slice(0, 10);
};

$(document).ready(()=>{

    //Botón de busqueda de Mapas
    $(document).on('click', '#buscarPres, .sesentaDias, .noventaDias, .totalDias, .ausenteDias, .hoyDias, .treintaDias', function() { 

        const diasPorClase = {
            'sesentaDias': 60,
            'noventaDias': 90,
            'totalDias': 90,
            'ausenteDias': 90,
            'hoyDias': 0,
            'treintaDias': 30
        };

        const clasesDias = [
            'sesentaDias',
            'noventaDias',
            'totalDias',
            'ausenteDias',
            'hoyDias',
            'treintaDias'
        ];

        const clasesAusente = {
            'sesentaDias': 'noAusente',
            'noventaDias': 'noAusente',
            'treintaDias': 'noAusente',
            'hoyDias': 'noAusente',
            'totalDias': 'todos',
            'ausenteDias': 'ausente'
        };

        const tiposPorClase = {
            'sesentaDias': 'todos',
            'totalDias': 'todos',
            'ausenteDias': 'todos',
            'treintaDias': 'todos',
            'hoyDias': 'interno'
        };

        const clasesConPendiente = [
            'sesentaDias',
            'noventaDias',
            'totalDias',
            'ausenteDias',
            'treintaDias'
        ];

        let ausente, tipo = null;

        let fechaHasta = $(this).hasClass('hoyDias')
            ? new Date() 
            : $(this).hasClass('sesentaDias') || $(this).hasClass('noventaDias') || $(this).hasClass('totalDias') || $(this).hasClass('ausenteDias') || $(this).hasClass('treintaDias')
                ? new Date(new Date().getTime() - 2 * 24 * 60 * 60 * 1000) //restamos 2 dias
                : $('#fechaHastaPres').val(); 
        
        const tieneClase = clasesDias.some(clase => $(this).hasClass(clase));

        let fechaDesde = tieneClase ? new Date(fechaHasta) : $('#fechaDesdePres').val();
        
        const clase = Object.keys(diasPorClase).find(c => $(this).hasClass(c));

        if (clase) {
            fechaDesde.setDate(fechaHasta.getDate() - diasPorClase[clase]);
            switch (clase) {
                case 'treintaDias':
                case 'sesentaDias':
                case 'noventaDias':
                case 'ausenteDias':
                case 'totalDias':
                    $('#fechaDesdePres').val(fechaDesde.toISOString().split('T')[0]);
                    $('#tipoPres').val('todos');
                    $('#pendientePres').attr('checked', true);
                    break;
                case 'hoyDias':
                    $('#fechaDesdePres').val(fechaDesde.toISOString().split('T')[0]);
                    $('#tipoPres').val('interno');
                    $('#pendientePres').attr('checked', false);
                    break;
            }
        } else {
            $('#fechaDesde').val();
        }

        if (tieneClase) {
            fechaDesde = obtenerFormato(fechaDesde);
            fechaHasta = obtenerFormato(fechaHasta);
        } else {
            $('#fechaDesdePres').val();
            $('#fechaHastaPres').val();
        }

        for (const clase in tiposPorClase) {
            if ($(this).hasClass(clase)) {
                tipo = tiposPorClase[clase];
                break; 
            }
        }

        if (!tipo) {
            tipo = $('#tipoPres').val();
        }
        
        for (const clase in clasesAusente) {
            if ($(this).hasClass(clase)) {
                ausente = clasesAusente[clase];
                break;
            }
        }
        
        let conPendiente = clasesConPendiente.some(clase => $(this).hasClass(clase)) ? 1 : ($('#pendientePres').prop('checked') ? 1 : 0);


        let adjuntoEfector = $(this).hasClass('hoyDias') ? 1 : null;

        if (fechaDesde === '' || fechaHasta === '') {
            toastr.warning("Las fechas son obligatorias");
            return;
        }

        $('#listaOrdenesPrestaciones').DataTable().clear().destroy();
       
        new DataTable("#listaOrdenesPrestaciones", {

            searching: false,
            ordering: false,
            processing: true,
            lengthChange: false,
            pageLength: 500,
            deferRender: true,
            responsive: false,
            serverSide: true,
            ajax: {
                url: SEARCHPRESTACION,
                data: function(d){
                    d.fechaDesde = fechaDesde;
                    d.fechaHasta = fechaHasta;
                    d.especialidad = $('#especialidadPres').val();
                    d.estado = $('#estadoPres').val();
                    d.efector = $('#efectorPres').val();
                    d.informador = $('#informadorPres').val();
                    d.profEfector = $('#profEfePres').val();
                    d.profInformador = $('#profInfPres').val();
                    d.tipo = tipo;
                    d.adjunto = $('#adjuntoPres').val();
                    d.examen = $('#examenPres').val();
                    d.pendiente = conPendiente;
                    d.vencido = $('#vencidoPres').prop('checked') ? 1:0;
                    d.ausente = ausente;
                    d.adjuntoEfector = adjuntoEfector;
                },

            },
            dataType: 'json',
            type: 'POST',
            columns: [
                {//1
                    data: null,
                    width: "80.3px",
                    render: function(data) {

                        return `<div id="listado" data-id="${data.IdItem}"><span title="${data.Especialidad}">${data.Especialidad}</span></div>`;
                    }
                },
                {//2
                    data: null,
                    width: "63.3px",
                    render: function(data) {
                        return `<div class="text-center">${fechaNow(data.Fecha,'/',0)}</div>`;
                    }
                },
                {//3
                    data: null,
                    width: "70.3px",
                    render: function(data) {
                        return `<div class="text-center">${data.IdPrestacion}</div>`;
                    } 
                },
                {//4
                    data: null,
                    render: function(data) {
                        return `<span title="${data.Empresa}">${data.Empresa}</span>`;
                    }
                },
                {//5
                    data: null,
                    render: function(data){
                        let NombreCompleto = data.NombrePaciente + ' ' + data.ApellidoPaciente;
                        return `<span title="${NombreCompleto}">${[null, undefined, ''].includes(NombreCompleto) ? '' : NombreCompleto}</span>`;
                    }
                },
                {
                    data: null,
                    width: "65.3px",
                    render: function(data) {
                        return data.Dni;
                    }
                },
                {//6
                    data: null,
                    width: "60.3px",
                    render: function(data){

                        let resultado = '';
                        resultado = ![undefined, null, ''].includes(data.estado) ? '' : resultado;

                        switch(data.estado) {

                            case 'Abierto':
                                resultado = '';
                                break;

                            case 'Cerrado':
                                resultado = '<div class="text-center"><span class="custom-badge verde">Cerrado</span></div>';
                                break;

                            case 'Finalizado':
                                resultado = '<div class="text-center"><span class="custom-badge verde">Finalizado</span></div>';
                                break;

                            case 'Entregado':
                                resultado = '<div class="text-center"><span class="custom-badge verde">Entregado</span></div>';
                                break;
                            
                            default:
                                resultado = '';
                                break;
                        }

                        console.log(resultado);
                        return resultado;  
                        
                    }
                },
                {
                    data: null,
                    render: function(data) {
                        return data.eEnv;
                    }
                },
                {//7
                    data: null,
                    render: function(data) {
                        return `<span title="${data.Examen}">${data.Examen}</span>`;
                    }
                },
                {//8
                    data: null,
                    render: function(data){
                        let nombre = [null, '', 0].includes(data.NombreProfesional) ? '' : data.NombreProfesional,
                            apellido = [null, '', 0].includes(data.ApellidoProfesional) ? '' : data.ApellidoProfesional;
                        let NombreCompleto = nombre + ' ' + apellido;
                        return `<span title="${NombreCompleto}">${NombreCompleto}</span>`
                    }
                },
                
                {//9
                    data: null,
                    render: function(data){
                        return `<div class="text-center">
                        ${![undefined, null, ''].includes(data.EstadoEfector)
                            ? data.EstadoEfector === 'Pendiente'
                                ? '<span class="custom-badge rojo">Abie</span>'
                                : data.EstadoEfector === 'Cerrado'
                                    ? '<span class="custom-badge verde">Cerr</span>'
                                    : '<span class="custom-badge rojo">Abie</span>'
                            : '<span class="custom-badge rojo">Abie</span>'}
                        </div>`;
                    }
                },
                {//10
                    data: null,
                    render: function(data){
                        return `<div class="text-center ${['Pdte_D','Pdte_F'].includes(data.Adj) ? 'rojo' : 'verde'}">${data.Adj}</div>`;
                    }
                },
                {//11
                    data: null,
                    render: function(data){
                        let NombreCompleto = data.NombreProfesional2 + ' ' + data.ApellidoProfesional2;
                        return `<span title="${NombreCompleto}">${NombreCompleto}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data) {
                        let resultado = '';
                
                        if (![undefined, null, ''].includes(data.EstadoInformador)) {
                            switch(data.EstadoInformador) {
                                case 'Pendiente':
                                    resultado = '<span class="custom-badge rojo">Pdte</span>';
                                    break;
                
                                case 'Borrador':
                                    resultado = '<span class="custom-badge naranja">Borr</span>';
                                    break;
                
                                case 'Cerrado':
                                    resultado = '<span class="custom-badge verde">Cerr</span>';
                                    break;
                
                                default:
                                    resultado = '';
                                    break;
                            }
                        }
                
                        return resultado;
                    }
                },
                {//13
                    data: null,
                    render: function(data) {
                        let fecha = new Date(data.Fecha + 'T00:00'); 
                        fecha.setDate(fecha.getDate() + parseInt(data.DiasVencimiento));

                        let dia = String(fecha.getDate()).padStart(2, '0'),
                            mes = String(fecha.getMonth() + 1).padStart(2, '0'),
                            ano = fecha.getFullYear();
                
                        return `<span class="custom-badge generalNegro">${dia}/${mes}/${ano}</span>`;
                    }
                },         
                {//13
                    data: null,
                    render: function(data) {


                        let editarEx = `<a title="Editar examen" href="${linkItemPrestacion}/${data.IdItem}/edit" target="_blank"><button type="button" class="btn btn-sm iconGeneralNegro"><i class="ri-edit-line"></i></button></a>`,
                        
                        editarPres = `<a title="Editar prestación" href="${linkPrestaciones}/${data.IdPrestacion}/edit" target="_blank"><button type="button" class="btn btn-sm iconGeneralNegro"><i class="far fa-address-card"></i></button></a>`;

                        return editarEx + ' ' + editarPres;
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
                info: "Mostrando _START_ a _END_ de _TOTAL_ de examenes",
            }
        });

    });
});