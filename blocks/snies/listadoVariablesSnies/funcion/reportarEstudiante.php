<?php
include_once ('component/GestorEstudiante/Componente.php');
include_once ('blocks/snies/listadoVariablesSnies/funcion/procesadorNombre.class.php');
use sniesEstudiante\Componente;
use bloqueSnies\procesadorNombre;
class FormProcessor {
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miSql;
	var $conexion;
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
		$annio = $_REQUEST ['annio'];
		$semestre = $_REQUEST ['semestre'];
		
		/**
		 * Esta funcion consulta todos los datos de los estudiantes de un período definido
		 * en la BD académica para poblar la tablas de SNIES:
		 * PARTICIPANTE
		 * ESTUDIANTE
		 * ESTUDIANTE_PROGRAMA - primer semestre
		 * MATRICULADO
		 */
		
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
		$estudiante = $this->miComponente->consultarEstudiante ( $annio, $semestre );
		
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
		
		$this->actualizarParticipante ( $estudiante );
		
		echo 'no se deja borrar por que es una fk de estudiante programa QUE HACEMOS';
		// borra el registro anterior y registra el nuevo en la tabla ESTUDIANTE del SNIES
		foreach ( $estudiante as $unEstudiante ) {
			// var_dump($unEstudiante);
			$borradoEstudiante = $this->miComponente->borrarEstudiante ( $unEstudiante );
			echo 'borrado';
			exit ();
			if ($borradoEstudiante == true) {
				$registroParticipanteEstudiante = $this->miComponente->registrarEstudiante ( $unEstudiante );
			}
		}
		// borra el registro anterior y registra el nuevo en la tabla ESTUDIANTE_PROGRAMA del SNIES
		
		// borra el registro anterior y registra el nuevo en la tabla MATRICULADO del SNIES
		
		exit ();
		
		exit ();
		
		foreach ( $estudianteAcademica as $estudiante ) {
			// echo $estudiante['CODIGO_UNICO']." ".$estudiante['TIPO_DOC_UNICO'].'<br>';
			$resultado = $this->miComponente->consultarEstudianteSNIES ( $estudiante ['CODIGO_UNICO'] );
		}
		exit ();
		// $estudianteSNIES = $this->miComponente->consultarEstudianteSnies ();
		
		var_dump ( $estudianteSNIES );
		
		exit ();
		
		// en el caso de que no se haga la consulta redirecciona
		if ($estudiante == false) {
			$valorCodificado = "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
			$valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );
			
			// Rescatar el parámetro enlace desde los datos de configuraión en la base de datos
			$variable = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
			$miEnlace = $this->host . $this->site . '/index.php?' . $variable . '=' . $valorCodificado;
			
			header ( "Location:$miEnlace" );
		}
		
		// insertar si no existe o actualizar si existe y es diferente
		$estudianteSNIES = 

		exit ();
		
		// borra todos los registros de la tabla estudiante del SNIES LOCAL
		$borrarEstudiantes = $this->miComponente->borrarEstudiante ();
		exit ();
		
		$valorCodificado = "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
		$valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );
		
		// Rescatar el parámetro enlace desde los datos de configuraión en la base de datos
		$variable = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
		$miEnlace = $this->host . $this->site . '/index.php?' . $variable . '=' . $valorCodificado;
		
		header ( "Location:$miEnlace" );
	}
	function actualizarParticipante($estudiante) {
		foreach ( $estudiante as $unEstudiante ) {
			
			$verificarParticipante = $this->miComponente->cosultarParticipante ( $unEstudiante );
			if (is_array($verificarParticipante)) {
				$this->miComponente->actualizarParticipante ( $unEstudiante );
				echo 'agregar las excepciones a la consulta de actualzación';
				exit;
			} else {
				$this->miComponente->insertarParticipante ( $unEstudiante );
			}
			var_dump ( $verificarParticipante );
			exit ();
			$borradoParticipanteEstudiante = $this->miComponente->borrarParticipanteEstudiante ( $unEstudiante );
			
			if ($borradoParticipanteEstudiante == true) {
				$registroParticipanteEstudiante = $this->miComponente->registrarParticipanteEstudiante ( $unEstudiante );
			}
		}
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );

$resultado = $miProcesador->procesarFormulario ();

