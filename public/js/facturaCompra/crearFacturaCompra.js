
$(document).ready(function () {

    cargarTablaExamenesEfector();
    cargarTablaExamenesInformador();

    $('#checkAllEfector').on('change', function () {
        const check = this.checked;
        $('.fila-checkbox-efector').prop('checked', check);
    });

    $('#checkAllInformador').on('change', function () {
        const check = this.checked;
        $('.fila-checkbox-informador').prop('checked', check);
    });
});

$("#buscarEfectores").on("click", function (e) {
    e.preventDefault();
    if($('#rol').val() == 1){
        $('#listadoExamenesInformador').DataTable().clear().destroy();
        cargarTablaExamenesEfector();
    }else if($('#rol').val() == 2){
        $('#listadoExamenesEfector').DataTable().clear().destroy();
        cargarTablaExamenesInformador();
    }else{
        cargarTablaExamenesEfector();
        cargarTablaExamenesInformador();
    }
});

$("#subtotal").on("click", function (e) {
    e.preventDefault();
    cargarCantidadExamenesEfector();
    cargarCantidadExamenesInformador();
    $("#modalSubtotal").modal("show");
});

$("#modalFacturarBtn").on("click", function (e) {
    e.preventDefault(); 
    $("#modalFacturar").modal("show");
}); 

$("#btnFacturar").on("click", function (e) {
    e.preventDefault(); 
    facturar();
});

function cargarTablaExamenesEfector() {


    $('#listadoExamenesEfector').DataTable().clear().destroy();

    const tabla = new DataTable("#listadoExamenesEfector", {
        searching: false,
        ordering: false,
        processing: true,
        lengthChange: false,
        pageLength: 50,
        responsive: false,
        serverSide: true,
        deferRender: true,
        scrollCollapse: true,
        autoWidth: false,
        select: {
            style: 'multi'
        },
        ajax: {
            url: rutaListarExamenesEfector,
            data: function (e) {
                e.fechaDesde = $('#fechaDesdeEfector').val();
                e.fechaHasta = $('#fechaHastaEfector').val();
                e.idProfesional = ID_PROFESIONAL;
            }
        },
        dataType: 'json',
        type: 'POST',
        columnDefs: [
            {
                data: null,
                orderable: false,
                targets: 0,
                className: 'select-checkbox',
                defaultContent: '',
                render: function (data, type, row) {
                    var check = $("#checkAllEfectores").is(":checked") ? "checked" : "";
                    return `<input type="checkbox" class="fila-checkbox-efector" value="${data.Id}" ${check} />`;
                }
            },
            {
                data: null,
                name: 'Paciente',
                orderable: true,
                targets: 1,
                render: function (data) {
                    return `<div class="text-start"><span>${data.IdPrestacion == null ? "" : data.IdPrestacion}</span></div>`;
                }

            },
            {
                data: null,
                name: 'Fecha',
                targets: 2,
                render: function (data) {
                    return `<div class="text-start"><span>${data.Fecha == null ? "" : data.Fecha}</span></div>`;
                }
            },
            {
                data: null,
                name: 'Examen',
                targets: 3,
                render: function (data) {
                    return `<div class="text-start"><span>${data.Examen == null ? "" : data.Examen}</span></div>`;
                }
            },{
                data: null,
                name: 'Paciente',
                targets: 4,
                render: function (data) {
                    return `<div class="text-start"><span>${data.ApPac == null ? "" : data.ApPac}, ${data.NomPac == null ? "" : data.NomPac}</span></div>`;
                }
            },{
                data: null,
                name: 'Estados',
                targets: 5,
                render: function (data) {
                    var adjuntado = "";
                    if(data.Adjunto == 1){
                        adjuntado = data.Adjuntado == 1 ? "<i title='Adjuntado' class='ri-attachment-line verde'></i>" : `<i title='No Adjuntado' class='ri-attachment-line rojo'></i>`;
                    }
                    var bloquear = data.Anulado == 1 ? "<i title='Bloqueado' class='ri-forbid-2-line rojo'></i>" : "";

                    return `<div class="text-start">${data.Adjunto == 1 ? adjuntado : ""} ${bloquear}</div>`;
                }
            }
        ],
        fnCreatedRow: function( row, data, dataIndex ) {
            console.log(data);
            if ( data.Anulado == 1  ) {
                $(row).addClass('table-danger border'); // Agrega una clase personalizada
            }
        },
        language: {
            processing:
                `<div class="text-center p-2">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>`,
            emptyTable: "No hay efectores con los datos buscados",
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
            info: "Mostrando _START_ a _END_ de _TOTAL_ de efectores",
        },
        stateLoadCallback: function (settings, callback) {
            $.ajax({
                url: SEARCH,
                dataType: 'json',
                success: function (json) {

                    // Pasar el objeto json a callback
                    callback(json);
                }
            });
        },
        stateSaveCallback: function (settings, data) {

        },
    });

}

function cargarTablaExamenesInformador() {


    $('#listadoExamenesInformador').DataTable().clear().destroy();

    const tabla = new DataTable("#listadoExamenesInformador", {
        searching: false,
        ordering: false,
        processing: true,
        lengthChange: false,
        pageLength: 50,
        responsive: false,
        serverSide: true,
        deferRender: true,
        scrollCollapse: true,
        autoWidth: false,
        select: {
            style: 'multi'
        },
        ajax: {
            url: rutaListarExamenesInformador,
            data: function (e) {
                e.fechaDesde = $('#fechaDesdeEfector').val();
                e.fechaHasta = $('#fechaHastaEfector').val();
                e.idProfesional = ID_PROFESIONAL;
            }
        },
        dataType: 'json',
        type: 'POST',
        columnDefs: [
            {
                data: null,
                orderable: false,
                targets: 0,
                className: 'select-checkbox',
                defaultContent: '',
                render: function (data, type, row) {
                    var check = $("#checkAllInformador").is(":checked") ? "checked" : "";
                    return `<input type="checkbox" class="fila-checkbox-informador" value="${data.Id}" ${check} />`;
                }
            },
            {
                data: null,
                name: 'Paciente',
                orderable: true,
                targets: 1,
                render: function (data) {
                    return `<div class="text-start"><span>${data.IdPrestacion == null ? "" : data.IdPrestacion}</span></div>`;
                }

            },
            {
                data: null,
                name: 'Fecha',
                targets: 2,
                render: function (data) {
                    return `<div class="text-start"><span>${data.Fecha == null ? "" : data.Fecha}</span></div>`;
                }
            },
            {
                data: null,
                name: 'Examen',
                targets: 3,
                render: function (data) {
                    return `<div class="text-start"><span>${data.Examen == null ? "" : data.Examen}</span></div>`;
                }
            },{
                data: null,
                name: 'Paciente',
                targets: 4,
                render: function (data) {
                    return `<div class="text-start"><span>${data.ApPac == null ? "" : data.ApPac}, ${data.NomPac == null ? "" : data.NomPac}</span></div>`;
                }
            },{
                data: null,
                name: 'Estados',
                targets: 5,
                render: function (data) {
                    var adjuntado = "";
                    if(data.Adjunto == 1){
                        adjuntado = data.Adjuntado == 1 ? "<i title='Adjuntado' class='ri-attachment-line verde'></i>" : `<i title='No Adjuntado' class='ri-attachment-line rojo'></i>`;
                    }
                    var bloquear = data.Anulado == 1 ? "<i title='Bloqueado' class='ri-forbid-2-line rojo'></i>" : "";

                    return `<div class="text-start">${data.Adjunto == 1 ? adjuntado : ""} ${bloquear}</div>`;
                }
            }
        ],
        fnCreatedRow: function( row, data, dataIndex ) {
            console.log(data);
            if ( data.Anulado == 1  ) {
                $(row).addClass('table-danger border'); // Agrega una clase personalizada
            }
        },
        language: {
            processing:
                `<div class="text-center p-2">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>`,
            emptyTable: "No hay efectores con los datos buscados",
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
            info: "Mostrando _START_ a _END_ de _TOTAL_ de efectores",
        },
        stateLoadCallback: function (settings, callback) {
            $.ajax({
                url: SEARCH,
                dataType: 'json',
                success: function (json) {

                    // Pasar el objeto json a callback
                    callback(json);
                }
            });
        },
        stateSaveCallback: function (settings, data) {

        },
    });

}

function cargarCantidadExamenesEfector() {
    $('#listadoCantidadExamenesEfector').DataTable().clear().destroy();

    const tabla = new DataTable("#listadoCantidadExamenesEfector", {
        searching: false,
        ordering: false,
        processing: true,
        lengthChange: false,
        responsive: false,
        serverSide: true,
        deferRender: true,
        scrollCollapse: true,
        autoWidth: false,
        select: {
            style: 'multi'
        },
        ajax: {
            url: rutaCantidadExamenesEfector,
            data: function (e) {
                e.fechaDesde = $('#fechaDesdeEfector').val();
                e.fechaHasta = $('#fechaHastaEfector').val();
                e.idProfesional = ID_PROFESIONAL;
            }
        },
        dataType: 'json',
        type: 'POST',
        columnDefs: [
            {
                data: null,
                name: 'Cantidad',
                orderable: true,
                targets: 0,
                render: function (data) {
                    return `<div class="text-start"><span>${data.cantidad == null ? "" : data.cantidad}</span></div>`;
                }

            },
            {
                data: null,
                name: 'Fecha',
                targets: 1,
                render: function (data) {
                    return `<div class="text-start"><span>${data.Nombre == null ? "" : data.Nombre}</span></div>`;
                }
            }
        ],
        language: {
            processing:
                `<div class="text-center p-2">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>`,
            emptyTable: "No hay efectores con los datos buscados",
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
            info: "Mostrando _START_ a _END_ de _TOTAL_ de efectores",
        },
        stateLoadCallback: function (settings, callback) {
            $.ajax({
                url: SEARCH,
                dataType: 'json',
                success: function (json) {

                    // Pasar el objeto json a callback
                    callback(json);
                }
            });
        },
        stateSaveCallback: function (settings, data) {

        },
    });

}

function cargarCantidadExamenesInformador() {
    $('#listadoCantidadExamenesInformador').DataTable().clear().destroy();

    const tabla = new DataTable("#listadoCantidadExamenesInformador", {
        searching: false,
        ordering: false,
        processing: true,
        lengthChange: false,
        responsive: false,
        serverSide: true,
        deferRender: true,
        scrollCollapse: true,
        autoWidth: false,
        select: {
            style: 'multi'
        },
        ajax: {
            url: rutaCantidadExamenesInformador,
            data: function (e) {
                e.fechaDesde = $('#fechaDesdeEfector').val();
                e.fechaHasta = $('#fechaHastaEfector').val();
                e.idProfesional = ID_PROFESIONAL;
            }
        },
        dataType: 'json',
        type: 'POST',
        columnDefs: [
            {
                data: null,
                name: 'Cantidad',
                orderable: true,
                targets: 0,
                render: function (data) {
                    return `<div class="text-start"><span>${data.cantidad == null ? "" : data.cantidad}</span></div>`;
                }

            },
            {
                data: null,
                name: 'Fecha',
                targets: 1,
                render: function (data) {
                    return `<div class="text-start"><span>${data.Nombre == null ? "" : data.Nombre}</span></div>`;
                }
            }
        ],
        language: {
            processing:
                `<div class="text-center p-2">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>`,
            emptyTable: "No hay efectores con los datos buscados",
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
            info: "Mostrando _START_ a _END_ de _TOTAL_ de efectores",
        },
        stateLoadCallback: function (settings, callback) {
            $.ajax({
                url: SEARCH,
                dataType: 'json',
                success: function (json) {

                    // Pasar el objeto json a callback
                    callback(json);
                }
            });
        },
        stateSaveCallback: function (settings, data) {

        },
    });

}

function facturar(){
    if(!validar()){
        return false;
    }else{
        var tipo = $("#tipo").val();
        var sucursal = $("#sucursal").val();
        var nroFactura = $("#nroFactura").val();
        var idProfesional = ID_PROFESIONAL;
        var fechaDesde = $('#fechaDesdeEfector').val();
        var fechaHasta = $('#fechaHastaEfector').val();

        var idsItemsEfector = $(".fila-checkbox-efector:checked").map(function () {
            return this.value;
        }).get();

        var idsItemsInformador = $(".fila-checkbox-informador:checked").map(function () {
            return this.value;
        }).get();

        var todosItemsEfector = $("#checkAllEfector").is(":checked") ? true : false;
        var todosItemsInformador = $("#checkAllInformador").is(":checked") ? true : false;
        preloader('on');
        $.ajax({
            url: rutaFacturar,
            type: 'POST',
            data: {
                idProfesional: idProfesional,
                fechaDesde: fechaDesde,
                fechaHasta: fechaHasta,
                idsItemsEfectores: idsItemsEfector,
                idsItemsInformador: idsItemsInformador,
                todosItemsEfector: todosItemsEfector,
                todosItemsInformador: todosItemsInformador,
                TipoFactura: tipo,
                SucursalFactura: sucursal,
                NroFactura: nroFactura,
                _token: TOKEN
            },
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message, '', { timeOut: 1000 });
                    cargarTablaExamenesEfector();
                    cargarTablaExamenesInformador();
                } else {
                    toastr.warning(response.message, '', { timeOut: 1000 });
                }
                
            },
            complete: function () {
                preloader('off');
                $("#modalFacturar").modal("hide");
            },
            error: function (xhr, status, error) {
                preloader('off');
                console.log(error);
                toastr.error("Error al reactivar el item.");
            }
        });
    }
}

function validar(){
    var tipo = $("#tipo").val();
    var sucursal = $("#sucursal").val();
    var nroFactura = $("#nroFactura").val();
    var idProfesional = ID_PROFESIONAL;
    var fechaDesde = $('#fechaDesdeEfector').val();
    var fechaHasta = $('#fechaHastaEfector').val();

    var idsItemsEfector = $(".fila-checkbox-efector:checked").map(function () {
        return this.value;
    }).get();

    var idsItemsInformador = $(".fila-checkbox-informador:checked").map(function () {
        return this.value;
    }).get();

    var todosItemsEfector = $("#checkAllEfector").is(":checked") ? true : false;
    var todosItemsInformador = $("#checkAllInformador").is(":checked") ? true : false;

    if(tipo == ""){
        toastr.warning("Debe seleccionar un tipo de factura");
        return false;
    }
    if(sucursal == ""){
        toastr.warning("Debe ingresar una sucursal");
        return false;
    }
    if(nroFactura == ""){
        toastr.warning("Debe ingresar un número de factura");
        return false;
    }

    console.log(idsItemsEfector.length);
    console.log(idsItemsInformador.length);
    console.log(todosItemsEfector);
    console.log(todosItemsInformador);

    if(idsItemsEfector.length == 0 && idsItemsInformador.length == 0 && !todosItemsEfector && !todosItemsInformador){
        toastr.warning("Debe seleccionar al menos un examen para facturar");
        return false;
    }

    return true;
}
