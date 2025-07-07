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
ALTER TABLE profesionales ADD RegHis TINYINT UNSIGNED DEFAULT 0;
UPDATE profesionales SET RegHis = 1; /*Se toman todos los datos como antiguos, salvo los que se creen de cero */
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
        proEfector.Nombre AS Especialidad, 
        proEfector.Id AS IdEspecialidad, 
        pre.Id AS IdPrestacion, 
        pre.Cerrado AS PresCerrado, 
        pre.Finalizado AS PresFinalizado, 
        pre.Entregado AS PresEntregado, 
        pre.eEnviado AS PresEnviado, 
        cli.RazonSocial AS Empresa, 
        CONCAT(pa.Apellido,' ',pa.Nombre) as NombreCompleto,
        pa.Documento AS Dni, 
         (CASE 
		     WHEN (userPro1.profesional_id <> 0 AND (prof1.Nombre IS NULL OR prof1.Nombre = '')) THEN 
		         CONCAT(COALESCE(datosPro1.Apellido, ''), ' ', COALESCE(datosPro1.Nombre, ''))
		     ELSE 
		         CONCAT(COALESCE(prof1.Apellido, ''), ' ', COALESCE(prof1.Nombre, ''))
		 END) as profesionalEfector,
         (CASE 
         	WHEN (userPro2.profesional_id <> 0 AND (prof2.Nombre IS NULL OR prof1.Nombre = '')) THEN
         		CONCAT(COALESCE(datosPro2.Apellido, ''),' ',COALESCE(datosPro2.Nombre, ''))
         	ELSE 
         		CONCAT(COALESCE(prof2.Apellido, ''),' ',COALESCE(prof2.Nombre,''))
        END) as profesionalInformador,
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
	        WHEN i.CInfo = 0 THEN ''
            WHEN i.CInfo = 1 THEN 'Pendiente' 
            WHEN i.CInfo = 2 THEN 'Borrador' 
            WHEN i.CInfo = 3 THEN 'Cerrado' 
            ELSE '-' 
        END) AS EstadoInformador
    FROM itemsprestaciones i 
    INNER JOIN prestaciones pre ON i.IdPrestacion = pre.Id 
        AND pre.Estado = 1 
        AND pre.Anulado = 0
    LEFT JOIN examenes exa ON i.IdExamen = exa.Id 
        AND (examen IS NULL OR exa.Id = examen) 
        AND (adjunto IS NULL OR 
            (CASE 
                WHEN adjunto = 'fisico' THEN exa.NoImprime = 0 
                WHEN adjunto = 'digital' THEN exa.NoImprime = 1 
            END)
        )
    LEFT JOIN proveedores proEfector ON exa.IdProveedor = proEfector.Id 
	LEFT JOIN proveedores proInformador ON exa.IdProveedor2 = proInformador.Id 
    LEFT JOIN clientes cli ON pre.IdEmpresa = cli.Id 
    LEFT JOIN pacientes pa ON pre.IdPaciente = pa.Id 
    LEFT JOIN profesionales prof1 ON i.IdProfesional = prof1.Id
        AND (efector IS NULL OR prof1.Id = efector)
    LEFT JOIN profesionales prof2 ON i.IdProfesional2 = prof2.Id 
        AND (informador IS NULL OR prof2.Id = informador)
    LEFT JOIN users userPro1 ON prof1.Id = userPro1.profesional_id
    LEFT JOIN users userPro2 ON prof2.Id = userPro2.profesional_id
    LEFT JOIN datos datosPro1 ON userPro1.datos_id = datosPro1.Id
    LEFT JOIN datos datosPro2 ON userPro2.datos_id = datosPro2.Id
    LEFT JOIN archivosefector a ON i.Id = a.IdEntidad 
    WHERE i.Id <> 0 
    AND i.Fecha BETWEEN fechaDesde AND fechaHasta
    AND (
        estadoPres IS NULL
        OR (estadoPres = 'abierto' AND pre.Finalizado = 0 AND pre.Cerrado = 0 AND pre.Entregado = 0)
        OR (estadoPres = 'cerrado' AND pre.Cerrado = 1 AND pre.Finalizado = 0)
        OR (estadoPres = 'finalizado' AND pre.Cerrado = 1 AND pre.Finalizado = 1 AND pre.Entregado = 0)
        OR (estadoPres = 'entregado' AND pre.Cerrado = 1 AND pre.Finalizado = 1 AND pre.Entregado = 1)
        OR (estadoPres = 'eenviado' AND pre.eEnviado = 1)
    )
    AND (
    	estadoEfector IS NULL 
    	OR (estadoEfector = 'pendientes' AND i.CAdj IN (0,1,2))
       	OR (estadoEfector = 'cerrados' AND i.CAdj IN (3,4,5))
   )
   AND (estadoInformador IS NULL 
   		OR (estadoInformador = 'pendientes' AND i.CInfo = 1)
        OR (estadoInformador = 'borrador' AND i.CInfo = 2)
        OR (estadoInformador = 'pendienteYborrador' AND i.CInfo IN (1,2))
   )
   AND (tipoProv IS NULL 
   		OR (tipoProv = 'interno' AND proEfector.Externo = 0)
        OR (tipoProv = 'externo' AND proEfector.Externo = 1)
        OR (tipoProv = 'todos' AND proEfector.Externo IN (0,1))
   )
   AND (adjuntoEfector IS NULL 
   		OR (adjuntoEfector = 1 AND a.IdEntidad = i.Id AND exa.adjunto = 1)
   )
   AND (especialidad IS NULL OR (proEfector.Id = especialidad))
   AND (efector IS NULL OR (prof1.Id = efector))
   AND (informador IS NULL OR (prof2.Id = informador))
   AND (
	    vencido IS NULL
	    OR (
	        vencido = 1 
	        AND exa.DiasVencimiento > 0 
	        AND pre.Finalizado = 0 
	        AND pre.Vto <> 0 
	        AND DATE_ADD(i.Fecha, INTERVAL exa.DiasVencimiento DAY) <= fechaHasta 
	        AND DATE_ADD(i.Fecha, INTERVAL exa.DiasVencimiento DAY) > i.Fecha
	    )
	)
	
    AND (ausente IS NULL 
    		OR (ausente = 'ausente' AND i.Ausente = 1)
            OR (ausente = 'noAusente' AND i.Ausente = 0)
            OR (ausente = 'todos' AND i.Ausente IN (0,1))
    )
   AND (pendiente IS NULL 
    OR 
    pendiente <> 1
    OR 
    (
        pendiente = 1 
        AND (i.CAdj IN (0,1,2,4) OR i.CInfo IN (1,2))
    )
)
    AND i.Anulado = 0
    GROUP BY(i.Id)
    ORDER BY i.Id DESC 
    LIMIT 5000;
END 

END


CREATE PROCEDURE getSearchEEnviar(IN fechaDesde DATE, IN fechaHasta DATE, IN empresa INT, IN paciente INT, IN completo VARCHAR, IN abierto VARCHAR, IN cerrado VARCHAR, IN eenviar VARCHAR, IN impago VARCHAR)

BEGIN
    SELECT 
        pre.Fecha AS Fecha,
        pre.Id AS IdPrestacion,
        pc2.IdPrestacion AS presta,
        pre.FechaEnviado AS FechaEnviado,
        cli.EMailInformes AS Correo,
        cli.RazonSocial AS Empresa,
        CONCAT(pa.Apellido, ' ', pa.Nombre) AS NombreCompleto,
        pa.Documento AS Documento,
        pa.Id AS IdPaciente,
        pre.eEnviado AS eEnviado,
        pre.Cerrado AS Cerrado
    FROM 
        prestaciones AS pre
    JOIN 
        clientes AS cli ON pre.IdEmpresa = cli.Id AND (empresa IS NULL OR cli.Id = empresa)
    JOIN 
        pacientes AS pa ON pre.IdPaciente = pa.Id AND (paciente IS NULL OR pa.Id = paciente)
    JOIN 
        pagosacuenta AS pc ON cli.Id = pc.IdEmpresa
    LEFT JOIN 
        pagosacuenta_it AS pc2 ON pc.Id = pc2.IdPago
    JOIN 
        itemsprestaciones AS i ON pre.Id = i.IdPrestacion
    WHERE 
        pre.Id != 0
        AND pre.Fecha != '0000-00-00'
        AND pre.Fecha IS NOT NULL
        AND i.Fecha BETWEEN fechaDesde AND fechaHasta
        AND pre.Cerrado = 1
        AND (
            CASE
                WHEN completo = 'activo' THEN i.CAdj IN (3, 5) AND i.CInfo = 3 AND pc.Pagado = 1
                WHEN abierto = 'activo' THEN i.CAdj IN (0, 1, 2) AND i.CInfo = 1 AND pc.Pagado = 0
                WHEN cerrado = 'activo' THEN i.CAdj IN (3, 4, 5) AND i.CInfo = 3 AND pc.Pagado IN (0, 1)
                WHEN impago = 'activo' THEN EXISTS (
                    SELECT 1 
                    FROM pagosacuenta_it AS pc_it
                    JOIN pagosacuenta AS pc_check ON pc_it.IdPago = pc_check.Id
                    WHERE pc_it.IdPrestacion = pre.Id 
                    AND pc_check.Pagado = 0
                )
                ELSE (completo IS NULL AND abierto IS NULL AND cerrado IS NULL AND impago IS NULL)
            END
        )
        AND (
            CASE
                WHEN eenviar IS NULL OR eenviar = '' THEN TRUE
                WHEN eenviar = 'eenviado' AND pre.eEnviado = 1 THEN TRUE
                WHEN eenviar = 'noeenviado' AND pre.eEnviado = 0 THEN TRUE
                WHEN eenviar = 'todos' AND pre.eEnviado IN (0, 1) THEN TRUE
                ELSE FALSE
            END
        )
    GROUP BY 
        pre.Id
    ORDER BY
    	CASE 
	        WHEN cli.EMailInformes IS NULL OR cli.EMailInformes = '' THEN 0
	        ELSE 1
	    END,
    	cli.EMailInformes DESC,
        pre.Fecha DESC,
        cli.RazonSocial DESC
    LIMIT 1000;
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

/* Funciones de ViteJS */
npm install
npm laravel-vite-plugin

-- npm install toastr jquery sweetalert select2 datatables.net datatables.net-dt
|
ALTER TABLE mapas ADD COLUMN FechaAsignacion DATE NULL;

CREATE TABLE llamador(
	Id int AUTO_INCREMENT NOT NULL,
	UNIQUE(Id),
	profesional_id int(11) NOT NULL,
	prestacion_id int(11) NOT NULL,
	itemprestacion_id int(11) NOT NULL,
	CONSTRAINT FK_ProfesionalLlamador FOREIGN KEY (profesional_id) REFERENCES profesionales(Id),
	CONSTRAINT FK_PrestacionLlamador FOREIGN KEY (prestacion_id) REFERENCES prestaciones(Id),
	CONSTRAINT FK_ItemprestacionLlamador FOREIGN KEY (itemprestacion_id) REFERENCES itemsprestaciones(Id),
	PRIMARY KEY(Id)
);

#Optimizacion de redis - agregar a los env
SESSION_CONNECTION=default

CREATE TABLE `user_sessions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `session_id` varchar(191) DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `login_at` datetime NOT NULL DEFAULT current_timestamp(),
  `logout_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_login_at` (`login_at`),
  KEY `idx_logout_at` (`logout_at`),
  CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

ALTER TABLE profesionales_prov MODIFY COLUMN IdRol varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT '0' NULL;


ALTER TABLE fichaslaborales MODIFY COLUMN FechaUltPeriod VARCHAR(15) DEFAULT NULL;
ALTER TABLE fichaslaborales MODIFY COLUMN FechaExArt VARCHAR(15) DEFAULT NULL;
ALTER TABLE proveedores ADD COLUMN Obs TEXT NULL;

DROP PROCEDURE IF EXISTS getExamenesPaquete;

DELIMITER $$
$$
CREATE DEFINER=`db_cmit`@`%` PROCEDURE `db_cmit`.`getExamenesPaquete`(IN `IdPaquete` INT)
BEGIN
	SELECT e.Id, e.Nombre FROM relpaqest r
	INNER JOIN examenes e ON r.IdExamen = e.Id 
	WHERE r.IdPaquete = IdPaquete;
END
$$
DELIMITER ;

DROP PROCEDURE IF EXISTS db_cmit.getListaExCta;

DELIMITER $$
$$
CREATE DEFINER=`db_cmit`@`%` PROCEDURE `db_cmit`.`getListaExCta`(IN `IdCliente` INT)
BEGIN
SELECT 
    COUNT(e.Nombre) AS CantidadExamenes,
    c.RazonSocial AS Empresa,
    e.Nombre AS NombreExamen,
    pi2.Precarga AS Documento,
    CONCAT(
        p.Tipo,
        LPAD(p.Suc, 4, '0'),
        '-',
        LPAD(p.Nro, 8, '0')
    ) AS Factura,
    MAX(p.Obs) as Obs,
    MAX(pi2.Id) as IdEx,
	MAX(e.Id) as IdFiltro
	FROM pagosacuenta_it pi2
	INNER JOIN pagosacuenta p ON pi2.IdPago = p.Id
	INNER JOIN clientes c ON p.IdEmpresa = c.Id
	INNER JOIN examenes e ON pi2.IdExamen = e.Id
	WHERE 
	    c.Id = IdCliente
	    AND pi2.Obs <> 'provisorio'
	    AND pi2.IdPrestacion = 0
	    AND NOT pi2.IdExamen = 0
	GROUP BY 
	    c.RazonSocial, 
	    e.Nombre,
	    pi2.Precarga,
	    Factura
	ORDER BY 
	    pi2.IdPrestacion,
	    pi2.Precarga ASC;
END

INSERT INTO rol_permisos (rol_id,permiso_id) VALUES(13,81); -- Permiso a administrador para imprimir Saldos y Detalles

-- agregamos la columna de vista previa en la tabla reportes
CREATE TABLE user_sessions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    session_id VARCHAR(191) NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NULL,
    login_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    logout_at DATETIME NULL DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE user_sessions ADD INDEX idx_user_id (user_id);
ALTER TABLE user_sessions ADD INDEX idx_login_at (login_at);
ALTER TABLE user_sessions ADD INDEX idx_logout_at (logout_at);


-- vista previa reportes
ALTER TABLE reportes ADD COLUMN VistaPrevia VARCHAR(200) DEFAULT NULL;


UPDATE reportes SET VistaPrevia = NULL WHERE Id = 0;
UPDATE reportes SET VistaPrevia = 'E1.jpg' WHERE Id = 1;
UPDATE reportes SET VistaPrevia = 'E2.jpg' WHERE Id = 2;
UPDATE reportes SET VistaPrevia = 'E3.jpg' WHERE Id = 3;
UPDATE reportes SET VistaPrevia = 'E4.jpg' WHERE Id = 4;
UPDATE reportes SET VistaPrevia = 'E5.jpg' WHERE Id = 5;
UPDATE reportes SET VistaPrevia = 'E6.jpg' WHERE Id = 6;
UPDATE reportes SET VistaPrevia = 'E10.jpg' WHERE Id = 7;
UPDATE reportes SET VistaPrevia = 'E8.jpg' WHERE Id = 8;
UPDATE reportes SET VistaPrevia = 'E6.jpg' WHERE Id = 9;
UPDATE reportes SET VistaPrevia = 'E10.jpg' WHERE Id = 10;
UPDATE reportes SET VistaPrevia = 'E11.jpg' WHERE Id = 11;
UPDATE reportes SET VistaPrevia = 'E5.jpg' WHERE Id = 12;
UPDATE reportes SET VistaPrevia = 'E13.jpg' WHERE Id = 13;
UPDATE reportes SET VistaPrevia = 'E14.jpg' WHERE Id = 14;
UPDATE reportes SET VistaPrevia = 'E15.jpg' WHERE Id = 15;
UPDATE reportes SET VistaPrevia = 'E16.jpg' WHERE Id = 16;
UPDATE reportes SET VistaPrevia = 'E17.jpg' WHERE Id = 17;
UPDATE reportes SET VistaPrevia = 'E18.jpg' WHERE Id = 18;
UPDATE reportes SET VistaPrevia = 'E6.jpg' WHERE Id = 19;
UPDATE reportes SET VistaPrevia = 'E20.jpg' WHERE Id = 20;
UPDATE reportes SET VistaPrevia = 'E10.jpg' WHERE Id = 21;
UPDATE reportes SET VistaPrevia = 'E22.jpg' WHERE Id = 22;
UPDATE reportes SET VistaPrevia = 'E23.jpg' WHERE Id = 23;
UPDATE reportes SET VistaPrevia = 'E24.jpg' WHERE Id = 24;
UPDATE reportes SET VistaPrevia = 'E25.jpg' WHERE Id = 25;
UPDATE reportes SET VistaPrevia = 'E25.jpg' WHERE Id = 26;
UPDATE reportes SET VistaPrevia = 'E25.jpg' WHERE Id = 27;
UPDATE reportes SET VistaPrevia = 'E28.jpg' WHERE Id = 28;
UPDATE reportes SET VistaPrevia = 'E29.jpg' WHERE Id = 29;
UPDATE reportes SET VistaPrevia = 'E30.jpg' WHERE Id = 30;
UPDATE reportes SET VistaPrevia = 'E31.jpg' WHERE Id = 31;
UPDATE reportes SET VistaPrevia = 'E31.jpg' WHERE Id = 32;
UPDATE reportes SET VistaPrevia = 'E33.jpg' WHERE Id = 33;
UPDATE reportes SET VistaPrevia = 'E34.jpg' WHERE Id = 34;
UPDATE reportes SET VistaPrevia = 'E35.jpg' WHERE Id = 35;
UPDATE reportes SET VistaPrevia = 'E36.jpg' WHERE Id = 36;
UPDATE reportes SET VistaPrevia = 'E37.jpg' WHERE Id = 37;
UPDATE reportes SET VistaPrevia = 'E38.jpg' WHERE Id = 38;
UPDATE reportes SET VistaPrevia = 'E39.jpg' WHERE Id = 39;
UPDATE reportes SET VistaPrevia = 'E40.jpg' WHERE Id = 40;
UPDATE reportes SET VistaPrevia = 'E41.jpg' WHERE Id = 41;
UPDATE reportes SET VistaPrevia = 'E42.jpg' WHERE Id = 42;
UPDATE reportes SET VistaPrevia = 'E43.jpg' WHERE Id = 44;
UPDATE reportes SET VistaPrevia = 'E45.jpg' WHERE Id = 45;
UPDATE reportes SET VistaPrevia = 'E46.jpg' WHERE Id = 46;
UPDATE reportes SET VistaPrevia = 'E47_1.jpg' WHERE Id = 47;
UPDATE reportes SET VistaPrevia = 'E48_1.jpg' WHERE Id = 48;
UPDATE reportes SET VistaPrevia = 'E49_1.jpg' WHERE Id = 49;
UPDATE reportes SET VistaPrevia = 'E50_1.jpg' WHERE Id = 50;
UPDATE reportes SET VistaPrevia = 'E51_1.jpg' WHERE Id = 51;
UPDATE reportes SET VistaPrevia = 'E52_1.jpg' WHERE Id = 52;
UPDATE reportes SET VistaPrevia = 'E53_1.jpg' WHERE Id = 53;
UPDATE reportes SET VistaPrevia = 'E54_1.jpg' WHERE Id = 54;
UPDATE reportes SET VistaPrevia = 'E55_1.jpg' WHERE Id = 55;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 56;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 57;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 58;
UPDATE reportes SET VistaPrevia = 'E59_1.jpg' WHERE Id = 59;
UPDATE reportes SET VistaPrevia = 'E60_1.jpg' WHERE Id = 60;
UPDATE reportes SET VistaPrevia = 'E61_1.jpg' WHERE Id = 61;
UPDATE reportes SET VistaPrevia = 'E62_1.jpg' WHERE Id = 62;
UPDATE reportes SET VistaPrevia = 'E63_1.jpg' WHERE Id = 63;
UPDATE reportes SET VistaPrevia = 'E64_3.jpg' WHERE Id = 64;
UPDATE reportes SET VistaPrevia = 'E65_1.jpg' WHERE Id = 65;
UPDATE reportes SET VistaPrevia = 'E66_1.jpg' WHERE Id = 66;
UPDATE reportes SET VistaPrevia = 'E67.jpg' WHERE Id = 67;
UPDATE reportes SET VistaPrevia = 'E68.jpg' WHERE Id = 68;
UPDATE reportes SET VistaPrevia = 'E69_1.jpg' WHERE Id = 69;
UPDATE reportes SET VistaPrevia = 'E70.jpg' WHERE Id = 70;
UPDATE reportes SET VistaPrevia = 'E71.jpg' WHERE Id = 71;
UPDATE reportes SET VistaPrevia = 'E72_1.jpg' WHERE Id = 72;
UPDATE reportes SET VistaPrevia = 'E72_1.jpg' WHERE Id = 73;
UPDATE reportes SET VistaPrevia = 'E74_1.jpg' WHERE Id = 74;
UPDATE reportes SET VistaPrevia = 'E75_1.jpg' WHERE Id = 75;
UPDATE reportes SET VistaPrevia = 'E76_1.jpg' WHERE Id = 76;
UPDATE reportes SET VistaPrevia = 'E77_1.jpg' WHERE Id = 77;
UPDATE reportes SET VistaPrevia = 'E78_1.jpg' WHERE Id = 78;
UPDATE reportes SET VistaPrevia = 'E79_1.jpg' WHERE Id = 79;
UPDATE reportes SET VistaPrevia = 'E80_1.jpg' WHERE Id = 80;
UPDATE reportes SET VistaPrevia = 'E81_1.jpg' WHERE Id = 81;
UPDATE reportes SET VistaPrevia = 'E82_1.jpg' WHERE Id = 82;
UPDATE reportes SET VistaPrevia = 'E83_1.jpg' WHERE Id = 83;
UPDATE reportes SET VistaPrevia = 'E84_1.jpg' WHERE Id = 84;
UPDATE reportes SET VistaPrevia = 'E85_1.jpg' WHERE Id = 85;
UPDATE reportes SET VistaPrevia = 'E86_1.jpg' WHERE Id = 86;
UPDATE reportes SET VistaPrevia = 'E87_1.jpg' WHERE Id = 87;
UPDATE reportes SET VistaPrevia = 'E88_1.jpg' WHERE Id = 88;
UPDATE reportes SET VistaPrevia = 'E89_1.jpg' WHERE Id = 89;
UPDATE reportes SET VistaPrevia = 'E90_1.jpg' WHERE Id = 90;
UPDATE reportes SET VistaPrevia = 'E91_1.jpg' WHERE Id = 91;
UPDATE reportes SET VistaPrevia = 'E92_1.jpg' WHERE Id = 92;
UPDATE reportes SET VistaPrevia = 'E93_1.jpg' WHERE Id = 93;
UPDATE reportes SET VistaPrevia = 'E94_1.jpg' WHERE Id = 94;
UPDATE reportes SET VistaPrevia = 'E95_1.jpg' WHERE Id = 95;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 96;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 97;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 98;
UPDATE reportes SET VistaPrevia = 'E99_1.jpg' WHERE Id = 99;
UPDATE reportes SET VistaPrevia = 'E100_1.jpg' WHERE Id = 100;
UPDATE reportes SET VistaPrevia = 'E101_1.jpg' WHERE Id = 101;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 103;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 104;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 105;
UPDATE reportes SET VistaPrevia = 'E106_1.jpg' WHERE Id = 106;
UPDATE reportes SET VistaPrevia = 'E102_1.jpg' WHERE Id = 107;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 108;
UPDATE reportes SET VistaPrevia = 'E109_1.jpg' WHERE Id = 109;
UPDATE reportes SET VistaPrevia = 'E109_1.jpg' WHERE Id = 111;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 112;
UPDATE reportes SET VistaPrevia = 'E113_1.jpg' WHERE Id = 113;
UPDATE reportes SET VistaPrevia = 'E114_1.jpg' WHERE Id = 114;
UPDATE reportes SET VistaPrevia = 'E115_1.jpg' WHERE Id = 115;
UPDATE reportes SET VistaPrevia = 'E116_1.jpg' WHERE Id = 116;
UPDATE reportes SET VistaPrevia = 'E117_1.jpg' WHERE Id = 117;
UPDATE reportes SET VistaPrevia = 'E118_1.jpg' WHERE Id = 118;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 119;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 120;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 121;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 122;
UPDATE reportes SET VistaPrevia = 'E123_1.jpg' WHERE Id = 123;
UPDATE reportes SET VistaPrevia = 'E124_1.jpg' WHERE Id = 124;
UPDATE reportes SET VistaPrevia = 'E125_1.jpg' WHERE Id = 125;
UPDATE reportes SET VistaPrevia = 'E126_1.jpg' WHERE Id = 126;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 127;
UPDATE reportes SET VistaPrevia = 'E128_1.jpg' WHERE Id = 128;
UPDATE reportes SET VistaPrevia = 'E129_1.jpg' WHERE Id = 129;
UPDATE reportes SET VistaPrevia = 'E130_1.jpg' WHERE Id = 130;
UPDATE reportes SET VistaPrevia = 'E131_1.jpg' WHERE Id = 131;
UPDATE reportes SET VistaPrevia = 'E132_1.jpg' WHERE Id = 132;
UPDATE reportes SET VistaPrevia = 'E133_1.jpg' WHERE Id = 133;
UPDATE reportes SET VistaPrevia = 'E134_1.jpg' WHERE Id = 134;
UPDATE reportes SET VistaPrevia = 'E135_1.jpg' WHERE Id = 135;
UPDATE reportes SET VistaPrevia = 'E136_1.jpg' WHERE Id = 136;
UPDATE reportes SET VistaPrevia = 'E137_1.jpg' WHERE Id = 137;
UPDATE reportes SET VistaPrevia = 'E138_1.jpg' WHERE Id = 138;
UPDATE reportes SET VistaPrevia = 'E139_1.jpg' WHERE Id = 139;
UPDATE reportes SET VistaPrevia = 'E140_1.jpg' WHERE Id = 140;
UPDATE reportes SET VistaPrevia = 'E141.jpg' WHERE Id = 141;
UPDATE reportes SET VistaPrevia = 'E144_1.jpg' WHERE Id = 142;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 143;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 144;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 145;
UPDATE reportes SET VistaPrevia = 'E146_1.jpg' WHERE Id = 146;
UPDATE reportes SET VistaPrevia = 'E146_1.jpg' WHERE Id = 147;
UPDATE reportes SET VistaPrevia = 'E148_1.jpg' WHERE Id = 148;
UPDATE reportes SET VistaPrevia = 'E149_1.jpg' WHERE Id = 149;
UPDATE reportes SET VistaPrevia = 'E150_1.jpg' WHERE Id = 150;
UPDATE reportes SET VistaPrevia = 'E151_1.jpg' WHERE Id = 151;
UPDATE reportes SET VistaPrevia = 'E152_1.jpg' WHERE Id = 152;
UPDATE reportes SET VistaPrevia = 'E153_1.jpg' WHERE Id = 153;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 154;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 155;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 156;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 157;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 158;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 159;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 160;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 161;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 162;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 163;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 164;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 165;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 166;
UPDATE reportes SET VistaPrevia = NULL WHERE Id = 167;

CREATE TABLE fichaprestacion_factura(
	id INT AUTO_INCREMENT NOT NULL,
	prestacion_id INT,
	fichalaboral_id INT,
	Tipo CHAR(1),
	Sucursal INT,
	NroFactura INT,
	NroFactProv TEXT,
	UNIQUE(id) 
);

ALTER TABLE fichaslaborales DROP COLUMN Tipo, DROP COLUMN Sucursal, DROP COLUMN NroFactura, DROP COLUMN NroFactProv;

ALTER TABLE prestaciones DROP COLUMN NroFactProv;

ALTER TABLE fichaslaborales ADD COLUMN datos_facturacion_id INT NULL;
ALTER TABLE prestaciones ADD COLUMN datos_facturacion_id INT NULL;

ALTER TABLE prestaciones ADD CONSTRAINT fk_prestacion_datos_facturacion FOREIGN KEY (datos_facturacion_id) REFERENCES fichaprestacion_factura(id);
ALTER TABLE fichaslaborales ADD CONSTRAINT fk_fichalaboral_datos_facturacion FOREIGN KEY(datos_facturacion_id) REFERENCES fichaprestacion_factura(id);

DELIMITER //

CREATE TRIGGER autocomplete_fichaslaborales
BEFORE INSERT ON fichaslaborales
FOR EACH ROW
BEGIN
    IF NEW.Jornada IS NULL OR NEW.Jornada = '' THEN
        SET NEW.Jornada = '';
    END IF;

    IF NEW.Observaciones IS NULL OR NEW.Observaciones = '' THEN
        SET NEW.Observaciones = '';
    END IF;

    IF NEW.TareasEmpAnterior IS NULL OR NEW.TareasEmpAnterior = '' THEN
        SET NEW.TareasEmpAnterior = '';
    END IF;

    IF NEW.Puesto IS NULL OR NEW.Puesto = '' THEN
        SET NEW.Puesto = '';
    END IF;
END;
//

DELIMITER ;

ALTER TABLE facturasdeventa ADD CONSTRAINT fk_facturadeventa_prestacion FOREIGN KEY(IdPrestacion) REFERENCES prestaciones(Id);
ALTER TABLE fichaprestacion_factura MODIFY COLUMN NroFactProv TEXT DEFAULT NULL NULL;




ALTER TABLE reportes ADD COLUMN VistaPrevia VARCHAR(200) DEFAULT NULL;


-- alias al paquete estudios
ALTER TABLE paqestudios ADD COLUMN Alias VARCHAR(200) DEFAULT NULL;		
ALTER TABLE paqestudios ADD COLUMN Baja BIT DEFAULT 0;		

ALTER TABLE relpaqest ADD COLUMN Baja BIT DEFAULT 0;

-- rel tabla grupocliente - cliente
ALTER TABLE clientesgrupos_it ADD COLUMN Baja BIT DEFAULT 0;		

ALTER TABLE clientesgrupos ADD COLUMN Baja BIT DEFAULT 0;

-- modificamos la tabla de paquetes de facturacion

ALTER TABLE paqfacturacion ADD COLUMN Alias VARCHAR(200) DEFAULT NULL;	
ALTER TABLE paqfacturacion ADD COLUMN Baja BIT DEFAULT 0;

-- modificamos la tabla de la relacion entre paquetes de facturacion y los estudios
ALTER TABLE relpaqfact ADD COLUMN Baja BIT DEFAULT 0;


DROP PROCEDURE IF EXISTS db_cmit.getExamenesPaqueteFac;

DELIMITER $$
$$
CREATE PROCEDURE db_cmit.getExamenesPaqueteFac()
BEGIN
	SELECT e.Id FROM relpaqfact r
	INNER JOIN examenes e ON r.IdExamen = e.Id 
	WHERE r.IdPaquete = IdPaquete AND r.Baja = 0;
END$$
DELIMITER ;

ALTER TABLE clientesgrupos_it DROP INDEX IdCliente_2;

ALTER TABLE itemsprestaciones MODIFY COLUMN IdProfesional2 INT NOT NULL DEFAULT 0;

-- CREATE DEFINER=`db_cmit`@`%` TRIGGER autocomplete_itemsprestaciones
-- BEFORE INSERT ON itemsprestaciones
-- FOR EACH ROW
-- BEGIN
--     IF NEW.IdProfesional2 IS NULL OR NEW.IdProfesional2 = '' THEN
--         SET NEW.IdProfesional2 = 0;
--     END IF;
-- END

CREATE PROCEDURE getExamenes(IN id_prestacion INT, IN tipo VARCHAR(10))
BEGIN
	SELECT 
	    examenes.Nombre AS Nombre,
	    examenes.Id AS IdExamen,
	    examenes.Adjunto AS ExaAdj,
	    examenes.Informe AS Informe,
	    informador.InfAdj AS InfAdj,
	    examenes.NoImprime AS ExaNI,
	    CONCAT(efector.Apellido,' ', efector.Nombre) AS EfectorFullName,
	    CONCAT(informador.Apellido,' ', informador.Nombre) AS InformadorFullName,
	    CONCAT(datosEfector.Apellido,' ', datosEfector.Nombre) AS DatosEfectorFullName,
	    CONCAT(datosInformador.Apellido,' ', datosInformador.Nombre) AS DatosInformadorFullName,
	    efector.Apellido AS EfectorApellido,
	    informador.Apellido AS InformadorApellido,
	    datosEfector.Apellido AS DatosEfectorApellido,
	    datosInformador.Apellido AS DatosInformadorApellido,
	    efector.RegHis AS RegHis,
	    itemsprestaciones.Ausente AS Ausente,
	    itemsprestaciones.Forma AS Forma,
	    itemsprestaciones.Incompleto AS Incompleto,
	    itemsprestaciones.SinEsc AS SinEsc,
	    itemsprestaciones.Devol AS Devol,
	    itemsprestaciones.CAdj AS CAdj,
	    itemsprestaciones.CInfo AS CInfo,
	    itemsprestaciones.Id AS IdItem,
	    itemsprestaciones.Anulado AS Anulado,
	    (SELECT COUNT(*) FROM archivosefector WHERE IdEntidad = itemsprestaciones.Id) AS archivos,
	    (SELECT COUNT(*) FROM archivosinformador WHERE IdEntidad = itemsprestaciones.Id) AS archivosI,
	    efector.Id AS IdEfector,
	    informador.Id AS IdInformador,
	    userEfector.id AS IdUserEfector,
	    userInformador.id AS IdUserInformador
	FROM itemsprestaciones
	LEFT JOIN profesionales AS efector ON itemsprestaciones.IdProfesional = efector.Id
	LEFT JOIN users AS userEfector ON efector.Id = userEfector.profesional_id
	LEFT JOIN datos AS datosEfector ON userEfector.datos_id = datosEfector.Id
	LEFT JOIN profesionales AS informador ON itemsprestaciones.IdProfesional2 = informador.Id
	LEFT JOIN users AS userInformador ON informador.Id = userInformador.profesional_id
	LEFT JOIN datos AS datosInformador ON userInformador.datos_id = datosInformador.Id
	JOIN examenes ON itemsprestaciones.IdExamen = examenes.Id
	JOIN proveedores AS proveedor2 ON examenes.IdProveedor = proveedor2.Id
	JOIN prestaciones ON itemsprestaciones.IdPrestacion = prestaciones.Id
	LEFT JOIN archivosefector ON itemsprestaciones.Id = archivosefector.IdEntidad
	LEFT JOIN archivosinformador ON itemsprestaciones.Id = archivosinformador.IdEntidad
	WHERE 1=1
	AND (
	    (tipo != 'listado') OR 
	    (tipo = 'listado' AND itemsprestaciones.IdPrestacion = id_prestacion)
	)
	GROUP BY itemsprestaciones.Id
	ORDER BY 
	    efector.IdProveedor ASC,
	    examenes.Nombre ASC,
	    itemsprestaciones.Fecha ASC;
END

ALTER TABLE itemsfacturacompra DROP FOREIGN KEY itemsfacturacompra_ibfk_2; --IdItemPrestacion
ALTER TABLE itemsfacturacompra2 DROP FOREIGN KEY itemsfacturacompra2_ibfk_2; --IdItemPrestacion
ALTER TABLE itemsprestaciones_info DROP FOREIGN KEY itemsprestaciones_info_ibfk_1; --IdIP
ALTER TABLE notascredito_it DROP FOREIGN KEY notascredito_it_ibfk_3; --IdIP
ALTER TABLE archivosinformador DROP FOREIGN KEY archivosinformador_ibfk_1; --IdEntidad
ALTER TABLE archivosefector DROP FOREIGN KEY archivosefector_ibfk_1; --IdEntidad
ALTER TABLE itemsprestaciones MODIFY COLUMN Id int(11) auto_increment NOT NULL;

ALTER TABLE itemsfacturacompra ADD CONSTRAINT itemsfacturacompra_ibfk_2 FOREIGN KEY (IdItemPrestacion) REFERENCES itemsprestaciones(Id);
ALTER TABLE itemsfacturacompra2 ADD CONSTRAINT itemsfacturacompra2_ibfk_2 FOREIGN KEY (IdItemPrestacion) REFERENCES itemsprestaciones(Id);
ALTER TABLE itemsprestaciones_info ADD CONSTRAINT itemsprestaciones_info_ibfk_1 FOREIGN KEY (IdIP) REFERENCES itemsprestaciones(Id);
ALTER TABLE notascredito_it ADD CONSTRAINT notascredito_it_ibfk_3 FOREIGN KEY (IdIP) REFERENCES itemsprestaciones(Id);
ALTER TABLE archivosinformador ADD CONSTRAINT archivosinformador_ibfk_1 FOREIGN KEY (IdEntidad) REFERENCES itemsprestaciones(Id);
ALTER TABLE archivosefector ADD CONSTRAINT archivosefector_ibfk_1 FOREIGN KEY (IdEntidad) REFERENCES itemsprestaciones(Id);

CREATE PROCEDURE getExamenesEstandar(IN id_prestacion INT, IN tipo VARCHAR(10))
BEGIN
	SELECT 
	    examenes.Nombre AS Nombre,
	    examenes.Id AS IdExamen,
	    itemsprestaciones.Id AS IdItem,
	    itemsprestaciones.Anulado AS Anulado
	FROM itemsprestaciones
	JOIN examenes ON itemsprestaciones.IdExamen = examenes.Id
	WHERE (
	    (tipo != 'listado') OR 
	    (tipo = 'listado' AND itemsprestaciones.IdPrestacion = id_prestacion)
	)
	GROUP BY itemsprestaciones.Id
	ORDER BY 
	    examenes.Nombre ASC,
	    itemsprestaciones.Fecha ASC;
END

ALTER TABLE autorizados DROP FOREIGN KEY autorizados_ibfk_1; --IdEntidad
ALTER TABLE clientesgrupos_it DROP FOREIGN KEY clientesgrupos_it_ibfk_2; --IdCliente
ALTER TABLE facturasventa DROP FOREIGN KEY facturasventa_ibfk_1; --IdEmpresa
ALTER TABLE fichaslaborales DROP FOREIGN KEY fichaslaborales_ibfk_2; --IdEmpresa
ALTER TABLE fichaslaborales DROP FOREIGN KEY fichaslaborales_ibfk_3; --IdART
ALTER TABLE hc_casos DROP FOREIGN KEY hc_casos_ibfk_2; --IdEmpresa
ALTER TABLE hc_casos DROP FOREIGN KEY hc_casos_ibfk_3; --IdART
ALTER TABLE mapas DROP FOREIGN KEY mapas_ibfk_1; --IdART
ALTER TABLE mapas DROP FOREIGN KEY mapas_ibfk_2; --IdEmpresa
ALTER TABLE notascredito DROP FOREIGN KEY notascredito_ibfk_1; --IdEmpresa
ALTER TABLE pagosacuenta DROP FOREIGN KEY pagosacuenta_ibfk_1;  --IdEmpresa
ALTER TABLE paqfacturacion DROP FOREIGN KEY paqfacturacion_ibfk_1;  --IdEmpresa
ALTER TABLE parametros DROP FOREIGN KEY parametros_ibfk_3; --IdCliCarnet
ALTER TABLE prestaciones DROP FOREIGN KEY prestaciones_ibfk_2; --IdEmpresa
ALTER TABLE prestaciones DROP FOREIGN KEY prestaciones_ibfk_3; --IdART

SHOW TABLE STATUS LIKE 'clientes';
ALTER TABLE clientes ENGINE=InnoDB;

CREATE TABLE cliente_fila_0_temp AS SELECT * FROM clientes WHERE Id = 0;
DELETE FROM clientes WHERE Id = 0;
ALTER TABLE clientes MODIFY COLUMN Id int(11) NOT NULL AUTO_INCREMENT;


INSERT INTO clientes (
    RazonSocial, Nacionalidad, CondicionIva, TipoIdentificacion, Identificacion, 
    Observaciones, TipoPersona, Envio, Entrega, ParaEmpresa, IdActividad, 
    NombreFantasia, Logo, Bloqueado, Motivo, Direccion, IdLocalidad, Provincia, 
    CP, EMail, ObsEMail, EMailResultados, Telefono, LogoCertificado, Oreste, 
    TipoCliente, FPago, ObsEval, ObsCE, Generico, SEMail, ObsCO, EMailFactura, 
    EnvioFactura, EMailInformes, EnvioInforme, Ajuste, SinPF, SinEval, RF, 
    Estado, Anexo, EMailAnexo, Descuento
)
SELECT 
    RazonSocial, Nacionalidad, CondicionIva, TipoIdentificacion, Identificacion, 
    Observaciones, TipoPersona, Envio, Entrega, ParaEmpresa, IdActividad, 
    NombreFantasia, Logo, Bloqueado, Motivo, Direccion, IdLocalidad, Provincia, 
    CP, EMail, ObsEMail, EMailResultados, Telefono, LogoCertificado, Oreste, 
    TipoCliente, FPago, ObsEval, ObsCE, Generico, SEMail, ObsCO, EMailFactura, 
    EnvioFactura, EMailInformes, EnvioInforme, Ajuste, SinPF, SinEval, RF, 
    Estado, Anexo, EMailAnexo, Descuento
FROM cliente_fila_0_temp;

DROP TABLE cliente_fila_0_temp;

ALTER TABLE autorizados ADD CONSTRAINT autorizados_ibfk_1 FOREIGN KEY (IdEntidad) REFERENCES clientes(Id);
ALTER TABLE clientesgrupos_it ADD CONSTRAINT clientesgrupos_it_ibfk_2 FOREIGN KEY (IdCliente) REFERENCES clientes(Id);
ALTER TABLE facturasventa ADD CONSTRAINT facturasventa_ibfk_1 FOREIGN KEY (IdEmpresa) REFERENCES clientes(Id);
ALTER TABLE fichaslaborales ADD CONSTRAINT fichaslaborales_ibfk_2 FOREIGN KEY (IdEmpresa) REFERENCES clientes(Id);
ALTER TABLE fichaslaborales ADD CONSTRAINT fichaslaborales_ibfk_3 FOREIGN KEY (IdART) REFERENCES clientes(Id);
ALTER TABLE hc_casos ADD CONSTRAINT hc_casos_ibfk_2 FOREIGN KEY (IdEmpresa) REFERENCES clientes(Id);
ALTER TABLE hc_casos ADD CONSTRAINT hc_casos_ibfk_3 FOREIGN KEY (IdART) REFERENCES clientes(Id);
ALTER TABLE mapas ADD CONSTRAINT mapas_ibfk_1 FOREIGN KEY (IdART) REFERENCES clientes(Id);
ALTER TABLE mapas ADD CONSTRAINT mapas_ibfk_2 FOREIGN KEY (IdEmpresa) REFERENCES clientes(Id);
ALTER TABLE notascredito ADD CONSTRAINT notascredito_ibfk_1 FOREIGN KEY (IdEmpresa) REFERENCES clientes(Id);
ALTER TABLE pagosacuenta ADD CONSTRAINT pagosacuenta_ibfk_1 FOREIGN KEY (IdEmpresa) REFERENCES clientes(Id);
ALTER TABLE paqfacturacion ADD CONSTRAINT paqfacturacion_ibfk_1 FOREIGN KEY (IdEmpresa) REFERENCES clientes(Id);
ALTER TABLE parametros ADD CONSTRAINT parametros_ibfk_3 FOREIGN KEY (IdCliCarnet) REFERENCES clientes(Id);
ALTER TABLE prestaciones ADD CONSTRAINT prestaciones_ibfk_2 FOREIGN KEY (IdEmpresa) REFERENCES clientes(Id);
ALTER TABLE prestaciones ADD CONSTRAINT prestaciones_ibfk_3 FOREIGN KEY (IdART) REFERENCES clientes(Id);

DELIMITER $$
CREATE TRIGGER autocomplete_clientes
BEFORE INSERT ON clientes
FOR EACH ROW
BEGIN
    IF NEW.EMail IS NULL OR NEW.EMail = '' THEN
        SET NEW.EMail = '';
    END IF;

	IF NEW.Telefono IS NULL OR NEW.Telefono = '' THEN
        SET NEW.Telefono = '';
    END IF;

	IF NEW.ObsEMail IS NULL OR NEW.ObsEMail = '' THEN
        SET NEW.ObsEMail = '';
    END IF;

	IF NEW.Direccion IS NULL OR NEW.Direccion = '' THEN
        SET NEW.Direccion = '';
    END IF;
END$$
DELIMITER;

INSERT INTO rol_permisos (rol_id, permiso_id) VALUES(13, 119); -- Permiso para administrador faltante en profesionalesEdit
ALTER TABLE permisos MODIFY COLUMN descripcion LONGTEXT DEFAULT NULL NULL;
ALTER TABLE roles MODIFY COLUMN descripcion LONGTEXT DEFAULT NULL NULL;


-- indice unico de nombre, ya que se tiene que validar cuando tambien esta dado de baja

ALTER TABLE paqfacturacion DROP INDEX Nombre;
ALTER TABLE paqestudios DROP INDEX Nombre;

-- indice unico de nombre en grupos clientes
ALTER TABLE clientesgrupos DROP INDEX Nombre;

-- damos de baja estas dos primeras filas de las tablas de paquetes
UPDATE paqestudios p SET p.Baja = 1 WHERE p.Id  = 0;
UPDATE paqfacturacion f SET f.Baja = 1 WHERE f.Id = 0; 
