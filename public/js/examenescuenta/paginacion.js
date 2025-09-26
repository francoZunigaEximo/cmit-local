function mostrarBotonesPago(valor) {

    if(valor === 'pago') {
        $('.quitarPago').show();
        $('.botonPagar').hide();
    
    }else if(valor === '') {
        $('.quitarPago').hide();
        $('.botonPagar').show();
    }else{
        $('.botonPagar, .quitarPago').hide();
    }
}

function habilitarMasivo(arg) {

    if (arg === 1) {
        $('.quitarPago').prop('disabled', false);
        $('.botonPagar').prop('disabled', true);

    } else if(arg === 0) {
        $('.quitarPago').prop('disabled', true);
        $('.botonPagar').prop('disabled', false);
        
    }else{
        $('.botonPagar, .quitarPago').prop('disabled', true);
    }
}


function obtenerFormato(date) {
    return date.toISOString().slice(0, 10);
};


function format(rowData) {
    return new Promise((resolve, reject) => {
        var div = `<div class="table-responsive table-card mt-3 mb-1 mx-auto">
                        <table id="detalles" class="display table table-bordered mx-auto" style="width:70%">
                            <thead class="table-light">
                                <tr>
                                    <th>Examen</th>
                                    <th>Prestación</th>
                                    <th>Paciente</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all">`;
        preloader('on');
        $.get(detallesExamenes, {Id: (rowData.IdEx === '' || rowData.IdEx === undefined ? 0 : rowData.IdEx)})
            .done(async function(response){
                let data = await response.result;
                preloader('off');
                $.each(data, function(index, d){
                   let nombreCompleto =  d.ApellidoPaciente + ' ' + d.NombrePaciente;

                    div += `<tr>
                                <td>${d.NombreExamen === undefined ? '-' : d.NombreExamen}</td>
                                <td>${d.IdPrestacion === undefined || d.IdPrestacion === 0 ? '-' : d.IdPrestacion}</td>
                                <td>${nombreCompleto}</td>
                            </tr>`;
                });
                div += `</tbody>
                        </table>
                    </div>`;

               

                resolve(div);
            })
            .fail(function(error){
                preloader('off');
                reject(error);
            });


    });
}


$(function(){

     $('#empresa').each(function() {
        $(this).select2({
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

    $('#paciente').each(function(){
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

    $('.botonPagar, .quitarPago').prop('disabled', true);
    mostrarBotonesPago($('#estado').val());

    const tabla = $('#listadoExamenesCuentas');

    let inputFechaDesde = $('#fechaDesde'),
        inputFechaHasta = $('#fechaHasta'),
        inputFacturaDesdeIndividual = $('#facturaDesdeIndividual'),
        inputFacturaHasta = $('#facturaHasta'),
        selectEmpresa = $('#empresa'),
        selectExamen = $('#examen'),
        selectPacienteDNI = $('#paciente'),
        selectEstado = $('#estado');

    let dataTable = new DataTable(tabla, {

        searching: false,
        ordering: true,
        order: [[0, 'desc'], [1, 'desc'], [2, 'desc'], [3, 'desc'], [4, 'desc'], [5, 'desc'], [6, 'desc'], [7, 'desc'], [8, 'desc']],
        fixedColumns: true,
        processing: true,
        lengthChange: false,
        pageLength: 7,
        deferRender: true,
        responsive: true,
        serverSide: true,
        stateSave: true,
        stateDuration: 60 * 60 * 24,
        stateLoadParams: function(settings, data) {
            if(data.customFilters) {
                inputFechaDesde.val(data.customFilters.fechaDesde);
                inputFechaHasta.val(data.customFilters.fechaHasta);
                inputFacturaDesdeIndividual.val(data.customFilters.facturaDesdeIndividual);
                inputFacturaHasta.val(data.customFilters.facturaHasta);
                if (data.customFilters.empresa && data.customFilters.empresa !== '') {
                    const EmpresaIdGuardado = data.customFilters.empresa;
                    const EmpresaTextGuardado = data.customFilters.nombreEmpresa;

                    if (!selectEmpresa.find('option[value="' + EmpresaIdGuardado + '"]').length) {

                        let newOption = new Option(EmpresaTextGuardado, EmpresaIdGuardado, true, true);
                        selectEmpresa.append(newOption);
                    }
                    selectEmpresa.val(EmpresaIdGuardado).trigger('change');
                } else {
                    selectEmpresa.val(null).trigger('change');
                }
                if (data.customFilters.examen && data.customFilters.examen !== '') {
                    const ExamenIdGuardado = data.customFilters.examen;
                    const ExamenTextGuardado = data.customFilters.nombreExamen;

                    if (!selectExamen.find('option[value="' + ExamenIdGuardado + '"]').length) {

                        let newOption = new Option(ExamenTextGuardado, ExamenIdGuardado, true, true);
                        selectExamen.append(newOption);
                    }
                    selectExamen.val(ExamenIdGuardado).trigger('change');
                } else {
                    selectExamen.val(null).trigger('change');
                }
                if (data.customFilters.pacienteDNI && data.customFilters.pacienteDNI !== '') {
                    const PacienteIdGuardado = data.customFilters.pacienteDNI;
                    const PacienteTextGuardado = data.customFilters.nombrePaciente;

                    if (!selectPacienteDNI.find('option[value="' + PacienteIdGuardado + '"]').length) {

                        let newOption = new Option(PacienteTextGuardado, PacienteIdGuardado, true, true);
                        selectPacienteDNI.append(newOption);
                    }
                    selectPacienteDNI.val(PacienteIdGuardado).trigger('change');
                } else {
                    selectPacienteDNI.val(null).trigger('change');
                }
                selectEstado.val(data.customFilters.estado).trigger('change');
            }
        },
        stateLoadCallback: function(settings) {
            let storedData = localStorage.getItem('DataTables_listadoExamenesCuentas_/examenesCuenta');
            if (storedData) {
                let data = JSON.parse(storedData);

                if (data.customFilters) {
     
                    inputFacturaDesdeIndividual.val(data.customFilters.facturaDesdeIndividual);
                    inputFacturaHasta.val(data.customFilters.facturaHasta);
                    if (data.customFilters.empresa && data.customFilters.empresa !== '') {
                        const EmpresaIdGuardado = data.customFilters.empresa;
                        const EmpresaTextGuardado = data.customFilters.nombreEmpresa;
                        if (!selectEmpresa.find('option[value="' + EmpresaIdGuardado + '"]').length) {
                            let newOption = new Option(EmpresaTextGuardado, EmpresaIdGuardado, true, true);
                            selectEmpresa.append(newOption);
                        }
                        selectEmpresa.val(EmpresaIdGuardado).trigger('change');
                    } else {
                        selectEmpresa.val(null).trigger('change');
                    }

                    if (data.customFilters.examen && data.customFilters.examen !== '') {
                        const ExamenIdGuardado = data.customFilters.examen;
                        const ExamenTextGuardado = data.customFilters.nombreExamen;
                        if (!selectExamen.find('option[value="' + ExamenIdGuardado + '"]').length) {
                            let newOption = new Option(ExamenTextGuardado, ExamenIdGuardado, true, true);
                            selectExamen.append(newOption);
                        }
                        selectExamen.val(ExamenIdGuardado).trigger('change');
                    } else {
                        selectExamen.val(null).trigger('change');
                    }
                    if (data.customFilters.pacienteDNI && data.customFilters.pacienteDNI !== '') {
                        const PacienteIdGuardado = data.customFilters.pacienteDNI;
                        const PacienteTextGuardado = data.customFilters.nombrePaciente;
                        if (!selectPacienteDNI.find('option[value="' + PacienteIdGuardado + '"]').length) {
                            let newOption = new Option(PacienteTextGuardado, PacienteIdGuardado, true, true);
                            selectPacienteDNI.append(newOption);
                        }
                        selectPacienteDNI.val(PacienteIdGuardado).trigger('change');
                    } else {
                        selectPacienteDNI.val(null).trigger('change');
                    }

                    selectEstado.val(data.customFilters.estado).trigger('change');

                    return data;
                }
            }

            return null;
        },
        stateSaveCallback: function (settings, data) {
            data.customFilters = {
                fechaDesde: inputFechaDesde.val(),
                fechaHasta: inputFechaHasta.val(),
                facturaDesdeIndividual: inputFacturaDesdeIndividual.val(), 
                facturaHasta: inputFacturaHasta.val(),
                empresa: selectEmpresa.val(),
                nombreEmpresa: selectEmpresa.find(':selected').text(),
                examen: selectExamen.val(),
                nombreExamen: selectExamen.find(':selected').text(),
                pacienteDNI: selectPacienteDNI.val(),
                nombrePaciente: selectPacienteDNI.find(':selected').text(),
                estado: selectEstado.val()
            };
            localStorage.setItem('DataTables_listadoExamenesCuentas_/examenesCuenta', JSON.stringify(data));
        },
        ajax: {
            url: SEARCH,
            data: function(d){
                d.fechaDesde = $('#fechaDesde').val();
                d.fechaHasta = $('#fechaHasta').val();
                d.rangoDesde = $('#rangoDesde').val();
                d.rangoHasta = $('#rangoHasta').val();
                d.empresa = $('#empresa').val();
                d.paciente = $('#paciente').val();
                d.examen = $('#examen').val();
                d.estado = $('#estado').val();
            }
        },
        dataType: 'json',
        type: 'POST',
        columnDefs: [
            {
                data: null,
                name: 'selectId',
                orderable: false,
                targets: 0,
                render: function(data){
               
                    return `<div class="text-center"><input type="checkbox" name="Id" value="${data.IdEx}" disabled></div>`;
                }
            },
            {
                data: null,
                name: 'IdEx',
                orderable: true,
                targets: 1,
                render: function(data){
                    return ("000000" + data.IdEx).slice(-6);
                }
            },
            {
                data: null,
                name: 'Numero',
                orderable: true,
                targets: 2,
                render: function(data){
                    return data.Tipo + ("0000" + data.Sucursal).slice(-4) + '-' + ("00000000" + data.Numero).slice(-8);
                }
            },
            {
                data: null,
                name: 'Fecha',
                orderable: true,
                targets: 3,
                render: function(data){
                    return fechaNow(data.Fecha,'/',0);
                }
            },
            {
                data: null,
                name: 'Empresa',
                orderable: true,
                targets: 4,
                render: function(data){
                    let EmpresaCompleto = data.Empresa + ' - ' + data.Cuit;
                    let recorte = (EmpresaCompleto).substring(0,25) + "...";
                    return recorte.length >= 25 ? `<span title="${EmpresaCompleto}">${recorte}</span>` : EmpresaCompleto;
                }
            },
            {
                data: null,
                name: 'ParaEmpresa',
                orderable: true,
                targets: 5,
                render: function(data){
                    let ParaEmpresa = data.ParaEmpresa;
                    let recorte = (ParaEmpresa).substring(0,25) + "...";
                    return recorte.length >= 25 ? `<span title="${ParaEmpresa}">${recorte}</span>` : ParaEmpresa;
                }
            },
            {
                data: null,
                name: 'FechaPagado',
                orderable: true,
                targets: 6,
                render: function(data){

                    return data.FechaPagado === '0000-00-00' ? '-' : fechaNow(data.FechaPagado,'/',0);
                }
            },
            {
                className: 'details-control',
                orderable: false,
                data: null,
                defaultContent: '',
                targets: 7
            },
            {
                data: null,
                name: 'ParaEmpresa',
                orderable: false,
                targets: 8,
                render: function(data) {
                    let nroFactura = data.Tipo + ("0000" + data.Sucursal).slice(-4) + '-' + ("00000000" + data.Numero).slice(-8);
                    let empresa = data.Empresa;

                    let editar = `<a title="Editar" href="${location.href}/${data.IdEx}/edit"><button type="button" class="btn btn-sm iconGeneralNegro"><i class="ri-edit-line"></i></button></a>`,
                
                        baja = `<button data-id="${data.IdEx}" title="Dar de baja" type="button" class="btn btn-sm iconGeneralNegro deleteExamen" ><i class="ri-delete-bin-2-line"></i></button>`,
                        
                        pago = `<button type="button" data-id="${data.IdEx}" data-nro="${nroFactura}" data-empresa="${empresa}" class="btn btn-sm botonGeneral cambiarBoton">${data.FechaPagado === '0000-00-00' ? 'Pagar' : 'Quitar pago'}</button>`;

                    return editar + ' ' + baja + ' ' + pago;  
                }
            }
        ],
        language: {
            processing: "<div style='text-align: center; margin-top: 20px;'><img src='./images/spinner.gif' /><p>Cargando...</p></div>",
            emptyTable: "No hay examenes con los datos buscados",
            paginate: {
                first: "Primera",
                previous: "Anterior",
                next: "Siguiente",
                last: "Última"
            },
            aria: {
                paginate: {
                    first: "Primera",
                    previous: "Anterior",
                    next: "Siguiente",
                    last: "Última"
                }
            },
            info: "Mostrando _START_ a _END_ de _TOTAL_ de facturas",
        },
        drawCallback: function() {
            $(document).on('change', '#estado', function() {
                let valor = $(this).val();
                mostrarBotonesPago(valor);
                
            });

            $('.botonPagar').prop('disabled', true);

            $('#listadoExamenesCuentas tbody').off('click', 'td.details-control').on('click', 'td.details-control', function(){
                let tr = $(this).closest('tr'), row = dataTable.row(tr);
            
                if(row.child.isShown()){
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    format(row.data()).then(function(div){
                        row.child(div).show();
                        tr.addClass('shown');
                    }).catch(function(error){
                        console.error('Error al cargar detalles:', error);
                    });
                }
            });
        
            $(document).on('click', '#btn-show-all-children', function(){
                tableIndex.rows().every(function(){
                    if(!this.child.isShown()){
                        this.child(format(this.data())).show();
                        $(this.node()).addClass('shown');
                    }
                });
            });
        
            $(document).on('click', '#btn-hide-all-children', function(){
                tableIndex.rows().every(function(){
                    if(this.child.isShown()){
                        this.child.hide();
                        $(this.node()).removeClass('shown');
                    }
                });
            });
        

        }
    });

    $(document).on('click', '#buscar, .sieteDias, .tresMeses, .facturasHoy', function (e) {
        e.preventDefault();

        if ($(this).hasClass('sieteDias') || $(this).hasClass('tresMeses')) {
            let fechaHastaObj = new Date(); // Fecha actual
            let fechaDesdeObj = new Date(fechaHastaObj);

            if ($(this).hasClass('sieteDias')) {
                fechaDesdeObj.setDate(fechaHastaObj.getDate() - 7);
            } else if ($(this).hasClass('tresMeses')) {
                fechaDesdeObj.setDate(fechaHastaObj.getDate() - 90);
            } else if ($(this).hasClass('facturasHoy')) {
                fechaDesdeObj.setData(fechaHastaObj.getDate());
            }

            inputFechaDesde.val(obtenerFormato(fechaDesdeObj));
            inputFechaHasta.val(obtenerFormato(fechaHastaObj)); 

        }

        if (!inputFechaDesde.val() || !inputFechaHasta.val()) {
            toastr.warning("Las fechas son obligatorias", "", { timeOut: 1000 });
            return;
        }

        dataTable.draw();
    });

});