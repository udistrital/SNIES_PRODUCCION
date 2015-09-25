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
			
			$estudiante [$clave] ['FECHA_NACIM'] = $this->excepcionFechaNacim ( $estudiante [$clave] );
			$estudiante [$clave] ['MUNICIPIO_LN'] = $this->excepcionMunicipio ( $estudiante [$clave] );
			$estudiante [$clave] ['EMAIL'] = $this->excepcionEmail ( $estudiante [$clave] );
			$estudiante [$clave] ['TIPO_DOC_UNICO'] = $this->excepcionTipoDocUnico ( $estudiante [$clave] );
			$estudiante [$clave] ['CODIGO_ID_ANT'] = $this->excepcionCodigoIdAnt ( $estudiante [$clave] );
			$estudiante [$clave] ['TIPO_ID_ANT'] = $this->excepcionTipoIdAnt ( $estudiante [$clave] );
			$estudiante [$clave] ['NUMERO_TEL'] = $this->excepcionNumeroTel ( $estudiante [$clave] );
		}
		
		return $estudiante;
	}
	
	/**
	 * si no existe la fecha de nacimiento se coloca '1900-01-01'
	 *
	 * @param unknown $unEstudiante        	
	 * @return Ambigous <string, unknown>
	 */
	function excepcionFechaNacim($unEstudiante) {
		if (isset ( $unEstudiante ['FECHA_NACIM'] )) {
			$resultado = $unEstudiante ['FECHA_NACIM'];
		} else {
			$resultado = '1900-01-01'; // para distinguir los que tiene valor nulo
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
		// si es de usme es Bogotá (11001)
		if ($unEstudiante ['MUNICIPIO_LN'] == '11850') {
			$resultado = '11001';
		} else {
			$resultado = $unEstudiante ['MUNICIPIO_LN'];
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
			$resultado = $unEstudiante ['TIPO_DOC_UNICO'];
		} else {
			//echo $unEstudiante ['CODIGO_UNICO']."<br>";
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
		if (isset($unEstudiante['CODIGO_ID_ANT'])) {
			$resultado = $unEstudiante ['CODIGO_ID_ANT'];
		}else{
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
			//echo $unEstudiante ['CODIGO_UNICO']."<br>";
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
}