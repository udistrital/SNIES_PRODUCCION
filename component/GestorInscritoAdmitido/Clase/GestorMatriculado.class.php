<?php

namespace snies;

use snies\Componente;
use component\GestorInscritoAdmitido\Sql;

include_once ('component/GestorInscritoAdmitido/Sql.class.php');
require_once ('component/GestorInscritoAdmitido/Interfaz/IGestorMatriculado.php');
include_once ("core/manager/Configurador.class.php");
class GestorMatriculado implements IGestorMatriculado {
	// private $miGestorAdmitido;
	var $miConfigurador;
	var $miSql;
	function __construct() {
		$this->miConfigurador = \Configurador::singleton ();
	}
	function contarMatriculados($periodo) {
		$this->miSql = new Sql ();
		
		$this->miConfigurador = \Configurador::singleton ();
		// configuracion es el nombre de la conexión principal de SARA - se crea de forma automática tomando los
		// datos de config.inc.php
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->cadena_sql ( 'contarMatriculados', $periodo );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		
		return $resultado [0] [0];
	}
	function consultarParticipanteEstudiante($annio, $semestre) {		
		
		$conexion = "academica";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		$cadenaSql = $this->miSql->cadena_sql ( 'consultarParticipanteEstudiante', $periodo );
		
		echo $cadenaSql;exit;
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		
		return $resultado;
	}
}
