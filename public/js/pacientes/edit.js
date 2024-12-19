$(document).ready(function(){

    quitarDuplicados("#tipoDocumento");
    quitarDuplicados("#provincia");
    quitarDuplicados("#tipoIdentificacion");
    checkProvincia();
    lstResultadosPrest(ID);

    
    $('#provincia').change(function() {
        let provincia = $(this).val();

        $.ajax({
            url: getLocalidades,
            type: "GET",
            data: {
                provincia: provincia,
            },
            success: function(response) {
                let localidades = response.localidades;

                $('#localidad').empty().append('<option selected>Elija una opción...</option>');

                localidades.forEach(function(localidad) {
                    $('#localidad').append('<option value="' + localidad.id + '">' + localidad.nombre + '</option>');
                });
            }
        });
    });

 

    $('#localidad').change(function() {
        let localidadId = $(this).val();

        // Realizar la solicitud Ajax
        $.ajax({
            url: getCodigoPostal,
            type: "GET",
            data: {
                localidadId: localidadId,
            },
            success: function(response) {
                // Actualizar el valor del input de Código Postal
                $('#codigoPostal').val(response.codigoPostal);
            }
        });
    });

    $(document).on('click', '.multiVolver', function(e) {
        window.history.back();
    });

    $(document).on('click', '.exportSimple, .exportDetallado', function(e){
        e.preventDefault();

        let id = $(this).data('id'),
            tipo = $(this).hasClass('exportSimple') ? 'exportSimple' : 'exportDetallado';

        if([0, null, undefined, ''].includes(id)) return;

        preloader('on');
        $.get(exResultado, {IdPaciente: id, Tipo: tipo})
            .done(function(response){
                createFile("excel", response.filePath, generarCodigoAleatorio() + '_reporte');
                preloader('off');
                toastr.success('Se ha generado el archivo correctamente');
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            });
    });

    $(document).on('click', '.verPrestacion', function(e){
        e.preventDefault();

        let link = url.replace('__prestacion__', $(this).data('id'));
        window.open(link, '_blank');

    });


    function checkProvincia(){

        let provincia = $('#provincia').val();
        let localidad = $('#localidad').val();

        if (provincia === 0)
        {
            $.ajax({
                url: checkP,
                type: 'GET',
                data: {
                    localidad: localidad,
                },
                success: function(response){
                    
                    let provinciaNombre = response.fillProvincia;
                        
                    let nuevoOption = $('<option>', {
                        value: provinciaNombre,
                        text: provinciaNombre,
                        selected: true,
                    });

                    $('#provincia').append(nuevoOption);
                },
                error: function(xhr){
                    preloader('off');
                    let errorData = JSON.parse(jqXHR.responseText);
                    checkError(jqXHR.status, errorData.msg);
                    return;
                }
            });
        }
    }

    async function lstResultadosPrest(idPaciente){

        if([0,null,'', undefined].includes(idPaciente)) return;

        $('#lstResultadosPres, #lstResultadosPres2').empty();
        preloader('on');
        $.get(await loadResultadosPres, {IdPaciente: idPaciente})
            .done(function(response){

                preloader('off');
                $.each(response, function(index, r){

                    let icon = r.Evaluacion === 0 ? `<span class="custom-badge generalNegro">Antiguo</span>` : '',
                        evaluacion = r.Evaluacion === 0 ? '' : r.Evaluacion.slice(2),
                        calificacion = r.Calificacion ? r.Calificacion.slice(2) : '',
                        boton = r.Evaluacion !== 0 ? `<button data-id="${r.Id}" class="btn btn-sm iconGeneral verPrestacion" title="Ver">
                                    <i class="ri-search-eye-line"></i>
                                </button>` : '';

                    let contenido = `
                        <tr>
                            <td style="width: 50px">${fechaNow(r.Fecha, "/", 0)}</td>
                            <td style="width: 50px">${r.Id} ${icon}</td>
                            <td style="width: 160px">${r.Empresa}</td>
                            <td style="width: 100px">${r.Tipo}</td>
                            <td style="width: 120px">${evaluacion}</td>
                            <td style="width: 120px">${calificacion}</td>
                            <td>${r.Obs}</td>
                            <td style="width: 30px">
                                ${boton}
                            </td>
                        </tr>
                    `;

                    $('#lstResultadosPres, #lstResultadosPres2').append(contenido);

                });

                $("#listadoResultadosPres").fancyTable({
                    pagination: true,
                    perPage: 15,
                    searchable: false,
                    globalSearch: false,
                    sortable: false, 
                });

                $("#listadoResultadosPres2").fancyTable({
                    pagination: true,
                    perPage: 15,
                    searchable: false,
                    globalSearch: false,
                    sortable: false, 
                });
            });

    }

});