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
		
		// estudiante de la académica
		$estudiante = $this->miComponente->consultarEstudianteBpudc ( $this->annio, $this->semestre );
		
		// var_dump($estudiante);
		
		$miProcesadorNombre = new procesadorNombre ();
		
		// $caracteresInvalidos = $miProcesadorNombre->buscarCaracteresInvalidos ( $estudiante, 'EST_NOMBRE' );
		
		// quita acentos del nombre
		// $estudiante = $miProcesadorNombre->quitarAcento ( $estudiante, 'EST_NOMBRE' );
		
		// descompone nombre completo en sus partes y las aglega al final de cada registro
		foreach ( $estudiante as $clave => $valor ) {
			// echo $estudiante [$clave] ['CODIGO_UNICO'].'<br>';
			$nombreCompleto = $miProcesadorNombre->dividirNombreCompleto ( $estudiante [$clave] ['NOMBRE'] );
			$estudiante [$clave] ['APELLIDO_1'] = $nombreCompleto ['primer_apellido'];
			$estudiante [$clave] ['APELLIDO_2'] = $nombreCompleto ['segundo_apellido'];
			$estudiante [$clave] ['NOMBRE_1'] = $nombreCompleto ['primer_nombre'];
			$estudiante [$clave] ['NOMBRE_2'] = $nombreCompleto ['segundo_nombre'];
		}
		
		// $miProcesadorExcepcion = new procesadorExcepcion ();
		// FORMATEA LOS VALORES NULOS, CODIFICA EXCEPCIONES
		// $estudiante = $miProcesadorExcepcion->procesarExcepcionEstudianteBPUDC ( $estudiante );
		
		$this->generarListadoEstudiantesBPUDC ( $estudiante );
		$raizDocumento = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
		echo 'Se ha generado el archivo: ' . $raizDocumento . '/document/estudiante_bpudc' . $this->annio . $this->semestre . '.csv';
		echo '<br>';
	}
	function generarListadoEstudiantesBPUDC($estudiante) {
		$raizDocumento = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
		$fp = fopen ( $raizDocumento . '/document/estudiante_bpudc' . $this->annio . $this->semestre . '.csv', 'w' );
		fputcsv ( $fp, array (
				'ID',
				'NOMBRE_1',
				'NOMBRE_2',
				'APELLIDO_1',
				'APELLIDO_2',
				'TIP_ID',
				'NUM_ID',
				'MUN_NAC',
				'PAIS_NAC',
				'FEC_ID',
				'SEXO',
				'FEC_NAC',
				'GRU_SANG',
				'FACT_RH',
				'ETNIA',
				'CUAL_ETNIA',
				'GENERO',
				'CUAL_GENERO',
				'NOM_IDENTITARIO',
				'ORIENT_SEX',
				'CUAL_ORIENT_SEX',
				'OCUPACION',
				'CUAL_OCUPACION',
				'COND_HABITACION',
				'TIPO_ATEN_POB_INFANTIL',
				'OCUP_ESPECIAL',
				'COND_ESPECIAL',
				'CARA_ESPE_PADRES',
				'COND_ESPE_SALUD',
				'TRABA_SEXUAL',
				'PERSONA_TALENTO',
				'EST_AFI_SGSSS',
				'LOCALIDAD',
				'TIPO_ZONA',
				'TIP_VIA_PRIN',
				'NUM_VIA_PRIN',
				'NOM_VIA_PRIN',
				'NOM_SIN_VIA_PRIN',
				'LETRA_VIA_PRIN',
				'BIS',
				'LETRA_BIS',
				'CUAD_VIA_PRIN',
				'NUM_VIA_GEN',
				'LETRA_VIA_GEN',
				'NUM_PLACA',
				'CUAD_VIA_GEN',
				'COMPLEMENTO',
				'DIRECCION_RURAL',
				'ESTRATO',
				'TEL_FIJO_CONTACTO',
				'TEL_CELULAR_CONTACTO',
				'CORREO_ELECTR',
				'LOCALIDAD_CONTACTO',
				'TIPO_ZONA_CONTACTO',
				'TIP_VIA_PRIN_CONTACTO',
				'NUM_VIA_PRIN_CONTACTO',
				'NOM_VIA_PRIN_CONTACTO',
				'NOM_SIN_VIA_PRIN_CONTACTO',
				'LETRA_VIA_PRIN_CONTACTO',
				'BIS_CONTACTO',
				'LETRA_BIS_CONTACTO',
				'CUAD_VIA_PRIN_CONTACTO',
				'NUM_VIA_GEN_CONTACTO',
				'LETRA_VIA_GEN_CONTACTO',
				'NUM_PLACA_CONTACTO',
				'CUAD_VIA_GEN_CONTACTO',
				'COMPLEMENTO_CONTACTO',
				'DIRECCION_RURAL_CONTACTO',
				'ESTRATO_CONTACTO',
				'TEL_FIJO_CONTACTO_CONTACTO',
				'TEL_CELULAR_CONTACTO_CONTACTO',
				'CORREO_ELECTR_CONTACTO',
				'NOMBRE_CONTACTO' 
		), '|' );
		
		foreach ( $estudiante as $unEstudiante ) {
			var_dump ( $unEstudiante ['FACT_RH'] );
			
			$estudianteBPUDC ['ID'] = isset ( $unEstudiante ['ID'] ) ? $unEstudiante ['ID'] : '';
			$estudianteBPUDC ['NOMBRE_1'] = isset ( $unEstudiante ['NOMBRE_1'] ) ? $unEstudiante ['NOMBRE_1'] : '';
			$estudianteBPUDC ['NOMBRE_2'] = isset ( $unEstudiante ['NOMBRE_2'] ) ? $unEstudiante ['NOMBRE_2'] : '';
			$estudianteBPUDC ['APELLIDO_1'] = isset ( $unEstudiante ['APELLIDO_1'] ) ? $unEstudiante ['APELLIDO_1'] : '';
			$estudianteBPUDC ['APELLIDO_2'] = isset ( $unEstudiante ['APELLIDO_2'] ) ? $unEstudiante ['APELLIDO_2'] : '';
			$estudianteBPUDC ['TIP_ID'] = isset ( $unEstudiante ['TIP_ID'] ) ? $unEstudiante ['TIP_ID'] : '';
			$estudianteBPUDC ['NUM_ID'] = isset ( $unEstudiante ['NUM_ID'] ) ? $unEstudiante ['NUM_ID'] : '';
			$estudianteBPUDC ['MUN_NAC'] = isset ( $unEstudiante ['MUN_NAC'] ) ? $unEstudiante ['MUN_NAC'] : '';
			$estudianteBPUDC ['PAIS_NAC'] = isset ( $unEstudiante ['PAIS_NAC'] ) ? $unEstudiante ['PAIS_NAC'] : '';
			$estudianteBPUDC ['FEC_ID'] = isset ( $unEstudiante ['FEC_ID'] ) ? $unEstudiante ['FEC_ID'] : '';
			$estudianteBPUDC ['SEXO'] = isset ( $unEstudiante ['SEXO'] ) ? $unEstudiante ['SEXO'] : '';
			$estudianteBPUDC ['FEC_NAC'] = isset ( $unEstudiante ['FEC_NAC'] ) ? $unEstudiante ['FEC_NAC'] : '';
			$estudianteBPUDC ['GRU_SANG'] = isset ( $unEstudiante ['GRU_SANG'] ) ? $unEstudiante ['GRU_SANG'] : '';
			if (isset ( $unEstudiante ['FACT_RH'] )) {
				
				switch ($unEstudiante ['FACT_RH']) {
					
					case '+' :
						$estudianteBPUDC ['FACT_RH'] = 'positivo';
						break;
					case '-' :
						$estudianteBPUDC ['FACT_RH'] = 'negativo';
						break;
				}
			} else {
				$estudianteBPUDC ['FACT_RH'] = 'no existe';
			}
			$estudianteBPUDC ['ETNIA'] = isset ( $unEstudiante ['ETNIA'] ) ? $unEstudiante ['ETNIA'] : '';
			$estudianteBPUDC ['CUAL_ETNIA'] = isset ( $unEstudiante ['CUAL_ETNIA'] ) ? $unEstudiante ['CUAL_ETNIA'] : '';
			$estudianteBPUDC ['GENERO'] = isset ( $unEstudiante ['GENERO'] ) ? $unEstudiante ['GENERO'] : '';
			$estudianteBPUDC ['CUAL_GENERO'] = isset ( $unEstudiante ['CUAL_GENERO'] ) ? $unEstudiante ['CUAL_GENERO'] : '';
			$estudianteBPUDC ['NOM_IDENTITARIO'] = isset ( $unEstudiante ['NOM_IDENTITARIO'] ) ? $unEstudiante ['NOM_IDENTITARIO'] : '';
			$estudianteBPUDC ['ORIENT_SEX'] = isset ( $unEstudiante ['ORIENT_SEX'] ) ? $unEstudiante ['ORIENT_SEX'] : '';
			$estudianteBPUDC ['CUAL_ORIENT_SEX'] = isset ( $unEstudiante ['CUAL_ORIENT_SEX'] ) ? $unEstudiante ['CUAL_ORIENT_SEX'] : '';
			$estudianteBPUDC ['OCUPACION'] = isset ( $unEstudiante ['OCUPACION'] ) ? $unEstudiante ['OCUPACION'] : '';
			$estudianteBPUDC ['CUAL_OCUPACION'] = isset ( $unEstudiante ['CUAL_OCUPACION'] ) ? $unEstudiante ['CUAL_OCUPACION'] : '';
			$estudianteBPUDC ['COND_HABITACION'] = isset ( $unEstudiante ['COND_HABITACION'] ) ? $unEstudiante ['COND_HABITACION'] : '';
			$estudianteBPUDC ['TIPO_ATEN_POB_INFANTIL'] = isset ( $unEstudiante ['TIPO_ATEN_POB_INFANTIL'] ) ? $unEstudiante ['TIPO_ATEN_POB_INFANTIL'] : '';
			$estudianteBPUDC ['OCUP_ESPECIAL'] = isset ( $unEstudiante ['OCUP_ESPECIAL'] ) ? $unEstudiante ['OCUP_ESPECIAL'] : '';
			$estudianteBPUDC ['COND_ESPECIAL'] = isset ( $unEstudiante ['COND_ESPECIAL'] ) ? $unEstudiante ['COND_ESPECIAL'] : '';
			$estudianteBPUDC ['CARA_ESPE_PADRES'] = isset ( $unEstudiante ['CARA_ESPE_PADRES'] ) ? $unEstudiante ['CARA_ESPE_PADRES'] : '';
			$estudianteBPUDC ['COND_ESPE_SALUD'] = isset ( $unEstudiante ['COND_ESPE_SALUD'] ) ? $unEstudiante ['COND_ESPE_SALUD'] : '';
			$estudianteBPUDC ['TRABA_SEXUAL'] = isset ( $unEstudiante ['TRABA_SEXUAL'] ) ? $unEstudiante ['TRABA_SEXUAL'] : '';
			$estudianteBPUDC ['PERSONA_TALENTO'] = isset ( $unEstudiante ['PERSONA_TALENTO'] ) ? $unEstudiante ['PERSONA_TALENTO'] : '';
			$estudianteBPUDC ['EST_AFI_SGSSS'] = isset ( $unEstudiante ['EST_AFI_SGSSS'] ) ? $unEstudiante ['EST_AFI_SGSSS'] : '';
			$estudianteBPUDC ['LOCALIDAD'] = isset ( $unEstudiante ['LOCALIDAD'] ) ? $unEstudiante ['LOCALIDAD'] : '';
			$estudianteBPUDC ['TIPO_ZONA'] = isset ( $unEstudiante ['TIPO_ZONA'] ) ? $unEstudiante ['TIPO_ZONA'] : '';
			$estudianteBPUDC ['TIP_VIA_PRIN'] = isset ( $unEstudiante ['TIP_VIA_PRIN'] ) ? $unEstudiante ['TIP_VIA_PRIN'] : '';
			$estudianteBPUDC ['NUM_VIA_PRIN'] = isset ( $unEstudiante ['NUM_VIA_PRIN'] ) ? $unEstudiante ['NUM_VIA_PRIN'] : '';
			$estudianteBPUDC ['NOM_VIA_PRIN'] = isset ( $unEstudiante ['NOM_VIA_PRIN'] ) ? $unEstudiante ['NOM_VIA_PRIN'] : '';
			$estudianteBPUDC ['NOM_SIN_VIA_PRIN'] = isset ( $unEstudiante ['NOM_SIN_VIA_PRIN'] ) ? $unEstudiante ['NOM_SIN_VIA_PRIN'] : '';
			$estudianteBPUDC ['LETRA_VIA_PRIN'] = isset ( $unEstudiante ['LETRA_VIA_PRIN'] ) ? $unEstudiante ['LETRA_VIA_PRIN'] : '';
			$estudianteBPUDC ['BIS'] = isset ( $unEstudiante ['BIS'] ) ? $unEstudiante ['BIS'] : '';
			$estudianteBPUDC ['LETRA_BIS'] = isset ( $unEstudiante ['LETRA_BIS'] ) ? $unEstudiante ['LETRA_BIS'] : '';
			$estudianteBPUDC ['CUAD_VIA_PRIN'] = isset ( $unEstudiante ['CUAD_VIA_PRIN'] ) ? $unEstudiante ['CUAD_VIA_PRIN'] : '';
			$estudianteBPUDC ['NUM_VIA_GEN'] = isset ( $unEstudiante ['NUM_VIA_GEN'] ) ? $unEstudiante ['NUM_VIA_GEN'] : '';
			$estudianteBPUDC ['LETRA_VIA_GEN'] = isset ( $unEstudiante ['LETRA_VIA_GEN'] ) ? $unEstudiante ['LETRA_VIA_GEN'] : '';
			$estudianteBPUDC ['NUM_PLACA'] = isset ( $unEstudiante ['NUM_PLACA'] ) ? $unEstudiante ['NUM_PLACA'] : '';
			$estudianteBPUDC ['CUAD_VIA_GEN'] = isset ( $unEstudiante ['CUAD_VIA_GEN'] ) ? $unEstudiante ['CUAD_VIA_GEN'] : '';
			$estudianteBPUDC ['COMPLEMENTO'] = isset ( $unEstudiante ['COMPLEMENTO'] ) ? $unEstudiante ['COMPLEMENTO'] : '';
			$estudianteBPUDC ['DIRECCION_RURAL'] = isset ( $unEstudiante ['DIRECCION_RURAL'] ) ? $unEstudiante ['DIRECCION_RURAL'] : '';
			$estudianteBPUDC ['ESTRATO'] = isset ( $unEstudiante ['ESTRATO'] ) ? $unEstudiante ['ESTRATO'] : '';
			$estudianteBPUDC ['TEL_FIJO_CONTACTO'] = isset ( $unEstudiante ['TEL_FIJO_CONTACTO'] ) ? $unEstudiante ['TEL_FIJO_CONTACTO'] : '';
			$estudianteBPUDC ['TEL_CELULAR_CONTACTO'] = isset ( $unEstudiante ['TEL_CELULAR_CONTACTO'] ) ? $unEstudiante ['TEL_CELULAR_CONTACTO'] : '';
			$estudianteBPUDC ['CORREO_ELECTR'] = isset ( $unEstudiante ['CORREO_ELECTR'] ) ? $unEstudiante ['CORREO_ELECTR'] : '';
			$estudianteBPUDC ['LOCALIDAD_CONTACTO'] = isset ( $unEstudiante ['LOCALIDAD_CONTACTO'] ) ? $unEstudiante ['LOCALIDAD_CONTACTO'] : '';
			$estudianteBPUDC ['TIPO_ZONA_CONTACTO'] = isset ( $unEstudiante ['TIPO_ZONA_CONTACTO'] ) ? $unEstudiante ['TIPO_ZONA_CONTACTO'] : '';
			$estudianteBPUDC ['TIP_VIA_PRIN_CONTACTO'] = isset ( $unEstudiante ['TIP_VIA_PRIN_CONTACTO'] ) ? $unEstudiante ['TIP_VIA_PRIN_CONTACTO'] : '';
			$estudianteBPUDC ['NUM_VIA_PRIN_CONTACTO'] = isset ( $unEstudiante ['NUM_VIA_PRIN_CONTACTO'] ) ? $unEstudiante ['NUM_VIA_PRIN_CONTACTO'] : '';
			$estudianteBPUDC ['NOM_VIA_PRIN_CONTACTO'] = isset ( $unEstudiante ['NOM_VIA_PRIN_CONTACTO'] ) ? $unEstudiante ['NOM_VIA_PRIN_CONTACTO'] : '';
			$estudianteBPUDC ['NOM_SIN_VIA_PRIN_CONTACTO'] = isset ( $unEstudiante ['NOM_SIN_VIA_PRIN_CONTACTO'] ) ? $unEstudiante ['NOM_SIN_VIA_PRIN_CONTACTO'] : '';
			$estudianteBPUDC ['LETRA_VIA_PRIN_CONTACTO'] = isset ( $unEstudiante ['LETRA_VIA_PRIN_CONTACTO'] ) ? $unEstudiante ['LETRA_VIA_PRIN_CONTACTO'] : '';
			$estudianteBPUDC ['BIS_CONTACTO'] = isset ( $unEstudiante ['BIS_CONTACTO'] ) ? $unEstudiante ['BIS_CONTACTO'] : '';
			$estudianteBPUDC ['LETRA_BIS_CONTACTO'] = isset ( $unEstudiante ['LETRA_BIS_CONTACTO'] ) ? $unEstudiante ['LETRA_BIS_CONTACTO'] : '';
			$estudianteBPUDC ['CUAD_VIA_PRIN_CONTACTO'] = isset ( $unEstudiante ['CUAD_VIA_PRIN_CONTACTO'] ) ? $unEstudiante ['CUAD_VIA_PRIN_CONTACTO'] : '';
			$estudianteBPUDC ['NUM_VIA_GEN_CONTACTO'] = isset ( $unEstudiante ['NUM_VIA_GEN_CONTACTO'] ) ? $unEstudiante ['NUM_VIA_GEN_CONTACTO'] : '';
			$estudianteBPUDC ['LETRA_VIA_GEN_CONTACTO'] = isset ( $unEstudiante ['LETRA_VIA_GEN_CONTACTO'] ) ? $unEstudiante ['LETRA_VIA_GEN_CONTACTO'] : '';
			$estudianteBPUDC ['NUM_PLACA_CONTACTO'] = isset ( $unEstudiante ['NUM_PLACA_CONTACTO'] ) ? $unEstudiante ['NUM_PLACA_CONTACTO'] : '';
			$estudianteBPUDC ['CUAD_VIA_GEN_CONTACTO'] = isset ( $unEstudiante ['CUAD_VIA_GEN_CONTACTO'] ) ? $unEstudiante ['CUAD_VIA_GEN_CONTACTO'] : '';
			$estudianteBPUDC ['COMPLEMENTO_CONTACTO'] = isset ( $unEstudiante ['COMPLEMENTO_CONTACTO'] ) ? $unEstudiante ['COMPLEMENTO_CONTACTO'] : '';
			$estudianteBPUDC ['DIRECCION_RURAL_CONTACTO'] = isset ( $unEstudiante ['DIRECCION_RURAL_CONTACTO'] ) ? $unEstudiante ['DIRECCION_RURAL_CONTACTO'] : '';
			$estudianteBPUDC ['ESTRATO_CONTACTO'] = isset ( $unEstudiante ['ESTRATO_CONTACTO'] ) ? $unEstudiante ['ESTRATO_CONTACTO'] : '';
			$estudianteBPUDC ['TEL_FIJO_CONTACTO_CONTACTO'] = isset ( $unEstudiante ['TEL_FIJO_CONTACTO_CONTACTO'] ) ? $unEstudiante ['TEL_FIJO_CONTACTO_CONTACTO'] : '';
			$estudianteBPUDC ['TEL_CELULAR_CONTACTO_CONTACTO'] = isset ( $unEstudiante ['TEL_CELULAR_CONTACTO_CONTACTO'] ) ? $unEstudiante ['TEL_CELULAR_CONTACTO_CONTACTO'] : '';
			$estudianteBPUDC ['CORREO_ELECTR_CONTACTO'] = isset ( $unEstudiante ['CORREO_ELECTR_CONTACTO'] ) ? $unEstudiante ['CORREO_ELECTR_CONTACTO'] : '';
			$estudianteBPUDC ['NOMBRE_CONTACTO'] = isset ( $unEstudiante ['NOMBRE_CONTACTO'] ) ? $unEstudiante ['NOMBRE_CONTACTO'] : '';
			var_dump ( $estudianteBPUDC ['FACT_RH'] );
			exit ();
			fputcsv ( $fp, $estudianteBPUDC, '|' );
		}
		
		fclose ( $fp );
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );

$resultado = $miProcesador->procesarFormulario ();

