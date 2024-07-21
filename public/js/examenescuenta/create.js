$(document).ready(function(){

    toastr.options = {
        closeButton: true,   
        progressBar: true,    
        timeOut: 3000,        
    };

    nuevoEmpresa(localStorage.getItem('nuevaId'), localStorage.getItem('nuevaRazonSocial'));

    $(document).on('click', '.crearPagoCuenta', function(){
        
        let empresaCreate = $('#empresaCreate').val(), FechaCreate = $('#FechaCreate').val(), FacturaCreate = $('#FacturaCreate').val(), FechaPago = $('#FechaPago').val(), ObsPago = $('#ObsPago').val(),
            condiciones = [empresaCreate, FechaCreate, FacturaCreate],
            partes = FacturaCreate.split('-');

        if (condiciones.some(condicion => condicion === '' || condicion === null) === true) {
            toastr.warning("Los campos marcados con astericos son obligatorios");
            return;
        }

        preloader('on');
        $.post(saveExamenCuenta, {_token: TOKEN, IdEmpresa: empresaCreate, Fecha: FechaCreate, Tipo: partes[0], Suc: parseInt(partes[1], 10), Nro: parseInt(partes[2], 10), Obs: ObsPago, FechaP: FechaPago})
            .done(function(response){
                preloader('off');
                toastr.success('Se ha realizado la operación correctamente. Se habilitarán las opciones');
                setTimeout(()=> {
                    let nuevo = location.href.replace("create", "");
                    let lnk = nuevo + response.id + "/edit";
                    window.location.href = lnk;
                }, 3000);
            })
    });

    $('.volverPagoCuenta').click(function(){
        history.back();
    });

    function preloader(opcion) {
        $('#preloader').css({
            opacity: '0.3',
            visibility: opcion === 'on' ? 'visible' : 'hidden'
        });
    }

    function nuevoEmpresa(id, name) {
        if (id && name) {

            if ($('#empresaCreate').data('select2')) {
                var nuevaOpcion = new Option(name, id, true, true);
                $('#empresaCreate').append(nuevaOpcion).trigger('change');
                localStorage.removeItem('nuevaId');
                localStorage.removeItem('nuevaRazonSocial');
            } else {
                setTimeout(function() { nuevoEmpresa(id, name); }, 500);
            }
        }
    }
    

});