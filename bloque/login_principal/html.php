<?
/*
############################################################################
#    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        #
#    Copyright: Vea el archivo EULA.txt que viene con la distribucion      #
############################################################################
*/
	$formulario="autenticacion";
	$validar="control_vacio(".$formulario.",'usuario')";
	$validar.="&& control_vacio(".$formulario.",'clave')";
	
?><script src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["javascript"]  ?>/md5.js" type="text/javascript" language="javascript"></script>
<form method="post" action="index.php" name="<?echo $formulario?>">
<table cellpadding=0 border=0 cellspacing=0>
	<tr>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-0-0.png" width="7" height="102"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-0-1.png" width="90" height="102"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-0-2.png" width="19" height="102"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-0-3.png" width="42" height="102"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-0-4.png" width="35" height="102"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-0-5.png" width="107" height="102"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-0-6.png" width="100" height="102"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-0-7.png" width="100" height="102"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-0-8.png" width="100" height="102"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-0-9.png" width="102" height="102"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-0-10.png" width="98" height="102"></td>
	</tr>
	<tr>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-1-0.png" width="7" height="98"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-1-1.png" width="90" height="98"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-1-2.png" width="19" height="98"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-1-3.png" width="42" height="98"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-1-4.png" width="35" height="98"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-1-5.png" width="107" height="98"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-1-6.png" width="100" height="98"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-1-7.png" width="100" height="98"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-1-8.png" width="100" height="98"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-1-9.png" width="102" height="98"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-1-10.png" width="98" height="98"></td>
	</tr>
	<tr>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-2-0.png" width="7" height="120"></td><?
		if(!isset($_REQUEST["no_usuario"]))
		{
		?><td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-2-1.png" width="90" height="120"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-2-2.png" width="19" height="120"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-2-3.png" width="42" height="120"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-2-4.png" width="35" height="120"></td><?
		}
		else
		{
		?><td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/sin_usuario_0.png" width="90" height="120"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/sin_usuario_1.png" width="19" height="120"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/sin_usuario_2.png" width="42" height="120"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/sin_usuario_3.png" width="35" height="120"></td><?		
		}?><td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-2-5.png" width="107" height="120"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-2-6.png" width="100" height="120"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-2-7.png" width="100" height="120"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-2-8.png" width="100" height="120"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-2-9.png" width="102" height="120"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-2-10.png" width="98" height="120"></td>
	</tr>
	<tr>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-3-0.png" width="7" height="52"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-3-1.png" width="90" height="52"></td>
		<td colspan="3" class="login_celda1">
			<table align="center" border="0" cellpadding="0" cellspacing="2">
				<tr>
					<td><? $tab=1;?>
					<input  class="cuadro_login" maxlength="30" size="12" tabindex="<? echo $tab++;?>" name="usuario" >
					</td>
				</tr>
				<tr>
					<td>
					<input class="cuadro_login" maxlength="60" size="12" tabindex="<?echo $tab++;?>	" name="clave" type="password" >
					<input type="hidden" name="action" value="login_principal">
					</td>
				</tr>
			</table>
		</td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-3-5.png" width="107" height="52"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-3-6.png" width="100" height="52"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-3-7.png" width="100" height="52"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-3-8.png" width="100" height="52"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-3-9.png" width="102" height="52"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-3-10.png" width="98" height="52"></td>
	</tr>
	<tr>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-4-0.png" width="7" height="33"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-4-1.png" width="90" height="33"></td>
		<td colspan="3" class="login_celda1">
			<table align="center" border="0" cellpadding="0" cellspacing="2">
				<tr>
					<td>
					<input class="cuadro_login" name="aceptar" type="button" value="Aceptar" tabindex="<? echo $tab++;?>" onclick="<?echo $formulario?>.clave.value = hex_md5(<?echo $formulario?>.clave.value);return(<? echo $validar; ?>)? document.forms['<? echo $formulario?>'].submit():false">
					</td>
				</tr>
			</table>
		</td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-4-5.png" width="107" height="33"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-4-6.png" width="100" height="33"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-4-7.png" width="100" height="33"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-4-8.png" width="100" height="33"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-4-9.png" width="102" height="33"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-4-10.png" width="98" height="33"></td>
	</tr>
	<tr>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-5-0.png" width="7" height="42"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-5-1.png" width="90" height="42"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-5-2.png" width="19" height="42"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-5-3.png" width="42" height="42"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-5-4.png" width="35" height="42"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-5-5.png" width="107" height="42"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-5-6.png" width="100" height="42"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-5-7.png" width="100" height="42"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-5-8.png" width="100" height="42"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-5-9.png" width="102" height="42"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-5-10.png" width="98" height="42"></td>
	</tr>
	<tr>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-6-0.png" width="7" height="18"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-6-1.png" width="90" height="18"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-6-2.png" width="19" height="18"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-6-3.png" width="42" height="18"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-6-4.png" width="35" height="18"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-6-5.png" width="107" height="18"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-6-6.png" width="100" height="18"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-6-7.png" width="100" height="18"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-6-8.png" width="100" height="18"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-6-9.png" width="102" height="18"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-6-10.png" width="98" height="18"></td>
	</tr>
	<tr>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-7-0.png" width="7" height="13"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-7-1.png" width="90" height="13"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-7-2.png" width="19" height="13"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-7-3.png" width="42" height="13"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-7-4.png" width="35" height="13"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-7-5.png" width="107" height="13"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-7-6.png" width="100" height="13"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-7-7.png" width="100" height="13"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-7-8.png" width="100" height="13"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-7-9.png" width="102" height="13"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-7-10.png" width="98" height="13"></td>
	</tr>
	<tr>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-8-0.png" width="7" height="16"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-8-1.png" width="90" height="16"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-8-2.png" width="19" height="16"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-8-3.png" width="42" height="16"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-8-4.png" width="35" height="16"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-8-5.png" width="107" height="16"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-8-6.png" width="100" height="16"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-8-7.png" width="100" height="16"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-8-8.png" width="100" height="16"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-8-9.png" width="102" height="16"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-8-10.png" width="98" height="16"></td>
	</tr>
	<tr>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-9-0.png" width="7" height="106"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-9-1.png" width="90" height="106"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-9-2.png" width="19" height="106"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-9-3.png" width="42" height="106"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-9-4.png" width="35" height="106"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-9-5.png" width="107" height="106"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-9-6.png" width="100" height="106"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-9-7.png" width="100" height="106"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-9-8.png" width="100" height="106"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-9-9.png" width="102" height="106"></td>
		<td><img alt=" " src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["bloques"]."/login_principal/" ?>imagen/index-9-10.png" width="98" height="106"></td>
	</tr>
</table>
</form>