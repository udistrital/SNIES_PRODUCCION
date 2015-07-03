<?
/*
############################################################################
#    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        #
#    Desarrollo Por:                                                       #
#    Paulo Cesar Coronado 2004 - 2005                                      #
#    paulo_cesar@berosa.com                                                #
#    Copyright: Vea el archivo EULA.txt que viene con la distribucion      #
############################################################################
*/
?><?
/*************************************************************************************************************
  
bloque.php 

Paulo Cesar Coronado
Copyright (C) 2001-2005

Última revisión 6 de Marzo de 2006

***************************************************************************************************************
* @subpackage   registro_seleccionar_modelo
* @package	bloques
* @copyright    
* @version      0.2
* @author      	Paulo Cesar Coronado
* @link		N/D
* @description  Formulario para la seleccion de modelos dentro de la ponderacion
* @usage        
************************************************************************************************************/ 
?><?
if(!isset($GLOBALS["autorizado"]))
{
	include("../index.php");
	exit;		
}
if(!isset($_POST['action']))
{
	include_once("html.php");	

}
else
{
	include_once("action.php");	
}

?>