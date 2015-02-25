<?
/*
############################################################################
#    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        #
#    Copyright: Vea el archivo LICENCIA.txt que viene con la distribucion  #
############################################################################
*/
/****************************************************************************
  
estilo.php 

Paulo Cesar Coronado
Copyright (C) 2001-2007

Última revisión 6 de junio de 2007

******************************************************************************
* @subpackage   
* @package	bloques
* @copyright    
* @version      0.2
* @author      	Paulo Cesar Coronado
* @link		N/D
* @description  Definicion de estilos - es una pagina CSS
* @usage        
*****************************************************************************/


   include_once("../../clase/config.class.php");
   include_once("tema.php");

   $esta_configuracion=new config();
   $configuracion=$esta_configuracion->variable("../../"); 

    if (!isset($mi_tema)) 
    {
        $mi_tema = "basico";
	
    }

?>


body, td, th, li 
{
    font-family: "Arial", Verdana, Trebuchet MS, Helvetica, sans-serif;
}

th 
{
    font-weight: bold;
    background-image: url(<?PHP echo $configuracion['host'].$configuracion['site'].$configuracion['estilo'].'/'.$mi_tema ?>/gradient.jpg);
}

a:link 
{
    text-decoration: none;
    color: <? echo $tema->enlace ?>;
}

a:visited 
{
    text-decoration: none;
    color: <? echo $tema->enlace ?>;
}

a:hover 
{
    text-decoration: underline;
    color: <? echo $tema->sobre ?>;
}

a.enlace:link 
{
    text-decoration: none;
    color: #FFFFFF;
}

a.enlace:visited {
    text-decoration: none;
    color: #FFFFFF;
}

a.enlace:hover {
    text-decoration: underline;
    color: #FFFFFF;
    font-weight: bold;	
}

a.wiki:link 
{
    text-decoration: none;
    color: #0000FF;    
}

a.wiki:visited {
    text-decoration: none;
    color: #0000FF;
}

a.wiki:hover {
    text-decoration: underline;
    color: #FF0000;
    
}

hr.hr_subtitulo
{
	border: 0;
	color: #000000;
	background-color: #999999;
	height: 1px;
	width: 100%;
	text-align: left;
}

.fondoprincipal {
    background-color: <?PHP echo $tema->fondo?>;
}

.tabla_general {
    background-color: <?PHP echo $tema->cuerpotabla ?>;
    border-width: 1px;
    border-color: <?PHP echo $tema->bordes?>;
    border-style: solid;

}

form {
    margin-bottom: 0;
}


.highlight {
    background-color: <?PHP echo $tema->highlight?>;
}

.bloquelateral {
    border-width: 1px;
    border-color: <?PHP echo $tema->bordes?>;
    border-style: solid;
    -moz-border-radius-bottomleft: 10px;
    -moz-border-radius-bottomright: 10px;
    background-color: <?PHP echo $tema->cuerpotabla?>;
}

.bloquelateral_2 {
    border-width: 1px;
    border-color: <?PHP echo $tema->bordes?>;
    border-style: solid;
    background-color: <?PHP echo $tema->celda_clara?>;
}


.seccion_B {
    background-color: <?PHP echo $tema->fondo_B ?>;
}


td.seccion_B
{
	width:<?PHP echo $configuracion["tamanno_gui"]*(0.2) ?>;
	border: 0px;
	border-collapse: collapse;
	border-spacing: 0px;
}

td.seccion_C
{
	width:<?PHP echo $configuracion["tamanno_gui"]*(0.6) ?>;
	border: 0px;
	border-collapse: collapse;
	border-spacing: 0px;
}

.seccion_D {
    background-color: <?PHP echo $tema->fondo_B ?>;
}


td.seccion_D
{
	width:<?PHP echo $configuracion["tamanno_gui"]*(0.2) ?>;
	border: 0px;
	border-collapse: collapse;
	border-spacing: 0px;
}

td.seccion_C_colapsada
{
	width:<?PHP echo $configuracion["tamanno_gui"]*(0.8) ?>;
	border: 0px;
	border-collapse: collapse;
	border-spacing: 0px;
}


.login_celda1 
{
    background-color: #f4f5eb;
}

.cuadro_color
{
    background-color: #f4f5eb;
}

.cuadro_login {
    border-width: 1px;
    font-size: 12;
    font-family: "Arial", Verdana, Trebuchet MS, Helvetica, sans-serif;
    text-align: left;
}

.cuadro_plano {
    border-width: 1px;
    border:1px solid #AAAAAA;
    font-size: 11;
    font-family: "Arial", Verdana, Trebuchet MS, Helvetica, sans-serif;
    text-align: left;
}


.cuadro_simple {
    border-width: 0px;
    font-size: 12;
    font-family: "Arial", Verdana, Trebuchet MS, Helvetica, sans-serif;
    text-align: left;
    background-color: <?PHP echo $tema->celda?>;
}

.cuadro_corregir {
    font-family: "Arial", Verdana, Trebuchet MS, Helvetica, sans-serif;
    text-align: left;
    font-weight: bold;	
    color: #FF0000;
}

<? /*===================Estilos de Texto ===================================*/
/**************Encabezado cuando se muestra un registro*********************/?>
.encabezado_registro 
{
    border-width: 0px;
    font-size: 16;
    font-family: "Arial", Verdana, Trebuchet MS, Helvetica, sans-serif;
    text-align: left;
    font-weight: bold;	
}
<?/***************************************************************************/?>

.texto_negrita {
    font-weight: bold;	
}


.texto_subtitulo 
{
    border-width: 0px;
    font-size: 14;
    font-family: "Arial", Verdana, Trebuchet MS, Helvetica, sans-serif;
    text-align: left;    
    color:  <?PHP echo $tema->subtitulo?>;
}

.texto_elegante 
{
    border-width: 0px;
    font-size: 14;
    font-family: "Arial", Verdana, Trebuchet MS, Helvetica, sans-serif;
    text-align: left;    
    color:  <?PHP echo $tema->subtitulo?>;
}

.texto_azul
{
    color:#0000FF;
}

.texto_gris
{
    color:#555555;
}

.texto_titulo 
{
    border-width: 0px;
    font-size: 18;
    font-family: "Arial", Verdana, Trebuchet MS, Helvetica, sans-serif;
    text-align: left;
    font-weight: bold;
    color:  <?PHP echo $tema->titulo?>;
}

<? /*===================Estilos de Tablas ===================================*/?>

table.contenidotabla 
{
	font-size: 10;
	font-family: "Arial", Verdana, Trebuchet MS, Helvetica, sans-serif;
	text-align: justify;
	width:100%;
	border-collapse: collapse;
	border-spacing: 0px;	
    }
    
table.contenidotabla td
{
	padding:3px;
}

.tabla_basico {
	background-color:#F9F9F9;
	border:1px solid #AAAAAA;
	font-size:95%;
	padding:5px;
	width:90%;
	margin-left:10%; 
	margin-right:10%;
}

.tabla_organizacion
{
	border:0px;
	padding:10px;
	width:100%;	
}

.tabla_alerta {
	background-color:#fdffe5;
	border:1px solid #AAAAAA;
	font-size:95%;
	padding:5px;
	width:90%;
	margin-left:10%; 
	margin-right:10%;
}

.tabla_simple 
{
	background-color:#FFFFF5;
	border:1px solid #CCCCCC;
	font-size:11px;
	width:100%;
	text-align: center;
}



.paginacentral {
    border-width: 1px;
    border-color: <?PHP echo $tema->bordes?>;
    border-style: solid;
    -moz-border-radius-bottomleft: 10px;
    -moz-border-radius-bottomright: 10px;
    -moz-border-radius-topleft: 10px;
    -moz-border-radius-topright: 10px;
    background-color: <?PHP echo $tema->cuerpotabla?>;
}

.bloquelateralencabezado {
    font-size: 13;
    font-family: "Arial", Verdana, Trebuchet MS, Helvetica, sans-serif;
    font-weight: bold;	
    background-color: <?PHP echo $tema->encabezado?>;
    background-image: url(<?PHP echo $configuracion["host"].$configuracion["site"].$configuracion["estilo"].'/'.$mi_tema ?>/gradient.jpg)
}

.bloquelateralcuerpo {
    font-size: 11;
    font-family: "Arial", Verdana, Trebuchet MS, Helvetica, sans-serif;
    }
    
.bloquecentralencabezado {
    font-size: 13;
    font-family: "Arial", Verdana, Trebuchet MS, Helvetica, sans-serif;
    font-weight: bold;	
    background-color: <?PHP echo $tema->encabezado?>;
    background-image: url(<?PHP echo $configuracion['host'].$configuracion['site'].$configuracion['estilo'].'/'.$mi_tema ?>/gradient.jpg)
}

.bloquecentralcuerpo {
    font-size: 11;
    font-family: "Arial", Verdana, Trebuchet MS, Helvetica, sans-serif;
    text-align: justify;
    }

.bloquecentralcuerpo2 {
    font-size: 10;
    font-family: "Arial", Verdana, Trebuchet MS, Helvetica, sans-serif;
    text-align: justify;
    }

    
.bloquelateralayuda {
    font-size: 10;
    font-family: "Arial", Verdana, Trebuchet MS, Helvetica, sans-serif;
    text-decoration: italic;
    }    
    
.centralcuerpo {
    font-size: 12;
    font-family: "Arial", Verdana, Trebuchet MS, Helvetica, sans-serif;
    color: #0F0F0F;
    background-color: <?PHP echo $tema->celda_clara?>;
    background-image: url(<?PHP echo $configuracion['host'].$configuracion['site'].$configuracion['estilo'].'/'.$mi_tema ?>/gradient.jpg)
}    
.centralencabezado {
    font-size: 12;
    font-family: "Arial", Verdana, Trebuchet MS, Helvetica, sans-serif;
    text-align: center;
    background-color: <?PHP echo $tema->encabezado?>;
    background-image: url(<?PHP echo $configuracion['host'].$configuracion['site'].$configuracion['estilo'].'/'.$mi_tema ?>/gradient.jpg)
}


.centrar {
    text-align: center;
}