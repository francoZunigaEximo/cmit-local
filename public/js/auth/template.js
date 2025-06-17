$(function() {

    const profesionales =  ["Efector", "Informador", "Evaluador", "Combinado", "Evaluador ART"];

    console.log("ChoiseT: " + choiseT);

    seleccionPerfil(mprof, tlp, IDSESSION);
    opcionesChoise(IDSESSION);

    $(document).on('change', '#choisePerfil', function(){
        especialidadChoise(IDSESSION);
    });



    function opcionesChoise(id)
    {
        const select = $("#choisePerfil");
        console.log("opcionesChoise: "+ id);
        if(id === '' || id === null) return;

        select.empty().append('<option value="" selected>Elija una opción...</option>');

        $.get(choisePerfil, {Id: id})
            .done(function(response){

                console.log(response)

                for(let index = 0; index < response.length; index++){

                    let data = response[index];

                    select.append(`<option value="${data.Nombre.toLowerCase()}">${data.Nombre}</option>`);
                    
                }
                
            });
    }

    function seleccionPerfil(m, t, id){
        console.log("valor m: " + m)
        console.log("valor t: " + t)

        $.get(choisePerfil, {Id: id}, function(response){
            let resultado = response.some(item => profesionales.includes(item.Nombre));

            if(resultado && 
                (
                    ['',0,null, undefined, '0'].includes(m) || 
                    ['',0,null, undefined, '0'].includes(t) 
                )
            ){

            $('#choisePModal').modal('show');
        }
        });
    }

    function especialidadChoise(profesional){

        let tipo = $('#choisePerfil').val();

        if(profesional === '' || tipo === '') return;

        $('#choiseEspecialidad').empty().append('<option value="" selected>Elija una opción...</option>');

        $.get(choiseEspecialidad, { Id: IDSESSION, Tipo: tipo})
            .done(function(response){
                
                
                $.each(response, function(index, data){
                    
                    let contenido =  `<option value="${data.Nombre}">${data.Nombre}</option>`;

                    $('#choiseEspecialidad').append(contenido);
                });
            });
    }

    $(document).on('click', '.cargarPrestador', function(){

        let especialidad = $('#choiseEspecialidad').val(),
            perfil = $('#choisePerfil').val(),
            error = `
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong> Atención </strong> Debe seleccionar un perfil y una especialidad
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            `;
        if(especialidad === '' || perfil === ''){
            $('.message-sesion').empty().append(error);
            return;
        }

        $.post(savePrestador, {perfil: perfil, especialidad: especialidad, _token: TOKEN})
            .done(function(){

                $('#choisePModal').modal('hide');
                location.reload();

            })
            .fail(function(){
                seleccionPerfil(mprof, tlp);
            });
    });
   
});