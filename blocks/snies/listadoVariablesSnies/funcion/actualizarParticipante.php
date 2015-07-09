<?php

namespace bloquesModelo\bloqueModelo2\funcion;

class FormProcessor {
    
    var $miConfigurador;
    var $lenguaje;
    var $miFormulario;
    var $miSql;
    var $conexion;
    
    function __construct($lenguaje, $sql) {
        
        $this->miConfigurador = \Configurador::singleton ();
        $this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
        $this->lenguaje = $lenguaje;
        $this->miSql = $sql;
       
    }
    
    function procesarFormulario() {    

    	echo 'En este método se deben actualizar e insertar los registros de participantes';
    	exit;
    	
        /**
         * @todo lógica de procesamiento
         */
        return false;
    }
    
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );

$resultado= $miProcesador->procesarFormulario ();

