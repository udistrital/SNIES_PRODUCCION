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
		echo 'hola CSV'; exit;
		
		/**
		 * PROCEDIMIENTO
		 * 1. Consultar los datos de los estudiantes para un período
		 * 2. Quitar acentos
		 * 3. Dividir nombres
		 * 4. Actualizar en PARTICIPANTE
		 */
		
		// estudiante de la académica
		
		$graduado = $this->miComponente->consultarGraduadoAcademica ( $this->annio, $this->semestre );
		
		$miProcesadorNombre = new procesadorNombre ();
		
		//Busca y presenta los caracteres inválidos
		$caracteresInvalidos = $miProcesadorNombre->buscarCaracteresInvalidos ( $graduado, 'EST_NOMBRE' );
		
		
		// quita acentos del nombre
		$graduado = $miProcesadorNombre->quitarAcento ( $graduado, 'EST_NOMBRE' );
		
		// descompone nombre completo en sus partes y las aglega al final de cada registro
		foreach ( $graduado as $clave => $valor ) {
			// echo $estudiante [$clave] ['CODIGO_UNICO'].'<br>';
			$nombreCompleto = $miProcesadorNombre->dividirNombreCompleto ( $graduado [$clave] ['EST_NOMBRE'] );
			$graduado [$clave] ['PRIMER_APELLIDO'] = $nombreCompleto ['primer_apellido'];
			$graduado [$clave] ['SEGUNDO_APELLIDO'] = $nombreCompleto ['segundo_apellido'];
			$graduado [$clave] ['PRIMER_NOMBRE'] = $nombreCompleto ['primer_nombre'];
			$graduado [$clave] ['SEGUNDO_NOMBRE'] = $nombreCompleto ['segundo_nombre'];
		}
		
		$miProcesadorExcepcion = new procesadorExcepcion ();
		// FORMATEA LOS VALORES NULOS, CODIFICA EXCEPCIONES
		$graduado = $miProcesadorExcepcion->procesarExcepcionGraduado ( $graduado );						
		
		$this->generar_csv_auditoria_graduado ( $graduado );
		
		exit;
	/**
	 * $valorCodificado = "&pagina=" .
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
	function generar_csv_auditoria_graduado($estudiante) {
		$raizDocumento = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
		$file=$raizDocumento . '/document/auditoria_graduado_' . $this->annio . $this->semestre . '.csv';
		$fp = fopen ( $file, 'w' );
		
		$consecutivoGraduado=1;
		foreach ( $estudiante as $unEstudiante ) {
			
			$matriculado ['CODIGO'] = $unEstudiante ['CODIGO_ESTUDIANTE'];
			$matriculado ['IES_CODE'] = '1301';
			$matriculado ['IES_NOMBRE'] = 'UNIVERSIDAD DISTRITAL FRANCISCO JOSE DE CALDAS';
			$matriculado ['NUM_DOCUMENTO'] = $unEstudiante ['NUM_DOCUMENTO'];
			$matriculado ['TIPO_DOCUMENTO'] = $unEstudiante ['ID_TIPO_DOCUMENTO'];
			$matriculado ['PRIMER_NOMBRE'] = $unEstudiante ['PRIMER_NOMBRE'];
			$matriculado ['SEGUNDO_NOMBRE'] = $unEstudiante ['SEGUNDO_NOMBRE'];
			$matriculado ['PRIMER_APELLIDO'] = $unEstudiante ['PRIMER_APELLIDO'];
			$matriculado ['SEGUNDO_APELLIDO'] = $unEstudiante ['SEGUNDO_APELLIDO'];
			$matriculado ['ANO'] = $this->annio;
			$matriculado ['SEMESTRE'] = $this->semestre;
			$matriculado ['CODIGO_ACREDITACION_IES'] = '';
			$matriculado ['ACREDITACION_IES'] = '';
			$matriculado ['IES_PADRE'] = '1301';
			$matriculado ['TIPO_IES'] = '1';
			$matriculado ['CARACTER'] = '4';
			$matriculado ['ORIGEN'] = '01';
			$matriculado ['COD DEPARTAMENTO'] = '11';
			$matriculado ['COD MUNICIPIO'] = '11001';
			$matriculado ['CODIGO_PROGRAMA'] = $unEstudiante ['PRO_CONSECUTIVO'];
			$matriculado ['PROG_NOMBRE'] = $unEstudiante ['PROG_NOMBRE'];
			$matriculado ['TIPO_ACREDITACION'] = '';
			$matriculado ['TITULO'] = $unEstudiante ['TITULO'];
			$matriculado ['NIVEL'] = $unEstudiante ['NIVEL'];
			$matriculado ['MODALIDAD'] = $unEstudiante ['MODALIDAD'];
			$matriculado ['METODOLOGIA'] = '';
			$matriculado ['AREA'] = '';
			$matriculado ['NBC_PRIM_AREA'] = '';
			$matriculado ['NUCLEO'] = '';
			$matriculado ['NUCLEO_DESC'] = '';
			$matriculado ['FECHA_GRADO'] = $unEstudiante ['FECHA_GRADO'];
			$matriculado ['FECHA_REPORTE'] = $unEstudiante ['FECHA_GRADO'];
			$matriculado ['ACTA'] = $unEstudiante ['ACTA'];
			$matriculado ['FOLIO'] = $unEstudiante ['FOLIO'];
			$matriculado ['CONS_GRAD'] = $consecutivoGraduado;
															
			fputcsv ( $fp, $matriculado );
			$consecutivoGraduado=$consecutivoGraduado+1;
		}
		
		fclose ( $fp );
		
		echo 'Se ha generado el archivo <b>' . $file . '</b>';
		echo '<br>';
		
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

