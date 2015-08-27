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
			
			// CONSULTAS BASE DE DATOS SNIES LOCAL - POSTGRES
			
			case "contarInscritos" :
				$cadenaSql = "SELECT COUNT(*) FROM";
				$cadenaSql .= " inscrito ";
				$cadenaSql .= " WHERE ins_annio=" . $variable ['annio'];
				$cadenaSql .= " AND ins_semestre='" . $variable ['semestre'] . "'";
				
				break;
			
			case "contarAdmitidos" :
				$cadenaSql = "SELECT COUNT(*) FROM";
				$cadenaSql .= " admitido ";
				$cadenaSql .= " WHERE adm_annio=" . $variable ['annio'];
				$cadenaSql .= " AND adm_semestre='" . $variable ['semestre'] . "'";
				
				break;
			
			/**
			 * CONSULTA BASE DE DATOS INSTITUCIONAL
			 * Datos necesarios para la tabla participante de estudiantes
			 * se consulta los estudiantes que pagaron matrícula en un período
			 */
			
			case "consultarParticipanteEstudiante" :
				$cadenaSql = " SELECT TO_CHAR('1301') IES_CODE,";
				$cadenaSql .= " EST_NOMBRE,";
				$cadenaSql .= " TO_CHAR(eot_fecha_nac, 'yyyy-mm-dd') fecha_nacim,";
				$cadenaSql .= " TO_CHAR('CO') pais_ln,";
				$cadenaSql .= " TO_CHAR(DECODE (mun_dep_cod,0,11,'',11, mun_dep_cod)) departamento_ln,";
				$cadenaSql .= " TO_CHAR(DECODE (mun_cod,0,11001,'',11001,99999,11001, mun_cod)) municipio_ln,";
				$cadenaSql .= " TO_CHAR(DECODE(est_sexo,'M','01','F','02','01')) genero_code,";
				$cadenaSql .= " eot_email email,";
				$cadenaSql .= " DECODE(eot_estado_civil,1,'01',2,'02',3,'05',4,'03',5,'04', '01') est_civil_code,";
				$cadenaSql .= " TO_CHAR(DECODE(est_tipo_iden,'',DECODE(LENGTH(est_nro_iden),11,'TI',12,'TI','CC'),'C', 'CC', '1', 'CC', 'c', 'CC', 'T', 'TI', '2', 'TI', 't', 'TI', 'E', 'CE', 'P', 'PS', 'CC')) tipo_doc_unico,";
				$cadenaSql .= " TO_CHAR(est_nro_iden) codigo_unico,";
				$cadenaSql .= " TO_CHAR(DECODE(est_tipo_iden_ant,'',DECODE(LENGTH(est_nro_iden_ant),11,'TI',12,'TI','CC'),'C', 'CC', '1', 'CC', 'c', 'CC', 'T', 'TI', '2', 'TI', 't', 'TI', 'E', 'CE', 'P', 'PS', 'CC')) tipo_id_ant,";
				$cadenaSql .= " est_nro_iden_ant codigo_id_ant,";
				$cadenaSql .= " '57' pais_tel,";
				$cadenaSql .= " '1' area_tel,";
				$cadenaSql .= " TO_CHAR(est_telefono) numero_tel,";
				$cadenaSql .= " est_cod codigo";
				$cadenaSql .= " FROM mntac.acest";
				$cadenaSql .= " INNER JOIN mntac.acestotr";
				$cadenaSql .= " ON est_cod = eot_cod";
				$cadenaSql .= " INNER JOIN mntge.gemunicipio";
				$cadenaSql .= " ON MUN_COD=DECODE(EOT_COD_MUN_NAC,0,11001,'',11001,EOT_COD_MUN_NAC)";
				$cadenaSql .= " INNER JOIN mntac.v_tot_matri_ape_per ON est_cod = mat_est_cod";
				$cadenaSql .= " WHERE ";
				$cadenaSql .= " mat_ano=" . $variable ['annio'];
				$cadenaSql .= " AND mat_per=" . $variable ['semestre']; // el semestre 03 de la universidad es el semestre 02 de SNIES
				$cadenaSql .= " AND rownum < 1000"; // solo procesa 5 registros
				
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
				//echo $cadenaSql.'<br>';
				break;
		}
		
		return $cadenaSql;
	}
}
?>
