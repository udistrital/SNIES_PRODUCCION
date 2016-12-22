<?php
include_once ('component/GestorEstudiante/Componente.php');
include_once ('blocks/snies/matriculado/reportarMatriculado/funcion/procesadorNombre.class.php');
include_once ('blocks/snies/matriculado/reportarMatriculado/funcion/procesadorExcepcion.class.php');
use sniesEstudiante\Componente;
use bloqueSnies\procesadorExcepcion;
use bloqueSnies\procesadorNombre;

/**
 * CON ESTA CLASE SE CREAN LOS ARCHIVOS CSV DE GRADUADOS
 * 
 */
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
		 * PROCEDIMIENTO
		 * 1. Consultar los datos de los estudiantes para un período
		 * 2. Quitar acentos
		 * 3. Dividir nombres
		 * 4. Actualizar en PARTICIPANTE
		 */
		
		// consulta datos de las tablas participante, graduado y programa de postgres
		
		$graduado = $this->miComponente->consultarGraduadoTodos ( $this->annio, $this->semestre );
		
				
		$this->generar_graduado_csv_hecaa ( $graduado );
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

	

/**
	 * Genera el archivo csv de graduado
	 */
	function generar_graduado_csv_hecaa($graduado) {
		$raizDocumento = $this -> miConfigurador -> getVariableConfiguracion("raizDocumento");
		$this -> annio = $_REQUEST['annio'];
		$this -> semestre = $_REQUEST['semestre'];
		$file = $raizDocumento . '/document/graduado_' . $this -> annio . $this -> semestre . '.csv';
		$fp = fopen($file, 'w');
		//ENCABEZADO DE LA PLANTILLA GRADUADO
		$linea1 = array('Herramienta de Cargue Hecca - V 3.4');
		$linea2 = array(utf8_decode('[60] Nombre de la Plantilla: [Graduados] Descripcion: [Persona natural que, previa culminación del programa académico y cumplimiento de los requisitos de ley y los exigidos por la respectiva institución de educación superior, recibe el título académico.]'));
		$linea3 = array('Licenciado para Ministerio de Educacion Nacional 2016');
		fwrite($fp, implode(',', $linea1) . "\r\n");
		//con esto elimina las comillas dobles del encabezado
		fwrite($fp, implode(',', $linea2) . "\r\n");
		fwrite($fp, implode(',', $linea3) . "\r\n");
		fputcsv($fp, array(utf8_decode ('AÑO'),'SEMESTRE','ID_TIPO_DOCUMENTO','NUM_DOCUMENTO',
		'PRO_CONSECUTIVO','ID_MUNICIPIO','EMAIL_PERSONAL','TELEFONO_CONTACTO',
		'SNP_SABER_PRO','NUM_ACTA_GRADO','FECHA_GRADO','NUM_FOLIO'), ";");
		
		foreach ($graduado as $registro) {
			//var_dump ( $registro );exit;
			//Se debe redefinir el arrglo para que no presenta las asociaciones numéricas
			$arreglo['ano'] = $registro['ano'];
			$arreglo['semestre'] = $registro['semestre'];
			$arreglo['id_tipo_documento'] = $registro['id_tipo_documento'];
			$arreglo['num_documento'] = $registro['num_documento'];
			$arreglo['pro_consecutivo'] = $registro['pro_consecutivo'];
			$arreglo['id_municipio_programa'] = $registro['id_municipio'];
			$arreglo['email_personal'] = $registro['email_personal'];
			$arreglo['telefono_contacto'] = $registro['telefono_contacto'];
			$arreglo['snp_saber_pro'] = $registro['snp_saber_pro'];
			$arreglo['num_acta_grado'] = $registro['num_acta_grado'];
			$arreglo['fecha_grado'] = $registro['fecha_grado'];
			$arreglo['num_folio'] = $registro['num_folio'];
			
			fputcsv($fp, $arreglo, ";");
		}

		fclose($fp);

		echo 'Se ha generado el archivo <b>' . $file . '</b>';
		echo '<br>';

}

//GENERA EL ARCHIVO DE AUDITORIA T&T
	function generar_csv_auditoria_graduado($estudiante) {
		$raizDocumento = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
		$file=$raizDocumento . '/document/auditoria_graduado_' . $this->annio . $this->semestre . '.csv';
		$fp = fopen ( $file, 'w' );
		
		$consecutivoGraduado=1;
		foreach ( $estudiante as $unEstudiante ) {
				
			
			$matriculado ['CONSECUTIVO'] = $consecutivoGraduado;
			$matriculado ['IES_CODE'] = '1301';
			$matriculado ['IES_NOMBRE'] = 'UNIVERSIDAD DISTRITAL FRANCISCO JOSE DE CALDAS';
			$matriculado ['NUM_DOCUMENTO'] = $unEstudiante ['num_documento'];
			$matriculado ['TIPO_DOCUMENTO'] = $unEstudiante ['id_tipo_documento'];
			$matriculado ['PRIMER_NOMBRE'] = $unEstudiante ['primer_nombre'];
			$matriculado ['SEGUNDO_NOMBRE'] = $unEstudiante ['segundo_nombre'];
			$matriculado ['PRIMER_APELLIDO'] = $unEstudiante ['primer_apellido'];
			$matriculado ['SEGUNDO_APELLIDO'] = $unEstudiante ['segundo_apellido'];
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
			$matriculado ['CODIGO_PROGRAMA'] = $unEstudiante ['pro_consecutivo'];
			$matriculado ['PROG_NOMBRE'] = $unEstudiante ['prog_nombre'];
			$matriculado ['TIPO_ACREDITACION'] = '';
			$matriculado ['TITULO'] = $unEstudiante ['titulo'];
			$matriculado ['NIVEL'] = $unEstudiante ['nivel'];
			$matriculado ['MODALIDAD'] = $unEstudiante ['modalidad'];
			$matriculado ['METODOLOGIA'] = '';
			$matriculado ['AREA'] = '';
			$matriculado ['NBC_PRIM_AREA'] = '';
			$matriculado ['NUCLEO'] = '';
			$matriculado ['NUCLEO_DESC'] = '';
			$matriculado ['FECHA_GRADO'] = $unEstudiante ['fecha_grado'];
			$matriculado ['FECHA_REPORTE'] = $unEstudiante ['fecha_grado'];
			$matriculado ['ACTA'] = $unEstudiante ['num_acta_grado'];
			$matriculado ['FOLIO'] = $unEstudiante ['num_folio'];
			$matriculado ['CONS_GRAD'] = $consecutivoGraduado;
															
			fputcsv ( $fp, $matriculado );
			$consecutivoGraduado=$consecutivoGraduado+1;
			
		}
		
		fclose ( $fp );
		
		echo 'Se ha generado el archivo <b>' . $file . '</b>';
		echo '<br>';
		
	}
}
$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );

$resultado = $miProcesador->procesarFormulario ();

