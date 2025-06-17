let clientes = [];
let clientesNuevos = [];
let clientesRenderizar = [];
let clientesEliminar = [];

let tabla = new DataTable("#listaEmpresas", {
    searching: false,
    lengthChange: false,
});

$(document).ready(() => {

    let id = $("#idGrupo").val();
    preloader('on');

    $.ajax({
        url: getEmpresasGrupoCliente,
        type: 'POST',
        data: {
            _token: TOKEN,
            Id: id,
        },

        success: function (response) {
            let data = response;

            data.forEach(empresa => {
                cargarCliente(empresa.IdCliente);
            });
            clientes = response;
            renderizarClientes(clientesRenderizar);
            clientesRenderizar = [];

            preloader('off');
        },
        error: function (jqXHR) {
            preloader('off');
            let errorData = JSON.parse(jqXHR.responseText);
            checkError(jqXHR.status, errorData.msg);
            return;
        }
    });

    $('#empresaSelect2').select2({
        language: {
            noResults: function () {

                return "No hay empresas con esos datos";
            },
            searching: function () {

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
            data: function (params) {
                return {
                    buscar: params.term,
                    tipo: 'E'
                };
            },
            processResults: function (data) {
                return {
                    results: data.clientes
                };
            },
            cache: true
        },
        minimumInputLength: 2
    });
});

function cargarCliente(idCliente) {
    $.ajax({
        url: getCliente,
        method: 'GET',
        async: false,
        data: { id: idCliente }
    })
        .done(function (result) {
            let enClientes = clientes.find(x => x.Id == result.Id);
            let enClientesNuevos = clientesNuevos.find(x => x.Id == result.Id);

            if (!enClientesNuevos && !enClientes) {
                clientesRenderizar.push(result);
            } else {
                toastr.warning("El cliente ya se encuentra cargado", "", { timeout: 1000 });
            }
        })
        .fail(function (jqXHR) {
            preloader('off');
            let errorData = JSON.parse(jqXHR.responseText);
            checkError(jqXHR.status, errorData.msg);
            return;
        });
}

function renderizarClientes(clientesRenderizar) {
    preloader('on');
    clientesRenderizar.forEach(x => {
        tabla.row.add([
            x.Id,
            x.RazonSocial,
            x.ParaEmpresa,
            x.Identificacion,
            "<button class=\"btn btn-sm iconGeneral remove-item-btn\" type=\"button\" data-id=\"" + x.Id + "\" ><i class=\"ri-delete-bin-2-line\"></i></button>"
        ]).draw();
    });
    preloader('off');
}

$('.agregarCliente').on('click', function (e) {
    e.preventDefault();
    let id = $("#empresaSelect2").val();

    cargarCliente(id);
    clientesNuevos = clientesNuevos.concat(clientesRenderizar);
    renderizarClientes(clientesRenderizar);
    clientesRenderizar = [];
});

tabla.on('click', 'button.remove-item-btn', function () {
    let id = $(this).data().id;
    index = clientes.indexOf(clientes.find(x => x.IdCliente == id));
    if (index != -1) {
        clientes.slice(index, 1);
        clientesEliminar.push(id);
    } else {
        index = clientesNuevos.indexOf(clientesNuevos.find(x => x.Id == id));
        clientesNuevos.splice(index, 1);
    }

    tabla
        .row($(this).parents('tr'))
        .remove()
        .draw();
});

$("#btnRegistrar").on('click', function (e) {
    e.preventDefault();
    preloader('on');
    let id = $("#idGrupo").val();
    let nombre = $("#nombregrupo").val();


    if (nombre) {
        $.post(postEditGrupoCliente, {
            _token: TOKEN,
            Id: id,
            Nombre: nombre,
            ClientesNuevos: clientesNuevos,
            ClientesEliminar: clientesEliminar
        })
            .done(function () {
                preloader('off');
                toastr.success('Se ha cargado el paquete correctamente', '', { timeOut: 1000 });

            })
            .fail(function (jqXHR) {
                let errorData = JSON.parse(jqXHR.responseText);
                checkError(jqXHR.status, errorData.msg);
                return;
            });

    } else {
        preloader('off');
        toastr.warning("Tiene que ingresar nombre, descricpion y seleccionar al menos un examen", '', { timeOut: 1000 });
    }
});