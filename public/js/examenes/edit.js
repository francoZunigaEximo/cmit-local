$(document).ready(()=>{

    quitarDuplicados('#Estudio');
    quitarDuplicados('#Reporte');
    quitarDuplicados('#ProvEfector');
    quitarDuplicados('#ProvInformador');

    $(document).on('click', '.multiVolver', function(e) {
        window.history.back();
    });

    $(document).on('click', '#guardar', function(){

        let Examen = $('#Examen').val(),
            Estudio =$('#Estudio').val(),
            Descripcion = $('#Descripcion').val(),
            Reporte = $('#Reporte').val(),
            CodigoEx = $('#CodigoEx').val(),
            Formulario = $('#Formulario').val(),
            CodigoE = $('#CodigoE').val(),
            DiasVencimiento = $('#DiasVencimiento').val(),
            Inactivo = $('#Inactivo').prop('checked'),
            priImpresion = $('#priImpresion').prop('checked'),
            ProvEfector = $('#ProvEfector').val(),
            ProvInformador = $('#ProvInformador').val(),
            Informe = $('#Informe').prop('checked'),
            Fisico = $('#Fisico').prop('checked'),
            Adjunto = $('#Adjunto').prop('checked'),
            Ausente = $('#Ausente').prop('checked'),
            Devolucion = $('#Devolucion').prop('checked'),
            EvalExclusivo = $('#EvalExclusivo').prop('checked'),
            ExpAnexo = $('#ExpAnexo').prop('checked');

            if($('#form-update').valid()) {

                swal({
                    title: "¿Esta seguro que desea guardar",
                    icon: "warning",
                    buttons: ["Cancelar", "Aceptar"]
                }).then((confirmar) => {
                    if(confirmar) {

                        $.post(updateExamen, {_token: TOKEN, Id: ID, Examen:Examen, IdEstudio: Estudio, Descripcion: Descripcion, IdReporte: Reporte, Cod: CodigoEx, Cod2: CodigoE, IdForm: Formulario, DiasVencimiento: DiasVencimiento, Inactivo: Inactivo, PI: priImpresion, IdProveedor: ProvEfector, IdProveedor2: ProvInformador, Informe: Informe, NoImprime: Fisico, Adjunto: Adjunto, Ausente: Ausente, Devol: Devolucion, Evaluador: EvalExclusivo, EvalCopia: ExpAnexo})
                        .done(function(){
                            toastr.success('Se ha actualizado el exámen correctamente');
                            setTimeout(() => {
                                location.reload();
                            }, 3000);
                            
                        })
                        .fail(function(jqXHR){
                            preloader('off');
                            let errorData = JSON.parse(jqXHR.responseText);            
                            checkError(jqXHR.status, errorData.msg);
                            return; 
                        });
                    }
                });
            }
   
    });

    $(document).on('click', '#clonar', function(){
        
        let Examen = $('#Examen').val(),
            Estudio =$('#Estudio').val(),
            Descripcion = $('#Descripcion').val(),
            Reporte = $('#Reporte').val(),
            Formulario = $('#Formulario').val(),
            CodigoE = $('#CodigoE').val(),
            DiasVencimiento = $('#DiasVencimiento').val(),
            Inactivo = $('#Inactivo').prop('checked'),
            priImpresion = $('#priImpresion').prop('checked'),
            ProvEfector = $('#ProvEfector').val(),
            ProvInformador = $('#ProvInformador').val(),
            Informe = $('#Informe').prop('checked'),
            Fisico = $('#Fisico').prop('checked'),
            Adjunto = $('#Adjunto').prop('checked'),
            Ausente = $('#Ausente').prop('checked'),
            Devolucion = $('#Devolucion').prop('checked'),
            EvalExclusivo = $('#EvalExclusivo').prop('checked'),
            ExpAnexo = $('#ExpAnexo').prop('checked');

        swal({
            title: "¿Esta seguro que desea clonar la infomación?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                localStorage.setItem('clon_Examen', Examen);
                localStorage.setItem('clon_Estudio', Estudio);
                localStorage.setItem('clon_Descripcion', Descripcion);
                localStorage.setItem('clon_Reporte', Reporte);
                localStorage.setItem('clon_Formulario', Formulario);
                localStorage.setItem('clon_CodigoE', CodigoE);
                localStorage.setItem('clon_Diasvencimiento', DiasVencimiento);
                localStorage.setItem('clon_Inactivo', Inactivo);
                localStorage.setItem('clon_priImpresion', priImpresion);
                localStorage.setItem('clon_ProvEfector', ProvEfector);
                localStorage.setItem('clon_ProvInformador', ProvInformador)
                localStorage.setItem('clon_Informe', Informe)
                localStorage.setItem('clon_Fisico', Fisico)
                localStorage.setItem('clon_Adjunto', Adjunto)
                localStorage.setItem('clon_Ausente', Ausente)
                localStorage.setItem('clon:Devolucion', Devolucion)
                localStorage.setItem('clon_EvalExclusivo', EvalExclusivo)
                localStorage.setItem('clon_ExpAnexo', ExpAnexo)

                window.location.href = GOCREATE;
            }
        })
    });

    $(document).on('click', '#eliminar', function(e){
        e.preventDefault();

        swal({
            title: "¿Está seguro que desea eliminar el examen?",
            icon: "warning",
            buttons: ["Cancelar", "Aceptar"]
        }).then((confirmar) => {
            if(confirmar) {
                $.post(deleteExamen, {Id: ID, _token: TOKEN})
                .done(function(estatus){

                    if (estatus.estatus === true) {
                        toastr.warning('No se puede eliminar el exámen porque esta siendo utilizada por una prestación');
                        return;
            
                    } else if(estatus.estatus === false){

                        toastr.success('Se ha eliminado correctamente el exámen. Se redireccionará a la pantalla de creación de examenes');
                        setTimeout(()=>{
                            location.href = GOINDEX;
                        }, 3000);
                    }
                })
                .fail(function(jqXHR){
                    preloader('off');
                    let errorData = JSON.parse(jqXHR.responseText);            
                    checkError(jqXHR.status, errorData.msg);
                    return; 
                });   
            }
        })
    });

});