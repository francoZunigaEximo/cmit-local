$(document).ready(()=>{

    $('#buscar, #FPago, #TipoCliente, #filtro').on('change', function() {

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
                dataType: 'json',
                type: 'POST',
                ajax: {
                    url: SEARCH,
                    data: function(d){
                        d.tipo = $('#TipoCliente').val();
                        //d.filtro = $('#filtro').select2('data').map(option => option.id);
                        d.filtro = $('#filtro').val();
                        d.formaPago = $('#FPago').val();
                        d.buscar = $('#buscar').val();
                    }
                },    
                columns: [
                    {
                        data: null,
                        render: function(data){
                            return `<input type="checkbox" name="Id" value="${data.Id}" checked>`;
                        }
                    },
                    {
                        data: null,
                        render: function(data){
                            let razonSocial = data.RazonSocial;
                            return (razonSocial.length > 15) ? razonSocial.slice(0, 15) + "..." : razonSocial;
                        }
                    },
                    {
                        data: null,
                        render: function(data){
                            let paraEmpresa = data.ParaEmpresa;
                            return (paraEmpresa.length > 15) ? paraEmpresa.slice(0,15) + "..." : paraEmpresa;
                        }
                    },
                    {
                        data: 'Identificacion',
                        name: 'Identificacion',
                    },
                    {
                        data: null,
                        render: function(data){
                            let tipo = (data.TipoCliente === 'E')? 'Empresa': 'ART';
                            
                            return tipo;
                        }
                    },
                    {
                        data: null,
                        render: function(data){
                            let contenido = `<span class="badge badge-soft-${ (data.Bloqueado === 0)?'success':'danger' } text-uppercase">${(data.Bloqueado === 0)? 'Habilitado':'Bloqueado' }</span>`;
                            
                            return contenido;
                        }
                    },
                    {
                        data: null,
                        render: function(data){
                            let contenido = (data.FPago == 'A')? 'CC': (data.FPago == 'B')? 'Ctdo': 'Ctdo(CC Bloq)';
        
                            return contenido;
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
                    processing: "Cargando listado de clientes de CMIT",
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