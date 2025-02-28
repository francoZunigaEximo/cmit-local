$(function() {

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
            toastr.warning("Las fechas son obligatorias", "", {timeOut: 1000});
            return;
        }

        $('#listaOrdenesPrestaciones').DataTable().clear().destroy();
       
        new DataTable("#listaOrdenesPrestaciones", {

            searching: false,
            ordering: true,
            processing: true,
            lengthChange: false,
            pageLength: 500,
            deferRender: true,
            responsive: true,
            serverSide: true,
            stateSave: true,
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
                    data: "Especialidad",
                    width: "80.3px",
                    render: function(data, type, row) {

                        return `<div id="listado" data-id="${row.IdItem}"><span title="${data}">${data}</span></div>`;
                    }
                },
                {//2
                    data: "Fecha",
                    width: "63.3px",
                    render: function(data, type, row) {
                        return `<div class="text-center">${fechaNow(data,'/',0)}</div>`;
                    }
                },
                {//3
                    data: "IdPrestacion",
                    width: "70.3px",
                    render: function(data, type, row) {
                        return `<div class="text-center">${data}</div>`;
                    } 
                },
                {//4
                    data: "Empresa",
                    render: function(data, type, row) {
                        return `<span title="${data}">${data}</span>`;
                    }
                },
                {//5
                    data: "NombreCompleto",
                    render: function(data, type, row){
                        return `<span title="${data}">${[null, undefined, ''].includes(data) ? '' : data}</span>`;
                    }
                },
                {
                    data: "Dni",
                    width: "65.3px",
                    render: function(data, type, row) {
                        return data;
                    }
                },
                {//6
                    data: "estado",
                    width: "60.3px",
                    render: function(data, type, row){

                        switch(data) {

                            case 'Abierto':
                                return '';
                            case 'Cerrado':
                                return '<div class="text-center"><span class="custom-badge verde">Cerrado</span></div>';
                            case 'Finalizado':
                                return '<div class="text-center"><span class="custom-badge verde">Finalizado</span></div>';
                            case 'Entregado':
                                return '<div class="text-center"><span class="custom-badge verde">Entregado</span></div>';
                            default:
                                return '';
                        }     
                    }
                },
                {
                    data: "eEnv",
                    render: function(data, type, row) {
                        return data;
                    }
                },
                {//7
                    data: "Examen",
                    width: "50px",
                    render: function(data, type, row) {
                        return `<span title="${data}">${data}</span>`;
                    }
                },
                {//8
                    data: "profesionalEfector",
                    render: function(data, type, row){

                        let profesional = data;
                        return ![null, undefined, ''].includes(profesional) ? `<span title="${profesional}">${profesional}</span>` : '';
                    }
                },
                {//9
                    data: "EstadoEfector",
                    render: function(data, type, row){
                        switch (data) {
                            case 'Pendiente':
                                return '<span class="custom-badge rojo">Abie</span>';
                            case 'Cerrado':
                                return '<span class="custom-badge verde">Cerr</span>';
                            default:
                                return '<span class="custom-badge rojo">Abie</span>';
                        }
                    }
                },
                {//10
                    data: "Adj",
                    render: function(data, type, row){
                        return `<div class="text-center ${['Pdte_D','Pdte_F'].includes(data) ? 'rojo' : 'verde'}">${data}</div>`;
                    }
                },
                {//11
                    data: "profesionalInformador",
                    render: function(data, type, row){
                        let profesional = data;
                        return ![null, undefined, ''].includes(profesional) ? `<span title="${profesional}">${profesional}</span>` : '';
                    }
                },
                {
                    data: "EstadoInformador",
                    render: function(data, type, row) {
                        let resultado = '';
                
                        if (![undefined, null, ''].includes(data)) {
                            switch(data) {
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
                    data: "DiasVencimiento",
                    render: function(data, type, row) {
                        let fecha = new Date(row.Fecha + 'T00:00'); 
                        fecha.setDate(fecha.getDate() + parseInt(data));

                        let dia = String(fecha.getDate()).padStart(2, '0'),
                            mes = String(fecha.getMonth() + 1).padStart(2, '0'),
                            ano = fecha.getFullYear();
                
                        return `<span class="custom-badge generalNegro">${dia}/${mes}/${ano}</span>`;
                    }
                },         
                {//13
                    data: "Acciones",
                    render: function(data, type, row) {


                        let editarEx = `<a title="Editar examen" href="${linkItemPrestacion}/${row.IdItem}/edit" target="_blank"><button type="button" class="btn btn-sm iconGeneralNegro"><i class="ri-edit-line"></i></button></a>`,
                        
                        editarPres = `<a title="Editar prestación" href="${linkPrestaciones}/${row.IdPrestacion}/edit" target="_blank"><button type="button" class="btn btn-sm iconGeneralNegro"><i class="far fa-address-card"></i></button></a>`;

                        return editarEx + ' ' + editarPres;
                    }
                }
            ],
            language: {
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
});