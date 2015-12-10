<?php
include_once ('component/GestorDocente/Componente.php');
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
		echo 'estoy consultando docente	';
		$docente = $this->miComponente->consultarDocenteAcademica ( $this->annio, $this->semestre );
		
		exit ();
		
		$miProcesadorNombre = new procesadorNombre ();
		
		// $caracteresInvalidos = $miProcesadorNombre->buscarCaracteresInvalidos ( $estudiante, 'EST_NOMBRE' );
		
		// quita acentos del nombre
		// $estudiante = $miProcesadorNombre->quitarAcento ( $estudiante, 'EST_NOMBRE' );
		
		// descompone nombre completo en sus partes y las aglega al final de cada registro
		foreach ( $estudiante as $clave => $valor ) {
			// echo $estudiante [$clave] ['CODIGO_UNICO'].'<br>';
			$nombreCompleto = $miProcesadorNombre->dividirNombreCompleto ( $estudiante [$clave] ['EST_NOMBRE'] );
			$estudiante [$clave] ['PRIMER_APELLIDO'] = $nombreCompleto ['primer_apellido'];
			$estudiante [$clave] ['SEGUNDO_APELLIDO'] = $nombreCompleto ['segundo_apellido'];
			$estudiante [$clave] ['PRIMER_NOMBRE'] = $nombreCompleto ['primer_nombre'];
			$estudiante [$clave] ['SEGUNDO_NOMBRE'] = $nombreCompleto ['segundo_nombre'];
		}
		
		$miProcesadorExcepcion = new procesadorExcepcion ();
		// FORMATEA LOS VALORES NULOS, CODIFICA EXCEPCIONES
		$estudiante = $miProcesadorExcepcion->procesarExcepcionEstudiante ( $estudiante );
		
		$this->generarListadoMatriculados ( $estudiante );
		$raizDocumento = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
		echo 'Se ha generado el archivo: ' . $raizDocumento . '/document/matriculado' . $this->annio . $this->semestre . '.csv';
		echo '<br>';
		$this->generarListadoEstudiantePrograma ( $estudiante );
		echo 'Se ha generado el archivo: ' . $raizDocumento . '/document/matriculadoPrimerCurso' . $this->annio . $this->semestre . '.csv';
		echo '<br>';
	/**
	 * $valorCodificado = "&pagina=" .
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
	function generarListadoMatriculados($estudiante) {
		$raizDocumento = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
		$fp = fopen ( $raizDocumento . '/document/matriculado_' . $this->annio . $this->semestre . '.csv', 'w' );
		fputcsv ( $fp, array (
				'IDPROGRAMACION',
				'ANNIO',
				'SEMESTRE',
				'IES_CODE',
				'PRO_CONSECUTIVO',
				'DEPARTAMENTO',
				'MUNICIPIO',
				'CERES',
				'TIPO_DOCUMENTO',
				'NUM_DOCUMENTO',
				'PRIMER_NOMBRE',
				'SEGUNDO_NOMBRE',
				'PRIMER_APELLIDO',
				'SEGUNDO_APELLIDO',
				'GENERO',
				'TRANSFERENCIA',
				'ESTUDIANTE_ARTICULACION',
				'GRADO_QUE_CURSA' 
		) );
		foreach ( $estudiante as $unEstudiante ) {
			// var_dump ( $unEstudiante );
			$matriculado ['CODIGO'] = $unEstudiante ['CODIGO_UNICO'];
			$matriculado ['ANIO'] = $this->annio;
			$matriculado ['SEMESTRE'] = $this->semestre;
			$matriculado ['IES_CODE'] = '1301';
			$matriculado ['PRO_CONSECUTIVO'] = $unEstudiante ['PRO_CONSECUTIVO'];
			$matriculado ['DEPATAMENTO'] = '11';
			$matriculado ['MUNICIPIO'] = '11001';
			$matriculado ['CERES'] = '1301';
			$matriculado ['TIPO_DOC_UNICO'] = $unEstudiante ['TIPO_DOC_UNICO'];
			$matriculado ['CODIGO_UNICO'] = $unEstudiante ['CODIGO_UNICO'];
			$matriculado ['PRIMER_NOMBRE'] = $unEstudiante ['PRIMER_NOMBRE'];
			$matriculado ['SEGUNDO_NOMBRE'] = $unEstudiante ['SEGUNDO_NOMBRE'];
			$matriculado ['PRIMER_APELLIDO'] = $unEstudiante ['PRIMER_APELLIDO'];
			$matriculado ['SEGUNDO_APELLIDO'] = $unEstudiante ['SEGUNDO_APELLIDO'];
			$matriculado ['GENERO'] = $unEstudiante ['GENERO_CODE'];
			$matriculado ['ES_TRANSFERENCIA'] = $unEstudiante ['ES_TRANSFERENCIA'];
			$matriculado ['ESTUDIANTE_ARTICULACION'] = '02';
			$matriculado ['GRADO_QUE_CURSA'] = '0';
			
			fputcsv ( $fp, $matriculado );
		}
		
		fclose ( $fp );
	}
	function generarListadoEstudiantePrograma($estudiante) {
		$raizDocumento = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
		$archivoMatriculadoPrimerCurso = fopen ( $raizDocumento . '/document/matriculadoPrimerCurso_' . $this->annio . $this->semestre . '.csv', 'w' );
		fputcsv ( $archivoMatriculadoPrimerCurso, array (
				'IDPROGRAMACION',
				'ANNIO',
				'SEMESTRE',
				'IES_CODE',
				'PRO_CONSECUTIVO',
				'DEPARTAMENTO',
				'MUNICIPIO',
				'CERES',
				'TIPO_DOCUMENTO',
				'NUM_DOCUMENTO',
				'PRIMER_NOMBRE',
				'SEGUNDO_NOMBRE',
				'PRIMER_APELLIDO',
				'SEGUNDO_APELLIDO',
				'GENERO' 
		) );
		foreach ( $estudiante as $unEstudiante ) {
			
			if ($unEstudiante ['ANIO'] == $this->annio and $unEstudiante ['SEMESTRE'] == $this->semestre) {
				// var_dump ( $unEstudiante );
				$matriculadoPrimerCurso ['CODIGO'] = $unEstudiante ['CODIGO_UNICO'];
				$matriculadoPrimerCurso ['ANIO'] = $this->annio;
				$matriculadoPrimerCurso ['SEMESTRE'] = $this->semestre;
				$matriculadoPrimerCurso ['IES_CODE'] = '1301';
				$matriculadoPrimerCurso ['PRO_CONSECUTIVO'] = $unEstudiante ['PRO_CONSECUTIVO'];
				$matriculadoPrimerCurso ['DEPATAMENTO'] = '11';
				$matriculadoPrimerCurso ['MUNICIPIO'] = '11001';
				$matriculadoPrimerCurso ['CERES'] = '1301';
				$matriculadoPrimerCurso ['TIPO_DOC_UNICO'] = $unEstudiante ['TIPO_DOC_UNICO'];
				$matriculadoPrimerCurso ['CODIGO_UNICO'] = $unEstudiante ['CODIGO_UNICO'];
				$matriculadoPrimerCurso ['PRIMER_NOMBRE'] = $unEstudiante ['PRIMER_NOMBRE'];
				$matriculadoPrimerCurso ['SEGUNDO_NOMBRE'] = $unEstudiante ['SEGUNDO_NOMBRE'];
				$matriculadoPrimerCurso ['PRIMER_APELLIDO'] = $unEstudiante ['PRIMER_APELLIDO'];
				$matriculadoPrimerCurso ['SEGUNDO_APELLIDO'] = $unEstudiante ['SEGUNDO_APELLIDO'];
				$matriculadoPrimerCurso ['GENERO'] = $unEstudiante ['GENERO_CODE'];
				
				fputcsv ( $archivoMatriculadoPrimerCurso, $matriculadoPrimerCurso );
			}
		}
		
		fclose ( $archivoMatriculadoPrimerCurso );
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );

$resultado = $miProcesador->procesarFormulario ();

