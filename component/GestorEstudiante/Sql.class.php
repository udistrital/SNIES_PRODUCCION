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
			case "consultarEstudiante" :
				
				$cadenaSql = " SELECT TO_CHAR('1301') ies_code,";
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
				$cadenaSql .= " AND rownum < 10";
				
				break;
			
			case "consultarParticipante" :
				$cadenaSql = "SELECT codigo_unico FROM";
				$cadenaSql .= " participante ";
				$cadenaSql .= " WHERE codigo_unico=" . $variable ['CODIGO_UNICO'];
				
				break;
			
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
				$cadenaSql .= " tipo_doc_unico ='" . $variable ['TIPO_DOC_UNICO'] . "',";
				$cadenaSql .= " codigo_unico ='" . $variable ['CODIGO_UNICO'] . "',";
				$cadenaSql .= " tipo_id_ant ='" . $variable ['TIPO_ID_ANT'] . "',";
				$cadenaSql .= " codigo_id_ant ='" . $variable ['CODIGO_ID_ANT'] . "',";
				$cadenaSql .= " pais_tel ='" . $variable ['PAIS_TEL'] . "',";
				$cadenaSql .= " area_tel ='" . $variable ['AREA_TEL'] . "',";
				$cadenaSql .= " numero_tel ='" . $variable ['NUMERO_TEL'] . "'";
				$cadenaSql .= " WHERE codigo_unico='" . $variable ['CODIGO_UNICO'] . "'";
				
				break;
			
			case "borrarParticipanteEstudiante" :
				$cadenaSql = "DELETE FROM";
				$cadenaSql .= " participante ";
				$cadenaSql .= " WHERE codigo_unico=" . $variable ['CODIGO_UNICO'];
				
				break;
			
			case "registrarParticipanteEstudiante" :
				$cadenaSql = "INSERT INTO ";
				$cadenaSql .= "participante ";
				$cadenaSql .= "VALUES ";
				$cadenaSql .= "( ";
				$cadenaSql .= "'" . $variable ['IES_CODE'] . "', ";
				$cadenaSql .= "'" . $variable ['PRIMER_APELLIDO'] . "', ";
				$cadenaSql .= "'" . $variable ['SEGUNDO_APELLIDO'] . "', ";
				$cadenaSql .= "'" . $variable ['PRIMER_NOMBRE'] . "', ";
				$cadenaSql .= "'" . $variable ['SEGUNDO_NOMBRE'] . "', ";
				if (isset ( $variable ['FECHA_NACIM'] )) {
					$cadenaSql .= "'" . $variable ['FECHA_NACIM'] . "', ";
				} else {
					$cadenaSql .= "'1980-01-01', "; // este el valor por defecto se debe corregir en la base de datos academica
				}
				$cadenaSql .= "'" . $variable ['PAIS_LN'] . "', ";
				$cadenaSql .= "'" . $variable ['DEPARTAMENTO_LN'] . "', ";
				$cadenaSql .= "'" . $variable ['MUNICIPIO_LN'] . "', ";
				$cadenaSql .= "'" . $variable ['GENERO_CODE'] . "', ";
				if (isset ( $variable ['EMAIL'] )) {
					$cadenaSql .= "'" . $variable ['EMAIL'] . "', ";
				} else {
					$cadenaSql .= "'', ";
				}
				$cadenaSql .= "'" . $variable ['EST_CIVIL_CODE'] . "', ";
				$cadenaSql .= "'" . $variable ['TIPO_DOC_UNICO'] . "', ";
				$cadenaSql .= "'" . $variable ['CODIGO_UNICO'] . "', ";
				$cadenaSql .= "'" . $variable ['TIPO_ID_ANT'] . "', ";
				$cadenaSql .= "'" . $variable ['CODIGO_ID_ANT'] . "', ";
				$cadenaSql .= "'" . $variable ['PAIS_TEL'] . "', ";
				$cadenaSql .= "'" . $variable ['AREA_TEL'] . "', ";
				if (isset ( $variable ['NUMERO_TEL'] )) {
					$cadenaSql .= "'" . $variable ['NUMERO_TEL'] . "'";
				} else {
					$cadenaSql .= "''";
				}
				$cadenaSql .= " )";
				// echo $cadenaSql.'<br>';exit;
				break;
			
			case "borrarEstudianteSnies" :
				$cadenaSql = "DELETE FROM";
				$cadenaSql .= " estudiante ";
				$cadenaSql .= " WHERE codigo_unico=" . $variable ['CODIGO_UNICO'];
				echo $cadenaSql;
				break;
			
			case "consultarEstudiante" :
				$cadenaSql = " SELECT est_nro_iden codigo_unico,";
				$cadenaSql .= " TO_CHAR(DECODE(est_tipo_iden,'',DECODE(LENGTH(est_nro_iden),11,'TI',12,'TI','CC'),'C', 'CC', '1', 'CC', 'c', 'CC', 'T', 'TI', '2', 'TI', 't', 'TI', 'E', 'CE', 'P', 'PS', 'CC')) tipo_doc_unico";
				$cadenaSql .= " FROM acest  ";
				$cadenaSql .= " WHERE est_estado='A'";
				$cadenaSql .= " AND rownum < 100";
				
				break;
			
			case "consultarEstudianteSNIES" :
				$cadenaSql = " SELECT codigo_unico,";
				$cadenaSql .= " tipo_doc_unico";
				$cadenaSql .= " FROM estudiante";
				$cadenaSql .= " WHERE ";
				$cadenaSql .= " codigo_unico=" . "'$variable ['codigo_unico']'";
				echo $cadenaSql;
				
				break;
				
				// case "contarMatriculadosPrimerCurso" :
				// $cadenaSql = "SELECT COUNT(*) FROM";
				// $cadenaSql .= " estudiante_programa ";
				// $cadenaSql .= " WHERE anio=" . $variable ['annio'];
				// $cadenaSql .= " AND semestre='" . $variable ['semestre'] . "'";
				
				// break;
				
				// case "contarMatriculados" :
				// $cadenaSql = "SELECT COUNT(*) FROM";
				// $cadenaSql .= " matriculado ";
				// $cadenaSql .= " WHERE est_annio=" . $variable ['annio'];
				// $cadenaSql .= " AND est_semestre='" . $variable ['semestre'] . "'";
				
				break;
		}
		
		return $cadenaSql;
	}
}
?>
