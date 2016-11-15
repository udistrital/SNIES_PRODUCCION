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
		$docente = $this->miComponente->consultarDocenteAcademica ( $this->annio, $this->semestre );
				
		// consulta todas las vinculaciones de todos los decentes para un año y período
		$vinculacionDocente = $this->miComponente->consultarVinculacionDocente ( $this->annio, $this->semestre );
		
		$miProcesadorNombre = new procesadorNombre ();
		foreach ( $docente as $clave => $valor ) {
			
			$apellidoCompleto = $miProcesadorNombre->dividirApellidos ( $docente [$clave] ['DOC_APELLIDO'] );
			$docente [$clave] ['PRIMER_APELLIDO'] = $apellidoCompleto ['primer_apellido'];
			$docente [$clave] ['SEGUNDO_APELLIDO'] = $apellidoCompleto ['segundo_apellido'];
			
			$nombreCompleto = $miProcesadorNombre->dividirNombres ( $docente [$clave] ['DOC_NOMBRE'] );
			$docente [$clave] ['PRIMER_NOMBRE'] = $nombreCompleto ['primer_nombre'];
			$docente [$clave] ['SEGUNDO_NOMBRE'] = $nombreCompleto ['segundo_nombre'];
		}
		
		// codificar vinculacion docente
		foreach ( $vinculacionDocente as $clave => $valor ) {
			switch ($vinculacionDocente [$clave] ['VINCULACION']) {
				case 1 :
					$vinculacionDocente [$clave] ['TIPO_CONTRATO'] = '01';
					$vinculacionDocente [$clave] ['DEDICACION'] = '01';
					break;
				case 2 :
					$vinculacionDocente [$clave] ['TIPO_CONTRATO'] = '02';
					$vinculacionDocente [$clave] ['DEDICACION'] = '01';
					break;
				case 3 :
					$vinculacionDocente [$clave] ['TIPO_CONTRATO'] = '02';
					$vinculacionDocente [$clave] ['DEDICACION'] = '02';
					break;
				case 4 :
					$vinculacionDocente [$clave] ['TIPO_CONTRATO'] = '02';
					$vinculacionDocente [$clave] ['DEDICACION'] = '04';
					break;
				case 5 :
					$vinculacionDocente [$clave] ['TIPO_CONTRATO'] = '03';
					$vinculacionDocente [$clave] ['DEDICACION'] = '04';
					break;
				case 6 :
					$vinculacionDocente [$clave] ['TIPO_CONTRATO'] = '01';
					$vinculacionDocente [$clave] ['DEDICACION'] = '02';
					break;
				case 7 :
					$vinculacionDocente [$clave] ['TIPO_CONTRATO'] = '03';
					$vinculacionDocente [$clave] ['DEDICACION'] = '03';
					break;
				case 8 :
					$vinculacionDocente [$clave] ['TIPO_CONTRATO'] = '01';
					$vinculacionDocente [$clave] ['DEDICACION'] = '01';
					break;
				
				default :
					echo 'Sin vinculación';
					break;
			}
		}
		
		foreach ( $docente as $key => $value ) {
			$docente [$key] ['DEDICACION'] = '04';
			$docente [$key] ['TIPO_CONTRATO'] = '03';
			foreach ( $vinculacionDocente as $unaVinculacion ) {
				if ($docente [$key] ['CODIGO_UNICO'] == $unaVinculacion ['DOCUMENTO']) {
					if ($docente [$key] ['DEDICACION'] > $unaVinculacion ['DEDICACION']) {
						$docente [$key] ['DEDICACION'] = $unaVinculacion ['DEDICACION'];
					}
					if ($docente [$key] ['TIPO_CONTRATO'] > $unaVinculacion ['TIPO_CONTRATO']) {
						$docente [$key] ['TIPO_CONTRATO'] = $unaVinculacion ['TIPO_CONTRATO'];
					}
				}
			}
		}
		
		// var_dump ( $vinculacionDocente );
		// exit ();
		// foreach ( $docente as $unDocente ) {
		
		// $caracteresInvalidos = $miProcesadorNombre->buscarCaracteresInvalidos ( $docente, 'DOC_APELLIDO' );
		// $caracteresInvalidos = $miProcesadorNombre->buscarCaracteresInvalidos ( $docente, 'DOC_NOMBRE' );
		
		// quita acentos del nombre
		// $docente = $miProcesadorNombre->quitarAcento ( $docente, 'EST_NOMBRE' );
		
		// descompone nombre completo en sus partes y las aglega al final de cada registro
		
		$miProcesadorExcepcion = new procesadorExcepcion ();
		// FORMATEA LOS VALORES NULOS, CODIFICA EXCEPCIONES
		$docente = $miProcesadorExcepcion->procesarExcepcionEstudiante ( $docente );		
		
		$this->generarListadoDocentes ( $docente );
		$raizDocumento = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );		
		echo '<br>';
	}
	function generarListadoDocentes($docente) {
		$raizDocumento = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
		$file=$raizDocumento . '/document/auditoria_docente_' . $this->annio . $this->semestre . '.csv';
		$fp = fopen ( $file, 'w' );
		$consecutivoDocente=1;
		foreach ( $docente as $unDocente ) {
			
			$arregloDocente ['ID'] = '';
			$arregloDocente ['IES_CODE'] = '1301';
			$arregloDocente ['IES_PADRE'] = '1301';
			$arregloDocente ['TIPO_IES'] = '1';
			$arregloDocente ['IES_NOMBRE'] = 'UNIVERSIDAD DISTRITAL FRANCISCO JOSE DE CALDAS';
			$arregloDocente ['TIPO_ACREDITACION'] = '';
			$arregloDocente ['CARACTER'] = '4';			
			$arregloDocente ['ORIGEN'] = '01';
			$arregloDocente ['COD DEPARTAMENTO'] = '11';
			$arregloDocente ['COD MUNICIPIO'] = '11001';
			$arregloDocente ['TIPO_DOCUMENTO'] = $unDocente ['TIPO_DOC_UNICO'];
			$arregloDocente ['NUM_DOCUMENTO'] = $unDocente ['CODIGO_UNICO'];
			$arregloDocente ['NOMBRE1'] = $unDocente ['PRIMER_NOMBRE'];
			$arregloDocente ['NOMBRE2'] = $unDocente ['SEGUNDO_NOMBRE'];
			$arregloDocente ['APELLIDO1'] = $unDocente ['PRIMER_APELLIDO'];
			$arregloDocente ['APELLIDO2'] = $unDocente ['SEGUNDO_APELLIDO'];			
			$arregloDocente ['GENERO'] = $unDocente ['GENERO_CODE'];			
			$arregloDocente ['UNIDAD_ORGANIZACIONAL'] = '';
			$arregloDocente ['NOMBRE_UNIDAD'] = '';
			$arregloDocente ['NIVEL_EST_CODE'] = $unDocente ['NIVEL_EST_CODE'];		
			$arregloDocente ['DEDICACION'] = $unDocente ['DEDICACION'];
			$arregloDocente ['TIPO_CONTRATO'] = $unDocente ['TIPO_CONTRATO'];
			$arregloDocente ['FECHA_NACIMIENTO'] = $unDocente ['FECHA_NACIM'];
			$arregloDocente ['FECHA_INGRESO'] = $unDocente ['FECHA_INGRESO'];
			$arregloDocente ['ANO'] = $this->annio;
			$arregloDocente ['SEMESTRE'] = $this->semestre;
			$arregloDocente ['SEMESTRE'] = $this->semestre;
			$arregloDocente ['CONS_DOC'] = $consecutivoDocente;
			$arregloDocente ['ESTADO'] = 'A';
						
			fputcsv ( $fp, $arregloDocente );
			
			$consecutivoDocente=$consecutivoDocente+1;
		
		}
		
		fclose ( $fp );
		echo 'Se ha generado el archivo: <b>' . $file.'</b>';
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );

$resultado = $miProcesador->procesarFormulario ();

