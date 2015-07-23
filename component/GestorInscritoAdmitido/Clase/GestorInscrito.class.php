<?php

namespace snies;

use snies\Componente;
use component\GestorInscritoAdmitido\Sql;

include_once ('component/GestorInscritoAdmitido/Sql.class.php');
require_once ('component/GestorInscritoAdmitido/Interfaz/IGestorInscrito.php');
include_once ("core/manager/Configurador.class.php");
class GestorInscrito implements IGestorInscrito {
	// private $miGestorInscrito;
	var $miConfigurador;
	var $miSql;
	function __construct() {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miSql = new Sql ();
		$this->miConfigurador = \Configurador::singleton ();
	}
	function consultarInscritoAcademica($annio, $semestre) {
		$periodo ['annio'] = $annio;
		$periodo ['semestre'] = $semestre;
		$conexion = "academica";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		$cadenaSql = $this->miSql->cadena_sql ( 'consultarInscritoAcademica', $periodo );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		
		if ($resultado==false) {
			return false;
		}
		return $resultado;
	}
	function consultarInscritoSnies() {
	}
	function insertarInscritoSnies($inscrito) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		$cadenaSql = $this->miSql->cadena_sql ( 'insertarInscrito', $inscrito );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		
		if ($resultado == true) {
			return true;
		} else {
			return false;
		}
	}
	function actualizarInscritoSnies() {
	}
	function borrarInscritoSnies($annio, $semestre) {
		$datos ['annio'] = $annio;
		$datos ['semestre'] = $semestre;
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		$cadenaSql = $this->miSql->cadena_sql ( 'borrarInscritos', $datos );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		
		return true;
	}
	function contarInscritos($periodo) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		$cadenaSql = $this->miSql->cadena_sql ( 'contarInscritos', $periodo );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		
		return $resultado [0] [0];
	}
}

