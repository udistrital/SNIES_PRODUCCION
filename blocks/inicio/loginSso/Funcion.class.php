<?php
if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}

include_once ("core/manager/Configurador.class.php");
include_once ("core/builder/InspectorHTML.class.php");
include_once ("core/builder/Mensaje.class.php");
include_once ("core/crypto/Encriptador.class.php");

// Esta clase contiene la logica de negocio del bloque y extiende a la clase funcion general la cual encapsula los
// metodos mas utilizados en la aplicacion

// Para evitar redefiniciones de clases el nombre de la clase del archivo funcion debe corresponder al nombre del bloque
// en camel case precedido por la palabra Funcion
class FuncionloginSso {
	var $sql;
	var $funcion;
	var $lenguaje;
	var $ruta;
	var $miConfigurador;
	var $miInspectorHTML;
	var $error;
	var $miRecursoDB;
	var 

	$crypto;
	function verificarCampos() {
		include_once ($this->ruta . "/funcion/verificarCampos.php");
		if ($this->error == true) {
			return false;
		} else {
			return true;
		}
	}
	function funcion() {
		include_once ($this->ruta . "/funcion/formProcessor.php");
	}
	function action() {
		$this->funcion ();
	}
	function __construct() {
		$this->miConfigurador = Configurador::singleton ();
		$this->ruta = $this->miConfigurador->getVariableConfiguracion ( "rutaBloque" );
	}
	public function setRuta($unaRuta) {
		$this->ruta = $unaRuta;
	}
	function setSql($a) {
		$this->sql = $a;
	}
	function setFuncion($funcion) {
		$this->funcion = $funcion;
	}
	public function setLenguaje($lenguaje) {
		$this->lenguaje = $lenguaje;
	}
	public function setFormulario($formulario) {
		$this->formulario = $formulario;
	}
}
?>
