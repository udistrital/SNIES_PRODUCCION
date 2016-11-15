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
		
		// estudiante de la académica
		$graduado = $this->miComponente->consultarGraduadoAcademica ( $this->annio, $this->semestre );
		var_dump($graduado);exit;
		
		$miProcesadorExcepcion = new procesadorExcepcion ();
		// FORMATEA LOS VALORES NULOS, CODIFICA EXCEPCIONES
		$graduado = $miProcesadorExcepcion->procesarExcepcionGraduado ( $graduado );
		
		$this->actualizarGraduado ( $graduado );
		
		exit ();
	}
	
	/**
	 *
	 * Función que actualiza o registra los datos de la tabla ESTUDIANTE_PROGRAMA DEL SNIES (Se refiere a estudiantes de primer semestre):
	 * Si no existe el registro en la tabla lo registra
	 * Si existe el registo lo actualiza
	 *
	 * @param array $estudiante        	
	 */
	function actualizarGraduado($graduado) {
		
		// borrar todos los registros de estudiante_programa para el periodo seleccionado
		$this->miComponente->borrarGraduadoPeriodoTodos ( $this->annio, $this->semestre );
		
		// registrar los estudiantes de la cohorte seleccionada, año y período
		foreach ( $graduado as $unGraduado ) {
			
			if ($unGraduado ['GRAD_ANNIO'] == $this->annio and $unGraduado ['GRAD_SEMESTRE'] == $this->semestre) {
				
				$this->miComponente->registrarGraduado ( $unGraduado );
			}
		}
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );

$resultado = $miProcesador->procesarFormulario ();

