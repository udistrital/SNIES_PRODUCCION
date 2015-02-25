<?
/*
############################################################################
#    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        #
#    Copyright: Vea el archivo EULA.txt que viene con la distribucion      #
############################################################################
*/
/***********************************************************************************************************
  
login.action.php 

Paulo Cesar Coronado
Copyright (C) 2001-2005

Última revisión 24 de noviembre de 2005

*************************************************************************************************************
* @subpackage   
* @package	formulario
* @copyright    
* @version      0.2
* @author      	Paulo Cesar Coronado
* @link		N/D
* 
*
* Script de procesamiento del formulario de autenticacion de usuarios
*
************************************************************************************************************/

if(!isset($GLOBALS["autorizado"]))
{
	include("../index.php");
	exit;
			
}

$acceso_db=new dbms($configuracion);

if ($acceso_db->probar_conexion()==TRUE)
{
	$nueva_sesion=new sesiones($configuracion);
	$nueva_sesion->especificar_enlace($acceso_db->obtener_enlace());
	$esta_sesion=$nueva_sesion->numero_sesion();
	$acceso_db->vaciar_temporales($configuracion,$esta_sesion);
	$nueva_sesion->borrar_sesion($configuracion,$esta_sesion);
	$valor["usuario"]=$_REQUEST["usuario"];
	$valor["clave"]=$_REQUEST["clave"];
	$cadena_sql=cadena_sql_login($configuracion, $acceso_db, $valor, "autenticacion");
	
	$campos=$acceso_db->registro_db($cadena_sql,0);
	$registro=$acceso_db->obtener_registro_db();
	
	if($campos==0)
	{
		unset($_REQUEST['action']);
		
		$pagina=$configuracion["host"].$configuracion["site"]."/index.php?";
		$variable="pagina=index";
		$variable.="&no_usuario=1";
		include_once($configuracion["raiz_documento"].$configuracion["clases"]."/encriptar.class.php");
		$cripto=new encriptar();
		$variable=$cripto->codificar_url($variable,$configuracion);
		
		echo "<script>location.replace('".$pagina.$variable."')</script>";   
			
	}	
	else
	{
		include_once($configuracion["raiz_documento"].$configuracion["clases"]."/encriptar.class.php");
		$cripto=new encriptar();
		$pagina=$configuracion["host"].$configuracion["site"]."/index.php?";
		
		$id_usuario=$registro[0][0];
		$usuario=$registro[0][1];
		$esta_sesion=$nueva_sesion->numero_sesion();
			
		if (strlen($esta_sesion) != 32) 
		{
			$nueva_sesion->especificar_usuario($usuario);
			$nueva_sesion->especificar_nivel($acceso);
			$la_sesion=$nueva_sesion->crear_sesion($configuracion,'','');	
			$resultado = $nueva_sesion->guardar_valor_sesion($configuracion,"id_usuario",$id_usuario,$la_sesion);
		
		} 
		else 
		{
			$resultado = $nueva_sesion->borrar_valor_sesion($configuracion,"usuario",$esta_sesion);
			$resultado = $nueva_sesion->guardar_valor_sesion($configuracion,"usuario",$usuario,$esta_sesion);
			$resultado = $nueva_sesion->borrar_valor_sesion($configuracion,"id_usuario",$esta_sesion);
			$resultado = $nueva_sesion->guardar_valor_sesion($configuracion,"id_usuario",$id_usuario,$esta_sesion);
			$resultado = $nueva_sesion->borrar_valor_sesion($configuracion,"expiracion",$esta_sesion);
			$resultado = $nueva_sesion->guardar_valor_sesion($configuracion,"expiracion",time()+$configuracion["expiracion"],$esta_sesion);
		}
		
		if($campos==1)
		{
			
			$acceso=$registro[0][2];
			if (strlen($esta_sesion) == 32) 
			{
				$resultado = $nueva_sesion->borrar_valor_sesion($configuracion,"acceso",$esta_sesion);
				$resultado = $nueva_sesion->guardar_valor_sesion($configuracion,"acceso",$acceso,$esta_sesion);			
			}
			else
			{
				$resultado = $nueva_sesion->borrar_valor_sesion($configuracion,"acceso",$la_sesion);
				$resultado = $nueva_sesion->guardar_valor_sesion($configuracion,"acceso",$acceso,$la_sesion);			
			}
			
			$cadena_sql=cadena_sql_login($configuracion, $acceso_db, $acceso, "pagina");			
			
			$campos=$acceso_db->registro_db($cadena_sql,0);
			if($campos>0)
			{
				$registro=$acceso_db->obtener_registro_db();
				$variable="pagina=".$registro[0][1];	
				
				//Variables especificas para cada seccion...
				switch($registro[0][1])
				{
					case "entidad":
						$variable.="&googlemaps=entidad";
					break;
					
					
					default:
					break;
				
				}
				
			}
			else
			{
				echo "No se logr&oacute; rescatar una secci&oacte;n v&aacute;lida";
			}
			
			$variable=$cripto->codificar_url($variable,$configuracion);
			echo "<script>location.replace('".$pagina.$variable."')</script>";     	
		}
		else
		{
			//El usuario esta registrado con mas de un perfil
			$variable="pagina=desambiguacion";
			//$variable.="&googlemaps=true";
			$variable=$cripto->codificar_url($variable,$configuracion);
			echo "<script>location.replace('".$pagina.$variable."')</script>";     	
			
			
		
		}
	}	
}
else
{
	include_once($configuracion["raiz_documento"].$configuracion["clase"]."/mensaje.class.php");
	
	$el_mensaje=new mensaje;
	$el_mensaje->mensaje("error_conexion",$configuracion);
	exit;
}

function cadena_sql_login($configuracion, $acceso_db, $valor, $tipo)
{
	$valor=$acceso_db->verificar_variables($valor);
	
	switch($tipo)
	{
		case "autenticacion":
		$cadena_sql="SELECT ";
		$cadena_sql.=$configuracion["prefijo"]."registrado.id_usuario, ";
		$cadena_sql.=$configuracion["prefijo"]."registrado.nombre, ";
		$cadena_sql.=$configuracion["prefijo"]."registrado_subsistema.id_subsistema, ";
		$cadena_sql.=$configuracion["prefijo"]."registrado_subsistema.estado ";
		$cadena_sql.="FROM ";
		$cadena_sql.=$configuracion["prefijo"]."registrado, ";
		$cadena_sql.=$configuracion["prefijo"]."registrado_subsistema ";
		$cadena_sql.="WHERE ";
		$cadena_sql.=$configuracion["prefijo"]."registrado.usuario='".$valor["usuario"]."' ";
		$cadena_sql.="AND ";
		$cadena_sql.=$configuracion["prefijo"]."registrado.clave='".$valor['clave']."' ";
		$cadena_sql.="AND ";
		$cadena_sql.=$configuracion["prefijo"]."registrado_subsistema.estado=1 ";
		$cadena_sql.="AND ";
		$cadena_sql.=$configuracion["prefijo"]."registrado.id_usuario=".$configuracion["prefijo"]."registrado_subsistema.id_usuario ";		
		break;
		
		case "carrera":
		
		case "pagina":
			$cadena_sql="SELECT ";
			$cadena_sql.=$configuracion["prefijo"]."subsistema.id_pagina, ";
			$cadena_sql.=$configuracion["prefijo"]."pagina.nombre ";
			$cadena_sql.="FROM ";
			$cadena_sql.=$configuracion["prefijo"]."subsistema, ";
			$cadena_sql.=$configuracion["prefijo"]."pagina ";
			$cadena_sql.="WHERE ";
			$cadena_sql.="id_subsistema='".$valor."' ";
			$cadena_sql.="AND ";
			$cadena_sql.=$configuracion["prefijo"]."subsistema.id_pagina=".$configuracion["prefijo"]."pagina.id_pagina ";			
			$cadena_sql.="LIMIT 1";
		break;
		
		default:
		$cadena_sql="";
		break;

		
	}
	//echo $cadena_sql;exit;
	return $cadena_sql;
}
	
?>
