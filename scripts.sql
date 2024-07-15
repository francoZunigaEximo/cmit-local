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
ALTER TABLE profesionales ADD COLUMN wImage VARCHAR(10) DEFAULT 100;
ALTER TABLE profesionales ADD COLUMN hImage VARCHAR(10) DEFAULT 100;
SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION';

/************************************************************************************************************/
ALTER TABLE facturasventa ADD COLUMN IdPrestacion INT DEFAULT 0;
/************************************************************************************************************/
ALTER TABLE prestaciones_obsfases DROP FOREIGN KEY prestaciones_obsfases_ibfk_2;

/************************************************************************************************************/


SET foreign_key_checks = 0;
ALTER TABLE prestaciones_obsfases CHANGE IdUsuario IdUsuario CHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
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


CREATE PROCEDURE getSearchA(IN fechaDesde DATE, IN fechaHasta DATE, IN prestacion INT, IN examen INT, IN paciente INT, 
				IN estados VARCHAR, IN efector INT, IN especialidad INT, IN empresa INT)
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
    inner join examenes exa on i.IdExamen = exa.Id AND (examen IS NULL OR exa.Id = examen) AND (exa.Informe = 1)
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
    inner join examenes exa on i.IdExamen = exa.Id AND (examen IS NULL OR exa.Id = examen) AND (exa.Informe = 1)
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
    and NULLIF(i.FechaPagado, '0000-00-00') IS NULL
    and not exists (select 1 from itemsprestaciones_info where itemsprestaciones_info.IdIP = i.Id)
    AND i.Anulado = 0
    order by i.Id desc 
    limit 5000;
END

CREATE PROCEDURE getSearchInfAdj(IN fechaDesde DATE, IN fechaHasta DATE, IN informador INT, IN especialidad INT, IN art INT, IN empresa INT)
BEGIN
    SELECT (CASE WHEN pro.MultiE = 1 THEN "Multi Examen" ELSE exa.Nombre END) AS examen_nombre, i.Id AS IdItem, i.Fecha AS Fecha, i.CAdj AS Estado, pro.Nombre AS Especialidad, pro.MultiE AS MultiInformador, pre.Id AS IdPrestacion, cli.RazonSocial AS Empresa, pa.Apellido AS pacApellido, pa.Nombre AS pacNombre, prof.Apellido AS proApellido, prof.Nombre AS proNombre, pa.Documento AS Documento, pa.Id AS IdPaciente, exa.Nombre AS Examen, exa.Id AS IdExamen, pre.Cerrado AS prestacionCerrado, pre.Id
    FROM itemsprestaciones i 
    INNER JOIN prestaciones pre ON i.IdPrestacion = pre.Id AND (pre.Anulado = 0)
    INNER JOIN examenes exa ON i.IdExamen = exa.Id AND (exa.Informe = 1) 
    INNER JOIN proveedores pro ON exa.IdProveedor2 = pro.Id AND (especialidad IS NULL OR pro.Id = especialidad)
    INNER JOIN clientes cli ON pre.IdEmpresa = cli.Id AND (empresa IS NULL OR cli.Id = empresa)
    INNER JOIN clientes cli2 ON pre.IdART = cli2.Id AND (art IS NULL OR cli2.Id = art)
    INNER JOIN pacientes pa ON pre.IdPaciente = pa.Id 
    INNER JOIN profesionales prof ON i.IdProfesional2 = prof.Id AND (informador IS NULL OR prof.Id = informador) AND (prof.InfAdj = 1)
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
    (CASE WHEN i.CAdj IN (0,1,2) THEN 'Pendiente' WHEN i.CAdj IN (3,4,5) THEN 'Cerrado' ELSE '-' END) AS EstadoEfector,
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


CREATE PROCEDURE getSearchEEnviar(IN fechaDesde DATE, IN fechaHasta DATE, IN empresa INT, IN paciente INT, IN completo VARCHAR, IN eenviar VARCHAR)

BEGIN
    SELECT i.Fecha AS Fecha, pre.Id AS IdPrestacion, pre.FechaEnviado AS FechaEnviado, cli.EMailInformes AS Correo, cli.RazonSocial AS Empresa, CONCAT(pa.Apellido, ' ', pa.Nombre) AS NombreCompleto, pa.Documento AS Documento, pa.Id AS IdPaciente, exa.Nombre AS Examen, pc.Pagado AS Pagado, i.Id AS IdExa
    FROM itemsprestaciones i
    INNER JOIN prestaciones pre on i.IdPrestacion = pre.Id AND (pre.Anulado = 0) AND (eenviar IS NULL OR
        (CASE 
            WHEN eenviar = 'eenviado' THEN pre.eEnviado = 1
            WHEN eenviar = 'noeenviado' THEN pre.eEnviado = 0
            WHEN eenviar = 'todos' THEN pre.eEnviado IN (0,1)
        END)
    )
    INNER JOIN examenes exa ON i.IdExamen = exa.Id AND (exa.Informe = 1)
    INNER JOIN clientes cli ON pre.IdEmpresa = cli.Id AND (empresa IS NULL OR cli.Id = empresa)
    INNER JOIN pacientes pa ON pre.IdPaciente = pa.Id AND (paciente IS NULL OR pa.Id = paciente)
    INNER JOIN pagosacuenta pc ON cli.Id = pc.IdEmpresa
    LEFT JOIN pagosacuenta_it pc2 ON pre.Id = pc2.IdPrestacion
    WHERE (CASE
	    	WHEN completo IS NULL THEN i.Fecha BETWEEN fechaDesde AND fechaHasta
	    	WHEN completo = "activo" THEN i.CAdj in (3,5) AND i.CInfo = 3 AND pc.Pagado = 1
	    END)
    AND NOT i.Id = 0
    AND i.Anulado = 0
    AND NOT (i.Fecha IS NULL OR i.Fecha = '0000-00-00')
    GROUP BY pre.Id
    ORDER BY i.Id DESC 
    LIMIT 5000;
END

ALTER TABLE users DROP FOREIGN KEY users_ibfk_2;
ALTER TABLE users DROP FOREIGN KEY users_ibfk_4;
ALTER TABLE users DROP COLUMN IdPerfil;
ALTER TABLE users DROP COLUMN SR;
ALTER TABLE users DROP COLUMN Rol;
ALTER TABLE users CHANGE IdProfesional profesional_id int(11) DEFAULT 0 NULL;
ALTER TABLE users CHANGE idPersonal datos_id int(11) DEFAULT NULL NULL;
ALTER TABLE users ADD inactivo INT DEFAULT 0 NULL;
ALTER TABLE users ADD Anulado INT DEFAULT 0 NULL;

--Password 123456
INSERT INTO users (name,email,datos_id,email_verified_at,password,remember_token,created_at,updated_at,profesional_id,inactivo,Anulado) VALUES
	 ('nicolas','nmaximowicz@eximo.com.ar',52,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2024-05-20 01:44:18',230,0,0),
	 ('lucas','lucas@cmit.com.ar',1,NULL,'$2y$10$5xxjrbNwlDFSu/Q.wAvCMeJ/URPDh57efpgaPTfflvk2fmM74CzHK',NULL,'2023-05-08 16:27:18','2024-05-20 01:34:23',0,0,0),
	 ('abigailh','abigailh@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',212,0,0),
	 ('admin','admin@cmit.com.ar',1,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2024-05-20 02:55:32',0,0,0),
	 ('agalvan','agalvan@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',139,0,0),
	 ('alagos','alagos@cmit.com.ar',42,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2024-05-23 11:16:52',0,0,0),
	 ('arusso','arusso@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',175,0,0),
	 ('astridd','astridd@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',193,0,0),
	 ('astriddfichas','astriddfichas@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',201,0,0),
	 ('belgrano','belgrano@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',169,0,0);
INSERT INTO users (name,email,datos_id,email_verified_at,password,remember_token,created_at,updated_at,profesional_id,inactivo,Anulado) VALUES
	 ('bjara','bjara@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',203,0,0),
	 ('candelagl','candelagl@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',199,0,0),
	 ('cantoninni','cantoninni@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',202,0,0),
	 ('caroca','caroca@cmit.com.ar',37,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2024-05-23 11:24:34',0,0,0),
	 ('caroquar','caroquar@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',57,0,0),
	 ('cguevara','cguevara@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',185,0,0),
	 ('cintia','cintia@cmit.com.ar',7,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',0,0,0),
	 ('cmit','cmit@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',26,0,0),
	 ('cmit1','cmit1@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',214,0,0),
	 ('conciencia','conciencia@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',168,0,0);
INSERT INTO users (name,email,datos_id,email_verified_at,password,remember_token,created_at,updated_at,profesional_id,inactivo,Anulado) VALUES
	 ('crear','crear@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',182,0,0),
	 ('crosa','crosa@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',190,0,0),
	 ('crossi','crossi@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',178,0,0),
	 ('dcampos','dcampos@cmit.com.ar',23,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',0,0,0),
	 ('dcamposcarnet','dcamposcarnet@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',162,0,0),
	 ('dcortes','dcortes@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',138,0,0),
	 ('dcortescarnet','dcortescarnet@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',176,0,0),
	 ('denisep','denisep@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',174,0,0),
	 ('dibarra','dibarra@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',192,0,0),
	 ('dino','dino@cmit.com.ar',34,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',0,0,0);
INSERT INTO users (name,email,datos_id,email_verified_at,password,remember_token,created_at,updated_at,profesional_id,inactivo,Anulado) VALUES
	 ('dinocarnet','dinocarnet@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',164,0,0),
	 ('dinoeeg','dinoeeg@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',160,0,0),
	 ('dkriger','dkriger@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',148,0,0),
	 ('dsasso','dsasso@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',140,0,0),
	 ('emilianob','emilianob@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',179,0,0),
	 ('eorejas','eorejas@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',221,0,0),
	 ('ergoma','ergoma@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',224,0,0),
	 ('fdelvalle','fdelvalle@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',207,0,0),
	 ('fgomez','fgomez@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',217,0,0),
	 ('fmari','fmari@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',186,0,0);
INSERT INTO users (name,email,datos_id,email_verified_at,password,remember_token,created_at,updated_at,profesional_id,inactivo,Anulado) VALUES
	 ('fmassaro','fmassaro@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',158,0,0),
	 ('fmassaro2','fmassaro2@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',158,0,0),
	 ('fmontiveros','fmontiveros@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',206,0,0),
	 ('fochoa','fochoa@cmit.com.ar',26,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',0,0,0),
	 ('fochoa2','fochoa2@cmit.com.ar',26,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',0,0,0),
	 ('fochoacarnet','fochoacarnet@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',177,0,0),
	 ('fronda','fronda@cmit.com.ar',39,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',0,0,0),
	 ('fsciammarella','fsciammarella@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',197,0,0),
	 ('gbante','gbante@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',171,0,0),
	 ('gbecerra','gbecerra@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',146,0,0);
INSERT INTO users (name,email,datos_id,email_verified_at,password,remember_token,created_at,updated_at,profesional_id,inactivo,Anulado) VALUES
	 ('gfranco','gfranco@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',80,0,0),
	 ('gfranco2','gfranco2@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',80,0,0),
	 ('ggomez','ggomez@cmit.com.ar',17,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',0,0,0),
	 ('gmarini','gmarini@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',188,0,0),
	 ('gmontiveros','gmontiveros@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',149,0,0),
	 ('gparada','gparada@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',218,0,0),
	 ('htorres','htorres@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',167,0,0),
	 ('ipaccmit','ipaccmit@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',166,0,0),
	 ('isegovia','isegovia@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',151,0,0),
	 ('jmontesino','jmontesino@cmit.com.ar',30,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',0,0,0);
INSERT INTO users (name,email,datos_id,email_verified_at,password,remember_token,created_at,updated_at,profesional_id,inactivo,Anulado) VALUES
	 ('josiniri','josiniri@cmit.com.ar',43,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',0,0,0),
	 ('josiniri2','josiniri2@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',223,0,0),
	 ('juan manuel','manuel@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',47,0,0),
	 ('jurdiales','jurdiales@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',204,0,0),
	 ('katy','katy@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',184,0,0),
	 ('lalvarez','lalvarez@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',191,0,0),
	 ('lalvarez2','lalvarez2@cmit.com.ar',35,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',0,0,0),
	 ('lalvarezrx','lalvarezrx@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',191,0,0),
	 ('lantiqueo','lantiqueo@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',154,0,0),
	 ('liliana','liliana@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',48,0,0);
INSERT INTO users (name,email,datos_id,email_verified_at,password,remember_token,created_at,updated_at,profesional_id,inactivo,Anulado) VALUES
	 ('llamador','llamador@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',0,0,0),
	 ('llofeudo','llofeudo@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',156,0,0),
	 ('llopez','llopez@cmit.com.ar',14,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',0,0,0),
	 ('lmaero','lmaero@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',183,0,0),
	 ('lpinto','lpinto@cmit.com.ar',32,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',0,0,0),
	 ('lquezada','lquezada@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',153,0,0),
	 ('lsaavedra','lsaavedra@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',216,0,0),
	 ('lucas4','lucas4@cmit.com.ar',1,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',0,0,0),
	 ('lucas2','lucas2@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',161,0,0),
	 ('lucas3','lucas3@cmit.com.ar',33,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',0,0,0);
INSERT INTO users (name,email,datos_id,email_verified_at,password,remember_token,created_at,updated_at,profesional_id,inactivo,Anulado) VALUES
	 ('luis','luis@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',24,0,0),
	 ('mailend','mailend@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',155,0,0),
	 ('malmeira','malmeira@cmit.com.ar',25,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',0,0,0),
	 ('malmeira2','malmeira2@cmit.com.ar',25,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',0,0,0),
	 ('marguello','marguello@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',165,0,0),
	 ('martin','martin@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',60,0,0),
	 ('martin22','martin22@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',60,0,0),
	 ('mbustos','mbustos@cmit.com.ar',41,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',0,0,0),
	 ('mcogo','mcogo@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',200,0,0),
	 ('medicos','medicos@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',181,0,0);
INSERT INTO users (name,email,datos_id,email_verified_at,password,remember_token,created_at,updated_at,profesional_id,inactivo,Anulado) VALUES
	 ('mespinoza','mespinoza@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',131,0,0),
	 ('mgalera','mgalera@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',225,0,0),
	 ('mgutierrez','mgutierrez@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',29,0,0),
	 ('miguel','miguel@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',2,0,0),
	 ('miommi','miommi@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',157,0,0),
	 ('mkaty','mkaty@cmit.com.ar',36,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',0,0,0),
	 ('mmacedo','mmacedo@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',219,0,0),
	 ('mmoreno','mmoreno@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',194,0,0),
	 ('mpbelli','mpbelli@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',210,0,0),
	 ('mporro','mporro@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',226,0,0);
INSERT INTO users (name,email,datos_id,email_verified_at,password,remember_token,created_at,updated_at,profesional_id,inactivo,Anulado) VALUES
	 ('mrosas','mrosas@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',142,0,0),
	 ('mteruel','mteruel@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',172,0,0),
	 ('nmendoza','nmendoza@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',150,0,0),
	 ('paola','paola@cmit.com.ar',3,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',0,0,0),
	 ('pcourtade','pcourtade@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',19,0,0),
	 ('pfrancese','pfrancese@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',65,0,0),
	 ('plopez','plopez@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',7,0,0),
	 ('porejas','porejas@cmit.com.ar',40,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',0,0,0),
	 ('psico','psico@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',123,0,0),
	 ('rfalcone','rfalcone@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',196,0,0);
INSERT INTO users (name,email,datos_id,email_verified_at,password,remember_token,created_at,updated_at,profesional_id,inactivo,Anulado) VALUES
	 ('rmorales','rmorales@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',215,0,0),
	 ('rvaca','rvaca@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',144,0,0),
	 ('rvaca2','rvaca2@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',220,0,0),
	 ('santoniazzi','santoniazzi@cmit.com.ar',15,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',0,0,0),
	 ('smarini','smarini@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',189,0,0),
	 ('tmaguire','tmaguire@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',205,0,0),
	 ('varesco','varesco@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',159,0,0),
	 ('varesco1','varesco1@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',180,0,0),
	 ('vperea','vperea@cmit.com.ar',38,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',0,0,0),
	 ('wfigueroa','wfigueroa@cmit.com.ar',0,NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2023-05-10 07:19:53',112,0,0);
INSERT INTO users (name,email,datos_id,email_verified_at,password,remember_token,created_at,updated_at,profesional_id,inactivo,Anulado) VALUES
	 ('nicolas2','miw@correo.com',53,NULL,'$2y$10$Ev52omlEi1XoareOoCLsGuyHjEpSZ68nHFk4mRdeDPRxexrLVxYNi',NULL,'2024-05-17 13:33:33','2024-05-22 21:52:08',0,0,0),
	 ('tomura','info@tomura.com',55,NULL,'$2y$10$OX6VwTGH4/t/HCLKdlG4XuXPlSavwx..JaIXx7aWLjpaAXk7S8iuS',NULL,'2024-05-22 21:59:15','2024-05-22 22:14:52',0,0,0);

/* Eliminar si es una version vieja */
composer remove spatie/laravel-permission
rm config/permission.php
rm database/migrations/202x_xx_xx_xxxxxx_create_permission_tables.php  /*Si existe*/
/*** Eliminar entidades ***/
permissions
roles
model_has_permissions
model_has_roles
role_has_permissions
/*** Fin ***/

CREATE TABLE `roles` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` blob DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Id` (`Id`),
  KEY `idx_roles_id` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


INSERT INTO roles(nombre, descripcion) VALUES 
("Admin JR",""),
("Admin STD",""),
("Admin SR",""),
("Recepción JR",""),
("Recepción STD",""),
("Recepción SR",""),
("Evaluador ART",""),
("Efector",""),
("Informador","");

CREATE TABLE permisos(
    Id INT NOT NULL AUTO_INCREMENT,
    slug VARCHAR(50) NOT NULL,
    descripcion BLOB,
    PRIMARY KEY (Id),
    UNIQUE(Id)
);

INSERT INTO permisos (slug,descripcion) VALUES
	 ('clientes_add',0x4167726567617220636C69656E746573206E7565766F73),
	 ('clientes_edit',0x65646974617220636C69656E746573),
	 ('clientes_show',0x76697375616C697A6172206461746F732064656C20636C69656E7465),
	 ('clientes_send',0x456E7669617220656D61696C73206120636C69656E746573),
	 ('clientes_export',0x4578706F72746172207265706F7274657320656E2067656E6572616C),
	 ('clientes_delete',0x456C696D696E617220756E20636C69656E7465),
	 ('prestaciones_add',0x4167726567617220756E61206E75657661207072657374616369C3B36E),
	 ('prestaciones_edit',0x45646974617220756E61207072657374616369C3B36E),
	 ('prestaciones_show',0x56697375616C697A6172207072657374616369C3B36E),
	 ('prestaciones_delete',0x456C696D696E61722070726573746163696F6E6573);
INSERT INTO permisos (slug,descripcion) VALUES
	 ('prestaciones_block',0x416E756C61722070726573746163696F6E6573),
	 ('prestaciones_todo',0x5574696C697A616369C3B36E2064656C20626F74C3B36E20746F646F),
	 ('prestaciones_eEnviar',0x5574696C697A616369C3B36E2064656C20626F74C3B36E2065456E76696172),
	 ('etapas_show',0x56697375616C697A617220657461706173),
	 ('etapas_apply',0x4574617061732061706C696361722C206365727261722C2061646A756E746172),
	 ('etapas_informador',0x5365636369C3B36E20696E666F726D61646F72),
	 ('etapas_efector',0x5365636369C3B36E2065666563746F72),
	 ('etapas_eenviar',0x5365636369C3B36E2065456E76696172),
	 ('mapas_add',0x4167726567617220756E206D617061),
	 ('mapas_edit',0x45646974617220756E206D617061);
INSERT INTO permisos (slug,descripcion) VALUES
	 ('mapas_finalizar',0x46696E616C697A617220756E206D617061),
	 ('mapas_eenviar',0x65456E7669617220756E206D617061),
	 ('pacientes_add',0x416772656761722070616369656E7465),
	 ('pacientes_edit',0x4564697461722070616369656E746573),
	 ('pacientes_delete',0x456C696D696E61722070616369656E746573),
	 ('examenCta_add',0x41677265676172206578616D656E2061206375656E7461),
	 ('examenCta_edit',0x456469746172206578616D656E2061206375656E7461),
	 ('examenCta_delete',0x456C696D696E6172206578616D656E2061206375656E7461),
	 ('boton_usuarios',NULL),
	 ('mapas_add',0x41677265676172206D61706173206E7565766F73);
INSERT INTO permisos (slug,descripcion) VALUES
	 ('mapas_cerrar',0x43657272617220756E206D617061),
	 ('mapas_finalizar',0x46696E616C697A617220756E206D617061),
	 ('mapas_eenviar',0x65456E7669617220756E206D617061),
	 ('stock_show',0x56697375616C697A61722073746F636B),
	 ('stock_baja',0x6461722064652062616A612073746F636B),
	 ('stock_add',0x416772656761722073746F636B),
	 ('stock_parametros',0x506172616D6574726F732064652073746F636B),
	 ('manual',0x4D6F64756C6F2064652073746F636B20636F6D706C65746F),
	 ('boton_prestaciones',0x426F746F6E20736C696465722070726573746163696F6E6573),
	 ('pacientes_show',0x56697375616C697A61722070616369656E746573);
INSERT INTO permisos (slug,descripcion) VALUES
	 ('paciente_report',0x5265706F727465732070616369656E7465),
	 ('mapas_show',0x56697375616C697A6172206D61706173),
	 ('especialidades_show',0x56697375616C697A617220657370656369616C696461646573),
	 ('especialidades_add',0x4167726567617220657370656369616C69646164),
	 ('especialidades_edit',0x4D6F6469666963617220657370656369616C69646164),
	 ('especialidades_delete',0x456C696D696E617220657370656369616C69646164),
	 ('noticias_show',0x56697375616C697A6172206E6F746963696173),
	 ('noticias_add',0x4372656172206E6F7469636961),
	 ('noticias_edit',0x456469746172206E6F7469636961),
	 ('noticias_delete',0x456C696D696E6172206E6F746963696173);
INSERT INTO permisos (slug,descripcion) VALUES
	 ('examenCuenta_show',0x56697375616C697A6172206578616D656E65732061206375656E7461);

CREATE TABLE `user_rol` (
  `user_id` bigint(20) unsigned NOT NULL,
  `rol_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`rol_id`),
  KEY `rol_id` (`rol_id`),
  CONSTRAINT `user_rol_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`Id`) ON UPDATE CASCADE,
  CONSTRAINT `user_rol_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO user_rol(user_id, rol_id) VALUES
(1,3),
(2,3);

CREATE TABLE rol_permisos(
    rol_id INT,
    permiso_id INT,
    PRIMARY KEY (`rol_id`,`permiso_id`),
    KEY `rol_id` (`rol_id`),
    CONSTRAINT FOREIGN KEY (rol_id) REFERENCES roles(Id),
    CONSTRAINT FOREIGN KEY (permiso_id) REFERENCES permisos(Id)
) ENGINE=InnoDB DEFAULT COLLATE=utf8mb4_unicode_ci;

INSERT INTO rol_permisos(rol_id, permiso_id) VALUES
(1,1),(2,1),(3,1),(6,1),(1,2),(2,2),(3,2),(4,2),(5,2),(6,2),(1,3),(2,3),(3,3),(6,3),(3,4),(3,5),(3,6),(1,7),(2,7),(3,7),(4,7),(5,7),(6,7),(1,8),(2,8),(3,8),(4,8),(5,8),(6,8),(1,9),(2,9),(3,9),(4,9),(5,9),(6,9),(2,10),(3,10),(5,10),(6,10),(1,11),(2,11),(3,11),(4,11),(5,11),(6,11),(3,12),(6,12),(2,13),(3,13),(4,13),(5,13),(6,13),(2,14),(3,14),(5,14),(6,14)

RENAME TABLE personal TO datos;

ALTER TABLE datos DROP COLUMN Inactivo;
ALTER TABLE datos DROP COLUMN EMail;
ALTER TABLE datos ADD Telefono VARCHAR(12) NULL;
ALTER TABLE clientes DROP FOREIGN KEY clientes_ibfk_3;
ALTER TABLE clientes DROP COLUMN IdAsignado;
ALTER TABLE hist_clientes DROP FOREIGN KEY hist_clientes_ibfk_3; /* IdAsignado en historial relacionado con personal */
ALTER TABLE iso_minutas_as DROP FOREIGN KEY iso_minutas_as_ibfk_6; /* IdPersonal relacionado con personal/datos */
ALTER TABLE iso_minutas_pd DROP FOREIGN KEY iso_minutas_pd_ibfk_2; /* IdPersonal relacionado con personal/datos */
ALTER TABLE stockmov DROP FOREIGN KEY stockmov_ibfk_6;
ALTER TABLE users ADD Anulado INT DEFAULT 0 NULL;

composer remove spatie/laravel-permission


SELECT TABLE_NAME 
FROM INFORMATION_SCHEMA.COLUMNS
WHERE COLUMN_NAME = 'IdProfesional'

facturascompra
hc_consultas
hist_facturascompra
hist_itemsprestaciones
itemsprestaciones
telefonos
usuarios

INSERT INTO rol_permisos(rol_id, permiso_id) VALUES
(1,42),(2,42),(3,42),(4,42),(5,42),(6,42),(8,42),(9,42);

INSERT INTO permisos(slug, descripcion) VALUES
("noticias","Botón y modulo de noticias");

INSERT INTO roles(nombre, descripcion) VALUES 
("Stock",""),
("Combinado",""),
("Evaluador","");

INSERT INTO rol_permisos(rol_id, permiso_id) VALUES
(2,43),(3,43),(4,43),(5,43),(6,43),(2,44),(3,44),(4,44),(5,44),(6,44),(2,45),(3,45),(4,45),(5,45),(6,45);

INSERT INTO rol_permisos(rol_id, permiso_id) VALUES
(2,46),(3,46),(4,46),(5,46),(6,46),(2,47),(3,47),(4,47),(5,47),(6,47);

INSERT INTO rol_permisos(rol_id, permiso_id) VALUES
(4,3),(5,3);

INSERT INTO permisos(slug, descripcion) VALUES
("mapas_show","Visualizar mapas");

INSERT INTO rol_permisos(rol_id, permiso_id) VALUES
(1,54),(2,54),(3,54),(4,54),(5,54),(6,54);

INSERT INTO permisos(slug, descripcion) VALUES
("examenCuenta_show","Visualizar examenes a cuenta");

INSERT INTO permisos(slug, descripcion) VALUES
("mensajeria_edit","Editar y actualizar correos");

INSERT INTO rol_permisos(rol_id, permiso_id) VALUES
(2,41),(4,41),(5,41),(6,41),(3,43),(3,44),(3,45),(3,46);


INSERT INTO rol_permisos(rol_id, permiso_id) VALUES
(3,59),(6,59);

ALTER TABLE profesionales_prov DROP COLUMN Tipo;
ALTER TABLE profesionales_prov ADD IdRol INT(11) DEFAULT 0 NULL;

INSERT INTO rol_permisos(rol_id, permiso_id) VALUES
(3,60),(3,61),(3,62),(3,63);

sudo mkdir /opt/lampp/htdocs/cmit/storage/app/public/facturas
sudo chmod 777 -R /opt/lampp/htdocs/cmit/storage/app/public/facturas

sudo mkdir /opt/lampp/htdocs/cmit/storage/app/public/examenescuenta
sudo chmod 777 -R /opt/lampp/htdocs/cmit/storage/app/public/examenescuenta

sudo chmod 777 -R /opt/lampp/htdocs/cmit/storage/logs/

sudo chmod 777 -R /opt/lampp/htdocs/cmit/storage/framework/views/

CREATE TABLE reportes_finneg (
    Id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    IdFinneg INT(11) NOT NULL,
    IdFactura INT(11) NOT NULL,
    cuit_cliente varchar(50) NOT NULL,
    created_at timestamp NULL DEFAULT NULL,
    updated_at timestamp NULL DEFAULT NULL,
    PRIMARY KEY (Id)
) ENGINE=InnoDB DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1000;


INSERT INTO rol_permisos(rol_id, permiso_id) VALUES
(3,65),(3,66),(3,67),(3,68);

ALTER TABLE profesionales ADD InfAdj INT DEFAULT 1 NOT NULL;

UPDATE proveedores SET InfAdj = 1;

INSERT INTO roles('Id', 'nombre') values('13', 'Adminstrador');


INSERT INTO rol_permisos(rol_id, permiso_id) VALUES
(13,1),(13,2),(13,3),(13,4),(13,5),(13,6),(13,7),(13,8),(13,9),(13,10);
INSERT INTO rol_permisos(rol_id, permiso_id) VALUES
(13,11),(13,12),(13,13),(13,14),(13,15),(13,16),(13,17),(13,18),(13,20);
INSERT INTO rol_permisos(rol_id, permiso_id) VALUES
(13,23),(13,24),(13,25),(13,26),(13,27),(13,28),(13,29),(13,30),(13,31),(13,32);
INSERT INTO rol_permisos(rol_id, permiso_id) VALUES
(13,33),(13,34),(13,35),(13,36),(13,37),(13,38),(13,39),(13,40),(13,41),(13,42),(13,43),(13,44);
INSERT INTO rol_permisos(rol_id, permiso_id) VALUES
(13,45),(13,46),(13,47),(13,48),(13,49),(13,50),(13,51),(13,52),(13,53),(13,59),(13,60),(13,61),(13,62);
INSERT INTO rol_permisos(rol_id, permiso_id) VALUES
(13,63),(13,64),(13,65),(13,66),(13,67),(13,68);

DELETE FROM rol_permisos 
WHERE rol_id = 3 AND permiso_id IN (43, 44, 45, 46);