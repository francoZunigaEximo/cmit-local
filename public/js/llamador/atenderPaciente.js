$(function(){

    const variables = {
        profesional: $('#profesional'),
        prestacion: $('#prestacion_var')
    };

    const principal = {
        efector: 'Efector',
        tabla: '#listaLlamadaEfector',
        atenderEfector: '#atenderEfector'
    };

    $(document).on('click', 'input[type="checkbox"][name^="Id_"]', function () {

        let chequeado = $(this).is(':checked'),
            idCheck = $(this).val();

        if(!idCheck) return;

        // console.log(chequeado, idCheck, variables.profesional.val());
        preloader('on')
        $.get(asignacionProfesional, {Id: idCheck, Profesional: variables.profesional.val(), estado: chequeado})
            .done(function(response) {
                preloader('off')
                toastr.success(response.msg);
            })
            .fail(function(jqXHR) {
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;
            });
    });

    $(document).on('click', '.abrir, .cerrar', function(e){
        e.preventDefault();
        accion = $(this).hasClass('abrir') ? 'abrir' : 'cerrar';

        if(!accion) return;

        let fila = $(this).closest('tr.listadoAtencion'),
             id = fila.data('id'); 

        swal({
            title: "¿Esta seguro que desea cambiar el estado?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                preloader('on')
                $.get(itemPrestacionEstado, {Id: id, accion: accion, tipo: principal.efector})
                    .done(function(response){
                        preloader('off');
                        toastr.success(response.msg);
                        estado(response.CAdj, response.IdItem);
                        $(principal.tabla).DataTable().draw(false);
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

        $(document).on('click', '.terminarAtencion', function(e){
            e.preventDefault();

            $(principal.tabla).DataTable().draw(false);
            principal.atenderEfector.modal(hide);
            $.get(cerrarAtencion, {Id: variables.prestacion.val(), tipo: (variables.efector).toUpperCase()})
                .done(function(response){
                    $(principal.tabla).DataTable().draw(false);
                })
                .fail(function(jqXHR){
                    preloader('off');
                    let errorData = JSON.parse(jqXHR.responseText);            
                    checkError(jqXHR.status, errorData.msg);
                    return;
                });

        });


        function estado(CAdj, itemId) {

            let fila = $('.listadoAtencion[data-id="' + itemId + '"]'),
                td = fila.find('td').eq(1),
                html = '';

            if ([0, 1, 2].includes(CAdj)) {
                html = '<span class="rojo">Abierto <i class="fs-6 ri-lock-unlock-line cerrar"></i></span>';
            } else if ([3, 4, 5].includes(CAdj)) {
                html = '<span class="verde">Cerrado <i class="fs-6 ri-lock-2-line abrir"></i></span>';
            } else {
                html = '';
            }

            td.html(html);
        }
        

});