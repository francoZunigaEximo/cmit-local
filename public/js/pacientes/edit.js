$(function(){

    const principal = {
        provincia: $('#provincia'),
        multiVolver: $('.multiVolver'),
        exportSimple: $('.exportSimple'),
        exportDetallado: $('.exportDetallado'),
        verPrestacion: $('.verPrestacion'),
        exportExcel: $('.exportExcel'),
        listaPacientes: $('#listaPacientes')
    };

    const variables = {
        localidad: $('#localidad'),
        codigoPostal: $('#codigoPostal')
    };

    quitarDuplicados("#tipoDocumento");
    quitarDuplicados("#provincia");
    quitarDuplicados("#tipoIdentificacion");
    quitarDuplicados("#Sexo");
    checkProvincia();
    lstResultadosPrest(ID);
    
    principal.provincia.change(function() {
        let provincia = $(this).val();

        $.get(getLocalidades, {provincia: provincia})
            .done(function(response){
                let localidades = response.localidades;

                variables.localidad.empty().append('<option selected>Elija una opción...</option>');

                for(let index = 0; index < localidades.length; index++){
                    let localidad = localidades[index];
                    variables.localidad.append('<option value="' + localidad.id + '">' + localidad.nombre + '</option>');
                }
            });
    });
 

    variables.localidad.change(function() {
        let localidadId = $(this).val();

        $.get(getCodigoPostal,{localidadId: localidadId})
            .done(function(response){
                variables.codigoPostal.val(response.codigoPostal);
            });
        
    });

    principal.multiVolver.on('click', function(e) {
        e.preventDefault();
        window.history.back();
    });

    principal.exportSimple.add(principal.exportDetallado).on('click', function(e){
        e.preventDefault();

        let id = $(this).data('id'),
            tipo = $(this).hasClass('exportSimple') ? 'exportSimple' : 'exportDetallado';

        if([0, null, undefined, ''].includes(id)) return;

        preloader('on');
        $.get(exResultado, {IdPaciente: id, Tipo: tipo})
            .done(function(response){
                createFile("excel", response.filePath, generarCodigoAleatorio() + '_reporte');
                preloader('off');
                toastr.success('Se ha generado el archivo correctamente','',{timeOut: 1000});
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            });
    });

    principal.verPrestacion.on('click',function(e){
        e.preventDefault();

        let link = url.replace('__prestacion__', $(this).data('id'));
        window.open(link, '_blank');

    });

    principal.exportExcel.on('click', function(e){
        e.preventDefault();
        let tipo = $(this).data('id'), ids = [], filters = "";
        const table = principal.listaPacientes.find('tbody tr');
    
        ids = table.map(function(){
            return $(this).data('id');
        }).get();
    
        if (ids.length === 0) { 
            toastr.warning('No existen registros para exportar');
            return;
        }

        swal({
            title: "¿Está seguro que desea exportar la lista de prestaciones?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"],
        }).then((aceptar) => {
            if(aceptar) {
                preloader('on');
                $.get(sendExcel, {ids: ids, filters: filters, tipo: tipo})
                .done(function(response){
                    preloader('off');
                    createFile("excel", response.filePath, generarCodigoAleatorio() + "_reporte_" + tipo);
                        preloader('off');
                        toastr.success(response.msg);
                        return;
                })
                .fail(function(jqXHR) {
                    preloader('off');
                    let errorData = JSON.parse(jqXHR.responseText);            
                    checkError(jqXHR.status, errorData.msg);
                    return; 
                });

            };
        });

    });

    function checkProvincia(){

        let provincia = $('#provincia').val(), localidad = $('#localidad').val();

        if (provincia === 0)
        {
            $.get(checkP, {localidad: localidad})
                .done(function(response){
                    let provinciaNombre = response.fillProvincia,
                        nuevoOption = $('<option>', {
                        value: provinciaNombre,
                        text: provinciaNombre,
                        selected: true,
                    });

                    variables.provincia.append(nuevoOption);
                })
                .fail(function(jqXHR){
                    preloader('off');
                    let errorData = JSON.parse(jqXHR.responseText);
                    checkError(jqXHR.status, errorData.msg);
                    return;
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

                for(let index = 0; index < response.length; index++) {
                    let r = response[index],
                        icon = r.Evaluacion === 0 ? `<span class="custom-badge generalNegro">Antiguo</span>` : '',
                        evaluacion = r.Evaluacion === 0 ? '' : r.Evaluacion.slice(2),
                        calificacion = r.Calificacion ? r.Calificacion.slice(2) : '',
                        boton = r.Evaluacion !== 0 ? `<button data-id="${r.Id}" class="btn btn-sm iconGeneral verPrestacion" title="Ver">
                                    <i class="ri-search-eye-line"></i>
                                </button>` : '',
                        contenido = `
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
                }

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