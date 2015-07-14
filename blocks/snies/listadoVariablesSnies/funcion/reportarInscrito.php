<?php
include_once ('component/GestorSniesLocal/Componente.php');
include_once ('blocks/snies/listadoVariablesSnies/funcion/procesadorNombre.class.php');
use snies\Componente;
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
		 * Esta función realiza las siguientes acciones
		 * 1.consulta en la académica
		 * 2.Procesar los datos obtenidos, cambiar acentos.
		 * 3.Registrar errores de la fuente para reportarlos
		 * 4.Borrar los registros para el año y periodo seleccionado en SNIES LOCAL
		 * 4.Insertar los registros en el SNIES LOCAL
		 */
		$inscritos = $this->miComponente->consultarInscritoAcademica ( $annio, $semestre );
		
		$miProcesadorNombre = new procesadorNombre ();
		
		// $inscritos=$miProcesadorNombre->quitarAcento($inscritos, 'PRIMER_NOMBRE');
		// $inscritos=$miProcesadorNombre->quitarAcento($inscritos, 'SEGUNDO_NOMBRE');
		// $inscritos=$miProcesadorNombre->quitarAcento($inscritos, 'PRIMER_APELLIDO');
		// $inscritos=$miProcesadorNombre->quitarAcento($inscritos, 'SEGUNDO_APELLIDO');
		// $inscritos=$miProcesadorNombre->quitarAcento($inscritos, 'PROG');
		
		$borrarInscritos = $this->miComponente->borrarInscritoSnies ( $annio, $semestre );
		
		foreach ( $inscritos as $inscrito ) {
			
			$insertarInscrito = $this->miComponente->insertarInscritoSnies ($inscrito);
						
		}
		
		exit ();
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );

$resultado = $miProcesador->procesarFormulario ();

