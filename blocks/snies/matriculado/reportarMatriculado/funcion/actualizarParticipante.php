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
		echo '<b>PARTICIPANTE - Inicio del proceso...</b><br>';
		$this -> registrarParticipante($estudiante);

		// $valorCodificado = "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
		// $valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );

		// Rescatar el parámetro enlace desde los datos de configuraión en la base de datos
		// $variable = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
		// $miEnlace = $this->host . $this->site . '/index.php?' . $variable . '=' . $valorCodificado;

		// header ( "Location:$miEnlace" );
		exit();
	}

	/**
	 * Funcion que inserta, actualiza o borra en la tabla inscritos de SNIES
	 * Registra si no existe en SNIES
	 * Actualiza si existe en el SNIES
	 * Borra si no está en la ACADEMICA
	 */
	function registrarParticipante($estudiante) {

		//definir clave del arreglo como DOCUMENTO_ID_TIPO_DOCUMENTO ejemplo: 1000124545CC
		foreach ($estudiante as $key => $value) {
			$participanteAcademica[$estudiante[$key]['NUM_DOCUMENTO'] . $estudiante[$key]['ID_TIPO_DOCUMENTO']] = $value;
		}

		// CONSULTA LA TABLA INSCRITO SNIES
		$participante = $this -> miComponente -> consultarParticipanteTodos($this -> annio, $this -> semestre);

		//Coloca en el indice de cada arreglo de lo consultado en el SNIES ano||semestre||id_tipo_documento||documento
		if ($participante != NULL) {
			foreach ($participante as $key => $value) {
				$participanteSnies[$participante[$key]['num_documento'] . $participante[$key]['id_tipo_documento']] = $value;
			}

			//REGISTRA LOS NUEVOS PARTICIPANTES EN SNIES
			$participanteNuevo = array_diff_key($participanteAcademica, $participanteSnies);			
			//Estan en académica y no en SNIES, INSERTAR
			foreach ($participanteNuevo as $unParticipanteNuevo) {
				$this -> miComponente -> registrarParticipante($participanteNuevo);
			}
			echo 'NUEVOS<br>';

			//ACTUALIZA LOS QUE YA ESTAN EN SNIES
			echo 'ACTUALIZAR<br>';
			$inscritosActualizar = array_intersect_key($participanteAcademica, $participanteSnies);
			//aqui debería estar la función de actualizacion, por agilizar el tiempo de ejecución no se implementa en esta estapa
			//$this -> miComponente -> actualizarParticipante($unEstudiante);
			echo 'Registros existentes actualizados en inscrito<br>';

			//BORRA LOS QUE NO DEBERÍAN ESTAR EN SNIES, NO SE BORRAN
			//Los documentos que estén repetidos con diferente tipo se ajustan con la plantilla de correción de participantes de HECAA

		} else {

			//Estan en académica y no en SNIES, INSERTAR
			foreach ($estudiante as $unEstudiante) {
				$this -> miComponente -> registrarParticipante($unEstudiante);
			}
			echo 'Registros nuevos insertados en inscrito<br>';
		}

	}

}

$miProcesador = new FormProcessor($this -> lenguaje, $this -> sql);

$resultado = $miProcesador -> procesarFormulario();
