<?php

namespace sniesDocente;

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
			
			// Datos del docente tomados de la base de datos académica
			case "consultarDocenteAcademica" :
				
				$cadenaSql = " select unique TO_CHAR('1301') IES_CODE,";
				$cadenaSql .= " doc_apellido,";
				$cadenaSql .= " doc_nombre,";
				$cadenaSql .= " TO_DATE(doc_fecha_nac) fecha_nacim,";
				$cadenaSql .= " 'CO' pais_ln,";
				$cadenaSql .= " '11' departamento_ln,";
				$cadenaSql .= " '11001' municipio_ln,";
				$cadenaSql .= " TO_CHAR(DECODE(doc_sexo,'M','01','F','02','01')) genero_code,";
				$cadenaSql .= " doc_email email,";
				$cadenaSql .= " DECODE(doc_estado_civil,1,'01',2,'02',3,'05',4,'03',5,'04','','01') est_civil_code,";
				$cadenaSql .= " DECODE(DOC_TIP_IDEN,'',DECODE(LENGTH(doc_nro_iden),11,'TI','CC'),'C', 'CC', 'T','TI', 'E', 'CE', 'P', 'PS', DOC_TIP_IDEN ) tipo_doc_unico,";
				$cadenaSql .= " TO_CHAR(doc_nro_iden) codigo_unico,";
				$cadenaSql .= " DECODE(DOC_TIP_IDEN,'',DECODE(LENGTH(doc_nro_iden),11,'TI','CC'),'C', 'CC', 'T','TI', 'E', 'CE', 'P', 'PS', DOC_TIP_IDEN ) tipo_id_ant,";
				$cadenaSql .= " TO_CHAR(doc_nro_iden) codigo_id_ant,";
				$cadenaSql .= " '57' pais_tel,";
				$cadenaSql .= " '1' AREA_TEL,";
				$cadenaSql .= " TO_CHAR(DOC_TELEFONO) NUMERO_TEL,";
				$cadenaSql .= " DECODE(DOC_NIVEL_ESTUDIO, 'postdoctorado', '01', 'doctorado', '02', 'maestria', '03', 'especializacion', '04', 'pregrado', '05', 'licenciatura', '06', 'tecnologia', '07', 'tecnico', '08', '05') NIVEL_EST_CODE,";
				$cadenaSql .= " DECODE(DOC_FECHA_DESDE,'0','1980-02-29','','1980-02-29', TO_CHAR(DOC_FECHA_DESDE, 'yyyy-mm-dd')) FECHA_INGRESO";
				$cadenaSql .= " FROM mntac.acdocente";
				$cadenaSql .= " WHERE doc_nro_iden IN";
				$cadenaSql .= " (SELECT DISTINCT car_doc_nro AS car_doc_nro_iden";
				$cadenaSql .= " FROM accursos";
				$cadenaSql .= " INNER JOIN mntac.achorarios";
				$cadenaSql .= " ON hor_id_curso=cur_id";
				$cadenaSql .= " INNER JOIN mntac.accargas";
				$cadenaSql .= " ON car_hor_id =hor_id";
				$cadenaSql .= " WHERE cur_estado='A'";
				$cadenaSql .= " AND car_estado ='A'";
				$cadenaSql .= " AND cur_ape_ano='" . $variable ['annio'] . "'";
				$cadenaSql .= " AND cur_ape_per='" . $variable ['semestre'] . "'";
				$cadenaSql .= " UNION";
				$cadenaSql .= " SELECT DISTINCT car_doc_nro_iden";
				$cadenaSql .= " FROM MNTAC.ACCARGAHIS";
				$cadenaSql .= " WHERE car_ape_ano='" . $variable ['annio'] . "'";
				$cadenaSql .= " AND car_ape_per='" . $variable ['semestre'] . "'";
				$cadenaSql .= " AND car_estado='A'";
				$cadenaSql .= " )";
				//$cadenaSql .= " AND doc_nro_iden=79708124";
				
				
				break;
			
			// Datos del docente tomados de la base de datos académica
			case "consultarVinculacionDocente" :
				
				$cadenaSql = " SELECT dtv_ape_ano ANO,";
				$cadenaSql .= " DTV_APE_PER SEMESTRE ,";
				$cadenaSql .= " DTV_DOC_NRO_IDEN DOCUMENTO,";
				$cadenaSql .= " TVI_COD VINCULACION,";
				$cadenaSql .= " tvi_nombre NOMBRE_VINCULACION";
				$cadenaSql .= " FROM acdoctipvin";
				$cadenaSql .= " INNER JOIN actipvin";
				$cadenaSql .= " ON tvi_cod = dtv_tvi_cod";
				$cadenaSql .= " WHERE dtv_ape_ano='" . $variable ['annio'] . "'";
				$cadenaSql .= " AND dtv_ape_per ='" . $variable ['semestre'] . "'";
				
				break;
		}
		
		return $cadenaSql;
	}
}
?>
