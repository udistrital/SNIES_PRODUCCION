<?php
include_once ('component/GestorInscritoAdmitido/Componente.php');
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
		 * 5.Insertar los registros en el SNIES LOCAL
		 * 6.Redireccionar a lista de variables
		 */
		//$admitidos = $this->miComponente->consultarAdmitidoPregradoAcademica ( $annio, $semestre );
		$admitidos = $this->miComponente->consultarAdmitidoPostgradoAcademica ( $annio, $semestre );
		var_dump ( $admitidos );
		exit ();
		
		// $miProcesadorNombre = new procesadorNombre ();
		
		// $admitidos=$miProcesadorNombre->quitarAcento($admitidos, 'PRIMER_NOMBRE');
		// $admitidos=$miProcesadorNombre->quitarAcento($admitidos, 'SEGUNDO_NOMBRE');
		// $admitidos=$miProcesadorNombre->quitarAcento($admitidos, 'PRIMER_APELLIDO');
		// $admitidos=$miProcesadorNombre->quitarAcento($admitidos, 'SEGUNDO_APELLIDO');
		// $admitidos=$miProcesadorNombre->quitarAcento($admitidos, 'PROG');
		
		$borraradmitidos = $this->miComponente->borrarAdmitidoSnies ( $annio, $semestre );
		
		foreach ( $admitidos as $admitido ) {
			
			$insertarAdmitido = $this->miComponente->insertarAdmitidoSnies ( $admitido );
		}
		
		$valorCodificado = "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
		// $valorCodificado .= "&action=" . $this->esteBloque ["nombre"];
		// $valorCodificado .= '&bloqueGrupo='.$this->esteBloque ['grupo'];
		// $valorCodificado .= "&opcion=".$opcion;
		// $valorCodificado .= "&annio=".$this->annio;
		// $valorCodificado .= "&semestre=".$this->semestre;
		$valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );
		
		// Rescatar el parámetro enlace desde los datos de configuraión en la base de datos
		$variable = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
		$miEnlace = $this->host . $this->site . '/index.php?' . $variable . '=' . $valorCodificado;
		
		header ( "Location:$miEnlace" );
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );

$resultado = $miProcesador->procesarFormulario ();

