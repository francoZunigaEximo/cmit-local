$(document).ready(function(){

    let resizing = false, startWidth, startHeight, startX, startY; //variables de ancho de imagen

    cargarPerfiles();
    quitarDuplicados("#provincia");
    quitarDuplicados("#estado");
    perfiles(ID);

    toastr.options = {
        closeButton: true,   
        progressBar: true,    
        timeOut: 3000,        
    };

    $("#Apellido, #Nombre").on("input", function() {
        $(this).val($(this).val().toUpperCase());
    });

    $(document).on('click', '.saveOpciones', function(){

        let efector = $('#T1').prop('checked'),
            informador = $('#T2').prop('checked'),
            evaluador = $('#T3').prop('checked'),
            combinado = $('#T4').prop('checked'),
            pago = $('#Pago').prop('checked'),
            informeAdj = $('#InfAdj').prop('checked');
            
        let total = (efector ? 1 : 0) + (informador ? 1 : 0) + (evaluador ? 1 : 0) + (combinado ? 1 : 0),
            tlp = (total > 1) ? 1 : 0,
            tmp = (total > 1) ? 1 : 0;
       
        $.post(opcionesProf, {_token: TOKEN, T1: efector, T2: informador, T3: evaluador, T4: combinado, Pago: pago, InfAdj: informeAdj, TMP: tmp, TLP: tlp, Id: ID})
            .done(function(){
                toastr.success("Se han guardado los cambios de manera correcta", "Perfecto");
                $('#perfiles').empty().append('<option value="" selected>Elija una opción</option>');
                perfiles(ID);
            })
            .fail(function(xhr){
                console.error(xhr);
                toastr.error("Ha ocurrido un error. Actualice la página y si el problema persiste, consulte con el administrador", "Error");
            })
    });

    $(document).on('click', '.saveSeguro', function(){

        let mn = $('#MN').val(),
            mp = $('#MP').val(),
            seguroMP = $('#SeguroMP').val();

        if(mn < 0) {
            toastr.warning("Matricula no acepta números negativos", "Atención");
            return;
        }

        $.post(seguroProf, {_token: TOKEN, MN: mn, MP: mp, SeguroMP: seguroMP, Id: ID})
            .done(function(){
                swal('Perfecto', 'Se han guardado los cambios de manera correcta', 'success');
            })
            .fail(function(xhr){
                console.error(xhr);
                toastr.error("Ha ocurrido un error. Actualice la página y si el problema persiste, consulte con el administrador", "Error");
            })

    });

    $(document).on('click', '.addPerfilProf', function(){

        let perfil = $('#perfiles').val(),
            especialidad = $('#listaEspecialidad').val();

        if(perfil === '' || especialidad === '') {
            toastr.warning("Debe seleccionar una especialidad y un perfil para poder añadirlo a la lista", "Atención");
            return;
        }

        $.post(setPerfiles, {_token: TOKEN, perfil: perfil, especialidad: especialidad, Id: ID})
            .done(function(response){
                
                if(response.error){
                    toastr.info(response.error);
                    return;

                }else{
                    toastr.success(`Se ha añadido el perfil de manera correcta`);
                    cargarPerfiles();
                }   
            })
            .fail(function(xhr){

                console.error(xhr);
                toastr.error("Ha ocurrido un error. Actualice la página y si el problema persiste, consulte con el administrador", "Error");
            });
    });

    $(document).on('click', '.eliminarPerfil',  function(){
        
        let IdProf = $(this).data('prof'),
            IdProv = $(this).data('prov');

        if(confirm("¿Está seguro que desea eliminar?")){

            $.post(delPerfil, {_token: TOKEN, IdProf: IdProf, IdProv: IdProv})
            .done(function(){

                toastr.options = {
                    closeButton: true,   
                    progressBar: true,    
                    timeOut: 3000,        
                };
                toastr.success(`Se ha añadido el perfil de manera correcta`);
                cargarPerfiles();
            })
            .fail(function(xhr){

                toastr.error("Ha ocurrido un error. Actualice la página y si el problema persiste, consulte con el administrador", "Error");
                console.error(xhr);
            });
        }
        
    });

    $(document).on('click', '#volverProfesionales', function(){

        window.location.href = GOINDEX;
    });

    $('#imagenModal').mousedown(function (e) {
        resizing = true;
        startWidth = $('#imagenModal').width();
        startHeight = $('#imagenModal').height();
        startX = e.clientX;
        startY = e.clientY;
    });

    $(document).mousemove(function (e) {
        if (resizing) {
            let newWidth = startWidth + (e.clientX - startX);
            let newHeight = startHeight + (e.clientY - startY);

            // Aplica nuevas dimensiones
            $('#imagenModal').width(newWidth);
            $('#imagenModal').height(newHeight);

            $('#wImage').val(newWidth);
            $('#hImage').val(newHeight);
        }
    });

    $(document).mouseup(function () {
        resizing = false;
    });

    function cargarPerfiles(){

        $.get(getPerfiles, {Id: ID})
            .done(function(response){
                
                let data = response.data;

                $('#listaProfesionales').empty();

                data.forEach(function (d) {
                    let arr = d.Tipos.split(',');
                
                    const perfiles = {
                        't1': ['Efector'],
                        't2': ['Informador'],
                        't3': ['Evaluador'],
                        't4': ['Combinado']
                    }
                
                    let imprimir = arr.map(e => {
                        if (perfiles[e]) {
                            return `<span class="badge custom-badge pequeno text-uppercase" style="margin-right: 3px; display:inline-block">${perfiles[e][0]}</span><br>`;
                        }
                        return ''; 
                    }).join(''); 
                
                    let contenido = `
                        <tr style="text-align: center">
                            <td>${d.especialidad}</td>
                            <td style="margin-right: 3px;">
                                ${imprimir}
                            </td>
                            <td>
                                <div class="remove">
                                    <button data-prof="${d.IdProf}" data-prov="${d.IdProv}" class="btn btn-sm iconGeneral eliminarPerfil" title="Dar de baja"><i class="ri-delete-bin-2-line"></i></button>
                                </div>
                            </td>
                        </tr>
                    `;
                
                    $('#listaProfesionales').append(contenido);
                    $('#perfiles').val(''),
                    $('#listaEspecialidad').val('');
                });
            });    
    }

    function quitarDuplicados(selector) {
        let seleccion = $(selector).val();
        let countSeleccion = $(selector + " option[value='" + seleccion + "']").length;
    
        if (countSeleccion > 1) {
            $(selector + " option[value='" + seleccion + "']:gt(0)").hide();
        }
    }

    function perfiles(id)
    {
        if(id === '' || id === null) return;

        $.get(choisePerfil, {Id: id})
            .done(function(response){

                select = $("#perfiles");
                
                if (response.T1 === 1) {
                    select.append(`<option value="t1">Efector</option>`);
                }
                if (response.T2 === 1) {
                    select.append(`<option value="t2">Informador</option>`);
                }
                if (response.T3 === 1) {
                    select.append(`<option value="t3">Evaluador</option>`);
                }
                if (response.T4 === 1) {
                    select.append(`<option value="t4">Combinado</option>`);
                }
              
            });
    }

    

});