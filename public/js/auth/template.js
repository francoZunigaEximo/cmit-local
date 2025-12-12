$(function() {

    const profesionales =  ["Efector", "Informador", "Evaluador", "Combinado", "Evaluador ART"];
    const select = $("#choisePerfil");
    const especialidad = $('#choiseEspecialidad');
    const choisePModal = $('#choisePModal');

    seleccionPerfil(PROFESIONAL, ESPECIALIDAD, IDPROFESIONAL);
    opcionesChoise(IDPROFESIONAL);

    $(document).on('change', '#choisePerfil', function(){
        especialidadChoise(IDPROFESIONAL);
    });

    async function opcionesChoise(id) {
        
        if(!id) return;

        select.empty().append('<option value="" selected>Elija una opción...</option>');

        let response = await $.get(choisePerfil, {Id: id});
 
        for(let index = 0; index < response.length; index++){
            let data = response[index];
            select.append(`<option value="${data.Nombre.toLowerCase()}">${data.Nombre}</option>`);
        }          
    }

    async function seleccionPerfil(profesional, especialidad, id,){
        let response = await $.get(choisePerfil, {Id: id}),
            resultado = response.some(item => profesionales.includes(item.Nombre));

        let multiCheck = await $.get(multiEspecialidadCheck); 

        if(multiCheck) return;

        if(resultado && (!profesional || !especialidad) && !response.find(item => item.Nombre === "Administrador")) {
            choisePModal.modal('show');
        }
    }

    async function especialidadChoise(profesional){

        if(!profesional || !select.val()) return;

        especialidad.empty().append('<option value="" selected>Elija una opción...</option>');

        let response = await  $.get(choiseEspecialidad, { Id: profesional, Tipo: select.val()});

        $.each(response, function(index, data){
            let contenido =  `<option value="${data.Nombre}">${data.Nombre}</option>`;
            especialidad.append(contenido);
        });
    }

    $(document).on('click', '.cargarPrestador', function(){

        let error = `
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong> Atención </strong> Debe seleccionar un perfil y una especialidad
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            `;

        if(!especialidad.val() || !select.val()){
            $('.message-sesion').empty().append(error);
            return;
        }

        $.post(savePrestador, {perfil: select.val(), especialidad: especialidad.val(), _token: TOKEN})
            .done(function(){
                choisePModal.modal('hide');
                location.reload();
            })
            .fail(function(){
                seleccionPerfil(PROFESIONAL, ESPECIALIDAD, IDSESSION);
            });
    });
   
});