$(document).ready(()=>{

    //Botón de busqueda de Mapas
    $(document).on('click', '#buscarAdjunto', function() {

        $('#Liberar, #Cerrar, #Abrir').hide();

        let fechaDesde = $('#fechaDesdeAdjunto').val(),
            fechaHasta = $('#fechaHastaAdjunto').val(),
            especialidad = $('#especialidadAdjunto').val();

        if (fechaDesde === '' || fechaHasta === '') {
            toastr.warning("Las fechas son obligatorias", "Atención");
            return;
        }

        if (especialidad === '') {
            toastr.warning('Debe seleccionar una especialidad para continuar', 'Atención');
            return;
        }

        $(document).on('change', '#estadoAsignados', function(){
            var nuevoValor = $(this).val();
            
            if (nuevoValor !== estado) {

                estado = nuevoValor;

            }
        });

        $('#listaOrdenesEfectoresAdj').DataTable().clear().destroy();

        new DataTable("#listaOrdenesEfectoresAdj", {

            searching: false,
            ordering: false,
            processing: true,
            lengthChange: false,
            pageLength: 50,
            deferRender: true,
            responsive: true,
            serverSide: true,
            ajax: {
                url: SEARCHADJ,
                data: function(d){
                    d.fechaDesde = $('#fechaDesdeAdjunto').val();
                    d.fechaHasta = $('#fechaHastaAdjunto').val();
                    d.especialidad = $('#especialidadAdjunto').val();
                    d.empresa = $('#empresaAdjunto').val();
                    d.efectores = $('#efectorAdjunto').val();
                    d.art = $('#artAdjunto').val();
                }
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
                        return `<span title="${data.Especialidad}">${acortadorTexto(data.Especialidad, 15)}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data) {
                        return `<div class="text-center"><a href="${linkPrestaciones}/${data.IdPrestacion}/edit" target="_blank">${data.IdPrestacion}</a></div>`;
                    } 
                },
                {
                    data: null,
                    render: function(data) {
                        return `<span title="${data.Empresa}">${acortadorTexto(data.Empresa, 7)}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        let NombreCompleto = data.pacNombre + ' ' + data.pacApellido;
                        return `<span title="${NombreCompleto}">${acortadorTexto(NombreCompleto, 7)}</span>`;
                    }
                },
                {
                    data: 'Documento',
                    name: 'Documento',
                },
                {
                    data: null,
                    render: function(data) {
                        return `<span title="${data.examen_nombre}">${acortadorTexto(data.examen_nombre,)}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        let NombreProfesional = data.proApellido + " " + data.proNombre; 
                        return `<span title="${NombreProfesional}">${acortadorTexto(NombreProfesional,7)}</span>`;
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
                                            : 'sin datos';

                        return `<span class="custom-badge ${mostrar === 'Abierto' ? 'rojo' : mostrar === 'Cerrado' ? 'verde' : 'gris'}">${mostrar}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data) {
                        
                        return  `<input type="checkbox" name="Id_adjunto" value="${data.IdItem}" checked>`;      
                    }
                },
                {
                    data: null,
                    render: function(data){

                        let masivo = `<span title="Subir automáticamente el reporte" class="custom-badge iconGeneral"><i class="ri-file-upload-line automaticUpload" data-id="${data.IdItem}" data-forma="${data.examen_nombre === 'Multi Examen' ? 'multi' : 'individual'}"></i></span>`,
                            individual = `<span data-id="${data.IdItem}" data-idprestacion="${data.IdPrestacion}" data-tipo="${data.MultiEfector === 1 ? 'multiefector' : 'efector'}" title="Subir manualmente el reporte" class="custom-badge iconGeneral uploadFile"><i class="ri-folder-line"></i></span><input type="file" class="fileManual" style="display: none;">`,
                            qr = `<span title="Copiar QR del examen" class="custom-badge iconGeneral copiarQr" data-prestacion="${data.IdPrestacion}" data-paciente="${data.IdPaciente}" data-examen="${data.Examen}" data-examenid="${data.IdExamen}">
                                <i class="ri-qr-code-line"></i>
                            </span>`;

                        return `${masivo} ${individual} ${qr}`;
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