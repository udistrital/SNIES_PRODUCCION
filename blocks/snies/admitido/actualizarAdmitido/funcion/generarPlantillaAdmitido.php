<?php
include_once ('component/GestorInscritoAdmitido/Componente.php');
include_once ('blocks/snies/funcion/procesadorNombre.class.php');
include_once ('blocks/snies/funcion/procesadorExcepcion.class.php');

use sniesInscritoAdmitido\Componente;
use bloqueSnies\procesadorExcepcion;
use bloqueSnies\procesadorNombre;
class FormProcessor {
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miSql;
	var $conexion;
	function __construct($lenguaje, $sql) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->lenguaje = $lenguaje;
		$this->miSql = $sql;
		$this->miComponente = new Componente ();
		$this->host = $this->miConfigurador->getVariableConfiguracion ( "host" );
		$this->site = $this->miConfigurador->getVariableConfiguracion ( "site" );
		$this->esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
	}

	/**
	*Esta Función genera el archivo CSV que se reporta al sistema HECAA del SNIES
	*/
	function procesarFormulario() {
		$annio = $_REQUEST ['annio'];
		$semestre = $_REQUEST ['semestre'];

		/**
		 * Consultar admitidos pregrado y prostgrado
		 */
		$admitidosPregrado = $this->miComponente->consultarAdmitidoPregradoAcademica ( $annio, $semestre );
		$admitidosPostgrado = $this->miComponente->consultarAdmitidoPostgradoAcademica ( $annio, $semestre );

		$admitidos=array_merge($admitidosPregrado,$admitidosPostgrado);

		$this->generarPlantillaAdmitido ( $admitidos );
		echo 'Se ha generado un archivo CSV "admitidos_año_semestre" en el directorio document de la aplicación';


	}

	function generarPlantillaAdmitido($admitido) {
		$raizDocumento = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );

		$this->annio=$_REQUEST['annio'];
		$this->semestre=$_REQUEST['semestre'];
		$fp = fopen ( $raizDocumento . '/document/admitidos_' . $this->annio . $this->semestre . '.csv', 'w' );
		//ENCABEZADO DE LA PLANTILLA
		fputcsv ( $fp, array ('Herramienta de Cargue Hecca - V 3.1'));
		fputcsv ( $fp, array ('[59]', 'Nombre de la Plantilla: [Admitidos] Descripcion: [Persona natural que, previo el proceso de selección realizado por el programa académico o la institución de educación superior y el cumplimiento de los'));
		fputcsv ( $fp, array ('requisitos de ley, es aceptado para iniciar el proceso de formalización como estudiante en el programa al que se inscribió.]'));
		fputcsv ( $fp, array ('Licenciado para Ministerio de Educacion Nacional 2014'));
		fputcsv ( $fp, array (
				'AÑO',
				'SEMESTRE',
				'ID_TIPO_DOCUMENTO',
				'NUM_DOCUMENTO',
				'PRO_CONSECUTIVO',
				'ID_MUNICIPIO'
		) , ";");
		foreach ( $admitido as $unadmitido ) {
			 //var_dump ( $unadmitido );exit;
			$Relacionadmitido ['AÑO'] = $unadmitido ['ANNIO'];
			$Relacionadmitido ['SEMESTRE'] = $unadmitido ['SEMESTRE'];
			$Relacionadmitido ['ID_TIPO_DOCUMENTO'] = $unadmitido ['ID_TIPO_DOCUMENTO'];
			$Relacionadmitido ['NUM_DOCUMENTO'] = $unadmitido ['NUM_DOCUMENTO'];
			$Relacionadmitido ['PRO_CONSECUTIVO'] = $unadmitido ['PRO_CONSECUTIVO'];
			$Relacionadmitido ['ID_MUNICIPIO'] = $unadmitido ['ID_MUNICIPIO'];

			fputcsv ( $fp, $Relacionadmitido, ";" );
		}

		fclose ( $fp );

	}

}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );

$resultado = $miProcesador->procesarFormulario ();
