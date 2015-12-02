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
		
		$this->actualizarEstudiante ( $estudiante );
		
		// $valorCodificado = "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
		// $valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );
		
		// Rescatar el parámetro enlace desde los datos de configuraión en la base de datos
		// $variable = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
		// $miEnlace = $this->host . $this->site . '/index.php?' . $variable . '=' . $valorCodificado;
		
		// header ( "Location:$miEnlace" );
		exit ();
	/**
	 * echo 'proceso 2 actualizarEstudiante...<br>';
	 * $this->actualizarEstudiante ( $estudiante );
	 * echo 'proceso 3 actualizarEstudiantePrograma...<br>';
	 * $this->actualizarEstudiantePrograma ( $estudiante );
	 * echo 'proceso 4 actualizarMatriculado<br>';
	 * $this->actualizarMatriculado ( $estudiante );
	 * echo 'FIN<br>';
	 * exit ();
	 */
	}
	
	/**
	 * Funcion que decide que hacer con el registro de un participante
	 * 1.
	 * Si no existe lo registra
	 * 2. Si existe y es igual el tipo de documento no se hace nada
	 * 3. Si existe y es diferente el tipo de documento lo borra en cascada
	 * graduado, egresado, estudiante_programa, estudiante
	 *
	 * @param array $estudiante
	 *        	datos de estudiante
	 */
	function actualizarEstudiante($estudiante) {
		echo 'Actualizando estudiantes...';
		$numero = 0;
		foreach ( $estudiante as $unEstudiante ) {
			$numero ++;
			if (fmod ( $numero, 1000 ) == 0) {
				echo $numero . '<br>';
			}
			
			// echo 'CODIGO: ' . $unEstudiante ['CODIGO_UNICO'] . '<br>';
			// consulta enla tabla estudiante del SNIES LOCAL
			$estudianteParticipante = $this->miComponente->consultarEstudiante ( $unEstudiante );
			
			// si no existe insertar el nuevo registro
			if ($estudianteParticipante == false) {
				$this->miComponente->registrarEstudiante ( $unEstudiante );
				echo $unEstudiante ['CODIGO_UNICO'] . ' Nuevo<br>';
			} else {
				foreach ( $estudianteParticipante as $unEstudianteParticipante ) {
					// Si existe y es igual el tipo actualizar si no es igual borrar
					if ($unEstudianteParticipante ['tipo_doc_unico'] == $unEstudiante ['TIPO_DOC_UNICO']) {
						// $this->miComponente->actualizarEstudiante ( $unEstudiante );
						// echo $unEstudiante ['CODIGO_UNICO'] . ' No requiere actualizar<br>';
					} else {
						//Borra los registros (el filtro es  codigo y tipo de documento)
						$this->miComponente->borrarGraduado ( $unEstudiante );
						$this->miComponente->borrarEgresado ( $unEstudiante );
						$this->miComponente->borrarMatriculado ( $unEstudiante );						
						$this->miComponente->borrarEstudiantePrograma ( $unEstudiante );
						$this->miComponente->borrarEstudiante ( $unEstudiante );
						echo $unEstudiante ['CODIGO_UNICO'] . ' borrado<br>';
					}
				}
			}
		}
		echo 'terminado';
	}
	
	/**
	 * Función que actualiza o registra los datos de la tabla ESTUDIANTE DEL SNIES:
	 * Si no existe el registro en la tabla lo registra
	 * Si existe el registo lo actualiza
	 *
	 * @param array $estudiante        	
	 */
	function actualizarEstudianteold($estudiante) {
		foreach ( $estudiante as $unEstudiante ) {
			$this->miComponente->borrarEstudiante ( $unEstudiante );
			$this->miComponente->registrarEstudiante ( $unEstudiante );
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
	function actualizarEstudiantePrograma($estudiante) {
		
		// borrar todos los registros de estudiante_programa para el periodo seleccionado
		$this->miComponente->borrarEstudiantePrograma ( $this->annio, $this->semestre );
		
		// registrar los estudiantes de la cohorte seleccionada, año y período
		foreach ( $estudiante as $unEstudiante ) {
			
			if ($unEstudiante ['ANIO'] == $this->annio and $unEstudiante ['SEMESTRE'] == $this->semestre) {
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
		$this->miComponente->borrarMatriculado ( $this->annio, $this->semestre );
		
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

