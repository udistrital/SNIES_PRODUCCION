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

			$estudiante[$clave]['ID_TIPO_DOCUMENTO'] = $this -> excepcionTipoDocUnico($estudiante[$clave]);
			$estudiante[$clave]['FECHA_NACIMIENTO'] = $this -> excepcionFechaNacim($estudiante[$clave]);
			$estudiante[$clave]['ID_MUNICIPIO_PROGRAMA'] = $this -> excepcionMunicipioPrograma($estudiante[$clave]);
			$estudiante[$clave]['ID_MUNICIPIO_NACIMIENTO'] = $this -> excepcionMunicipioNacimiento($estudiante[$clave]);
			$estudiante[$clave]['EMAIL_PERSONAL'] = $this -> excepcionEmailPersonal($estudiante[$clave]);
			$estudiante[$clave]['EMAIL_INSTITUCIONAL'] = $this -> excepcionEmailInstitucional($estudiante[$clave]);
			$estudiante[$clave]['COD_PRUEBA_SABER_11'] = $this -> cod_prueba_saber_11($estudiante[$clave]);
			$estudiante[$clave]['TELEFONO_CONTACTO'] = $this -> excepcionNumeroTel($estudiante[$clave]);
			//$estudiante [$clave] ['ID_SEXO_BIOLOGICO'] = $this->excepcionSexoBiologico ( $estudiante [$clave] );
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
		foreach ($graduado as $clave => $valor) {

			$graduado[$clave]['SNP'] = $this -> excepcionSNP($graduado[$clave]);
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

		if (isset($unEstudiante['FECHA_NACIMIENTO'])) {

			// si la fecha es inferior a 1927 o mayor a 2002 se coloca valor por defecto '1990-01-01'
			// SNIES valida que la edad esté entre 14 y 90 años
			$fecha = split('/', $unEstudiante['FECHA_NACIMIENTO']);
			$ano = $fecha[2];
			if ($ano < '1927' or $ano > '2002') {
				$resultado = '1990-01-01';
			} else {
				$resultado = $unEstudiante['FECHA_NACIMIENTO'];
			}
			//si es TI la fecha debe corresponder a los primeros 6 digitos
			if (strlen($unEstudiante['NUM_DOCUMENTO']) == 11 and $unEstudiante['ID_TIPO_DOCUMENTO'] = 'TI') {

				//presenta la fecha en formato yyyymmdd
				$fechayymmdd = $fecha[2] . $fecha[1] . $fecha[0];
				//Obtiene la tarjeta de identidad son seis digitos precedido del 19. Ej 19850625
				$tarjetaIdentidadSeisDigitos = '19' . substr($unEstudiante['NUM_DOCUMENTO'], 0, 6);

				//es diferente la fecha se ajusta a los seis primeros digitos de la TI
				if ($tarjetaIdentidadSeisDigitos != $fechayymmdd) {
					$resultado = substr($tarjetaIdentidadSeisDigitos, 6) . '/' . substr($tarjetaIdentidadSeisDigitos, 4, 2) . '/' . substr($tarjetaIdentidadSeisDigitos, 0, 4);
				} else {
					$resultado = $unEstudiante['FECHA_NACIMIENTO'];
				}

			}

		} else {
			
			$resultado = '1990-01-01';
			// para distinguir los que tiene valor nulo
		}

		return $resultado;
	}

	function excepcionMunicipioPrograma($unEstudiante) {
		switch ($unEstudiante ['ID_MUNICIPIO_PROGRAMA']) {
			case '11850' :
				// Usme
				$resultado = '11001';
				;
				break;

			case '1' :
				// extranjero
				$resultado = '0001';
				break;

			case NULL :
				$resultado = '11001';
				break;

			default :
				$resultado = $unEstudiante['ID_MUNICIPIO_PROGRAMA'];
				break;
		}

		return $resultado;
	}

	function excepcionMunicipioNacimiento($unEstudiante) {
		switch ($unEstudiante ['ID_MUNICIPIO_NACIMIENTO']) {
			case '11850' :
				// Usme
				$resultado = '11001';
				;
				break;

			case '1' :
				// extranjero
				$resultado = '0001';
				break;

			default :
				$resultado = $unEstudiante['ID_MUNICIPIO_NACIMIENTO'];
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
			case '30' :
				// extranjero
				$resultado = '0';
				;
				break;

			default :
				$resultado = $unEstudiante['DEPARTAMENTO_LN'];
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
		if (isset($unEstudiante['EMAIL_PERSONAL'])) {
			$resultado = $unEstudiante['EMAIL_PERSONAL'];
		} else {
			$resultado = '';
			// para distinguir los que tiene valor nulo
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
		if (isset($unEstudiante['EMAIL_INSTITUCIONAL'])) {
			$resultado = $unEstudiante['EMAIL_INSTITUCIONAL'];
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
	 * @param array $unEstudiante
	 */
	function excepcionTipoDocUnico($unEstudiante) {
		//si el tipo de documento exist y es CC o TI o PS
		if (isset($unEstudiante['ID_TIPO_DOCUMENTO']) and ($unEstudiante['ID_TIPO_DOCUMENTO'] == 'CC' or $unEstudiante['ID_TIPO_DOCUMENTO'] == 'TI' or $unEstudiante['ID_TIPO_DOCUMENTO'] == 'PS')) {
			$longitudDocumento = strlen($unEstudiante['NUM_DOCUMENTO']);
			//Si es de longitud 11 => TI sino es el que tiene
			if ($longitudDocumento == 11) {
				$resultado = 'TI';
			} else {
				$resultado = $unEstudiante['ID_TIPO_DOCUMENTO'];
			}
		} else {
			// echo $unEstudiante ['NUM_DOCUMENTO']."<br>";
			$longitudDocumento = strlen($unEstudiante['NUM_DOCUMENTO']);
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
			$resultado = $unEstudiante['CODIGO_ID_ANT'];
		} else {
			$resultado = $unEstudiante['NUM_DOCUMENTO'];
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
		if (isset($unEstudiante['TIPO_ID_ANT']) and ($unEstudiante['TIPO_ID_ANT'] == 'CC' or $unEstudiante['TIPO_ID_ANT'] == 'TI' or $unEstudiante['TIPO_ID_ANT'] == 'PS')) {
			$resultado = $unEstudiante['TIPO_ID_ANT'];
		} else {
			// echo $unEstudiante ['NUM_DOCUMENTO']."<br>";
			$longitudDocumento = strlen($unEstudiante['CODIGO_ID_ANT']);
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
		if (!isset($unEstudiante['TELEFONO_CONTACTO'])) {
			$resultado = '3239300';
		} else {
			$longitudNumero = strlen($unEstudiante['TELEFONO_CONTACTO']);

			if ($longitudNumero < 7) {
				$resultado = '3239300';
			} else {
				$resultado = $unEstudiante['TELEFONO_CONTACTO'];
			}

		}

		return $resultado;
	}

	function cod_prueba_saber_11($unEstudiante) {
		if (isset($unEstudiante['COD_PRUEBA_SABER_11'])) {
			$resultado = $unEstudiante['COD_PRUEBA_SABER_11'];
		} else {
			$resultado = '0';
			// para distinguir los que tiene valor nulo
		}

		return $resultado;
	}

	/**
	 *
	 * @param unknown $graduado
	 * @return Ambigous <string, unknown>
	 */
	function excepcionSNP($graduado) {
		if (isset($graduado['SNP'])) {
			$resultado = $graduado['SNP'];
		} else {
			$resultado = 'N/A';
			// para distinguir los que tiene valor nulo
		}

		return $resultado;
	}

	/**
	 *
	 * @param unknown
	 * @return Ambigous <string, unknown>
	 */
	function excepcionSexoBiologico($estudiante) {

		echo 'implementar';
	}

}
