$(function(){

    $(document).on('click', '#buscarResumenes', function() {

         let fechaDesde = $('#fechaDesdeResumenes').val(),
             fechaHasta = $('#fechaHastaResumenes').val(),
             especialidad = $('#especialidadResumenes').val(),
             estado = $('#estadoResumenes').val(),
             efector = $('#efectorResumenes').val(),
             profesional = $('#profEfectorResumenes');

        if (!fechaDesde || !fechaHasta) {
            toastr.warning("Las fechas son obligatorias",'',{timeOut: 1000});
            return;
        }

        $('#listaOrdenesResumenes').DataTable().clear().destroy();

        new DataTable("#listaOrdenesResumenes", {

            searching: false,
            ordering: false,
            processing: true,
            lengthChange: false,
            pageLength: 50,
            deferRender: true,
            responsive: false,
            serverSide: true,
            ajax: {
                url: SEARCHRESUMEN,
                data: function(d){
                    d.fechaDesde = fechaDesde;
                    d.fechaHasta = fechaHasta;
                    d.especialidad = especialidad;
                    d.estado = estado;
                    d.efector = efector;
                    d.profesional = profesional;
                },
            },
            dataType: 'json',
            type: 'POST',
            columns: [
                {//1
                    data: 'avance',
                    name: 'avance'
                },
                {//2
                    data: 'especialidad',
                    name: 'especialidad'
                },
                {//3
                    data: 'fecha',
                    name: 'fecha'
                },
                {//4
                    data: 'prestacion',
                    name: 'prestacion'

                },
                {//5
                    data: 'empresa',
                    name: 'empresa'
                },
                {//6
                    data: 'nombreCompleto',
                    name: 'nombreCompleto'
                },
                {//7
                    data: 'dni',
                    name: 'dni'
                },
                {//8
                    data: 'efector',
                    name: 'efector'
                },
                {//9
                    data: 'estado',
                    name: 'estado'
                },
                {//10
                    data: 'archivos',
                    name: 'archivos'
                },
                {//11
                    data: null,
                    render: function(data){
                        return '';
                    }
                }

            ],
            language: {
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
                info: "Mostrando _START_ a _END_ de _TOTAL_ de examenes",
            }
        });

    });

   

});