$(document).ready(function(){

    $('.abrir, .cerrar, .asignar, .liberar').hide();
    
    const valAbrir = ['3','4','5'], valCerrar = ['0','1','2'], ID = $('#Id').val();;
    let cadj = $('#CAdj').val(), efector = $('#efectores').val(), informador = $('#informadores').val(), provEfector = $('#IdEfector').val(), provInformador = $('#IdInformador').val();

    $('#efectores option[value="0"]').text('Elija una opción...');
    $('#informadores option[value="0"]').text('Elija una opción...');

    abrir(cadj);
    cerrar(cadj);
    asignar(efector);
    liberar(efector);
    optionsGeneral(provEfector, 'efector');
    optionsGeneral(provInformador, 'informador');
    listadoE();
    listadoI();

    $(document).on('click', '#abrir', function(){

        let lista = {3: 0, 4: 1, 5: 2};

        if(cadj in lista){

            $.post(updateItem, {Id : ID, _token: TOKEN, CAdj: lista[cadj] })
                .done(function(){

                    toastr.options = {
                        closeButton: true,   
                        progressBar: true,    
                        timeOut: 3000,        
                    };
                    toastr.success('Se ha realizado la acción correctamente', 'Actualizacion realizada');
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                    
                })
                .fail(function(xhr){
                    toastr.options = {
                        closeButton: true,   
                        progressBar: true,    
                        timeOut: 3000,        
                    };
                    toastr.success('Ha ocurrido un error. Consulte con el administrador', 'Error');
                    console.error(xhr);
                });
        }
    });

    $(document).on('click', '#cerrar', function(){

        let lista = {0: 3, 1: 4, 2: 5};

        if(cadj in lista){

            $.post(updateItem, {Id : ID, _token: TOKEN, CAdj: lista[cadj] })
                .done(function(){

                    toastr.options = {
                        closeButton: true,   
                        progressBar: true,    
                        timeOut: 3000,        
                    };
                    toastr.success('Se ha realizado la acción correctamente', 'Actualizacion realizada');
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                    
                })
                .fail(function(xhr){
                    toastr.options = {
                        closeButton: true,   
                        progressBar: true,    
                        timeOut: 3000,        
                    };
                    toastr.success('Ha ocurrido un error. Consulte con el administrador', 'Error');
                    console.error(xhr);
                });
        }
    });

    $(document).on('click', '#asignar', function(){
        
        let check = $('#efectores').val();
        $.post(updateEfector, { Id: ID, _token: TOKEN, IdProfesional: check, fecha: 1})
            .done(function(){

                toastr.options = {
                    closeButton: true,   
                    progressBar: true,    
                    timeOut: 3000,        
                };
                toastr.success('Se ha actualizado el efector de manera correcta', 'Actualizacion realizada');
                setTimeout(() => {
                    location.reload();
                }, 3000);
            })
    });

    $(document).on('click', '#liberar', function(){

        let checkEmpty = $('#efectores').val();

        if(checkEmpty !== '0'){

            $.post(updateEfector, { Id: ID, _token: TOKEN, IdProfesional: 0, fecha: 0})
            .done(function(){

                toastr.options = {
                    closeButton: true,   
                    progressBar: true,    
                    timeOut: 3000,        
                };
                toastr.success('Se ha actualizado el efector de manera correcta', 'Actualizacion realizada');
                setTimeout(() => {
                    location.reload();
                }, 3000);
            })
        }
   
    });

    $(document).on('click', '#adjuntos', function(){

        let cadj = $('#CAdj').val(), lista = {1: 2, 4: 5, 2: 1, 5: 4};

        if(cadj === '0' || cadj === 'null') return;

        if(cadj in lista){
            
            $.post(updateAdjunto, {Id: ID, _token: TOKEN, CAdj: lista[cadj]})
                .done(function(){
                  
                    toastr.options = {
                        closeButton: true,   
                        progressBar: true,
                        timeOut: 3000,        
                    };
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

    function abrir(val){
        return (valAbrir.includes(val)) ? $('.abrir').show() : $('.abrir').hide();    
    }

    function cerrar(val){
        return (valCerrar.includes(val)) ? $('.cerrar').show() : $('.cerrar').hide();
    }

    function asignar(efector){

        return (efector === '' || efector === 'null' || efector === '0') ? $('.asignar').show() : $('.asignar').hide();
    }

    function liberar(efector){
        return (efector !== '' && efector !== 'null' && efector !== '0') ? $('.liberar').show() : $('.liberar').hide();
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

        $.get(paginacionGeneral, {Id: ID})
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

        $.get(paginacionGeneral, {Id: ID})
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

                    $('#listainformadores').append(contenido);
                });
            })
    }

    $('#btnVolver').click(function() {
        history.back();
    }); 
});