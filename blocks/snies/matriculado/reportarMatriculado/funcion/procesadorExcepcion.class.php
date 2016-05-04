<?php

namespace bloqueSnies;

class procesadorExcepcion {
	
	/**
	 * Recorre el arreglo e inserta las excepciones en el arreglo
	 *
	 * @param unknown $estudiante        	
	 * @return \bloqueSnies\Ambigous
	 */
	function procesarExcepcionEstudiante($estudiante) {
		foreach ( $estudiante as $clave => $valor ) {
			
			$estudiante [$clave] ['GRU_SANG'] = $this->excepcion_gru_sang ( $estudiante [$clave] );
			$estudiante [$clave] ['MUNICIPIO_LN'] = $this->excepcionMunicipio ( $estudiante [$clave] );
			$estudiante [$clave] ['DEPARTAMENTO_LN'] = $this->excepcionDepartamento ( $estudiante [$clave] );
			$estudiante [$clave] ['EMAIL'] = $this->excepcionEmail ( $estudiante [$clave] );
			$estudiante [$clave] ['TIPO_DOC_UNICO'] = $this->excepcionTipoDocUnico ( $estudiante [$clave] );
			$estudiante [$clave] ['CODIGO_ID_ANT'] = $this->excepcionCodigoIdAnt ( $estudiante [$clave] );
			$estudiante [$clave] ['TIPO_ID_ANT'] = $this->excepcionTipoIdAnt ( $estudiante [$clave] );
			$estudiante [$clave] ['NUMERO_TEL'] = $this->excepcionNumeroTel ( $estudiante [$clave] );
		}
		
		return $estudiante;
	}
	
	/**
	 * BPUDC - Base de datos Poblacional Unificada Distrito Capital MINTIC
	 * @param unknown $estudiante
	 * @return \bloqueSnies\Ambigous
	 */
	function procesarExcepcionEstudianteBPUDC($estudiante) {
		foreach ( $estudiante as $clave => $valor ) {
			
			$estudiante [$clave] ['GRU_SANG'] = $this->excepcionGruSang ( $estudiante [$clave] );
			//$estudiante [$clave] ['MUNICIPIO_LN'] = $this->excepcionMunicipio ( $estudiante [$clave] );
			//$estudiante [$clave] ['DEPARTAMENTO_LN'] = $this->excepcionDepartamento ( $estudiante [$clave] );
			//$estudiante [$clave] ['EMAIL'] = $this->excepcionEmail ( $estudiante [$clave] );
			//$estudiante [$clave] ['TIPO_DOC_UNICO'] = $this->excepcionTipoDocUnico ( $estudiante [$clave] );
			//$estudiante [$clave] ['CODIGO_ID_ANT'] = $this->excepcionCodigoIdAnt ( $estudiante [$clave] );
			//$estudiante [$clave] ['TIPO_ID_ANT'] = $this->excepcionTipoIdAnt ( $estudiante [$clave] );
			//$estudiante [$clave] ['NUMERO_TEL'] = $this->excepcionNumeroTel ( $estudiante [$clave] );
		}		
				
		return $estudiante;
	}
	
	/**
	 * Excepciones para registrar en la tabla graduados
	 *
	 * @param unknown $graduado        	
	 * @return \bloqueSnies\Ambigous
	 */
	function procesarExcepcionGraduado($graduado) {
		foreach ( $graduado as $clave => $valor ) {
			
			$graduado [$clave] ['SNP'] = $this->excepcionSNP ( $graduado [$clave] );
		}
		
		return $graduado;
	}
	
	/**
	 * Excepciones para grupo sanguineo
	 * @param unknown $unEstudiante
	 */
	function excepcionGruSang($unEstudiante) {
		if (isset ( $unEstudiante ['GRU_SANG'] )) {
				
			$resultado = $unEstudiante ['GRU_SANG'];

		} else {			
			$resultado = ' '; // para distinguir los que tienen valor nulo
		}
	
		return $resultado;
	}
	
	
	/**
	 * si no existe la fecha de nacimiento se coloca '1900-01-01'
	 *
	 * @param unknown $unEstudiante        	
	 * @return Ambigous <string, unknown>
	 */
	function excepcionFechaNacim($unEstudiante) {
		if (isset ( $unEstudiante ['FECHA_NACIM'] )) {
			
			// si la fecha es inferior a 1926 o mayor a 2001 se coloca valor por defecto '1990-01-01'
			// SNIES valida que la edad esté entre 14 y 90 años
			$fecha = split ( '-', $unEstudiante ['FECHA_NACIM'] );
			$ano = $fecha [0];
			if ($ano < '1926' or $ano > '2001') {
				$resultado = '1990-01-01';
			} else {
				$resultado = $unEstudiante ['FECHA_NACIM'];
			}
		} else {
			$resultado = '1990-01-01'; // para distinguir los que tiene valor nulo
		}
		
		return $resultado;
	}
	
	/**
	 * si no existe la fecha de nacimiento se coloca '1900-01-01'
	 *
	 * @param unknown $unEstudiante        	
	 * @return Ambigous <string, unknown>
	 */
	function excepcionMunicipio($unEstudiante) {
		switch ($unEstudiante ['MUNICIPIO_LN']) {
			case '11850' : // Usme
				$resultado = '11001';
				;
				break;
			
			case '1' : // extranjero
				$resultado = '0001';
				break;
			
			default :
				$resultado = $unEstudiante ['MUNICIPIO_LN'];
				break;
		}
		
		return $resultado;
	}
	
	/**
	 *
	 * @param unknown $unEstudiante        	
	 * @return unknown
	 */
	function excepcionDepartamento($unEstudiante) {
		switch ($unEstudiante ['DEPARTAMENTO_LN']) {
			case '30' : // extranjero
				$resultado = '0';
				;
				break;
			
			default :
				$resultado = $unEstudiante ['DEPARTAMENTO_LN'];
				break;
		}
		
		return $resultado;
	}
	
	/**
	 * si no existe el Email coloca ''
	 *
	 * @param unknown $unEstudiante        	
	 * @return Ambigous <string, unknown>
	 */
	function excepcionEmail($unEstudiante) {
		if (isset ( $unEstudiante ['EMAIL'] )) {
			$resultado = $unEstudiante ['EMAIL'];
		} else {
			$resultado = ''; // para distinguir los que tiene valor nulo
		}
		
		return $resultado;
	}
	
	/**
	 * Si el número de identificación es de 11 dígitos es TI
	 * el resto es CC
	 *
	 * @param array $unEstudiante        	
	 */
	function excepcionTipoDocUnico($unEstudiante) {
		if (isset ( $unEstudiante ['TIPO_DOC_UNICO'] ) and ($unEstudiante ['TIPO_DOC_UNICO'] == 'CC' or $unEstudiante ['TIPO_DOC_UNICO'] == 'TI' or $unEstudiante ['TIPO_DOC_UNICO'] == 'PS')) {
			$longitudDocumento = strlen ( $unEstudiante ['CODIGO_UNICO'] );
			if ($longitudDocumento == 11) {
				$resultado = 'TI';
			} else {
				$resultado = $unEstudiante ['TIPO_DOC_UNICO'];
			}
		} else {
			// echo $unEstudiante ['CODIGO_UNICO']."<br>";
			$longitudDocumento = strlen ( $unEstudiante ['CODIGO_UNICO'] );
			if ($longitudDocumento == 11) {
				$resultado = 'TI';
			} else {
				$resultado = 'CC';
			}
		}
		
		return $resultado;
	}
	
	/**
	 * Si el número de identificación anterior o existe se coloca el número actual
	 *
	 *
	 * @param array $unEstudiante        	
	 */
	function excepcionCodigoIdAnt($unEstudiante) {
		if (isset ( $unEstudiante ['CODIGO_ID_ANT'] )) {
			$resultado = $unEstudiante ['CODIGO_ID_ANT'];
		} else {
			$resultado = $unEstudiante ['CODIGO_UNICO'];
		}
		
		return $resultado;
	}
	/**
	 * Si el número de identificación anterior es de 11 dígitos es TI
	 * el resto es CC
	 *
	 * @param array $unEstudiante        	
	 */
	function excepcionTipoIdAnt($unEstudiante) {
		if (isset ( $unEstudiante ['TIPO_ID_ANT'] ) and ($unEstudiante ['TIPO_ID_ANT'] == 'CC' or $unEstudiante ['TIPO_ID_ANT'] == 'TI' or $unEstudiante ['TIPO_ID_ANT'] == 'PS')) {
			$resultado = $unEstudiante ['TIPO_ID_ANT'];
		} else {
			// echo $unEstudiante ['CODIGO_UNICO']."<br>";
			$longitudDocumento = strlen ( $unEstudiante ['CODIGO_ID_ANT'] );
			if ($longitudDocumento == 11) {
				$resultado = 'TI';
			} else {
				$resultado = 'CC';
			}
		}
		
		return $resultado;
	}
	
	/**
	 * si no existe el numero_tel coloca ''
	 *
	 * @param unknown $unEstudiante        	
	 * @return Ambigous <string, unknown>
	 */
	function excepcionNumeroTel($unEstudiante) {
		if (isset ( $unEstudiante ['NUMERO_TEL'] )) {
			$resultado = $unEstudiante ['NUMERO_TEL'];
		} else {
			$resultado = ''; // para distinguir los que tiene valor nulo
		}
		
		return $resultado;
	}
	
	/**
	 *
	 * @param unknown $graduado        	
	 * @return Ambigous <string, unknown>
	 */
	function excepcionSNP($graduado) {
		if (isset ( $graduado ['SNP'] )) {
			$resultado = $graduado ['SNP'];
		} else {
			$resultado = 'N/A'; // para distinguir los que tiene valor nulo
		}
		
		return $resultado;
	}
}