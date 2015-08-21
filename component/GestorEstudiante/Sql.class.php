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
			
			case "matriculado" :
				$cadena_sql = "SELECT UNIQUE ";
				$cadena_sql .= "'1301' ies_code, ";
				$cadena_sql .= "'" . $valor [0] . "' ano, ";
				$cadena_sql .= "'" . $valor [1] . "' periodo, ";
				$cadena_sql .= " est_nro_iden identificacion, ";
				$cadena_sql .= "DECODE(cra_jornada, 'DIURNA', '01', 'NOCTURNA', '02', '01' ) horario_code, ";
				$cadena_sql .= "'1301' ceres, ";
				$cadena_sql .= "'11' departamento, ";
				$cadena_sql .= "'11001' municipio, ";
				$cadena_sql .= "as_cra_cod_snies, ";
				$cadena_sql .= "'01' pago, ";
				$cadena_sql .= "DECODE(est_tipo_iden,'',DECODE(length(est_nro_iden),11,'TI',12,'TI','CC'),'C', 'CC', '1', 'CC', 'c', 'CC', 'T', 'TI', '2', 'TI', 't', 'TI', 'E', 'CE', 'P', 'PS', 'CC') tipo_doc_unico, ";
				$cadena_sql .= " est_cod codigo, ";
				$cadena_sql .= " as_cra_nom ";
				$cadena_sql .= " FROM mntac.acest";
				$cadena_sql .= " INNER JOIN mntac.v_tot_matri_ape_per ON est_cod = mat_est_cod";
				$cadena_sql .= " INNER JOIN mntac.accra ON cra_cod = mat_cra_cod";
				$cadena_sql .= " INNER JOIN mntac.actipcra ON cra_tip_cra = tra_cod";
				$cadena_sql .= " INNER JOIN mntac.accra_snies ON as_cra_cod = mat_cra_cod";
				$cadena_sql .= " WHERE ";
				$cadena_sql .= " mat_ano=";
				$cadena_sql .= "'" . $valor [0] . "' ";
				$cadena_sql .= " AND mat_per=";
				$cadena_sql .= "'" . $valor [1] . "' ";
				$cadena_sql .= " AND est_nro_iden <> '101' ";
				$cadena_sql .= " AND est_estado_est <> 'N' ";
				break;
			
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
				
				break;
		}
		
		return $cadenaSql;
	}
}
?>
