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
		$docenteDoctoradoMaestria = $this->miComponente->consultarDocenteDoctoradoMaestria ( $this->annio, $this->semestre );
		
		// $miProcesadorExcepcion = new procesadorExcepcion ();
		// FORMATEA LOS VALORES NULOS, CODIFICA EXCEPCIONES
		// $docente = $miProcesadorExcepcion->procesarExcepcionDocente ( $docenteDoctoradoMaestria );
		
		$this->actualizarDocenteDoctoradoMaestria ( $docenteDoctoradoMaestria );
		
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
	function actualizarDocenteDoctoradoMaestria($docente) {
		echo 'Actualizando docentes doctorado maestría...<br>';
		
		$this->miComponente->borrarDocenteDoctoradoMaestriaTodos ( $this->annio, $this->semestre );
		
		foreach ( $docente as $unDocente ) {
			
			$this->miComponente->registrarDocenteDoctoradoMaestria ( $unDocente );

		}
		echo 'actualización docente terminado <br>';
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );

$resultado = $miProcesador->procesarFormulario ();

