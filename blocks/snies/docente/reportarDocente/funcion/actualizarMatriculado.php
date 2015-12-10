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
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->lenguaje = $lenguaje;
		$this->miSql = $sql;
		$this->miComponente = new Componente ();
		$this->host = $this->miConfigurador->getVariableConfiguracion ( "host" );
		$this->site = $this->miConfigurador->getVariableConfiguracion ( "site" );
		$this->esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
	}
	function procesarFormulario() {
		$this->annio = $_REQUEST ['annio'];
		$this->semestre = $_REQUEST ['semestre'];
		
		/**
		 * PROCEDIMIENTO
		 * 1.
		 * Consultar los datos de los estudiantes para un período
		 * 2. Quitar acentos
		 * 3. Dividir nombres
		 * 4. Actualizar en PARTICIPANTE
		 * 5. Actualizar ESTUDIANTE
		 * 6. Actualizar ESTUDIANTE_PROGRAMA
		 * 7. Actualizar MATRICULADO
		 */
		
		// estudiante de la académica
		$estudiante = $this->miComponente->consultarEstudianteAcademica ( $this->annio, $this->semestre );
		
		// en el caso de que no se haga la consulta redirecciona
		if ($estudiante == false) {
			$valorCodificado = "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
			$valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );
			
			// Rescatar el parámetro enlace desde los datos de configuraión en la base de datos
			$variable = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
			$miEnlace = $this->host . $this->site . '/index.php?' . $variable . '=' . $valorCodificado;
			
			header ( "Location:$miEnlace" );
		}
		
		$miProcesadorExcepcion = new procesadorExcepcion ();
		// FORMATEA LOS VALORES NULOS, CODIFICA EXCEPCIONES
		$estudiante = $miProcesadorExcepcion->procesarExcepcionEstudiante ( $estudiante );
		
		echo 'proceso 1 actualizarEstudiantePrograma...<br>';
		$this->actualizarEstudiantePrograma ( $estudiante );
		echo 'proceso 2 actualizarMatriculado<br>';
		$this->actualizarMatriculado ( $estudiante );
		echo 'FIN<br>';
		exit ();
	}
	
	/**
	 *
	 * Función que actualiza o registra los datos de la tabla ESTUDIANTE_PROGRAMA DEL SNIES (Se refiere a estudiantes de primer semestre):
	 * Si no existe el registro en la tabla lo registra
	 * Si existe el registo lo actualiza
	 *
	 * @param array $estudiante        	
	 */
	function actualizarEstudiantePrograma($estudiante) {
		
		// borrar todos los registros de estudiante_programa para el periodo seleccionado
		$this->miComponente->borrarEstudianteProgramaPeriodoTodos ( $this->annio, $this->semestre );
		
		// registrar los estudiantes de la cohorte seleccionada, año y período
		foreach ( $estudiante as $unEstudiante ) {
			
			if ($unEstudiante ['ANIO'] == $this->annio and $unEstudiante ['SEMESTRE'] == $this->semestre) {
				$this->miComponente->borrarEstudiantePrograma ( $unEstudiante );//borra si un fue estudiante en 
				$this->miComponente->registrarEstudiantePrograma ( $unEstudiante );
			}
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
		
		// borrar todos los registros de la tabla MATRICULADO para el periodo seleccionado
		$this->miComponente->borrarMatriculadoPeriodoTodos ( $this->annio, $this->semestre );
		
		// registrar los matriculados de un semestre año y período
		foreach ( $estudiante as $unEstudiante ) {
			
			// Registrar todos los registros de la consulta (que son de un período y semestre determinado) en la tabla MATRICULADO
			//
			$this->miComponente->registrarMatriculado ( $unEstudiante, $this->annio, $this->semestre );
		}
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );

$resultado = $miProcesador->procesarFormulario ();

