<?php
include_once ('component/GestorDocente/Componente.php');
include_once ('blocks/snies/matriculado/reportarMatriculado/funcion/procesadorNombre.class.php');
include_once ('blocks/snies/matriculado/reportarMatriculado/funcion/procesadorExcepcion.class.php');
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
		
		// estudiante de la académica
		$docente = $this->miComponente->consultarDocenteAcademica ( $this->annio, $this->semestre );
		
		$miProcesadorNombre = new procesadorNombre ();
		
		// $caracteresInvalidos = $miProcesadorNombre->buscarCaracteresInvalidos ( $docente, 'DOC_APELLIDO' );
		// $caracteresInvalidos = $miProcesadorNombre->buscarCaracteresInvalidos ( $docente, 'DOC_NOMBRE' );
		
		// quita acentos del nombre
		// $docente = $miProcesadorNombre->quitarAcento ( $docente, 'EST_NOMBRE' );
		
		// descompone nombre completo en sus partes y las aglega al final de cada registro
		foreach ( $docente as $clave => $valor ) {
			
			$apellidoCompleto = $miProcesadorNombre->dividirApellidos ( $docente [$clave] ['DOC_APELLIDO'] );
			$docente [$clave] ['PRIMER_APELLIDO'] = $apellidoCompleto ['primer_apellido'];
			$docente [$clave] ['SEGUNDO_APELLIDO'] = $apellidoCompleto ['segundo_apellido'];
			
			$nombreCompleto = $miProcesadorNombre->dividirNombres ( $docente [$clave] ['DOC_NOMBRE'] );
			$docente [$clave] ['PRIMER_NOMBRE'] = $nombreCompleto ['primer_nombre'];
			$docente [$clave] ['SEGUNDO_NOMBRE'] = $nombreCompleto ['segundo_nombre'];
		}
		
		$miProcesadorExcepcion = new procesadorExcepcion ();
		// FORMATEA LOS VALORES NULOS, CODIFICA EXCEPCIONES
		$docente = $miProcesadorExcepcion->procesarExcepcionEstudiante ( $docente );
		
		$this->generarListadoDocentes ( $docente );
		$raizDocumento = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
		echo 'Se ha generado el archivo: ' . $raizDocumento . '/document/docente' . $this->annio . $this->semestre . '.csv';
		echo '<br>';

	/**
	 * $valorCodificado = "&pagina=" .
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 * $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
	 * $valorCodificado .= "&opcion=auditoriaMatriculado";
	 * $valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );
	 * //Rescatar el parámetro enlace desde los datos de configuraión en la base de datos
	 * $variable = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
	 * $miEnlace = $this->host . $this->site . '/index.php?' . $variable . '=' . $valorCodificado;
	 *
	 * header( "Location:$miEnlace" );
	 */
	}
	function generarListadoDocentes($docente) {
		$raizDocumento = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
		$fp = fopen ( $raizDocumento . '/document/docente_' . $this->annio . $this->semestre . '.csv', 'w' );
		fputcsv ( $fp, array (
				'IDPROGRAMACION',
				'IES_CODE',
				'ANNIO',
				'SEMESTRE',
				'TIPO_DOCUMENTO',
				'NUM_DOCUMENTO',
				'PRIMER_NOMBRE',
				'SEGUNDO_NOMBRE',
				'PRIMER_APELLIDO',
				'SEGUNDO_APELLIDO',
				'GENERO',
				'NIVEL_EST_CODE',
				'DEDICACION',
				'TIPO_CONTRATO',
				'FECHA_INGRESO'				
		) );
		foreach ( $docente as $unDocente ) {
	
			$arregloDocente ['CODIGO'] = $unDocente ['CODIGO_UNICO'];
			$arregloDocente ['IES_CODE'] = '1301';
			$arregloDocente ['ANIO'] = $this->annio;
			$arregloDocente ['SEMESTRE'] = $this->semestre;
			$arregloDocente ['TIPO_DOC_UNICO'] = $unDocente ['TIPO_DOC_UNICO'];
			$arregloDocente ['CODIGO_UNICO'] = $unDocente ['CODIGO_UNICO'];
			$arregloDocente ['PRIMER_NOMBRE'] = $unDocente ['PRIMER_NOMBRE'];
			$arregloDocente ['SEGUNDO_NOMBRE'] = $unDocente ['SEGUNDO_NOMBRE'];
			$arregloDocente ['PRIMER_APELLIDO'] = $unDocente ['PRIMER_APELLIDO'];
			$arregloDocente ['SEGUNDO_APELLIDO'] = $unDocente ['SEGUNDO_APELLIDO'];
			$arregloDocente ['GENERO'] = $unDocente ['GENERO_CODE'];
			$arregloDocente ['NIVEL_EST_CODE'] = $unDocente ['NIVEL_EST_CODE'];
			$arregloDocente ['DEDICACION'] = $unDocente ['NIVEL_EST_CODE'];
			$arregloDocente ['TIPO_CONTRATO'] = $unDocente ['NIVEL_EST_CODE'];
			$arregloDocente ['FECHA_INGRESO'] = $unDocente ['FECHA_INGRESO'];
			
			fputcsv ( $fp, $arregloDocente );
		}
		
		fclose ( $fp );
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );

$resultado = $miProcesador->procesarFormulario ();
