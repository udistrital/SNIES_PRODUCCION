<?
if(!isset($GLOBALS["autorizado"]))
{
	include("../index.php");
	exit;		
}		
$acceso_db=new dbms($configuracion);
$enlace=$acceso_db->conectar_db();

if (is_resource($enlace))
{
	$cadena_sql="SELECT ";
	$cadena_sql.="id_usuario ";
	$cadena_sql.="FROM ";
	$cadena_sql.=$configuracion["prefijo"]."registrado ";
	$cadena_sql.="LIMIT 0,".$configuracion['registro'];
	
	$campos=$acceso_db->registro_db($cadena_sql,0);
	$registro=$acceso_db->obtener_registro_db();
	
	if($campos>0)
	{
		for($contador=0;$contador<$campos;$contador++)
		{
			if(isset($_REQUEST["usuario".$contador]))
			{
				if(isset($_REQUEST["tipo".$contador]))
				{
					
					if($_REQUEST["estado".$contador]==0)
					{
						/*Se realiza un update para deshabilitar el usuario y un borrado de la tabla de usuario*/
						$cadena_sql="UPDATE ";
						$cadena_sql.=$configuracion["prefijo"]."registrado_subsistema ";
						$cadena_sql.="SET ";
						$cadena_sql.="estado=1 ";
						$cadena_sql.="WHERE ";
						$cadena_sql.="id_usuario=".$_REQUEST["usuario".$contador]." ";
						$cadena_sql.="AND ";
						$cadena_sql.="id_subsistema=".$_REQUEST["subsistema".$contador]." ";
						//echo $cadena_sql."<br>";
						$acceso_db->ejecutar_acceso_db($cadena_sql);	
						
					}
				
				}
				else
				{
					if($_POST['estado'.$contador]==1)
					{
						$cadena_sql="UPDATE ";
						$cadena_sql.=$configuracion["prefijo"]."registrado_subsistema ";
						$cadena_sql.="SET ";
						$cadena_sql.="estado=0 ";
						$cadena_sql.="WHERE ";
						$cadena_sql.="id_usuario=".$_REQUEST["usuario".$contador]." ";
						$cadena_sql.="AND ";
						$cadena_sql.="id_subsistema=".$_REQUEST["subsistema".$contador]." ";
						//echo $cadena_sql."<br>";
						$acceso_db->ejecutar_acceso_db($cadena_sql);
					}	
					
				}
			}
				
		}	
	}		
	

	unset($_POST['action']);
	include_once($configuracion["raiz_documento"].$configuracion["clases"]."/encriptar.class.php");
	$cripto=new encriptar();
	
	$indice=$configuracion["host"].$configuracion["site"]."/index.php?";
	$variable="pagina=administrar_usuario";
	if(isset($_POST["hoja"]))
	{
		$variable.="&hoja=".$_POST["hoja"];		
	}
	if(isset($_POST["accion"]))
	{
		$variable.="&accion=".$_POST["accion"];
	}
	else
	{
		$variable.="&accion=1";
	}
	$variable.="&mostrar=lista";
	$variable=$cripto->codificar_url($variable,$configuracion);
	echo "<script>location.replace('".$indice.$variable."')</script>";   
							

}	
?>
