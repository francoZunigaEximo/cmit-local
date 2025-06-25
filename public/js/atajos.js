$(function(){

    let prestacionFast = document.getElementById('prestacionFast');
    
    $("#prestacionButton").on('click', function() {
        $("#prestacionFast").offcanvas("show");
    });


    prestacionFast.addEventListener('shown.bs.offcanvas', function(e){
        let dniPrestacion = document.getElementById('dniPrestacion');

        if(dniPrestacion) {
            dniPrestacion.focus();
        }
    })

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
            $("#dniPrestacion").focus();
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
