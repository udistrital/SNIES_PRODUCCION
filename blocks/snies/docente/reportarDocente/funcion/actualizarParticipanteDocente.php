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
		
		$miProcesadorNombre = new procesadorNombre ();
		
		$caracteresInvalidosApellido = $miProcesadorNombre->buscarCaracteresInvalidos ( $docente, 'DOC_APELLIDO' );
		$caracteresInvalidosNombre = $miProcesadorNombre->buscarCaracteresInvalidos ( $docente, 'DOC_NOMBRE' );
		
		// quita acentos del nombre
		$docente = $miProcesadorNombre->quitarAcento ( $docente, 'DOC_APELLIDO' );
		$docente = $miProcesadorNombre->quitarAcento ( $docente, 'DOC_NOMBRE' );
		
		// descompone nombre completo en sus partes y las aglega al final de cada registro
		foreach ( $docente as $clave => $valor ) {
			// echo $docente [$clave] ['CODIGO_UNICO'].'<br>';
			$apellidos = $miProcesadorNombre->dividirApellidos ( $docente [$clave] ['DOC_APELLIDO'] );
			$docente [$clave] ['PRIMER_APELLIDO'] = $apellidos ['primer_apellido'];
			$docente [$clave] ['SEGUNDO_APELLIDO'] = $apellidos ['segundo_apellido'];
			
			$nombres = $miProcesadorNombre->dividirNombres ( $docente [$clave] ['DOC_NOMBRE'] );
			$docente [$clave] ['PRIMER_NOMBRE'] = $nombres ['primer_nombre'];
			$docente [$clave] ['SEGUNDO_NOMBRE'] = $nombres ['segundo_nombre'];
		}
		
		
		$miProcesadorExcepcion = new procesadorExcepcion ();
		// FORMATEA LOS VALORES NULOS, CODIFICA EXCEPCIONES
		$docente = $miProcesadorExcepcion->procesarExcepcionDocente ( $docente );
		
		
		$this->actualizarParticipante ( $docente );
		
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
	 * @param array $docente
	 *        	datos de estudiante
	 */
	function actualizarParticipante($docente) {
		foreach ( $docente as $unDocente ) {
			// echo 'CODIGO: ' . $unDocente ['CODIGO_UNICO'] . '<br>';
			// consulta enla tabla participante y cuenta el número de registros retornados
			$participante = $this->miComponente->consultarParticipante ( $unDocente );
			
			// si no existe insertar el nuevo registro
			if ($participante == false) {
				$this->miComponente->registrarParticipante ( $unDocente );
				echo $unDocente ['CODIGO_UNICO'] . ' Nuevo<br>';exit;
			} else {
				foreach ( $participante as $unParticipante ) {
					// Si existe y es igual el tipo actualizar si no es igual borrar
					if ($unParticipante ['tipo_doc_unico'] == $unDocente ['TIPO_DOC_UNICO']) {
						$this->miComponente->actualizarParticipante ( $unDocente );
						// echo $unDocente ['CODIGO_UNICO'] . ' actualizado<br>';
					} else {
						// Borra los registros
						// El filtro es codigo y tipo de documento que aparece en la tabla participante
						// OJO, NO es el obtenido de la DB académica
						$docenteError ['CODIGO_UNICO'] = $unParticipante ['codigo_unico'];
						$docenteError ['TIPO_DOC_UNICO'] = $unParticipante ['tipo_doc_unico'];
						
						$this->miComponente->borrarParticipante ( $docenteError );
						
						$participante = $this->miComponente->consultarParticipante ( $unDocente );
						
						// si no existe insertar el nuevo registro
						if ($participante == false) {
							$this->miComponente->registrarParticipante ( $unDocente );
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

