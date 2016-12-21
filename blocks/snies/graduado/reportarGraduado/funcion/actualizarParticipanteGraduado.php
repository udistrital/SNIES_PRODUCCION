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

	/**
	 * ESTO ACTUALIZA LAS TABLAS PARTICIPANTE, PRIMER_CURSO Y GRADUADO
	 */
	function procesarFormulario() {

		$this -> annio = $_REQUEST['annio'];
		$this -> semestre = $_REQUEST['semestre'];



		// graduado de la académica

		$graduado = $this -> miComponente -> consultarGraduadoAcademica($this -> annio, $this -> semestre);

		$miProcesadorNombre = new procesadorNombre();

		//Busca y presenta los caracteres inválidos
		$caracteresInvalidos = $miProcesadorNombre -> buscarCaracteresInvalidos($graduado, 'EST_NOMBRE');

		// quita acentos del nombre
		$graduado = $miProcesadorNombre -> quitarAcento($graduado, 'EST_NOMBRE');

		// descompone nombre completo en sus partes y las aglega al final de cada registro
		foreach ($graduado as $clave => $valor) {
			// echo $estudiante [$clave] ['CODIGO_UNICO'].'<br>';
			$nombreCompleto = $miProcesadorNombre -> dividirNombreCompleto($graduado[$clave]['EST_NOMBRE']);
			$graduado[$clave]['PRIMER_APELLIDO'] = $nombreCompleto['primer_apellido'];
			$graduado[$clave]['SEGUNDO_APELLIDO'] = $nombreCompleto['segundo_apellido'];
			$graduado[$clave]['PRIMER_NOMBRE'] = $nombreCompleto['primer_nombre'];
			$graduado[$clave]['SEGUNDO_NOMBRE'] = $nombreCompleto['segundo_nombre'];
		}

		$miProcesadorExcepcion = new procesadorExcepcion();
		// FORMATEA LOS VALORES NULOS, CODIFICA EXCEPCIONES
		$graduado = $miProcesadorExcepcion -> procesarExcepcionGraduado($graduado);
		
		var_dump($graduado);

		$this -> actualizarParticipante($graduado);
		//$this -> actualizarPrimerCurso($graduado);// no hacer esto por que actualiza la cohorte de ingreso, que problema!
		$this->actualizarGraduado($graduado);

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
			// echo 'CODIGO: ' . $unEstudiante ['CODIGO_UNICO'] . '<br>';
			// consulta enla tabla participante y cuenta el número de registros retornados
			$participante = $this -> miComponente -> consultarParticipante($unEstudiante);

			// si no existe insertar el nuevo registro
			if ($participante == false) {
				$this -> miComponente -> registrarParticipante($unEstudiante);
				echo $unEstudiante['NUM_DOCUMENTO'] . ' Nuevo<br>';
			} else {
				foreach ($participante as $unParticipante) {
					// Si existe y es igual el tipo actualizar si no es igual borrar
					if ($unParticipante['id_tipo_documento'] == $unEstudiante['ID_TIPO_DOCUMENTO']) {
						$this -> miComponente -> actualizarParticipante($unEstudiante);
						// echo $unEstudiante ['CODIGO_UNICO'] . ' actualizado<br>';
					} else {
						// Borra los registros
						// El filtro es codigo y tipo de documento que aparece en la tabla participante
						// OJO, NO es el obtenido de la DB académica
						$estudianteError['CODIGO_UNICO'] = $unParticipante['codigo_unico'];
						$estudianteError['TIPO_DOC_UNICO'] = $unParticipante['tipo_doc_unico'];

						$this -> miComponente -> borrarParticipante($estudianteError);

						$participante = $this -> miComponente -> consultarParticipante($unEstudiante);

						// si no existe insertar el nuevo registro
						if ($participante == false) {
							$this -> miComponente -> registrarParticipante($unEstudiante);
							echo $unEstudiante['CODIGO_UNICO'] . ' Nuevo<br>';
						}

						echo $unEstudiante['CODIGO_UNICO'] . ' borrado<br>';
					}
				}
			}
		}
		echo 'terminado';
	}

	/**
	 *
	 * Función que actualiza o registra los datos de la tabla ESTUDIANTE_PROGRAMA DEL SNIES (Se refiere a estudiantes de primer semestre):
	 * Si no existe el registro en la tabla lo registra
	 * Si existe el registo lo actualiza
	 *
	 * @param array $estudiante
	 */
	function actualizarPrimerCurso($estudiante) {

		// registrar los estudiantes en su cohorte
		foreach ($estudiante as $unEstudiante) {

			//if ($unEstudiante['ANIO'] == $this -> annio and $unEstudiante['SEMESTRE'] == $this -> semestre) {

			$estudiantePrimerCurso = $this -> miComponente -> consultarEstudiantePrimerCurso($unEstudiante);

			//si existe el registro en la tabla primer_curso lo actualiza si no lo registra
			if (isset($estudiantePrimerCurso[0]['num_documento'])) {
				$this -> miComponente -> actualizarEstudiantePrimerCurso($unEstudiante);
			} else {
				$this -> miComponente -> registrarEstudiantePrimerCurso($unEstudiante);
			}

			//}
		}
	}

	/**
	 *
	 * @param array $estudiante
	 */
	function actualizarGraduado($graduado) {
		
		//Borrar registro del año y período de carga
		$this -> miComponente -> borrarGraduadoPeriodoTodos($this -> annio, $this -> semestre);

		// registrar el graduado para un proyecto específico. Solo se puede graduar una vez de un proyecto
		foreach ($graduado as $unGraduado) {

			$this -> miComponente -> registrarGraduado($unGraduado);
		
		}
		
		echo 'Registro de graduados terminado';

	}

}

$miProcesador = new FormProcessor($this -> lenguaje, $this -> sql);

$resultado = $miProcesador -> procesarFormulario();
