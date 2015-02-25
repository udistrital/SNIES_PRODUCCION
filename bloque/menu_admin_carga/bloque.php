<?
/*
############################################################################
#    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        #
#    Desarrollo Por:                        #
#    Paulo Cesar Coronado 2004 - 2005                                      #
#    paulo_cesar@berosa.com                                                   #
#    Copyright: Vea el archivo EULA.txt que viene con la distribucion      #
############################################################################
*/
?>
<?
/****************************************************************************************************************
  
index.php 

Paulo Cesar Coronado
Copyright (C) 2001-2005

Última revisión 6 de Marzo de 2006

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

$indice=$configuracion["host"].$configuracion["site"]."/index.php?";
$cripto=new encriptar();

$conexion=new dbConexion($configuracion);

$acceso_db=$conexion->recursodb($configuracion,"oracle2");
$enlace=$acceso_db->conectar_db();

$accesoSnies=$conexion->recursodb($configuracion,"sniesLocal");
$enlaceBlade1=$accesoSnies->conectar_db();

/*if($enlace)
{
	$nueva_sesion=new sesiones($configuracion);
	$nueva_sesion->especificar_enlace($enlace);
	$esta_sesion=$nueva_sesion->numero_sesion();
	//Rescatar el valor de la variable usuario de la sesion
	$registro=$nueva_sesion->rescatar_valor_sesion($configuracion,"usuario");
	if($registro)
	{
		
		$el_usuario=$registro[0][0];
	}
}


*/?>


<?/*<TABLE WIDTH=100% BORDER=0 CELLPADDING=5 CELLSPACING=0 class="bloquelateral">
  <thead style="font-family: Helvetica,Arial,sans-serif;"> <tr>
	<TD WIDTH=100% class="bloquelateralencabezado">
	Crear Pregunta
	</TD>
  </tr>
  </thead>
  <tbody>
		<TR>
			<TD WIDTH=100% class="bloquelateralcuerpo">
						<a href="<?		
							$variable="pagina=administrar_inscritos";
							$variable.="&accion=1";
							$variable.="&hoja=1";
							$variable.="&mostrar=nuevo";
							$variable=$cripto->codificar_url($variable,$configuracion);
							$indice=$configuracion["host"].$configuracion["site"]."/index.php?";
							echo $indice.$variable;		
							?>"> Inscritos</a>
			</TD>
		</TR>
		<TR>
			<td class="bloquelateralcuerpo">
						<a href="<?		
							$variable="pagina=administrar_inscritos";
							$variable.="&accion=1";
							$variable.="&hoja=1";
							$variable.="&mostrar=nuevo";
							$variable=$cripto->codificar_url($variable,$configuracion);
							$indice=$configuracion["host"].$configuracion["site"]."/index.php?";
							echo $indice.$variable;		
							?>"> Inscritos</a>
			</TD>
		</TR>
		<TR>
			<td class="bloquelateralcuerpo">
			<BR>
			</TD>
		</TR>
		</tbody>	
	</TABLE><br>
	*/?>
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
				..:: Actualizar Inscritos
			</TD>
		</TR>
		<TR class="bloquelateralcuerpo">
			<td>
				Registrados desde la fecha:
			</TD>
		</TR>
		<TR>
			<td>
			<table cellpading="2">
			<tr>
									
									<td class="bloquelateralcuerpo">Semestre <?
									$contador=0;
									echo "<select class='bloquecentralcuerpo2' name='mes_1' size='1'>\n";
									echo "<option value='0'> </option>\n";
									for($semestre=1;$semestre<3;$semestre++)
									{	
										echo "<option value='".$semestre."'>".$semestre."</option>\n";
										
									}		
									echo "</select>\n";
						
									?>
									</td>
									
									<td class="bloquelateralcuerpo">A&ntilde;o <?
									echo "<select class='bloquecentralcuerpo2' name='anno_1' size='1'>\n";
									echo "<option value='0'> </option>\n";
									for($anno=2001;$anno<date("Y")+1;$anno++)
									{	
										echo "<option selected value='".$anno."'>".$anno."</option>\n";
										
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
										
									</TD>
					</TR>
		</form>
	</tbody>	
	</TABLE>
	</td>
	</tr>
	</table>
