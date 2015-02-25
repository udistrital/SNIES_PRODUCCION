<?
/*
############################################################################
#    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        #
#    Copyright: Vea el archivo LICENCIA.txt que viene con la distribucion  #
############################################################################
*/
/****************************************************************************
* @name          bloque.php 
* @revision     Ultima revision 10 de junio de 2008
*****************************************************************************
* @subpackage   admin_admitido
* @package	bloques
* @copyright    
* @version      0.3
* @link		N/D
* @description  Bloque principal para la administracion de admitidos
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
include_once($configuracion["raiz_documento"].$configuracion["clases"]."/log.class.php");
require_once($configuracion["raiz_documento"].$configuracion["clases"]."/barraProgreso.class.php");
global $valor;
//Pagina a donde direcciona el menu
$pagina="registro_admitido";

$conexion=new dbConexion($configuracion);
$acceso_db=$conexion->recursodb($configuracion,"oracle2");
$log_us=new log();

$enlace=$acceso_db->conectar_db();

//echo $enlace." este es el recurso ORACLE";

$accesoSnies=$conexion->recursodb($configuracion,"sniesLocal");
$enlaceBlade1=$accesoSnies->conectar_db();

//echo $enlace."<br>";
//echo $enlaceAcademica."<br>";
//echo $enlaceBlade1."<br>";
//exit;

if($enlace && $enlaceBlade1)
{

	//Rescatar los admitidos de la base de datos academica
	// si se envia la variable de anno y/o periodo 
	

	if(isset($_REQUEST["annio"]) and isset($_REQUEST["periodo"]))
			{
				$valor[0]=$_REQUEST["annio"];
				//echo "annio=".$valor[0]."<br>";
				
				$valor[1]=$_REQUEST["periodo"];
				//echo "periodo=".$valor[1];
				$participante=0;
				$estudiante=0;
				$admitido=0;
				?>
					<table width="100%" align="center" border="0" cellpadding="10" cellspacing="0" >
						<tbody>
							<tr>
								<td >
									<table width="100%" border="0" align="center" cellpadding="5 px" cellspacing="1px" >
										<tr class="centralcuerpo">
											<td>
											.::: Actualuzaci&oacute;n Valores de matriculas <? echo $valor[0]." - ".$valor[1];?>
											</td>
										</tr>	
									</table>
								</td>
							</tr>
						</tbody>
					</table>
		
						<?		
		
						//barra de progreso busqueda de registros
					//	$bar = new barraProgreso($message='<br>Buscando Registros...', $hide=true, $sleepOnFinish=0, $barLength=500, $precision=50, $backgroundColor='#cccccc', $foregroundColor='blue'); 
		 			//	$bar->initialize(1);
		
					
						
			
			//Rescatar TODOS los admitidos de un periodo y anno especifico
			
				//Termina barra de progreso despues del proceso de busqueda
				//$bar->increase();	
			
				
			$cadena_sql=cadena_busqueda_admitido($configuracion, $acceso_db, $valor,"programas_matricula");
			//echo "<br> matriculas <br>".$cadena_sql;exit;
			$registro=ejecutar_admin_admitido($cadena_sql,$acceso_db);
			
			//echo "admitidos ".count($registro);
			if(!is_array($registro))
					{	
						$cadena=htmlentities($cadena, ENT_COMPAT, "UTF-8");
						echo "No existen registros en la Base de Datos para este per&iacute;odo";
						//alerta::sin_registro($configuracion,$cadena);	
					}
			else
					{
						$contar=count($registro);		
						//barra de progreso
						//$bar = new barraProgreso($message=$mensaje, $hide=true, $sleepOnFinish=0, $barLength=500, $precision=50, $backgroundColor='#cccccc', $foregroundColor='blue'); 
				 		//$bar->initialize($contar);
				 		
				 								
						for($contador=0;$contador<$contar;$contador++)
							{
								//echo "codigo ".number_format($registro[$contador][4],1,".","");
								//$bar->increase();
								
								//se selecciona cada registro de admitidos, uno a la vez	
								unset($valor[2]);
								unset($unError);
								$valor[2]=$registro[$contador][2];
								$valor[3]=$registro[$contador][1];
								
								//echo "<H2>".$contador."</H2>".$valor[2]."<br>";
								//echo "<hr class='hr_subtitulo'>";
                                                                 $registroPrincipal[0]=$registro[$contador];		//Que contiene los datos de la busqueda inicial
                                        			echo "<hr>".$contador." - Programa:<h3>".$registroPrincipal[0][1]." ".$registroPrincipal[0][0]."</h3>";
								//echo $valor[2]."<br>";
								//exit;
				
								if($valor[3]!="")
										{			
											//ACTUALIZAR INSCRITO
											
											//buscar los registros de participantes en el SNIES
											$cadena_sql=cadena_busqueda_admitido($configuracion, $accesoSnies, $valor,"programaSnies");
											//echo "<br> programa_snies <br>".$cadena_sql;		//exit;
											$registroMatriculasSnies=ejecutar_admin_admitido($cadena_sql,$accesoSnies);
																	
											//Verificar que el admitido no esta en la tabla INSCRITO del SNIES <<EXISTE EL PARTICIPANTE=NO>> 
										
											if(!is_array($registroMatriculasSnies))
											
														{  //busca que exista el programa para el periodo seleccionado
														   $cubrimiento[0]=$valor[0];//anno
														   $cubrimiento[1]=$valor[1];//periodo
														   $cubrimiento[2]=$registro[$contador][1];
																														
														    $cadena_sql=cadena_busqueda_admitido($configuracion, $accesoSnies, $cubrimiento,"cubrimiento");
														//echo "<br>".$cadena_sql;		exit;
														    $cubrimientoPrograma=ejecutar_admin_admitido($cadena_sql,$accesoSnies);
														    
															if($cubrimientoPrograma[0][0]==0)
																{
																$cadena_sql=cadena_busqueda_admitido($configuracion, $accesoSnies, $cubrimiento,"insertarCubrimiento");
																//echo "<br>".$cadena_sql;exit;
														    		$Programa=ejecutar_admin_admitido($cadena_sql,$accesoSnies);
																}
                                                                                                                                

                                                                                                                          if($registro[$contador][7]=='PREGRADO')
																{
																$cadena_sql=cadena_busqueda_admitido($configuracion, $acceso_db, $valor,"programa_nuevos_pregrado");
																}
                                                                                                                           else {$cadena_sql=cadena_busqueda_admitido($configuracion, $acceso_db, $valor,"programa_nuevos_postgrado");
                                                                                                                                }
                                                                                                                                // echo "<br> matriculas <br>".$cadena_sql;//exit;
                                                                                                                                $registro_nuevo=ejecutar_admin_admitido($cadena_sql,$acceso_db);

                                                                                                                                $matricula[0]=$registro[$contador][2];//año
                                                                                                                                $matricula[1]=$registro_nuevo[0][3];//promedio_matricula_est_nuevo
                                                                                                                                $matricula[2]=$registro_nuevo[0][5];//Nro estudiantes_nuevos
                                                                                                                                $matricula[3]=$registro[$contador][6];//Nro estudiantes_antiguos
                                                                                                                                $matricula[4]=$registro_nuevo[0][4];//promedio_matricula_ext_est_nuevo
                                                                                                                                $matricula[5]=$registro[$contador][5];//promedio_matricula_estemporanea_nuevos
                                                                                                                                $matricula[6]=$registro[$contador][1];//codigo carrera
                                                                                                                                $matricula[7]=$registro[$contador][4];//promedio_matricula_antiguos
                                                                                                                                $matricula[8]=$registro[$contador][3];//periodo//insertar los datos del admitido en la tabla INSCRITO del SNIES
															
																$cadena_sql=cadena_busqueda_admitido($configuracion, $accesoSnies,  $matricula,"insertarMatricula");
																//echo "<br>insertar<br> ".$cadena_sql."<br>";//exit;
																$resultadoMatricula=ejecutar_admin_admitido($cadena_sql,$accesoSnies,"insertar");
																
																
																
																if($resultadoMatricula==false)
																{
																	$resultadoMatricula=ejecutar_admin_admitido($cadena_sql,$accesoSnies,"error");
																	echo "El registro no pudo ser cargado en la tabla programa_h por:".$resultadoMatricula."<br>";
																	$unError=true;
																	//echo "El registro no pudo ser cargado, por favor verifique los datos en la Aplicaci&oacute;n Acad&eacute;mica <br>";
																	//echo "<hr class='hr_subtitulo'>";
																	
																}	
																else
																{   $matri++;
                                                                                                                                    echo "<br>El registro se reporto satisfactoriamente";
																	
																}	
														}//cierra el if que carga el admitido
											}//cierra el if que verifica el nuemro de documento
									}//cierra el for que recorre los registros
							
						}	//fin actualizacion
				//VARIABLES PARA EL LOG
						$logger[0]="CARGAR AL SNIES LOCAL";
						$logger[2]='0';
						$logger[3]=$matri;
						$logger[4]=date('d/m/Y h:i:s',time());
						$logger[5]=$valor[0];
						$logger[6]=$valor[1];
						$logger[7]="Se cargo los datos, para el reporte de matricuals de programas al SNIES para el periodo ".$valor[0]."-".$valor[1];
						//echo $logger[7];
						$log_us->log_usuario($logger,$configuracion);
						

					
					?>
						<table width="100%" align="center" border="0" cellpadding="10" cellspacing="0" >
							<tbody>
								<tr>
									<td >
									<table align="center" border="0" cellpadding="5" cellspacing="0" class="bloquelateral_2" width="100%">
										<tr class="bloquecentralencabezado">
											<td colspan='3'>Resultados</td>
										</tr>	
										<tr class="bloquecentralcuerpo">
											<td>TABLA</td>
											<td>REGISTROS CARGADOS</td>
										</tr>
										<tr class="bloquecentralcuerpo">
											<td>PROGRAMAS</td>
											<td><?	echo $matri;?></td>
										</tr>
										</table>
									</td>
								</tr>
							</tbody>
						</table>						
	<?		}//cierra el if que verifica año y periodo
		
	///busca los taotales
	$cadena_sql=cadena_busqueda_admitido($configuracion, $accesoSnies, "","totalProgramasMatricula");
	//echo "<br>totales<br> ".$cadena_sql."<br>";
	$registroAdmitidoTotales=ejecutar_admin_admitido($cadena_sql,$accesoSnies);
	

	//echo $totalAdmitido."total";
	//exit;
		
		?>
				
<table width="100%" align="center" border="0" cellpadding="10" cellspacing="0" >
	<tbody>
		<tr>
			<td >
				<table width="100%" border="0" align="center" cellpadding="5 px" cellspacing="1px" >
					<tr class="centralcuerpo">
						<td>
						.::: Matriculas Programas
						</td>
					</tr>
							
					<tr>
						<td>
							<table class="contenidotabla">
									<tr class="cuadro_color">
										<td width="25%" class="cuadro_plano centrar">
										A&Ntilde;O
										</td>
										<td width="25%" class="cuadro_plano centrar">
										SEMESTRE
										</td>
										<td class="cuadro_plano centrar">
										TOTAL PROGRAMAS
										</td>					
									</tr>	
									
									<?$n=0;
								while (count($registroAdmitidoTotales)>$n)
									{	
									if(is_array($registroAdmitidoTotales[$n]))
									{?>
									
									<tr>
										<td class="cuadro_plano centrar">
										<span class="texto_negrita"><? echo $totalAdmitido=$registroAdmitidoTotales[$n][0];?></span> 
										</td>
										<td class="cuadro_plano centrar">
										<span class="texto_negrita"><? echo $totalAdmitido=$registroAdmitidoTotales[$n][1];?></span>
										</td>
										<td class="cuadro_plano centrar centrar">
										<span class="texto_negrita"><? echo $totalAdmitido=$registroAdmitidoTotales[$n][2];?></span>
										</td>
									</tr>
									<?$n=$n+1;
									}
								}?>
						</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</tbody>
</table>
			

		

		<?		
		
		
		
			
		
	}	
	
	





/****************************************************************
*  			Funciones				*
****************************************************************/



//Esta funcion sirve para definir la(s) clausula(s) SQL que se utilizan en este bloque

function cadena_busqueda_admitido($configuracion, $acceso_db, $valor,$opcion="")
{
	$valor=$acceso_db->verificar_variables($valor);
	
	switch($opcion)
	{
	
		//consulta para generar las matriculas de estudiantes antiguos de todas las carreras
		case "programas_matricula":

                        $prefijo="mntac.";
			$cadena_sql="SELECT UNIQUE ";
			$cadena_sql.="as_cra_nom programa,  ";
                        $cadena_sql.="as_cra_cod_snies programa_snies,  ";
                        $cadena_sql.="ema_ano ano, ";
			$cadena_sql.="ema_per periodo, ";
			$cadena_sql.="ROUND(AVG(ema_valor)) prom_matriculam,  ";
			$cadena_sql.="ROUND(AVG(ema_valor)*1.2) prom_matriculam_ext, ";
			$cadena_sql.="COUNT (ema_est_cod) estudiantes,";
			$cadena_sql.="tra_nivel nivel ";
			$cadena_sql.="FROM ".$prefijo."accra ";
			$cadena_sql.="INNER JOIN ".$prefijo."acestmat ON cra_cod = ema_cra_cod ";
			$cadena_sql.="INNER JOIN ".$prefijo."actipcra ON cra_tip_cra = tra_cod ";
			$cadena_sql.="INNER JOIN ".$prefijo."accra_snies ON cra_cod=as_cra_cod ";
			$cadena_sql.="WHERE  ";
			$cadena_sql.="ema_estado = 'A' ";
			if($valor[1]==2 || $valor[1]==3)
					{
					$cadena_sql.="AND substr(ema_est_cod,0,5)<>'".$valor[0]."2' ";
					}
			else
					{
					$cadena_sql.="AND substr(ema_est_cod,0,5)<>'".$valor[0]."1' ";
					}
			$cadena_sql.="AND ema_ano=".$valor[0]." ";
			$cadena_sql.="AND ema_per=".$valor[1]." ";  //los periodos son '1' , '2' o '3'
			$cadena_sql.=" GROUP BY ";
                        $cadena_sql.="as_cra_nom, ";
			$cadena_sql.="as_cra_cod_snies, ";
			$cadena_sql.="ema_ano, ";
			$cadena_sql.="ema_per, ";
			$cadena_sql.="tra_nivel ";
			$cadena_sql.=" ORDER BY ";
			$cadena_sql.="as_cra_cod_snies ";
								
			//echo $cadena_sql;
			//exit;
			
			break;
			        

                        case "programa_nuevos_pregrado":

                        $prefijo="mntac.";
			$cadena_sql="SELECT UNIQUE ";
			$cadena_sql.="as_cra_cod_snies programa_snies,  ";
                        $cadena_sql.="asp_ape_ano ano, ";
			$cadena_sql.="asp_ape_per periodo, ";
			$cadena_sql.="ROUND(AVG(ama_valor)) prom_matricula,  ";
			$cadena_sql.="ROUND(AVG(ama_ext)) prom_matricula_ext, ";
			$cadena_sql.="COUNT(asp_cred) estudiantes_nuevos ";
			$cadena_sql.="FROM ".$prefijo."acasp ";
			$cadena_sql.="INNER JOIN ".$prefijo."acadmmat ON asp_cred=ama_asp_cred AND asp_cra_cod=ama_cra_cod AND asp_ape_per= ama_per AND asp_ape_ano=ama_ano ";
			$cadena_sql.="INNER JOIN ".$prefijo."accra_snies ON asp_cra_cod=as_cra_cod ";
			$cadena_sql.="WHERE  ";
			$cadena_sql.="ama_estado = 'A' ";
			$cadena_sql.="AND ama_ano=".$valor[0]." ";
			$cadena_sql.="AND ama_per=".$valor[1]." ";  //los periodos son '1' , '2' o '3'
                        $cadena_sql.="AND as_cra_cod_snies=".$valor[3]." ";
			$cadena_sql.=" GROUP BY ";
			$cadena_sql.="as_cra_cod_snies, ";
			$cadena_sql.="asp_ape_ano, ";
			$cadena_sql.="asp_ape_per ";
			$cadena_sql.=" ORDER BY ";
			$cadena_sql.="as_cra_cod_snies ";

			//echo $cadena_sql;
			//exit;

			break;

                        case "programa_nuevos_postgrado":

                        $prefijo="mntac.";
			$cadena_sql="SELECT UNIQUE ";
			$cadena_sql.="as_cra_cod_snies programa_snies,  ";
                        $cadena_sql.="ema_ano ano, ";
			$cadena_sql.="ema_per periodo, ";
			$cadena_sql.="ROUND(AVG(ema_valor)) prom_matriculam_nuevos,  ";
			$cadena_sql.="ROUND(AVG(ema_valor)) prom_matriculam_ext_nuevos, ";
			$cadena_sql.="COUNT (ema_est_cod) estudiantes_nuevos ";
			$cadena_sql.="FROM ".$prefijo."accra ";
			$cadena_sql.="INNER JOIN ".$prefijo."acestmat ON cra_cod = ema_cra_cod ";
			$cadena_sql.="INNER JOIN ".$prefijo."actipcra ON cra_tip_cra = tra_cod ";
			$cadena_sql.="INNER JOIN ".$prefijo."accra_snies ON cra_cod=as_cra_cod ";
			$cadena_sql.="WHERE  ";
			$cadena_sql.="ema_estado = 'A' ";
			if($valor[1]==2 || $valor[1]==3)
					{
					$cadena_sql.="AND substr(ema_est_cod,0,5)='".$valor[0]."2' ";
					}
			else
					{
					$cadena_sql.="AND substr(ema_est_cod,0,5)='".$valor[0]."1' ";
					}
			$cadena_sql.="AND ema_ano=".$valor[0]." ";
			$cadena_sql.="AND ema_per=".$valor[1]." ";  //los periodos son '1' , '2' o '3'
                        $cadena_sql.="AND as_cra_cod_snies=".$valor[3]." ";
			$cadena_sql.=" GROUP BY ";
			$cadena_sql.="as_cra_cod_snies, ";
			$cadena_sql.="ema_ano, ";
			$cadena_sql.="ema_per, ";
			$cadena_sql.="tra_nivel ";
			$cadena_sql.=" ORDER BY ";
			$cadena_sql.="as_cra_cod_snies ";

			//echo $cadena_sql;
			//exit;

			break;


			//Consulta de la tabla admitido del SNIES LOCAL
		
			case "programaSnies":
			$cadena_sql="SELECT ";
			$cadena_sql.="pro_consecutivo,";
			$cadena_sql.="prog_annio, ";
			$cadena_sql.="prog_semestre ";
			$cadena_sql.="FROM programa_h ";
			$cadena_sql.="WHERE ";
			$cadena_sql.="prog_annio=".$valor[0]." " ;
			if($valor[1]==2 || $valor[1]==3)
					{
					$cadena_sql.="AND prog_semestre='02' " ;
					}
			else		
					{
					$cadena_sql.="AND prog_semestre='01' " ;
					}			
			$cadena_sql.=" AND " ;
			$cadena_sql.="pro_consecutivo=".$valor[3]." " ;
			
			//echo $cadena_sql;
			//exit;
			
			break;
		
		
		
			case "insertarMatricula":
			
			$cadena_sql="INSERT INTO ";
			$cadena_sql.="programa_h ";
			$cadena_sql.="VALUES ";
                        $cadena_sql.="(";
                        $cadena_sql.="'1301', ";
			$cadena_sql.="'".$valor[0]."', ";
                        if($valor[1]=='')
                                {$cadena_sql.="0," ;}
                        else
                                {$cadena_sql.="'".$valor[1]."', ";}
                        if($valor[2]=='')
                                {$cadena_sql.="0," ;}
                        else
                                {$cadena_sql.="'".$valor[2]."', ";}
                        if($valor[3]=='')
                                {$cadena_sql.="0," ;}
                        else
                                {$cadena_sql.="'".$valor[3]."', ";}
			if($valor[4]=='')
                                {$cadena_sql.="0," ;}
                        else
                                {$cadena_sql.="'".$valor[4]."', ";}
			if($valor[5]=='')
                                {$cadena_sql.="0," ;}
                        else
                                {$cadena_sql.="'".$valor[5]."', ";}
                        $cadena_sql.="'".$valor[6]."', ";
			$cadena_sql.="'11', ";
			$cadena_sql.="'11001', ";
                        if($valor[7]=='')
                                {$cadena_sql.="0," ;}
                        else
                                {$cadena_sql.="'".$valor[7]."', ";}
                        $cadena_sql.="'1301', ";
                        if($valor[8]==2 || $valor[8]==3)
                                {
                                $cadena_sql.="'02' " ;
                                }
                        else
                                {
                                $cadena_sql.="'01' " ;
                                }
			$cadena_sql.=") ";
			
			//echo $cadena_sql."<br><br>";
			//exit;
			break;
	
	
			//Con esta cadena se pueden obtener las consultas para subir manualmente algunos registros
			//echo "ano=".$valor[0][9]."<br>";

			
						//Consulta de la tabla admitido del SNIES LOCAL
		
			case "totalProgramasMatricula":
					$cadena_sql="Select ";			
					$cadena_sql.="prog_annio, ";
					$cadena_sql.="prog_semestre, ";					//[0]
					$cadena_sql.="count (*)  ";			
					$cadena_sql.="from  programa_h ";
					if($valor!="")
						{$cadena_sql.="where ";
						 $cadena_sql.="prog_annio=";
						 $cadena_sql.="'".$valor[0]."' ";		
						 $cadena_sql.="AND prog_semestre=";
						 if($valor[1]==2 || $valor[1]==3)
							{
							$cadena_sql.="'02' " ;
							}
						 else		
							{
							$cadena_sql.="'01' " ;
							}
						}
					$cadena_sql.="group by prog_semestre, prog_annio ";
					$cadena_sql.="order by prog_annio DESC, prog_semestre DESC";
					
					//echo $cadena_sql;
					//exit;
		
					
			        break;
			case "cubrimiento":
					$cadena_sql="Select ";
					$cadena_sql.="count (*)  ";
					$cadena_sql.="from  cubrimiento_programa ";
					$cadena_sql.="WHERE ";
					$cadena_sql.="annio='".$valor[0]."' " ;
					$cadena_sql.="AND semestre=" ;
					if($valor[1]==2 || $valor[1]==3)
								{
								$cadena_sql.="'02' " ;
								}
						else
								{
								$cadena_sql.="'01' " ;
								}

					$cadena_sql.="AND pro_consecutivo='".$valor[2]."' " ;

					//echo $cadena_sql;
					//exit;

				break;


			case "insertarCubrimiento":
					$cadena_sql="INSERT INTO ";
					$cadena_sql.="cubrimiento_programa ";
					$cadena_sql.="VALUES ";
					$cadena_sql.="(";
					$cadena_sql.="'1301', ";
					$cadena_sql.="'".$valor[0]."', ";
					if($valor[1]==2 || $valor[1]==3)
								{
								$cadena_sql.="'02', " ;
								}
						else
								{
								$cadena_sql.="'01', " ;
								}
					$cadena_sql.="'01', ";
					$cadena_sql.="'11', ";
					$cadena_sql.="'11001', ";
					$cadena_sql.="'1301', ";
					$cadena_sql.="'01', ";
					$cadena_sql.="'".$valor[2]."'";
					$cadena_sql.=") ";
					//echo $cadena_sql."<br><br>";
					//exit;
		        break;
				
			default:
			$cadena_sql="";
			break;
			
		
	}
	//echo $cadena_sql."<br>";
	//exit;
	return $cadena_sql;
}

function ejecutar_admin_admitido($cadena_sql,$acceso_db,$tipo="")
{
	
	switch($tipo)
	{
		case "":
		$acceso_db->registro_db($cadena_sql,0);
		$registro=$acceso_db->obtener_registro_db();
		//echo $registro[0][0]."<br>";
		return $registro;
		
		case "insertar":
		return $acceso_db->ejecutar_acceso_db($cadena_sql);
		
		case "error":
			return $acceso_db->obtener_error();
	}
}

?>
