<?
/*
############################################################################
#    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        #
#    Desarrollo Por:                        #
#    Paulo Cesar Coronado 2004 - 2008                                      #
#    paulo_cesar@berosa.com                                                   #
#    Copyright: Vea el archivo EULA.txt que viene con la distribucion      #
############################################################################
*/
?>
<?
/****************************************************************************************************************
  
index.php 

Paulo Cesar Coronado
Luis Fernando Torres
Copyright (C) 2001-2005

Última revisión 13 de Agosto de 2008

*******************************************************************************************************************
* @subpackage   
* @package	bloques
* @copyright    
* @version      0.2
* @author      	Paulo Cesar Coronado
* @link		N/D
* @description  Menu principal
* @usage        
*****************************************************************************************************************/
if(!isset($GLOBALS["autorizado"]))
{
	include("../index.php");
	exit;		
}

include ($configuracion["raiz_documento"].$configuracion["estilo"]."/".$this->estilo."/tema.php");
//Se incluye para manejar los mensajes de error
include_once($configuracion["raiz_documento"].$configuracion["clases"]."/alerta.class.php");
include_once($configuracion["raiz_documento"].$configuracion["clases"]."/navegacion.class.php");
include_once($configuracion["raiz_documento"].$configuracion["clases"]."/encriptar.class.php");
global $variable;
$indice=$configuracion["host"].$configuracion["site"]."/index.php?";
$cripto=new encriptar();

?>
<table width="100%" align="center" border="0" cellpadding="10" cellspacing="0" >
	<tbody>
		<tr>
			<td >

<table align="center" border="0" cellpadding="5" cellspacing="0" class="bloquelateral_2" width="100%">
   <tbody>		
		<form action="index.php" method="GET">
		<TR class="centralcuerpo">
			<TD WIDTH=100%>
				<INPUT type="hidden" NAME="page" VALUE="<? echo $indice.$variable ?>">
				<input type="hidden" name= "accion" value="2">
				<input type="hidden" name= "hoja" value="0">
				..:: Actualizar Matriculado
			</TD>
		</TR>
		<TR class="bloquelateralcuerpo">
			<td>
				
			</TD>
		</TR>
		<TR>
			<td>
			<table cellpading="2">
			<tr>
									
									<td class="bloquelateralcuerpo">Per&iacute;odo <?
									//$semestre=date("m");
									//echo $semestre."=este es el semestre.";
									$contador=0;
									echo "<select class='bloquecentralcuerpo2' name='periodo' size='1'>\n";
										if (date("m")<7)
										{
											echo "<option selected value='1'>1</option>\n";
											//echo "<option value='2'>2</option>\n";
											echo "<option value='3'>3</option>\n";
										}
										else
										{
											echo "<option value='1'>1</option>\n";
											//echo "<option selected value='2'>2</option>\n";
											echo "<option selected value='3'>3</option>\n";
										}

									echo "</select>\n";
						
									?>
									</td>
									
									<td class="bloquelateralcuerpo">A&ntilde;o <?
									echo "<select class='bloquecentralcuerpo2' name='annio' size='1'>\n";
									echo "<option value='0'> </option>\n";
									for($annio=2001;$annio<date("Y")+1;$annio++)
									{	
										if ($annio==date("Y"))
										{
										echo "<option selected value='".$annio."'>".$annio."</option>\n";
										}
										else
										{
										echo "<option value='".$annio."'>".$annio."</option>\n";
										}
									}
									echo "</select>\n";
						
									?>
									</td>
									
			</tr>
			
			
			</table>
		</TR>
					<TR>
									<TD WIDTH=100% ALIGN=CENTER>
										
										<INPUT TYPE=SUBMIT NAME="aceptar" VALUE="actualizar">
										<?
										$datos="&pagina=administrar_matriculado";     						//nombre del bloque						
										$datos=$cripto->codificar($datos,$configuracion);	
										?>	<input type='hidden' name='redireccion' value="<? echo $datos ?>">
									</TD>
					</TR>
					<?/*
					<TR class="bloquelateralcuerpo">
									<TD WIDTH=100%>
											Para cargar los datos del segundo semestre al sistema SNIES, se deben actualizar los per&iacute;odos 2 y 3.
									</TD>
					</TR>
					*/?>
					
		</form>
	</tbody>	
	</TABLE>
	</td>
	</tr>
	</table>
