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
		
		echo 'se debe crear un método que maneje las excepciones del arreglo para no realizarlas en la consulta. Ejemplo: Si el municipio es suba
				cambiarlo por Bogotá, si no existe el mail ingresar vacio';
		
		echo 'proceso 1';
		$this->actualizarParticipante ( $estudiante );
		echo 'proceso 2';
		$this->actualizarEstudiante ( $estudiante );
		echo 'proceso 3';
		$this->actualizarEstudiantePrograma ( $estudiante );
		echo 'actualizado hasta estudiante_programa';
		exit ();
		
		$valorCodificado = "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
		$valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );
		
		// Rescatar el parámetro enlace desde los datos de configuraión en la base de datos
		$variable = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
		$miEnlace = $this->host . $this->site . '/index.php?' . $variable . '=' . $valorCodificado;
		
		header ( "Location:$miEnlace" );
	}
	
	/**
	 * Función que actualiza o registra los datos de la tabla PARTICIPANTE DEL SNIES
	 * 1.
	 * consulta si el estudiante existe en la tabla PARTICIPANTE
	 * Si existe lo actualiza, si no existe lo registra
	 *
	 * @param array $estudiante
	 *        	datos de estudiante
	 */
	function actualizarParticipante($estudiante) {
		$a = 0;
		foreach ( $estudiante as $unEstudiante ) {
			$a ++;
			echo $a . '<br>';
			$verificarParticipante = $this->miComponente->cosultarParticipante ( $unEstudiante );
			if (is_array ( $verificarParticipante )) {
				$this->miComponente->actualizarParticipante ( $unEstudiante );
			} else {
				$this->miComponente->registrarParticipante ( $unEstudiante );
			}
		}
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
			
			$verificarEstudiante = $this->miComponente->consultarEstudiante ( $unEstudiante );
			if (is_array ( $verificarEstudiante )) {
				$this->miComponente->actualizarEstudiante ( $unEstudiante );
			} else {
				$this->miComponente->registrarEstudiante ( $unEstudiante );
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
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );

$resultado = $miProcesador->procesarFormulario ();

