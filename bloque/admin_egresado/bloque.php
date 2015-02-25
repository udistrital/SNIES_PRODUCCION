<?
/*
############################################################################
#    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        #
#    Copyright: Vea el archivo LICENCIA.txt que viene con la distribucion  #
############################################################################
Bloque que actualiza los Egresados en el snies local

*****************************************************************************
*modificaciones
-----------------------------------------------
|  Jairo lavado Hernández  | Junio 18 de 2009 |
-----------------------------------------------
|  Jairo lavado Hernández  | Junio 19 de 2009 |
-----------------------------------------------
*/
/****************************************************************************
* @name          bloque.php 
* @revision     Ultima revision 10 de junio de 2008
*****************************************************************************
* @subpackage   admin_egresado
* @package	bloques
* @copyright    
* @version      0.3
* @link		N/D
* @description  Bloque principal para la administracion de egresados
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

//$bar = new barraProgreso($message='', $hide=true, $sleepOnFinish=0, $barLength=200, $precision=20,$backgroundColor='#cccccc', $foregroundColor='blue');

//Pagina a donde direcciona el menu
$pagina="admin_egresado";

$conexion=new dbConexion($configuracion);

$acceso_db=$conexion->recursodb($configuracion,"oracle2");
$enlace=$acceso_db->conectar_db();

$accesoSnies=$conexion->recursodb($configuracion,"sniesLocal");
$enlaceBlade1=$accesoSnies->conectar_db();
$log_us=new log();
//echo $enlace."<br>";
//echo $enlaceAcademica."<br>";
//echo $enlaceBlade1."<br>";
//exit;

if($enlace && $enlaceBlade1)
{

		if(isset($_REQUEST["annio"]) and isset($_REQUEST["periodo"]))
			{
				//echo $_REQUEST["periodo"];exit;
				
				$valor[0]=$_REQUEST["annio"];
				$valor[1]=$_REQUEST["periodo"];
				//variable para verificar cubrimiento programa
				$cubrimiento[0]=$valor[0];//anno
				$cubrimiento[1]=$valor[1];//periodo
				$participante=0;
				$estudiante=0;
				$egresado=0;
				//toma los totales iniciales de los registros
				$cadena_sql=cadena_busqueda_egresado($configuracion, $accesoSnies,$valor,"totalEgresadosSnies");
				//echo $cadena_sql; 
				$inicialEgresados=ejecutar_admin_egresado($cadena_sql,$accesoSnies);
				
				$cadena_sql=cadena_busqueda_egresado($configuracion, $accesoSnies,"","totalParticipanteSnies");
				//echo $cadena_sql; 
				$inicialParticipante=ejecutar_admin_egresado($cadena_sql,$accesoSnies);
				
				$cadena_sql=cadena_busqueda_egresado($configuracion, $accesoSnies,"","totalEstudianteSnies");
				//echo $cadena_sql; 
				$inicialEstudiante=ejecutar_admin_egresado($cadena_sql,$accesoSnies);
				
	
				?>
			<table width="100%" align="center" border="0" cellpadding="10" cellspacing="0" >
				<tbody>
					<tr>
						<td >
							<table width="100%" border="0" align="center" cellpadding="5 px" cellspacing="1px" >
								<tr class="centralcuerpo">
									<td>
									.::: Actualizaci&oacute;n Egresados <? echo $valor[0]." - ".$valor[1];?>
									</td>
								</tr>	
							</table>
						</td>
					</tr>
				</tbody>
			</table>
						
			
						<?		
				unset($egresado_borrar);			
				
				//Rescatar TODOS los egresados de un periodo y anno especifico
				
				$cadena_sql=cadena_busqueda_egresado($configuracion, $acceso_db, $valor,"egresado");	
			//	echo $cadena_sql;
			//exit;
				$registro=ejecutar_admin_egresado($cadena_sql,$acceso_db);
				
				
				
				if(!is_array($registro))
				{	
					$cadena=htmlentities($cadena, ENT_COMPAT, "UTF-8");
					alerta::sin_registro($configuracion,$cadena);	
				}
				else
				{
					$contar=count($registro);		
					
					//$bar->initialize($contar);
					for($contador=0;$contador<$contar;$contador++)
					{
						
						//$bar->increase();
					
						//se selecciona cada registro de graduados, uno a la vez	
						unset($valor);
						unset($unError);
						$valor[2]=$registro[$contador][4];			//documento de identidad
						$valor[3]=$registro[$contador][10];			//codigo del estudiante
						
                                                $registroPrincipal[0]=$registro[$contador];		//Que contiene los datos de la busqueda inicial
                        			echo "<hr>".$contador." - Estudiante : ".$registroPrincipal[0][8]." ".$registroPrincipal[0][0]." => cod: ".$registroPrincipal[0][10]." ";
                                                echo "<br>proyecto : ".$registroPrincipal[0][11]." ";

						//exit;
					
					//$bar->increase();
					
						
						if($valor[2]!="" && $valor[3]!="" )
						{	
							//revisa si el programa que esta inscrito el estudiante, esta en la tabla cubrimiento programa
							
							$cubrimiento[2]=$registro[$contador][9];//codigo del programa snies
							
							$cadena_sql=cadena_busqueda_egresado($configuracion, $accesoSnies, $cubrimiento,"cubrimiento");
							//echo "<br>".$cadena_sql;//exit;
						    $cubrimientoPrograma=ejecutar_admin_egresado($cadena_sql,$accesoSnies);
						    
							if($cubrimientoPrograma[0][0]==0)
								{
									$cadena_sql=cadena_busqueda_egresado($configuracion, $accesoSnies, $cubrimiento,"insertarCubrimiento");
									//echo "<br>".$cadena_sql;//exit;
						    		$Programa=ejecutar_admin_egresado($cadena_sql,$accesoSnies);
								}//termina cubrimiento programa
									
							//ACTUALIZAR PARTICIPANTE
							
							//buscar los registros de participantes en el SNIES
							$cadena_sql=cadena_busqueda_egresado($configuracion, $accesoSnies, $valor,"participanteSnies");
							$cadena_sql.=" AND tipo_doc_unico='".$registro[$contador][8]."'";//tipo documento			
							//echo $cadena_sql;
							@$registroParticipanteSnies=ejecutar_admin_egresado($cadena_sql,$accesoSnies);
			
							
							//Verificar que el egresado no esta en la tabla participante del SNIES <<EXISTE EL PARTICIPANTE=NO>> 
							
							if(!is_array($registroParticipanteSnies))
							
							{	
								/*/Buscar los datos en la DB Academica de ese registro del egresado para la tabla participante
								$cadena_sql=cadena_busqueda_egresado($configuracion, $acceso_db, $valor,"participanteAcademica");		
								echo 	$cadena_sql;		
								$registroDatosParticipante=ejecutar_admin_egresado($cadena_sql,$acceso_db);
								
								//echo "*El Egresado con documento = <span class='texto_negrita'>".$valor."</span> no existe en la tabla PARTICIPANTE<br>";
								$participante++;
								
								//insertar los datos del egresado en la tabla participante del SNIES
														
									$cadena_sql=cadena_busqueda_egresado($configuracion, $accesoSnies, $registroDatosParticipante,"insertarParticipante");
									echo 	$cadena_sql;		
									//$resultadoParticipante=ejecutar_admin_egresado($cadena_sql,$accesoSnies,"insertar");
									if($resultadoParticipante==false)
									{
										$resultadoParticipante=ejecutar_admin_egresado($cadena_sql,$accesoSnies,"error");
										echo "El registro no pudo ser cargado en la tabla participante por:".$resultadoParticipante."<br>";
										$unError=true;
										//echo "El registro no pudo ser cargado, por favor verifique los datos en la Aplicaci&oacute;n Acad&eacute;mica <br>";
										//echo "<hr class='hr_subtitulo'>";
										
									}		*/
								
								//Buscar los datos en la DB Academica de ese registro del egresado para la tabla participante
								$cadena_sql=cadena_busqueda_egresado($configuracion, $acceso_db, $registroPrincipal,"participanteAcademica");
								//echo 	$cadena_sql;		exit;
								@$registroDatosParticipante=ejecutar_admin_egresado($cadena_sql,$acceso_db);
								
								//si no se obtiene un arreglo de la base de datos académica se debe saltar el registro
										if(!is_array($registroDatosParticipante))
											{
											//echo "esto es un :".$registroDatosParticipante;
											//echo "eso no esun arreglo continua con el siguiente registro<br><br>";
											echo "<font color='red'><br>*El Egresado con documento = <span class='texto_negrita'>".$valor[2]."</span> no existe en la tabla PARTICIPANTE<br></font>";
                                                                                											
											}
										
										else{
											//verifica si hay un registro ya egresado del participante
											$cadena_sql=cadena_busqueda_egresado($configuracion, $accesoSnies, $valor,"participanteSnies");	
											//echo "<br><br>".$cadena_sql;
											@$ParticipanteSnies=ejecutar_admin_egresado($cadena_sql,$accesoSnies);
																						
											if(is_array($ParticipanteSnies) && $ParticipanteSnies[0][12]!=$registro[$contador][8])
													{
													//Buscar los datos en la DB Academica de ese registro del graduado para la tabla estudiante
														$egresado_borrar[1]=$ParticipanteSnies[0][13];//numero documento
														$egresado_borrar[2]=$ParticipanteSnies[0][12];//tipo documento
													}
											
											//verifica que el tipo de documento a cargar sea el mismo si no lo unifica
											if ($registroDatosParticipante[0][12]!=$registro[$contador][8])
													{$registroDatosParticipante[0][12]=$registro[$contador][8];}
													
											//insertar los datos del graduado en la tabla participante del SNIES					
											$cadena_sql=cadena_busqueda_egresado($configuracion, $accesoSnies, $registroDatosParticipante,"insertarParticipante");
											//echo $cadena_sql;		exit;
											@$resultadoParticipante=ejecutar_admin_egresado($cadena_sql,$accesoSnies,"insertar");
											if($resultadoParticipante==false)
											{
												$resultadoParticipante=ejecutar_admin_egresado($cadena_sql,$accesoSnies,"error");
												echo "<font color='red'><br>El registro participante no pudo ser cargado en la tabla participante por:".$resultadoParticipante."<br></font>";
                                                                                		$unError=true;
												//echo "El registro no pudo ser cargado, por favor verifique los datos en la Aplicaci&oacute;n Acad&eacute;mica <br>";
												//echo "<hr class='hr_subtitulo'>";
												}
											else{
                                                                                             echo "<font color='green'><br>El registro fue guardado con exito, como Participantes</font>";
                                                                                             $participante++;}
											}	
									
									
									
							}//fin actualizar participante
						
						
							// ACTUALIZAR ESTUDIANTE
							if(!isset($unError))
							{
									unset($unError);
									
									//buscar los registros de estudiantes en el SNIES
									
									$cadena_sql=cadena_busqueda_egresado($configuracion, $accesoSnies, $valor,"estudianteSnies");
									$cadena_sql.=" AND tipo_doc_unico='".$registro[$contador][8]."'";
									//echo  $cadena_sql;
									$registroEstudianteSnies=ejecutar_admin_egresado($cadena_sql,$accesoSnies);
									
									//Verificar que el graduado no esta en la tabla participante del SNIES <<EXISTE EL PARTICIPANTE=NO>> 
							
									if(!is_array($registroEstudianteSnies))
												{	
													//Buscar los datos en la DB Academica de ese registro del graduado para la tabla estudiante
													$cadena_sql=cadena_busqueda_egresado($configuracion, $acceso_db, $valor,"estudianteAcademica");
													//echo "<br>".$cadena_sql;
													$registroDatosEstudiante=ejecutar_admin_egresado($cadena_sql,$acceso_db);
													
													//si no se obtiene un arreglo de estudiante de la academica se ignora
													if(!is_array($registroDatosEstudiante)){
													     echo "<font color='red'><br>*El Egresado con documento = <span class='texto_negrita'>".$valor[2]."</span> no existe en la tabla ESTUDIANTE<br></font>";
                                                                                                             
														}
													else{
														
														//verifica si hay un registro ya guardado del participante
														$cadena_sql=cadena_busqueda_egresado($configuracion, $accesoSnies, $valor,"estudianteSnies");
														//echo $cadena_sql;exit;
														$EstudianteSnies=ejecutar_admin_egresado($cadena_sql,$accesoSnies);
														
														if(is_array($EstudianteSnies) && $EstudianteSnies[0][2]!=$registro[$contador][8])
																{
																//Buscar los datos en la DB Academica de ese registro del graduado para la tabla estudiante
																	$egresado_borrar[1]=$EstudianteSnies[0][1];//numero documento
																	$egresado_borrar[2]=$EstudianteSnies[0][2];//tipo documento
																	
																	//echo $egresado_borrar[1]." ".$egresado_borrar[2];
																}
														
														//verifica que el tipo de documento a cargar sea el mismo si no lo unifica
															if ($registroDatosEstudiante[0][2]!=$registro[$contador][8])
																	{$registroDatosEstudiante[0][2]=$registro[$contador][8];}
												
												 		//insertar los datos del graduado en la tabla estudiante del SNIES
														$cadena_sql=cadena_busqueda_egresado($configuracion, $accesoSnies, $registroDatosEstudiante,"insertarEstudiante");
														//echo "<br>".$cadena_sql;		exit;
														@$resultadoEstudiante=ejecutar_admin_egresado($cadena_sql,$accesoSnies,"insertar");
														if($resultadoEstudiante==false)
														{
															@$resultadoEstudiante=ejecutar_admin_egresado($cadena_sql,$accesoSnies,"error");
															echo "<font color='red'><br>El registro estudiante no pudo ser cargado por:".$resultadoEstudiante."<br></font>";
                                                                                                                        
															$unError=true;
															//echo "El registro no pudo ser cargado, por favor verifique los datos en la Aplicaci&oacute;n Acad&eacute;mica <br>";
															//echo "<hr class='hr_subtitulo'>";
														}
														else{
                                                                                                                    echo "<font color='green'><br>El registro fue guardado con exito, como Estudiante<br></font>";
                                                                                                                     $estudiante++;}
														}		
												
												}
					
									}//fin actualizar estudiante
									
							// ACTUALIZAR EGRESADO
							if(!isset($unError))
							   {
									unset($unError);
									
																
										//buscar los registros de estudiantes en el SNIES
											$cadena_sql=cadena_busqueda_egresado($configuracion, $accesoSnies, $valor,"egresadoSnies");	
											$cadena_sql.=" AND tipo_doc_unico='".$registro[$contador][8]."'";	
											//echo "<br><br>".$cadena_sql;		//exit;
											
											$registroEgresado=ejecutar_admin_egresado($cadena_sql,$accesoSnies);
																		
											//Verificar que el egresado no esta en la tabla EGRESADO del SNIES <<EXISTE EL EGRESADO=NO>> 
											
											if(!is_array($registroEgresado))
											
											{	//echo "*El Graduado con documento = <span class='texto_negrita'>".$valor."</span> no existe en la tabla GRADUADO (actualice la p&aacute;gina para cargarlo)<br>";
												
												//verifica si hay un registro ya del egresado
												$cadena_sql=cadena_busqueda_egresado($configuracion, $accesoSnies, $valor,"egresadoSnies");	
												//echo "<br>".$cadena_sql;		exit;
												$EgresadoSnies=ejecutar_admin_egresado($cadena_sql,$accesoSnies);
												
												if(is_array($EgresadoSnies) && $EgresadoSnies[0][4]!=$registro[$contador][8])
														{  
														//Buscar los datos en la DB Academica de ese registro del graduado para la tabla estudiante
															$egresado_borrar[1]=$EgresadoSnies[0][3];
															$egresado_borrar[2]=$EgresadoSnies[0][4];
														//	echo $egresado_borrar[1]." ".$egresado_borrar[2];
														}
										
												//insertar los datos del graduado en la tabla estudiante del SNIES
																							
													$miRegistro[0]=$registro[$contador];
													$cadena_sql=cadena_busqueda_egresado($configuracion, $accesoSnies, $miRegistro,"insertarEgresado");
													//echo "<br><br>".$cadena_sql;   //exit;
													$resultadoEgresado=ejecutar_admin_egresado($cadena_sql,$accesoSnies,"insertar");
													if($resultadoEgresado==false)
														{
															$resultadoEgresado=ejecutar_admin_egresado($cadena_sql,$accesoSnies,"error");
															echo "<font color='red'><br>El registro graduado no pudo ser cargado por:".$resultadoEgresado."<br></font>";
                                                                                                                        
															//echo "El registro no pudo ser cargado, por favor verifique los datos en la Aplicaci&oacute;n Acad&eacute;mica <br>";
															//echo "<hr class='hr_subtitulo'>";
														}
													else
														 {
                                                                                                                  echo "<font color='green'><br>El registro fue guardado con exito, como Egresado<br></font>";   
                                                                                                                     $egresado++;
														 }		
											}
                                                                                    else
                                                                                     {
                                                                                      echo "<font color='blue'><br>El registro ya existe como Egresado<br></font>";   
                                                                                     }    
									//exit;
									
								
							   }//cierra el fi actualizar egresado
							
                                                 }
									
						}//cierra el for de registro
						
					}		
					
				//VARIABLES PARA EL LOG
                                                date_default_timezone_set('UTC');
						$logger[0]="CARGAR AL SNIES LOCAL";
						$logger[1]="Participante";
						$logger[2]=$inicialParticipante[0][0];
						$logger[3]=$participante;
						$logger[4]=date('d/m/Y h:i:s',time());
						$logger[5]=$cubrimiento[0];
						$logger[6]=$cubrimiento[1];
						$logger[7]="Se cargo los datos, para el reporte de egresados al SNIES para el periodo ".$cubrimiento[0]."-".$cubrimiento[1];
						
						$log_us->log_usuario($logger,$configuracion);
						
						$logger[1]="Estudiante";
						$logger[2]=$inicialEstudiante[0][0];
						$logger[3]=$estudiante;
						$log_us->log_usuario($logger,$configuracion);
						
						$logger[1]="Egresado";
						$logger[2]=$inicialEgresados[0][2];
						$logger[3]=$egresado;
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
											<td>PARTICIPANTE</td>
											<td><?	echo $participante;?></td>
										</tr>
										<tr class="bloquecentralcuerpo">
											<td>ESTUDIANTE</td>
											<td><? echo $estudiante;?></td>								
										</tr>
										<tr class="bloquecentralcuerpo">
											<td>EGRESADO</td>
											<td><? echo $egresado;?></td>
										</tr>
										</td>
										</tr>
									</table>
									</td>
								</tr>
							</tbody>
						</table>						
	<?	}//cierra el if que captura año y periodo					
	
			
	
	$cadena_sql=cadena_busqueda_egresado($configuracion, $accesoSnies,"","totalEgresadosSnies");
	//echo $cadena_sql; //exit;
	$registroEgresadosTotales=ejecutar_admin_egresado($cadena_sql,$accesoSnies);
	

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
								.::: Datos Egresados
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
												TOTAL EGRESADOS
												</td>					
											</tr>	
											
											<?$n=0;
										while (count($registroEgresadosTotales)>$n)
											{	
												if (is_array($registroEgresadosTotales[$n]))
												{?>
												
												<tr>
													<td class="cuadro_plano centrar">
													<span class="texto_negrita"><? echo $totalEgresado=$registroEgresadosTotales[$n][0];?></span> 
													</td>
													<td class="cuadro_plano centrar">
													<span class="texto_negrita"><? echo $totalEgresado=$registroEgresadosTotales[$n][1];?></span>
													</td>
													<td class="cuadro_plano centrar centrar">
													<span class="texto_negrita"><? echo $totalEgresado=$registroEgresadosTotales[$n][2];?></span>
													</td>
												</tr>
												<?
												$n=$n+1;
												}
											}	?>
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

function cadena_busqueda_egresado($configuracion, $acceso_db, $valor,$opcion="")
{
	//$valor=$acceso_db->verificar_variables($valor);
	
	switch($opcion)
	{
	
		//consulta para generar los datos para la tabla egresados del SNIES a partir de la DB Academica
		/*
		case "egresado":	
			$prefijo="mntac.";			
			$cadena_sql="SELECT UNIQUE ";
			$cadena_sql.="est_nro_iden, ";
			$cadena_sql.="'1301' ies_cod, ";
			$cadena_sql.="max(not_ano) ins_annio, ";
			$cadena_sql.="DECODE(max(not_per),1,'01',3,'02') ins_semestre, ";
			$cadena_sql.="est_nro_iden codigo_unico, ";
			$cadena_sql.="'11' departamento, ";
			$cadena_sql.="'11001' municipio, ";
			$cadena_sql.="'1301' codigo_ent_aula, ";
			$cadena_sql.="TO_CHAR(DECODE(est_tipo_iden,'C','CC','T','TI','CC')) tipo_doc_unico, ";
			$cadena_sql.="as_cra_cod_snies ";
			//$cadena_sql.="est_cod ";
			$cadena_sql.="from mntac.acnot, mntac.acest, mntac.accra_snies ";
			$cadena_sql.="where est_cod=not_est_cod ";
			$cadena_sql.="and as_cra_cod=est_cra_cod ";
			$cadena_sql.="and est_estado_est='T' ";
			$cadena_sql.="and not_ano=";
			$cadena_sql.=$valor[0];
			$cadena_sql.=" group by est_nro_iden, est_tipo_iden, est_cra_cod, as_cra_cod_snies, est_cod ";
							
			//echo $cadena_sql;
			//exit;
			
			break;*/
			
			
		case "egresado":	
			$prefijo="mntac.";			
			$cadena_sql="SELECT UNIQUE ";
			$cadena_sql.="est_nro_iden, ";
			$cadena_sql.="'1301' ies_cod, ";
			$cadena_sql.="max(not_ano) ins_annio, ";
			$cadena_sql.="DECODE(not_per,1,'01',2,'02',3,'02') ins_semestre, ";
			$cadena_sql.="est_nro_iden codigo_unico, ";
			$cadena_sql.="'11' departamento, ";
			$cadena_sql.="'11001' municipio, ";
			$cadena_sql.="'1301' codigo_ent_aula, ";
                        $cadena_sql.="TO_CHAR(DECODE(est_tipo_iden,'',DECODE(length(est_nro_iden),11,'TI',12,'TI','CC'),'C', 'CC', '1', 'CC', 'c', 'CC', 'T', 'TI', '2', 'TI', 't', 'TI', 'E', 'CE', 'P', 'PS', 'CC')) tipo_doc_unico, ";
			//$cadena_sql.="TO_CHAR(DECODE(est_tipo_iden,'C','CC','T','TI','CC')) tipo_doc_unico, ";
			$cadena_sql.="as_cra_cod_snies, ";
			$cadena_sql.="est_cod, ";
                        $cadena_sql.=" as_cra_nom ";
			$cadena_sql.="FROM ";
			$cadena_sql.=$prefijo."accra_snies ";
			$cadena_sql.="INNER JOIN ".$prefijo."acest ON as_cra_cod = est_cra_cod  ";
			$cadena_sql.="INNER JOIN ".$prefijo."accra ON as_cra_cod = cra_cod ";
			$cadena_sql.="INNER JOIN ".$prefijo."actipcra ON cra_tip_cra = tra_cod ";
			$cadena_sql.="INNER JOIN ".$prefijo."acnot ON not_est_cod = est_cod AND not_cra_cod=est_cra_cod ";
			$cadena_sql.="WHERE ";
			$cadena_sql.="est_estado_est IN ('H','T','E') ";
			//$cadena_sql.="AND tra_nivel = 'PREGRADO' ";
			$cadena_sql.="GROUP BY est_nro_iden,not_per, est_tipo_iden, est_cra_cod, as_cra_cod_snies, est_cod, as_cra_nom  ";
			$cadena_sql.="HAVING MAX(not_ano||not_per) = ";
			$cadena_sql.="'".$valor[0].$valor[1]."'";
			//echo $cadena_sql;
			//exit;
			
			break;	
			
		//Consulta de la tabla participante del SNIES LOCAL

			case "participanteSnies":
			$cadena_sql="SELECT ";
			$cadena_sql.="ies_code, ";					//[0]
			$cadena_sql.="primer_apellido, ";
			$cadena_sql.="segundo_apellido,";
			$cadena_sql.="primer_nombre,";
			$cadena_sql.="segundo_nombre, ";
			$cadena_sql.="fecha_nacim, ";				//[5]
			$cadena_sql.="pais_ln, ";
			$cadena_sql.="departamento_ln, ";
			$cadena_sql.="municipio_ln, ";
			$cadena_sql.="genero_code, ";
			$cadena_sql.="email, ";
			$cadena_sql.="est_civil_code, ";
			$cadena_sql.="tipo_doc_unico, ";
			$cadena_sql.="codigo_unico, ";  			//[13]
			$cadena_sql.="tipo_id_ant, ";
			$cadena_sql.="codigo_id_ant, ";
			$cadena_sql.="pais_tel, ";
			$cadena_sql.="area_tel, ";
			$cadena_sql.="numero_tel ";
			$cadena_sql.="FROM participante ";
			$cadena_sql.="WHERE ";
			$cadena_sql.="codigo_unico=".$valor[2]." " ;
			
			//echo "<br>".$cadena_sql;
			//exit;
			
			break;
			
			
		/*/Consulta de la DB Academica para generar los datos de la tabla participante del SNIES para los egresados
		case "participanteAcademica":
			$prefijo="mntac.";	 
			$cadena_sql="SELECT ";
			$cadena_sql.="TO_CHAR('1301') ies_code, ";
			$cadena_sql.="DECODE((SUBSTR(trim(est_nombre),1,INSTR(trim(est_nombre),' ',1,2)-1)),NULL,est_nombre, (SUBSTR(trim(est_nombre),1,INSTR(trim(est_nombre),' ',1,2)-1)))primer_apellido, ";
			$cadena_sql.="SUBSTR(trim(est_nombre),1,INSTR(trim(est_nombre),' ',1,2)-1) segundo_apellido,";
			$cadena_sql.="SUBSTR(trim(est_nombre),INSTR(trim(est_nombre),' ',1,2)+1) primer_nombre,";
			$cadena_sql.="SUBSTR(trim(est_nombre),INSTR(trim(est_nombre),' ',1,2)+1) segundo_nombre, ";
			$cadena_sql.="TO_CHAR(eot_fecha_nac, 'yyyy-mm-dd') fecha_nacim, ";
			$cadena_sql.="TO_CHAR('CO') pais_ln, ";
			$cadena_sql.="TO_CHAR(SUBSTR(lug_cod,1,2)) departamento_ln, ";
			$cadena_sql.="TO_CHAR(SUBSTR(lug_cod,2,2)||SUBSTR(lug_cod,5,3)) municipio_ln, ";
			$cadena_sql.="TO_CHAR(DECODE(est_sexo,'M','01','F','02','01')) genero_code, ";
			$cadena_sql.="eot_email email, ";
			$cadena_sql.="DECODE(eot_estado_civil,1,'01',2,'02',3,'05',4,'03',5,'04', '01') est_civil_code, ";
			$cadena_sql.="TO_CHAR(DECODE(est_tipo_iden,'C','CC','T','TI','CC')) tipo_doc_unico, ";
			$cadena_sql.="TO_CHAR(est_nro_iden) codigo_unico, ";
			$cadena_sql.="'CC' tipo_id_ant, ";
			$cadena_sql.="' ' codigo_id_ant, ";
			$cadena_sql.="'57' pais_tel, ";
			$cadena_sql.="'1' area_tel, ";
			$cadena_sql.="TO_CHAR(est_telefono) numero_tel ";
			$cadena_sql.="FROM ".$prefijo."acest a, ".$prefijo."acestotr, ".$prefijo."gelugar ";
			$cadena_sql.="WHERE ";
			$cadena_sql.="est_nro_iden=".$valor." " ;
			//$cadena_sql.="AND est_estado IN ('A','B','H','L','T','E','R') ";   // ver tabla mntac.acestado
			$cadena_sql.="AND est_cod = eot_cod ";
			$cadena_sql.="AND lug_cod = eot_cod_lug_nac ";
		
			//echo $valor."<br>";
			//exit;
			
			break;*/
			
		//Consulta de la DB Academica para generar los datos de la tabla participante del SNIES para los graduados
		case "participanteAcademica":
			/*$prefijo="mntac.";
			$cadena_sql="SELECT ";
			$cadena_sql.="TO_CHAR('1301') ies_code, ";
			$cadena_sql.="DECODE((SUBSTR(trim(est_nombre),1,INSTR(trim(est_nombre),' ',1,2)-1)),NULL,est_nombre, (SUBSTR(trim(est_nombre),1,INSTR(trim(est_nombre),' ',1,2)-1)))primer_apellido, ";
			$cadena_sql.="SUBSTR(trim(est_nombre),1,INSTR(trim(est_nombre),' ',1,2)-1) segundo_apellido,";
			$cadena_sql.="SUBSTR(trim(est_nombre),INSTR(trim(est_nombre),' ',1,2)+1) primer_nombre,";
			$cadena_sql.="SUBSTR(trim(est_nombre),INSTR(trim(est_nombre),' ',1,2)+1) segundo_nombre, ";
			$cadena_sql.="TO_CHAR(eot_fecha_nac, 'yyyy-mm-dd') fecha_nacim, ";
			$cadena_sql.="TO_CHAR('CO') pais_ln, ";
			$cadena_sql.="TO_CHAR(decode (eot_cod_dep_nac,0,11,'',11, eot_cod_dep_nac)) departamento_ln, ";
			//$cadena_sql.="TO_CHAR(eot_cod_dep_nac) departamento_ln, ";
			//$cadena_sql.="TO_CHAR(eot_cod_mun_nac) municipio_ln, ";
			$cadena_sql.="TO_CHAR(DECODE(est_sexo,'M','01','F','02','01')) genero_code, ";
			$cadena_sql.="eot_email email, ";
			$cadena_sql.="DECODE(eot_estado_civil,1,'01',2,'02',3,'05',4,'03',5,'04', '01') est_civil_code, ";
			$cadena_sql.="DECODE(est_tipo_iden,'',DECODE(length(est_nro_iden),11,'TI','CC'),'C','CC','T','TI','c','CC','I','TI','t','TI','1','CC','2','TI',est_tipo_iden) tipo_doc_unico, ";
			$cadena_sql.="TO_CHAR(est_nro_iden) codigo_unico, ";
			$cadena_sql.="DECODE(est_tipo_iden,'',DECODE(length(est_nro_iden),11,'TI','CC'),'C','CC','T','TI','c','CC','I','TI','t','TI','1','CC','2','TI',est_tipo_iden) tipo_id_ant, ";
			$cadena_sql.="' ' codigo_id_ant, ";
			$cadena_sql.="'57' pais_tel, ";
			$cadena_sql.="'1' area_tel, ";
			$cadena_sql.="TO_CHAR(est_telefono) numero_tel ";
			$cadena_sql.="FROM ".$prefijo."acest ";
			$cadena_sql.="INNER JOIN ".$prefijo."acestotr ON est_cod = eot_cod ";
			$cadena_sql.="WHERE ";
			$cadena_sql.="est_cod=".$valor[3]." " ;
			$cadena_sql.="AND est_estado IN ('A','B','H','L','T','E','R') ";   */
					
			//echo $valor."<br>";
			//echo $cadena_sq."<br><br>";
			//exit;

                        /* octubre 28 de 2008 se modifica query para resolver los problemas hallados en la auditoria del snies
                 * la fecha del 1980-02-29 se coloca, puesto que la fecha del estudiante se encuentra en blanco
                 */
			$cadena_sql="SELECT ";
                        $cadena_sql.="TO_CHAR('1301') ies_code, ";
                        $cadena_sql.="SUBSTR(trim(est_nombre),0,INSTR(trim(est_nombre),' ',1,1)) primer_apellido, ";
                        $cadena_sql.="SUBSTR(trim(est_nombre),INSTR(trim(est_nombre),' ',1,1) +1 ,INSTR(trim(est_nombre),' ',1,2) - INSTR(trim(est_nombre),' ',1,1)) segundo_apellido, ";
                        $cadena_sql.="(case when INSTR(trim(est_nombre),' ',1,3)='0' AND INSTR(trim(est_nombre),' ',1,2)='0'
                              then SUBSTR(trim(est_nombre),instr(trim(est_nombre),' ',1,1) +1,length(trim(est_nombre)) - instr(trim(est_nombre),' ',1,1))
                              when INSTR(trim(est_nombre),' ',1,3)='0' AND INSTR(trim(est_nombre),' ',1,2)>'0'
                              then SUBSTR(trim(est_nombre),instr(trim(est_nombre),' ',1,2) +1,length(trim(est_nombre)) - instr(trim(est_nombre),' ',1,2))
                              else SUBSTR(trim(est_nombre),INSTR(trim(est_nombre),' ',1,2) +1 ,INSTR(trim(est_nombre),' ',1,3) - INSTR(trim(est_nombre),' ',1,2))
                              end) primer_nombre, ";
                        $cadena_sql.="(case when INSTR(trim(est_nombre),' ',1,3)='0'
                              then '  '
                              else SUBSTR(trim(est_nombre),instr(trim(est_nombre),' ',1,3) +1,length(est_nombre) - instr(est_nombre,' ',1,3))
                              end) segundo_nombre, ";
                        $cadena_sql.="decode (eot_fecha_nac,'0','1980-02-29','','1980-02-29', TO_CHAR(eot_fecha_nac, 'yyyy-mm-dd'))  fecha_nacim, ";
                        $cadena_sql.="TO_CHAR('CO') pais_ln, ";
                        $cadena_sql.="TO_CHAR(decode (mun_dep_cod,0,11,'',11, mun_dep_cod)) departamento_ln, ";
                        $cadena_sql.="TO_CHAR(decode (mun_cod,0,11001,'',11001, mun_cod)) municipio_ln, ";
                        $cadena_sql.="TO_CHAR(DECODE(est_sexo,'M','01','F','02','01')) genero_code, ";
                        $cadena_sql.="eot_email email, ";
                        $cadena_sql.="DECODE(eot_estado_civil,1,'01',2,'02',3,'05',4,'03',5,'04', '01') est_civil_code, ";
                        $cadena_sql.="TO_CHAR(DECODE(est_tipo_iden,'',DECODE(length(est_nro_iden),11,'TI',12,'TI','CC'),'C', 'CC', '1', 'CC', 'c', 'CC', 'T', 'TI', '2', 'TI', 't', 'TI', 'E', 'CE', 'P', 'PS', 'CC')) tipo_doc_unico, ";
                        $cadena_sql.="TO_CHAR(est_nro_iden) codigo_unico, ";
                        $cadena_sql.="TO_CHAR(DECODE(est_tipo_iden,'',DECODE(length(est_nro_iden),11,'TI',12,'TI','CC'),'C', 'CC', '1', 'CC', 'c', 'CC', 'T', 'TI', '2', 'TI', 't', 'TI', 'E', 'CE', 'P', 'PS', 'CC')) tipo_id_ant, ";
                        $cadena_sql.="' ' codigo_id_ant, ";
                        $cadena_sql.="'57' pais_tel, ";
                        $cadena_sql.="'1' area_tel ";
                        $cadena_sql.="FROM mntac.acest ";
                        $cadena_sql.="INNER JOIN mntac.acestotr ON est_cod = eot_cod ";
                        $cadena_sql.="INNER JOIN mntge.gemunicipio ON mun_cod=decode(eot_cod_mun_nac,0,11001,'',11001,eot_cod_mun_nac) ";
                        $cadena_sql.="WHERE ";
                        $cadena_sql.="est_nro_iden='".$valor[0][0]."' ";
                        $cadena_sql.="ORDER BY est_cod DESC";
			
			break;	
			
			
		//Consulta de la DB Academica para generar los datos de la tabla participante del SNIES para los egresados
		
		case "insertarParticipante":
			$cadena_sql="INSERT INTO ";
			$cadena_sql.="participante ";
			$cadena_sql.="VALUES ";
			$cadena_sql.="( ";
			$cadena_sql.="'".$valor[0][0]."', ";
			$cadena_sql.="'".$valor[0][1]."', ";
			$cadena_sql.="'".$valor[0][2]."', ";
			$cadena_sql.="'".$valor[0][3]."', ";
			$cadena_sql.="'".$valor[0][4]."', ";
			if ($valor[0][5]!=""){
			$cadena_sql.="'".$valor[0][5]."', ";
			}
			else
			{
			$cadena_sql.="'1980-02-29', ";
			}
			$cadena_sql.="'".$valor[0][6]."', ";
			$cadena_sql.="'".$valor[0][7]."', ";
			$cadena_sql.="'".$valor[0][8]."', ";
			$cadena_sql.="'".$valor[0][9]."', ";
			$cadena_sql.="'".$valor[0][10]."', ";
			$cadena_sql.="'".$valor[0][11]."', ";
			$cadena_sql.="'".$valor[0][12]."', ";
			$cadena_sql.="'".$valor[0][13]."', ";
			$cadena_sql.="'".$valor[0][14]."', ";
			if ($valor[0][15]!=""){
			$cadena_sql.="'".$valor[0][15]."', ";
			}
			else
			{
			$cadena_sql.="'', ";
			}
			$cadena_sql.="'".$valor[0][16]."', ";
			$cadena_sql.="'".$valor[0][17]."', ";
			if ($valor[0][18]!=""){
			$cadena_sql.="'".$valor[0][18]."'";
			}
			else
			{
			$cadena_sql.="'0' ";
			}
			$cadena_sql.=") ";
	
			//Con esta cadena se pueden obtener las consultas para subir manualmente algunos registros
			//echo $cadena_sql."<br><br>";
			//echo "tel=-".$valor[0][12]."-";
			//exit;
			
	        break;
			
			case "borraParticipante":
				$cadena_sql="DELETE ";
				$cadena_sql.="FROM ";
				$cadena_sql.="participante ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="codigo_unico=";			//numero doc
				$cadena_sql.="'".$valor[1]."' ";		//tipo doc
				$cadena_sql.="AND tipo_doc_unico=";
				$cadena_sql.="'".$valor[2]."'";
				//echo $cadena_sql."<br>";
				//exit;
			
	        break;        
			        
			//Consulta de la tabla estudiante del SNIES LOCAL
		
			case "estudianteSnies":
				$cadena_sql="SELECT ";
				$cadena_sql.="ies_code, ";					//[0]
				$cadena_sql.="codigo_unico, ";
				$cadena_sql.="tipo_doc_unico ";
				$cadena_sql.="FROM estudiante ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="codigo_unico=".$valor[2]." " ;
				//$cadena_sql.="codigo_unico=".$valor." " ;
				
				//echo $cadena_sql;
				//exit;	
			break;
		
			/*
			case "estudianteAcademica":
			$prefijo="mntac.";	 
			$cadena_sql="SELECT UNIQUE ";
			$cadena_sql.="TO_CHAR('1301') ies_code, ";
			$cadena_sql.="TO_CHAR(est_nro_iden) codigo_unico, ";
			$cadena_sql.="TO_CHAR(DECODE(est_tipo_iden,'C','CC','T','TI','CC')) tipo_doc_unico ";
			$cadena_sql.="FROM ".$prefijo."acest a, ".$prefijo."acestotr, ".$prefijo."gelugar ";
			$cadena_sql.="WHERE ";
			$cadena_sql.="est_nro_iden=".$valor." " ;
			$cadena_sql.="AND est_estado_est IN ('A','B','H','L','T','E','R') ";   // ver tabla mntac.acestado
			$cadena_sql.="AND est_cod = eot_cod ";
			$cadena_sql.="AND lug_cod = eot_cod_lug_nac ";
		
			//echo $cadena_sql."<br>";
			//exit;
			
			break;*/
			
			case "estudianteAcademica":
					$prefijo="mntac.";	 
					$cadena_sql="SELECT UNIQUE ";
					$cadena_sql.="TO_CHAR('1301') ies_code, ";
					$cadena_sql.="TO_CHAR(est_nro_iden) codigo_unico, ";
                                        $cadena_sql.="TO_CHAR(DECODE(est_tipo_iden,'',DECODE(length(est_nro_iden),11,'TI',12,'TI','CC'),'C', 'CC', '1', 'CC', 'c', 'CC', 'T', 'TI', '2', 'TI', 't', 'TI', 'E', 'CE', 'P', 'PS', 'CC')) tipo_doc_unico ";
					//$cadena_sql.="DECODE(est_tipo_iden,'',DECODE(length(est_nro_iden),11,'TI','CC'),'C','CC','T','TI','c','CC','I','TI','t','TI','1','CC','2','TI',est_tipo_iden) tipo_doc_unico ";
					$cadena_sql.="FROM ".$prefijo."acest  ";
					$cadena_sql.="INNER JOIN ".$prefijo."acestotr ON est_cod = eot_cod ";
					$cadena_sql.="WHERE ";
					$cadena_sql.="est_cod=".$valor[3]." " ;
					$cadena_sql.="AND est_estado_est IN ('A','B','H','L','T','E','R') ";   // ver tabla mntac.acestado
					
					//echo $cadena_sql."<br>";
					//exit;
			break;
		
		
		
				//Consulta de la DB Academica para generar los datos de la tabla participante del SNIES para los egresados
		
		case "insertarEstudiante":
			$cadena_sql="INSERT INTO ";
			$cadena_sql.="estudiante ";
			$cadena_sql.="VALUES ";
			$cadena_sql.="( ";
			$cadena_sql.=$valor[0][0].", ";		
			$cadena_sql.=$valor[0][1].", ";			//numero doc
			$cadena_sql.="'".$valor[0][2]."' ";		//tipo doc
			$cadena_sql.=") ";
	
			//Con esta cadena se pueden obtener las consultas para subir manualmente algunos registros
			//echo $cadena_sql."<br><br>";
			//echo "ies=".$valor[0][0]."<br>";
			//echo "documento=".$valor[0][1]."<br>";
			//echo "tipo doc=".$valor[0][2]."<br>";
			//exit;
			
	        break;

							
		case "borraEstudiante":
			$cadena_sql="DELETE ";
			$cadena_sql.="FROM ";
			$cadena_sql.="estudiante ";
			$cadena_sql.="WHERE ";
			$cadena_sql.="codigo_unico=";			//numero doc
			$cadena_sql.="'".$valor[1]."' ";		//tipo doc
			$cadena_sql.="AND tipo_doc_unico=";
			$cadena_sql.="'".$valor[2]."'";
							
			
			
			//echo $cadena_sql."<br>";
			//exit;
			
	        break;
		
			//Consulta de la tabla egresado del SNIES LOCAL
		
			case "egresadoSnies":
			$cadena_sql="SELECT ";
			$cadena_sql.="ies_code, ";					//[0]
			$cadena_sql.="ins_annio, ";
			$cadena_sql.="ins_semestre,";
			$cadena_sql.="codigo_unico,";
			//$cadena_sql.="departamento, ";
			//$cadena_sql.="municipio, ";				//[5]
			//$cadena_sql.="codigo_ent_aula, ";
			$cadena_sql.="tipo_doc_unico, ";
			$cadena_sql.="pro_consecutivo ";
			$cadena_sql.="FROM egresado ";
			$cadena_sql.="WHERE ";
			$cadena_sql.="codigo_unico='".$valor[2]."' " ;
			//$cadena_sql.="codigo_unico=".$valor." " ;
			
			//echo $cadena_sql;
			//exit;
			
			break;
		
		
		
		case "insertarEgresado":
			$cadena_sql="INSERT INTO ";
			$cadena_sql.="egresado ";
			$cadena_sql.="VALUES ";
			$cadena_sql.="(";
			$cadena_sql.="'".$valor[0][1]."', ";	
			$cadena_sql.="'".$valor[0][2]."', ";		
			$cadena_sql.="'".$valor[0][3]."', ";		
			$cadena_sql.="'".$valor[0][4]."', ";		
			$cadena_sql.="'".$valor[0][5]."', ";	
			$cadena_sql.="'".$valor[0][6]."', ";		
			$cadena_sql.="'".$valor[0][7]."', ";		
			$cadena_sql.="'".$valor[0][8]."', ";
			$cadena_sql.="'".$valor[0][9]."' ";			
		
			/*if ($valor[0][13]!=""){
			$cadena_sql.="'".$valor[0][13]."' ";
			}
			else
			{
			$cadena_sql.="'0'";
			}*/
			$cadena_sql.=") ";
	
			//Con esta cadena se pueden obtener las consultas para subir manualmente algunos registros
			//echo "ano=".$valor[0][9]."<br>";
			//echo "mes=".$valor[0][16];
			//echo $cadena_sql."<br><br>";
			//exit;
			
	        break;
			
		case "borraEgresado":
		
				$cadena_sql="DELETE ";
				$cadena_sql.="FROM ";
				$cadena_sql.="egresado ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="codigo_unico=";			//numero doc
				$cadena_sql.="'".$valor[1]."' ";		//tipo doc
				$cadena_sql.="AND tipo_doc_unico=";
				$cadena_sql.="'".$valor[2]."' ";
				$cadena_sql.="AND ins_annio=";
				$cadena_sql.="'".$valor[3]."' ";
				$cadena_sql.="AND ins_semestre=";
				$cadena_sql.="'".$valor[4]."' ";
				
				//echo $cadena_sql."<br>";
				//exit;
			
	        break;	

			
		case "totalEgresadosSnies":
					$cadena_sql="Select ";			
					$cadena_sql.="ins_annio, ";
					$cadena_sql.="ins_semestre, ";					//[0]
					$cadena_sql.="count (*)  ";			
					$cadena_sql.="from  egresado ";
					if($valor!="")
						{$cadena_sql.="where ";
						 $cadena_sql.="ins_annio=";			
						 $cadena_sql.="'".$valor[0]."' ";		
						 $cadena_sql.="AND ins_semestre=";
						 if($valor[1]==2 || $valor[1]==3)
							{
							$cadena_sql.="'02' " ;
							}
						 else		
							{
							$cadena_sql.="'01' " ;
							}
						}
					$cadena_sql.="group by ins_semestre, ins_annio ";
					$cadena_sql.="order by ins_annio DESC, ins_semestre DESC";			
					
					//echo $cadena_sql;
					//exit;
			
	        break;
			
			case "totalParticipanteSnies":
					$cadena_sql="Select ";			
					$cadena_sql.="count (codigo_unico) ";			
					$cadena_sql.="from  participante ";
									
					//echo $cadena_sql;
					//exit;
			
	        break;
	        
	        case "totalEstudianteSnies":
					$cadena_sql="Select ";			
					$cadena_sql.="count (codigo_unico)  ";			
					$cadena_sql.="from  estudiante ";
									
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

function ejecutar_admin_egresado($cadena_sql,$acceso_db,$tipo="")
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
