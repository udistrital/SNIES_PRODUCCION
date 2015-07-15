<?php

namespace snies;
use snies\Componente;
use component\GestorInscritoAdmitido\Sql;

include_once ('component/GestorInscritoAdmitido/Sql.class.php');
include_once ('component/GestorInscritoAdmitido/Interfaz/IGestorMatriculaPrimerCurso.php');
include_once ("core/manager/Configurador.class.php");


class GestorMatriculaPrimerCurso implements IGestorMatriculaPrimerCurso {
	//private $miGestorInscrito;
	var $miConfigurador;
	var $miSql;
	function __construct() {
		$this->miConfigurador = \Configurador::singleton ();
	}
	
 function contarMatriculadosPrimerCurso($periodo) {

 	$this->miSql = new Sql();
 	
 	
 	
 	$this->miConfigurador = \Configurador::singleton ();
 	// configuracion es el nombre de la conexión principal de SARA - se crea de forma automática tomando los
 	// datos de config.inc.php
 	$conexion = "sniesLocal";
 	$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
 	
 	$ano=2015;
 	$cadenaSql = $this->miSql->cadena_sql ( 'contarMatriculadosPrimerCurso', $periodo );
 
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
