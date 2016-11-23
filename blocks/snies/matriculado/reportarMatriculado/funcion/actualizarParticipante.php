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
		 * 1. Consultar los datos de los estudiantes para un período
		 * 2. Quitar acentos
		 * 3. Dividir nombres
		 * 4. Actualizar en PARTICIPANTE
		 */
		
		// estudiante de la académica
		echo 'Consultando estudiantes...<br>';
		$estudiante = $this -> miComponente -> consultarEstudianteAcademica($this -> annio, $this -> semestre);
		
		$miProcesadorNombre = new procesadorNombre();
		
		echo 'Eliminando caracteres no válidos...<br>';
		//Busca y presenta los caracteres inválidos
		$caracteresInvalidos = $miProcesadorNombre -> buscarCaracteresInvalidos($estudiante, 'EST_NOMBRE');

		// quita acentos del nombre
		$estudiante = $miProcesadorNombre -> quitarAcento($estudiante, 'EST_NOMBRE');		

		echo 'Separando nombres...<br>';
		// descompone nombre completo en sus partes y las aglega al final de cada registro
		foreach ($estudiante as $clave => $valor) {
			// echo $estudiante [$clave] ['NUM_DOCUMENTO'].'<br>';
			$nombreCompleto = $miProcesadorNombre -> dividirNombreCompleto($estudiante[$clave]['EST_NOMBRE']);
			$estudiante[$clave]['PRIMER_APELLIDO'] = $nombreCompleto['primer_apellido'];
			$estudiante[$clave]['SEGUNDO_APELLIDO'] = $nombreCompleto['segundo_apellido'];
			$estudiante[$clave]['PRIMER_NOMBRE'] = $nombreCompleto['primer_nombre'];
			$estudiante[$clave]['SEGUNDO_NOMBRE'] = $nombreCompleto['segundo_nombre'];
		}

		echo 'Codificando valores nulos...<br>';
		$miProcesadorExcepcion = new procesadorExcepcion();

		// FORMATEA LOS VALORES NULOS, CODIFICA EXCEPCIONES

		//************************************/// OJO REVISAR LAS EXCEPCIONES
		$estudiante = $miProcesadorExcepcion -> procesarExcepcionEstudiante($estudiante);
		//var_dump($estudiante);
		echo 'Actualizando participantes <br>';
		$this -> actualizarParticipante($estudiante);

		// $valorCodificado = "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
		// $valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );

		// Rescatar el parámetro enlace desde los datos de configuraión en la base de datos
		// $variable = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
		// $miEnlace = $this->host . $this->site . '/index.php?' . $variable . '=' . $valorCodificado;

		// header ( "Location:$miEnlace" );
		exit();
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
		foreach ($estudiante as $unEstudiante) {
			echo 'N. DOCUMENTO: ' . $unEstudiante['NUM_DOCUMENTO'] . '<br>';
			// consulta en la tabla participante y cuenta el número de registros retornados
			$participante = $this -> miComponente -> consultarParticipante($unEstudiante);
			//echo 'Participante en SNIES';
			//var_dump($participante);			
			// si no existe insertar el nuevo registro
			if ($participante == false) {
				$this -> miComponente -> registrarParticipante($unEstudiante);
				echo $unEstudiante['NUM_DOCUMENTO'] . ' Nuevo<br>';
			} else {
				// Si existe y es igual el tipo actualizar si no es igual borrar
				foreach ($participante as $unParticipante) {
					if ($unParticipante['id_tipo_documento'] == $unEstudiante['ID_TIPO_DOCUMENTO']) {
						//Mejoras de rendimiento: se debe verificar por software las diferencias entre los dos registros 
						// si hay diferencias hacer la actializacion si no hay, no hacerlo.
						$this -> miComponente -> actualizarParticipante($unEstudiante);
						//echo $unEstudiante ['NUM_DOCUMENTO'] . ' actualizado<br>';exit;
					} else {
						// Borra los registros
						// El filtro es número y tipo de documento que aparece en la tabla participante
						// OJO, NO es el obtenido de la DB académica
						$estudianteError['NUM_DOCUMENTO'] = $unParticipante['num_documento'];
						$estudianteError['ID_TIPO_DOCUMENTO'] = $unParticipante['id_tipo_documento'];

						$this -> miComponente -> borrarParticipante($estudianteError);

						$participante = $this -> miComponente -> consultarParticipante($unEstudiante);

						// si no existe insertar el nuevo registro
						if ($participante == false) {
							$this -> miComponente -> registrarParticipante($unEstudiante);
							echo $unEstudiante['NUM_DOCUMENTO'] . ' Nuevo<br>';
						}

						echo $unEstudiante['NUM_DOCUMENTO'] . ' borrado<br>';
					}
				}
			}
		}
		echo 'terminado';
	}

}

$miProcesador = new FormProcessor($this -> lenguaje, $this -> sql);

$resultado = $miProcesador -> procesarFormulario();
