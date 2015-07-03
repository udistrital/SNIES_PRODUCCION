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
* @subpackage   admin_inscrito
* @package	bloques
* @copyright    
* @version      0.3
* @link		N/D
* @description  Bloque principal para la administracion de inscritos
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
//include_once($configuracion["raiz_documento"].$configuracion["clases"]."/funcionGeneral.class.php");
include_once($configuracion["raiz_documento"].$configuracion["clases"]."/dbConexion.class.php");
global $valor;

//Pagina a donde direcciona el menu
$pagina="registro_inscrito";

$conexion=new dbConexion($configuracion);
$log_us=new log();

$acceso_db=$conexion->recursodb($configuracion,"oracle2");
//$acceso_db=$this->conectarDB($configuracion,"oracle1");
$enlace=$acceso_db->conectar_db();

$accesoSnies=$conexion->recursodb($configuracion,"sniesLocal");
$enlaceBlade1=$accesoSnies->conectar_db();

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
		//variable para verificar cubrimiento programa
		$cubrimiento[0]=$valor[0];//anno
		$cubrimiento[1]=$valor[1];//periodo

                ?>
			<table width="100%" align="center" border="0" cellpadding="10" cellspacing="0" >
				<tbody>
					<tr>
						<td >
							<table width="100%" border="0" align="center" cellpadding="5 px" cellspacing="1px" >
								<tr class="centralcuerpo">
									<td>
									.::: Actualizaci&oacute;n Inscritos <? echo $valor[0]." - ".$valor[1];?>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		<?
	//Rescatar TODOS los inscritos de un periodo y anno especifico
	$cadena_sql=cadena_busqueda_inscrito($configuracion, $acceso_db, $valor,"inscrito");
	//$cadena_sql.=" and asp_nro_iden=1016030592";
        //$cadena_sql.=" AND ROWNUM <=78";
        //echo $cadena_sql;exit;
	$registro=ejecutar_admin_inscrito($cadena_sql,$acceso_db);
	
	
	// estos arreglos se utilizan para quitar los acentos debido a que el SNIES central no acepta caracteres con acento, si acepta la ñ y la Ñ
	$acento = array (
				'á',
				'é',
				'í',
				'ó',
				'ú',
				'ü',
				'ñ',
				'Á',
				'É',
				'Í',
				'Ó',
				'Ú',
				'Ü',
				'Ñ'			
		);
		$sinAcento = array (
				'a',
				'e',
				'i',
				'o',
				'u',
				'u',
				'n',
				'A',
				'E',
				'I',
				'O',
				'U',
				'U',
				'N' 
		);
	
	//se reempĺaza en cada registo el caracter de acento por el sencillo ej: á=>a
	foreach ( $registro as $key => $value ) {
		
		/**
		 echo '<br>1 ' . $registro [$key] [1];
		 echo '<br>2 ' . $registro [$key] [2];
		 echo '<br>3 ' . $registro [$key] [3];
		 echo '<br>4 ' . $registro [$key] [4];
		 echo '<br>5 ' . $registro [$key] [5];
		 echo '<br>6 ' . $registro [$key] [6];
		 echo '<br>7 ' . $registro [$key] [7];
		 echo '<br>8 ' . $registro [$key] [8];
		 echo '<br>9 ' . $registro [$key] [9];
		 echo '<br>10 ' . $registro [$key] [10];
		 echo '<br>11 ' . $registro [$key] [11];
		 echo '<br>12 ' . $registro [$key] [12];
		 echo '<br>13 ' . $registro [$key] [13];
		 echo '<br>14 ' . $registro [$key] [14];
		 echo '<br>15 ' . $registro [$key] [15];
		 echo '<br>16 ' . $registro [$key] [16];
		 echo '<br>17 ' . $registro [$key] [17];
		 */
			
		$registro [$key] ['5'] = str_replace ( $acento, $sinAcento, $registro [$key] ['5'] );//SEGUNDO_APELLIDO
		$registro [$key] ['9'] = str_replace ( $acento, $sinAcento, $registro [$key] ['9'] );//PRIMER_NOMBRE
		$registro [$key] ['10'] = str_replace ( $acento, $sinAcento, $registro [$key] ['10'] );//SEGUNDO_NOMBRE
		$registro [$key] ['11'] = str_replace ( $acento, $sinAcento, $registro [$key] ['11'] );//PRIMER_APELLIDO
		$registro [$key] ['15'] = str_replace ( $acento, $sinAcento, $registro [$key] ['15'] );//PROG
		$registro [$key] ['PRIMER_NOMBRE'] = str_replace ( $acento, $sinAcento, $registro [$key] ['PRIMER_NOMBRE'] );
		$registro [$key] ['SEGUNDO_NOMBRE'] = str_replace ( $acento, $sinAcento, $registro [$key] ['SEGUNDO_NOMBRE'] );
		$registro [$key] ['PRIMER_APELLIDO'] = str_replace ( $acento, $sinAcento, $registro [$key] ['PRIMER_APELLIDO'] );
		$registro [$key] ['SEGUNDO_APELLIDO'] = str_replace ( $acento, $sinAcento, $registro [$key] ['SEGUNDO_APELLIDO'] );
		$registro [$key] ['PROG'] = str_replace ( $acento, $sinAcento, $registro [$key] ['PROG'] );

	}
			
	
	$inscrito=0;
        $sindatos=0;
        $reg_sindatos=array();
        $error=array();
	if(!is_array($registro)){	
		$cadena=htmlentities($cadena, ENT_COMPAT, "UTF-8");
		echo "No existen registros en la Base de Datos Academica para este per&iacute;odo";
		//alerta::sin_registro($configuracion,$cadena);	
	}
	else{
                set_time_limit(0);
		$contar=count($registro);		
			
		//toma los totales iniciales de los registros
		$cadena_sql=cadena_busqueda_inscrito($configuracion, $accesoSnies,$valor,"totalesIncritoSnies");
		//echo $cadena_sql;exit; 
		$inicialInscritos=ejecutar_admin_inscrito($cadena_sql,$accesoSnies);
		
                for($contador=0; $contador<$contar; $contador++)
		{
                    unset($valor[2]);
			unset($unError);
			$valor[2]=$registro[$contador][4]; //documento
			$valor[3]=$registro[$contador][6];  //programa primera opcion
                    $registroPrincipal[0]=$registro[$contador];
                    $aux=0;
                    $valido='SI';
                    
                    $cont_error=0;
                    //valida que el registro no tenga datos nulos que son obligatorios en Snies
                    while($aux<=14 && $valido=='SI')
                        { if((chop($registroPrincipal[0][$aux])=='' || $registroPrincipal[0][$aux]=='N/A') && $aux!=5 && $aux!=10 )
                            {
                             if(chop($registroPrincipal[0][$aux])=='')
                                    { $valido='NO';}
                            
                             switch ($aux)
                                {case "1":
                                     $error[$aux]="El año esta vacio";
                                     $cont_error++;
                                     break;
                                 case "2":
                                     $error[$aux]="El Periodo esta vacio";
                                     $cont_error++;
                                     break;
                                 case "3":
                                     $error[$aux]="El Tipo de Identificación esta vacio";
                                     $cont_error++;
                                     break;
                                 case "4":
                                     $error[$aux]="El Número de Identificación esta vacio";
                                     $cont_error++;
                                     break;
                                 case "6":
                                     $error[$aux]="El codigo del proyecto Curricular esta vacio";
                                     $cont_error++;
                                     break;
                                 case "7":
                                     $error[$aux]="El codigo SNP del ICFES esta vacio";
                                     $cont_error++;
                                     break;
                                 case "8":
                                     $error[$aux]="El Genero esta vacio";
                                     $cont_error++;
                                     break;
                                 case "9":
                                     $error[$aux]="El Primer Nombre esta vacio";
                                     $cont_error++;
                                     break;
                                 case "11":
                                     $error[$aux]="El Primer Apellido esta vacio";
                                     $cont_error++;
                                     break;
                                }
                            
                            }
                            $aux++;
                        }//exit;
			
                      		//Que contiene los datos de la busqueda inicial
			echo "<hr>".$contador." - Inscrito: ".$registroPrincipal[0][3]." ".$registroPrincipal[0][4]." ";
                        echo "<br>".$registroPrincipal[0][11]." ".$registroPrincipal[0][5]." ".$registroPrincipal[0][9]." ".$registroPrincipal[0][10]."";
                        echo "<br>proyecto : ".$registroPrincipal[0][15]." ";
                        
                        
                         if($cont_error>0)
                                {   $reg_sindatos[$sindatos]['tipo_doc']=$registroPrincipal[0][3];
                                    $reg_sindatos[$sindatos]['nro_doc']=$registroPrincipal[0][4];
                                    $reg_sindatos[$sindatos]['nombre']=$registroPrincipal[0][11]." ".$registroPrincipal[0][5]." ".$registroPrincipal[0][9]." ".$registroPrincipal[0][10];
                                    $reg_sindatos[$sindatos]['proyecto']=$registroPrincipal[0][15];
                                    $errores='';
                                    foreach ($error as $key=>$value)
                                       { if(chop($error[$key])!="")
                                          { $errores.=$error[$key].", ";}
                                       }
                                   
                                   $reg_sindatos[$sindatos]['errores']=$errores;
                                   $sindatos=$sindatos+1;

                                 unset($error);  
                                 unset($cont_error);  
                                 
                                }
                        
			//revisa si el programa que esta inscrito el estudiante, esta en la tabla cubrimiento programa
			//exit;				
				    $cubrimiento[2]=$registro[$contador][6];//codigo del programa snies
				    $cadena_sql=cadena_busqueda_inscrito($configuracion, $accesoSnies, $cubrimiento,"cubrimiento");
				    //echo "<br>".$cadena_sql;exit;
				    $cubrimientoPrograma=ejecutar_admin_inscrito($cadena_sql,$accesoSnies);
				    
					if($cubrimientoPrograma[0][0]==0)
						{
						$cadena_sql=cadena_busqueda_inscrito($configuracion, $accesoSnies, $cubrimiento,"insertarCubrimiento");
						//echo "<br>".$cadena_sql;exit;		
				    		$Programa=ejecutar_admin_inscrito($cadena_sql,$accesoSnies);
						}//termina cubrimiento programa
                                 //verifica que existe el proyecto en la tabla lista programa
                                        $cadena_sql=cadena_busqueda_inscrito($configuracion, $accesoSnies, $cubrimiento,"lista_programa");
                                        //echo "<br>".$cadena_sql;
                                        $listaPrograma=ejecutar_admin_inscrito($cadena_sql,$accesoSnies);

                                        if($listaPrograma[0][0]==0)
                                                {
                                                $cadena_sql=cadena_busqueda_inscrito($configuracion, $accesoSnies, $cubrimiento,"insertarPrograma");
                                                //echo "<br>".$cadena_sql;exit;
                                                $Programa=ejecutar_admin_inscrito($cadena_sql,$accesoSnies);
                                                }//termina lista programa

			if($valido=="SI")
                             {
				//ACTUALIZAR INSCRITO
				
				//buscar los registros de inscritos en el SNIES
				$cadena_sql=cadena_busqueda_inscrito($configuracion, $accesoSnies, $valor,"inscritoSnies");		
				//echo $cadena_sql;//exit;
				$registroInscritoSnies=ejecutar_admin_inscrito($cadena_sql,$accesoSnies);
						
				//Verificar que el inscrito no esta en la tabla INSCRITO del SNIES <<EXISTE EL PARTICIPANTE=NO>> 
				
				if(!is_array($registroInscritoSnies))
				
				{	
				
					//insertar los datos del inscrito en la tabla INSCRITO del SNIES
						$miRegistro[0]=$registro[$contador];
						$cadena_sql=cadena_busqueda_inscrito($configuracion, $accesoSnies, $miRegistro,"insertarInscrito");		
						//echo "<br>".$cadena_sql;//exit;
						echo $resultadoInscrito=ejecutar_admin_inscrito($cadena_sql,$accesoSnies,"insertar");
						
						if($resultadoInscrito==false)
						{
							$resultadoInscrito=ejecutar_admin_inscrito($cadena_sql,$accesoSnies,"error");
                                                        echo "<font color='red'>El registro no pudo ser cargado en la tabla INSCRITO por:".$resultadoInscrito."<br></font>";
							$unError=true;
							//echo "<a title='".$resultadoInscrito."'>El registro no pudo ser cargado, por favor verifique los datos en la Aplicaci&oacute;n Acad&eacute;mica </a><br>";
							//echo "<hr class='hr_subtitulo'>";
							//exit;
						}
						else
						{echo "<font color='green'><br>El registro se guardo satisfactoriamente como INSCRITO<br></font>";
                                                 $inscrito++;
						}		
				}
                                else
                                {//actualizar los datos del inscrito en la tabla INSCRITO del SNIES
                                    $miRegistro[0]=$registro[$contador];
                                    $cadena_sql=cadena_busqueda_inscrito($configuracion, $accesoSnies, $miRegistro,"actualizarInscrito");
                                  // echo "<br>".$cadena_sql.";";//exit;
                                    $actualizaInscrito=ejecutar_admin_inscrito($cadena_sql,$accesoSnies,"insertar");
                                    echo "<font color='green'><br>El registro se ha actualizado<br></font>";

                                }
                            }
                           else{
                                 echo "<font color='red'><br><br>El registro contiene algunos datos nulos, revise su fuente de información:<br></font>";
                                 echo $errores;    
                               }
                          unset($valido);
                          unset($errores);     
			}//fin for
		}	//fin actualizacion
				//VARIABLES PARA EL LOG
                                date_default_timezone_set('UTC');
						$logger[0]="CARGAR AL SNIES LOCAL";
						$logger[1]="Inscrito";
						$logger[2]=$inicialInscritos[0][2];
						$logger[3]=$inscrito;
						$logger[4]=date('d/m/Y h:i:s',time());
						$logger[6]=$valor[1];
						$logger[7]="Se cargo los datos, para el reporte de inscritos al SNIES para el periodo ".$valor[0]."-".$valor[1];
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
											<td>INSCRITO</td>
											<td><?	echo $inscrito;?></td>
										</tr>
                                                                                <tr class="bloquecentralcuerpo">
											<td> Registros SIN CARGAR o con Informaci&oacute;n incompleta</td>
											<td><?	echo $sindatos;?></td>
										</tr>
										</table>
									</td>
								</tr>
							</tbody>
						</table>						
	<?	if(isset($reg_sindatos))
                    {
                    $nombre_xls="ErroresInscritos".$valor[0]."-".$valor[1];
                    generar_archivo_errores($configuracion,$nombre_xls,$reg_sindatos);
                    unset($reg_sindatos);
                    }

                }//cierra el if que captura año y periodo	
		//echo "<br><span class='texto_negrita'>Faltan ".$inscritoFaltante." registros de INSCRITOS por actualizar en el SNIES. </span><br>";
	
	$cadena_sql=cadena_busqueda_inscrito($configuracion, $accesoSnies, "","totalesIncritoSnies");
        $registroInscritoTotales=ejecutar_admin_inscrito($cadena_sql,$accesoSnies);
	

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
						.::: Datos Inscritos
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
										TOTAL INSCRITOS
										</td>					
									</tr>	
									
									<?$n=0;
								while (count($registroInscritoTotales)>$n)
									{	
										if (is_array($registroInscritoTotales[$n]))
										{?>
										
										<tr>
											<td class="cuadro_plano centrar">
											<span class="texto_negrita"><? echo $totalAdmitido=$registroInscritoTotales[$n][0];?></span> 
											</td>
											<td class="cuadro_plano centrar">
											<span class="texto_negrita"><? echo $totalAdmitido=$registroInscritoTotales[$n][1];?></span>
											</td>
											<td class="cuadro_plano centrar centrar">
											<span class="texto_negrita"><? echo $totalAdmitido=$registroInscritoTotales[$n][2];?></span>
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
                        <td >
				<table width="100%" border="0" align="center" cellpadding="5 px" cellspacing="1px" >
					<tr class="centralcuerpo">
						<td>
						.::: Errores
						</td>
					</tr>
							
					<tr>
						<td>
							<table class="contenidotabla">
									<tr class="cuadro_color">
										<td width="25%" class="cuadro_plano centrar">
										Registros Errores
										</td>
										
									</tr>	
									<?
                                                                        //definimos el directorio donde se guadan los archivos
                                                                        $path = $configuracion['raiz_documento']."/bloque/admin_inscrito/errores/";
                                                                        //$path = $configuracion['host'].$configuracion['site']."/bloque/admin_inscrito/errores/";
                                                                        $path2 = $configuracion['host'].$configuracion['site']."/bloque/admin_inscrito/errores/";
                                                                        //abrimos el directorio
                                                                        $dir = opendir($path);
                                                                        //guardamos los archivos en un arreglo
                                                                        $img_total=0;
                                                                        $img_array=array();
                                                                        while ($elemento = readdir($dir))
                                                                        {
                                                                        if (strlen($elemento)>3)
                                                                        {
                                                                        $img_array[$img_total]=$elemento;
                                                                        }

                                                                        $img_total++;
                                                                        }
                                                                         $t_reg=count($img_array);
                                                                        
                                                                        foreach($img_array as $key => $value)
                                                                            { ?><tr>
                                                                                     <td class="cuadro_plano centrar">
											<span class="texto_negrita"><a href="<?echo $path2.$img_array[$key];?>"><?echo $img_array[$key];?></a>
                                                                                     </td>
                                                                                <?
                                                                            } 
                                                                        
                                                                        
									?>
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

function generar_archivo_errores($configuracion,$nombre,$datos)
{
    
$shtml="<table>
          <tr>
             <td>Tipo documento</td>
             <td>Numero documento</td>
             <td>Nombre Inscrito</td>
             <td>Proyecto Curricular</td>
             <td>Errores</td>
          </tr>";

foreach($datos as $key=>$value)
        {
         $shtml.="
          <tr>
             <td>".$datos[$key]['tipo_doc'] ."</td>
             <td>".$datos[$key]['nro_doc'] ."</td>
             <td>".$datos[$key]['nombre']."</td>
             <td>".$datos[$key]['proyecto'] ."</td>
             <td>".$datos[$key]['errores'] ."</td>
          </tr>";
    
        }

$shtml.="</table>";


$carpeta=$configuracion['raiz_documento']."/bloque/admin_inscrito/errores/"; //carpeta donde guardar el archivo.
//debe tener permisos 775 por lo menos
$archivo=$carpeta.$nombre.".xls"; //ruta del archivo a generar
$fp=fopen($archivo,"w");
fwrite($fp,$shtml);
fclose($fp);

}        

        
        
//Esta funcion sirve para definir la(s) clausula(s) SQL que se utilizan en este bloque

function cadena_busqueda_inscrito($configuracion, $acceso_db, $valor,$opcion="")
{
	//$valor=$acceso_db->verificar_variables($valor);
	
	switch($opcion)
	{
	
		//consulta para generar los datos para la tabla inscritos del SNIES a partir de la DB Academica
			case "inscrito":	

                                $prefijo="mntac.";
                                $cadena_sql="SELECT UNIQUE ";
                                $cadena_sql.="TO_CHAR('1301') ies_code, ";
                                $cadena_sql.="asp_ape_ano ins_annio, ";
                                $cadena_sql.="DECODE(asp_ape_per,1,'01',3,'02', asp_ape_per) ins_per, ";
                                $cadena_sql.="DECODE(asp_tip_doc,'',DECODE(length(asp_nro_iden),11,'TI',12,'TI','CC'),'C', 'CC', '1', 'CC', 'c', 'CC', 'T', 'TI', '2', 'TI', 't', 'TI', 'E', 'CE', 'P', 'PS', 'CC') tipo_ident_code, ";
                                $cadena_sql.="asp_nro_iden documento, ";
                                $cadena_sql.="case
                                        when INSTR(trim(asp_apellido),' ',1,1)='0'
                                        then ' '
                                        else SUBSTR(trim(asp_apellido),instr(trim(asp_apellido),' ',-1,1),length(trim(asp_apellido)))
                                        end segundo_apellido, ";
                                $cadena_sql.="as_cra_cod_snies prog_prim_opcion, ";
                                $cadena_sql.="TO_CHAR(DECODE(asp_snp,'','N/A',NULL,'N/A',replace(asp_snp,' ',''))) snp,";
                                $cadena_sql.="TO_CHAR(DECODE(asp_sexo,'M','01','F','02','01')) genero, ";
                                $cadena_sql.="case
                                        when INSTR(trim(asp_nombre),' ',1,1)='0'
                                        then SUBSTR(trim(asp_nombre),instr(trim(asp_nombre),' ',1,1),length(trim(asp_nombre)))
                                        else SUBSTR(trim(asp_nombre),0,INSTR(trim(asp_nombre),' ',1,1))
                                        end primer_nombre, ";
                                $cadena_sql.="case
                                        when INSTR(trim(asp_nombre),' ',1,1)='0'
                                        then ' '
                                        else SUBSTR(trim(asp_nombre),instr(trim(asp_nombre),' ',-1,1),length(trim(asp_nombre)))
                                        end segundo_nombre, ";
                                $cadena_sql.="case
                                        when INSTR(trim(asp_apellido),' ',1,1)='0'
                                        then SUBSTR(trim(asp_apellido),instr(trim(asp_apellido),' ',1,1),length(trim(asp_apellido)))
                                        else SUBSTR(trim(asp_apellido),0,INSTR(trim(asp_apellido),' ',1,1))
                                        end primer_apellido, ";
                                $cadena_sql.="'1301' codigo_ent_aula, ";
                                $cadena_sql.="'11001' municipio, ";
                                $cadena_sql.="'11' departamento, ";
                                $cadena_sql.="as_cra_nom prog ";
                                $cadena_sql.="FROM ".$prefijo."accra_snies ";
                                $cadena_sql.="INNER JOIN ".$prefijo."accra ON cra_cod = as_cra_cod ";
                                $cadena_sql.="INNER JOIN ".$prefijo."acasp ON cra_cod = asp_cra_cod ";
                                $cadena_sql.="INNER JOIN ".$prefijo."actipcra ON cra_tip_cra = tra_cod ";
                                $cadena_sql.="WHERE  ";
                                $cadena_sql.="asp_ape_ano=".$valor[0]." ";
                                $cadena_sql.="AND asp_ape_per=".$valor[1]." ";
                                $cadena_sql.="AND tra_nivel IN ('PREGRADO') ";

                                $cadena_sql.="UNION ";

                                $cadena_sql.="SELECT UNIQUE ";
                                $cadena_sql.="TO_CHAR('1301') ies_code, ";
                                $cadena_sql.="mat_ano ins_annio, ";
                                $cadena_sql.="DECODE(mat_per,1,'01',3,'02', mat_per) ins_per, ";
                                $cadena_sql.="DECODE(est_tipo_iden,'',DECODE(length(est_nro_iden),11,'TI',12,'TI','CC'),'C', 'CC', '1', 'CC', 'c', 'CC', 'T', 'TI', '2', 'TI', 't', 'TI', 'E', 'CE', 'P', 'PS', 'CC') tipo_ident_code, ";
                                $cadena_sql.="est_nro_iden  documento, ";
                                $cadena_sql.="(case when INSTR(trim(est_nombre),' ',1,4)>'0' AND INSTR(trim(est_nombre),' ',1,2)=INSTR(trim(est_nombre),' ',1,1)+1 
                                      then trim(SUBSTR(trim(est_nombre),instr(trim(est_nombre),' ',1,1)+1,INSTR(trim(est_nombre),' ',1,3) - INSTR(trim(est_nombre),' ',1,2))) 
                                      when INSTR(trim(est_nombre),' ',1,4)='0' AND INSTR(trim(est_nombre),' ',1,2)=INSTR(trim(est_nombre),' ',1,1)+1
                                      then trim(SUBSTR(trim(est_nombre),instr(trim(est_nombre),' ',1,1)+1,length(trim(est_nombre)) - instr(trim(est_nombre),' ',1,3)))
                                      else trim(SUBSTR(trim(est_nombre),INSTR(trim(est_nombre),' ',1,1) +1 ,INSTR(trim(est_nombre),' ',1,2) - INSTR(trim(est_nombre),' ',1,1)))
                                      end) segundo_apellido, ";
                                $cadena_sql.="as_cra_cod_snies prog_prim_opcion,  ";
                                $cadena_sql.="TO_CHAR(DECODE(eot_nro_snp,'','N/A',NULL,'N/A',replace(eot_nro_snp,' ',''))) snp,";
                                $cadena_sql.="TO_CHAR(DECODE(est_sexo,'M','01','F','02','01')) genero, ";
                                $cadena_sql.="(case when INSTR(trim(est_nombre),' ',1,3)='0' AND INSTR(trim(est_nombre),' ',1,2)='0' 
                                      then trim(SUBSTR(trim(est_nombre),instr(trim(est_nombre),' ',1,1) +1,length(trim(est_nombre)) - instr(trim(est_nombre),' ',1,1))) 
                                      when INSTR(trim(est_nombre),' ',1,3)='0' AND INSTR(trim(est_nombre),' ',1,2)>'0' 
                                      then trim(SUBSTR(trim(est_nombre),instr(trim(est_nombre),' ',1,2) +1,length(trim(est_nombre)) - instr(trim(est_nombre),' ',1,2))) 
                                      when INSTR(trim(est_nombre),' ',1,4)>'0' AND INSTR(trim(est_nombre),' ',1,2)=INSTR(trim(est_nombre),' ',1,1)+1 
                                      then trim(SUBSTR(trim(est_nombre),instr(trim(est_nombre),' ',1,3),INSTR(trim(est_nombre),' ',1,4) - INSTR(trim(est_nombre),' ',1,3))) 
                                      when INSTR(trim(est_nombre),' ',1,4)='0' AND INSTR(trim(est_nombre),' ',1,2)=INSTR(trim(est_nombre),' ',1,1)+1
                                      then trim(SUBSTR(trim(est_nombre),instr(trim(est_nombre),' ',1,3),length(trim(est_nombre)) - instr(trim(est_nombre),' ',1,3)))
                                      when INSTR(trim(est_nombre),' ',1,4)>'0' AND INSTR(trim(est_nombre),' ',1,3)=INSTR(trim(est_nombre),' ',1,2)+1 
                                      then trim(SUBSTR(trim(est_nombre),instr(trim(est_nombre),' ',1,2)+1,INSTR(trim(est_nombre),' ',1,4) - INSTR(trim(est_nombre),' ',1,3))) 
                                      when INSTR(trim(est_nombre),' ',1,4)='0' AND INSTR(trim(est_nombre),' ',1,3)=INSTR(trim(est_nombre),' ',1,2)+1 
                                      then trim(SUBSTR(trim(est_nombre),instr(trim(est_nombre),' ',1,3),length(est_nombre) - instr(est_nombre,' ',1,3)+1))
                                      else trim(SUBSTR(trim(est_nombre),INSTR(trim(est_nombre),' ',1,2)+1 ,INSTR(trim(est_nombre),' ',1,3) - INSTR(trim(est_nombre),' ',1,2))) 
                                      end) primer_nombre, ";
                                $cadena_sql.="(case when INSTR(trim(est_nombre),' ',1,3)='0'
                                      then ' ' 
                                      when INSTR(trim(est_nombre),' ',1,4)='0' AND (INSTR(trim(est_nombre),' ',1,3)=INSTR(trim(est_nombre),' ',1,2)+1 OR INSTR(trim(est_nombre),' ',1,2)=INSTR(trim(est_nombre),' ',1,1)+1)
                                      then ' '
                                      when INSTR(trim(est_nombre),' ',1,4)>'0' AND INSTR(trim(est_nombre),' ',1,3)=INSTR(trim(est_nombre),' ',1,2)+1 
                                      then trim(SUBSTR(trim(est_nombre),instr(trim(est_nombre),' ',1,2)+1,INSTR(trim(est_nombre),' ',1,4) - INSTR(trim(est_nombre),' ',1,3))) 
                                      when INSTR(trim(est_nombre),' ',1,4)>'0' AND INSTR(trim(est_nombre),' ',1,2)=INSTR(trim(est_nombre),' ',1,1)+1 
                                      then trim(SUBSTR(trim(est_nombre),instr(trim(est_nombre),' ',1,4),(length(trim(est_nombre))+1) - INSTR(trim(est_nombre),' ',1,4))) 
                                      when INSTR(trim(est_nombre),' ',1,4)>'0' AND INSTR(trim(est_nombre),' ',1,3)=INSTR(trim(est_nombre),' ',1,2)+1 
                                      then trim(SUBSTR(trim(est_nombre),instr(trim(est_nombre),' ',1,4),(length(trim(est_nombre))+1) - instr(est_nombre,' ',1,4)+1))
                                      else trim(SUBSTR(trim(est_nombre),instr(trim(est_nombre),' ',1,3) +1,(length(est_nombre)+1) - instr(est_nombre,' ',1,3))) 
                                      end) segundo_nombre,  ";
                                $cadena_sql.="SUBSTR(trim(est_nombre),0,INSTR(trim(est_nombre),' ',1,1)) primer_apellido, ";
                                $cadena_sql.="'1301' codigo_ent_aula, ";
                                $cadena_sql.="'11001' municipio, ";
                                $cadena_sql.="'11' departamento, ";
                                $cadena_sql.="as_cra_nom prog ";
                                $cadena_sql.="FROM ".$prefijo."acest ";
                                $cadena_sql.="INNER JOIN ".$prefijo."acestotr ON est_cod = eot_cod  ";
                                $cadena_sql.="INNER JOIN ".$prefijo."v_tot_matri_ape_per ON est_cod = mat_est_cod ";
                                $cadena_sql.="INNER JOIN ".$prefijo."accra ON cra_cod = mat_cra_cod ";
                                $cadena_sql.="INNER JOIN ".$prefijo."actipcra ON cra_tip_cra = tra_cod ";
                                $cadena_sql.="INNER JOIN ".$prefijo."accra_snies ON as_cra_cod = mat_cra_cod ";
                                $cadena_sql.="WHERE  ";
                                $cadena_sql.="mat_ano=".$valor[0]." ";
                                $cadena_sql.="AND mat_per=".$valor[1]." ";
                                $cadena_sql.="AND SUBSTR(est_cod,0,4)=mat_ano ";
                                $cadena_sql.="AND SUBSTR(est_cod,5,1)=DECODE(mat_per,1,'1',3,'2',mat_per) ";
                                $cadena_sql.="AND tra_nivel IN ('DOCTORADO','MAESTRIA','POSGRADO') ";

                                                   
			break;
			        

			//Consulta de la tabla inscrito del SNIES LOCAL
		
			case "inscritoSnies":
					$cadena_sql="SELECT ";
					$cadena_sql.="ins_annio, ";
					$cadena_sql.="ins_semestre,";
					$cadena_sql.="documento, ";
					$cadena_sql.="prog_prim_opc ";
					$cadena_sql.="FROM inscrito ";
					$cadena_sql.="WHERE ";
					$cadena_sql.="ins_annio='".$valor[0]."' " ;
					if($valor[1]==2 || $valor[1]==3)
                                            $cadena_sql.="AND ins_semestre='02' " ;
					else		
                                            $cadena_sql.="AND ins_semestre='01' " ;
					$cadena_sql.="AND documento='".$valor[2]."' " ;
					$cadena_sql.="AND prog_prim_opc='".$valor[3]."' " ;
					
					//echo "<br>".$cadena_sql;
					//exit;
			
			break;
		
			case "insertarInscrito":
                                $cadena_sql="INSERT INTO ";
                                $cadena_sql.="inscrito ";
                                $cadena_sql.="(";
                                $cadena_sql.="ies_code, ";					//[0]
                                $cadena_sql.="ins_annio, ";
                                $cadena_sql.="ins_semestre,";
                                $cadena_sql.="tipo_ident_code,";
                                $cadena_sql.="documento, ";
                                $cadena_sql.="segundo_apellido, ";				//[5]
                                $cadena_sql.="prog_prim_opc, ";
                                $cadena_sql.="snp, ";
                                $cadena_sql.="genero, ";
                                $cadena_sql.="primer_nombre, ";
                                $cadena_sql.="segundo_nombre, ";
                                $cadena_sql.="primer_apellido, ";  			//[13]
                                $cadena_sql.="codigo_ent_aula, ";
                                $cadena_sql.="municipio, ";
                                $cadena_sql.="departamento ";
                                $cadena_sql.=") ";
                                $cadena_sql.="VALUES ";
                                $cadena_sql.="(";
                                $cadena_sql.="'".$valor[0][0]."', ";
                                $cadena_sql.="'".$valor[0][1]."', ";
                                $cadena_sql.="'".$valor[0][2]."', ";
                                $cadena_sql.="'".$valor[0][3]."', ";
                                $cadena_sql.="'".$valor[0][4]."', ";
                                $cadena_sql.="replace('".$valor[0][5]."','?','') ,";
                                $cadena_sql.="'".$valor[0][6]."', ";
                                $cadena_sql.="'".$valor[0][7]."', ";
                                $cadena_sql.="'".$valor[0][8]."', ";
                                $cadena_sql.="replace('".$valor[0][9]."','?','') ,";
                                $cadena_sql.="replace('".$valor[0][10]."','?','') ,";
                                $cadena_sql.="replace('".$valor[0][11]."','?','') ,";
                                $cadena_sql.="'".$valor[0][12]."', ";
                                $cadena_sql.="'".$valor[0][13]."', ";
                                $cadena_sql.="'".$valor[0][14]."' ";
                                $cadena_sql.="); ";
	        break;


                case "actualizarInscrito":
                                $cadena_sql="UPDATE ";
                                $cadena_sql.="inscrito ";
                                $cadena_sql.="SET ";
                                $cadena_sql.="tipo_ident_code=";
                                $cadena_sql.="'".$valor[0][3]."', ";
                                $cadena_sql.="segundo_apellido= ";
                                $cadena_sql.="replace('".$valor[0][5]."','?','') ,";
                                $cadena_sql.="snp= ";
                                $cadena_sql.="'".$valor[0][7]."', ";
                                $cadena_sql.="genero= ";
                                $cadena_sql.="'".$valor[0][8]."', ";
                                $cadena_sql.="primer_nombre= ";
                                $cadena_sql.="replace('".$valor[0][9]."','?','') ,";
                                $cadena_sql.="segundo_nombre= ";
                                $cadena_sql.="replace('".$valor[0][10]."','?','') ,";
                                $cadena_sql.="primer_apellido= ";
                                $cadena_sql.="replace('".$valor[0][11]."','?','') ,";
                                $cadena_sql.="municipio= ";
                                $cadena_sql.="'".$valor[0][13]."', ";
                                $cadena_sql.="departamento= ";
                                $cadena_sql.="'".$valor[0][14]."' ";
                                $cadena_sql.="where ";
                                $cadena_sql.="documento= ";
                                $cadena_sql.="'".$valor[0][4]."' ";
	        break;
			
                case "totalesIncritoSnies":
                                $cadena_sql="Select ";			
                                $cadena_sql.="ins_annio, ";
                                $cadena_sql.="ins_semestre, ";					//[0]
                                $cadena_sql.="count (*)  ";			
                                $cadena_sql.="from  inscrito ";
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

                        case "lista_programa":
				$cadena_sql="Select ";
				$cadena_sql.="count (*)  ";
				$cadena_sql.="from  lista_programas ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="lp_annio='".$valor[0]."' " ;
				$cadena_sql.="AND lp_semestre=" ;
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


			case "insertarPrograma":
				$cadena_sql="INSERT INTO ";
				$cadena_sql.="lista_programas ";
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

function ejecutar_admin_inscrito($cadena_sql,$acceso_db,$tipo="")
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
