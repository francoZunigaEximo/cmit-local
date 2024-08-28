<?php

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
use App\Http\Controllers\FileController;
use App\Http\Controllers\MensajesController;
use App\Http\Controllers\NotasCreditoController;
use App\Http\Controllers\PaqueteEstudioController;
use App\Http\Controllers\PaqueteFacturacionController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UsuariosController;
use App\Models\FacturaDeVenta;
//use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;

//Route::get('/password', [AuthController::class, 'testCreate'])->name('password');

Route::group(['middleware' => 'guest'], function () {
    Route::view('/', 'layouts.login')->name('login');
    Route::post('/validate-login', [AuthController::class, 'login'])->name('validate-login');
});

Route::group(['middleware' => 'auth'], function () {

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/perfil', [AuthController::class, 'profile'])->name('perfil');
    Route::post('actualizarPass', [AuthController::class, 'updatePass'])->name('actualizarPass');
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::get('checkPassword', [AuthController::class, 'checkPassword'])->name('checkPassword');
    Route::get('/passw', function (){
        return Hash::make('cmit1234');
    });

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
    Route::get('/pacientes/buscar', [PacientesController::class, 'search'])->name('search');
    Route::post('/pacientes/baja', [PacientesController::class, 'down'])->name('pacientes.down');
    Route::get('/pacientes/verficar-documento', [PacientesController::class, 'verifyDocument'])->name('verify');
    Route::get('/pacientes/excel', [PacientesController::class, 'exportExcel'])->name('excelPacientes');
    Route::get('getPacientes', [PacientesController::class, 'getPacientes'])->name('getPacientes');
    Route::get('searchPrestPacientes', [PacientesController::class, 'searchPrestPacientes'])->name('searchPrestPacientes');
    Route::get('getNombre', [PacientesController::class, 'getNombre'])->name('getNombre');
    Route::post('deletePicture', [PacientesController::class, 'deletePicture'])->name('deletePicture');
    Route::resource('pacientes', PacientesController::class);

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
    Route::get('/prestaciones/baja', [PrestacionesController::class, 'down'])->name('downPrestaActiva');
    Route::get('prestaciones/block', [PrestacionesController::class, 'blockPrestacion'])->name('blockPrestacion');
    Route::get('/prestaciones/buscar', [PrestacionesController::class, 'search'])->name('searchPrestaciones');
    Route::post('/prestaciones/guardar', [PrestacionesController::class, 'savePrestacion'])->name('savePrestacion');
    Route::post('/prestaciones/obtener-para-empresa', [PrestacionesController::class, 'getParaEmpresas'])->name('getParaEmpresas');
    Route::post('/prestaciones/chequear-financiador', [PrestacionesController::class, 'checkFinanciador'])->name('checkFinanciador');
    Route::post('/prestaciones/verificar-bloqueo', [PrestacionesController::class, 'verifyBlock'])->name('verifyBlock');
    Route::post('/prestaciones/obtener-paciente', [PrestacionesController::class, 'getPresPaciente'])->name('getPresPaciente');
    Route::post('/prestaciones/actualizar', [PrestacionesController::class, 'updatePrestacion'])->name('updatePrestacion');
    Route::post('/prestaciones/actualizar-estado', [PrestacionesController::class, 'estados'])->name('actualizarEstados');
    Route::post('/prestaciones/actualizar-vencimiento', [PrestacionesController::class, 'vencimiento'])->name('actualizarVto');
    Route::post('/prestaciones/guardar/evaluador', [PrestacionesController::class, 'setEvaluador'])->name('setEvaluador');
    Route::get('/prestaciones/wizard', [PrestacionesController::class, 'verifyWizard'])->name('verifyWizard');
    Route::get('/prestaciones/excel', [PrestacionesController::class, 'exportExcel'])->name('excelPrestaciones');
    Route::get('/prestaciones/obtener-bloqueo', [PrestacionesController::class, 'getBloqueo'])->name('getBloqueoPrestacion');
    Route::get('lstTipoPrestacion', [PrestacionesController::class, 'lstTipoPrestacion'])->name('lstTipoPrestacion');
    Route::get('buscarEx', [PrestacionesController::class, 'buscarEx'])->name('buscarEx');
    Route::get('/prestaciones/check-incompleto', [PrestacionesController::class, 'checkIncompleto'])->name('prestaciones.checkIncompleto');
    Route::resource('prestaciones', PrestacionesController::class);

    //Ruta Ficha Laboral
    Route::post('saveFichaAlta', [FichaAltaController::class, 'save'])->name('saveFichaAlta');
    Route::get('verificarAlta', [FichaAltaController::class, 'verificar'])->name('verificarAlta');
    Route::get('getTipoPrestacion', [FichaAltaController::class, 'getTipoPrestacion'])->name('getTipoPrestacion');
    Route::get('checkObs', [FichaAltaController::class, 'checkObs'])->name('checkObs');
    Route::get('verFicha', [FichaAltaController::class, 'verFicha'])->name('verFicha');
    Route::resource('fichalaboral', FichaAltaController::class);

    //Ruta Examenes
    Route::resource('examenes', ExamenesController::class);
    Route::get('searchExamen', [ExamenesController::class, 'search'])->name('searchExamen');
    Route::post('IdExamen', [ExamenesController::class, 'getId'])->name('IdExamen');
    Route::post('deleteExamen', [ExamenesController::class, 'deleteEx'])->name('deleteExamen');
    Route::post('saveExamen',[ExamenesController::class, 'saveExamen'])->name('saveExamen');
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
    Route::get('/mapas/excel', [MapasController::class, 'export'])->name('mapas.exportar');
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
    Route::post('reverseRemito', [MapasController::class, 'reverseRemito'])->name('reverseRemito');
    
    //Rutas de Profesionales
    //Route::resource('profesionales', ProfesionalesController::class);
    Route::get('getEvaluador', [ProfesionalesController::class, 'getEvaluador'])->name('getEvaluador');
    Route::get('searchProfesionales', [ProfesionalesController::class, 'search'])->name('searchProfesionales');
    Route::post('estadoProfesional', [ProfesionalesController::class, 'estado'])->name('estadoProfesional');
    Route::post('setPerfiles', [ProfesionalesController::class, 'setPerfil'])->name('setPerfiles');
    Route::get('getPerfiles', [ProfesionalesController::class, 'getPerfil'])->name('getPerfiles');
    Route::post('delPerfil', [ProfesionalesController::class, 'delPerfil'])->name('delPerfil');
    Route::post('checkDocumento', [ProfesionalesController::class, 'checkDocumento'])->name('checkDocumento');
    Route::post('profesionales/opcion/save', [ProfesionalesController::class, 'opciones'])->name('profesionales.opciones');
    Route::post('profesionales/seguro/save', [ProfesionalesController::class, 'seguro'])->name('profesionales.seguro');
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
    Route::post('itemsprestaciones/lista-examenes', [ItemPrestacionesController::class, 'getExamenes'])->name('itemsprestaciones.listadoexamenes');
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
    Route::get('lstExamenes', [ItemPrestacionesController::class, 'lstExamenes'])->name('lstExamenes');
    Route::get('preExamenes', [ItemPrestacionesController::class, 'preExamenes'])->name('preExamenes');

    //Rutas de FacturasdeVenta
    Route::get('getFactura', [FacturasVentaController::class, 'getFactura'])->name('getFactura');
    Route::get('facturas/search', [FacturasVentaController::class, 'search'])->name('facturas.search');
    Route::get('facturas/delete', [FacturasVentaController::class, 'delete'])->name('facturas.delete');
    Route::get('facturas/export', [FacturasVentaController::class, 'export'])->name('facturas.export');
    Route::get('facturas/enviar',[FacturasVentaController::class, 'enviar'])->name('facturas.enviar');
    Route::get('facturas/excel', [FacturasVentaController::class, 'excel'])->name('facturas.excel');
    Route::get('facturas/paginacion/alta',[FacturasVentaController::class, 'paginacionAlta'])->name('facturas.paginacion');
    Route::get('facturas/detalles', [FacturasVentaController::class, 'verDetalle'])->name('facturas.detalle');
    Route::resource('facturas', FacturasVentaController::class);

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
    Route::get('searchPrestacion', [OrdenesExamenController::class, 'searchPrestacion'])->name('searchPrestacion');
    Route::get('exportarOrdExa', [OrdenesExamenController::class, 'exportar'])->name('exportarOrdExa');
    Route::get('searchEenviar', [OrdenesExamenController::class, 'searchEenviar'])->name('searchEenviar');

    //Rutas de Examenes a Cuenta
    Route::resource('examenesCuenta', ExamenesCuentaController::class);
    Route::get('searchExCuenta', [ExamenesCuentaController::class, 'search'])->name('searchExCuenta');
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
    Route::get('exportGeneral', [ExamenesCuentaController::class, 'reporteGeneral'])->name('exportGeneral');
    Route::get('lstExDisponibles', [ExamenesCuentaController::class, 'disponibilidad'])->name('lstExDisponibles');
    Route::get('lstFacturadas', [ExamenesCuentaController::class, 'listadoUltimas'])->name('lstFacturadas');
    Route::get('saldoNoDatatable', [ExamenesCuentaController::class, 'saldoNoDatatable'])->name('saldoNoDatatable');
    Route::get('cantTotalDisponibles', [ExamenesCuentaController::class, 'disponibles'])->name('cantTotalDisponibles');

    //Rutas de Paquete de Estudio
    Route::get('getPaquetes', [PaqueteEstudioController::class, 'paquetes'])->name('getPaquetes');
    Route::post('paqueteId', [PaqueteEstudioController::class, 'paqueteId'])->name('paqueteId');

    //RUtas de Paquete de Facturación
    Route::get('getPaqueteFact', [PaqueteFacturacionController::class, 'paquetes'])->name('getPaqueteFact');

    //Rutas de Paquete usuarios
    Route::get('usuarios/delete', [UsuariosController::class, 'baja'])->name('usuarios.delete');
    Route::resource('usuarios', UsuariosController::class);
    Route::get('searchNombreUsuario', [UsuariosController::class, 'NombreUsuario'])->name('searchNombreUsuario');
    Route::get('searchUsuario', [UsuariosController::class, 'Usuario'])->name('searchUsuario');
    Route::get('buscarUsuario', [UsuariosController::class, 'buscar'])->name('buscarUsuario');
    Route::get('checkUsuario', [UsuariosController::class, 'checkUsuario'])->name('checkUsuario');
    Route::get('checkCorreo', [UsuariosController::class, 'checkCorreo'])->name('checkCorreo');
    Route::get('checkMail', [UsuariosController::class, 'checkMail'])->name('checkMail');
    Route::get('checkEmailUpdate', [UsuariosController::class, 'checkEmailUpdate'])->name('checkEmailUpdate');
    Route::get('bloquearUsuario', [UsuariosController::class, 'bloquear'])->name('bloquearUsuario');
    Route::get('checkTelefono', [UsuariosController::class, 'checkTelefono'])->name('checkTelefono');
    Route::get('cambiarPassUsuario', [UsuariosController::class, 'cambiarPassword'])->name('cambiarPassUsuario');
    Route::post('/usuarios/update/profesional', [UsuariosController::class, 'updateProfesional'])->name('usuarios.updateProfesional');
    Route::get('checkRoles', [UsuariosController::class, 'checkRoles'])->name('checkRoles');

    //Rutas de Roles
    Route::get('searchRol', [RolesController::class, 'listado'])->name('searchRol');
    Route::get('listadoRoles', [RolesController::class, 'paginacion'])->name('listadoRoles');
    Route::get('lstRolAsignados', [RolesController::class, 'asignados'])->name('lstRolAsignados');
    Route::post('addRol', [RolesController::class, 'add'])->name('addRol');
    Route::get('deleteRol', [RolesController::class, 'delete'])->name('deleteRol');

    //Rutas de Personal
    Route::post('actualizarDatos', [DatosController::class, 'save'])->name('actualizarDatos');
   
    //Rutas de Mensajes
    Route::get('mensajes/auditoria', [MensajesController::class, 'auditoria'])->name('mensajes.auditoria');
    Route::get('mensajes/modelos', [MensajesController::class, 'modelos'])->name('mensajes.modelos');
    Route::get('mensajes/modelos/create', [MensajesController::class, 'createModelo'])->name('mensajes.modelos.create');
    Route::get('mensajes/modelos/edit', [MensajesController::class, 'editModelo'])->name('mensajes.modelos.edit');
    Route::get('mensajes/modelos/delete', [MensajesController::class, 'deleteModelo'])->name('mensajes.modelos.delete');
    Route::post('mensajes/modelos/save', [MensajesController::class, 'saveModelo'])->name('mensajes.modelos.save');
    Route::post('mensajes/modelos/update', [MensajesController::class, 'actualizarModelo'])->name('mensajes.modelos.update');
    Route::resource('mensajes', MensajesController::class);
    Route::get('searchMensaje', [MensajesController::class, 'search'])->name('searchMensaje');
    Route::post('updateEmail', [MensajesController::class, 'updateEmail'])->name('updateEmail');
    Route::get('loadModelos', [MensajesController::class, 'loadModelos'])->name('loadModelos');
    Route::get('loadMensaje', [MensajesController::class, 'loadMensaje'])->name('loadMensaje');
    Route::get('verAuditoria', [MensajesController::class, 'verAuditoria'])->name('verAuditoria');
    Route::get('mensajes/search', [MensajesController::class, 'search'])->name('mensajes.search');
    Route::get('sendEmails', [MensajesController::class, 'sendEmails'])->name('sendEmails');
    Route::get('testEmail', [MensajesController::class, 'testEmail'])->name('testEmail');

    //Rutas de Notas de Crédito
    Route::get('nota-de-credito/check', [NotasCreditoController::class, 'checkNotaCredito'])->name('nota-de-credito.check');

    //Rutas SMB
    Route::get('/files/{filePath}', [FileController::class, 'show'])->where('filePath', '.*');
});

