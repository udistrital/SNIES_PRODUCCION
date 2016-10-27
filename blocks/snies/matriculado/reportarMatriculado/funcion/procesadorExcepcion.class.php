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
						
			$estudiante [$clave] ['FECHA_NACIMIENTO'] = $this->excepcionFechaNacim ( $estudiante [$clave] );
			$estudiante [$clave] ['ID_MUNICIPIO'] = $this->excepcionMunicipio ( $estudiante [$clave] );
			$estudiante [$clave] ['EMAIL_PERSONAL'] = $this->excepcionEmailPersonal ( $estudiante [$clave] );
			$estudiante [$clave] ['EMAIL_INSTITUCIONAL'] = $this->excepcionEmailInstitucional ( $estudiante [$clave] );
			$estudiante [$clave] ['ID_TIPO_DOCUMENTO'] = $this->excepcionTipoDocUnico ( $estudiante [$clave] );
			//$estudiante [$clave] ['TELEFONO_CONTACTO'] = $this->excepcionNumeroTel ( $estudiante [$clave] );			
			//$estudiante [$clave] ['CODIGO_ID_ANT'] = $this->excepcionCodigoIdAnt ( $estudiante [$clave] );
			//$estudiante [$clave] ['TIPO_ID_ANT'] = $this->excepcionTipoIdAnt ( $estudiante [$clave] );
			
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
	 * si no existe la fecha de nacimiento se coloca '1900-01-01'
	 *
	 * @param unknown $unEstudiante        	
	 * @return Ambigous <string, unknown>
	 */
	function excepcionFechaNacim($unEstudiante) {
		if (isset ( $unEstudiante ['FECHA_NACIMIENTO'] )) {
			
			// si la fecha es inferior a 1926 o mayor a 2001 se coloca valor por defecto '1990-01-01'
			// SNIES valida que la edad esté entre 14 y 90 años
			$fecha = split ( '-', $unEstudiante ['FECHA_NACIMIENTO'] );
			$ano = $fecha [0];
			if ($ano < '1926' or $ano > '2001') {
				$resultado = '1990-01-01';
			} else {
				$resultado = $unEstudiante ['FECHA_NACIMIENTO'];
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
		switch ($unEstudiante ['ID_MUNICIPIO']) {
			case '11850' : // Usme
				$resultado = '11001';
				;
				break;
			
			case '1' : // extranjero
				$resultado = '0001';
				break;
			
			default :
				$resultado = $unEstudiante ['ID_MUNICIPIO'];
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
	function excepcionEmailPersonal($unEstudiante) {
		if (isset ( $unEstudiante ['EMAIL_PERSONAL'] )) {
			$resultado = $unEstudiante ['EMAIL_PERSONAL'];
		} else {
			$resultado = ''; // para distinguir los que tiene valor nulo
		}
		
		return $resultado;
	}
	
		/**
	 * si no existe el Email coloca ''
	 *
	 * @param unknown $unEstudiante        	
	 * @return Ambigous <string, unknown>
	 */
	function excepcionEmailInstitucional($unEstudiante) {
		if (isset ( $unEstudiante ['EMAIL_INSTITUCIONAL'] )) {
			$resultado = $unEstudiante ['EMAIL_INSTITUCIONAL'];
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
		if (isset ( $unEstudiante ['ID_TIPO_DOCUMENTO'] ) and ($unEstudiante ['ID_TIPO_DOCUMENTO'] == 'CC' or $unEstudiante ['ID_TIPO_DOCUMENTO'] == 'TI' or $unEstudiante ['ID_TIPO_DOCUMENTO'] == 'PS')) {
			$longitudDocumento = strlen ( $unEstudiante ['NUM_DOCUMENTO'] );
			if ($longitudDocumento == 11) {
				$resultado = 'TI';
			} else {
				$resultado = $unEstudiante ['ID_TIPO_DOCUMENTO'];
			}
		} else {
			// echo $unEstudiante ['NUM_DOCUMENTO']."<br>";
			$longitudDocumento = strlen ( $unEstudiante ['NUM_DOCUMENTO'] );
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
			$resultado = $unEstudiante ['NUM_DOCUMENTO'];
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
			// echo $unEstudiante ['NUM_DOCUMENTO']."<br>";
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
	 * si no existe el TELEFONO_CONTACTO coloca ''
	 *
	 * @param unknown $unEstudiante        	
	 * @return Ambigous <string, unknown>
	 */
	function excepcionNumeroTel($unEstudiante) {
		if (isset ( $unEstudiante ['TELEFONO_CONTACTO'] )) {
			$resultado = $unEstudiante ['TELEFONO_CONTACTO'];
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