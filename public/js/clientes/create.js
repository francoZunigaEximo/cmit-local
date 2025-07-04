$(function(){

    //Verifica si ya existe ese ParaEmpresa con ese Cuit
    $('#Identificacion, #ParaEmpresa').blur(function () {
        let cuit = $('#Identificacion').val(),
            empresa = $('#ParaEmpresa').val();

        if (cuit && empresa) {
            $.ajax({
                url: verifyIdentificacion,
                type: "GET",
                data: {
                    Identificacion: cuit,
                    ParaEmpresa: empresa,
                },
                success: function (response) {
                    if (response && Object.keys(response).length > 0) {
                            url = editUrl.replace('__cliente__', response.Id);
                        $('#editLink').attr('href', url);

                        $('#myModal').modal('show');
                    }
                }
            });
        }
    });


    //Autocompletamos ParaEmpresa con RazonSocial
    $('#RazonSocial').change(function() {
        let razonSocial = $(this).val(),
            paraEmpresa = $('#ParaEmpresa');
    
        if (paraEmpresa.val().length == 0 && razonSocial.length > 0) {
            paraEmpresa.val(razonSocial);
        }
    });


    //Aviso de cuit existente
    $('#Identificacion').change(function () {
        let cuit = $(this).val();

        $.ajax({
            url: verifyIdentificacion,
            type: "GET",
            data: {
                Identificacion: cuit,
            },
            success: function(response){

                if(response && Object.keys(response).length > 0){
                    toastr.warning('Ya existe ese cuit registrado en la base de datos','',{timeOut: 1000});
                    return;
                }
            }
        });
    });

    $('#Descuento').on('input', function () {
        let valor = $(this).val().replace('%', '');
        if (!isNaN(valor) && valor) {
            $(this).val(valor + '%');
        }
    });

    function buscarLocalidad(id){
        if(!id) return;

        $.ajax({
            url: searchLocalidad,
            type: 'GET',
            data: {
                Id: id,
            },
            success: function(response){
                let resultado = response.resultado,
                    contenido = `<option selected value="${resultado.Nombre}">${resultado.Nombre}</option>`;
                $('#IdLocalidad').append(contenido);
            },
            error: function(xhr){
                console.error(xhr);
                toastr.error("Hay un error en la busqueda de la localidad. Consulte al administrador", "Error", {timeOut: 1000});
            }
        });
    }

    let clonTipoCliente = localStorage.getItem('clon_TipoCliente'),
        clonIdentificacion = localStorage.getItem('clon_Identificacion'),
        clonRazonSocial = localStorage.getItem('clon_RazonSocial'),
        clonCondicionIva = localStorage.getItem('clon_CondicionIva'),
        clonTelefono = localStorage.getItem('clon_Telefono'),
        clonEMail = localStorage.getItem('clon_EMail'),
        clonObsEMail = localStorage.getItem('clon_ObsEMail'),
        clonDireccion = localStorage.getItem('clon_Direccion'),
        clonProvincia = localStorage.getItem('clon_Provincia'),
        clonLocalidad = localStorage.getItem('clon_Localidad'),
        clonCodigoPostal = localStorage.getItem('clon_CodigoPostal');

    //Agregamos los campos clonados
    $('#TipoCliente').val(clonTipoCliente);
    $('#Identificacion').val(clonIdentificacion);
    $('#RazonSocial').val(clonRazonSocial);
    $('#CondicionIva').val(clonCondicionIva);
    $('#Telefono').val(clonTelefono);
    $('#EMail').val(clonEMail);
    $('#ObsEMail').val(clonObsEMail);
    $('#Direccion').val(clonDireccion);
    $('#Provincia').val(clonProvincia);
    //let addlocalidad = buscarLocalidad(clonLocalidad);
    buscarLocalidad(clonLocalidad);
    $('#CP').val(clonCodigoPostal);

    //Borramos los item clonados
    localStorage.clear();

    let exito = '<div class="alert alert-primary alert-dismissible fade show" role="alert">' +
                '<strong> ¡Clonación correcta! </strong> Se ha clonado al cliente. Relleno los datos faltantes' +
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                '</div>';
    
    if(clonTipoCliente == '' && clonIdentificacion == ''){
        $('#messageClientes').html(exito);
        return
    }   
    //Añadimos nuevos números para guardar
    $(document).on('click', '#addNumero', function() {
        let prefijo = $('#prefijoExtra').val(),
            numero = $('#numeroExtra').val(),
            observacion = $('#obsExtra').val();

        if (prefijo && numero && observacion) {
            $('#tablaTelefonos').append(`
                <tr>
                    <td>${prefijo !== "" ? prefijo : ""}</td>
                    <td>${numero !== "" ? numero : ""}</td>
                    <td>${observacion !== "" ? observacion : ""}</td>
                    <td>
                        <i class="ri-delete-bin-line"></i>
                    </td>
                </tr>
            `);

            let datosArray = [prefijo, numero, observacion],
                datosArrayJSON = JSON.stringify(datosArray);

            $('#hiddens').append(`
                <input type='hidden' class='telefono-input' name='telefonos[]' value='${datosArrayJSON}'>
            `);

            $('#prefijoExtra, #numeroExtra, #obsExtra').val("");

            actualizarInputHidden();
        }
    });

    //Eliminamos de la grilla los datos
    $(document).on('click', '.ri-delete-bin-line', function() {
        let fila = $(this).closest('tr'),
            index = fila.index();

        fila.remove();
        $(`#hiddens .telefono-input:eq(${index})`).remove(); 
        actualizarInputHidden();
    });
    
    //Actualizamos los Hiddens que se generan para guardar en la base de datos
    function actualizarInputHidden() {
        $('#hiddens .telefono-input').each(function(index) {
            $(this).attr('name', `telefonos[]`);
        });
    }
              
});

