$(document).ready(function(){

    const perfilesMap = {
        T1: { value: 't1', text: 'Efector' },
        T2: { value: 't2', text: 'Informador' },
        T3: { value: 't3', text: 'Evaluador' },
        T4: { value: 't4', text: 'Combinado' }
    };
    const select = $("#perfiles");
    const listaEspecialidad = $('#listaEspecialidad');

    let resizing = false, startWidth, startHeight, startX, startY; //variables de ancho de imagen

    cargarPerfiles();
    quitarDuplicados("#provincia");
    quitarDuplicados("#estado");
    perfiles(ID);

    $("#Apellido, #Nombre").on("input", function() {
        $(this).val($(this).val().toUpperCase());
    });

    $(document).on('click', '.saveOpciones', function(e){
        e.preventDefault();

        let efector = $('#T1').prop('checked'),
            informador = $('#T2').prop('checked'),
            evaluador = $('#T3').prop('checked'),
            combinado = $('#T4').prop('checked'),
            pago = $('#Pago').prop('checked'),
            informeAdj = $('#InfAdj').prop('checked');
            
        let total = (efector ? 1 : 0) + (informador ? 1 : 0) + (evaluador ? 1 : 0) + (combinado ? 1 : 0),
            tlp = (total > 1) ? 1 : 0,
            tmp = (total > 1) ? 1 : 0;
        
            preloader('on');
        $.post(opcionesProf, {_token: TOKEN, T1: efector, T2: informador, T3: evaluador, T4: combinado, Pago: pago, InfAdj: informeAdj, TMP: tmp, TLP: tlp, Id: ID})
            .done(function(response){
                preloader('off');
                toastr.success(response.msg);
                select.empty().append('<option value="" selected>Elija una opción</option>');
                perfiles(ID);
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;  
            })
    });

    $(document).on('click', '.saveSeguro', function(e){
        e.preventDefault();

        let mn = $('#MN').val(),
            mp = $('#MP').val(),
            seguroMP = $('#SeguroMP').val();

        if(mn < 0) {
            toastr.warning("Matricula no acepta números negativos");
            return;
        }
        preloader('on');
        $.post(seguroProf, {_token: TOKEN, MN: mn, MP: mp, SeguroMP: seguroMP, Id: ID})
            
            .done(function(response){
                preloader('off');
                toastr.success(response.msg);
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            })

    });

    $(document).on('click', '.addPerfilProf', function(e){
        e.preventDefault();

        let perfil = select.val(), especialidad = listaEspecialidad.val();

        if(!perfil || !especialidad) {
            toastr.warning("Debe seleccionar una especialidad y un perfil para poder añadirlo a la lista");
            return;
        }
        preloader('on');
        $.post(setPerfiles, {_token: TOKEN, perfil: perfil, especialidad: especialidad, Id: ID})
            
            .done(function(response){
                preloader('off');
                toastr.success(response.msg);
                cargarPerfiles(); 
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return; 
            });
    });

    $(document).on('click', '.eliminarPerfil',  function(e){
        e.preventDefault();
        let IdProf = $(this).data('prof'),
            IdProv = $(this).data('prov');

        swal({
            title: "¿Está seguro que desea eliminar?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                preloader('on');
                $.post(delPerfil, {_token: TOKEN, IdProf: IdProf, IdProv: IdProv})
                .done(function(){
                    preloader('off')
                    toastr.success(`Se ha añadido el perfil de manera correcta`);
                    cargarPerfiles();
                })
                .fail(function(jqXHR){
                    preloader('off');
                    let errorData = JSON.parse(jqXHR.responseText);            
                    checkError(jqXHR.status, errorData.msg);
                    return; 
                });
            }
        });           
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
        preloader('on');
        $.get(getPerfiles, {Id: ID})
            .done(function(response){
                preloader('off')
                let data = response.data;

                $('#listaProfesionales').empty();

                data.forEach(function (d) {

                    if(d.Tipos) {
                        let arr = d.Tipos.split(',');
                    
                        let imprimir = arr.map(e => {
                            let key = e.toUpperCase;

                            if (perfilesMap[key]) {
                                return `<span class="badge custom-badge pequeno text-uppercase" style="margin-right: 3px; display:inline-block">${perfilesMap[key].text}</span><br>`;
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
                        select.val(''),
                        listaEspecialidad.val('');
                    }
                });
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return; 
            });
    }

    function perfiles(id)
    {
        if(!id) return;

        $.get(choisePerfil, {Id: id})
            .done(function(response){
                
                for (let key in perfilesMap) {
                    if (response[key] === 1) {
                        let perfil = perfilesMap[key];
                        let option = `<option value="${perfil.value}">${perfil.text}</option>`;
                        select.append(option);
                    }
                }
              
            });
    }

    

});