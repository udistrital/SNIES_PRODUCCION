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
				// $cadenaSql .= " AND doc_nro_iden=3182871";
				$cadenaSql .= " AND doc_nro_iden=79708124";
				
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
			
			// //DOCENTE SNIES
			
			case "consultarDocente" :
				$cadenaSql = "SELECT codigo_unico, tipo_doc_unico, nivel_est_code, fecha_ingreso FROM";
				$cadenaSql .= " docente ";
				$cadenaSql .= " WHERE codigo_unico='" . $variable ['CODIGO_UNICO'] . "'";
				
				break;
			
			case "actualizarDocente" :
				$cadenaSql = " UPDATE docente";
				$cadenaSql .= " SET ";
				$cadenaSql .= " ies_code ='" . $variable ['IES_CODE'] . "',";
				$cadenaSql .= " tipo_doc_unico ='" . $variable ['TIPO_DOC_UNICO'] . "',";
				$cadenaSql .= " nivel_est_code ='" . $variable ['NIVEL_EST_CODE'] . "',";
				$cadenaSql .= " fecha_ingreso ='" . $variable ['FECHA_INGRESO'] . "'";
				$cadenaSql .= " WHERE codigo_unico='" . $variable ['CODIGO_UNICO'] . "'";
				
				break;
			
			case "registrarDocente" :
				$cadenaSql = " INSERT";
				$cadenaSql .= " INTO docente";
				$cadenaSql .= " (";
				$cadenaSql .= " ies_code,";
				$cadenaSql .= " codigo_unico,";
				$cadenaSql .= " nivel_est_code,";
				$cadenaSql .= " tipo_doc_unico,";
				$cadenaSql .= " fecha_ingreso";
				$cadenaSql .= " )";
				$cadenaSql .= " VALUES";
				$cadenaSql .= " (";
				$cadenaSql .= "'" . $variable ['IES_CODE'] . "', ";
				$cadenaSql .= "'" . $variable ['CODIGO_UNICO'] . "', ";
				$cadenaSql .= "'" . $variable ['NIVEL_EST_CODE'] . "', ";
				$cadenaSql .= "'" . $variable ['TIPO_DOC_UNICO'] . "', ";
				$cadenaSql .= "'" . $variable ['FECHA_INGRESO'] . "' ";
				$cadenaSql .= " );";
				
				break;
			
			case "borrarDocente" :
				$cadenaSql = "DELETE FROM";
				$cadenaSql .= " docente ";
				$cadenaSql .= " WHERE codigo_unico='" . $variable ['CODIGO_UNICO'] . "'";
				$cadenaSql .= " AND tipo_doc_unico='" . $variable ['TIPO_DOC_UNICO'] . "'";
				
				break;
			
			// //DOCENTE_H SNIES
			
			case "consultarDocente_h" :
				$cadenaSql = "SELECT codigo_unico, tipo_doc_unico FROM";
				$cadenaSql .= " docente_h ";
				$cadenaSql .= " WHERE codigo_unico='" . $variable ['CODIGO_UNICO'] . "'";
				
				break;
			
			case "registrarDocente_h" :
				$cadenaSql = " INSERT";
				$cadenaSql .= " INTO docente_h";
				$cadenaSql .= " (";
				$cadenaSql .= " ies_code,";
				$cadenaSql .= " annio,";
				$cadenaSql .= " semestre,";
				$cadenaSql .= " cod_uni_org,";
				$cadenaSql .= " codigo_unico,";
				$cadenaSql .= " dedicacion,";
				$cadenaSql .= " porcentaje_docencia,";
				$cadenaSql .= " porcentaje_investigacion,";
				$cadenaSql .= " porcentaje_administrativa,";
				$cadenaSql .= " porcentaje_bienestar,";
				$cadenaSql .= " porcentaje_edu_no_formal_ycont,";
				$cadenaSql .= " porcentaje_proy_progr_remun,";
				$cadenaSql .= " porcentaje_proy_no_remun,";
				$cadenaSql .= " tipo_contrato,";
				$cadenaSql .= " premios_semestre_nal,";
				$cadenaSql .= " libros_publ_texto_calificados,";
				$cadenaSql .= " premios_semestre_internal,";
				$cadenaSql .= " duracion_en_horas,";
				$cadenaSql .= " tipo_doc_unico,";
				$cadenaSql .= " porcentaje_otras_actividades,";
				$cadenaSql .= " libros_pub_investigacion,";
				$cadenaSql .= " libros_pub_texto,";
				$cadenaSql .= " reportes_investigacion,";
				$cadenaSql .= " patentes_obtenidas_semestre,";
				$cadenaSql .= " redes_academicas,";
				$cadenaSql .= " docente_ceres,";
				$cadenaSql .= " certificacion_tic";
				$cadenaSql .= " )";
				$cadenaSql .= " VALUES";
				$cadenaSql .= " (";
				$cadenaSql .= "'" . $variable ['IES_CODE'] . "', ";
				$cadenaSql .= "'" . $variable ['ANNIO'] . "', ";
				$cadenaSql .= "'" . $variable ['SEMESTRE'] . "', ";//Unidad Organizacional
				$cadenaSql .= "'" . $variable ['ANNIO'] . "', ";
				$cadenaSql .= "'" . $variable ['ANNIO'] . "', ";
				$cadenaSql .= "'" . $variable ['ANNIO'] . "', ";
				$cadenaSql .= "'" . $variable ['ANNIO'] . "', ";
				$cadenaSql .= "'" . $variable ['ANNIO'] . "', ";
				$cadenaSql .= "'" . $variable ['ANNIO'] . "', ";
				$cadenaSql .= "'" . $variable ['ANNIO'] . "', ";
				$cadenaSql .= "'" . $variable ['ANNIO'] . "', ";
				$cadenaSql .= "'" . $variable ['ANNIO'] . "', ";
				$cadenaSql .= "'" . $variable ['ANNIO'] . "', ";
				$cadenaSql .= "'" . $variable ['ANNIO'] . "', ";
				$cadenaSql .= "'" . $variable ['ANNIO'] . "', ";
				$cadenaSql .= "'" . $variable ['ANNIO'] . "', ";
				$cadenaSql .= "'" . $variable ['ANNIO'] . "', ";
				
				var_dump ( $variable );
				echo $cadenaSql;
				exit ();
				
				break;
			
			case "borrarDocente_h" :
				$cadenaSql = "DELETE FROM";
				$cadenaSql .= " docente_h ";
				$cadenaSql .= " WHERE codigo_unico='" . $variable ['CODIGO_UNICO'] . "'";
				$cadenaSql .= " AND tipo_doc_unico='" . $variable ['TIPO_DOC_UNICO'] . "'";
				
				break;
			
			case "borrarDocente_hPeriodoTodos" :
				$cadenaSql = "DELETE FROM";
				$cadenaSql .= " docente_h ";
				$cadenaSql .= " WHERE annio='" . $variable ['ANNIO'] . "'";
				$cadenaSql .= " AND semestre='" . $variable ['SEMESTRE'] . "'";
				
				break;
		}
		
		return $cadenaSql;
	}
}
?>
