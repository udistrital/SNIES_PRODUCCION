<?
include_once("clase/encriptar.class.php");
$cripto=new encriptar();
$variable="pagina=marcador";
$variable.="&no_pagina=true";
$variable.="&opcion=mostrar";
$variable.="&tipo=sede";
$variable=$cripto->codificar_url($variable,$configuracion);
echo $variable;
?>