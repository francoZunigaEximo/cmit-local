$(function(){

    const grillaEfector = $('#listaLlamadaEfector');

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
            toastr.warning('No hay datos para exportar');
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
                toastr.success(response.msg)
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

        let id = $(this).data('id'), profesional = $(this).data('profesional');

        $('#profesionalEfector, #prestacionEfector, #tipoEfector, #artEfector, #empresaEfector, #paraEmpresaEfector, #pacienteEfector, #edadEfector, #fechaEfector').empty();
        $('#fotoEfector').attr('src', '');

        if([null,0,undefined,''].includes(id)) return;

        preloader('on')
        $.get(dataPaciente, {Id: id, IdProfesional: profesional})
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

    echo.listen('.ListadoProfesionalesEvent', (response) => {
        const efectores = response.efectores;

        $('#profesional').empty();

        toastr.info('Se ha actualizado la lista de profesionales');

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