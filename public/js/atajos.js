$(document).ready(() => {
    
    $("#prestacionButton").click(function() {
        $("#prestacionFast").offcanvas("show");
    });

    const options = {
        'a': lnkPacientes,
        'c': lnkClientes,
        'r': lnkPrestaciones,
        'm': lnkMapas,
        'o': lnkProfesionales,
        'e': lnkEspecialidades
    };

    $(document).keydown(function(e) {
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
