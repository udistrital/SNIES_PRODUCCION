<?
/*
############################################################################
#    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        #
#    Copyright: Vea el archivo EULA.txt que viene con la distribucion      #
############################################################################
*/
/***************************************************************************
  
html.php 

Paulo Cesar Coronado
Copyright (C) 2001-2007

Última revisión 6 de Marzo de 2007

*****************************************************************************
* @subpackage   
* @package	bloques
* @copyright    
* @version      0.2
* @author      	Paulo Cesar Coronado
* @link		N/D
* @description  Formulario de registro de entidades
* @usage        Toda pagina tiene un id_pagina que es propagado por cualquier metodo GET, POST.
*******************************************************************************/

if(!isset($GLOBALS["autorizado"]))
{
	include("../index.php");
	exit;		
}

include ($configuracion["raiz_documento"].$configuracion["estilo"]."/".$this->estilo."/tema.php");

$estilo=$this->estilo;

$formulario="registro_solicitud_recibo";
$verificar="control_vacio(".$formulario.",'codigo')";

$acceso_db=new dbms($configuracion);
$enlace=$acceso_db->conectar_db();

if (is_resource($enlace))
{
	if(isset($_REQUEST['opcion']))
	{
		$accion=$_REQUEST['opcion'];
		
		if($accion=="mostrar")
		{
			
			if(isset($_REQUEST['registro']))
			{
				mostrar_registro($configuracion,$tema,$_REQUEST['registro'], $acceso_db, $formulario);
			}
		}
		else
		{
			
			if($accion=="nuevo")
			{
				nuevo_registro($configuracion,$tema,$accion,$formulario,$verificar,1,1,$estilo,$acceso_db);
			
			}
			else
			{
				if($accion=="editar")
				{
					editar_registro($configuracion,$tema,$_REQUEST['registro'], $acceso_db, $formulario);
				
				}
				else
				{
					if($accion=="corregir")
					{
						corregir_registro($configuracion,$tema,$accion,$formulario,$verificar,$fila,$tab);
					
					}
				}		
			}
			
		
		}
	}
	else
	{
		$accion="nuevo";
		nuevo_registro($configuracion,$tema,$accion,$formulario,$verificar,1,1,$estilo,$acceso_db);
	}
}
/****************************************************************************************
*				Funciones						*
****************************************************************************************/

function nuevo_registro($configuracion,$tema,$accion,$formulario,$verificar,$fila,$tab,$estilo,$acceso_db)
{

	include_once($configuracion["raiz_documento"].$configuracion["clases"]."/encriptar.class.php");
	$cripto=new encriptar();
	$datos="";
	$contador=0;
?><form enctype='multipart/form-data' method="post" action="index.php" name="<? echo $formulario?>">
<table width="100%" cellpadding="12" cellspacing="0"  align="center">
	<tbody>
		<tr>
			<td align="center" valign="middle">
				<table class='bloquelateral' align='center' width='100%' cellpadding='0' cellspacing='0'>
					<tr>
					<td>
					<table align='center' width='100%' cellpadding='7' cellspacing='1'>
						<tr class="bloquecentralencabezado">
							<td colspan="2" rowspan="1">::.. Solicitud de Recibo de Pago</td>
						</tr>
						<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $contador ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $contador ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $contador++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
							<td bgcolor='<? echo $tema->celda ?>'>
								C&oacute;digo del Estudiante:
							</td>
							<td bgcolor='<? echo $tema->celda ?>'>
								<input id='codigo' type='text' name='codigo' size='20' maxlength='255' tabindex='<? echo $tab++ ?>' onchange="xajax_datos_basicos(document.getElementById('codigo').value)">
							</td>
						</tr>
						<tr class='bloquecentralcuerpo'>
							<td colspan="2" rowspan="1">
							<div id=registro></div>
							</td>
						</tr>						
						<tr class='bloquecentralcuerpo' bgcolor='<? echo $tema->celda ?>' >
							<td colspan="2" rowspan="1">::.. Exenciones</td>
						</tr><?
							$variable="";
							$cadena_sql=cadena_sql_recibo($configuracion,"select_exencion",$variable);
							
							$registro=ejecutar_busqueda_recibo($cadena_sql,$acceso_db);
							//echo $cadena_sql;
							
							if(is_array($registro))
							{
								for($i=0;$i<count($registro);$i++)
								{		
						?><tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $contador ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $contador ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $contador++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
							<td bgcolor='<? echo $tema->celda ?>' colspan="2">
								<input id="exencion_<? echo $registro[$i][0] ?>" type="checkbox" name="exencion_<? echo $registro[$i][0] ?>"><? echo $registro[$i][1]?><br>
							</td>
						</tr><?
								}
							}
						?><tr align="center" class="bloquecentralcuerpo">
							<td colspan="2" align="center">
								<table  width="70%" align="center" border="0">
									<tr>
										<td align="center"><?
										$datos.="&action=registro_solicitud_recibo";						
										$datos=$cripto->codificar($datos,$configuracion);	
										?>	<input type='hidden' name='formulario' value="<? echo $datos ?>">
											<input value="Enviar" name="aceptar" tabindex='<? echo $tab++ ?>' type="button" onclick="if(<? echo $verificar; ?>){document.forms['<? echo $formulario?>'].submit()}else{false}"><br>								
										</td>
										<td align="center">
											<input name='cancelar' value='Cancelar' type='submit'><br>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					</td>
					</tr>
				</table>
			</td>
		</tr>
	</tbody>
</table>
</form>
<?
}


function editar_registro($configuracion,$tema,$id_entidad,$acceso_db,$formulario)
{
	
	$nueva_sesion=new sesiones($configuracion);
	$nueva_sesion->especificar_enlace($enlace);
	$esta_sesion=$nueva_sesion->numero_sesion();
	//Rescatar el valor de la variable usuario de la sesion
	$registro=$nueva_sesion->rescatar_valor_sesion($configuracion,"id_usuario");
	if($registro)
	{
		
		$id_usuario=$registro[0][0];
	}
	
	$variable["id_entidad"]=$id_entidad;
	$cadena_sql=cadena_sql_sede($configuracion,"select",$variable);
	$registro=ejecutar_busqueda_sede($cadena_sql,$acceso_db);	
	if(is_array($registro))
	{		
?><script src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["javascript"]  ?>/funciones.js" type="text/javascript" language="javascript"></script>
<form enctype='multipart/form-data' method='POST' action='index.php' name='<? echo $formulario?>'>
<table class='bloquelateral' align='center' width='100%' cellpadding='0' cellspacing='0'>
	<tr>
		<td>
			<table align='center' width='100%' cellpadding='7' cellspacing='1'>
				<tr class="bloquecentralencabezado">
					<td colspan="2" rowspan="1" align="center">Registro de Especialistas</td>
				</tr>
				<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $contador ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $contador ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $contador++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
					<td bgcolor='<? echo $tema->celda ?>'>
						<b>Registro M&eacute;dico:</b>
					</td>
					<td bgcolor='<? echo $tema->celda ?>'>
						<input type='hidden' name='id_usuario' value='<? echo $registro[0][0] ?>'>
						<input type='text' name='registro' value='<? echo $registro[0][1] ?>' size='40' maxlength='50' tabindex='<? echo $tab++ ?>' >
					</td>
				</tr>
				<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $contador ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $contador ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $contador++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
					<td bgcolor='<? echo $tema->celda ?>'><?
						if($registro[0][8]!="N/D")
						{
					?>	<img width="100" height="120" src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["fotografia"]."/".$registro[0][8] ?>" alt="Especialista" title="Especialista" border="0" />
					<?      }
						else
						{
					?>	<img width="100" height="120" src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["fotografia"]."/sin_imagen.jpg" ?>" alt="Especialista" title="Especialista" border="0" />
					<?	}
				?>	</td>
					<td bgcolor='<? echo $tema->celda ?>'>
						<b>Cambiar Fotograf&iacute;a:</b><br>
						<input type='hidden' name='imagen_anterior' value='<? echo $registro[0][8] ?>'>
						<input type='file' name='imagen' tabindex='<? echo $tab++ ?>' >
					</td>
				</tr>
				<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $contador ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $contador ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $contador++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
					<td bgcolor='<? echo $tema->celda ?>'>
						<b>Experiencia Profesional:</b>
					</td>
					<td bgcolor='<? echo $tema->celda ?>'>
						<textarea name='experiencia' cols='40' rows='2' tabindex='<? echo $tab++ ?>' ><? echo $registro[0][2] ?></textarea>
					</td>
				</tr>
				<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $contador ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $contador ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $contador++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
					<td bgcolor='<? echo $tema->celda ?>'>
						<b>Experiencia Asistencial:</b>
					</td>
					<td bgcolor='<? echo $tema->celda ?>'>
						<textarea name='asistencial' cols='40' rows='2' tabindex='<? echo $tab++ ?>' ><? echo $registro[0][3] ?></textarea>
					</td>
				</tr>
				<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $contador ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $contador ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $contador++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
					<td bgcolor='<? echo $tema->celda ?>'>
						<b>Experiencia Administrativo:</b>
					</td>
					<td bgcolor='<? echo $tema->celda ?>'>
						<textarea name='administrativo' cols='40' rows='2' tabindex='<? echo $tab++ ?>' ><? echo $registro[0][4] ?></textarea>
					</td>
				</tr>
				<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $contador ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $contador ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $contador++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
					<td bgcolor='<? echo $tema->celda ?>'>
						<b>Experiencia Docente:</b>
					</td>
					<td bgcolor='<? echo $tema->celda ?>'>
						<textarea name='docente' cols='40' rows='2' tabindex='<? echo $tab++ ?>' ><? echo $registro[0][5] ?></textarea>
					</td>
				</tr>
				<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $contador ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $contador ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $contador++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
					<td bgcolor='<? echo $tema->celda ?>'>
						<b>Experiencia Investigativa:</b>
					</td>
					<td bgcolor='<? echo $tema->celda ?>'>
						<textarea name='investigativo' cols='40' rows='2' tabindex='<? echo $tab++ ?>' ><? echo $registro[0][6] ?></textarea>
					</td>
				</tr>
				<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $contador ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $contador ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $contador++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
					<td bgcolor='<? echo $tema->celda ?>'>
						<b>Habilidades:</b>
					</td>
					<td bgcolor='<? echo $tema->celda ?>'>
						<textarea name='habilidades' cols='40' rows='2' tabindex='<? echo $tab++ ?>' ><? echo $registro[0][7] ?></textarea>
					</td>
				</tr>
				<tr align='center'>
					<td colspan='2'>
						<table align='center' width='50%'>
							<tr align='center'>
								<td>
									<input type='hidden' name='action' value='<? echo $formulario?>'>
									<input name='aceptar' value='Aceptar' type='submit'>
								</td>
								<td>
									<input name='cancelar' value='Cancelar' type='submit'>
								</td>
							</tr>
						</table>	
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</form><?
		
	}	
}


function mostrar_registro($configuracion,$tema,$id_entidad, $acceso_db, $formulario)
{
	
	
	$variable["id_entidad"]=$id_entidad;
	$cadena_sql=cadena_sql_sede($configuracion,"select",$variable);
	$registro=ejecutar_busqueda_sede($cadena_sql,$acceso_db);
	
	if(is_array($registro))
	{			
?><table class='bloquelateral' align='center' width='100%' cellpadding='0' cellspacing='0'>
	<tr>
		<td>
			<table class='bloquecentralcuerpo' align='center' width='100%' cellpadding='7' cellspacing='1'>
				<tr>
					<td align="center" colspan="2">
					<br>
					</td>							
				</tr>
				<tr>
					<td class="encabezado_registro">
						<h3><? echo strtoupper($registro[0][4]) ?></h3>
					</td>	
				</tr>
				<tr class='texto_subtitulo'>
					<td colspan="2">
						Datos B&aacute;sicos<hr class="hr_subtitulo" />
					</td>
				</tr>				
				<tr class='bloquecentralcuerpo'>
					<td>
						<table class='tabla_basico'>
							<tr class='bloquecentralcuerpo' >
								<td  class="texto_negrita">
									Nombre Corto
								</td>
								<td>
									<? echo $registro[0][4] ?>
								</td>
							</tr>
							<tr>
								<td  class="texto_negrita">
									Direcci&oacute;n
								</td>
								<td>
									<? echo $registro[0][9] ?>
								</td>
							</tr>
							<tr>
								<td  class="texto_negrita">
									Tel&eacute;fono
								</td>
								<td>
									<? echo $registro[0][10] ?>
								</td>
							</tr>
							<tr>
								<td  class="texto_negrita">
									Correo Electr&oacute;nico
								</td>
								<td>
									<? echo $registro[0][12] ?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class='texto_subtitulo'>
					<td>
						Ubicaci&oacute;n<hr class="hr_subtitulo" />
					</td>
				</tr>
				<tr class='bloquecentralcuerpo'>
					<td colspan="2" align="center">
						<div id="map" style="width: 100%; height: 300px"></div>
					</td>
				</tr>
				<tr class='texto_subtitulo'>
					<td>
						Descripci&oacute;n<hr class="hr_subtitulo" />
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<? echo $registro[0][15] ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<form enctype='multipart/form-data' method='POST' action='index.php' name='<? echo $formulario ?>'>
	<input type='hidden' name='marcador' id='marcador' value='<? 
	include_once("clase/encriptar.class.php");
	$cripto=new encriptar();
	$indice="index.php?";
	$variable="pagina=marcador";
	$variable.="&no_pagina=true";
	$variable.="&opcion=mostrar";
	$variable.="&tipo=sede";
	$variable.="&registro=".$id_entidad;
	$variable=$cripto->codificar_url($variable,$configuracion);
	echo $indice.$variable; ?>'>
</form>
<?		}
		else
		{
			echo "Existe una incompatibilidad en el registro. Por favor consulte con el administrador del sistema.";
		}
	
}

function cadena_sql_recibo($configuracion,$tipo,$variable="")
{
	
	switch($tipo)
	{
		case "select_exencion":
			$cadena_sql="SELECT ";
			$cadena_sql.="`id_exencion`, ";
			$cadena_sql.="`nombre`, ";
			$cadena_sql.="`porcentaje` ";
			$cadena_sql.="FROM ";
			$cadena_sql.=$configuracion["prefijo"]."exencion ";
			break;
			 
		case "select":
			$cadena_sql="SELECT ";
			$cadena_sql.="`id_entidad`, ";
			$cadena_sql.="`id_padre`, ";
			$cadena_sql.="`id_usuario`, ";
			$cadena_sql.="`fecha`, ";
			$cadena_sql.="`nombre`, ";
			$cadena_sql.="`etiqueta`, ";
			$cadena_sql.="`logosimbolo`, ";
			$cadena_sql.="`nit`, ";
			$cadena_sql.="`fundacion`, ";
			$cadena_sql.="`direccion`, ";
			$cadena_sql.="`telefono`, ";
			$cadena_sql.="`web`, ";
			$cadena_sql.="`correo`, ";
			$cadena_sql.="`mision`, ";
			$cadena_sql.="`vision`, ";
			$cadena_sql.="`descripcion`, ";
			$cadena_sql.="`comentario`, ";
			$cadena_sql.="`tipo`, ";
			$cadena_sql.="`latitud`, ";
			$cadena_sql.="`longitud` ";
			$cadena_sql.="FROM ";
			$cadena_sql.=$configuracion["prefijo"]."entidad "; 			
			$cadena_sql.="WHERE ";
			
			foreach ($variable as $key => $value) 
			{
				$cadena_sql.=$key."=".$value." ";
				$cadena_sql.="AND ";
			}
			$cadena_sql=substr($cadena_sql,0,(strlen($cadena_sql)-4));
		
		
		default:
			break;
	}

	return $cadena_sql;

}

function ejecutar_busqueda_recibo($cadena_sql,$acceso_db)
{
	$acceso_db->registro_db($cadena_sql,0);
	$registro=$acceso_db->obtener_registro_db();
	return $registro;
}



?>