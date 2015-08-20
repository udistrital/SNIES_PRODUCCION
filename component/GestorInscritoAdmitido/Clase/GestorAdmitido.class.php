<?php

namespace snies;

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
		$this->miConfigurador = \Configurador::singleton ();
		$this->miSql = new Sql ();
	}
	function contarAdmitidos($periodo) {
		$this->miSql = new Sql ();
		
		$this->miConfigurador = \Configurador::singleton ();
		// configuracion es el nombre de la conexión principal de SARA - se crea de forma automática tomando los
		// datos de config.inc.php
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$ano = 2015;
		$cadenaSql = $this->miSql->cadena_sql ( 'contarAdmitidos', $periodo );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		
		return $resultado [0] [0];
	}
	function consultarAdmitidoAcademica($annio, $semestre) {
		$periodo ['annio'] = $annio;
		$periodo ['semestre'] = $semestre;
		$conexion = "academica";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		$cadenaSql = $this->miSql->cadena_sql ( 'consultarAdmitidoAcademica', $periodo );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		
		return $resultado;
	}
	function borrarAdmitidoSnies($annio, $semestre) {
		$datos ['annio'] = $annio;
		$datos ['semestre'] = $semestre;
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		$cadenaSql = $this->miSql->cadena_sql ( 'borrarAdmitidosSnies', $datos );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		
		return true;
	}
	function insertarAdmitidoSnies($admitido) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		$cadenaSql = $this->miSql->cadena_sql ( 'insertaAdmitidoSnies', $admitido );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		
		if ($resultado == true) {
			return true;
		} else {
			return false;
		}
	}
}
