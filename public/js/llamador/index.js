$(function(){

    const grillaEfector = $('#listaLlamadaEfector');
    const grillaExamenes = $('#tablasExamenes');

    let echo = window.Echo.channel('listado-efectores');

    $('#fechaHasta').val(fechaNow(null, "-", 0));
    $('#estado').val('abierto');

    $(document).on('click', '.verPrestacion', function(e){
        e.preventDefault();

        let prestacion = $(this).data('prestacion');
        window.open(lnkPrestaciones.replace('__item__', prestacion), '_blank');
    });

    $(document).on('click','.exportar, .detalles', function(e){
        e.preventDefault();

        let opcion = $(this).hasClass('exportar') ? 'exportar' : 'detalles';

        let lista = grillaEfector.DataTable();

        if(!lista.data().any()){
            lista.clear().destroy();
            toastr.warning('No hay datos para exportar', '', {timeOut: 1000});
            return;
        }

        //lista.rows().data().toArray(); 
        let data = lista.rows({page: 'current'}).data().toArray(),
            ids = data.map(function(row) {
            return row.prestacion;
        });

        preloader('on');
        $.get(printExportar, {Ids: ids, tipo: 'efector', modo: opcion === 'exportar' ? 'basico' : 'full'})
            .done(function(response){
                createFile("xlsx", response.filePath, generarCodigoAleatorio() + '_reporte');
                preloader('off')
                toastr.success(response.msg, '', {timeOut: 1000});
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            })
    });

    $(document).on('click', '.atenderPaciente', function(e){
        e.preventDefault();

        let id = $(this).data('id'), profesional = $(this).data('profesional'), especialidades = $(this).data('especialidades');

        $('#profesionalEfector, #prestacionEfector, #tipoEfector, #artEfector, #empresaEfector, #paraEmpresaEfector, #pacienteEfector, #edadEfector, #fechaEfector').empty();
        $('#fotoEfector').attr('src', '');

        preloader('on')
        $.get(dataPaciente, {Id: id, IdProfesional: profesional, Especialidades: especialidades})
            .done(function(response){
                const prestacion = response.prestacion, profesional = response.profesional;

                let paciente = prestacion.paciente.Apellido + ' ' + prestacion.paciente.Nombre,
                    edad = calcularEdad(prestacion.paciente.FechaNacimiento),
                    fecha = fechaNow(prestacion.Fecha,'/',0);

                preloader('off');
                $('#prestacionEfector').val(prestacion.Id);
                $('#profesionalEfector').val(profesional);
                $('#tipoEfector').val(prestacion.TipoPrestacion);
                $('#artEfector').val(prestacion.art.RazonSocial);
                $('#empresaEfector').val(prestacion.empresa.RazonSocial);
                $('#paraEmpresaEfector').val(prestacion.empresa.ParaEmpresa);
                $('#pacienteEfector').val(paciente);
                $('#edadEfector').val(edad);
                $('#fechaEfector').val(fecha);
                $('#fotoEfector').attr('src', FOTO + prestacion.paciente.Foto);

                tablasExamenes(response.itemsprestaciones);

            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            });
    });

    $('#fotoEfector').hover(
        function() {
            $(this).addClass('zoomed');
        },
        function() {
            $(this).removeClass('zoomed');
        }
    );

    $(document).on('click', '.llamarExamen',function(e){
        e.preventDefault();

        
    })

    function tablasExamenes(data) { 
        $(grillaExamenes).empty();
        preloader('on');

        const categoria = {};
        data.forEach(function (item) {
            if (!categoria[item.NombreEspecialidad]) {
                categoria[item.NombreEspecialidad] = [];
            }
            categoria[item.NombreEspecialidad].push(item);
        });
    
        // Recorre cada grupo de especialidades
        for (const especialidad in categoria) {
            if (categoria.hasOwnProperty(especialidad)) {
                const examenes = categoria[especialidad];
    
                // Crea la tabla para la especialidad actual
                let contenido = `
                    <div class="especialidad-grilla mb-2">
                        <h4>${especialidad}</h4>
                        <table class="table table-bordered no-footer dataTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 250px">Examen</th>
                                    <th style="width: 90px">Estado</th>
                                    <th style="width: 120px">Adjunto</th>
                                    <th>Observaciones</th>
                                    <th style="width: 150px">Efector</th>
                                    <th style="width: 150px">Informador</th>
                                    <th style="width: 50px">
                                        <input type="checkbox" class="checkAllExamenes" name="Id_examenes">
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                `;
    
       
                examenes.forEach(function (examen) {
                    contenido += `
                        <tr>
                            <td>${examen.NombreExamen}</td>
                            <td>${estado(examen.CAdj)}</td>
                            <td>${checkAdjunto(examen.NoImprime, examen.Adjunto, examen.Archivo)}</td>
                            <td>${[null, undefined, ''].includes(examen.ObsExamen) ? '' : examen.ObsExamen}</td>
                            <td>Efector</td>
                            <td>Informador</td>
                            <td>
                                <input type="checkbox" name="Id_examenes" value="${examen.IdItem}">
                            </td>
                        </tr>
                    `;
                });
    
                contenido += `
                            </tbody>
                        </table>
                    </div>
                `;
                preloader('off');
                $(grillaExamenes).append(contenido);
            }
        }
    }

    function estado(data) {
        
        if([0,1,2].includes(data)){
            return `<span class="rojo">Abierto <i class="fs-6 ri-lock-unlock-line"></i><span>`;
        
        }else if([3,4,5].includes(data)){
            return `<span class="verde">Cerrado <i class="fs-6 ri-lock-2-line"></i><span>`;
        }          
    }

    //No Imprime: saber si es fisico o digital / adjunto: si acepta o no adjuntos / condicion: pendiente o adjuntado
    function checkAdjunto(noImprime, adjunto, condicion) {
        console.log(noImprime, adjunto, condicion)
        if (adjunto === 0) {
            return ``;
        }else if(adjunto === 1 && condicion > 0 && noImprime === 0) {
            return `<span class="verde">Adjuntado <i class="fs-6 ri-map-pin-line"></i><span>`;
        }else if(adjunto === 1 && condicion === 0 && noImprime === 0) {
            return `<span class="rojo d-flex align-items-center justify-content-between w-100">
                        <span class="me-auto">Pendiente</span>
                        <i class="fs-6 ri-map-pin-line mx-auto"></i>
                        <i class="fs-6 ri-folder-add-line ms-auto"></i>
                    </span>`;
        }else if(adjunto === 1 && noImprime === 1){
            return `<span class="mx-auto"><i class="gris fs-6 ri-map-pin-line"></i><span>`;
        }else{
            return ``;
        }
    }

    echo.listen('.ListadoProfesionalesEvent', (response) => {
        const efectores = response.efectores;

        $('#profesional').empty();

        console.log(efectores);

        toastr.info('Se ha actualizado la lista de profesionales', '', {timeOut: 1000});

        if (efectores.length === 1) {

            $('#profesional').append(
                `<option value="${efectores[0].Id}" selected>${efectores[0].NombreCompleto}</option>`
            );
        } else if(efectores.length > 1) {

            $('#profesional').append('<option value="" selected>Elija una opción...</option>');
            $.each(efectores, function(index, value){
                let contenido = `<option value="${value.Id}">${value.NombreCompleto}</option>`;

                $('#profesional').append(contenido);
            });

        } else {

            $('#profesional').append(
                `<option value="" selected>No hay efectores</option>`
            );
        }
    });

   
    

});