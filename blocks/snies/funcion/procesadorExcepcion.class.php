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

		foreach ($estudiante as $clave => $valor) {

			$estudiante[$clave]['FECHA_NACIMIENTO'] = $this -> excepcionFechaNacim($estudiante[$clave]);
			$estudiante[$clave]['ID_MUNICIPIO_NACIMIENTO'] = $this -> excepcionMunicipio($estudiante[$clave]);
			$estudiante[$clave]['EMAIL_PERSONAL'] = $this -> excepcionEmail($estudiante[$clave]);
			$estudiante[$clave]['EMAIL_INSTITUCIONAL'] = $this -> excepcionEmailIns($estudiante[$clave]);
			$estudiante[$clave]['ID_TIPO_DOCUMENTO'] = $this -> excepcionTipoDocUnico($estudiante[$clave]);
			$estudiante[$clave]['TELEFONO_CONTACTO'] = $this -> excepcionNumeroTel($estudiante[$clave]);
			//$estudiante[$clave]['CODIGO_ID_ANT'] = $this -> excepcionCodigoIdAnt($estudiante[$clave]);
			//$estudiante[$clave]['TIPO_ID_ANT'] = $this -> excepcionTipoIdAnt($estudiante[$clave]);
		}

		return $estudiante;
	}

	function procesarExcepcionInscrito($inscrito) {

		foreach ($inscrito as $clave => $valor) {

			$inscrito[$clave]['ID_TIPO_DOCUMENTO'] = $this -> excepcionTipoDocUnico($inscrito[$clave]);
		}

		return $inscrito;
	}

	function procesarExcepcionDocente($docente) {
		foreach ($docente as $clave => $valor) {

			$docente[$clave]['FECHA_NACIMIENTO'] = $this -> excepcionFechaNacim($docente[$clave]);
			$docente[$clave]['MUNICIPIO_LN'] = $this -> excepcionMunicipio($docente[$clave]);
			$docente[$clave]['EMAIL'] = $this -> excepcionEmail($docente[$clave]);
			$docente[$clave]['EMAIL_INS'] = $this -> excepcionEmailIns($docente[$clave]);
			$docente[$clave]['TIPO_DOC_UNICO'] = $this -> excepcionTipoDocUnico($docente[$clave]);
			$docente[$clave]['CODIGO_ID_ANT'] = $this -> excepcionCodigoIdAnt($docente[$clave]);
			$docente[$clave]['TIPO_ID_ANT'] = $this -> excepcionTipoIdAnt($docente[$clave]);
			$docente[$clave]['NUMERO_TEL'] = $this -> excepcionNumeroTel($docente[$clave]);
		}

		return $docente;
	}

	function procesarExcepcionGraduado($docente) {
		foreach ($docente as $clave => $valor) {

			$docente[$clave]['FECHA_NACIMIENTO'] = $this -> excepcionFechaNacim($docente[$clave]);
			$docente[$clave]['MUNICIPIO_LN'] = $this -> excepcionMunicipio($docente[$clave]);
			$docente[$clave]['EMAIL'] = $this -> excepcionEmail($docente[$clave]);
			$docente[$clave]['TIPO_DOC_UNICO'] = $this -> excepcionTipoDocUnico($docente[$clave]);
			$docente[$clave]['CODIGO_ID_ANT'] = $this -> excepcionCodigoIdAnt($docente[$clave]);
			$docente[$clave]['TIPO_ID_ANT'] = $this -> excepcionTipoIdAnt($docente[$clave]);
			$docente[$clave]['NUMERO_TEL'] = $this -> excepcionNumeroTel($docente[$clave]);
		}

		return $docente;
	}

	/**
	 * si no existe la fecha de nacimiento se coloca '1900-01-01'
	 *
	 * @param unknown $persona
	 * @return Ambigous <string, unknown>
	 */
	function excepcionFechaNacim($persona) {
		
		if (isset($persona['FECHA_NACIMIENTO'])) {

			//el participante no puede tener mas de 90 o menos de 14 años, en el momento de registro: $persona['ANO']
			$fechaMinima = $persona['ANO'] - 90;
			$fechaMaxima = $persona['ANO'] + 14;

			// SNIES valida que la edad esté entre 14 y 90 años
			// la fecha debe estar en formato 'DD/MM/YYYY'; $fecha[0]=dia; $fecha[1]=mes;$fecha[2]=ano;
			$fecha = split('/', $persona['FECHA_NACIMIENTO']);
			$ano = $fecha[2];
			if ($ano < $fechaMinima or $ano > $fechaMaxima) {
				$resultado = '1990-01-01';
			} else {
				$resultado = $persona['FECHA_NACIMIENTO'];
			}
		} else {
			// para distinguir los que tiene valor nulo
			$resultado = '1990-01-01';
		}

		return $resultado;
	}

	/**
	 * si no existe la fecha de nacimiento se coloca '1900-01-01'
	 *
	 * @param unknown $persona
	 * @return Ambigous <string, unknown>
	 */
	function excepcionMunicipio($persona) {
		// si es de usme es Bogotá (11001)
		if ($persona['ID_MUNICIPIO_NACIMIENTO'] == '11850') {
			$resultado = '11001';
		} else {
			$resultado = $persona['ID_MUNICIPIO_NACIMIENTO'];
		}

		return $resultado;
	}

	/**
	 * si no existe el Email coloca ''
	 *
	 * @param unknown $persona
	 * @return Ambigous <string, unknown>
	 */
	function excepcionEmail($persona) {
		if (isset($persona['EMAIL_PERSONAL'])) {
			$resultado = $persona['EMAIL_PERSONAL'];
		} else {
			$resultado = '';
			// para distinguir los que tiene valor nulo
		}

		return $resultado;
	}

	/**
	 * si no existe el Email coloca ''
	 *
	 * @param unknown $persona
	 * @return Ambigous <string, unknown>
	 */
	function excepcionEmailIns($persona) {
		if (isset($persona['EMAIL_INSTITUCIONAL'])) {
			$resultado = $persona['EMAIL_INSTITUCIONAL'];
		} else {
			$resultado = '';
			// para distinguir los que tiene valor nulo
		}

		return $resultado;
	}

	/**
	 * Si el número de identificación es de 11 dígitos es TI
	 * el resto es CC
	 *
	 * @param array $persona
	 */
	function excepcionTipoDocUnico($persona) {

		$longitudDocumento = strlen($persona['NUM_DOCUMENTO']);

		//si existe pasar el valor que tiene
		if (isset($persona['ID_TIPO_DOCUMENTO'])) {
			$resultado = $persona['ID_TIPO_DOCUMENTO'];
		}

		//Todos los documentos de longitud 11 incluidos los NULL son 'TI'
		if ($longitudDocumento == 11) {
			$resultado = 'TI';
		}

		//Los documentos de tipo TI, que no tienen 11 digitos se marcan como CC
		if (isset($persona['ID_TIPO_DOCUMENTO']) and $persona['ID_TIPO_DOCUMENTO'] == 'TI' and $longitudDocumento != 11) {
			$resultado = 'CC';
		}

		//Los de tipo de documento NULL con longitud diferente de 11 de marcan como CC
		if (!isset($persona['ID_TIPO_DOCUMENTO']) and $longitudDocumento != 11) {
			$resultado = 'CC';
		}

		return $resultado;
	}

	/**
	 * Si el número de identificación anterior o existe se coloca el número actual
	 *
	 *
	 * @param array $persona
	 */
	function excepcionCodigoIdAnt($persona) {
		if (isset($persona['CODIGO_ID_ANT'])) {
			$resultado = $persona['CODIGO_ID_ANT'];
		} else {
			$resultado = $persona['CODIGO_UNICO'];
		}

		return $resultado;
	}

	/**
	 * Si el número de identificación anterior es de 11 dígitos es TI
	 * el resto es CC
	 *
	 * @param array $persona
	 */
	function excepcionTipoIdAnt($persona) {
		if (isset($persona['TIPO_ID_ANT']) and ($persona['TIPO_ID_ANT'] == 'CC' or $persona['TIPO_ID_ANT'] == 'TI' or $persona['TIPO_ID_ANT'] == 'PS')) {
			$resultado = $persona['TIPO_ID_ANT'];
		} else {
			// echo $persona ['CODIGO_UNICO']."<br>";
			$longitudDocumento = strlen($persona['CODIGO_ID_ANT']);
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
	 * @param unknown $persona
	 * @return Ambigous <string, unknown>
	 */
	function excepcionNumeroTel($persona) {
		if (isset($persona['NUMERO_TEL'])) {
			$resultado = preg_replace("/[^0-9,.]/", "", $persona['NUMERO_TEL']);
		} else {
			$resultado = '';
			// para distinguir los que tiene valor nulo
		}

		return $resultado;
	}

}
