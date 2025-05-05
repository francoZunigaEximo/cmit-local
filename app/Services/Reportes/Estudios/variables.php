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


?>