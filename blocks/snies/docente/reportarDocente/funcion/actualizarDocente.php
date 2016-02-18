<?php
include_once ('component/GestorDocente/Componente.php');
include_once ('blocks/snies/funcion/procesadorNombre.class.php');
include_once ('blocks/snies/funcion/procesadorExcepcion.class.php');
use sniesDocente\Componente;
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
		
		// docente de la académica
		$docente = $this->miComponente->consultarDocenteAcademica ( $this->annio, $this->semestre );
		
		$miProcesadorExcepcion = new procesadorExcepcion ();
		// FORMATEA LOS VALORES NULOS, CODIFICA EXCEPCIONES
		$docente = $miProcesadorExcepcion->procesarExcepcionDocente ( $docente );
		
		$this->actualizarDocente ( $docente );
		
		exit ();
	}
	
	/**
	 * Funcion que decide que hacer con el registro de un participante
	 * 1.
	 * Si no existe lo registra
	 * 2. Si existe y es igual el tipo de documento no se hace nada
	 * 3. Si existe y es diferente el tipo de documento lo borra en cascada docente_h, docente
	 *
	 * @param unknown $docente        	
	 */
	function actualizarDocente($docente) {
		echo 'Actualizando docentes...';
		foreach ( $docente as $unDocente ) {
			
			$docenteParticipante = $this->miComponente->consultarDocente ( $unDocente );
			
			// si no existe insertar el nuevo registro
			if ($docenteParticipante == false) {
				$this->miComponente->registrarDocente ( $unDocente );
				echo 'Se ha registrado el docente' . $unDocente ['CODIGO_UNICO'] . '<br>';
			} else {
				foreach ( $docenteParticipante as $unDocenteParticipante ) {
					// Si existe y es igual el tipo actualizar los demas datos, si no es igual borrar
					if ($unDocenteParticipante ['tipo_doc_unico'] == $unDocente ['TIPO_DOC_UNICO']) {
						// $this->miComponente->actualizarEstudiante ( $unDocente );
						// echo $unDocente ['CODIGO_UNICO'] . ' No requiere actualizar<br>';
					} else {
						// Borra los registros
						// El filtro es codigo y tipo de documento que aparece en la tabla participante
						// OJO, NO es el obtenido de la DB académica
						$docenteError ['CODIGO_UNICO'] = $unDocenteParticipante ['codigo_unico'];
						$docenteError ['TIPO_DOC_UNICO'] = $unDocenteParticipante ['tipo_doc_unico'];
						
						$this->miComponente->borrarDocente_h ( $docenteError );
						$this->miComponente->borrarDocente ( $docenteError );
						
						echo 'Ingresar los datos del docente, aquí vamos';
						exit;
						// $docenteParticipante = $this->miComponente->consultarEstudiante ( $unDocente );
						
						// si no existe insertar el nuevo registro
						if ($docenteParticipante == false) {
							$this->miComponente->registrarEstudiante ( $unDocente );
							echo $unDocente ['CODIGO_UNICO'] . ' Nuevo<br>';
						}
						
						echo $unDocente ['CODIGO_UNICO'] . ' borrado<br>';
					}
				}
			}
		}
		echo 'terminado';
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );

$resultado = $miProcesador->procesarFormulario ();

