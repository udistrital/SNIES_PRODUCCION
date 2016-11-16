<?php

namespace component\GestorInscritoAdmitido;

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
			 * ***************************
			 *
			 * INSCRITOS ACADEMICA
			 *
			 * ***************************
			 */

			case "consultarInscritoPregradoAcademica" :
				$prefijo = "mntac.";
				$cadenaSql = "SELECT UNIQUE ";
				$cadenaSql .= "asp_ape_ano ins_annio, ";
				$cadenaSql .= "DECODE(asp_ape_per,1,'1',3,'2', asp_ape_per) ins_semestre, ";
				$cadenaSql .= "DECODE(asp_tip_doc,'',DECODE(length(asp_nro_iden),11,'TI',12,'TI','CC'),'C', 'CC', '1', 'CC', 'c', 'CC', 'T', 'TI', '2', 'TI', 't', 'TI', 'E', 'CE', 'P', 'PS', 'CC') tipo_ident_code, ";
				$cadenaSql .= "asp_nro_iden documento, ";
				$cadenaSql .= "asp_apellido apellido, ";
				// en este campo están los apellidos
				$cadenaSql .= "asp_nombre nombre, ";
				// en este campo estan los nombres
				$cadenaSql .= "TO_CHAR(DECODE(asp_sexo,'M','1','F','2','1')) genero, ";
				$cadenaSql .= "as_cra_cod_snies prog_prim_opc, ";
				$cadenaSql .= "'11001' municipio ";
				//$cadenaSql .= "TO_CHAR(DECODE(asp_snp,'','N/A',NULL,'N/A',replace(asp_snp,' ',''))) snp,";
				//$cadenaSql .= "'11' departamento ";
				//$cadenaSql .= "as_cra_nom prog ";
				$cadenaSql .= "FROM " . $prefijo . "accra_snies ";
				$cadenaSql .= "INNER JOIN " . $prefijo . "accra ON cra_cod = as_cra_cod ";
				$cadenaSql .= "INNER JOIN " . $prefijo . "acasp ON cra_cod = asp_cra_cod ";
				$cadenaSql .= "INNER JOIN " . $prefijo . "actipcra ON cra_tip_cra = tra_cod ";
				$cadenaSql .= "WHERE  ";
				$cadenaSql .= "asp_ape_ano=" . $variable['annio'];
				if ($variable['semestre'] == 1) {
					$cadenaSql .= " AND asp_ape_per='1'";
				} else {
					$cadenaSql .= " AND asp_ape_per=3";
				}
				$cadenaSql .= " AND tra_nivel IN ('PREGRADO') ";
				//$cadenaSql .= " AND asp_nro_iden = 99070301136 ";///COMENTAR ESTA LINEA inscrito de 2016 2

				break;

			case "consultarInscritoPostgradoAcademica" :
				$prefijo = "mntac.";
				$cadenaSql = "SELECT UNIQUE ";
				$cadenaSql .= "mat_ano ins_annio, ";
				$cadenaSql .= "DECODE(mat_per,1,'1',3,'2', mat_per) ins_semestre, ";
				$cadenaSql .= "DECODE(est_tipo_iden,'',DECODE(length(est_nro_iden),11,'TI',12,'TI','CC'),'C', 'CC', '1', 'CC', 'c', 'CC', 'T', 'TI', '2', 'TI', 't', 'TI', 'E', 'CE', 'P', 'PS', 'CC') tipo_ident_code, ";
				$cadenaSql .= "est_nro_iden  documento, ";
				$cadenaSql .= "est_nombre nombre, ";
				// en este campo estan los nombres y apellidos
				$cadenaSql .= "TO_CHAR(DECODE(est_sexo,'M','1','F','2','1')) genero, ";
				$cadenaSql .= "as_cra_cod_snies prog_prim_opc,  ";
				$cadenaSql .= "'11001' municipio ";
				//$cadenaSql .= "TO_CHAR(DECODE(eot_nro_snp,'','N/A',NULL,'N/A',replace(eot_nro_snp,' ',''))) snp,";
				//$cadenaSql .= "'11' departamento, ";
				//$cadenaSql .= "as_cra_nom prog ";
				$cadenaSql .= "FROM " . $prefijo . "acest ";
				$cadenaSql .= "INNER JOIN " . $prefijo . "acestotr ON est_cod = eot_cod  ";
				$cadenaSql .= "INNER JOIN " . $prefijo . "v_tot_matri_ape_per ON est_cod = mat_est_cod ";
				$cadenaSql .= "INNER JOIN " . $prefijo . "accra ON cra_cod = mat_cra_cod ";
				$cadenaSql .= "INNER JOIN " . $prefijo . "actipcra ON cra_tip_cra = tra_cod ";
				$cadenaSql .= "INNER JOIN " . $prefijo . "accra_snies ON as_cra_cod = mat_cra_cod ";
				$cadenaSql .= "WHERE  ";
				$cadenaSql .= "mat_ano=" . $variable['annio'];
				if ($variable['semestre'] == 1) {
					$cadenaSql .= " AND mat_per='1'";
				} else {
					$cadenaSql .= " AND mat_per=3";
				}
				$cadenaSql .= "AND SUBSTR(est_cod,0,4)=mat_ano ";
				$cadenaSql .= "AND SUBSTR(est_cod,5,1)=DECODE(mat_per,1,'1',3,'2',mat_per) ";
				$cadenaSql .= "AND tra_nivel IN ('DOCTORADO','MAESTRIA','POSGRADO') ";
				//$cadenaSql .= " AND est_nro_iden = 1023925664 ";///COMENTAR ESTA LINEA inscrito de 2016 2

				break;

			/**
			 * ***************************
			 *
			 * INSCRITOS SNIES LOCAL
			 *
			 * ***************************
			 */

			case "contarInscritos" :
				$cadenaSql = "SELECT COUNT(documento) FROM";
				$cadenaSql .= " inscrito ";
				$cadenaSql .= " WHERE ins_annio=" . $variable['annio'];
				$cadenaSql .= " AND ins_semestre='" . $variable['semestre'] . "'";

				break;
				
			//consulta todos los inscritos de la tabla poblacion.inscrito				
			case "consultarInscritoSnies" :
				$cadenaSql = " SELECT";
				$cadenaSql .= " ano, semestre, id_tipo_documento, num_documento, primer_nombre, 
      						 segundo_nombre, primer_apellido, segundo_apellido, id_sexo_biologico";
				$cadenaSql .= " FROM";
				$cadenaSql .= " inscritos ";
				$cadenaSql .= " WHERE ano='" . $variable['annio']."'";
				$cadenaSql .= " AND semestre='" . $variable['semestre']."'";
					
				break;				

				
			//consulta todos los inscritos de la tabla poblacion.inscrito_programa			
			case "consultarInscritoProgramaSnies" :
				$cadenaSql = " SELECT";
				$cadenaSql .= " ano, semestre, id_tipo_documento, num_documento, pro_consecutivo, id_municipio";       
				$cadenaSql .= " FROM";
				$cadenaSql .= " inscrito_programa ";
				$cadenaSql .= " WHERE ano='" . $variable['annio']."'";
				$cadenaSql .= " AND semestre='" . $variable['semestre']."'";				
					
				break;					
				
			//Borra inscritos de un año y semestre especifico
			case "borrarInscritoSnies" :
				$cadenaSql = "DELETE FROM";
				$cadenaSql .= " inscritos ";
				$cadenaSql .= " WHERE ano='" . $variable['annio'] . "'";
				$cadenaSql .= " AND semestre='" . $variable['semestre'] . "'";

				break;

			case "borrarInscritoProgramaSnies" :
				$cadenaSql = "DELETE FROM";
				$cadenaSql .= " inscrito_programa ";
				$cadenaSql .= " WHERE ano='" . $variable['annio'] . "'";
				$cadenaSql .= " AND semestre='" . $variable['semestre'] . "'";

				break;

			case "insertarInscritoSnies" :
				$cadenaSql = "INSERT INTO ";
				$cadenaSql .= "inscritos ";
				$cadenaSql .= "(";
				$cadenaSql .= "ano, ";
				$cadenaSql .= "semestre,";
				$cadenaSql .= "id_tipo_documento,";
				$cadenaSql .= "num_documento,";
				$cadenaSql .= "primer_nombre, ";
				$cadenaSql .= "segundo_nombre, ";
				$cadenaSql .= "primer_apellido, ";
				$cadenaSql .= "segundo_apellido, ";
				$cadenaSql .= "id_sexo_biologico ";
				$cadenaSql .= ") ";
				$cadenaSql .= "VALUES ";
				$cadenaSql .= "(";
				$cadenaSql .= "'" . $variable['INS_ANNIO'] . "', ";
				$cadenaSql .= "'" . $variable['INS_SEMESTRE'] . "', ";
				$cadenaSql .= "'" . $variable['TIPO_IDENT_CODE'] . "', ";
				$cadenaSql .= "'" . $variable['DOCUMENTO'] . "', ";
				$cadenaSql .= "'" . $variable['PRIMER_NOMBRE'] . "', ";
				$cadenaSql .= "'" . $variable['SEGUNDO_NOMBRE'] . "', ";
				$cadenaSql .= "'" . $variable['PRIMER_APELLIDO'] . "', ";
				$cadenaSql .= "'" . $variable['SEGUNDO_APELLIDO'] . "', ";
				$cadenaSql .= "'" . $variable['GENERO'] . "' ";
				$cadenaSql .= "); ";

				break;

			case "insertarInscritoProgramaSnies" :
				$cadenaSql = "INSERT INTO ";
				$cadenaSql .= "inscrito_programa ";
				$cadenaSql .= "(";
				$cadenaSql .= "ano, ";
				$cadenaSql .= "semestre,";
				$cadenaSql .= "id_tipo_documento,";
				$cadenaSql .= "num_documento,";
				$cadenaSql .= "pro_consecutivo, ";
				$cadenaSql .= "id_municipio ";
				$cadenaSql .= ") ";
				$cadenaSql .= "VALUES ";
				$cadenaSql .= "(";
				$cadenaSql .= "'" . $variable['INS_ANNIO'] . "', ";
				$cadenaSql .= "'" . $variable['INS_SEMESTRE'] . "', ";
				$cadenaSql .= "'" . $variable['TIPO_IDENT_CODE'] . "', ";
				$cadenaSql .= "'" . $variable['DOCUMENTO'] . "', ";
				$cadenaSql .= "'" . $variable['PROG_PRIM_OPC'] . "', ";
				$cadenaSql .= "'" . $variable['MUNICIPIO'] . "' ";
				$cadenaSql .= "); ";

				break;

			/**
			 * ***************************
			 *
			 * ADMITIDOS ACADEMICA
			 *
			 * ***************************
			 */

			case "consultarAdmitidoPregradoAcademica" :
				$prefijo = "mntac.";
				$cadenaSql = " SELECT UNIQUE ";
				$cadenaSql .= "asp_ape_ano annio, ";
				$cadenaSql .= "DECODE(asp_ape_per,1,'1',3,'2', asp_ape_per) semestre, ";
				$cadenaSql .= " DECODE(asp_tip_doc,'',DECODE(length(asp_nro_iden),11,'TI',12,'TI','CC'),'C', 'CC', '1', 'CC', 'c', 'CC', 'T', 'TI', '2', 'TI', 't', 'TI', 'E', 'CE', 'P', 'PS', 'CC') id_tipo_documento, ";
				$cadenaSql .= " asp_nro_iden num_documento, ";
				$cadenaSql .= " as_cra_cod_snies pro_consecutivo, ";
				$cadenaSql .= " '11001' id_municipio ";
				//$cadenaSql .= " asp_apellido apellido, ";
				//$cadenaSql .= " asp_nombre nombre, ";
				//$cadenaSql .= " TO_CHAR(DECODE(asp_snp,'','N/A',NULL,'N/A',replace(asp_snp,' ',''))) snp,";
				//$cadenaSql .= " '2014-09-01' fecha_snp, "; // Se debe incluir la fecha de presentación de SNP
				//$cadenaSql .= " TO_CHAR('1301') ies_code, ";
				//$cadenaSql .= " '11' departamento, ";
				//$cadenaSql .= " '1301' codigo_ent_aula, ";
				//$cadenaSql .= " TO_CHAR(DECODE(asp_sexo,'M','1','F','2','1')) genero, ";
				//$cadenaSql .= " as_cra_nom prog";
				$cadenaSql .= " FROM " . $prefijo . "accra_snies ";
				$cadenaSql .= " INNER JOIN " . $prefijo . "accra ON cra_cod = as_cra_cod ";
				$cadenaSql .= " INNER JOIN " . $prefijo . "acasp ON cra_cod = asp_cra_cod ";
				$cadenaSql .= " INNER JOIN " . $prefijo . "actipcra ON cra_tip_cra = tra_cod ";
				$cadenaSql .= " WHERE  ";
				$cadenaSql .= " as_estado = 'A' ";
				$cadenaSql .= " AND asp_admitido = 'A' ";
				$cadenaSql .= " AND asp_ape_ano=" . $variable['annio'] . " ";
				if ($variable['semestre'] == 1) {
					$cadenaSql .= " AND asp_ape_per='1'";
				} else {
					$cadenaSql .= " AND asp_ape_per=3";
				}
				$cadenaSql .= " AND tra_nivel IN ('PREGRADO') ";
				//$cadenaSql .= " AND ROWNUM <= 100 ";

				break;

			case "consultarAdmitidoPostgradoAcademica" :
				$prefijo = "mntac.";
				$cadenaSql = " SELECT UNIQUE ";
				$cadenaSql .= " mat_ano annio, ";
				$cadenaSql .= " DECODE(mat_per,1,'1',3,'2', mat_per) semestre, ";
				$cadenaSql .= " DECODE(est_tipo_iden,'',DECODE(length(est_nro_iden),11,'TI',12,'TI','CC'),'C', 'CC', '1', 'CC', 'c', 'CC', 'T', 'TI', '2', 'TI', 't', 'TI', 'E', 'CE', 'P', 'PS', 'CC') id_tipo_documento, ";
				$cadenaSql .= " est_nro_iden num_documento, ";
				$cadenaSql .= " as_cra_cod_snies pro_consecutivo, ";
				$cadenaSql .= " '11001' id_municipio ";
				//$cadenaSql .= " '2010-09-01' fecha_snp, "; // se debe buscar
				//$cadenaSql .= " TO_CHAR(DECODE(eot_nro_snp,'','N/A',NULL,'N/A',replace(eot_nro_snp,' ',''))) snp,";
				//$cadenaSql .= " TO_CHAR('1301') ies_code, ";
				//$cadenaSql .= " est_nombre nombre, ";
				//$cadenaSql .= " '11' departamento, ";
				//$cadenaSql .= " '1301' codigo_ent_aula, ";
				//$cadenaSql .= " TO_CHAR(DECODE(est_sexo,'M','1','F','2','1')) genero, ";
				//$cadenaSql .= " as_cra_nom prog ";
				$cadenaSql .= " FROM " . $prefijo . "acest ";
				$cadenaSql .= " INNER JOIN " . $prefijo . "acestotr ON est_cod = eot_cod ";
				$cadenaSql .= " INNER JOIN " . $prefijo . "v_tot_matri_ape_per ON est_cod = mat_est_cod ";
				$cadenaSql .= " INNER JOIN " . $prefijo . "accra ON cra_cod = mat_cra_cod ";
				$cadenaSql .= " INNER JOIN " . $prefijo . "actipcra ON cra_tip_cra = tra_cod ";
				$cadenaSql .= " INNER JOIN " . $prefijo . "accra_snies ON as_cra_cod = mat_cra_cod ";
				$cadenaSql .= " WHERE ";
				$cadenaSql .= " mat_ano=" . $variable['annio'] . " ";
				if ($variable['semestre'] == 1) {
					$cadenaSql .= " AND mat_per='1'";
				} else {
					$cadenaSql .= " AND mat_per=3";
				}
				$cadenaSql .= " AND SUBSTR(est_cod,0,4)=mat_ano ";
				$cadenaSql .= " AND SUBSTR(est_cod,5,1)=DECODE(mat_per,1,'1',3,'2',mat_per) ";
				$cadenaSql .= " AND tra_nivel IN ('DOCTORADO','MAESTRIA','POSGRADO') ";
				//$cadenaSql .= " AND ROWNUM <= 100 ";

				break;

			/**
			 * ***************************
			 *
			 *
			 * ADMITIDOS SNIES LOCAL
			 *
			 *
			 * ***************************
			 */

			case "contarAdmitidos" :
				$cadenaSql = "SELECT COUNT(documento) FROM";
				$cadenaSql .= " admitido ";
				$cadenaSql .= " WHERE adm_annio=" . $variable['annio'];
				$cadenaSql .= " AND adm_semestre='" . $variable['semestre'] . "'";

				break;

			case "borrarAdmitidoSnies" :
				$cadenaSql = "DELETE FROM";
				$cadenaSql .= " admitido ";
				$cadenaSql .= " WHERE ano=" . $variable['annio'];
				$cadenaSql .= " AND semestre='" . $variable['semestre'] . "'";

				break;

			case "insertaAdmitidoSnies" :
				$cadenaSql = " INSERT";
				$cadenaSql .= " INTO admitido";
				$cadenaSql .= " (";
				$cadenaSql .= " ano,";
				$cadenaSql .= " semestre,";
				$cadenaSql .= " id_tipo_documento,";
				$cadenaSql .= " num_documento,";
				$cadenaSql .= " pro_consecutivo,";
				$cadenaSql .= " id_municipio";
				$cadenaSql .= " )";
				$cadenaSql .= " VALUES";
				$cadenaSql .= " (";
				$cadenaSql .= "'" . $variable['ANNIO'] . "',";
				$cadenaSql .= "'" . $variable['SEMESTRE'] . "',";
				$cadenaSql .= "'" . $variable['ID_TIPO_DOCUMENTO'] . "',";
				$cadenaSql .= "'" . $variable['NUM_DOCUMENTO'] . "',";
				$cadenaSql .= "'" . $variable['PRO_CONSECUTIVO'] . "',";
				$cadenaSql .= " '11001'";
				$cadenaSql .= " );";

				break;
		}

		return $cadenaSql;
	}

}
?>
