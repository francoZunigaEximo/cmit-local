$(document).ready(()=> {

    let messageClientes = $('#messageClientes');

    toastr.options = {
        closeButton: true,   
        progressBar: true,    
        timeOut: 3000,        
    };

    tabActivo();
    cargarAutorizados();
    checkProvincia();
    telefonos();
    quitarDuplicados("#Provincia");
    quitarDuplicados("#CondicionIva");
    checkBloq();

    $(document).on('click', '.delete-icon', function() {
        let Id = $(this).data('id');
       
        $.ajax({
            url: deleteAutorizado,
            type: 'Post',
            data: {
                Id: Id,
                _token: TOKEN,
            },
            success: function(){
                toastr.success('El autorizado ha sido eliminado correctamente', 'Perfecto');
                $('.body-autorizado').empty();
                cargarAutorizados();
            },
            error: function(xhr){
                toastr.error('Hubo un inconveniento con el proceso. Consulto con el administrador', 'Error');
                console.error(xhr);
            }
        });
    });

    //Opciones en CLientes - carga de datos
    $('#btnOpciones').off('click').on('click', function() {
    
        let fisico = $('#RF').prop('checked')?1:0,
            sinEvaluacion = $('#SinEval').prop('checked')?1:0,
            facturacionSinPaq = $('#SinPF').prop('checked')?1:0,
            correo = $('#correoItem').prop('checked'),
            mensajeria = $('#mensajeriaItem').prop('checked'),
            bloqueado = $('#Bloqueado').prop('checked')?1:0,
            motivo = $('#MotivoB').val();

    
        if(correo == true && mensajeria == true){
            toastr.warning('¡No puede tener la opcion Mensajeria y Correo seleccionadas. Debe escoger por una opción!', 'Atención');
            return;
        }

        if(motivo === '' && bloqueado === 1){
        
            toastr.warning('¡El motivo es un campo obligatorio si ha seleccionado la opción bloquear. Debe escribir un motivo!', 'Atención');
            return;
        }

        if(motivo !== '' && bloqueado === 0){
        
            toastr.warning('¡No puede escribir un motivo si no ha bloqueado al cliente!', 'Atención');
            return;
        }
        
        $.ajax({
            url: checkOpciones,
            type: 'Post',
            data: {
                _token: TOKEN,
                fisico: fisico,
                sinEvaluacion: sinEvaluacion,
                facturacionSinPaq: facturacionSinPaq,
                correo: correo,
                mensajeria: mensajeria,
                bloqueado: bloqueado,
                Id: ID,
            },
            success: function(){

                toastr.success('¡Los datos se han guardado correctamente!', 'Perfecto');
                
               if(bloqueado === 1 && motivo !== ''){

                    $.ajax({
                        url: block,
                        type: 'POST',
                        data: {
                            _token: TOKEN,
                            motivo: motivo,
                            cliente: ID
                        },
                        success: function() {
                            
                            swal('Atención', 'Se recargará la app para bloquear las funcionalidades', 'warning');
                            setTimeout(()=> {
                                location.reload();
                            }, 3000);
                            
        
                        },
                        error: function(xhr) {
                            toastr.error('¡Ha ocurrido un inconveniente y la solicitud no podrá llevarse a cabo. Consulte con el administrador!', 'Error');
                            console.error(xhr);
                            }
                        });

               }
                return;
          
            },
            error: function(xhr){
                toastr.error('Hubo un error al obtener los autorizados. Consulte con el administrador', 'Error');
                console.error(xhr);
            }
        });

    });

    //Registro de autorizado
    $('#btnAutorizado').click(function () {

        let TipoEntidad = $('#TipoEntidad').val(),
            Nombre = $('#Nombre').val(),
            Apellido = $('#Apellido').val(),
            DNI = $('#DNI').val(),
            Derecho = $('#Derecho').val();

        if (Nombre === '' || Apellido === '' || DNI === '' || Derecho === '') {

            toastr.warning('Por favor, complete todos los campos obligatorios.', 'Atención');
            return;
        }


        if(DNI.length > 8 || parseInt(DNI) < 0){
            toastr.warning("El dni no puede contener más de 8 digitos o ser negativo", "Atención");
            return;
        }

        if(Nombre.length > 25){
            toastr.warning("El nombre no puede contener mas de 25 caracteres", "Atención");
            return;
        }

        if(Apellido.length > 30){
            toastr.warning("El apellido no puede contener mas de 30 caracteres", "Atención");
            return;
        }

        $.ajax({
            url: altaAutorizado,
            type: 'POST',
            data: {
               _token: TOKEN,
               Nombre: Nombre,
               Apellido: Apellido,
               DNI: DNI,
               TipoEntidad: TipoEntidad,
               Id: ID,
               Derecho: Derecho
            },
            success: function(){

                toastr.success('El autorizado se registró correctamente', 'Felicitaciones');
                cargarAutorizados();
                $('#Nombre, #Apellido, #DNI, #Derecho').val('');
            },
            error: function(xhr){

                toastr.error('Hubo un error en el registro. Consulte con el administrador.', 'Error');
                console.error(xhr);
            }
        });
    });

    //Chequeo de email. Carga de datos
    $('#guardarEmail').click(function(){

        let controlEjecucion = true;
        
        // Verificación de correos
        let emailsResultados = $('#EMailResultados').val(),
            emailsInformes = $('#EMailInformes').val(),
            emailsFactura = $('#EMailFactura').val(),
            sinEnvio = $('#SEMail').prop('checked');
    
        if (!verificarCorreos(emailsResultados) || !verificarCorreos(emailsInformes) || !verificarCorreos(emailsFactura)) {
            controlEjecucion = false;
        }
            
        if (controlEjecucion) {

            $.ajax({
            url: checkEmail,
            type: 'Post',
            data: {
                _token: TOKEN,
                resultados: emailsResultados,
                informes: emailsInformes,
                facturas: emailsFactura,
                sinEnvio: sinEnvio,
                Id: ID
            },
            success: function(){

                toastr.success('¡Se han registrado los cambios correctamente!', 'Excelente');
            },
            error: function(xhr){

                toastr.error('Hubo un error al obtener los autorizados. Consulte con el administrador', 'Error');
                console.error(xhr);
            }
            });
        }
    });


    //Guardar las Observaciones en la base de datos
    $('#btnObservaciones').off('click').on('click', function() {
        
        let Observaciones = $('#Observaciones').val(),
            ObsCE = $('#ObsCE').val(),
            ObsCO = $('#ObsCO').val(),
            ObsEval = $('#ObsEval').val();
            Motivo = $('#Motivo').val();
        
        $.ajax({
            url: setObservaciones,
            type: 'POST',
            data: {
                _token: TOKEN,
                Observaciones: Observaciones,
                ObsCE: ObsCE,
                ObsCO: ObsCO,
                ObsEval: ObsEval,
                Motivo: Motivo,
                Id: ID,
            },
            success: function(){

                toastr.success('Las observaciones se han cargado correctamente', 'Perfecto');
            },
            error: function(xhr){

                toastr.error('Hubo un error en el registro. Consulte con el administrador', 'Error')
                console.error(xhr);
            }
        });
    });


    $(document).on('click', '#clonar', function(){
        
        let TipoCliente = $('#TipoCliente').val();
            Identificacion = $('#Identificacion').val(),
            RazonSocial = $('#RazonSocial').val(),
            CondicionIva = $('#CondicionIva').val(),
            Telefono = $('#Telefono').val(),
            EMail = $('#EMail').val(),
            ObsEMail = $('#ObsEMail').val(),
            Direccion = $('#Direccion').val(),
            Provincia = $('#Provincia').val(),
            IdLocalidad = $('#IdLocalidad').val(),
            CP = $('#CP').val();

        localStorage.setItem('clon_TipoCliente', TipoCliente);
        localStorage.setItem('clon_Identificacion', Identificacion);
        localStorage.setItem('clon_RazonSocial', RazonSocial);
        localStorage.setItem('clon_CondicionIva', CondicionIva);
        localStorage.setItem('clon_Telefono', Telefono);
        localStorage.setItem('clon_EMail', EMail);
        localStorage.setItem('clon_ObsEMail', ObsEMail);
        localStorage.setItem('clon_Direccion', Direccion);
        localStorage.setItem('clon_Provincia', Provincia);
        localStorage.setItem('clon_Localidad', IdLocalidad);
        localStorage.setItem('clon_CodigoPostal', CP);

        window.location.href = GOCREATE;

    });

       //get de los Autorizados
       function cargarAutorizados() {

        $.ajax({
            url: getAutorizados,
            type: 'GET',
            dataType: 'json',
            data: {
                Id: ID,
            },
            success: function(response) {
                let autorizados = response;

                $('.body-autorizado').empty();

                if (autorizados.length == 0) {
                    let contenido = '<p> Sin datos de Autorizados </p>';
                    $('.body-autorizado').append(contenido);

                }else{

                    $.each(autorizados, function(index, autorizado) {
                        let contenido = `<div class="d-flex align-items-center mb-3">
                                            <div class="flex-shrink-0 avatar-sm">
                                                <div class="avatar-title bg-light text-primary rounded-3 fs-18">
                                                    <i class="ri-shield-user-line"></i>
                                                </div> 
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6>${autorizado.Nombre} ${autorizado.Apellido} | DNI: ${autorizado.DNI} | <span class="derechoAutorizado">${autorizado.Derecho}</span> | <i id="deleteAutorizado" class="fas fa-trash-alt delete-icon" data-id="${autorizado.Id}"></i></h6>
                                            </div>
                                            <div>

                                            </div>
                                        </div>`;

                        $('.body-autorizado').append(contenido);
                    });
                }
            },
            error: function(xhr){

                toastr.error('Hubo un error al obtener los autorizados. Consulte con el administrador', 'Error');
                console.error(xhr);
            }
        });
    }

    function checkProvincia(){

        let provincia = $('#Provincia').val(), localidad = $('#IdLocalidad').val();

        if ((provincia.length == 0 || provincia == 0) && (localidad.length > 0 || localidad == true))
        {
            $.ajax({
                url: checkProvController,
                type: 'POST',
                data: {
                    localidad: localidad,
                    _token: TOKEN
                },
                success: function(response){
                    let provinciaNombre = response.fillProvincia;

                    let nuevoOption = $('<option>', {
                        value: provinciaNombre,
                        text: provinciaNombre,
                        selected: true,
                    });

                    $('#Provincia').append(nuevoOption);
                },
                error: function(xhr){
                    toastr.warning('No se pudo autocompletar la provincia. Debe cargarlo manualmente.', 'Atención');
                    console.error(xhr);
                    
                }
            });
        }
    }

    const idProvincias = [
        { key: "26", value: "BARILOCHE" },
        { key: "4", value: "BUENOS AIRES" },
        { key: "3", value: "CAPITAL FEDERAL" },
        { key: "15", value: "CATAMARCA" },
        { key: "29", value: "CHACO" },
        { key: "11", value: "CHUBUT" },
        { key: "7", value: "CIUDAD DE BUENOS AIRES" },
        { key: "28", value: "COLOMBIA" },
        { key: "6", value: "CORDOBA" },
        { key: "10", value: "CORRIENTES" },
        { key: "13", value: "ENTRE RIOS" },
        { key: "32", value: "FORMOSA" },
        { key: "30", value: "GENERAL ALVEAL" },
        { key: "31", value: "GENERAL ALVEAR" },
        { key: "16", value: "JUJUY" },
        { key: "5", value: "LA PAMPA" },
        { key: "14", value: "LA RIOJA" },
        { key: "27", value: "LAS OVEJAS" },
        { key: "8", value: "MENDOZA" },
        { key: "18", value: "MISIONES" },
        { key: "25", value: "NECOCHEA" },
        { key: "1", value: "NEUQUEN" },
        { key: "33", value: "RIO GRANDE" },
        { key: "2", value: "RIO NEGRO" },
        { key: "17", value: "ROSARIO" },
        { key: "9", value: "SALTA" },
        { key: "22", value: "SAN JUAN" },
        { key: "19", value: "SAN LUIS" },
        { key: "23", value: "SANTA CRUZ" },
        { key: "20", value: "SANTA FE" },
        { key: "21", value: "SANTIAGO DEL ESTERO" },
        { key: "34", value: "TIERRA DEL FUEGO" },
        { key: "12", value: "TUCUMAN" },
        { key: "24", value: "VENEZUELA" },
    ];

    $(document).on('click', '#addNumero', function() {
        let prefijo = $('#prefijoExtra').val(), numero = $('#numeroExtra').val(), observacion = $('#obsExtra').val();

        if (prefijo !== '' && numero !== '' && observacion !== '') {
            $('#tablaTelefonos').append(`
                <tr>
                    <td>${prefijo}</td>
                    <td>${numero}</td>
                    <td>${observacion}</td>
                    <td>
                        <i id="eliminarTelefono" class="ri-delete-bin-line" title="Eliminar"></i> <span class="badge text-bg-warning" title="Actualice el cliente para guardar">Nuevo</span>
                    </td>
                </tr>
            `);

            let datosArray = [prefijo, numero, observacion],
                datosArrayJSON = JSON.stringify(datosArray);

            $('#hiddens').append(`
                <input type='hidden' class='telefono-input' name='telefonos[]' value='${datosArrayJSON}'>
            `);

            $('#prefijoExtra').val("");
            $('#numeroExtra').val("");
            $('#obsExtra').val("");

            actualizarInputHidden();

        }
    });

    $(document).on('click', '.ri-delete-bin-line', function() {
        let fila = $(this).closest('tr'), index = fila.index();
        fila.remove();
        $(`#hiddens .telefono-input:eq(${index})`).remove(); 
        actualizarInputHidden();
    });

     //Click para eliminar el telefono
     $(document).on('click', '.eliminarTelefono', function(){

        let telefonoId = $(this).data("id");
        quitarTelefono(telefonoId);

    });

    $(document).on('click', '.editarTelefono', function(){

        let telefonoId = $(this).data('id');

        verTelefono(telefonoId);
    });

    $(document).on('click', '#saveCambiosEdit', function(){
        
        let Id = $('#Id').val(), CodigoArea = $('#nuevoPrefijo').val(), NumeroTelefono = $('#nuevoNumero').val(), Observaciones = $('#nuevaObservacion').val();

        if(CodigoArea == "" || NumeroTelefono == "") {
            toastr.warning("Faltan datos en el número de teléfono del cliente. No pueden haber campos vacíos.", "Atención");
            return;
        }

        if(Observaciones == ""){
            toastr.warning("Debe escribir alguna referencia en la observación.", "Atención");
            return;
        }

        saveEdicion(Id, CodigoArea, NumeroTelefono, Observaciones);
        telefonos();
        
    });

    function actualizarInputHidden() {
        $('#hiddens .telefono-input').each(function(index) {
            $(this).attr('name', `telefonos[]`);
        });
    }

    function telefonos(){

        $.ajax({
            url: getTelefonos,
            type: 'GET',
            data: {
                Id: ID,
                tipo: 'all',
            },
            success: function(response){
                let telefonos = response;

                if(telefonos.length > 0) {

                    $.each(telefonos, function(index, result) {
                        let contenido = `<tr>
                                            <td>${result.CodigoArea}</td>
                                            <td>${result.NumeroTelefono}</td>
                                            <td>${result.Observaciones}</td>
                                            <td><div class="telefonoAcciones"><i data-id="${result.Id}" class="ri-edit-2-line editarTelefono" title="Editar" data-bs-toggle="modal" data-bs-target="#editTelefonoModal" id="editarTelefono"></i> <i data-id="${result.Id}" class="ri-delete-bin-line eliminarTelefono" title="Eliminar" id="eliminarTelefono"></i></li> <span class="badge text-bg-success" title="Este registro ya se encuentra en la base de datos">Base de datos</span></div></td>
                                        </tr>`;
                        
                        $('#tablaTelefonos').append(contenido);
                    });
                } else {

                    let contenido = '<p> Sin telefonos adicionales </p>';
                    $('#tablaTelefonos').append(contenido);
                }
            },
            error: function(xhr){
                toastr.error('Hay un error en la carga de los telefonos adicionales. Consulte con el administrador', 'Error');
                console.error(xhr);
            }

        });
    }
   
    function quitarTelefono(id){

        $.ajax({

            url: deleteTelefono,
            type: 'Post',
            data: {
                Id: id,
                _token: TOKEN
            },
            success: function(){

                toastr.success("El número de telefono adicional se ha borrado de la base de datos.", "Excelente");
            },
            error: function(xhr){ 

                toastr.error("Ha ocurrido un error al intentar eliminar el número de telefono adicional. Consulte con el administrador", "Error");
                console.error(xhr);
            }

        });

    }

    function saveEdicion(id, prefijo, numero, observacion){

        $.ajax({
            url: saveTelefono,
            type: 'post',
            data: {
                IdRegistro: id,
                prefijo: prefijo,
                numero: numero,
                observacion: observacion,
                _token: TOKEN
            },

            success: function(result){
                
                let contenido = `
                    <lord-icon
                        src="https://cdn.lordicon.com/lupuorrc.json"
                        trigger="loop"
                        colors="primary:#121331,secondary:#08a88a"
                        style="width:250px;height:250px">
                    </lord-icon>
                    <div class="mt-4">
                        <h4 class="mb-3">Se han realizado los cambios correctamente</h4>
                        <p class="text-muted mb-4">Los cambios se verán en unos 3 segundos...</p>
                    </div>
                `;


                $('#editTelefonoModal .modal-body').empty().append(contenido);
                $('#editTelefonoModal .modal-footer').hide();

                setTimeout(function() {
                    $('#editTelefonoModal ').modal('hide');
                }, 3000);
                $('#tablaTelefonos').empty();
                telefonos(); 
                
            },
            error: function(xhr){
                toastr.error('Hubo un error en el proceso de guardar los cambios telefonicos. Consulte con el administrador', 'Error');
                console.error(xhr);
            }
        });
    }

    function verTelefono(id){

        $('#editTelefonoModal .modal-footer').show();

        $.ajax({
            url: getTelefonos,
            type: "GET",
            data: {
                Id: id,
                tipo: 'one',
            },
            success: function(result){
                
                let telefono = result;
                $('#editTelefonoModal .modal-body').empty();
                
                let contenido = `
                <div class="row g-3">
                    <div class="col-xxl-6">
                        <input type="hidden" value="${telefono.Id}" id="Id">
                        <div>
                            <label class="form-label d-inline" for="nuevoPrefijo">Prefijo</label>
                            <input class="form-control d-inline" type="number" name="nuevoPrefijo" id="nuevoPrefijo" value="${telefono.CodigoArea}">
                        </div>
                    </div>
                    <div class="col-xxl-6">
                        <div>
                            <label class="form-label d-inline"for="nuevoNumero">Número</label>
                            <input class="form-control d-inline" type="number" name="nuevoNumero" id="nuevoNumero" value="${telefono.NumeroTelefono}">
                        </div>
                    </div>
                    <div class="col-xxl-12">
                        <div>
                            <label class="form-label d-inline" for="nuevaObservacion">Observación</label>
                            <input class="form-control d-inline" type="text" name="nuevaObservacion" id="nuevaObservacion" value="${telefono.Observaciones}">
                        </div>
                    </div>
                </div>
                `;

                $('#editTelefonoModal .modal-body').append(contenido);

            },
            error: function(xhr){
                toastr.error("Ha ocurrido un error en el proceso de obtener los datos. Consulte con el administrador", "Error");
                console.error(xhr);
            }
        });
    }

    function verificarCorreos(emails) {
        
        let emailRegex = /^[\w.-]+(\.[\w.-]+)*@[\w.-]+\.[A-Za-z]{2,}$/;
        let correosInvalidos = [];
        let emailsArray = emails.split(',');

        for (let i = 0; i < emailsArray.length; i++) {
            let email = emailsArray[i].trim();

            if (email !== "" && !emailRegex.test(email)) {
                correosInvalidos.push(email);
            }
        }

        if (correosInvalidos.length > 0) {
            swal("Atención", "Estos correos tienen formato inválido. Verifique por favor: " + correosInvalidos.join(", "), "warning");
            return false; 
        }

        return true; 
    }
    
    function tabActivo(){
        $('.tab-pane').removeClass('active show');
        $('#datosBasicos').addClass('active show');
        $('.nav-link[href="datosBasicos"]').tab('show');
    }

    function quitarDuplicados(selector) {
        let seleccion = $(selector).val();
        let countSeleccion = $(selector + " option[value='" + seleccion + "']").length;
    
        if (countSeleccion > 1) {
            $(selector + " option[value='" + seleccion + "']:gt(0)").hide();
        }
    }

    function checkBloq(){
        
        $.get(getBloqueo, {Id: ID})
            .done(async function(response){

                if(response){

                    let data = await response.cliente;

                    messageClientes.html(`<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong> Motivo del bloqueo: </strong> ${data.Motivo}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>`);

                    $('#addNumero, #Bloqueado, #btnOpciones, #guardarEmail, #btnAutorizado, #btnObservaciones, [type="submit"]').prop('disabled', true);
                    $('.telefonoAcciones').empty().hide();
                    }
            })
            .fail(function(xhr){
                console.error(xhr);
                toastr.error("Ha ocurrido un error. Consulte con el administrador");
            });
    }

});