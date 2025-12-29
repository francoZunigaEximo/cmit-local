-- MySQL dump 10.13  Distrib 5.5.62, for Linux (x86_64)
--
-- Host: localhost    Database: db_cmit
-- ------------------------------------------------------
-- Server version	5.5.62-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `actividades`
--

DROP TABLE IF EXISTS `actividades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `actividades` (
  `Id` int(11) NOT NULL,
  `Nombre` char(30) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Nombre` (`Nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `agenda`
--

DROP TABLE IF EXISTS `agenda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agenda` (
  `Id` int(11) NOT NULL,
  `Fecha` date NOT NULL DEFAULT '0000-00-00',
  `Hora` time NOT NULL DEFAULT '00:00:00',
  `Asunto` char(50) NOT NULL,
  `Detalle` text NOT NULL,
  `Obs` text NOT NULL,
  `Usuario` char(20) NOT NULL,
  `Estado` tinyint(4) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `Usuario` (`Usuario`),
  CONSTRAINT `agenda_ibfk_1` FOREIGN KEY (`Usuario`) REFERENCES `usuarios` (`Usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `agenda_dest`
--

DROP TABLE IF EXISTS `agenda_dest`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agenda_dest` (
  `Id` int(11) NOT NULL,
  `IdAgenda` int(11) NOT NULL,
  `Usuario` char(20) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `IdAgenda_2` (`IdAgenda`,`Usuario`),
  KEY `IdAgenda` (`IdAgenda`),
  KEY `Usuario` (`Usuario`),
  CONSTRAINT `agenda_dest_ibfk_3` FOREIGN KEY (`IdAgenda`) REFERENCES `agenda` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `agenda_dest_ibfk_4` FOREIGN KEY (`Usuario`) REFERENCES `usuarios` (`Usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `archivosefector`
--

DROP TABLE IF EXISTS `archivosefector`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `archivosefector` (
  `Id` int(11) NOT NULL,
  `IdEntidad` int(11) NOT NULL,
  `Descripcion` char(50) NOT NULL,
  `Ruta` char(50) NOT NULL,
  `IdPrestacion` int(11) NOT NULL,
  `Tipo` tinyint(1) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdEntidad` (`IdEntidad`),
  CONSTRAINT `archivosefector_ibfk_1` FOREIGN KEY (`IdEntidad`) REFERENCES `itemsprestaciones` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `archivosinformador`
--

DROP TABLE IF EXISTS `archivosinformador`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `archivosinformador` (
  `Id` int(11) NOT NULL,
  `IdEntidad` int(11) NOT NULL,
  `Descripcion` char(50) NOT NULL,
  `Ruta` char(50) NOT NULL,
  `IdPrestacion` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdEntidad` (`IdEntidad`),
  CONSTRAINT `archivosinformador_ibfk_1` FOREIGN KEY (`IdEntidad`) REFERENCES `itemsprestaciones` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `archivosprestacion`
--

DROP TABLE IF EXISTS `archivosprestacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `archivosprestacion` (
  `Id` int(11) NOT NULL,
  `IdEntidad` int(11) NOT NULL,
  `Descripcion` char(50) NOT NULL,
  `Ruta` char(50) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdEntidad` (`IdEntidad`),
  CONSTRAINT `archivosprestacion_ibfk_1` FOREIGN KEY (`IdEntidad`) REFERENCES `prestaciones` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auditoria`
--

DROP TABLE IF EXISTS `auditoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auditoria` (
  `Id` int(11) NOT NULL,
  `IdTabla` int(11) NOT NULL DEFAULT '0',
  `IdAccion` int(11) NOT NULL DEFAULT '0',
  `IdRegistro` int(11) NOT NULL DEFAULT '0',
  `IdUsuario` char(20) NOT NULL,
  `Fecha` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`Id`),
  KEY `IdUsuario` (`IdUsuario`),
  KEY `IdTabla` (`IdTabla`),
  KEY `IdAccion` (`IdAccion`),
  CONSTRAINT `auditoria_ibfk_1` FOREIGN KEY (`IdTabla`) REFERENCES `auditoriatablas` (`Id`),
  CONSTRAINT `auditoria_ibfk_2` FOREIGN KEY (`IdAccion`) REFERENCES `auditoriaacciones` (`Id`),
  CONSTRAINT `auditoria_ibfk_3` FOREIGN KEY (`IdUsuario`) REFERENCES `usuarios` (`Usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auditoriaacciones`
--

DROP TABLE IF EXISTS `auditoriaacciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auditoriaacciones` (
  `Id` int(11) NOT NULL,
  `Nombre` char(30) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Nombre` (`Nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auditoriaagenda`
--

DROP TABLE IF EXISTS `auditoriaagenda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auditoriaagenda` (
  `Id` int(11) NOT NULL,
  `IdAccion` int(11) NOT NULL DEFAULT '0',
  `IdRegistro` int(11) NOT NULL DEFAULT '0',
  `IdUsuario` char(20) NOT NULL,
  `Fecha` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`Id`),
  KEY `IdUsuario` (`IdUsuario`),
  KEY `IdAccion` (`IdAccion`),
  KEY `IdRegistro` (`IdRegistro`),
  CONSTRAINT `auditoriaagenda_ibfk_1` FOREIGN KEY (`IdAccion`) REFERENCES `auditoriaacciones` (`Id`),
  CONSTRAINT `auditoriaagenda_ibfk_3` FOREIGN KEY (`IdUsuario`) REFERENCES `usuarios` (`Usuario`),
  CONSTRAINT `auditoriaagenda_ibfk_4` FOREIGN KEY (`IdRegistro`) REFERENCES `agenda` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auditoriaeventos`
--

DROP TABLE IF EXISTS `auditoriaeventos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auditoriaeventos` (
  `Id` int(11) NOT NULL,
  `Fecha` date NOT NULL DEFAULT '0000-00-00',
  `Obs` text NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auditoriamailsfact`
--

DROP TABLE IF EXISTS `auditoriamailsfact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auditoriamailsfact` (
  `Id` int(11) NOT NULL,
  `Fecha` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Destinatarios` text NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auditoriamailsmasivos`
--

DROP TABLE IF EXISTS `auditoriamailsmasivos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auditoriamailsmasivos` (
  `Id` int(11) NOT NULL,
  `Fecha` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Asunto` text NOT NULL,
  `Detalle` text NOT NULL,
  `Destinatarios` text NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auditoriamailsprest`
--

DROP TABLE IF EXISTS `auditoriamailsprest`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auditoriamailsprest` (
  `Id` int(11) NOT NULL,
  `Fecha` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Destinatarios` text NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auditoriatablas`
--

DROP TABLE IF EXISTS `auditoriatablas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auditoriatablas` (
  `Id` int(11) NOT NULL,
  `Nombre` char(30) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `autorizados`
--

DROP TABLE IF EXISTS `autorizados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `autorizados` (
  `Id` int(11) NOT NULL,
  `IdEntidad` int(11) NOT NULL DEFAULT '0',
  `Nombre` char(30) NOT NULL,
  `Apellido` char(30) NOT NULL,
  `DNI` char(13) NOT NULL,
  `Derecho` char(50) NOT NULL,
  `TipoEntidad` char(1) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdEntidad` (`IdEntidad`),
  KEY `Apellido` (`Apellido`),
  CONSTRAINT `autorizados_ibfk_1` FOREIGN KEY (`IdEntidad`) REFERENCES `clientes` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientes` (
  `Id` int(11) NOT NULL,
  `RazonSocial` char(100) NOT NULL,
  `Nacionalidad` char(30) NOT NULL,
  `CondicionIva` char(30) NOT NULL,
  `TipoIdentificacion` char(10) NOT NULL,
  `Identificacion` char(13) NOT NULL,
  `Observaciones` text NOT NULL,
  `TipoPersona` char(1) NOT NULL,
  `Envio` tinyint(1) NOT NULL DEFAULT '0',
  `Entrega` tinyint(1) NOT NULL DEFAULT '0',
  `ParaEmpresa` char(100) NOT NULL,
  `IdActividad` int(11) NOT NULL DEFAULT '0',
  `NombreFantasia` char(100) NOT NULL,
  `Logo` char(50) NOT NULL,
  `Bloqueado` tinyint(1) NOT NULL DEFAULT '0',
  `Motivo` char(200) NOT NULL,
  `Direccion` char(200) NOT NULL,
  `IdLocalidad` int(11) NOT NULL DEFAULT '0',
  `Provincia` char(30) NOT NULL,
  `CP` char(10) NOT NULL,
  `EMail` char(50) NOT NULL,
  `ObsEMail` char(150) NOT NULL,
  `EMailResultados` char(255) NOT NULL,
  `Telefono` char(100) NOT NULL,
  `LogoCertificado` tinyint(1) NOT NULL DEFAULT '0',
  `Oreste` tinyint(1) NOT NULL DEFAULT '0',
  `TipoCliente` char(1) NOT NULL,
  `FPago` char(1) NOT NULL,
  `ObsEval` text NOT NULL,
  `ObsCE` char(150) NOT NULL,
  `Generico` tinyint(1) NOT NULL,
  `SEMail` tinyint(1) NOT NULL,
  `ObsCO` text NOT NULL,
  `IdAsignado` int(11) NOT NULL,
  `EMailFactura` char(200) NOT NULL,
  `EnvioFactura` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `EMailInformes` char(255) NOT NULL,
  `EnvioInforme` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Ajuste` decimal(10,2) NOT NULL,
  `SinPF` tinyint(1) NOT NULL,
  `SinEval` tinyint(1) NOT NULL,
  `RF` tinyint(1) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdActividad` (`IdActividad`),
  KEY `IdLocalidad` (`IdLocalidad`),
  KEY `IdAsignado` (`IdAsignado`),
  KEY `RazonSocial` (`RazonSocial`),
  KEY `Identificacion` (`Identificacion`),
  KEY `ParaEmpresa` (`ParaEmpresa`),
  CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`IdActividad`) REFERENCES `actividades` (`Id`),
  CONSTRAINT `clientes_ibfk_2` FOREIGN KEY (`IdLocalidad`) REFERENCES `localidades` (`Id`),
  CONSTRAINT `clientes_ibfk_3` FOREIGN KEY (`IdAsignado`) REFERENCES `personal` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clientesgrupos`
--

DROP TABLE IF EXISTS `clientesgrupos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientesgrupos` (
  `Id` int(11) NOT NULL,
  `Nombre` char(50) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Nombre` (`Nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clientesgrupos_it`
--

DROP TABLE IF EXISTS `clientesgrupos_it`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientesgrupos_it` (
  `Id` int(11) NOT NULL,
  `IdGrupo` int(11) NOT NULL,
  `IdCliente` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `IdCliente_2` (`IdCliente`),
  KEY `IdGrupo` (`IdGrupo`),
  KEY `IdCliente` (`IdCliente`),
  CONSTRAINT `clientesgrupos_it_ibfk_1` FOREIGN KEY (`IdGrupo`) REFERENCES `clientesgrupos` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `clientesgrupos_it_ibfk_2` FOREIGN KEY (`IdCliente`) REFERENCES `clientes` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `constanciase`
--

DROP TABLE IF EXISTS `constanciase`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `constanciase` (
  `Id` int(11) NOT NULL,
  `NroC` int(11) NOT NULL,
  `Fecha` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `constanciase_it`
--

DROP TABLE IF EXISTS `constanciase_it`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `constanciase_it` (
  `Id` int(11) NOT NULL,
  `IdC` int(11) NOT NULL,
  `IdP` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdC` (`IdC`),
  KEY `IdP` (`IdP`),
  CONSTRAINT `constanciase_it_ibfk_1` FOREIGN KEY (`IdC`) REFERENCES `constanciase` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `constanciase_it_ibfk_2` FOREIGN KEY (`IdP`) REFERENCES `prestaciones` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `datospacientes`
--

DROP TABLE IF EXISTS `datospacientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datospacientes` (
  `Id` int(11) NOT NULL,
  `IdPaciente` int(11) NOT NULL DEFAULT '0',
  `IdPrestacion` int(11) NOT NULL DEFAULT '0',
  `Edad` tinyint(2) NOT NULL DEFAULT '0',
  `EstadoCivil` char(12) NOT NULL,
  `ObsEC` char(100) NOT NULL,
  `Direccion` char(200) NOT NULL,
  `IdLocalidad` int(11) NOT NULL DEFAULT '0',
  `TipoActividad` char(1) NOT NULL,
  `Tareas` char(40) NOT NULL,
  `TareasEmpAnterior` char(30) NOT NULL,
  `Puesto` char(40) NOT NULL,
  `Sector` char(30) NOT NULL,
  `FechaIngreso` date NOT NULL DEFAULT '0000-00-00',
  `FechaEgreso` date NOT NULL DEFAULT '0000-00-00',
  `AntigPuesto` int(11) NOT NULL DEFAULT '0',
  `AntigEmpresa` int(11) NOT NULL DEFAULT '0',
  `TipoJornada` char(12) NOT NULL,
  `Jornada` char(12) NOT NULL,
  `ObsJornada` char(100) NOT NULL,
  `CCosto` char(50) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdPrestacion` (`IdPrestacion`),
  KEY `IdLocalidad` (`IdLocalidad`),
  CONSTRAINT `datospacientes_ibfk_1` FOREIGN KEY (`IdPrestacion`) REFERENCES `prestaciones` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `datospacientes_ibfk_2` FOREIGN KEY (`IdLocalidad`) REFERENCES `localidades` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `depositos`
--

DROP TABLE IF EXISTS `depositos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `depositos` (
  `Id` int(11) NOT NULL,
  `Nombre` char(30) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Nombre` (`Nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `enviarmodelos`
--

DROP TABLE IF EXISTS `enviarmodelos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enviarmodelos` (
  `Id` int(11) NOT NULL,
  `Nombre` char(50) NOT NULL,
  `Asunto` char(100) NOT NULL,
  `Cuerpo` text NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Nombre` (`Nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `epparticulos`
--

DROP TABLE IF EXISTS `epparticulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `epparticulos` (
  `Id` int(11) NOT NULL,
  `Nombre` char(50) CHARACTER SET latin1 NOT NULL,
  `IdRubro` int(11) NOT NULL,
  `IdMarca` int(11) NOT NULL,
  `IdUnidad` int(11) NOT NULL,
  `Obs` char(100) CHARACTER SET latin1 NOT NULL,
  `Modelo` char(30) CHARACTER SET latin1 NOT NULL,
  `FechaBaja` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`Id`),
  KEY `IdRubro` (`IdRubro`),
  KEY `IdMarca` (`IdMarca`),
  KEY `IdUnidad` (`IdUnidad`),
  CONSTRAINT `epparticulos_ibfk_1` FOREIGN KEY (`IdRubro`) REFERENCES `rubros` (`Id`),
  CONSTRAINT `epparticulos_ibfk_2` FOREIGN KEY (`IdMarca`) REFERENCES `marcas` (`Id`),
  CONSTRAINT `epparticulos_ibfk_3` FOREIGN KEY (`IdUnidad`) REFERENCES `unidadesmedida` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `estudios`
--

DROP TABLE IF EXISTS `estudios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `estudios` (
  `Id` int(11) NOT NULL,
  `Nombre` char(50) NOT NULL,
  `Descripcion` char(100) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Nombre` (`Nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `examenes`
--

DROP TABLE IF EXISTS `examenes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `examenes` (
  `Id` int(11) NOT NULL,
  `IdEstudio` int(11) NOT NULL DEFAULT '0',
  `Nombre` char(50) NOT NULL,
  `Descripcion` char(100) NOT NULL,
  `IdReporte` int(11) NOT NULL DEFAULT '0',
  `IdProveedor` int(11) NOT NULL DEFAULT '0',
  `IdProveedor2` int(11) NOT NULL,
  `DiasVencimiento` tinyint(4) NOT NULL DEFAULT '0',
  `Inactivo` tinyint(1) NOT NULL,
  `Cod` char(10) NOT NULL,
  `Cod2` char(10) NOT NULL,
  `SinEsc` tinyint(1) NOT NULL,
  `Forma` tinyint(1) NOT NULL,
  `Ausente` tinyint(1) NOT NULL,
  `Devol` tinyint(1) NOT NULL,
  `Informe` tinyint(1) NOT NULL,
  `Adjunto` tinyint(1) NOT NULL,
  `NoImprime` tinyint(1) NOT NULL,
  `Cerrado` tinyint(1) NOT NULL,
  `Evaluador` tinyint(1) NOT NULL,
  `EvalCopia` tinyint(1) NOT NULL,
  `PI` tinyint(1) NOT NULL,
  `IdForm` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdEstudio` (`IdEstudio`),
  KEY `IdReporte` (`IdReporte`),
  KEY `IdProveedor` (`IdProveedor`),
  KEY `IdProveedor2` (`IdProveedor2`),
  KEY `IdForm` (`IdForm`),
  CONSTRAINT `examenes_ibfk_1` FOREIGN KEY (`IdEstudio`) REFERENCES `estudios` (`Id`),
  CONSTRAINT `examenes_ibfk_2` FOREIGN KEY (`IdReporte`) REFERENCES `reportes` (`Id`),
  CONSTRAINT `examenes_ibfk_3` FOREIGN KEY (`IdProveedor`) REFERENCES `proveedores` (`Id`),
  CONSTRAINT `examenes_ibfk_4` FOREIGN KEY (`IdProveedor2`) REFERENCES `proveedores` (`Id`),
  CONSTRAINT `examenes_ibfk_5` FOREIGN KEY (`IdForm`) REFERENCES `reportesf` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `examenesprecioproveedor`
--

DROP TABLE IF EXISTS `examenesprecioproveedor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `examenesprecioproveedor` (
  `Id` int(11) NOT NULL,
  `IdEstudio` int(11) NOT NULL DEFAULT '0',
  `IdExamen` int(11) NOT NULL DEFAULT '0',
  `IdProveedor` int(11) NOT NULL DEFAULT '0',
  `Honorarios` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `IdExamen_2` (`IdExamen`,`IdProveedor`),
  KEY `IdEstudio` (`IdEstudio`),
  KEY `IdExamen` (`IdExamen`),
  KEY `IdProveedor` (`IdProveedor`),
  CONSTRAINT `examenesprecioproveedor_ibfk_1` FOREIGN KEY (`IdEstudio`) REFERENCES `estudios` (`Id`),
  CONSTRAINT `examenesprecioproveedor_ibfk_2` FOREIGN KEY (`IdProveedor`) REFERENCES `proveedores` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `examenesprecioproveedor_ibfk_3` FOREIGN KEY (`IdExamen`) REFERENCES `examenes` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `facturascompra`
--

DROP TABLE IF EXISTS `facturascompra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facturascompra` (
  `Id` int(11) NOT NULL,
  `Tipo` char(2) NOT NULL,
  `Sucursal` int(4) NOT NULL,
  `NroFactura` bigint(8) NOT NULL,
  `Fecha` date NOT NULL DEFAULT '0000-00-00',
  `Anulada` tinyint(4) NOT NULL,
  `FechaAnulada` date NOT NULL DEFAULT '0000-00-00',
  `IdProfesional` int(11) NOT NULL,
  `ObsAnulado` char(200) NOT NULL,
  `Obs` char(200) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdProfesional` (`IdProfesional`),
  CONSTRAINT `facturascompra_ibfk_1` FOREIGN KEY (`IdProfesional`) REFERENCES `profesionales` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `facturasresumen`
--

DROP TABLE IF EXISTS `facturasresumen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facturasresumen` (
  `Id` int(11) NOT NULL DEFAULT '0',
  `IdFactura` int(11) NOT NULL DEFAULT '0',
  `Total` int(11) NOT NULL DEFAULT '0',
  `Detalle` text NOT NULL,
  `Cod` char(10) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdFactura` (`IdFactura`),
  CONSTRAINT `facturasresumen_ibfk_1` FOREIGN KEY (`IdFactura`) REFERENCES `facturasventa` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `facturasventa`
--

DROP TABLE IF EXISTS `facturasventa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facturasventa` (
  `Id` int(11) NOT NULL DEFAULT '0',
  `Tipo` char(2) NOT NULL,
  `Sucursal` int(4) NOT NULL,
  `NroFactura` bigint(8) NOT NULL,
  `Fecha` date NOT NULL DEFAULT '0000-00-00',
  `Anulada` tinyint(1) NOT NULL DEFAULT '0',
  `FechaAnulada` date NOT NULL DEFAULT '0000-00-00',
  `IdEmpresa` int(11) NOT NULL DEFAULT '0',
  `TipoCliente` char(10) NOT NULL,
  `ObsAnulado` char(200) NOT NULL,
  `EnvioFacturaF` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Obs` char(200) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdEmpresa` (`IdEmpresa`),
  CONSTRAINT `facturasventa_ibfk_1` FOREIGN KEY (`IdEmpresa`) REFERENCES `clientes` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fichaslaborales`
--

DROP TABLE IF EXISTS `fichaslaborales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fichaslaborales` (
  `Id` int(11) NOT NULL,
  `IdPaciente` int(11) NOT NULL DEFAULT '0',
  `IdEmpresa` int(11) NOT NULL DEFAULT '0',
  `IdART` int(11) NOT NULL DEFAULT '0',
  `Tareas` char(40) NOT NULL,
  `TareasEmpAnterior` char(30) NOT NULL,
  `Puesto` char(40) NOT NULL,
  `Sector` char(30) NOT NULL,
  `FechaIngreso` date NOT NULL DEFAULT '0000-00-00',
  `FechaEgreso` date NOT NULL DEFAULT '0000-00-00',
  `AntigPuesto` int(11) NOT NULL DEFAULT '0',
  `TipoJornada` char(12) NOT NULL,
  `Jornada` char(12) NOT NULL,
  `ObsJornada` char(200) NOT NULL,
  `Observaciones` text NOT NULL,
  `TipoActividad` char(1) NOT NULL,
  `Solicitante` char(1) NOT NULL,
  `CCosto` char(50) NOT NULL,
  `Pago` char(1) NOT NULL,
  `Estado` char(1) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdPaciente` (`IdPaciente`),
  KEY `IdEmpresa` (`IdEmpresa`),
  KEY `IdART` (`IdART`),
  CONSTRAINT `fichaslaborales_ibfk_1` FOREIGN KEY (`IdPaciente`) REFERENCES `pacientes` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `fichaslaborales_ibfk_2` FOREIGN KEY (`IdEmpresa`) REFERENCES `clientes` (`Id`),
  CONSTRAINT `fichaslaborales_ibfk_3` FOREIGN KEY (`IdART`) REFERENCES `clientes` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hc_casos`
--

DROP TABLE IF EXISTS `hc_casos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hc_casos` (
  `Id` int(11) NOT NULL DEFAULT '0',
  `Fecha` date NOT NULL DEFAULT '0000-00-00',
  `Nombre` char(100) NOT NULL,
  `Descripcion` text NOT NULL,
  `IdPaciente` int(11) NOT NULL DEFAULT '0',
  `IdEmpresa` int(11) NOT NULL DEFAULT '0',
  `IdART` int(11) NOT NULL DEFAULT '0',
  `Clasificacion` char(50) NOT NULL,
  `Denunciado` tinyint(11) NOT NULL DEFAULT '0',
  `Estado` char(15) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdPaciente` (`IdPaciente`),
  KEY `IdEmpresa` (`IdEmpresa`),
  KEY `IdART` (`IdART`),
  CONSTRAINT `hc_casos_ibfk_1` FOREIGN KEY (`IdPaciente`) REFERENCES `pacientes` (`Id`),
  CONSTRAINT `hc_casos_ibfk_2` FOREIGN KEY (`IdEmpresa`) REFERENCES `clientes` (`Id`),
  CONSTRAINT `hc_casos_ibfk_3` FOREIGN KEY (`IdART`) REFERENCES `clientes` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hc_consultas`
--

DROP TABLE IF EXISTS `hc_consultas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hc_consultas` (
  `Id` int(11) NOT NULL DEFAULT '0',
  `Fecha` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `IdCaso` int(11) NOT NULL DEFAULT '0',
  `IdProfesional` int(11) NOT NULL DEFAULT '0',
  `Observaciones` text NOT NULL,
  `FControl` date NOT NULL DEFAULT '0000-00-00',
  `HControl` time NOT NULL DEFAULT '00:00:00',
  `Facturado` tinyint(11) NOT NULL DEFAULT '0',
  `IdFactura` int(11) NOT NULL DEFAULT '0',
  `Motivo` char(100) NOT NULL,
  `TipoCliente` char(10) NOT NULL,
  `Pagado` tinyint(11) NOT NULL DEFAULT '0',
  `FechaPagado` date NOT NULL DEFAULT '0000-00-00',
  `Tipo` char(15) NOT NULL,
  `Diagnostico` text NOT NULL,
  `Recomendaciones` text NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdCaso` (`IdCaso`),
  KEY `IdProfesional` (`IdProfesional`),
  CONSTRAINT `hc_consultas_ibfk_1` FOREIGN KEY (`IdCaso`) REFERENCES `hc_casos` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `hc_consultas_ibfk_2` FOREIGN KEY (`IdProfesional`) REFERENCES `profesionales` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hist_autorizados`
--

DROP TABLE IF EXISTS `hist_autorizados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hist_autorizados` (
  `Id` int(11) NOT NULL,
  `IdEntidad` int(11) NOT NULL DEFAULT '0',
  `Nombre` char(30) NOT NULL,
  `Apellido` char(30) NOT NULL,
  `DNI` char(13) NOT NULL,
  `Derecho` char(50) NOT NULL,
  `TipoEntidad` char(1) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdEntidad` (`IdEntidad`),
  CONSTRAINT `hist_autorizados_ibfk_1` FOREIGN KEY (`IdEntidad`) REFERENCES `hist_clientes` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hist_clientes`
--

DROP TABLE IF EXISTS `hist_clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hist_clientes` (
  `Id` int(11) NOT NULL,
  `RazonSocial` char(100) NOT NULL,
  `Nacionalidad` char(30) NOT NULL,
  `CondicionIva` char(30) NOT NULL,
  `TipoIdentificacion` char(10) NOT NULL,
  `Identificacion` char(13) NOT NULL,
  `Observaciones` text NOT NULL,
  `TipoPersona` char(1) NOT NULL,
  `Envio` tinyint(11) NOT NULL DEFAULT '0',
  `Entrega` tinyint(11) NOT NULL DEFAULT '0',
  `ParaEmpresa` char(100) NOT NULL,
  `IdActividad` int(11) NOT NULL DEFAULT '0',
  `NombreFantasia` char(100) NOT NULL,
  `Logo` char(50) NOT NULL,
  `Bloqueado` tinyint(11) NOT NULL DEFAULT '0',
  `Motivo` char(200) NOT NULL,
  `Direccion` char(200) NOT NULL,
  `IdLocalidad` int(11) NOT NULL DEFAULT '0',
  `Provincia` char(30) NOT NULL,
  `CP` char(10) NOT NULL,
  `EMail` char(50) NOT NULL,
  `ObsEMail` char(150) NOT NULL,
  `EMailResultados` char(150) NOT NULL,
  `Telefono` char(100) NOT NULL,
  `LogoCertificado` tinyint(11) NOT NULL DEFAULT '0',
  `Oreste` tinyint(11) NOT NULL DEFAULT '0',
  `TipoCliente` char(1) NOT NULL,
  `FPago` char(1) NOT NULL,
  `ObsEval` text NOT NULL,
  `ObsCE` char(150) NOT NULL,
  `Generico` tinyint(4) NOT NULL,
  `SEMail` tinyint(4) NOT NULL,
  `ObsCO` text NOT NULL,
  `IdAsignado` int(11) NOT NULL,
  `EMailFactura` char(150) NOT NULL,
  `EnvioFactura` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `EMailInformes` char(150) NOT NULL,
  `EnvioInforme` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`Id`),
  KEY `IdActividad` (`IdActividad`),
  KEY `IdLocalidad` (`IdLocalidad`),
  KEY `IdAsignado` (`IdAsignado`),
  CONSTRAINT `hist_clientes_ibfk_1` FOREIGN KEY (`IdActividad`) REFERENCES `actividades` (`Id`),
  CONSTRAINT `hist_clientes_ibfk_2` FOREIGN KEY (`IdLocalidad`) REFERENCES `localidades` (`Id`),
  CONSTRAINT `hist_clientes_ibfk_3` FOREIGN KEY (`IdAsignado`) REFERENCES `personal` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hist_datospacientes`
--

DROP TABLE IF EXISTS `hist_datospacientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hist_datospacientes` (
  `Id` int(11) NOT NULL,
  `IdPaciente` int(11) NOT NULL DEFAULT '0',
  `IdPrestacion` int(11) NOT NULL DEFAULT '0',
  `Edad` int(11) NOT NULL DEFAULT '0',
  `EstadoCivil` char(15) NOT NULL,
  `ObsEC` char(100) NOT NULL,
  `Direccion` char(200) NOT NULL,
  `IdLocalidad` int(11) NOT NULL DEFAULT '0',
  `TipoActividad` char(1) NOT NULL,
  `Tareas` char(30) NOT NULL,
  `TareasEmpAnterior` char(30) NOT NULL,
  `Puesto` char(30) NOT NULL,
  `Sector` char(30) NOT NULL,
  `FechaIngreso` date NOT NULL DEFAULT '0000-00-00',
  `FechaEgreso` date NOT NULL DEFAULT '0000-00-00',
  `AntigPuesto` int(11) NOT NULL DEFAULT '0',
  `AntigEmpresa` int(11) NOT NULL DEFAULT '0',
  `TipoJornada` char(15) NOT NULL,
  `Jornada` char(15) NOT NULL,
  `ObsJornada` char(100) NOT NULL,
  `CCosto` char(30) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdPrestacion` (`IdPrestacion`),
  KEY `IdLocalidad` (`IdLocalidad`),
  CONSTRAINT `hist_datospacientes_ibfk_2` FOREIGN KEY (`IdLocalidad`) REFERENCES `localidades` (`Id`),
  CONSTRAINT `hist_datospacientes_ibfk_3` FOREIGN KEY (`IdPrestacion`) REFERENCES `hist_prestaciones` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hist_facturascompra`
--

DROP TABLE IF EXISTS `hist_facturascompra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hist_facturascompra` (
  `Id` int(11) NOT NULL,
  `Tipo` char(2) NOT NULL,
  `Sucursal` int(4) NOT NULL,
  `NroFactura` bigint(8) NOT NULL,
  `Fecha` date NOT NULL DEFAULT '0000-00-00',
  `Anulada` tinyint(4) NOT NULL,
  `FechaAnulada` date NOT NULL DEFAULT '0000-00-00',
  `IdProfesional` int(11) NOT NULL,
  `ObsAnulado` char(200) NOT NULL,
  `Obs` char(200) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdProfesional` (`IdProfesional`),
  CONSTRAINT `hist_facturascompra_ibfk_1` FOREIGN KEY (`IdProfesional`) REFERENCES `profesionales` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hist_facturasresumen`
--

DROP TABLE IF EXISTS `hist_facturasresumen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hist_facturasresumen` (
  `Id` int(11) NOT NULL DEFAULT '0',
  `IdFactura` int(11) NOT NULL DEFAULT '0',
  `Total` int(11) NOT NULL DEFAULT '0',
  `Detalle` text NOT NULL,
  `Cod` char(10) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdFactura` (`IdFactura`),
  CONSTRAINT `hist_facturasresumen_ibfk_1` FOREIGN KEY (`IdFactura`) REFERENCES `hist_facturasventa` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hist_facturasventa`
--

DROP TABLE IF EXISTS `hist_facturasventa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hist_facturasventa` (
  `Id` int(11) NOT NULL DEFAULT '0',
  `Tipo` char(2) NOT NULL,
  `Sucursal` int(4) NOT NULL,
  `NroFactura` bigint(8) NOT NULL,
  `Fecha` date NOT NULL DEFAULT '0000-00-00',
  `Anulada` tinyint(11) NOT NULL DEFAULT '0',
  `FechaAnulada` date NOT NULL DEFAULT '0000-00-00',
  `IdEmpresa` int(11) NOT NULL DEFAULT '0',
  `TipoCliente` char(10) NOT NULL,
  `ObsAnulado` char(200) NOT NULL,
  `EnvioFacturaF` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Obs` char(200) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdEmpresa` (`IdEmpresa`),
  CONSTRAINT `hist_facturasventa_ibfk_1` FOREIGN KEY (`IdEmpresa`) REFERENCES `hist_clientes` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hist_fichaslaborales`
--

DROP TABLE IF EXISTS `hist_fichaslaborales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hist_fichaslaborales` (
  `Id` int(11) NOT NULL,
  `IdPaciente` int(11) NOT NULL DEFAULT '0',
  `IdEmpresa` int(11) NOT NULL DEFAULT '0',
  `IdART` int(11) NOT NULL DEFAULT '0',
  `Tareas` char(30) NOT NULL,
  `TareasEmpAnterior` char(30) NOT NULL,
  `Puesto` char(30) NOT NULL,
  `Sector` char(30) NOT NULL,
  `FechaIngreso` date NOT NULL DEFAULT '0000-00-00',
  `FechaEgreso` date NOT NULL DEFAULT '0000-00-00',
  `AntigPuesto` int(11) NOT NULL DEFAULT '0',
  `TipoJornada` char(30) NOT NULL,
  `Jornada` char(30) NOT NULL,
  `ObsJornada` char(200) NOT NULL,
  `Observaciones` text NOT NULL,
  `TipoActividad` char(1) NOT NULL,
  `Solicitante` char(1) NOT NULL,
  `CCosto` char(30) NOT NULL,
  `Pago` char(1) NOT NULL,
  `Estado` char(1) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdPaciente` (`IdPaciente`),
  KEY `IdEmpresa` (`IdEmpresa`),
  KEY `IdART` (`IdART`),
  CONSTRAINT `hist_fichaslaborales_ibfk_1` FOREIGN KEY (`IdPaciente`) REFERENCES `hist_pacientes` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `hist_fichaslaborales_ibfk_2` FOREIGN KEY (`IdEmpresa`) REFERENCES `hist_clientes` (`Id`),
  CONSTRAINT `hist_fichaslaborales_ibfk_3` FOREIGN KEY (`IdART`) REFERENCES `hist_clientes` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hist_informesradiologicos`
--

DROP TABLE IF EXISTS `hist_informesradiologicos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hist_informesradiologicos` (
  `Id` int(11) NOT NULL DEFAULT '0',
  `IdPrestacion` int(11) NOT NULL DEFAULT '0',
  `Informe` text NOT NULL,
  `Profesional` text NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdPrestacion` (`IdPrestacion`),
  CONSTRAINT `hist_informesradiologicos_ibfk_1` FOREIGN KEY (`IdPrestacion`) REFERENCES `hist_prestaciones` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hist_itemsfacturacompra`
--

DROP TABLE IF EXISTS `hist_itemsfacturacompra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hist_itemsfacturacompra` (
  `Id` int(11) NOT NULL,
  `IdFactura` int(11) NOT NULL,
  `IdItemPrestacion` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdFactura` (`IdFactura`),
  KEY `IdItemPrestacion` (`IdItemPrestacion`),
  CONSTRAINT `hist_itemsfacturacompra_ibfk_1` FOREIGN KEY (`IdFactura`) REFERENCES `hist_facturascompra` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `hist_itemsfacturacompra_ibfk_2` FOREIGN KEY (`IdItemPrestacion`) REFERENCES `hist_itemsprestaciones` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hist_itemsfacturaventa`
--

DROP TABLE IF EXISTS `hist_itemsfacturaventa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hist_itemsfacturaventa` (
  `Id` int(11) NOT NULL DEFAULT '0',
  `IdFactura` int(11) NOT NULL DEFAULT '0',
  `IdPrestacion` int(11) NOT NULL DEFAULT '0',
  `Detalle` text NOT NULL,
  `Anulado` tinyint(4) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdFactura` (`IdFactura`),
  CONSTRAINT `hist_itemsfacturaventa_ibfk_1` FOREIGN KEY (`IdFactura`) REFERENCES `hist_facturasventa` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hist_itemsprestaciones`
--

DROP TABLE IF EXISTS `hist_itemsprestaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hist_itemsprestaciones` (
  `Id` int(11) NOT NULL,
  `IdPrestacion` int(11) NOT NULL DEFAULT '0',
  `IdExamen` int(11) NOT NULL DEFAULT '0',
  `ObsExamen` text NOT NULL,
  `Asignado` tinyint(11) NOT NULL DEFAULT '0',
  `Pagado` tinyint(11) NOT NULL DEFAULT '0',
  `IdProveedor` int(11) NOT NULL DEFAULT '0',
  `IdProfesional` int(11) NOT NULL DEFAULT '0',
  `FechaPagado` date NOT NULL DEFAULT '0000-00-00',
  `Anulado` tinyint(11) NOT NULL DEFAULT '0',
  `FechaAnulado` date NOT NULL DEFAULT '0000-00-00',
  `Fecha` date NOT NULL DEFAULT '0000-00-00',
  `FechaAsignado` date NOT NULL DEFAULT '0000-00-00',
  `Facturado` tinyint(11) NOT NULL DEFAULT '0',
  `FechaFacturado` date NOT NULL DEFAULT '0000-00-00',
  `NumeroFacturaVta` int(11) NOT NULL DEFAULT '0',
  `VtoItem` int(11) NOT NULL DEFAULT '0',
  `Honorarios` decimal(10,2) NOT NULL DEFAULT '0.00',
  `NroFactCompra` int(11) NOT NULL,
  `Incompleto` tinyint(4) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdPrestacion` (`IdPrestacion`),
  KEY `IdExamen` (`IdExamen`),
  KEY `IdProveedor` (`IdProveedor`),
  KEY `IdProfesional` (`IdProfesional`),
  CONSTRAINT `hist_itemsprestaciones_ibfk_2` FOREIGN KEY (`IdExamen`) REFERENCES `examenes` (`Id`),
  CONSTRAINT `hist_itemsprestaciones_ibfk_3` FOREIGN KEY (`IdProveedor`) REFERENCES `proveedores` (`Id`),
  CONSTRAINT `hist_itemsprestaciones_ibfk_4` FOREIGN KEY (`IdProfesional`) REFERENCES `profesionales` (`Id`),
  CONSTRAINT `hist_itemsprestaciones_ibfk_5` FOREIGN KEY (`IdPrestacion`) REFERENCES `hist_prestaciones` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hist_notascredito`
--

DROP TABLE IF EXISTS `hist_notascredito`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hist_notascredito` (
  `Id` int(11) NOT NULL DEFAULT '0',
  `Tipo` char(2) NOT NULL,
  `Sucursal` int(4) NOT NULL,
  `Nro` bigint(8) NOT NULL,
  `Fecha` date NOT NULL DEFAULT '0000-00-00',
  `IdEmpresa` int(11) NOT NULL DEFAULT '0',
  `TipoCliente` char(1) NOT NULL,
  `IdFactura` int(11) NOT NULL,
  `IdP` int(11) NOT NULL,
  `TipoNC` tinyint(4) NOT NULL,
  `Obs` char(200) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdEmpresa` (`IdEmpresa`),
  KEY `IdFactura` (`IdFactura`),
  KEY `IdP` (`IdP`),
  CONSTRAINT `hist_notascredito_ibfk_1` FOREIGN KEY (`IdEmpresa`) REFERENCES `hist_clientes` (`Id`),
  CONSTRAINT `hist_notascredito_ibfk_2` FOREIGN KEY (`IdFactura`) REFERENCES `hist_facturasventa` (`Id`),
  CONSTRAINT `hist_notascredito_ibfk_3` FOREIGN KEY (`IdP`) REFERENCES `hist_prestaciones` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hist_notascredito_it`
--

DROP TABLE IF EXISTS `hist_notascredito_it`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hist_notascredito_it` (
  `Id` int(11) NOT NULL,
  `IdNC` int(11) NOT NULL,
  `IdIP` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdNC` (`IdNC`),
  KEY `IdIP` (`IdIP`),
  CONSTRAINT `hist_notascredito_it_ibfk_1` FOREIGN KEY (`IdNC`) REFERENCES `hist_notascredito` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `hist_notascredito_it_ibfk_2` FOREIGN KEY (`IdIP`) REFERENCES `hist_itemsprestaciones` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hist_pacientes`
--

DROP TABLE IF EXISTS `hist_pacientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hist_pacientes` (
  `Id` int(11) NOT NULL,
  `TipoIdentificacion` char(5) NOT NULL,
  `Identificacion` char(13) NOT NULL,
  `TipoDocumento` char(5) NOT NULL,
  `Documento` char(13) NOT NULL,
  `Nacionalidad` char(30) NOT NULL,
  `Sexo` char(1) NOT NULL,
  `Nombre` char(30) NOT NULL,
  `Apellido` char(30) NOT NULL,
  `FechaNacimiento` date NOT NULL DEFAULT '0000-00-00',
  `LugarNacimiento` char(50) NOT NULL,
  `EstadoCivil` char(15) NOT NULL,
  `ObsEstadoCivil` char(100) NOT NULL,
  `Hijos` int(11) NOT NULL DEFAULT '0',
  `Direccion` char(200) NOT NULL,
  `IdLocalidad` int(11) NOT NULL DEFAULT '0',
  `Provincia` char(30) NOT NULL,
  `CP` char(10) NOT NULL,
  `EMail` char(50) NOT NULL,
  `ObsEMail` char(100) NOT NULL,
  `Foto` char(50) NOT NULL,
  `Antecedentes` text NOT NULL,
  `Observaciones` text NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdLocalidad` (`IdLocalidad`),
  CONSTRAINT `hist_pacientes_ibfk_1` FOREIGN KEY (`IdLocalidad`) REFERENCES `localidades` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hist_pagosacuenta`
--

DROP TABLE IF EXISTS `hist_pagosacuenta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hist_pagosacuenta` (
  `Id` int(11) NOT NULL DEFAULT '0',
  `IdEmpresa` int(11) NOT NULL DEFAULT '0',
  `Fecha` date NOT NULL DEFAULT '0000-00-00',
  `Tipo` char(1) NOT NULL,
  `Suc` int(4) NOT NULL,
  `Nro` bigint(8) NOT NULL,
  `Obs` char(200) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdEmpresa` (`IdEmpresa`),
  CONSTRAINT `hist_pagosacuenta_ibfk_1` FOREIGN KEY (`IdEmpresa`) REFERENCES `hist_clientes` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hist_pagosacuenta_it`
--

DROP TABLE IF EXISTS `hist_pagosacuenta_it`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hist_pagosacuenta_it` (
  `Id` int(11) NOT NULL,
  `IdPago` int(11) NOT NULL,
  `IdExamen` int(11) NOT NULL,
  `IdPrestacion` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdPago` (`IdPago`),
  KEY `IdExamen` (`IdExamen`),
  KEY `IdPrestacion` (`IdPrestacion`),
  CONSTRAINT `hist_pagosacuenta_it_ibfk_1` FOREIGN KEY (`IdPago`) REFERENCES `hist_pagosacuenta` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `hist_pagosacuenta_it_ibfk_2` FOREIGN KEY (`IdExamen`) REFERENCES `examenes` (`Id`),
  CONSTRAINT `hist_pagosacuenta_it_ibfk_3` FOREIGN KEY (`IdPrestacion`) REFERENCES `hist_prestaciones` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hist_prestaciones`
--

DROP TABLE IF EXISTS `hist_prestaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hist_prestaciones` (
  `Id` int(11) NOT NULL,
  `IdPaciente` int(11) NOT NULL DEFAULT '0',
  `IdEmpresa` int(11) NOT NULL DEFAULT '0',
  `IdART` int(11) NOT NULL DEFAULT '0',
  `TipoPrestacion` char(15) NOT NULL,
  `Fecha` date NOT NULL DEFAULT '0000-00-00',
  `Anulado` tinyint(11) NOT NULL DEFAULT '0',
  `Cerrado` tinyint(11) NOT NULL DEFAULT '0',
  `Entregado` tinyint(11) NOT NULL DEFAULT '0',
  `Facturado` tinyint(11) NOT NULL DEFAULT '0',
  `ObsAnulado` text NOT NULL,
  `Evaluacion` char(100) NOT NULL,
  `Calificacion` char(150) NOT NULL,
  `Observaciones` text NOT NULL,
  `NumeroFacturaVta` int(11) NOT NULL DEFAULT '0',
  `FechaCierre` date NOT NULL DEFAULT '0000-00-00',
  `FechaEntrega` date NOT NULL DEFAULT '0000-00-00',
  `FechaFact` date NOT NULL DEFAULT '0000-00-00',
  `FechaAnul` date NOT NULL DEFAULT '0000-00-00',
  `Finalizado` tinyint(11) NOT NULL DEFAULT '0',
  `FechaFinalizado` date NOT NULL DEFAULT '0000-00-00',
  `ObsExamenes` text NOT NULL,
  `Vto` int(11) NOT NULL DEFAULT '0',
  `FechaVto` date NOT NULL DEFAULT '0000-00-00',
  `NroCEE` int(11) NOT NULL DEFAULT '0',
  `Pago` char(1) NOT NULL,
  `SPago` char(1) NOT NULL,
  `TSN` char(15) NOT NULL,
  `FechaT` date NOT NULL DEFAULT '0000-00-00',
  `Incompleto` tinyint(4) NOT NULL,
  `AutorizaSC` char(30) NOT NULL,
  `RxPreliminar` tinyint(4) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdPaciente` (`IdPaciente`),
  KEY `IdEmpresa` (`IdEmpresa`),
  KEY `IdART` (`IdART`),
  CONSTRAINT `hist_prestaciones_ibfk_4` FOREIGN KEY (`IdPaciente`) REFERENCES `hist_pacientes` (`Id`),
  CONSTRAINT `hist_prestaciones_ibfk_5` FOREIGN KEY (`IdEmpresa`) REFERENCES `hist_clientes` (`Id`),
  CONSTRAINT `hist_prestaciones_ibfk_6` FOREIGN KEY (`IdART`) REFERENCES `hist_clientes` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `idblindados`
--

DROP TABLE IF EXISTS `idblindados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `idblindados` (
  `Id` int(11) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `informesradiologicos`
--

DROP TABLE IF EXISTS `informesradiologicos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `informesradiologicos` (
  `Id` int(11) NOT NULL DEFAULT '0',
  `IdPrestacion` int(11) NOT NULL DEFAULT '0',
  `Informe` text NOT NULL,
  `Profesional` text NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdPrestacion` (`IdPrestacion`),
  CONSTRAINT `informesradiologicos_ibfk_1` FOREIGN KEY (`IdPrestacion`) REFERENCES `prestaciones` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iso_minutas`
--

DROP TABLE IF EXISTS `iso_minutas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iso_minutas` (
  `Id` int(11) NOT NULL,
  `Fecha` date NOT NULL DEFAULT '0000-00-00',
  `Hora` time NOT NULL DEFAULT '00:00:00',
  `Nombre` char(100) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iso_minutas_as`
--

DROP TABLE IF EXISTS `iso_minutas_as`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iso_minutas_as` (
  `Id` int(11) NOT NULL,
  `IdMin` int(11) NOT NULL,
  `Nombre` char(100) NOT NULL,
  `IdSector` int(11) NOT NULL,
  `IdPersonal` int(11) NOT NULL,
  `IdProf` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdMin` (`IdMin`),
  KEY `IdSector` (`IdSector`),
  KEY `IdPersonal` (`IdPersonal`),
  KEY `IdProf` (`IdProf`),
  CONSTRAINT `iso_minutas_as_ibfk_1` FOREIGN KEY (`IdMin`) REFERENCES `iso_minutas` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `iso_minutas_as_ibfk_5` FOREIGN KEY (`IdSector`) REFERENCES `sector` (`Id`),
  CONSTRAINT `iso_minutas_as_ibfk_6` FOREIGN KEY (`IdPersonal`) REFERENCES `personal` (`Id`),
  CONSTRAINT `iso_minutas_as_ibfk_7` FOREIGN KEY (`IdProf`) REFERENCES `profesionales` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iso_minutas_des`
--

DROP TABLE IF EXISTS `iso_minutas_des`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iso_minutas_des` (
  `Id` int(11) NOT NULL,
  `IdMin` int(11) NOT NULL,
  `Obs` text NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdMin` (`IdMin`),
  CONSTRAINT `iso_minutas_des_ibfk_1` FOREIGN KEY (`IdMin`) REFERENCES `iso_minutas` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iso_minutas_pd`
--

DROP TABLE IF EXISTS `iso_minutas_pd`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iso_minutas_pd` (
  `Id` int(11) NOT NULL,
  `IdMin` int(11) NOT NULL,
  `Obs` text NOT NULL,
  `IdPersonal` int(11) NOT NULL,
  `IdProf` int(11) NOT NULL,
  `Estado` tinyint(4) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdMin` (`IdMin`),
  KEY `IdPersonal` (`IdPersonal`),
  KEY `IdProf` (`IdProf`),
  CONSTRAINT `iso_minutas_pd_ibfk_1` FOREIGN KEY (`IdMin`) REFERENCES `iso_minutas` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `iso_minutas_pd_ibfk_2` FOREIGN KEY (`IdPersonal`) REFERENCES `personal` (`Id`),
  CONSTRAINT `iso_minutas_pd_ibfk_3` FOREIGN KEY (`IdProf`) REFERENCES `profesionales` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iso_minutasarchivos`
--

DROP TABLE IF EXISTS `iso_minutasarchivos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iso_minutasarchivos` (
  `Id` int(11) NOT NULL DEFAULT '0',
  `IdEntidad` int(11) NOT NULL DEFAULT '0',
  `Descripcion` char(100) NOT NULL,
  `Ruta` char(50) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdEntidad` (`IdEntidad`),
  CONSTRAINT `iso_minutasarchivos_ibfk_1` FOREIGN KEY (`IdEntidad`) REFERENCES `iso_minutas` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `itemsfacturacompra`
--

DROP TABLE IF EXISTS `itemsfacturacompra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `itemsfacturacompra` (
  `Id` int(11) NOT NULL,
  `IdFactura` int(11) NOT NULL,
  `IdItemPrestacion` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `IdItemPrestacion` (`IdItemPrestacion`),
  KEY `IdFactura` (`IdFactura`),
  CONSTRAINT `itemsfacturacompra_ibfk_1` FOREIGN KEY (`IdFactura`) REFERENCES `facturascompra` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `itemsfacturacompra_ibfk_2` FOREIGN KEY (`IdItemPrestacion`) REFERENCES `itemsprestaciones` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `itemsfacturacompra2`
--

DROP TABLE IF EXISTS `itemsfacturacompra2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `itemsfacturacompra2` (
  `Id` int(11) NOT NULL,
  `IdFactura` int(11) NOT NULL,
  `IdItemPrestacion` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `IdItemPrestacion` (`IdItemPrestacion`),
  KEY `IdFactura` (`IdFactura`),
  CONSTRAINT `itemsfacturacompra2_ibfk_1` FOREIGN KEY (`IdFactura`) REFERENCES `facturascompra` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `itemsfacturacompra2_ibfk_2` FOREIGN KEY (`IdItemPrestacion`) REFERENCES `itemsprestaciones` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `itemsfacturaventa`
--

DROP TABLE IF EXISTS `itemsfacturaventa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `itemsfacturaventa` (
  `Id` int(11) NOT NULL DEFAULT '0',
  `IdFactura` int(11) NOT NULL DEFAULT '0',
  `IdPrestacion` int(11) NOT NULL DEFAULT '0',
  `Detalle` text NOT NULL,
  `Anulado` tinyint(1) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdFactura` (`IdFactura`),
  KEY `IdPrestacion` (`IdPrestacion`),
  CONSTRAINT `itemsfacturaventa_ibfk_1` FOREIGN KEY (`IdFactura`) REFERENCES `facturasventa` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `itemsfacturaventa_ibfk_2` FOREIGN KEY (`IdPrestacion`) REFERENCES `prestaciones` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `itemsprestaciones`
--

DROP TABLE IF EXISTS `itemsprestaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `itemsprestaciones` (
  `Id` int(11) NOT NULL,
  `IdPrestacion` int(11) NOT NULL DEFAULT '0',
  `IdExamen` int(11) NOT NULL DEFAULT '0',
  `ObsExamen` text NOT NULL,
  `IdProveedor` int(11) NOT NULL DEFAULT '0',
  `IdProfesional` int(11) NOT NULL DEFAULT '0',
  `IdProfesional2` int(11) NOT NULL,
  `FechaPagado` date NOT NULL DEFAULT '0000-00-00',
  `FechaPagado2` date NOT NULL DEFAULT '0000-00-00',
  `Anulado` tinyint(1) NOT NULL DEFAULT '0',
  `Fecha` date NOT NULL DEFAULT '0000-00-00',
  `FechaAsignado` date NOT NULL DEFAULT '0000-00-00',
  `Facturado` tinyint(1) NOT NULL DEFAULT '0',
  `NumeroFacturaVta` int(11) NOT NULL DEFAULT '0',
  `VtoItem` int(11) NOT NULL DEFAULT '0',
  `Honorarios` decimal(10,2) NOT NULL DEFAULT '0.00',
  `NroFactCompra` int(11) NOT NULL,
  `NroFactCompra2` int(11) NOT NULL,
  `Incompleto` tinyint(1) NOT NULL,
  `HoraAsignado` time NOT NULL DEFAULT '00:00:00',
  `HoraFAsignado` time NOT NULL DEFAULT '00:00:00',
  `SinEsc` tinyint(1) NOT NULL,
  `Forma` tinyint(1) NOT NULL,
  `Ausente` tinyint(1) NOT NULL,
  `Devol` tinyint(1) NOT NULL,
  `CInfo` tinyint(1) NOT NULL,
  `CAdj` tinyint(1) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdPrestacion` (`IdPrestacion`),
  KEY `IdExamen` (`IdExamen`),
  KEY `IdProveedor` (`IdProveedor`),
  KEY `IdProfesional` (`IdProfesional`),
  KEY `NumeroFacturaVta` (`NumeroFacturaVta`),
  KEY `NroFactCompra` (`NroFactCompra`),
  KEY `IdProfesional2` (`IdProfesional2`),
  KEY `NroFactCompra2` (`NroFactCompra2`),
  CONSTRAINT `itemsprestaciones_ibfk_1` FOREIGN KEY (`IdPrestacion`) REFERENCES `prestaciones` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `itemsprestaciones_ibfk_2` FOREIGN KEY (`IdExamen`) REFERENCES `examenes` (`Id`),
  CONSTRAINT `itemsprestaciones_ibfk_3` FOREIGN KEY (`IdProveedor`) REFERENCES `proveedores` (`Id`),
  CONSTRAINT `itemsprestaciones_ibfk_4` FOREIGN KEY (`IdProfesional`) REFERENCES `profesionales` (`Id`),
  CONSTRAINT `itemsprestaciones_ibfk_5` FOREIGN KEY (`NumeroFacturaVta`) REFERENCES `facturasventa` (`Id`),
  CONSTRAINT `itemsprestaciones_ibfk_6` FOREIGN KEY (`IdProfesional2`) REFERENCES `profesionales` (`Id`),
  CONSTRAINT `itemsprestaciones_ibfk_7` FOREIGN KEY (`NroFactCompra2`) REFERENCES `facturascompra` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `itemsprestaciones_info`
--

DROP TABLE IF EXISTS `itemsprestaciones_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `itemsprestaciones_info` (
  `Id` int(11) NOT NULL,
  `IdIP` int(11) NOT NULL,
  `IdP` int(11) NOT NULL,
  `Obs` text NOT NULL,
  `C1` tinyint(1) NOT NULL,
  `C2` tinyint(1) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `IdIP` (`IdIP`),
  KEY `IdP` (`IdP`),
  CONSTRAINT `itemsprestaciones_info_ibfk_1` FOREIGN KEY (`IdIP`) REFERENCES `itemsprestaciones` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `localidades`
--

DROP TABLE IF EXISTS `localidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `localidades` (
  `Id` int(11) NOT NULL,
  `Nombre` char(30) NOT NULL,
  `CP` char(20) NOT NULL,
  `IdPcia` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Nombre` (`Nombre`),
  KEY `IdPcia` (`IdPcia`),
  CONSTRAINT `localidades_ibfk_1` FOREIGN KEY (`IdPcia`) REFERENCES `provincias` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mapas`
--

DROP TABLE IF EXISTS `mapas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mapas` (
  `Id` int(11) NOT NULL,
  `Nro` char(8) NOT NULL,
  `Fecha` date NOT NULL DEFAULT '0000-00-00',
  `IdART` int(11) NOT NULL,
  `IdEmpresa` int(11) NOT NULL,
  `Obs` text NOT NULL,
  `Inactivo` tinyint(4) NOT NULL,
  `FechaE` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Nro` (`Nro`,`IdART`),
  KEY `IdART` (`IdART`),
  KEY `IdEmpresa` (`IdEmpresa`),
  CONSTRAINT `mapas_ibfk_1` FOREIGN KEY (`IdART`) REFERENCES `clientes` (`Id`),
  CONSTRAINT `mapas_ibfk_2` FOREIGN KEY (`IdEmpresa`) REFERENCES `clientes` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `marcas`
--

DROP TABLE IF EXISTS `marcas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `marcas` (
  `Id` int(11) NOT NULL,
  `Nombre` char(30) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Nombre` (`Nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notascredito`
--

DROP TABLE IF EXISTS `notascredito`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notascredito` (
  `Id` int(11) NOT NULL DEFAULT '0',
  `Tipo` char(2) NOT NULL,
  `Sucursal` int(4) NOT NULL,
  `Nro` bigint(8) NOT NULL,
  `Fecha` date NOT NULL DEFAULT '0000-00-00',
  `IdEmpresa` int(11) NOT NULL DEFAULT '0',
  `TipoCliente` char(1) NOT NULL,
  `IdFactura` int(11) NOT NULL,
  `IdP` int(11) NOT NULL,
  `TipoNC` tinyint(4) NOT NULL,
  `Obs` char(200) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdEmpresa` (`IdEmpresa`),
  KEY `IdFactura` (`IdFactura`),
  KEY `IdP` (`IdP`),
  KEY `Fecha` (`Fecha`),
  CONSTRAINT `notascredito_ibfk_1` FOREIGN KEY (`IdEmpresa`) REFERENCES `clientes` (`Id`),
  CONSTRAINT `notascredito_ibfk_2` FOREIGN KEY (`IdFactura`) REFERENCES `facturasventa` (`Id`),
  CONSTRAINT `notascredito_ibfk_3` FOREIGN KEY (`IdP`) REFERENCES `prestaciones` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notascredito_it`
--

DROP TABLE IF EXISTS `notascredito_it`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notascredito_it` (
  `Id` int(11) NOT NULL,
  `IdNC` int(11) NOT NULL,
  `IdIP` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdNC` (`IdNC`),
  KEY `IdIP` (`IdIP`),
  CONSTRAINT `notascredito_it_ibfk_1` FOREIGN KEY (`IdNC`) REFERENCES `notascredito` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `notascredito_it_ibfk_3` FOREIGN KEY (`IdIP`) REFERENCES `itemsprestaciones` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `noticias`
--

DROP TABLE IF EXISTS `noticias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `noticias` (
  `Id` int(11) NOT NULL,
  `Titulo` char(40) NOT NULL,
  `Subtitulo` char(40) NOT NULL,
  `Texto` text NOT NULL,
  `Urgente` tinyint(4) NOT NULL,
  `Ruta` char(50) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pacientes`
--

DROP TABLE IF EXISTS `pacientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pacientes` (
  `Id` int(11) NOT NULL,
  `TipoIdentificacion` char(5) NOT NULL,
  `Identificacion` char(13) NOT NULL,
  `TipoDocumento` char(5) NOT NULL,
  `Documento` char(13) NOT NULL,
  `Nacionalidad` char(30) NOT NULL,
  `Sexo` char(1) NOT NULL,
  `Nombre` char(30) NOT NULL,
  `Apellido` char(30) NOT NULL,
  `FechaNacimiento` date NOT NULL DEFAULT '0000-00-00',
  `LugarNacimiento` char(50) NOT NULL,
  `EstadoCivil` char(15) NOT NULL,
  `ObsEstadoCivil` char(100) NOT NULL,
  `Hijos` tinyint(4) NOT NULL DEFAULT '0',
  `Direccion` char(200) NOT NULL,
  `IdLocalidad` int(11) NOT NULL DEFAULT '0',
  `Provincia` char(30) NOT NULL,
  `CP` char(10) NOT NULL,
  `EMail` char(50) NOT NULL,
  `ObsEMail` char(100) NOT NULL,
  `Foto` char(50) NOT NULL,
  `Antecedentes` text NOT NULL,
  `Observaciones` text NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdLocalidad` (`IdLocalidad`),
  KEY `Documento` (`Documento`),
  KEY `Nombre` (`Nombre`),
  KEY `Apellido` (`Apellido`),
  CONSTRAINT `pacientes_ibfk_1` FOREIGN KEY (`IdLocalidad`) REFERENCES `localidades` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pagosacuenta`
--

DROP TABLE IF EXISTS `pagosacuenta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pagosacuenta` (
  `Id` int(11) NOT NULL DEFAULT '0',
  `IdEmpresa` int(11) NOT NULL DEFAULT '0',
  `Fecha` date NOT NULL DEFAULT '0000-00-00',
  `Tipo` char(1) NOT NULL,
  `Suc` int(4) NOT NULL,
  `Nro` bigint(8) NOT NULL,
  `Obs` char(200) NOT NULL,
  `Pagado` tinyint(1) NOT NULL,
  `FechaP` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`Id`),
  KEY `IdEmpresa` (`IdEmpresa`),
  CONSTRAINT `pagosacuenta_ibfk_1` FOREIGN KEY (`IdEmpresa`) REFERENCES `clientes` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pagosacuenta_it`
--

DROP TABLE IF EXISTS `pagosacuenta_it`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pagosacuenta_it` (
  `Id` int(11) NOT NULL,
  `IdPago` int(11) NOT NULL,
  `IdExamen` int(11) NOT NULL,
  `IdPrestacion` int(11) NOT NULL,
  `Obs` char(100) NOT NULL,
  `Obs2` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdPago` (`IdPago`),
  KEY `IdExamen` (`IdExamen`),
  KEY `IdPrestacion` (`IdPrestacion`),
  CONSTRAINT `pagosacuenta_it_ibfk_2` FOREIGN KEY (`IdExamen`) REFERENCES `examenes` (`Id`),
  CONSTRAINT `pagosacuenta_it_ibfk_3` FOREIGN KEY (`IdPrestacion`) REFERENCES `prestaciones` (`Id`),
  CONSTRAINT `pagosacuenta_it_ibfk_4` FOREIGN KEY (`IdPago`) REFERENCES `pagosacuenta` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paqestudios`
--

DROP TABLE IF EXISTS `paqestudios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paqestudios` (
  `Id` int(11) NOT NULL,
  `Nombre` char(50) NOT NULL,
  `Descripcion` char(100) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Nombre` (`Nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paqfacturacion`
--

DROP TABLE IF EXISTS `paqfacturacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paqfacturacion` (
  `Id` int(11) NOT NULL,
  `Nombre` char(50) NOT NULL,
  `Descripcion` char(100) NOT NULL,
  `CantExamenes` tinyint(4) NOT NULL DEFAULT '0',
  `IdEmpresa` int(11) NOT NULL,
  `IdGrupo` int(11) NOT NULL,
  `Cod` char(10) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Nombre` (`Nombre`),
  KEY `IdEmpresa` (`IdEmpresa`),
  KEY `IdGrupo` (`IdGrupo`),
  CONSTRAINT `paqfacturacion_ibfk_1` FOREIGN KEY (`IdEmpresa`) REFERENCES `clientes` (`Id`),
  CONSTRAINT `paqfacturacion_ibfk_2` FOREIGN KEY (`IdGrupo`) REFERENCES `clientesgrupos` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `parametros`
--

DROP TABLE IF EXISTS `parametros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parametros` (
  `Id` int(11) NOT NULL,
  `RazonSocial` char(50) NOT NULL,
  `CUIT` char(13) NOT NULL,
  `Logo` char(50) NOT NULL,
  `NombreFantasia` char(50) NOT NULL,
  `Direccion` char(100) NOT NULL,
  `IdLocalidad` int(11) NOT NULL DEFAULT '0',
  `Provincia` char(30) NOT NULL,
  `CP` char(10) NOT NULL,
  `EMail` char(50) NOT NULL,
  `Pagina` char(30) NOT NULL,
  `Telefono` char(50) NOT NULL,
  `ProfesionalRadiologo` text NOT NULL,
  `IdProveedorPlacas` int(11) NOT NULL DEFAULT '0',
  `ServerSMTP` char(40) NOT NULL,
  `ServerPOP` char(40) NOT NULL,
  `Usuario` char(30) NOT NULL,
  `Password` char(45) NOT NULL,
  `MailFrom` char(30) NOT NULL,
  `MailToAlertas` char(150) NOT NULL,
  `IdCliCarnet` int(11) NOT NULL,
  `MailFromFact` char(30) NOT NULL,
  `MailFromMasivo` char(30) NOT NULL,
  `NombreMailI` char(40) NOT NULL,
  `NombreMailF` char(40) NOT NULL,
  `NombreMailM` char(40) NOT NULL,
  `MailAudi` char(30) NOT NULL,
  `MailMsg` char(30) NOT NULL,
  `RutaMasivos` char(50) NOT NULL,
  `Path1` char(100) NOT NULL,
  `Path2` char(100) NOT NULL,
  `Path3` char(100) NOT NULL,
  `Path4` char(100) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdLocalidad` (`IdLocalidad`),
  KEY `IdProveedorPlacas` (`IdProveedorPlacas`),
  KEY `IdCliCarnet` (`IdCliCarnet`),
  CONSTRAINT `parametros_ibfk_1` FOREIGN KEY (`IdLocalidad`) REFERENCES `localidades` (`Id`),
  CONSTRAINT `parametros_ibfk_2` FOREIGN KEY (`IdProveedorPlacas`) REFERENCES `proveedores` (`Id`),
  CONSTRAINT `parametros_ibfk_3` FOREIGN KEY (`IdCliCarnet`) REFERENCES `clientes` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `parametros_carnet`
--

DROP TABLE IF EXISTS `parametros_carnet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parametros_carnet` (
  `Id` int(11) NOT NULL,
  `IdParametro` int(11) NOT NULL,
  `IdEstudio` int(11) NOT NULL,
  `IdExamen` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdParametro` (`IdParametro`),
  KEY `IdEstudio` (`IdEstudio`),
  KEY `IdExamen` (`IdExamen`),
  CONSTRAINT `parametros_carnet_ibfk_1` FOREIGN KEY (`IdParametro`) REFERENCES `parametros` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `parametros_carnet_ibfk_2` FOREIGN KEY (`IdEstudio`) REFERENCES `estudios` (`Id`),
  CONSTRAINT `parametros_carnet_ibfk_3` FOREIGN KEY (`IdExamen`) REFERENCES `examenes` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `parametros_pcr`
--

DROP TABLE IF EXISTS `parametros_pcr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parametros_pcr` (
  `Id` int(11) NOT NULL,
  `IdParametro` int(11) NOT NULL,
  `IdEstudio` int(11) NOT NULL,
  `IdExamen` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdParametro` (`IdParametro`),
  KEY `IdEstudio` (`IdEstudio`),
  KEY `IdExamen` (`IdExamen`),
  CONSTRAINT `parametros_pcr_ibfk_1` FOREIGN KEY (`IdParametro`) REFERENCES `parametros` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `parametros_pcr_ibfk_2` FOREIGN KEY (`IdEstudio`) REFERENCES `estudios` (`Id`),
  CONSTRAINT `parametros_pcr_ibfk_3` FOREIGN KEY (`IdExamen`) REFERENCES `examenes` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `perfiles`
--

DROP TABLE IF EXISTS `perfiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `perfiles` (
  `Id` int(11) NOT NULL DEFAULT '0',
  `Nombre` char(30) NOT NULL,
  `Tipo` tinyint(1) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Nombre` (`Nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `personal`
--

DROP TABLE IF EXISTS `personal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal` (
  `Id` int(11) NOT NULL,
  `TipoIdentificacion` char(4) NOT NULL,
  `Identificacion` char(13) NOT NULL,
  `TipoDocumento` char(3) NOT NULL,
  `Documento` char(13) NOT NULL,
  `Nacionalidad` char(20) NOT NULL,
  `Sexo` char(1) NOT NULL,
  `Nombre` char(30) NOT NULL,
  `Apellido` char(30) NOT NULL,
  `FechaNacimiento` date NOT NULL DEFAULT '0000-00-00',
  `LugarNacimiento` char(50) NOT NULL,
  `EstadoCivil` char(20) NOT NULL,
  `ObsEstadoCivil` char(100) NOT NULL,
  `Hijos` tinyint(4) NOT NULL DEFAULT '0',
  `Direccion` char(200) NOT NULL,
  `IdLocalidad` int(11) NOT NULL DEFAULT '0',
  `Provincia` char(30) NOT NULL,
  `CP` char(10) NOT NULL,
  `EMail` char(30) NOT NULL,
  `ObsEMail` char(100) NOT NULL,
  `Inactivo` tinyint(4) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdLocalidad` (`IdLocalidad`),
  CONSTRAINT `personal_ibfk_1` FOREIGN KEY (`IdLocalidad`) REFERENCES `localidades` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `preciosxcod`
--

DROP TABLE IF EXISTS `preciosxcod`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `preciosxcod` (
  `Id` int(11) NOT NULL,
  `Cod` char(10) NOT NULL,
  `Precio` decimal(10,2) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Cod` (`Cod`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prestaciones`
--

DROP TABLE IF EXISTS `prestaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prestaciones` (
  `Id` int(11) NOT NULL,
  `IdPaciente` int(11) NOT NULL DEFAULT '0',
  `IdEmpresa` int(11) NOT NULL DEFAULT '0',
  `IdART` int(11) NOT NULL DEFAULT '0',
  `TipoPrestacion` char(12) NOT NULL,
  `Fecha` date NOT NULL DEFAULT '0000-00-00',
  `Anulado` tinyint(1) NOT NULL DEFAULT '0',
  `Cerrado` tinyint(1) NOT NULL DEFAULT '0',
  `Entregado` tinyint(1) NOT NULL DEFAULT '0',
  `Facturado` tinyint(1) NOT NULL DEFAULT '0',
  `ObsAnulado` char(100) NOT NULL,
  `Evaluacion` char(70) NOT NULL,
  `Calificacion` char(70) NOT NULL,
  `Observaciones` text NOT NULL,
  `NumeroFacturaVta` int(11) NOT NULL DEFAULT '0',
  `FechaCierre` date NOT NULL DEFAULT '0000-00-00',
  `FechaEntrega` date NOT NULL DEFAULT '0000-00-00',
  `FechaFact` date NOT NULL DEFAULT '0000-00-00',
  `FechaAnul` date NOT NULL DEFAULT '0000-00-00',
  `Finalizado` tinyint(1) NOT NULL DEFAULT '0',
  `FechaFinalizado` date NOT NULL DEFAULT '0000-00-00',
  `ObsExamenes` text NOT NULL,
  `Vto` int(11) NOT NULL DEFAULT '0',
  `FechaVto` date NOT NULL DEFAULT '0000-00-00',
  `NroCEE` int(11) NOT NULL DEFAULT '0',
  `Pago` char(1) NOT NULL,
  `SPago` char(1) NOT NULL,
  `TSN` char(15) NOT NULL,
  `FechaT` date NOT NULL DEFAULT '0000-00-00',
  `Incompleto` tinyint(1) NOT NULL,
  `AutorizaSC` char(30) NOT NULL,
  `RxPreliminar` tinyint(1) NOT NULL,
  `IdMapa` int(11) NOT NULL,
  `SinEsc` tinyint(1) NOT NULL,
  `Forma` tinyint(1) NOT NULL,
  `Ausente` tinyint(1) NOT NULL,
  `Devol` tinyint(1) NOT NULL,
  `IdEvaluador` int(11) NOT NULL,
  `eEnviado` tinyint(1) NOT NULL,
  `FechaEnviado` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`Id`),
  KEY `IdPaciente` (`IdPaciente`),
  KEY `IdEmpresa` (`IdEmpresa`),
  KEY `IdART` (`IdART`),
  KEY `NumeroFacturaVta` (`NumeroFacturaVta`),
  KEY `IdMapa` (`IdMapa`),
  KEY `NroCEE` (`NroCEE`),
  KEY `Fecha` (`Fecha`),
  KEY `IdEvaluador` (`IdEvaluador`),
  CONSTRAINT `prestaciones_ibfk_1` FOREIGN KEY (`IdPaciente`) REFERENCES `pacientes` (`Id`),
  CONSTRAINT `prestaciones_ibfk_2` FOREIGN KEY (`IdEmpresa`) REFERENCES `clientes` (`Id`),
  CONSTRAINT `prestaciones_ibfk_3` FOREIGN KEY (`IdART`) REFERENCES `clientes` (`Id`),
  CONSTRAINT `prestaciones_ibfk_4` FOREIGN KEY (`NumeroFacturaVta`) REFERENCES `facturasventa` (`Id`),
  CONSTRAINT `prestaciones_ibfk_5` FOREIGN KEY (`IdMapa`) REFERENCES `mapas` (`Id`),
  CONSTRAINT `prestaciones_ibfk_6` FOREIGN KEY (`IdEvaluador`) REFERENCES `profesionales` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prestaciones_atributos`
--

DROP TABLE IF EXISTS `prestaciones_atributos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prestaciones_atributos` (
  `Id` int(11) NOT NULL,
  `IdPadre` int(11) NOT NULL,
  `SinEval` tinyint(1) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `IdPadre` (`IdPadre`),
  CONSTRAINT `prestaciones_atributos_ibfk_1` FOREIGN KEY (`IdPadre`) REFERENCES `prestaciones` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prestaciones_comentarios`
--

DROP TABLE IF EXISTS `prestaciones_comentarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prestaciones_comentarios` (
  `Id` int(11) NOT NULL,
  `IdP` int(11) NOT NULL,
  `Obs` text NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `IdP_2` (`IdP`),
  CONSTRAINT `prestaciones_comentarios_ibfk_1` FOREIGN KEY (`IdP`) REFERENCES `prestaciones` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prestaciones_obsfases`
--

DROP TABLE IF EXISTS `prestaciones_obsfases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prestaciones_obsfases` (
  `Id` int(11) NOT NULL DEFAULT '0',
  `IdEntidad` int(11) NOT NULL DEFAULT '0',
  `Comentario` text NOT NULL,
  `IdUsuario` char(20) NOT NULL,
  `Fecha` date NOT NULL DEFAULT '0000-00-00',
  `Rol` tinyint(4) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdEntidad` (`IdEntidad`),
  KEY `IdUsuario` (`IdUsuario`),
  CONSTRAINT `prestaciones_obsfases_ibfk_1` FOREIGN KEY (`IdEntidad`) REFERENCES `prestaciones` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `prestaciones_obsfases_ibfk_2` FOREIGN KEY (`IdUsuario`) REFERENCES `usuarios` (`Usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prestaciones_tipo`
--

DROP TABLE IF EXISTS `prestaciones_tipo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prestaciones_tipo` (
  `Id` int(11) NOT NULL,
  `Nombre` char(20) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Nombre` (`Nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prestacionespaqfact`
--

DROP TABLE IF EXISTS `prestacionespaqfact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prestacionespaqfact` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `IdPrestacion` int(11) NOT NULL,
  `IdItem` int(11) NOT NULL,
  `IdExamen` int(11) NOT NULL,
  `IdPaqFact` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdPrestacion` (`IdPrestacion`),
  KEY `IdExamen` (`IdExamen`)
) ENGINE=InnoDB AUTO_INCREMENT=2151323 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `profesionales`
--

DROP TABLE IF EXISTS `profesionales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profesionales` (
  `Id` int(11) NOT NULL,
  `TipoIdentificacion` char(4) NOT NULL,
  `Identificacion` char(13) NOT NULL,
  `TipoDocumento` char(3) NOT NULL,
  `Documento` char(13) NOT NULL,
  `Nombre` char(30) NOT NULL,
  `Apellido` char(30) NOT NULL,
  `Direccion` char(200) NOT NULL,
  `IdLocalidad` int(11) NOT NULL DEFAULT '0',
  `Provincia` char(30) NOT NULL,
  `CP` char(10) NOT NULL,
  `EMail` char(40) NOT NULL,
  `ObsEMail` char(100) NOT NULL,
  `MP` char(50) NOT NULL,
  `MN` char(50) NOT NULL,
  `IdProveedor` int(11) NOT NULL DEFAULT '0',
  `SeguroMP` date NOT NULL DEFAULT '0000-00-00',
  `Inactivo` tinyint(4) NOT NULL,
  `Firma` text NOT NULL,
  `Foto` char(50) NOT NULL,
  `T1` tinyint(4) NOT NULL,
  `T2` tinyint(4) NOT NULL,
  `T3` tinyint(4) NOT NULL,
  `T4` tinyint(1) NOT NULL,
  `TMP` tinyint(1) NOT NULL,
  `Pago` tinyint(1) NOT NULL,
  `TLP` tinyint(1) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdLocalidad` (`IdLocalidad`),
  KEY `IdProveedor` (`IdProveedor`),
  CONSTRAINT `profesionales_ibfk_1` FOREIGN KEY (`IdLocalidad`) REFERENCES `localidades` (`Id`),
  CONSTRAINT `profesionales_ibfk_2` FOREIGN KEY (`IdProveedor`) REFERENCES `proveedores` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `profesionales_prov`
--

DROP TABLE IF EXISTS `profesionales_prov`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profesionales_prov` (
  `Id` int(11) NOT NULL,
  `IdProf` int(11) NOT NULL,
  `IdProv` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `IdProf_2` (`IdProf`,`IdProv`),
  KEY `IdProf` (`IdProf`),
  KEY `IdProv` (`IdProv`),
  CONSTRAINT `profesionales_prov_ibfk_1` FOREIGN KEY (`IdProf`) REFERENCES `profesionales` (`Id`),
  CONSTRAINT `profesionales_prov_ibfk_2` FOREIGN KEY (`IdProv`) REFERENCES `proveedores` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `profesionalestipo`
--

DROP TABLE IF EXISTS `profesionalestipo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profesionalestipo` (
  `Id` int(11) NOT NULL,
  `Nombre` char(15) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Nombre` (`Nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proveedores` (
  `Id` int(11) NOT NULL,
  `Nombre` char(40) NOT NULL,
  `Telefono` char(60) NOT NULL,
  `Direccion` char(200) NOT NULL,
  `IdLocalidad` int(11) NOT NULL DEFAULT '0',
  `Inactivo` tinyint(1) NOT NULL,
  `Min` tinyint(4) NOT NULL,
  `PR` tinyint(4) NOT NULL,
  `Multi` tinyint(1) NOT NULL,
  `MultiE` tinyint(1) NOT NULL,
  `InfAdj` tinyint(1) NOT NULL,
  `Externo` tinyint(1) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Nombre` (`Nombre`),
  KEY `IdLocalidad` (`IdLocalidad`),
  CONSTRAINT `proveedores_ibfk_1` FOREIGN KEY (`IdLocalidad`) REFERENCES `localidades` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `provincias`
--

DROP TABLE IF EXISTS `provincias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `provincias` (
  `Id` int(11) NOT NULL,
  `Nombre` char(30) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Nombre` (`Nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `relpaqest`
--

DROP TABLE IF EXISTS `relpaqest`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `relpaqest` (
  `Id` int(11) NOT NULL,
  `IdPaquete` int(11) NOT NULL DEFAULT '0',
  `IdEstudio` int(11) NOT NULL DEFAULT '0',
  `IdExamen` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  KEY `IdPaquete` (`IdPaquete`),
  KEY `IdEstudio` (`IdEstudio`),
  KEY `IdExamen` (`IdExamen`),
  CONSTRAINT `relpaqest_ibfk_1` FOREIGN KEY (`IdPaquete`) REFERENCES `paqestudios` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `relpaqest_ibfk_2` FOREIGN KEY (`IdEstudio`) REFERENCES `estudios` (`Id`),
  CONSTRAINT `relpaqest_ibfk_3` FOREIGN KEY (`IdExamen`) REFERENCES `examenes` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `relpaqfact`
--

DROP TABLE IF EXISTS `relpaqfact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `relpaqfact` (
  `Id` int(11) NOT NULL,
  `IdPaquete` int(11) NOT NULL DEFAULT '0',
  `IdEstudio` int(11) NOT NULL DEFAULT '0',
  `IdExamen` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  KEY `IdPaquete` (`IdPaquete`),
  KEY `IdEstudio` (`IdEstudio`),
  KEY `IdExamen` (`IdExamen`),
  CONSTRAINT `relpaqfact_ibfk_1` FOREIGN KEY (`IdPaquete`) REFERENCES `paqfacturacion` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `relpaqfact_ibfk_2` FOREIGN KEY (`IdEstudio`) REFERENCES `estudios` (`Id`),
  CONSTRAINT `relpaqfact_ibfk_3` FOREIGN KEY (`IdExamen`) REFERENCES `examenes` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reportes`
--

DROP TABLE IF EXISTS `reportes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reportes` (
  `Id` int(11) NOT NULL,
  `Nombre` char(50) NOT NULL,
  `IdReporte` char(20) NOT NULL,
  `Inactivo` tinyint(4) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Nombre` (`Nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reportesf`
--

DROP TABLE IF EXISTS `reportesf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reportesf` (
  `Id` int(11) NOT NULL,
  `Nombre` char(50) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Nombre` (`Nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rubros`
--

DROP TABLE IF EXISTS `rubros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rubros` (
  `Id` int(11) NOT NULL,
  `Nombre` char(30) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Nombre` (`Nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sector`
--

DROP TABLE IF EXISTS `sector`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sector` (
  `Id` int(11) NOT NULL,
  `Nombre` char(30) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Nombre` (`Nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stock_tipomov`
--

DROP TABLE IF EXISTS `stock_tipomov`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_tipomov` (
  `Id` int(11) NOT NULL,
  `Nombre` char(20) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Nombre` (`Nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stockdepositos`
--

DROP TABLE IF EXISTS `stockdepositos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stockdepositos` (
  `Id` int(11) NOT NULL,
  `IdDeposito` int(11) NOT NULL,
  `IdArticulo` int(11) NOT NULL,
  `Stock` decimal(10,2) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdDeposito` (`IdDeposito`),
  KEY `IdArticulo` (`IdArticulo`),
  CONSTRAINT `stockdepositos_ibfk_1` FOREIGN KEY (`IdDeposito`) REFERENCES `depositos` (`Id`),
  CONSTRAINT `stockdepositos_ibfk_2` FOREIGN KEY (`IdArticulo`) REFERENCES `epparticulos` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stockmov`
--

DROP TABLE IF EXISTS `stockmov`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stockmov` (
  `Id` int(11) NOT NULL,
  `IdTipoMov` int(11) NOT NULL,
  `Fecha` date NOT NULL DEFAULT '0000-00-00',
  `IdDeposito` int(11) NOT NULL,
  `Obs` char(200) NOT NULL,
  `Anulado` tinyint(4) NOT NULL,
  `Tipo` char(1) NOT NULL,
  `Suc` int(11) NOT NULL,
  `Nro` int(11) NOT NULL,
  `IdPersonal` int(11) NOT NULL,
  `IdProf` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdTipoMov` (`IdTipoMov`),
  KEY `IdDeposito` (`IdDeposito`),
  KEY `IdPersonal` (`IdPersonal`),
  KEY `IdProf` (`IdProf`),
  CONSTRAINT `stockmov_ibfk_1` FOREIGN KEY (`IdTipoMov`) REFERENCES `stock_tipomov` (`Id`),
  CONSTRAINT `stockmov_ibfk_3` FOREIGN KEY (`IdDeposito`) REFERENCES `depositos` (`Id`),
  CONSTRAINT `stockmov_ibfk_6` FOREIGN KEY (`IdPersonal`) REFERENCES `personal` (`Id`),
  CONSTRAINT `stockmov_ibfk_7` FOREIGN KEY (`IdProf`) REFERENCES `profesionales` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stockmov_items`
--

DROP TABLE IF EXISTS `stockmov_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stockmov_items` (
  `Id` int(11) NOT NULL,
  `IdMov` int(11) NOT NULL,
  `IdArticulo` int(11) NOT NULL,
  `Cantidad` decimal(10,2) NOT NULL,
  `CantidadCS` decimal(10,2) NOT NULL,
  `FV` date NOT NULL DEFAULT '0000-00-00',
  `Lote` char(10) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdMov` (`IdMov`),
  KEY `IdArticulo` (`IdArticulo`),
  CONSTRAINT `stockmov_items_ibfk_1` FOREIGN KEY (`IdMov`) REFERENCES `stockmov` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `stockmov_items_ibfk_2` FOREIGN KEY (`IdArticulo`) REFERENCES `epparticulos` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `telefonos`
--

DROP TABLE IF EXISTS `telefonos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `telefonos` (
  `Id` int(11) NOT NULL,
  `IdEntidad` int(11) NOT NULL DEFAULT '0',
  `CodigoArea` char(6) NOT NULL,
  `NumeroTelefono` char(20) NOT NULL,
  `Observaciones` char(200) NOT NULL,
  `TipoEntidad` char(1) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `IdEntidad` (`IdEntidad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unidadesmedida`
--

DROP TABLE IF EXISTS `unidadesmedida`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unidadesmedida` (
  `Id` int(11) NOT NULL,
  `Nombre` char(20) NOT NULL,
  `Abr` char(3) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Nombre` (`Nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `Usuario` char(20) NOT NULL,
  `Puesto` char(20) NOT NULL,
  `Password` char(20) NOT NULL,
  `IdPerfil` int(11) NOT NULL DEFAULT '0',
  `Tipo` char(20) NOT NULL,
  `IdProfesional` int(11) NOT NULL DEFAULT '0',
  `IdPersonal` int(11) NOT NULL DEFAULT '0',
  `SR` tinyint(1) NOT NULL,
  PRIMARY KEY (`Usuario`),
  KEY `IdPerfil` (`IdPerfil`),
  KEY `IdProfesional` (`IdProfesional`),
  KEY `IdPersonal` (`IdPersonal`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`IdProfesional`) REFERENCES `profesionales` (`Id`),
  CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`IdPersonal`) REFERENCES `personal` (`Id`),
  CONSTRAINT `usuarios_ibfk_3` FOREIGN KEY (`IdPerfil`) REFERENCES `perfiles` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-24 14:56:22
