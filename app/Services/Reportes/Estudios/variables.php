<?php
use App\Models\Cliente;
use App\Models\DatoPaciente;
use App\Models\Fichalaboral;
use App\Models\Localidad;
use App\Models\Prestacion;
use App\Models\Provincia;
use App\Models\Telefono;
use App\Models\Examen;

include_once 'funciones.php';

$prestacion = new Prestacion();
$examen = new Examen(   );
$datosPaciente = new DatoPaciente();
$telefonoPaciente = new Telefono();

if($vistaPrevia){
    //modificamos los valores par la vista previa
    $ide = '000000';
    $paraempresa = 'Empresa de Prueba';

    $ide = str_pad( 0, 6, "0", STR_PAD_LEFT);
    $rsempresa = 'Empresa de Prueba';
    $actividad = 'Actividad de Prueba';
    $cuit = '20-12345678-9';
    $domie = 'Direccion de Prueba';
    $loce = 'Localidad de Prueba';
    $cpe = '1234';
    $pciae = 'Provincia de Prueba';
    $rf = 'RF de Prueba';
    $art = 'ART de Prueba';
    $ida = str_pad( 123456, 6, "0", STR_PAD_LEFT);
    $telempre = '123456789';
    $mailempre = 'empresa@prueba.com';  
    $idpac = '00000000';
    $paciente = 'Paciente de Prueba';
    $apellido = 'Apellido de Prueba';
    $nombre = 'Nombre de Prueba';
    $cuil = '20-12345678-9';
    $doc = '12345678';
    $tipodoc = 'DNI';   
    $edad = '30';
    $ec = 'Soltero';
    $obsec = 'Observaciones de Prueba';
    $sexo = 'Masculino';
    $fechanac = '01-01-1990';
    $lugarnac = 'Lugar de Nacimiento de Prueba';
    $nac = 'Argentino';
    $locpac = 'Localidad de Prueba';
    $cp = '1234';
    $domipac = 'Direccion de Prueba';
    $pcia = 'Provincia de Prueba';
    $puestoestudio = 'Puesto de Estudio de Prueba';
    $foto = public_path("/archivos/reportes/foto_prueba.jpg");
    $puesto = 'Puesto de Prueba';
    $sector = 'Sector de Prueba';
    $tareas = 'Tareas de Prueba';
    $tareasempanterior = 'Tareas de Empresa Anterior de Prueba';
    $oj = 'Observaciones de Jornada de Prueba';
    $antigpto = 'Antiguedad en el Puesto de Prueba';
    $antig = '01-01-2020';
    $fi = '01-01-2020';
    $fe = '01-01-2021';
    $jor = 'Jornada de Prueba';
    $obsjor = 'Observaciones de Jornada de Prueba';
    $obsfl = 'Observaciones de Ficha Laboral de Prueba';
    $telpac = '(123) 456-7890';
    $fecha = '01-01-2021';
    $anio = '2021';
    $nombreExamen = 'Examen de Prueba';
    $idp = '00000000';
    $foto = public_path("/archivos/reportes/foto_prueba.jpg");
    $localidad = new Localidad();
    $localidad->Nombre = 'Localidad de Prueba';
    $localidad->CP = '1234';
    $provinciaPaciente = new Provincia();
    $provinciaPaciente->Nombre = 'Provincia de Prueba';
    $localidadCliente = new Localidad();
    $localidadCliente->Nombre = 'Localidad de Prueba';
    $localidadCliente->CP = '1234';
    $provinciaCliente = new Provincia();
    $provinciaCliente->Nombre = 'Provincia de Prueba';
    $Art = new Cliente();
    $Art->RazonSocial = 'ART de Prueba';
    $Art->Id = 123456;
    $Art->RazonSocial = 'ART de Prueba';
    $Art->Id = 123456;
    $Art->cuit = '20-12345678-9';
    $Art->Id = 123456;
    $Art->Telefono = '123456789';
    $Art->Email = 'empresa@prueba.com';
    $Art->DireccionE = 'Direccion de Prueba';
    $Art->CP = '1234';
    $Art->RF = 'RF de Prueba';
    $Art->IdLocalidad = 1; // Asignar un ID de localidad de prueba
    $Art->IdPcia = 1; // Asignar un ID de provincia de prueba
    $Art->Id = 123456;

    $cliente = new Cliente();
    $cliente->IdEmpresa = 123456;
    $cliente->RazonSocial = 'Empresa de Prueba';
    $cliente->Actividad = 'Actividad de Prueba';
    $cliente->cuit = '20-12345678-9';   
    $cliente->DireccionE = 'Direccion de Prueba';
    $cliente->CP = '1234';
    $cliente->IdLocalidad = 1; // Asignar un ID de localidad de prueba
    $cliente->IdPcia = 1; // Asignar un ID de provincia de prueba   
    $cliente->RF = 'RF de Prueba';
    $cliente->Telefono = '123456789';
    $cliente->Email = 'empresa@prueba.com';
    $cliente->Id = 123456; // Asignar un ID de cliente de prueba
    $cliente->IdART = 123456; // Asignar un ID de ART de prueba
    $cliente->Id = 123456; // Asignar un ID de cliente de prueba
    $prestacion->TipoPrestacion = 'PERIODICO'; // Asignar un tipo de prestacion de prueba
    $prestacion->Fecha = '01-01-2021'; // Asignar una fecha de prestacion de prueba
    $prestacion->Id = 12345678; // Asignar un ID de prestacion de prueba
    $prestacion->paciente = new \stdClass();
    $prestacion->paciente->Id = 12345678; // Asignar un ID de paciente de prueba
    $prestacion->paciente->Apellido = 'Apellido de Prueba';
    $prestacion->paciente->Nombre = 'Nombre de Prueba';
    $prestacion->paciente->Identificacion = '20-12345678-9';
    $prestacion->paciente->Documento = '12345678';
    $prestacion->paciente->TipoDocumento = 'DNI';
    $prestacion->paciente->FechaNacimiento = '01-01-1990';
    $prestacion->paciente->LugarNacimiento = 'Lugar de Nacimiento de Prueba';
    $prestacion->paciente->Sexo = 'Masculino';
    $prestacion->paciente->EstadoCivil = 'Soltero';
    $prestacion->paciente->ObsEC = 'Observaciones de Estado Civil de Prueba';
    $prestacion->paciente->Hijos = 0;
    $prestacion->paciente->Direccion = 'Direccion de Prueba';
    $prestacion->paciente->IdLocalidad = 1; // Asignar un ID de localidad de prueba
    $prestacion->paciente->IdProvincia = 1; // Asignar un ID de provincia de prueba
    $prestacion->paciente->Foto = 'foto_prueba.jpg'; // Asignar una foto de prueba
    $prestacion->paciente->IdPaciente = 12345678; // Asignar un ID de paciente de prueba
    $prestacion->paciente->Telefono = new \stdClass();
    $prestacion->paciente->Telefono->CodigoArea = '123';
    $prestacion->paciente->Telefono->NumeroTelefono = '4567890';
    $prestacion->paciente->Id = 12345678; // Asignar un ID de paciente de prueba
    $prestacion->Id = 12345678; // Asignar un ID de prestacion de prueba
    $prestacion->empresa = new \stdClass();
    $prestacion->empresa->IdEmpresa = 123456; // Asignar un ID de empresa de prueba
    $prestacion->empresa->ParaEmpresa = 'Empresa de Prueba';
    $prestacion->empresa->IdLocalidad = 1; // Asignar un ID de localidad de prueba
    $prestacion->empresa->IdPcia = 1; // Asignar un ID de provincia de prueba
    $prestacion->empresa->RazonSocial = 'Empresa de Prueba';
    $prestacion->empresa->Actividad = 'Actividad de Prueba';
    $prestacion->empresa->cuit = '20-12345678-9';
    $prestacion->empresa->DireccionE = 'Direccion de Prueba';
    $prestacion->empresa->CP = '1234';
    $prestacion->empresa->RF = 'RF de Prueba';
    $prestacion->empresa->Telefono = '123456789';
    $prestacion->empresa->Email = 'empresa@prueba.com';
    $prestacion->IdART = 123456; // Asignar un ID de ART de prueba
    $prestacion->empresa = $cliente;
    $prestacion->paciente->Id = 12345678; // Asignar un ID de paciente de prueba
}else{
$prestacion = $this->prestacion($datos['id']);
$examen = Examen::where('Id', $datos['idExamen'])->first();
$datosPaciente = $this->datosPaciente($prestacion->paciente->Id);
$telefonoPaciente = $this->telefono($prestacion->paciente->Id);
$paciente = $prestacion->paciente->Apellido.' '.$prestacion->paciente->Nombre;
$localidad = $this->localidad($prestacion->paciente->IdLocalidad) ?? '';
$provinciaPaciente = Provincia::where('Id', $localidad->IdPcia)->first();
$fichaLaboral = Fichalaboral::where('IdPaciente', $prestacion->paciente->Id)->first();
$cliente = $prestacion->empresa;

$localidadCliente = Localidad::where('Id', $cliente->IdLocalidad)->first();
$provinciaCliente = Provincia::where('Id', $localidadCliente->IdPcia)->first();

$Art = Cliente::where('Id', $prestacion->IdART)->first();

$tipo = $prestacion->TipoPrestacion;
$fechal = LugarFechaLargo(date('d/m/Y'));
$idp = str_pad($prestacion->Id, 8, "0", STR_PAD_LEFT);

//empresa
$paraempresa = $prestacion->empresa->ParaEmpresa;
$ide = str_pad( $prestacion->empresa->IdEmpresa, 6, "0", STR_PAD_LEFT);
$rsempresa =  $prestacion->empresa->RazonSocial;
$actividad =  $prestacion->empresa->Actividad;
$cuit =  $prestacion->empresa->cuit;
$domie = $cliente->DireccionE;
$loce =  $localidadCliente->Nombre;
$cpe =  $localidadCliente->CP;
$cpempre = $cliente->CP;
$pciae =  $provinciaCliente->Nombre;
$rf =  $cliente->RF;
$art =  $Art->RazonSocial;
$ida = str_pad( $Art->Id, 6, "0", STR_PAD_LEFT);
$telempre = $cliente->Telefono;
$mailempre = $cliente->Email;

//paciente

$idpac = $prestacion->paciente->IdPaciente;
$paciente = $paciente;
$apellido = $prestacion->paciente->Apellido;
$nombre = $prestacion->paciente->Nombre;
$cuil = $prestacion->paciente->Identificacion;
$doc = $prestacion->paciente->Documento;
$tipodoc = $prestacion->paciente->TipoDocumento;
$edad = $this->edad($prestacion->paciente->FechaNacimiento);
$ec = $prestacion->paciente->EstadoCivil;
$obsec = $prestacion->paciente->ObsEC;
$hijos = $prestacion->paciente->Hijos;
if ($prestacion->paciente->Sexo == 'F') {
    $sexo = "Femenino";
} else {
    $sexo = "Masculino";
}
$fechanac = $prestacion->paciente->FechaNacimiento;
$lugarnac = $prestacion->paciente->LugarNacimiento;
$nac = $prestacion->paciente->Nacionalidad;

if ($prestacion->paciente->Foto != '') {
    $foto = public_path("/archivos/fotos/" . $prestacion->paciente->Foto) ;
} else {
    $foto = '';
}
//datos extras
$locpac = $localidad->Nombre;
$cp = $localidad->CP;
$domipac = $prestacion->paciente->Direccion;
$pcia = $provinciaPaciente->Nombre;
//puesto

$puesto = $datosPaciente ? $datosPaciente->Puesto : "";
$sector = $datosPaciente ? $datosPaciente->Sector : "";
$tareas = $datosPaciente ? $datosPaciente->Tareas : "";
$tareasea = $datosPaciente ? $datosPaciente->TareasEmpAnterior : "";
$oj = $datosPaciente ? $datosPaciente->ObsJornada : "" ;
$antigpto = $datosPaciente ? $datosPaciente->AntigPuesto : "";
$antig = $datosPaciente ? $datosPaciente->FechaIngreso : "";
if ($antig == '00-00-0000') {
    $antig = "";
    $fi = "";
} else {
    $fi = $antig;
    $antig = $datosPaciente ? $datosPaciente->FechaIngreso : "";
}
$fe = $datosPaciente ? $datosPaciente->FechaEgreso : "";
if ($fe == '00-00-0000') {
    $fe = "";
}
$jor = $datosPaciente ? $datosPaciente->TipoJornada . ' ' . $datosPaciente->Jornada : "";
$obsjor = $datosPaciente ? $datosPaciente->ObsJornada : "";
//ficha laboral
$obsfl = $fichaLaboral->Observaciones;
$telpac = "(".$telefonoPaciente->CodigoArea.") ".$telefonoPaciente->NumeroTelefono;
$fecha = $prestacion->Fecha;

$anio = date('Y');

list($a,$m,$d)=explode("-",$fecha);

$nombreExamen = $examen == null? "" : $examen->Nombre;


$puestoestudio = '';
if ($tipo == 'PERIODICO' or $tipo == 'EGRESO') {
	$puestoestudio = $puesto;
}
if ($tipo == 'INGRESO' or $tipo == 'OCUPACIONAL') {
	$puestoestudio = $tareas;
}
}
?>