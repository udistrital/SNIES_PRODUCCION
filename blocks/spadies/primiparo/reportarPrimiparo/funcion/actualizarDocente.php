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
		$this->actualizarDocente_h ( $docente );
		
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
		echo 'Actualizando docentes...<br>';
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
						
						// si el nivel de estudios es diferente o si la fecha de ingreso es diferente; actualizar el registro
						if ($unDocenteParticipante ['nivel_est_code'] != $unDocente ['NIVEL_EST_CODE'] or $unDocenteParticipante ['fecha_ingreso'] != $unDocente ['FECHA_INGRESO']) {
							$this->miComponente->actualizarDocente ( $unDocente );
							echo $unDocente ['CODIGO_UNICO'] . ' Docente actualizado<br>';
						}
					} else {
						// Borra los registros
						// El filtro es codigo y tipo de documento que aparece en la tabla participante
						// OJO, NO es el obtenido de la DB académica
						$docenteError ['CODIGO_UNICO'] = $unDocenteParticipante ['codigo_unico'];
						$docenteError ['TIPO_DOC_UNICO'] = $unDocenteParticipante ['tipo_doc_unico'];
						
						$this->miComponente->borrarDocente_h ( $docenteError );
						$this->miComponente->borrarDocente ( $docenteError );
						
						$docenteParticipante = $this->miComponente->consultarDocente ( $unDocente );
						
						// si no existe insertar el nuevo registro
						if ($docenteParticipante == false) {
							$this->miComponente->registrarDocente ( $unDocente );
							echo $unDocente ['CODIGO_UNICO'] . ' Nuevo<br>';
						}
						
						echo $unDocente ['CODIGO_UNICO'] . ' borrado<br>';
					}
				}
			}
		}
		echo 'actualización docente terminado <br>';
	}
	
	/**
	 * Pasos
	 * 1.
	 * Borrar regitro para el año y semestre dado
	 * 2. Ajustar dedicación
	 * 3. Registrar valores
	 *
	 * @param unknown $docente        	
	 */
	function actualizarDocente_h($docente) {
		echo 'Actualizando docentes_h...<br>';
		// Borrar todos los registros para un perído definido
		$this->miComponente->borrarDocente_hPeriodoTodos ( $this->annio, $this->semestre );
		$vinculacionDocente = $this->miComponente->consultarVinculacionDocente ( $this->annio, $this->semestre );
		
		// codificar vinculacion docente
		foreach ( $vinculacionDocente as $clave => $valor ) {
			switch ($vinculacionDocente [$clave] ['VINCULACION']) {
				case 1 :
					$vinculacionDocente [$clave] ['TIPO_CONTRATO'] = '01';
					$vinculacionDocente [$clave] ['DEDICACION'] = '01';
					break;
				case 2 :
					$vinculacionDocente [$clave] ['TIPO_CONTRATO'] = '02';
					$vinculacionDocente [$clave] ['DEDICACION'] = '01';
					break;
				case 3 :
					$vinculacionDocente [$clave] ['TIPO_CONTRATO'] = '02';
					$vinculacionDocente [$clave] ['DEDICACION'] = '02';
					break;
				case 4 :
					$vinculacionDocente [$clave] ['TIPO_CONTRATO'] = '02';
					$vinculacionDocente [$clave] ['DEDICACION'] = '04';
					break;
				case 5 :
					$vinculacionDocente [$clave] ['TIPO_CONTRATO'] = '03';
					$vinculacionDocente [$clave] ['DEDICACION'] = '04';
					break;
				case 6 :
					$vinculacionDocente [$clave] ['TIPO_CONTRATO'] = '01';
					$vinculacionDocente [$clave] ['DEDICACION'] = '02';
					break;
				case 7 :
					$vinculacionDocente [$clave] ['TIPO_CONTRATO'] = '03';
					$vinculacionDocente [$clave] ['DEDICACION'] = '03';
					break;
				case 8 :
					$vinculacionDocente [$clave] ['TIPO_CONTRATO'] = '01';
					$vinculacionDocente [$clave] ['DEDICACION'] = '01';
					break;
				
				default :
					echo 'Sin vinculación';
					break;
			}
		}
		
		foreach ( $docente as $key => $value ) {
			$docente [$key] ['DEDICACION'] = '04';
			$docente [$key] ['TIPO_CONTRATO'] = '03';
			foreach ( $vinculacionDocente as $unaVinculacion ) {
				if ($docente [$key] ['CODIGO_UNICO'] == $unaVinculacion ['DOCUMENTO']) {
					if ($docente [$key] ['DEDICACION'] > $unaVinculacion ['DEDICACION']) {
						$docente [$key] ['DEDICACION'] = $unaVinculacion ['DEDICACION'];
					}
					if ($docente [$key] ['TIPO_CONTRATO'] > $unaVinculacion ['TIPO_CONTRATO']) {
						$docente [$key] ['TIPO_CONTRATO'] = $unaVinculacion ['TIPO_CONTRATO'];
					}
				}
			}
		}
		
		foreach ($docente as $unDocente) {
			$this->miComponente->registrarDocente_h ( $unDocente, $this->annio, $this->semestre );
		}
		

		echo 'SE DEBE INCLUIR LA UNIDAD ORGANIACIONAL ES DECIR LA FACULTAD';
		echo 'Actualización docente_h terminado <br>';
	
		exit ();
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );

$resultado = $miProcesador->procesarFormulario ();

