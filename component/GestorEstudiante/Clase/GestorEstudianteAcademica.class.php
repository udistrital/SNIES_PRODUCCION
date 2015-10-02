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
	function consultarEstudianteAcademica($annio, $semestre) {
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
		
		$cadenaSql = $this->miSql->cadena_sql ( 'consultarEstudianteAcademica', $periodo );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		
		return $resultado;
	}
	
	// ////PARTICIPANTE SNIES
	function cosultarParticipante($estudiante) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->cadena_sql ( 'consultarParticipante', $estudiante );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		
		return $resultado;
	}
	function actualizarParticipante($estudiante) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->cadena_sql ( 'actualizarParticipante', $estudiante );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		
		if ($resultado == FALSE) {
			$error = $esteRecursoDB->obtener_error ();
			echo '<b>INFORMACION DEL ERROR:</b><br>';
			echo $cadenaSql;
			var_dump ( $error );
			var_dump ( $estudiante );
		}
		
		return $resultado;
	}
	function registrarParticipante($estudiante) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->cadena_sql ( 'registrarParticipante', $estudiante );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		
			if ($resultado == FALSE) {
			$error = $esteRecursoDB->obtener_error ();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump ( $error );
		}
		
		return $resultado;
	}
	function borrarParticipante($estudiante) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->cadena_sql ( 'borrarParticipante', $estudiante );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		if ($resultado == FALSE) {
			$error = $esteRecursoDB->obtener_error ();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump ( $error );
		}
		return $resultado;
	}
	
	// ///ESTUDIANTE SNIES
	function consultarEstudiante($estudiante) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->cadena_sql ( 'consultarEstudiante', $estudiante );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		
		return $resultado;
	}
	function actualizarEstudiante($estudiante) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->cadena_sql ( 'actualizarEstudiante', $estudiante );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		
		return $resultado;
	}
	function registrarEstudiante($estudiante) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->cadena_sql ( 'registrarEstudiante', $estudiante );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		
		return $resultado;
	}
	
	// ///ESTUDIANTE_PROGRAMA SNIES
	function consultarEstudiantePrograma($estudiante) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->cadena_sql ( 'consultarEstudiantePrograma', $estudiante );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		
		return $resultado;
	}
	function borrarEstudiantePrograma($annio, $semestre) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$variable ['annio'] = $annio;
		$variable ['semestre'] = $semestre;
		$cadenaSql = $this->miSql->cadena_sql ( 'borrarEstudiantePrograma', $variable );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		
		return $resultado;
	}
	function registrarEstudiantePrograma($estudiante) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->cadena_sql ( 'registrarEstudiantePrograma', $estudiante );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		
		return $resultado;
	}
	
	// //MATRICULADO
	function borrarMatriculado($annio, $semestre) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$variable ['annio'] = $annio;
		$variable ['semestre'] = $semestre;
		$cadenaSql = $this->miSql->cadena_sql ( 'borrarMatriculado', $variable );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		
		return $resultado;
	}
	function registrarMatriculado($estudiante, $annio, $semestre) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		// pasar los valores de annio y semestre de la matrícula del período que se va a reportar
		$estudiante ['ANNIO_MATRICULA'] = $annio;
		$estudiante ['SEMESTRE_MATRICULA'] = $semestre;
		
		$cadenaSql = $this->miSql->cadena_sql ( 'registrarMatriculado', $estudiante );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		
		return $resultado;
	}
}
