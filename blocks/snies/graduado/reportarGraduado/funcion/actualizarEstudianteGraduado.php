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
		$graduado = $this->miComponente->consultarGraduadoAcademica ( $this->annio, $this->semestre );
		
		$miProcesadorExcepcion = new procesadorExcepcion ();
		// FORMATEA LOS VALORES NULOS, CODIFICA EXCEPCIONES
		$graduado = $miProcesadorExcepcion->procesarExcepcionEstudiante ( $graduado );
		
		$this->actualizarEstudiante ( $graduado );
		
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
			if (fmod ( $numero, 10 ) == 0) {
				echo '.';
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
						// Borra los registros
						// El filtro es codigo y tipo de documento que aparece en la tabla participante
						// OJO, NO es el obtenido de la DB académica
						$estudianteError ['CODIGO_UNICO'] = $unEstudianteParticipante ['codigo_unico'];
						$estudianteError ['TIPO_DOC_UNICO'] = $unEstudianteParticipante ['tipo_doc_unico'];
						
						$this->miComponente->borrarGraduado ( $estudianteError );
						$this->miComponente->borrarEgresado ( $estudianteError );
						$this->miComponente->borrarMatriculado ( $estudianteError );
						$this->miComponente->borrarEstudiantePrograma ( $estudianteError );
						$this->miComponente->borrarEstudiante ( $estudianteError );
						
						$estudianteParticipante = $this->miComponente->consultarEstudiante ( $unEstudiante );
						
						// si no existe insertar el nuevo registro
						if ($estudianteParticipante == false) {
							$this->miComponente->registrarEstudiante ( $unEstudiante );
							echo $unEstudiante ['CODIGO_UNICO'] . ' Nuevo<br>';
						}
						
						echo $unEstudiante ['CODIGO_UNICO'] . ' borrado<br>';
					}
				}
			}
		}
		echo 'terminado';
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );

$resultado = $miProcesador->procesarFormulario ();

