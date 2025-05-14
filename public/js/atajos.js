$(function(){
    
    $("#prestacionButton").on('click', function() {
        $("#prestacionFast").offcanvas("show");
    });

    $("#prestacionFast").on('shown.bs.offcanvas', function() {
        $("#dniPrestacion").on('focus');
    });
    
    $("#dniPrestacion").on('keyup', function(e) {
        if (e.which === 13) {
            $("#btnWizardPrestacion").on('click');
        }
    });


    const options = {
        'a': lnkPacientes,
        'c': lnkClientes,
        'r': lnkPrestaciones,
        'm': lnkMapas,
        'e': lnkEspecialidades,
        'n': lnkNoticias,
        'x': lnkExamenes,
        's': lnkEtapas
    };

    $(document).on('keydown', function(e) {
        if (e.altKey && e.key === 'p') {
            e.preventDefault();
            $("#prestacionFast").offcanvas("show");
            $("#dniPrestacion").on('focus');
        }

        if (e.altKey) {
            e.preventDefault();
            choiseButton(e.key);
        }
    });

    function choiseButton(letra) {
        if (options[letra]) {
            window.location.href = options[letra];
        }
    }
});
