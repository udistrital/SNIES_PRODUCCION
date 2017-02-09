<?php

namespace sniesInscritoAdmitido;

use snies\Componente;
use component\GestorInscritoAdmitido\Sql;

include_once ('component/GestorInscritoAdmitido/Sql.class.php');
require_once ('component/GestorInscritoAdmitido/Interfaz/IGestorAdmitido.php');
include_once ("core/manager/Configurador.class.php");
class GestorAdmitido implements IGestorAdmitido {
	// private $miGestorAdmitido;
	var $miConfigurador;
	var $miSql;
	function __construct() {
		$this -> miConfigurador = \Configurador::singleton();
		$this -> miSql = new Sql();
	}

	function consultarAdmitidoSnies($annio, $semestre) {

		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);

		$periodo['ano'] = $annio;
		$periodo['semestre'] = $semestre;
		$cadenaSql = $this -> miSql -> cadena_sql('consultarAdmitidoSnies', $periodo);
		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, 'busqueda');

		if ($resultado == FALSE) {
			$error = $esteRecursoDB -> obtener_error();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump($error);
		}

		return $resultado;
	}

	function consultarAdmitidoPregradoAcademica($annio, $semestre) {
		$periodo['annio'] = $annio;
		$periodo['semestre'] = $semestre;
		$conexion = "academica";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);
		$cadenaSql = $this -> miSql -> cadena_sql('consultarAdmitidoPregradoAcademica', $periodo);
		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, 'busqueda');

		if ($resultado == FALSE) {
			$error = $esteRecursoDB -> obtener_error();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump($error);
		}

		return $resultado;
	}

	function consultarAdmitidoPostgradoAcademica($annio, $semestre) {
		$periodo['annio'] = $annio;
		$periodo['semestre'] = $semestre;
		$conexion = "academica";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);
		$cadenaSql = $this -> miSql -> cadena_sql('consultarAdmitidoPostgradoAcademica', $periodo);

		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, 'busqueda');

		return $resultado;
	}

	function borrarAdmitidoSnies($annio, $semestre) {
		$datos['annio'] = $annio;
		$datos['semestre'] = $semestre;
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);
		$cadenaSql = $this -> miSql -> cadena_sql('borrarAdmitidoSnies', $datos);
		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, '');

		return true;
	}

	function insertarAdmitidoSnies($admitido) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this -> miConfigurador -> fabricaConexiones -> getRecursoDB($conexion);
		$cadenaSql = $this -> miSql -> cadena_sql('insertarAdmitidoSnies', $admitido);
		$resultado = $esteRecursoDB -> ejecutarAcceso($cadenaSql, '');

		if ($resultado == FALSE) {
			$error = $esteRecursoDB -> obtener_error();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump($error);
		}else{
			return true;
		}
	}

}
