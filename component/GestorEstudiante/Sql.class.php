<?php

namespace sniesEstudiante;

if (!isset($GLOBALS["autorizado"])) {
	include ("../index.php");
	exit();
}

include_once ("core/manager/Configurador.class.php");
include_once ("core/connection/Sql.class.php");

// Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
// en camel case precedida por la palabra sql
class Sql extends \Sql {
	var $miConfigurador;
	function __construct() {
		$this -> miConfigurador = \Configurador::singleton();
	}

	function cadena_sql($tipo, $variable = "") {

		/**
		 * 1.
		 * Revisar las variables para evitar SQL Injection
		 */
		$prefijo = 'sniesud_';
		$idSesion = $this -> miConfigurador -> getVariableConfiguracion("id_sesion");

		switch ($tipo) {

			/**
			 * CONSULTA BASE DE DATOS INSTITUCIONAL
			 * Datos necesarios para la tabla participante de estudiantes
			 * se consulta los estudiantes que pagaron matrícula en un período
			 */

			/**
			 * Consulta que rescata los datos de la base de datos institucional
			 * para poblar las tablas del SNIES relacionadas con estudiantes:
			 * PARTICIPANTE
			 * ESTUDIANTE
			 * ESTUDIANTE_PROGRAMA - primer semestre
			 * MATRICULADO
			 */
			case "consultarEstudianteAcademica" :
				$cadenaSql = " SELECT ";
				$cadenaSql .= " EST_COD CODIGO_ESTUDIANTE,";
				$cadenaSql .= " TO_CHAR(DECODE(est_tipo_iden_ant,'C', 'CC', 'T', 'TI', 'E', 'CE', 'P', 'PS')) id_tipo_documento,";
				$cadenaSql .= " TO_CHAR(est_nro_iden) num_documento,";
				$cadenaSql .= " '' fecha_expedicion,";
				$cadenaSql .= " EST_NOMBRE,";
				$cadenaSql .= " TO_CHAR(DECODE(est_sexo,'M','1','F','2','1')) id_sexo_biologico,";
				$cadenaSql .= " DECODE(eot_estado_civil,1,'1',2,'2',3,'5',4,'3',5,'4', '1') id_estado_civil,";
				$cadenaSql .= " TO_CHAR(eot_fecha_nac, 'DD/MM/YYYY') fecha_nacimiento,";
				$cadenaSql .= " '170' id_pais_nacimiento,";
				$cadenaSql .= " TO_CHAR(DECODE (eot_cod_mun_nac,0,11001,'',11001,NULL, 11001,99999,11001, eot_cod_mun_nac)) id_municipio_nacimiento,";
				$cadenaSql .= " est_telefono telefono_contacto,";
				$cadenaSql .= " eot_email email_personal,";
				$cadenaSql .= " eot_email_ins email_institucional,";
				$cadenaSql .= " '' direccion_institucional,";
				//obligatorio para autoridades
				$cadenaSql .= " as_cra_cod_snies pro_consecutivo,";
				$cadenaSql .= " '11001' id_municipio_programa,";
				$cadenaSql .= " DECODE(LENGTH(est_cod),7,(SUBSTR(est_cod,1,2)+1900),11,(SUBSTR(est_cod, 1,4))) anio,";
				$cadenaSql .= " DECODE(DECODE(LENGTH(est_cod),7,((SUBSTR(est_cod,3,1))),11,(SUBSTR(est_cod, 5,1))), '1','01','02') semestre,";
				$cadenaSql .= " '1' id_tipo_vinculacion,";
				$cadenaSql .= " '0' id_grupo_etnico,";
				//$cadenaSql .= " DECODE (eot_grupo_etnico, '200', '2', '400', '3', NULL, '0', '999', '4', '4') id_grupo_etnico,";
				$cadenaSql .= " '0' id_pueblo_indigena,";
				$cadenaSql .= " '0' id_comunidad_negra,";
				$cadenaSql .= " '0' persona_condicion_discapacidad,";
				$cadenaSql .= " '0' id_tipo_discapacidad,";
				$cadenaSql .= " '0' id_capacidad_excepcional,";
				$cadenaSql .= " eot_nro_snp cod_prueba_saber_11,";
				$cadenaSql .= " DECODE (eot_arural, 'S', '2', 'N', '1', '1') id_zona_residencia,";
				// S es rural (2) N es urbano (1), default urbano (1)
				$cadenaSql .= " 'N' es_reintegro";
				//$cadenaSql .= " TO_CHAR(DECODE (mun_dep_cod,0,11,'',11, mun_dep_cod)) departamento_ln,";
				//$cadenaSql .= " '02' es_transferencia,";
				// $cadenaSql .= " --datos matriculado";
				//$cadenaSql .= " DECODE(cra_jornada, 'DIURNA', '01', 'NOCTURNA', '02', '01' ) horario_code,";
				//$cadenaSql .= " '01' pago";
				$cadenaSql .= " FROM mntac.acest";
				$cadenaSql .= " INNER JOIN mntac.acestotr";
				$cadenaSql .= " ON est_cod = eot_cod";
				$cadenaSql .= " INNER JOIN accra_snies";
				$cadenaSql .= " ON as_cra_cod=est_cra_cod";
				$cadenaSql .= " INNER JOIN mntge.gemunicipio";
				$cadenaSql .= " ON MUN_COD=DECODE(EOT_COD_MUN_NAC,0,11001,'',11001,EOT_COD_MUN_NAC)";
				$cadenaSql .= " INNER JOIN mntac.v_tot_matri_ape_per";
				$cadenaSql .= " ON est_cod = mat_est_cod";
				$cadenaSql .= " INNER JOIN accra";
				$cadenaSql .= " ON cra_cod = est_cra_cod";
				$cadenaSql .= " INNER JOIN actipcra";
				$cadenaSql .= " ON cra_tip_cra = tra_cod";
				$cadenaSql .= " WHERE mat_ano ='" . $variable['annio'] . "'";
				if ($variable['semestre'] == '01') {
					$cadenaSql .= " AND mat_per =1 ";
				} else {
					$cadenaSql .= " AND mat_per =3 ";
					// el semestre 03 de la universidad es el semestre 02 de SNIES";
				}

				//$cadenaSql .= " AND est_cod=20021001083";
				//$cadenaSql .= " AND est_nro_iden=80071376";				
				//$cadenaSql .= " AND rownum < 10";

				break;

			// consulta para la Base Poblacional Unificada del Distrito Capital,
			// En los casos que el valor es null se coloca el simbolo "|" para que se cree la variable
			// para procesarla más adelante
			case "consultarEstudianteBpudc" :
				$cadenaSql = " SELECT";
				$cadenaSql .= " EST_COD ID,";
				$cadenaSql .= " EST_NOMBRE NOMBRE,";
				// NOMBRE_1, NOMBRE_2, APELLIDO_1, APELLIDO_2
				$cadenaSql .= " EST_TIPO_IDEN TIP_ID,";
				$cadenaSql .= " EST_NRO_IDEN NUM_ID,";
				$cadenaSql .= " EOT_COD_LUG_NAC MUN_NAC,";
				$cadenaSql .= " 'CO' PAIS_NAC,";
				$cadenaSql .= " '' FEC_ID,";
				$cadenaSql .= " EOT_SEXO SEXO,";
				$cadenaSql .= " TO_CHAR(to_date(EOT_FECHA_NAC),'DD/MM/YYYY') FEC_NAC,";
				$cadenaSql .= " EOT_TIPOSANGRE GRU_SANG,";
				$cadenaSql .= " EOT_RH FACT_RH,";
				$cadenaSql .= " EOT_GRUPO_ETNICO ETNIA,";
				$cadenaSql .= " '' CUAL_ETNIA,";
				$cadenaSql .= " '' GENERO,";
				$cadenaSql .= " '' CUAL_GENERO,";
				$cadenaSql .= " '' NOM_IDENTITARIO,";
				$cadenaSql .= " '' ORIENT_SEX,";
				$cadenaSql .= " '' CUAL_ORIENT_SEX,";
				$cadenaSql .= " '' OCUPACION,";
				$cadenaSql .= " '' CUAL_OCUPACION,";
				$cadenaSql .= " '' COND_HABITACION,";
				$cadenaSql .= " '' TIPO_ATEN_POB_INFANTIL,";
				$cadenaSql .= " '' OCUP_ESPECIAL,";
				$cadenaSql .= " '' COND_ESPECIAL,";
				$cadenaSql .= " '' CARA_ESPE_PADRES,";
				$cadenaSql .= " '' COND_ESPE_SALUD,";
				$cadenaSql .= " '' TRABA_SEXUAL,";
				$cadenaSql .= " '' PERSONA_TALENTO,";
				$cadenaSql .= " '' EST_AFI_SGSSS,";
				$cadenaSql .= " '' LOCALIDAD,";
				$cadenaSql .= " '' TIPO_ZONA,";
				$cadenaSql .= " '' TIP_VIA_PRIN,";
				$cadenaSql .= " '' NUM_VIA_PRIN,";
				$cadenaSql .= " '' NOM_VIA_PRIN,";
				$cadenaSql .= " '' NOM_SIN_VIA_PRIN,";
				$cadenaSql .= " '' LETRA_VIA_PRIN,";
				$cadenaSql .= " '' BIS,";
				$cadenaSql .= " '' LETRA_BIS,";
				$cadenaSql .= " '' CUAD_VIA_PRIN,";
				$cadenaSql .= " '' NUM_VIA_GEN,";
				$cadenaSql .= " '' LETRA_VIA_GEN,";
				$cadenaSql .= " '' NUM_PLACA,";
				$cadenaSql .= " '' CUAD_VIA_GEN,";
				$cadenaSql .= " '' COMPLEMENTO,";
				$cadenaSql .= " EST_DIRECCION DIRECCION_RURAL,";
				$cadenaSql .= " eot_ESTRATO_SOCIAL ESTRATO,";
				$cadenaSql .= " est_telefono TEL_FIJO_CONTACTO,";
				$cadenaSql .= " EOT_TEL_CEL TEL_CELULAR_CONTACTO,";
				$cadenaSql .= " EOT_EMAIL CORREO_ELECTR,";
				$cadenaSql .= " '' LOCALIDAD_CONTACTO,";
				$cadenaSql .= " '' TIPO_ZONA_CONTACTO,";
				$cadenaSql .= " '' TIP_VIA_PRIN_CONTACTO,";
				$cadenaSql .= " '' NUM_VIA_PRIN_CONTACTO,";
				$cadenaSql .= " '' NOM_VIA_PRIN_CONTACTO,";
				$cadenaSql .= " '' NOM_SIN_VIA_PRIN_CONTACTO,";
				$cadenaSql .= " '' LETRA_VIA_PRIN_CONTACTO,";
				$cadenaSql .= " '' BIS_CONTACTO,";
				$cadenaSql .= " '' LETRA_BIS_CONTACTO,";
				$cadenaSql .= " '' CUAD_VIA_PRIN_CONTACTO,";
				$cadenaSql .= " '' NUM_VIA_GEN_CONTACTO,";
				$cadenaSql .= " '' LETRA_VIA_GEN_CONTACTO,";
				$cadenaSql .= " '' NUM_PLACA_CONTACTO,";
				$cadenaSql .= " '' CUAD_VIA_GEN_CONTACTO,";
				$cadenaSql .= " '' COMPLEMENTO_CONTACTO,";
				$cadenaSql .= " '' DIRECCION_RURAL_CONTACTO,";
				$cadenaSql .= " '' ESTRATO_CONTACTO,";
				$cadenaSql .= " '' TEL_FIJO_CONTACTO_CONTACTO,";
				$cadenaSql .= " '' TEL_CELULAR_CONTACTO_CONTACTO,";
				$cadenaSql .= " '' CORREO_ELECTR_CONTACTO,";
				$cadenaSql .= " '' NOMBRE_CONTACTO";
				$cadenaSql .= " FROM ACEST";
				$cadenaSql .= " INNER JOIN acestotr";
				$cadenaSql .= " ON ACESTOTR.EOT_COD = ACEST.EST_COD";
				$cadenaSql .= " WHERE EST_COD > 20160000000";
				// $cadenaSql .= " WHERE EST_COD = '20161025106'";
				// exit;

				break;

			// //PARTICIPANTE SNIES

			case "consultarParticipante" :
				$cadenaSql = "SELECT num_documento, id_tipo_documento FROM";
				$cadenaSql .= " participante ";
				$cadenaSql .= " WHERE num_documento='" . $variable['NUM_DOCUMENTO'] . "'";

				break;
				
			case "consultarParticipanteTodos" :
				$cadenaSql = "SELECT id_tipo_documento, num_documento, fecha_expedicion, primer_nombre, 
       							segundo_nombre, primer_apellido, segundo_apellido, id_sexo_biologico, 
       							id_estado_civil, to_char(fecha_nacimiento, 'DD/MM/YYYY') fecha_nacimiento, id_pais, id_municipio, telefono_contacto, 
       							email_personal, email_institucional, direccion_institucional ";
       			$cadenaSql .= " FROM ";
				$cadenaSql .= " participante ";			

				break;							

			// actualiza los datos de un participante
			case "actualizarParticipante" :
				$cadenaSql = " UPDATE participante";
				$cadenaSql .= " SET id_tipo_documento ='" . $variable['ID_TIPO_DOCUMENTO'] . "',";
				$cadenaSql .= " num_documento ='" . $variable['NUM_DOCUMENTO'] . "',";
				//$cadenaSql .= " fecha_expedicion='" . $variable['FECHA_EXPEDICION'] . "',";
				$cadenaSql .= " primer_nombre ='" . $variable['PRIMER_NOMBRE'] . "',";
				$cadenaSql .= " segundo_nombre ='" . $variable['SEGUNDO_NOMBRE'] . "',";
				$cadenaSql .= " primer_apellido ='" . $variable['PRIMER_APELLIDO'] . "',";
				$cadenaSql .= " segundo_apellido ='" . $variable['SEGUNDO_APELLIDO'] . "',";
				$cadenaSql .= " id_sexo_biologico ='" . $variable['ID_SEXO_BIOLOGICO'] . "',";
				$cadenaSql .= " id_estado_civil ='" . $variable['ID_ESTADO_CIVIL'] . "',";
				$cadenaSql .= " fecha_nacimiento ='" . $variable['FECHA_NACIMIENTO'] . "',";
				$cadenaSql .= " id_pais ='" . $variable['ID_PAIS_NACIMIENTO'] . "',";
				$cadenaSql .= " id_municipio ='" . $variable['ID_MUNICIPIO_NACIMIENTO'] . "',";
				$cadenaSql .= " telefono_contacto ='" . $variable['TELEFONO_CONTACTO'] . "',";
				$cadenaSql .= " email_personal ='" . $variable['EMAIL_PERSONAL'] . "',";
				$cadenaSql .= " email_institucional ='" . str_replace("'", "", $variable['EMAIL_INSTITUCIONAL']) . "'";//elimina las comillas sencillas que existen en algunos registros
				//$cadenaSql .= " direccion_institucional ='" . $variable['DIRECCION_INSTITUCIONAL'] . "'";
				$cadenaSql .= " WHERE NUM_DOCUMENTO='" . $variable['NUM_DOCUMENTO'] . "'";				

				break;

			case "borrarParticipante" :
				$cadenaSql = "DELETE FROM";
				$cadenaSql .= " participante ";
				$cadenaSql .= " WHERE num_documento='" . $variable['NUM_DOCUMENTO'] . "'";
				$cadenaSql .= " AND id_tipo_documento='" . $variable['ID_TIPO_DOCUMENTO'] . "'";

				break;

			case "registrarParticipante" :
				$cadenaSql = "INSERT INTO ";
				$cadenaSql .= "participante ";
				$cadenaSql = " INSERT";
				$cadenaSql .= " INTO participante";
				$cadenaSql .= " (";
				$cadenaSql .= " id_tipo_documento,";
				$cadenaSql .= " num_documento,";
				$cadenaSql .= " fecha_expedicion,";
				$cadenaSql .= " primer_nombre,";
				$cadenaSql .= " segundo_nombre,";
				$cadenaSql .= " primer_apellido,";
				$cadenaSql .= " segundo_apellido,";
				$cadenaSql .= " id_sexo_biologico,";
				$cadenaSql .= " id_estado_civil,";
				$cadenaSql .= " fecha_nacimiento,";
				$cadenaSql .= " id_pais,";
				$cadenaSql .= " id_municipio,";
				$cadenaSql .= " telefono_contacto,";
				$cadenaSql .= " email_personal,";
				$cadenaSql .= " email_institucional,";
				$cadenaSql .= " direccion_institucional";
				$cadenaSql .= " )";
				$cadenaSql .= "VALUES ";
				$cadenaSql .= "( ";
				$cadenaSql .= "'" . $variable['ID_TIPO_DOCUMENTO'] . "', ";
				$cadenaSql .= "'" . $variable['NUM_DOCUMENTO'] . "', ";
				$cadenaSql .= "NULL, ";
				//Fecha de Expedición, no se captura
				$cadenaSql .= "'" . $variable['PRIMER_NOMBRE'] . "', ";
				$cadenaSql .= "'" . $variable['SEGUNDO_NOMBRE'] . "', ";
				$cadenaSql .= "'" . $variable['PRIMER_APELLIDO'] . "', ";
				$cadenaSql .= "'" . $variable['SEGUNDO_APELLIDO'] . "', ";
				$cadenaSql .= "'" . $variable['ID_SEXO_BIOLOGICO'] . "', ";
				$cadenaSql .= "'" . $variable['ID_ESTADO_CIVIL'] . "', ";
				$cadenaSql .= "'" . $variable['FECHA_NACIMIENTO'] . "', ";
				$cadenaSql .= "'" . $variable['ID_PAIS_NACIMIENTO'] . "', ";
				$cadenaSql .= "'" . $variable['ID_MUNICIPIO_NACIMIENTO'] . "', ";
				$cadenaSql .= "'" . $variable['TELEFONO_CONTACTO'] . "', ";
				$cadenaSql .= "'" . $variable['EMAIL_PERSONAL'] . "', ";
				$cadenaSql .= "'" . str_replace("'", "", $variable['EMAIL_INSTITUCIONAL']) . "',";//elimina las comillas sencillas que existen en algunos registros
				$cadenaSql .= "NULL";
				//NO OBLIGATORIO PARA ESTUDIANTES
				$cadenaSql .= " )";

				break;

			///////////////////////
			////PRIMER_CURSO SNIES
			///////////////////////

			case "actualizarEstudiantePrimerCurso" :
				$cadenaSql = " UPDATE primer_curso";
				$cadenaSql .= " SET ano ='" . $variable['ANIO'] . "',";
				$cadenaSql .= " semestre ='" . $variable['SEMESTRE'] . "',";
				$cadenaSql .= " id_tipo_documento ='" . $variable['ID_TIPO_DOCUMENTO'] . "',";
				$cadenaSql .= " num_documento ='" . $variable['NUM_DOCUMENTO'] . "',";
				$cadenaSql .= " pro_consecutivo ='" . $variable['PRO_CONSECUTIVO'] . "',";
				$cadenaSql .= " id_municipio_programa ='" . $variable['ID_MUNICIPIO_PROGRAMA'] . "',";
				$cadenaSql .= " id_tipo_vinculacion ='" . $variable['ID_TIPO_VINCULACION'] . "',";
				$cadenaSql .= " id_grupo_etnico ='" . $variable['ID_GRUPO_ETNICO'] . "',";
				$cadenaSql .= " id_pueblo_indigena ='" . $variable['ID_PUEBLO_INDIGENA'] . "',";
				$cadenaSql .= " id_comunidad_negra ='" . $variable['ID_COMUNIDAD_NEGRA'] . "',";
				$cadenaSql .= " persona_condicion_discapacidad ='" . $variable['PERSONA_CONDICION_DISCAPACIDAD'] . "',";
				$cadenaSql .= " id_tipo_discapacidad ='" . $variable['ID_TIPO_DISCAPACIDAD'] . "',";
				$cadenaSql .= " id_capacidad_excepcional ='" . $variable['ID_CAPACIDAD_EXCEPCIONAL'] . "',";
				$cadenaSql .= " cod_prueba_saber_11 ='" . $variable['COD_PRUEBA_SABER_11'] . "'";
				$cadenaSql .= " WHERE codigo_estudiante ='" . $variable['CODIGO_ESTUDIANTE'] . "'";
				//UN CODIGO DE ESTUDIANTE SOLO EXISTE COMO EN UNA COHORTE, PARA PRIMER_CURSO

				break;

			case "consultarEstudiantePrimerCurso" :
				$cadenaSql = "SELECT num_documento FROM";
				$cadenaSql .= " primer_curso ";
				$cadenaSql .= " WHERE codigo_estudiante='" . $variable['CODIGO_ESTUDIANTE'] . "'";

				break;

			case "consultarPrimerCursoTodos" :
				$cadenaSql = "SELECT ano, semestre, id_tipo_documento, num_documento, pro_consecutivo, 
       			id_municipio_programa, id_tipo_vinculacion, id_grupo_etnico, 
       			id_pueblo_indigena, id_comunidad_negra, persona_condicion_discapacidad, 
       			id_tipo_discapacidad, id_capacidad_excepcional, cod_prueba_saber_11, 
       			codigo_estudiante FROM";
				$cadenaSql .= " primer_curso ";
				$cadenaSql .= " WHERE ano='" . $variable['ANNIO'] . "'";
				$cadenaSql .= " AND semestre='" . $variable['SEMESTRE'] . "'";
				break;

			case "consultarPrimerCursoAuditoria" :
				$cadenaSql = "SELECT participante.id_tipo_documento, participante.num_documento, fecha_expedicion, primer_nombre, 
       				segundo_nombre, primer_apellido, segundo_apellido, id_sexo_biologico, 
       				id_estado_civil, fecha_nacimiento, id_pais, id_municipio, telefono_contacto, 
       				email_personal, email_institucional, direccion_institucional, ";//campos de participante
       			$cadenaSql .= " ano, semestre, primer_curso.pro_consecutivo, 
       				id_municipio_programa, id_tipo_vinculacion, id_grupo_etnico, 
       				id_pueblo_indigena, id_comunidad_negra, persona_condicion_discapacidad, 
       				id_tipo_discapacidad, id_capacidad_excepcional, codigo_estudiante,";//campos de primer curso
       			$cadenaSql .= " nombre, titulo, nivel, modalidad";//campos de programa
				$cadenaSql .= " FROM participante";
				$cadenaSql .= " inner join primer_curso on primer_curso.num_documento=participante.num_documento";
				$cadenaSql .= " left join programa on programa.pro_consecutivo=primer_curso. pro_consecutivo";								
				$cadenaSql .= " WHERE ano='" . $variable['ANNIO'] . "'";
				$cadenaSql .= " AND semestre='" . $variable['SEMESTRE'] . "'";
				
				
				break;				
				
			case "registrarEstudiantePrimerCurso" :
				$cadenaSql = " INSERT";
				$cadenaSql .= " INTO primer_curso";
				$cadenaSql .= " (";
				$cadenaSql .= " codigo_estudiante,";
				$cadenaSql .= " ano,";
				$cadenaSql .= " semestre,";
				$cadenaSql .= " id_tipo_documento,";
				$cadenaSql .= " num_documento,";
				$cadenaSql .= " pro_consecutivo,";
				$cadenaSql .= " id_municipio_programa,";
				$cadenaSql .= " id_tipo_vinculacion,";
				$cadenaSql .= " id_grupo_etnico,";
				$cadenaSql .= " id_pueblo_indigena,";
				$cadenaSql .= " id_comunidad_negra,";
				$cadenaSql .= " persona_condicion_discapacidad,";
				$cadenaSql .= " id_tipo_discapacidad,";
				$cadenaSql .= " id_capacidad_excepcional,";
				$cadenaSql .= " cod_prueba_saber_11";
				$cadenaSql .= " )";
				$cadenaSql .= " VALUES";
				$cadenaSql .= " (";
				$cadenaSql .= "'" . $variable['CODIGO_ESTUDIANTE'] . "', ";
				$cadenaSql .= "'" . $variable['ANIO'] . "', ";
				$cadenaSql .= "'" . $variable['SEMESTRE'] . "', ";
				$cadenaSql .= "'" . $variable['ID_TIPO_DOCUMENTO'] . "', ";
				$cadenaSql .= "'" . $variable['NUM_DOCUMENTO'] . "', ";
				$cadenaSql .= "'" . $variable['PRO_CONSECUTIVO'] . "', ";
				$cadenaSql .= "'11001', ";
				// id_municipio_programa bogota:11001
				$cadenaSql .= "'" . $variable['ID_TIPO_VINCULACION'] . "', ";
				$cadenaSql .= "'" . $variable['ID_GRUPO_ETNICO'] . "', ";
				$cadenaSql .= "'" . $variable['ID_PUEBLO_INDIGENA'] . "', ";
				$cadenaSql .= "'" . $variable['ID_COMUNIDAD_NEGRA'] . "', ";
				$cadenaSql .= "'" . $variable['PERSONA_CONDICION_DISCAPACIDAD'] . "', ";
				$cadenaSql .= "'" . $variable['ID_TIPO_DISCAPACIDAD'] . "', ";
				$cadenaSql .= "'" . $variable['ID_CAPACIDAD_EXCEPCIONAL'] . "', ";
				$cadenaSql .= "'" . $variable['COD_PRUEBA_SABER_11'] . "' ";
				$cadenaSql .= " );";
				$cadenaSql .= " ";

				break;

			// borra todos los estudiantes de la tabla estudiante_programa para un año y semestre definido
			//No se utiliza
			case "borrarEstudiantePrimerCursoPeriodoTodos" :
				$cadenaSql = "DELETE FROM";
				$cadenaSql .= " primer_curso ";
				$cadenaSql .= " WHERE ano='" . $variable['ANNIO'] . "'";
				$cadenaSql .= " AND semestre='" . $variable['SEMESTRE'] . "'";

				break;

			// /MATRICULADO

			// borra el matriculado con el numero y tipo de documento dado para todos los períodos
			case "borrarMatriculado" :
				$cadenaSql = "DELETE FROM";
				$cadenaSql .= " matriculado ";
				$cadenaSql .= " WHERE codigo_unico='" . $variable['CODIGO_UNICO'] . "'";
				$cadenaSql .= " AND tipo_doc_unico='" . $variable['TIPO_DOC_UNICO'] . "'";

				break;

			// borra el todos los matriculados para el año y periodo dados
			case "borrarMatriculadoPeriodoTodos" :
				$cadenaSql = "DELETE FROM";
				$cadenaSql .= " matriculado ";
				$cadenaSql .= " WHERE est_annio='" . $variable['ANNIO_MATRICULA'] . "'";
				$cadenaSql .= " AND est_semestre='" . $variable['SEMESTRE_MATRICULA'] . "'";

				break;

			case "contarMatriculados" :
				$cadenaSql = "SELECT COUNT(*) FROM";
				$cadenaSql .= " matriculado ";
				$cadenaSql .= " WHERE est_annio=" . $variable['annio'];
				$cadenaSql .= " AND est_semestre='" . $variable['semestre'] . "'";

				break;

			case "consultarMatriculado" :
				$cadenaSql = "SELECT codigo_estudiante FROM";
				$cadenaSql .= " matriculado ";
				$cadenaSql .= " WHERE codigo_estudiante='" . $variable['CODIGO_ESTUDIANTE'] . "'";
				$cadenaSql .= " AND ano ='" . $variable['ANNIO_MATRICULA'] . "'";
				$cadenaSql .= " AND semestre ='" . $variable['SEMESTRE_MATRICULA'] . "'";

				break;
				
			case "consultarMatriculadoTodos" :
				$cadenaSql = "SELECT ano, semestre, id_tipo_documento, num_documento, codigo_estudiante, 
       			pro_consecutivo, id_municipio, to_char(fecha_nacimiento,'DD/MM/YYYY') fecha_nacimiento, id_pais_nacimiento, 
       			id_municipio_nacimiento, id_zona_residencia, es_reintegro";
				$cadenaSql .= " FROM ";
				$cadenaSql .= " matriculado ";				
				$cadenaSql .= " WHERE ano ='" . $variable['ANNIO_MATRICULA'] . "'";
				$cadenaSql .= " AND semestre ='" . $variable['SEMESTRE_MATRICULA'] . "'";				

				break;	
				
					
			case "consultarMatriculadoAuditoria" :
				$cadenaSql = "SELECT participante.id_tipo_documento, participante.num_documento, fecha_expedicion, primer_nombre, 
       				segundo_nombre, primer_apellido, segundo_apellido, 
       				email_personal, email_institucional, direccion_institucional, ";//campos de participante
       			$cadenaSql .= " ano, semestre, codigo_estudiante, matriculado.pro_consecutivo, ";//campos de matriculado
       			$cadenaSql .= " nombre, titulo, nivel, modalidad";//campos de programa
				$cadenaSql .= " FROM participante";
				$cadenaSql .= " inner join matriculado on matriculado.num_documento=  participante.num_documento ";
				$cadenaSql .= " inner join programa on programa.pro_consecutivo=matriculado.pro_consecutivo ";				
				$cadenaSql .= " WHERE ano ='" . $variable['ANNIO_MATRICULA'] . "'";
				$cadenaSql .= " AND semestre ='" . $variable['SEMESTRE_MATRICULA'] . "'";								

				break;						
				
										

			case "registrarMatriculado" :
				$cadenaSql = " INSERT";
				$cadenaSql .= " INTO matriculado";
				$cadenaSql .= " (";
				$cadenaSql .= " ano,";
				// se refiere al año y semestre de la matricula no a la cohorte
				$cadenaSql .= " semestre,";
				$cadenaSql .= " id_tipo_documento,";
				$cadenaSql .= " num_documento,";
				$cadenaSql .= " codigo_estudiante,";
				$cadenaSql .= " pro_consecutivo,";
				$cadenaSql .= " id_municipio,";
				$cadenaSql .= " fecha_nacimiento,";
				$cadenaSql .= " id_pais_nacimiento,";
				$cadenaSql .= " id_municipio_nacimiento,";
				$cadenaSql .= " id_zona_residencia,";
				$cadenaSql .= " es_reintegro";
				$cadenaSql .= " )";
				$cadenaSql .= " VALUES";
				$cadenaSql .= " (";
				$cadenaSql .= "'" . $variable['ANNIO_MATRICULA'] . "', ";
				$cadenaSql .= "'" . $variable['SEMESTRE_MATRICULA'] . "', ";
				$cadenaSql .= "'" . $variable['ID_TIPO_DOCUMENTO'] . "', ";
				$cadenaSql .= "'" . $variable['NUM_DOCUMENTO'] . "', ";
				$cadenaSql .= "'" . $variable['CODIGO_ESTUDIANTE'] . "', ";
				$cadenaSql .= "'" . $variable['PRO_CONSECUTIVO'] . "', ";
				$cadenaSql .= "'" . $variable['ID_MUNICIPIO_PROGRAMA'] . "', ";
				$cadenaSql .= "'" . $variable['FECHA_NACIMIENTO'] . "', ";
				$cadenaSql .= "'" . $variable['ID_PAIS_NACIMIENTO'] . "', ";
				$cadenaSql .= "'" . $variable['ID_MUNICIPIO_NACIMIENTO'] . "', ";
				$cadenaSql .= "'" . $variable['ID_ZONA_RESIDENCIA'] . "', ";
				$cadenaSql .= "'" . $variable['ES_REINTEGRO'] . "' ";
				$cadenaSql .= " )";

				break;

			case "actualizarMatriculado" :
				$cadenaSql = " UPDATE matriculado";
				$cadenaSql .= " SET id_tipo_documento ='" . $variable['ID_TIPO_DOCUMENTO'] . "',";
				$cadenaSql .= " num_documento ='" . $variable['NUM_DOCUMENTO'] . "',";
				$cadenaSql .= " pro_consecutivo='" . $variable['PRO_CONSECUTIVO'] . "',";
				$cadenaSql .= " id_municipio ='" . $variable['ID_MUNICIPIO_PROGRAMA'] . "',";
				$cadenaSql .= " fecha_nacimiento ='" . $variable['FECHA_NACIMIENTO'] . "',";
				$cadenaSql .= " id_pais_nacimiento ='" . $variable['ID_PAIS_NACIMIENTO'] . "',";
				$cadenaSql .= " id_municipio_nacimiento ='" . $variable['ID_MUNICIPIO_NACIMIENTO'] . "',";
				$cadenaSql .= " id_zona_residencia ='" . $variable['ID_ZONA_RESIDENCIA'] . "',";
				$cadenaSql .= " es_reintegro ='" . $variable['ES_REINTEGRO'] . "'";
				$cadenaSql .= " WHERE codigo_estudiante='" . $variable['CODIGO_ESTUDIANTE'] . "'";

				break;

			// EGRESADO SNIES

			case "consultarGraduadoAcademica" :
				$cadenaSql = " SELECT ";
				$cadenaSql .= " TO_NUMBER(TO_CHAR(egr_fecha_grado,'yyyy')) ano,";
				$cadenaSql .= " DECODE(TO_NUMBER(TO_CHAR(egr_fecha_grado,'mm')),1,'01',2,'01',3,'01',4,'01',5,'01',6,'01',7,'02',8,'02',9,'02',10,'02',11,'02',12,'02') semestre,";
				$cadenaSql .= " TO_CHAR(DECODE(est_tipo_iden,'C', 'CC', 'T', 'TI', 'E', 'CE', 'P', 'PS')) id_tipo_documento,";
				$cadenaSql .= " TO_CHAR(est_nro_iden) num_documento,";
				$cadenaSql .= " as_cra_cod_snies pro_consecutivo,";
				$cadenaSql .= " '11' DEPARTAMENTO,";
				// Donde se gradúa
				$cadenaSql .= " '11001' MUNICIPIO,";
				
				
				$cadenaSql .= " EST_COD CODIGO_ESTUDIANTE,";				
				$cadenaSql .= " EST_NOMBRE,";
				$cadenaSql .= " TO_CHAR(eot_fecha_nac, 'DD/MM/YYYY') fecha_nacim,";
				$cadenaSql .= " TO_CHAR('CO') pais_ln,";
				$cadenaSql .= " TO_CHAR(DECODE (mun_dep_cod,0,11,'',11, mun_dep_cod)) departamento_ln,";
				$cadenaSql .= " TO_CHAR(DECODE (mun_cod,0,11001,'',11001,99999,11001, mun_cod)) municipio_ln,";
				$cadenaSql .= " TO_CHAR(DECODE(est_sexo,'M','01','F','02','01')) genero_code,";
				$cadenaSql .= " eot_email email,";
				$cadenaSql .= " DECODE(eot_estado_civil,1,'01',2,'02',3,'05',4,'03',5,'04', '01') est_civil_code,";
				
				
				
				
				
				$cadenaSql .= " TO_CHAR(est_telefono) numero_tel,";
				
				$cadenaSql .= " DECODE(LENGTH(est_cod),7,(SUBSTR(est_cod,1,2)+1900),11,(SUBSTR(est_cod, 1,4))) anio,";
				$cadenaSql .= " DECODE(DECODE(LENGTH(est_cod),7,((SUBSTR(est_cod,3,1))),11,(SUBSTR(est_cod, 5,1))), '1','01','02') semestre,";
				$cadenaSql .= " '02' es_transferencia,";
				$cadenaSql .= " DECODE(CRA_JORNADA, 'DIURNA', '01', 'NOCTURNA', '02', '01' ) HORARIO_CODE,";
				$cadenaSql .= " '01' PAGO,";
				$cadenaSql .= " egr_fecha_grado FECHA_GRADO,";				
				$cadenaSql .= " 'no' ECAES_OBSERVACIONES,";
				$cadenaSql .= " '0' ECAES_RESULTADOS,";
				
				// Donde se gradúa
				$cadenaSql .= " '1301' CODIGO_ENT_AULA,";
				$cadenaSql .= " EGR_ACTA_GRADO ACTA,";
				$cadenaSql .= " EGR_FOLIO FOLIO,";
				$cadenaSql .= " EOT_NRO_SNP SNP";
				$cadenaSql .= " FROM ACEGRESADO";
				$cadenaSql .= " INNER JOIN mntac.acest";
				$cadenaSql .= " ON egr_est_cod=est_cod";
				$cadenaSql .= " INNER JOIN mntac.acestotr";
				$cadenaSql .= " ON est_cod = eot_cod";
				$cadenaSql .= " INNER JOIN accra_snies";
				$cadenaSql .= " ON as_cra_cod=est_cra_cod";
				$cadenaSql .= " INNER JOIN mntge.gemunicipio";
				$cadenaSql .= " ON MUN_COD=DECODE(EOT_COD_MUN_NAC,0,11001,'',11001,EOT_COD_MUN_NAC)";
				$cadenaSql .= " INNER JOIN accra";
				$cadenaSql .= " ON CRA_COD = EST_CRA_COD";
				$cadenaSql .= " WHERE TO_NUMBER(TO_CHAR(egr_fecha_grado,'yyyy'))='" . $variable['annio'] . "'";
				$cadenaSql .= " AND DECODE(TO_NUMBER(TO_CHAR(egr_fecha_grado,'mm')),1,1,2,1,3,1,4,1,5,1,6,1,7,3,8,3,9,3,10,3,11,3,12,3)='" . $variable['semestre'] . "'";
				echo $cadenaSql;exit;

				break;

			case "borrarEgresado" :
				$cadenaSql = "DELETE FROM";
				$cadenaSql .= " egresado ";
				$cadenaSql .= " WHERE codigo_unico='" . $variable['CODIGO_UNICO'] . "'";
				$cadenaSql .= " AND tipo_doc_unico='" . $variable['TIPO_DOC_UNICO'] . "'";

				break;

			// GRADUADO SNIES

			case "borrarGraduadoPeriodoTodos" :
				$cadenaSql = "DELETE FROM";
				$cadenaSql .= " graduado ";
				$cadenaSql .= " WHERE grad_annio='" . $variable['ANNIO_GRADO'] . "'";
				$cadenaSql .= " AND grad_semestre='" . $variable['SEMESTRE_GRADO'] . "'";

				break;

			case "registrarGraduado" :
				$cadenaSql = " INSERT";
				$cadenaSql .= " INTO graduado";
				$cadenaSql .= " (";
				$cadenaSql .= " ies_code,";
				$cadenaSql .= " grad_annio,";
				$cadenaSql .= " grad_semestre,";
				$cadenaSql .= " codigo_unico,";
				$cadenaSql .= " pro_consecutivo,";
				$cadenaSql .= " fecha_grado,";
				$cadenaSql .= " ecaes_observaciones,";
				$cadenaSql .= " ecaes_resultados,";
				$cadenaSql .= " departamento,";
				$cadenaSql .= " municipio,";
				$cadenaSql .= " codigo_ent_aula,";
				$cadenaSql .= " acta,";
				$cadenaSql .= " folio,";
				$cadenaSql .= " tipo_doc_unico,";
				$cadenaSql .= " snp";
				$cadenaSql .= " )";
				$cadenaSql .= " VALUES";
				$cadenaSql .= " (";
				$cadenaSql .= "'" . $variable['IES_CODE'] . "', ";
				$cadenaSql .= "'" . $variable['GRAD_ANNIO'] . "', ";
				$cadenaSql .= "'" . $variable['GRAD_SEMESTRE'] . "', ";
				$cadenaSql .= "'" . $variable['CODIGO_UNICO'] . "', ";
				$cadenaSql .= "'" . $variable['PRO_CONSECUTIVO'] . "', ";
				$cadenaSql .= "'" . $variable['FECHA_GRADO'] . "', ";
				$cadenaSql .= "'" . $variable['ECAES_OBSERVACIONES'] . "', ";
				$cadenaSql .= "'" . $variable['ECAES_RESULTADOS'] . "', ";
				$cadenaSql .= "'" . $variable['DEPARTAMENTO'] . "', ";
				$cadenaSql .= "'" . $variable['MUNICIPIO'] . "', ";
				$cadenaSql .= "'" . $variable['CODIGO_ENT_AULA'] . "', ";
				$cadenaSql .= "'" . $variable['ACTA'] . "', ";
				$cadenaSql .= "'" . $variable['FOLIO'] . "', ";
				$cadenaSql .= "'" . $variable['TIPO_DOC_UNICO'] . "', ";
				$cadenaSql .= "'" . $variable['SNP'] . "' ";
				$cadenaSql .= " )";

				break;

			case "borrarGraduado" :
				$cadenaSql = "DELETE FROM";
				$cadenaSql .= " graduado ";
				$cadenaSql .= " WHERE codigo_unico='" . $variable['CODIGO_UNICO'] . "'";
				$cadenaSql .= " AND tipo_doc_unico='" . $variable['TIPO_DOC_UNICO'] . "'";

				break;

			// //ESTUDIANTE SNIES NO SE UTILIZA EN EL NUEVO MODELO

			case "consultarEstudiante" :
				$cadenaSql = "SELECT codigo_unico, tipo_doc_unico FROM";
				$cadenaSql .= " estudiante ";
				$cadenaSql .= " WHERE codigo_unico='" . $variable['CODIGO_UNICO'] . "'";

				break;

			case "consultarEstudianteTodos" :
				$cadenaSql = "SELECT codigo_unico, tipo_doc_unico FROM";
				$cadenaSql .= " estudiante ";

				break;

			case "actualizarEstudiante" :
				$cadenaSql = " UPDATE estudiante";
				$cadenaSql .= " SET ";
				$cadenaSql .= " ies_code ='" . $variable['IES_CODE'] . "',";
				$cadenaSql .= " tipo_doc_unico ='" . $variable['TIPO_DOC_UNICO'] . "'";
				$cadenaSql .= " WHERE codigo_unico='" . $variable['CODIGO_UNICO'] . "'";

				break;

			case "registrarEstudiante" :
				$cadenaSql = " INSERT";
				$cadenaSql .= " INTO estudiante";
				$cadenaSql .= " (";
				$cadenaSql .= " ies_code,";
				$cadenaSql .= " codigo_unico,";
				$cadenaSql .= " tipo_doc_unico";
				$cadenaSql .= " )";
				$cadenaSql .= " VALUES";
				$cadenaSql .= " (";
				$cadenaSql .= "'" . $variable['IES_CODE'] . "', ";
				$cadenaSql .= "'" . $variable['CODIGO_UNICO'] . "', ";
				$cadenaSql .= "'" . $variable['TIPO_DOC_UNICO'] . "' ";
				$cadenaSql .= " );";

				break;

			case "borrarEstudiante" :
				$cadenaSql = "DELETE FROM";
				$cadenaSql .= " estudiante ";
				$cadenaSql .= " WHERE codigo_unico='" . $variable['CODIGO_UNICO'] . "'";
				$cadenaSql .= " AND tipo_doc_unico='" . $variable['TIPO_DOC_UNICO'] . "'";

				break;

			// // ESTUDIANTE PROGRAMA NO SE UTILIZA EN EL NUEVO MODELO
			// borra el estudiante_programa con el numero y tipo de documento dado para todos los períodos
			case "borrarEstudiantePrograma" :
				$cadenaSql = "DELETE FROM";
				$cadenaSql .= " estudiante_programa ";
				$cadenaSql .= " WHERE codigo_unico='" . $variable['CODIGO_UNICO'] . "'";
				$cadenaSql .= " AND tipo_doc_unico='" . $variable['TIPO_DOC_UNICO'] . "'";

				break;
		}

		return $cadenaSql;
	}

}
?>
