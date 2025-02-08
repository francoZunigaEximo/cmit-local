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
                        return `<span title="ver prestación" class="verPrestacion azul text-decoration-underline fw-bolder cursor-pointer" data-prestacion="${data.prestacion}">${data.prestacion}</span>`;
                    }
                },
                {
                    data: 'empresa',
                    name: 'empresa',
                    width: '120px'
                },
                {
                    data: 'paraEmpresa',
                    name: 'paraEmpresa',
                    width: '120px'
                },
                {
                    data: 'art',
                    name: 'art',
                    width: '120px'
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
                         return `<span title="${data.CAdj}" class="custom-badge generalNegro">${[0,1,2].includes(data.CAdj) ? 'Abierto' : 'Cerrado'}</span>`;
                    }
                },
                {
                    data: null,
                    width:'100px',
                    render: function(data){
                        
                        let llamar = '<button type="button" class="btn btn-sm botonGeneral"><i class="ri-edit-line"></i>Llamar</button>';
                        
                        let atender = '<button type="button" class="btn btn-sm botonGeneral"><i class="ri-edit-line"></i>Atender</button>';
    
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
        });
    })

});