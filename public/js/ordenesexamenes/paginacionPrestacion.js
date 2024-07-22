function obtenerFormato(date) {
    return date.toISOString().slice(0, 10);
};

$(document).ready(()=>{

    //Botón de busqueda de Mapas
    $(document).on('click', '#buscarPres, .sesentaDias, .noventaDias, .totalDias, .ausenteDias, .hoyDias, .treintaDias', function() { 

        let hoy = new Date().toLocaleDateString('en-CA');
        let fechaHasta = $(this).hasClass('hoyDias')
            ? new Date() 
            : $(this).hasClass('sesentaDias') || $(this).hasClass('noventaDias') || $(this).hasClass('totalDias') || $(this).hasClass('ausenteDias') || $(this).hasClass('treintaDias')
                ? new Date(new Date().getTime() - 2 * 24 * 60 * 60 * 1000) 
                : $('#fechaHastaPres').val(); 
        
        let fechaDesde = $(this).hasClass('sesentaDias') || $(this).hasClass('noventaDias') || $(this).hasClass('totalDias') || $(this).hasClass('ausenteDias') || $(this).hasClass('hoyDias') || $(this).hasClass('treintaDias')
            ? new Date(fechaHasta) 
            : $('#fechaDesdePres').val();
        
        $(this).hasClass('sesentaDias') 
            ? fechaDesde.setDate(fechaHasta.getDate() - 60) 
            : $(this).hasClass('noventaDias') 
                ? fechaDesde.setDate(fechaHasta.getDate() - 90) 
                : $(this).hasClass('totalDias')
                    ?  fechaDesde.setDate(fechaHasta.getDate() - 90) 
                    : $(this).hasClass('ausenteDias') 
                        ? fechaDesde.setDate(fechaHasta.getDate() - 90) 
                        : $(this).hasClass('hoyDias') 
                            ? fechaDesde.setDate(fechaHasta.getDate() - 0) 
                            : $(this).hasClass('treintaDias')
                                ? fechaDesde.setDate(fechaHasta.getDate() - 30) 
                                : $('#fechaDesde').val();
        
        $(this).hasClass('sesentaDias') || $(this).hasClass('noventaDias') || $(this).hasClass('totalDias') || $(this).hasClass('ausenteDias') || $(this).hasClass('hoyDias') || $(this).hasClass('treintaDias') ? fechaDesde = obtenerFormato(fechaDesde) : $('#fechaDesdePres').val();
        $(this).hasClass('sesentaDias') || $(this).hasClass('noventaDias') || $(this).hasClass('totalDias') || $(this).hasClass('ausenteDias') || $(this).hasClass('hoyDias') || $(this).hasClass('treintaDias') ? fechaHasta = obtenerFormato(fechaHasta) : $('#fechaHastaPres').val();

        let tipo = $(this).hasClass('sesentaDias') || $(this).hasClass('totalDias') || $(this).hasClass('ausenteDias') || $(this).hasClass('treintaDias')
                        ? 'todos' 
                        : $(this).hasClass('hoyDias')
                            ?  'interno'
                            : $('#tipoPres').val();
        
        let ausente = $(this).hasClass('sesentaDias') ||  $(this).hasClass('noventaDias') || $(this).hasClass('treintaDias') || $(this).hasClass('hoyDias')
                        ? 'noAusente' 
                        : $(this).hasClass('totalDias') 
                            ? 'todos'
                            : $(this).hasClass('ausenteDias')
                                ? 'ausente'
                                : null;
        
        let conPendiente = $(this).hasClass('sesentaDias') || $(this).hasClass('noventaDias') || $(this).hasClass('totalDias') || $(this).hasClass('ausenteDias') || $(this).hasClass('treintaDias') ? 1 : (($('#pendientePres').prop('checked') ? 1:0));

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
            responsive: true,
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
                    render: function(data) {

                        return `<div id="listado" data-id="${data.IdItem}"><span title="${data.Especialidad}">${acortadorTexto(data.Especialidad, 11)}</span></div>`;
                    }
                },
                {//2
                    data: null,
                    render: function(data) {
                        return `<div class="text-center">${fechaNow(data.Fecha,'/',0)}</div>`;
                    }
                },
                {//3
                    data: null,
                    render: function(data) {
                        return `<div class="text-center">${data.IdPrestacion}</div>`;
                    } 
                },
                {//4
                    data: null,
                    render: function(data) {

                        return `<span title="${data.Empresa}">${acortadorTexto(data.Empresa, 7)}</span>`;
                    }
                },
                {//5
                    data: null,
                    render: function(data){
                        let NombreCompleto = data.NombrePaciente + ' ' + data.ApellidoPaciente;
                        return `<span title="${NombreCompleto}">${acortadorTexto(NombreCompleto, 9)}</span>`;
                    }
                },
                {//6
                    data: null,
                    render: function(data){
                        return data.estado !== undefined 
                            ? data.estado === 'Abierto'
                                ? '<div class="text-center"><span class="custom-badge rojo">Abierto</span></div>'
                                : data.estado === 'Cerrado'
                                    ? '<div class="text-center"><span class="custom-badge verde">Cerrado</span></div>'
                                    : data.estado === 'Finalizado'
                                        ? '<div class="text-center"><span class="custom-badge verde">Finalizado</span></div>'
                                        : data.estado === 'Entregado'
                                            ? '<div class="text-center"><span class="custom-badge verde">Entregado</span></div>'
                                            : data.estado === 'eEnviado'
                                                ? '<div class="text-center"><span class="custom-badge verde">eEnviado</span></div>'
                                                : '-'
                            : `<div class="text-center"><span class="custom-badge gris">${data.estado}</span></div>`;    
                    }
                },
                {//7
                    data: null,
                    render: function(data) {
                        return `<span title="${data.Examen}">${acortadorTexto(data.Examen, 7)}</span>`;
                    }
                },
                {//8
                    data: null,
                    render: function(data){
                        let NombreCompleto = data.NombreProfesional + ' ' + data.ApellidoProfesional;
                        return `<span title="${NombreCompleto}">${acortadorTexto(NombreCompleto, 6)}</span>`
                    }
                },
                {//9
                    data: null,
                    render: function(data){
                        console.log(data.EstadoEfector);
                        return `<div class="text-center">
                        ${data.EstadoEfector !== undefined 
                            ? data.EstadoEfector === 'Pendiente'
                                ? '<span class="custom-badge rojo">Pendiente</span>'
                                : data.EstadoEfector === 'Cerrado'
                                    ? '<span class="custom-badge verde">Cerrado</span>'
                                    : ' - '
                            : '<span class="custom-badge gris">' + data.EstadoEfector + '</span>'}
                        </div>`;
                    }
                },
                {//10
                    data: null,
                    render: function(data){
                        return `<div class="text-center">${data.NoImprime === 1 ? 'ADJ_D' : 'ADJ_F'}</div>`;
                    }
                },
                {//11
                    data: null,
                    render: function(data){
                        let NombreCompleto = data.NombreProfesional2 + ' ' + data.ApellidoProfesional2;
                        return `<span title="${NombreCompleto}">${acortadorTexto(NombreCompleto)}</span>`;
                    }
                },
                {//12
                    data: null,
                    render: function(data) {

                        return `<div class="text-center">
                        ${data.EstadoInformador !== undefined 
                            ? data.Informador === 'Pendiente' 
                                ? '<span class="custom-badge rojo">Pendiente</span>'
                                : data.Informador === 'Borrador' 
                                    ? '<span class="custom-badge rojo">Borrador</span>'
                                    : data.Informador === "Cerrado"
                                        ? '<span class="custom-badge verde">Cerrado</span>'
                                        : '<span class="custom-badge gris">-</span>'
                            : data.EstadoInformador}
                        </div>`;
                    }
                },
                /*{//13
                    data: null,
                    render: function(data) {
                        let fecha = new Date(data.Fecha + 'T00:00'); 
                        fecha.setDate(fecha.getDate() + parseInt(data.DiasVencimiento));

                        let dia = String(fecha.getDate()).padStart(2, '0');
                        let mes = String(fecha.getMonth() + 1).padStart(2, '0');
                        let ano = fecha.getFullYear();
                
                        return `<span class="custom-badge generalNegro">${dia}/${mes}/${ano}</span>`;
                    }
                },  */        
                {//13
                    data: null,
                    render: function(data) {


                        let editarEx = `<a title="Editar examen" href="${linkItemPrestacion}/${data.IdItem}/edit" target="_blank"><button type="button" class="btn btn-sm iconGeneralNegro"><i class="ri-edit-line"></i></button></a>`,
                        
                        editarPres = `<a title="Editar prestación" href="${linkPrestaciones}/${data.IdPrestacion}/edit" target="_blank"><button type="button" class="btn btn-sm iconGeneralNegro"><i class="ri-edit-box-line"></i></button></a>`;

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

        function acortadorTexto(cadena, nroCaracteres = 10) {
            return cadena.length <= nroCaracteres ? cadena : cadena.substring(0,nroCaracteres);
        }

    });
});