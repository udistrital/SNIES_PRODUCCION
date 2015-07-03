<?php

class FormProcessor {
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miSql;
	var $conexion;
	var $host;
	var $site;
	
	
	function __construct($lenguaje, $sql) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->host = $this->miConfigurador->getVariableConfiguracion ( "host" );
		$this->site = $this->miConfigurador->getVariableConfiguracion ( "site" );
		$this->lenguaje = $lenguaje;
		$this->miSql = $sql;
	}
	function procesarFormulario() {
		$saml_lib_path = '/var/simplesamlphp/lib/_autoload.php';
		
		require_once ($saml_lib_path);
		
		// $aplication_base_url = 'http://10.20.2.68/splocal/';
		$aplication_base_url = $this->host.$this->site.'/';
		$source = 'SP_SNIES'; // Fuente de autenticación definida en el authsources del SP
		
		$as = new SimpleSAML_Auth_Simple ( $source ); // Se pasa como parametro la fuente de autenticación
		
		$login_params = array (
				'ReturnTo' => $aplication_base_url . 'index.php' 
		);
		
		$as->requireAuth ( $login_params );
		
		return false;
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );

$resultado = $miProcesador->procesarFormulario ();

