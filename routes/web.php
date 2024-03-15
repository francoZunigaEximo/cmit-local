<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AutorizadoController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\ComentariosPrestacionesController;
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
//use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

//Route::get('/password', [AuthController::class, 'testCreate'])->name('password');

Route::group(['middleware' => 'guest'], function () {
    Route::view('/', 'layouts.login')->name('login');
    Route::post('/validate-login', [AuthController::class, 'login'])->name('validate-login');
});

Route::group(['middleware' => 'auth'], function () {

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/changePassword', [AuthController::class, 'cambiarPass'])->name('changePassword');
    Route::post('/changePassword', [AuthController::class, 'cambiarPostPass'])->name('changePass');

    /*Route::get('/test-redis', function () {
        Redis::set('prueba', 'Test de Redis!');
        $result = Redis::get('prueba');
        return $result;
    });*/

    //Home del sitio
    //Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/home', function () {
        return redirect('/prestaciones');
    })->name('home');

    //Rutas de Utility
    Route::get('getLocalidades', [UtilityController::class, 'getLocalidades'])->name('getLocalidades');
    Route::get('getCodigoPostal', [UtilityController::class, 'getCodigoPostal'])->name('getCodigoPostal');
    Route::get('checkProvincia', [UtilityController::class, 'checkProvincia'])->name('checkProvincia');

    //Rutas de Pacientes
    Route::resource('pacientes', PacientesController::class);
    Route::get('searchPacientes', [PacientesController::class, 'search'])->name('search');
    Route::post('paciente/down', [PacientesController::class, 'down'])->name('down');
    Route::get('verifydocument', [PacientesController::class, 'verifyDocument'])->name('verify');
    Route::post('pacientes/multipleDown', [PacientesController::class, 'multipleDown'])->name('pacientes.multipleDown');
    Route::get('excelPacientes', [PacientesController::class, 'exportExcel'])->name('excelPacientes');
    Route::get('getPacientes', [PacientesController::class, 'getPacientes'])->name('getPacientes');
    Route::get('searchPrestPacientes', [PacientesController::class, 'searchPrestPacientes'])->name('searchPrestPacientes');
    Route::get('getNombre', [PacientesController::class, 'getNombre'])->name('getNombre');
    Route::post('deletePicture', [PacientesController::class, 'deletePicture'])->name('deletePicture');

    //Rutas de Clientes
    Route::resource('clientes', ClientesController::class);
    Route::post('cliente/down', [ClientesController::class, 'baja'])->name('baja');
    Route::post('clientes/multipleDown', [ClientesController::class, 'multipleDown'])->name('clientes.multipleDown');
    Route::post('cliente/block', [ClientesController::class, 'block'])->name('clientes.block');
    Route::get('searchClientes', [ClientesController::class, 'search'])->name('searchClientes');
    Route::get('/verifycuitEmpresa', [ClientesController::class, 'verifyCuitEmpresa'])->name('verifycuitEmpresa');
    Route::get('getClientes', [ClientesController::class, 'getClientes'])->name('getClientes');
    Route::post('/clientes/setObservaciones', [ClientesController::class, 'setObservaciones'])->name('clientes.setObservaciones');
    Route::post('checkEmail', [ClientesController::class, 'checkEmail'])->name('checkEmail');
    Route::post('checkOpciones', [ClientesController::class, 'checkOpciones'])->name('checkOpciones');
    Route::get('verifyIdentificacion', [ClientesController::class, 'verifyIdentificacion'])->name('verifyIdentificacion');
    Route::get('exportExcelClientes', [ClientesController::class, 'excel'])->name('exportExcelClientes');
    Route::get('checkParaEmpresa', [ClientesController::class, 'checkParaEmpresa'])->name('checkParaEmpresa');
    route::get('getBloqueo', [ClientesController::class, 'getBloqueo'])->name('getBloqueo');

    //Rutas de Prestaciones
    Route::resource('prestaciones', PrestacionesController::class);
    Route::get('downPrestaActiva', [PrestacionesController::class, 'down'])->name('downPrestaActiva');
    Route::get('prestacion/block', [PrestacionesController::class, 'blockPrestacion'])->name('blockPrestacion');
    Route::get('searchPrestaciones', [PrestacionesController::class, 'search'])->name('searchPrestaciones');
    Route::post('savePrestacion', [PrestacionesController::class, 'savePrestacion'])->name('savePrestacion');
    Route::post('getParaEmpresas', [PrestacionesController::class, 'getParaEmpresas'])->name('getParaEmpresas');
    Route::post('checkFinanciador', [PrestacionesController::class, 'checkFinanciador'])->name('checkFinanciador');
    Route::post('verifyBlock', [PrestacionesController::class, 'verifyBlock'])->name('verifyBlock');
    Route::post('getPresPaciente', [PrestacionesController::class, 'getPresPaciente'])->name('getPresPaciente');
    Route::post('updatePrestacion', [PrestacionesController::class, 'updatePrestacion'])->name('updatePrestacion');
    Route::post('actualizarEstados', [PrestacionesController::class, 'estados'])->name('actualizarEstados');
    Route::post('actualizarVto', [PrestacionesController::class, 'vencimiento'])->name('actualizarVto');
    Route::post('setEvaluador', [PrestacionesController::class, 'setEvaluador'])->name('setEvaluador');
    Route::get('verifyWizard', [PrestacionesController::class, 'verifyWizard'])->name('verifyWizard');
    Route::get('excelPrestaciones', [PrestacionesController::class, 'exportExcel'])->name('excelPrestaciones');
    Route::get('getBloqueoPrestacion', [PrestacionesController::class, 'getBloqueo'])->name('getBloqueoPrestacion');

    //Ruta Ficha Laboral
    Route::post('saveFichaAlta', [FichaAltaController::class, 'save'])->name('saveFichaAlta');
    Route::get('verificarAlta', [FichaAltaController::class, 'verificar'])->name('verificarAlta');
    Route::get('getTipoPrestacion', [FichaAltaController::class, 'getTipoPrestacion'])->name('getTipoPrestacion');
    Route::get('checkObs', [FichaAltaController::class, 'checkObs'])->name('checkObs');

    //Ruta Examenes
    Route::resource('examenes', ExamenesController::class);
    Route::get('getPaquetes', [ExamenesController::class, 'paquetes'])->name('getPaquetes');
    Route::get('searchExamen', [ExamenesController::class, 'search'])->name('searchExamen');
    Route::post('IdExamen', [ExamenesController::class, 'getId'])->name('IdExamen');
    Route::post('deleteExamen', [ExamenesController::class, 'deleteEx'])->name('deleteExamen');
    Route::post('saveExamen',[ExamenesController::class, 'saveExamen'])->name('saveExamen');
    Route::post('paqueteId', [ExamenesController::class, 'paqueteId'])->name('paqueteId');
    Route::get('porcentajeExamen', [ExamenesController::class, 'porcentajeExamen'])->name('porcentajeExamen');
    Route::get('searchExamenes', [ExamenesController::class, 'searchExamenes'])->name('searchExamenes');
    Route::post('updateExamen', [ExamenesController::class, 'updateExamen'])->name('updateExamen');

    //Ruta de Comentarios de Prestaciones
    Route::post('setComentarioPres', [ComentariosPrestacionesController::class, 'setComentarioPres'])->name('setComentarioPres');
    Route::get('getComentarioPres', [ComentariosPrestacionesController::class, 'getComentarioPres'])->name('getComentarioPres');

    //Rutas de Autorizados
    Route::post('deleteAutorizado', [AutorizadoController::class, 'delete'])->name('deleteAutorizado');
    Route::get('getAutorizados', [AutorizadoController::class, 'getAut'])->name('getAutorizados');
    Route::post('/clientes/altaAutorizado', [AutorizadoController::class, 'alta'])->name('clientes.altaAutorizado');

    //Rutas de Telefonos
    Route::get('getTelefonos', [TelefonosController::class, 'getTelefonos'])->name('getTelefonos');
    Route::post('deleteTelefono', [TelefonosController::class, 'deleteTelefono'])->name('deleteTelefono');
    Route::post('saveTelefono', [TelefonosController::class, 'saveTelefono'])->name('saveTelefono');

    //Rutas de Localidades
    Route::get('searchLocalidad', [LocalidadController::class, 'searchLocalidad'])->name('searchLocalidad');

    //Rutas de Mapas
    Route::resource('mapas', MapasController::class);
    Route::post('updateMapa', [MapasController::class, 'updateMapa'])->name('updateMapa');
    Route::post('deleteMapa', [MapasController::class, 'delete'])->name('deleteMapa');
    Route::get('getRemito', [MapasController::class, 'getRemito'])->name('getRemito');
    Route::get('getMapas', [MapasController::class, 'getMapas'])->name('getMapas');
    Route::get('getPacienteMapa', [MapasController::class, 'getPacienteMapa'])->name('getPacienteMapa');
    Route::get('getExamenMapa', [MapasController::class, 'examenes'])->name('getExamenMapa');
    Route::get('getPrestaciones', [MapasController::class, 'prestaciones'])->name('getPrestaciones');
    Route::get('getCerrar', [MapasController::class, 'getCerrar'])->name('getCerrar');
    Route::get('getFinalizar', [MapasController::class, 'getFinalizar'])->name('getFinalizar');
    Route::get('getFMapa', [MapasController::class, 'getFinalizar'])->name('getFMapa');
    Route::get('searchMapas', [MapasController::class, 'search'])->name('searchMapas');
    Route::post('searchMapaPres', [MapasController::class, 'searchMapaPres'])->name('searchMapaPres');
    Route::get('serchInCerrar', [MapasController::class, 'serchInCerrar'])->name('serchInCerrar');
    Route::get('searchInFinalizar', [MapasController::class, 'searchInFinalizar'])->name('searchInFinalizar');
    Route::get('searchInEnviar', [MapasController::class, 'searchInEnviar'])->name('searchInEnviar');
    Route::post('saveCerrar', [MapasController::class, 'saveCerrar'])->name('saveCerrar');
    Route::post('saveFinalizar', [MapasController::class, 'saveFinalizar'])->name('saveFinalizar');
    Route::post('saveEnviar', [MapasController::class, 'saveEnviar'])->name('saveEnviar');
    Route::post('saveEstado', [MapasController::class, 'saveEstado'])->name('saveEstado');
    Route::post('saveRemitos', [MapasController::class, 'saveRemitos'])->name('saveRemitos');
    Route::get('checkMapa', [MapasController::class, 'checker'])->name('checkMapa');
    Route::post('changeEstado', [MapasController::class, 'changeEstado'])->name('changeEstado');
    Route::get('enviarMapa', [MapasController::class, 'geteEnviar'])->name('enviarMapa');
    Route::get('fileExport', [MapasController::class, 'export'])->name('fileExport');
    Route::post('reverseRemito', [MapasController::class, 'reverseRemito'])->name('reverseRemito');
    
    //Rutas de Profesionales
    Route::resource('profesionales', ProfesionalesController::class);
    Route::get('getEvaluador', [ProfesionalesController::class, 'getEvaluador'])->name('getEvaluador');
    Route::get('searchProfesionales', [ProfesionalesController::class, 'search'])->name('searchProfesionales');
    Route::post('estadoProfesional', [ProfesionalesController::class, 'estado'])->name('estadoProfesional');
    Route::post('setPerfiles', [ProfesionalesController::class, 'setPerfil'])->name('setPerfiles');
    Route::get('getPerfiles', [ProfesionalesController::class, 'getPerfil'])->name('getPerfiles');
    Route::post('delPerfil', [ProfesionalesController::class, 'delPerfil'])->name('delPerfil');
    Route::post('checkDocumento', [ProfesionalesController::class, 'checkDocumento'])->name('checkDocumento');
    Route::post('opcionesProf', [ProfesionalesController::class, 'opciones'])->name('opcionesProf');
    Route::post('seguroProf', [ProfesionalesController::class, 'seguro'])->name('seguroProf');
    Route::get('choisePerfil', [ProfesionalesController::class, 'choisePerfil'])->name('choisePerfil');
    Route::get('choiseEspecialidad', [ProfesionalesController::class, 'choiseEspecialidad'])->name('choiseEspecialidad');
    Route::post('savePrestador', [ProfesionalesController::class, 'savePrestador'])->name('savePrestador');
    Route::get('listGeneral', [ProfesionalesController::class, 'listGeneral'])->name('listGeneral');

    //Rutas de Proveedores
    Route::resource('especialidades', ProveedoresController::class);
    Route::get('getProveedores', [ProveedoresController::class, 'getProveedores'])->name('getProveedores');
    Route::get('searchEspecialidad', [ProveedoresController::class, 'search'])->name('searchEspecialidad');
    Route::get('especialidadExcel', [ProveedoresController::class, 'excel'])->name('especialidadExcel');
    Route::post('multiDownEspecialidad', [ProveedoresController::class, 'multiDown'])->name('multiDownEspecialidad');
    Route::post('bajaEspecialidad', [ProveedoresController::class, 'down'])->name('bajaEspecialidad');
    Route::get('checkProveedor', [ProveedoresController::class, 'check'])->name('checkProveedor');
    Route::post('saveBasico', [ProveedoresController::class, 'save'])->name('saveBasico');
    Route::post('updateProveedor', [ProveedoresController::class, 'updateProveedor'])->name('updateProveedor');
    Route::get('lstProveedores', [ProveedoresController::class, 'lstProveedores'])->name('lstProveedores');

    //Rutas de ItemsPrestaciones
    Route::resource('itemsprestaciones', ItemPrestacionesController::class);
    Route::post('updateItem', [ItemPrestacionesController::class, 'updateItem'])->name('updateItem');
    Route::post('updateAsignado', [ItemPrestacionesController::class, 'updateAsignado'])->name('updateAsignado');
    Route::post('updateAdjunto', [ItemPrestacionesController::class, 'updateAdjunto'])->name('updateAdjunto');
    Route::get('paginacionGeneral', [ItemPrestacionesController::class, 'paginacionGeneral'])->name('paginacionGeneral');
    Route::post('updateItemExamen', [ItemPrestacionesController::class, 'updateExamen'])->name('updateItemExamen');
    Route::post('uploadAdjunto', [ItemPrestacionesController::class, 'uploadAdjunto'])->name('uploadAdjunto');
    Route::get('deleteIdAdjunto', [ItemPrestacionesController::class, 'deleteIdAdjunto'])->name('deleteIdAdjunto');
    Route::post('replaceIdAdjunto', [ItemPrestacionesController::class, 'replaceIdAdjunto'])->name('replaceIdAdjunto');
    Route::post('deleteItemExamen', [ItemPrestacionesController::class, 'deleteEx'])->name('deleteItemExamen');
    Route::post('getItemExamenes', [ItemPrestacionesController::class, 'getExamenes'])->name('getItemExamenes');
    Route::post('saveItemExamenes', [ItemPrestacionesController::class, 'save'])->name('saveItemExamenes');
    Route::get('checkItemExamen', [ItemPrestacionesController::class, 'check'])->name('checkItemExamen');
    Route::post('itemExamen', [ItemPrestacionesController::class, 'itemExamen'])->name('itemExamen');
    Route::post('bloquearItemExamen', [ItemPrestacionesController::class, 'bloquearEx'])->name('bloquearItemExamen');
    Route::post('asignarProfesional', [ItemPrestacionesController::class, 'asignarProfesional'])->name('asignarProfesional');
    Route::get('getBloqueoItemPrestacion', [ItemPrestacionesController::class, 'getBloqueo'])->name('getBloqueoItemPrestacion');
    Route::post('archivosAutomatico', [ItemPrestacionesController::class, 'archivosAutomatico'])->name('archivosAutomatico');
    Route::post('archivosAutomaticoI', [ItemPrestacionesController::class, 'archivosAutomaticoI'])->name('archivosAutomaticoI');
    Route::post('updateEstadoItem', [ItemPrestacionesController::class, 'updateEstadoItem'])->name('updateEstadoItem');
    Route::post('liberarExamen', [ItemPrestacionesController::class, 'liberarExamen'])->name('liberarExamen');
    Route::post('marcarExamenAdjunto', [ItemPrestacionesController::class, 'marcarExamenAdjunto'])->name('marcarExamenAdjunto');

    //Rutas de FacturasdeVenta
    Route::get('getFactura', [FacturasVentaController::class, 'getFactura'])->name('getFactura');

    // Rutas de Noticias
    Route::resource('noticias', NoticiasController::class);
    Route::post('updateNoticia', [NoticiasController::class, 'update'])->name('updateNoticia');

    //Rutas de Observaciones de Fases de Prestaciones
    Route::get('comentariosPriv', [PrestacionesObsFasesController::class, 'comentariosPriv'])->name('comentariosPriv');
    Route::post('savePrivComent', [PrestacionesObsFasesController::class, 'addComentario'])->name('savePrivComent');

    //Rutas de Ordenes de examenes efectores
    Route::resource('ordenesExamen', OrdenesExamenController::class);
    Route::get('seachOrdenesExamen', [OrdenesExamenController::class, 'search'])->name('seachOrdenesExamen');
    Route::get('searchOrExaAsignados', [OrdenesExamenController::class, 'searchA'])->name('searchOrExaAsignados');
    Route::get('searchOrExaAdjunto', [OrdenesExamenController::class, 'searchAdj'])->name('searchOrExaAdjunto');
    Route::get('seachOrExInf', [OrdenesExamenController::class, 'searchInf'])->name('seachOrExInf');
    Route::get('seachOrExAsigInf', [OrdenesExamenController::class, 'searchInfA'])->name('seachOrExAsigInf');
    Route::get('searchOrExaAdjInf', [OrdenesExamenController::class, 'searchInfAdj'])->name('searchOrExaAdjInf');

    //Rutas de Examenes a Cuenta
    Route::resource('examenesCuenta', ExamenesCuentaController::class);
    Route::get('searchExCuenta', [ExamenesCuentaController::class, 'search'])->name('searchExCuenta');
    Route::post('cambiarPago', [ExamenesCuentaController::class, 'cambiarPago'])->name('cambiarPago');
});
