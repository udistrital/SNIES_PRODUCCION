<?php
include_once ('component/GestorEstudiante/Componente.php');
include_once ('blocks/snies/matriculado/reportarMatriculado/funcion/procesadorNombre.class.php');
include_once ('blocks/snies/matriculado/reportarMatriculado/funcion/procesadorExcepcion.class.php');
use sniesEstudiante\Componente;
use bloqueSnies\procesadorExcepcion;
use bloqueSnies\procesadorNombre;
class FormProcessor {
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miSql;
	var $conexion;
	var $annio;
	var $semestre;
	function __construct($lenguaje, $sql) {
		$this -> miConfigurador = \Configurador::singleton();
		$this -> miConfigurador -> fabricaConexiones -> setRecursoDB('principal');
		$this -> lenguaje = $lenguaje;
		$this -> miSql = $sql;
		$this -> miComponente = new Componente();
		$this -> host = $this -> miConfigurador -> getVariableConfiguracion("host");
		$this -> site = $this -> miConfigurador -> getVariableConfiguracion("site");
		$this -> esteBloque = $this -> miConfigurador -> getVariableConfiguracion("esteBloque");
	}

	function procesarFormulario() {
		$this -> annio = $_REQUEST['annio'];
		$this -> semestre = $_REQUEST['semestre'];

		/**
		 * PROCEDIMIENTO
		 * 1.
		 * Consultar los datos de los estudiantes para un período
		 * 6. Actualizar ESTUDIANTE PRIMER_SEMESTRE
		 * 7. Actualizar MATRICULADO
		 */

		// estudiante de la académica
		$estudiante = $this -> miComponente -> consultarEstudianteAcademica($this -> annio, $this -> semestre);
		//var_dump($estudiante);
		//echo count($estudiante);exit;
				
		$miProcesadorExcepcion = new procesadorExcepcion();
		// FORMATEA LOS VALORES NULOS, CODIFICA EXCEPCIONES
		$estudiante = $miProcesadorExcepcion -> procesarExcepcionEstudiante($estudiante);

		echo 'proceso 1 actualizarEstudiantePrimerCurso...<br>';
		$this -> actualizarEstudiantePrimerCurso($estudiante);
		echo 'proceso 2 actualizarMatriculado<br>';
		$this -> actualizarMatriculado($estudiante);
		echo 'FIN<br>';exit;
		$valorCodificado = "&pagina=" . $this -> miConfigurador -> getVariableConfiguracion('pagina');
		$valorCodificado = $this -> miConfigurador -> fabricaConexiones -> crypto -> codificar($valorCodificado);

		// Rescatar el parámetro enlace desde los datos de configuraión en la base de datos
		$variable = $this -> miConfigurador -> getVariableConfiguracion("enlace");
		$miEnlace = $this -> host . $this -> site . '/index.php?' . $variable . '=' . $valorCodificado;
		echo '<script>window.location.assign("' . $miEnlace . '")</script>';
		exit();
	}

	/**
	 *
	 * Función que actualiza o registra los datos de la tabla ESTUDIANTE_PROGRAMA DEL SNIES (Se refiere a estudiantes de primer semestre):
	 * Si no existe el registro en la tabla lo registra
	 * Si existe el registo lo actualiza
	 *
	 * @param array $estudiante
	 */
	function actualizarEstudiantePrimerCurso($estudiante) {

		// registrar los estudiantes en su cohorte
		foreach ($estudiante as $unEstudiante) {

			//if ($unEstudiante['ANIO'] == $this -> annio and $unEstudiante['SEMESTRE'] == $this -> semestre) {

				$estudiantePrimerCurso = $this -> miComponente -> consultarEstudiantePrimerCurso($unEstudiante);
								
				//si existe el registro en la tabla primer_curso lo actualiza si no lo registra
				if (isset($estudiantePrimerCurso[0]['num_documento'])) {
					$this -> miComponente -> actualizarEstudiantePrimerCurso($unEstudiante);
				} else {
					$this -> miComponente -> registrarEstudiantePrimerCurso($unEstudiante);
				}

			//}
		}
	}

	/**
	 *
	 * Función que actualiza o registra los datos de la tabla ESTUDIANTE_PROGRAMA DEL SNIES (Se refiere a estudiantes de primer semestre):
	 * Si no existe el registro en la tabla lo registra
	 * Si existe el registo lo actualiza
	 *
	 * @param array $estudiante
	 */
	function actualizarMatriculado($estudiante) {

		// registrar los estudiantes matriculados en un año y período determinados
		foreach ($estudiante as $unEstudiante) {
						
			$matriculado = $this -> miComponente -> consultarMatriculado($unEstudiante, $this -> annio, $this -> semestre);

			//si existe el registro en la tabla matriculado para el año y período dados lo actualiza si no lo registra
			if (isset($matriculado[0]['codigo_estudiante'])) {				
				$this -> miComponente -> actualizarMatriculado($unEstudiante , $this -> annio, $this -> semestre);
			} else {				
				$this -> miComponente -> registrarMatriculado($unEstudiante, $this -> annio, $this -> semestre);
			}
		}

	}

}

$miProcesador = new FormProcessor($this -> lenguaje, $this -> sql);

$resultado = $miProcesador -> procesarFormulario();
