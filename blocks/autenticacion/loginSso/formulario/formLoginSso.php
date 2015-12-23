<?php

class loginSso {
	
	var $host;
	var $site;
	var $miConfigurador;
	var $urlImagenes;
	var $pagina;
	var $miFormulario;
	var $lenguaje;
	
	function __construct($lenguaje,$formulario){
		$this->miConfigurador = Configurador::singleton ();
		$this->urlImagenes = $this->miConfigurador->getVariableConfiguracion ( "rutaUrlBloque" );
		$this->pagina = $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
		$this->host = $this->miConfigurador->getVariableConfiguracion ( "host" );
		$this->site = $this->miConfigurador->getVariableConfiguracion ( "site" );
		$this->lenguaje = $lenguaje;		
		$this->miFormulario = $formulario;
		
	}
	
	
	function mostrarBotonLogin() {

		//configuración de simplesaml para autenticación SSO (single sign ON)
		
		$saml_lib_path = '/var/simplesamlphp/lib/_autoload.php';
		require_once($saml_lib_path);
		
		$aplication_base_url = $this->host.$this->site;
		$source = 'SP_SNIES';   # Fuente de autenticación definida en el authsources del SP		
		
		$as = new SimpleSAML_Auth_Simple($source);   # Se pasa como parametro la fuente de autenticación		
		//var_dump($as->isAuthenticated());//activar
		//if(!$as->isAuthenticated()) {//activar
		$a=false;//borrar 
		if($a==true) {	//borrar
			
			$this->formulario();
			
		}
		else {			
		
			//$valorCodificado = "action=loginSso";
			$valorCodificado= "&pagina=inicio";
			//$esteBloque=$this->miConfigurador->getVariableConfiguracion ( 'esteBloque' );
			//$valorCodificado .= "&bloque=" . $esteBloque ['nombre'];
			//$valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
			$valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );
			
			//Mostrar enlace
			//Rescatar el parámetro enlace desde los datos de configuraión en la base de datos
			$variable = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
			$miEnlace=$this->host.$this->site.'/index.php?'.$variable.'='.$valorCodificado;
			
			header("Location: ".$miEnlace);
			//var_dump($miEnlace);
			
			$attributes = $as->getAttributes();
		
			if(empty($attributes)) {
				echo 'No se obtuvieron atributos del usuario';
			}
			else {
				echo '<table class="table table-bordered table-striped">';
		
				foreach($attributes as $key => $values) {
					echo '<tr><td>' . $key . '</td><td>';
					echo implode('<br>',$values);
					echo '</td></tr>';
				}
				echo '</table>';
			}
			//echo '<p><a class="btn" href="logout.php">Cerrar sesión</a></p>';
		}
		
		
	}
	
	function formulario(){
		
		$valorCodificado = "action=loginSso";
		$valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
		$esteBloque=$this->miConfigurador->getVariableConfiguracion ( 'esteBloque' );		
		$valorCodificado .= "&bloque=" . $esteBloque ['nombre'];
		$valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
		$valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );
		
		//Mostrar enlace
		//Rescatar el parámetro enlace desde los datos de configuraión en la base de datos
		$variable = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
		$miEnlace=$this->host.$this->site.'/index.php?'.$variable.'=';
        
		$tab=1;
		$atributos['id']='enlaceSp';
		$atributos['enlace']=$miEnlace.$valorCodificado;
		$atributos['tabIndex']=$tab;
		$atributos['estilo']='jqueryui';
		$atributos['enlaceTexto']=$this->lenguaje->getCadena('enlace');		
		echo $this->miFormulario->enlace($atributos);
		
		
		
		
	}
	
	function enlaceLogin() {
		$url = 'index.php?data';
		$variable = "&pagina=permisoCalendario";
		$variable .= "&opcion=crearPermisos"; // va a frontera
		$variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $variable, $url );
		
		return $variable;
	}
}
$miMenu = new loginSso ($this->lenguaje,$this->miFormulario);
$miMenu->mostrarBotonLogin ();
?>



