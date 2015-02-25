<?
/*
############################################################################
#    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        #
#    Copyright: Vea el archivo LICENCIA.txt que viene con la distribucion  #
############################################################################
Bloque que actualiza los graduados en el snies local

*****************************************************************************
Modificaciones  |  Jairo lavado Hernández       | Junio 18 de 2009 |
Modificaciones  |  Miguel Angle Vargas Cabezas  | Marzo 26 de 2010 |
---------------------------------------------------------------------

*/
/****************************************************************************
* @name          bloque.php 
* @revision     Ultima revision 10 de junio de 2008
*****************************************************************************
* @subpackage   admin_graduado
* @package	bloques
* @copyright    
* @version      0.3
* @link		N/D
* @description  Bloque principal para la administracion de graduados
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
				$graduado=0;
				//toma los totales iniciales de los registros
				$cadena_sql=cadena_busqueda_graduado($configuracion, $accesoSnies,$valor,"totalesGraduadosSnies");
				//echo $cadena_sql;
				$inicialGraduados=ejecutar_admin_graduado($cadena_sql,$accesoSnies);
				
				$cadena_sql=cadena_busqueda_graduado($configuracion, $accesoSnies,"","totalParticipanteSnies");
				//echo $cadena_sql; 
				$inicialParticipante=ejecutar_admin_graduado($cadena_sql,$accesoSnies);
				
				$cadena_sql=cadena_busqueda_graduado($configuracion, $accesoSnies,"","totalEstudianteSnies");
				//echo $cadena_sql; 
				$inicialEstudiante=ejecutar_admin_graduado($cadena_sql,$accesoSnies);
				
				
			?>		
				
			<table width="100%" align="center" border="0" cellpadding="10" cellspacing="0" >
				<tbody>
					<tr>
						<td >
							<table width="100%" border="0" align="center" cellpadding="5 px" cellspacing="1px" >
								<tr class="centralcuerpo">
									<td>
									.::: Actualizaci&oacute;n Graduados <? echo $valor[0]." - ".$valor[1];?>
									</td>
								</tr>	
							</table>
						</td>
					</tr>
				</tbody>
			</table>
						
			
						<?		
							
				
				//Rescatar TODOS los graduados de un periodo y anno especifico
				
				$cadena_sql=cadena_busqueda_graduado($configuracion, $acceso_db, $valor,"graduado");	
				//echo $cadena_sql;
				$registro=ejecutar_admin_graduado($cadena_sql,$acceso_db);
				
				
				if(!is_array($registro))
				{	
					$cadena=htmlentities($cadena, ENT_COMPAT, "UTF-8");
					alerta::sin_registro($configuracion,$cadena);	
				}
				else
				{
					unset($contar);
					$contar=count($registro);
					

			 		$sindatos=0;
			 		
			 		
					for($contador=0;$contador<$contar;$contador++)
					{
						
						//$bar->increase();
					
						//se selecciona cada registro de graduados, uno a la vez	
						unset($valor);
						unset($unError);
						$valor[2]=$registro[$contador][1];			//documento de identidad
						$valor[3]=$registro[$contador][16];			//codigo del estudiante
						$registroPrincipal[0]=$registro[$contador];	//Que contiene los datos de la busqueda inicial

                                                //valida que el registro no tenga datos nulos que son obligatorios en Snies
                                                $aux=0;$valido='SI';
                                                //echo $cadena_sql;
                                                unset($logError);
                                                while($aux<=16 && $valido=='SI')
                                                    { if(chop($registroPrincipal[0][$aux])=='' && $aux!=5 && $aux!=6 && $aux!=13 )
                                                        {$valido='NO';
                                                            if($aux == 9) $logError = "No tiene acta de Grado Asignada<br/>";
                                                        }
                                                       // echo "<br>".$aux." - ".$registroPrincipal[0][$aux]." ".$valido;
                                                        $aux++;
                                                    }//exit;

                        			echo "<hr>".$contador." - Estudiante : ".$registroPrincipal[0][11]." ".$registroPrincipal[0][1]." => cod: ".$registroPrincipal[0][16]." ";
                                                echo "<br>proyecto : ".$registroPrincipal[0][17]." ";
                                            //exit;
						
						if($valido=="SI")
						{	
							//busca que exista el cubrimiento de programa para el periodo seleccionado
														
							$cubrimiento[2]=$registro[$contador][2];//codigo snies de la carrera
							
							$cadena_sql=cadena_busqueda_graduado($configuracion, $accesoSnies, $cubrimiento,"cubrimiento");
							//echo "<br>".$cadena_sql;	
						        $cubrimientoPrograma=ejecutar_admin_graduado($cadena_sql,$accesoSnies);
						    
							if($cubrimientoPrograma[0][0]==0)
								{
									$cadena_sql=cadena_busqueda_graduado($configuracion, $accesoSnies, $cubrimiento,"insertarCubrimiento");
									//echo "<br>".$cadena_sql;exit;		
						    		@$Programa=ejecutar_admin_graduado($cadena_sql,$accesoSnies);
								}
									
							//ACTUALIZAR PARTICIPANTE
							
							//buscar los registros de participantes en el SNIES
							//busca_participante en snies
							$cadena_sql=cadena_busqueda_graduado($configuracion, $accesoSnies, $valor,"participanteSnies");	
							$cadena_sql.=" AND tipo_doc_unico='".$registro[$contador][11]."'";	
							//echo $cadena_sql;exit;
							$registroParticipanteSnies=ejecutar_admin_graduado($cadena_sql,$accesoSnies);


                                                        //obtiene datos de la tabla para el participante del aplicativo academico
                                                        $cadena_sql=cadena_busqueda_graduado($configuracion, $acceso_db, $registroPrincipal,"participanteAcademica");
							//if ($registroPrincipal[0][1] = 53031813)
                                                          //  echo "<br><br>".$cadena_sql;	//exit;
							$registroDatosParticipante=ejecutar_admin_graduado($cadena_sql,$acceso_db);
                                                        
                                                        //verifica que los datos minimo exigido sean correctos

                                                        $aux2=0;$validoP='SI';
                                                        while($aux2<=17 && $validoP=='SI')
                                                            {if(chop($registroDatosParticipante[0][$aux2])=='' && $aux2!=2 && $aux2!=4 && $aux2!=5 && $aux2!=7 && $aux2!=8 && $aux2!=9 && $aux2!=10 && $aux2!=14 && $aux2!=15)
                                                                $validoP='NO';
                                                                //echo "<br>".$aux2." - '".$registroDatosParticipante[0][$aux2]."' ".$validoP;
                                                                $aux2++;
                                                            }//exit;

                                                       if($validoP=='SI')
							   {
							//Verificar que el graduado no esta en la tabla participante del SNIES <<EXISTE EL PARTICIPANTE=NO>>
                                                                if(!is_array($registroParticipanteSnies))
                                                                                {       /*/Buscar los datos en la DB Academica de ese registro del graduado para la tabla participante
                                                                                        $cadena_sql=cadena_busqueda_graduado($configuracion, $acceso_db, $registroPrincipal,"participanteAcademica");
                                                                                        //echo "<br><br>".$cadena_sql;	exit;
                                                                                        $registroDatosParticipante=ejecutar_admin_graduado($cadena_sql,$acceso_db);
                                                                                        //si no se obtiene un arreglo de la base de datos académica se debe saltar el registro*/
                                                                                        if(!is_array($registroDatosParticipante))
                                                                                                {
                                                                                                //echo "esto es un :".$registroDatosParticipante;
                                                                                                //echo "eso no esun arreglo continua con el siguiente registro<br><br>";
                                                                                                echo "<font color='red'><br>*El Graduado con documento = <span class='texto_negrita'>".$valor[2]."</span> no existe en la tabla PARTICIPANTE<br></font>";
                                                                                                
                                                                                                //$participante++;
                                                                                                }

                                                                                        else{
                                                                                                //verifica si hay un registro ya guardado del participante
                                                                                                $cadena_sql=cadena_busqueda_graduado($configuracion, $accesoSnies, $valor,"participanteSnies");
                                                                                                $cadena_sql.=" AND tipo_doc_unico!='".$registro[$contador][11]."'";
                                                                                                //echo "<br><br>".$cadena_sql;exit;
                                                                                                $ParticipanteSnies=ejecutar_admin_graduado($cadena_sql,$accesoSnies);

                                                                                                if(is_array($ParticipanteSnies) && $ParticipanteSnies[0][12]!=$registro[$contador][11])
                                                                                                                {
                                                                                                                //Buscar los datos en la DB Academica de ese registro del graduado para la tabla estudiante
                                                                                                                        $graduado_borrar[1]=$ParticipanteSnies[0][13];//numero documento
                                                                                                                        $graduado_borrar[2]=$ParticipanteSnies[0][12];//tipo documento
                                                                                                                }

                                                                                                //verifica que el tipo de documento a cargar sea el mismo si no lo unifica
                                                                                                if ($registroDatosParticipante[0][12]!=$registro[$contador][11])
                                                                                                                {$registroDatosParticipante[0][12]=$registro[$contador][11];}

                                                                                                //insertar los datos del graduado en la tabla participante del SNIES
                                                                                                $cadena_sql=cadena_busqueda_graduado($configuracion, $accesoSnies, $registroDatosParticipante,"insertarParticipante");
                                                                                               // echo $cadena_sql;exit;
                                                                                                @$resultadoParticipante=ejecutar_admin_graduado($cadena_sql,$accesoSnies,"insertar");
                                                                                                if($resultadoParticipante==false)
                                                                                                    {
                                                                                                        $resultadoParticipante=ejecutar_admin_graduado($cadena_sql,$accesoSnies,"error");
                                                                                                      echo "<font color='red'><br>El registro participante no pudo ser cargado en la tabla participante por:".$resultadoParticipante."<br></font>";
                                                                                                        
                                                                                                        $unError=true;
                                                                                                        //echo "El registro no pudo ser cargado, por favor verifique los datos en la Aplicaci&oacute;n Acad&eacute;mica <br>";
                                                                                                        //echo "<hr class='hr_subtitulo'>";
                                                                                                        }
                                                                                                else{
                                                                                                     echo "<font color='green'><br>El registro se guardo satisfactoriamente como Participante<br></font>";
                                                                                                     $participante++;}
                                                                                                }
                                                                                                unset($registroDatosParticipante);
                                                                                                unset($resultadoParticipante);
                                                                                }
                                                                            else{   //Buscar los datos en la DB Academica de ese registro del graduado para la tabla participante
                                                                                   // $cadena_sql=cadena_busqueda_graduado($configuracion, $acceso_db, $registroPrincipal,"participanteAcademica");
                                                                                    //echo "<br><br>".$cadena_sql;	//exit;
                                                                                   // @$registroDatosParticipante=ejecutar_admin_graduado($cadena_sql,$acceso_db);
                                                                                    if(is_array($registroDatosParticipante))
                                                                                                {
                                                                                                $cadena_sql=cadena_busqueda_graduado($configuracion, $accesoSnies, $registroDatosParticipante,"actualiza_participante");
                                                                                              //  echo "<br>".$cadena_sql;//exit;
                                                                                                @$resultadoParticipante=ejecutar_admin_graduado($cadena_sql,$accesoSnies,"insertar");
                                                                                                unset($resultadoParticipante);
                                                                                                }
                                                                                      unset($registroDatosParticipante);
                                                                                    }
                                                                /*fin carga participante*/


                                                                // ACTUALIZAR ESTUDIANTE
                                                                if(!isset($unError))
                                                                        {
                                                                                        unset($unError);

                                                                                        //buscar los registros de estudiantes en el SNIES

                                                                                        $cadena_sql=cadena_busqueda_graduado($configuracion, $accesoSnies, $valor,"estudianteSnies");
                                                                                        $cadena_sql.=" AND tipo_doc_unico='".$registro[$contador][11]."'";
                                                                                       //  echo "<br>".$cadena_sql;
                                                                                        $registroEstudianteSnies=ejecutar_admin_graduado($cadena_sql,$accesoSnies);


                                                                                        //Verificar que el graduado no esta en la tabla estudiante del SNIES <<EXISTE EL ESTUDIANTE=NO>>

                                                                                        if(!is_array($registroEstudianteSnies))
                                                                                                        {
                                                                                                                        //verifica si hay un registro ya guardado del participante
                                                                                                                        $cadena_sql=cadena_busqueda_graduado($configuracion, $accesoSnies, $valor,"estudianteSnies");
                                                                                                                        //echo $cadena_sql;exit;
                                                                                                                        $EstudianteSnies=ejecutar_admin_graduado($cadena_sql,$accesoSnies);

                                                                                                                        if(is_array($EstudianteSnies) && $EstudianteSnies[0][2]!=$registro[$contador][11])
                                                                                                                                        {
                                                                                                                                        //Buscar los datos en la DB Academica de ese registro del graduado para la tabla estudiante
                                                                                                                                                $graduado_borrar[1]=$EstudianteSnies[0][1];
                                                                                                                                                $graduado_borrar[2]=$EstudianteSnies[0][2];
                                                                                                                                        //	echo $graduado_borrar[1]." ".$graduado_borrar[2];exit;
                                                                                                                                        }

                                                                                                                        $miEstudiante[0]=$registro[$contador];
                                                                                                                        //insertar los datos del graduado en la tabla estudiante del SNIES
                                                                                                                        $cadena_sql=cadena_busqueda_graduado($configuracion, $accesoSnies, $miEstudiante,"insertarEstudiante");
                                                                                                                        //echo "<br>".$cadena_sql;exit;
                                                                                                                        @$resultadoEstudiante=ejecutar_admin_graduado($cadena_sql,$accesoSnies,"insertar");
                                                                                                                        if($resultadoEstudiante==false)
                                                                                                                        {
                                                                                                                                @$resultadoEstudiante=ejecutar_admin_graduado($cadena_sql,$accesoSnies,"error");
                                                                                                                                echo "<font color='red'><br>El registro estudiante no pudo ser cargado por:".$resultadoEstudiante."<br></font>";
                                                                                                                                //echo "El registro no pudo ser cargado, por favor verifique los datos en la Aplicaci&oacute;n Acad&eacute;mica <br>";
                                                                                                                                //echo "<hr class='hr_subtitulo'>";
                                                                                                                        }
                                                                                                                        else{
                                                                                                                             echo "<font color='green'><br>El registro se guardo satisfactoriamente como estudiante<br></font>";       
                                                                                                                            $estudiante++;
                                                                                                                            }
                                                                                                        }

                                                                                }//fin actualiza estudiante


                                                                        // ACTUALIZAR GRADUADO
                                                                        if(!isset($unError))
                                                                                {
                                                                                                unset($unError);

                                                                                                //buscar los registros de estudiantes en el SNIES
                                                                                                $cadena_sql=cadena_busqueda_graduado($configuracion, $accesoSnies, $valor,"graduadoSnies");
                                                                                                $cadena_sql.=" AND tipo_doc_unico='".$registro[$contador][11]."'";
                                                                                                $cadena_sql.=" AND pro_consecutivo='".$registro[$contador][2]."'";
                                                                                               // echo "<br>".$cadena_sql;//exit;
                                                                                                $registroGraduado=ejecutar_admin_graduado($cadena_sql,$accesoSnies);

                                                                                                //Verificar que el graduado no esta en la tabla GRADUADO del SNIES <<EXISTE EL GRADUADO=NO>>
                                                                                                if(!is_array($registroGraduado))

                                                                                                {	//echo "*El Graduado con documento = <span class='texto_negrita'>".$valor."</span> no existe en la tabla GRADUADO (actualice la p&aacute;gina para cargarlo)<br>";

                                                                                                        //verifica si hay un registro ya guardado del participante
                                                                                                        $cadena_sql=cadena_busqueda_graduado($configuracion, $accesoSnies, $valor,"graduadoSnies");
                                                                                                        //echo "<br>".$cadena_sql;
                                                                                                        $GraduadoSnies=ejecutar_admin_graduado($cadena_sql,$accesoSnies);

                                                                                                        if(is_array($GraduadoSnies) && $graduadoSnies[0][13]!=$registro[$contador][11])
                                                                                                                        {
                                                                                                                        //Buscar los datos en la DB Academica de ese registro del graduado para la tabla estudiante
                                                                                                                                $graduado_borrar[1]=$GraduadoSnies[0][3];
                                                                                                                                $graduado_borrar[2]=$GraduadoSnies[0][13];
                                                                                                                        }

                                                                                                        //insertar los datos del graduado en la tabla estudiante del SNIES

                                                                                                        $miRegistro[0]=$registro[$contador];
                                                                                                                $cadena_sql=cadena_busqueda_graduado($configuracion, $accesoSnies, $miRegistro,"insertarGraduado");
                                                                                                                //echo "<br>".$cadena_sql;  exit;
                                                                                                                $resultadoGraduado=ejecutar_admin_graduado($cadena_sql,$accesoSnies,"insertar");
                                                                                                                if($resultadoGraduado==false)
                                                                                                                        {
                                                                                                                                $resultadoGraduado=ejecutar_admin_graduado($cadena_sql,$accesoSnies,"error");
                                                                                                                                echo "<font color='red'><br>El registro graduado no pudo ser cargado por:".$resultadoGraduado."<br></font>";       
                                                                                                                                //echo "El registro no pudo ser cargado, por favor verifique los datos en la Aplicaci&oacute;n Acad&eacute;mica <br>";
                                                                                                                                //echo "<hr class='hr_subtitulo'>";
                                                                                                                        }
                                                                                                                else
                                                                                                                         {
                                                                                                                          echo "<font color='green'><br>El registro se guardo satisfactoriamente como Graduado<br></font>";       
                                                                                                                             $graduado++;
                                                                                                                         }
                                                                                                }
                                                                                              else
                                                                                                 {
                                                                                                  echo "<font color='blue'><br>El registro ya existe como Graduado<br></font>";       
                                                                                                 }
                                                                                }//cierra if de actualizar graduado
                                                                        }//cierra la validacion de datos en blanco de participante
                                                                    else
                                                                        {echo "<font color='red'><br>El registro de graduado contiene datos de participante vacios que son importante reportar.<br>Revise su fuente de informaci&oacute;n!<br></font>";  
                                                                         $sindatos++;
                                                                        }
									if(isset($graduado_borrar))
										{//buscar los registros de estudiantes en el SNIES
											$graduado_borrar[3]=$registro[$contador][14];
											$graduado_borrar[4]=$registro[$contador][15];
                                                                                        $cadena_sql=cadena_busqueda_graduado($configuracion, $accesoSnies, $graduado_borrar,"borraGraduado");
											//echo "<br>".$cadena_sql;
											//$borraoGraduado=ejecutar_admin_graduado($cadena_sql,$accesoSnies);

											//$graduado--;
										 unset($graduado_borrar);
										 }
								
								
							}//cierra el if de verificacion de codigo y documento
						   else
                                                        {
                                                            echo $logError;
                                                            echo "<font color='red'><br>El registro de graduado contiene datos vacios que son importante reportar.<br>Revise su fuente de informaci&oacute;n!<br></font>";  
                                                        $sindatos++;
                                                        }
						}//cierra el for(contador)
						
					}		
					
			
											
						//VARIABLES PARA EL LOG
						$logger[0]="CARGAR AL SNIES LOCAL";
						$logger[1]="Participante";
						$logger[2]=$inicialParticipante[0][0];
						$logger[3]=$participante;
                                                date_default_timezone_set('UTC');
                                                $logger[4]=date('d/m/Y h:i:s',time());
                                               	$logger[5]=$cubrimiento[0];
						$logger[6]=$cubrimiento[1];
						$logger[7]="Se cargo los datos, para el reporte de graduados al SNIES para el periodo ".$cubrimiento[0]."-".$cubrimiento[1];
						
						$log_us->log_usuario($logger,$configuracion);
						
						$logger[1]="Estudiante";
						$logger[2]=$inicialEstudiante[0][0];
						$logger[3]=$estudiante;
						$log_us->log_usuario($logger,$configuracion);
						
						$logger[1]="Graduado";
						$logger[2]=$inicialGraduados[0][2];
						$logger[3]=$graduado;
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
											<td>GRADUADO</td>
											<td><? echo $graduado;?></td>
										</tr>
                                                                                <tr class="bloquecentralcuerpo">
											<td>SIN CARGAR <br>(por falta de informaci&oacute;n)</td>
											<td><? echo $sindatos;?></td>
										</tr>
										</td>
										</tr>
									</table>
									</td>
								</tr>
							</tbody>
						</table>
						<?
						
		}//cierra el if(isset($_REQUEST["annio"]) and isset($_REQUEST["periodo"]))
		
	
	$cadena_sql=cadena_busqueda_graduado($configuracion, $accesoSnies,"","totalesGraduadosSnies");
	//echo $cadena_sql; //exit;
	$registroGraduadosTotales=ejecutar_admin_graduado($cadena_sql,$accesoSnies);
	

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
								.::: Datos Graduados
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
												TOTAL GRADUADOS
												</td>					
											</tr>	
											
											<?$n=0;
										while (count($registroGraduadosTotales)>$n)
											{	
												if (is_array($registroGraduadosTotales[$n]))
												{?>
												
												<tr>
													<td class="cuadro_plano centrar">
													<span class="texto_negrita"><? echo $totalGraduado=$registroGraduadosTotales[$n][0];?></span> 
													</td>
													<td class="cuadro_plano centrar">
													<span class="texto_negrita"><? echo $totalGraduado=$registroGraduadosTotales[$n][1];?></span>
													</td>
													<td class="cuadro_plano centrar centrar">
													<span class="texto_negrita"><? echo $totalGraduado=$registroGraduadosTotales[$n][2];?></span>
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

function cadena_busqueda_graduado($configuracion, $acceso_db, $valor,$opcion="")
{
	//$valor=$acceso_db->verificar_variables($valor);
	
	switch($opcion)
	{
	
		//consulta para generar los datos para la tabla graduados del SNIES a partir de la DB Academica
		case "graduado":	
			$prefijo="mntac.";			
			$cadena_sql="SELECT UNIQUE ";
			$cadena_sql.="TO_CHAR('1301') ies_code, ";
			$cadena_sql.="TO_CHAR(egr_nro_iden) codigo_unico, ";
			$cadena_sql.="TO_CHAR(as_cra_cod_snies) pro_consecutivo, ";
			$cadena_sql.="TO_CHAR(egr_fecha_grado,'yyyy-mm-dd') fecha_grado, ";
			$cadena_sql.="'no' ecaes_observaciones, ";
			$cadena_sql.="'0' ecaes_resultados, ";
			$cadena_sql.="'11'departamento, ";
			$cadena_sql.="'11001' municipio, ";
			$cadena_sql.="'1301' codigo_ent_aula, ";
			$cadena_sql.="TO_CHAR(egr_acta_grado) acta, ";
                        $cadena_sql.="TO_CHAR(DECODE(egr_folio,'','N/A',egr_folio)) folio, ";
			$cadena_sql.="TO_CHAR(DECODE(egr_tip_iden,'C','CC','T','TI','CC')) tipo_doc_unico, ";
			$cadena_sql.="TO_CHAR(DECODE(eot_nro_snp,'','N/A',NULL,'N/A',replace(eot_nro_snp,' ',''))) snp,  ";
			$cadena_sql.="egr_fecha_grado fecha, ";
			$cadena_sql.="TO_CHAR(egr_fecha_grado,'yyyy') anno_grado, ";
			$cadena_sql.="TO_CHAR(egr_fecha_grado,'mm') mes_grado, ";
			$cadena_sql.="egr_est_cod codigo_estudiante, ";
                        $cadena_sql.=" as_cra_nom ";
			$cadena_sql.="FROM ".$prefijo."acegresado";
			$cadena_sql.=" INNER JOIN ".$prefijo."acestotr ON egr_est_cod = eot_cod ";
			$cadena_sql.=" INNER JOIN ".$prefijo."accra_snies ON egr_cra_cod = as_cra_cod ";
			$cadena_sql.=" INNER JOIN ".$prefijo."accra ON egr_cra_cod = cra_cod ";
			$cadena_sql.=" INNER JOIN ".$prefijo."actipcra ON cra_tip_cra = tra_cod ";						
			$cadena_sql.=" WHERE egr_fecha_grado is not null ";
			$cadena_sql.=" AND egr_nro_iden <> 101";
			$cadena_sql.=" AND egr_estado = 'A'";
			$cadena_sql.=" AND TO_NUMBER(TO_CHAR(egr_fecha_grado,'yyyy'))=";
			$cadena_sql.=$valor[0];
			$cadena_sql.=" AND DECODE(TO_NUMBER(TO_CHAR(egr_fecha_grado,'mm')),1,1,2,1,3,1,4,1,5,1,6,1,7,3,8,3,9,3,10,3,11,3,12,3)=";
			$cadena_sql.=$valor[1];
			$cadena_sql.=" ORDER BY egr_fecha_grado";
			
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
			
			
			
			
		//Consulta de la DB Academica para generar los datos de la tabla participante del SNIES para los graduados
		case "participanteAcademica":

            //nuevo para participante *cuando no existe fecha de nacimiento se coloca por defecto la fecha 1980-02-29
                        $cadena_sql="SELECT ";
                        $cadena_sql.="TO_CHAR('1301') ies_code, ";
                        $cadena_sql.="SUBSTR(trim(est_nombre),0,INSTR(trim(est_nombre),' ',1,1)) primer_apellido, ";
                        $cadena_sql.="case
                                        when (instr(trim(substr(trim(est_nombre),instr(trim(est_nombre),' ',1,1),length(trim(est_nombre)))),' ',1,2) > 0
                                        or instr(trim(substr(trim(est_nombre),instr(trim(est_nombre),' ',1,1),length(trim(est_nombre)))),' ',1,3) > 0
                                        or instr(trim(substr(trim(est_nombre),instr(trim(est_nombre),' ',1,1),length(trim(est_nombre)))),' ',1,4) > 0)
                                        then trim(substr(trim(substr(trim(est_nombre),1,instr(trim(est_nombre),(trim(substr(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))),instr(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))),' ',-1,1),length(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))))))),1,1) - 1)),instr(trim(substr(trim(est_nombre),1,instr(trim(est_nombre),(trim(substr(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))),instr(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))),' ',-1,1),length(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))))))),1,1) - 1)),' ',-1,1),length(trim(substr(trim(est_nombre),1,instr(trim(est_nombre),(trim(substr(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))),instr(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))),' ',-1,1),length(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))))))),1,1) - 1)))))
                                        else trim(substr(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))),instr(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))),' ',-1,1),length(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))))))
                                     end segundo_apellido, ";
                        $cadena_sql.="case
                                        when (instr(trim(substr(trim(est_nombre),instr(trim(est_nombre),' ',1,1),length(trim(est_nombre)))),' ',1,2) > 0
                                        or instr(trim(substr(trim(est_nombre),instr(trim(est_nombre),' ',1,1),length(trim(est_nombre)))),' ',1,3) > 0
                                        or instr(trim(substr(trim(est_nombre),instr(trim(est_nombre),' ',1,1),length(trim(est_nombre)))),' ',1,4) > 0)
                                        then trim(substr(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))),instr(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))),' ',-1,1),length(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))))))
                                        else trim(substr(est_nombre,instr(est_nombre,' ',-1,1),length(trim(est_nombre))))
                                      end primer_nombre,  ";
                        $cadena_sql.="case
                                        when (instr(trim(substr(trim(est_nombre),instr(trim(est_nombre),' ',1,1),length(trim(est_nombre)))),' ',1,2) > 0
                                        or instr(trim(substr(trim(est_nombre),instr(trim(est_nombre),' ',1,1),length(trim(est_nombre)))),' ',1,3) > 0
                                        or instr(trim(substr(trim(est_nombre),instr(trim(est_nombre),' ',1,1),length(trim(est_nombre)))),' ',1,4) > 0)
                                        then trim(substr(est_nombre,instr(est_nombre,' ',-1,1),length(trim(est_nombre))))
                                        else ' '
                                      end segundo_nombre,  ";
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
                        $cadena_sql.="est_nro_iden='".$valor[0][1]."' ";
                        $cadena_sql.="and est_cod=".$valor[0][16]." ";
                        $cadena_sql.="ORDER BY est_cod DESC";
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
			
			
		//Consulta de la DB Academica para generar los datos de la tabla participante del SNIES para los graduados
		
		case "insertarParticipante":
			$cadena_sql="INSERT INTO ";
			$cadena_sql.="participante ";
			$cadena_sql.="VALUES ";
			$cadena_sql.="( ";
			$cadena_sql.="'".$valor[0][0]."', ";
                        $cadena_sql.="replace('".$valor[0][1]."','?','') ,";
			$cadena_sql.="replace('".$valor[0][2]."','?','') ,";
                        $cadena_sql.="replace('".$valor[0][3]."','?','') ,";
                        $cadena_sql.="replace('".$valor[0][4]."','?','') ,";
                        if ($valor[0][5]!=""){
			$cadena_sql.="'".$valor[0][5]."', ";
			}
			else
			{
			$cadena_sql.="'1980-01-01', ";
			}
			$cadena_sql.="'".$valor[0][6]."', ";
			$cadena_sql.="'".$valor[0][7]."', ";
			$cadena_sql.="'".$valor[0][8]."', ";
			//$cadena_sql.="'11', ";						//con esto se carga por defecto el departamento '11' BOGOTA'
			//$cadena_sql.="'11001', ";				//con esto se carga por defecto el municipio '11001' BOGOTA D.C'
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
	
			//echo "tel=-".$valor[0][12]."-";
			//echo $cadena_sql."<br><br>";
			//exit;
			
	        break;
			        
		case "actualiza_participante":
			$cadena_sql="UPDATE participante";
			$cadena_sql.=" SET ";
			$cadena_sql.="primer_apellido=";
			$cadena_sql.="replace('".$valor[0][1]."','?','') ,";
                        $cadena_sql.="segundo_apellido= ";
			$cadena_sql.="replace('".$valor[0][2]."','?','') ,";
                        $cadena_sql.="primer_nombre= ";
			$cadena_sql.="replace('".$valor[0][3]."','?','') ,";
                        $cadena_sql.="segundo_nombre= ";
			$cadena_sql.="replace('".$valor[0][4]."','?','') ,";
                        $cadena_sql.="fecha_nacim= ";
			$cadena_sql.="'".$valor[0][5]."',";
                        $cadena_sql.="departamento_ln= ";
			$cadena_sql.="'".$valor[0][7]."',";
                        $cadena_sql.="municipio_ln= ";
			$cadena_sql.="'".$valor[0][8]."',";
                        $cadena_sql.="genero_code= ";
			$cadena_sql.="'".$valor[0][9]."',";
                        $cadena_sql.="email= ";
			$cadena_sql.="'".$valor[0][10]."',";
                        $cadena_sql.="est_civil_code= ";
			$cadena_sql.="'".$valor[0][11]."' ";
                        $cadena_sql.="WHERE ";
			$cadena_sql.="codigo_unico=";
                        $cadena_sql.=" '".$valor[0][13]."' ";//numero doc
                        //$cadena_sql.="AND tipo_doc_unico=";
                       // $cadena_sql.=" '".$valor[0][12]."' ";//numero doc

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
			
			//echo $cadena_sql;
			//exit;
			
			break;
		
		
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
		
		
		
				//Consulta de la DB Academica para generar los datos de la tabla participante del SNIES para los graduados
		
		case "insertarEstudiante":
			$cadena_sql="INSERT INTO ";
			$cadena_sql.="estudiante ";
			$cadena_sql.="VALUES ";
			$cadena_sql.="( ";
			$cadena_sql.="'".$valor[0][0]."', ";			//ies_code
			$cadena_sql.="'".$valor[0][1]."', ";			//numero doc
			$cadena_sql.="'".$valor[0][11]."' ";		//tipo doc
			$cadena_sql.=") ";
	
			//Con esta cadena se pueden obtener las consultas para subir manualmente algunos registros
			//echo $cadena_sql."<br>";
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
		
		
				//Consulta de la tabla participante del SNIES LOCAL
		
		case "graduadoSnies":
			$cadena_sql="SELECT ";
			$cadena_sql.="ies_code, ";					//[0]
			$cadena_sql.="grad_annio, ";
			$cadena_sql.="grad_semestre,";
			$cadena_sql.="codigo_unico,";
			$cadena_sql.="pro_consecutivo, ";
			$cadena_sql.="fecha_grado, ";				//[5]
			$cadena_sql.="ecaes_observaciones, ";
			$cadena_sql.="ecaes_resultados, ";
			$cadena_sql.="departamento, ";
			$cadena_sql.="municipio, ";
			$cadena_sql.="codigo_ent_aula, ";
			$cadena_sql.="acta, ";
			$cadena_sql.="folio, ";
			$cadena_sql.="tipo_doc_unico, ";  			//[13]
			$cadena_sql.="snp ";
			$cadena_sql.="FROM graduado ";
			$cadena_sql.="WHERE ";
			$cadena_sql.="codigo_unico=".$valor[2]." " ;
			
			//echo $cadena_sql;
			//exit;
			
			break;
		
		
		
	case "insertarGraduado":
			//echo $valor[0][4]."<br> ";
			$cadena_sql="INSERT INTO ";
			$cadena_sql.="graduado ";
			$cadena_sql.="VALUES ";
			$cadena_sql.="(";
			$cadena_sql.="'".$valor[0][0]."', ";	
			$cadena_sql.="'".$valor[0][14]."', ";
			if ($valor[0][15]<=6){
				$cadena_sql.="'01', ";
				}
				else
				{
				$cadena_sql.="'02', ";
				}
			$cadena_sql.="'".$valor[0][1]."', ";		
			$cadena_sql.="'".$valor[0][2]."', ";		
			$cadena_sql.="'".$valor[0][3]."', ";		
			if ($valor[0][4]!=""){
			$cadena_sql.="'".$valor[0][4]."', ";
			}
			else
			{
			$cadena_sql.="0, ";
			}	
			$cadena_sql.="'".$valor[0][5]."', ";		
			$cadena_sql.="'".$valor[0][6]."', ";		
			$cadena_sql.="'".$valor[0][7]."', ";		
			$cadena_sql.="'".$valor[0][8]."', ";		
			//$cadena_sql.="'".$valor[0][9]."', ";
                        if ($valor[0][9]!="")
                            {$cadena_sql.="'".$valor[0][9]."', ";}
			else
                            {$cadena_sql.="'0',";}
			//$cadena_sql.="'".$valor[0][10]."', ";
                        if ($valor[0][10]!="")
                            {$cadena_sql.="'".$valor[0][10]."', ";}
			else
                            {$cadena_sql.="'0',";}
			$cadena_sql.="'".$valor[0][11]."', ";		
			if ($valor[0][12]!="")
                            {$cadena_sql.="'".$valor[0][12]."' ";}
			else
                            {$cadena_sql.="'0'";}
			$cadena_sql.=") ";
	
			//Con esta cadena se pueden obtener las consultas para subir manualmente algunos registros
			//echo "ano=".$valor[0][9]."<br>";
			//echo "mes=".$valor[0][16];
		//	echo $cadena_sql."<br>";
			//exit;
			
	        break;
	        
	     	case "borraGraduado":
				$cadena_sql="DELETE ";
				$cadena_sql.="FROM ";
				$cadena_sql.="graduado ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="codigo_unico=";			//numero doc
				$cadena_sql.="'".$valor[1]."' ";		//tipo doc
				$cadena_sql.="AND tipo_doc_unico=";
				$cadena_sql.="'".$valor[2]."' ";
				$cadena_sql.="AND grad_annio=";
				$cadena_sql.="'".$valor[3]."' ";
				$cadena_sql.="AND grad_semestre=";
				if ($valor[4]<=6){
				$cadena_sql.="'01' ";
				}
				else
				{
				$cadena_sql.="'02' ";
				}
	
				//echo $cadena_sql."<br>";
				//exit;
			
	        break;
		
		
	        
			
		case "totalesGraduadosSnies":
					$cadena_sql="Select ";			
					$cadena_sql.="grad_annio, ";
					$cadena_sql.="grad_semestre, ";					//[0]
					$cadena_sql.="count (*)  ";			
					$cadena_sql.="from  graduado ";
					if($valor!="")
						{$cadena_sql.="where ";
						 $cadena_sql.="grad_annio=";			
						 $cadena_sql.="'".$valor[0]."' ";		
						 $cadena_sql.="AND grad_semestre=";
						 if($valor[1]==2 || $valor[1]==3)
							{
							$cadena_sql.="'02' " ;
							}
						 else		
							{
							$cadena_sql.="'01' " ;
							}
						}
					$cadena_sql.="group by grad_semestre, grad_annio ";
					$cadena_sql.="order by grad_annio DESC, grad_semestre DESC";			
					
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

function ejecutar_admin_graduado($cadena_sql,$acceso_db,$tipo="")
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
