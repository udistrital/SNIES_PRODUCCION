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
		 * 1. Consultar los datos de los estudiantes para un período
		 * 2. Quitar acentos
		 * 3. Dividir nombres
		 * 4. Actualizar en PARTICIPANTE
		 */
		
		// estudiante de la académica
		$estudiante = $this->miComponente->consultarEstudianteAcademica ( $this->annio, $this->semestre );
		
		$miProcesadorNombre = new procesadorNombre ();
		
		//Busca y presenta los caracteres inválidos
		$caracteresInvalidos = $miProcesadorNombre->buscarCaracteresInvalidos ( $estudiante, 'EST_NOMBRE' );
		
		// quita acentos del nombre
		$estudiante = $miProcesadorNombre->quitarAcento ( $estudiante, 'EST_NOMBRE' );
		
		// descompone nombre completo en sus partes y las aglega al final de cada registro
		foreach ( $estudiante as $clave => $valor ) {
			// echo $estudiante [$clave] ['CODIGO_UNICO'].'<br>';
			$nombreCompleto = $miProcesadorNombre->dividirNombreCompleto ( $estudiante [$clave] ['EST_NOMBRE'] );
			$estudiante [$clave] ['PRIMER_APELLIDO'] = $nombreCompleto ['primer_apellido'];
			$estudiante [$clave] ['SEGUNDO_APELLIDO'] = $nombreCompleto ['segundo_apellido'];
			$estudiante [$clave] ['PRIMER_NOMBRE'] = $nombreCompleto ['primer_nombre'];
			$estudiante [$clave] ['SEGUNDO_NOMBRE'] = $nombreCompleto ['segundo_nombre'];
		}
		
		$miProcesadorExcepcion = new procesadorExcepcion ();
		// FORMATEA LOS VALORES NULOS, CODIFICA EXCEPCIONES
		
		//************************************/// OJO REVISAR LAS EXCEPCIONES
		//$estudiante = $miProcesadorExcepcion->procesarExcepcionEstudiante ( $estudiante );
		
		$this->actualizarParticipante ( $estudiante );
		
		// $valorCodificado = "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
		// $valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );
		
		// Rescatar el parámetro enlace desde los datos de configuraión en la base de datos
		// $variable = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
		// $miEnlace = $this->host . $this->site . '/index.php?' . $variable . '=' . $valorCodificado;
		
		// header ( "Location:$miEnlace" );
		exit ();
	}
	
	/**
	 * Funcion que decide que hacer con el registro de un participante
	 * 1.
	 * Si no existe lo registra
	 * 2. Si existe y es igual el tipo de documento se actualiza
	 * 3. Si existe y es diferente el tipo de documento lo borra
	 *
	 * @param array $estudiante
	 *        	datos de estudiante
	 */
	function actualizarParticipante($estudiante) {
		foreach ( $estudiante as $unEstudiante ) {
			// echo 'CODIGO: ' . $unEstudiante ['CODIGO_UNICO'] . '<br>';
			// consulta enla tabla participante y cuenta el número de registros retornados
			$participante = $this->miComponente->consultarParticipante ( $unEstudiante );
			
			// si no existe insertar el nuevo registro
			if ($participante == false) {
				$this->miComponente->registrarParticipante ( $unEstudiante );
				echo $unEstudiante ['CODIGO_UNICO'] . ' Nuevo<br>';
			} else {
				foreach ( $participante as $unParticipante ) {
					// Si existe y es igual el tipo actualizar si no es igual borrar
					if ($unParticipante ['tipo_doc_unico'] == $unEstudiante ['TIPO_DOC_UNICO']) {
						$this->miComponente->actualizarParticipante ( $unEstudiante );
						// echo $unEstudiante ['CODIGO_UNICO'] . ' actualizado<br>';
					} else {
						// Borra los registros
						// El filtro es codigo y tipo de documento que aparece en la tabla participante
						// OJO, NO es el obtenido de la DB académica
						$estudianteError ['CODIGO_UNICO'] = $unParticipante ['codigo_unico'];
						$estudianteError ['TIPO_DOC_UNICO'] = $unParticipante ['tipo_doc_unico'];
						
						$this->miComponente->borrarParticipante ( $estudianteError );
						
						$participante = $this->miComponente->consultarParticipante ( $unEstudiante );
						
						// si no existe insertar el nuevo registro
						if ($participante == false) {
							$this->miComponente->registrarParticipante ( $unEstudiante );
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

