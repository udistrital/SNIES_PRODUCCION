<?
/*
############################################################################
#    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        #
#    Desarrollo Por:                                                       #
#    Paulo Cesar Coronado 2004 - 2006                                      #
#    Copyright: Vea el archivo EULA.txt que viene con la distribucion      #
############################################################################
*/
/***************************************************************************
  
html.php 

Paulo Cesar Coronado
Copyright (C) 2001-2005

Última revisión 1 de Noviembre de 2006

****************************************************************************
* @subpackage   
* @package	bloques
* @copyright    
* @version      0.2
* @author      	Paulo Cesar Coronado
* @link		N/D
* @description  Formulario de registro de usuarios
* @usage        Toda pagina tiene un id_pagina que es propagado por cualquier 
*               metodo. 
*****************************************************************************/
?><?

if(!isset($GLOBALS["autorizado"]))
{
	include("../index.php");
	exit;		
}


include ($configuracion["raiz_documento"].$configuracion["estilo"]."/".$this->estilo."/tema.php");
//Variables

$formulario="registro_usuario";
$verificar="control_vacio(".$formulario.",'nombre')";
$verificar.="&& control_vacio(".$formulario.",'apellido')";
$verificar.="&& control_vacio(".$formulario.",'correo')";
$verificar.="&& control_vacio(".$formulario.",'usuario')";
$verificar.="&& control_vacio(".$formulario.",'clave')";
$verificar.="&& comparar_contenido(".$formulario.",'clave','clave_2')";
$verificar.="&& longitud_cadena(".$formulario.",'nombre',3)";
$verificar.="&& longitud_cadena(".$formulario.",'apellido',3)";
$verificar.="&& longitud_cadena(".$formulario.",'clave',5)";
$verificar.="&& longitud_cadena(".$formulario.",'usuario',4)";
$verificar.="&& verificar_correo(".$formulario.",'correo')";

if(isset($_REQUEST['opcion']))
{
	$accion=$_REQUEST['opcion'];
	
	if($accion=="mostrar")
	{
		mostrar_registro($configuracion,$tema,$accion);
	}
	else
	{
		
		if($accion=="nuevo")
		{
			nuevo_registro($configuracion,$tema,$accion,$formulario,$verificar,$fila,$tab);
		
		}
		else
		{
			if($accion=="editar")
			{
				editar_registro($configuracion,$tema,$accion,$formulario,$verificar,$fila,$tab);
			
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
	nuevo_registro($configuracion,$tema,$accion,$formulario,$verificar,$fila,$tab);
}	

/****************************************************************************************
*				Funciones						*
****************************************************************************************/


function nuevo_registro($configuracion,$tema,$accion,$formulario,$verificar,$fila,$tab)
{
	
?><script src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["javascript"]  ?>/funciones.js" type="text/javascript" language="javascript"></script>
<form method="post" action="index.php" name="<? echo $formulario?>">
<table width="100%" cellpadding="12" cellspacing="0"  align="center">
	<tbody>
		<tr>
			<td align="center" valign="middle">
				<table style="width: 100%; text-align: left;" border="0" cellpadding="5" cellspacing="1" class=bloquelateral>
					<tbody>
						<tr class="bloquecentralencabezado">
							<td colspan="2" rowspan="1">Registro para usuarios nuevos:</td>
						</tr>
						<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $fila ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $fila ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $fila++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
							<td bgcolor="<? echo $tema->celda ?>">
								<font color="red">*</font>Nombres:<br>
							</td>
							<td bgcolor="<? echo $tema->celda ?>">
								<input maxlength="80" size="40" tabindex="<? echo $tab++ ?>" name="nombre"><br>
							</td>
						</tr>
						<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $fila ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $fila ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $fila++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
							<td bgcolor="<? echo $tema->celda ?>">
								<font color="red">*</font>Apellidos:<br>
							</td>
							<td bgcolor="<? echo $tema->celda ?>">
								<input maxlength="80" size="40" tabindex="<? echo $tab++ ?>" name="apellido">
							</td>
						</tr>
						<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $fila ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $fila ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $fila++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
							<td bgcolor="<? echo $tema->celda ?>">
								<font color="red">*</font>Correo Electr&oacute;nico:<br>
							</td>
							<td bgcolor="<? echo $tema->celda ?>">
								<input maxlength="80" size="40" tabindex="<? echo $tab++ ?>" name="correo">
							</td>
						</tr>
						<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $fila ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $fila ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $fila++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
							<td bgcolor="<? echo $tema->celda ?>">
								Tel&eacute;fono:<br>
							</td>
							<td bgcolor="<? echo $tema->celda ?>">
								<input maxlength="50" size="30" tabindex="<? echo $tab++ ?>" name="telefono">
							</td>
						</tr>
						<tr>
							<td class="bloquecentralencabezado" colspan="2" rowspan="1">
								Datos para la autenticaci&oacute;n:
							</td>
						</tr>
						<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $fila ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $fila ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $fila++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
							<td bgcolor="<? echo $tema->celda ?>">
								<font color="red">*</font>Usuario:<br>
							</td>
							<td bgcolor="<? echo $tema->celda ?>">
								<input maxlength="50" size="30" tabindex="<? echo $tab++; ?>" name="usuario">
							</td>
						</tr>
						<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $fila ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $fila ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $fila++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
							<td bgcolor="<? echo $tema->celda ?>">
								<font color="red">*</font>Clave:
							</td>
							<td bgcolor="<? echo $tema->celda ?>">
								<input maxlength="50" size="30" tabindex="<? echo $tab++; ?>" name="clave"  type="password">
							</td>
						</tr>
						<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $fila ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $fila ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $fila++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
							<td bgcolor="<? echo $tema->celda ?>">
							<font color="red">*</font>Reescriba la clave:<br>
							</td>
							<td bgcolor="<? echo $tema->celda ?>">
								<input maxlength="50" size="30" tabindex="<? echo $tab++; ?>" name="clave_2" type="password">
							</td>
						</tr>
						<tr class='bloquecentralcuerpo' >
							<td bgcolor="<? echo $tema->celda ?>">
							<font color="red">*</font>Acceso requerido:
							</td>
							<td bgcolor="<? echo $tema->celda ?>"><?
								
								include_once($configuracion["raiz_documento"].$configuracion["clases"]."/encriptar.class.php");
								$indice=$configuracion["host"].$configuracion["site"]."/index.php?";
								$variable="pagina=seleccionar_rol";
								$variable.="&admin=lista";
								$cripto=new encriptar();
								$variable=$cripto->codificar_url($variable,$configuracion);							
							
							?>	<input type='hidden' name='roles'>
								<a name="enlace_roles" href="#enlace_roles" onclick="abrir_emergente('<?echo $indice.$variable  ?>','roles_usuario',window.document.<? echo $formulario?>.roles,window.document.<? echo $formulario?>.rol,<? echo (840/2) ?>,600)"><img src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["grafico"]?>/info.png" alt="Mostar roles" title="Mostrar roles" border="0" /> Seleccionar roles.</a>
							</td>
						</tr>
						<tr class='bloquecentralcuerpo' >
							<td bgcolor="<? echo $tema->celda ?>" valign="top">
							Roles seleccionados:
							</td>
							<td bgcolor="<? echo $tema->celda ?>">
								<textarea class="texto_negrita" rows="4" cols="40" name='rol'  tabindex='<? echo $tab++ ?>' >Ninguno</textarea>
							</td>
						</tr>
						<tr align="center" class="bloquecentralcuerpo">
							<td colspan="2" rowspan="1" align="center"><?
							if(isset($_REQUEST["admin"]))
							{?>
							<input type="hidden" name="admin" value="true">
							<?}?>
								<input type="hidden" name="action" value="registro_usuario">
								<input value="enviar" name="aceptar" type="button" tabindex='<? echo $tab++ ?>' onclick="return(<? echo $verificar; ?>)?document.forms['<? echo $formulario?>'].submit():false"><br>
							</td>
						</tr>
						<tr class="bloquecentralcuerpo">
							<td colspan="2" rowspan="1">
								Los campos marcados con <font color="red">*</font> deben ser diligenciados obligatoriamente.<br><br>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
</form>
<?
}


function corregir_registro($configuracion,$tema,$accion,$formulario,$verificar,$fila,$tab)
{

	include_once($configuracion["raiz_documento"].$configuracion["clases"]."/sesion.class.php");
	$nueva_sesion=new sesiones($configuracion);
	$esta_sesion=$nueva_sesion->numero_sesion();
	$cadena_sql="SELECT ";
	$cadena_sql.="`nombre`, ";
	$cadena_sql.="`apellido`, ";
	$cadena_sql.="`correo`, ";
	$cadena_sql.="`telefono`, ";
	$cadena_sql.="`usuario`, ";
	$cadena_sql.="`identificador` ";
	$cadena_sql.="FROM ";
	$cadena_sql.=$configuracion["prefijo"]."registrado_borrador "; 
	$cadena_sql.="WHERE ";
	$cadena_sql.="identificador='".$_REQUEST["identificador"]."' ";
	$cadena_sql.="LIMIT 1";
	
	//echo $cadena_sql;
	include_once($configuracion["raiz_documento"].$configuracion["clases"]."/dbms.class.php");
	$acceso_db=new dbms($configuracion);
	$enlace=$acceso_db->conectar_db();
	if (is_resource($enlace))
	{
		$total=$acceso_db->registro_db($cadena_sql,0);
		if($total>0)
		{
			$registro=$acceso_db->obtener_registro_db();
			//echo $registro[0][0];
		}
		else
		{
			nuevo_registro($configuracion,$tema,$accion,$formulario,$verificar,$fila,$tab);
			return TRUE;
		}
	}
	
?><script src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["javascript"]  ?>/funciones.js" type="text/javascript" language="javascript"></script>
<form method="post" action="index.php" name="<? echo $formulario?>">
<table width="100%" cellpadding="12" cellspacing="0"  align="center">
	<tbody>
		<tr>
			<td align="center" valign="middle">
				<table style="width: 100%; text-align: left;" border="0" cellpadding="5" cellspacing="1" class=bloquelateral>
					<tbody>
						<tr class="bloquecentralencabezado">
							<td colspan="2" rowspan="1">Registro para usuarios nuevos:</td>
						</tr>
						<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $fila ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $fila ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $fila++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
							<td bgcolor="<? echo $tema->celda ?>">
								<font color="red">*</font>Nombres:<br>
							</td>
							<td bgcolor="<? echo $tema->celda ?>">
								<input maxlength="80" size="40" tabindex="<? echo $tab++ ?>" name="nombre" value="<? echo $registro[0][0]?>" ><br>
							</td>
						</tr>
						<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $fila ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $fila ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $fila++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
							<td bgcolor="<? echo $tema->celda ?>">
								<font color="red">*</font>Apellidos:<br>
							</td>
							<td bgcolor="<? echo $tema->celda ?>">
								<input maxlength="80" size="40" tabindex="<? echo $tab++ ?>" name="apellido" value="<? echo $registro[0][1]?>">
							</td>
						</tr>
						<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $fila ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $fila ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $fila++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
							<td bgcolor="<? echo $tema->celda ?>">
								<font color="red">*</font>Correo Electr&oacute;nico:<br>
							</td>
							<td bgcolor="<? echo $tema->celda ?>"><?							
							if(strtolower($registro[0][2])=="verificar correo")
							{							
							?><input class="cuadro_corregir" maxlength="80" size="40" tabindex="<? echo $tab++ ?>" name="correo" value="<? echo $registro[0][2]?>"><?
							}
							else
							{
							?><input maxlength="80" size="40" tabindex="<? echo $tab++ ?>" name="correo" value="<? echo $registro[0][2]?>"><?
							}								
							?></td>
						</tr>
						<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $fila ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $fila ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $fila++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
							<td bgcolor="<? echo $tema->celda ?>">
								Tel&eacute;fono:<br>
							</td>
							<td bgcolor="<? echo $tema->celda ?>">
								<input maxlength="50" size="30" tabindex="<? echo $tab++ ?>" name="telefono" value="<? echo $registro[0][3]?>">
							</td>
						</tr>
						<tr>
							<td class="bloquecentralencabezado" colspan="2" rowspan="1">
								Datos para la autenticaci&oacute;n:
							</td>
						</tr>
						<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $fila ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $fila ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $fila++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
							<td bgcolor="<? echo $tema->celda ?>">
								<font color="red">*</font>Usuario:<br>
							</td>
							<td bgcolor="<? echo $tema->celda ?>"><?							
							if(strtolower($registro[0][4])=="verificar usuario")
							{							
							?><input class="cuadro_corregir" maxlength="50" size="30" tabindex="<? echo $tab++; ?>" name="usuario" value="<? echo $registro[0][4]?>"><?
							}
							else
							{
							?><input maxlength="50" size="30" tabindex="<? echo $tab++; ?>" name="usuario" value="<? echo $registro[0][4]?>"><?
							}								
							?>					
							</td>
						</tr>
						<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $fila ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $fila ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $fila++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
							<td bgcolor="<? echo $tema->celda ?>">
								<font color="red">*</font>Clave:
							</td>
							<td bgcolor="<? echo $tema->celda ?>">
								<input maxlength="50" size="30" tabindex="<? echo $tab++; ?>" name="clave"  type="password">
							</td>
						</tr>
						<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $fila ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $fila ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $fila++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
							<td bgcolor="<? echo $tema->celda ?>">
							<font color="red">*</font>Reescriba la clave:<br>
							</td>
							<td bgcolor="<? echo $tema->celda ?>">
								<input maxlength="50" size="30" tabindex="<? echo $tab++; ?>" name="clave_2" type="password">
							</td>
						</tr>
						<tr class='bloquecentralcuerpo' >
							<td bgcolor="<? echo $tema->celda ?>">
							<font color="red">*</font>Acceso requerido:
							</td>
							<td bgcolor="<? echo $tema->celda ?>"><?
								
								include_once($configuracion["raiz_documento"].$configuracion["clases"]."/encriptar.class.php");
								$indice=$configuracion["host"].$configuracion["site"]."/index.php?";
								$variable="pagina=seleccionar_rol";
								$variable.="&admin=lista";
								$cripto=new encriptar();
								$variable=$cripto->codificar_url($variable,$configuracion);							
							
							?>	<input type='hidden' name='roles'>
								<a name="enlace_roles" href="#enlace_roles" onclick="abrir_emergente('<?echo $indice.$variable  ?>','roles_usuario',window.document.<? echo $formulario?>.roles,window.document.<? echo $formulario?>.rol,<? echo (840/2) ?>,600)"><img src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["grafico"]?>/info.png" alt="Mostar roles" title="Mostrar roles" border="0" /> Seleccionar roles.</a>
							</td>
						</tr>
						<tr class='bloquecentralcuerpo' >
							<td bgcolor="<? echo $tema->celda ?>" valign="top">
							Roles seleccionados:
							</td>
							<td bgcolor="<? echo $tema->celda ?>">
								<textarea class="texto_negrita" rows="4" cols="40" name='rol'  tabindex='<? echo $tab++ ?>' >Ninguno</textarea>
							</td>
						</tr>
						<tr align="center" class="bloquecentralcuerpo">
							<td colspan="2" rowspan="1" align="center"><?
							if(isset($_REQUEST["admin"]))
							{?>
							<input type="hidden" name="admin" value="true">
							<?}?>
								<input type="hidden" name="action" value="registro_usuario">
								<input value="enviar" name="aceptar" tabindex='<? echo $tab++ ?>' type="button" onclick="return(<? echo $verificar; ?>)?document.forms['<? echo $formulario?>'].submit():false"><br>
							</td>
						</tr>
						<tr class="bloquecentralcuerpo">
							<td colspan="2" rowspan="1">
								Los campos marcados con <font color="red">*</font> deben ser diligenciados obligatoriamente.<br><br>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
</form>
<?
}


function editar_registro($configuracion,$tema,$accion,$formulario,$verificar,$fila,$tab)
{

	include_once($configuracion["raiz_documento"].$configuracion["clases"]."/encriptar.class.php");
	$cripto=new encriptar();
	$datos="";
	
	include_once($configuracion["raiz_documento"].$configuracion["clases"]."/sesion.class.php");
	$nueva_sesion=new sesiones($configuracion);
	$esta_sesion=$nueva_sesion->numero_sesion();
	$cadena_sql="SELECT ";
	$cadena_sql.="`nombre`, ";
	$cadena_sql.="`apellido`, ";
	$cadena_sql.="`correo`, ";
	$cadena_sql.="`telefono`, ";
	$cadena_sql.="`usuario`, ";
	$cadena_sql.="`id_usuario`, ";
	$cadena_sql.="`clave` ";
	$cadena_sql.="FROM ";
	$cadena_sql.=$configuracion["prefijo"]."registrado "; 
	$cadena_sql.="WHERE ";
	$cadena_sql.="id_usuario='".$_REQUEST["id_usuario"]."' ";
	$cadena_sql.="LIMIT 1";	
	//echo $cadena_sql;
	include_once($configuracion["raiz_documento"].$configuracion["clases"]."/dbms.class.php");
	$acceso_db=new dbms($configuracion);
	$enlace=$acceso_db->conectar_db();
	if (is_resource($enlace))
	{
		$total=$acceso_db->registro_db($cadena_sql,0);
		if($total>0)
		{
			$registro=$acceso_db->obtener_registro_db();
			//echo $registro[0][0];
		}
		else
		{
			nuevo_registro($configuracion,$tema,$accion,$formulario,$verificar,$fila,$tab);
			return TRUE;
		}
	}
	
?><script src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["javascript"]  ?>/funciones.js" type="text/javascript" language="javascript"></script>
<form method="post" action="index.php" name="<? echo $formulario?>">
<table width="100%" cellpadding="12" cellspacing="0"  align="center">
	<tbody>
		<tr>
			<td align="center" valign="middle">
				<table style="width: 100%; text-align: left;" border="0" cellpadding="5" cellspacing="1" class=bloquelateral>
					<tbody>
						<tr class="bloquecentralencabezado">
							<td colspan="2" rowspan="1">Registro para usuarios nuevos:</td>
						</tr>
						<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $fila ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $fila ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $fila++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
							<td bgcolor="<? echo $tema->celda ?>">
								<font color="red">*</font>Nombres:<br>
							</td>
							<td bgcolor="<? echo $tema->celda ?>">
								<input maxlength="80" size="40" tabindex="<? echo $tab++ ?>" name="nombre" value="<? echo $registro[0][0]?>" ><br>
							</td>
						</tr>
						<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $fila ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $fila ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $fila++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
							<td bgcolor="<? echo $tema->celda ?>">
								<font color="red">*</font>Apellidos:<br>
							</td>
							<td bgcolor="<? echo $tema->celda ?>">
								<input maxlength="80" size="40" tabindex="<? echo $tab++ ?>" name="apellido" value="<? echo $registro[0][1]?>">
							</td>
						</tr>
						<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $fila ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $fila ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $fila++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
							<td bgcolor="<? echo $tema->celda ?>">
								<font color="red">*</font>Correo Electr&oacute;nico:<br>
							</td>
							<td bgcolor="<? echo $tema->celda ?>">
								<input maxlength="80" size="40" tabindex="<? echo $tab++ ?>" name="correo" value="<? echo $registro[0][2]?>">
							</td>
						</tr>
						<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $fila ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $fila ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $fila++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
							<td bgcolor="<? echo $tema->celda ?>">
								Tel&eacute;fono:<br>
							</td>
							<td bgcolor="<? echo $tema->celda ?>">
								<input maxlength="50" size="30" tabindex="<? echo $tab++ ?>" name="telefono" value="<? echo $registro[0][3]?>">
							</td>
						</tr>
						<tr>
							<td class="bloquecentralencabezado" colspan="2" rowspan="1">
								Datos para la autenticaci&oacute;n:
							</td>
						</tr>
						<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $fila ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $fila ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $fila++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
							<td bgcolor="<? echo $tema->celda ?>">
								<font color="red">*</font>Usuario:<br>
							</td>
							<td bgcolor="<? echo $tema->celda ?>">
								<input maxlength="50" size="30" tabindex="<? echo $tab++; ?>" name="usuario" value="<? echo $registro[0][4]?>">
							</td>
						</tr>
						<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $fila ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $fila ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $fila++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
							<td bgcolor="<? echo $tema->celda ?>">
								<font color="red">*</font>Clave:
							</td>
							<td bgcolor="<? echo $tema->celda ?>">
								<input maxlength="50" size="30" tabindex="<? echo $tab++; ?>" name="clave"  type="password" value="<?echo $cripto->codificar("la_clave",$configuracion);?>">
							</td>
						</tr>
						<tr class='bloquecentralcuerpo' onmouseover="setPointer(this, <? echo $fila ?>, 'over', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmouseout="setPointer(this, <? echo $fila ?>, 'out', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');" onmousedown="setPointer(this, <? echo $fila++ ?>, 'click', '<? echo $tema->celda ?>', '<? echo $tema->apuntado ?>', '<? echo $tema->seleccionado ?>');">
							<td bgcolor="<? echo $tema->celda ?>">
							<font color="red">*</font>Reescriba la clave:<br>
							</td>
							<td bgcolor="<? echo $tema->celda ?>">
								<input maxlength="50" size="30" tabindex="<? echo $tab++; ?>" name="clave_2" type="password" value="<?echo $cripto->codificar("la_clave",$configuracion);?>">
							</td>
						</tr>
						<tr class='bloquecentralcuerpo' >
							<td bgcolor="<? echo $tema->celda ?>">
							<font color="red">*</font>Acceso requerido:
							</td>
							<td bgcolor="<? echo $tema->celda ?>"><?
								
								include_once($configuracion["raiz_documento"].$configuracion["clases"]."/encriptar.class.php");
								$indice=$configuracion["host"].$configuracion["site"]."/index.php?";
								$variable="pagina=seleccionar_rol";
								$variable.="&admin=lista";
								$cripto=new encriptar();
								$variable=$cripto->codificar_url($variable,$configuracion);							
							
							?>	<input type='hidden' name='roles'>
								<a name="enlace_roles" href="#enlace_roles" onclick="abrir_emergente('<?echo $indice.$variable  ?>','roles_usuario',window.document.<? echo $formulario?>.roles,window.document.<? echo $formulario?>.rol,<? echo (840/2) ?>,600)"><img src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["grafico"]?>/info.png" alt="Mostar roles" title="Mostrar roles" border="0" /> Seleccionar roles.</a>
							</td>
						</tr>
						<tr class='bloquecentralcuerpo' >
							<td bgcolor="<? echo $tema->celda ?>" valign="top">
							Roles seleccionados:
							</td>
							<td bgcolor="<? echo $tema->celda ?>">
								<textarea class="texto_negrita" rows="4" cols="40" name='rol'  tabindex='<? echo $tab++ ?>' ><?
								
								$cadena_sql="SELECT ";
								$cadena_sql.=$configuracion["prefijo"]."registrado_subsistema."."id_usuario, ";
								$cadena_sql.=$configuracion["prefijo"]."registrado_subsistema."."id_subsistema, ";
								$cadena_sql.=$configuracion["prefijo"]."subsistema."."etiqueta, ";
								$cadena_sql.=$configuracion["prefijo"]."registrado_subsistema."."estado ";
								$cadena_sql.="FROM ";
								$cadena_sql.=$configuracion["prefijo"]."registrado_subsistema, "; 
								$cadena_sql.=$configuracion["prefijo"]."subsistema "; 
								$cadena_sql.="WHERE "; 
								$cadena_sql.=$configuracion["prefijo"]."registrado_subsistema."."id_usuario='".$_REQUEST["id_usuario"]."' ";
								$cadena_sql.="AND ";
								$cadena_sql.=$configuracion["prefijo"]."registrado_subsistema."."id_subsistema=".$configuracion["prefijo"]."subsistema."."id_subsistema "; 
								$cadena_sql.="AND ";
								$cadena_sql.=$configuracion["prefijo"]."registrado_subsistema."."estado<2"; 
								//echo $cadena_sql;
								$roles=$acceso_db->registro_db($cadena_sql,0);
								if($roles>0)
								{
									$los_roles=$acceso_db->obtener_registro_db();
									$cadena="";
									for($i=0;$i<$roles;$i++)
									{
										$cadena.=$los_roles[$i][2]."\n";
									}
									
									
								}
								else
								{
									$cadena="Ninguno";
								}			
								
								echo $cadena;
								?></textarea>
							</td>
						</tr>
						<tr align="center" class="bloquecentralcuerpo">
							<td colspan="2" rowspan="1" align="center"><?
							if(isset($_REQUEST["admin"]))
							{
								$datos.="&admin=true";
							}
							$datos.="&action=registro_usuario";
							$datos.="&id_usuario=".$_REQUEST["id_usuario"];
							
							$datos=$cripto->codificar($datos,$configuracion);	
							?>	<input type='hidden' name='formulario' value="<? echo $datos ?>">
								<input value="enviar" name="aceptar" tabindex='<? echo $tab++ ?>' type="button" onclick="return(<? echo $verificar; ?>)?document.forms['<? echo $formulario?>'].submit():false"><br>
							</td>
						</tr>
						<tr class="bloquecentralcuerpo">
							<td colspan="2" rowspan="1">
								Los campos marcados con <font color="red">*</font> deben ser diligenciados obligatoriamente.<br><br>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
</form>
<?
}
