$(function(){

    const ADMIN = [
        'Administrador', 
        'Admin SR', 
        'Recepcion SR'
    ];

    $(document).on('click', '#buscar', function(e){
        e.preventDefault();

        let profesional = $('#profesional').val(),
            fechaDesde = $('#fechaDesde').val(),
            fechaHasta = $('#fechaHasta').val(),
            prestacion = $('#prestacion').val(),
            estado = $('#estado').val(),
            especialidad = $('#especialidad').data('id'),
            especialidadSelect = $('#especialidadSelect').val(),
            table = $('#listaLlamadaEfector');

        //La prestacion es individual y no acepta otros filtros
        if(prestacion === ''){

            if(!profesional){ 
                toastr.warning('El campo profesional no puede estar vacío');
                return;
            }
    
            if(fechaDesde == '' || fechaHasta == ''){
                swal('Alerta','La fecha "Desde" y "Hasta" son obligatorias.', 'warning');
                return;
            }

        }
        
        table.DataTable().clear().destroy();

        new DataTable(table, {

            searching: false,
            ordering: false,
            processing: true,
            lengthChange: false,
            pageLength: 10,
            deferRender: true,
            responsive: false,
            serverSide: true,
            ajax: {
                url: SEARCH,
                data: function(d){
                    d.profesional = profesional;
                    d.fechaDesde = fechaDesde;
                    d.fechaHasta = fechaHasta;
                    d.prestacion = prestacion;
                    d.especialidad = especialidad || especialidadSelect;
                    d.estado = estado;
                }
            },
            dataType: 'json',
            type: 'POST',
            columns: [
                {
                    data: 'fecha',
                    name: 'fecha',
                    width: '50px'
                },
                {
                    data: null,
                    width: '50px',
                    render: function(data){
                        return data.prestacion;
                    }
                },
                {
                    data: 'empresa',
                    name: 'empresa',
                    width: '100px'
                },
                {
                    data: 'paraEmpresa',
                    name: 'paraEmpresa',
                    width: '100px'
                },
                {
                    data: 'art',
                    name: 'art',
                    width: '100px'
                },
                {
                    data: null,
                    width: '120px',
                    render: function(data) {
                        return `<span class="text-uppercase">${data.paciente} <span class="custom-badge generalNegro"></span></span>`;
                    }
                },
                {
                    data: 'dni',
                    name: 'dni'
                },
                {
                    data: null,
                    render: function(data){
                        return data.tipo;
                    }
                },
                {
                    data:null,
                    render: function(data){
                        return calcularEdad(data.fechaNacimiento);
                    }
                },
                {
                    data: null,
                    render: function(data){
                        return [0, null, '', undefined].includes(data.telefono) ? '' : data.telefono;
                    }
                },
                {
                    data: null,
                    render: function(data) {
                         return data.Cerrado === 1 ? `<span class="custom-badge generalNegro">Cerrado</span>` : '';
                    }
                },
                {
                    data: null,
                    width:'100px',
                    render: function(data){
                        
                        let llamar = `<button type="button" data-id="${data.prestacion}" data-profesional=${data.idProfesional} data-tipo="EFECTOR"class="btn btn-sm botonGeneral llamarExamen"><i class="ri-edit-line" ></i>Llamar</button>`;
                        
                        let atender = `<button type="button" data-id="${data.prestacion}" data-profesional=${data.idProfesional}" data-especialidades="${data.especialidades}" class="btn btn-sm botonGeneral atenderPaciente" data-bs-toggle="modal" data-bs-target="#atenderEfector"><i class="ri-edit-line"></i>Atender</button>`;
    
                        return llamar + ' ' + atender;
                    }
                }
            ],
            language: {
                processing: "<div style='text-align: center; margin-top: 20px;'><img src='../images/spinner.gif' /><p>Cargando...</p></div>",
                emptyTable: "No hay prestaciones con los datos buscados",
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
                info: "Mostrando _START_ a _END_ de _TOTAL_ de prestaciones",
            },
            // rowCallback: function(row, data, dataIndex) {
                
            // },
            createdRow: async function(row, data, dataIndex) {
                $('.atenderPaciente').hide();

                let response = await $.get(checkLlamado, { id: data.prestacion, tipo: 'EFECTOR' });

                preloader('on');

                if (await response && Object.keys(response).length !== 0) {
                    $(row).css('color', 'red');

                    $('.llamarExamen', row)
                        .removeClass('llamarExamen')
                        .addClass('liberarExamen')
                        .html('<i class="ri-edit-line"></i> Liberar');

                        $('.atenderPaciente', row).show();
                }else{
                     $(row).find('td').css('color', 'green');
                     $('.atenderPaciente', row).hide();
                }

                let lstRoles = await $.get(getRoles),
                    roles = lstRoles.map(rol => rol.nombre),
                    tienePermiso = ADMIN.some(rol => roles.includes(rol));

                $('button[data-id]').each(async function () {
                    let boton = $(this), botonId = boton.data('id');

                    if (botonId == response.prestacion_id) {
                        let fila = boton.closest('tr, div.row, div.fila'),
                            botones = $('.llamarExamen, .liberarExamen, .atenderPaciente', fila);

                        if (!fila.find('.mensaje-ocupado').length && parseInt(USERACTIVO) !== parseInt(response.profesional_id)) {
                            botones.hide();
                            
                            if(tienePermiso) {
                                botones.last().after(`<span id="clickCierreForzado" title="Liberar atencion" class="cerrar-atencion" data-profesional="${response.profesional_id}" data-prestacion="${data.prestacion}"><i class="ri-logout-box-line"></i></span>`);
                                botones.last().after('<span id="clickAtencion" title="Visualizar actividad" class="vista-admin px-2"><i class="ri-search-eye-line"></span>');
                            }
                            
                            botones.last().after('<span class="mensaje-ocupado rojo text-center fs-bolder">Ocupado</span>');
                            fila.find('td').css('color', 'red')
                        }
                    }
                });

                preloader('off');

                $(row).attr('data-id', data.prestacion);
            }
        });
    })

});