$(document).ready(()=>{

    //Botón de busqueda de Mapas
    $(document).on('click', '#buscarAsignados', function() {

        $('#Liberar, #Cerrar, #Abrir').hide();

        let fechaDesde = $('#fechaDesdeAsignados').val(),
            fechaHasta = $('#fechaHastaAsignados').val(),
            estado = $('#estadoAsignados').val(),
            nroPrestacion = $('#prestacionAsignados').val();

            if ((fechaDesde === '' || fechaHasta === '') && nroPrestacion === '') {
                toastr.warning("Las fechas son obligatorias");
                return;
            }

        if ((estado == '' || estado == 0) && nroPrestacion === '') {
            toastr.warning("Debe seleccionar un estado para continuar", "Atención");
            return;
        }

        $(document).on('change', '#estadoAsignados', function(){
            var nuevoValor = $(this).val();
            
            if (nuevoValor !== estado) {

                estado = nuevoValor;

            }
        });


        estado == 'abiertos' 
            ? $('#Cerrar').show()
            : estado == 'cerrados'
                ? $('#Abrir').show()
                : estado == 'asignados'
                    ? $('#Liberar').show()
                    : '';

        $('#listaOrdenesEfectoresAsig').DataTable().clear().destroy();

        new DataTable("#listaOrdenesEfectoresAsig", {

            searching: false,
            ordering: false,
            processing: true,
            lengthChange: false,
            pageLength: 50,
            deferRender: true,
            responsive: true,
            serverSide: true,
            ajax: {
                url: SEARCHASIG,
                data: function(d){
                    d.fechaDesde = $('#fechaDesdeAsignados').val();
                    d.fechaHasta = $('#fechaHastaAsignados').val();
                    d.especialidad = $('#especialidadAsignados').val();
                    d.prestacion = $('#prestacionAsignados').val();
                    d.empresa = $('#empresaAsignados').val();
                    d.paciente = $('#pacienteAsignados').val();
                    d.examen = $('#examenAsignados').val();
                    d.estados = $('#estadoAsignados').val();
                    d.dni = $('#dniAsignados').val();
                    d.efectores = $('#efectorAsignado').val();
                }
            },
            dataType: 'json',
            type: 'POST',
            columns: [
                {
                    data: null,
                    render: function(data){
                        return fechaNow(data.Fecha,'/',0);
                    }
                },
                {
                    data: 'Especialidad',
                    name: 'Especialidad',
                },
                {
                    data: 'IdPrestacion',
                    name: 'IdPrestacion',
                },
                {
                    data: null,
                    render: function(data) {

                        let recorte = (data.Empresa).substring(0,20) + "...";
                        return recorte.length >= 20 ? `<span title="${data.Empresa}">${recorte}</span>` : data.Empresa;
                    }
                },
                {
                    data: null,
                    render: function(data){
                        let NombreCompleto = data.pacNombre + ' ' + data.pacApellido;
                        let recorte = (NombreCompleto).substring(0,15) + "...";
                        return recorte.length >= 15 ? `<span title="${NombreCompleto}">${recorte}</span>` : NombreCompleto;
                    }
                },
                {
                    data: 'Documento',
                    name: 'Documento',
                },
                {
                    data: null,
                    render: function(data) {
                        let recorte = (data.Examen).substring(0,20) + "...";
                        return recorte.length >= 20 ? `<span title="${data.Examen}">${recorte}</span>` : data.Examen;
                    }
                },
                {
                    data: null,
                    render: function(data) {

                        let estados = {
                            cerrados: "Cerrado",
                            abiertos: "Abierto",
                            asignados: "Asignado"
                        };

                        let estadoCheck = (estados[estado] === undefined)
                            ? data.Estado == 2 
                                ? 'Abierto'
                                : data.Estado == 5
                                    ? 'Cerrado'
                                    : data.IdProfesional != 0
                                        ? 'Asignado'
                                        : ''
                            : estados[estado];
                        
                        return `<span class="custom-badge pequeno">${estadoCheck}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data) {
                        
                        let abierto = [0, 1, 2], cerrado = [3, 4, 5], estatus = data.Estado;

                        return  estado == 0 || estado == ''
                            ? `<input type ="checkbox" disabled>`
                            : abierto.includes(estatus) &&  estado == 'abiertos'
                                ? `<input type="checkbox" name="Id_asignado" value="${data.IdItem}" checked>`
                                : cerrado.includes(estatus) &&  estado == 'cerrados'
                                    ? `<input type="checkbox" name="Id_asignado" value="${data.IdItem}" checked>`
                                    : data.IdProfesional != 0 && estado == 'asignados' && abierto.includes(estatus)
                                        ? `<input type="checkbox" name="Id_asignado" value="${data.IdItem}" checked>`
                                        : `<input type ="checkbox" disabled>`;      
                    }
                },
            ],
            language: {
                processing: "<div style='text-align: center; margin-top: 20px;'><img src='./images/spinner.gif' /><p>Cargando...</p></div>",
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

        function fechaNow(fechaAformatear, divider, format) {
            let dia, mes, anio; 
        
            if (fechaAformatear === null) {
                let fechaHoy = new Date();
        
                dia = fechaHoy.getDate().toString().padStart(2, '0');
                mes = (fechaHoy.getMonth() + 1).toString().padStart(2, '0');
                anio = fechaHoy.getFullYear();
            } else {
                let nuevaFecha = fechaAformatear.split("-"); 
                dia = nuevaFecha[0]; 
                mes = nuevaFecha[1]; 
                anio = nuevaFecha[2];
            }
        
            return (format === '0') ? `${dia}${divider}${mes}${divider}${anio}` : `${anio}${divider}${mes}${divider}${dia}`;
        }

    });
});