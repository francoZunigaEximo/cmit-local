$(function() {

    const tabla = $('#lstBloqueoCliente');

    $(document).on('click', '.btnRestaurar', function(e) {
        e.preventDefault();

        let id = $(this).data('id');

        if(!id) return;

        swal({
            'title': '¿Esta seguro que desea restaurar al cliente?',
            'icon': 'warning',
            'buttons': ['Cancelar', 'Aceptar']
        }).then(async (confirmar) => {

            if(confirmar) {
                 preloader('on');
                    try {
                        let response = await $.get(restaurarEliminado, {Id: id});

                        if(response.status === 'success') {
                                toastr.success(response.msg, '', { timeOut: 2000 });
                                tabla.DataTable().draw(false);
                        }

                    }catch(jqXHR) {
                        let errorData = JSON.parse(jqXHR.responseText);            
                        checkError(jqXHR.status, errorData.msg);
                        return;  

                    }finally{
                        preloader('off');
                    }
            }
        });
    });



});