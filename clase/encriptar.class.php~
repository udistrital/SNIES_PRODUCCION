<?
/***************************************************************************
*    Copyright (c) 2004 - 2006 :                                           *
*    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        *
*    Comite Institucional de Acreditacion                                  *
*    siae@udistrital.edu.co                                                *
*    Paulo Cesar Coronado                                                  *
*    paulo_cesar@udistrital.edu.co                                         *
*                                                                          *
****************************************************************************
*                                                                          *
*                                                                          *
* SIAE es software libre. Puede redistribuirlo y/o modificarlo bajo los    *
* términos de la Licencia Pública General GNU tal como la publica la       *
* Free Software Foundation en la versión 2 de la Licencia ó, a su elección,*
* cualquier versión posterior.                                             *
*                                                                          *
* SIAE se distribuye con la esperanza de que sea útil, pero SIN NINGUNA    *
* GARANTÍA. Incluso sin garantía implícita de COMERCIALIZACIÓN o ADECUACIÓN*
* PARA UN PROPÓSITO PARTICULAR. Vea la Licencia Pública General GNU para   *
* más detalles.                                                            *
*                                                                          *
* Debería haber recibido una copia de la Licencia pública General GNU junto*
* con SIAE; si esto no ocurrió, escriba a la Free Software Foundation, Inc,*
* 59 Temple Place, Suite 330, Boston, MA 02111-1307, Estados Unidos de     *
* América                                                                  *
*                                                                          *
*                                                                          *
***************************************************************************/
?><?
/****************************************************************************
* @name          encriptar.class.php 
* @author        Paulo Cesar Coronado
* @revision      Última revisión 28 de agosto de 2006
*****************************************************************************
* @subpackage   
* @package	clase
* @copyright    
* @version      0.1
* @author      	Paulo Cesar Coronado
* @link		
* @description  Esta clase esta disennada para cifrar y decifrar las variables que se pasan a las paginas
*		se recomienda que en cada distribucion el administrador del sistema use mecanismos de cifrado.
*		diferentes a los originales
******************************************************************************/
?><?

class encriptar
{
	//Constructor
	function encriptar()
	{
	
	
	
	}
	
	
	
	function codificar_url($cadena,$configuracion)
	{
		$cadena=base64_encode($cadena);
		$cadena=strrev($cadena);
		$cadena=$configuracion["enlace"]."=".$cadena;
		return $cadena;
	
	}
	
	
	function decodificar_url($cadena,$configuracion)
	{
		$cadena=strrev($cadena);
		$cadena=base64_decode($cadena);
		
		parse_str($cadena,$matriz);
		
		foreach($_REQUEST as $clave => $valor) 
		{
			unset($_REQUEST[$clave]);
		} 
		
		foreach($matriz as $clave=>$valor)
		{
			$_REQUEST[$clave]=$valor;			
		}
		
		return TRUE;
	}
	
	function codificar($cadena,$configuracion)
	{
		$cadena=base64_encode($cadena);
		$cadena=strrev($cadena);
		return $cadena;
	
	}
	
	
	function decodificar($cadena)
	{
		$cadena=strrev($cadena);
		$cadena=base64_decode($cadena);
		
		return $cadena;
	
	
	}
	
}//Fin de la clase

?>
