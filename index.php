<?
/***************************************************************************
*    Copyright (c) 2004 - 2006 :                                           *
*    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        *
*    Paulo Cesar Coronado                                                  *
*    paulo_cesar@udistrital.edu.co                                         *
*                                                                          *
****************************************************************************
*                                                                          *
/*Los caracteres tipograficos especificos del Espannol se han omitido      *
* deliberadamente para mantener la compatibilidad con editores que no      *
* soporten la codificacion                                                 *
/***************************************************************************
* @name          index.php 
* @author        Paulo Cesar Coronado
* @revision      Última revisión 27 de marzo de2008
****************************************************************************
* @subpackage   
* @package	oas.weboffice
* @copyright    
* @version      0.2
* @author      	Paulo Cesar Coronado
* @link		N/D
* @description  Pagina principal del aplicativo
*
****************************************************************************/


require_once("clase/config.class.php");

$esta_configuracion=new config();
$configuracion=$esta_configuracion->variable(); 

if(!isset($configuracion["instalado"]))
{
	echo "<script>location.replace('instalar/index.html')</script>";	
	exit;
}



include_once($configuracion["raiz_documento"].$configuracion["clases"]."/pagina.class.php");	
$la_pagina=new pagina($configuracion);

?>