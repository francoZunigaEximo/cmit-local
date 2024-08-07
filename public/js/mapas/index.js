$(document).ready(()=>{

    //Botón reset en el buscador
    $('#reset').click(function(){ 
        $('#form-index :input, #form-index select').val('');
        $('#Estado').val([]).trigger('change.select2');
        $('#Vencimiento').val([]).trigger('change.select2');
        $('#Ver').val('activo');
        $('#listaMapas').DataTable().clear().destroy();
    });

    //Datos Default
    $('#Ver').val('activo');

    $('#Nro, #ART, #TipoPrestacion, #Empresa, #Estado, #corteDesde, #corteHasta, #entregaDesde, #entregaHasta, #Vencimiento, #Ver ').on('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
        }
    });

    //Exportar Excel a clientes
    $('#excel').click(function(e) {
        e.preventDefault();

        let ids = [];
        $('input[name="Id"]:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            toastr.warning('Debes seleccionar al menos un mapa para exportar.');
            return;
        }

        swal({
            title: "¿Estás seguro de que deseas generar el reporte de Excel con todos los items seleccionados?",
            icon: "warning",
            buttons: ["Cancelar", "Exportar"]
        }).then((confirmar) => {
            if(confirmar){
                preloader('on');
                $.ajax({
                    url: fileExport,
                    type: "GET",
                    data: {
                        Id: ids,
                        archivo: 'csv'
                    },
                    success: function(response) {
                        preloader('off');
                        createFile("excel", response.filePath, generarCodigoAleatorio() + "_reporte");
                        toastr.success(response.msg);
                        return;
                    },
                    error: function(jqXHR) {
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return;  
                    }
                });
            }
        })
    });

    $(document).on('click','.deleteMapa', function(e){
        e.preventDefault();
        
        let id = $(this).data('id');
        swal({
            title: "¿Desea eliminar el registro del mapa?",
            icon: "warning",
            buttons: ["Cancelar", "Eliminar"]
        }).then((confirmar) => {
            if(confirmar){
                preloader("on");
                $.ajax({
                    url: deleteMapa,
                    type: 'POST',
                    data: {
                        _token: TOKEN, 
                        Id: id
                    },
                    success: function(response){
                        preloader("off");
                        toastr.success(response.msg);
                        $('#listaMapas').DataTable();
                        $('#listaMapas').DataTable().draw(false);
                    },
                    error: function(jqXHR){
                        preloader('off');
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return;  
                    }
                });
            }
        });  
    });

    $('#Empresa').select2({
        language: {
            noResults: function() {

            return "No hay empresas con esos datos";        
            },
            searching: function() {

            return "Buscando..";
            },
            inputTooShort: function () {
                return "Por favor, ingrese 2 o más caracteres";
            }
        },
        placeholder: 'Nombre Empresa, Alias o ParaEmpresa',
        allowClear: true,
        ajax: {
            url: getClientes, 
            dataType: 'json',
            data: function(params) {
                return {
                    buscar: params.term,
                    tipo: 'E'
                };
            },
            processResults: function(data) {
                return {
                    results: data.clientes 
                };
            },
            cache: true
        },
        minimumInputLength: 2 
    });

    $('#ART').select2({
        language: {
            noResults: function() {

            return "No hay art con esos datos";        
            },
            searching: function() {

            return "Buscando..";
            },
            inputTooShort: function () {
                return "Por favor, ingrese 2 o más caracteres";
            }
        },
        placeholder: 'Nombre de la ART',
        allowClear: true,
        ajax: {
            url: getClientes, 
            dataType: 'json',
            data: function(params) {
                return {
                    buscar: params.term,
                    tipo: 'A'
                };
            },
            processResults: function(data) {
                return {
                    results: data.clientes 
                };
            },
            cache: true
        },
        minimumInputLength: 2 
    });

    $(document).on("select2:open", () => {
        document.querySelector(".select2-container--open .select2-search__field").focus()
    });

});