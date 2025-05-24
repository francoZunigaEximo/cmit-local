$(function(){

    const variables = {
        profesional: $('#profesional')
    };

    $(document).on('click', 'input[type="checkbox"][name^="Id_examenes_"]', function () {

        let chequeado = $(this).is(':checked'),
            idCheck = $(this).val();

        if([null, undefined, ''].includes(idCheck)) return;

        // console.log(chequeado, idCheck, variables.profesional.val());
        preloader('on')
        $.get(asignacionProfesional, {Id: idCheck, Profesional: variables.profesional.val(), estado: chequeado})
            .done(function(response) {

                preloader('off')
            })
            .fail(function(jqXHR) {
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            });


    });

});