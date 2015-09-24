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