<?php

namespace bloqueSnies;

class procesadorNombre {
	
	/**
	 *
	 * @param unknown $arreglo        	
	 * @param unknown $campo        	
	 */
	function buscarCaracteresInvalidos($arreglo, $campo) {
		//$a=0;
		foreach ( $arreglo as $key => $value ) {
			$nombre = $arreglo [$key] [$campo];
			$codigo = $arreglo [$key] ['CODIGO_ESTUDIANTE'];
			$documento = $arreglo [$key] ['CODIGO_UNICO'];
			if (! preg_match ( "/^[a-zA-ZnÑáéíóúüÁÉÍÓÚÜ ]*$/", $nombre )) {
				//$a++;
				//echo $a.'<br>';
				echo $codigo.', '.$nombre.', '.$documento;
				$nameErr = " <b>Solo letras y espacio permitidos</b>";
				//echo $nameErr;
				echo '<br>';
			}else {
				//echo ' válido<br>';
			}
		}
	}
	
	/**
	 * recorre el arreglo $arreglo y para los campos1 a campo5 del arreglo, reeemplaza áéíóú por aeiou
	 *
	 * @param arreglo $arreglo        	
	 * @param string $campo1        	
	 * @param string $campo2        	
	 * @param string $campo3        	
	 * @param string $campo4        	
	 * @param string $campo5        	
	 * @return mixed
	 */
	function quitarAcento($arreglo, $campo) {
					
		// estos arreglos se utilizan para quitar los acentos debido a que el SNIES central no acepta caracteres con acento, si acepta la ñ y la Ñ
		$acento = array (
				'á',
				'é',
				'í',
				'ó',
				'ú',
				'ü',
				'Á',
				'É',
				'Í',
				'Ó',
				'Ú',
				'Ü',
				'Ñ',
				'?',
				'¿',
				'	' 
		); // ' 'tabulador por espacio
		
		$sinAcento = array (
				'a',
				'e',
				'i',
				'o',
				'u',
				'u',
				'A',
				'E',
				'I',
				'O',
				'U',
				'U',
				'N',
				'N',
				'N',
				'N',
				' ' 
		);
		
		// se reemplaza en cada registo el caracter de acento por el sencillo ej: á=>a
		// SNIES ya soporta acentos esta clase solo ajusta los errores
		foreach ( $arreglo as $key => $value ) {
			
			$arreglo [$key] [$campo] = str_replace ( $acento, $sinAcento, $arreglo [$key] [$campo] );
			
		}

		return $arreglo;
	}
	
	/**
	 * Divide nombres completos en las siguientes partes
	 * Primer Apellido, Segundo Apellido, Primer nombre, Segundo Nombre
	 * Si tiene mas de cuatro partes estas se unen al segundo nombre
	 *
	 * @param string $nombreCompleto        	
	 * @return array
	 */
	function dividirNombreCompleto($nombreCompleto) {
		$nombreCompleto = trim ( $nombreCompleto );
		// Reemplaza los espacios dobles, triples y cuatriples por sencillos
		
		$espacios = array (
				"     ", // 5 espacios
				"    ", // 4 espacios
				"   ", // 3 espacios
				"  " 
		);
		$nombreCompleto = str_replace ( $espacios, " ", $nombreCompleto );
		// dividir el nombre por espacios
		$arregloNombres = explode ( " ", $nombreCompleto );
		
		$numeroPartes = sizeof ( $arregloNombres );
		
		// echo $numeroPartes;
		
		if ($numeroPartes == 8) {
			$nombre ['primer_apellido'] = $arregloNombres [0];
			$nombre ['segundo_apellido'] = $arregloNombres [1];
			$nombre ['primer_nombre'] = $arregloNombres [2];
			$nombre ['segundo_nombre'] = $arregloNombres [3] . $arregloNombres [4] . $arregloNombres [5] . $arregloNombres [6] . $arregloNombres [7];
		}
		
		if ($numeroPartes == 7) {
			$nombre ['primer_apellido'] = $arregloNombres [0];
			$nombre ['segundo_apellido'] = $arregloNombres [1];
			$nombre ['primer_nombre'] = $arregloNombres [2];
			$nombre ['segundo_nombre'] = $arregloNombres [3] . $arregloNombres [4] . $arregloNombres [5] . $arregloNombres [6];
		}
		
		if ($numeroPartes == 6) {
			$nombre ['primer_apellido'] = $arregloNombres [0];
			$nombre ['segundo_apellido'] = $arregloNombres [1];
			$nombre ['primer_nombre'] = $arregloNombres [2];
			$nombre ['segundo_nombre'] = $arregloNombres [3] . $arregloNombres [4] . $arregloNombres [5];
		}
		
		if ($numeroPartes == 5) {
			$nombre ['primer_apellido'] = $arregloNombres [0];
			$nombre ['segundo_apellido'] = $arregloNombres [1];
			$nombre ['primer_nombre'] = $arregloNombres [2];
			$nombre ['segundo_nombre'] = $arregloNombres [3] . $arregloNombres [4];
		}
		if ($numeroPartes == 4) {
			$nombre ['primer_apellido'] = $arregloNombres [0];
			$nombre ['segundo_apellido'] = $arregloNombres [1];
			$nombre ['primer_nombre'] = $arregloNombres [2];
			$nombre ['segundo_nombre'] = $arregloNombres [3];
		}
		if ($numeroPartes == 3) {
			$nombre ['primer_apellido'] = $arregloNombres [0];
			$nombre ['segundo_apellido'] = $arregloNombres [1];
			$nombre ['primer_nombre'] = $arregloNombres [2];
			$nombre ['segundo_nombre'] = '';
		}
		if ($numeroPartes == 2) {
			$nombre ['primer_apellido'] = $arregloNombres [0];
			$nombre ['segundo_apellido'] = '';
			$nombre ['primer_nombre'] = $arregloNombres [1];
			$nombre ['segundo_nombre'] = '';
		}
		// Error
		// cuando es una sola palabra, primer_nombre es igual a primer apellido
		// se debe depurar desde la fuente
		if ($numeroPartes == 1) {
			$nombre ['primer_apellido'] = $arregloNombres [0];
			$nombre ['segundo_apellido'] = '';
			$nombre ['primer_nombre'] = $arregloNombres [0];
			$nombre ['segundo_nombre'] = '';
		}
		// var_dump ( $nombre );
		
		return $nombre;
	}
	/**
	 * Divide apellidos compuestos en
	 * primer_apellido
	 * segundo_apellido
	 *
	 * @param unknown $apellidos        	
	 * @return string
	 */
	function dividirApellidos($apellidos) {
		$apellidos = trim ( $apellidos );
		// dividir el nombre por espacios
		$arregloApellidos = explode ( " ", $apellidos );
		
		$numeroPartes = sizeof ( $arregloApellidos );
		// echo $numeroPartes;
		
		if ($numeroPartes == 5) {
			$nombre ['primer_apellido'] = $arregloApellidos [0];
			$nombre ['segundo_apellido'] = $arregloApellidos [1] . $arregloApellidos [2] . $arregloApellidos [3] . $arregloApellidos [4];
		}
		if ($numeroPartes == 4) {
			$nombre ['primer_apellido'] = $arregloApellidos [0];
			$nombre ['segundo_apellido'] = $arregloApellidos [1] . $arregloApellidos [2] . $arregloApellidos [3];
		}
		if ($numeroPartes == 3) {
			$nombre ['primer_apellido'] = $arregloApellidos [0];
			$nombre ['segundo_apellido'] = $arregloApellidos [1] . $arregloApellidos [2];
		}
		if ($numeroPartes == 2) {
			$nombre ['primer_apellido'] = $arregloApellidos [0];
			$nombre ['segundo_apellido'] = $arregloApellidos [1];
		}
		if ($numeroPartes == 1) {
			$nombre ['primer_apellido'] = $arregloApellidos [0];
			$nombre ['segundo_apellido'] = '';
		}
		
		return $nombre;
	}
	/**
	 * Divide nombres compuestos en
	 * primer_nombre
	 * segundo_nombre
	 *
	 * @param string $nombres        	
	 * @return array $nombre
	 */
	function dividirNombres($nombres) {
		$nombres = trim ( $nombres );
		// dividir el nombre por espacios
		$arregloNombres = explode ( " ", $nombres );
		
		$numeroPartes = sizeof ( $arregloNombres );
		
		if ($numeroPartes == 5) {
			$nombre ['primer_nombre'] = $arregloNombres [0];
			$nombre ['segundo_nombre'] = $arregloNombres [1] . $arregloNombres [2] . $arregloNombres [3] . $arregloNombres [4];
		}
		if ($numeroPartes == 4) {
			$nombre ['primer_nombre'] = $arregloNombres [0];
			$nombre ['segundo_nombre'] = $arregloNombres [1] . $arregloNombres [2] . $arregloNombres [3];
		}
		if ($numeroPartes == 3) {
			$nombre ['primer_nombre'] = $arregloNombres [0];
			$nombre ['segundo_nombre'] = $arregloNombres [1] . $arregloNombres [2];
		}
		if ($numeroPartes == 2) {
			$nombre ['primer_nombre'] = $arregloNombres [0];
			$nombre ['segundo_nombre'] = $arregloNombres [1];
		}
		if ($numeroPartes == 1) {
			$nombre ['primer_nombre'] = $arregloNombres [0];
			$nombre ['segundo_nombre'] = '';
		}
		
		return $nombre;
	}
}
