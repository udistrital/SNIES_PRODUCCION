<?
/*
############################################################################
#    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        #
#    Desarrollo Por:                       				   #
#    Paulo Cesar Coronado 2004 - 2005                                      #
#    paulo_cesar@etb.net.co                                                #
#    Copyright: Vea el archivo EULA.txt que viene con la distribucion      #
############################################################################
*/
/****************************************************************************
  
registro.action.php 

Paulo Cesar Coronado
Copyright (C) 2001-2005

Última revisión 6 de Marzo de 2006

******************************************************************************
* @subpackage   
* @package	bloques
* @copyright    
* @version      0.2
* @author      	Paulo Cesar Coronado
* @link		N/D
* @description  Action de registro de usuarios
* @usage        
******************************************************************************/
?><?

if(!isset($GLOBALS["autorizado"]))
{
	include("../index.php");
	exit;		
}

if(!(strlen($_REQUEST['nombre'])>2)||!(strlen($_REQUEST['apellido'])>2)||!(strlen($_REQUEST['correo'])>6)||!(strlen($_REQUEST['usuario'])>2)||!(strlen($_REQUEST['clave'])>4))
{
	//Instanciar a la clase pagina con mensaje de correcion de datos
}
else
{
	include_once($configuracion["raiz_documento"].$configuracion["clases"]."/encriptar.class.php");
	$cripto=new encriptar();
	
	$acceso_db=new dbms($configuracion);
	$enlace=$acceso_db->conectar_db();
	if (is_resource($enlace))
	{	 
		if(!isset($_REQUEST["id_usuario"]))
		{
			$cadena_sql="SELECT ";
			$cadena_sql.="* ";
			$cadena_sql.="FROM ";
			$cadena_sql.="".$configuracion["prefijo"]."registrado ";
			$cadena_sql.="WHERE ";
			$cadena_sql.="usuario='".$_REQUEST['usuario']."'";
		
			$acceso_db->registro_db($cadena_sql,0);
			$registro=$acceso_db->obtener_registro_db();
			$campos=$acceso_db->obtener_conteo_db();
			if($campos>0)
			{
				
				$cadena_sql="SELECT ";
				$cadena_sql.="* ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$configuracion["prefijo"]."registrado ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="correo='".$_REQUEST['correo']."'";
				$acceso_db->registro_db($cadena_sql,0);
				$registro=$acceso_db->obtener_registro_db();
				$campos=$acceso_db->obtener_conteo_db();
				if($campos>0)
				{
					unset ($_REQUEST["correo"]);
				}
				unset ($_REQUEST["action"]);
				
				$identificador=time();
				
				$cadena_sql = "INSERT INTO ";
				$cadena_sql.=$configuracion["prefijo"]."registrado_borrador ";
				$cadena_sql.="( ";
				$cadena_sql.="nombre, ";
				$cadena_sql.="apellido, ";
				$cadena_sql.="correo, ";
				$cadena_sql.="telefono, ";
				$cadena_sql.="usuario, ";
				$cadena_sql.="identificador ";				
				$cadena_sql.=") ";
				$cadena_sql.="VALUES ";
				$cadena_sql.="(";
				$cadena_sql.="'".$_REQUEST['nombre']."',";
				$cadena_sql.="'".$_REQUEST['apellido']."' , ";
				if(isset($_REQUEST['correo']))
				{
					$cadena_sql.="'".$_REQUEST['correo']."', ";
				}
				else
				{
					$cadena_sql.="'Verificar correo', ";
				}
				$cadena_sql.="'".$_REQUEST['telefono']."',";
				$cadena_sql.="'Verificar Usuario',";
				$cadena_sql.="'".$identificador."'";				
				$cadena_sql.=")";
				
				$resultado=$acceso_db->ejecutar_acceso_db($cadena_sql); 
				
				if($resultado==TRUE)
				{
					$indice=$configuracion["host"].$configuracion["site"]."/index.php?";
					$cripto=new encriptar();
					$variable="pagina=registro_usuario";
					$variable.="&opcion=corregir";
					$variable.="&identificador=".$identificador;
					$variable=$cripto->codificar_url($variable,$configuracion);
					echo "<script>location.replace('".$indice.$variable."')</script>"; 
					exit(); 				
				}	
			}
			else
			{
				$cadena_sql="SELECT ";
				$cadena_sql.="* ";
				$cadena_sql.="FROM ";
				$cadena_sql.=$configuracion["prefijo"]."registrado ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="correo='".$_REQUEST['correo']."'";
				
				$acceso_db->registro_db($cadena_sql,0);
				$registro=$acceso_db->obtener_registro_db();
				$campos=$acceso_db->obtener_conteo_db();
				if($campos>0)
				{
					unset ($_REQUEST["action"]);
					unset ($_REQUEST["correo"]);
					$identificador=time();				
					$cadena_sql = "INSERT INTO ";
					$cadena_sql.=$configuracion["prefijo"]."registrado_borrador ";
					$cadena_sql.="( ";
					$cadena_sql.="nombre, ";
					$cadena_sql.="apellido, ";
					$cadena_sql.="correo, ";
					$cadena_sql.="telefono, ";
					$cadena_sql.="usuario, ";
					$cadena_sql.="identificador ";				
					$cadena_sql.=") ";
					$cadena_sql.="VALUES ";
					$cadena_sql.="(";
					$cadena_sql.="'".$_REQUEST['nombre']."',";
					$cadena_sql.="'".$_REQUEST['apellido']."' , ";
					if(isset($_REQUEST['correo']))
					{
						$cadena_sql.="'".$_REQUEST['correo']."', ";
					}
					else
					{
						$cadena_sql.="'Verificar correo', ";
					}
					$cadena_sql.="'".$_REQUEST['telefono']."',";
					$cadena_sql.="'".$_REQUEST['usuario']."',";
					$cadena_sql.="'".$identificador."'";				
					$cadena_sql.=")";
					
					$resultado=$acceso_db->ejecutar_acceso_db($cadena_sql); 
					if($resultado==TRUE)
					{
						$indice=$configuracion["host"].$configuracion["site"]."/index.php?";
						$cripto=new encriptar();
						$variable="pagina=registro_usuario";
						$variable.="&opcion=corregir";
						$variable.="&identificador=".$identificador;
						$variable=$cripto->codificar_url($variable,$configuracion);
						echo "<script>location.replace('".$indice.$variable."')</script>";
						exit(); 				
					}	
				}
				
				$cadena_sql = "INSERT INTO ";
				$cadena_sql.=$configuracion["prefijo"]."registrado ";
				$cadena_sql.="( ";
				$cadena_sql.="id_usuario, ";
				$cadena_sql.="nombre, ";
				$cadena_sql.="apellido, ";
				$cadena_sql.="correo, ";
				$cadena_sql.="telefono, ";
				$cadena_sql.="usuario, ";
				$cadena_sql.="clave ";				
				$cadena_sql.=") ";
				$cadena_sql.="VALUES ";
				$cadena_sql.="(";
				$cadena_sql.="NULL, ";
				$cadena_sql.="'".$_REQUEST['nombre']."',";
				$cadena_sql.="'".$_REQUEST['apellido']."' , ";
				$cadena_sql.="'".$_REQUEST['correo']."', ";
				$cadena_sql.="'".$_REQUEST['telefono']."',";
				$cadena_sql.="'".$_REQUEST['usuario']."',";
				$cadena_sql.="'".md5($_REQUEST['clave'])."'";
				$cadena_sql.=")";
				
				$resultado=$acceso_db->ejecutar_acceso_db($cadena_sql); 
				
				$este_usuario=$acceso_db->ultimo_insertado($enlace);
				
				if(isset($_REQUEST["roles"]))
				{
					$mis_roles=explode("&",$_REQUEST["roles"]);
										
					foreach($mis_roles as $clave=>$valor )
					{
						$cadena_sql= "INSERT INTO ";
						$cadena_sql.=$configuracion["prefijo"]."registrado_subsistema "; 
						$cadena_sql.="( ";
						$cadena_sql.="`id_usuario`, ";
						$cadena_sql.="`id_subsistema`, ";
						$cadena_sql.="`estado` ";
						$cadena_sql.=") ";
						$cadena_sql.="VALUES ";
						$cadena_sql.="( ";
						$cadena_sql.=$este_usuario.", ";
						$cadena_sql.="'".$valor."', ";
						$cadena_sql.="0 ";
						$cadena_sql.=")";					
						$resultado&=$acceso_db->ejecutar_acceso_db($cadena_sql);
					}
				}	
				
				
				if($resultado==TRUE)
				{
					if(!isset($_REQUEST["admin"]))
					{
						enviar_correo($configuracion);
						reset($_REQUEST);
						while(list($clave,$valor)=each($_REQUEST))
						{
							unset($_REQUEST[$clave]);
								
						}
						unset($_REQUEST['action']);
						
						$pagina=$configuracion["host"].$configuracion["site"]."/index.php?";
						$variable="pagina=index";
						$variable.="&registro_exito=1";
						include_once($configuracion["raiz_documento"].$configuracion["clases"]."/encriptar.class.php");
						$cripto=new encriptar();
						$variable=$cripto->codificar_url($variable,$configuracion);
						
						echo "<script>location.replace('".$pagina.$variable."')</script>";   				
						exit(); 
					}
					else
					{
						unset($_REQUEST['action']);
						$cripto=new encriptar();
						$indice=$configuracion["host"].$configuracion["site"]."/index.php?";
						$variable="pagina=admin_usuario";
						$variable.="&accion=1";
						$variable.="&hoja=0";
						$variable=$cripto->codificar_url($variable,$configuracion);
						echo "<script>location.replace('".$indice.$variable."')</script>"; 
						exit(); 
					}
				}
				else
				{
					
				}
						
			}
		}
		else
		{
			//Rescatar valores anteriores del registro
			$cadena_sql="SELECT ";
			$cadena_sql.="`id_usuario`, ";
			$cadena_sql.="`nombre`, ";
			$cadena_sql.="`apellido`, ";
			$cadena_sql.="`correo`, ";
			$cadena_sql.="`telefono`, ";
			$cadena_sql.="`usuario`, ";
			$cadena_sql.="`clave` ";
			$cadena_sql.="FROM ";
			$cadena_sql.="".$configuracion["prefijo"]."registrado ";
			$cadena_sql.="WHERE ";
			$cadena_sql.="id_usuario='".$_REQUEST['id_usuario']."' ";			
			$cadena_sql.="LIMIT 1";
			//echo $cadena_sql;
			
			$acceso_db->registro_db($cadena_sql,0);
			$registro=$acceso_db->obtener_registro_db();
			$campos=$acceso_db->obtener_conteo_db();
			if($campos>0)
			{
			
				//Verificar nombre de usuario y correo
				if($registro[0][3]!=$_REQUEST["correo"])
				{
				
				
				}
				else
				{
					$correo=$_REQUEST["correo"];
				}
				
				if($registro[0][5]!=$_REQUEST["usuario"])
				{
				
				
				}
				else
				{
					$usuario=$_REQUEST["usuario"];
				}
				
				
				
				
				
				
				$cadena_sql="UPDATE ";
				$cadena_sql.=$configuracion["prefijo"]."registrado "; 
				$cadena_sql.="SET "; 
				$cadena_sql.="`id_usuario`='".$registro[0][0]."', ";
				$cadena_sql.="`nombre`='".$_REQUEST['nombre']."', ";
				$cadena_sql.="`apellido`='".$_REQUEST['apellido']."', ";
				$cadena_sql.="`correo`='".$correo."', ";
				$cadena_sql.="`telefono`='".$_REQUEST['telefono']."', ";
				$cadena_sql.="`usuario`='".$usuario."', ";
				if($_REQUEST["clave"]==$cripto->codificar("la_clave",$configuracion))
				{
					$cadena_sql.= "`clave`='".$registro[0][6]."' ";
				}
				else
				{
					$cadena_sql.= "`clave`='".md5($_REQUEST['clave'])."' ";
				}
				$cadena_sql.="WHERE "; 
				$cadena_sql.="`id_usuario`='".$registro[0][0]."' ";
				
				$resultado=$acceso_db->ejecutar_acceso_db($cadena_sql);
				$logger=$acceso_db->logger($configuracion,$_REQUEST["id_usuario"],"Actualizacion datos de usuario No ".$_REQUEST["id_usuario"]);
				unset($_REQUEST['action']);
				
				if(isset($_REQUEST["roles"]))
				{
					$mis_roles=explode("&",$_REQUEST["roles"]);
					$cadena_borrar="";					
					foreach($mis_roles as $clave=>$valor )
					{
						if($cadena_borrar=="")
						{
							$cadena_borrar.="id_subsistema !=".$valor." ";
						}
						else
						{
							$cadena_borrar.="AND id_subsistema !=".$valor." ";
						
						}
						$cadena_sql="SELECT ";
						$cadena_sql.="`id_usuario`, ";
						$cadena_sql.="`id_subsistema`, ";
						$cadena_sql.="`estado` ";
						$cadena_sql.="FROM ";
						$cadena_sql.=$configuracion["prefijo"]."registrado_subsistema ";
						$cadena_sql.="WHERE ";
						$cadena_sql.="`id_usuario`='".$registro[0][0]."' ";
						$cadena_sql.="AND ";
						$cadena_sql.="`id_subsistema`='".$valor."' ";
						$cadena_sql.="AND ";
						$cadena_sql.="`estado`<2";
						$campos=$acceso_db->registro_db($cadena_sql,0);
						$subsistema=$acceso_db->obtener_registro_db();
						if($campos==0)
						{
							$cadena_sql= "INSERT INTO ";
							$cadena_sql.=$configuracion["prefijo"]."registrado_subsistema "; 
							$cadena_sql.="( ";
							$cadena_sql.="`id_usuario`, ";
							$cadena_sql.="`id_subsistema`, ";
							$cadena_sql.="`estado` ";
							$cadena_sql.=") ";
							$cadena_sql.="VALUES ";
							$cadena_sql.="( ";
							$cadena_sql.=$_REQUEST["id_usuario"].", ";
							$cadena_sql.="'".$valor."', ";
							$cadena_sql.="0 ";
							$cadena_sql.=")";	
							//echo $cadena_sql."<br>";
							
							$resultado&=$acceso_db->ejecutar_acceso_db($cadena_sql);
						
						}
					}
					
					$cadena_sql="DELETE ";
					$cadena_sql.="FROM ";
					$cadena_sql.=$configuracion["prefijo"]."registrado_subsistema ";
					$cadena_sql.="WHERE ";
					$cadena_sql.="(";
					$cadena_sql.=$cadena_borrar;
					$cadena_sql.=") ";
					$cadena_sql.="AND ";
					$cadena_sql.="`id_usuario`='".$registro[0][0]."' ";
					//echo $cadena_sql."<br>";
					//exit;
					
					$resultado&=$acceso_db->ejecutar_acceso_db($cadena_sql);
				}			
											
											
					
				$indice=$configuracion["host"].$configuracion["site"]."/index.php?";
				$variable="pagina=administrar_usuario";
				$variable.="&mostrar=lista";
				if(isset($_REQUEST["hoja"]))
				{
					$variable="&hoja=".$_REQUEST["hoja"];
					
				}
				else
				{
					$variable.="&hoja=1";
				}
				
				if(isset($_REQUEST["accion"]))
				{
					$variable.="&accion=".$_REQUEST["accion"];
				}
				else
				{
					$variable.="&accion=1";
				}
				$variable=$cripto->codificar_url($variable,$configuracion);
				echo "<script>location.replace('".$indice.$variable."')</script>";				
				exit(); 
								
								
			}
			else
			{
				echo "<h1>Error de Acceso</h1>Por favor contacte con el administrador del sistema.";				
			}
		}	
	} 
	else
	{
		//Mensaje de error de no disponibilidad de base de datos 
			
	}
}


function enviar_correo($configuracion)
{

	$destinatario=$configuracion["correo"];
	$encabezado="Nuevo Usuario ".$configuracion["titulo"];
	
	$mensaje="Señor Administrador:\n";
	$mensaje.=$_REQUEST['nombre']." ".$_REQUEST['apellido']."\n";
	$mensaje.="Correo Electronico:".$_REQUEST['correo']."\n";
	$mensaje.="Telefono:".$_REQUEST['telefono']."\n\n";
	$mensaje.="Ha solicitado acceso a ".$configuracion["titulo"]."\n\n";
	$mensaje.="Por favor visite la seccion de administracion para gestionar esta peticion.\n";
	$mensaje.="_____________________________________________________________________\n";
	$mensaje.="Por compatibilidad con los servidores de correo, en este mensaje se han omitido a\n";
	$mensaje.="proposito las tildes.";
	
	$correo= mail($destinatario, $encabezado,$mensaje) ;
	
	
	$destinatario=$_REQUEST['correo'];
	$encabezado="Solicitud de Confirmacion ".$configuracion["titulo"];
	
	
	$mensaje="Hemos recibido una solicitud para acceder al portal web\n";
	$mensaje.=$configuracion["titulo"];
	$mensaje.="en donde se referencia esta direccion de correo electronico.\n\n";
	$mensaje.="Si efectivamente desea inscribirse a nuestra comunidad por favor seleccione el siguiente enlace:\n";	
	$mensaje="En caso contrario por favor omita el contenido del presente mensaje.";
	$mensaje.="_____________________________________________________________________\n";
	$mensaje.="Por compatibilidad con los servidores de correo en este mensaje se han omitido a\n";
	$mensaje.="proposito las tildes.";
	$mensaje.="_____________________________________________________________________\n";
	$mensaje.="Si tiene inquietudes por favor envie un correo a: ".$configuracion["correo"]."\n";
	
	$correo= mail($destinatario, $encabezado,$mensaje) ;


}
	
?>
