composer require setasign/fpdf /** Reportes **/
composer require robgridley/flysystem-smb /** Trabajar en el NAS alternativo **/
composer require icewind/smb

/*** Permisos CHMOD ***/
sudo mkdir /opt/lampp/htdocs/cmit/storage/app/public/facturas
sudo chmod 777 -R /opt/lampp/htdocs/cmit/storage/app/public/facturas
sudo mkdir /opt/lampp/htdocs/cmit/storage/app/public/examenescuenta
sudo chmod 777 -R /opt/lampp/htdocs/cmit/storage/app/public/examenescuenta
sudo chmod 777 -R /opt/lampp/htdocs/cmit/storage/logs/
sudo chmod 777 -R /opt/lampp/htdocs/cmit/storage/framework/views/
sudo chmod +w foto-default.png
sudo chmod 666 foto-default.png

/*** Clientes ***/
ALTER TABLE clientes ADD COLUMN Estado INT DEFAULT 1;
ALTER TABLE clientes ADD Anexo INT DEFAULT 0 NULL;
ALTER TABLE clientes ADD EMailAnexo varchar(100) NULL;
ALTER TABLE clientes add Descuento INTEGER DEFAULT 0;

/*** FichaLaboral ***/
ALTER TABLE fichaslaborales ADD COLUMN TipoPrestacion VARCHAR(12) NULL;
ALTER TABLE fichaslaborales  ADD FechaPreocupacional DATE NULL;
ALTER TABLE fichaslaborales  ADD FechaUltPeriod DATE NULL;
ALTER TABLE fichaslaborales  ADD FechaExArt DATE NULL;
ALTER TABLE fichaslaborales ADD SPago CHAR(1);
ALTER TABLE fichaslaborales ADD Tipo CHAR(1);
ALTER TABLE fichaslaborales ADD Sucursal INT;
ALTER TABLE fichaslaborales ADD NroFactura INT;
ALTER TABLE fichaslaborales ADD NroFactProv VARCHAR(50);
ALTER TABLE fichaslaborales ADD Autorizado VARCHAR(150);

/*** Mapas ***/
ALTER TABLE mapas ADD COLUMN Cpacientes int(11) NULL DEFAULT 0;
ALTER TABLE mapas ADD COLUMN Cmapeados int(11) NULL DEFAULT 0;

/*** Constanciase ***/
ALTER TABLE constanciase ADD COLUMN Obs TEXT NULL;

/*** Telefono ***/
ALTER TABLE telefonos ADD COLUMN IdProfesional int(11) NULL DEFAULT 0;
ALTER TABLE telefonos ADD COLUMN IdCliente INT DEFAULT 0 NULL;

/*** Profesionales Proveedor ***/
ALTER TABLE profesionales_prov ADD Tipo VARCHAR(10) NULL; 

ALTER TABLE pacientes ADD COLUMN Estado INT DEFAULT 1;
ALTER TABLE pacientes MODIFY COLUMN Foto VARCHAR(100) DEFAULT 'foto-default.png' NULL;

/*** Pacientes ***/
ALTER TABLE prestaciones ADD COLUMN Estado INT DEFAULT 1;
ALTER TABLE prestaciones add NroFactProv VARCHAR(200) NULL;

/*** Profesionales ***/
ALTER TABLE profesionales ADD COLUMN wImage varchar(100) DEFAULT '250px';
ALTER TABLE profesionales ADD COLUMN hImage varchar(100) DEFAULT '250px';
ALTER TABLE profesionales ADD RegHis TINYINT UNSIGNED DEFAULT 1;
ALTER TABLE profesionales ADD T5 TINYINT UNSIGNED DEFAULT 0;
ALTER TABLE profesionales ADD InfAdj INT DEFAULT 1 NOT NULL;

/*** Facturas de Venta ***/
ALTER TABLE facturasventa ADD COLUMN IdPrestacion INT DEFAULT 0;

/*** Auditoria Acciones ***/
INSERT INTO auditoriaacciones (Id, Nombre) VALUES ('44', 'ENVIO E-ESTUDIO ART');

/*** Examen a cuenta **/
ALTER TABLE pagosacuenta_it ADD Precarga INT(9) DEFAULT 0 NULL;

/*** ItemsPrestaciones ***/
ALTER TABLE itemsprestaciones ADD FechaAsignadoI DATE DEFAULT '0000-00-00';
ALTER TABLE itemsprestaciones ADD HoraAsignadoI TIME DEFAULT '00:00:00'; 


/*** Personal a datos ***/
ALTER TABLE personal RENAME TO datos;
ALTER TABLE datos DROP COLUMN inactivo;
ALTER TABLE datos DROP COLUMN EMail;
ALTER TABLE datos ADD Telefono VARCHAR(12) NULL;
/*HAY QUE INSERTAR LOS DATOS DEL USUARIO QUE FALTA MANUALMENTE */

/*** User ***/
ALTER TABLE users DROP COLUMN `update`;
ALTER TABLE users ADD COLUMN datos_id INT;
ALTER TABLE users DROP COLUMN SR;
ALTER TABLE users CHANGE COLUMN idProfesional profesional_id INT;
ALTER TABLE users ADD inactivo INT DEFAULT 0 NULL;
ALTER TABLE users ADD Anulado INT DEFAULT 0 NULL;
INSERT INTO users (name,email,datos_id,email_verified_at,password,remember_token,created_at,updated_at,profesional_id,inactivo,Anulado) VALUES
	 ('nicolas','nmaximowicz@eximo.com.ar',{CAMBIAR POR EL ID DE DATOS},NULL,'$2y$10$gzHm1LAWcRT7Z1jpptQ25.JMLkxK64YEH/O3m/CqEzGXDq00dofW.',NULL,'2023-05-04 15:19:58','2024-05-20 01:44:18',230,0,0),
	 ('lucas','lucas@cmit.com.ar',1,NULL,'$2y$10$5xxjrbNwlDFSu/Q.wAvCMeJ/URPDh57efpgaPTfflvk2fmM74CzHK',NULL,'2023-05-08 16:27:18','2024-05-20 01:34:23',0,0,0),

/*** Roles ***/
/*INCORPORAR LA TABLA A LA BASE DE DATOS*/
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
("Informador",""),
("Stock",""),
("Combinado",""),
("Evaluador",""),
("Administrador","");

/*** Permisos ***/
CREATE TABLE permisos(
    Id INT NOT NULL AUTO_INCREMENT,
    slug VARCHAR(50) NOT NULL,
    descripcion BLOB,
    PRIMARY KEY (Id),
    UNIQUE(Id)
);

/* Registros insert actualizados */
INSERT INTO permisos (Id,slug,descripcion) VALUES
	 (1,'clientes_add',0x4167726567617220636C69656E746573206E7565766F73),
	 (2,'clientes_edit',0x65646974617220636C69656E746573),
	 (3,'clientes_show',0x76697375616C697A6172206461746F732064656C20636C69656E7465),
	 (5,'clientes_export',0x4578706F72746172207265706F7274657320656E2067656E6572616C),
	 (6,'clientes_delete',0x456C696D696E617220756E20636C69656E7465),
	 (7,'prestaciones_add',0x4167726567617220756E61206E75657661207072657374616369C3B36E),
	 (8,'prestaciones_edit',0x45646974617220756E61207072657374616369C3B36E),
	 (9,'prestaciones_show',0x56697375616C697A6172207072657374616369C3B36E),
	 (10,'prestaciones_delete',0x456C696D696E61722070726573746163696F6E6573),
	 (11,'prestaciones_block',0x416E756C61722070726573746163696F6E6573);
INSERT INTO permisos (Id,slug,descripcion) VALUES
	 (12,'prestaciones_todo',0x5574696C697A616369C3B36E2064656C20626F74C3B36E20746F646F),
	 (13,'prestaciones_eEnviar',0x5574696C697A616369C3B36E2064656C20626F74C3B36E2065456E76696172),
	 (14,'etapas_show',0x56697375616C697A617220657461706173),
	 (15,'etapas_apply',0x4574617061732061706C696361722C206365727261722C2061646A756E746172),
	 (16,'etapas_informador',0x5365636369C3B36E20696E666F726D61646F72),
	 (17,'etapas_efector',0x5365636369C3B36E2065666563746F72),
	 (18,'etapas_eenviar',0x5365636369C3B36E2065456E76696172),
	 (20,'mapas_edit',0x45646974617220756E206D617061),
	 (23,'pacientes_add',0x416772656761722070616369656E7465),
	 (24,'pacientes_edit',0x4564697461722070616369656E746573);
INSERT INTO permisos (Id,slug,descripcion) VALUES
	 (25,'pacientes_delete',0x456C696D696E61722070616369656E746573),
	 (26,'examenCta_add',0x41677265676172206578616D656E2061206375656E7461),
	 (27,'examenCta_edit',0x456469746172206578616D656E2061206375656E7461),
	 (28,'examenCta_delete',0x456C696D696E6172206578616D656E2061206375656E7461),
	 (29,'boton_usuarios',0x41636365736F2061207573756172696F73),
	 (30,'mapas_add',0x41677265676172206D61706173206E7565766F73),
	 (31,'mapas_cerrar',0x43657272617220756E206D617061),
	 (32,'mapas_finalizar',0x46696E616C697A617220756E206D617061),
	 (33,'mapas_eenviar',0x65456E7669617220756E206D617061),
	 (34,'stock_show',0x56697375616C697A61722073746F636B);
INSERT INTO permisos (Id,slug,descripcion) VALUES
	 (35,'stock_baja',0x6461722064652062616A612073746F636B),
	 (36,'stock_add',0x416772656761722073746F636B),
	 (37,'stock_parametros',0x506172616D6574726F732064652073746F636B),
	 (38,'manual',0x4D6F64756C6F2064652073746F636B20636F6D706C65746F),
	 (39,'boton_prestaciones',0x426F746F6E20736C696465722070726573746163696F6E6573),
	 (40,'pacientes_show',0x56697375616C697A61722070616369656E746573),
	 (41,'paciente_report',0x5265706F727465732070616369656E7465),
	 (42,'mapas_show',0x56697375616C697A6172206D61706173),
	 (43,'especialidades_show',0x56697375616C697A617220657370656369616C696461646573),
	 (44,'especialidades_add',0x4167726567617220657370656369616C69646164);
INSERT INTO permisos (Id,slug,descripcion) VALUES
	 (45,'especialidades_edit',0x4D6F6469666963617220657370656369616C69646164),
	 (46,'especialidades_delete',0x456C696D696E617220657370656369616C69646164),
	 (47,'noticias_show',0x56697375616C697A6172206E6F746963696173),
	 (48,'noticias_add',0x4372656172206E6F7469636961),
	 (49,'noticias_edit',0x456469746172206E6F7469636961),
	 (50,'noticias_delete',0x456C696D696E6172206E6F746963696173),
	 (51,'examenCta_show',0x56697375616C697A6172206578616D656E65732061206375656E7461),
	 (53,'mensajeria_edit',0x45646974617220792061637475616C697A617220636F7272656F73),
	 (59,'prestaciones_report',0x496D7072657369C3B36E206465207265706F727465732064652070726573746163696F6E6573),
	 (60,'facturacion_show',0x56697375616C697A616369C3B36E20646520666163747572616369C3B36E);
INSERT INTO permisos (Id,slug,descripcion) VALUES
	 (61,'facturacion_add',0x4167726567617220666163747572616369C3B36E),
	 (62,'facturacion_edit',0x45646974617220666163747572616369C3B36E),
	 (63,'facturacion_delete',0x456C696D696E617220666163747572616369C3B36E),
	 (64,'mapas_delete',0x456C696D696E6172206D617061),
	 (69,'mensajeria_show',0x56697375616C697A6172206D656E73616A6573206120656E76696172206120636C69656E746573),
	 (70,'mensajeria_edit',0x45646963696F6E2079207265616C697A6163696F6E20646520656E76696F73),
	 (71,'boton_todo',0x426F74C3B36E20544F444F20656E2070726573746163696F6E657320636F6E2066756E63696F6E616C696461646573206D756C7469706C6573),
	 (72,'examenes_show',0x56697375616C697A6163696F6E206465206578616D656E6573),
	 (73,'examenes_add',0x41677265676172206E7565766F73206578616D656E6573),
	 (74,'examenes_edit',0x456469746172206578616D656E6573);
INSERT INTO permisos (Id,slug,descripcion) VALUES
	 (75,'examenes_delete',0x456C696D696E6172206578616D656E6573),
	 (76,'datos_add',0x4372656163696F6E20792061637475616C697A6163696F6E206465206461746F732064656C207573756172696F),
	 (77,'usuarios_show',0x56697375616C697A6172207573756172696F73),
	 (78,'usuarios_add',0x4372656172206E7565766F207573756172696F),
	 (79,'usuarios_edit',0x456469746172207573756172696F),
	 (80,'usuarios_delete',0x456C696D696E6172207573756172696F),
	 (81,'examenCta_report',0x5265706F727465206578616D656E2061206375656E7461);

/*** Rol de usuarios ***/
CREATE TABLE `user_rol` (
  `user_id` bigint(20) unsigned NOT NULL,
  `rol_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`rol_id`),
  KEY `rol_id` (`rol_id`),
  CONSTRAINT `user_rol_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`Id`) ON UPDATE CASCADE,
  CONSTRAINT `user_rol_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO user_rol(user_id, rol_id) VALUES
(1,13),
(2,13);

/*** Rol permisos ***/
CREATE TABLE rol_permisos(
    rol_id INT,
    permiso_id INT,
    PRIMARY KEY (`rol_id`,`permiso_id`),
    KEY `rol_id` (`rol_id`),
    CONSTRAINT FOREIGN KEY (rol_id) REFERENCES roles(Id),
    CONSTRAINT FOREIGN KEY (permiso_id) REFERENCES permisos(Id)
) ENGINE=InnoDB DEFAULT COLLATE=utf8mb4_unicode_ci;

INSERT INTO rol_permisos (rol_id,permiso_id) VALUES
	 (1,1),
	 (1,2),
	 (1,3),
	 (1,26),
	 (1,27),
	 (1,28),
	 (1,38),
	 (1,47),
	 (1,51),
	 (2,1);
INSERT INTO rol_permisos (rol_id,permiso_id) VALUES
	 (2,2),
	 (2,3),
	 (2,7),
	 (2,8),
	 (2,9),
	 (2,10),
	 (2,11),
	 (2,13),
	 (2,14),
	 (2,15);
INSERT INTO rol_permisos (rol_id,permiso_id) VALUES
	 (2,16),
	 (2,17),
	 (2,18),
	 (2,20),
	 (2,23),
	 (2,24),
	 (2,25),
	 (2,26),
	 (2,27),
	 (2,28);
INSERT INTO rol_permisos (rol_id,permiso_id) VALUES
	 (2,30),
	 (2,31),
	 (2,32),
	 (2,33),
	 (2,38),
	 (2,40),
	 (2,41),
	 (2,42),
	 (2,47),
	 (2,48);
INSERT INTO rol_permisos (rol_id,permiso_id) VALUES
	 (2,49),
	 (2,50),
	 (2,51),
	 (2,64),
	 (3,1),
	 (3,2),
	 (3,3),
	 (3,5),
	 (3,6),
	 (3,7);
INSERT INTO rol_permisos (rol_id,permiso_id) VALUES
	 (3,8),
	 (3,9),
	 (3,10),
	 (3,11),
	 (3,12),
	 (3,13),
	 (3,14),
	 (3,15),
	 (3,16),
	 (3,17);
INSERT INTO rol_permisos (rol_id,permiso_id) VALUES
	 (3,18),
	 (3,20),
	 (3,23),
	 (3,24),
	 (3,25),
	 (3,26),
	 (3,27),
	 (3,28),
	 (3,30),
	 (3,31);
INSERT INTO rol_permisos (rol_id,permiso_id) VALUES
	 (3,32),
	 (3,33),
	 (3,34),
	 (3,35),
	 (3,36),
	 (3,37),
	 (3,38),
	 (3,40),
	 (3,41),
	 (3,42);
INSERT INTO rol_permisos (rol_id,permiso_id) VALUES
	 (3,47),
	 (3,48),
	 (3,49),
	 (3,50),
	 (3,51),
	 (3,53),
	 (3,59),
	 (3,60),
	 (3,61),
	 (3,62);
INSERT INTO rol_permisos (rol_id,permiso_id) VALUES
	 (3,63),
	 (3,64),
	 (3,71),
	 (3,72),
	 (3,73),
	 (3,74),
	 (3,75),
	 (4,3),
	 (4,7),
	 (4,8);
INSERT INTO rol_permisos (rol_id,permiso_id) VALUES
	 (4,9),
	 (4,11),
	 (4,13),
	 (4,14),
	 (4,15),
	 (4,23),
	 (4,24),
	 (4,25),
	 (4,38),
	 (4,40);
INSERT INTO rol_permisos (rol_id,permiso_id) VALUES
	 (4,41),
	 (4,47),
	 (5,3),
	 (5,7),
	 (5,8),
	 (5,9),
	 (5,11),
	 (5,13),
	 (5,14),
	 (5,15);
INSERT INTO rol_permisos (rol_id,permiso_id) VALUES
	 (5,16),
	 (5,17),
	 (5,18),
	 (5,20),
	 (5,23),
	 (5,24),
	 (5,25),
	 (5,30),
	 (5,33),
	 (5,38);
INSERT INTO rol_permisos (rol_id,permiso_id) VALUES
	 (5,40),
	 (5,41),
	 (5,42),
	 (5,47),
	 (6,1),
	 (6,2),
	 (6,3),
	 (6,7),
	 (6,8),
	 (6,9);
INSERT INTO rol_permisos (rol_id,permiso_id) VALUES
	 (6,10),
	 (6,11),
	 (6,12),
	 (6,13),
	 (6,14),
	 (6,15),
	 (6,16),
	 (6,17),
	 (6,18),
	 (6,20);
INSERT INTO rol_permisos (rol_id,permiso_id) VALUES
	 (6,23),
	 (6,24),
	 (6,25),
	 (6,26),
	 (6,27),
	 (6,28),
	 (6,30),
	 (6,31),
	 (6,32),
	 (6,33);
INSERT INTO rol_permisos (rol_id,permiso_id) VALUES
	 (6,34),
	 (6,38),
	 (6,40),
	 (6,41),
	 (6,42),
	 (6,47),
	 (6,48),
	 (6,49),
	 (6,50),
	 (6,51);
INSERT INTO rol_permisos (rol_id,permiso_id) VALUES
	 (6,59),
	 (6,71),
	 (7,20),
	 (7,31),
	 (7,38),
	 (7,42),
	 (8,42),
	 (10,34),
	 (10,36),
	 (10,38);
INSERT INTO rol_permisos (rol_id,permiso_id) VALUES
	 (12,20),
	 (12,31),
	 (12,42),
	 (13,1),
	 (13,2),
	 (13,3),
	 (13,5),
	 (13,6),
	 (13,7),
	 (13,8);
INSERT INTO rol_permisos (rol_id,permiso_id) VALUES
	 (13,9),
	 (13,10),
	 (13,11),
	 (13,12),
	 (13,13),
	 (13,14),
	 (13,15),
	 (13,16),
	 (13,17),
	 (13,18);
INSERT INTO rol_permisos (rol_id,permiso_id) VALUES
	 (13,20),
	 (13,23),
	 (13,24),
	 (13,25),
	 (13,26),
	 (13,27),
	 (13,28),
	 (13,29),
	 (13,30),
	 (13,31);
INSERT INTO rol_permisos (rol_id,permiso_id) VALUES
	 (13,32),
	 (13,33),
	 (13,34),
	 (13,35),
	 (13,36),
	 (13,37),
	 (13,38),
	 (13,39),
	 (13,40),
	 (13,41);
INSERT INTO rol_permisos (rol_id,permiso_id) VALUES
	 (13,42),
	 (13,43),
	 (13,44),
	 (13,45),
	 (13,46),
	 (13,47),
	 (13,48),
	 (13,49),
	 (13,50),
	 (13,51);
INSERT INTO rol_permisos (rol_id,permiso_id) VALUES
	 (13,59),
	 (13,60),
	 (13,61),
	 (13,62),
	 (13,63),
	 (13,64),
	 (13,69),
	 (13,70),
	 (13,71),
	 (13,72);
INSERT INTO rol_permisos (rol_id,permiso_id) VALUES
	 (13,73),
	 (13,74),
	 (13,75),
	 (13,76),
	 (13,77),
	 (13,78),
	 (13,79),
	 (13,80);

/*** Fases ***/
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

ALTER TABLE prestaciones_obsfases ADD COLUMN obsfases_id INT DEFAULT 1;

ALTER TABLE prestaciones_obsfases ADD FOREIGN KEY(obsfases_id) REFERENCES tipos_obsfases(Id);
ALTER TABLE prestaciones_obsfases MODIFY COLUMN Rol VARCHAR(50) NOT NULL;
INSERT INTO auditoriatablas (Id, Nombre) VALUES ('5', 'MAPAS');
INSERT INTO auditoriaacciones (Id, Nombre) VALUES 
('41', 'ENVIO E-ESTUDIO ART'),
('42', 'ENVIO E-ESTUDIO EMPRESA'),
('43', 'DESCARGA E-ESTUDIO');

ALTER TABLE auditoria MODIFY COLUMN IdUsuario varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL; 

/*** Store Procedure ***/
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
            (i.CAdj IN (1,2,4) AND i.IdProfesional <> 0)
        WHEN estados = 'cerrados' THEN
            (i.CAdj IN (3,5) AND i.IdProfesional <> 0)
        WHEN estados = 'asignados' THEN
            (i.IdProfesional <> 0
            AND
            (
                SELECT COUNT(*)
                FROM archivosefector
                WHERE IdEntidad = i.Id
            ) = 0
            AND
            i.CAdj IN (1,2,4))
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
    and not i.CInfo IN (3,0) 
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
    AND i.CInfo = 1 
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
    SELECT 
        i.Id AS IdItem, 
        i.Fecha AS Fecha, 
        i.CAdj AS Efector, 
        i.CInfo AS Informador, 
        i.IdProfesional AS IdProfesional, 
        pro.Nombre AS Especialidad, 
        pro.Id AS IdEspecialidad, 
        pre.Id AS IdPrestacion, 
        pre.Cerrado AS PresCerrado, 
        pre.Finalizado AS PresFinalizado, 
        pre.Entregado AS PresEntregado, 
        pre.eEnviado AS PresEnviado, 
        cli.RazonSocial AS Empresa, 
        pa.Nombre AS NombrePaciente, 
        pa.Apellido AS ApellidoPaciente, 
        pa.Documento AS Dni, 
        prof1.Nombre AS NombreProfesional, 
        prof1.Apellido AS ApellidoProfesional, 
        prof2.Nombre AS NombreProfesional2, 
        prof2.Apellido AS ApellidoProfesional2, 
        exa.Nombre AS Examen, 
        exa.Id AS IdExamen, 
        exa.DiasVencimiento AS DiasVencimiento, 
        (CASE 
            WHEN i.CAdj IN (1,4) AND exa.NoImprime = 1 THEN 'Pdte_D' 
            WHEN i.CAdj IN (1,4) AND exa.NoImprime = 0 THEN 'Pdte_F' 
            WHEN i.CAdj IN (2,5) THEN 'Adj' 
            ELSE ' ' 
        END) AS Adj,
        (CASE 
            WHEN pre.Finalizado = 0 AND pre.Cerrado = 0 AND pre.Entregado = 0 THEN 'Abierto' 
            WHEN pre.Cerrado = 1 AND pre.Finalizado = 0 THEN 'Cerrado' 
            WHEN pre.Cerrado = 1 AND pre.Finalizado = 1 AND pre.Entregado = 0 THEN 'Finalizado' 
            WHEN pre.Cerrado = 1 AND pre.Finalizado = 1 AND pre.Entregado = 1 THEN 'Entregado' 
            ELSE ' ' 
        END) AS estado,
        (CASE 
            WHEN pre.eEnviado = 1 THEN 'eEnv' 
            ELSE '' 
        END) AS eEnv,
        (CASE 
            WHEN i.CAdj IN (1,2,4) THEN 'Pendiente' 
            WHEN i.CAdj IN (3,5) THEN 'Cerrado' 
            ELSE '-' 
        END) AS EstadoEfector,
        (CASE 
            WHEN i.CInfo = 1 THEN 'Pendiente' 
            WHEN i.CInfo = 2 THEN 'Borrador' 
            WHEN i.CInfo IN (3,0) THEN 'Cerrado' 
            ELSE '-' 
        END) AS EstadoInformador
    FROM itemsprestaciones i 
    INNER JOIN prestaciones pre ON i.IdPrestacion = pre.Id 
        AND pre.Estado = 1 
        AND pre.Anulado = 0
    INNER JOIN examenes exa ON i.IdExamen = exa.Id 
        AND (examen IS NULL OR exa.Id = examen) 
        AND (adjunto IS NULL OR 
            (CASE 
                WHEN adjunto = 'fisico' THEN exa.NoImprime = 0 
                WHEN adjunto = 'digital' THEN exa.NoImprime = 1 
            END)
        )
    INNER JOIN proveedores pro ON exa.IdProveedor2 = pro.Id 
        AND (especialidad IS NULL OR pro.Id = especialidad)
    INNER JOIN clientes cli ON pre.IdEmpresa = cli.Id 
    INNER JOIN pacientes pa ON pre.IdPaciente = pa.Id 
    INNER JOIN profesionales prof1 ON i.IdProfesional = prof1.Id 
        AND (efector IS NULL OR prof1.Id = efector)
    INNER JOIN profesionales prof2 ON i.IdProfesional2 = prof2.Id 
        AND (informador IS NULL OR prof2.Id = informador)
    LEFT JOIN archivosefector a ON i.Id = a.IdEntidad 
    WHERE 
        i.Id <> 0 
        AND i.Fecha BETWEEN fechaDesde AND fechaHasta 
        AND (estadoPres IS NULL OR
            (estadoPres = 'abierto' AND pre.Finalizado = 0 AND pre.Cerrado = 0 AND pre.Entregado = 0
            OR estadoPres = 'cerrado' AND pre.Cerrado = 1 AND pre.Finalizado = 0
            OR estadoPres = 'finalizado' AND pre.Cerrado = 1 AND pre.Finalizado = 1 AND pre.Entregado = 0
            OR estadoPres = 'entregado' AND pre.Cerrado = 1 AND pre.Finalizado = 1 AND pre.Entregado = 1
            OR estadoPres = 'eenviado' AND pre.eEnviado = 1
            )
        )
        AND (estadoEfector IS NULL OR
            (estadoEfector = 'pendientes' AND i.CAdj IN (0,1,2) 
            OR estadoEfector = 'cerrados' AND i.CAdj IN (3,4,5)
            )
        )
        AND (estadoInformador IS NULL OR
            (estadoInformador = 'pendientes' AND i.CInfo = 1 
            OR estadoInformador = 'borrador' AND i.CInfo = 2
            OR estadoInformador = 'pendienteYborrador' AND i.CInfo IN (1,2)
            )
        )
        AND (tipoProv IS NULL OR
            (tipoProv = 'interno' AND pro.Externo = 0 
            OR tipoProv = 'externo' AND pro.Externo = 1
            OR tipoProv = 'todos' AND pro.Externo IN (0,1)
            )
        )
        AND (ausente IS NULL OR
            (ausente = 'ausente' AND i.Ausente = 1 
            OR ausente = 'noAusente' AND i.Ausente = 0
            OR ausente = 'todos' AND i.Ausente IN (0,1)
            )
        )
        AND (adjuntoEfector IS NULL OR
            (adjuntoEfector = 1 AND a.IdEntidad = i.Id AND exa.adjunto = 1)
        )
        AND (vencido IS NULL OR
            (vencido = 1 AND exa.DiasVencimiento > 0 AND pre.Finalizado = 0 AND pre.Vto <> 0 AND DATE_ADD(i.Fecha, INTERVAL exa.DiasVencimiento DAY) <= fechaHasta AND DAY(DATE_ADD(i.Fecha, INTERVAL exa.DiasVencimiento DAY)) > DAY(i.Fecha))
        )
        AND (pendiente IS NULL OR 
            (pendiente = 1 AND i.CAdj IN(0,1,2) 
                           OR i.CAdj IN(1,4) 
                           OR i.CInfo=1 
                           OR i.CInfo=2 
                           AND (estadoInformador IS NULL
                           AND tipoProv IS NULL 
                           AND ausente IS NULL
                           AND adjuntoEfector IS NULL)
            )
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
	AND (CASE
		WHEN abierto IS NULL THEN i.Fecha BETWEEN fechaDesde AND fechaHasta
		WHEN abierto = "abierto" THEN i.CAdj in (0,1,2) AND i.CInfo = 1 AND pc.Pagado = 0
		END)
	AND (CASE
		WHEN cerrado IS NULL THEN i.Fecha BETWEEN fechaDesde AND fechaHasta
		WHEN cerrado = "cerrado" THEN i.CAdj in (3,4,5) AND i.CInfo = 3 AND pc.Pagado IN (0,1)
		END)
	AND NOT i.Id = 0
	AND i.Anulado = 0
	AND NOT (i.Fecha IS NULL OR i.Fecha = '0000-00-00')
	GROUP BY pre.Id
	ORDER BY i.Id DESC 
	LIMIT 5000;
		END)
    AND NOT i.Id = 0
    AND i.Anulado = 0
    AND NOT (i.Fecha IS NULL OR i.Fecha = '0000-00-00')
    GROUP BY pre.Id
    ORDER BY i.Id DESC 
    LIMIT 5000;
END

/*********** Cambio en Informador de columna Informador *******************/
(CASE
	        WHEN i.CInfo = 0 THEN ''
            WHEN i.CInfo = 1 THEN 'Pendiente' 
            WHEN i.CInfo = 2 THEN 'Borrador' 
            WHEN i.CInfo = 3 THEN 'Cerrado' 
            ELSE '-' 
        END) AS EstadoInformador

ALTER TABLE profesionales_prov MODIFY COLUMN IdRol VARCHAR(10) DEFAULT '0' NULL;
#Aplicar a PreProduccion

CREATE TABLE aliasExamenes(
	Id int AUTO_INCREMENT NOT NULL,
	UNIQUE(Id),
	Nombre VARCHAR(30) NOT NULL,
	Descripcion VARCHAR(225) NULL,
	PRIMARY KEY(Id)
);

/*** No va en Pre Produccion ***/
#ALTER TABLE examenes ADD COLUMN aliasexamen_id INT DEFAULT 0 NOT NULL;
#INSERT INTO aliasExamenes(Id, Nombre, Descripcion) VALUES(0, 'Sin datos', 'Sin datos');
#ALTER TABLE examenes ADD CONSTRAINT fk_aliasexamenes FOREIGN KEY (aliasexamen_id) REFERENCES aliasExamenes(Id);


#Verificar el Id 0 de alias para evitar problemas

ALTER TABLE archivosefector ADD PuntoCarga INT DEFAULT 0 NOT NULL;
ALTER TABLE archivosinformador ADD PuntoCarga INT DEFAULT 0 NOT NULL;

#CASCADE en fk_archivosefector y fk_archivosinformador (NO VA)
ALTER TABLE examenes DROP FOREIGN KEY fk_aliasexamenes;
ALTER TABLE examenes CHANGE aliasexamen_id aliasexamen VARCHAR(50) NULL;
ALTER TABLE examenes MODIFY COLUMN aliasexamen VARCHAR(50) NULL;

UPDATE examenes SET aliasexamen = NULL;

ALTER TABLE mapas CHANGE IdEMpresa IdEmpresa int(11) NOT NULL
