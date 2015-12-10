<?php

namespace sniesDocente;

use sniesDocente\Componente;
use sniesDocente\Sql;

include_once ('component/GestorDocente/Sql.class.php');
require_once ('component/GestorDocente/Interfaz/IGestorDocente.php');
include_once ("core/manager/Configurador.class.php");
class Docente implements IGestorDocente {
	// private $miGestorAdmitido;
	var $miConfigurador;
	var $miSql;
	function __construct() {
		$this->miSql = new Sql ();
		$this->miConfigurador = \Configurador::singleton ();
	}
	function consultarDocenteAcademica($annio, $semestre) {
		$conexion = "academica";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		// el semestre 03 de la universidad corresponde al semestre 02 de SNIES
		$variable ['annio'] = $annio;
		if ($semestre == 02) {
			$variable ['semestre'] = 3;
			;
		} else {
			$variable ['semestre'] = 1;
		}
		$cadenaSql = $this->miSql->cadena_sql ( 'consultarDocenteAcademica', $variable );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		
		return $resultado;
	}
	
	function consultarVinculacionDocente($annio, $semestre) {
		$conexion = "academica";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		// el semestre 03 de la universidad corresponde al semestre 02 de SNIES
		$variable ['annio'] = $annio;
		if ($semestre == 02) {
			$variable ['semestre'] = 3;
			;
		} else {
			$variable ['semestre'] = 1;
		}
		$cadenaSql = $this->miSql->cadena_sql ( 'consultarVinculacionDocente', $variable );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		
		return $resultado;
	}
	
	
	
}
