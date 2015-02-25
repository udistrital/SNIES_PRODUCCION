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
* @subpackage   admin_primiparo
* @package	bloques
* @copyright    
* @version      0.3
* @link		N/D
* @description  Bloque principal para la administracion de primiparos
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
$pagina="registro_inscrito";

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

	//Rescatar los inscritos de la base de datos academica
	// si se envia la variable de anno y/o periodo 
	

	if(isset($_REQUEST["annio"]) and isset($_REQUEST["periodo"]))
	{
		$valor[0]=$_REQUEST["annio"];
		//echo "annio=".$valor[0]."<br>";
		
		$valor[1]=$_REQUEST["periodo"];
		//echo "periodo=".$valor[1];


				//barra de progreso busqueda de registros
				//$bar = new barraProgreso($message='<br>Buscando Registros...', $hide=true, $sleepOnFinish=0, $barLength=500, $precision=50, $backgroundColor='#cccccc', $foregroundColor='blue'); 
 				//$bar->initialize(1);

				$cubrimiento[0]=$valor[0];//anno
				if($valor[1]==2)
					{$cubrimiento[1]=3;//periodo
					}
				else
					{$cubrimiento[1]=$valor[1];//periodo
					}
					
				$participante=0;
				$estudiante=0;
				$primiparo=0;
				//toma los totales iniciales de los registros
				$cadena_sql=cadena_busqueda_primiparo($configuracion, $accesoSnies,$valor,"totalesPrimiparoSnies");
				//echo "<br>".$cadena_sql; 
				$inicialPrimiparo=ejecutar_admin_primiparo($cadena_sql,$accesoSnies);
				
				$cadena_sql=cadena_busqueda_primiparo($configuracion, $accesoSnies,"","totalParticipanteSnies");
				//echo "<br>".$cadena_sql; 
				$inicialParticipante=ejecutar_admin_primiparo($cadena_sql,$accesoSnies);
				
				$cadena_sql=cadena_busqueda_primiparo($configuracion, $accesoSnies,"","totalEstudianteSnies");
				//echo "<br>".$cadena_sql; 
				$inicialEstudiante=ejecutar_admin_primiparo($cadena_sql,$accesoSnies);
				
					?>
			<table width="100%" align="center" border="0" cellpadding="10" cellspacing="0" >
				<tbody>
					<tr>
						<td >
							<table width="100%" border="0" align="center" cellpadding="5 px" cellspacing="1px" >
								<tr class="centralcuerpo">
									<td>
									.::: Actualizaci&oacute;n Primiparos <? echo $valor[0]." - ".$valor[1];?>
									</td>
								</tr>	
							</table>
						</td>
					</tr>
				</tbody>
			</table>
						
			
						<?		
	unset($primiparo_borrar);			
	
	//Rescatar TODOS los primiparos de un periodo y anno especifico
	
	$cadena_sql=cadena_busqueda_primiparo($configuracion, $acceso_db, $valor,"primiparo");	
	//echo "PRIMI <BR>".$cadena_sql; 	
	$registro=ejecutar_admin_primiparo($cadena_sql,$acceso_db);
	$sindatos=0;
        $reg_sindatos=array();
        $error=array();

		//Termina barra de progreso despues del proceso de busqueda
		//$bar->increase();		
	
	if(!is_array($registro))
	{	
		$cadena=htmlentities($cadena, ENT_COMPAT, "UTF-8");
		alerta::sin_registro($configuracion,$cadena);	
	}
	else
	{
		$contar=count($registro);		
		//echo $contar;
		//exit;
				//barra de progreso
				//$mensaje="Prim&iacute;paros  ".$valor[0]." - ".$valor[1]."<br>Actualizando SNIES...";
				//$bar = new barraProgreso($message=$mensaje, $hide=true, $sleepOnFinish=0, $barLength=500, $precision=50, $backgroundColor='#cccccc', $foregroundColor='blue'); 
 				//$bar->initialize($contar);
 				

		for($contador=0;$contador<$contar;$contador++)
		{
			//$bar->increase();
			//se selecciona cada registro de graduados, uno a la vez	
			//unset($valor);
			unset($unError);
			$valor[2]=$registro[$contador][0];			//documento de identidad
			$valor[3]=$registro[$contador][8];			//codigo del estudiante
                        $valor[5]=$registro[$contador][1];			//codigo del programa
                        $registroPrincipal[0]=$registro[$contador];		//Que contiene los datos de la busqueda inicial
			
                        echo "<hr>".$contador." - Estudiante : ".$registroPrincipal[0][6]." ".$registroPrincipal[0][7]." ";
                        echo "<br>proyecto : ".$registroPrincipal[0][9]." ";
                        

			//echo "<H2>".$contador."</H2>".$valor[2]."<br>";
			//echo $valor;
			//exit;
			
			if($valor[2]!="" && $valor[3]!="")
			{	
				
				$cubrimiento[2]=$registro[$contador][1];//codigo del programa snies
							
				$cadena_sql=cadena_busqueda_primiparo($configuracion, $accesoSnies, $cubrimiento,"cubrimiento");
				//echo "<br>".$cadena_sql;exit;
			        $cubrimientoPrograma=ejecutar_admin_primiparo($cadena_sql,$accesoSnies);
			    
				if($cubrimientoPrograma[0][0]==0)
					{
						$cadena_sql=cadena_busqueda_primiparo($configuracion, $accesoSnies, $cubrimiento,"insertarCubrimiento");
						//echo "<br>".$cadena_sql;exit;		
			    		$Programa=ejecutar_admin_primiparo($cadena_sql,$accesoSnies);
					}//termina cubrimiento programa
	
				
						
				//ACTUALIZAR PARTICIPANTE
				
				//buscar los registros de participantes en el SNIES 
				$cadena_sql=cadena_busqueda_primiparo($configuracion, $accesoSnies, $registroPrincipal,"participanteSnies");
				$cadena_sql.=" AND tipo_doc_unico='".$registro[$contador][7]."'";//tipo documento	
				//echo "<br>part_snies<br>".$cadena_sql;	exit;
				$registroParticipanteSnies=ejecutar_admin_primiparo($cadena_sql,$accesoSnies);
		
				
				//Verificar que el primiparo no esta en la tabla participante del SNIES <<EXISTE EL PARTICIPANTE=NO>> 
				
				if(!is_array($registroParticipanteSnies))
				
				{	
				
					//Buscar los datos en la DB Academica de ese registro del primiparo para la tabla participante
					$cadena_sql=cadena_busqueda_primiparo($configuracion, $acceso_db, $registroPrincipal,"participanteAcademica");
					//echo "<br>part_acad<br>".$cadena_sql."<br><br>";exit;
					$registroDatosParticipante=ejecutar_admin_primiparo($cadena_sql,$acceso_db);
					
						//si no se obtiene un arreglo de la base de datos académica se debe saltar el registro
							if(!is_array($registroDatosParticipante))
								{
								//echo "esto es un :".$registroDatosParticipante;
								//echo "eso no esun arreglo continua con el siguiente registro<br><br>";
								
                                                                echo "<font color='red'><br>Para el Estudiante con documento = <span class='texto_negrita'>".$valor[2]."</span> no existen datos completos para cargar en la tabla PARTICIPANTE<br></font>";
								
								}
							
							else{
								//verifica si hay un registro ya egresado del participante
								$cadena_sql=cadena_busqueda_primiparo($configuracion, $accesoSnies, $valor,"participanteSnies");	
								//echo "<br><br>".$cadena_sql;
								@$ParticipanteSnies=ejecutar_admin_primiparo($cadena_sql,$accesoSnies);
																			
								if(is_array($ParticipanteSnies) && $ParticipanteSnies[0][12]!=$registro[$contador][7])
										{
										//Buscar los datos en la DB Academica de ese registro del graduado para la tabla estudiante
											$primiparo_borrar[1]=$ParticipanteSnies[0][13];//numero documento
											$primiparo_borrar[2]=$ParticipanteSnies[0][12];//tipo documento
											//echo $primiparo_borrar[1]." ".$primiparo_borrar[2];
										}
								
								//verifica que el tipo de documento a cargar sea el mismo si no lo unifica
								if ($registroDatosParticipante[0][12]!=$registro[$contador][7])
										{$registroDatosParticipante[0][12]=$registro[$contador][7];}
										
								//insertar los datos del graduado en la tabla participante del SNIES					
								$cadena_sql=cadena_busqueda_primiparo($configuracion, $accesoSnies, $registroDatosParticipante,"insertarParticipante");
								//echo $cadena_sql;exit;
								@$resultadoParticipante=ejecutar_admin_primiparo($cadena_sql,$accesoSnies,"insertar");
								//exit;
								if($resultadoParticipante==false)
								{
									$resultadoParticipante=ejecutar_admin_primiparo($cadena_sql,$accesoSnies,"error");
                                                                        echo "<font color='red'><br>El registro participante no pudo ser cargado en la tabla participante por:".$resultadoParticipante."<br></font>";
									$unError=true;
									//echo "El registro no pudo ser cargado, por favor verifique los datos en la Aplicaci&oacute;n Acad&eacute;mica <br>";
									//echo "<hr class='hr_subtitulo'>";
									}
								else{
                                                                    
                                                                    echo "<font color='green'><br>El registro se cargo con exito como Participante<br></font>";
                                                                    $participante++;}
								}
								
					/*/ en caso de que los datos -para el primiparo- de la tabla mntac.acestotr no se encuantren y no genere un arreglo
					if(is_array($registroDatosParticipante))
		
					{ //echo "<br>hola<br>";
							
					//echo $registroDatosParticipante[0][1];
						//exit;	
						
					echo "El Primiparo con documento = <span class='texto_negrita'>".$valor."</span> no existe en la tabla PARTICIPANTE<br>";
					//echo "<hr class='hr_subtitulo'>";
					$participanteFaltante++;
					
					
					//insertar los datos del primiparo en la tabla participante del SNIES
					
											
						$cadena_sql=cadena_busqueda_primiparo($configuracion, $accesoSnies, $registroDatosParticipante,"insertarParticipante");		
						$resultadoParticipante=ejecutar_admin_primiparo($cadena_sql,$accesoSnies,"insertar");
						
						
						
						if($resultadoParticipante==false)
						{
							$resultadoParticipante=ejecutar_admin_primiparo($cadena_sql,$accesoSnies,"error");
							$unError=true;
							//echo "El registro no pudo ser cargado, por favor verifique los datos en la Aplicaci&oacute;n Acad&eacute;mica <br>";
							//echo "<span class='texto_negrita'>Mensaje de la Base de Datos SNIES:</span> ".$resultadoParticipante."<br>";
							//echo $registroDatosParticipante[0][10].", ";
							//echo "<hr class='hr_subtitulo'>";
														
						}								
				
							
					}//echo "este registro no esta en la tabla acestrot numero <br>" ;
					*/
	
			
				}//fin actualizar participante
			
	
				// ACTUALIZAR ESTUDIANTE
				unset($unError);
						
						//buscar los registros de estudiantes en el SNIES
						
						$cadena_sql=cadena_busqueda_primiparo($configuracion, $accesoSnies, $valor,"estudianteSnies");
						$cadena_sql.=" AND tipo_doc_unico='".$registro[$contador][7]."'";
						//echo  $cadena_sql;
						$registroEstudianteSnies=ejecutar_admin_primiparo($cadena_sql,$accesoSnies);
						
						//Verificar que el primparo no esta en la tabla participante del SNIES <<EXISTE EL PARTICIPANTE=NO>> 
				
						if(!is_array($registroEstudianteSnies))
									{	
										//Buscar los datos en la DB Academica de ese registro del graduado para la tabla estudiante
										$cadena_sql=cadena_busqueda_primiparo($configuracion, $acceso_db, $valor,"estudianteAcademica");
										//echo "<br>".$cadena_sql;				exit;
										$registroDatosEstudiante=ejecutar_admin_primiparo($cadena_sql,$acceso_db);
										
										//si no se obtiene un arreglo de estudiante de la academica se ignora
										if(!is_array($registroDatosEstudiante)){
										     echo "<font color='red'><br>El estudiante con documento = <span class='texto_negrita'>".$valor[2]."</span> no tiene datos completo para cargar a la tabla ESTUDIANTE</font>";
                                                                                     
											}
										else{
											
											//verifica si hay un registro ya guardado del participante
											$cadena_sql=cadena_busqueda_primiparo($configuracion, $accesoSnies, $valor,"estudianteSnies");
											//echo $cadena_sql;
											$EstudianteSnies=ejecutar_admin_primiparo($cadena_sql,$accesoSnies);
											
											if(is_array($EstudianteSnies) && $EstudianteSnies[0][2]!=$registro[$contador][7])
													{
													//Buscar los datos en la DB Academica de ese registro del graduado para la tabla estudiante
														$primiparo_borrar[1]=$EstudianteSnies[0][1];//numero documento
														$primiparo_borrar[2]=$EstudianteSnies[0][2];//tipo documento
													//	echo $primiparo_borrar[1]." ".$primiparo_borrar[2];
													}
											
											//verifica que el tipo de documento a cargar sea el mismo si no lo unifica
												if ($registroDatosEstudiante[0][2]!=$registro[$contador][7])
														{$registroDatosEstudiante[0][2]=$registro[$contador][7];}
									
									 		//insertar los datos del graduado en la tabla estudiante del SNIES
											$cadena_sql=cadena_busqueda_primiparo($configuracion, $accesoSnies, $registroDatosEstudiante,"insertarEstudiante");
											//echo "<br>".$cadena_sql;exit;
											@$resultadoEstudiante=ejecutar_admin_primiparo($cadena_sql,$accesoSnies,"insertar");
											if($resultadoEstudiante==false)
											{
												@$resultadoEstudiante=ejecutar_admin_primiparo($cadena_sql,$accesoSnies,"error");
												                                                                                               
                                                                                                echo "<font color='red'><br>El registro estudiante no pudo ser cargado por:".$resultadoEstudiante."<br></font>";
                                                                                                
												$unError=true;
												//echo "El registro no pudo ser cargado, por favor verifique los datos en la Aplicaci&oacute;n Acad&eacute;mica <br>";
												//echo "<hr class='hr_subtitulo'>";
											}
											else{
                                                                                              echo "<font color='green'><br>El registro se cargo con exito como estudiante<br><br></font>";
                                                                                              $estudiante++;
                                                                                            }
											}		
									
									}
		
						/*/Verificar que el primiparo no esta en la tabla estudiante del SNIES <<EXISTE EL ESTUDIANTE=NO>> 
						
						if(!is_array($registroEstudianteSnies))
						
						{	
						
							//Buscar los datos en la DB Academica de ese registro del primiparo para la tabla estudiante
							$cadena_sql=cadena_busqueda_primiparo($configuracion, $acceso_db, $valor,"estudianteAcademica");					
							$registroDatosEstudiante=ejecutar_admin_primiparo($cadena_sql,$acceso_db);
							
							
							
							echo "*El primiparo con documento = <span class='texto_negrita'>".$valor."</span> no existe en la tabla ESTUDIANTE<br>";
							$estudianteFaltante++;
							
							//insertar los datos del primiparo en la tabla estudiante del SNIES
													
								$cadena_sql=cadena_busqueda_primiparo($configuracion, $accesoSnies, $registroDatosEstudiante,"insertarEstudiante");		
								$resultadoEstudiante=ejecutar_admin_primiparo($cadena_sql,$accesoSnies,"insertar");
								if($resultadoEstudiante==false)
								{
									$resultadoEstudiante=ejecutar_admin_primiparo($cadena_sql,$accesoSnies,"error");
									//echo "El registro no pudo ser cargado por:".$resultadoEstudiante."<br><br>";
									echo "El registro no pudo ser cargado, por favor verifique los datos en la Aplicaci&oacute;n Acad&eacute;mica <br>";
									echo "<hr class='hr_subtitulo'>";
									
								}		
						
						}*/
				//fin actualizar estudiante
	
				
										
	
				// ACTUALIZAR PRIMIPARO --GRADUADO??
				
						unset($unError);
						
						//buscar los registros de estudiantes en el SNIES
						
						$cadena_sql=cadena_busqueda_primiparo($configuracion, $accesoSnies, $valor,"primiparoSnies");
						$cadena_sql.=" AND tipo_doc_unico='".$registro[$contador][7]."'";	
						//echo "<br><br>".$cadena_sql;				exit;
						$registroPrimiparo=ejecutar_admin_primiparo($cadena_sql,$accesoSnies);
					
						//Verificar que el primiparo no esta en la tabla ESTUDIANTE_PROGRAMA del SNIES <<EXISTE EL PRIMIPARO=NO>> 
						
						if(!is_array($registroPrimiparo))
						
						
						{	
							$cadena_sql=cadena_busqueda_primiparo($configuracion, $accesoSnies, $valor,"primiparoSnies");
							//echo "<br><br>".$cadena_sql;				
							$PrimiparoSnies=ejecutar_admin_primiparo($cadena_sql,$accesoSnies);
							
							
							
							if(is_array($PrimiparoSnies) && $PrimiparoSnies[0][6]!=$registro[$contador][7])
									{
									//Buscar los datos en la DB Academica de ese registro del primiparo para la tabla estudiante
										$primiparo_borrar[1]=$PrimiparoSnies[0][5];//numero documento
										$primiparo_borrar[2]=$PrimiparoSnies[0][6];//tipo documento
										//echo $primiparo_borrar[1]." ".$primiparo_borrar[2];
									}
							
                                                            //insertar los datos del primiparo en la tabla estudiante_programa del SNIES
                                                                $miRegistro[0]=$registro[$contador];
								$cadena_sql=cadena_busqueda_primiparo($configuracion, $accesoSnies, $miRegistro,"insertarPrimiparo");
								//echo $cadena_sql;//exit;
								@$resultadoPrimiparo=ejecutar_admin_primiparo($cadena_sql,$accesoSnies,"insertar");
								
								
								if($resultadoPrimiparo==false)
								{
									$resultadoPrimiparo=ejecutar_admin_primiparo($cadena_sql,$accesoSnies,"error");
                                                                        
                                                                         echo "<font color='red'><br>El registro no pudo ser cargado por:".$resultadoPrimiparo."<br></font>";
                                                                          
									//echo "El registro no pudo ser cargado, por favor verifique los datos en la Aplicaci&oacute;n Acad&eacute;mica <br>";
									//echo "<hr class='hr_subtitulo'>";
								}	
								else
									 {
                                                                          echo "<font color='green'><br>El registro se cargo con exito como Estudiante de Primer curso<br><br></font>";
                                                                          
                                                                             $primiparo++;
									 }					
							
							/*/echo "*El Primiparo con documento = <span class='texto_negrita'>".$valor."</span> no existe en la tabla ESTUDIANTE_PROGRAMA (actualice la p&aacute;gina para cargarlo)<br>";
							$primiparoFaltante++;
							
							
							//insertar los datos del primiparo en la tabla estudiante_programa del SNIES
							$miRegistro[0]=$registro[$contador];
								$cadena_sql=cadena_busqueda_primiparo($configuracion, $accesoSnies, $miRegistro,"insertarPrimiparo");		
								$resultadoPrimiparo=ejecutar_admin_primiparo($cadena_sql,$accesoSnies,"insertar");
								
								
								if($resultadoPrimiparo==false)
								{
									$resultadoPrimiparo=ejecutar_admin_primiparo($cadena_sql,$accesoSnies,"error");
									//echo "El registro no pudo ser cargado por:".$resultadoPrimiparo."<br>";
									//echo "El registro no pudo ser cargado, por favor verifique los datos en la Aplicaci&oacute;n Acad&eacute;mica <br>";
									//echo "<hr class='hr_subtitulo'>";
								}*/		
						}//cierra el if verificar primiparo
                                            else{echo "<font color='blue'><br>El estudiante ya esta registrado como primiparo<br></font>";
                                                
                                                }
						
				
					if(isset($primiparo_borrar))
								{//buscar los registros de estudiantes en el SNIES
									$primiparo_borrar[3]=$registro[$contador][2];
									$primiparo_borrar[4]=$registro[$contador][3];
									$cadena_sql=cadena_busqueda_primiparo($configuracion, $accesoSnies, $primiparo_borrar,"borraprimiparo");
									//echo "<br>".$cadena_sql;		
									@$borraprimiparo=ejecutar_admin_primiparo($cadena_sql,$accesoSnies);
									$primiparo--;
									
								 unset($primiparo_borrar);
								 }
						
						
					
				//cierre actualizar primiparo	
			  }//fin if($valor[2]!="" && $valor[3]!="")
			}//cierra el for del registro
		}	//cierra el if de registro
	//VARIABLES PARA EL LOG
                                                date_default_timezone_set('UTC');
						$logger[0]="CARGAR AL SNIES LOCAL";
						$logger[1]="Participante";
						$logger[2]=$inicialParticipante[0][0];
						$logger[3]=$participante;
						$logger[4]=date('d/m/Y h:i:s',time());
						$logger[5]=$cubrimiento[0];
						$logger[6]=$cubrimiento[1];
						$logger[7]="Se cargo los datos, para el reporte de primiparos al SNIES para el periodo ".$cubrimiento[0]."-".$cubrimiento[1];
						
						$log_us->log_usuario($logger,$configuracion);
						
						$logger[1]="Estudiante";
						$logger[2]=$inicialEstudiante[0][0];
						$logger[3]=$estudiante;
						$log_us->log_usuario($logger,$configuracion);
						
						$logger[1]="Primiparo";
						$logger[2]=$inicialPrimiparo[0][2];
						$logger[3]=$primiparo;
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
											<td>PRIMIPARO</td>
											<td><? echo $primiparo;?></td>
										</tr>
										</td>
										</tr>
									</table>
									</td>
								</tr>
							</tbody>
						</table>		
										
	<?}//cierra el if  de captura de año y periodo
	
			
		/*echo "<br><span class='texto_negrita'>Faltan ".$participanteFaltante." registros de primiparos por subir a la tabla PARTICIPANTE. </span><br>";
		$totalEstudianteFalatante=$participanteFaltante+$estudianteFaltante;
		echo "<span class='texto_negrita'>Faltan ".$totalEstudianteFalatante." registros de primiparos por subir a la tabla ESTUDIANTE.</h4> </span><br>";
		$totalprimiparoFaltante=$totalEstudianteFalatante+$primiparoFaltante;
		echo "<span class='texto_negrita'>Faltan ".$totalprimiparoFaltante." registros por subir a la tabla ESTUDIANTE_PROGRAMA. </span><br>";*/



///busca los totales
	$cadena_sql=cadena_busqueda_primiparo($configuracion, $accesoSnies, "","totalesPrimiparoSnies");		
	$registroPrimiparoTotales=ejecutar_admin_primiparo($cadena_sql,$accesoSnies);
	

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
						.::: Datos Prim&iacute;paros
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
										TOTAL PRIMIPAROS
										</td>					
									</tr>	
									
									<?$n=0;
								while (count($registroPrimiparoTotales)>$n)
								{	
									if (is_array($registroPrimiparoTotales[$n]))
									{?>
									
									<tr>
										<td class="cuadro_plano centrar">
										<span class="texto_negrita"><? echo $totalPrimiparo=$registroPrimiparoTotales[$n][0];?></span> 
										</td>
										<td class="cuadro_plano centrar">
										<span class="texto_negrita"><? echo $totalPrimiparo=$registroPrimiparoTotales[$n][1];?></span>
										</td>
										<td class="cuadro_plano centrar centrar">
										<span class="texto_negrita"><? echo $totalPrimiparo=$registroPrimiparoTotales[$n][2];?></span>
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

function cadena_busqueda_primiparo($configuracion, $acceso_db, $valor,$opcion="")
{
	//$valor=$acceso_db->verificar_variables($valor);
	
	switch($opcion)
	{
	
		//consulta para generar los datos para la tabla primiparos del SNIES a partir de la DB Academica para PHP
		//se agrega el numero de documento en el registro [0]
		case "primiparo":	
			$prefijo="mntac.";			
			$cadena_sql="SELECT UNIQUE ";
			$cadena_sql.="est_nro_iden, ";
			$cadena_sql.="as_cra_cod_snies pro_consecutivo, ";
			$cadena_sql.="decode(length(est_cod),7,(substr(est_cod,1,2)+1900),11,(substr(est_cod, 1,4))) anio, ";
			$cadena_sql.="decode(decode(length(est_cod),7,((substr(est_cod,3,1))),11,(substr(est_cod, 5,1))), '1','01','02') semestre, ";
			//$cadena_sql.="est_cod, ";			
			$cadena_sql.="'02' es_transferencia, ";
			$cadena_sql.="'1301' ies_code, ";
			$cadena_sql.="est_nro_iden codigo_unico, ";
			$cadena_sql.="TO_CHAR(DECODE(est_tipo_iden,'',DECODE(length(est_nro_iden),11,'TI',12,'TI','CC'),'C', 'CC', '1', 'CC', 'c', 'CC', 'T', 'TI', '2', 'TI', 't', 'TI', 'E', 'CE', 'P', 'PS', 'CC')) tipo_doc_unico,  ";
			$cadena_sql.="est_cod, ";
                        $cadena_sql.="as_cra_nom prog ";
			//$cadena_sql.="TO_CHAR(DECODE(est_tipo_iden,'C','CC','T','TI','CC')) tipo_doc_unico ";
			//$cadena_sql.="FROM "."$prefijo"."acest, "."$prefijo"."accra_snies ";
			//$cadena_sql.="WHERE as_cra_cod=est_cra_cod ";
			$cadena_sql.="FROM ".$prefijo."accra_snies ";
			$cadena_sql.="INNER JOIN ".$prefijo."accra ON as_cra_cod=cra_cod ";
                        $cadena_sql.="INNER JOIN ".$prefijo."acest ON as_cra_cod=est_cra_cod ";
			$cadena_sql.="WHERE est_estado_est != 'M' ";
			$cadena_sql.="AND est_estado_est != 'N' ";
                        $cadena_sql.="AND est_nro_iden<>101 ";
			$cadena_sql.="AND decode(length(est_cod),7,(substr(est_cod,1,2)+1900),11,(substr(est_cod, 1,5))) in ('".$valor[0].$valor[1]."')" ;			
						
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
			$cadena_sql.="codigo_unico='".$valor[0][0]."' " ;
			
			//echo $cadena_sql;
			//exit;
			
			break;
		
		
		//Consulta de la DB Academica para generar los datos de la tabla participante del SNIES para los graduados
		case "participanteAcademica":
                        /*se actualizo la consulta para corregir datos de la auditoria del snies Noviembre 3 de 2009

			$prefijo="mntac.";	 
			$cadena_sql="SELECT ";
			$cadena_sql.="TO_CHAR('1301') ies_code, ";
			$cadena_sql.="DECODE((SUBSTR(trim(est_nombre),1,INSTR(trim(est_nombre),' ',1,2)-1)),NULL,est_nombre, (SUBSTR(trim(est_nombre),1,INSTR(trim(est_nombre),' ',1,2)-1)))primer_apellido, ";
			$cadena_sql.="SUBSTR(trim(est_nombre),1,INSTR(trim(est_nombre),' ',1,2)-1) segundo_apellido,";
			$cadena_sql.="SUBSTR(trim(est_nombre),INSTR(trim(est_nombre),' ',1,2)+1) primer_nombre,";
			$cadena_sql.="SUBSTR(trim(est_nombre),INSTR(trim(est_nombre),' ',1,2)+1) segundo_nombre, ";
			$cadena_sql.="TO_CHAR(eot_fecha_nac, 'yyyy-mm-dd') fecha_nacim, ";
			$cadena_sql.="TO_CHAR('CO') pais_ln, ";
			$cadena_sql.="TO_CHAR(decode (eot_cod_dep_nac,0,11,'',11, eot_cod_dep_nac)) departamento_ln, ";
			$cadena_sql.="TO_CHAR(decode (eot_cod_mun_nac,0,11001,'',11001, eot_cod_mun_nac)) municipio_ln, ";
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
			$cadena_sql.="AND est_estado IN ('A','B','H','L','T','E','R') ";   // ver tabla mntac.acestado
			//echo $valor."<br>";
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
                        $cadena_sql.="'1' area_tel, ";
                        $cadena_sql.="est_telefono tel ";
                        $cadena_sql.="FROM mntac.acest ";
                        $cadena_sql.="INNER JOIN mntac.acestotr ON est_cod = eot_cod ";
                        $cadena_sql.="INNER JOIN mntge.gemunicipio ON mun_cod=decode(eot_cod_mun_nac,0,11001,'',11001,eot_cod_mun_nac) ";
                        $cadena_sql.="WHERE ";
                        $cadena_sql.="est_nro_iden='".$valor[0][0]."' ";
                        $cadena_sql.="ORDER BY est_cod DESC";

			//echo $cadena_sql."<br><br>";
			//exit;
			
			break;	
			
			
		/*/Consulta de la DB Academica para generar los datos de la tabla participante del SNIES para los primiparos
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
			$cadena_sql.="TO_CHAR(eot_cod_dep_nac)) departamento_ln, ";
			$cadena_sql.="TO_CHAR(eot_cod_mun_nac) municipio_ln, ";
			//$cadena_sql.="TO_CHAR(SUBSTR(lug_cod,1,2)) departamento_ln, ";
			//$cadena_sql.="TO_CHAR(SUBSTR(lug_cod,2,2)||SUBSTR(lug_cod,5,3)) municipio_ln, ";
			//para cargar por defecto 11 y 11001 habilitar las dos lineas siguientes y deshabilitar las anteriores, despues de haber cargado todos los registros.
			//$cadena_sql.="'11' departamento_ln, ";
			//$cadena_sql.="'11001' municipio_ln, ";
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
			$cadena_sql.="est_nro_iden=".$valor[2]." " ;
			$cadena_sql.="AND est_cod = eot_cod ";
			$cadena_sql.="AND lug_cod = eot_cod_lug_nac ";
		
			//echo $cadena_sql."<br>";
			//exit;
			
			break;*/
			
			
		//Consulta de la DB Academica para generar los datos de la tabla participante del SNIES para los primiparos
		
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
			//echo "tel=-".$valor[0][12]."-";
			//echo $cadena_sql."<br><br>";
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
			$cadena_sql.="codigo_unico='".$valor[2]."' " ;
			
			//echo $cadena_sql;
			//exit;
			
			break;
		
		
			case "estudianteAcademica":
			$prefijo="mntac.";	 
			$cadena_sql="SELECT UNIQUE ";
			$cadena_sql.="TO_CHAR('1301') ies_code, ";
			$cadena_sql.="TO_CHAR(est_nro_iden) codigo_unico, ";
			//$cadena_sql.="TO_CHAR(DECODE(est_tipo_iden,'C','CC','T','TI','CC')) tipo_doc_unico ";
			$cadena_sql.="DECODE(est_tipo_iden,'',DECODE(length(est_nro_iden),11,'TI','CC'),'C','CC','T','TI','c','CC','I','TI','t','TI','1','CC','2','TI',est_tipo_iden) tipo_doc_unico ";
			$cadena_sql.="FROM ".$prefijo."acest ";
			$cadena_sql.="WHERE ";
			//$cadena_sql.="est_nro_iden=".$valor[2]." " ;
			$cadena_sql.="est_cod=".$valor[3]." " ;
			//$cadena_sql.="AND est_estado_est IN ('A','B','H','L','T','E','R') ";
			
			
			
			//echo $cadena_sql."<br>";
			//exit;
			
			break;
		
		
		
				//Consulta de la DB Academica para generar los datos de la tabla participante del SNIES para los primiparos
		
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
			//echo "ies=".$valor[0][0]."<br>";
			//echo "documento=".$valor[0][1]."<br>";
			//echo "tipo doc=".$valor[0][2]."<br>";
			//echo $cadena_sql."<br><br>";
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
		
		
		//Consulta de la tabla participante del SNIES LOCAL
		
			case "primiparoSnies":
			$cadena_sql="SELECT ";
			$cadena_sql.="pro_consecutivo, ";					//[0]
			$cadena_sql.="anio, ";
			$cadena_sql.="semestre,";
			$cadena_sql.="es_transferencia,";
			$cadena_sql.="ies_code, ";
			$cadena_sql.="codigo_unico, ";				//[5]
			$cadena_sql.="tipo_doc_unico ";
			$cadena_sql.="FROM estudiante_programa ";
			$cadena_sql.="WHERE ";
			$cadena_sql.="codigo_unico='".$valor[2]."' " ;
                        $cadena_sql.="AND pro_consecutivo='".$valor[5]."' " ;
                        
			
			//echo $cadena_sql;
			//exit;
			
			break;
		
		
		
			case "insertarPrimiparo":
			$cadena_sql="INSERT INTO ";
			$cadena_sql.="estudiante_programa ";
			$cadena_sql.="VALUES ";
			$cadena_sql.="(";
			$cadena_sql.="'".$valor[0][1]."', ";	
			$cadena_sql.="'".$valor[0][2]."', ";
			$cadena_sql.="'".$valor[0][3]."', ";		
			$cadena_sql.="'".$valor[0][4]."', ";		
			$cadena_sql.="'".$valor[0][5]."', ";
			$cadena_sql.="'".$valor[0][6]."', ";		
			$cadena_sql.="'".$valor[0][7]."'";		
			$cadena_sql.=") ";
	
			//Con esta cadena se pueden obtener las consultas para subir manualmente algunos registros
			//echo "ano=".$valor[0][9]."<br>";
			//echo "mes=".$valor[0][16];
			//echo $cadena_sql."<br>";			
			//exit;
			
	        break;
			
			
		case "borraprimiparo":
		
				$cadena_sql="DELETE ";
				$cadena_sql.="FROM ";
				$cadena_sql.="estudiante_programa ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="codigo_unico=";			//numero doc
				$cadena_sql.="'".$valor[1]."' ";		//tipo doc
				$cadena_sql.="AND tipo_doc_unico=";
				$cadena_sql.="'".$valor[2]."' ";
				$cadena_sql.="AND anio=";
				$cadena_sql.="'".$valor[3]."' ";
				$cadena_sql.="AND semestre=";
				$cadena_sql.="'".$valor[4]."' ";
				
				//echo $cadena_sql."<br>";
				//exit;
			
	        break;		
			
			
				//Consulta de la tablas para obtener primiparos  del SNIES LOCAL
		
			case "totalesPrimiparoSnies":
			$cadena_sql="Select ";			
			$cadena_sql.="anio, ";
			$cadena_sql.="semestre, ";					//[0]
			$cadena_sql.="count (*)  ";			
			$cadena_sql.="from  estudiante_programa ";
			if($valor!="")
						{$cadena_sql.="where ";
						 $cadena_sql.="anio=";			
						 $cadena_sql.="'".$valor[0]."' ";		
						 $cadena_sql.="AND semestre=";
						 if($valor[1]==2 || $valor[1]==3)
							{
							$cadena_sql.="'02' " ;
							}
						 else		
							{
							$cadena_sql.="'01' " ;
							}
						}
			$cadena_sql.="group by semestre, anio ";
			$cadena_sql.="order by anio DESC, semestre DESC";			
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

function ejecutar_admin_primiparo($cadena_sql,$acceso_db,$tipo="")
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
