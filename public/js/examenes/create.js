$(document).ready(function(){

    let clonExamen = localStorage.getItem('clon_Examen'),
    clonEstudio = localStorage.getItem('clon_Estudio'),
    clonDescripcion = localStorage.getItem('clon_Descripcion'),
    clonReporte = localStorage.getItem('clon_Reporte'),
    clonFormulario = localStorage.getItem('clon_Formulario'),
    clonCodigoE = localStorage.getItem('clon_CodigoE'),
    clonDiasVencimiento = localStorage.getItem('clon_DiasVencimiento'),
    clonInactivo = (localStorage.getItem('clon_Inactivo') === 'true'),
    clonpriImpresion = (localStorage.getItem('clon_priImpresion') === 'true'),
    clonProvEfector = localStorage.getItem('clon_ProvEfector'),
    clonProvInformador = localStorage.getItem('clon_ProvInformador'),
    clonInforme = (localStorage.getItem('clon_Informe') === 'true'),
    clonFisico = (localStorage.getItem('clon_Fisico') === 'true'),
    clonAdjunto = (localStorage.getItem('clon_Adjunto') === 'true'),
    clonAusente = (localStorage.getItem('clon_Ausente') === 'true'),
    clonDevolucion = (localStorage.getItem('clon_Devolucion') === 'true'),
    clonEvalExclusivo = (localStorage.getItem('clon_EvalExclusivo') === 'true'),
    clonExpAnexo = (localStorage.getItem('clon_ExpAnexo') === 'true');
    clonAliasExamen = (localStorage.getItem('clon_AliasExamen') === 'true');

    $(document).on('click', '#volver', function(){
        history.back();
    });

    $('#Examen').val(clonExamen);
    $('#Estudio').val(clonEstudio);
    $('#Descripcion').val(clonDescripcion);
    $('#Reporte').val(clonReporte);
    $('#Formulario').val(clonFormulario);
    $('#CodigoE').val(clonCodigoE);
    $('#DiasVencimiento').val(clonDiasVencimiento);
    $('#Inactivo').prop('checked', clonInactivo);
    $('#priImpresion').prop('checked', clonpriImpresion);
    $('#ProvEfector').val(clonProvEfector);
    $('#ProvInformador').val(clonProvInformador);
    $('#Informe').prop('checked', clonInforme);
    $('#Fisico').prop('checked', clonFisico);
    $('#Adjunto').prop('checked', clonAdjunto);
    $('#Ausente').prop('checked', clonAusente);
    $('#Devolucion').prop('checked', clonDevolucion);
    $('#EvalExclusivo').prop('checked', clonEvalExclusivo);
    $('#ExpAnexo').prop('checked', clonExpAnexo);
    $('#aliasexamenes').val(clonAliasExamen);

    localStorage.clear(); 

    let exito = '<div class="alert alert-primary alert-dismissible fade show" role="alert">' +
                '<strong> ¡Clonación correcta! </strong> Se ha clonado el exámen. Rellene los datos faltantes' +
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                '</div>';

    if(clonExamen !== null){
        $('#messageExamenes').html(exito);
    } 
    

});