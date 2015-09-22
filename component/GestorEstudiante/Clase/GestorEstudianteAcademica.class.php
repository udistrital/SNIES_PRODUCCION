<?php

namespace sniesEstudiante;

use sniesEstudiante\Componente;
use sniesEstudiante\Sql;

include_once ('component/GestorEstudiante/Sql.class.php');
require_once ('component/GestorEstudiante/Interfaz/IGestorEstudianteAcademica.php');
include_once ("core/manager/Configurador.class.php");
class estudiante implements IGestorEstudiante {
	// private $miGestorAdmitido;
	var $miConfigurador;
	var $miSql;
	function __construct() {
		$this->miSql = new Sql ();
		$this->miConfigurador = \Configurador::singleton ();
	}
	function contarMatriculados($periodo) {
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
		// el semestre 03 de la universidad corresponde al semestre 02 de SNIES
		$periodo ['annio'] = $annio;
		if ($semestre == 02) {
			$periodo ['semestre'] = 3;
			;
		} else {
			$periodo ['semestre'] = 1;
		}
		
		$cadenaSql = $this->miSql->cadena_sql ( 'consultarParticipanteEstudiante', $periodo );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		
		return $resultado;
	}
	function registrarParticipanteEstudiante($estudiante) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->cadena_sql ( 'registrarParticipanteEstudiante', $estudiante );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		
		return $resultado;
	}
	function borrarParticipanteEstudiante($estudiante) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->cadena_sql ( 'borrarParticipanteEstudiante', $estudiante );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		
		return $resultado;
	}
	function consultarEstudiante($annio, $semestre) {
		$conexion = "academica";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		$variable['annio']=$annio;
		$variable['semestre']=$semestre;
		
		$cadenaSql = $this->miSql->cadena_sql ( 'consultarEstudiante', $variable );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		
		return $resultado;
	}
	function consultarEstudianteSNIES($estudiante) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->cadena_sql ( 'consultarEstudianteSNIES', $estudiante );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		
		return $resultado;
	}
	function borrarEstudiante($unEstudiante) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->cadena_sql ( 'borrarEstudianteSnies', $unEstudiante);
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		
		return $resultado;
	}
}
