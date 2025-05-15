$(function(){

    $(document).on('click', '#buscar', function(e){
        e.preventDefault();

        let profesional = $('#profesional').val(),
            fechaDesde = $('#fechaDesde').val(),
            fechaHasta = $('#fechaHasta').val(),
            prestacion = $('#prestacion').val(),
            estado = $('#estado').val(),
            table = $('#listaLlamadaEfector');

        //La prestacion es individual y no acepta otros filtros
        if(prestacion === ''){

            if([0,'',null].includes(profesional)){ 
                toastr.warning('El campo profesional no puede estar vacío', '', {timeOut: 1000});
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
                        
                        let llamar = `<button type="button" data-id="${data.prestacion}" data-profesional=${data.idProfesional} class="btn btn-sm botonGeneral llamarExamen"><i class="ri-edit-line" ></i>Llamar</button>`;
                        
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
            createdRow: async function(row, data, dataIndex) {

                let response = await $.get(checkLlamado, { id: data.prestacion });

                if (response && Object.keys(response).length !== 0) {
                    $(row).css('color', 'red');

                    $('.llamarExamen', row)
                        .removeClass('llamarExamen')
                        .addClass('liberarExamen')
                        .html('<i class="ri-edit-line"></i> Liberar');
                }

                $('button[data-id]').each(function () {
                    let boton = $(this), botonId = boton.data('id');

                    if (botonId == response.prestacion_id) {
                        let fila = boton.closest('tr, div.row, div.fila');

                        if (parseInt(USERACTIVO) !== parseInt(response.profesional_id)) {
                            $('.llamarExamen, .liberarExamen, .atenderPaciente', fila).hide();
                        }
                    }
                });

                $(row).attr('data-id', data.prestacion);
            }
        });
    })

});