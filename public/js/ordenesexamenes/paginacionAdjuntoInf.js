$(function() {

    //Botón de busqueda de Mapas
    $(document).on('click', '#buscarAdjuntoInf', function() {

        let fechaDesde = $('#fechaDesdeAdjuntoInf').val(),
            fechaHasta = $('#fechaHastaAdjuntoInf').val()/*
            especialidad = $('#especialidadAdjuntoInf').val()*/;

        if (fechaDesde === '' || fechaHasta === '') {
            toastr.warning("Las fechas son obligatorias", "Atención",{timeOut: 1000});
            return;
        }

        /*if (especialidad === '') {
            toastr.warning('Debe seleccionar una especialidad para continuar', 'Atención');
            return;
        }*/

        $('#listaOrdenesInformadoresAdj').DataTable().clear().destroy();
        let currentDraw = 1;

        new DataTable("#listaOrdenesInformadoresAdj", {

            searching: false,
            ordering: false,
            processing: true,
            lengthChange: false,
            pageLength: 50,
            deferRender: true,
            responsive: false,
            serverSide: true,
            ajax: {
                url: SEARCHADJINF,
                data: function(d){
                    d.fechaDesde = $('#fechaDesdeAdjuntoInf').val();
                    d.fechaHasta = $('#fechaHastaAdjuntoInf').val();
                    d.especialidad = $('#especialidadAdjuntoInf').val();
                    d.empresa = $('#empresaAdjuntoInf').val();
                    d.efectores = $('#informadorAdjuntoInf').val();
                    d.art = $('#artAdjuntoInf').val();
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
                {
                    data: null,
                    render: function(data){
                        return fechaNow(data.Fecha,'/',0);
                    }
                },
                {
                    data: null,
                    render: function(data){
                        return `<span title="${data.Especialidad}">${data.Especialidad}</span>` ;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        return `<span><a href="${linkPrestaciones}/${data.IdPrestacion}/edit" target="_blank">${data.IdPrestacion}</a> ${data.prestacionCerrado === 1 ? '<i class="ri-information-line rojo"></i>' : ''}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data) {
                        return `<span title="${data.Empresa}">${data.Empresa}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        let NombreCompleto = data.pacApellido + ' ' + data.pacNombre;
                        return `<span title="${NombreCompleto}">${NombreCompleto}</span>`;
                    }
                },
                {
                    data: 'Documento',
                    name: 'Documento',
                },
                {
                    data: null,
                    render: function(data) {
                        return `<span title="${data.examen_nombre}">${data.examen_nombre}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        let NombreProfesional = data.proApellido + ' ' + data.proNombre;
                        return `<span title="${NombreProfesional}">${NombreProfesional}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data) {

                        let abierto = [0, 1, 2], cerrado = [3, 4, 5], estatus = data.Estado;

                        let mostrar = abierto.includes(estatus)
                                        ? 'Abierto'
                                        : cerrado.includes(estatus)
                                            ? 'Cerrado'
                                            : '-';

                        return `<span class="custom-badge ${mostrar === 'Abierto' ? 'rojo' : mostrar === 'Cerrado' ? 'verde' : 'gris'}">${mostrar}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data) {
                        
                        return  `<input type="checkbox" name="Id_adjuntoInf" value="${data.IdItem}" checked>`;      
                    }
                },
                {
                    data: null,
                    render: function(data){

                        let masivo = `<span title="Subir automáticamente el reporte" class="custom-badge iconGeneral"><i class="ri-file-upload-line automaticUploadI" data-id="${data.IdItem}" data-idprestacion="${data.IdPrestacion}" data-forma="individual"></i></span>`,
                            masivoCerrar = `<span title="Subir automáticamente el reporte y cerrar" class="custom-badge iconGeneral"><i class="ri-file-upload-line automaticUploadIC" data-id="${data.IdItem}" data-forma="individual"></i></span>`,
                            individual = `<span data-id="${data.IdItem}" data-idprestacion="${data.IdPrestacion}" data-tipo="${data.MultiInformador === 1 ? 'multiInformador' : 'informador'}" title="Subir manualmente el reporte" class="custom-badge iconGeneral uploadFile"><i class="ri-folder-line"></i></span><input type="file" class="fileManual" style="display: none;">`;

                        return `${masivo} ${individual}`;
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