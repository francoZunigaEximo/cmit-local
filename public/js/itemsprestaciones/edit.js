$(document).ready(function(){

    $.fn.filepond.registerPlugin(FilePondPluginImagePreview);

    toastr.options = {
        closeButton: true,   
        progressBar: true,    
        timeOut: 3000,        
    };

    const valAbrir = ['3','4','5'], valCerrar = ['0','1','2'], ID = $('#Id').val();
    let cadj = $('#CAdj').val(), CInfo = $('#CInfo').val(), efector = $('#efectores').val(), informador = $('#informadores').val(), provEfector = $('#IdEfector').val(), provInformador = $('#IdInformador').val(), Estado = $('#Estado').val(), EstadoI = $('#EstadoI').val();

    $('.abrir, .cerrar, .asignar, .liberar, .asignarI, .cerrarI, .adjuntarEfector, .adjuntarInformador').hide();
    $('#efectores option[value="0"]').text('Elija una opción...');
    $('#informadores option[value="0"]').text('Elija una opción...');
    
    asignar(efector, 'efector');
    asignar(informador, 'informador');
    liberar(cadj, efector);
    abrir(cadj);
    cerrar(cadj, efector, 'efector');
    cerrar(cadj, informador, 'informador');
    optionsGeneral(provEfector, 'efector');
    optionsGeneral(provInformador, 'informador');
    listadoE();
    listadoI();

    $(document).on('click', '.btnAdjEfector, .btnAdjInformador', function () {
        let who = $(this).hasClass('btnAdjEfector') ? 'efector' : 'informador';
    
        let fileInput = $('.fileEfector')[0];  // Obten el elemento input de tipo file
        let archivo = fileInput.files[0]; 
        
        // Comprobar si hay archivos
        if (archivo) {
            let descripcionE = $('#DescripcionE').val(),
                descripcionI = $('#DescripcionI').val(),
                descripcion = (who === 'efector') ? descripcionE : descripcionI,
                identificacion = $('#identificacion').val(),
                prestacion = $('#prestacion').val();
    
            let formData = new FormData();
            formData.append('archivo', archivo);
            formData.append('Id', ID);
            formData.append('Descripcion', descripcion);
            formData.append('IdEntidad', identificacion);
            formData.append('IdPrestacion', prestacion);
            formData.append('_token', TOKEN);
    
            $.ajax({
                type: 'POST',
                url: fileUpload,
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    // Manejar el éxito de la solicitud
                },
                error: function (xhr) {
                    console.error(xhr);
                    toastr.error("Ha ocurrido un error. Consulte con el administrador", "Atención");
                }
            });
        } else {
            console.error('No se seleccionó ningún archivo.');
        }
    });
    
    

    $(document).on('click', '#abrir', function(){

        let lista = {3: 0, 4: 1, 5: 2};

        if(cadj in lista){

            $.post(updateItem, {Id : ID, _token: TOKEN, CAdj: lista[cadj], Para: 'abrir' })
                .done(function(){
                    toastr.success('Se ha realizado la acción correctamente', 'Actualizacion realizada');
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                    
                })
                .fail(function(xhr){
                    toastr.success('Ha ocurrido un error. Consulte con el administrador', 'Error');
                    console.error(xhr);
                });
        }
    });

    $('#btnVolver').click(function() {
        location.href = volver;
    }); 

    $('#adjuntarEfector').on('click', function () {
        $('#archivoEfector').click();
    });

    $(document).on('click', '#cerrar, #cerrarI', function(){

        let who = $(this).hasClass('cerrar') ? 'cerrar' : 'cerrarI',
            listaE = {0: 3, 1: 4, 2: 5},
            listaI = ['0', '1', '2'];

        if(who === 'cerrar' && cadj in listaE){

            $.post(updateItem, {Id : ID, _token: TOKEN, CAdj: listaE[cadj], Para: who })
                .done(function(){
                    toastr.success('Se ha cerrado al efector correctamente', 'Actualizacion realizada');
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                    
                })
                .fail(function(xhr){
                    toastr.success('Ha ocurrido un error. Consulte con el administrador', 'Error');
                    console.error(xhr);
                });

        }else if(who === 'cerrarI' && listaI.includes(CInfo)){

            $.post(updateItem, {Id : ID, _token: TOKEN, CInfo: 3, Para: who })
                .done(function(){
                    toastr.success('Se ha cerrado al informador correctamente', 'Actualizacion realizada');
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                    
                })
                .fail(function(xhr){
                    toastr.success('Ha ocurrido un error. Consulte con el administrador', 'Error');
                    console.error(xhr);
                });
        }
    });

    $(document).on('click', '#asignar, #asignarI', function(){

        let who = $(this).hasClass('asignar') ? 'asignar' : 'asignarI',
            check = (who === 'asignar') ? $('#efectores').val() : $('#informadores').val();

        if(check === '' || check === '0'){
            toastr.warning("Debe seleccionar un Efector/Informador para poder asignar uno", "Atención");
            return;
        }
        
        $.post(updateEfector, { Id: ID, _token: TOKEN, IdProfesional: check, fecha: 1, Para: who})
            .done(function(){
                toastr.success('Se ha actualizado la información de manera correcta', 'Actualización realizada');
                setTimeout(() => {
                    location.reload();             
                }, 3000); 
            })
    });

    $(document).on('click', '#liberar', function(){

        let checkEmpty = $('#efectores').val();

        if(checkEmpty !== '0'){

            $.post(updateEfector, { Id: ID, _token: TOKEN, IdProfesional: 0, fecha: 0, Para: 'asignar'})
            .done(function(){
                toastr.success('Se ha actualizado el efector de manera correcta', 'Actualizacion realizada');
                setTimeout(() => {
                    location.reload();
                }, 3000);
                
            })
        }
   
    });

    $(document).on('click', '#adjuntos', function(){

        let lista = {1: 2, 4: 5, 2: 1, 5: 4};

        if(cadj === '0' || cadj === null) return;

        if(cadj in lista){
            
            $.post(updateAdjunto, {Id: ID, _token: TOKEN, CAdj: lista[cadj]})
                .done(function(){
                    toastr.success('Se ha actualizado el efector de manera correcta', 'Actualizacion realizada');
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                })
        }
            
    });

    $(document).on('click', '#actualizarExamen', function(){

        let ObsExamen = $('#ObsExamen').val(), Profesionales2 = $('#informadores').val(), Obs = $('#Obs').val(), Fecha = $('#Fecha').val();
        
        $.post(updateExamen, {Id: ID, _token: TOKEN, ObsExamen: ObsExamen, Profesionales2: Profesionales2, Obs: Obs, Fecha: Fecha})
            .done(function(){

                swal('Perfecto', 'Se han actualizado los datos correctamente', 'success');
                setTimeout(() => {
                    location.reload();
                }, 3000);
            })
            .fail(function(xhr){
                swal('Error', 'Ha ocurrido un error. Consulte con el administrador', 'error');
                console.error(xhr);
            });
    });

    async function abrir(val){
        let resultado = await (valAbrir.includes(val));
        
        if(resultado){

            $('.abrir').show();
            $('#informadores').prop('disabled', false);
        
        }else{

            $('.abrir').hide();  
        }
    }

    async function cerrar(val, e, tipo){  

        if(tipo === 'efector'){

            let resultado = await (valCerrar.includes(val) && e !== '0');
            
            if(resultado){
                $('.cerrar').show();
                $('#informadores').prop('disabled', true);

            }
        }else if(tipo === 'informador'){

            let resultado = await (efector !== '0' && informador !== '0') && (CInfo !== '3'),
                final = await (efector !== '0' && informador !== '0') && (Estado === 'Cerrado' && EstadoI === 'Cerrado');

            if(resultado){
   
                $('.cerrarI').show();
                $('.abrir').hide();
                $('.adjuntarInformador').show();

            }else if(final){

                $('.cerrarI').hide();
                $('.abrir').hide();
                $('.adjuntarInformador').hide();
            }
        }
    }

    async function asignar(e, tipo){

        if(tipo === 'efector'){

            let resultado = await (e === '0' || e === null || e === '');

            if(resultado){

                $('.asignar').show();
                $('#informadores').prop('disabled', true);
                $('.adjuntarInformador').hide();
            }
        
        }else if(tipo === 'informador'){

            let resultado = await (e === '0' || e === null || e === '') && (efector !== '0');
            
            if(resultado){
                $('.asignarI').show();
                $('.abrir').show();
            }
        }
    }
       

    async function liberar(val, e){
        let resultado = await (e !== '' && e !== null && e !== '0' && valCerrar.includes(val));
        
        if(resultado){
            $('.liberar').show();
            $('.asignarI').hide();
            $('.adjuntarEfector').show();

        }  

    }

    function optionsGeneral(id, tipo) {
        
        let etiqueta, valor;

        if(tipo === 'efector') {
            etiqueta = $('#efectores');
            valor = etiqueta.val();

        }else if (tipo === 'informador') {
            etiqueta = $('#informadores');
            valor = etiqueta.val();
        }
    
        if (valor === '0') {

            $.get(listGeneral, { proveedor: id, tipo: tipo })
                .done(function (response) {
                    let data = response.resultados;

                    $.each(data, function (index, d) {
                        let contenido = `<option value="${d.Id}">${d.NombreCompleto}</option>`;
                        etiqueta.append(contenido);
                    });
                });
        }
    }

    function listadoE(){

        $.get(paginacionGeneral, {Id: ID, tipo: 'efector'})
            .done(function(response){
                
                let data = response.resultado;

                $.each(data, function(index, d){

                    let contenido = `
                        <tr>
                            <td>${d.Nombre}</td>
                            <td>${(d.DescripcionE !== null || d.DescripcionE !== 'undefined' ? d.Descripcion : ' - ')}</td>
                            <td>${(d.Adjunto === 0 ? 'Físico' : 'Digital')}</td>
                            <td>${(d.MultiE === 0 ? 'Simple' : 'Multi')}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <div class="edit">
                                        <button type="button" class="btn btn-sm btn-soft-primary edit-item-btn" title="Ver"><i class="ri-search-eye-line"></i></button>
                                    </div>
                                    <div class="remove">
                                        <button class="btn btn-sm btn-danger" title="Eliminar">
                                            <i class="ri-delete-bin-2-line"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `;

                    $('#listaefectores').append(contenido);
                });
            })
    }

    function listadoI(){

        $.get(paginacionGeneral, {Id: ID, tipo: 'informador'})
            .done(function(response){
                
                let data = response.resultado;

                $.each(data, function(index, d){

                    let contenido = `
                        <tr>
                            <td>${d.Nombre}</td>
                            <td>${(d.DescripcionI !== null ? d.Descripcion : ' - ')}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <div class="edit">
                                        <button type="button" class="btn btn-sm iconGeneral" title="Ver"><i class="ri-search-eye-line"></i></button>
                                    </div>
                                    <div class="remove">
                                        <button class="btn btn-sm iconGeneral" title="Eliminar">
                                            <i class="ri-delete-bin-2-line"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `;

                    $('#listainformadores').append(contenido);
                });
            })
    }
});