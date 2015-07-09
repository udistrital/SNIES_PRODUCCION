<?php
require_once ("core/manager/Configurador.class.php");
class ProcesadorPagina {
	var $miConfigurador;
	var $raizDocumentos;
	var $unBloque;
	const NOMBRE = 'nombre';
	const BLOQUEGRUPO = 'bloqueGrupo';
	const CARPETABLOQUES = '/blocks/';
	const ARCHIVOBLOQUE = '/bloque.php';
	function __construct() {
		$this->miConfigurador = Configurador::singleton ();
		
		$this->raizDocumentos = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
	}
	function procesarPagina() {
		
		/**
		 * Siempre debe existir una variable bloque que identifica el bloque que va a procesar los datos recibidos por REQUEST
		 * esta variable, y la variable bloqueGrupo se definen en el formulario y va codificada dentro de la variable formsaradata
		 */
		$carpeta = '';
		if (isset ( $_REQUEST [self::BLOQUEGRUPO] ) && $_REQUEST [self::BLOQUEGRUPO] != "") {
			$carpeta = $_REQUEST [self::BLOQUEGRUPO] . '/';
			$unBloque ['grupo'] = $carpeta;
		}
		/**
		 * Si está definida la variable $_REQUEST ['bloque'], entonces la variable $_REQUEST ['action'] podría contener
		 * la opción a ejecutar.
		 * 
		 * Si no está definida la variable $_REQUEST ['bloque'], entonces la la variable $_REQUEST ['action'] DEBE contener el
		 * nombre del bloque que procesará la petición y será necesaria una variable $_REQUEST ['opcion'] que determinaría
		 * la opción que se ejecutará al procesar el bloque.
		 * 
		 * En todo caso, si el bloque está contenido dentro de una carpeta, es necesario que la ruta se pase en la
		 * variable $_REQUEST ['bloqueGrupo'].
		 * 
		 * Dichas variables están disponibles para el programador en el objeto de la clase Configurador
		 *  
		 */
		if (isset ( $_REQUEST ['bloque'] )) {
			
			$unBloque [self::NOMBRE] = $_REQUEST ['bloque'];
			$unBloque ['id_bloque'] = $_REQUEST ["bloque"];
			include_once ($this->raizDocumentos . self::CARPETABLOQUES . $carpeta . $unBloque [self::NOMBRE] . self::ARCHIVOBLOQUE);
			return true;
		} elseif (isset ( $_REQUEST ["procesarAjax"] )) {
			
			include_once ($this->raizDocumentos . self::CARPETABLOQUES . $carpeta . $_REQUEST ["bloqueNombre"] . self::ARCHIVOBLOQUE);
		} elseif (isset ( $_REQUEST ['action'] )) {
			$unBloque [self::NOMBRE] = $_REQUEST ['action'];
			include_once ($this->raizDocumentos . self::CARPETABLOQUES . $carpeta . $unBloque [self::NOMBRE] . self::ARCHIVOBLOQUE);
			return true;
		}else{
			//TO DO poner 
		}
		
		return false;
	}
}