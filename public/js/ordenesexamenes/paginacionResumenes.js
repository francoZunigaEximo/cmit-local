$(function(){

    $(document).on('click', '#buscarResumenes, #treintaDiasResumenes, #noventaDiasResumenes, #sesentaDiasResumenes', function() {

        const diasBuscar = {
            'sesentaDiasResumenes': 60,
            'noventaDiasResumenes': 90,
            'treintaDiasResumenes': 30
        };

         let fecha = new Date(),
            fechaDesde = null,
            fechaHasta = $('#fechaHastaResumenes').val(),
            especialidad = $('#especialidadResumenes').val(),
            estado = $('#estadoResumenes').val(),
            efector = $('#efectorResumenes').val(),
            profesional = $('#profEfectorResumenes').val(),
            restar = this.id;


        if(Object.hasOwn(diasBuscar, restar)) {

            fecha.setDate(fecha.getDate() - diasBuscar[restar]);
            fechaDesde = `${fecha.getFullYear()}-${fecha.getMonth() + 1}-${fecha.getDate()}`;

        }else{
            fechaDesde = $('#fechaDesdeResumenes').val();
        }

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
            scrollCollapse: true,
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
                }
            },
            dataType: 'json',
            type: 'POST',
            columns: [
                {//1
                    data: null,
                    render: function(data) {
                        return `<div id="listado" data-id="${data.prestacion}">${data.avance}%</div>`;
                    }
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
                        return `<div class="text-center"><a href="${linkPrestaciones}/${data.prestacion}/edit" target="_blank"><i class="ri-edit-line"></i></a></div>`;
                    }
                }

            ],
            language: {
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
            }
        });

    });

   

});