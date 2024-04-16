function obtenerFormato(date) {
    return date.toISOString().slice(0, 10);
};

$(document).ready(()=>{

    //Botón de busqueda de Mapas
    $(document).on('click', '#buscarPres, .sesentaDias, .noventaDias, .totalDias, .ausenteDias', function() {

        let fechaHasta = $(this).hasClass('sesentaDias') || $(this).hasClass('noventaDias') || $(this).hasClass('totalDias') || $(this).hasClass('ausenteDias')
            ? new Date() 
            : $('#fechaHastaPres').val(); 
        
        let fechaDesde = $(this).hasClass('sesentaDias') || $(this).hasClass('noventaDias') || $(this).hasClass('totalDias') || $(this).hasClass('ausenteDias')
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
                        : $('#fechaDesde').val();
        
        $(this).hasClass('sesentaDias') || $(this).hasClass('noventaDias') || $(this).hasClass('totalDias') || $(this).hasClass('ausenteDias') ? fechaDesde = obtenerFormato(fechaDesde) : $('#fechaDesdePres').val();
        $(this).hasClass('sesentaDias') || $(this).hasClass('noventaDias') || $(this).hasClass('totalDias') || $(this).hasClass('ausenteDias') ? fechaHasta = obtenerFormato(fechaHasta) : $('#fechaHastaPres').val();

        let tipo = $(this).hasClass('sesentaDias') || $(this).hasClass('totalDias') || $(this).hasClass('ausenteDias') ? 'todos' : $('#tipoPres').val();
        
        let ausente = $(this).hasClass('sesentaDias') ||  $(this).hasClass('noventaDias') 
                        ? 'noAusente' 
                        : $(this).hasClass('totalDias') 
                            ? 'todos'
                            : $(this).hasClass('ausenteDias')
                                ? 'ausente'
                                : null;
        
        let conPendiente = $(this).hasClass('sesentaDias') || $(this).hasClass('noventaDias') || $(this).hasClass('totalDias') || $(this).hasClass('ausenteDias') ? 1 : (($('#pendientePres').prop('checked') ? 1:0));

        if (fechaDesde === '' || fechaHasta === '') {
            toastr.warning("Las fechas son obligatorias");
            return;
        }

        $('#listaOrdenesPrestaciones').DataTable().clear().destroy();
        let currentDraw = 1;

        new DataTable("#listaOrdenesPrestaciones", {

            searching: false,
            ordering: false,
            processing: true,
            lengthChange: false,
            pageLength: 100,
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
                    d.page = d.start / d.length + 1;
                },
                dataSrc: function (response) {
                    let data = {
                        draw: currentDraw,
                        recordsTotal: response.total,
                        recordsFiltered: response.total,
                        data: response.data,
                    };
    
                    currentDraw++;
    
                    return data.data;
                },
            },
            dataType: 'json',
            type: 'POST',
            columns: [
                {//1
                    data: null,
                    render: function(data) {

                        let recorte = (data.Especialidad).substring(0,5) + "...";
                        return recorte.length >= 5 ? `<div id="listado" data-id="${data.IdItem}"><span title="${data.Especialidad}">${recorte}</span></div>` : `<div id="listado" data-id="${data.IdItem}">${data.Especialidad}</div>`;
                    }
                },
                {//2
                    data: null,
                    render: function(data) {
                        return `<div class="text-center"><span class="custom-badge generalNegro">${fechaNow(data.Fecha,'/',0)}</span></div>`;
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

                        let recorte = (data.Empresa).substring(0,5) + "...";
                        return recorte.length >= 5 ? `<span title="${data.Empresa}">${recorte}</span>` : data.Empresa;
                    }
                },
                {//5
                    data: null,
                    render: function(data){
                        let NombreCompleto = data.NombrePaciente + ' ' + data.ApellidoPaciente;
                        let recorte = (NombreCompleto).substring(0,5) + "...";
                        return recorte.length >= 5 ? `<span title="${NombreCompleto}">${recorte}</span>` : NombreCompleto;
                    }
                },
                {//6
                    data: null,
                    render: function(data){
                        return data.estado === undefined 
                            ? (data.PresCerrado === 0 && data.PresFinalizado === 0 && data.PresEntregado === 0 && data.PresEnviado === 0
                                ? '<div class="text-center"><span class="custom-badge generalNegro">Abierto</span></div>'
                                : (data.PresCerrado === 1 && data.PresFinalizado === 0 && data.PresEntregado === 0 && data.PresEnviado === 0
                                    ? '<div class="text-center"><span class="custom-badge generalNegro">Cerrado</span></div>'
                                    : (data.PresCerrado === 1 && data.PresFinalizado === 1 && data.PresEntregado === 0 && data.PresEnviado === 0
                                        ? '<div class="text-center"><span class="custom-badge generalNegro">Finalizado</span></div>'
                                        : (data.PresCerrado === 1 && data.PresFinalizado === 1 && data.PresEntregado === 1 && data.PresEnviado === 0
                                            ? '<div class="text-center"><span class="custom-badge generalNegro">Entregado</span></div>'
                                            : (data.PresCerrado === 1 && data.PresEnviado === 1
                                                ? '<div class="text-center"><span class="custom-badge generalNegro">eEnviado</span></div>'
                                                : '-'))))) 
                            : `<div class="text-center"><span class="custom-badge generalNegro ">${data.estado}</span></div>`;    
                    }
                },
                {//7
                    data: null,
                    render: function(data) {
                        let recorte = (data.Examen).substring(0,5) + "...";
                        return recorte.length >= 5 ? `<span title="${data.Examen}">${recorte}</span>` : data.Examen;
                    }
                },
                {//8
                    data: null,
                    render: function(data){
                        let NombreCompleto = data.NombreProfesional + ' ' + data.ApellidoProfesional;
                        let recorte = (NombreCompleto).substring(0,5) + "...";
                        return recorte.length >= 5 ? `<span title="${NombreCompleto}">${recorte}</span>` : NombreCompleto;
                    }
                },
                {//9
                    data: null,
                    render: function(data){
                        return `<div class="text-center"><span class="custom-badge generalNegro">
                        ${data.EstadoEfector === undefined 
                            ? ([1,4].includes(data.Efector) 
                                ? 'pendiente'
                                : ([3,4,5].includes(data.Efector) 
                                    ? 'cerrado'
                                    : ' - ')) 
                            : data.EstadoEfector}
                        </span></div>`;
                    }
                },
                {//10
                    data: null,
                    render: function(data){
                        return `<div class="text-center"><span class="custom-badge generalNegro">${data.NoImprime === 1 ? 'ADJ_D' : 'ADJ_F'}</span></div>`;
                    }
                },
                {//11
                    data: null,
                    render: function(data){
                        let NombreCompleto = data.NombreProfesional2 + ' ' + data.ApellidoProfesional2;
                        let recorte = (NombreCompleto).substring(0,10) + "...";
                        return recorte.length >= 10 ? `<span title="${NombreCompleto}">${recorte}</span>` : NombreCompleto;
                    }
                },
                {//12
                    data: null,
                    render: function(data) {

                        return `<div class="text-center"><span class="custom-badge generalNegro">
                        ${data.EstadoInformador === undefined 
                            ? ([0,1].includes(data.Informador) 
                                ? 'Pendiente'
                                : (data.Informador === 2 
                                    ? 'borrador'
                                    : ([0,1].includes(data.Informador)
                                        ? 'pend y borrador'
                                        : (data.Informador === 3
                                            ? 'Cerrado'
                                            : '-')))) 
                            : data.EstadoInformador}
                        </span></div>`;
                    }
                },
                {//13
                    data: null,
                    render: function(data) {
                        let fecha = new Date(data.Fecha + 'T00:00'); 
                        fecha.setDate(fecha.getDate() + parseInt(data.DiasVencimiento));

                        let dia = String(fecha.getDate()).padStart(2, '0');
                        let mes = String(fecha.getMonth() + 1).padStart(2, '0');
                        let ano = fecha.getFullYear();
                
                        return `<span class="custom-badge generalNegro">${dia}/${mes}/${ano}</span>`;
                    }
                },          
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

    });
});