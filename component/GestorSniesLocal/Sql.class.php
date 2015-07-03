<?php

namespace component\GestorSniesLocal;

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
			
			case "contarMatriculadosPrimerCurso" :
				$cadenaSql = "SELECT COUNT(*) FROM";
				$cadenaSql .= " estudiante_programa ";
				$cadenaSql .= " WHERE anio=" . $variable ['annio'];
				$cadenaSql .= " AND semestre='" . $variable ['semestre'] . "'";
				
				break;
			
			case "contarMatriculados" :
				$cadenaSql = "SELECT COUNT(*) FROM";
				$cadenaSql .= " matriculado ";
				$cadenaSql .= " WHERE est_annio=" . $variable ['annio'];
				$cadenaSql .= " AND est_semestre='" . $variable ['semestre'] . "'";
				
				break;
		}
		
		return $cadenaSql;
	}
}
?>
