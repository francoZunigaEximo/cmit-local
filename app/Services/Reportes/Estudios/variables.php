<?php
use App\Models\Cliente;
use App\Models\DatoPaciente;
use App\Models\Fichalaboral;
use App\Models\Localidad;
use App\Models\Prestacion;
use App\Models\Provincia;
use App\Models\Telefono;
use App\Models\Examen;

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
$puesto = $datosPaciente->Puesto;
$sector = $datosPaciente->Sector;
$tareas = $datosPaciente->Tareas;
$tareasea = $datosPaciente->TareasEmpAnterior;
$oj = $datosPaciente->ObsJornada;
$antigpto = $datosPaciente->AntigPuesto;
$antig = $datosPaciente->FechaIngreso;
if ($antig == '00-00-0000') {
    $antig = "";
    $fi = "";
} else {
    $fi = $antig;
    $antig = $datosPaciente->FechaIngreso;
}
$fe = $datosPaciente->FechaEgreso;
if ($fe == '00-00-0000') {
    $fe = "";
}
$jor = $datosPaciente->TipoJornada . ' ' . $datosPaciente->Jornada;
$obsjor = $datosPaciente->ObsJornada;
//ficha laboral
$obsfl = $fichaLaboral->Observaciones;
$telpac = "(".$telefonoPaciente->CodigoArea.") ".$telefonoPaciente->NumeroTelefono;
$fecha = date('d/m/Y');
$anio = date('Y');

list($d,$m,$a)=explode("/",$fecha);

$nombreExamen = $examen->Nombre;


function LugarFechaLargo($fecha){ 
	list($d,$m,$a)=explode("/",$fecha);
	switch ($m) {
		case 1:	$mes="Enero";break;
		case 2:	$mes="Febrero";break;
		case 3:	$mes="Marzo";break;
		case 4:	$mes="Abril";break;
		case 5:	$mes="Mayo";break;	
		case 6:	$mes="Junio";break;	
		case 7:	$mes="Julio";break;	
		case 8:	$mes="Agosto";break;	
		case 9:	$mes="Septiembre";break;	
		case 10:$mes="Octubre";break;	
		case 11:$mes="Noviembre";break;	
		case 12:$mes="Diciembre";break;	
	}		
	return "Neuquen, $d de $mes de $a";
}
?>