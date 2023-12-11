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
use Illuminate\Support\Facades\Redis;
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
    Route::post('getLocalidades', [UtilityController::class, 'getLocalidades'])->name('getLocalidades');
    Route::post('getCodigoPostal', [UtilityController::class, 'getCodigoPostal'])->name('getCodigoPostal');
    Route::post('checkProvincia', [UtilityController::class, 'checkProvincia'])->name('checkProvincia');

    //Rutas de Pacientes
    Route::resource('pacientes', PacientesController::class);
    Route::get('searchPacientes', [PacientesController::class, 'search'])->name('search');
    Route::post('down', [PacientesController::class, 'down'])->name('down');
    Route::get('/verifydocument', [PacientesController::class, 'verifyDocument'])->name('verify');
    Route::post('/pacientes/multiple-down', [PacientesController::class, 'multipleDown'])->name('pacientes.multipleDown');
    Route::post('excelPacientes', [PacientesController::class, 'exportExcel'])->name('excelPacientes');
    Route::get('getPacientes', [PacientesController::class, 'getPacientes'])->name('getPacientes');
    Route::post('searchPrestPacientes', [PacientesController::class, 'searchPrestPacientes'])->name('searchPrestPacientes');
    Route::get('updateFinanciador', [PacientesController::class, 'updateFinanciador'])->name('updateFinanciador');
    Route::get('getNombre', [PacientesController::class, 'getNombre'])->name('getNombre');
    Route::post('deletePicture', [PacientesController::class, 'deletePicture'])->name('deletePicture');

    //Rutas de Clientes
    Route::resource('clientes', ClientesController::class);
    Route::post('baja', [ClientesController::class, 'baja'])->name('baja');
    Route::post('clientes/multipleDown', [ClientesController::class, 'multipleDown'])->name('clientes.multipleDown');
    Route::post('clientes/blockCliente', [ClientesController::class, 'block'])->name('clientes.block');
    Route::get('searchClientes', [ClientesController::class, 'search'])->name('searchClientes');
    Route::post('/verifycuitEmpresa', [ClientesController::class, 'verifyCuitEmpresa'])->name('verifycuitEmpresa');
    Route::post('/codigopostal', [PacientesController::class, 'getCodigoPostal'])->name('getCPostal');
    Route::get('getClientes', [ClientesController::class, 'getClientes'])->name('getClientes');
    Route::post('/clientes/setObservaciones', [ClientesController::class, 'setObservaciones'])->name('clientes.setObservaciones');
    Route::post('checkEmail', [ClientesController::class, 'checkEmail'])->name('checkEmail');
    Route::post('checkOpciones', [ClientesController::class, 'checkOpciones'])->name('checkOpciones');
    Route::post('verifyIdentificacion', [ClientesController::class, 'verifyIdentificacion'])->name('verifyIdentificacion');
    Route::post('exportExcelClientes', [ClientesController::class, 'excel'])->name('exportExcelClientes');
    Route::get('checkParaEmpresa', [ClientesController::class, 'checkParaEmpresa'])->name('checkParaEmpresa');

    //Rutas de Prestaciones
    Route::resource('prestaciones', PrestacionesController::class);
    Route::get('downPrestaActiva', [PrestacionesController::class, 'down'])->name('downPrestaActiva');
    Route::get('blockPrestacion', [PrestacionesController::class, 'blockPrestacion'])->name('blockPrestacion');
    Route::get('searchPrestaciones', [PrestacionesController::class, 'search'])->name('searchPrestaciones');
    Route::post('savePrestacion', [PrestacionesController::class, 'savePrestacion'])->name('savePrestacion');
    Route::post('getParaEmpresas', [PrestacionesController::class, 'getParaEmpresas'])->name('getParaEmpresas');
    Route::post('checkFinanciador', [PrestacionesController::class, 'checkFinanciador'])->name('checkFinanciador');
    Route::post('getPago', [PrestacionesController::class, 'getPago'])->name('getPago');
    Route::post('verifyBlock', [PrestacionesController::class, 'verifyBlock'])->name('verifyBlock');
    Route::post('getPresPaciente', [PrestacionesController::class, 'getPresPaciente'])->name('getPresPaciente');
    Route::post('updatePrestacion', [PrestacionesController::class, 'updatePrestacion'])->name('updatePrestacion');
    Route::post('actualizarEstados', [PrestacionesController::class, 'estados'])->name('actualizarEstados');
    Route::post('actualizarVto', [PrestacionesController::class, 'vencimiento'])->name('actualizarVto');
    Route::post('setEvaluador', [PrestacionesController::class, 'setEvaluador'])->name('setEvaluador');
    Route::get('verifyWizard', [PrestacionesController::class, 'verifyWizard'])->name('verifyWizard');
    Route::get('excelPrestaciones', [PrestacionesController::class, 'exportExcel'])->name('excelPrestaciones');

    //Ruta Ficha Laboral
    Route::post('saveFichaAlta', [FichaAltaController::class, 'save'])->name('saveFichaAlta');
    Route::get('verificarAlta', [FichaAltaController::class, 'verificar'])->name('verificarAlta');
    Route::get('getTipoPrestacion', [FichaAltaController::class, 'getTipoPrestacion'])->name('getTipoPrestacion');
    Route::get('checkObs', [FichaAltaController::class, 'checkObs'])->name('checkObs');

    //Ruta Examenes
    Route::post('getExamenes', [ExamenesController::class, 'getExamenes'])->name('getExamenes');
    Route::post('saveExamenes', [ExamenesController::class, 'save'])->name('saveExamenes');
    Route::post('checkExamen', [ExamenesController::class, 'check'])->name('checkExamen');
    Route::get('getPaquetes', [ExamenesController::class, 'paquetes'])->name('getPaquetes');
    Route::get('searchExamen', [ExamenesController::class, 'search'])->name('searchExamen');
    Route::post('IdExamen', [ExamenesController::class, 'getId'])->name('IdExamen');
    Route::post('deleteExamen', [ExamenesController::class, 'deleteEx'])->name('deleteExamen');
    Route::post('bloquearExamen', [ExamenesController::class, 'bloquearEx'])->name('bloquearExamen');
    Route::post('paqueteId', [ExamenesController::class, 'paqueteId'])->name('paqueteId');
    Route::post('itemExamen', [ExamenesController::class, 'itemExamen'])->name('itemExamen');

    //Ruta de Comentarios de Prestaciones
    Route::post('setComentarioPres', [ComentariosPrestacionesController::class, 'setComentarioPres'])->name('setComentarioPres');
    Route::post('getComentarioPres', [ComentariosPrestacionesController::class, 'getComentarioPres'])->name('getComentarioPres');

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
    Route::get('searchMapas', [MapasController::class, 'search'])->name('searchMapas');
    Route::post('deleteMapa', [MapasController::class, 'delete'])->name('deleteMapa');
    Route::post('exportExcelMapas', [MapasController::class, 'excel'])->name('exportExcelMapas');
    Route::get('getMapas', [MapasController::class, 'getMapas'])->name('getMapas');
    Route::post('mapasPdf', [MapasController::class, 'pdf'])->name('mapasPdf');
    Route::post('updateMapa', [MapasController::class, 'updateMapa'])->name('updateMapa'); 
    Route::post('saveRemitos', [MapasController::class, 'saveRemitos'])->name('saveRemitos');
    Route::post('searchMapaPres', [MapasController::class, 'searchMapaPres'])->name('searchMapaPres');
    Route::get('getPacienteMapa', [MapasController::class, 'getPacienteMapa'])->name('getPacienteMapa');
    Route::get('getExamenMapa', [MapasController::class, 'examenes'])->name('getExamenMapa');
    Route::get('getCerrarMapa', [MapasController::class, 'cerrar'])->name('getCerrarMapa');
    Route::get('getFinalizarMapa', [MapasController::class, 'finalizar'])->name('getFinalizarMapa');
    Route::post('saveCerrar', [MapasController::class, 'saveCerrar'])->name('saveCerrar');
    Route::post('saveFinalizar', [MapasController::class, 'saveFinalizar'])->name('saveFinalizar');
    Route::get('getEnviarMapa', [MapasController::class, 'eEnviar'])->name('getEnviarMapa');
    Route::post('saveEnviar', [MapasController::class, 'saveEnviar'])->name('saveEnviar');
    Route::post('saveEstado', [MapasController::class, 'saveEstado'])->name('saveEstado');
    Route::get('checkMapa', [MapasController::class, 'checker'])->name('checkMapa'); 
    Route::get('getPrestaciones', [MapasController::class, 'prestaciones'])->name('getPrestaciones');
    Route::get('getCerrar', [MapasController::class, 'getCerrar'])->name('getCerrar');
    Route::get('getFinalizar', [MapasController::class, 'getFinalizar'])->name('getFinalizar');
    Route::get('getFMapa', [MapasController::class, 'getFinalizar'])->name('getFMapa');
    Route::get('enviarMapa', [MapasController::class, 'geteEnviar'])->name('enviarMapa');

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

    //Rutas de Proveedores
    Route::resource('especialidades', ProveedoresController::class);
    Route::get('getProveedores', [ProveedoresController::class, 'getProveedores'])->name('getProveedores');
    Route::get('searchEspecialidad', [ProveedoresController::class, 'search'])->name('searchEspecialidad');
    Route::post('especialidadExcel', [ProveedoresController::class, 'excel'])->name('especialidadExcel');
    Route::post('multiDownEspecialidad', [ProveedoresController::class, 'multiDown'])->name('multiDownEspecialidad');
    Route::post('bajaEspecialidad', [ProveedoresController::class, 'down'])->name('bajaEspecialidad');
    Route::get('checkProveedor', [ProveedoresController::class, 'check'])->name('checkProveedor');
    Route::post('saveBasico', [ProveedoresController::class, 'save'])->name('saveBasico');
    Route::post('updateProveedor', [ProveedoresController::class, 'updateProveedor'])->name('updateProveedor');

    //Rutas de ItemsPrestaciones
    Route::resource('itemsprestaciones', ItemPrestacionesController::class);
    Route::post('updateItem', [ItemPrestacionesController::class, 'updateItem'])->name('updateItem');
    Route::post('updateAsignado', [ItemPrestacionesController::class, 'updateAsignado'])->name('updateAsignado');
    Route::get('listGeneral', [ItemPrestacionesController::class, 'listGeneral'])->name('listGeneral');
    Route::post('updateAdjunto', [ItemPrestacionesController::class, 'updateAdjunto'])->name('updateAdjunto');
    Route::get('paginacionGeneral', [ItemPrestacionesController::class, 'paginacionGeneral'])->name('paginacionGeneral');
    Route::post('updateExamen', [ItemPrestacionesController::class, 'updateExamen'])->name('updateExamen');
    Route::post('uploadAdjunto', [ItemPrestacionesController::class, 'uploadAdjunto'])->name('uploadAdjunto');
    Route::get('deleteIdAdjunto', [ItemPrestacionesController::class, 'deleteIdAdjunto'])->name('deleteIdAdjunto');


    //Rutas de FacturasdeVenta
    Route::get('getFactura', [FacturasVentaController::class, 'getFactura'])->name('getFactura');

});
