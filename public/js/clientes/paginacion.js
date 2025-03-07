$(function(){

    $(document).on('click', '#buscarBtn', function(e) {
            e.preventDefault();

            let buscar = $('#buscar').val(),
                formaPago = $('#FPago').val(),
                filtro = $('#filtro').val(),
                tipoCliente = $('#TipoCliente').val();


            $('#listaClientes').DataTable().clear().destroy();

            new DataTable("#listaClientes", {

                searching: false,
                fixedColumns: {
                    heightMatch: 'none'
                },
                ordering: false,
                processing: true,
                lengthChange: false,
                pageLength: 50,
                responsive: true,
                serverSide: true,
                deferRender: true,
                dataType: 'json',
                type: 'POST',
                ajax: {
                    url: SEARCH,
                    data: function(d){
                        d.tipo = tipoCliente;
                        //d.filtro = $('#filtro').select2('data').map(option => option.id);
                        d.filtro = filtro;
                        d.formaPago = formaPago;
                        d.buscar = buscar;
                    }
                },    
                columns: [
                    {
                        data: null,
                        render: function(data){
                            return `<div class="text-center"><input type="checkbox" name="Id" value="${data.Id}" checked></div>`;
                        }
                    },
                    {
                        data: null,
                        render: function(data){
                            return `<span title="${data.RazonSocial}">${acortadorTexto((data.RazonSocial).toUpperCase(), 20)}</span>`;
                        }
                    },
                    {
                        data: null,
                        render: function(data){
                            return `<span title="${data.ParaEmpresa}">${acortadorTexto((data.ParaEmpresa).toUpperCase(), 20)}</span>`;
                        }
                    },
                    {
                        data: 'Identificacion',
                        name: 'Identificacion',
                    },
                    {
                        data: null,
                        render: function(data){
                            return data.TipoCliente === 'E' ? 'Empresa': 'ART';
                        }
                    },
                    {
                        data: null,
                        render: function(data){
                            return `<span class="badge badge-soft-${ (data.Bloqueado === 0)?'success':'danger' } text-uppercase">${(data.Bloqueado === 0)? 'Habilitado':'Bloqueado' }</span>`;
                        }
                    },
                    {
                        data: null,
                        render: function(data){
                            return ['', null, undefined, 'A'].includes(data.FPago) ? 'CC' : (data.FPago == 'B')? 'Ctdo': 'Ctdo(CC Bloq)';
                        }
                    },
                    {
                        data: null,
                        render: function(data){
                            let editar = `<a href="${location.href}/${data.Id}/edit/"><button type="button" class="btn btn-sm iconGeneral edit-item-btn"><i class="ri-edit-line"></i></button></a>`,

                                eliminar = `<button data-id="${data.Id}" type="button" class="btn btn-sm iconGeneral downCliente"><i class="ri-delete-bin-2-line"></i></button></a>`;
                        
                            return editar + ' ' + eliminar;
                        },
                    }  
                ],
                language: {
                    processing: "<div style='text-align: center; margin-top: 20px;'><img src='./images/spinner.gif' /><p>Cargando...</p></div>",
                    emptyTable: "No hay clientes con los datos buscados",
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
                    info: "Mostrando _START_ a _END_ de _TOTAL_ de clientes",
                }
            });

    });
});