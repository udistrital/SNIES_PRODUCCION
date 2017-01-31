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
	
	function consultarInscritoSnies( $annio, $semestre ) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		$periodo ['annio'] = $annio;
		$periodo ['semestre'] = $semestre;
		$cadenaSql = $this->miSql->cadena_sql ( 'consultarInscritoSnies', $periodo );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );

		if ($resultado == FALSE) {
			$error = $esteRecursoDB->obtener_error ();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump ( $error );
		}

		return $resultado;
		
	}	
	
	function consultarInscritoProgramaSnies( $annio, $semestre ) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		$periodo ['annio'] = $annio;
		$periodo ['semestre'] = $semestre;
		$cadenaSql = $this->miSql->cadena_sql ( 'consultarInscritoProgramaSnies', $periodo );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );

		if ($resultado == FALSE) {
			$error = $esteRecursoDB->obtener_error ();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump ( $error );
		}

		return $resultado;
		
	}	
	
	function insertarInscritoSnies($inscrito) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		$cadenaSql = $this->miSql->cadena_sql ( 'insertarInscritoSnies', $inscrito );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );

		if ($resultado == FALSE) {
			$error = $esteRecursoDB->obtener_error ();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump ( $error );
		}

		return $resultado;
	}
	
	
	function insertarInscritoProgramaSnies($inscrito) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		$cadenaSql = $this->miSql->cadena_sql ( 'insertarInscritoProgramaSnies', $inscrito );
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
	
	function borrarInscritoSnies($inscrito) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );

		$cadenaSql = $this->miSql->cadena_sql ( 'borrarInscritoSnies', $inscrito );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		
		if ($resultado == FALSE) {
			$error = $esteRecursoDB->obtener_error ();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump ( $error );
		}		

		return true;
	}
		function borrarInscritoProgramaSnies($annio, $semestre) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		

		$datos ['annio'] = $annio;
		$datos ['semestre'] = $semestre;

		$cadenaSql = $this->miSql->cadena_sql ( 'borrarInscritoProgramaSnies', $datos );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		
		if ($resultado == FALSE) {
			$error = $esteRecursoDB->obtener_error ();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump ( $error );
		}		

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
