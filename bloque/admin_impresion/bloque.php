<?
/*
############################################################################
#    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        #
#    Copyright: Vea el archivo LICENCIA.txt que viene con la distribucion  #
############################################################################
*/
/****************************************************************************
* @name          bloque.php 
* @revision      Última revisión 2 de junio de 2007
*****************************************************************************
* @subpackage   admin_recibo
* @package	bloques
* @copyright    
* @version      0.3
* @link		N/D
* @description  Bloque principal para la administración de medicamentoes
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
$pagina="registro_recibo";

$acceso_db=new dbms($configuracion);
$enlace=$acceso_db->conectar_db();
if (is_resource($enlace))
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
	$registro=$nueva_sesion->rescatar_valor_sesion($configuracion,"id_usuario");
	if($registro)
	{
		
		$usuario=$registro[0][0];
	}
	
	//Rescatar los recibos que se encuentran en proceso de impresion
	$cadena_sql=cadena_busqueda_recibo($configuracion, $acceso_db, $usuario,"completa");
	$registro=ejecutar_admin_recibo($cadena_sql,$acceso_db);
	
	if(!is_array($registro))
	{	
		
		$cadena="En la actualidad esta Coordinación no tiene ningún recibo registrado para impresión.";
		$cadena=htmlentities($cadena, ENT_COMPAT, "UTF-8");
		alerta::sin_registro($configuracion,$cadena);	
	}
	else
	{
		
		$campos=count($registro);
		
		con_registro_recibo($configuracion,$registro,$campos,$tema,$acceso_db);
		
	}
}



/****************************************************************
*  			Funciones				*
****************************************************************/



function con_registro_recibo($configuracion,$registro,$campos,$tema,$acceso_db)
{
	include_once($configuracion["raiz_documento"].$configuracion["clases"]."/encriptar.class.php");
	include_once($configuracion["raiz_documento"].$configuracion["clases"]."/pdf/fpdf.php");
	include_once($configuracion["raiz_documento"].$configuracion["clases"]."/barcode/barcode.inc.php");
	$cripto=new encriptar();
	$indice=$configuracion["host"].$configuracion["site"]."/index.php?";	
	setlocale(LC_MONETARY, 'en_US');
	
	$pdf=new FPDF('P','mm','letter');
	
	for($contador=0;$contador<$campos;$contador++)
	{
		
		$valor[0]=$registro[$contador][2];
		$valor[1]=$registro[$contador][6];

		//Generar la imagen del codigo de barras
		$codigo=$registro[$contador][2].$registro[$contador][2];
		include($configuracion["raiz_documento"].$configuracion["clases"]."/barcode/barcode.php");
		
		$pdf->SetMargins(0.5, 1);
		$pdf->AddPage();
		$pdf->Image($configuracion["raiz_documento"].$configuracion["grafico"].'/recibo.png',0,8);
		$pdf->Image($configuracion["raiz_documento"].$configuracion["documento"].'/codigo.png',10,70);
		$pdf->SetFont('Arial','B',10);
		$pdf->Ln(42);
		$pdf->Cell(6,4,"",0,0);
		//Nombreestudiante
		$pdf->Cell(72,4,$registro[$contador][5],0,0);
		//Codigo estudiante
		$pdf->Cell(31,4,$registro[$contador][2],0,0,"C");
		//Documento de Identidad
		$pdf->Cell(29,4,$registro[$contador][7],0,0,"C");
		//Codigo carrera
		$pdf->Cell(70,4,$registro[$contador][8],0,0,"C");
		//Fecha
		
		//
		$mi_matricula=calcular_pago($configuracion, $acceso_db, $valor);
		$pdf->Ln(12);
		$pdf->Cell(79,4,"",0);
		$pdf->Cell(45,4,money_format('$ %!.0i', $mi_matricula[4]),0);
		$pdf->Ln(43);
		$pdf->Cell(10,4,"",0);
		$pdf->Cell(45,4,$mi_matricula[1],0);
		//$mi_matricula[1];
	}
	$pdf->Output();
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
<?}



function calcular_pago($configuracion, $acceso_db, $valores)
{
	//1. Verificar pago inicial y reliquidado
	$cadena_sql=cadena_busqueda_recibo($configuracion, $acceso_db, $valores,"matricula");
	$registro=ejecutar_admin_recibo($cadena_sql,$acceso_db);	
	if(is_array($registro))
	{
		
		$valor_matricula=$registro[0][2];
		$valor_reliquidado=$valor_matricula;
		$valor_original=$registro[0][1];
		unset($registro);
		
		//2. Rescatar exenciones del estudiante
		$descripcion="";
		$cadena_sql=cadena_busqueda_recibo($configuracion, $acceso_db, $valores,"exencion");		
		$registro=ejecutar_admin_recibo($cadena_sql,$acceso_db);
		if(is_array($registro))
		{
			
			//3. Calcular el pago de acuerdo a las exenciones y construir las observaciones
			for($i=0;$i<count($registro);$i++)
			{
				$esta_exencion=(100-$registro[$i][7])/100;
				$valor_matricula=$valor_matricula*$esta_exencion;
				$descripcion=$descripcion." ".$registro[$i][8];
			}
			
		}
		$matricula[0]=$valor_matricula;
		$matricula[1]=$descripcion;
		$matricula[2]=$valor_original;
		$matricula[3]=$valor_reliquidado;
		
		//echo $matricula[1];
		return $matricula;
			
	}
	

}

function cadena_busqueda_recibo($configuracion, $acceso_db, $valor,$opcion="")
{
	$valor=$acceso_db->verificar_variables($valor);
	
	switch($opcion)
	{
		case "completa":	
			$cadena_sql="SELECT ";
			$cadena_sql.=$configuracion["prefijo"]."solicitud_recibo.id_solicitud_recibo, ";
			$cadena_sql.=$configuracion["prefijo"]."solicitud_recibo.id_usuario, ";
			$cadena_sql.=$configuracion["prefijo"]."solicitud_recibo.codigo_est, ";
			$cadena_sql.=$configuracion["prefijo"]."solicitud_recibo.estado, ";
			$cadena_sql.=$configuracion["prefijo"]."solicitud_recibo.fecha, ";
			$cadena_sql.=$configuracion["prefijo"]."estudiante.nombre, ";
			$cadena_sql.=$configuracion["prefijo"]."estudiante.id_carrera, ";
			$cadena_sql.=$configuracion["prefijo"]."estudiante.documento, ";
			$cadena_sql.=$configuracion["prefijo"]."programa.nombre ";
			$cadena_sql.="FROM ";
			$cadena_sql.=$configuracion["prefijo"]."estudiante, ";  
			$cadena_sql.=$configuracion["prefijo"]."solicitud_recibo, ";
			$cadena_sql.=$configuracion["prefijo"]."programa ";
			$cadena_sql.="WHERE ";
			$cadena_sql.=$configuracion["prefijo"]."solicitud_recibo.codigo_est=".$configuracion["prefijo"]."estudiante.codigo_est ";
			$cadena_sql.="AND ";
			$cadena_sql.=$configuracion["prefijo"]."estudiante.id_carrera=".$configuracion["prefijo"]."programa.id_programa ";
		
			if(isset($_REQUEST["accion"]))
			{
				
				$variable="";
				
				reset ($_REQUEST);
				while (list ($clave, $val) = each ($_REQUEST)) 
				{
					
					if($clave!='pagina')
					{
						$variable.="&".$clave."=".$val;
						//echo $clave;
					}
				}		
				
				switch($_REQUEST["accion"])
				{	
					case '1':				
						$cadena_sql.="AND ";
						$cadena_sql.=$configuracion["prefijo"]."solicitud_recibo.id_usuario=".$valor." ";
						$cadena_sql.="AND ";
						$cadena_sql.=$configuracion["prefijo"]."solicitud_recibo.estado<2 "; 
					
						break;
						
					//Todas los servicios que cumplan con el criterio de busqueda
					case '2':
							
						
						break;
						
								
					
					
					default:
						break;
							
					
				}
			}
			else
			{
				
				
			}
			break;
			
		case "matricula":
			$cadena_sql="SELECT ";
			$cadena_sql.="codigo_est, ";
			$cadena_sql.="valor_mat, ";
			$cadena_sql.="valor_bruto ";
			$cadena_sql.="FROM ";
			$cadena_sql.=$configuracion["prefijo"]."estudiante ";
			$cadena_sql.="WHERE ";
			$cadena_sql.="codigo_est=".$valor[0]." ";
			$cadena_sql.="AND ";
			$cadena_sql.="id_carrera=".$valor[1]." ";
			$cadena_sql.="LIMIT 1 ";
			//echo $cadena_sql;
			break;
		
		case "exencion":
			$cadena_sql="SELECT ";
			$cadena_sql.=$configuracion["prefijo"]."estudiante_exencion.codigo_est, ";
			$cadena_sql.=$configuracion["prefijo"]."estudiante_exencion.id_programa, ";
			$cadena_sql.=$configuracion["prefijo"]."estudiante_exencion.id_exencion, ";
			$cadena_sql.=$configuracion["prefijo"]."estudiante_exencion.anno, ";
			$cadena_sql.=$configuracion["prefijo"]."estudiante_exencion.periodo, ";
			$cadena_sql.=$configuracion["prefijo"]."estudiante_exencion.fecha, ";
			$cadena_sql.=$configuracion["prefijo"]."exencion.nombre, ";
			$cadena_sql.=$configuracion["prefijo"]."exencion.porcentaje, ";
			$cadena_sql.=$configuracion["prefijo"]."exencion.etiqueta ";			
			$cadena_sql.="FROM ";
			$cadena_sql.=$configuracion["prefijo"]."estudiante_exencion, ";
			$cadena_sql.=$configuracion["prefijo"]."exencion, ";
			$cadena_sql.=$configuracion["prefijo"]."estudiante ";
			$cadena_sql.="WHERE ";
			$cadena_sql.=$configuracion["prefijo"]."estudiante_exencion.codigo_est=".$valor[0]." ";
			$cadena_sql.="AND ";
			$cadena_sql.=$configuracion["prefijo"]."estudiante.id_carrera=".$valor[1]." ";
			$cadena_sql.="AND ";
			$cadena_sql.=$configuracion["prefijo"]."estudiante_exencion.codigo_est=".$configuracion["prefijo"]."estudiante.codigo_est ";			
			$cadena_sql.="AND ";
			$cadena_sql.=$configuracion["prefijo"]."estudiante_exencion.id_exencion=".$configuracion["prefijo"]."exencion.id_exencion ";
			//$cadena_sql.="LIMIT 1 ";
			//echo $cadena_sql;exit;
			break;
			
		
		default:
			$cadena_sql="";
			break;
	}
	//echo $cadena_sql;
	return $cadena_sql;
}

function ejecutar_admin_recibo($cadena_sql,$acceso_db)
{
	$acceso_db->registro_db($cadena_sql,0);
	$registro=$acceso_db->obtener_registro_db();
	return $registro;
}

?>
