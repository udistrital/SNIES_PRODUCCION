<?php

namespace snies\matriculado\reportarMatriculado;

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
class Funcion {
	var $sql;
	var $funcion;
	var $lenguaje;
	var $ruta;
	var $miConfigurador;
	var $miInspectorHTML;
	var $error;
	var $miRecursoDB;
	var $crypto;
	// function verificarCampos() {
	// include_once ($this->ruta . "/funcion/verificarCampos.php");
	// if ($this->error == true) {
	// return false;
	// } else {
	// return true;
	// }
	// }
	function procesarAjax() {
		include_once ($this->ruta . "funcion/procesarAjax.php");
	}
	function actualizarParticipanteDocente() {
		include_once ($this->ruta . "/funcion/actualizarParticipanteDocente.php");
	}
	function actualizarEstudiante() {
		include_once ($this->ruta . "/funcion/actualizarEstudiante.php");
	}
	function actualizarMatriculado() {
		include_once ($this->ruta . "/funcion/actualizarMatriculado.php");
	}
	function generarCsvDocente() {
		include_once ($this->ruta . "/funcion/generarCsvDocente.php");
	}
	
	function action() {
	
		if (isset ( $_REQUEST ['procesarAjax'] )) {
			$this->procesarAjax ();
		} else if (isset ( $_REQUEST ["opcion"] )) {
			
			switch ($_REQUEST ["opcion"]) {
				case 'actualizarParticipanteDocente' :
					$this->actualizarParticipanteDocente ();
					break;
				
				case 'actualizarEstudiante' :
					$this->actualizarEstudiante ();
					break;
				
				case 'actualizarMatriculado' :
					$this->actualizarMatriculado ();
					break;
				
				case 'actualizarMatriculado' :
					$this->actualizarMatriculado ();
					break;
				
				case 'generarCsvDocente' :
					$this->generarCsvDocente ();
					break;
				
				case 'actualizar' :
				case 'actualizar' :
					if (isset ( $_REQUEST ["botonRegresar"] ) && $_REQUEST ["botonRegresar"] == 'true') {
						$arreglo = unserialize ( $_REQUEST ['arreglo'] );
						redireccion::redireccionar ( "paginaConsulta", $arreglo );
						exit ();
					} else if (isset ( $_REQUEST ["botonGuardar"] ) && $_REQUEST ["botonGuardar"] == 'true') {
						$this->actualizar ();
					}
					break;
			}
		} else {
			echo "request opcion no existe";
		}
	}
	function __construct() {
		$this->miConfigurador = \Configurador::singleton ();
		
		$this->miInspectorHTML = \InspectorHTML::singleton ();
		
		$this->ruta = $this->miConfigurador->getVariableConfiguracion ( "rutaBloque" );
		
		$this->miMensaje = \Mensaje::singleton ();
		
		$conexion = "aplicativo";
		$this->miRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		if (! $this->miRecursoDB) {
			
			$this->miConfigurador->fabricaConexiones->setRecursoDB ( $conexion, "tabla" );
			$this->miRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		}
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
