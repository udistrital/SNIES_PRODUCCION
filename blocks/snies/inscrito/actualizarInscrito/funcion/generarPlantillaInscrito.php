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
		$this -> miConfigurador = \Configurador::singleton();
		$this -> lenguaje = $lenguaje;
		$this -> miSql = $sql;
		$this -> miComponente = new Componente();
		$this -> host = $this -> miConfigurador -> getVariableConfiguracion("host");
		$this -> site = $this -> miConfigurador -> getVariableConfiguracion("site");
		$this -> esteBloque = $this -> miConfigurador -> getVariableConfiguracion("esteBloque");
	}

	/**
	 * Esta función realiza las siguientes acciones
	 * 1.consulta en la académica inscritos pregrado
	 * 2.consulta en la académica inscritos postgrado
	 * 4.Borrar los registros para el año y periodo seleccionado en SNIES LOCAL tabla inscrito
	 * 2.Procesar los datos obtenidos, cambiar acentos.
	 * 3.Registrar errores de la fuente para reportarlos
	 * 5.Insertar los registros en el SNIES LOCAL
	 * 6.Redireccionar a lista de variables
	 */
	function procesarFormulario() {

		$annio = $_REQUEST['annio'];
		$semestre = $_REQUEST['semestre'];

		//problacion.inscritos
		$inscritos = $this -> miComponente -> consultarInscritoSnies($annio, $semestre);
		$this -> generarPlantillaInscrito($inscritos);

		//problacion.inscrito_programa
		$inscritoPrograma = $this -> miComponente -> consultarInscritoProgramaSnies($annio, $semestre);
		$this -> generarPlantillaInscritoPrograma($inscritoPrograma);

		//problacion.inscrito_programa
		$admitido = $this -> miComponente -> consultarAdmitidoSnies($annio, $semestre);
		$this -> generarPlantillaAdmitido($admitido);

		echo 'Proceso finalizado';

	}

	function generarPlantillaInscrito($inscrito) {
		$raizDocumento = $this -> miConfigurador -> getVariableConfiguracion("raizDocumento");
		$this -> annio = $_REQUEST['annio'];
		$this -> semestre = $_REQUEST['semestre'];
		$file = $raizDocumento . '/document/inscritos_relacion' . $this -> annio . $this -> semestre . '.csv';
		$fp = fopen($file, 'w');
		//ENCABEZADO DE LA PLANTILLA INSCRITOS - RELACION DE INSCRITOS
		$linea1 = array('Herramienta de Cargue Hecca - V 3.4');
		$linea2 = array('[143] Nombre de la Plantilla: [Inscritos - Relación de Inscritos] Descripcion: [Persona natural que solicita formalmente el ingreso a un programa académico en calidad de estudiante.]');
		$linea3 = array('Licenciado para Ministerio de Educacion Nacional 2016');
		fwrite($fp, implode(',', $linea1) . "\r\n");
		//con esto elimina las comillas dobles del encabezado
		fwrite($fp, implode(',', $linea2) . "\r\n");
		fwrite($fp, implode(',', $linea3) . "\r\n");
		//fputcsv($fp, array(utf8_decode('AÑO'), 'SEMESTRE', 'ID_TIPO_DOCUMENTO', 'NUM_DOCUMENTO', 'PRIMER_NOMBRE', 'SEGUNDO_NOMBRE', 'PRIMER_APELLIDO', 'SEGUNDO_APELLIDO', 'ID_SEXO_BIOLOGICO'), ";");
		fputcsv($fp, array('AÑO', 'SEMESTRE', 'ID_TIPO_DOCUMENTO', 'NUM_DOCUMENTO', 'PRIMER_NOMBRE', 'SEGUNDO_NOMBRE', 'PRIMER_APELLIDO', 'SEGUNDO_APELLIDO', 'ID_SEXO_BIOLOGICO'), ";");
		foreach ($inscrito as $unInscrito) {

			if ($unInscrito['estado'] == 't') {
				$RelacionInscrito['AÑO'] = $unInscrito['ano'];
				$RelacionInscrito['SEMESTRE'] = $unInscrito['semestre'];
				$RelacionInscrito['ID_TIPO_DOCUMENTO'] = $unInscrito['id_tipo_documento'];
				$RelacionInscrito['NUM_DOCUMENTO'] = $unInscrito['num_documento'];
				$RelacionInscrito['PRIMER_NOMBRE'] = $unInscrito['primer_nombre'];
				$RelacionInscrito['SEGUNDO_NOMBRE'] = $unInscrito['segundo_nombre'];
				$RelacionInscrito['PRIMER_APELLIDO'] = $unInscrito['primer_apellido'];
				$RelacionInscrito['SEGUNDO_APELLIDO'] = $unInscrito['segundo_apellido'];
				$RelacionInscrito['ID_SEXO_BIOLOGICO'] = $unInscrito['id_sexo_biologico'];

				fputcsv($fp, $RelacionInscrito, ";");
			}
		}

		fclose($fp);

		echo "Se ha generado el archivo: <b>" . $file . "</b><br>";

		$file = $raizDocumento . '/document/inscritos_relacion_revision_' . $this -> annio . $this -> semestre . '.csv';
		$fp = fopen($file, 'w');
		//ENCABEZADO DE LA PLANTILLA INSCRITOS - RELACION DE INSCRITOS
		$linea1 = array('Herramienta de Cargue Hecca - V 3.4');
		$linea2 = array('[143] Nombre de la Plantilla: [Inscritos - Relación de Inscritos] Descripcion: [Persona natural que solicita formalmente el ingreso a un programa académico en calidad de estudiante.]');
		$linea3 = array('Licenciado para Ministerio de Educacion Nacional 2016');
		fwrite($fp, implode(',', $linea1) . "\r\n");
		//con esto elimina las comillas dobles del encabezado
		fwrite($fp, implode(',', $linea2) . "\r\n");
		fwrite($fp, implode(',', $linea3) . "\r\n");
		//fputcsv($fp, array(utf8_decode('AÑO'), 'SEMESTRE', 'ID_TIPO_DOCUMENTO', 'NUM_DOCUMENTO', 'PRIMER_NOMBRE', 'SEGUNDO_NOMBRE', 'PRIMER_APELLIDO', 'SEGUNDO_APELLIDO', 'ID_SEXO_BIOLOGICO'), ";");
		fputcsv($fp, array('AÑO', 'SEMESTRE', 'ID_TIPO_DOCUMENTO', 'NUM_DOCUMENTO', 'PRIMER_NOMBRE', 'SEGUNDO_NOMBRE', 'PRIMER_APELLIDO', 'SEGUNDO_APELLIDO', 'ID_SEXO_BIOLOGICO'), ";");
		foreach ($inscrito as $unInscrito) {
			if ($unInscrito['estado'] == '') {
				$RelacionInscrito['AÑO'] = $unInscrito['ano'];
				$RelacionInscrito['SEMESTRE'] = $unInscrito['semestre'];
				$RelacionInscrito['ID_TIPO_DOCUMENTO'] = $unInscrito['id_tipo_documento'];
				$RelacionInscrito['NUM_DOCUMENTO'] = $unInscrito['num_documento'];
				$RelacionInscrito['PRIMER_NOMBRE'] = $unInscrito['primer_nombre'];
				$RelacionInscrito['SEGUNDO_NOMBRE'] = $unInscrito['segundo_nombre'];
				$RelacionInscrito['PRIMER_APELLIDO'] = $unInscrito['primer_apellido'];
				$RelacionInscrito['SEGUNDO_APELLIDO'] = $unInscrito['segundo_apellido'];
				$RelacionInscrito['ID_SEXO_BIOLOGICO'] = $unInscrito['id_sexo_biologico'];

				fputcsv($fp, $RelacionInscrito, ";");
			}
		}

		fclose($fp);

		echo "Se ha generado el archivo: <b>" . $file . "</b><br>";

		$file = $raizDocumento . '/document/inscritos_relacion_error_' . $this -> annio . $this -> semestre . '.csv';
		$fp = fopen($file, 'w');
		//ENCABEZADO DE LA PLANTILLA INSCRITOS - RELACION DE INSCRITOS
		$linea1 = array('Herramienta de Cargue Hecca - V 3.4');
		$linea2 = array('[143] Nombre de la Plantilla: [Inscritos - Relación de Inscritos] Descripcion: [Persona natural que solicita formalmente el ingreso a un programa académico en calidad de estudiante.]');
		$linea3 = array('Licenciado para Ministerio de Educacion Nacional 2016');
		fwrite($fp, implode(',', $linea1) . "\r\n");
		//con esto elimina las comillas dobles del encabezado
		fwrite($fp, implode(',', $linea2) . "\r\n");
		fwrite($fp, implode(',', $linea3) . "\r\n");
		//fputcsv($fp, array(utf8_decode('AÑO'), 'SEMESTRE', 'ID_TIPO_DOCUMENTO', 'NUM_DOCUMENTO', 'PRIMER_NOMBRE', 'SEGUNDO_NOMBRE', 'PRIMER_APELLIDO', 'SEGUNDO_APELLIDO', 'ID_SEXO_BIOLOGICO'), ";");
		fputcsv($fp, array('AÑO', 'SEMESTRE', 'ID_TIPO_DOCUMENTO', 'NUM_DOCUMENTO', 'PRIMER_NOMBRE', 'SEGUNDO_NOMBRE', 'PRIMER_APELLIDO', 'SEGUNDO_APELLIDO', 'ID_SEXO_BIOLOGICO'), ";");
		foreach ($inscrito as $unInscrito) {
			if ($unInscrito['estado'] == 'f') {
				$RelacionInscrito['AÑO'] = $unInscrito['ano'];
				$RelacionInscrito['SEMESTRE'] = $unInscrito['semestre'];
				$RelacionInscrito['ID_TIPO_DOCUMENTO'] = $unInscrito['id_tipo_documento'];
				$RelacionInscrito['NUM_DOCUMENTO'] = $unInscrito['num_documento'];
				$RelacionInscrito['PRIMER_NOMBRE'] = $unInscrito['primer_nombre'];
				$RelacionInscrito['SEGUNDO_NOMBRE'] = $unInscrito['segundo_nombre'];
				$RelacionInscrito['PRIMER_APELLIDO'] = $unInscrito['primer_apellido'];
				$RelacionInscrito['SEGUNDO_APELLIDO'] = $unInscrito['segundo_apellido'];
				$RelacionInscrito['ID_SEXO_BIOLOGICO'] = $unInscrito['id_sexo_biologico'];

				fputcsv($fp, $RelacionInscrito, ";");
			}
		}

		fclose($fp);

		echo "Se ha generado el archivo: <b>" . $file . "</b><br>";

	}

	function generarPlantillaInscritoPrograma($inscrito) {
		$raizDocumento = $this -> miConfigurador -> getVariableConfiguracion("raizDocumento");
		$file = $raizDocumento . '/document/inscrito_programa_' . $this -> annio . $this -> semestre . '.csv';
		$this -> annio = $_REQUEST['annio'];
		$this -> semestre = $_REQUEST['semestre'];
		$fp = fopen($file, 'w');
		//ENCABEZADO DE LA PLANTILLA - INSCRITO - PROGRAMA
		$linea1 = array('Herramienta de Cargue Hecca - V 3.4');
		$linea2 = array('[144] Nombre de la Plantilla: [Inscrito Programa] Descripcion: [Relación de programas de los inscritos]');
		$linea3 = array('Licenciado para Ministerio de Educacion Nacional 2016');
		//con esto elimina las comillas dobles del encabezado
		fwrite($fp, implode(',', $linea1) . "\r\n");
		fwrite($fp, implode(',', $linea2) . "\r\n");
		fwrite($fp, implode(',', $linea3) . "\r\n");
		fputcsv($fp, array(utf8_decode('AÑO'), 'SEMESTRE', 'ID_TIPO_DOCUMENTO', 'NUM_DOCUMENTO', 'PRO_CONSECUTIVO', 'ID_MUNICIPIO'), ";");
		foreach ($inscrito as $unInscrito) {
			if ($unInscrito['estado'] == 't') {
				//var_dump ( $unInscrito );exit;
				$RelacionInscrito['ANO'] = $unInscrito['ano'];
				$RelacionInscrito['SEMESTRE'] = $unInscrito['semestre'];
				$RelacionInscrito['ID_TIPO_DOCUMENTO'] = $unInscrito['id_tipo_documento'];
				$RelacionInscrito['NUM_DOCUMENTO'] = $unInscrito['num_documento'];
				$RelacionInscrito['PRO_CONSECUTIVO'] = $unInscrito['pro_consecutivo'];
				$RelacionInscrito['ID_MUNICIPIO'] = $unInscrito['id_municipio'];

				fputcsv($fp, $RelacionInscrito, ";");
			}

		}

		fclose($fp);
		echo "Se ha generado el archivo: <b>" . $file . "</b><br>";

		$file = $raizDocumento . '/document/inscrito_programa_error_' . $this -> annio . $this -> semestre . '.csv';
		$this -> annio = $_REQUEST['annio'];
		$this -> semestre = $_REQUEST['semestre'];
		$fp = fopen($file, 'w');
		//ENCABEZADO DE LA PLANTILLA - INSCRITO - PROGRAMA
		$linea1 = array('Herramienta de Cargue Hecca - V 3.4');
		$linea2 = array('[144] Nombre de la Plantilla: [Inscrito Programa] Descripcion: [Relación de programas de los inscritos]');
		$linea3 = array('Licenciado para Ministerio de Educacion Nacional 2016');
		//con esto elimina las comillas dobles del encabezado
		fwrite($fp, implode(',', $linea1) . "\r\n");
		fwrite($fp, implode(',', $linea2) . "\r\n");
		fwrite($fp, implode(',', $linea3) . "\r\n");
		fputcsv($fp, array(utf8_decode('AÑO'), 'SEMESTRE', 'ID_TIPO_DOCUMENTO', 'NUM_DOCUMENTO', 'PRO_CONSECUTIVO', 'ID_MUNICIPIO'), ";");
		foreach ($inscrito as $unInscrito) {
			if ($unInscrito['estado'] == 'f') {
				//var_dump ( $unInscrito );exit;
				$RelacionInscrito['ANO'] = $unInscrito['ano'];
				$RelacionInscrito['SEMESTRE'] = $unInscrito['semestre'];
				$RelacionInscrito['ID_TIPO_DOCUMENTO'] = $unInscrito['id_tipo_documento'];
				$RelacionInscrito['NUM_DOCUMENTO'] = $unInscrito['num_documento'];
				$RelacionInscrito['PRO_CONSECUTIVO'] = $unInscrito['pro_consecutivo'];
				$RelacionInscrito['ID_MUNICIPIO'] = $unInscrito['id_municipio'];

				fputcsv($fp, $RelacionInscrito, ";");
			}

		}

		fclose($fp);
		echo "Se ha generado el archivo: <b>" . $file . "</b><br>";

		$file = $raizDocumento . '/document/inscrito_programa_revision_' . $this -> annio . $this -> semestre . '.csv';
		$this -> annio = $_REQUEST['annio'];
		$this -> semestre = $_REQUEST['semestre'];
		$fp = fopen($file, 'w');
		//ENCABEZADO DE LA PLANTILLA - INSCRITO - PROGRAMA
		$linea1 = array('Herramienta de Cargue Hecca - V 3.4');
		$linea2 = array('[144] Nombre de la Plantilla: [Inscrito Programa] Descripcion: [Relación de programas de los inscritos]');
		$linea3 = array('Licenciado para Ministerio de Educacion Nacional 2016');
		//con esto elimina las comillas dobles del encabezado
		fwrite($fp, implode(',', $linea1) . "\r\n");
		fwrite($fp, implode(',', $linea2) . "\r\n");
		fwrite($fp, implode(',', $linea3) . "\r\n");
		fputcsv($fp, array(utf8_decode('AÑO'), 'SEMESTRE', 'ID_TIPO_DOCUMENTO', 'NUM_DOCUMENTO', 'PRO_CONSECUTIVO', 'ID_MUNICIPIO'), ";");
		foreach ($inscrito as $unInscrito) {
			if ($unInscrito['estado'] == '') {
				//var_dump ( $unInscrito );exit;
				$RelacionInscrito['ANO'] = $unInscrito['ano'];
				$RelacionInscrito['SEMESTRE'] = $unInscrito['semestre'];
				$RelacionInscrito['ID_TIPO_DOCUMENTO'] = $unInscrito['id_tipo_documento'];
				$RelacionInscrito['NUM_DOCUMENTO'] = $unInscrito['num_documento'];
				$RelacionInscrito['PRO_CONSECUTIVO'] = $unInscrito['pro_consecutivo'];
				$RelacionInscrito['ID_MUNICIPIO'] = $unInscrito['id_municipio'];

				fputcsv($fp, $RelacionInscrito, ";");
			}

		}

		fclose($fp);
		echo "Se ha generado el archivo: <b>" . $file . "</b><br>";

	}

	function generarPlantillaAdmitido($admitido) {

		$raizDocumento = $this -> miConfigurador -> getVariableConfiguracion("raizDocumento");
		$file = $raizDocumento . '/document/admitido_' . $this -> annio . $this -> semestre . '.csv';
		$this -> annio = $_REQUEST['annio'];
		$this -> semestre = $_REQUEST['semestre'];
		$fp = fopen($file, 'w');
		//ENCABEZADO DE LA PLANTILLA
		$linea1 = array('Herramienta de Cargue Hecca - V 3.4');
		$linea2 = array('[59] Nombre de la Plantilla: [Admitidos] Descripcion: [Persona natural que, previo el proceso de selección realizado por el programa académico o la institución de educación superior y el cumplimiento de los');
		$linea3 = array('requisitos de ley, es aceptado para iniciar el proceso de formalización como estudiante en el programa al que se inscribió.]');
		$linea4 = array('Licenciado para Ministerio de Educacion Nacional 2016');
		//con esto elimina las comillas dobles del encabezado
		fwrite($fp, implode(',', $linea1) . "\r\n");
		fwrite($fp, implode(',', $linea2) . "\r\n");
		fwrite($fp, implode(',', $linea3) . "\r\n");
		fwrite($fp, implode(',', $linea4) . "\r\n");

		fputcsv($fp, array('AÑO', 'SEMESTRE', 'ID_TIPO_DOCUMENTO', 'NUM_DOCUMENTO', 'PRO_CONSECUTIVO', 'ID_MUNICIPIO'), ";");

		foreach ($admitido as $unadmitido) {
			//var_dump ( $unadmitido );exit;
			$Relacionadmitido['ANO'] = $unadmitido['ano'];
			$Relacionadmitido['SEMESTRE'] = $unadmitido['semestre'];
			$Relacionadmitido['ID_TIPO_DOCUMENTO'] = $unadmitido['id_tipo_documento'];
			$Relacionadmitido['NUM_DOCUMENTO'] = $unadmitido['num_documento'];
			$Relacionadmitido['PRO_CONSECUTIVO'] = $unadmitido['pro_consecutivo'];
			$Relacionadmitido['ID_MUNICIPIO'] = $unadmitido['id_municipio'];

			fputcsv($fp, $Relacionadmitido, ";");
		}

		fclose($fp);
		echo "Se ha generado el archivo: <b>" . $file . "</b><br>";

	}

}

$miProcesador = new FormProcessor($this -> lenguaje, $this -> sql);

$resultado = $miProcesador -> procesarFormulario();
