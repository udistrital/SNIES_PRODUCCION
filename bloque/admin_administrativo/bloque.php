<?
/*
############################################################################
#    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        #
#    Copyright: Vea el archivo LICENCIA.txt que viene con la distribucion  #
############################################################################
*/
/****************************************************************************
* @name          bloque.php 
* @revision     Ultima revision 2 de junio de 2007
*****************************************************************************
* @subpackage   admin_inscrito
* @package	bloques
* @copyright    
* @version      0.3
* @link		N/D
* @description  Bloque principal para la administracion de incritos y admitidos
*
******************************************************************************/
if(!isset($GLOBALS["autorizado"]))
{
	include("../index.php");
	exit;		
}

include ($configuracion["raiz_documento"].$configuracion["estilo"]."/".$this->estilo."/tema.php");
//Se incluye para manejar los mensajes de error
include_once($configuracion["raiz_documento"].$configuracion["clases"]."/alerta.class.php");
include_once($configuracion["raiz_documento"].$configuracion["clases"]."/navegacion.class.php");

//Pagina a donde direcciona el menu
$pagina="registro_inscrito";

$conexion=new dbConexion($configuracion);
$acceso_db=$conexion->recursodb($configuracion,"oracle1");
$enlace=$acceso_db->conectar_db();

if (is_resource($enlace))
{
	//Rescatar los inscritos de la base de datos academica
	// si se envia la variable de a�o y/o periodo 
	
	if(isset($_REQUEST["annoInscrito"]))
	{
		$valor[0]=$_REQUEST["annoInscrito"];
	}
	else
	{
		$valor[0]="2008";		
	}
	
	if(isset($_REQUEST["periodoInscrito"]))
	{
		$valor[1]=$_REQUEST["periodoInscrito"];
	}
	else
	{
		$valor[1]="1";		
	}
	
		?>
	
<table width="100%" align="center" border="0" cellpadding="10" cellspacing="0" >
	<tbody>
		<tr>
			<td >
				<table width="100%" border="0" align="center" cellpadding="5 px" cellspacing="1px" >
					<tr class="centralcuerpo">
						<td>
						.::: Actualizaci&oacute;n Administrativos
						</td>
					</tr>	
				</table>
			</td>
		</tr>
		<tr>
			<td >
				<table width="100%" border="0" align="center" cellpadding="5 px" cellspacing="1px" >
					<tr>
						<td >
						<span class='texto_azul'>.::: P&aacute;gina en construcci&oacute;n</span>
						<?exit;?>
						</td>
					</tr>	
				</table>
			</td>
		</tr>
	</tbody>
</table>
			

			<?	
	
	
	//Rescatar TODOS los primiparos de un periodo y anno especifico
	$cadena_sql=cadena_busqueda_inscrito($configuracion, $acceso_db, $valor,"primiparo");		
	$registro=ejecutar_admin_inscrito($cadena_sql,$acceso_db);
	if(!is_array($registro))
	{	
		$cadena=htmlentities($cadena, ENT_COMPAT, "UTF-8");
		alerta::sin_registro($configuracion,$cadena);	
	}
	else
	{
		$contar=count($registro);
		$faltante=0;
		for($contador=0;$contador<10;$contador++)
		{
			//Verificar que el estudiante se encuentra en participantes
			unset($valor);
			$valor=$registro[$contador][0];
			if($valor!="")
			{
				$cadena_sql=cadena_busqueda_inscrito($configuracion, $acceso_db, $valor,"participantecc");		
				$registro2=ejecutar_admin_inscrito($cadena_sql,$acceso_db);
				if(!is_array($registro2))
				{	
					//Buscar los datos de ese registro�
					$cadena_sql=cadena_busqueda_inscrito($configuracion, $acceso_db, $valor,"datosEstudiante");					
					$registro2=ejecutar_admin_inscrito($cadena_sql,$acceso_db);
					
					//Guardar los datos en la tabla participante
					
					echo "El estudiante con documento = ".$valor." no existe en la tabla participante<br>";
					$faltante++;	
				}
			}
			
		}		
		
		echo "Faltan ".$faltante." registros por subir a la tabla de Participante. ";
		
			
		
	}	
	
	

}



/****************************************************************
*  			Funciones				*
****************************************************************/



function con_registro_inscrito($configuracion,$registro,$campos,$tema,$acceso_db)
{
	include_once($configuracion["raiz_documento"].$configuracion["clases"]."/encriptar.class.php");
	$cripto=new encriptar();
	$indice=$configuracion["host"].$configuracion["site"]."/index.php?";	
	setlocale(LC_MONETARY, 'en_US');
	
?><table width="100%" align="center" border="0" cellpadding="10" cellspacing="0" >
	<tbody>
		<tr>
			<td >
				<table width="100%" border="0" align="center" cellpadding="5 px" cellspacing="1px" >
					<tr class="centralcuerpo">
						<td>
						.::: Inscritos
						</td>
					</tr>
					<tr>
						<td>		
							<table class="contenidotabla">
								<tr class="cuadro_color">
									<td class="cuadro_plano centrar">
									A&ntilde;o
									</td>
								</tr>	
					<?
	for($contador=0;$contador<$campos;$contador++)
	{
		//Anno
		$valor[0]=$registro[$contador][0];				?>	
								<tr>
									<td class="cuadro_plano">
									<span class="texto_negrita"><? echo $valor[0]?></span>
									</td>
									<td class="cuadro_plano">
									<span class="texto_negrita"><? echo $valor[1]?></span>
									</td>
								</tr>
	<?
	}
	?>						</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</tbody>
</table><?
}

function estadistica($configuracion,$contador)
{?><table style="text-align: left;" border="0"  cellpadding="5" cellspacing="0" class="bloquelateral" width="100%">
	<tr>
		<td >
			<table cellpadding="10" cellspacing="0" align="center">
				<tr class="bloquecentralcuerpo">
					<td valign="middle" align="right" width="10%">
						<img src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["grafico"]?>/info.png" border="0" />
					</td>
					<td align="left">
						Actualmente hay <b><? echo $contador ?> usuarios</b> registrados.
					</td>
				</tr>
				<tr class="bloquecentralcuerpo">
					<td align="right" colspan="2" >
						<a href="<?
						echo $configuracion["site"].'/index.php?page='.enlace('admin_dir_dedicacion').'&registro='.$_REQUEST['registro'].'&accion=1&hoja=0&opcion='.enlace("mostrar").'&admin='.enlace("lista"); 
						
						?>">Ver m&aacute;s informaci&oacute;n >></a>
					</td>
				</tr>
			</table> 
		</td>
	</tr>  
</table>
<?

}

//Esta funcion sirve para definir la(s) clausula(s) SQL que se utilizan en este bloque

function cadena_busqueda_inscrito($configuracion, $acceso_db, $valor,$opcion="")
{
	$valor=$acceso_db->verificar_variables($valor);
	
	switch($opcion)
	{
		
		case "primiparo":	
			$prefijo="mntac.";			
			$cadena_sql="SELECT DISTINCT ";
			$cadena_sql.="asp_nro_iden codigo_unico, ";
			$cadena_sql.="as_cra_cod_snies pro_consecutivo, ";
			$cadena_sql.="'".$valor[0]."' anio, ";
			$cadena_sql.="'0".$valor[1]."' semestre, ";
			$cadena_sql.="'02' es_transferencia, ";
			$cadena_sql.="'1301' ies_code, ";			
			$cadena_sql.="TO_CHAR(DECODE(est_tipo_iden,'C','CC','T','TI','CC')) tipo_doc_unico ";
			$cadena_sql.="FROM ";
			$cadena_sql.=$prefijo."acest, ";
			$cadena_sql.=$prefijo."acasp, "; 
			$cadena_sql.=$prefijo."accra_snies ";
			$cadena_sql.="WHERE asp_nro_iden in (select est_nro_iden from mntac.acest) ";
			$cadena_sql.="AND asp_ape_ano=".$valor[0]." ";
			$cadena_sql.="AND asp_ape_per=".$valor[1]." ";
			$cadena_sql.="AND asp_nro_iden=est_nro_iden ";
			$cadena_sql.="AND as_cra_cod=est_cra_cod ";
	
			//echo $cadena_sql;
			//exit;
			
			break;
			
			case "participantecc":						
			$cadena_sql="SELECT ";
			$cadena_sql.="codigo_unico ";
			$cadena_sql.="FROM ";
			$cadena_sql.="sistemaslft.participantecc ";
			$cadena_sql.="WHERE ";
			$cadena_sql.="codigo_unico=".$valor;	
			//echo $cadena_sql;	
			break;
		
		case "datosEstudiante":
			$cadena_sql="SELECT ";
			$cadena_sql.="TO_CHAR('1301') ies_code, ";
			$cadena_sql.="DECODE((SUBSTR(trim(est_nombre),1,INSTR(trim(est_nombre),' ',1,2)-1)),NULL,est_nombre, (SUBSTR(trim(est_nombre),1,INSTR(trim(est_nombre),' ',1,2)-1)))primer_apellido, ";
			$cadena_sql.="SUBSTR(trim(est_nombre),1,INSTR(trim(est_nombre),' ',1,2)-1) segundo_apellido,";
			$cadena_sql.="SUBSTR(trim(est_nombre),INSTR(trim(est_nombre),' ',1,2)+1) primer_nombre,";
			$cadena_sql.="SUBSTR(trim(est_nombre),INSTR(trim(est_nombre),' ',1,2)+1) segundo_nombre, ";
			$cadena_sql.="TO_DATE(eot_fecha_nac) fecha_nacim, ";
			$cadena_sql.="TO_CHAR('CO') pais_ln, ";
			$cadena_sql.="TO_CHAR(SUBSTR(lug_cod,1,2)) departamento_ln, ";
			$cadena_sql.="TO_CHAR(SUBSTR(lug_cod,2,2)||SUBSTR(lug_cod,5,3)) municipio_ln, ";
			$cadena_sql.="TO_CHAR(DECODE(est_sexo,'M','01','F','02','01')) genero_code, ";
			$cadena_sql.="eot_email email, ";
			$cadena_sql.="DECODE(eot_estado_civil,1,'01',2,'02',3,'05',4,'03',5,'04') est_civil_code, ";
			$cadena_sql.="TO_CHAR(DECODE(est_tipo_iden,'C','CC','T','TI','CC')) tipo_doc_unico, ";
			$cadena_sql.="TO_CHAR(est_nro_iden) codigo_unico, ";
			$cadena_sql.="'CC' tipo_id_ant, ";
			$cadena_sql.="' ' codigo_id_ant, ";
			$cadena_sql.="'57' pais_tel, ";
			$cadena_sql.="'1' area_tel, ";
			$cadena_sql.="TO_CHAR(est_telefono) numero_tel ";
			$cadena_sql.="FROM acest a, acestotr, gelugar ";
			$cadena_sql.="WHERE ";
			$cadena_sql.="est_nro_iden=".$valor." " ;
			$cadena_sql.="AND est_estado_est IN ('A','B','H','L') ";
			$cadena_sql.="AND est_cod = eot_cod ";
			$cadena_sql.="AND lug_cod = eot_cod_lug_nac ";
			break;
			//echo $cadena_sql;
			//exit;
			
		
		case "datosEstudiante":
			$cadena_sql="INSERT INTO ";
			$cadena_sql.="mntac.participante ";
			$cadena_sql.="SELECT ";
			$cadena_sql.="TO_CHAR('1301') ies_code, ";
			$cadena_sql.="DECODE((SUBSTR(trim(est_nombre),1,INSTR(trim(est_nombre),' ',1,2)-1)),NULL,est_nombre, (SUBSTR(trim(est_nombre),1,INSTR(trim(est_nombre),' ',1,2)-1)))primer_apellido, ";
			$cadena_sql.="SUBSTR(trim(est_nombre),1,INSTR(trim(est_nombre),' ',1,2)-1) segundo_apellido,";
			$cadena_sql.="SUBSTR(trim(est_nombre),INSTR(trim(est_nombre),' ',1,2)+1) primer_nombre,";
			$cadena_sql.="SUBSTR(trim(est_nombre),INSTR(trim(est_nombre),' ',1,2)+1) segundo_nombre, ";
			$cadena_sql.="TO_DATE(eot_fecha_nac) fecha_nacim, ";
			$cadena_sql.="TO_CHAR('CO') pais_ln, ";
			$cadena_sql.="TO_CHAR(SUBSTR(lug_cod,1,2)) departamento_ln, ";
			$cadena_sql.="TO_CHAR(SUBSTR(lug_cod,2,2)||SUBSTR(lug_cod,5,3)) municipio_ln, ";
			$cadena_sql.="TO_CHAR(DECODE(est_sexo,'M','01','F','02','01')) genero_code, ";
			$cadena_sql.="eot_email email, ";
			$cadena_sql.="DECODE(eot_estado_civil,1,'01',2,'02',3,'05',4,'03',5,'04') est_civil_code, ";
			$cadena_sql.="TO_CHAR(DECODE(est_tipo_iden,'C','CC','T','TI','CC')) tipo_doc_unico, ";
			$cadena_sql.="TO_CHAR(est_nro_iden) codigo_unico, ";
			$cadena_sql.="'CC' tipo_id_ant, ";
			$cadena_sql.="' ' codigo_id_ant, ";
			$cadena_sql.="'57' pais_tel, ";
			$cadena_sql.="'1' area_tel, ";
			$cadena_sql.="TO_CHAR(est_telefono) numero_tel ";
			$cadena_sql.="FROM acest a, acestotr, gelugar ";
			$cadena_sql.="WHERE ";
			$cadena_sql.="est_nro_iden=".$valor." " ;
			$cadena_sql.="AND est_estado_est IN ('A','B','H','L') ";
			$cadena_sql.="AND est_cod = eot_cod ";
			$cadena_sql.="AND lug_cod = eot_cod_lug_nac ";
			break;
			//echo $cadena_sql;
			//exit;
				
		default:
			$cadena_sql="";
			break;
			
		
	}
	//echo $cadena_sql."<br>";
	//exit;
	return $cadena_sql;
}

function ejecutar_admin_inscrito($cadena_sql,$acceso_db)
{
	$acceso_db->registro_db($cadena_sql,0);
	$registro=$acceso_db->obtener_registro_db();
	//echo $registro[0][0]."<br>";
	return $registro;
}

?>
