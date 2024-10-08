$(document).ready(()=> {

    let messageClientes = $('#messageClientes');

    tabActivo();
    cargarAutorizados();
    checkProvincia();
    telefonos();
    quitarDuplicados("#Provincia");
    quitarDuplicados("#CondicionIva");
    quitarDuplicados("#FPago");
    checkBloq();
    examenes();

    $(document).on('click', '.delete-icon', function(e) {
        e.preventDefault();
        let Id = $(this).data('id');
       
        preloader('on');
        $.ajax({
            url: deleteAutorizado,
            type: 'Post',
            data: {
                Id: Id,
                _token: TOKEN,
            },
            success: function(){
                preloader('off');
                toastr.success('El autorizado ha sido eliminado correctamente');
                $('.body-autorizado').empty();
                cargarAutorizados();
            },
            error: function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;  
            }
        });
    });

    //Opciones en CLientes - carga de datos
    $('#btnOpciones').off('click').on('click', function(e) {
        e.preventDefault();
    
        let fisico = $('#RF').prop('checked')?1:0,
            sinEvaluacion = $('#SinEval').prop('checked')?1:0,
            facturacionSinPaq = $('#SinPF').prop('checked')?1:0,
            correo = $('#correoItem').prop('checked'),
            mensajeria = $('#mensajeriaItem').prop('checked'),
            bloqueado = $('#Bloqueado').prop('checked')?1:0,
            anexo =$('#anexo').prop('checked')?1:0,
            motivo = $('#MotivoB').val();

    
        if(correo == true && mensajeria == true){
            toastr.warning('¡No puede tener la opcion Mensajeria y Correo seleccionadas. Debe escoger por una opción!');
            return;
        }

        if(motivo === '' && bloqueado === 1){
            toastr.warning('¡El motivo es un campo obligatorio si ha seleccionado la opción bloquear. Debe escribir un motivo!');
            return;
        }
        
        preloader('on');
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
                anexo: anexo,
                motivo: motivo,
                Id: ID,
            },
            success: function(){
                preloader('off');
                toastr.success('¡Los datos se han guardado correctamente. Se recargará la página');
                setTimeout(()=> {
                    location.reload();
                }, 3000);
                
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
                            
                            swal('Atención', 'Se recargará la app tras el bloqueo', 'warning');
                            setTimeout(()=> {
                                location.reload();
                            }, 3000);
                            
        
                        },
                        error: function(jqXHR) {
                            preloader('off');
                            let errorData = JSON.parse(jqXHR.responseText);            
                            checkError(jqXHR.status, errorData.msg);
                            return;  
                            }
                        });

               }
                return;
          
            },
            error: function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;  
            }
        });

    });

    //Registro de autorizado
    $('#btnAutorizado').click(function (e) {
        e.preventDefault();

        let TipoEntidad = $('#TipoEntidad').val(),
            Nombre = $('#Nombre').val(),
            Apellido = $('#Apellido').val(),
            DNI = $('#DNI').val(),
            Derecho = $('#Derecho').val();

        let camposBasicos = [Nombre, Apellido, DNI, Derecho];

        if (camposBasicos.every(contenido => contenido === '')) {
            toastr.warning('Por favor, complete todos los campos obligatorios.');
            return;
        }

        if(DNI.length > 8 || parseInt(DNI) < 0){
            toastr.warning("El dni no puede contener más de 8 digitos o ser negativo");
            return;
        }

        if(Nombre.length > 25){
            toastr.warning("El nombre no puede contener mas de 25 caracteres");
            return;
        }

        if(Apellido.length > 30){
            toastr.warning("El apellido no puede contener mas de 30 caracteres");
            return;
        }
        preloader('on');
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
                preloader('off');
                toastr.success('El autorizado se registró correctamente');
                cargarAutorizados();
                $('#Nombre, #Apellido, #DNI, #Derecho').val('');
            },
            error: function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;  
            }
        });
    });

    //Chequeo de email. Carga de datos
    $('#guardarEmail').click(function(e){
        e.preventDefault();
        let controlEjecucion = true;
        
        // Verificación de correos
        let emailsResultados = $('#EMailResultados').val(),
            emailsInformes = $('#EMailInformes').val(),
            emailsFactura = $('#EMailFactura').val(),
            emailsAnexo = $('#EMailAnexo').val(),
            sinEnvio = $('#SEMail').prop('checked');
    
        if (!verificarCorreos(emailsResultados) || !verificarCorreos(emailsInformes) || !verificarCorreos(emailsFactura) || !verificarCorreos(emailsAnexo)) {
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
                anexo: emailsAnexo,
                Id: ID
            },
            success: function(response){
                toastr.success(response.msg);
                return;
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


    //Guardar las Observaciones en la base de datos
    $('#btnObservaciones').off('click').on('click', function(e) {
        e.preventDefault();

        let Observaciones = $('#Observaciones').val(),
            ObsCE = $('#ObsCE').val(),
            ObsCO = $('#ObsCO').val(),
            ObsEval = $('#ObsEval').val();
            Motivo = $('#Motivo').val();
        
        preloader('on');
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
                preloader('off');
                toastr.success('Las observaciones se han cargado correctamente');
            },
            error: function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;  
            }
        });
    });


    $(document).on('click', '#clonar', function(e){
        e.preventDefault();

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
            
        swal({
            title: "¿Esta seguro que desea clonar la información?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar)=>{
            if(confirmar){
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
            }
        });           
    });

       //get de los Autorizados
       function cargarAutorizados() {
        preloader('on');
        $.ajax({
            url: getAutorizados,
            type: 'GET',
            dataType: 'json',
            data: {
                Id: ID,
            },
            success: function(response) {
                preloader('off');
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
            error: function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;  
            }
        });
    }

    function checkProvincia(){

        let provincia = $('#Provincia').val(), localidad = $('#IdLocalidad').val();

        if ((provincia.length == 0 || provincia == 0) && (localidad.length > 0 || localidad == true))
        {
            $.ajax({
                url: checkProvController,
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

                    $('#Provincia').append(nuevoOption);
                },
                error: function(xhr){
                    toastr.warning('No se pudo autocompletar la provincia. Debe cargarlo manualmente.');                    
                }
            });
        }
    }

    $(document).on('click', '#addNumero', function(e) {
        e.preventDefault();
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

    $(document).on('click', '.ri-delete-bin-line', function(e) {
        e.preventDefault();
        let fila = $(this).closest('tr'), index = fila.index();
        fila.remove();
        $(`#hiddens .telefono-input:eq(${index})`).remove(); 
        actualizarInputHidden();
    });

     //Click para eliminar el telefono
     $(document).on('click', '.eliminarTelefono', function(e){
        e.preventDefault();
        let telefonoId = $(this).data("id");
        quitarTelefono(telefonoId);

    });

    $(document).on('click', '.editarTelefono', function(e){
        e.preventDefault();
        let telefonoId = $(this).data('id');

        verTelefono(telefonoId);
    });

    $(document).on('click', '.multiVolver', function(e) {
        window.history.back();
    });

    $(document).on('click', '#saveCambiosEdit', function(e){
        e.preventDefault();
        let Id = $('#Id').val(), CodigoArea = $('#nuevoPrefijo').val(), NumeroTelefono = $('#nuevoNumero').val(), Observaciones = $('#nuevaObservacion').val();

        if(CodigoArea == "" || NumeroTelefono == "") {
            toastr.warning("Faltan datos en el número de teléfono del cliente. No pueden haber campos vacíos.");
            return;
        }

        if(Observaciones == ""){
            toastr.warning("Debe escribir alguna referencia en la observación.");
            return;
        }

        saveEdicion(Id, CodigoArea, NumeroTelefono, Observaciones);
        telefonos();
        
    });

    $(document).on('click', '.nuevoExamen', function(e){
        e.preventDefault();
        localStorage.setItem('nuevaId', $(this).data('id'));
        localStorage.setItem('nuevaRazonSocial', $(this).data('name'));
        window.location.href = RUTAEXAMEN;
    });

    function actualizarInputHidden() {
        $('#hiddens .telefono-input').each(function(index) {
            $(this).attr('name', `telefonos[]`);
        });
    }

    function telefonos(){
        preloader('on');
        $.ajax({
            url: getTelefonos,
            type: 'GET',
            data: {
                Id: ID,
                tipo: 'all',
            },
            success: function(response){
                preloader('off');
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
            error: function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;  
            }

        });
    }
   
    function quitarTelefono(id){
        preloader('on');
        $.ajax({

            url: deleteTelefono,
            type: 'Post',
            data: {
                Id: id,
                _token: TOKEN
            },
            success: function(){
                preloader('off');
                toastr.success("El número de telefono adicional se ha borrado de la base de datos.");
                return;
            },
            error: function(jqXHR){ 
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;  
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

            success: function(){
                
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
            error: function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;  
            }
        });
    }

    function verTelefono(id){

        $('#editTelefonoModal .modal-footer').show();
        preloader('on');
        $.ajax({
            url: getTelefonos,
            type: "GET",
            data: {
                Id: id,
                tipo: 'one',
            },
            success: function(result){
                preloader('off');
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
            error: function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;  
            }
        });
    }
    
    function tabActivo(){
        $('.tab-pane').removeClass('active show');
        $('#datosBasicos').addClass('active show');
        $('.nav-link[href="datosBasicos"]').tab('show');
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
                    }
            })
            .fail(function(jqXHR){
                preloader('off');
                let errorData = JSON.parse(jqXHR.responseText);            
                checkError(jqXHR.status, errorData.msg);
                return;  
            });
    }

    function examenes(){
    $('#lstFact').empty();
    preloader('on');
    $.get(lstClientes, {Id: ID})
        .done(async function(response){
            preloader('off');
            let promises = response.map(async function(r) {
                if(response && response.length) {
                    let suc = (r.Suc).toString().padStart(4, '0'), numero = (r.Nro).toString().padStart(8, '0');
                    let moduloResult = await modulo(r.Id);
                    let contenido = `
                    <tr class="fondo-gris mb-2">
                        <td><span class="fw-bolder text-capitalize">fecha </span> ${fechaNow(r.Fecha,'/',0)}</td>
                        <td><span class="fw-bolder text-capitalize">factura </span> ${r.Tipo}${suc}${numero}</td>
                        <td><span class="fw-bolder text-capitalize">cantidad Pacientes </span>${r.Cantidad}</td>
                        <td>
                            <tr>
                                <td colspan="4">
                                    <span class="fw-bolder text-capitalize">Observación: </span><span>${r.Obs}</span>
                                </td>
                            </tr>
                        </td>  
                        ${moduloResult}
                    </tr>`;
                    return contenido;
                }else{
                    return '<tr class="mb-2"><td>No hay historial de facturas disponible</td></tr>';
                } 
            });
            let contents = await Promise.all(promises);
            contents.forEach(content => $('#lstFact').append(content));
        });
    }

    async function modulo(id) {
        return new Promise((resolve, reject) => {
            preloader('on');
            $.get(listadoDni, {Id: id})
                .done(async function(response){
                    preloader('off');
                    if (response && response.length) {
                        let result = '';
                        for (let r of response) {
                            let detallesResult = await detalles(r.IdPrestacion, r.IdPago); 
                            result += `
                                <tr class="fondo-grisClaro">
                                    <td colspan="4" class="fw-bolder"><span class="fw-bolder">${r.IdPrestacion === 0 ? 'Generales' : 'DNI'}</span> ${r.IdPrestacion === 0 ? '' : r.Documento}</td>
                                    ${detallesResult}
                                </tr>
                            `;
                        }
                        resolve(result);
                    }
                })
                .fail(function(error){
                    reject(error);
                });
        });
    }
    

    async function detalles(id, idpago) {
        return new Promise((resolve, reject) => {
            preloader('on');
            $.get(listadoEx, {Id: id, IdPago: idpago})
                .done(async function(response){
                    preloader('off');
                    if (response && response.length) {
                        let result =  '';
                        for (let r of response) {
                            let suc = [null, undefined, 0, ''].includes(r.Suc) ? '' : (r.Suc).toString().padStart(4, '0'), 
                                numero = [null, undefined, 0, ''].includes(r.Nro) ? '' : (r.Nro).toString().padStart(8, '0');
                            result += `
                            <tr>
                                <td>${r.Cantidad}</td>
                                <td>${r.NombreExamen}</td>
                                <td colspan="2"><span class="${r.Pagado === 0 ? 'rojo': ''}">${[null, undefined, 0, ''].includes(r.Tipo) ? '' : r.Tipo}${suc}${numero}</span></td>
                            </tr>
                            `;
                        }
                        resolve(result);
                    }
                })
                .fail(function(error){
                    reject(error);
                });
        });
        
    }    

});