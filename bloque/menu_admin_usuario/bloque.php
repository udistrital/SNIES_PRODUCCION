<?
/*
El modulo se maneja a partir de la variable accion que determina lo que debe hacer:
accion 1: Mostrar todos los usuarios
accion 2: Mostrar usuarios activos
accion 3: Mostrar usuarios inactivos
accion 4: Agregar un nuevo usuario manualmente
accion 5: Borrar un usuario
accion 6: Editar un usuario

*/

include_once($configuracion["raiz_documento"].$configuracion["clases"]."/encriptar.class.php");
$cripto=new encriptar();
$indice=$configuracion["host"].$configuracion["site"]."/index.php?";

$acceso_db=new dbms($configuracion);
$enlace=$acceso_db->conectar_db();
if (is_resource($enlace))
{
	$cadena_hoja="SELECT ";
	$cadena_hoja.="count(id_usuario) ";
	$cadena_hoja.="FROM ";
	$cadena_hoja.=$configuracion["prefijo"]."registrado_subsistema ";
	$cadena_hoja.="WHERE estado=1 ";
	$acceso_db->registro_db($cadena_hoja,0);
	$registro=$acceso_db->obtener_registro_db();
	$campos=$acceso_db->obtener_conteo_db();
	if($campos>0)
	{
		$activo=$registro[0][0];
		//echo $hoja;
	}
	else
	{
		$activo=0;
	
	}
	$cadena_hoja="SELECT ";
	$cadena_hoja.="count(id_usuario) ";
	$cadena_hoja.="FROM ";
	$cadena_hoja.=$configuracion["prefijo"]."registrado_subsistema "; 
	$cadena_hoja.="WHERE ";
	$cadena_hoja.="estado=0 ";
	$acceso_db->registro_db($cadena_hoja,0);
	$registro=$acceso_db->obtener_registro_db();
	$campos=$acceso_db->obtener_conteo_db();
	if($campos>0)
	{
		$inactivo=$registro[0][0];
		//echo $hoja;
	}
	else
	{
		$inactivo=0;
	
	}
}
else
{
	$activo=0;
	$inactivo=0;

}
	$todo=$activo+$inactivo;
?><table width="100%" align="center" border="0" cellpadding="10" cellspacing="0" >
	<tbody>
		<tr>
			<td>
				<table width="100%" border="0" cellpadding="5" cellspacing="0" class="bloquelateral">
					<tr class="centralcuerpo">
					<td>
						<b>::::..</b> Mostrar
					</td>
					</tr>
					<tr class="bloquelateralcuerpo">
						<td width="100%">
							<a href="<?
							$variable="pagina=administrar_usuario";
							$variable.="&accion=1";
							$variable.="&hoja=1";
							$variable.="&mostrar=lista";
							$variable=$cripto->codificar_url($variable,$configuracion);
							echo $indice.$variable;
							?>">Todos los usuarios <b>(<?echo $todo ?>)</b></A>
						</td>
					</tr>
					<tr class="bloquelateralcuerpo">
						<td width="100%">
							<a href="<?
							$variable="pagina=administrar_usuario";
							$variable.="&accion=2";
							$variable.="&hoja=1";
							$variable.="&mostrar=lista";
							$variable=$cripto->codificar_url($variable,$configuracion);
							echo $indice.$variable;	
							?>">Usuarios activos <b>(<?echo $activo ?>)</b></A>
						</td>
					</tr>
					<tr class="bloquelateralcuerpo">
						<td width="100%">
							<a href="<?
							$variable="pagina=administrar_usuario";
							$variable.="&accion=3";
							$variable.="&hoja=1";
							$variable.="&mostrar=lista";
							$variable=$cripto->codificar_url($variable,$configuracion);
							echo $indice.$variable;
							?>">Usuarios inactivos <b>(<?echo $inactivo ?>)</b></A>
						</td>
					</tr>
					<tr class="bloquelateralcuerpo">
						<td width="100%">
						<BR>
							<a href="<?
							$variable="pagina=registro_admin_usuario";
							$variable.="&admin=1";
							$variable=$cripto->codificar_url($variable,$configuracion);
							echo $indice.$variable;
							?>"><B>Agregar Usuario</B></A>
						</td>
					</tr>
					<tr class="bloquelateralcuerpo">
						<td width="100%">
							<BR>
						</td>
					</tr>
					<tr class="centralcuerpo">
						<td width="100%" >
							<img src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["grafico"]?>/buscar.png"  border="0" /> Buscar Usuario
						</td>
					</tr>
					<tr class="centralcuerpo">
						<form name="standard" action="index.php" method="GET">
						<td width="100%" align="center">
							<input type="hidden" name="redireccion" value="<?
							$variable="pagina=administrar_usuario";
							$variable.="&accion=4";
							$variable.="&hoja=1";
							$variable.="&mostrar=lista";
							$variable=$cripto->codificar($variable,$configuracion);
							
							echo $variable;
							?>">
							<input type="text" name="busqueda" size="15"> 
						</td>
						<tr>
						<td width="100%" ALIGN=CENTER>
							
							<input type="submit" name="aceptar" value="buscar">
							
						</td>
						</tr>
						</form>
					</tr>
				</TABLE>
			</td>
		</tr>
	</tbody>
</table>
