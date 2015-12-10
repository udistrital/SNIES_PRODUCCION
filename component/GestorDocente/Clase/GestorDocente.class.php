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
		$periodo ['annio'] = $annio;
		if ($semestre == 02) {
			$periodo ['semestre'] = 3;
			;
		} else {
			$periodo ['semestre'] = 1;
		}
		
		$cadenaSql = $this->miSql->cadena_sql ( 'consultarDocenteAcademica', $periodo );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		
		return $resultado;
	}
}
