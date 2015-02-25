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
* @subpackage   admin_matriculado
* @package	bloques
* @copyright    
* @version      0.3
* @link		N/D
* @description  Bloque principal para la administracion de matriculados
*
******************************************************************************/
if(!isset($GLOBALS["autorizado"])){
	include("../index.php");
	exit;
}

include ($configuracion["raiz_documento"].$configuracion["estilo"]."/".$this->estilo."/tema.php");
//Se incluye para manejar los mensajes de error
include_once($configuracion["raiz_documento"].$configuracion["clases"]."/alerta.class.php");
include_once($configuracion["raiz_documento"].$configuracion["clases"]."/navegacion.class.php");
include_once($configuracion["raiz_documento"].$configuracion["clases"]."/log.class.php");
//require_once($configuracion["raiz_documento"].$configuracion["clases"]."/barraProgreso.class.php");


//Pagina a donde direcciona el menu
$conexion=new dbConexion($configuracion);
$acceso_db=$conexion->recursodb($configuracion,"oracle2");
$enlace=$acceso_db->conectar_db();

$accesoSnies=$conexion->recursodb($configuracion,"sniesLocal");
$enlaceBlade1=$accesoSnies->conectar_db();
$log_us=new log();

if($enlace && $enlaceBlade1)
{

	//Rescatar los inscritos de la base de datos academica
	// si se envia la variable de anno y/o periodo 
	

if(isset($_REQUEST["annio"]) and isset($_REQUEST["periodo"])){
		$valor[0]=$_REQUEST["annio"];
		
		$valor[1]=$_REQUEST["periodo"];
		//Rescatar TODOS los matriculados de un periodo y anno especifico
				$participante=0;
				$estudiante=0;
				$matriculado=0;
				$cubre[0]=$valor[0];//anno
				$cubre[1]=$valor[1];//periodo
				
				//toma los totales iniciales de los registros
				$cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies,$valor,"totalesMatriculadosSnies");
				//echo "<br>".$cadena_sql; 
				$inicialMatriculado=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
				
				
				$cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies,"","totalParticipanteSnies");
				//echo "<br>".$cadena_sql; 
				$inicialParticipante=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
				
				
				$cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies,"","totalEstudianteSnies");
				//echo "<br>".$cadena_sql; 
				$inicialEstudiante=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
				
	
		
	?>
		
		
		
	<table width="100%" align="center" border="0" cellpadding="10" cellspacing="0" >
		<tbody>
			<tr>
				<td >
					<table width="100%" border="0" align="center" cellpadding="5 px" cellspacing="1px" >
						<tr class="centralcuerpo">
							<td>
							.::: Actualizaci&oacute;n Matriculados <? echo $valor[0]." - ".$valor[1];?>
							</td>
						</tr>	
					</table>
				</td>
			</tr>
		</tbody>
	</table>
				
	
	<?
/*
 * NO ELIMINAR ESTE BLOQUE, ES PARA ELIMINAR REGISTROS QUE YA SON RUIDOSOS PARA LA BASE DE DATOS
 *
 * foreach($prueba as $nest){
    $cadena_sql=cadena_busqueda_matriculado($configuracion, $acceso_db, $nest,"tomarIdActual");
    $consulta=ejecutar_admin_matriculado($cadena_sql,$acceso_db);
    $cedula = $consulta[0][7];
    //Se valida que para la identificacion nueva ya existe un registro en participante
    $temporalEnvio[0][3] = $cedula;
    $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $temporalEnvio,"participanteSnies");
    @$consultat=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
    if(is_array($consulta)){
        unset($temporalEnvio);
        //arma vector general
        $valTemp[0] = 'TI';     //Tipo de Documento
        $valTemp[1] = $nest;    //Numero Identificacion Viejo
        $valTemp[2] = $cedula;  //Numero actual de identificacion
        //Se eliminan todos los registros asociados a $valTemp
        controlEliminaDuplicados($valTemp,$accesoSnies);
        //Se comprueba si existe el mismo documento pero con diferente tipo, si es asi se procede de la misma manera
        $valTemp[5] = "estudiante";
        $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $valTemp,"consultar");
        $consulta=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
        if($consulta[0][0] > 0){
            $valTemp[0] = 'CC';     //Tipo de Documento
            controlEliminaDuplicados($valTemp,$accesoSnies);
        }
    }
    else{   //No se encontro relacion de cambio de identificacion
        array_push($casosRaros,$nest);
    }
}
*/
		$cadena_sql=cadena_busqueda_matriculado($configuracion, $acceso_db, $valor,"matriculado");		
		//$cadena_sql.=" AND ROWNUM <=10";
                $cadena_sql.=" and est_nro_iden= 88011459875";
                //echo $cadena_sql."<br>Consulta Auditoria<br/>";
		$registro=ejecutar_admin_matriculado($cadena_sql,$acceso_db);
                		
		if(!is_array($registro)){	
			$cadena=htmlentities($cadena, ENT_COMPAT, "UTF-8");
			alerta::sin_registro($configuracion,$cadena);	
		}
		else{
                    set_time_limit(0);  //Se asigna, para que no expida el logeo en el servidor
                    $secuencia=1000;
                    $contar=count($registro);
                    $iteracion=($contar/$secuencia);
                    $iter_ini=0;
                    while($iteracion>=0){
                        $iter_fin=($iter_ini+$secuencia);
                        if($iter_fin>$contar){$iter_fin=$contar;
                    }

                    //INICIA LA VALIDACION POR ESTUDIANTE MATRICULADO
                    for($contador=$iter_ini;$contador<$iter_fin;$contador++){
                                //se selecciona cada registro de matriculados, uno a la vez
                                $cubrimiento[0]=$cubre[0];//anno
                                $cubrimiento[1]=$cubre[1];//periodo
                                unset($valor[2]);
                                unset($unError);
                                $valor[2]=$registro[$contador][3]; //Numero de Identificacion del estudiante

                                //verifica que existe el proyecto en la tabla cubrimiento programa
                                $cubrimiento[2]=$registro[$contador][8];//codigo del programa snies

                                $registroPrincipal[0]=$registro[$contador];//Que contiene los datos de la busqueda inicial
                                //valida que el registro no tenga datos nulos que son obligatorios en Snies

                                $aux=0;$valido='SI';
                                while($aux<=10 && $valido=='SI')
                                    { if(chop($registroPrincipal[0][$aux])=='')
                                        {$valido='NO';}
                                        $aux++;
                                    }//exit;
                                
                              if($valido=='SI'){
                                    $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $cubrimiento,"cubrimiento");
                                    @$cubrimientoPrograma=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                    //Si no encuentra el cubrimiento lo agrega en el Snies Local
                                    if($cubrimientoPrograma[0][0]==0){
                                            $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $cubrimiento,"insertarCubrimiento");
                                            @$Programa=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                    }//termina cubrimiento programa
                                    //verifica que existe el proyecto en la tabla lista programa
                                    $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $cubrimiento,"lista_programa");
                                    //echo "<br>".$cadena_sql;
                                    $listaPrograma=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                    //Si no encuentra el programa lo agrega
                                    if($listaPrograma[0][0]==0){
                                            $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $cubrimiento,"insertarPrograma");
                                            //echo "<br>".$cadena_sql;exit;
                                            @$Programa=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                    }//termina lista programa
                                    //ACTUALIZAR PARTICIPANTE
                                    //buscar los registros de participantes en el SNIES
                                    echo "<hr>".$contador." - Documento Estudiante:<h3>".$registroPrincipal[0][10]." ".$registroPrincipal[0][3]."</h3>";

                                    $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $registroPrincipal,"participanteSnies");
                                    $cadena_sql.=" AND tipo_doc_unico='".$registro[$contador][10]."'";//tipo documento
                                    //echo "<br>".$cadena_sql."<br>";//exit;
                                    @$registroParticipanteSnies=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);


                                    //Buscar los datos en la DB Academica de ese registro del matriculado para la tabla participante
                                    $cadena_sql=cadena_busqueda_matriculado($configuracion, $acceso_db, $registroPrincipal,"participanteAcademica");
                                   // echo $cadena_sql;exit;
                                    @$registroDatosParticipante=ejecutar_admin_matriculado($cadena_sql,$acceso_db);
                                    $aux2=0;$validoP='SI';
                                    while($aux2<=22 && $validoP=='SI')
                                        {if(chop($registroDatosParticipante[0][$aux2])=='' && $aux2!=2 && $aux2!=4 && $aux2!=5 && $aux2!=7 && $aux2!=8 && $aux2!=9 && $aux2!=10 && $aux2!=14 && $aux2!=15 && $aux2!=18)
                                            {$validoP='NO';}
                                            $aux2++;
                                        }//exit;

                                    //Si el matriculado no esta en la tabla participante del SNIES <<EXISTE EL PARTICIPANTE=NO>>
                                if($validoP=='SI'){
                                        //Si no existe en la tabla participante, lo ingresa
                                        if(!is_array($registroParticipanteSnies)){                                                        

                                                            if(!is_array($registroDatosParticipante))
                                                                     echo "***El Estudiante con documento = <span class='texto_negrita'>".$valor[2]."</span> no se puede cargar como Participante, por falta de datos<br>";
                                                            else{
                                                                    //verifica si hay un registro ya ingresado del participante y el tipo de documento no es correcto
                                                                    $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $registroPrincipal,"participanteSnies");
                                                                    $cadena_sql.=" AND tipo_doc_unico!='".$registro[$contador][10]."'";//tipo documento
                                                                    @$ParticipanteSnies=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);

                                                                    //Si el tipo de documento no es correcto se elimina el registro que tiene el dato incorrecto
                                                                    if(is_array($ParticipanteSnies) && $ParticipanteSnies[0][1]!=$registro[$contador][10]){
                                                                        //Buscar los datos en la DB Academica de ese registro del graduado para la tabla estudiante
                                                                        $matriculado_borrar[1]=$ParticipanteSnies[0][2];//numero documento
                                                                        $matriculado_borrar[2]=$ParticipanteSnies[0][1];//tipo documento
                                                                    }

                                                                    //verifica que el tipo de documento a cargar sea el mismo si no lo unifica
                                                                    if ($registroDatosParticipante[0][12]!=$registro[$contador][10])
                                                                        $registroDatosParticipante[0][12]=$registro[$contador][10];

                                                                        //insertar los datos del graduado en la tabla participante del SNIES
                                                                        $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $registroDatosParticipante,"insertarParticipante");
                                                                        @$resultadoParticipante=ejecutar_admin_matriculado($cadena_sql,$accesoSnies,"insertar");
                                                                        if($resultadoParticipante==false){
                                                                                @$resultadoParticipante=ejecutar_admin_matriculado($cadena_sql,$accesoSnies,"error");
                                                                                echo "<br>***El registro Matriculado no pudo ser cargado en la tabla participante por:".$resultadoParticipante."<br>";
                                                                                $unError=true;
                                                                                //echo "El registro no pudo ser cargado, por favor verifique los datos en la Aplicaci&oacute;n Acad&eacute;mica <br>";
                                                                                //echo "<hr class='hr_subtitulo'>";
                                                                        }
                                                                        else{
                                                                            echo "<br>El registro fue guardado con Exito, como participante";
                                                                            $participante++;
                                                                        }
                                                            }


                                       }//if(!is_array($registroParticipanteSnies)){
                                       else{   
                                            //Existe registro en la tabla Participante
                                            //Buscar los datos en la DB Academica de ese registro del graduado para la tabla participante
                                            if(is_array($registroDatosParticipante)){
                                                        $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $registroDatosParticipante,"actualiza_participante");
                                                        //echo "<br>".$cadena_sql;//exit;
                                                        @$resultadoParticipante=ejecutar_admin_matriculado($cadena_sql,$accesoSnies,"insertar");
                                                        unset($resultadoParticipante);
                                             }
                                       }

                                        // ACTUALIZAR ESTUDIANTE
                                        if(!isset($unError))
                                        {
                                            unset($unError);
                                            //buscar los registros de estudiantes en el SNIES
                                            $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $registroPrincipal,"estudianteSnies");
                                            $cadena_sql.=" AND tipo_doc_unico='".$registro[$contador][10]."'";
                                            @$registroEstudianteSnies=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);

                                            //Verificar que el matriculado no esta en la tabla participante del SNIES <<EXISTE EL PARTICIPANTE=NO>>
                                            if(!is_array($registroEstudianteSnies)){
                                                    //verifica si hay un registro ya guardado del participante
                                                    $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies,  $registroPrincipal,"estudianteSnies");
                                                    $cadena_sql.=" AND tipo_doc_unico!='".$registro[$contador][10]."'";
                                                    @$EstudianteSnies=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                                    //Si el documento es diferente elimina el registro de estudiante que no corresponde con el aplicativo academico
                                                    if(is_array($EstudianteSnies) && $EstudianteSnies[0][2]!=$registro[$contador][10]){
                                                        //Buscar los datos en la DB Academica de ese registro del graduado para la tabla estudiante
                                                        $matriculado_borrar[1]=$EstudianteSnies[0][1];//numero documento
                                                        $matriculado_borrar[2]=$EstudianteSnies[0][2];//tipo documento
                                                    }

                                                    //insertar los datos del matriculado en la tabla estudiante del SNIES
                                                    $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $registroPrincipal,"insertarEstudiante");
                                                    @$resultadoEstudiante=ejecutar_admin_matriculado($cadena_sql,$accesoSnies,"insertar");
                                                    if($resultadoEstudiante==false){
                                                            @$resultadoEstudiante=ejecutar_admin_matriculado($cadena_sql,$accesoSnies,"error");
                                                            echo "<br/>***El registro estudiante no pudo ser cargado por:".$resultadoEstudiante."<br><br>";
                                                            $unError=true;
                                                    }
                                                    else{echo "<br>El registro fue guardado con Exito, como estudiante";
                                                        $estudiante++;
                                                    }
                                            }//if(!is_array($registroEstudianteSnies))
                                        }//fin actualiza estudiante

                                        //VERIFICA QUE EL ESTUDIANTE ESTE EN LA LISTA DE INSCRITOS Y ADMITIDOS
                                        $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies,  $registroPrincipal,"consultarInscrito");
                                        @$inscrito=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);

                                        if($inscrito[0][0]==0){
                                                if(is_array($registroDatosParticipante)){
                                                        if(strlen($registroDatosParticipante[0][20])<11){
                                                            $cubrimiento[0]="19".substr($registroDatosParticipante[0][20],0,2);
                                                            if(substr($registroDatosParticipante[0][20],2,1)==1)
                                                                $cubrimiento[1]="01";
                                                            else $cubrimiento[1]="02";
                                                        }
                                                        else{
                                                            $cubrimiento[0]=substr($registroDatosParticipante[0][20],0,4);
                                                            if(substr($registroDatosParticipante[0][20],4,1)==1)
                                                                $cubrimiento[1]="01";
                                                            else $cubrimiento[1]="02";
                                                        }
                                                        $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $cubrimiento,"cubrimiento");
                                                        @ $cubrimientoPrograma=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                                        if($cubrimientoPrograma[0][0]==0){
                                                                $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $cubrimiento,"insertarCubrimiento");
                                                                @$Programa=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                                         }//termina cubrimiento programa
                                                        $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies,$registroDatosParticipante,"insertarInscrito");
                                                        @$inscrito=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                                        unset($inscrito);
                                                 }
                                          }//termina actualizacion inscrito
                                          else{
                                              if(is_array($registroDatosParticipante))
                                                   {   $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies,$registroDatosParticipante,"actualizarInscrito");
                                                       @$inscrito=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                                       unset($inscrito);
                                                   }
                                          }

                                            $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies,  $registroPrincipal,"consultarAdmitido");
                                            @$admitido=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);

                                            if($admitido[0][0]==0){
                                                if(is_array($registroDatosParticipante)){
                                                    $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies,$registroDatosParticipante,"insertarAdmitido");
                                                    //echo "<br>".$cadena_sql;exit;
                                                    @$admitido=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                                    unset($admitido);
                                                }
                                            }//termina actualizacon admitido
                                            else{
                                                if(is_array($registroDatosParticipante)){
                                                    $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies,$registroDatosParticipante,"actualizarAdmitido");
                                                    //echo "<br>".$cadena_sql;exit;
                                                    @$admitido=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                                    unset($admitido);
                                                  }
                                            }

                                            $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies,  $registroPrincipal,"consultarPrimiparo");
                                            @$primiparo=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                            if($primiparo[0][0]==0){
                                                if(is_array($registroDatosParticipante)){
                                                        $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies,$registroDatosParticipante,"insertarPrimiparo");
                                                        //echo "<br>".$cadena_sql;exit;
                                                        @$primiparo=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                                        unset($primiparo);
                                                   }
                                             }//termina actualizacion primiparo



                                            // ACTUALIZAR MATRICULADO
                                            if(!isset($unError))
                                            {
                                                unset($unError);

                                                //buscar los registros de estudiantes en el SNIES
                                                $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $registroPrincipal,"matriculadoSnies");
                                                $cadena_sql.=" AND tipo_doc_unico='".$registro[$contador][10]."' " ;
                                                @$registroMatriculado=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);

                                                //Verificar que el matriculado no esta en la tabla MATRICULADO del SNIES <<EXISTE EL MATRICULADO=NO>>
                                                if(!is_array($registroMatriculado)){
                                                    //busca que exista el programa para el periodo seleccionado
                                                    $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $registroPrincipal,"matriculadoSnies");
                                                    $cadena_sql.="AND tipo_doc_unico!='".$registro[$contador][10]."' " ;
                                                    @$registroMatriculado=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                                    //Elimina registro si no corresponden los tipos de documentos
                                                    if(is_array($registroMatriculado) && $registroMatriculado[0][3]!=$registro[$contador][10]){
                                                        //Buscar los datos en la DB Academica de ese registro del matriculado para la tabla estudiante
                                                        $matriculado_borrar[1]=$registroMatriculado[0][2];//numeor documento
                                                        $matriculado_borrar[2]=$registroMatriculado[0][3];//tipo documento
                                                    }

                                                    //insertar los datos del matriculado en la tabla estudiante del SNIES
                                                    $miRegistro[0]=$registro[$contador];
                                                    $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $miRegistro,"insertarMatriculado");
                                                    @$resultadoMatriculado=ejecutar_admin_matriculado($cadena_sql,$accesoSnies,"insertar");
                                                    if($resultadoMatriculado==false){
                                                        @$resultadoMatriculado=ejecutar_admin_matriculado($cadena_sql,$accesoSnies,"error");
                                                        echo "El registro Del Matriculado no pudo ser cargado por:".$resultadoMatriculado."<br>";
                                                    }
                                                    else
                                                             {echo "<br>El registro fue guardado con Exito, como matriculado";
                                                                 $matriculado++;
                                                     }
                                                }
                                            }//cierra if actualiza matriculado


                                            //VERIFICA DUPLICADOS
                                            $est[1]=$registro[$contador][3];    //Documento de Identidad
                                            $est[2]=$registro[$contador][10];   //Tipo de documento
                                            $est[5]='estudiante';
                                            $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies,$est,"consultar");
                                            @$consultaestudiante=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                            //borra registros repetido en las tablas
                                            if(isset($matriculado_borrar) || $consultaestudiante[0][0]>=2){
                                                echo "ingreso Duplicado<br/>";
                                                //buscar los registros de estudiantes en el SNIES
                                                if(!isset($matriculado_borrar)){
                                                        $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $registroPrincipal,"estudianteduplicado");
                                                        @$duplicadoMatriculado=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                                        $matriculado_borrar[1]= $duplicadoMatriculado[0][0];//numero documento
                                                        $matriculado_borrar[2]= $duplicadoMatriculado[0][1];//tipo documento ERRADO
                                                }
                                                $matriculado_borrar[3]=$registro[$contador][1]; //Codio IES
                                                $matriculado_borrar[4]=$registro[$contador][2]; //Periodo
                                                $matriculado_borrar[6]=$registro[$contador][8]; //Codigo Carrera
                                                $matriculado_borrar[7]=$registro[$contador][10];//tipo documento CORRECTO
                                                $matriculado_borrar[8]=$registro[$contador][8]; //Codigo Snies de la carrera
                                                //verifica que que el registro este en las tablas y actualiza y borra duplicados
                                                //verifica graduados
                                                $matriculado_borrar[5]='graduado';
                                                $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $matriculado_borrar,"consultar");
                                                $cadena_sql .= "and pro_consecutivo = ".$registroPrincipal[0][8];
                                                @$consultaegresado=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                                if($consultaegresado[0][0] > 1){
                                                    $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $matriculado_borrar,"buscaDuplicado");
                                                    $duplicados=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                                    $numDuple=count($duplicados);
                                                    $aux=1;
                                                    //Se eliminan todos los registros que salen repetidos a excepción del primer registro
                                                    while($aux<$numDuple){
                                                        $mat_borrar[1]=$matriculado_borrar[1]; //Numero de documento
                                                        $mat_borrar[2]=$matriculado_borrar[8]; //Codigo Snies de la carrera
                                                        $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $matriculado_borrar,"borraRepetidos");
                                                        @$borraEstudiante=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                                        $aux++;
                                                    }
                                                    $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $matriculado_borrar,"actualizar");
                                                    @$actualizaegresado=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                                }
                                                 //verifica egresados
                                                $matriculado_borrar[5]='egresado';
                                                $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $matriculado_borrar,"consultar");
                                                $cadena_sql .= "and pro_consecutivo = ".$registroPrincipal[0][8];
                                                @$consultaegresado=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                                if($consultaegresado[0][0] > 1){
                                                    $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $matriculado_borrar,"buscaDuplicado");
                                                    $duplicados=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                                    $numDuple=count($duplicados);
                                                    $aux=1;
                                                    //Se eliminan todos los registros que salen repetidos a excepción del primer registro
                                                    while($aux<$numDuple){
                                                        $mat_borrar[1]=$matriculado_borrar[1]; //Numero de documento
                                                        $mat_borrar[2]=$matriculado_borrar[8]; //Codigo Snies de la carrera
                                                        $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $matriculado_borrar,"borraRepetidos");
                                                        @$borraEstudiante=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                                        $aux++;
                                                    }
                                                    $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $matriculado_borrar,"actualizar");
                                                    @$actualizaegresado=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                                }
                                                ///VERIFICA DUPLICADOS EN MATRICULADO
                                                $matriculado_borrar[5]='matriculado';
                                                $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $registroPrincipal,"matriculadoduplicado");
                                                @$duplicadoMatriculado=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                                $duple=count($duplicadoMatriculado);
                                                $aux=1;
                                                //Se eliminan todos los registros que salen repetidos a excepción del primer registro
                                                while($aux<$duple){
                                                    $mat_borrar[1]=$matriculado_borrar[1];
                                                    $mat_borrar[2]=$duplicadoMatriculado[$aux][3];  //Tipo de Documento
                                                    $mat_borrar[3]=$duplicadoMatriculado[$aux][0];  //Año
                                                    $mat_borrar[4]=$duplicadoMatriculado[$aux][1];  //Semestre
                                                    $mat_borrar[6]=$duplicadoMatriculado[$aux][4];  //Codigo Carrera
                                                    $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $mat_borrar,"borraMatriculado");
                                                    @$borraEstudiante=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                                    $aux++;
                                                }
                                                //Consulta los matriculados nuevamente, para actualizar el registro que no se modifico
                                                $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $matriculado_borrar,"consultar");
                                                @$consultaegresado=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                                if($consultaegresado[0][0]>=1){
                                                    $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $matriculado_borrar,"actualizar");
                                                    $cadena_sql .= " and est_annio = '".$cubrimiento[0]."'";
                                                    $cadena_sql .= "and est_semestre =";
                                                    if($cubrimiento[1]==2 || $cubrimiento[1]==3)
                                                        $cadena_sql.="'02' " ;
                                                    else
                                                        $cadena_sql.="'01' " ;
                                                    @$actualizaegresado=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                                }
                                                //verifica la tabla de estudiantes por duplicidad                                                
                                                $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $matriculado_borrar,"consultar");
                                                @$consultaestudiante=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                                if($consultaestudiante[0][0]>1){
                                                    //Se debe primero actualizar la tabla matriculado
                                                    $matriculado_borrar[5]='matriculado';
                                                    $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $matriculado_borrar,"actualizar");
                                                    @$actualizaegresado=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                                    //Se debe primero actualizar la relacion es la tabla estudiante_programa para ese estudiante
                                                    $matriculado_borrar[5]='estudiante_programa';
                                                    $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $matriculado_borrar,"actualizar");
                                                    @$actualizaegresado=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                                    //Despues se elimina el registro en estudiente
                                                    $matriculado_borrar[5]='estudiante';
                                                    $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $matriculado_borrar,"borraEstudiante");
                                                    @$actualizaegresado=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                                    $estudiante--;
                                                }
                                                else $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $matriculado_borrar,"actualizar");
                                                @$borraEstudiante=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                                $matriculado_borrar[5]='participante';
                                                $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $matriculado_borrar,"consultar");
                                                @$consultaestudiante=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                                if($consultaestudiante[0][0]>1){
                                                    @$cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $matriculado_borrar,"borraParticipante");
                                                    $participante--;
                                                }
                                                else $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $matriculado_borrar,"actualizar");
                                                 @$borraParticipante=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
                                                 unset($matriculado_borrar);
                                            }
                                }//if($validoP=='SI'){
                                else{
                                    echo "***El registro del estudiante matriculado contiene datos de la academica vacios que son importante reportar.<br>Revise su fuente de informaci&oacute;n!";
                                    $sindatos++;
                                }
                       }//if($valido=='SI'){
                       else{
                            echo "***El registro del estudiante matriculado contiene datos vacios que son importante reportar.<br>Revise su fuente de informaci&oacute;n!";
                            $sindatos++;
                       }
                                unset($registroDatosParticipante);
                       //echo $mensaje
                   }//for($contador=$iter_ini;$contador<$iter_fin;$contador++)
                          $iter_ini=($iter_ini+$secuencia);
                          $iteracion--;
          }//cierra el while para manejar intervalos
						
							
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
                $logger[7]="Se cargo los datos, para el reporte de matriculados al SNIES para el periodo ".$cubrimiento[0]."-".$cubrimiento[1];

                $log_us->log_usuario($logger,$configuracion);

                $logger[1]="Estudiante";
                $logger[2]=$inicialEstudiante[0][0];
                $logger[3]=$estudiante;
                $log_us->log_usuario($logger,$configuracion);

                $logger[1]="Matriculado";
                $logger[2]=$inicialMatriculado[0][2];
                $logger[3]=$matriculado;
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
                                        <td>MATRICULADO</td>
                                        <td><? echo $matriculado;?></td>
                                </tr>
                                <tr class="bloquecentralcuerpo">
                                        <td>SIN CARGAR<BR>(Por falta de información)</td>
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
		
	
	$cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies,"","totalesMatriculadosSnies");
	//echo $cadena_sql; exit;
	$registroMatriculadoTotales=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
	

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
						.::: Datos Matriculados
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
										TOTAL MATRICULADOS
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

			
<?	
	}


/****************************************************************
*  			Funciones				*
****************************************************************/



//Esta funcion sirve para definir la(s) clausula(s) SQL que se utilizan en este bloque

function cadena_busqueda_matriculado($configuracion, $acceso_db, $valor,$opcion="")
{
	$valor=$acceso_db->verificar_variables($valor);
	
	switch($opcion){
	//consulta para generar los datos para la tabla matriculados del SNIES a partir de la DB Academica
            case "matriculado":
                $cadena_sql="SELECT UNIQUE ";
                $cadena_sql.="'1301' ies_code, ";
                $cadena_sql.="'".$valor[0]."' ano, ";
                $cadena_sql.="'".$valor[1]."' periodo, ";
                $cadena_sql.=" est_nro_iden identificacion, ";
                $cadena_sql.="DECODE(cra_jornada, 'DIURNA', '01', 'NOCTURNA', '02', '01' ) horario_code, ";
                $cadena_sql.="'1301' ceres, ";
                $cadena_sql.="'11' departamento, ";
                $cadena_sql.="'11001' municipio, ";
                $cadena_sql.="as_cra_cod_snies, ";
                $cadena_sql.="'01' pago, ";
                $cadena_sql.="DECODE(est_tipo_iden,'',DECODE(length(est_nro_iden),11,'TI',12,'TI','CC'),'C', 'CC', '1', 'CC', 'c', 'CC', 'T', 'TI', '2', 'TI', 't', 'TI', 'E', 'CE', 'P', 'PS', 'CC') tipo_doc_unico, ";
                $cadena_sql.=" est_cod codigo ";
                $cadena_sql.=" FROM mntac.acest";
                $cadena_sql.=" INNER JOIN mntac.v_tot_matri_ape_per ON est_cod = mat_est_cod";
                $cadena_sql.=" INNER JOIN mntac.accra ON cra_cod = mat_cra_cod";
                $cadena_sql.=" INNER JOIN mntac.actipcra ON cra_tip_cra = tra_cod";
                $cadena_sql.=" INNER JOIN mntac.accra_snies ON as_cra_cod = mat_cra_cod";
                $cadena_sql.=" WHERE ";
                $cadena_sql.=" mat_ano=";
                $cadena_sql.="'".$valor[0]."' ";
                $cadena_sql.=" AND mat_per=";
                $cadena_sql.="'".$valor[1]."' ";
                $cadena_sql.=" AND est_nro_iden <> '101' ";
                $cadena_sql.=" AND est_estado_est <> 'N' ";
            break;

//	*******************************************************************************************************PARTICIPANTE
			
		//Consulta de la tabla participante del SNIES LOCAL
		
		case "participanteSnies":
                    $cadena_sql="SELECT ";
                    $cadena_sql.="ies_code, ";					//[0]
                    $cadena_sql.="tipo_doc_unico, ";
                    $cadena_sql.="codigo_unico ";  			//[13]
                    $cadena_sql.="FROM participante ";
                    $cadena_sql.="WHERE ";
                    $cadena_sql.="codigo_unico='" .$valor[0][3]."' " ;
		break;
						
			
		//Consulta de la DB Academica para generar los datos de la tabla participante del SNIES para los matriculados

                /* octubre 28 de 2008 se modifica query para resolver los problemas hallados en la auditoria del snies
                 * la fecha del 1980-02-29 se coloca, puesto que la fecha del estudiante se encuentra en blanco
                 */
                    //nuevo para participante *cuando no existe fecha de nacimiento se coloca por defecto la fecha 1980-02-29

		case "participanteAcademica":
			
			$cadena_sql="SELECT ";
                        $cadena_sql.="TO_CHAR('1301') ies_code, ";
                        $cadena_sql.="SUBSTR(trim(est_nombre),0,INSTR(trim(est_nombre),' ',1,1)) primer_apellido, ";
                        $cadena_sql.="case
                                        when (instr(trim(substr(trim(est_nombre),instr(trim(est_nombre),' ',1,1),length(trim(est_nombre)))),' ',1,2) > 0
                                        or instr(trim(substr(trim(est_nombre),instr(trim(est_nombre),' ',1,1),length(trim(est_nombre)))),' ',1,3) > 0
                                        or instr(trim(substr(trim(est_nombre),instr(trim(est_nombre),' ',1,1),length(trim(est_nombre)))),' ',1,4) > 0)
                                        then trim(substr(trim(substr(trim(est_nombre),1,instr(trim(est_nombre),(trim(substr(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))),instr(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))),' ',-1,1),length(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))))))),1,1) - 1)),instr(trim(substr(trim(est_nombre),1,instr(trim(est_nombre),(trim(substr(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))),instr(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))),' ',-1,1),length(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))))))),1,1) - 1)),' ',-1,1),length(trim(substr(trim(est_nombre),1,instr(trim(est_nombre),(trim(substr(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))),instr(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))),' ',-1,1),length(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))))))),1,1) - 1)))))
                                        else trim(substr(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))),instr(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))),' ',-1,1),length(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))))))
                                      end segundo_apellido,";
                        $cadena_sql.="case
                                        when (instr(trim(substr(trim(est_nombre),instr(trim(est_nombre),' ',1,1),length(trim(est_nombre)))),' ',1,2) > 0
                                        or instr(trim(substr(trim(est_nombre),instr(trim(est_nombre),' ',1,1),length(trim(est_nombre)))),' ',1,3) > 0
                                        or instr(trim(substr(trim(est_nombre),instr(trim(est_nombre),' ',1,1),length(trim(est_nombre)))),' ',1,4) > 0)
                                        then trim(substr(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))),instr(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))),' ',-1,1),length(trim(substr(est_nombre,1,instr(est_nombre,substr(trim(est_nombre),instr(est_nombre,' ',-1,1),length(trim(est_nombre))),1,1))))))
                                        else trim(substr(est_nombre,instr(est_nombre,' ',-1,1),length(trim(est_nombre))))
                                      end primer_nombre,";
                        $cadena_sql.="case
                                        when (instr(trim(substr(trim(est_nombre),instr(trim(est_nombre),' ',1,1),length(trim(est_nombre)))),' ',1,2) > 0
                                        or instr(trim(substr(trim(est_nombre),instr(trim(est_nombre),' ',1,1),length(trim(est_nombre)))),' ',1,3) > 0
                                        or instr(trim(substr(trim(est_nombre),instr(trim(est_nombre),' ',1,1),length(trim(est_nombre)))),' ',1,4) > 0)
                                        then trim(substr(est_nombre,instr(est_nombre,' ',-1,1),length(trim(est_nombre))))
                                        else ' '
                                      end segundo_nombre,";
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
                        $cadena_sql.="TO_CHAR(est_telefono) numero_tel, ";
                        $cadena_sql.="case
                                        when eot_nro_snp = '' then 'N/A'
                                        when eot_nro_snp is null then 'N/A'
                                        when length(replace(eot_nro_snp,' ','')) < 10 then 'N/A' --esto valida datos incongruentes para no mostrar
                                      else replace(eot_nro_snp,' ','') end snp,";
                        $cadena_sql.="est_cod codigo, ";
                        $cadena_sql.="'".$valor[0][8]."' programa, ";
                        $cadena_sql.="TO_CHAR(DECODE(eot_sexo,'M','01','F','02','01')) genero, ";
                        $cadena_sql.="(CASE WHEN length(replace(eot_nro_snp,' ',''))<14 AND SUBSTR(TRIM(eot_nro_snp),5,1)=1  THEN '19'||SUBSTR(replace(eot_nro_snp,' ',''),3,2)||'-04-01'
                                      WHEN length(replace(eot_nro_snp,' ',''))<14 AND SUBSTR(replace(eot_nro_snp,' ',''),5,1)=2 THEN '19'||SUBSTR(replace(eot_nro_snp,' ',''),3,2)||'-09-01'
                                      WHEN length(replace(eot_nro_snp,' ',''))>=14 AND SUBSTR(replace(eot_nro_snp,' ',''),7,1)=1  THEN SUBSTR(replace(eot_nro_snp,' ',''),3,4)||'-04-01'
                                      WHEN length(replace(eot_nro_snp,' ',''))>=14 AND SUBSTR(replace(eot_nro_snp,' ',''),7,1)=2  THEN SUBSTR(replace(eot_nro_snp,' ',''),3,4)||'-09-01'
                                      ELSE 'null' END) fecha_snp ";
                        $cadena_sql.="FROM mntac.acest ";
                        $cadena_sql.="INNER JOIN mntac.acestotr ON est_cod = eot_cod ";
                        $cadena_sql.="INNER JOIN mntge.gemunicipio ON mun_cod=decode(eot_cod_mun_nac,0,11001,'',11001,eot_cod_mun_nac) ";
                        $cadena_sql.="WHERE ";
                        $cadena_sql.="est_nro_iden='".$valor[0][3]."' ";
                        $cadena_sql.="AND est_cod='".$valor[0][11]."' ";
                        $cadena_sql.="ORDER BY est_cod DESC";
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
			$cadena_sql.="'".$valor[0][11]."', ";
                        $cadena_sql.="numero_tel= ";
			$cadena_sql.="'".$valor[0][18]."' ";
                        $cadena_sql.="WHERE ";
			$cadena_sql.="codigo_unico=";
                        $cadena_sql.=" '".$valor[0][13]."' ";//numero doc
	        break;

                case "actualizar":
			$cadena_sql="UPDATE ";
			$cadena_sql.=$valor[5] ;
			$cadena_sql.=" SET ";
			$cadena_sql.="tipo_doc_unico= ";
			$cadena_sql.="'".$valor[7]."' ";
                        $cadena_sql.="WHERE ";
			$cadena_sql.="codigo_unico=";
                        $cadena_sql.="'".$valor[1]."'";//numero doc
	        break;
			
		
		//Consulta de la DB Academica para generar los datos de la tabla participante del SNIES para los matriculados
		
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
                    $cadena_sql.="'".$valor[0][5]."', ";
                    $cadena_sql.="'".$valor[0][6]."', ";
                    if ($valor[0][7]!=0)
                        $cadena_sql.="'".$valor[0][7]."', ";
                    else
                        $cadena_sql.="'11', ";
                    if ($valor[0][8]!=0)
                        $cadena_sql.="'".$valor[0][8]."', ";
                    else
                        $cadena_sql.="'11001', ";
                    $cadena_sql.="'".$valor[0][9]."', ";
                    $cadena_sql.="'".$valor[0][10]."', ";
                    $cadena_sql.="'".$valor[0][11]."', ";
                    $cadena_sql.="'".$valor[0][12]."', ";
                    $cadena_sql.="'".$valor[0][13]."', ";
                    $cadena_sql.="'".$valor[0][14]."', ";
                    if ($valor[0][15]!="")
                        $cadena_sql.="'".$valor[0][15]."', ";
                    else                    
                        $cadena_sql.="'', ";
                    $cadena_sql.="'".$valor[0][16]."', ";
                    $cadena_sql.="'".$valor[0][17]."', ";
                    if ($valor[0][18]!="")
                        $cadena_sql.="'".$valor[0][18]."'";
                    else
                        $cadena_sql.="'0' ";
                    $cadena_sql.=") ";
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
	        break;  	        
	        
//	*******************************************************************************************************ESTUDIANTE			        
			        
			//Consulta de la tabla estudiante del SNIES LOCAL
		
			case "estudianteSnies":
			$cadena_sql="SELECT ";
			$cadena_sql.="ies_code, ";					//[0]
			$cadena_sql.="codigo_unico, ";
			$cadena_sql.="tipo_doc_unico ";
			$cadena_sql.="FROM estudiante ";
			$cadena_sql.="WHERE ";
			$cadena_sql.="codigo_unico='" .$valor[0][3]."' " ;
			//$cadena_sql.="AND tipo_doc_unico='".$valor[0][10]."' " ;
			
			//echo $cadena_sql;
			//exit;
			
			break;
		
		
			case "estudianteAcademica":
			$prefijo="mntac.";	 
			$cadena_sql="SELECT ";
			$cadena_sql.="TO_CHAR('1301') ies_code, ";
			$cadena_sql.="TO_CHAR(est_nro_iden) codigo_unico, ";
                        $cadena_sql.="TO_CHAR(DECODE(est_tipo_iden,'',DECODE(length(est_nro_iden),11,'TI',12,'TI','CC'),'C', 'CC', '1', 'CC', 'c', 'CC', 'T', 'TI', '2', 'TI', 't', 'TI', 'E', 'CE', 'P', 'PS', 'CC')) tipo_doc_unico ";
			$cadena_sql.="FROM ".$prefijo."acest ";
			$cadena_sql.="WHERE ";
			$cadena_sql.="est_nro_iden='" .$valor[0][3]."' " ;
			//echo $cadena_sql."<br>";
			//exit;
			
			break;
		
		
		
				//Consulta de la DB Academica para generar los datos de la tabla participante del SNIES para los matriculados
		
		case "insertarEstudiante":
			$cadena_sql="INSERT INTO ";
			$cadena_sql.="estudiante ";
			$cadena_sql.="VALUES ";
			$cadena_sql.="( ";
                        $cadena_sql.="'".$valor[0][0]."', ";
			$cadena_sql.="'".$valor[0][3]."', ";
                        $cadena_sql.="'".$valor[0][10]."' ";	//numero doc
			$cadena_sql.=") ";
                	//echo $cadena_sql."<br><br>";
			//exit;
			
	        break;

                 case "estudianteduplicado":
			$cadena_sql="SELECT ";
			$cadena_sql.="codigo_unico,";
			$cadena_sql.="tipo_doc_unico ";
			$cadena_sql.="FROM estudiante ";
			$cadena_sql.="WHERE ";
			$cadena_sql.="codigo_unico='".$valor[0][3]."' " ;
			$cadena_sql.="AND tipo_doc_unico!='".$valor[0][10]."' " ;
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
		
//	*******************************************************************************************************MATRICULADO			
		
			//Consulta de la tabla matriculado del SNIES LOCAL
		
		case "matriculadoSnies":
			$cadena_sql="SELECT ";
			$cadena_sql.="est_annio, ";
			$cadena_sql.="est_semestre, ";
			$cadena_sql.="codigo_unico,";
			$cadena_sql.="tipo_doc_unico, ";
			$cadena_sql.="pro_consecutivo ";
			$cadena_sql.="FROM matriculado ";
			$cadena_sql.="WHERE ";
			$cadena_sql.="codigo_unico='" .$valor[0][3]."' " ;
			$cadena_sql.="AND est_annio='".$valor[0][1]."' " ;	
			$cadena_sql.="AND est_semestre=" ;	
			if($valor[0][2]==2 || $valor[0][2]==3)
                            $cadena_sql.="'02' " ;
                        else
                            $cadena_sql.="'01' " ;
			$cadena_sql.="AND pro_consecutivo='".$valor[0][8]."' " ;				
			break;

                        case "matriculadoduplicado":
                            $cadena_sql="SELECT ";
                            $cadena_sql.="est_annio, ";
                            $cadena_sql.="est_semestre, ";
                            $cadena_sql.="codigo_unico,";
                            $cadena_sql.="tipo_doc_unico, ";
                            $cadena_sql.="pro_consecutivo ";
                            $cadena_sql.="FROM matriculado ";
                            $cadena_sql.="WHERE ";
                            $cadena_sql.="codigo_unico='".$valor[0][3]."' ";
                            $cadena_sql.="AND pro_consecutivo='".$valor[0][8]."' " ;
                            $cadena_sql.="AND est_annio = '".$valor[0][1]."'";
                            $cadena_sql.="AND est_semestre=" ;
                            if($valor[0][2]==2 || $valor[0][2]==3)
                                $cadena_sql.="'02' " ;
                            else
                                $cadena_sql.="'01' " ;
			break;
			
			case "insertarMatriculado":
				$cadena_sql="INSERT INTO ";
				$cadena_sql.="matriculado ";
				$cadena_sql.="VALUES ";
				$cadena_sql.="(";
				$cadena_sql.="'".$valor[0][0]."', ";	
				$cadena_sql.="'".$valor[0][1]."', ";
				if($valor[0][2]==2 || $valor[0][2]==3)
							{
							$cadena_sql.="'02', " ;
							}
					else		
							{
							$cadena_sql.="'01', " ;
							}	
				//$cadena_sql.="'".$valor[0][2]."', ";		
				$cadena_sql.="'".$valor[0][3]."', ";		
				$cadena_sql.="'".$valor[0][4]."', ";		
				$cadena_sql.="'".$valor[0][5]."', ";	
				$cadena_sql.="'".$valor[0][6]."', ";		
				$cadena_sql.="'".$valor[0][7]."', ";		
				$cadena_sql.="'".$valor[0][8]."', ";
				$cadena_sql.="'".$valor[0][9]."', ";
				if($valor[0][10]!==null){
				$cadena_sql.="'".$valor[0][10]."' ";	
				}		
				else
				{
				$cadena_sql.="'CC' ";					
				}
				$cadena_sql.=") ";
		
				//Con esta cadena se pueden obtener las consultas para subir manualmente algunos registros
				//echo "0=".$valor[0][0]."<br>";
				//echo "1=".$valor[0][1]."<br>";
				//echo $cadena_sql."<br><br>";
				//exit;
				
	        break;
	        
	        case "borraMatriculado":
                    $cadena_sql="DELETE ";
                    $cadena_sql.="FROM ";
                    $cadena_sql.="matriculado ";
                    $cadena_sql.="WHERE ";
                    $cadena_sql.="codigo_unico=";			//numero doc
                    $cadena_sql.="'".$valor[1]."' ";		//tipo doc
                    $cadena_sql.="AND tipo_doc_unico=";
                    $cadena_sql.="'".$valor[2]."' ";
                    $cadena_sql.="AND est_annio=";
                    $cadena_sql.="'".$valor[3]."' ";
                    $cadena_sql.="AND est_semestre=";
                    if($valor[4]==2 || $valor[4]==3)
                        {$cadena_sql.="'02' " ;}
                    else{$cadena_sql.="'01' " ;}
                    $cadena_sql.="AND pro_consecutivo=";
                    $cadena_sql.="'".$valor[6]."' ";
	        break;

                case "consultarInscrito":
			$cadena_sql="Select ";
			$cadena_sql.="count (*)  ";
			$cadena_sql.="FROM inscrito ";
			$cadena_sql.="WHERE ";
			$cadena_sql.="documento='".$valor[0][3]."' " ;
			$cadena_sql.="AND prog_prim_opc='".$valor[0][8]."' " ;



			//echo $cadena_sql;
			//exit;

			break;

                case "insertarInscrito":
                        $cadena_sql="INSERT INTO ";
                        $cadena_sql.="inscrito ";
                        $cadena_sql.="VALUES ";
                        $cadena_sql.="(";
                        $cadena_sql.="'".$valor[0][0]."', ";
                        if(strlen($valor[0][20])<11)
                            {$cadena_sql.="'19".substr($valor[0][20],0,2)."', ";
                             $cadena_sql.="'0".substr($valor[0][20],2,1)."', ";
                            }
                        else{$cadena_sql.="'".substr($valor[0][20],0,4)."', ";
                             $cadena_sql.="'0".substr($valor[0][20],4,1)."', ";
                            }
                        $cadena_sql.="'".$valor[0][12]."', ";
                        $cadena_sql.="'".$valor[0][13]."', ";
                        $cadena_sql.="replace('".$valor[0][2]."','?','') ,";
                        $cadena_sql.="'".$valor[0][21]."', ";
                        $cadena_sql.="'0', ";
                        $cadena_sql.="'0', ";
                        $cadena_sql.="'".$valor[0][19]."', ";
                        $cadena_sql.="'".$valor[0][22]."', ";
                        $cadena_sql.="replace('".$valor[0][3]."','?','') ,";
                        $cadena_sql.="replace('".$valor[0][4]."','?','') ,";
                        $cadena_sql.="replace('".$valor[0][1]."','?','') ,";
                        $cadena_sql.="'1301', ";
                        if ($valor[0][15]="'0'"){
                        $cadena_sql.="'11001', ";
                        }
                        else
                        {
                        $cadena_sql.="'".$valor[0][15]."', ";
                        }
                        if ($valor[0][16]="'0'"){
                        $cadena_sql.="'11' ";
                        }
                        else
                        {
                        $cadena_sql.="'".$valor[0][16]."' ";
                        }
                        $cadena_sql.=") ";
                        //Con esta cadena se pueden obtener las consultas para subir manualmente algunos registros
                        //echo $cadena_sql."<br><br>";
                        //echo "ano=".$valor[0][9]."<br>";
                        //echo "mes=".$valor[0][16];
                        //echo $cadena_sql."<br>";
                        //exit;
	        break;

                case "actualizarInscrito":
                        $cadena_sql="UPDATE ";
                        $cadena_sql.="inscrito ";
                        $cadena_sql.="SET ";
                        $cadena_sql.="tipo_ident_code=";
                        $cadena_sql.="'".$valor[0][12]."', ";
                        $cadena_sql.="segundo_apellido= ";
                        $cadena_sql.="replace('".$valor[0][2]."','?','') ,";
                        $cadena_sql.="genero= ";
                        $cadena_sql.="'".$valor[0][22]."', ";
                        $cadena_sql.="primer_nombre= ";
                        $cadena_sql.="replace('".$valor[0][3]."','?','') ,";
                        $cadena_sql.="segundo_nombre= ";
                        $cadena_sql.="replace('".$valor[0][4]."','?','') ,";
                        $cadena_sql.="primer_apellido= ";
                        $cadena_sql.="replace('".$valor[0][1]."','?','') ,";
                        $cadena_sql.="where ";
                        $cadena_sql.="documento= ";
                        $cadena_sql.="'".$valor[0][13]."' ";

                        //Con esta cadena se pueden obtener las consultas para subir manualmente algunos registros
                        //echo $cadena_sql."<br><br>";
                        //echo "ano=".$valor[0][9]."<br>";
                        //echo "mes=".$valor[0][16];
                        //echo $cadena_sql."<br>";
                        //exit;

	        break;


                case "consultarAdmitido":
			$cadena_sql="Select ";
			$cadena_sql.="count (*)  ";
			$cadena_sql.="FROM admitido ";
			$cadena_sql.="WHERE ";
			$cadena_sql.="documento='".$valor[0][3]."' " ;
			$cadena_sql.="AND pro_consecutivo='".$valor[0][8]."' " ;



			//echo $cadena_sql;
			//exit;

			break;

                case "insertarAdmitido":
                            $cadena_sql="INSERT INTO ";
                            $cadena_sql.="admitido ";
                            $cadena_sql.="VALUES ";
                            $cadena_sql.="(";
                            $cadena_sql.="'".$valor[0][12]."', ";
                            $cadena_sql.="'".$valor[0][13]."', ";
                            $cadena_sql.="replace('".$valor[0][2]."','?','') ,";
                            $cadena_sql.="'".$valor[0][21]."', ";
                            $cadena_sql.="'".$valor[0][19]."', ";
                            if ($valor[0][23]!=""){
                            $cadena_sql.="'".$valor[0][23]."', ";
                            }
                            else
                            {
                            $cadena_sql.="null, ";
                            }
                            $cadena_sql.="'".$valor[0][0]."', ";
                            if(strlen($valor[0][20])<11)
                                    {$cadena_sql.="'19".substr($valor[0][20],0,2)."', ";
                                     $cadena_sql.="'0".substr($valor[0][20],2,1)."', ";
                                    }
                                else{$cadena_sql.="'".substr($valor[0][20],0,4)."', ";
                                     $cadena_sql.="'0".substr($valor[0][20],4,1)."', ";
                                    }
                            $cadena_sql.="'11', ";
                            $cadena_sql.="'11001', ";
                            //Segun diccinario de datos del snies de admitidos el departamento y municipio es donde se dicta el programa
                            $cadena_sql.="replace('".$valor[0][3]."','?','') ,";
                            $cadena_sql.="replace('".$valor[0][4]."','?','') ,";
                            $cadena_sql.="replace('".$valor[0][1]."','?','') ,";
                            $cadena_sql.="'".$valor[0][0]."', ";
                            $cadena_sql.="'".$valor[0][22]."' ";
                            $cadena_sql.=") ";
			//echo $cadena_sql."<br>";
			//exit;

	        break;

                case "actualizarAdmitido":

			$cadena_sql="UPDATE ";
			$cadena_sql.="admitido ";
			$cadena_sql.="SET ";
                        $cadena_sql.="tipo_identif=";
			$cadena_sql.="'".$valor[0][12]."', ";
                        $cadena_sql.="segundo_apellido=";
                        $cadena_sql.="replace('".$valor[0][2]."','?','') ,";
			//$cadena_sql.="'".$valor[0][6]."', ";
			$cadena_sql.="departamento=";
			$cadena_sql.="'11', ";
                        $cadena_sql.="municipio=";
			$cadena_sql.="'11001', ";
			//Segun diccinario de datos del snies de admitidos el departamento y municipio es donde se dicta el programa
			$cadena_sql.="primer_nombre=";
                        $cadena_sql.="replace('".$valor[0][3]."','?','') ,";
			$cadena_sql.="segundo_nombre=";
			$cadena_sql.="replace('".$valor[0][4]."','?','') ,";
                        $cadena_sql.="primer_apellido=";
			$cadena_sql.="replace('".$valor[0][1]."','?','') ,";
			$cadena_sql.="genero=";
			$cadena_sql.="'".$valor[0][12]."' ";
			$cadena_sql.="WHERE ";
                        $cadena_sql.="tipo_identif=";
			$cadena_sql.="'".$valor[0][12]."' ";
                        $cadena_sql.="AND ";
                        $cadena_sql.="documento='".$valor[0][13]."' ";
                        //echo $cadena_sql."<br><br>";
			//exit;
			break;


                case "consultarPrimiparo":
			$cadena_sql="Select ";
			$cadena_sql.="count (*)  ";
			$cadena_sql.="FROM estudiante_programa ";
			$cadena_sql.="WHERE ";
			$cadena_sql.="codigo_unico='".$valor[0][3]."' " ;
			$cadena_sql.="AND pro_consecutivo='".$valor[0][8]."' " ;

			//echo $cadena_sql;
			//exit;

			break;
                 case "insertarPrimiparo":
                            $cadena_sql="INSERT INTO ";
                            $cadena_sql.="estudiante_programa ";
                            $cadena_sql.="VALUES ";
                            $cadena_sql.="(";
                            $cadena_sql.="'".$valor[0][21]."', ";
                            if(strlen($valor[0][20])<11)
                                    {$cadena_sql.="'19".substr($valor[0][20],0,2)."', ";
                                     $cadena_sql.="'0".substr($valor[0][20],2,1)."', ";
                                    }
                                else{$cadena_sql.="'".substr($valor[0][20],0,4)."', ";
                                     $cadena_sql.="'0".substr($valor[0][20],4,1)."', ";
                                    }
                            $cadena_sql.="'02', ";
                            $cadena_sql.="'".$valor[0][0]."', ";
                            $cadena_sql.="'".$valor[0][13]."', ";
                            $cadena_sql.="'".$valor[0][12]."' ";
                            $cadena_sql.=") ";
			//echo $cadena_sql."<br>";
			//exit;

	        break;

			
			case "cubrimiento":
				$cadena_sql="Select ";			
				$cadena_sql.="count (*)  ";			
				$cadena_sql.="from  cubrimiento_programa ";
				$cadena_sql.="WHERE ";
				$cadena_sql.="annio='" .$valor[0]."' " ;
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
			
			case "totalesMatriculadosSnies":
					$cadena_sql="Select ";			
					$cadena_sql.="est_annio, ";
					$cadena_sql.="est_semestre, ";					//[0]
					$cadena_sql.="count (*)  ";			
					$cadena_sql.="from  matriculado ";
					if($valor!="")
						{$cadena_sql.="where ";
						 $cadena_sql.="est_annio=";			
						 $cadena_sql.="'".$valor[0]."' ";		
						 $cadena_sql.="AND est_semestre=";
						 if($valor[1]==2 || $valor[1]==3)
							{
							$cadena_sql.="'02' " ;
							}
						 else		
							{
							$cadena_sql.="'01' " ;
							}
						}
					$cadena_sql.="group by est_semestre, est_annio ";
					$cadena_sql.="order by est_annio DESC, est_semestre DESC";			
					
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

                    case "consultar":
				$cadena_sql="Select ";
				$cadena_sql.="count (*)  ";
				$cadena_sql.="from  ";
                                $cadena_sql.=$valor[5] ;
				$cadena_sql.=" WHERE ";
				$cadena_sql.="codigo_unico='".$valor[1]."' " ;
                    break;
				
                    case "tomarIdActual":
                            $cadena_sql="Select * ";
                            $cadena_sql.="from logaudit  ";
                            $cadena_sql.=" WHERE ";
                            $cadena_sql.="lau_vlrini like '%".$valor."%'";
                    break;

                    case "borrarDuplicado":
                            $cadena_sql="delete ";
                            $cadena_sql.="from ".$valor[5];
                            $cadena_sql.=" WHERE ";
                            $cadena_sql.="codigo_unico ='".$valor[1]."' ";
                            $cadena_sql.="and tipo_doc_unico ='".$valor[0]."'";
                    break;

                    case "buscaDuplicado":
                        $cadena_sql="SELECT ";
                        $cadena_sql.="codigo_unico,";
                        $cadena_sql.="tipo_doc_unico, ";
                        $cadena_sql.="pro_consecutivo ";
                        $cadena_sql.="FROM ".$valor[5];
                        $cadena_sql.=" WHERE ";
                        $cadena_sql.="codigo_unico='".$valor[1]."' ";
                        $cadena_sql.="AND pro_consecutivo='".$valor[8]."' " ;
                    break;

                    case "borraRepetidos":
                        $cadena_sql="DELETE ";
                        $cadena_sql.="FROM ".$valor[5];
                        $cadena_sql.=" WHERE ";
                        $cadena_sql.="codigo_unico=";			//numero doc
                        $cadena_sql.="'".$valor[1]."' ";
                        $cadena_sql.="AND pro_consecutivo=";
                        $cadena_sql.="'".$valor[8]."' ";                //Codigo Snies de la carrera
                        $cadena_sql.="AND tipo_doc_unico = '".$valor[2]."'";
                    break;

                    
                    default:
                        $cadena_sql="";
                    break;
	}
	//echo $cadena_sql."<br>";
	//exit;
	return $cadena_sql;
}

function ejecutar_admin_matriculado($cadena_sql,$acceso_db,$tipo="")
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

//Funcion donde controla los registros ruidosos que se eliminan
function controlEliminaDuplicados($valores,$baseDatos){
    echo "<hr>";
    echo "Documento Estudiante: ".$valores[1]." Tipo: ".$valores[0]."<br/>";
    //EGRESADO
    $valores[5] = "egresado";   //Nombre de la tabla
    eliminaDuplicados($valores,$baseDatos);
    //GRADUADO
    $valores[5] = "graduado";   //Nombre de la tabla
    eliminaDuplicados($valores,$baseDatos);
    //MATRICULADOS
    $valores[5] = "matriculado";   //Nombre de la tabla
    eliminaDuplicados($valores,$baseDatos);
    //ESTUDIANTE PROGRAMA
    $valores[5] = "estudiante_programa";   //Nombre de la tabla
    eliminaDuplicados($valores,$baseDatos);
    //ESTUDIANTE
    $valores[5] = "estudiante";   //Nombre de la tabla
    eliminaDuplicados($valores,$baseDatos);
    //PARTICIPANTE
    $valores[5] = "participante";   //Nombre de la tabla
    eliminaDuplicados($valores,$baseDatos);
}

//Funcion para eliminar registros ruidoso y duplicados asociados
function eliminaDuplicados($parame,$accesoSnies){
    $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $parame,"consultar");
    $consulta=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
    if($consulta[0][0]>0){
        $cadena_sql=cadena_busqueda_matriculado($configuracion, $accesoSnies, $parame,"borrarDuplicado");
        /*echo "encontro relacion en ".$parame[5]."<br/>";
        echo "<pre>";
        var_dump($parame);
        echo "</pre>";
        echo $cadena_sql;*/
        $consulta=ejecutar_admin_matriculado($cadena_sql,$accesoSnies);
        echo "Se elimino el registro de ".$parame[5]."<br/>";
        //exit;
    }
    return;
}

?>
