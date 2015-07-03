<?
/*
############################################################################
#    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        #
#    Copyright: Vea el archivo EULA.txt que viene con la distribucion      #
############################################################################
*/
/***************************************************************************
  
index.php 

Paulo Cesar Coronado
Copyright (C) 2001-2005

Última revisión 6 de Marzo de 2006

*****************************************************************************
* @subpackage   
* @package	bloques
* @copyright    
* @version      0.2
* @author      	Paulo Cesar Coronado
* @link		N/D
* @description  Menu principal
* @usage        
*****************************************************************************/
if(!isset($GLOBALS["autorizado"]))
{
	include("../index.php");
	exit;		
}


include_once($configuracion["raiz_documento"].$configuracion["clases"]."/encriptar.class.php");

$indice=$configuracion["host"].$configuracion["site"]."/index.php?";
$cripto=new encriptar();
?><table width="100%" align="center" border="0" cellpadding="10" cellspacing="0" >
	<tbody>
		<tr>
			<td >
				<table align="center" border="0" cellpadding="5" cellspacing="0" class="bloquelateral_2" width="100%">
				<?/*
					if(isset($_REQUEST["opcion"]) && $_REQUEST["opcion"]=="lista")
					{
					?><tr class="centralcuerpo">
						<td>
							::..
						</td>
						<td>
						<a href="<?		
							$variable="pagina=registro_recibo";
							$variable.="&accion=1";
							$variable.="&hoja=1";
							$variable.="&mostrar=nuevo";
							$variable.="&xajax=datos_basicos";
							$variable=$cripto->codificar_url($variable,$configuracion);
							echo $indice.$variable;		
							?>"> Agregar Solicitud</a>
							
						</td>
						<td>
						<a href="<?		
							$variable="pagina=registro_recibo_lote";
							$variable.="&accion=1";
							$variable.="&hoja=1";
							$variable.="&mostrar=nuevo";
							$variable=$cripto->codificar_url($variable,$configuracion);
							echo $indice.$variable;		
							?>"> Solicitud en Lote</a>
							
						</td>
						<td>
						<a href="<?		
							$variable="pagina=administar_recibo";
							$variable.="&accion=1";
							$variable.="&hoja=1";
							$variable.="&mostrar=lista";
							$variable=$cripto->codificar_url($variable,$configuracion);
							echo $indice.$variable;		
							?>"> Consolidados</a>
							
						</td>
					</tr><?
					}
					else
					{*/?>
						<tr class="centralcuerpo">
						<td colspan="3">
						<b>::::..</b>  Men&uacute;
						</td>
					</tr>
					
			<?/*			<tr class="bloquelateralcuerpo">
						<td>
						<a href="<?		
							$variable="pagina=administrar_inscritos";
							$variable.="&accion=1";
							$variable.="&hoja=1";
							$variable.="&mostrar=nuevo";
							$variable=$cripto->codificar_url($variable,$configuracion);
							$indice=$configuracion["host"].$configuracion["site"]."/index.php?";
							echo $indice.$variable;		
							?>"> Inscritos</a>
						
							
						</td>
					</tr>
					<tr class="bloquelateralcuerpo">
						<td>
						<a href="<?		
							$variable="pagina=administrar_admitidos";
							$variable.="&accion=1";
							$variable.="&hoja=1";
							$variable.="&mostrar=nuevo";
							$variable=$cripto->codificar_url($variable,$configuracion);
							$indice=$configuracion["host"].$configuracion["site"]."/index.php?";
							echo $indice.$variable;		
							?>"> Admitidos</a>
							
						
							
						</td>
					</tr>
					<tr class="bloquelateralcuerpo">
						<td>
						<a href="<?		
							$variable="pagina=administrar_primiparos";
							$variable.="&accion=1";
							$variable.="&hoja=1";
							$variable.="&mostrar=nuevo";
							$variable=$cripto->codificar_url($variable,$configuracion);
							$indice=$configuracion["host"].$configuracion["site"]."/index.php?";
							echo $indice.$variable;		
							?>"> Prim&iacute;paros</a>
						<hr class="hr_subtitulo">
							
						</td>
					</tr>
					<tr class="bloquelateralcuerpo">
						<td>
						<a href="<?		
							$variable="pagina=administrar_estudiantes";
							$variable.="&accion=1";
							$variable.="&hoja=1";
							$variable.="&mostrar=nuevo";
							$variable=$cripto->codificar_url($variable,$configuracion);
							echo $indice.$variable;		
							?>"> Estudiantes</a>
							<hr class="hr_subtitulo">
						</td>
					</tr>	
					<tr class="bloquelateralcuerpo">
						<td>
						<a href="<?		
							$variable="pagina=administrar_no se sabe";
							$variable.="&accion=1";
							$variable.="&hoja=1";
							$variable.="&mostrar=lista";
							$variable=$cripto->codificar_url($variable,$configuracion);
							echo $indice.$variable;		
							?>"> Evaluaci&oacute;n y Permanencia</a>
							<hr class="hr_subtitulo">
					
						</td>
					</tr>	*/?>
					
					<tr class="bloquelateralcuerpo">
						<td>
						<a href="<?		
							$variable="pagina=consultar_graduado";
							$variable.="&accion=1";
							$variable.="&hoja=1";
							$variable.="&mostrar=lista";
							$variable=$cripto->codificar_url($variable,$configuracion);
							echo $indice.$variable;		
							?>"> Graduados</a>
							<hr class="hr_subtitulo">
						</td>
					</tr>	
<?/*						<tr class="bloquelateralcuerpo">
						<td>
						<a href="<?		
							$variable="pagina=administrar_docentes";
							$variable.="&accion=1";
							$variable.="&hoja=1";
							$variable.="&mostrar=lista";
							$variable=$cripto->codificar_url($variable,$configuracion);
							echo $indice.$variable;		
							?>"> Docentes</a>
							<hr class="hr_subtitulo">
						</td>
					</tr>		
							</tr>	
						<tr class="bloquelateralcuerpo">
						<td>
						<a href="<?		
					$variable="pagina=administrar_administrativos";
							$variable.="&accion=1";
							$variable.="&hoja=1";
							$variable.="&mostrar=nuevo";
							$variable=$cripto->codificar_url($variable,$configuracion);
							$indice=$configuracion["host"].$configuracion["site"]."/index.php?";
							echo $indice.$variable;		
							?>"> Administrativos</a>
							<hr class="hr_subtitulo">
						</td>
					</tr>
*/?>				
					
					
					
					<?
				?></table>
			</td>
		</tr>
	</tbody>
</table>