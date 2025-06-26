let clientes = [];
let clientesRenderizar = [];

let tabla = new DataTable("#listaEmpresas",{
    searching: false,
    lengthChange: false,
});
const params = new URLSearchParams(window.location.search);

$(function () {
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

})

function cargarCliente(idCliente) {
    $.ajax({
        url: getCliente,
        method: 'GET',
        async: false,
        data: { id: idCliente }
    })
        .done(function (result) {
            let enExamenes = clientes.find(x => x.Id == result.Id);

            if (!enExamenes) {
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

tabla.on('click', 'button.remove-item-btn', function () {
    let id = $(this).data().id;
    index = clientes.indexOf(clientes.find(x => x.Id == id));
    clientes.splice(index, 1);
    if (index) {
        clientesEliminar.push(id);
    } else {
        clientesNuevos.splice(index, 1);
    }
    tabla
        .row($(this).parents('tr'))
        .remove()
        .draw();
});

$('.agregarCliente').on('click', function (e) {
    e.preventDefault();
    let id = $("#empresaSelect2").val();
    if(!id) {
        toastr.warning("Debe seleccionar una empresa", "", { timeout: 1000 });
        return;
    }
    cargarCliente(id);
    clientes = clientes.concat(clientesRenderizar);
    renderizarClientes(clientesRenderizar);
    clientesRenderizar = [];
});

 $('#btnRegistrar').on('click', function (e) {
        e.preventDefault();
        preloader('on');
        let nombre = $("#nombregrupo").val();

        if (validaciones()) {
            $.post(postGrupoCliente, {
                _token: TOKEN,
                Nombre: nombre,
                Empresas: clientes
            })
                .done(function () {
                    preloader('off');
                    toastr.success('Se ha cargado el grupo correctamente', '', { timeOut: 1000 });
                })
                .fail(function (jqXHR) {
                    preloader('off');
                    let errorData = JSON.parse(jqXHR.responseText);
                    console.log(errorData);
                    checkError(jqXHR.status, errorData.error || errorData.msg);
                    return;
                });

        }
    });

function validaciones() {
    let mensaje = "";
    if (!$("#nombregrupo").val()) {
        mensaje += "Debe ingresar un nombre para el grupo.\n";
    }

    if (clientes.length === 0) {
        mensaje += "Debe agregar al menos un cliente al grupo.\n";
    }

    if (mensaje) {
        preloader('off');
        toastr.warning(mensaje, '', { timeOut: 3000 });
        return false;
    }
    return true;
}
