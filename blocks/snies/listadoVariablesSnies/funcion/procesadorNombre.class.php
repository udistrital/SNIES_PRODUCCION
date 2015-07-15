<?php

namespace bloqueSnies;

class procesadorNombre {
	
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
				'Ü' 
		);
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
				'U' 
		);
		
		// se reemplaza en cada registo el caracter de acento por el sencillo ej: á=>a
		foreach ( $arreglo as $key => $value ) {
			
			$arreglo [$key] [$campo] = str_replace ( $acento, $sinAcento, $arreglo [$key] [$campo] );
		}
		
		return $arreglo;
	}
	
	
}