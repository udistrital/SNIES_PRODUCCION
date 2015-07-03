<?php

namespace snies;
use snies\Componente;
use component\GestorSniesLocal\Sql;

include_once ('component/GestorSniesLocal/Sql.class.php');
require_once ('component/GestorSniesLocal/Interfaz/IGestorInscrito.php');
include_once ("core/manager/Configurador.class.php");


class GestorInscrito implements IGestorInscrito {
	//private $miGestorInscrito;
	var $miConfigurador;
	var $miSql;
	function __construct() {
		$this->miConfigurador = \Configurador::singleton ();
	}
	
 function contarInscritos($periodo) {

 	$this->miSql = new Sql();
 	
 	
 	
 	$this->miConfigurador = \Configurador::singleton ();
 	// configuracion es el nombre de la conexión principal de SARA - se crea de forma automática tomando los
 	// datos de config.inc.php
 	$conexion = "sniesLocal";
 	$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
 	
 	$ano=2015;
 	$cadenaSql = $this->miSql->cadena_sql ( 'contarInscritos', $periodo );
 
 	$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' ); 	
 	
 	return $resultado[0][0];
 	
 	
    	
    }
    function insertarInscrito() {
    	
    	echo 'este método inserta registros de Inscrito';
    	
    }
    function borrarInscrito() {
    	
    }
    function actualizarInscrito() {
    	
    }
    
}
