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
	
	// ////PARTICIPANTE SNIES
	function consultarParticipante($docente) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->cadena_sql ( 'consultarParticipante', $docente );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		
		return $resultado;
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see \sniesEstudiante\IGestorEstudiante::actualizarParticipante()
	 */
	function actualizarParticipante($docente) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->cadena_sql ( 'actualizarParticipante', $docente );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		
		if ($resultado == FALSE) {
			$error = $esteRecursoDB->obtener_error ();
			echo '<b>INFORMACION DEL ERROR:</b><br>';
			echo $cadenaSql;
			var_dump ( $error );
			var_dump ( $docente );
		}
		
		return $resultado;
	}
	function registrarParticipante($docente) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->cadena_sql ( 'registrarParticipante', $docente );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		
		if ($resultado == FALSE) {
			$error = $esteRecursoDB->obtener_error ();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump ( $error );
		}
		
		return $resultado;
	}
	function borrarParticipante($docente) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->cadena_sql ( 'borrarParticipante', $docente );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		if ($resultado == FALSE) {
			$error = $esteRecursoDB->obtener_error ();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump ( $error );
		}
		return $resultado;
	}
	
	// ////DOCENTE SNIES
	function consultarDocente($docente) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->cadena_sql ( 'consultarDocente', $docente );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		
		return $resultado;
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see \sniesEstudiante\IGestorEstudiante::actualizarDocente()
	 */
	function actualizarDocente($docente) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->cadena_sql ( 'actualizarDocente', $docente );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		
		if ($resultado == FALSE) {
			$error = $esteRecursoDB->obtener_error ();
			echo '<b>INFORMACION DEL ERROR:</b><br>';
			echo $cadenaSql;
			var_dump ( $error );
			var_dump ( $docente );
		}
		
		return $resultado;
	}
	function registrarDocente($docente) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->cadena_sql ( 'registrarDocente', $docente );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		
		if ($resultado == FALSE) {
			$error = $esteRecursoDB->obtener_error ();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump ( $error );
		}
		
		return $resultado;
	}
	function borrarDocente($docente) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->cadena_sql ( 'borrarDocente', $docente );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		if ($resultado == FALSE) {
			$error = $esteRecursoDB->obtener_error ();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump ( $error );
		}
		return $resultado;
	}
	
	// ////DOCENTE_H SNIES
	function consultarDocente_h($docente) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->cadena_sql ( 'consultarDocente_h', $docente );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
		
		return $resultado;
	}
	/**
	 * (non-PHPdoc)
	 *
	 * @see \sniesEstudiante\IGestorEstudiante::actualizarDocente_h()
	 */
	function actualizarDocente_h($docente) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->cadena_sql ( 'actualizarDocente_h', $docente );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		
		if ($resultado == FALSE) {
			$error = $esteRecursoDB->obtener_error ();
			echo '<b>INFORMACION DEL ERROR:</b><br>';
			echo $cadenaSql;
			var_dump ( $error );
			var_dump ( $docente_h );
		}
		
		return $resultado;
	}
	function registrarDocenteContrato($docente, $annio, $semestre) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$docente ['ANNIO'] = $annio;
		$docente ['SEMESTRE'] = $semestre;
		
		$cadenaSql = $this->miSql->cadena_sql ( 'registrarDocenteContrato', $docente );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		
		if ($resultado == FALSE) {
			$error = $esteRecursoDB->obtener_error ();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump ( $error );
		}
		
		return $resultado;
	}
	function borrarDocente_h($docente) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->cadena_sql ( 'borrarDocente_h', $docente );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		if ($resultado == FALSE) {
			$error = $esteRecursoDB->obtener_error ();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump ( $error );
		}
		return $resultado;
	}
	function borrarDocenteContrato($annio, $semestre) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$variable ['ANNIO'] = $annio;
		$variable ['SEMESTRE'] = $semestre;
		$cadenaSql = $this->miSql->cadena_sql ( 'borrarDocenteContrato', $variable );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		if ($resultado == FALSE) {
			$error = $esteRecursoDB->obtener_error ();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump ( $error );
		}
		return $resultado;
	}
	
	function consultarDocenteDoctoradoMaestria($annio, $semestre) {
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
		$cadenaSql = $this->miSql->cadena_sql ( 'consultarDocenteDoctoradoMaestria', $variable );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
	
		return $resultado;
	}
	
	function borrarDocenteDoctoradoMaestriaTodos($annio, $semestre) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
	
		$variable ['ANNIO'] = $annio;
		$variable ['SEMESTRE'] = $semestre;
		$cadenaSql = $this->miSql->cadena_sql ( 'borrarDocenteDoctoradoMaestriaTodos', $variable );
	
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
		if ($resultado == FALSE) {
			$error = $esteRecursoDB->obtener_error ();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump ( $error );
		}
		return $resultado;
	}
	function registrarDocenteDoctoradoMaestria($docente) {
		$conexion = "sniesLocal";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
			// Ajusta el formato del mes de acuerdo al SNIES 1->01; 2->03
			if ($docente ['SEMESTRE'] == 1)
				$docente ['SEMESTRE'] = '01';
			{
			}
			if ($docente ['SEMESTRE'] == 3) 

			{
				$docente ['SEMESTRE'] = '02';
			}		
	
		$cadenaSql = $this->miSql->cadena_sql ( 'registrarDocenteDoctoradoMaestria', $docente );
	
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, '' );
	
		if ($resultado == FALSE) {
			$error = $esteRecursoDB->obtener_error ();
			echo '<b>INFORMACION DEL ERROR:</b><br><hr>';
			echo $cadenaSql;
			var_dump ( $error );
		}
	
		return $resultado;
	}
	
	
	
	
	
}
