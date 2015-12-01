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
		
		$miProcesadorNombre = new procesadorNombre ();
		
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
		$estudiante = $miProcesadorExcepcion->procesarExcepcionEstudiante ( $estudiante );
		
		$this->actualizarParticipante ( $estudiante );
		
		//$valorCodificado = "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
		//$valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );
		
		// Rescatar el parámetro enlace desde los datos de configuraión en la base de datos
		//$variable = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
		//$miEnlace = $this->host . $this->site . '/index.php?' . $variable . '=' . $valorCodificado;
		
		//header ( "Location:$miEnlace" );
		exit ();
		/**
		echo 'proceso 2 actualizarEstudiante...<br>';
		$this->actualizarEstudiante ( $estudiante );
		echo 'proceso 3 actualizarEstudiantePrograma...<br>';
		$this->actualizarEstudiantePrograma ( $estudiante );
		echo 'proceso 4 actualizarMatriculado<br>';
		$this->actualizarMatriculado ( $estudiante );
		echo 'FIN<br>';
		exit ();*/
		

	}
	
	/**
	 * Funcion que decide que hacer con el registro de un participante
	 * 1.
	 * Si no existe lo registra
	 * 2. Si existe una vez y es igual el tipo de documento, lo actualiza
	 * 3. Si existe una vez y es diferente el tipo de documento, inserta el nuevo y borra el incorrecto
	 * (en cascada, graduado, egresado, matriculado, estudiante_programa, estudiante, participante)
	 * 4. Si existe dos veces actualiza el correcto y borra el incorrecto
	 * (en cascada, graduado, egresado, matriculado, estudiante_programa, estudiante, participante)
	 *
	 * @param array $estudiante
	 *        	datos de estudiante
	 */
	function actualizarParticipante($estudiante) {
		foreach ( $estudiante as $unEstudiante ) {
			echo 'CODIGO: ' . $unEstudiante ['CODIGO_UNICO'] . '<br>';
			// consulta enla tabla participante y cuenta el número de registros retornados
			$participante = $this->miComponente->consultarParticipante ( $unEstudiante );
			
			// si no existe insertar el nuevo registro
			if ($participante == false) {
				$this->miComponente->registrarParticipante ( $unEstudiante );
				echo $unEstudiante ['CODIGO_UNICO'] . ' Nuevo<br>';
			} else {
				foreach ( $participante as $unParticipante ) {
					// Si existe y es igual eltipo actualizar si no es igual borrar (en cascada)
					if ($unParticipante ['tipo_doc_unico'] == $unEstudiante ['TIPO_DOC_UNICO']) {
						$this->miComponente->actualizarParticipante ( $unEstudiante );
						echo $unEstudiante ['CODIGO_UNICO'] . ' actualizado<br>';
					} else {
						
						$this->miComponente->borrarGraduado ( $unEstudiante );
						$this->miComponente->borrarEgresado ( $unEstudiante );
						$this->miComponente->borrarMatriculado ( $unEstudiante );
						$this->miComponente->borrarEstudiantePrograma ( $unEstudiante );
						$this->miComponente->borrarEstudiante ( $unEstudiante );
						$this->miComponente->borrarParticipante ( $unEstudiante );
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
	function actualizarEstudiante($estudiante) {
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

