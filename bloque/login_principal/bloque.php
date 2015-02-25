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
/****************************************************************************************************************
  
login_principal.html.php 

Paulo Cesar Coronado
Copyright (C) 2001-2006

Última revisión 6 de Marzo de 2006

*******************************************************************************************************************
* @subpackage   login_principal
* @package	bloque
* @copyright    
* @version      0.2
* @author      	Paulo Cesar Coronado
* @link		http://gitem.udistrital.edu.co
* 
*
*****************************************************************************************************************/
if(!isset($_REQUEST['action']))
{
	include_once("html.php");	

}else
{
	include_once("action.php");	
}
?>