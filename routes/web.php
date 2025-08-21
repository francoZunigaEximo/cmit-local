<?php

use App\Http\Controllers\AliasExamenesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AutorizadoController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\ComentariosPrestacionesController;
use App\Http\Controllers\DatosController;
use App\Http\Controllers\ExamenesController;
use App\Http\Controllers\FichaAltaController;
use App\Http\Controllers\LocalidadController;
use App\Http\Controllers\MapasController;
use App\Http\Controllers\PacientesController;
use App\Http\Controllers\PrestacionesController;
use App\Http\Controllers\ProfesionalesController;
use App\Http\Controllers\TelefonosController;
use App\Http\Controllers\ProveedoresController;
use App\Http\Controllers\ItemPrestacionesController;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\FacturasVentaController;
use App\Http\Controllers\NoticiasController;
use App\Http\Controllers\PrestacionesObsFasesController;
use App\Http\Controllers\OrdenesExamenController;
use App\Http\Controllers\ExamenesCuentaController;
use App\Http\Controllers\GrupoClientesController;
use App\Http\Controllers\LlamadorController;
use App\Http\Controllers\MensajesController;
use App\Http\Controllers\NotasCreditoController;
use App\Http\Controllers\PaqueteEstudioController;
use App\Http\Controllers\PaqueteFacturacionController;
use App\Http\Controllers\PaquetesController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UserSessionsController;
use App\Http\Controllers\UsuariosController;
use App\Models\FacturaDeVenta;
use App\Models\GrupoClientes;
use App\Models\ItemPrestacion;
use App\Models\PaqueteEstudio;
use App\Models\PaqueteFacturacion;
use App\Models\PrestacionComentario;
use App\Models\Rol;
//use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

//Route::get('/password', [AuthController::class, 'testCreate'])->name('password');

Route::group(['middleware' => 'guest'], function () {
    Route::view('/', 'layouts.login')->name('login');
    Route::post('/validate-login', [AuthController::class, 'login'])->name('validate-login');
});


// Route::group(['middleware' => 'auth'], function () {
Route::middleware(['auth', 'auth.session'])->group(function() {
    Route::get('/usuario/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/usuario/perfil', [AuthController::class, 'profile'])->name('perfil');
    Route::post('/actualizarPass', [AuthController::class, 'updatePass'])->name('actualizarPass');
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::get('/usuario/check-password', [AuthController::class, 'checkPassword'])->name('usuario.checkPassword');
    Route::get('/usuario/forzar-cierre', [AuthController::class, 'forzarLogout'])->name('usuario.forzarCierre');
    Route::get('usuarios/cierre-automatico', [AuthController::class, 'logoutId'])->name('usuario.cierreAutomatico');
    Route::post('usuarios/heartbeat', [AuthController::class, 'heartbeat'])->name('usuario.heartBeat');
    // Route::get('/passw', function (){
    //     return Hash::make('cmit1234');
    // });
    // Route::get('/test', function () {
    //     ob_start();  // Inicia el almacenamiento en buffer de salida
    //     phpinfo();   // Ejecuta phpinfo()
    //     $phpinfo = ob_get_clean();  // Obtiene el contenido del buffer y limpia el buffer
    //     return $phpinfo;  // Devuelve el contenido del phpinfo() como respuesta
    // });

    /*Route::get('/test-redis', function () {
        Redis::set('prueba', 'Test de Redis!');
        $result = Redis::get('prueba');
        return $result;
    });*/

    //Home del sitio
    Route::get('/home', function () {
        return redirect('/prestaciones');
    })->name('home');

    //Rutas de Utility
    Route::get('localidades', [UtilityController::class, 'getLocalidades'])->name('getLocalidades');
    Route::get('codigo-postal', [UtilityController::class, 'getCodigoPostal'])->name('getCodigoPostal');
    Route::get('provincia', [UtilityController::class, 'checkProvincia'])->name('checkProvincia');

    //Rutas de Pacientes
    Route::get('pacientes/buscar', [PacientesController::class, 'search'])->name('search');
    Route::post('pacientes/baja', [PacientesController::class, 'down'])->name('pacientes.down');
    Route::get('pacientes/verficar-documento', [PacientesController::class, 'verifyDocument'])->name('verify');
    Route::post('pacientes/excel', [PacientesController::class, 'exportExcel'])->name('excelPacientes');
    Route::get('pacientes/datos', [PacientesController::class, 'getPacientes'])->name('getPacientes');
    Route::get('pacientes/buscar-prestacion', [PacientesController::class, 'searchPrestPacientes'])->name('searchPrestPacientes');
    Route::get('pacientes/obtener-nombre', [PacientesController::class, 'getNombre'])->name('getNombre');
    Route::post('pacientes/eliminar-picture', [PacientesController::class, 'deletePicture'])->name('deletePicture');
    Route::resource('pacientes', PacientesController::class);

    //Rutas de Clientes
    Route::get('clientes/check-estado', [ClientesController::class, 'cambioEstado'])->name('clientes.checkEstado');
    Route::get('clientes/forma-pago', [ClientesController::class, 'formaPago'])->name('clientes.formaPago');
    Route::post('clientes/baja', [ClientesController::class, 'baja'])->name('clientes.baja');
    Route::post('cliente/block', [ClientesController::class, 'block'])->name('clientes.block');
    Route::get('clientes/buscar', [ClientesController::class, 'search'])->name('searchClientes');
    Route::get('cliente/verificar-cuit', [ClientesController::class, 'verifyCuitEmpresa'])->name('verifycuitEmpresa');
    Route::get('clientes/datos', [ClientesController::class, 'getClientes'])->name('getClientes');
    Route::post('clientes/observaciones', [ClientesController::class, 'setObservaciones'])->name('clientes.setObservaciones');
    Route::get('cliente/chequear-correo', [ClientesController::class, 'checkEmail'])->name('checkEmail');
    Route::post('cliente/check-opciones', [ClientesController::class, 'checkOpciones'])->name('checkOpciones');
    Route::get('cliente/verificar-identificacion', [ClientesController::class, 'verificarCuit'])->name('verifyIdentificacion');
    Route::get('cliente/exportar-excel', [ClientesController::class, 'excel'])->name('exportExcelClientes');
    Route::get('cliente/check-paraempresa', [ClientesController::class, 'checkParaEmpresa'])->name('checkParaEmpresa');
    Route::get('cliente/bloquear', [ClientesController::class, 'getBloqueo'])->name('getBloqueo');
    Route::resource('clientes', ClientesController::class);


    //Rutas de Prestaciones
    Route::get('prestaciones/baja', [PrestacionesController::class, 'down'])->name('prestaciones.baja');
    Route::get('prestaciones/block', [PrestacionesController::class, 'blockPrestacion'])->name('blockPrestacion');
    Route::get('prestaciones/buscar', [PrestacionesController::class, 'search'])->name('searchPrestaciones');
    Route::get('prestaciones/guardar', [PrestacionesController::class, 'savePrestacion'])->name('savePrestacion');
    Route::post('prestaciones/obtener-para-empresa', [PrestacionesController::class, 'getParaEmpresas'])->name('getParaEmpresas');
    Route::post('prestaciones/chequear-financiador', [PrestacionesController::class, 'checkFinanciador'])->name('checkFinanciador');
    Route::post('prestaciones/verificar-bloqueo', [PrestacionesController::class, 'verifyBlock'])->name('verifyBlock');
    Route::post('prestaciones/obtener-paciente', [PrestacionesController::class, 'getPresPaciente'])->name('getPresPaciente');
    Route::post('prestaciones/actualizar', [PrestacionesController::class, 'updatePrestacion'])->name('updatePrestacion');
    Route::post('prestaciones/actualizar-estado', [PrestacionesController::class, 'estados'])->name('actualizarEstados');
    Route::post('prestaciones/actualizar-vencimiento', [PrestacionesController::class, 'vencimiento'])->name('actualizarVto');
    Route::post('prestaciones/guardar/evaluador', [PrestacionesController::class, 'setEvaluador'])->name('setEvaluador');
    Route::get('prestaciones/wizard', [PrestacionesController::class, 'verifyWizard'])->name('verifyWizard');
    Route::get('prestaciones/excel', [PrestacionesController::class, 'exportExcel'])->name('prestaciones.excel');
    Route::get('prestaciones/obtener-bloqueo', [PrestacionesController::class, 'getBloqueo'])->name('getBloqueoPrestacion');
    Route::get('lstTipoPrestacion', [PrestacionesController::class, 'lstTipoPrestacion'])->name('lstTipoPrestacion');
    Route::get('buscarEx', [PrestacionesController::class, 'buscarEx'])->name('buscarEx');
    Route::get('prestaciones/check-incompleto', [PrestacionesController::class, 'checkIncompleto'])->name('prestaciones.checkIncompleto');
    Route::post('prestaciones/nueva-observacion', [PrestacionesController::class, 'obsNuevaPrestacion'])->name('obsNuevaPrestacion');
    Route::post('prestaciones/borrar-cache', [PrestacionesController::class, 'cacheDelete'])->name('prestaciones.cacheDelete');
    Route::get('prestaciones/pdf', [PrestacionesController::class, 'pdf'])->name('prestaciones.pdf');
    Route::get('prestaciones/estudios-listado', [PrestacionesController::class, 'getEstudiosReporte'])->name('prestaciones.estudioReporte');
    Route::get('prestaciones/enviar-reporte', [PrestacionesController::class, 'enviarReporte'])->name('prestaciones.enviar');
    Route::get('prestaciones/aviso-reporte', [PrestacionesController::class, 'avisoReporte'])->name('prestaciones.aviso');
    Route::get('prestaciones/resumen-export-excel', [PrestacionesController::class, 'resumenExcel'])->name('prestaciones.excelResumen');
    Route::get('prestaciones/visible-eEnviar', [PrestacionesController::class, 'visibleButtonEnviar'])->name('prestaciones.visibleEnviar');
    Route::get('prestaciones/boton-todo', [PrestacionesController::class, 'cmdTodo'])->name('prestaciones.btnTodo');
    Route::post('prestaciones/archivo-adjunto-prestacion', [PrestacionesController::class, 'uploadAdjuntoPrestacion'])->name('prestaciones.uploadAdjPres');
    Route::get('prestaciones/listado-adjunto-prestacion', [PrestacionesController::class, 'getListadoAdjPres'])->name('prestaciones.listaAdjPres');
    Route::get('prestaciones/eliminar-adjunto-prestacion', [PrestacionesController::class, 'deleteAdjPrest'])->name('prestaciones.deleteAdjPres');
    Route::get('prestaciones/listado-resultados', [PrestacionesController::class, 'getResultados'])->name('prestaciones.resultados');
    Route::get('prestaciones/exportar-resultados', [PrestacionesController::class, 'exportResultados'])->name('prestaciones.exportarResultado');
    Route::get('prestaciones/enviar-reporteEspecial', [PrestacionesController::class, 'enviarReporteEspecial'])->name('prestaciones.reporteEspecial');
    Route::get('prestaciones/pdfPrueba', [PrestacionesController::class, 'pdfPrueba'])->name('prestaciones.pdfPrueba');
    Route::resource('prestaciones', PrestacionesController::class);

    //Ruta Ficha Laboral
    Route::post('saveFichaAlta', [FichaAltaController::class, 'save'])->name('saveFichaAlta');
    Route::get('verificarAlta', [FichaAltaController::class, 'verificar'])->name('verificarAlta');
    Route::get('getTipoPrestacion', [FichaAltaController::class, 'getTipoPrestacion'])->name('getTipoPrestacion');
    Route::get('checkObs', [FichaAltaController::class, 'checkObs'])->name('checkObs');
    Route::get('verFicha', [FichaAltaController::class, 'verFicha'])->name('verFicha');
    Route::resource('fichalaboral', FichaAltaController::class);

    //Ruta Examenes
    Route::get('searchExamen', [ExamenesController::class, 'search'])->name('searchExamen');
    Route::post('examenes/Id', [ExamenesController::class, 'getId'])->name('IdExamen');
    Route::post('examenes/eliminar', [ExamenesController::class, 'deleteEx'])->name('deleteExamen');
    Route::post('examenes/guardar',[ExamenesController::class, 'saveExamen'])->name('saveExamen');
    Route::get('examenes/buscar', [ExamenesController::class, 'searchExamenes'])->name('searchExamenes');
    Route::post('examenes/actualizar', [ExamenesController::class, 'updateExamen'])->name('updateExamen');
    Route::get('examenes/exportar/excel', [ExamenesController::class, 'excel'])->name('examenes.excel');
    Route::get('reporte/vistaprevia', [ExamenesController::class, 'getVistaPrevia'])->name('examenes.getVistaPrevia');
    Route::get('examenes/getReportes', [ExamenesController::class, 'getReportes'])->name('examenes.getReportes');
    Route::get('porcentajeExamen', [ExamenesController::class, 'porcentajeExamen'])->name('porcentajeExamen');
    Route::get('examenes/listar', [ExamenesController::class, 'getExamenes'])->name('examenes.getExamenes');
    Route::get('examenes/listar/Id', [ExamenesController::class, 'getById'])->name('examenes.getById');
    Route::resource('examenes', ExamenesController::class);
    
    //Ruta de Comentarios de Prestaciones
    Route::post('comentarios/guardar', [ComentariosPrestacionesController::class, 'setComentarioPres'])->name('setComentarioPres');
    Route::get('comentarios', [ComentariosPrestacionesController::class, 'getComentarioPres'])->name('getComentarioPres');

    //Rutas de Autorizados
    Route::post('autorizados/eliminar', [AutorizadoController::class, 'delete'])->name('deleteAutorizado');
    Route::get('autorizados/listado', [AutorizadoController::class, 'getAut'])->name('getAutorizados');
    Route::post('autorizados/alta', [AutorizadoController::class, 'alta'])->name('clientes.altaAutorizado');

    //Rutas de Telefonos
    Route::get('telefonos', [TelefonosController::class, 'getTelefonos'])->name('getTelefonos');
    Route::post('telefonos/eliminar', [TelefonosController::class, 'deleteTelefono'])->name('deleteTelefono');
    Route::post('telefonos/guardar', [TelefonosController::class, 'saveTelefono'])->name('saveTelefono');

    //Rutas de Localidades
    Route::get('localidades/buscar', [LocalidadController::class, 'searchLocalidad'])->name('searchLocalidad');

    //Rutas de Mapas
    Route::get('mapas/excel', [MapasController::class, 'export'])->name('mapas.exportar');
    Route::post('mapas/actualizar', [MapasController::class, 'updateMapa'])->name('updateMapa');
    Route::post('mapas/eliminar', [MapasController::class, 'delete'])->name('deleteMapa');
    Route::get('mapas/remitos', [MapasController::class, 'getRemito'])->name('getRemito');
    Route::get('mapas/listado', [MapasController::class, 'getMapas'])->name('getMapas');
    Route::get('mapas/pacientes', [MapasController::class, 'getPacienteMapa'])->name('getPacienteMapa');
    Route::get('mapas/prestaciones/examenes', [MapasController::class, 'examenes'])->name('mapas.getExamen');
    Route::get('mapas/prestaciones', [MapasController::class, 'prestaciones'])->name('getPrestaciones');
    Route::get('mapas/cerrar', [MapasController::class, 'getCerrar'])->name('getCerrar');
    Route::get('mapas/Finalizados', [MapasController::class, 'getFinalizar'])->name('getFinalizar');
    Route::get('mapas/Finalizados', [MapasController::class, 'getFinalizar'])->name('getFMapa');
    Route::get('mapas/buscar', [MapasController::class, 'search'])->name('searchMapas');
    Route::post('mapas/buscar/prestaciones', [MapasController::class, 'searchMapaPres'])->name('searchMapaPres');
    Route::get('mapas/buscar/cerrados', [MapasController::class, 'serchInCerrar'])->name('serchInCerrar');
    Route::get('mapas/buscar/finalizados', [MapasController::class, 'searchInFinalizar'])->name('searchInFinalizar');
    Route::get('mapas/buscar/enviados', [MapasController::class, 'searchInEnviar'])->name('searchInEnviar');
    Route::post('mapas/cerrar/guardar', [MapasController::class, 'saveCerrar'])->name('saveCerrar');
    Route::post('mapas/finalizar/guardar', [MapasController::class, 'saveFinalizar'])->name('saveFinalizar');
    Route::post('mapas/enviar/guardar', [MapasController::class, 'saveEnviar'])->name('saveEnviar');
    Route::post('mapas/estados/guardar', [MapasController::class, 'saveEstado'])->name('saveEstado');
    Route::post('mapas/remitos/guardar', [MapasController::class, 'saveRemitos'])->name('saveRemitos');
    Route::get('mapas/check', [MapasController::class, 'checker'])->name('checkMapa');
    Route::post('mapas/cambiar-estado', [MapasController::class, 'changeEstado'])->name('changeEstado');
    Route::get('mapas/enviar', [MapasController::class, 'geteEnviar'])->name('enviarMapa');
    Route::post('mapas/revertir-remito', [MapasController::class, 'reverseRemito'])->name('reverseRemito');
    Route::get('mapas/mapa-prestacion-Id', [MapasController::class, 'getMapaPrestacion'])->name('prestaciones.mapaPrestacionId');
    Route::get('mapas/enviar/vista-previa', [MapasController::class, 'vistaPreviaReporte'])->name('mapas.vistaPrevia');
    Route::get('mapas/auditoria', [MapasController::class, 'listadoAuditorias'])->name('mapas.auditorias');
    Route::resource('mapas', MapasController::class);
    
    //Rutas de Profesionales
    //Route::resource('profesionales', ProfesionalesController::class);
    Route::get('profesionales/evaluador', [ProfesionalesController::class, 'getEvaluador'])->name('getEvaluador');
    Route::get('profesionales/buscar', [ProfesionalesController::class, 'search'])->name('searchProfesionales');
    Route::post('profesionales/estado', [ProfesionalesController::class, 'estado'])->name('estadoProfesional');
    Route::post('profesionales/perfil/guardar', [ProfesionalesController::class, 'setPerfil'])->name('setPerfiles');
    Route::get('profesionales/perfil', [ProfesionalesController::class, 'getPerfil'])->name('getPerfiles');
    Route::post('profesionales/eliminar/perfil', [ProfesionalesController::class, 'delPerfil'])->name('delPerfil');
    Route::post('profesionales/documento/chequear', [ProfesionalesController::class, 'checkDocumento'])->name('checkDocumento');
    Route::post('profesionales/opcion/guardar', [ProfesionalesController::class, 'opciones'])->name('profesionales.opciones');
    Route::post('profesionales/seguro/guardar', [ProfesionalesController::class, 'seguro'])->name('profesionales.seguro');
    Route::get('profesionales/seleccion-perfil', [ProfesionalesController::class, 'choisePerfil'])->name('choisePerfil');
    Route::get('profesionales/seleccion-especialidad', [ProfesionalesController::class, 'choiseEspecialidad'])->name('choiseEspecialidad');
    Route::post('profesionales/prestador/guardar', [ProfesionalesController::class, 'savePrestador'])->name('savePrestador');
    Route::get('profesionales/listado', [ProfesionalesController::class, 'listGeneral'])->name('listGeneral');

    //Rutas de Proveedores
    Route::get('/especialidades/select', [ProveedoresController::class, 'getProveedores'])->name('getProveedores');
    Route::get('/especialidades/buscar', [ProveedoresController::class, 'search'])->name('searchEspecialidad');
    Route::get('/especialidades/exportar/excel', [ProveedoresController::class, 'excel'])->name('especialidadExcel');
    Route::post('/especialidades/baja-multiple', [ProveedoresController::class, 'baja'])->name('multiDownEspecialidad');
    Route::get('/especialidades/chequear', [ProveedoresController::class, 'check'])->name('checkProveedor');
    Route::post('/especialidades/guardar', [ProveedoresController::class, 'save'])->name('saveBasico');
    Route::post('/especialidades/actualizar', [ProveedoresController::class, 'updateProveedor'])->name('updateProveedor');
    Route::get('/especialidades/listado', [ProveedoresController::class, 'lstProveedores'])->name('lstProveedores');
    Route::resource('especialidades', ProveedoresController::class);
    
    //Rutas de ItemsPrestaciones
    Route::get('itemsprestaciones/lista-examenes', [ItemPrestacionesController::class, 'getExamenes'])->name('itemsprestaciones.listadoexamenes');
    Route::get('itemsprestaciones/lista-examenes/estandar', [ItemPrestacionesController::class, 'getExamenesStd'])->name('itemsprestaciones.lstExamenesEstandar');
    Route::get('itemsprestaciones/check-adjuntos', [ItemPrestacionesController::class, 'checkAdjunto'])->name('itemsprestaciones.checkAdjuntos');
    Route::get('itemsprestaciones/checkid', [ItemPrestacionesController::class, 'checkPrimeraCarga'])->name('itemsprestaciones.checkId');
    Route::get('itemsprestaciones/editModal', [ItemPrestacionesController::class, 'editModal'])->name('itemsprestaciones.editModal');
    Route::get('itemsprestaciones/contador', [ItemPrestacionesController::class, 'contadorExamenes'])->name('itemsprestaciones.contador');
    Route::get('itemsprestaciones/check-facturas', [ItemPrestacionesController::class, 'checkFacturaItemPrestacion'])->name('itemsprestaciones.checkFacturas');
    Route::post('updateItem', [ItemPrestacionesController::class, 'updateItem'])->name('updateItem');
    Route::post('updateAsignado', [ItemPrestacionesController::class, 'updateAsignado'])->name('updateAsignado');
    Route::post('updateAdjunto', [ItemPrestacionesController::class, 'updateAdjunto'])->name('updateAdjunto');
    Route::get('paginacionGeneral', [ItemPrestacionesController::class, 'paginacionGeneral'])->name('paginacionGeneral');
    Route::post('updateItemExamen', [ItemPrestacionesController::class, 'updateExamen'])->name('updateItemExamen');
    Route::post('uploadAdjunto', [ItemPrestacionesController::class, 'uploadAdjunto'])->name('uploadAdjunto');
    Route::get('deleteIdAdjunto', [ItemPrestacionesController::class, 'deleteIdAdjunto'])->name('deleteIdAdjunto');
    Route::post('replaceIdAdjunto', [ItemPrestacionesController::class, 'replaceIdAdjunto'])->name('replaceIdAdjunto');
    Route::post('deleteItemExamen', [ItemPrestacionesController::class, 'deleteEx'])->name('deleteItemExamen');
    Route::post('itemsprestaciones/guardar', [ItemPrestacionesController::class, 'save'])->name('saveItemExamenes');
    Route::post('itemExamen', [ItemPrestacionesController::class, 'itemExamen'])->name('itemExamen');
    Route::post('bloquearItemExamen', [ItemPrestacionesController::class, 'bloquearEx'])->name('bloquearItemExamen');
    Route::post('asignarProfesional', [ItemPrestacionesController::class, 'asignarProfesional'])->name('asignarProfesional');
    Route::get('getBloqueoItemPrestacion', [ItemPrestacionesController::class, 'getBloqueo'])->name('getBloqueoItemPrestacion');
    Route::post('archivosAutomatico', [ItemPrestacionesController::class, 'archivosAutomatico'])->name('archivosAutomatico');
    Route::post('archivosAutomaticoI', [ItemPrestacionesController::class, 'archivosAutomaticoI'])->name('archivosAutomaticoI');
    Route::post('updateEstadoItem', [ItemPrestacionesController::class, 'updateEstadoItem'])->name('updateEstadoItem');
    Route::post('liberarExamen', [ItemPrestacionesController::class, 'liberarExamen'])->name('liberarExamen');
    Route::post('marcarExamenAdjunto', [ItemPrestacionesController::class, 'marcarExamenAdjunto'])->name('marcarExamenAdjunto');
    Route::get('lstExamenes', [ItemPrestacionesController::class, 'lstExamenes'])->name('lstExamenes');
    Route::get('preExamenes', [ItemPrestacionesController::class, 'preExamenes'])->name('preExamenes');
    Route::get('itemprestaciones/paginacion-prestacion', [ItemPrestacionesController::class, 'paginacionByPrestacion'])->name('paginacionByPrestacion');
    Route::resource('itemsprestaciones', ItemPrestacionesController::class);

    //Rutas de FacturasdeVenta
    Route::get('getFactura', [FacturasVentaController::class, 'getFactura'])->name('getFactura');
    Route::get('/facturas/search', [FacturasVentaController::class, 'search'])->name('facturas.search');
    Route::get('/facturas/delete', [FacturasVentaController::class, 'delete'])->name('facturas.delete');
    Route::get('/facturas/export', [FacturasVentaController::class, 'export'])->name('facturas.export');
    Route::get('/facturas/enviar',[FacturasVentaController::class, 'enviar'])->name('facturas.enviar');
    Route::get('/facturas/excel', [FacturasVentaController::class, 'excel'])->name('facturas.excel');
    Route::get('/facturas/paginacion/alta',[FacturasVentaController::class, 'paginacionAlta'])->name('facturas.paginacion');
    Route::get('facturas/detalles', [FacturasVentaController::class, 'verDetalle'])->name('facturas.detalle');
    Route::resource('facturas', FacturasVentaController::class);

    // Rutas de Noticias
    Route::resource('noticias', NoticiasController::class);
    Route::post('/noticias/actualizar', [NoticiasController::class, 'update'])->name('updateNoticia');

    //Rutas de Observaciones de Fases de Prestaciones
    Route::get('comentarios-privados', [PrestacionesObsFasesController::class, 'comentariosPriv'])->name('comentariosPriv');
    Route::post('comentarios-privados/guardar', [PrestacionesObsFasesController::class, 'addComentario'])->name('savePrivComent');
    Route::get('comentarios-privados/check-rol', [PrestacionesObsFasesController::class, 'listadoRoles'])->name('comentariosPrivados.checkRoles');
    Route::get('comentarios-privados/eliminar', [PrestacionesObsFasesController::class, 'deleteComentario'])->name('comentariosPriv.eliminar');
    Route::get('comentarios-privados/editar', [PrestacionesObsFasesController::class, 'editComentario'])->name('comentariosPriv.editar');
    Route::get('comentarios-privados/comentario', [PrestacionesObsFasesController::class, 'getComentario'])->name('comentariosPriv.data');

    //Rutas de Ordenes de examenes efectores
    Route::get('/etapas/buscar', [OrdenesExamenController::class, 'search'])->name('seachOrdenesExamen');
    Route::get('/etapas/efector-asignado/buscar', [OrdenesExamenController::class, 'searchA'])->name('searchOrExaAsignados');
    Route::get('/etapas/ordenes-adjunto-efector/buscar', [OrdenesExamenController::class, 'searchAdj'])->name('searchOrExaAdjunto');
    Route::get('/etapas/informador/buscar', [OrdenesExamenController::class, 'searchInf'])->name('seachOrExInf');
    Route::get('/etapas/informador-asignado/buscar', [OrdenesExamenController::class, 'searchInfA'])->name('seachOrExAsigInf');
    Route::get('/etapas/ordenes-adjunto-informador/buscar', [OrdenesExamenController::class, 'searchInfAdj'])->name('searchOrExaAdjInf');
    Route::get('/etapas/prestacion/buscar', [OrdenesExamenController::class, 'searchPrestacion'])->name('searchPrestacion');
    Route::get('/etapas/exportar', [OrdenesExamenController::class, 'exportar'])->name('exportarOrdExa');
    Route::get('/etapas/enviar/buscar', [OrdenesExamenController::class, 'searchEenviar'])->name('searchEenviar');
    Route::get('/etapas/vista-previa', [OrdenesExamenController::class, 'vistaPreviaReporte'])->name('ordenesExamen.vistaPrevia');
    Route::get('/etapas/envio-avisos', [OrdenesExamenController::class, 'envioAviso'])->name('ordenesExamen.aviso');
    Route::get('/etapas/obtener-pagado', [OrdenesExamenController::class, 'getPagado'])->name('ordenesExamen.obtenerPagado');
    Route::get('/etapas/enviar-estudio', [OrdenesExamenController::class, 'enviarEstudio'])->name('ordenesExamen.enviarEstudio');
    Route::resource('ordenesExamen', OrdenesExamenController::class);

    //Rutas de Examenes a Cuenta
    Route::get('/examen-cuenta/buscar', [ExamenesCuentaController::class, 'search'])->name('searchExCuenta');
    Route::post('cambiarPago', [ExamenesCuentaController::class, 'cambiarPago'])->name('cambiarPago');
    Route::get('detallesExamenes', [ExamenesCuentaController::class, 'detalles'])->name('detallesExamenes');
    Route::post('eliminarExCuenta', [ExamenesCuentaController::class, 'delete'])->name('eliminarExCuenta');
    Route::get('searchSaldo', [ExamenesCuentaController::class, 'saldo'])->name('searchSaldo');
    Route::post('saveExamenCuenta', [ExamenesCuentaController::class, 'save'])->name('saveExamenCuenta');
    Route::get('listadoExCta', [ExamenesCuentaController::class, 'listado'])->name('listadoExCta');
    Route::post('updateExamenCuenta', [ExamenesCuentaController::class, 'update'])->name('updateExamenCuenta');
    Route::get('deleteItemExCta', [ExamenesCuentaController::class, 'deleteItem'])->name('deleteItemExCta');
    Route::get('liberarItemExCta', [ExamenesCuentaController::class, 'liberarItem'])->name('liberarItemExCta');
    Route::post('savePrecarga', [ExamenesCuentaController::class, 'precarga'])->name('savePrecarga');
    Route::post('savePaquete', [ExamenesCuentaController::class, 'saveEx'])->name('savePaquete');
    Route::get('lstClientes', [ExamenesCuentaController::class, 'lstClientes'])->name('lstClientes');
    Route::get('lstExClientes', [ExamenesCuentaController::class, 'lstExClientes'])->name('lstExClientes');
    Route::get('listadoDni', [ExamenesCuentaController::class, 'listadoDni'])->name('listadoDni');
    Route::get('listPrecarga', [ExamenesCuentaController::class, 'listadoPrecarga'])->name('listPrecarga');
    Route::get('listadoEx', [ExamenesCuentaController::class, 'listadoEx'])->name('listadoEx');
    Route::get('listExCta', [ExamenesCuentaController::class, 'listadoExCta'])->name('listExCta');
    Route::get('exportExcel', [ExamenesCuentaController::class, 'excel'])->name('exportExcel');
    Route::get('exportPDF', [ExamenesCuentaController::class, 'pdf'])->name('exportPDF');
    Route::get('exportarDetalle', [ExamenesCuentaController::class, 'reporteDetalle'])->name('notasCredito.exportarDetalle');
    Route::get('notasCredito/exportarExcel', [ExamenesCuentaController::class, 'reporteGeneral'])->name('notasCredito.exportarExcel');
    Route::get('/examenesCuenta/disponibilidad', [ExamenesCuentaController::class, 'disponibilidad'])->name('lstExDisponibles');
    Route::get('lstFacturadas', [ExamenesCuentaController::class, 'listadoUltimas'])->name('lstFacturadas');
    Route::get('saldoNoDatatable', [ExamenesCuentaController::class, 'saldoNoDatatable'])->name('saldoNoDatatable');
    Route::get('examenesCuenta/listado-examenes', [ExamenesCuentaController::class, 'listExCta'])->name('examenesCuenta.listado');
    Route::post('examenesCuenta/actualizar-prestacion', [ExamenesCuentaController::class, 'cargarExCtaPrestacion'])->name('examenesCuenta.cargar');
    Route::get('examenesCuenta/lista-examenes-cuenta', [ExamenesCuentaController::class, 'listaExCtaEmpresa'])->name('examenesCuenta.listaEmpresa');
    Route::get('examenesCuenta/contador-pagos', [ExamenesCuentaController::class, 'contadorPagos'])->name('examenesCuenta.contadoPagos');
    Route::resource('examenesCuenta', ExamenesCuentaController::class);

    //Rutas de Paquete de Estudio
    Route::get('getPaquetes', [PaqueteEstudioController::class, 'paquetes'])->name('getPaquetes');
    Route::post('paqueteId', [PaqueteEstudioController::class, 'paqueteId'])->name('paqueteId');

    //RUtas de Paquete de Facturación
    Route::get('getPaqueteFact', [PaqueteFacturacionController::class, 'paquetes'])->name('getPaqueteFact');
    Route::post('getPaqueteFacturacionId', [PaqueteFacturacionController::class, 'paqueteId'])->name('paqueteFactId');

    
    //Rutas de Paquete usuarios
    Route::get('/usuarios/buscar', [UsuariosController::class, 'NombreUsuario'])->name('searchNombreUsuario');
    Route::get('usuarios/delete', [UsuariosController::class, 'baja'])->name('usuarios.delete');
    Route::get('/usuario/buscar', [UsuariosController::class, 'Usuario'])->name('searchUsuario');
    Route::get('/usuario/password/actualizar', [UsuariosController::class, 'cambiarPassword'])->name('cambiarPassUsuario');
    Route::get('/usuario/checkear', [UsuariosController::class, 'checkUsuario'])->name('checkUsuario');
    Route::get('/usuario/checkear/correo', [UsuariosController::class, 'checkCorreo'])->name('checkCorreo');
    Route::get('/usuario/bloquear', [UsuariosController::class, 'bloquear'])->name('bloquearUsuario');
    Route::get('/usuario/checkear/telefono', [UsuariosController::class, 'checkTelefono'])->name('checkTelefono');
    Route::post('/usuarios/update/profesional', [UsuariosController::class, 'updateProfesional'])->name('usuarios.updateProfesional');
    Route::get('usuarios/roles/checkear', [UsuariosController::class, 'checkRoles'])->name('checkRoles');
    
    Route::resource('usuarios', UsuariosController::class);
    Route::get('buscarUsuario', [UsuariosController::class, 'buscar'])->name('buscarUsuario');
    Route::get('checkMail', [UsuariosController::class, 'checkMail'])->name('checkMail');
    Route::get('checkEmailUpdate', [UsuariosController::class, 'checkEmailUpdate'])->name('checkEmailUpdate');
   
    //Rutas de Roles
    Route::get('/roles/buscar', [RolesController::class, 'listado'])->name('searchRol');
    Route::get('/roles/listado', [RolesController::class, 'paginacion'])->name('listadoRoles');
    Route::get('/roles/listado/asignados', [RolesController::class, 'asignados'])->name('lstRolAsignados');
    Route::post('/roles/agregar', [RolesController::class, 'add'])->name('addRol');
    Route::get('/roles/eliminar', [RolesController::class, 'delete'])->name('deleteRol');
    Route::get('/roles/obtener', [RolesController::class, 'getRoles'])->name('roles.getRoles');

    //Rutas de Personal
    Route::post('/perfil/actualizar', [DatosController::class, 'save'])->name('actualizarDatos');
   
    //Rutas de Mensajes
    Route::get('mensajes/auditoria', [MensajesController::class, 'auditoria'])->name('mensajes.auditoria');
    Route::get('mensajes/modelos', [MensajesController::class, 'modelos'])->name('mensajes.modelos');
    Route::get('mensajes/modelos/create', [MensajesController::class, 'createModelo'])->name('mensajes.modelos.create');
    Route::get('mensajes/modelos/edit', [MensajesController::class, 'editModelo'])->name('mensajes.modelos.edit');
    Route::get('mensajes/modelos/delete', [MensajesController::class, 'deleteModelo'])->name('mensajes.modelos.delete');
    Route::post('mensajes/modelos/save', [MensajesController::class, 'saveModelo'])->name('mensajes.modelos.save');
    Route::post('mensajes/modelos/update', [MensajesController::class, 'actualizarModelo'])->name('mensajes.modelos.update');
    Route::get('mensajes/buscar-mensajes', [MensajesController::class, 'search'])->name('searchMensaje');
    Route::post('mensajes/update-correo', [MensajesController::class, 'updateEmail'])->name('updateEmail');
    Route::get('mensajes/cargar-modelos', [MensajesController::class, 'loadModelos'])->name('loadModelos');
    Route::get('loadMensaje', [MensajesController::class, 'loadMensaje'])->name('loadMensaje');
    Route::get('verAuditoria', [MensajesController::class, 'verAuditoria'])->name('verAuditoria');
    Route::get('mensajes/search', [MensajesController::class, 'search'])->name('mensajes.search');
    Route::get('mensajes/enviar-emails', [MensajesController::class, 'sendEmails'])->name('sendEmails');
    Route::get('mensajes/test-email', [MensajesController::class, 'testEmail'])->name('testEmail');
    Route::resource('mensajes', MensajesController::class);

    //Rutas de Notas de Crédito
    Route::get('nota-de-credito/check', [NotasCreditoController::class, 'checkNotaCredito'])->name('nota-de-credito.check');

    //Rutas de Alias de Examenes
    Route::post('alias-examenes/add', [AliasExamenesController::class, 'saveAlias'])->name('aliasExamenes.add');
    Route::get('alias-examenes/getList', [AliasExamenesController::class, 'getListadoExamenes'])->name('aliasExamenes.getList');
    Route::get('alias-examenes/delete', [AliasExamenesController::class, 'deleteAlias'])->name('aliasExamenes.del');
    Route::get('alias-examenes/getExamenSelect', [AliasExamenesController::class, 'getAliasSelect'])->name('aliasExamenes.getExamenSelect');

    //Ruta de llamador
    Route::get('llamador/efector', [LlamadorController::class, 'efector'])->name('llamador.efector');
    Route::get('llamador/informador', [LlamadorController::class, 'informador'])->name('llamador.informador');
    Route::get('llamador/combinado', [LlamadorController::class, 'combinado'])->name('llamador.combinado');
    Route::get('llamador/evaluador', [LlamadorController::class, 'evaluador'])->name('llamador.evaluador');
    Route::get('llamador/buscar', [LlamadorController::class, 'buscar'])->name('llamador.buscar');
    Route::get('llamador/efector/exportar', [LlamadorController::class, 'exportar'])->name('llamador.exportar');
    Route::get('llamador/ver-paciente', [LlamadorController::class, 'verPaciente'])->name('llamador.verPaciente');
    Route::get('llamador/efector/llamar-paciente',[LlamadorController::class, 'controlLlamado'])->name('llamador.llamar-paciente');
    Route::get('llamador/check-status', [LlamadorController::class, 'checkLlamado'])->name('llamador.check');
    Route::get('llamador/asignar-profesional', [LlamadorController::class, 'asignarProfesional'])->name('llamador.asignarPaciente');
    Route::get('llamador/informador/buscar', [LlamadorController::class, 'buscarInf'])->name('llamador.buscarInformador');
    Route::get('llamador/buscar/especialidad', [LlamadorController::class, 'listadoEspecialidades'])->name('llamador.buscarEspecialidad');
    Route::get('llamador/estado/efector', [LlamadorController::class, 'cambioEstado'])->name('llamador.cambioEstado');
    Route::get('llamador/cerrarAtencion', [LlamadorController::class, 'cerrarAtencion'])->name('llamador.cerrarAtencion');
    Route::get('llamador.getItemPrestacion', [LlamadorController::class, 'getItemprestacion'])->name('llamador.getItemPrestacion');

    //Ruta de Sesiones
    Route::get('sesiones/sesiones-usuarios', [UserSessionsController::class, 'getSessiones'])->name('sesiones.listaSesiones');
    

    Route::get('paquetes/searchExamenes', [PaquetesController::class, 'searchExamenes'])->name('paquetes.searchExamenes');
    Route::get('paquetes/crearPaqueteExamen', [PaquetesController::class, 'crearPaqueteExamen'])->name('paquetes.crearPaqueteExamen');
    Route::get('paquetes/editPaqueteExamen/{id}', [PaquetesController::class, 'editPaqueteExamen'])->name('paquetes.editPaqueteExamen');
    Route::post('paquetes/postPaqueteEstudio', [PaquetesController::class, 'postPaqueteExamen'])->name('paquetes.postPaqueteExamen');
    Route::post('paquetes/postEditPaqueteExamen', [PaquetesController::class, 'postEditPaqueteExamen'])->name('paquetes.postEditPaqueteExamen');
    Route::get('paquetes/getPaqueteExamen', [PaquetesController::class, 'getPaqueteExamen'])->name('paquetes.getPaqueteExamen');
    Route::get('paquetes/exportExcel', [PaquetesController::class, 'exportExcel'])->name('paquetes.exportExcel');
    Route::get('paquetes/detallesPaquetesEstudios', [PaquetesController::class, 'detalleEstudios'])->name('paquetes.detalleEstudios');
    Route::post('paquetes/eliminarPaqueteExamen', [PaquetesController::class, 'eliminarPaqueteEstudio'])->name('paquetes.eliminarPaqueteEstudio');

    Route::get('paquetes/getEstudiosPaqueteEstudios', [PaquetesController::class, 'getEstudiosPaqueteEstudio'])->name('paquetes.getEstudiosPaqueteEstudio');

    Route::get('paquetes/searchDetallesPaquetesEstudios', [PaquetesController::class, 'searchDetalleEstudios'])->name('paquetes.searchDetalleEstudios');
    Route::get('paquetes/exportEstudioDetalleExcel', [PaquetesController::class, 'exportDetalleExcel'])->name('paquetes.exportDetalleExcel');

    Route::get('paquetes/searchPaquetesFacturacion', [PaquetesController::class, 'searchPaquetesFacturacion'])->name('paquetes.searchPaquetesFacturacion');
    Route::get('paquetes/createPaqueteFacturacion', [PaquetesController::class, 'createPaqueteFacturacion'])->name('paquetes.createPaqueteFacturacion');
    Route::post('paquetes/postPaqueteFacturacionCreate', [PaquetesController::class, 'postPaqueteFacturacionCreate'])->name('paquetes.postPaqueteFacturacionCreate');
    Route::get('paquetes/editPaqueteFacturacion/{id}', [PaquetesController::class, 'editPaqueteFacturacion'])->name('paquetes.editPaqueteFacturacion');
    Route::post('paquetes/postEditPaqueteFacturacion', [PaquetesController::class, 'postEditPaqueteFactutacion'])->name('paquetes.postEditPaqueteFactutacion');
    Route::get('paquetes/getPaqueteFacturacion', [PaquetesController::class, 'getPaqueteFacturacion'])->name('paquetes.getPaqueteFacturacion');
    Route::post('paquetes/eliminarPaqueteFacturacion', [PaquetesController::class, 'eliminarPaqueteFacturacion'])->name('paquetes.eliminarPaqueteFacturacion');

    Route::get('paquetes/getEstudiosPaqueteFacturacion', [PaquetesController::class, 'getEstudiosPaqueteFacturacion'])->name('paquetes.getEstudiosPaqueteFacturacion');

    Route::get('paquetes/detallesFacturacion', [PaquetesController::class, 'detallesFacturacion'])->name('paquetes.detallesFacturacion');
    Route::get('paquetes/searchDetalleFacturacion', [PaquetesController::class, 'searchDetalleFacturacion'])->name('paquetes.searchDetalleFacturacion');
    Route::get('paquetes/exportDetalleFacturacionExcel', [PaquetesController::class, 'exportDetalleFacturacionExcel'])->name('paquetes.exportDetalleFacturacionExcel');

    //ruta paquete estudios id
    Route::post('paquetes/examenesPaqueteId', [PaquetesController::class, 'getExamenesPaqueteId'])->name('paquetes.examenesPaqueteId');
    Route::post('paquetes/examenesPaqueteFacturacionId', [PaquetesController::class, 'getExamenesPaqueteEstudio'])->name('paquetes.examenesPaqueteFacturacionId');
    Route::get('paquetes/index', [PaquetesController::class, 'index'])->name('paquetes.index');
    Route::resource('paquetes', PaquetesController::class);

    //grupos
    Route::get('grupos/search', [GrupoClientesController::class, 'searchGrupos'])->name('grupos.search');
    Route::get('grupos/exportarExcel', [GrupoClientesController::class, 'exportExcel'])->name('grupos.exportExcel');
    Route::get('grupos/create', [GrupoClientesController::class, 'create'])->name('grupos.create');
    Route::get('grupos/getCliente', [GrupoClientesController::class, 'getCliente'])->name('grupos.getCliente');
    Route::get('grupos/getGrupo', [GrupoClientesController::class, 'getGrupo'])->name('grupos.getGrupo');
    Route::post('grupos/postGrupoCliente', [GrupoClientesController::class, 'postGrupoCliente'])->name('grupos.postGrupoCliente');
    Route::get('grupos/edit/{id}', [GrupoClientesController::class, 'edit'])->name('grupos.edit');
    Route::post('grupos/getEmpresasGrupo' ,[GrupoClientesController::class, 'getEmpresasGrupoCliente'])->name('grupos.getEmpresasGrupoCliente');
    Route::post('grupos/postEditGrupoCliente', [GrupoClientesController::class, 'postEditGrupoCliente'])->name('grupos.postEditGrupoCliente');
    Route::post('grupos/deleteGrupoCliente', [GrupoClientesController::class, 'deleteGrupoCliente'])->name('grupos.deleteGrupoCliente');

    Route::get('grupos/detalle', [GrupoClientesController::class, 'detalle'])->name('grupos.detalle');
    Route::get('grupos/searchDetalle', [GrupoClientesController::class, 'detalleSearch'])->name('grupos.detalleSearch');
    Route::get('grupos/exportDetalleExcel', [GrupoClientesController::class, 'exportDetalleExcel'])->name('grupos.exportDetalleExcel');
    Route::get('getGrupos', [GrupoClientesController::class, 'grupos'])->name('getGrupos');

    Route::resource('grupos', GrupoClientesController::class);

    //notas credito
    Route::get('notasCredito/getClientes', [NotasCreditoController::class, 'getClientes'])->name('notasCredito.getClientes');
    Route::get('notasCredito/itemsanulados/{id}', [NotasCreditoController::class, 'getItemsAnulados'])->name('paquetes.itemsAnulados');
    Route::get('notasCredito/getItemsAnuladosClientes', [NotasCreditoController::class, 'getItemsFacturaVenta'])->name('notasCredito.getItemsAnuladosClientes');
    Route::post('notasCredito/reactivarItem', [NotasCreditoController::class, 'reactivarItem'])->name('notasCredito.reactivarItem');
    Route::post('notasCredito/crear', [NotasCreditoController::class, 'crearNotaCredito'])->name('notasCredito.crear');
    Route::get('notasCredito/getNotaCredito', [NotasCreditoController::class, 'getNotas'])->name('notasCredito.getNotaCredito');
    Route::get('notasCredito/editarNotaCredito/{id}', [NotasCreditoController::class, 'editarNotasCredito'])->name('notasCredito.editarNotaCredito');
    Route::get('notasCredito/getItemsNotaCredito', [NotasCreditoController::class, 'getItemsNotaCredito'])->name('notasCredito.getItemsNotaCredito');
    Route::post('notasCredito/editarNotasCreditoPost', [NotasCreditoController::class, 'editarNotasCreditoPost'])->name('notasCredito.editarNotasCreditoPost');
    Route::post('notasCredito/eliminarNotaCredito', [NotasCreditoController::class, 'eliminarNotaCredito'])->name('notasCredito.eliminarNotaCredito');
    Route::get('notasCredito/exportarNotaCreditoExcel', [NotasCreditoController::class, 'exportDetalleNotaCreditoExcel'])->name('notasCredito.exportDetalleNotaCreditoExcel');
    Route::post('notasCredito/eliminarNotaCreditoMasivo', [NotasCreditoController::class, 'eliminarNotaCreditoMasivo'])->name('notasCredito.eliminarNotaCreditoMasivo');
    Route::get('notasCredito/exportClientesItemsAnuladosExcel', [NotasCreditoController::class, 'exportClientesItemsAnuladosExcel'])->name('notasCredito.exportClientesItemsAnuladosExcel');

    Route::resource('notasCredito', NotasCreditoController::class);
});