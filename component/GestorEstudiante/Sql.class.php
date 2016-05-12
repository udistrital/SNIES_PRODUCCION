<?php

namespace sniesEstudiante;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}

include_once ("core/manager/Configurador.class.php");
include_once ("core/connection/Sql.class.php");

// Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
// en camel case precedida por la palabra sql
class Sql extends \Sql {
	var $miConfigurador;
	function __construct() {
		$this->miConfigurador = \Configurador::singleton ();
	}
	function cadena_sql($tipo, $variable = "") {
		
		/**
		 * 1.
		 * Revisar las variables para evitar SQL Injection
		 */
		$prefijo = 'sniesud_';
		$idSesion = $this->miConfigurador->getVariableConfiguracion ( "id_sesion" );
		
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
				$cadenaSql .= " TO_CHAR('1301') ies_code,";
				$cadenaSql .= " EST_NOMBRE,";
				$cadenaSql .= " TO_CHAR(eot_fecha_nac, 'yyyy-mm-dd') fecha_nacim,";
				$cadenaSql .= " TO_CHAR('CO') pais_ln,";
				$cadenaSql .= " TO_CHAR(DECODE (mun_dep_cod,0,11,'',11, mun_dep_cod)) departamento_ln,";
				$cadenaSql .= " TO_CHAR(DECODE (mun_cod,0,11001,'',11001,99999,11001, mun_cod)) municipio_ln,";
				$cadenaSql .= " TO_CHAR(DECODE(est_sexo,'M','01','F','02','01')) genero_code,";
				$cadenaSql .= " eot_email email,";
				$cadenaSql .= " DECODE(eot_estado_civil,1,'01',2,'02',3,'05',4,'03',5,'04', '01') est_civil_code,";
				$cadenaSql .= " TO_CHAR(DECODE(est_tipo_iden,'C', 'CC', 'T', 'TI', 'E', 'CE', 'P', 'PS')) tipo_doc_unico,";
				$cadenaSql .= " TO_CHAR(est_nro_iden) codigo_unico,";
				$cadenaSql .= " TO_CHAR(DECODE(est_tipo_iden_ant,'C', 'CC', 'T', 'TI', 'E', 'CE', 'P', 'PS')) tipo_id_ant,";
				$cadenaSql .= " est_nro_iden_ant codigo_id_ant,";
				$cadenaSql .= " '57' pais_tel,";
				$cadenaSql .= " '1' area_tel,";
				$cadenaSql .= " TO_CHAR(est_telefono) numero_tel,";
				// $cadenaSql .= " --datos estudiante_programa";
				$cadenaSql .= " as_cra_cod_snies pro_consecutivo,";
				$cadenaSql .= " DECODE(LENGTH(est_cod),7,(SUBSTR(est_cod,1,2)+1900),11,(SUBSTR(est_cod, 1,4))) anio,";
				$cadenaSql .= " DECODE(DECODE(LENGTH(est_cod),7,((SUBSTR(est_cod,3,1))),11,(SUBSTR(est_cod, 5,1))), '1','01','02') semestre,";
				$cadenaSql .= " '02' es_transferencia,";
				// $cadenaSql .= " --datos matriculado";
				$cadenaSql .= " DECODE(cra_jornada, 'DIURNA', '01', 'NOCTURNA', '02', '01' ) horario_code,";
				$cadenaSql .= " '01' pago";
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
				$cadenaSql .= " WHERE mat_ano ='" . $variable ['annio'] . "'";
				if ($variable ['semestre'] == '01') {
					$cadenaSql .= " AND mat_per =1 ";
				} else {
					$cadenaSql .= " AND mat_per =3 "; // el semestre 03 de la universidad es el semestre 02 de SNIES";
				}
				
				// $cadenaSql .= " AND est_nro_iden=1020742945";
				// $cadenaSql .= " AND rownum < 10";
				
				break;
			
			// consulta para la Base Poblacional Unificada del Distrito Capital,
			// En los casos que el valor es null se coloca el simbolo "|" para que se cree la variable
			// para procesarla más adelante
			case "consultarEstudianteBpudc" :
				
				$cadenaSql = " SELECT";
				$cadenaSql .= " EST_COD ID,";
				$cadenaSql .= " EST_NOMBRE NOMBRE,"; // NOMBRE_1, NOMBRE_2, APELLIDO_1, APELLIDO_2
				$cadenaSql .= " EST_TIPO_IDEN TIP_ID,";
				$cadenaSql .= " EST_NRO_IDEN NUM_ID,";
				$cadenaSql .= " EOT_COD_LUG_NAC MUN_NAC,";
				$cadenaSql .= " 'CO' PAIS_NAC,";
				$cadenaSql .= " '' FEC_ID,";
				$cadenaSql .= " EOT_SEXO SEXO,";
				$cadenaSql .= " EOT_FECHA_NAC FEC_NAC,";
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
				$cadenaSql .= " WHERE EST_COD LIKE '20161020%'";
				// $cadenaSql .= " WHERE EST_COD = '20161025106'";
				// echo $cadenaSql;
				// exit;
				
				break;
			
			// //PARTICIPANTE SNIES
			
			case "consultarParticipante" :
				$cadenaSql = "SELECT codigo_unico, tipo_doc_unico FROM";
				$cadenaSql .= " participante ";
				$cadenaSql .= " WHERE codigo_unico='" . $variable ['CODIGO_UNICO'] . "'";
				
				break;
			
			// actualiza los datos de un participante
			case "actualizarParticipante" :
				$cadenaSql = " UPDATE participante";
				$cadenaSql .= " SET ies_code ='" . $variable ['IES_CODE'] . "',";
				$cadenaSql .= " primer_apellido ='" . $variable ['PRIMER_APELLIDO'] . "',";
				$cadenaSql .= " segundo_apellido='" . $variable ['SEGUNDO_APELLIDO'] . "',";
				$cadenaSql .= " primer_nombre ='" . $variable ['PRIMER_NOMBRE'] . "',";
				$cadenaSql .= " segundo_nombre ='" . $variable ['SEGUNDO_NOMBRE'] . "',";
				$cadenaSql .= " fecha_nacim ='" . $variable ['FECHA_NACIM'] . "',";
				$cadenaSql .= " pais_ln ='" . $variable ['PAIS_LN'] . "',";
				$cadenaSql .= " departamento_ln ='" . $variable ['DEPARTAMENTO_LN'] . "',";
				$cadenaSql .= " municipio_ln ='" . $variable ['MUNICIPIO_LN'] . "',";
				$cadenaSql .= " genero_code ='" . $variable ['GENERO_CODE'] . "',";
				$cadenaSql .= " email ='" . $variable ['EMAIL'] . "',";
				$cadenaSql .= " est_civil_code ='" . $variable ['EST_CIVIL_CODE'] . "',";
				// $cadenaSql .= " tipo_doc_unico ='" . $variable ['TIPO_DOC_UNICO'] . "',";
				// $cadenaSql .= " codigo_unico ='" . $variable ['CODIGO_UNICO'] . "',";
				$cadenaSql .= " tipo_id_ant ='" . $variable ['TIPO_ID_ANT'] . "',";
				$cadenaSql .= " codigo_id_ant ='" . $variable ['CODIGO_ID_ANT'] . "',";
				$cadenaSql .= " pais_tel ='" . $variable ['PAIS_TEL'] . "',";
				$cadenaSql .= " area_tel ='" . $variable ['AREA_TEL'] . "',";
				$cadenaSql .= " numero_tel ='" . $variable ['NUMERO_TEL'] . "'";
				$cadenaSql .= " WHERE codigo_unico='" . $variable ['CODIGO_UNICO'] . "'";
				
				break;
			
			case "borrarParticipante" :
				$cadenaSql = "DELETE FROM";
				$cadenaSql .= " participante ";
				$cadenaSql .= " WHERE codigo_unico='" . $variable ['CODIGO_UNICO'] . "'";
				$cadenaSql .= " AND tipo_doc_unico='" . $variable ['TIPO_DOC_UNICO'] . "'";
				echo $cadenaSql;
				
				break;
			
			case "registrarParticipante" :
				$cadenaSql = "INSERT INTO ";
				$cadenaSql .= "participante ";
				$cadenaSql = " INSERT";
				$cadenaSql .= " INTO participante";
				$cadenaSql .= " (";
				$cadenaSql .= " ies_code,";
				$cadenaSql .= " primer_apellido,";
				$cadenaSql .= " segundo_apellido,";
				$cadenaSql .= " primer_nombre,";
				$cadenaSql .= " segundo_nombre,";
				$cadenaSql .= " fecha_nacim,";
				$cadenaSql .= " pais_ln,";
				$cadenaSql .= " departamento_ln,";
				$cadenaSql .= " municipio_ln,";
				$cadenaSql .= " genero_code,";
				$cadenaSql .= " email,";
				$cadenaSql .= " est_civil_code,";
				$cadenaSql .= " tipo_doc_unico,";
				$cadenaSql .= " codigo_unico,";
				$cadenaSql .= " tipo_id_ant,";
				$cadenaSql .= " codigo_id_ant,";
				$cadenaSql .= " pais_tel,";
				$cadenaSql .= " area_tel,";
				$cadenaSql .= " numero_tel";
				$cadenaSql .= " )";
				$cadenaSql .= "VALUES ";
				$cadenaSql .= "( ";
				$cadenaSql .= "'" . $variable ['IES_CODE'] . "', ";
				$cadenaSql .= "'" . $variable ['PRIMER_APELLIDO'] . "', ";
				$cadenaSql .= "'" . $variable ['SEGUNDO_APELLIDO'] . "', ";
				$cadenaSql .= "'" . $variable ['PRIMER_NOMBRE'] . "', ";
				$cadenaSql .= "'" . $variable ['SEGUNDO_NOMBRE'] . "', ";
				$cadenaSql .= "'" . $variable ['FECHA_NACIM'] . "', ";
				$cadenaSql .= "'" . $variable ['PAIS_LN'] . "', ";
				$cadenaSql .= "'" . $variable ['DEPARTAMENTO_LN'] . "', ";
				$cadenaSql .= "'" . $variable ['MUNICIPIO_LN'] . "', ";
				$cadenaSql .= "'" . $variable ['GENERO_CODE'] . "', ";
				$cadenaSql .= "'" . $variable ['EMAIL'] . "', ";
				$cadenaSql .= "'" . $variable ['EST_CIVIL_CODE'] . "', ";
				$cadenaSql .= "'" . $variable ['TIPO_DOC_UNICO'] . "', ";
				$cadenaSql .= "'" . $variable ['CODIGO_UNICO'] . "', ";
				$cadenaSql .= "'" . $variable ['TIPO_ID_ANT'] . "', ";
				$cadenaSql .= "'" . $variable ['CODIGO_ID_ANT'] . "', ";
				$cadenaSql .= "'" . $variable ['PAIS_TEL'] . "', ";
				$cadenaSql .= "'" . $variable ['AREA_TEL'] . "', ";
				$cadenaSql .= "'" . $variable ['NUMERO_TEL'] . "'";
				$cadenaSql .= " )";
				
				break;
			
			// //ESTUDIANTE SNIES
			
			case "consultarEstudiante" :
				$cadenaSql = "SELECT codigo_unico, tipo_doc_unico FROM";
				$cadenaSql .= " estudiante ";
				$cadenaSql .= " WHERE codigo_unico='" . $variable ['CODIGO_UNICO'] . "'";
				
				break;
			
			case "consultarEstudianteTodos" :
				$cadenaSql = "SELECT codigo_unico, tipo_doc_unico FROM";
				$cadenaSql .= " estudiante ";
				
				break;
			
			case "actualizarEstudiante" :
				$cadenaSql = " UPDATE estudiante";
				$cadenaSql .= " SET ";
				$cadenaSql .= " ies_code ='" . $variable ['IES_CODE'] . "',";
				$cadenaSql .= " tipo_doc_unico ='" . $variable ['TIPO_DOC_UNICO'] . "'";
				$cadenaSql .= " WHERE codigo_unico='" . $variable ['CODIGO_UNICO'] . "'";
				
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
				$cadenaSql .= "'" . $variable ['IES_CODE'] . "', ";
				$cadenaSql .= "'" . $variable ['CODIGO_UNICO'] . "', ";
				$cadenaSql .= "'" . $variable ['TIPO_DOC_UNICO'] . "' ";
				$cadenaSql .= " );";
				
				break;
			
			case "borrarEstudiante" :
				$cadenaSql = "DELETE FROM";
				$cadenaSql .= " estudiante ";
				$cadenaSql .= " WHERE codigo_unico='" . $variable ['CODIGO_UNICO'] . "'";
				$cadenaSql .= " AND tipo_doc_unico='" . $variable ['TIPO_DOC_UNICO'] . "'";
				
				break;
			
			// //ESTUDIANTE_PROGRAMA SNIES
			
			case "consultarEstudiantePrograma" :
				$cadenaSql = "SELECT codigo_unico FROM";
				$cadenaSql .= " estudiante_programa ";
				$cadenaSql .= " WHERE codigo_unico='" . $variable ['CODIGO_UNICO'] . "'";
				
				break;
			
			// borra el estudiante_programa con el numero y tipo de documento dado para todos los períodos
			case "borrarEstudiantePrograma" :
				$cadenaSql = "DELETE FROM";
				$cadenaSql .= " estudiante_programa ";
				$cadenaSql .= " WHERE codigo_unico='" . $variable ['CODIGO_UNICO'] . "'";
				$cadenaSql .= " AND tipo_doc_unico='" . $variable ['TIPO_DOC_UNICO'] . "'";
				
				break;
			
			// borra todos los estudiantes de la tabla estudiante_programa para un año y semestre definido
			case "borrarEstudianteProgramaPeriodoTodos" :
				$cadenaSql = "DELETE FROM";
				$cadenaSql .= " estudiante_programa ";
				$cadenaSql .= " WHERE anio='" . $variable ['ANNIO'] . "'";
				$cadenaSql .= " AND semestre='" . $variable ['SEMESTRE'] . "'";
				
				break;
			
			case "registrarEstudiantePrograma" :
				$cadenaSql = " INSERT";
				$cadenaSql .= " INTO estudiante_programa";
				$cadenaSql .= " (";
				$cadenaSql .= " pro_consecutivo,";
				$cadenaSql .= " anio,";
				$cadenaSql .= " semestre,";
				$cadenaSql .= " es_transferencia,";
				$cadenaSql .= " ies_code,";
				$cadenaSql .= " codigo_unico,";
				$cadenaSql .= " tipo_doc_unico";
				$cadenaSql .= " )";
				$cadenaSql .= " VALUES";
				$cadenaSql .= " (";
				$cadenaSql .= "'" . $variable ['PRO_CONSECUTIVO'] . "', ";
				$cadenaSql .= "'" . $variable ['ANIO'] . "', ";
				$cadenaSql .= "'" . $variable ['SEMESTRE'] . "', ";
				$cadenaSql .= "'" . $variable ['ES_TRANSFERENCIA'] . "', ";
				$cadenaSql .= "'" . $variable ['IES_CODE'] . "', ";
				$cadenaSql .= "'" . $variable ['CODIGO_UNICO'] . "', ";
				$cadenaSql .= "'" . $variable ['TIPO_DOC_UNICO'] . "' ";
				$cadenaSql .= " );";
				$cadenaSql .= " ";
				
				break;
			
			// /MATRICULADO
			
			// borra el matriculado con el numero y tipo de documento dado para todos los períodos
			case "borrarMatriculado" :
				$cadenaSql = "DELETE FROM";
				$cadenaSql .= " matriculado ";
				$cadenaSql .= " WHERE codigo_unico='" . $variable ['CODIGO_UNICO'] . "'";
				$cadenaSql .= " AND tipo_doc_unico='" . $variable ['TIPO_DOC_UNICO'] . "'";
				
				break;
			
			// borra el todos los matriculados para el año y periodo dados
			case "borrarMatriculadoPeriodoTodos" :
				$cadenaSql = "DELETE FROM";
				$cadenaSql .= " matriculado ";
				$cadenaSql .= " WHERE est_annio='" . $variable ['ANNIO_MATRICULA'] . "'";
				$cadenaSql .= " AND est_semestre='" . $variable ['SEMESTRE_MATRICULA'] . "'";
				
				break;
			
			case "contarMatriculados" :
				$cadenaSql = "SELECT COUNT(*) FROM";
				$cadenaSql .= " matriculado ";
				$cadenaSql .= " WHERE est_annio=" . $variable ['annio'];
				$cadenaSql .= " AND est_semestre='" . $variable ['semestre'] . "'";
				
				break;
			
			case "registrarMatriculado" :
				$cadenaSql = " INSERT";
				$cadenaSql .= " INTO matriculado";
				$cadenaSql .= " (";
				$cadenaSql .= " ies_code,";
				$cadenaSql .= " est_annio,"; // PERIODO A ACTUALIZAR, NO ES LA COHORTE DEL ESTUDIANTE
				$cadenaSql .= " est_semestre,"; // PERIODO A ACTUALIZAR, NO ES LA COHORTE DEL ESTUDIANTE
				$cadenaSql .= " codigo_unico,";
				$cadenaSql .= " horario_code,";
				$cadenaSql .= " ceres,";
				$cadenaSql .= " departamento,";
				$cadenaSql .= " municipio,";
				$cadenaSql .= " pro_consecutivo,";
				$cadenaSql .= " pago,";
				$cadenaSql .= " tipo_doc_unico,";
				$cadenaSql .= " municipio_le,";
				$cadenaSql .= " departamento_le,";
				$cadenaSql .= " ceres_univ,";
				$cadenaSql .= " estudiante_articulacion,";
				$cadenaSql .= " grado_que_cursa,";
				$cadenaSql .= " institucion_bachillerato";
				$cadenaSql .= " )";
				$cadenaSql .= " VALUES";
				$cadenaSql .= " (";
				$cadenaSql .= "'" . $variable ['IES_CODE'] . "', ";
				$cadenaSql .= "'" . $variable ['ANNIO_MATRICULA'] . "', "; // URGENTE SE DEBE REEMPLAZAR POR EL VALOR DE ANO Y DE SEMESTRE ACTUAL.
				$cadenaSql .= "'" . $variable ['SEMESTRE_MATRICULA'] . "', ";
				$cadenaSql .= "'" . $variable ['CODIGO_UNICO'] . "', ";
				$cadenaSql .= "'" . $variable ['HORARIO_CODE'] . "', ";
				$cadenaSql .= " '1301',"; // ceres
				$cadenaSql .= " '11',"; // departamento del proyecto
				$cadenaSql .= " '11001',"; // municipio del proyecto
				$cadenaSql .= "'" . $variable ['PRO_CONSECUTIVO'] . "', ";
				$cadenaSql .= " '01',"; // pago
				$cadenaSql .= "'" . $variable ['TIPO_DOC_UNICO'] . "', ";
				$cadenaSql .= " '11001',"; // municipio donde se dicta
				$cadenaSql .= " '11',"; // departamento donde se dicta
				$cadenaSql .= " '1301',"; // ceres
				$cadenaSql .= "'02', "; // estudiante_articulacion, si es de bachillerato 01 si 02 no
				$cadenaSql .= "'0', "; // grado que cursa - cero si no es de articulación
				$cadenaSql .= "'' ";
				$cadenaSql .= " )";
				
				break;
			
			// EGRESADO SNIES
			
			case "consultarGraduadoAcademica" :
				
				$cadenaSql = " SELECT ";
				$cadenaSql .= " EST_COD CODIGO_ESTUDIANTE,";
				$cadenaSql .= " TO_CHAR('1301') ies_code,";
				$cadenaSql .= " EST_NOMBRE,";
				$cadenaSql .= " TO_CHAR(eot_fecha_nac, 'yyyy-mm-dd') fecha_nacim,";
				$cadenaSql .= " TO_CHAR('CO') pais_ln,";
				$cadenaSql .= " TO_CHAR(DECODE (mun_dep_cod,0,11,'',11, mun_dep_cod)) departamento_ln,";
				$cadenaSql .= " TO_CHAR(DECODE (mun_cod,0,11001,'',11001,99999,11001, mun_cod)) municipio_ln,";
				$cadenaSql .= " TO_CHAR(DECODE(est_sexo,'M','01','F','02','01')) genero_code,";
				$cadenaSql .= " eot_email email,";
				$cadenaSql .= " DECODE(eot_estado_civil,1,'01',2,'02',3,'05',4,'03',5,'04', '01') est_civil_code,";
				$cadenaSql .= " TO_CHAR(DECODE(est_tipo_iden,'C', 'CC', 'T', 'TI', 'E', 'CE', 'P', 'PS')) tipo_doc_unico,";
				$cadenaSql .= " TO_CHAR(est_nro_iden) codigo_unico,";
				$cadenaSql .= " TO_CHAR(DECODE(est_tipo_iden_ant,'C', 'CC', 'T', 'TI', 'E', 'CE', 'P', 'PS')) tipo_id_ant,";
				$cadenaSql .= " est_nro_iden_ant codigo_id_ant,";
				$cadenaSql .= " '57' pais_tel,";
				$cadenaSql .= " '1' area_tel,";
				$cadenaSql .= " TO_CHAR(est_telefono) numero_tel,";
				$cadenaSql .= " as_cra_cod_snies pro_consecutivo,";
				$cadenaSql .= " DECODE(LENGTH(est_cod),7,(SUBSTR(est_cod,1,2)+1900),11,(SUBSTR(est_cod, 1,4))) anio,";
				$cadenaSql .= " DECODE(DECODE(LENGTH(est_cod),7,((SUBSTR(est_cod,3,1))),11,(SUBSTR(est_cod, 5,1))), '1','01','02') semestre,";
				$cadenaSql .= " '02' es_transferencia,";
				$cadenaSql .= " DECODE(CRA_JORNADA, 'DIURNA', '01', 'NOCTURNA', '02', '01' ) HORARIO_CODE,";
				$cadenaSql .= " '01' PAGO,";
				$cadenaSql .= " egr_fecha_grado FECHA_GRADO,";
				$cadenaSql .= " TO_NUMBER(TO_CHAR(egr_fecha_grado,'yyyy')) GRAD_ANNIO,";
				$cadenaSql .= " DECODE(TO_NUMBER(TO_CHAR(egr_fecha_grado,'mm')),1,'01',2,'01',3,'01',4,'01',5,'01',6,'01',7,'02',8,'02',9,'02',10,'02',11,'02',12,'02') GRAD_SEMESTRE,";
				$cadenaSql .= " 'no' ECAES_OBSERVACIONES,";
				$cadenaSql .= " '0' ECAES_RESULTADOS,";
				$cadenaSql .= " '11' DEPARTAMENTO,"; // Donde se gradúa
				$cadenaSql .= " '11001' MUNICIPIO,"; // Donde se gradúa
				$cadenaSql .= " '1301' CODIGO_ENT_AULA,";
				$cadenaSql .= " EGR_ACTA_GRADO ACTA,";
				$cadenaSql .= " EGR_FOLIO FOLIO,";
				$cadenaSql .= " eot_nro_snp SNP";
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
				$cadenaSql .= " WHERE TO_NUMBER(TO_CHAR(egr_fecha_grado,'yyyy'))='" . $variable ['annio'] . "'";
				$cadenaSql .= " AND DECODE(TO_NUMBER(TO_CHAR(egr_fecha_grado,'mm')),1,1,2,1,3,1,4,1,5,1,6,1,7,3,8,3,9,3,10,3,11,3,12,3)='" . $variable ['semestre'] . "'";
				
				break;
			
			case "borrarEgresado" :
				$cadenaSql = "DELETE FROM";
				$cadenaSql .= " egresado ";
				$cadenaSql .= " WHERE codigo_unico='" . $variable ['CODIGO_UNICO'] . "'";
				$cadenaSql .= " AND tipo_doc_unico='" . $variable ['TIPO_DOC_UNICO'] . "'";
				
				break;
			
			// GRADUADO SNIES
			
			case "borrarGraduadoPeriodoTodos" :
				$cadenaSql = "DELETE FROM";
				$cadenaSql .= " graduado ";
				$cadenaSql .= " WHERE grad_annio='" . $variable ['ANNIO_GRADO'] . "'";
				$cadenaSql .= " AND grad_semestre='" . $variable ['SEMESTRE_GRADO'] . "'";
				
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
				$cadenaSql .= "'" . $variable ['IES_CODE'] . "', ";
				$cadenaSql .= "'" . $variable ['GRAD_ANNIO'] . "', ";
				$cadenaSql .= "'" . $variable ['GRAD_SEMESTRE'] . "', ";
				$cadenaSql .= "'" . $variable ['CODIGO_UNICO'] . "', ";
				$cadenaSql .= "'" . $variable ['PRO_CONSECUTIVO'] . "', ";
				$cadenaSql .= "'" . $variable ['FECHA_GRADO'] . "', ";
				$cadenaSql .= "'" . $variable ['ECAES_OBSERVACIONES'] . "', ";
				$cadenaSql .= "'" . $variable ['ECAES_RESULTADOS'] . "', ";
				$cadenaSql .= "'" . $variable ['DEPARTAMENTO'] . "', ";
				$cadenaSql .= "'" . $variable ['MUNICIPIO'] . "', ";
				$cadenaSql .= "'" . $variable ['CODIGO_ENT_AULA'] . "', ";
				$cadenaSql .= "'" . $variable ['ACTA'] . "', ";
				$cadenaSql .= "'" . $variable ['FOLIO'] . "', ";
				$cadenaSql .= "'" . $variable ['TIPO_DOC_UNICO'] . "', ";
				$cadenaSql .= "'" . $variable ['SNP'] . "' ";
				$cadenaSql .= " )";
				
				break;
			
			case "borrarGraduado" :
				$cadenaSql = "DELETE FROM";
				$cadenaSql .= " graduado ";
				$cadenaSql .= " WHERE codigo_unico='" . $variable ['CODIGO_UNICO'] . "'";
				$cadenaSql .= " AND tipo_doc_unico='" . $variable ['TIPO_DOC_UNICO'] . "'";
				
				break;
		}
		
		return $cadenaSql;
	}
}
?>
