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

			case "consultarInscritoAcademica" :
				$cadenaSql = " SELECT asp_ape_ano ano,";
				$cadenaSql .= " DECODE(asp_ape_per,1,'1',3,'2', asp_ape_per) semestre,";
				$cadenaSql .= " DECODE (asp_tip_doc_act, 1, 'CC', 2, 'TI', 3, 'CE') id_tipo_documento,";
				$cadenaSql .= " asp_nro_iden_act documento,";
				$cadenaSql .= " asp_apellido  apellido,";
				$cadenaSql .= " asp_nombre  nombre,";
				$cadenaSql .= " TO_CHAR(DECODE(asp_sexo,'M','1','F','2','1')) genero,";
				$cadenaSql .= " as_cra_cod_snies pro_consecutivo,";
				$cadenaSql .= " '11001' municipio,";
				$cadenaSql .= " TO_CHAR(DECODE(est_tipo_iden,'C', 'CC', 'T', 'TI', 'E', 'CE', 'P', 'PS')) est_tipo_iden,";
				$cadenaSql .= " est_nro_iden,";
				$cadenaSql .= " est_cod,";
				$cadenaSql .= " ead_estado,";
				$cadenaSql .= " 'PREGRADO' nivel";
				$cadenaSql .= " FROM acasp";
				$cadenaSql .= " LEFT JOIN acestadm";
				$cadenaSql .= " ON EAD_ASP_CRED=asp_cred";
				$cadenaSql .= " AND ead_asp_ano=asp_ape_ano";
				$cadenaSql .= " AND ead_asp_per=asp_ape_per";
				$cadenaSql .= " LEFT JOIN acest";
				$cadenaSql .= " ON est_cod =ead_cod";
				$cadenaSql .= " INNER JOIN accra";
				$cadenaSql .= " ON cra_cod=asp_cra_cod";
				$cadenaSql .= " INNER JOIN accra_snies";
				$cadenaSql .= " ON as_cra_cod =asp_cra_cod";
				$cadenaSql .= " WHERE asp_ape_ano=2016";
				$cadenaSql .= " AND asp_ape_per =3";
				
				$cadenaSql .= " AND est_cod=20162005230";//para buscar solo un iscrito de pregrado											
				
				$cadenaSql .= " UNION";
				$cadenaSql .= " SELECT UNIQUE mat_ano ano,";
				$cadenaSql .= " DECODE(mat_per,1,'1',3,'2', mat_per) semestre,";
				$cadenaSql .= " TO_CHAR(DECODE(est_tipo_iden,'C', 'CC', 'T', 'TI', 'E', 'CE', 'P', 'PS')) id_tipo_documento,";
				$cadenaSql .= " est_nro_iden documento,";
				$cadenaSql .= " '0' apellido,";
				$cadenaSql .= " est_nombre  nombre,";
				$cadenaSql .= " TO_CHAR(DECODE(est_sexo,'M','1','F','2','1')) genero,";
				$cadenaSql .= " as_cra_cod_snies pro_consecutivo,";
				$cadenaSql .= " '11001' municipio,";
				$cadenaSql .= " TO_CHAR(DECODE(est_tipo_iden,'C', 'CC', 'T', 'TI', 'E', 'CE', 'P', 'PS')) est_tipo_iden,";
				$cadenaSql .= " est_nro_iden,";
				$cadenaSql .= " est_cod,";
				$cadenaSql .= " 'A' ead_estado,";
				$cadenaSql .= " 'POSTGRADO' nivel";
				$cadenaSql .= " FROM mntac.acest";
				$cadenaSql .= " INNER JOIN mntac.acestotr";
				$cadenaSql .= " ON est_cod = eot_cod";
				$cadenaSql .= " INNER JOIN mntac.v_tot_matri_ape_per";
				$cadenaSql .= " ON est_cod = mat_est_cod";
				$cadenaSql .= " INNER JOIN mntac.accra";
				$cadenaSql .= " ON cra_cod = mat_cra_cod";
				$cadenaSql .= " INNER JOIN mntac.actipcra";
				$cadenaSql .= " ON cra_tip_cra = tra_cod";
				$cadenaSql .= " INNER JOIN mntac.accra_snies";
				$cadenaSql .= " ON as_cra_cod = mat_cra_cod";
				$cadenaSql .= " WHERE mat_ano =2016";
				$cadenaSql .= " AND mat_per =3";
				$cadenaSql .= " AND SUBSTR(est_cod,0,4)=mat_ano";
				$cadenaSql .= " AND SUBSTR(est_cod,5,1)=DECODE(mat_per,1,'1',3,'2',mat_per)";
				$cadenaSql .= " AND tra_nivel IN ('DOCTORADO','MAESTRIA','POSGRADO')";
				
				$cadenaSql .= " AND est_nro_iden= 2965707";//para buscar solo un iscrito de postgrado
				//echo $cadenaSql;exit;
				
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
				$cadenaSql .= " WHERE ano=" . $variable['annio'] . "";
				$cadenaSql .= " AND semestre=" . $variable['semestre'] . "";

				break;

			//consulta todos los inscritos de la tabla poblacion.inscrito_programa
			case "consultarInscritoProgramaSnies" :
				$cadenaSql = " SELECT";
				$cadenaSql .= " ano, semestre, id_tipo_documento, num_documento, pro_consecutivo, id_municipio";
				$cadenaSql .= " FROM";
				$cadenaSql .= " inscrito_programa ";
				$cadenaSql .= " WHERE ano=" . $variable['ano'];
				$cadenaSql .= " AND semestre=" . $variable['semestre'];

				break;

			//Borra inscritos de un año y semestre especifico
			case "borrarInscritoSnies" :
				$cadenaSql = "DELETE FROM";
				$cadenaSql .= " inscritos ";
				$cadenaSql .= " WHERE ano=" . $variable['ano'];
				$cadenaSql .= " AND semestre=" . $variable['semestre'];
				$cadenaSql .= " AND id_tipo_documento='" . $variable['id_tipo_documento'] . "'";
				$cadenaSql .= " AND num_documento=" . $variable['num_documento'];

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
				$cadenaSql .= $variable['ANO'] . ", ";
				$cadenaSql .= $variable['SEMESTRE'] . ", ";
				$cadenaSql .= "'" . $variable['ID_TIPO_DOCUMENTO'] . "', ";
				$cadenaSql .= "'" . $variable['DOCUMENTO'] . "', ";
				$cadenaSql .= "'" . $variable['PRIMER_NOMBRE'] . "', ";
				$cadenaSql .= "'" . $variable['SEGUNDO_NOMBRE'] . "', ";
				$cadenaSql .= "'" . $variable['PRIMER_APELLIDO'] . "', ";
				$cadenaSql .= "'" . $variable['SEGUNDO_APELLIDO'] . "', ";
				$cadenaSql .= $variable['GENERO'] . " ";
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
				$cadenaSql .= $variable['ANO'] . ", ";
				$cadenaSql .= $variable['SEMESTRE'] . ", ";
				$cadenaSql .= "'" . $variable['ID_TIPO_DOCUMENTO'] . "', ";
				$cadenaSql .= $variable['DOCUMENTO'] . ", ";
				$cadenaSql .= $variable['PRO_CONSECUTIVO'] . ", ";
				$cadenaSql .= $variable['MUNICIPIO'];
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
				$cadenaSql .= " decode (asp_tip_doc_act, 1, 'CC', 2, 'TI', 3, 'CE') id_tipo_documento, ";
				$cadenaSql .= " asp_nro_iden_act num_documento, ";
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
				//echo $cadenaSql;exit;

				break;

			case "consultarAdmitidoPostgradoAcademica" :
				$prefijo = "mntac.";
				$cadenaSql = " SELECT UNIQUE ";
				$cadenaSql .= " mat_ano annio, ";
				$cadenaSql .= " DECODE(mat_per,1,'1',3,'2', mat_per) semestre, ";
				$cadenaSql .= " TO_CHAR(DECODE(est_tipo_iden,'C', 'CC', 'T', 'TI', 'E', 'CE', 'P', 'PS')) id_tipo_documento,";
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

			case "consultarAdmitidoSnies" :
				$cadenaSql = "SELECT ano, semestre, id_tipo_documento, num_documento, pro_consecutivo, 
       id_municipio ";
				$cadenaSql .= " FROM ";
				$cadenaSql .= " admitido ";
				$cadenaSql .= " WHERE ano=" . $variable['ano'];
				$cadenaSql .= " AND semestre=" . $variable['semestre'];

				break;

			case "borrarAdmitidoSnies" :
				$cadenaSql = "DELETE FROM";
				$cadenaSql .= " admitido ";
				$cadenaSql .= " WHERE ano=" . $variable['annio'];
				$cadenaSql .= " AND semestre='" . $variable['semestre'] . "'";
				echo $cadenaSql;
				echo 'modificar el registro de un solo admitido';
				exit ;

				break;

			case "insertarAdmitidoSnies" :
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
				$cadenaSql .= "'" . $variable['ANO'] . "',";
				$cadenaSql .= "'" . $variable['SEMESTRE'] . "',";
				$cadenaSql .= "'" . $variable['ID_TIPO_DOCUMENTO'] . "',";
				$cadenaSql .= "'" . $variable['DOCUMENTO'] . "',";
				$cadenaSql .= "'" . $variable['PRO_CONSECUTIVO'] . "',";
				$cadenaSql .= " '11001'";
				$cadenaSql .= " );";

				break;
		}

		echo $cadenaSql.'<hr>';
		return $cadenaSql;
	}

}
?>
