$(function(){

    const principal = {
        editLink: $('#editLink'),
        myModal: $('#myModal'),
        btnRegistrar: $('#btnRegistrar'),
        provincia: $('.provincia'),
        localidad: $('.localidad'),
        provincia2: $('.provincia2'),
        ciudad: $('.ciudad')
    };

    const variables = {
        documento: $('#documento'),
        provincia: $('#provincia'),
        localidad: $('#localidad'),
        codigoPostal: $('#codigoPostal'),
        pais: $('#pais'),
        provincia2: $('#provincia2'),
        ciudad: $('#ciudad')
    };

    paisSelect("Argentina");

    variables.pais.on('change', function() {
        let pais = $(this).find('option:selected').text();
        paisSelect(pais);
    });
   

    let doc = localStorage.getItem('insertDoc');
    
    if(doc){
        variables.documento.val(doc);
        localStorage.removeItem('insertDoc');
    }

    //Hacemos los cambios cuando hay un cambio en el campo
    variables.documento.on('change blur', function () {
        let documento = $(this).val();

        $.get(verify, {Documento: documento}, function(response){

            if (Object.keys(response).length >= 1) {

                url = editUrl.replace('__paciente__', response.Id);
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


    function paisSelect(pais) {
        $("#pais option:contains('" + pais + "')").prop('selected', true);

        if(pais === 'Argentina') {
            principal.provincia
                .add(principal.localidad)
                .show();

            principal.provincia2
                .add(principal.ciudad)
                .hide();
        }else{
            principal.provincia
                .add(principal.localidad)
                .hide();

            principal.provincia2
                .add(principal.ciudad)
                .show();
        }
    }

});