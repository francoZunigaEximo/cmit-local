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