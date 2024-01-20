$(document).ready(function(){

    seleccionPerfil(mprof, tlp);
    opcionesChoise(IDSESSION);

    $(document).on('change', '#choisePerfil', function(){

        especialidadChoise(IDSESSION);

    });

    function seleccionPerfil(m,t){

        if(m === '0' && t === '1'){
            $('#choisePModal').modal('show');
        }
    }

    function opcionesChoise(id)
    {
        if(id === '' || id === null) return;

        $.get(choisePerfil, {Id: id})
            .done(function(response){

                const perfiles = {
                    1: ['T1','Efector'],
                    2: ['T2','Informador'],
                    3: ['T3','Evaluador'],
                    4: ['T4','Combinado']
                };
                
                const select = $("#choisePerfil");
               
                for(let i = 1; i<= 4; i++){

                    if (response[perfiles[i][0]] === 1) {
                        select.append(`<option value="${perfiles[i][0].toLowerCase()}">${perfiles[i][1]}</option>`);
                    }
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