<?php

namespace bloquesModelo\bloqueModelo2\formulario;

if (! isset ( $GLOBALS ["autorizado"] )) {
    include ("../index.php");
    exit ();
}

class Formulario {
    
    var $miConfigurador;
    var $lenguaje;
    var $miFormulario;
    
    function __construct($lenguaje, $formulario) {
        
        $this->miConfigurador = \Configurador::singleton ();
        
        $this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
        
        $this->urlImagenes = $this->miConfigurador->getVariableConfiguracion ( "rutaUrlBloque" ).'imagenes/';
        
        $this->lenguaje = $lenguaje;
        
        $this->miFormulario = $formulario;
    
    }
    
    function formulario() {
        
        ?>
        <div align="center">       
        <img src="<?php echo $this->urlImagenes."porvenir_ud.jpg";?>" style="width:525px;height:263px;">
        </div>
        <div align="center">SISTEMA DE INTEGRACIÓN SECTORIAL</b></div>
         <div align="center">Universidad Distriral</div>
         <div align="center">Francisco José de Caldas</div>
         
        <?
    
    }
    
    function mensaje() {
        
        // Si existe algun tipo de error en el login aparece el siguiente mensaje
        $mensaje = $this->miConfigurador->getVariableConfiguracion ( 'mostrarMensaje' );
        $this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', null );
        
        if ($mensaje) {
            
            $tipoMensaje = $this->miConfigurador->getVariableConfiguracion ( 'tipoMensaje' );
            
            if ($tipoMensaje == 'json') {
                
                $atributos ['mensaje'] = $mensaje;
                $atributos ['json'] = true;
            } else {
                $atributos ['mensaje'] = $this->lenguaje->getCadena ( $mensaje );
            }
            // -------------Control texto-----------------------
            $esteCampo = 'divMensaje';
            $atributos ['id'] = $esteCampo;
            $atributos ["tamanno"] = '';
            $atributos ["estilo"] = 'information';
            $atributos ['efecto'] = 'desvanecer';
            $atributos ["etiqueta"] = '';
            $atributos ["columnas"] = ''; // El control ocupa 47% del tamaño del formulario
            echo $this->miFormulario->campoMensaje ( $atributos );
            unset ( $atributos );
        
        }
        
        return true;
    
    }

}

$miFormulario = new Formulario ( $this->lenguaje, $this->miFormulario );

$miFormulario->formulario ();
$miFormulario->mensaje ();

?>