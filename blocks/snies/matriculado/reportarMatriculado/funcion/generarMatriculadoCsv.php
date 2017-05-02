<?php
include_once ('component/GestorEstudiante/Componente.php');
//include_once ('blocks/snies/funcion/procesadorNombre.class.php');
//include_once ('blocks/snies/funcion/procesadorExcepcion.class.php');
use sniesEstudiante\Componente;
//use bloqueSnies\procesadorExcepcion;
//use bloqueSnies\procesadorNombre;
class FormProcessor {
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miSql;
	var $conexion;
	var $annio;
	var $semestre;
	function __construct($lenguaje, $sql) {
		$this -> miConfigurador = \Configurador::singleton();
		$this -> miConfigurador -> fabricaConexiones -> setRecursoDB('principal');
		$this -> lenguaje = $lenguaje;
		$this -> miSql = $sql;
		$this -> miComponente = new Componente();
		$this -> host = $this -> miConfigurador -> getVariableConfiguracion("host");
		$this -> site = $this -> miConfigurador -> getVariableConfiguracion("site");
		$this -> esteBloque = $this -> miConfigurador -> getVariableConfiguracion("esteBloque");
	}

	function procesarFormulario() {
		$this -> annio = $_REQUEST['annio'];
		$this -> semestre = $_REQUEST['semestre'];

		/**
		 *Genera archivos csv de participante, primer curso y auditoria
		 *
		 */

		$participante = $this -> miComponente -> consultarParticipanteTodos();
		$this -> generarParticipanteCsv($participante);

		$primer_curso = $this -> miComponente -> consultarPrimerCursoTodos($this -> annio, $this -> semestre);
		$this -> generarPrimerCursoCsv($primer_curso);

		$auditoria_primer_curso = $this -> miComponente -> consultarPrimerCursoAuditoria($this -> annio, $this -> semestre);
		$this -> generarAuditoriaPrimerCursoCsv($auditoria_primer_curso);

		$matriculado = $this -> miComponente -> consultarMatriculadoTodos($this -> annio, $this -> semestre);
		$this -> generarMatriculadoCsv($matriculado);

		$auditoria_matriculado = $this -> miComponente -> consultarMatriculadoAuditoria($this -> annio, $this -> semestre);
		$this -> generarAuditoriaMatriculadoCsv($auditoria_matriculado);

		exit ;

		$miProcesadorExcepcion = new procesadorExcepcion();
		// FORMATEA LOS VALORES NULOS, CODIFICA EXCEPCIONES
		$estudiante = $miProcesadorExcepcion -> procesarExcepcionEstudiante($estudiante);

		echo 'proceso 1 actualizarEstudiantePrimerCurso...<br>';
		$this -> actualizarEstudiantePrimerCurso($estudiante);
		echo 'proceso 2 actualizarMatriculado<br>';
		$this -> actualizarMatriculado($estudiante);
		echo 'FIN<br>';
		exit ;
		$valorCodificado = "&pagina=" . $this -> miConfigurador -> getVariableConfiguracion('pagina');
		$valorCodificado = $this -> miConfigurador -> fabricaConexiones -> crypto -> codificar($valorCodificado);

		// Rescatar el parámetro enlace desde los datos de configuraión en la base de datos
		$variable = $this -> miConfigurador -> getVariableConfiguracion("enlace");
		$miEnlace = $this -> host . $this -> site . '/index.php?' . $variable . '=' . $valorCodificado;
		echo '<script>window.location.assign("' . $miEnlace . '")</script>';
		exit();
	}

	/**
	 * Genera el archivo csv de primer_curso
	 */
	function generarPrimerCursoCsv($primerCurso) {
		$raizDocumento = $this -> miConfigurador -> getVariableConfiguracion("raizDocumento");
		$this -> annio = $_REQUEST['annio'];
		$this -> semestre = $_REQUEST['semestre'];
		$file = $raizDocumento . '/document/primerCurso_' . $this -> annio . $this -> semestre . '.csv';
		$fp = fopen($file, 'w');
		//ENCABEZADO DE LA PLANTILLA PRIMER_CURSO
		$linea1 = array('Herramienta de Cargue Hecca - V 3.4');
		$linea2 = array('[62] Nombre de la Plantilla: [Estudiantes  de primer curso] Descripcion: [Estudiante de primer semestre de programas tanto pregrado como postgrado, Esta variable se calcula a partir de la información matricula]');
		$linea3 = array('Licenciado para Ministerio de Educacion Nacional 2016');
		fwrite($fp, implode(',', $linea1) . "\r\n");
		//con esto elimina las comillas dobles del encabezado
		fwrite($fp, implode(',', $linea2) . "\r\n");
		fwrite($fp, implode(',', $linea3) . "\r\n");
		fputcsv($fp, array(utf8_decode('AÑO'), 'SEMESTRE', 'ID_TIPO_DOCUMENTO', 'NUM_DOCUMENTO', 'PRO_CONSECUTIVO', 'ID_MUNICIPIO_PROGRAMA', 'ID_TIPO_VINCULACION', 'ID_GRUPO_ETNICO', 'ID_PUEBLO_INDIGENA', 'ID_COMUNIDAD_NEGRA', 'PERSONA_CONDICION_DISCAPACIDAD', 'ID_TIPO_DISCAPACIDAD', 'ID_CAPACIDAD_EXCEPCIONAL', 'COD_PRUEBA_SABER_11'), ";");
		foreach ($primerCurso as $registro) {
			//var_dump ( $unInscrito );
			//Se debe redefinir el arrglo para que no presenta las asociaciones numéricas
			$arreglo['ano'] = $registro['ano'];
			$arreglo['semestre'] = $registro['semestre'];
			$arreglo['id_tipo_documento'] = $registro['id_tipo_documento'];
			$arreglo['num_documento'] = $registro['num_documento'];
			$arreglo['pro_consecutivo'] = $registro['pro_consecutivo'];
			$arreglo['id_municipio_programa'] = $registro['id_municipio_programa'];
			$arreglo['id_tipo_vinculacion'] = $registro['id_tipo_vinculacion'];
			$arreglo['id_grupo_etnico'] = $registro['id_grupo_etnico'];
			$arreglo['id_pueblo_indigena'] = $registro['id_pueblo_indigena'];
			$arreglo['id_comunidad_negra'] = $registro['id_comunidad_negra'];
			$arreglo['persona_condicion_discapacidad'] = $registro['persona_condicion_discapacidad'];
			$arreglo['id_tipo_discapacidad'] = $registro['id_tipo_discapacidad'];
			$arreglo['id_capacidad_excepcional'] = $registro['id_capacidad_excepcional'];
			$arreglo['cod_prueba_saber_11'] = $registro['cod_prueba_saber_11'];

			fputcsv($fp, $arreglo, ";");
		}

		fclose($fp);

		echo 'Se ha generado el archivo <b>' . $file . '</b>';
		echo '<br>';

	}

	/**
	 * Auditoria de primer_curso
	 */
	function generarAuditoriaPrimerCursoCsv($primerCurso) {
		$raizDocumento = $this -> miConfigurador -> getVariableConfiguracion("raizDocumento");
		$this -> annio = $_REQUEST['annio'];
		$this -> semestre = $_REQUEST['semestre'];
		$file = $raizDocumento . '/document/auditoria_primerCurso_' . $this -> annio . $this -> semestre . '.csv';
		$fp = fopen($file, 'w');

		$consecutivo = 1;
		foreach ($primerCurso as $registro) {
			//Se redefine el arreglo para que no presenta las asociaciones numéricas
			$arreglo['ID'] = $consecutivo;
			$arreglo['IES_CODE'] = '1301';
			$arreglo['IES_NOMBRE'] = 'UNIVERSIDAD DISTRITAL FRANCISCO JOSE DE CALDAS';
			$arreglo['NUM_DOCUMENTO'] = $registro['num_documento'];
			$arreglo['TIPO_DOCUMENTO'] = $registro['id_tipo_documento'];
			$arreglo['NOMBRE1'] = $registro['primer_nombre'];
			$arreglo['NOMBRE2'] = $registro['segundo_nombre'];
			$arreglo['APELLIDO1'] = $registro['primer_apellido'];
			$arreglo['APELLIDO2'] = $registro['segundo_apellido'];
			$arreglo['ANO'] = $registro['ano'];
			$arreglo['SEMESTRE'] = $registro['semestre'];
			$arreglo['CODIGO_ACREDITACION_IES'] = '';
			$arreglo['ACREDITACION_IES'] = '';
			$arreglo['TIPO_IES'] = '1';
			$arreglo['CARACTER'] = '4';
			$arreglo['ORIGEN'] = '01';
			$arreglo['COD DEPARTAMENTO'] = '11';
			$arreglo['COD MUNICIPIO'] = '11001';
			$arreglo['CODIGO_PROGRAMA'] = $registro['pro_consecutivo'];
			$arreglo['PROG_NOMBRE'] = $registro['nombre'];
			$arreglo['TIPO_ACREDITACION'] = '';
			$arreglo['NIVEL'] = $registro['nivel'];
			$arreglo['MODALIDAD'] = $registro['modalidad'];
			$arreglo['METODOLOGIA'] = '';
			$arreglo['AREA'] = '';
			$arreglo['NBC_PRIM_AREA'] = '';
			$arreglo['NUCLEO'] = '';
			$arreglo['NUCLEO_DESC'] = '';
			$arreglo['ESTADO'] = 'A';

			fputcsv($fp, $arreglo, ";");
			$consecutivo = $consecutivo + 1;
		}

		fclose($fp);

		echo 'Se ha generado el archivo <b>' . $file . '</b>';
		echo '<br>';

	}

	/**
	 * Genera el archivo csv de matriculado
	 */
	function generarMatriculadoCsv($matriculado) {
		$raizDocumento = $this -> miConfigurador -> getVariableConfiguracion("raizDocumento");
		$this -> annio = $_REQUEST['annio'];
		$this -> semestre = $_REQUEST['semestre'];
		$file = $raizDocumento . '/document/matriculado_' . $this -> annio . $this -> semestre . '.csv';
		$fp = fopen($file, 'w');
		//ENCABEZADO DE LA PLANTILLA MATRICULADO
		$linea1 = array('Herramienta de Cargue Hecca - V 3.4');
		$linea2 = array(utf8_decode('[61] Nombre de la Plantilla: [Matriculados] Descripcion: [Persona natural que posee matrícula vigente para un programa académico en una Institución de Educación Superior.]'));
		$linea3 = array('Licenciado para Ministerio de Educacion Nacional 2016');
		fwrite($fp, implode(',', $linea1) . "\r\n");
		//con esto elimina las comillas dobles del encabezado
		fwrite($fp, implode(',', $linea2) . "\r\n");
		fwrite($fp, implode(',', $linea3) . "\r\n");
		fputcsv($fp, array(utf8_decode('AÑO'), 'SEMESTRE', 'ID_TIPO_DOCUMENTO', 'NUM_DOCUMENTO', 'CODIGO_ESTUDIANTE', 'PRO_CONSECUTIVO', 'ID_MUNICIPIO', 'FECHA_NACIMIENTO', 'ID_PAIS_NACIMIENTO', 'ID_MUNICIPIO_NACIMIENTO', 'ID_ZONA_RESIDENCIA', 'ES_REINTEGRO'), ";");
		foreach ($matriculado as $registro) {
			//var_dump ( $unInscrito );
			//Se debe redefinir el arrglo para que no presenta las asociaciones numéricas
			$arreglo['ano'] = $registro['ano'];
			$arreglo['semestre'] = $registro['semestre'];
			$arreglo['id_tipo_documento'] = $registro['id_tipo_documento'];
			$arreglo['num_documento'] = $registro['num_documento'];
			$arreglo['codigo_estudiante'] = $registro['codigo_estudiante'];
			$arreglo['pro_consecutivo'] = $registro['pro_consecutivo'];
			$arreglo['id_municipio'] = $registro['id_municipio'];
			$arreglo['fecha_nacimiento'] = $registro['fecha_nacimiento'];
			$arreglo['id_pais_nacimiento'] = $registro['id_pais_nacimiento'];
			$arreglo['id_municipio_nacimiento'] = $registro['id_municipio_nacimiento'];
			$arreglo['id_zona_residencia'] = $registro['id_zona_residencia'];
			$arreglo['es_reintegro'] = $registro['es_reintegro'];

			fputcsv($fp, $arreglo, ";");
		}

		fclose($fp);

		echo 'Se ha generado el archivo <b>' . $file . '</b>';
		echo '<br>';

	}

	/**
	 * Genera el archivo csv de matriculado
	 */
	function generarAuditoriaMatriculadoCsv($matriculado) {
		$raizDocumento = $this -> miConfigurador -> getVariableConfiguracion("raizDocumento");
		$this -> annio = $_REQUEST['annio'];
		$this -> semestre = $_REQUEST['semestre'];
		$file = $raizDocumento . '/document/auditoria_matriculado_' . $this -> annio . $this -> semestre . '.csv';
		$fp = fopen($file, 'w');
		//ENCABEZADO DE LA PLANTILLA MATRICULADO
		$secuenciaMatricualado = 1;
		foreach ($matriculado as $registro) {

			//Se debe redefinir el arrglo para que no presenta las asociaciones numéricas
			$arreglo['ID'] = $secuenciaMatricualado;
			$arreglo['IES_CODE'] = '1301';
			$arreglo['IES_NOMBRE'] = 'UNIVERSIDAD DISTRITAL FRANCISCO JOSE DE CALDAS';
			$arreglo['num_documento'] = $registro['num_documento'];
			$arreglo['id_tipo_documento'] = $registro['id_tipo_documento'];
			$arreglo['primer_nombre'] = $registro['primer_nombre'];
			$arreglo['segundo_nombre'] = $registro['segundo_nombre'];
			$arreglo['primer_apellido'] = $registro['primer_apellido'];
			$arreglo['segundo_apellido'] = $registro['segundo_apellido'];
			$arreglo['ano'] = $registro['ano'];
			$arreglo['semestre'] = $registro['semestre'];
			$arreglo['CODIGO_ACREDITACION_IES'] = '';
			$arreglo['ACREDITACION_IES'] = '';
			$arreglo['IES_PADRE'] = '1301';
			$arreglo['TIPO_IES'] = '1';
			$arreglo['CARACTER'] = '4';
			$arreglo['ORIGEN'] = '01';
			$arreglo['COD DEPARTAMENTO'] = '11';
			$arreglo['COD MUNICIPIO'] = '11001';
			$arreglo['pro_consecutivo'] = $registro['pro_consecutivo'];
			$arreglo['PROG_NOMBRE'] = $registro['nombre'];
			$arreglo['TIPO_ACREDITACION'] = '';
			$arreglo['TITULO'] = $registro['titulo'];
			$arreglo['NIVEL'] = $registro['nivel'];
			$arreglo['MODALIDAD'] = $registro['modalidad'];
			$arreglo['METODOLOGIA'] = '';
			$arreglo['AREA'] = '';
			$arreglo['NBC_PRIM_AREA'] = '';
			$arreglo['NUCLEO'] = '';
			$arreglo['NUCLEO_DESC'] = '';
			$arreglo['CONS_ESTUD'] = $secuenciaMatricualado;
			$arreglo['ESTADO'] = 'A';

			fputcsv($fp, $arreglo, ";");
			$secuenciaMatricualado = $secuenciaMatricualado + 1;
		}

		fclose($fp);

		echo 'Se ha generado el archivo <b>' . $file . '</b>';
		echo '<br>';

	}

	/**
	 * Genera csv de participante
	 */
	function generarParticipanteCsv($participante) {
		$raizDocumento = $this -> miConfigurador -> getVariableConfiguracion("raizDocumento");
		$this -> annio = $_REQUEST['annio'];
		$this -> semestre = $_REQUEST['semestre'];
		$file = $raizDocumento . '/document/participante.csv';
		$fp = fopen($file, 'w');
		//ENCABEZADO DE LA PLANTILLA PARTICIPANTE
		$linea1 = array('Herramienta de Cargue Hecca - V 3.4');
		$linea2 = array('[55] Nombre de la Plantilla: [Participante] Descripcion: [Participante]');
		$linea3 = array('Licenciado para Ministerio de Educacion Nacional 2016');
		fwrite($fp, implode(',', $linea1) . "\r\n");
		//con esto elimina las comillas dobles del encabezado
		fwrite($fp, implode(',', $linea2) . "\r\n");
		fwrite($fp, implode(',', $linea3) . "\r\n");
		fputcsv($fp, array('ID_TIPO_DOCUMENTO', 'NUM_DOCUMENTO', 'FECHA_EXPEDICION', 'PRIMER_NOMBRE', 'SEGUNDO_NOMBRE', 'PRIMER_APELLIDO', 'SEGUNDO_APELLIDO', 'ID_SEXO_BIOLOGICO', 'ID_ESTADO_CIVIL', 'FECHA_NACIMIENTO', 'ID_PAIS', 'ID_MUNICIPIO', 'TELEFONO_CONTACTO', 'EMAIL_PERSONAL', 'EMAIL_INSTITUCIONAL', 'DIRECCION_INSTITUCIONAL'), ";");
		foreach ($participante as $registro) {
			if ($registro['estado'] == 't') {
				//var_dump ( $unInscrito );
				//Se debe redefinir el arrglo para que no presente las asociaciones numéricas
				$arreglo['id_tipo_documento'] = $registro['id_tipo_documento'];
				$arreglo['num_documento'] = $registro['num_documento'];
				$arreglo['fecha_expedicion'] = $registro['fecha_expedicion'];
				$arreglo['primer_nombre'] = $registro['primer_nombre'];
				$arreglo['segundo_nombre'] = $registro['segundo_nombre'];
				$arreglo['primer_apellido'] = $registro['primer_apellido'];
				$arreglo['segundo_apellido'] = $registro['segundo_apellido'];
				$arreglo['id_sexo_biologico'] = $registro['id_sexo_biologico'];
				$arreglo['id_estado_civil'] = $registro['id_estado_civil'];
				$arreglo['fecha_nacimiento'] = $registro['fecha_nacimiento'];
				$arreglo['id_pais'] = $registro['id_pais'];
				$arreglo['id_municipio'] = $registro['id_municipio'];
				$arreglo['telefono_contacto'] = $registro['telefono_contacto'];
				$arreglo['email_personal'] = $registro['email_personal'];
				$arreglo['email_institucional'] = $registro['email_institucional'];
				$arreglo['direccion_institucional'] = $registro['direccion_institucional'];

				fputcsv($fp, $arreglo, ";");

			}

		}

		fclose($fp);

		echo 'Se ha generado el archivo <b>' . $file . '</b>';
		echo '<br>';
		
		//SE CREA EL ARCHIVO DE LOS PARTICIPANTES QUE NO HAN SIDO CARGADOS ESTADO FALSE
		$file = $raizDocumento . '/document/participante_nochargue.csv';
		$fp = fopen($file, 'w');
		//ENCABEZADO DE LA PLANTILLA PARTICIPANTE
		$linea1 = array('Herramienta de Cargue Hecca - V 3.4');
		$linea2 = array('[55] Nombre de la Plantilla: [Participante] Descripcion: [Participante]');
		$linea3 = array('Licenciado para Ministerio de Educacion Nacional 2016');
		fwrite($fp, implode(',', $linea1) . "\r\n");
		//con esto elimina las comillas dobles del encabezado
		fwrite($fp, implode(',', $linea2) . "\r\n");
		fwrite($fp, implode(',', $linea3) . "\r\n");
		fputcsv($fp, array('ID_TIPO_DOCUMENTO', 'NUM_DOCUMENTO', 'FECHA_EXPEDICION', 'PRIMER_NOMBRE', 'SEGUNDO_NOMBRE', 'PRIMER_APELLIDO', 'SEGUNDO_APELLIDO', 'ID_SEXO_BIOLOGICO', 'ID_ESTADO_CIVIL', 'FECHA_NACIMIENTO', 'ID_PAIS', 'ID_MUNICIPIO', 'TELEFONO_CONTACTO', 'EMAIL_PERSONAL', 'EMAIL_INSTITUCIONAL', 'DIRECCION_INSTITUCIONAL'), ";");
		foreach ($participante as $registro) {
			if ($registro['estado'] == 'f' and $registro['id_tipo_documento']=='CE') {
				//var_dump ( $unInscrito );
				//Se debe redefinir el arrglo para que no presente las asociaciones numéricas
				$arreglo['id_tipo_documento'] = $registro['id_tipo_documento'];
				$arreglo['num_documento'] = $registro['num_documento'];
				$arreglo['fecha_expedicion'] = $registro['fecha_expedicion'];
				$arreglo['primer_nombre'] = $registro['primer_nombre'];
				$arreglo['segundo_nombre'] = $registro['segundo_nombre'];
				$arreglo['primer_apellido'] = $registro['primer_apellido'];
				$arreglo['segundo_apellido'] = $registro['segundo_apellido'];
				$arreglo['id_sexo_biologico'] = $registro['id_sexo_biologico'];
				$arreglo['id_estado_civil'] = $registro['id_estado_civil'];
				$arreglo['fecha_nacimiento'] = $registro['fecha_nacimiento'];
				$arreglo['id_pais'] = $registro['id_pais'];
				$arreglo['id_municipio'] = $registro['id_municipio'];
				$arreglo['telefono_contacto'] = $registro['telefono_contacto'];
				$arreglo['email_personal'] = $registro['email_personal'];
				$arreglo['email_institucional'] = $registro['email_institucional'];
				$arreglo['direccion_institucional'] = $registro['direccion_institucional'];

				fputcsv($fp, $arreglo, ";");

			}

		}

		fclose($fp);

		echo 'Se ha generado el archivo <b>' . $file . '</b>';
		echo '<br>';

	}

}

$miProcesador = new FormProcessor($this -> lenguaje, $this -> sql);

$resultado = $miProcesador -> procesarFormulario();
