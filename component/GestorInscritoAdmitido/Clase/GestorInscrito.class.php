<?php

namespace sniesInscritoAdmitido;

use sniesInscritoAdmitido\Componente;
use component\GestorInscritoAdmitido\Sql;
use sniesInscritoAdmitido\IGestorInscrito;

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
	}
	function consultarInscritoPregadoAcademica($annio, $semestre) {
		$periodo ['annio'] = $annio;
		$periodo ['semestre'] = $semestre;
		$conexion = "academica";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		$cadenaSql = $this->miSql->cadena_sql ( 'consultarInscritoPregradoAcademica', $periodo );

		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		echo count($resultado);exit;

if ($resultado == false) {
			return false;
		}
		return $resultado;
	}
	function consultarInscritoPostgradoAcademica($annio, $semestre) {
		$periodo ['annio'] = $annio;
		$periodo ['semestre'] = $semestre;
		$conexion = "academica";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		$cadenaSql = $this->miSql->cadena_sql ( 'consultarInscritoPostgradoAcademica', $periodo );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );


		if ($resultado == false) {
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

		if ($resultado == FALSE) {
			$error = $esteRecursoDB->obtener_error ();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump ( $error );
		}

		return $resultado;
	}
	function actualizarInscritoSnies() {
	}
	function borrarInscritoSnies($annio, $semestre) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );

		$datos ['annio'] = $annio;
		$datos ['semestre'] = $semestre;

		$cadenaSql = $this->miSql->cadena_sql ( 'borrarInscritos', $datos );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );

		return true;
	}
	function contarInscritos($annio, $semestre) {
		$conexion = 'sniesLocal';

		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );

		$periodo ['annio'] = $annio;
		$periodo ['semestre'] = $semestre;
		$cadenaSql = $this->miSql->cadena_sql ( 'contarInscritos', $periodo );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );

		return $resultado [0] [0];
	}
}
