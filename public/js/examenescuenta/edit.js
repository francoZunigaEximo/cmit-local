$(document).ready(function(){

    toastr.options = {
        closeButton: true,   
        progressBar: true,     
        timeOut: 3000,        
    };

    listado();

    $('#dni').each(function(){
        $(this).select2({
            language: {
                noResults: function() {
    
                return "No hay pacientes con esos datos";        
                },
                searching: function() {
    
                return "Buscando..";
                },
                inputTooShort: function () {
                    return "Por favor, ingrese 2 o más caracteres";
                }
            },
            placeholder: 'Apellido y/o nombre del paciente',
            allowClear: true,
            ajax: {
                url: getPacientes, 
                dataType: 'json',
                data: function(params) {
                    return {
                        buscar: params.term,
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.pacientes 
                    };
                },
                cache: true
            },
            minimumInputLength: 2 
        });
    });

    $('#examen').each(function(){
        $(this).select2({
            language: {
                noResults: function() {
    
                return "No hay examenes con esos datos";        
                },
                searching: function() {
    
                return "Buscando..";
                },
                inputTooShort: function () {
                    return "Por favor, ingrese 2 o más caracteres";
                }
            },
            placeholder: 'Nombre del exámen',
            allowClear: true,
            ajax: {
                url: searchExamen, 
                dataType: 'json',
                data: function(params) {
                    return {
                        buscar: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.examen 
                    };
                },
                cache: true
            },
            minimumInputLength: 2
        });
    });

    $('.volverPrincipal').click(function(){
        window.location.href = INDEX;
    });

    $('#paquete').select2({
        placeholder: 'Seleccionar paquete...',
        language: {
            noResults: function() {

            return "No hay paquete con esos datos";        
            },
            searching: function() {

            return "Buscando..";
            },
            inputTooShort: function () {
                return "Por favor, ingrese 2 o más caracteres";
            }
        },
        allowClear: true,
        ajax: {
           url: getPaquetes,
           dataType: 'json',
           delay: 250,
           data: function(params) {
                return {
                    buscar: params.term,
                };
           },
           processResults: function(data) {
                return {
                    results: data.paquete
                };
           },
           cache: true,
        },
        minimumInputLength: 2
    });

    $('#facturacion').select2({
        placeholder: 'Seleccionar paquete...',
        language: {
            noResults: function() {

            return "No hay paquete con esos datos";        
            },
            searching: function() {

            return "Buscando..";
            },
            inputTooShort: function () {
                return "Por favor, ingrese 2 o más caracteres";
            }
        },
        allowClear: true,
        ajax: {
           url: getPaqueteFact,
           dataType: 'json',
           delay: 250,
           data: function(params) {
                return {
                    buscar: params.term,
                };
           },
           processResults: function(data) {
                return {
                    results: data.paquete
                };
           },
           cache: true,
        },
        minimumInputLength: 2
    });

    Inputmask.extendAliases({
        'rango': {
            mask: 'a-9999-99999999',
            placeholder: "X-0000-00000000",
            clearMaskOnLostFocus: true,
            onBeforePaste: function (pastedValue, opts) {
                
                return pastedValue.charAt(0).toUpperCase() + pastedValue.slice(1);
            },
            definitions: {
                'a': {
                    validator: "[A-Za-z]",
                    cardinality: 1,
                    casing: "upper"
                }
            }
        }
    });
    
    $("#Factura").inputmask('rango');

    $(document).on('click', '.aplicar', function(e){
        e.preventDefault();

        let examen = $('#examen').val(), paquete = $('#paquete').val(), facturacion = $('#facturacion').val(), cantidad = $('#cantidad').val(), empresa = $('#empresa').val(), contador = [examen, paquete, facturacion].filter(conteo => conteo !== null).length;

        if (contador !== 1) {
            toastr.warning("Solo puede elegir un paquete o examen para aplicar a la vez. No puede seleccionar más de uno", {timeOut: 2000});
            return;
        }

        if  (cantidad <= 0 || cantidad === '') {
            toastr.warning("Debe seleccionar una cantidad");
            return;
        }

        let valor = examen !== null ? examen : paquete !== null ? paquete : facturacion !== null ? facturacion : 0;
        let tipo = examen !== null ? 'examen' : paquete !== null ? 'paquete' : facturacion !== null ? 'facturacion' : 0;

        if(valor === 0 || tipo === 0) { toastr.warning("Hay un problema en la selección del paquete. Verifique la selección"); return; }

        preloader('on');

        $.post(savePaquete, {_token: TOKEN, Id: ID, Tipo: tipo, examen: valor, })
            .done(function(){
                preloader('off');
                toastr.success('Se ha realizado ha cargado el o los examenes.')
                setTimeout(()=> {
                    listado();
                }, 3000);
            })
    });

    function listado() {
        
        preloader('on');
        $('#lstSaldos').empty();

        $.get(listadoExCta, {Id: ID})
            .done(function(response){
                preloader('off');
          
                $.each(response, function(index, r){

                    let contenido = `
                        <tr>
                            <td>${r.Precarga === '' ? '-' : r.Precarga}</td>
                            <td>${r.Examen}</td>
                            <td>${r.Prestacion}</td>
                            <td>${r.NombrePaciente}</td>
                            <td>
                                <button data-remito="${r.IdEx}" type="button" class="pdf btn iconGeneral" title="Generar reporte en Pdf">
                                    <i class="ri-file-pdf-line"></i>
                                </button>
                            </td>
                        </tr>
                    `;

                    $('#lstSaldos').append(contenido);
                });

                $("#listadoSaldos").fancyTable({
                    pagination: true,
                    perPage: 15,
                    searchable: false,
                    globalSearch: false,
                    sortable: false, 
                });
            })
    }

    function preloader(opcion) {
        $('#preloader').css({
            opacity: '0.3',
            visibility: opcion === 'on' ? 'visible' : 'hidden'
        });
    }

});