$(function(){

    const principal = {
        grillaEfector: $('#listaLlamadaEfector'),
        grillaExamenes: $('#tablasExamenes'),
        atenderPaciente: $('.atenderPaciente'),
        chekAllExamenes: $('.checkAllExamenes'),
        buscar: $('#buscar'),
        atenderEfector: $('#atenderEfector'),
        clickAtencion: $('#clickAtencion'),
        verAtencion: $('#verAtencion')
    };

    const variables = {
        fechaHasta: $('#fechaHasta'),
        estado: $('#estado'),
        profesional: $('#profesional'),
        prestacion: $('#prestacion'),
        prestacionVar: $('#prestacionVista_var'),
        profesionalEfector: $('#profesionalVista_var'),
        tipoEfector: $('#tipoVista_var'),
        artEfector: $('#artVista_var'),
        empresaEfector: $('#empresaVista_var'),
        paraEmpresaEfector: $('#paraEmpresaVista_var'),
        pacienteEfector: $('#pacienteVista_var'),
        edadEfector: $('#edadVista_var'),
        fechaEfector: $('#fechaVista_var'),
        fotoEfector: $('#fotoVista_var'),
        profesional: $('#profesional'),
        descargaFoto: $('#descargaFoto'),
        efector: 'Efector',
        especialidadSelect: $('#especialidadSelect'),
        especialidad: $('#especialidad'),
    };

    $(document).on('click', '#clickAtencion', async function(e){
        e.preventDefault();
        principal.verAtencion.modal('show');

        let id = $(this).data('id'),  
            especialidades = variables.especialidadSelect.val();

        variables.profesionalEfector
            .add(variables.prestacionVar)
            .add(variables.tipoEfector)
            .add(variables.artEfector)
            .add(variables.empresaEfector)
            .add(variables.paraEmpresaEfector)
            .add(variables.pacienteEfector)
            .add(variables.edadEfector)
            .add(variables.fechaEfector)
            .empty();

        principal.atenderEfector.removeAttr('data-itemp');
        variables.fotoEfector.attr('src', '');

        preloader('on')
        try {
            
            const response =  await $.get(dataPaciente, {Id: id, IdProfesional: variables.profesional.val(), Especialidades: especialidades});
            const prestacion = response.prestacion;

            let paciente = prestacion.paciente.Apellido + ' ' + prestacion.paciente.Nombre,
                    edad = calcularEdad(prestacion.paciente.FechaNacimiento),
                    fecha = fechaNow(prestacion.Fecha,'/',0);

                
                let nombreProfesional = variables.profesional.find(':selected').text();
                variables.prestacionVar.val(prestacion.Id);
                variables.profesionalEfector.val(nombreProfesional);
                variables.tipoEfector.val(prestacion.TipoPrestacion);
                variables.artEfector.val(prestacion.art.RazonSocial);
                variables.empresaEfector.val(prestacion.empresa.RazonSocial);
                variables.paraEmpresaEfector.val(prestacion.empresa.ParaEmpresa);
                variables.pacienteEfector.val(paciente);
                variables.edadEfector.val(edad);
                variables.fechaEfector.val(fecha);
                variables.fotoEfector.attr('src', FOTO + prestacion.paciente.Foto);
                variables.descargaFoto.attr('href', FOTO + prestacion.paciente.Foto);

                comentariosPrivados(parseInt(prestacion.Id));
                tablasExamenes(response.itemsprestaciones, parseInt(variables.profesional.val()));
                cargarArchivosEfector(parseInt(prestacion.Id), parseInt(variables.profesional.val()), variables.especialidad.data('id') || variables.especialidadSelect.val());
                preloader('off');
        
        }catch(jqXHR){
            preloader('off');
            let errorData = JSON.parse(jqXHR.responseText);            
            checkError(jqXHR.status, errorData.msg);
            return;
        }

    });

    $(document).on('click', '#clickCierreForzado', function(e){
        e.preventDefault();

        let llamado = $(this),
            prestacion = llamado.data('prestacion'),
            profesional = llamado.data('profesional');

        swal({
            title: "Cierre forzado de llamado a paciente",
            text: "Este cierre no tiene en cuenta el estado del examen. Cierra de manera forzada la atención de la misma",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {

            if(confirmar) {
                preloader('on');
                $.post(cierreForzadoLlamado, {_token: TOKEN, prestacion: prestacion, profesional: profesional})
                    .done(function(response){
                        preloader('off');
                        swal("Cierre realizado de manera correcta", {
                            icon: "success",
                        });

                    })
                    .fail(function(jqXHR){
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return;
                    })   
            }
        });
    });



});