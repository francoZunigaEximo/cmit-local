$(function(){

    $(document).on('click', '#buscar', function(e){
        e.preventDefault();

        let profesional = $('#profesional').val(),
            fechaDesde = $('#fechaDesde').val(),
            fechaHasta = $('#fechaHasta').val();

        if([0,'',null].includes(profesional)) {
            toastr.warning('El campo profesional no puede estar vacío');
            return;
        }

        if(fechaDesde == '' || fechaHasta == ''){
            swal('Alerta','La fecha "Desde" y "Hasta" son obligatorias.', 'warning');
            return;
        }
       
        let table = $('#listaLlamadaEfector');

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
                    d.prestacion = $('#Estado').val();
                    d.estado = $('#estado').val();
                }
            },
            dataType: 'json',
            type: 'POST',
            columns: [
                {
                    data: null,
                    render: function(data){
                        return fechaNow(data.fecha, '/', 1);
                    }
                },
                {
                    data: 'prestacion',
                    name: 'prestacion',
                    width: '50px'
                },
                {
                    data: 'empresa',
                    name: 'empresa'
                },
                {
                    data: 'paraEmpresa',
                    name: 'paraEmpresa'
                },
                {
                    data: 'art',
                    name: 'art'
                },
                {
                    data: 'paciente',
                    name: 'paciente'
                },
                {
                    data: 'dni',
                    name: 'dni'
                },
                {
                    data:null,
                    render: function(data){
                        return calcularEdad(data.fechaNacimiento);
                    }
                },
                {
                    data: 'tipo',
                    name: 'tipo'
                },
                {
                    data: 'telefono',
                    name: 'telefono'
                },
                {
                    data: null,
                    render: function(data) {
                        return 0;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        
                        let llamar = '<button type="button" class="btn btn-sm botonGeneral"><i class="ri-edit-line"></i>Llamar</button>';
                        
                        let atender = '<button type="button" class="btn btn-sm botonGeneral"><i class="ri-edit-line"></i>Atender</button>';
    
                        return llamar + ' ' + atender;
                    }
                }
            ],
            language: {
                processing: "<div style='text-align: center; margin-top: 20px;'><img src='../images/spinner.gif' /><p>Cargando...</p></div>",
                emptyTable: "No hay mapas con los datos buscados",
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
                info: "Mostrando _START_ a _END_ de _TOTAL_ de mapas",
            },
        });
    })

});