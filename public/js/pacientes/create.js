$(function(){

    const principal = {
        editLink: $('#editLink'),
        myModal: $('#myModal'),
        btnRegistrar: $('#btnRegistrar')
    };

    const variables = {
        documento: $('#documento'),
        provincia: $('#provincia'),
        localidad: $('#localidad'),
        codigoPostal: $('#codigoPostal')
    };

    let doc = localStorage.getItem('insertDoc');
    
    if(doc){
        variables.documento.val(doc);
        localStorage.removeItem('insertDoc');
    }

    //Hacemos los cambios cuando hay un cambio en el campo
    variables.documento.on('change blur', function () {
        let documento = $(this).val();

        $.get(verify, {documento: documento, _token: TOKEN}, function(response){
            if (response.existe) {
                let paciente = response.paciente,
                    url = editUrl.replace('__paciente__', paciente.Id);
                principal.editLink.attr('href', url);

                principal.myModal.modal('show');
                principal.btnRegistrar.prop('disabled', true);
            }else{
                principal.btnRegistrar.prop('disabled', false);
            }
        })
    });


    variables.provincia.change(function() {
        let provincia = $(this).val();

        $.get(getLocalidades, {provincia: provincia})
            .done(function(response){
                let localidades = response.localidades;
                variables.localidad
                    .empty()
                    .append('<option selected>Elija una opción...</option>');

                localidades.forEach(function(localidad) {
                    variables.localidad.append('<option value="' + localidad.id + '">' + localidad.nombre + '</option>');
                });
            })
    });


    variables.localidad.change(function() {
        let localidadId = $(this).val();
        $.get(getCodigoPostal, {localidadId: localidadId}, function(response){
            variables.codigoPostal.val(response.codigoPostal);
        })
    });

});