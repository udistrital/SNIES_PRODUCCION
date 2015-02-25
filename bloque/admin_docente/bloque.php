<?
/*
############################################################################
#    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        #
#    Copyright: Vea el archivo LICENCIA.txt que viene con la distribucion  #
############################################################################
*/
/****************************************************************************
* @name          bloque.php 
* @revision     Ultima revision 05 de agosto de 2008
*****************************************************************************
* @subpackage   admin_docente
* @package	bloques
* @copyright    
* @version      0.3
* @link		N/D
* @description  Bloque principal para la administracion de docentes
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
include_once($configuracion["raiz_documento"].$configuracion["clases"]."/log.class.php");
include_once($configuracion["raiz_documento"].$configuracion["clases"]."/navegacion.class.php");

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
		
	//Rescatar los inscritos de la base de datos academica
	// si se envia la variable de annio y/o periodo 
		$valor[0]=$_REQUEST["annio"];
		//echo "annio=".$valor[0]."<br>";
		
		$valor[1]=$_REQUEST["periodo"];
		//echo "periodo=".$valor[1];
		$cubrimiento[0]=$valor[0];//anno
		if($valor[1]==2)
			{$cubrimiento[1]=3;//periodo
			}
		else
			{$cubrimiento[1]=$valor[1];//periodo
			}
		$participante=0;
		$docente=0;
		
		$cadena_sql=cadena_busqueda_docente($configuracion, $accesoSnies,"","totalParticipanteSnies");
		//echo "<br>".$cadena_sql; 
		$inicialParticipante=ejecutar_admin_docente($cadena_sql,$accesoSnies);
		//echo "<br>".$inicialParticipante[0][0];
		$cadena_sql=cadena_busqueda_docente($configuracion, $accesoSnies,"","totalesDocentesSnies");
		//echo "<br>".$cadena_sql; 
		$inicialDocente=ejecutar_admin_docente($cadena_sql,$accesoSnies);
		//echo "<br>".$inicialDocente[0][2];
		
		
	?>
	
<table width="100%" align="center" border="0" cellpadding="10" cellspacing="0" >
	<tbody>
		<tr>
			<td >
				<table width="100%" border="0" align="center" cellpadding="5 px" cellspacing="1px" >
					<tr class="centralcuerpo">
						<td>
						.::: Actualizaci&oacute;n Docentes <? echo $valor[0]." - ".$valor[1];?>
						</td>
					</tr>	
				</table>
			</td>
		</tr>
	</tbody>
</table>
			

			<?		
			
				
	
	//Rescatar TODOS los docentes de un periodo y anno especifico
	
	$cadena_sql=cadena_busqueda_docente($configuracion, $acceso_db, $valor,"docente");		
	//echo $cadena_sql."<br>";exit;
	$registro=ejecutar_admin_docente($cadena_sql,$acceso_db);
	
	
	if(!is_array($registro))
	{	
		$cadena=htmlentities($cadena, ENT_COMPAT, "UTF-8");
		alerta::sin_registro($configuracion,$cadena);	
	}
	else
	{
		$contar=count($registro);	
	echo $contar;	
		
		for($contador=0;$contador<$contar;$contador++)
		{
			//echo "<hr><br>".$contador."<br>";
                        $registroPrincipal[0]=$registro[$contador];		//Que contiene los datos de la busqueda inicial
			echo "<hr>".$contador." - Documento Docente:<h3>".$registroPrincipal[0][3]." ".$registroPrincipal[0][1]."</h3>";
                        
			//se selecciona cada registro de docentes, uno a la vez	
			unset($valor[2]);
			unset($unError);
			$valor[2]=$registro[$contador][1];  //Captura el numero de cedula
			//echo $valor[2]."<br>";
			//exit;
			
			if($valor!="")
			{			
				
				
				//ACTUALIZAR PARTICIPANTE
				
				//buscar los registros de participantes en el SNIES para los docentes
				$cadena_sql=cadena_busqueda_docente($configuracion, $accesoSnies, $valor,"participanteSnies");	
				$cadena_sql.=" AND tipo_doc_unico='".$registro[$contador][3]."'";//tipo documento	
				//echo "<br>".$cadena_sql;exit;
				@$registroParticipanteSnies=ejecutar_admin_docente($cadena_sql,$accesoSnies);

				
				//Verificar que el docente no esta en la tabla participante del SNIES <<EXISTE EL PARTICIPANTE=NO>> 
				
				if(!is_array($registroParticipanteSnies))
				
				{
					
                                        //Buscar los datos en la DB Academica de ese registro del primiparo para la tabla participante
					$cadena_sql=cadena_busqueda_docente($configuracion, $acceso_db, $valor,"participanteAcademica");
					//echo "<br>part_acad<br>".$cadena_sql;exit;
					$registroDatosParticipante=ejecutar_admin_docente($cadena_sql,$acceso_db);
					
                                                        //si no se obtiene un arreglo de la base de datos académica se debe saltar el registro
							if(!is_array($registroDatosParticipante))
								{
								//echo "esto es un :".$registroDatosParticipante;
								//echo "eso no esun arreglo continua con el siguiente registro<br><br>";
								echo "*El docente con documento = <span class='texto_negrita'>".$valor[2]."</span> no existe en la tabla PARTICIPANTE<br>";
								
								}
							
							else{
								//verifica si hay un registro ya egresado del participante
								$cadena_sql=cadena_busqueda_docente($configuracion, $accesoSnies, $valor,"participanteSnies");	
								//echo "<br><br>".$cadena_sql;
								@$ParticipanteSnies=ejecutar_admin_docente($cadena_sql,$accesoSnies);
																			
								if(is_array($ParticipanteSnies) && $ParticipanteSnies[0][0]!=$registro[$contador][3])
										{
                                                                                        //Buscar los datos en la DB Academica de ese registro del graduado para la tabla estudiante
											$docente_borrar[1]=$ParticipanteSnies[0][1];//numero documento
											$docente_borrar[2]=$ParticipanteSnies[0][0];//tipo documento
											//echo $docente_borrar[1]." ".$docente_borrar[2];
										}
								
								//verifica que el tipo de documento a cargar sea el mismo si no lo unifica
								if ($registroDatosParticipante[0][12]!=$registro[$contador][3])
										{$registroDatosParticipante[0][12]=$registro[$contador][3];}
										
								//insertar los datos del graduado en la tabla participante del SNIES					
								$cadena_sql=cadena_busqueda_docente($configuracion, $accesoSnies, $registroDatosParticipante,"insertarParticipante");
								//echo $cadena_sql;exit;
								@$resultadoParticipante=ejecutar_admin_docente($cadena_sql,$accesoSnies,"insertar");
								if($resultadoParticipante==false)
								{
									$resultadoParticipante=ejecutar_admin_docente($cadena_sql,$accesoSnies,"error");
									echo "El registro docente no pudo ser cargado en la tabla participante por:".$resultadoParticipante."<br>";
									$unError=true;
									//echo "El registro no pudo ser cargado, por favor verifique los datos en la Aplicaci&oacute;n Acad&eacute;mica <br>";
									//echo "<hr class='hr_subtitulo'>";
									}
								else{$participante++;}
								}	
				}
			
			
			
				// ACTUALIZAR DOCENTE
				if(!isset($unError))
				{
						unset($unError);
						
						//buscar los registros de docentes en el SNIES
						//echo "hata aqui llego";
						$cadena_sql=cadena_busqueda_docente($configuracion, $accesoSnies, $valor,"docenteSnies");
						$cadena_sql.=" AND tipo_doc_unico='".$registro[$contador][3]."'";//tipo documento	
						//echo "<br>".$cadena_sql;//	exit;
						$registroDocenteSnies=ejecutar_admin_docente($cadena_sql,$accesoSnies);
						
						
						//Verificar que el docente no esta en la tabla estudiante del SNIES <<EXISTE EL DOCENTE=NO>> 
						//echo "<br>doc: ".$registroDocenteSnies[0][0]."<br>";
						if(!is_array($registroDocenteSnies))
						
						{	
						
							//Buscar los datos en la DB Academica de ese registro del docente para la tabla DOCENTE
							$cadena_sql=cadena_busqueda_docente($configuracion, $acceso_db, $valor,"docenteAcademica");
							//echo "<br> doc acad ".$cadena_sql;			exit;
							$registroDatosDocente=ejecutar_admin_docente($cadena_sql,$acceso_db);
							//si no se obtiene un arreglo de la base de datos académica se debe saltar el registro
							if(!is_array($registroDatosDocente))
								{
								//echo "esto es un :".$registroDatosParticipante;
								//echo "eso no esun arreglo continua con el siguiente registro<br><br>";
								echo "*El docente con documento = <span class='texto_negrita'>".$valor[2]."</span> no existe en la tabla DOCENTE<br>";
								
								}
							
							else{
													
								//verifica que el tipo de documento a cargar sea el mismo si no lo unifica
								if ($registroDatosDocente[0][4]!=$registro[$contador][3])
										{$registroDatosDocente[0][4]=$registro[$contador][3];}
										
								//insertar los datos del graduado en la tabla participante del SNIES					
								$cadena_sql=cadena_busqueda_docente($configuracion, $accesoSnies, $registroDatosDocente,"insertarDocente");		
								//echo $cadena_sql;exit;		
								@$resultadoDocente=ejecutar_admin_docente($cadena_sql,$accesoSnies,"insertar");
								if($resultadoDocente==false)
								{
									$resultadoDocente=ejecutar_admin_docente($cadena_sql,$accesoSnies,"error");
									echo "El registro docente no pudo ser cargado en la tabla docente por:".$resultadoDocente."<br>";
									$unError=true;
									//echo "El registro no pudo ser cargado, por favor verifique los datos en la Aplicaci&oacute;n Acad&eacute;mica <br>";
									//echo "<hr class='hr_subtitulo'>";
									}
								}

						}
				}
						
				
				
                                // ACTUALIZAR DOCENTE_UNIDAD
				if(!isset($unError))
				{
						unset($unError);
						
						//buscar los registros de DOCENTE_UNIDAD en el SNIES
						
						$cadena_sql=cadena_busqueda_docente($configuracion, $accesoSnies, $valor,"docenteUnidadSnies");	
						//echo "<br>".$cadena_sql;exit;	
						$registroDocenteUnidad=ejecutar_admin_docente($cadena_sql,$accesoSnies);
						
				
						
						//Verificar que el docente no esta en la tabla DOCENTE_UNIDAD del SNIES <<EXISTE EL DOCENTE=NO>> 
						
						if(!is_array($registroDocenteUnidad))
						
						{	//echo "NO EXISTE";
							
								//Buscar los datos en la DB Academica de ese registro del docente para la tabla DOCENTE_UNIDAD
								$cadena_sql=cadena_busqueda_docente($configuracion, $acceso_db, $valor,"docenteUnidadAcademica");		
								//echo "<br>".$cadena_sql;//exit;			
								$registroDatosDocenteUnidad=ejecutar_admin_docente($cadena_sql,$acceso_db);
								
								
								if(!is_array($registroDatosDocenteUnidad))
									{
									//echo "esto es un :".$registroDatosParticipante;
									//echo "eso no esun arreglo continua con el siguiente registro<br><br>";
									echo "*El docente con documento = <span class='texto_negrita'>".$valor[2]."</span> no existe en la tabla DOCENTE_unidad<br>";
									
									}
								
								else{
																	
									//verifica que el tipo de documento a cargar sea el mismo si no lo unifica
									if ($registroDatosDocenteUnidad[0][4]!=$registro[$contador][3])
											{$registroDatosDocenteUnidad[0][4]=$registro[$contador][3];}
											
									//insertar los datos del graduado en la tabla participante del SNIES
									$miRegistro[0]=$registroDatosDocenteUnidad[0];					
									$cadena_sql=cadena_busqueda_docente($configuracion, $accesoSnies, $miRegistro,"insertarDocenteUnidad");			
									//echo $cadena_sql;exit;
									@$resultadoDocenteUnidad=ejecutar_admin_docente($cadena_sql,$accesoSnies,"insertar");
									if($resultadoDocenteUnidad==false)
									{
										$resultadoDocenteUnidad=ejecutar_admin_docente($cadena_sql,$accesoSnies,"error");
										echo "El registro docente no pudo ser cargado en la tabla docente_unidad por:".$resultadoDocenteUnidad."<br>";
										$unError=true;
										//echo "El registro no pudo ser cargado, por favor verifique los datos en la Aplicaci&oacute;n Acad&eacute;mica <br>";
										//echo "<hr class='hr_subtitulo'>";
										}
								}
				}
					
				}//fin actualiza docente_unidad
				
				// ACTUALIZAR DOCENTE_H
				if(!isset($unError))
				{
						unset($unError);
						
						//buscar los registros de DOCENTE_H en el SNIES
						
						$cadena_sql=cadena_busqueda_docente($configuracion, $accesoSnies, $valor,"docenteHSnies");	
						//$cadena_sql.=" AND tipo_doc_unico='".$registro[$contador][3]."'";//tipo documento	
						//echo "<br>".$cadena_sql;exit;
						$registroDocenteH=ejecutar_admin_docente($cadena_sql,$accesoSnies);
						
						
				
						
						//Verificar que el docente no esta en la tabla DOCENTE_H del SNIES <<EXISTE EL DOCENTE_H=NO>> 						
						if(!is_array($registroDocenteH))
						
						{	
								//Buscar los datos en la DB Academica de ese registro del docente para la tabla DOCENTE_H
								$cadena_sql=cadena_busqueda_docente($configuracion, $acceso_db, $valor,"docenteHAcademica");	
								
								$registroDatosDocenteH=ejecutar_admin_docente($cadena_sql,$acceso_db);
								//echo "<br>"."<br>".$cadena_sql;//exit;
								if(is_array($registroDatosDocenteH)){   //Valida que el docente tenga vinculacion para el periodo de consulta con la UD
                                                                
                                                                        //verifica que el tipo de documento a cargar sea el mismo si no lo unifica
                                                                        if ($registroDatosDocenteH[0][18]!=$registro[$contador][3])
                                                                                        {$registroDatosDocenteH[0][18]=$registro[$contador][3];}


                                                                        //insertar los datos del docente en la tabla DOCENTE_UNIDAD del SNIES
                                                                        $miRegistro[0]=$registroDatosDocenteH[0];


                                                                        $cadena_sql=cadena_busqueda_docente($configuracion, $accesoSnies, $miRegistro,"insertarDocente_h");
                                                                        //echo "<br>".$cadena_sql;exit;
                                                                        @$resultadoDocenteH=ejecutar_admin_docente($cadena_sql,$accesoSnies,"insertar");


                                                                        if($resultadoDocenteH==false)
                                                                                {
                                                                                        @$resultadoDocenteH=ejecutar_admin_docente($cadena_sql,$accesoSnies,"error");
                                                                                        //echo "El registro no pudo ser cargado por:".$resultadoDocenteH."<br>";
                                                                                        echo "El registro no pudo ser cargado, por favor verifique los datos en la Aplicaci&oacute;n Acad&eacute;mica <br>";
                                                                                        echo "<hr class='hr_subtitulo'>";
                                                                                }
                                                                        else    {echo "El docente se registro exitosamente  <br>";
                                                                                 $docente++;
                                                                                }
                                                                }
						}
						else
						{
							//Buscar los datos en la DB Academica de ese registro del docente para la tabla DOCENTE_H
								$cadena_sql=cadena_busqueda_docente($configuracion, $acceso_db, $valor,"docenteHAcademica");	
								//echo "<br>".$cadena_sql;				
								$registroDatosDocenteH=ejecutar_admin_docente($cadena_sql,$acceso_db);
								//exit;	
							if(is_array($registroDatosDocenteH))
								{		$cadena_sql=cadena_busqueda_docente($configuracion, $accesoSnies, $registroDatosDocenteH,"Actualiza_docenteH");	
										//echo "<br>".$cadena_sql;//exit;
										@$registroDocenteH=ejecutar_admin_docente($cadena_sql,$accesoSnies);
								}
						}
					}	//fin actualiza docenteH	
				}//Fin if valor!=""
			}//final for
			
		}		
		date_default_timezone_set('UTC');
		$logger[0]="CARGAR AL SNIES LOCAL";
		$logger[1]="Participante";
		$logger[2]=$inicialParticipante[0][0];
		$logger[3]=$participante;
		$logger[4]=date('d/m/Y h:i:s',time());
		$logger[5]=$cubrimiento[0];
		$logger[6]=$cubrimiento[1];
		$logger[7]="Se cargo los datos, para el reporte de docentes al SNIES para el periodo ".$cubrimiento[0]."-".$cubrimiento[1];
		
		$log_us->log_usuario($logger,$configuracion);
		
		$logger[1]="Docente";
		$logger[2]=$inicialDocente[0][2];
		$logger[3]=$docente;
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
											<td>DOCENTE</td>
											<td><? echo $docente;?></td>
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
		
	
	$cadena_sql=cadena_busqueda_docente($configuracion, $accesoSnies,"","totalesDocentesSnies");
	//echo $cadena_sql; exit;
	$registroMatriculadoTotales=ejecutar_admin_docente($cadena_sql,$accesoSnies);
	

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
						.::: Datos Docentes
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
										TOTAL DOCENTES
										</td>					
									</tr>	
									
									<?$n=0;
								while (count($registroMatriculadoTotales)>$n)
									{	
										if (is_array($registroMatriculadoTotales[$n]))
										{?>
										
										<tr>
											<td class="cuadro_plano centrar">
											<span class="texto_negrita"><? echo $totalAdmitido=$registroMatriculadoTotales[$n][0];?></span> 
											</td>
											<td class="cuadro_plano centrar">
											<span class="texto_negrita"><? echo $totalAdmitido=$registroMatriculadoTotales[$n][1];?></span>
											</td>
											<td class="cuadro_plano centrar centrar">
											<span class="texto_negrita"><? echo $totalAdmitido=$registroMatriculadoTotales[$n][2];?></span>
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
<?}
/****************************************************************
*  			Funciones				*
****************************************************************/




//Esta funcion sirve para definir la(s) clausula(s) SQL que se utilizan en este bloque

function cadena_busqueda_docente($configuracion, $acceso_db, $valor,$opcion="")
{
	$valor=$acceso_db->verificar_variables($valor);
	
	switch($opcion)
	{
	
		//consulta para generar los datos para la tabla docentes del SNIES a partir de la DB Academica
		case "docente":	
			$prefijo="mntac.";			
			$cadena_sql="SELECT DISTINCT ";
			$cadena_sql.="'1301' IES_CODE, ";
			$cadena_sql.="doc_nro_iden CODIGO_UNICO, ";
			$cadena_sql.="DECODE(doc_nivel_estudio, 'postdoctorado', '01', 'doctorado', '02', 'maestria', '03', 'especializacion', '04', 'pregrado', '05', 'licenciatura', '06', 'tecnologia', '07', 'tecnico', '08', '05') NIVEL_EST_CODE, ";
			$cadena_sql.="TO_CHAR(DECODE(doc_tip_iden,'',DECODE(length(doc_nro_iden),11,'TI',12,'TI','CC'),'C', 'CC', '1', 'CC', 'c', 'CC', 'T', 'TI', '2', 'TI', 't', 'TI', 'E', 'CE', 'P', 'PS', 'CC')) tipo_doc_unico, ";
                        //$cadena_sql.="DECODE(DOC_TIP_IDEN,'',DECODE(length(doc_nro_iden),11,'TI','CC'),'C', 'CC', 'T','TI', 'E', 'CE', 'P', 'PS', DOC_TIP_IDEN ) tipo_doc_unico2, ";
			$cadena_sql.="decode(doc_fecha_desde,'0','1980-02-29','','1980-02-29', TO_CHAR(doc_fecha_desde, 'yyyy-mm-dd'))  fecha_ingreso ";
                        //$cadena_sql.="doc_fecha_desde FECHA_INGRESO2 ";
			$cadena_sql.="FROM ";
			$cadena_sql.=$prefijo."acdocente ";
			$cadena_sql.=" WHERE ";
			$cadena_sql.=" doc_nro_iden ";
				$cadena_sql.=" IN (SELECT DISTINCT ";
/*				$cadena_sql.=" car_doc_nro_iden";
				$cadena_sql.=" FROM ";
				$cadena_sql.=$prefijo."accarga ";
				$cadena_sql.=" WHERE ";
				$cadena_sql.=" car_ape_ano =";
				$cadena_sql.="'".$valor[0]."' ";
				$cadena_sql.=" AND ";
				$cadena_sql.=" car_ape_per= ";
				$cadena_sql.="'".$valor[1]."' ";
				$cadena_sql.=" AND car_estado = 'A' ";*/
				$cadena_sql.=" car_doc_nro AS car_doc_nro_iden ";
				$cadena_sql.=" from accursos ";
				$cadena_sql.=" inner join mntac.achorarios ON hor_id_curso=cur_id ";
				$cadena_sql.=" inner join mntac.accargas on car_hor_id=hor_id ";
				$cadena_sql.=" where cur_estado='A' ";
				$cadena_sql.=" and car_estado='A' ";
				$cadena_sql.=" and cur_ape_ano=";
				$cadena_sql.="'".$valor[0]."' ";
				$cadena_sql.=" and cur_ape_per=";
				$cadena_sql.="'".$valor[1]."' ";
				$cadena_sql.=" UNION ";
				$cadena_sql.=" SELECT DISTINCT ";
				$cadena_sql.=" car_doc_nro_iden";
				$cadena_sql.=" FROM ";
				$cadena_sql.=$prefijo."accargahis ";
				$cadena_sql.=" WHERE ";
				$cadena_sql.=" car_ape_ano =";
				$cadena_sql.="'".$valor[0]."' ";
				$cadena_sql.=" AND ";
				$cadena_sql.=" car_ape_per= ";
				$cadena_sql.="'".$valor[1]."' ";
				$cadena_sql.=" AND car_estado = 'A')";
					
								
		//	echo $cadena_sql;
		//	exit;
			
			break;
			
		//Consulta de la tabla participante del SNIES LOCAL
		
			case "participanteSnies":
			$cadena_sql="SELECT ";
			//$cadena_sql.="ies_code, ";					//[0]
			//$cadena_sql.="primer_apellido, ";
			//$cadena_sql.="segundo_apellido,";
			//$cadena_sql.="primer_nombre,";
			//$cadena_sql.="segundo_nombre, ";
			//$cadena_sql.="fecha_nacim, ";				//[5]
			//$cadena_sql.="pais_ln, ";
			//$cadena_sql.="departamento_ln, ";
			//$cadena_sql.="municipio_ln, ";
			//$cadena_sql.="genero_code, ";
			//$cadena_sql.="email, ";
			//$cadena_sql.="est_civil_code, ";
			$cadena_sql.="tipo_doc_unico, ";
			$cadena_sql.="codigo_unico ";  			//[13]
			//$cadena_sql.="tipo_id_ant, ";
			//$cadena_sql.="codigo_id_ant, ";
			//$cadena_sql.="pais_tel, ";
			//$cadena_sql.="area_tel, ";
			//$cadena_sql.="numero_tel ";
			$cadena_sql.="FROM participante ";
			$cadena_sql.="WHERE ";
			$cadena_sql.="codigo_unico='".$valor[2]."' " ;
			
			//echo $cadena_sql;
			//exit;
			
			break;
			
			
			
		//Consulta de la DB Academica para generar los datos de la tabla participante del SNIES para los docentes
		case "participanteAcademica":
			$prefijo="mntac.";	
			$cadena_sql="SELECT UNIQUE ";
			$cadena_sql.="TO_CHAR('1301') ies_code, ";
			$cadena_sql.="SUBSTR(trim(doc_apellido),INSTR(trim(doc_apellido),' ',1,2)+1) primer_apellido, ";
			$cadena_sql.="SUBSTR(trim(doc_apellido),INSTR(trim(doc_apellido),' ',1,2)+1) segundo_apellido, ";			
			$cadena_sql.="DECODE((SUBSTR(trim(doc_nombre),1,INSTR(trim(doc_nombre),' ',1,2)-1)),NULL,doc_nombre, (SUBSTR(trim(doc_nombre),1,INSTR(trim(doc_nombre),' ',1,2)-1)))primer_nombre, ";
			$cadena_sql.="SUBSTR(trim(doc_nombre),1,INSTR(trim(doc_nombre),' ',1,2)-1) segundo_nombre, ";
			$cadena_sql.="TO_DATE(doc_fecha_nac) fecha_nacim, ";
			$cadena_sql.="TO_CHAR('CO') pais_ln, ";
			$cadena_sql.="'11' departamento_ln, ";   	//TO_CHAR(SUBSTR(lug_cod,1,2)) departamento_ln, actualmente no existe el campo
			$cadena_sql.="'11001' municipio_ln,";		//TO_CHAR(SUBSTR(lug_cod,2,2)||SUBSTR(lug_cod,5,3)) municipio_ln, actualmente no existe el campo
			$cadena_sql.="TO_CHAR(DECODE(doc_sexo,'M','01','F','02','01')) genero_code, ";
			$cadena_sql.="doc_email email, ";
			$cadena_sql.="DECODE(doc_estado_civil,1,'01',2,'02',3,'05',4,'03',5,'04','','01') est_civil_code, ";
			$cadena_sql.="DECODE(DOC_TIP_IDEN,'',DECODE(length(doc_nro_iden),11,'TI','CC'),'C', 'CC', 'T','TI', 'E', 'CE', 'P', 'PS', DOC_TIP_IDEN ) tipo_doc_unico, ";
			//	$cadena_sql.="DECODE(DOC_TIP_IDEN , 'C', 'CC', 'T','TI', 'E', 'CE', 'P', 'PS', DOC_TIP_IDEN ) tipo_doc_unico, ";
			$cadena_sql.="TO_CHAR(doc_nro_iden) codigo_unico, ";
			$cadena_sql.="DECODE(DOC_TIP_IDEN,'',DECODE(length(doc_nro_iden),11,'TI','CC'),'C', 'CC', 'T','TI', 'E', 'CE', 'P', 'PS', DOC_TIP_IDEN ) tipo_id_ant, ";
					//$cadena_sql.="DECODE(doc_tip_iden,'C', 'CC', 'T', 'TI', 'E', 'CE', 'CC') tipo_id_ant, ";
			$cadena_sql.="' ' codigo_id_ant, ";
			$cadena_sql.="'57' pais_tel, ";
			$cadena_sql.="'1' area_tel, ";
			$cadena_sql.="TO_CHAR(doc_telefono) numero_tel ";
			$cadena_sql.="FROM ".$prefijo."acdocente ";
			$cadena_sql.="WHERE doc_nro_iden ";
			$cadena_sql.="IN (" ;
				$cadena_sql.="SELECT DISTINCT " ;
				$cadena_sql.=" car_doc_nro AS car_doc_nro_iden ";
				$cadena_sql.=" from accursos ";
				$cadena_sql.=" inner join mntac.achorarios ON hor_id_curso=cur_id ";
				$cadena_sql.=" inner join mntac.accargas on car_hor_id=hor_id ";
				$cadena_sql.=" where cur_estado='A' ";
				$cadena_sql.=" and car_estado='A' ";
				$cadena_sql.=" AND car_doc_nro=";
				$cadena_sql.="".$valor[2]." " ;
				$cadena_sql.=" UNION " ;
				$cadena_sql.="SELECT car_doc_nro_iden " ;
				$cadena_sql.=" FROM " ;
				$cadena_sql.=$prefijo."accargahis ";
				$cadena_sql.="WHERE doc_nro_iden=".$valor[2]." " ;
			$cadena_sql.=" ) " ;
			
			break;
			
			
		//Consulta de la DB Academica para generar los datos de la tabla participante del SNIES para los docentes
		
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
			$cadena_sql.="'1960-01-01', ";
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
	
			//echo "tel=-".$valor[0][12]."-";
			//echo $cadena_sql;
			//exit;
			
	        break;
			        
			//***************************************************************************************************DOCENTE

		        
			//Consulta de la tabla docente del SNIES LOCAL
		
			case "docenteSnies":
			$cadena_sql="SELECT ";
			//$cadena_sql.="ies_code, ";					//[0]
			$cadena_sql.="codigo_unico, ";
			//$cadena_sql.="nivel_est_code, ";
			$cadena_sql.="tipo_doc_unico ";
			//$cadena_sql.="fecha_ingreso ";
			$cadena_sql.="FROM docente ";
			$cadena_sql.="WHERE ";
			$cadena_sql.="codigo_unico='".$valor[2]."' " ;
			
			//echo $cadena_sql;
			//exit;
			
			break;
		
		
		//consulta para generar los datos de un docente para la tabla DOCENTE del SNIES a partir de la DB Academica
		case "docenteAcademica":	
			$prefijo="mntac.";			
			$cadena_sql="SELECT DISTINCT ";
			$cadena_sql.="doc_nro_iden, ";
			$cadena_sql.="1301 IES_CODE, ";
			$cadena_sql.="doc_nro_iden CODIGO_UNICO, ";
			$cadena_sql.="decode(doc_nivel_estudio, 'postdoctorado', '01', 'doctorado', '02', 'maestria', '03', 'especializacion', '04', 'pregrado', '05', 'licenciatura', '06', 'tecnologia', '07', 'tecnico', '08', '05') NIVEL_EST_CODE, ";
			$cadena_sql.="DECODE(DOC_TIP_IDEN , 'C', 'CC', 'T','TI', 'E', 'CE', 'P', 'PS', DOC_TIP_IDEN ) tipo_doc_unico, ";
			$cadena_sql.="doc_fecha_desde FECHA_INGRESO ";
			$cadena_sql.="FROM ".$prefijo."acdocente ";
			$cadena_sql.="WHERE doc_nro_iden ";
			$cadena_sql.="IN (" ;
				$cadena_sql.="SELECT DISTINCT " ;
				$cadena_sql.=" car_doc_nro AS car_doc_nro_iden ";
				$cadena_sql.=" from accursos ";
				$cadena_sql.=" inner join mntac.achorarios ON hor_id_curso=cur_id ";
				$cadena_sql.=" inner join mntac.accargas on car_hor_id=hor_id ";
				$cadena_sql.=" where cur_estado='A' ";
				$cadena_sql.=" and car_estado='A' ";
				$cadena_sql.=" AND car_doc_nro=";
				$cadena_sql.="".$valor[2]." " ;
				$cadena_sql.=" UNION " ;
				$cadena_sql.="SELECT car_doc_nro_iden " ;
				$cadena_sql.=" FROM " ;
				$cadena_sql.=$prefijo."accargahis ";
				$cadena_sql.="WHERE doc_nro_iden=".$valor[2]." " ;
			$cadena_sql.=" ) " ;
						
					
								
			//echo $cadena_sql;
			//exit;
			
			break;
		
		
		
				//Consulta de la DB Academica para generar los datos de la tabla participante del SNIES para los docentes
		
		case "insertarDocente":
			$cadena_sql="INSERT INTO ";
			$cadena_sql.="docente ";
			$cadena_sql.="VALUES ";
			$cadena_sql.="( ";
			$cadena_sql.="'".$valor[0][1]."', ";		
			$cadena_sql.="'".$valor[0][2]."', ";
			$cadena_sql.="'".$valor[0][3]."', ";
			$cadena_sql.="'".$valor[0][4]."', ";
			if($valor[0][5]!="")
			{
			$cadena_sql.="'".$valor[0][5]."' ";
			}
			else
			{
			$cadena_sql.="'1980-01-01' ";			
			}			
			$cadena_sql.=") ";

			//echo "tipo doc=".$valor[0][1]."<br>";
			//echo $cadena_sql."<br>";
			//exit;
			
	        break;
		
			//**************************************************************************************************UNIDAD		
	
				
			//Consulta de la tabla DOCENTE_H del SNIES LOCAL
		
			case "docenteUnidadSnies":
			$cadena_sql="SELECT DISTINCT ";
			//$cadena_sql.="ies_code, ";					//[0]
			$cadena_sql.="cod_unid_org, ";
			$cadena_sql.="codigo_unico, ";
			$cadena_sql.="tipo_doc_unico ";
			//$cadena_sql.="dedicacion ";
			$cadena_sql.="FROM docente_unidad ";
			$cadena_sql.="WHERE ";
			$cadena_sql.="codigo_unico=".$valor[2]." " ;
			
			//echo $cadena_sql;
			//exit;
			
			break;
		
			//consulta para generar los datos de un docente para la tabla DOCENTE_UNIDAD del SNIES a partir de la DB Academica
			
			case "docenteUnidadAcademica":	
			$prefijo="mntac.";			
			$cadena_sql="SELECT DISTINCT ";
			$cadena_sql.="doc_nro_iden, ";
			$cadena_sql.="1301 ies_code, ";
			$cadena_sql.="'2' cod_unid_org, ";			
			$cadena_sql.="doc_nro_iden codigo_unico, ";
			$cadena_sql.="DECODE(DOC_TIP_IDEN,'',DECODE(length(doc_nro_iden),11,'TI','CC'),'C', 'CC', 'T','TI', 'E', 'CE', 'P', 'PS', DOC_TIP_IDEN ) tipo_doc_unico, ";
			//	$cadena_sql.="DECODE(DOC_TIP_IDEN , 'C', 'CC', 'T','TI', 'E', 'CE', 'P', 'PS', DOC_TIP_IDEN ) tipo_doc_unico, ";
			$cadena_sql.="'04' dedicacion ";			
			$cadena_sql.="from ".$prefijo."acdocente ";
			$cadena_sql.="WHERE ";
			//$cadena_sql.="doc_nro_iden= emp_nro_iden ";
			$cadena_sql.="doc_nro_iden=".$valor[2]." ";
						
					
								
			//echo $cadena_sql;
			//exit;
			
			break;		
		
		
			case "insertarDocenteUnidad":
			$cadena_sql="INSERT INTO ";
			$cadena_sql.="docente_unidad ";
			$cadena_sql.="VALUES ";
			$cadena_sql.="(";
			$cadena_sql.="'".$valor[0][1]."', ";
			$cadena_sql.="'".$valor[0][2]."', ";	
			$cadena_sql.="'".$valor[0][3]."', ";
			$cadena_sql.="'".$valor[0][4]."', ";	
			$cadena_sql.="'".$valor[0][5]."' ";	
			$cadena_sql.=") ";	
		
			//echo $cadena_sql."<br>";
			//exit;
			
	        break;
			
			//***********************************************************************DOCENTE_H

			//DOCENTE_H			
	
			//Consulta de la tabla DOCENTE_UNIDAD del SNIES LOCAL
		
			case "docenteHSnies":
			$cadena_sql="SELECT ";
			//$cadena_sql.="ies_code, ";					//[0]
			$cadena_sql.="annio, ";
			$cadena_sql.="semestre, ";
			$cadena_sql.="cod_uni_org, ";
			$cadena_sql.="codigo_unico, ";
			$cadena_sql.="tipo_doc_unico ";
			$cadena_sql.="FROM docente_H ";
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
			$cadena_sql.="AND codigo_unico='".$valor[2]."' " ;
			
			//echo $cadena_sql;
			//exit;
			
			break;
		

			//consulta para generar los datos de un docente para la tabla DOCENTE_UNIDAD del SNIES a partir de la DB Academica
			
			case "docenteHAcademica":	
			$prefijo="mntac.";			
			$cadena_sql="SELECT DISTINCT ";
			$cadena_sql.="1301 ies_code, ";
			$cadena_sql.="'".$valor[0]."'  annio, ";
			$cadena_sql.="DECODE('".$valor[1]."', '03', '02', '".$valor[1]."') semestre, ";
			$cadena_sql.="'2' cod_unid_org, "; //vicerrectoria
			$cadena_sql.="doc_nro_iden codigo_unico, ";
			$cadena_sql.="DECODE(dtv_tvi_cod,'1', '01', '2','04', '3', '04', '4', '04', '5', '04', '6', '02', '04' ) dedicacion, ";
			//$cadena_sql.="'04' dedicacion, ";
			$cadena_sql.="'100' porcentaje_docencia, ";
			$cadena_sql.="'0' porcentaje_investigacion, ";
			$cadena_sql.="'0' porcentaje_administrativa, ";
			$cadena_sql.="'0' porcentaje_bienestar, ";
			$cadena_sql.="'0' porcentaje_edu_no_formal_ycont, ";
			$cadena_sql.="'0' porcentaje_proy_progr_remun, ";
			$cadena_sql.="'0' porcentaje_proy_progr_no_remun, ";			
			$cadena_sql.="DECODE(dtv_tvi_cod,'1', '01', '2','03', '3', '03', '4', '03', '5', '03', '6', '01', '03' ) tipo_contrato, ";
			//$cadena_sql.="'03' tipo_contrato, ";     //horas
			$cadena_sql.="'0' premios_semestre_nal, ";			
			$cadena_sql.="'0' libros_publ_texto_calificados, ";
			$cadena_sql.="'0' premios_semestre_internal, ";
			$cadena_sql.="'0' duracion_en_horas, ";   //cantidad	de horas del contrato			
			$cadena_sql.="DECODE(DOC_TIP_IDEN , 'C', 'CC', 'T','TI', 'E', 'CE', 'P', 'PS', DOC_TIP_IDEN ) tipo_doc_unico, ";
			$cadena_sql.="'0'  porcentaje_otras_actividades, ";	
			$cadena_sql.="'0' libros_pub_investigacion, ";					
			$cadena_sql.="'0' libros_pub_texto, ";			
			$cadena_sql.="'0' reportes_investigacion, ";	
			$cadena_sql.="'0' patentes_obtenidas_semestre ";		
			$cadena_sql.="FROM ".$prefijo."acdocente ";
			$cadena_sql.="INNER JOIN ".$prefijo."acdoctipvin ON doc_nro_iden=dtv_doc_nro_iden ";
			$cadena_sql.="WHERE ";
			$cadena_sql.="doc_nro_iden=".$valor[2]." ";
			$cadena_sql.="AND dtv_ape_ano=".$valor[0]." ";
			$cadena_sql.="AND dtv_ape_per=".$valor[1]." ";
			$cadena_sql.="AND dtv_estado='A'";
						
					
								
			//echo $cadena_sql;
			//exit;
			
			break;		
		
		
			case "insertarDocente_h":
			$cadena_sql="INSERT INTO ";
			$cadena_sql.="docente_h ";
			$cadena_sql.="VALUES ";
			$cadena_sql.="(";
			$cadena_sql.="'".$valor[0][0]."', ";
			$cadena_sql.="'".$valor[0][1]."', ";	
			if($valor[0][2]==2 || $valor[0][2]==3)
                            $cadena_sql.="'02', " ;
			else		
                            $cadena_sql.="'01', " ;
			$cadena_sql.="'".$valor[0][3]."', ";	
			$cadena_sql.="'".$valor[0][4]."', ";
			$cadena_sql.="'".$valor[0][5]."', ";	
			$cadena_sql.="'".$valor[0][6]."', ";
			$cadena_sql.="'".$valor[0][7]."', ";	
			$cadena_sql.="'".$valor[0][8]."', ";
			$cadena_sql.="'".$valor[0][9]."', ";	
			$cadena_sql.="'".$valor[0][10]."', ";
			$cadena_sql.="'".$valor[0][11]."', ";	
			$cadena_sql.="'".$valor[0][12]."', ";
			$cadena_sql.="'".$valor[0][13]."', ";	
			$cadena_sql.="'".$valor[0][14]."', ";
			$cadena_sql.="'".$valor[0][15]."', ";	
			$cadena_sql.="'".$valor[0][16]."', ";
			$cadena_sql.="'".$valor[0][17]."', ";	
			$cadena_sql.="'".$valor[0][18]."', ";
			$cadena_sql.="'".$valor[0][19]."', ";	
			$cadena_sql.="'".$valor[0][20]."', ";
			$cadena_sql.="'".$valor[0][21]."', ";	
			$cadena_sql.="'".$valor[0][22]."', ";							
			$cadena_sql.="'".$valor[0][23]."' ";	
			$cadena_sql.=") ";	
		
			//echo $cadena_sql."<br>";
			//exit;
			
	        break;
			
			case "Actualiza_docenteH":	
						
				$cadena_sql="UPDATE ";
				$cadena_sql.="docente_h ";
				$cadena_sql.="SET ";
				$cadena_sql.="dedicacion= ";
				$cadena_sql.="'".$valor[0][5]."', ";
				$cadena_sql.="tipo_contrato= ";
				$cadena_sql.="'".$valor[0][13]."' ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="codigo_unico='".$valor[0][4]."' ";
				$cadena_sql.="AND annio='".$valor[0][1]."' ";
				$cadena_sql.="AND semestre='".$valor[0][2]."' ";
				//echo $cadena_sql;
				//exit;
				
			break;		
			
			
			case "totalesDocentesSnies":
					$cadena_sql="Select ";			
					$cadena_sql.="annio, ";
					$cadena_sql.="semestre, ";					//[0]
					$cadena_sql.="count (*)  ";			
					$cadena_sql.="from  docente_h ";
					if($valor!="")
						{$cadena_sql.="where ";
						 $cadena_sql.="annio=";			
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
					
					$cadena_sql.="group by semestre, annio ";
					$cadena_sql.="order by annio DESC, semestre DESC";			
					
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

		//************************************************************			
	
				
			default:
			$cadena_sql="";
			break;
			
		
	}
	//echo $cadena_sql."<br>";
	//exit;
	return $cadena_sql;
}

function ejecutar_admin_docente($cadena_sql,$acceso_db,$tipo="")
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
