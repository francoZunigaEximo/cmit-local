INSERT INTO `users` (`id`, `name`, `email`, `idProfesional`, `idPersonal`, `update`, `SR`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'nicolas', 'nmaximowicz@eximo.com.ar', 0, 0, '1', 1, NULL, '$2y$10$RlV1zDRQEplFMOyUHSXTG.bkCN5rWCKXGk/DVMeN5mqTKORrIxiAy', NULL, '2023-05-04 15:19:58', '2023-05-04 15:19:58'),
(2, 'lucas', 'lucas@cmit.com.ar', 1, 1, '0', 1, NULL, '$2y$10$5xxjrbNwlDFSu/Q.wAvCMeJ/URPDh57efpgaPTfflvk2fmM74CzHK', NULL, '2023-05-08 16:27:18', '2023-05-08 16:27:18');
/* Solucionamos los problemas de fechas invalidas */
SET SQL_MODE='ALLOW_INVALID_DATES'; 
--Clientes
/* Cambio de default error a null */
UPDATE clientes SET EnvioInforme = NULL WHERE CAST(EnvioInforme AS CHAR(20)) = '0000-00-00 00:00:00';
UPDATE clientes SET EnvioFactura = NULL WHERE CAST(EnvioFactura AS CHAR(20)) = '0000-00-00 00:00:00';
/* Agregamos Estado a la columna con el  1 para que sean todos visibles */
ALTER TABLE clientes ADD COLUMN Estado INT DEFAULT 1;
/*Indexamos lo que necesitamos para el buscador*/
CREATE INDEX idx_razon_social ON clientes (RazonSocial);
CREATE INDEX idx_para_empresa ON clientes (ParaEmpresa);
CREATE INDEX idx_identificacion ON clientes (Identificacion);
/* Se actualiza clientes para permitir valores nulos. */
ALTER TABLE clientes MODIFY COLUMN RF TINYINT(1) NULL;
ALTER TABLE clientes MODIFY COLUMN SinEval TINYINT(1) NULL;
ALTER TABLE clientes MODIFY COLUMN SinPF TINYINT(1) NULL;
ALTER TABLE clientes MODIFY COLUMN Ajuste DECIMAL(10,2) NULL;
ALTER TABLE clientes MODIFY COLUMN EnvioInforme DATETIME NULL;
ALTER TABLE clientes MODIFY COLUMN EMailInformes CHAR(200) NULL;
ALTER TABLE clientes MODIFY COLUMN EnvioFactura DATETIME NULL;
ALTER TABLE clientes MODIFY COLUMN IdAsignado INT(11) NULL;
ALTER TABLE clientes MODIFY COLUMN ObsCO TEXT NULL;
ALTER TABLE clientes MODIFY COLUMN SEMail TINYINT(1) NULL;
ALTER TABLE clientes MODIFY COLUMN Generico TINYINT(1) NULL;
ALTER TABLE clientes MODIFY COLUMN ObsCE VARCHAR(150) NULL;
ALTER TABLE clientes MODIFY COLUMN ObsEval TEXT NULL;
ALTER TABLE clientes MODIFY COLUMN FPago CHAR(1) NULL;
ALTER TABLE clientes MODIFY COLUMN Oreste TINYINT(1) NULL;
ALTER TABLE clientes MODIFY COLUMN LogoCertificado TINYINT(1) NULL;
ALTER TABLE clientes MODIFY COLUMN EMailResultados CHAR(200) NULL;
ALTER TABLE clientes MODIFY COLUMN ObsEMail CHAR(150) NULL;
ALTER TABLE clientes MODIFY COLUMN EMail CHAR(50) NULL;
ALTER TABLE clientes MODIFY COLUMN CP VARCHAR(10) NULL;
ALTER TABLE clientes MODIFY COLUMN Provincia VARCHAR(255) NULL;
ALTER TABLE clientes MODIFY COLUMN IdLocalidad INT(11) NULL;
ALTER TABLE clientes MODIFY COLUMN Direccion VARCHAR(200) NULL;
ALTER TABLE clientes MODIFY COLUMN Motivo VARCHAR(200) NULL;
ALTER TABLE clientes MODIFY COLUMN Bloqueado TINYINT(1) NULL;
ALTER TABLE clientes MODIFY COLUMN Logo VARCHAR(50) NULL;
ALTER TABLE clientes MODIFY COLUMN NombreFantasia VARCHAR(100) NULL;
ALTER TABLE clientes MODIFY COLUMN IdActividad INT(11) NULL;
ALTER TABLE clientes MODIFY COLUMN Entrega TINYINT(1) NULL;
ALTER TABLE clientes MODIFY COLUMN Envio TINYINT(1) NULL;
ALTER TABLE clientes MODIFY COLUMN TipoPersona CHAR(1) NULL;
ALTER TABLE clientes MODIFY COLUMN Observaciones TEXT NULL;
ALTER TABLE clientes MODIFY COLUMN TipoIdentificacion VARCHAR(255) NULL;
ALTER TABLE clientes MODIFY COLUMN CondicionIva VARCHAR(30) NULL;
ALTER TABLE clientes MODIFY COLUMN Nacionalidad VARCHAR(30) NULL;
ALTER TABLE clientes MODIFY COLUMN EMailFactura VARCHAR(200) NULL;
/**************************************************************************************/
--Telefonos
/* Campos NOT NULL */
ALTER TABLE telefonos MODIFY COLUMN Observaciones VARCHAR(200) NULL;
ALTER TABLE telefonos MODIFY COLUMN TipoEntidad CHAR(1) NULL;
ALTER TABLE telefonos MODIFY COLUMN IdEntidad int(11) DEFAULT 0 NULL;
ALTER TABLE telefonos MODIFY COLUMN CodigoArea char(6) NULL;
ALTER TABLE telefonos MODIFY COLUMN NumeroTelefono char(20) NULL;

/*Especial Agregar varios numeros telefonicos a clientes */
ALTER TABLE telefonos ADD COLUMN IdCliente INT DEFAULT 0 NULL;
/* 'i' //Nuevo identificador para telefonos de clientes */
/**************************************************************************************/
--Pacientes
ALTER TABLE pacientes MODIFY COLUMN FechaNacimiento DATE NULL;
UPDATE pacientes SET FechaNacimiento = NULL WHERE CAST(FechaNacimiento AS CHAR(11)) = '0000-00-00';
/* Agregamos Estado a la columna con el  1 para que sean todos visibles */
ALTER TABLE pacientes ADD COLUMN Estado INT DEFAULT 1;
/* Se actualiza pacientes para permitir valores nulos. */
ALTER TABLE pacientes MODIFY COLUMN Observaciones TEXT NULL;
ALTER TABLE pacientes MODIFY COLUMN Antecedentes TEXT NULL;
ALTER TABLE pacientes MODIFY COLUMN Foto VARCHAR(50) NULL;
ALTER TABLE pacientes MODIFY COLUMN ObsEMail VARCHAR(100) NULL;
ALTER TABLE pacientes MODIFY COLUMN EMail CHAR(50) NULL;
ALTER TABLE pacientes MODIFY COLUMN Provincia CHAR(30) NULL;
ALTER TABLE pacientes MODIFY COLUMN IdLocalidad INT(11) NULL;
ALTER TABLE pacientes MODIFY COLUMN Direccion CHAR(200) NULL;
ALTER TABLE pacientes MODIFY COLUMN Hijos TINYINT(4) NULL;
ALTER TABLE pacientes MODIFY COLUMN EstadoCivil CHAR(15) NULL;
ALTER TABLE pacientes MODIFY COLUMN ObsEstadoCivil CHAR(100) NULL;
ALTER TABLE pacientes MODIFY COLUMN LugarNacimiento CHAR(50) NULL;
ALTER TABLE pacientes MODIFY COLUMN Sexo CHAR(1) NULL;
ALTER TABLE pacientes MODIFY COLUMN CP char(10) NULL;
ALTER TABLE pacientes MODIFY COLUMN Nacionalidad char(30) NULL;
ALTER TABLE pacientes MODIFY COLUMN Identificacion char(13) NULL;
ALTER TABLE pacientes MODIFY COLUMN TipoIdentificacion char(5) NULL;
/* Permisos para cambiar la foto DEFAULT */
sudo chmod +w foto-default.png
sudo chmod 666 foto-default.png
/**************************************************************************************/
--Autorizados
/* Campos NOT NULL */
ALTER TABLE autorizados MODIFY COLUMN Nombre CHAR(30) NULL;
ALTER TABLE autorizados MODIFY COLUMN Apellido CHAR(30) NULL;
ALTER TABLE autorizados MODIFY COLUMN DNI CHAR(13) NULL;
ALTER TABLE autorizados MODIFY COLUMN Derecho CHAR(50) NULL;
ALTER TABLE autorizados MODIFY COLUMN TipoEntidad CHAR(1) NULL;

/**************************************************************************************/
--Provincias
/* Campos NOT NULL */
ALTER TABLE provincias MODIFY Nombre CHAR(30) NULL;

/**************************************************************************************/
--Localidades
/* Campos NOT NULL */
ALTER TABLE localidades MODIFY Nombre CHAR(30) NULL, MODIFY CP CHAR(20) NULL, MODIFY IdPcia INT(11) NULL;

/**************************************************************************************/
--Prestaciones
/* Agregamos Estado a la columna con el  1 para que sean todos visibles */
ALTER TABLE prestaciones ADD COLUMN Estado INT DEFAULT 1;

ALTER TABLE prestaciones MODIFY COLUMN Fecha DATE NULL,
                         MODIFY COLUMN FechaCierre DATE NULL,
                        MODIFY COLUMN FechaEntrega DATE NULL,
                        MODIFY COLUMN FechaFact DATE NULL,
                        MODIFY COLUMN FechaAnul DATE NULL,
                        MODIFY COLUMN FechaFinalizado DATE NULL,
                        MODIFY COLUMN FechaVto DATE NULL,
                        MODIFY COLUMN FechaT DATE NULL,
                        MODIFY COLUMN FechaEnviado DATE NULL;
UPDATE prestaciones SET Fecha = NULL WHERE CAST(Fecha AS CHAR(11)) = '0000-00-00';

/* Campos NOT NULL */
ALTER TABLE prestaciones MODIFY COLUMN Observaciones TEXT NULL;
ALTER TABLE prestaciones MODIFY COLUMN Anulado tinyint(1) DEFAULT 0 NULL;
ALTER TABLE prestaciones MODIFY COLUMN Cerrado tinyint(1) DEFAULT 0 NULL;
ALTER TABLE prestaciones MODIFY COLUMN Entregado tinyint(1) DEFAULT 0 NULL;
ALTER TABLE prestaciones MODIFY COLUMN Facturado tinyint(1) DEFAULT 0 NULL;
ALTER TABLE prestaciones MODIFY COLUMN ObsAnulado char(100) NULL;
ALTER TABLE prestaciones MODIFY COLUMN Evaluacion char(70)  NULL;
ALTER TABLE prestaciones MODIFY COLUMN Calificacion char(70)  NULL;
ALTER TABLE prestaciones MODIFY COLUMN Observaciones text NULL;
ALTER TABLE prestaciones MODIFY COLUMN NumeroFacturaVta int(11) DEFAULT 0 NULL;
ALTER TABLE prestaciones MODIFY COLUMN Finalizado tinyint(1) DEFAULT 0 NULL;
ALTER TABLE prestaciones MODIFY COLUMN ObsExamenes text NULL;
ALTER TABLE prestaciones MODIFY COLUMN Vto int(11) DEFAULT 0 NULL;
ALTER TABLE prestaciones MODIFY COLUMN NroCEE int(11) DEFAULT 0 NULL;
ALTER TABLE prestaciones MODIFY COLUMN Pago char(1)  NULL;
ALTER TABLE prestaciones MODIFY COLUMN SPago char(1) NULL;
ALTER TABLE prestaciones MODIFY COLUMN TSN char(15) NULL;
ALTER TABLE prestaciones MODIFY COLUMN Incompleto tinyint(1) NULL;
ALTER TABLE prestaciones MODIFY COLUMN AutorizaSC char(30) NULL;
ALTER TABLE prestaciones MODIFY COLUMN RxPreliminar tinyint(1) NULL;
ALTER TABLE prestaciones MODIFY COLUMN IdMapa int(11) DEFAULT 0 NULL;
ALTER TABLE prestaciones MODIFY COLUMN SinEsc tinyint(1) NULL;
ALTER TABLE prestaciones MODIFY COLUMN Forma tinyint(1) NULL;
ALTER TABLE prestaciones MODIFY COLUMN Ausente tinyint(1) NULL;
ALTER TABLE prestaciones MODIFY COLUMN Devol tinyint(1) NULL;
ALTER TABLE prestaciones MODIFY COLUMN IdEvaluador int(11) NULL;
ALTER TABLE prestaciones MODIFY COLUMN eEnviado tinyint(1) NULL;
ALTER TABLE prestaciones MODIFY COLUMN Estado int(11) DEFAULT 1 NOT NULL;
/**************************************************************************************/
--Prestacion-comentario
/* Campos NOT NULL */
ALTER TABLE prestaciones_comentarios MODIFY Obs TEXT NULL;
/***************************************************************************************/
--FichaLaboral
/* Campos NOT NULL */
ALTER TABLE fichaslaborales MODIFY COLUMN FechaEgreso date NULL,
                            MODIFY COLUMN FechaIngreso date NULL;
ALTER TABLE fichaslaborales MODIFY COLUMN Tareas char(30) NULL;
ALTER TABLE fichaslaborales MODIFY COLUMN TareasEmpAnterior char(30) NULL;
ALTER TABLE fichaslaborales MODIFY COLUMN Puesto char(30) NULL;
ALTER TABLE fichaslaborales MODIFY COLUMN Sector char(30) NULL;
ALTER TABLE fichaslaborales MODIFY COLUMN AntigPuesto int(11) DEFAULT 0 NULL;
ALTER TABLE fichaslaborales MODIFY COLUMN TipoJornada char(12) NULL;
ALTER TABLE fichaslaborales MODIFY COLUMN Jornada char(12) NULL;
ALTER TABLE fichaslaborales MODIFY COLUMN ObsJornada char(200) NULL;
ALTER TABLE fichaslaborales MODIFY COLUMN Observaciones text NULL;
ALTER TABLE fichaslaborales MODIFY COLUMN TipoActividad char(1) NULL;
ALTER TABLE fichaslaborales MODIFY COLUMN Solicitante char(1) NULL;
ALTER TABLE fichaslaborales MODIFY COLUMN CCosto char(50) NULL;
ALTER TABLE fichaslaborales MODIFY COLUMN Pago char(1) NULL;
ALTER TABLE fichaslaborales MODIFY COLUMN Estado char(1) NULL;
/********************************************************************************************/
--Mapas
/* Cambio 0000-00-00 formato default en NULL */
ALTER TABLE mapas MODIFY COLUMN Fecha DATE NULL,
                  MODIFY COLUMN FechaE DATE NULL;
/* Campos NOT NULL */
ALTER TABLE mapas MODIFY COLUMN Nro CHAR(8) NULL;
ALTER TABLE mapas MODIFY COLUMN Fecha DATE NULL;
ALTER TABLE mapas MODIFY COLUMN IdART INT(11) NULL;
ALTER TABLE mapas MODIFY COLUMN IdEMpresa INT(11) NULL;
ALTER TABLE mapas MODIFY COLUMN Obs TEXT NULL;
ALTER TABLE mapas MODIFY COLUMN Inactivo TINYINT(4) NULL;
ALTER TABLE mapas MODIFY COLUMN FechaE DATE NULL;
/*******************************************************************************************/
--Prestaciones_tipo
/* Campos NOT NULL */
ALTER TABLE prestaciones_tipo MODIFY COLUMN Nombre CHAR(20) NULL;
/*******************************************************************************************/
--Examenes
/* Campos NOT NULL */
ALTER TABLE examenes MODIFY COLUMN IdEstudio int(11) DEFAULT 0 NULL;
ALTER TABLE examenes MODIFY COLUMN Nombre char(50) NULL;
ALTER TABLE examenes MODIFY COLUMN Descripcion char(100) NULL;
ALTER TABLE examenes MODIFY COLUMN IdReporte int(11) DEFAULT 0 NULL;
ALTER TABLE examenes MODIFY COLUMN IdProveedor int(11) DEFAULT 0 NULL;
ALTER TABLE examenes MODIFY COLUMN IdProveedor2 int(11) NULL;
ALTER TABLE examenes MODIFY COLUMN DiasVencimiento tinyint(4) DEFAULT 0 NULL;
ALTER TABLE examenes MODIFY COLUMN Inactivo tinyint(1) NULL;
ALTER TABLE examenes MODIFY COLUMN Cod char(10) NULL;
ALTER TABLE examenes MODIFY COLUMN Cod2 char(10) NULL;
ALTER TABLE examenes MODIFY COLUMN SinEsc tinyint(1) NULL;
ALTER TABLE examenes MODIFY COLUMN Forma tinyint(1) NULL;
ALTER TABLE examenes MODIFY COLUMN Ausente tinyint(1) NULL;
ALTER TABLE examenes MODIFY COLUMN Devol tinyint(1) NULL;
ALTER TABLE examenes MODIFY COLUMN Informe tinyint(1) NULL;
ALTER TABLE examenes MODIFY COLUMN Adjunto tinyint(1) NULL;
ALTER TABLE examenes MODIFY COLUMN NoImprime tinyint(1) NULL;
ALTER TABLE examenes MODIFY COLUMN Cerrado tinyint(1) NULL;
ALTER TABLE examenes MODIFY COLUMN Evaluador tinyint(1) NULL;
ALTER TABLE examenes MODIFY COLUMN EvalCopia tinyint(1) NULL;
ALTER TABLE examenes MODIFY COLUMN PI tinyint(1) NULL;
ALTER TABLE examenes MODIFY COLUMN IdForm int(11) NULL;
/*******************************************************************************************/
--Paquete Estudios
/* Campos NOT NULL */
ALTER TABLE paqestudios MODIFY COLUMN Descripcion char(100) NULL;
ALTER TABLE paqestudios MODIFY COLUMN Nombre char(50) NULL;
/*******************************************************************************************/
--PrestacionesPaqFact
/* Campos NOT NULL */
ALTER TABLE prestacionespaqfact MODIFY COLUMN IdPrestacion int(11) NULL;
ALTER TABLE prestacionespaqfact MODIFY COLUMN IdItem int(11) NULL;
ALTER TABLE prestacionespaqfact MODIFY COLUMN IdExamen int(11) NULL;
ALTER TABLE prestacionespaqfact MODIFY COLUMN IdPaqFact int(11) NULL;
/*******************************************************************************************/
--Mapas
ALTER TABLE mapas MODIFY COLUMN FechaE date DEFAULT '0000-00-00' NULL;
ALTER TABLE mapas MODIFY COLUMN Inactivo tinyint(4) NULL;
ALTER TABLE mapas MODIFY COLUMN Obs text NULL;
ALTER TABLE mapas MODIFY COLUMN IdEmpresa int(11) NULL;
ALTER TABLE mapas MODIFY COLUMN IdART int(11) NULL;
ALTER TABLE mapas MODIFY COLUMN Fecha date DEFAULT '0000-00-00' NULL;
ALTER TABLE mapas MODIFY COLUMN Nro char(8) NULL;
/*******************************************************************************************/
--Extras
/* Agregamos TipoPrestacion */
ALTER TABLE fichaslaborales ADD COLUMN TipoPrestacion VARCHAR(12) NULL;
/* Agregamos TipoPrestacion */
ALTER TABLE prestaciones ADD COLUMN Financiador int(11) NULL;
/*******************************************************************************************/
--Extras de Mapas: Añadimos nuevos campos a pedido del caso de uso
ALTER TABLE mapas ADD COLUMN Cpacientes int(11) NULL DEFAULT 0;
ALTER TABLE mapas ADD COLUMN Cmapeados int(11) NULL DEFAULT 0;
/********************************************************************************************/
-- Le damos NULL a todo para que no haya problema en datos que no carga la BD
UPDATE itemsprestaciones SET FechaPagado = NULL WHERE CAST(FechaPagado AS CHAR(11)) = '0000-00-00';
-- Entidad
ALTER TABLE itemsprestaciones MODIFY COLUMN ObsExamen text CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE itemsprestaciones MODIFY COLUMN FechaPagado date DEFAULT '0000-00-00' NULL;
ALTER TABLE itemsprestaciones MODIFY COLUMN FechaPagado2 date DEFAULT '0000-00-00' NULL;
ALTER TABLE itemsprestaciones MODIFY COLUMN Anulado tinyint(1) DEFAULT 0 NULL;
ALTER TABLE itemsprestaciones MODIFY COLUMN Fecha date DEFAULT '0000-00-00' NULL;
ALTER TABLE itemsprestaciones MODIFY COLUMN FechaAsignado date DEFAULT '0000-00-00' NULL;
ALTER TABLE itemsprestaciones MODIFY COLUMN Facturado tinyint(1) DEFAULT 0 NULL;
ALTER TABLE itemsprestaciones MODIFY COLUMN VtoItem int(11) DEFAULT 0 NULL;
ALTER TABLE itemsprestaciones MODIFY COLUMN Honorarios decimal(10,2) DEFAULT 0.00 NULL;
ALTER TABLE itemsprestaciones MODIFY COLUMN NroFactCompra int(11) DEFAULT 0 NULL;
ALTER TABLE itemsprestaciones MODIFY COLUMN NroFactCompra2 int(11) DEFAULT 0 NOT NULL;
ALTER TABLE itemsprestaciones MODIFY COLUMN Incompleto tinyint(1) DEFAULT 0 NULL;
ALTER TABLE itemsprestaciones MODIFY COLUMN HoraAsignado time DEFAULT '00:00:00' NULL;
ALTER TABLE itemsprestaciones MODIFY COLUMN HoraFAsignado time DEFAULT '00:00:00' NULL;
ALTER TABLE itemsprestaciones MODIFY COLUMN SinEsc tinyint(1) DEFAULT 0 NULL;
ALTER TABLE itemsprestaciones MODIFY COLUMN Forma tinyint(1) DEFAULT 0 NULL;
ALTER TABLE itemsprestaciones MODIFY COLUMN Ausente tinyint(1) DEFAULT 0 NULL;
ALTER TABLE itemsprestaciones MODIFY COLUMN Devol tinyint(1) DEFAULT 0 NULL;
ALTER TABLE itemsprestaciones MODIFY COLUMN CInfo tinyint(1) DEFAULT 0 NULL;
ALTER TABLE itemsprestaciones MODIFY COLUMN CAdj tinyint(1) DEFAULT 0 NULL;
ALTER TABLE itemsprestaciones MODIFY COLUMN IdProfesional2 int(11) DEFAULT 0 NOT NULL;

--Constanciase
ALTER TABLE constanciase ADD COLUMN Obs TEXT NULL; --Para remitos en Mapas
ALTER TABLE constanciase MODIFY COLUMN Fecha date NULL;

/*****************************************************************************************************************/
-- Autocompletamos y evitamos errores en el servidor por datos que no se cargan
DELIMITER //
CREATE TRIGGER autocomplete_prestaciones
BEFORE INSERT ON prestaciones
FOR EACH ROW
BEGIN
    IF NEW.eEnviado IS NULL THEN
        SET NEW.eEnviado = 0;
    END IF;

    IF NEW.IdEvaluador IS NULL THEN
        SET NEW.IdEvaluador = 0;
    END IF;

    IF NEW.Devol IS NULL THEN
        SET NEW.Devol = 0;
    END IF;

    IF NEW.Ausente IS NULL THEN
        SET NEW.Ausente = 0;
    END IF;

    IF NEW.Forma IS NULL THEN
        SET NEW.Forma = 0;
    END IF;

    IF NEW.SinEsc IS NULL THEN
        SET NEW.SinEsc = 0;
    END IF;

    IF NEW.RxPreliminar IS NULL THEN
        SET NEW.RxPreliminar = 0;
    END IF;

    IF NEW.Incompleto IS NULL THEN
        SET NEW.Incompleto = 0;
    END IF;

    IF NEW.Evaluacion IS NULL THEN
        SET NEW.Evaluacion = 0;
    END IF;

    IF NEW.IdMapa IS NULL THEN
        SET NEW.IdMapa = 0;
    END IF;


END;
//
DELIMITER ;

/****************************************************************************/
-- Autocompletamos y evitamos errores en el servidor por datos que no se cargan
DELIMITER //
CREATE TRIGGER autocomplete_itemsprestaciones
BEFORE INSERT ON itemsprestaciones
FOR EACH ROW
BEGIN
    IF NEW.IdProveedor IS NULL THEN
        SET NEW.IdProveedor = 0;
    END IF;

    IF NEW.IdProfesional IS NULL THEN
        SET NEW.IdProfesional = 0;
    END IF;

    IF NEW.IdProfesional2 IS NULL THEN
        SET NEW.IdProfesional2 = 0;
    END IF;

    IF NEW.Incompleto IS NULL THEN
        SET NEW.Incompleto = 0;
    END IF;

    IF NEW.SinEsc IS NULL THEN
        SET NEW.SinEsc = 0;
    END IF;

    IF NEW.Forma IS NULL THEN
        SET NEW.Forma = 0;
    END IF;

    IF NEW.Ausente IS NULL THEN
        SET NEW.Ausente = 0;
    END IF;

    IF NEW.Devol IS NULL THEN
        SET NEW.Devol = 0;
    END IF;

    IF NEW.CInfo IS NULL THEN
        SET NEW.CInfo = 0;
    END IF;

    IF NEW.CAdj IS NULL THEN
        SET NEW.CAdj = 0;
    END IF;

END;
//
DELIMITER ;

/**************************************************************************************************/
--Modificaciones para Profesionales. Relacionamos telefonos y añadimos el perfil para el listado en opciones profesionales
ALTER TABLE telefonos ADD COLUMN IdProfesional int(11) NULL DEFAULT 0;
ALTER TABLE profesionales_prov ADD COLUMN Tipo varchar(20) NULL DEFAULT 'Sin Datos';
-- Borrado para que pueda funcionar el tipo en profesionales
ALTER TABLE db_cmit.profesionales_prov ADD CONSTRAINT IdProf_2 UNIQUE KEY (IdProf,IdProv);
ALTER TABLE telefonos ADD FOREIGN KEY(IdProfesional) REFERENCES profesionales(Id); --Añadimos Profesionales en Telefonos
/**************************************************************************************************/
-- Habilitar link storage en servidores de testeo. Ver SELinux si llega a fallar esto y permisos de lectura/escritura storage
 php artisan storage:link
/**************************************************************************************************/
--Autocompletamos algunos campos obligatorios y evitamos errores en los servidores por NOT NULL en todos los campos
DELIMITER //
CREATE TRIGGER autocomplete_profesionales
BEFORE INSERT ON profesionales
FOR EACH ROW
BEGIN
    IF NEW.TipoIdentificacion IS NULL THEN
        SET NEW.TipoIdentificacion = 'CUIL';
    END IF;

    IF NEW.Identificacion IS NULL THEN
        SET NEW.Identificacion = '00-00000000-00';
    END IF;

    IF NEW.TipoDocumento IS NULL THEN
        SET NEW.TipoDocumento = 'DNI';
    END IF;

END;
//
DELIMITER ;
/**************************************************************************************************/
-- Modificaciones en User para adaptarlo a Usuario
ALTER TABLE users DROP COLUMN `update`;

ALTER TABLE users ADD FOREIGN KEY (IdPersonal) REFERENCES personal(Id);

ALTER TABLE users ADD COLUMN IdPerfil int(11) NULL DEFAULT 0;
ALTER TABLE users ADD FOREIGN KEY (IdPerfil) REFERENCES perfiles(Id);

ALTER TABLE users DROP COLUMN `idProfesional`;
ALTER TABLE users ADD COLUMN IdProfesional int(11) NULL DEFAULT 0;
ALTER TABLE users ADD FOREIGN KEY(IdProfesional) REFERENCES profesionales(Id);

ALTER TABLE users ADD COLUMN IdPerfil int(11) NULL DEFAULT 0;
ALTER TABLE users ADD FOREIGN KEY(IdPerfil) REFERENCES perfiles(Id);
ALTER TABLE users ADD COLUMN Rol ENUM('Admin', 'Personal', 'Prestador') NOT NULL DEFAULT 'Personal';

/*******************************************************************************************************/

DELIMITER //
CREATE TRIGGER autocomplete_prestaciones
BEFORE INSERT ON prestaciones
FOR EACH ROW
BEGIN
    IF NEW.IdMapa IS NULL THEN
        SET NEW.IdMapa = 0;
    END IF;

    IF NEW.Observaciones IS NULL THEN
        SET NEW.Observaciones = ' ';
    END IF;

    IF NEW.ObsExamenes IS NULL THEN
        SET NEW.ObsExamenes = ' ';
    END IF;

    IF NEW.TSN IS NULL THEN
        SET NEW.TSN = ' ';
    END IF;
END;
//
DELIMITER ;

/***********************************************************************************************************/
--Se elimina la relación en la base de datos de Prestaciones con NrodeFactura
ALTER TABLE db_cmit.prestaciones DROP FOREIGN KEY prestaciones_ibfk_4;
/***********************************************************************************************************/
--- Columna de Observaciones para Proveedores
ALTER TABLE proveedores ADD COLUMN Obs TEXT NULL;
/***********************************************************************************************************/
-- Columnas para tamaño de imagen en Profesionales
SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION';
ALTER TABLE profesionales DROP COLUMN wImage;
ALTER TABLE profesionales DROP COLUMN hImage;
ALTER TABLE profesionales ADD COLUMN wImage VARCHAR(10) DEFAULT 10;
ALTER TABLE profesionales ADD COLUMN hImage hImageVARCHAR(10) DEFAULT 10;
SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION';

/************************************************************************************************************/
ALTER TABLE facturaventas ADD COLUMN IdPrestacion INT DEFAULT 0;
/************************************************************************************************************/
ALTER TABLE prestaciones_obsfases DROP FOREIGN KEY prestaciones_obsfases_ibfk_2;

/************************************************************************************************************/
--Password 123456

INSERT INTO `users` (`Id`, `name`, `email`, `IdPersonal`, `SR`, `email_verified_at`, `password`, `remember_token`,`created_at`,`updated_at`,`IdPerfil`, `IdProfesional`,`Rol`) VALUES
('3','abigailh','abigailh@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',8,212,'PRESTADOR'),
('4','admin','admin@cmit.com.ar',1,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',1,0,'PERSONAL'),
('5','agalvan','agalvan@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',6,139,'PRESTADOR'),
('6','alagos','alagos@cmit.com.ar',42,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',12,0,'PERSONAL'),
('7','arusso','arusso@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,175,'PRESTADOR'),
('8','astridd','astridd@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,193,'PRESTADOR'),
('9','astriddfichas','astriddfichas@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,201,'PRESTADOR'),
('10','belgrano','belgrano@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,169,'PRESTADOR');
INSERT INTO `users` (`Id`, `name`, `email`, `IdPersonal`, `SR`, `email_verified_at`, `password`, `remember_token`,`created_at`,`updated_at`,`IdPerfil`, `IdProfesional`,`Rol`) VALUES
('11','bjara','bjara@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,203,'PRESTADOR'),
('12','candelagl','candelagl@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58', '2023-05-10 07:19:53',8,199,'PRESTADOR'),
('13','cantoninni','cantoninni@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,202,'PRESTADOR'),
('14','caroca','caroca@cmit.com.ar',37,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',14,0,'PERSONAL'),
('15','caroquar','caroquar@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,57,'PRESTADOR'),
('16','cguevara','cguevara@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,185,'PRESTADOR'),
('17','cintia','cintia@cmit.com.ar',7,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',12,0,'PERSONAL'),
('18','cmit', 'cmit@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',11,26,'PRESTADOR'),
('19','cmit1','cmit1@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10  07:19:53',3,214,'PRESTADOR'),
('20','conciencia','conciencia@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,168,'PRESTADOR'),
('21','crear','crear@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',5,182,'PRESTADOR'),
('22','crosa','crosa@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,190,'PRESTADOR'),
('23','crossi','crossi@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',8,178,'PRESTADOR'),
('24','dcampos','dcampos@cmit.com.ar',23,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',12,0,'PERSONAL'),
('25','dcamposcarnet','dcamposcarnet@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58', '2023-05-10 07:19:53',3,162,'PRESTADOR'),
('26','dcortes','dcortes@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,138,'PRESTADOR'),
('27','dcortescarnet','dcortescarnet@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58', '2023-05-10 07:19:53',3,176,'PRESTADOR'),
('28','denisep','denisep@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,174,'PRESTADOR'),
('29','dibarra','dibarra@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58', '2023-05-10 07:19:53',3,192,'PRESTADOR'),
('30','dino','dino@cmit.com.ar',34,1,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',12,0,'PERSONAL'),
('31','dinocarnet','dinocarnet@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,164,'PRESTADOR'),
('32','dinoeeg','dinoeeg@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',11,160,'PRESTADOR'),
('33','dkriger','dkriger@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,148,'PRESTADOR');
INSERT INTO `users` (`Id`, `name`, `email`, `IdPersonal`, `SR`, `email_verified_at`, `password`, `remember_token`,`created_at`,`updated_at`,`IdPerfil`, `IdProfesional`,`Rol`) VALUES
('34','dsasso','dsasso@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,140,'PRESTADOR'),
('35','emilianob','emilianob@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',8,179,'PRESTADOR'),
('36','eorejas','eorejas@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,221,'PRESTADOR'),
('37','ergoma','ergoma@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',8,224,'PRESTADOR'),
('38','fdelvalle','fdelvalle@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58', '2023-05-10 07:19:53',8,207,'PRESTADOR'),
('39','fgomez', 'fgomez@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58', '2023-05-10 07:19:53',3,217,'PRESTADOR'),
('40','fmari','fmari@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,186,'PRESTADOR'),
('41','fmassaro','fmassaro@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,158,'PRESTADOR'),
('42','fmassaro2','fmassaro2@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58', '2023-05-10 07:19:53',3,158,'PRESTADOR'),
('43','fmontiveros','fmontiveros@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58', '2023-05-10 07:19:53',3,206,'PRESTADOR'),
('44','fochoa','fochoa@cmit.com.ar',26,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',14,0,'PERSONAL'),
('45','fochoa2','fochoa2@cmit.com.ar',26,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',12,0,'PERSONAL'),
('46','fochoacarnet','fochoacarnet@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58', '2023-05-10 07:19:53',3,177,'PRESTADOR'),
('47','fronda','fronda@cmit.com.ar',39,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',12,0,'PERSONAL'),
('48','fsciammarella','fsciammarella@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,197,'PRESTADOR'),
('49','gbante','gbante@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,171,'PRESTADOR'),
('50','gbecerra','gbecerra@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,146,'PRESTADOR'),
('51','gfranco','gfranco@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',6,80,'PRESTADOR'),
('52','gfranco2','gfranco2@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',7,80,'PRESTADOR'),
('53','ggomez','ggomez@cmit.com.ar',17,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',13,0,'PERSONAL'),
('54','gmarini','gmarini@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,188,'PRESTADOR'),
('55','gmontiveros','gmontiveros@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58', '2023-05-10 07:19:53',3,149,'PRESTADOR'),
('56','gparada','gparada@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',8,218,'PRESTADOR'),
('57','htorres','htorres@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',8,167,'PRESTADOR'),
('58','ipaccmit','ipaccmit@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,166,'PRESTADOR'),
('59','isegovia','isegovia@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,151,'PRESTADOR'),
('60','jmontesino','jmontesino@cmit.com.ar',30,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',12,0,'PERSONAL');
INSERT INTO `users` (`Id`, `name`, `email`, `IdPersonal`, `SR`, `email_verified_at`, `password`, `remember_token`,`created_at`,`updated_at`,`IdPerfil`, `IdProfesional`,`Rol`) VALUES
('61','josiniri','josiniri@cmit.com.ar',43,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',12,0,'PERSONAL'),
('62','josiniri2','josiniri2@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,223,'PRESTADOR'),
('63','juan manuel','manuel@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',5,47,'PRESTADOR'),
('64','jurdiales','jurdiales@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,204,'PRESTADOR'),
('65','katy','katy@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,184,'PRESTADOR'),
('66','lalvarez','lalvarez@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,191,'PRESTADOR'),
('67','lalvarez2','lalvarez2@cmit.com.ar',35,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',12,0,'PERSONAL'),
('68','lalvarezrx','lalvarezrx@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',8,191,'PRESTADOR'),
('69','lantiqueo','lantiqueo@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,154,'PRESTADOR'),
('70','liliana','liliana@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',11,48,'PRESTADOR'),
('71','llamador','llamador@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',16,0,'PERSONAL'),
('72','llofeudo','llofeudo@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,156,'PRESTADOR'),
('73','llopez','llopez@cmit.com.ar',14,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',15,0,'PERSONAL'),
('74','lmaero','lmaero@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',6,183,'PRESTADOR'),
('75','lpinto','lpinto@cmit.com.ar',32,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',14,0,'PERSONAL'),
('76','lquezada','lquezada@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,153,'PRESTADOR'),
('77','lsaavedra','lsaavedra@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,216,'PRESTADOR'),
('78','lucas4','lucas4@cmit.com.ar',1,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',2,0,'PERSONAL'),
('79','lucas2','lucas2@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',8,161,'PRESTADOR'),
('80','lucas3','lucas3@cmit.com.ar',33,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',15,0,'PERSONAL'),
('81','luis','luis@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',8,24,'PRESTADOR'),
('82','mailend','mailend@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,155,'PRESTADOR'),
('83','malmeira','malmeira@cmit.com.ar',25,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',2,0,'PERSONAL'),
('84','malmeira2','malmeira2@cmit.com.ar',25,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',12,0,'PERSONAL'),
('85','marguello','marguello@cmit.com.ar',0, 0,NULL, '$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',8,165,'PRESTADOR'),
('86','martin','martin@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',6,60,'PRESTADOR'),
('87','martin22','martin22@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',11,60,'PRESTADOR'),
('88','mbustos','mbustos@cmit.com.ar',41,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',14,0,'PERSONAL'),
('89','mcogo','mcogo@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,200,'PRESTADOR'),
('90','medicos','medicos@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,181,'PRESTADOR'),
('91',' mespinoza','mespinoza@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,131,'PRESTADOR'),
('92','mgalera','mgalera@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,225,'PRESTADOR'),
('93','mgutierrez','mgutierrez@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',8,29,'PRESTADOR'),
('94','miguel','miguel@cmit.com.ar',0, 0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',5,2,'PRESTADOR'),
('95','miommi','miommi@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,157,'PRESTADOR'),
('96','mkaty','mkaty@cmit.com.ar',36,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',9,0,'PERSONAL'),
('97','mmacedo','mmacedo@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,219,'PRESTADOR'),
('98','mmoreno','mmoreno@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,194,'PRESTADOR'),
('99','mpbelli','mpbelli@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,210,'PRESTADOR'),
('100','mporro','mporro@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,226,'PRESTADOR');
INSERT INTO `users` (`Id`, `name`, `email`, `IdPersonal`, `SR`, `email_verified_at`, `password`, `remember_token`,`created_at`,`updated_at`,`IdPerfil`, `IdProfesional`,`Rol`) VALUES
('101','mrosas','mrosas@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,142,'PRESTADOR'),
('102','mteruel','mteruel@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,172,'PRESTADOR'),
('103','nmendoza','nmendoza@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,150,'PRESTADOR'),
('104','paola','paola@cmit.com.ar',3,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',12,0,'PERSONAL'),
('105','pcourtade','pcourtade@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',8,19,'PRESTADOR'),
('106','pfrancese','pfrancese@cmit.com.ar',0, 0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',5,65,'PRESTADOR'),
('107','plopez','plopez@cmit.com.ar',0, 0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,7,'PRESTADOR'),
('108','porejas','porejas@cmit.com.ar',40, 0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',12,0,'PERSONAL'),
('109','psico','psico@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',8,123,'PRESTADOR'),
('110','rfalcone','rfalcone@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,196,'PRESTADOR'),
('111','rmorales','rmorales@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,215,'PRESTADOR'),
('112','rvaca','rvaca@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',8,144,'PRESTADOR'),
('113','rvaca2','rvaca2@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58', '2023-05-10 07:19:53',8,220,'PRESTADOR'),
('114','santoniazzi','santoniazzi@cmit.com.ar',15,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58', '2023-05-10 07:19:53',12,0,'PERSONAL'),
('115','smarini','smarini@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58', '2023-05-10 07:19:53',3,189,'PRESTADOR'),
('116','tmaguire','tmaguire@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58', '2023-05-10 07:19:53',3,205,'PRESTADOR'),
('117','varesco','varesco@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58', '2023-05-10 07:19:53',3,159,'PRESTADOR'),
('118','varesco1','varesco1@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,180,'PRESTADOR'),
('119','vperea','vperea@cmit.com.ar',38,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',12,0,'PERSONAL'),
('120','wfigueroa','wfigueroa@cmit.com.ar',0,0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',3,112,'PRESTADOR');

SET foreign_key_checks = 0;
ALTER TABLE prestaciones_obsfases ADD FOREIGN KEY(IdUsuario) REFERENCES users(name);
CREATE TABLE IF NOT EXISTS tipos_obsfases (
    Id INT AUTO_INCREMENT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion BLOB NULL,
    PRIMARY KEY (Id),
    UNIQUE(Id)
) ENGINE = InnoDB;
INSERT INTO tipos_obsfases (Id, nombre, descripcion) VALUES
(1, '0', NULL),
(2, 'prestaciones', NULL),
(3, 'cerrado', NULL),
(4, 'finalizado', NULL),
(5, 'entregado', NULL),
(6, 'eEnviado', NULL);
SET foreign_key_checks = 0;
ALTER TABLE prestaciones_obsfases ADD COLUMN obsfases_id INT DEFAULT 1;
SET foreign_key_checks = 0;
ALTER TABLE prestaciones_obsfases ADD FOREIGN KEY(obsfases_id) REFERENCES tipos_obsfases(Id);
ALTER TABLE prestaciones_obsfases MODIFY COLUMN Rol VARCHAR(50) NOT NULL;
INSERT INTO auditoriatablas (Id, Nombre) VALUES ('5', 'MAPAS');
INSERT INTO auditoriaacciones (Id, Nombre) VALUES 
('41', 'ENVIO E-ESTUDIO ART'),
('42', 'ENVIO E-ESTUDIO EMPRESA'),
('43', 'DESCARGA E-ESTUDIO');
ALTER TABLE auditoria DROP FOREIGN KEY auditoria_ibfk_3;
ALTER TABLE auditoria MODIFY COLUMN IdUsuario varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;

ALTER TABLE auditoria ADD FOREIGN KEY(IdUsuario) REFERENCES users(name);

composer require setasign/fpdf

ALTER TABLE fichaslaborales  ADD FechaPreocupacional DATE NULL;
ALTER TABLE fichaslaborales  ADD FechaUltPeriod DATE NULL;
ALTER TABLE fichaslaborales  ADD FechaExArt DATE NULL;
ALTER TABLE clientes ADD Anexo INT DEFAULT 0 NULL;
ALTER TABLE clientes ADD EMailAnexo varchar(100) NULL;
INSERT INTO auditoriaacciones (Id, Nombre) VALUES ('44', 'ENVIO E-ESTUDIO ART');

CREATE INDEX itemsprestaciones_CAdj_IDX USING BTREE ON itemsprestaciones (CAdj);
CREATE INDEX itemsprestaciones_CInfo_IDX USING BTREE ON itemsprestaciones (CInfo);
CREATE INDEX itemsprestaciones_Fecha_IDX USING BTREE ON itemsprestaciones (Fecha);
CREATE INDEX examenes_Nombre_IDX USING BTREE ON examenes (Nombre);

ALTER TABLE pagosacuenta_it ADD Precarga INT(9) DEFAULT 0 NULL;


CREATE PROCEDURE getSearchA(IN fechaDesde* DATE, IN fechaHasta* DATE, IN prestacion* INT, IN examen* INT, IN paciente* INT, 
				IN estados* VARCHAR, IN efector INT, IN especialidad INT, IN empresa INT)
BEGIN
	SELECT i.Id as IdItem, i.Fecha as Fecha, i.CAdj as Estado, i.CInfo as Informado, i.IdProfesional as IdProfesional, pro.Nombre as Especialidad, pro.Id as IdEspecialidad, pre.Id as IdPrestacion, cli.RazonSocial as Empresa, pa.Apellido as pacApellido, pa.Nombre as pacNombre, pa.Documento as Documento, pa.Id as IdPaciente, ex.Nombre as Examen
	FROM itemsprestaciones i
	INNER JOIN prestaciones pre ON i.IdPrestacion = pre.Id AND (prestacion IS NULL OR pre.Id = prestacion) AND (pre.Anulado = 0)
	INNER JOIN examenes ex ON i.IdExamen = ex.Id AND (examen IS NULL OR ex.Id = examen)
	INNER JOIN proveedores pro ON ex.IdProveedor = pro.Id AND (especialidad IS NULL OR pro.Id = especialidad)
	INNER JOIN clientes cli ON pre.IdEmpresa = cli.Id AND (empresa IS NULL OR cli.Id = empresa)
	INNER JOIN clientes cli2 ON pre.IdART = cli2.Id
	INNER JOIN pacientes pa ON pre.IdPaciente = pa.Id AND (paciente IS NULL OR pa.Id = paciente)
	LEFT JOIN archivosefector a ON i.Id = a.IdEntidad
	INNER JOIN profesionales prof ON i.IdProfesional = prof.Id AND (efector IS NULL OR prof.Id = efector)
	WHERE i.Fecha BETWEEN fechaDesde AND fechaHasta
	AND NOT i.Id = 0
	AND NOT i.IdProfesional = 0
	AND (estados IS NULL OR CASE
        WHEN estados = 'abiertos' THEN
            (i.CAdj IN (0, 1, 2) AND i.IdProfesional <> 0)
        WHEN estados = 'cerrados' THEN
            (i.CAdj IN (3, 4, 5) AND i.IdProfesional <> 0)
        WHEN estados = 'asignados' THEN
            (i.IdProfesional <> 0
            AND
            (
                SELECT COUNT(*)
                FROM archivosefector
                WHERE IdEntidad = i.Id
            ) = 0
            AND
            i.CAdj IN (0, 1, 2))
            AND
            ex.Adjunto = 1
    END)
    AND i.Anulado = 0
    order by i.Id desc
    limit 5000;
END

CREATE PROCEDURE getSearchAdj(IN fechaDesde* DATE, IN fechaHasta* DATE, IN efector INT, IN especialidad INT, IN empresa INT, IN art INT)

BEGIN
    SELECT (CASE WHEN pro.Multi = 1 THEN "Multi Examen" ELSE exa.Nombre END) AS examen_nombre, i.Id AS IdItem, i.Fecha AS Fecha, i.CAdj AS Estado, pro.Nombre AS Especialidad, pro.Multi AS MultiEfector, pre.Id AS IdPrestacion, cli.RazonSocial AS Empresa, pa.Apellido AS pacApellido, pa.Nombre AS pacNombre, prof.Apellido AS proApellido, prof.Nombre AS proNombre, pa.Documento AS Documento, pa.Id AS IdPaciente, exa.Nombre AS Examen, exa.Id AS IdExamen
    FROM itemsprestaciones i
    INNER JOIN prestaciones pre ON i.IdPrestacion = pre.Id AND (pre.Anulado = 0)
	INNER JOIN examenes exa ON i.IdExamen = exa.Id AND (exa.Adjunto = 1)
	INNER JOIN proveedores pro ON exa.IdProveedor = pro.Id AND (especialidad IS NULL OR pro.Id = especialidad)
	INNER JOIN clientes cli ON pre.IdEmpresa = cli.Id AND (empresa IS NULL OR cli.Id = empresa)
	INNER JOIN clientes cli2 ON pre.IdART = cli2.Id AND (art IS NULL OR cli2.Id = art)
	INNER JOIN pacientes pa ON pre.IdPaciente = pa.Id
	LEFT JOIN archivosefector a ON i.Id = a.IdEntidad
	INNER JOIN profesionales prof ON i.IdProfesional = prof.Id AND (efector IS NULL OR prof.Id = efector)
    WHERE i.Fecha BETWEEN fechaDesde AND fechaHasta
    AND NOT i.Id = 0
	AND NOT i.IdProfesional = 0
    AND NOT EXISTS(SELECT 1 FROM archivosefector WHERE archivosefector.IdEntidad = i.Id)
    AND i.CAdj IN(1,4)
    AND NOT i.IdProfesional = 0
    AND i.Anulado = 0
    GROUP BY (CASE WHEN pro.Multi = 1 THEN pre.Id ELSE i.Id END)
    order by i.Id desc
    limit 5000;
END

CREATE PROCEDURE getSearchInf(IN fechaDesde DATE, IN fechaHasta DATE, IN informador INT, IN especialidad INT, IN examen INT, IN prestacion INT, IN empresa INT, IN paciente INT)

BEGIN
    SELECT i.Id as IdItem, i.Fecha as Fecha, i.CAdj as Estado, i.CInfo as Informado, i.IdProfesional as IdProfesional, pro.Nombre as Especialidad, pro.Id as IdEspecialidad, pro.Multi as MultiEfector, pro.MultiE as MultiInformador, pre.Id as IdPrestacion, cli.RazonSocial as Empresa, CONCAT(pa.Apellido, ' ', pa.Nombre) as NombreCompleto, CONCAT(prof.Apellido, ' ', prof.Nombre) as NombreProfesional, pa.Documento as Documento, pa.Id as IdPaciente, exa.Nombre as Examen, exa.Id as IdExamen
    from itemsprestaciones i 
    inner join prestaciones pre on i.IdPrestacion = pre.Id AND (prestacion IS NULL OR pre.Id = prestacion) AND (pre.Anulado = 0)
    inner join examenes exa on i.IdExamen = exa.Id AND (examen IS NULL OR exa.Id = examen)
    inner join proveedores pro on exa.IdProveedor2 = pro.Id AND (especialidad IS NULL OR pro.Id = especialidad)
    inner join clientes cli on pre.IdEmpresa = cli.Id AND (empresa IS NULL OR cli.Id = empresa)
    inner join clientes cli2 on pre.IdART = cli2.Id 
    inner join pacientes pa on pre.IdPaciente = pa.Id AND (paciente IS NULL OR pa.Id = paciente)
    LEFT JOIN archivosefector a on i.Id = a.IdEntidad 
    inner join profesionales prof on i.IdProfesional2 = prof.Id AND (informador IS NULL OR prof.Id = informador)
    WHERE i.Fecha BETWEEN fechaDesde AND fechaHasta
    and not i.Id = 0
    and not i.IdProfesional = 0 
    and i.IdProfesional2 = 0 
    and i.CAdj IN(3,5)
    AND i.Anulado = 0
    order by i.Id desc
    limit 5000;
END

CREATE PROCEDURE getSearchInfA(IN fechaDesde DATE, IN fechaHasta DATE, IN informador INT, IN especialidad INT, IN examen INT, IN prestacion INT, IN empresa INT, IN paciente INT)

BEGIN
    select i.Id as IdItem, i.Fecha as Fecha, i.CAdj as Estado, i.CInfo as Informado, i.IdProfesional as IdProfesional, pro.Nombre as Especialidad, pro.Id as IdEspecialidad, pro.Multi as MultiEfector, pro.MultiE as MultiInformador, pre.Id as IdPrestacion, cli.RazonSocial as Empresa, CONCAT(pa.Apellido, ' ', pa.Nombre) as NombreCompleto, CONCAT(prof.Apellido, ' ', prof.Nombre) as NombreProfesional, pa.Documento as Documento, pa.Id as IdPaciente, exa.Nombre as Examen, exa.Id as IdExamen 
    from itemsprestaciones i
    inner join prestaciones pre on i.IdPrestacion = pre.Id AND (prestacion IS NULL OR pre.Id = prestacion) AND (pre.Anulado = 0)
    inner join examenes exa on i.IdExamen = exa.Id AND (examen IS NULL OR exa.Id = examen)
    inner join proveedores pro on exa.IdProveedor2 = pro.Id AND (especialidad IS NULL OR pro.Id = especialidad)
    inner join clientes cli on pre.IdEmpresa = cli.Id AND (empresa IS NULL OR cli.Id = empresa)
    inner join clientes cli2 on pre.IdART = cli2.Id 
    inner join pacientes pa on pre.IdPaciente = pa.Id AND (paciente IS NULL OR pa.Id = paciente)
    LEFT JOIN archivosefector a on i.Id = a.IdEntidad 
    inner join profesionales prof on i.IdProfesional2 = prof.Id AND (informador IS NULL OR prof.Id = informador)
    WHERE i.Fecha BETWEEN fechaDesde AND fechaHasta
    and not i.Id = 0 
    and not i.IdProfesional = 0 
    and not i.IdProfesional2 = 0 
    and i.CAdj IN (3,5) 
    and not i.CInfo = 3 
    and i.FechaPagado = '0000-00-00' 
    and not exists (select 1 from itemsprestaciones_info where itemsprestaciones_info.IdIP = i.Id)
    AND i.Anulado = 0
    order by i.Id desc 
    limit 5000;
END

CREATE PROCEDURE getSearchInfAdj(IN fechaDesde DATE, IN fechaHasta DATE, IN informador INT, IN especialidad INT, IN art INT, IN empresa INT)
BEGIN
    SELECT (CASE WHEN pro.MultiE = 1 THEN "Multi Examen" ELSE exa.Nombre END) AS examen_nombre, i.Id AS IdItem, i.Fecha AS Fecha, i.CAdj AS Estado, pro.Nombre AS Especialidad, pro.MultiE AS MultiInformador, pre.Id AS IdPrestacion, cli.RazonSocial AS Empresa, pa.Apellido AS pacApellido, pa.Nombre AS pacNombre, prof.Apellido AS proApellido, prof.Nombre AS proNombre, pa.Documento AS Documento, pa.Id AS IdPaciente, exa.Nombre AS Examen, exa.Id AS IdExamen, pre.Cerrado AS prestacionCerrado 
    FROM itemsprestaciones i 
    INNER JOIN prestaciones pre ON i.IdPrestacion = pre.Id AND (pre.Anulado = 0)
    INNER JOIN examenes exa ON i.IdExamen = exa.Id 
    INNER JOIN proveedores pro ON exa.IdProveedor2 = pro.Id AND (especialidad IS NULL OR pro.Id = especialidad) AND (pro.InfAdj = 1)
    INNER JOIN clientes cli ON pre.IdEmpresa = cli.Id AND (empresa IS NULL OR cli.Id = empresa)
    INNER JOIN clientes cli2 ON pre.IdART = cli2.Id AND (art IS NULL OR cli2.Id = art)
    INNER JOIN pacientes pa ON pre.IdPaciente = pa.Id 
    INNER JOIN profesionales prof ON i.IdProfesional2 = prof.Id AND (informador IS NULL OR prof.Id = informador)
    WHERE i.Fecha BETWEEN fechaDesde AND fechaHasta
    AND NOT i.Id = 0 
    AND i.CInfo IN (0, 1) 
    AND NOT i.IdProfesional = 0 
    AND NOT i.IdProfesional2 = 0 
    AND i.CAdj IN (3, 5) 
    AND i.Anulado = 0
    AND NOT EXISTS (SELECT 1 FROM archivosinformador ai WHERE ai.IdEntidad = i.Id) 
    GROUP BY
        CASE
            WHEN pro.MultiE = 1 THEN pre.Id ELSE i.Id
        END
    ORDER BY i.Id DESC 
    LIMIT 5000;
END

CREATE PROCEDURE getSearchPrestacion(IN fechaDesde DATE, IN fechaHasta DATE, IN estadoPres VARCHAR, IN estadoEfector VARCHAR, IN estadoInformador VARCHAR, IN efector INT, IN informador INT, IN tipoProv VARCHAR, IN adjunto VARCHAR, IN examen INT, IN pendiente INT, IN vencido INT, IN especialidad INT, IN ausente VARCHAR, IN adjuntoEfector INT)

BEGIN
    SELECT i.Id AS IdItem, i.Fecha AS Fecha, i.CAdj AS Efector, i.CInfo AS Informador, i.IdProfesional AS IdProfesional, 
    pro.Nombre AS Especialidad, pro.Id AS IdEspecialidad, pre.Id AS IdPrestacion, 
    pre.Cerrado AS PresCerrado, pre.Finalizado AS PresFinalizado, 
    pre.Entregado AS PresEntregado, pre.eEnviado AS PresEnviado, 
    cli.RazonSocial AS Empresa, pa.Nombre AS NombrePaciente, 
    pa.Apellido AS ApellidoPaciente, prof1.Nombre AS NombreProfesional, 
    prof1.Apellido AS ApellidoProfesional, prof2.Nombre AS NombreProfesional2, 
    prof2.Apellido AS ApellidoProfesional2, exa.Nombre AS Examen, 
    exa.Id AS IdExamen, exa.DiasVencimiento as DiasVencimiento, 
    exa.NoImprime AS NoImprime, 
    (CASE WHEN pre.Finalizado = 0 AND pre.Cerrado = 0 AND pre.Entregado = 0 THEN 'Abierto' WHEN pre.Cerrado = 1 AND pre.Finalizado = 0 THEN 'Cerrado' WHEN pre.Cerrado = 1 AND pre.Finalizado = 1 AND pre.Entregado = 0 THEN 'Finalizado' WHEN pre.Cerrado = 1 AND pre.Finalizado = 1 AND pre.Entregado = 1 THEN 'Entregado' WHEN pre.eEnviado = 1 THEN 'eEnviado' WHEN i.CAdj IN (1,4) AND i.CInfo IN (0,1) THEN 'pendiente' ELSE '-' END) AS estado,
    (CASE WHEN i.CAdj IN (0,1,4) THEN 'Pendiente' WHEN i.CAdj IN (3,4,5) THEN 'Cerrado' ELSE '-' END) AS EstadoEfector,
    (CASE WHEN i.CInfo IN (0,1) THEN 'Pendiente' WHEN i.CInfo = 2 THEN 'Borrador' WHEN i.CInfo = 3 THEN 'Cerrado' ELSE '-' END) AS EstadoInformador 
    FROM itemsprestaciones i 
    INNER JOIN prestaciones pre ON i.IdPrestacion = pre.Id AND (pre.Estado = 1) AND (pre.Anulado = 0)
    INNER JOIN examenes exa ON i.IdExamen = exa.Id AND (examen IS NULL OR exa.Id = examen) AND (adjunto IS NULL OR (CASE WHEN adjunto = 'fisico' THEN exa.NoImprime = 0 WHEN adjunto = 'digital' THEN exa.NoImprime = 1 END))
    INNER JOIN proveedores pro ON exa.IdProveedor2 = pro.Id AND (especialidad IS NULL OR pro.Id = especialidad)
    INNER JOIN clientes cli ON pre.IdEmpresa = cli.Id 
    INNER JOIN pacientes pa ON pre.IdPaciente = pa.Id 
    INNER JOIN profesionales prof1 ON i.IdProfesional = prof1.Id AND (efector IS NULL OR prof1.Id = efector)
    INNER JOIN profesionales prof2 ON i.IdProfesional2 = prof2.Id AND (informador IS NULL OR prof2.Id = informador)
    LEFT JOIN archivosefector a ON i.Id = a.IdEntidad 
    WHERE NOT i.Id = 0 
    AND i.Fecha BETWEEN fechaDesde AND fechaHasta 
    AND (estadoPres IS NULL OR
        (CASE 
            WHEN estadoPres = 'abierto' THEN pre.Finalizado = 0 AND pre.Cerrado = 0 AND pre.Entregado = 0 
            WHEN estadoPres = 'cerrado' THEN pre.Cerrado = 1 AND pre.Finalizado = 0
            WHEN estadoPres = 'finalizado' THEN pre.Cerrado = 1 AND pre.Finalizado = 1 AND pre.Entregado = 0
            WHEN estadoPres = 'entregado' THEN pre.Cerrado = 1 AND pre.Finalizado = 1 AND pre.Entregado = 1
            WHEN estadoPres = 'eenviado' THEN pre.eEnviado = 1
        END)
    )
    AND (estadoEfector IS NULL OR
        (CASE 
            WHEN estadoEfector = 'pendientes' THEN i.CAdj IN (0,1,4) 
            WHEN estadoEfector = 'cerrados' THEN i.CAdj IN (3,4,5)
        END)
    )
    AND (estadoInformador IS NULL OR
        (CASE 
            WHEN estadoInformador = 'pendientes' THEN i.CInfo IN (0,1) 
            WHEN estadoInformador = 'borrador' THEN i.CInfo = 2
            WHEN estadoInformador = 'pendienteYborrador' THEN i.CInfo IN (0,1,2)
        END)
    )
    AND (tipoProv IS NULL OR
        (CASE 
            WHEN tipoProv = 'interno' THEN pro.Externo = 0 
            WHEN tipoProv = 'externo' THEN pro.Externo = 1
            WHEN tipoProv = 'todos' THEN pro.Externo IN (0,1)
        END)
    )
    AND (ausente IS NULL OR
        (CASE 
            WHEN ausente = 'ausente' THEN i.Ausente = 1 
            WHEN ausente = 'noAusente' THEN i.Ausente = 0
            WHEN ausente = 'todos' THEN i.Ausente IN (0,1)
        END)
    )
    AND (pendiente IS NULL OR 
        (CASE WHEN pendiente = 1 THEN i.CAdj IN(1,4) AND i.CInfo IN (0,1) END)
    )
    AND (adjuntoEfector IS NULL OR
        (CASE WHEN adjuntoEfector = 1 THEN a.IdEntidad = i.Id AND exa.adjunto = 1 END)
    )
    AND (vencido IS NULL OR
        CASE WHEN vencido = 1 THEN DATE_ADD(i.Fecha, INTERVAL exa.DiasVencimiento DAY) <= fechaHasta AND DAY(DATE_ADD(i.Fecha, INTERVAL exa.DiasVencimiento DAY)) > DAY(i.Fecha) END
    )
    AND i.Anulado = 0
    ORDER BY i.Id DESC 
    LIMIT 5000;
END