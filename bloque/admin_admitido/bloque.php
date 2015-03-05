<?
/*
 * ############################################################################
 * # UNIVERSIDAD DISTRITAL Francisco Jose de Caldas #
 * # Copyright: Vea el archivo LICENCIA.txt que viene con la distribucion #
 * ############################################################################
 */
/**
 * **************************************************************************
 * ription Bloque principal para la administracion de admitidos
 *
 * @name bloque.php
 *       @revision Ultima revision 10 de junio de 2008
 *       ****************************************************************************
 * @subpackage admin_admitido
 * @package bloques
 * @copyright
 *
 * @version 0.3
 * @link N/D
 *       ****************************************************************************
 */
if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}

include ($configuracion ["raiz_documento"] . $configuracion ["estilo"] . "/" . $this->estilo . "/tema.php");
// Se incluye para manejar los mensajes de error
include_once ($configuracion ["raiz_documento"] . $configuracion ["clases"] . "/alerta.class.php");
include_once ($configuracion ["raiz_documento"] . $configuracion ["clases"] . "/navegacion.class.php");
include_once ($configuracion ["raiz_documento"] . $configuracion ["clases"] . "/log.class.php");
require_once ($configuracion ["raiz_documento"] . $configuracion ["clases"] . "/barraProgreso.class.php");
global $valor;
// Pagina a donde direcciona el menu
$pagina = "registro_admitido";

$conexion = new dbConexion ( $configuracion );
$acceso_db = $conexion->recursodb ( $configuracion, "oracle2" );
$log_us = new log ();

$enlace = $acceso_db->conectar_db ();

// echo $enlace." este es el recurso ORACLE";

$accesoSnies = $conexion->recursodb ( $configuracion, "sniesLocal" );
$enlaceBlade1 = $accesoSnies->conectar_db ();

// echo $enlace."<br>";
// echo $enlaceAcademica."<br>";
// echo $enlaceBlade1."<br>";
// exit;

if ($enlace && $enlaceBlade1) {
	
	// Rescatar los admitidos de la base de datos academica
	// si se envia la variable de anno y/o periodo
	
	if (isset ( $_REQUEST ["annio"] ) and isset ( $_REQUEST ["periodo"] )) {
		$valor [0] = $_REQUEST ["annio"];
		// echo "annio=".$valor[0]."<br>";
		
		$valor [1] = $_REQUEST ["periodo"];
		// echo "periodo=".$valor[1];
		$participante = 0;
		$estudiante = 0;
		$admitido = 0;
		?>
<table width="100%" align="center" border="0" cellpadding="10"
	cellspacing="0">
	<tbody>
		<tr>
			<td>
				<table width="100%" border="0" align="center" cellpadding="5 px"
					cellspacing="1px">
					<tr class="centralcuerpo">
						<td>
											.::: Actualuzaci&oacute;n Admitidos <? echo $valor[0]." - ".$valor[1];?>
											</td>
					</tr>
				</table>
			</td>
		</tr>
	</tbody>
</table>

<?
		
		// barra de progreso busqueda de registros
		// $bar = new barraProgreso($message='<br>Buscando Registros...', $hide=true, $sleepOnFinish=0, $barLength=500, $precision=50, $backgroundColor='#cccccc', $foregroundColor='blue');
		// $bar->initialize(1);
		
		// Rescatar TODOS los admitidos de un periodo y anno especifico
		
		// Termina barra de progreso despues del proceso de busqueda
		// $bar->increase();
		
		$cadena_sql = cadena_busqueda_admitido ( $configuracion, $acceso_db, $valor, "admitido" );
		// $cadena_sql.=' and asp_nro_iden=10304286';
		// echo "<br> admitido <br>".$cadena_sql;//exit;
		$registro = ejecutar_admin_admitido ( $cadena_sql, $acceso_db );
		
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
			echo '<br>1' . $registro [$key] [1];
			echo '<br>2' . $registro [$key] [2];
			echo '<br>3' . $registro [$key] [3];
			echo '<br>4' . $registro [$key] [4];
			echo '<br>5' . $registro [$key] [5];
			echo '<br>6' . $registro [$key] [6];
			echo '<br>7' . $registro [$key] [7];
			echo '<br>8' . $registro [$key] [8];
			echo '<br>9' . $registro [$key] [9];
			echo '<br>10' . $registro [$key] [10];
			echo '<br>11' . $registro [$key] [11];
			echo '<br>12' . $registro [$key] [12];
			echo '<br>13' . $registro [$key] [13];
			echo '<br>14' . $registro [$key] [14];
			echo '<br>15' . $registro [$key] [15];
			echo '<br>16' . $registro [$key] [16];
			echo '<br>17' . $registro [$key] [17];
			*/			 
			
			$registro [$key] ['3'] = str_replace ( $acento, $sinAcento, $registro [$key] ['3'] );//nombre
			$registro [$key] ['12'] = str_replace ( $acento, $sinAcento, $registro [$key] ['12'] );//nombre
			$registro [$key] ['13'] = str_replace ( $acento, $sinAcento, $registro [$key] ['13'] );//apellido
			$registro [$key] ['14'] = str_replace ( $acento, $sinAcento, $registro [$key] ['14'] );//apellido
			$registro [$key] ['17'] = str_replace ( $acento, $sinAcento, $registro [$key] ['17'] );//programa
			$registro [$key] ['PRIMER_NOMBRE'] = str_replace ( $acento, $sinAcento, $registro [$key] ['PRIMER_NOMBRE'] );
			$registro [$key] ['SEGUNDO_NOMBRE'] = str_replace ( $acento, $sinAcento, $registro [$key] ['SEGUNDO_NOMBRE'] );
			$registro [$key] ['PRIMER_APELLIDO'] = str_replace ( $acento, $sinAcento, $registro [$key] ['PRIMER_APELLIDO'] );
			$registro [$key] ['SEGUNDO_APELLIDO'] = str_replace ( $acento, $sinAcento, $registro [$key] ['SEGUNDO_APELLIDO'] );
			$registro [$key] ['PROG'] = str_replace ( $acento, $sinAcento, $registro [$key] ['PROG'] );
			// echo $registro [$key] ['SEGUNDO_APELLIDO'];
			// var_dump($admitido);exit;
			
			// echo $admitido ['SEGUNDO_APELLIDO'];
			// echo '<br>';
		}
		
		$sindatos = 0;
		$reg_sindatos = array ();
		$error = array ();
		// echo "admitidos ".count($registro);
		if (! is_array ( $registro )) {
			$cadena = htmlentities ( $cadena, ENT_COMPAT, "UTF-8" );
			echo "No existen registros en la Base de Datos Academica para este per&iacute;odo";
			// alerta::sin_registro($configuracion,$cadena);
		} else {
			$contar = count ( $registro );
			// barra de progreso
			// $bar = new barraProgreso($message=$mensaje, $hide=true, $sleepOnFinish=0, $barLength=500, $precision=50, $backgroundColor='#cccccc', $foregroundColor='blue');
			// $bar->initialize($contar);
			
			// toma los totales iniciales de los registros
			$cadena_sql = cadena_busqueda_admitido ( $configuracion, $accesoSnies, $valor, "totalesAdmitidoSnies" );
			// echo $cadena_sql;//exit;
			$inicialAdmitidos = ejecutar_admin_admitido ( $cadena_sql, $accesoSnies );
			
			// inicia el la secuenca para la carga de datos
			for($contador = 0; $contador < $contar; $contador ++) {
				$registroPrincipal [0] = $registro [$contador];
				$aux = 0;
				$valido = 'SI';
				
				$cont_error = 0;
				// valida que el registro no tenga datos nulos que son obligatorios en Snies
				while ( $aux <= 16 && $valido == 'SI' ) {
					if ((chop ( $registroPrincipal [0] [$aux] ) == '' || $registroPrincipal [0] [$aux] == 'N/A') && $aux != 3 && $aux != 6 && $aux != 13) {
						if (chop ( $registroPrincipal [0] [$aux] ) == '') {
							$valido = 'NO';
						}
						// registra el error
						switch ($aux) {
							case "1" :
								$error [$aux] = "El Tipo de Identificación esta vacio";
								$cont_error ++;
								break;
							case "2" :
								$error [$aux] = "El Número de Identificación esta vacio";
								$cont_error ++;
								break;
							case "4" :
								$error [$aux] = "El codigo del proyecto Curricular esta vacio";
								$cont_error ++;
								break;
							case "5" :
								$error [$aux] = "El codigo SNP del ICFES esta vacio";
								$cont_error ++;
								break;
							case "8" :
								$error [$aux] = "El año esta vacio";
								$cont_error ++;
								break;
							case "9" :
								$error [$aux] = "El Periodo esta vacio";
								$cont_error ++;
								break;
							case "10" :
								$error [$aux] = "El Codigo del Departamento esta vacio";
								$cont_error ++;
								break;
							case "11" :
								$error [$aux] = "El Codigo del Municipio esta vacio";
								$cont_error ++;
								break;
							case "11" :
								$error [$aux] = "El Codigo del Departamento esta vacio";
								$cont_error ++;
								break;
							case "12" :
								$error [$aux] = "El Primer Nombre esta vacio";
								$cont_error ++;
								break;
							case "14" :
								$error [$aux] = "El Primer Apellido esta vacio";
								$cont_error ++;
								break;
							case "16" :
								$error [$aux] = "El Genero esta vacio";
								$cont_error ++;
								break;
						}
					}
					// echo "<br>".$aux." - ".$registroPrincipal[0][$aux]." ".$valido;
					$aux ++;
				} // exit;
				  // echo "codigo ".number_format($registro[$contador][4],1,".","");
				  // $bar->increase();
				  
				// se selecciona cada registro de admitidos, uno a la vez
				unset ( $valor [2] );
				unset ( $unError );
				$valor [2] = $registro [$contador] [0];
				$valor [3] = number_format ( $registro [$contador] [4], 1, ".", "" );
				
				// echo "<H2>".$contador."</H2>".$valor[2]."<br>";
				// echo "<hr class='hr_subtitulo'>";
				// Que contiene los datos de la busqueda inicial
				echo "<hr>" . $contador . " - Admitidos : " . $registroPrincipal [0] [1] . " " . $registroPrincipal [0] [0] . " ";
				echo "<br>" . $registroPrincipal [0] [14] . " " . $registroPrincipal [0] [3] . " " . $registroPrincipal [0] [12] . " " . $registroPrincipal [0] [13] . "";
				echo "<br>proyecto : " . $registroPrincipal [0] [17] . " ";
				
				// echo $valor[2]."<br>";
				// exit;
				
				if ($cont_error > 0) {
					$reg_sindatos [$sindatos] ['tipo_doc'] = $registroPrincipal [0] [1];
					$reg_sindatos [$sindatos] ['nro_doc'] = $registroPrincipal [0] [0];
					$reg_sindatos [$sindatos] ['nombre'] = $registroPrincipal [0] [14] . " " . $registroPrincipal [0] [3] . " " . $registroPrincipal [0] [12] . " " . $registroPrincipal [0] [13];
					$reg_sindatos [$sindatos] ['proyecto'] = $registroPrincipal [0] [17];
					
					$errores = '';
					foreach ( $error as $key => $value ) {
						if (chop ( $error [$key] ) != "") {
							$errores .= $error [$key] . ", ";
						}
					}
					$reg_sindatos [$sindatos] ['errores'] = $errores;
					$sindatos = $sindatos + 1;
					unset ( $error );
					unset ( $cont_error );
				}
				
				if ($valido == "SI") {
					// ACTUALIZAR ADMITIDO
					
					// buscar los registros de participantes en el SNIES
					$cadena_sql = cadena_busqueda_admitido ( $configuracion, $accesoSnies, $valor, "admitidoSnies" );
					// echo "<br> admitidosnies <br>".$cadena_sql;
					$registroAdmitidoSnies = ejecutar_admin_admitido ( $cadena_sql, $accesoSnies );
					
					// Verificar que el admitido no esta en la tabla INSCRITO del SNIES <<EXISTE EL PARTICIPANTE=NO>>
					/*
					 * if(!is_array($registroAdmitidoSnies))
					 *
					 * {
					 */
					// busca que exista el programa para el periodo seleccionado
					$cubrimiento [0] = $valor [0]; // anno
					$cubrimiento [1] = $valor [1]; // periodo
					$cubrimiento [2] = $registro [$contador] [4];
					
					$cadena_sql = cadena_busqueda_admitido ( $configuracion, $accesoSnies, $cubrimiento, "cubrimiento" );
					// echo "<br>".$cadena_sql;
					$cubrimientoPrograma = ejecutar_admin_admitido ( $cadena_sql, $accesoSnies );
					
					if ($cubrimientoPrograma [0] [0] == 0) {
						$cadena_sql = cadena_busqueda_admitido ( $configuracion, $accesoSnies, $cubrimiento, "insertarCubrimiento" );
						// echo "<br>".$cadena_sql;exit;
						$Programa = ejecutar_admin_admitido ( $cadena_sql, $accesoSnies );
					}
					// verifica que existe el proyecto en la tabla lista programa
					$cadena_sql = cadena_busqueda_admitido ( $configuracion, $accesoSnies, $cubrimiento, "lista_programa" );
					// echo "<br>".$cadena_sql;
					$listaPrograma = ejecutar_admin_admitido ( $cadena_sql, $accesoSnies );
					
					if ($listaPrograma [0] [0] == 0) {
						$cadena_sql = cadena_busqueda_admitido ( $configuracion, $accesoSnies, $cubrimiento, "insertarPrograma" );
						// echo "<br>".$cadena_sql;exit;
						$Programa = ejecutar_admin_admitido ( $cadena_sql, $accesoSnies );
					} // termina lista programa
					
					if (! is_array ( $registroAdmitidoSnies )) 

					{
						
						// insertar los datos del admitido en la tabla INSCRITO del SNIES
						
						$miRegistro [0] = $registro [$contador];
						$cadena_sql = cadena_busqueda_admitido ( $configuracion, $accesoSnies, $miRegistro, "insertarAdmitido" );
						// echo "<br>insertar<br> ".$cadena_sql."<br>";//exit;
						$resultadoAdmitido = ejecutar_admin_admitido ( $cadena_sql, $accesoSnies, "insertar" );
						
						if ($resultadoAdmitido == false) {
							$resultadoAdmitido = ejecutar_admin_admitido ( $cadena_sql, $accesoSnies, "error" );
							echo "<font color='red'>El registro no pudo ser cargado en la tabla admitido por:" . $resultadoAdmitido . "<br></font>";
							$unError = true;
							// echo "El registro no pudo ser cargado, por favor verifique los datos en la Aplicaci&oacute;n Acad&eacute;mica <br>";
							// echo "<hr class='hr_subtitulo'>";
						} else {
							$admitido ++;
							echo "<font color='green'><br>El registro se guardo satisfactoriamente como ADMITIDO<br></font>";
						}
					} else {
						
						// insertar los datos del admitido en la tabla INSCRITO del SNIES
						
						$miRegistro [0] = $registro [$contador];
						$cadena_sql = cadena_busqueda_admitido ( $configuracion, $accesoSnies, $miRegistro, "actualizarAdmitido" );
						// echo "<br>actualizar<br> ".$cadena_sql."<br>";//exit;
						@$resultadoAdmitido = ejecutar_admin_admitido ( $cadena_sql, $accesoSnies, "" );
						echo "<font color='green'><br>El registro se ha actualizado<br></font>";
					} // cierra el if que carga el admitido
				}  // cierra el if que verifica el nuemro de documento
else {
					echo "<font color='red'><br><br>El registro contiene algunos datos nulos, revise su fuente de información:<br></font>";
					echo $errores;
				}
				unset ( $errores );
				unset ( $valido );
			} // cierra el for que recorre los registros
		} // fin actualizacion
		  // VARIABLES PARA EL LOG
		$logger [0] = "CARGAR AL SNIES LOCAL";
		$logger [2] = $inicialAdmitidos [0] [2];
		$logger [3] = $admitido;
		date_default_timezone_set ( 'UTC' );
		$logger [4] = date ( 'd/m/Y h:i:s', time () );
		$logger [5] = $valor [0];
		$logger [6] = $valor [1];
		$logger [7] = "Se cargo los datos, para el reporte de Admitidos al SNIES para el periodo " . $valor [0] . "-" . $valor [1];
		// echo $logger[7];
		$log_us->log_usuario ( $logger, $configuracion );
		
		?>
<table width="100%" align="center" border="0" cellpadding="10"
	cellspacing="0">
	<tbody>
		<tr>
			<td>
				<table align="center" border="0" cellpadding="5" cellspacing="0"
					class="bloquelateral_2" width="100%">
					<tr class="bloquecentralencabezado">
						<td colspan='3'>Resultados</td>
					</tr>
					<tr class="bloquecentralcuerpo">
						<td>TABLA</td>
						<td>REGISTROS CARGADOS</td>
					</tr>
					<tr class="bloquecentralcuerpo">
						<td>ADMITIDO</td>
						<td><?	echo $admitido;?></td>
					</tr>
					<tr class="bloquecentralcuerpo">
						<td>Registros SIN CARGAR o con Informaci&oacute;n incompleta</td>
						<td><?	echo $sindatos;?></td>
					</tr>
				</table>
			</td>
		</tr>
	</tbody>
</table>
<?
		
		if (isset ( $reg_sindatos )) {
			$nombre_xls = "ErroresAdmitidos" . $valor [0] . "-" . $valor [1];
			generar_archivo_errores ( $configuracion, $nombre_xls, $reg_sindatos );
			unset ( $reg_sindatos );
		}
	} // cierra el if que verifica año y periodo
	  
	// /busca los taotales
	$cadena_sql = cadena_busqueda_admitido ( $configuracion, $accesoSnies, "", "totalesAdmitidoSnies" );
	// echo "<br>totales<br> ".$cadena_sql."<br>";
	$registroAdmitidoTotales = ejecutar_admin_admitido ( $cadena_sql, $accesoSnies );
	
	// echo $totalAdmitido."total";
	// exit;
	
	?>

<table width="100%" align="center" border="0" cellpadding="10"
	cellspacing="0">
	<tbody>
		<tr>
			<td>
				<table width="100%" border="0" align="center" cellpadding="5 px"
					cellspacing="1px">
					<tr class="centralcuerpo">
						<td>.::: Datos Admitidos</td>
					</tr>

					<tr>
						<td>
							<table class="contenidotabla">
								<tr class="cuadro_color">
									<td width="25%" class="cuadro_plano centrar">A&Ntilde;O</td>
									<td width="25%" class="cuadro_plano centrar">SEMESTRE</td>
									<td class="cuadro_plano centrar">TOTAL ADMITIDOS</td>
								</tr>	
									
									<?
	
	$n = 0;
	while ( count ( $registroAdmitidoTotales ) > $n ) {
		if (is_array ( $registroAdmitidoTotales [$n] )) {
			?>
									
									<tr>
									<td class="cuadro_plano centrar"><span class="texto_negrita"><? echo $totalAdmitido=$registroAdmitidoTotales[$n][0];?></span>
									</td>
									<td class="cuadro_plano centrar"><span class="texto_negrita"><? echo $totalAdmitido=$registroAdmitidoTotales[$n][1];?></span>
									</td>
									<td class="cuadro_plano centrar centrar"><span
										class="texto_negrita"><? echo $totalAdmitido=$registroAdmitidoTotales[$n][2];?></span>
									</td>
								</tr>
									<?
			
			$n = $n + 1;
		}
	}
	?>
						</table>
						</td>
					</tr>
				</table>
			</td>
			<td>
				<table width="100%" border="0" align="center" cellpadding="5 px"
					cellspacing="1px">
					<tr class="centralcuerpo">
						<td>.::: Errores</td>
					</tr>

					<tr>
						<td>
							<table class="contenidotabla">
								<tr class="cuadro_color">
									<td width="25%" class="cuadro_plano centrar">Registros Errores
									</td>

								</tr>	
									<?
	// definimos el directorio donde se guadan los archivos
	$path = $configuracion ['raiz_documento'] . "/bloque/admin_admitido/errores/";
	// $path = $configuracion['host'].$configuracion['site']."/bloque/admin_inscrito/errores/";
	$path2 = $configuracion ['host'] . $configuracion ['site'] . "/bloque/admin_admitido/errores/";
	// abrimos el directorio
	$dir = opendir ( $path );
	// guardamos los archivos en un arreglo
	$img_total = 0;
	$img_array = array ();
	while ( $elemento = readdir ( $dir ) ) {
		if (strlen ( $elemento ) > 3) {
			$img_array [$img_total] = $elemento;
		}
		
		$img_total ++;
	}
	$t_reg = count ( $img_array );
	
	foreach ( $img_array as $key => $value ) {
		?><tr>
									<td class="cuadro_plano centrar"><span class="texto_negrita"><a
											href="<?echo $path2.$img_array[$key];?>"><?echo $img_array[$key];?></a></td>
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

/**
 * **************************************************************
 * Funciones				*
 * ***************************************************************
 */
function generar_archivo_errores($configuracion, $nombre, $datos) {
	$shtml = "<table>
              <tr>
                 <td>Tipo documento</td>
                 <td>Numero documento</td>
                 <td>Nombre Inscrito</td>
                 <td>Proyecto Curricular</td>
                 <td>Errores</td>
              </tr>";
	
	foreach ( $datos as $key => $value ) {
		$shtml .= "
              <tr>
                 <td>" . $datos [$key] ['tipo_doc'] . "</td>
                 <td>" . $datos [$key] ['nro_doc'] . "</td>
                 <td>" . $datos [$key] ['nombre'] . "</td>
                 <td>" . $datos [$key] ['proyecto'] . "</td>
                 <td>" . $datos [$key] ['errores'] . "</td>
              </tr>";
	}
	
	$shtml .= "</table>";
	
	$carpeta = $configuracion ['raiz_documento'] . "/bloque/admin_admitido/errores/"; // carpeta donde guardar el archivo.
	                                                                                  // debe tener permisos 775 por lo menos
	$archivo = $carpeta . $nombre . ".xls"; // ruta del archivo a generar
	$fp = fopen ( $archivo, "w" );
	fwrite ( $fp, $shtml );
	fclose ( $fp );
}
function con_registro_admitido($configuracion, $registro, $campos, $tema, $acceso_db) {
	include_once ($configuracion ["raiz_documento"] . $configuracion ["clases"] . "/encriptar.class.php");
	$cripto = new encriptar ();
	$indice = $configuracion ["host"] . $configuracion ["site"] . "/index.php?";
	setlocale ( LC_MONETARY, 'en_US' );
	
	?><table width="100%" align="center" border="0" cellpadding="10"
	cellspacing="0">
	<tbody>
		<tr>
			<td>
				<table width="100%" border="0" align="center" cellpadding="5 px"
					cellspacing="1px">
					<tr class="centralcuerpo">
						<td>.::: Admitidos</td>
					</tr>
					<tr>
						<td>
							<table class="contenidotabla">
								<tr class="cuadro_color">
									<td class="cuadro_plano centrar">A&ntilde;o</td>
								</tr>	
					<?
	for($contador = 0; $contador < $campos; $contador ++) {
		// Anno
		$valor [0] = $registro [$contador] [0];
		?>
								<tr>
									<td class="cuadro_plano"><span class="texto_negrita"> <? echo $valor[0]?></span>
									</td>
									<td class="cuadro_plano"><span class="texto_negrita"> <? echo $valor[1]?> </span>
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
function estadistica($configuracion, $contador) {
	?>
<table style="text-align: left;" border="0" cellpadding="5"
	cellspacing="0" class="bloquelateral" width="100%">
	<tr>
		<td>
			<table cellpadding="10" cellspacing="0" align="center">
				<tr class="bloquecentralcuerpo">
					<td valign="middle" align="right" width="10%"><img
						src="<? echo $configuracion["host"].$configuracion["site"].$configuracion["grafico"]?>/info.png"
						border="0" /></td>
					<td align="left">Actualmente hay <b><? echo $contador ?> usuarios</b>
						registrados.
					</td>
				</tr>
				<tr class="bloquecentralcuerpo">
					<td align="right" colspan="2"><a
						href="<?
	echo $configuracion ["site"] . '/index.php?page=' . enlace ( 'admin_dir_dedicacion' ) . '&registro=' . $_REQUEST ['registro'] . '&accion=1&hoja=0&opcion=' . enlace ( "mostrar" ) . '&admin=' . enlace ( "lista" );
	
	?>">Ver m&aacute;s informaci&oacute;n >></a></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?
}

// Esta funcion sirve para definir la(s) clausula(s) SQL que se utilizan en este bloque
function cadena_busqueda_admitido($configuracion, $acceso_db, $valor, $opcion = "") {
	// $valor=$acceso_db->verificar_variables($valor);
	switch ($opcion) {
		
		// consulta para generar los datos para la tabla admitidos del SNIES a partir de la DB Academica
		case "admitido" :
			
			$prefijo = "mntac.";
			$cadena_sql = "SELECT UNIQUE ";
			$cadena_sql .= "asp_nro_iden , ";
			$cadena_sql .= "DECODE(asp_tip_doc,'',DECODE(length(asp_nro_iden),11,'TI',12,'TI','CC'),'C', 'CC', '1', 'CC', 'c', 'CC', 'T', 'TI', '2', 'TI', 't', 'TI', 'E', 'CE', 'P', 'PS', 'CC') tipo_ident_code, ";
			$cadena_sql .= "asp_nro_iden documento, ";
			$cadena_sql .= "(case when INSTR(trim(asp_apellido),' ',1,1)='0'
                                                         then '  '
                                                         else SUBSTR(trim(asp_apellido),instr(trim(asp_apellido),' ',1,1) +1,length(trim(asp_apellido)) - instr(trim(asp_apellido),' ',1,1))
                                                         end) segundo_apellido, ";
			$cadena_sql .= "as_cra_cod_snies pro_consecutivo, ";
			$cadena_sql .= "TO_CHAR(DECODE(asp_snp,'','N/A',NULL,'N/A',replace(asp_snp,' ',''))) snp,";
			$cadena_sql .= "'' fecha_snp, ";
			$cadena_sql .= "TO_CHAR('1301') ies_code, ";
			$cadena_sql .= "asp_ape_ano adm_annio, ";
			$cadena_sql .= "DECODE(asp_ape_per,1,'01',3,'02') adm_semestre, ";
			$cadena_sql .= "'11' departamento, ";
			$cadena_sql .= "'11001' municipio, ";
			$cadena_sql .= "(case when INSTR(trim(asp_nombre),' ',1,1)='0'
                                      then SUBSTR(trim(asp_nombre),instr(trim(asp_nombre),' ',1,1) +1,length(trim(asp_nombre)) - instr(trim(asp_nombre),' ',1,1))
                                      else SUBSTR(trim(asp_nombre),0,INSTR(trim(asp_nombre),' ',1,1))
                                      end) primer_nombre, ";
			$cadena_sql .= "(case when INSTR(trim(asp_nombre),' ',1,1)='0'
                                      then '  '
                                      else SUBSTR(trim(asp_nombre),instr(trim(asp_nombre),' ',1,1) +1,length(trim(asp_nombre)) - instr(trim(asp_nombre),' ',1,1))
                                      end) segundo_nombre, ";
			$cadena_sql .= "(case when INSTR(trim(asp_apellido),' ',1,1)='0'
                                      then SUBSTR(trim(asp_apellido),instr(trim(asp_apellido),' ',1,1) +1,length(trim(asp_apellido)) - instr(trim(asp_apellido),' ',1,1))
                                      else SUBSTR(trim(asp_apellido),0,INSTR(trim(asp_apellido),' ',1,1))
                                      end) primer_apellido, ";
			$cadena_sql .= "'1301' codigo_ent_aula, ";
			$cadena_sql .= "TO_CHAR(DECODE(asp_sexo,'M','01','F','02','01')) genero, ";
			$cadena_sql .= "as_cra_nom prog ";
			$cadena_sql .= "FROM " . $prefijo . "accra_snies ";
			$cadena_sql .= "INNER JOIN " . $prefijo . "accra ON cra_cod = as_cra_cod ";
			$cadena_sql .= "INNER JOIN " . $prefijo . "acasp ON cra_cod = asp_cra_cod ";
			$cadena_sql .= "INNER JOIN " . $prefijo . "actipcra ON cra_tip_cra = tra_cod ";
			$cadena_sql .= "WHERE  ";
			$cadena_sql .= "as_estado = 'A' ";
			$cadena_sql .= "AND asp_admitido = 'A' ";
			$cadena_sql .= "AND asp_snp is not null ";
			$cadena_sql .= "AND asp_ape_ano=" . $valor [0] . " ";
			$cadena_sql .= "AND asp_ape_per=" . $valor [1] . " "; // los periodos son '1' , '2' o '3'
			$cadena_sql .= "AND tra_nivel IN ('PREGRADO') ";
			
			$cadena_sql .= "UNION ";
			
			$cadena_sql .= "SELECT UNIQUE ";
			$cadena_sql .= "est_nro_iden  asp_nro_iden, ";
			$cadena_sql .= "DECODE(est_tipo_iden,'',DECODE(length(est_nro_iden),11,'TI',12,'TI','CC'),'C', 'CC', '1', 'CC', 'c', 'CC', 'T', 'TI', '2', 'TI', 't', 'TI', 'E', 'CE', 'P', 'PS', 'CC') tipo_ident_code, ";
			$cadena_sql .= "est_nro_iden  documento, ";
			$cadena_sql .= "(case when INSTR(trim(est_nombre),' ',1,4)>'0' AND INSTR(trim(est_nombre),' ',1,2)=INSTR(trim(est_nombre),' ',1,1)+1 
                              then trim(SUBSTR(trim(est_nombre),instr(trim(est_nombre),' ',1,1)+1,INSTR(trim(est_nombre),' ',1,3) - INSTR(trim(est_nombre),' ',1,2))) 
                              when INSTR(trim(est_nombre),' ',1,4)='0' AND INSTR(trim(est_nombre),' ',1,2)=INSTR(trim(est_nombre),' ',1,1)+1
                              then trim(SUBSTR(trim(est_nombre),instr(trim(est_nombre),' ',1,1)+1,length(trim(est_nombre)) - instr(trim(est_nombre),' ',1,3)))
                              else trim(SUBSTR(trim(est_nombre),INSTR(trim(est_nombre),' ',1,1) +1 ,INSTR(trim(est_nombre),' ',1,2) - INSTR(trim(est_nombre),' ',1,1)))
                              end) segundo_apellido, ";
			$cadena_sql .= "as_cra_cod_snies prog_consecutivo,  ";
			$cadena_sql .= "TO_CHAR(DECODE(eot_nro_snp,'','N/A',NULL,'N/A',replace(eot_nro_snp,' ',''))) snp,";
			$cadena_sql .= "'' fecha_snp, ";
			$cadena_sql .= "TO_CHAR('1301') ies_code, ";
			$cadena_sql .= "mat_ano adm_annio, ";
			$cadena_sql .= "DECODE(mat_per,1,'01',3,'02', mat_per) adm_semestre, ";
			$cadena_sql .= "'11' departamento, ";
			$cadena_sql .= "'11001' municipio, ";
			$cadena_sql .= "(case when INSTR(trim(est_nombre),' ',1,3)='0' AND INSTR(trim(est_nombre),' ',1,2)='0' 
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
			$cadena_sql .= "(case when INSTR(trim(est_nombre),' ',1,3)='0'
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
			$cadena_sql .= "SUBSTR(trim(est_nombre),0,INSTR(trim(est_nombre),' ',1,1)) primer_apellido, ";
			$cadena_sql .= "'1301' codigo_ent_aula, ";
			$cadena_sql .= "TO_CHAR(DECODE(est_sexo,'M','01','F','02','01')) genero, ";
			$cadena_sql .= "as_cra_nom prog ";
			$cadena_sql .= "FROM " . $prefijo . "acest ";
			$cadena_sql .= "INNER JOIN " . $prefijo . "acestotr ON est_cod = eot_cod  ";
			$cadena_sql .= "INNER JOIN " . $prefijo . "v_tot_matri_ape_per ON est_cod = mat_est_cod ";
			$cadena_sql .= "INNER JOIN " . $prefijo . "accra ON cra_cod = mat_cra_cod ";
			$cadena_sql .= "INNER JOIN " . $prefijo . "actipcra ON cra_tip_cra = tra_cod ";
			$cadena_sql .= "INNER JOIN " . $prefijo . "accra_snies ON as_cra_cod = mat_cra_cod ";
			$cadena_sql .= "WHERE  ";
			$cadena_sql .= "mat_ano=" . $valor [0] . " ";
			$cadena_sql .= "AND mat_per=" . $valor [1] . " ";
			$cadena_sql .= "AND SUBSTR(est_cod,0,4)=mat_ano ";
			$cadena_sql .= "AND SUBSTR(est_cod,5,1)=DECODE(mat_per,1,'1',3,'2',mat_per) ";
			$cadena_sql .= "AND tra_nivel IN ('DOCTORADO','MAESTRIA','POSGRADO') ";
			
			// echo $cadena_sql;
			// exit;
			
			break;
		
		// Consulta de la tabla admitido del SNIES LOCAL
		
		case "admitidoSnies" :
			$cadena_sql = "SELECT ";
			$cadena_sql .= "tipo_identif, "; // [0]
			$cadena_sql .= "documento, ";
			// $cadena_sql.="segundo_apellido,";
			$cadena_sql .= "pro_consecutivo,";
			// $cadena_sql.="snp, ";
			// $cadena_sql.="fecha_snp, "; //[5]
			// $cadena_sql.="ies_code, ";
			$cadena_sql .= "adm_annio, ";
			$cadena_sql .= "adm_semestre ";
			// $cadena_sql.="departamento, ";
			// $cadena_sql.="municipio, ";
			// $cadena_sql.="primer_nombre, ";
			// $cadena_sql.="segundo_nombre, ";
			// $cadena_sql.="primer_apellido, "; //[13]
			// $cadena_sql.="codigo_ent_aula, "; //[13]
			// $cadena_sql.="genero "; //[13]
			$cadena_sql .= "FROM admitido ";
			$cadena_sql .= "WHERE ";
			$cadena_sql .= "adm_annio='" . $valor [0] . "' ";
			if ($valor [1] == 2 || $valor [1] == 3) {
				$cadena_sql .= "AND adm_semestre='02' ";
			} else {
				$cadena_sql .= "AND adm_semestre='01' ";
			}
			$cadena_sql .= " AND ";
			$cadena_sql .= "documento='" . $valor [2] . "' ";
			$cadena_sql .= " AND ";
			$cadena_sql .= "pro_consecutivo='" . $valor [3] . "' ";
			
			// echo $cadena_sql;
			// exit;
			
			break;
		
		case "insertarAdmitido" :
			
			$cadena_sql = "INSERT INTO ";
			$cadena_sql .= "admitido ";
			$cadena_sql .= "VALUES ";
			$cadena_sql .= "(";
			$cadena_sql .= "'" . $valor [0] [1] . "', ";
			$cadena_sql .= "'" . $valor [0] [2] . "', ";
			$cadena_sql .= "replace('" . $valor [0] [3] . "','?','') ,";
			$cadena_sql .= "'" . $valor [0] [4] . "', ";
			$cadena_sql .= "'" . $valor [0] [5] . "', ";
			if ($valor [0] [6] != "") {
				$cadena_sql .= "'" . $valor [0] [6] . "', ";
			} else {
				$cadena_sql .= "null, ";
			}
			// $cadena_sql.="'".$valor[0][6]."', ";
			$cadena_sql .= "'" . $valor [0] [7] . "', ";
			$cadena_sql .= "'" . $valor [0] [8] . "', ";
			$cadena_sql .= "'" . $valor [0] [9] . "', ";
			$cadena_sql .= "'11', ";
			$cadena_sql .= "'11001', ";
			// Segun diccinario de datos del snies de admitidos el departamento y municipio es donde se dicta el programa
			// $cadena_sql.="'".$valor[0][10]."', ";
			// $cadena_sql.="'".$valor[0][11]."', ";
			$cadena_sql .= "replace('" . $valor [0] [14] . "','?','') ,";
			$cadena_sql .= "replace('" . $valor [0] [13] . "','?','') ,";
			$cadena_sql .= "replace('" . $valor [0] [14] . "','?','') ,";
			$cadena_sql .= "'" . $valor [0] [15] . "', ";
			$cadena_sql .= "'" . $valor [0] [16] . "' ";
			$cadena_sql .= ") ";
			
			// echo $cadena_sql."<br><br>";
			// exit;
			break;
		
		case "actualizarAdmitido" :
			
			$cadena_sql = "UPDATE ";
			$cadena_sql .= "admitido ";
			$cadena_sql .= "SET ";
			$cadena_sql .= "tipo_identif=";
			$cadena_sql .= "'" . $valor [0] [1] . "', ";
			$cadena_sql .= "documento=";
			$cadena_sql .= "'" . $valor [0] [2] . "', ";
			$cadena_sql .= "segundo_apellido=";
			$cadena_sql .= "replace('" . $valor [0] [3] . "','?','') ,";
			$cadena_sql .= "snp= ";
			$cadena_sql .= "'" . $valor [0] [5] . "', ";
			$cadena_sql .= "fecha_snp=";
			if ($valor [0] [6] != "") {
				$cadena_sql .= "'" . $valor [0] [6] . "', ";
			} else {
				$cadena_sql .= "null, ";
			}
			// $cadena_sql.="'".$valor[0][6]."', ";
			$cadena_sql .= "departamento=";
			$cadena_sql .= "'11', ";
			$cadena_sql .= "municipio=";
			$cadena_sql .= "'11001', ";
			// Segun diccinario de datos del snies de admitidos el departamento y municipio es donde se dicta el programa
			// $cadena_sql.="'".$valor[0][10]."', ";
			// $cadena_sql.="'".$valor[0][11]."', ";
			$cadena_sql .= "primer_nombre=";
			$cadena_sql .= "replace('" . $valor [0] [12] . "','?','') ,";
			$cadena_sql .= "segundo_nombre=";
			$cadena_sql .= "replace('" . $valor [0] [13] . "','?','') ,";
			$cadena_sql .= "primer_apellido=";
			$cadena_sql .= "replace('" . $valor [0] [14] . "','?','') ,";
			$cadena_sql .= "genero=";
			$cadena_sql .= "'" . $valor [0] [16] . "' ";
			$cadena_sql .= "WHERE ";
			$cadena_sql .= "tipo_identif=";
			$cadena_sql .= "'" . $valor [0] [1] . "' ";
			$cadena_sql .= "AND ";
			$cadena_sql .= "documento='" . $valor [0] [2] . "' ";
			$cadena_sql .= "AND ";
			$cadena_sql .= "adm_annio='" . $valor [0] [8] . "' ";
			$cadena_sql .= "AND ";
			$cadena_sql .= "adm_semestre='" . $valor [0] [9] . "' ";
			
			// echo $cadena_sql."<br><br>";
			// exit;
			break;
		
		// Con esta cadena se pueden obtener las consultas para subir manualmente algunos registros
		// echo "ano=".$valor[0][9]."<br>";
		
		// Consulta de la tabla admitido del SNIES LOCAL
		
		case "totalesAdmitidoSnies" :
			$cadena_sql = "Select ";
			$cadena_sql .= "adm_annio, ";
			$cadena_sql .= "adm_semestre, "; // [0]
			$cadena_sql .= "count (*)  ";
			$cadena_sql .= "from  admitido ";
			if ($valor != "") {
				$cadena_sql .= "where ";
				$cadena_sql .= "adm_annio=";
				$cadena_sql .= "'" . $valor [0] . "' ";
				$cadena_sql .= "AND adm_semestre=";
				if ($valor [1] == 2 || $valor [1] == 3) {
					$cadena_sql .= "'02' ";
				} else {
					$cadena_sql .= "'01' ";
				}
			}
			$cadena_sql .= "group by adm_semestre, adm_annio ";
			$cadena_sql .= "order by adm_annio DESC, adm_semestre DESC";
			
			// echo $cadena_sql;
			// exit;
			
			break;
		
		case "cubrimiento" :
			$cadena_sql = "Select ";
			$cadena_sql .= "count (*)  ";
			$cadena_sql .= "from  cubrimiento_programa ";
			$cadena_sql .= "WHERE ";
			$cadena_sql .= "annio='" . $valor [0] . "' ";
			$cadena_sql .= "AND semestre=";
			if ($valor [1] == 2 || $valor [1] == 3) {
				$cadena_sql .= "'02' ";
			} else {
				$cadena_sql .= "'01' ";
			}
			
			$cadena_sql .= "AND pro_consecutivo='" . $valor [2] . "' ";
			
			// echo $cadena_sql;
			// exit;
			
			break;
		
		case "insertarCubrimiento" :
			$cadena_sql = "INSERT INTO ";
			$cadena_sql .= "cubrimiento_programa ";
			$cadena_sql .= "VALUES ";
			$cadena_sql .= "(";
			$cadena_sql .= "'1301', ";
			$cadena_sql .= "'" . $valor [0] . "', ";
			if ($valor [1] == 2 || $valor [1] == 3) {
				$cadena_sql .= "'02', ";
			} else {
				$cadena_sql .= "'01', ";
			}
			$cadena_sql .= "'01', ";
			$cadena_sql .= "'11', ";
			$cadena_sql .= "'11001', ";
			$cadena_sql .= "'1301', ";
			$cadena_sql .= "'01', ";
			$cadena_sql .= "'" . $valor [2] . "'";
			$cadena_sql .= ") ";
			// echo $cadena_sql."<br><br>";
			// exit;
			break;
		
		case "lista_programa" :
			$cadena_sql = "Select ";
			$cadena_sql .= "count (*)  ";
			$cadena_sql .= "from  lista_programas ";
			$cadena_sql .= "WHERE ";
			$cadena_sql .= "lp_annio='" . $valor [0] . "' ";
			$cadena_sql .= "AND lp_semestre=";
			if ($valor [1] == 2 || $valor [1] == 3) {
				$cadena_sql .= "'02' ";
			} else {
				$cadena_sql .= "'01' ";
			}
			
			$cadena_sql .= "AND pro_consecutivo='" . $valor [2] . "' ";
			
			// echo $cadena_sql;
			// exit;
			
			break;
		
		case "insertarPrograma" :
			$cadena_sql = "INSERT INTO ";
			$cadena_sql .= "lista_programas ";
			$cadena_sql .= "VALUES ";
			$cadena_sql .= "(";
			$cadena_sql .= "'1301', ";
			$cadena_sql .= "'" . $valor [0] . "', ";
			if ($valor [1] == 2 || $valor [1] == 3) {
				$cadena_sql .= "'02', ";
			} else {
				$cadena_sql .= "'01', ";
			}
			$cadena_sql .= "'01', ";
			$cadena_sql .= "'01', ";
			$cadena_sql .= "'" . $valor [2] . "'";
			$cadena_sql .= ") ";
			
			// echo $cadena_sql."<br><br>";
			// exit;
			
			break;
		
		default :
			$cadena_sql = "";
			break;
	}
	// echo $cadena_sql."<br>";
	// exit;
	return $cadena_sql;
}
function ejecutar_admin_admitido($cadena_sql, $acceso_db, $tipo = "") {
	switch ($tipo) {
		case "" :
			$acceso_db->registro_db ( $cadena_sql, 0 );
			$registro = $acceso_db->obtener_registro_db ();
			// echo $registro[0][0]."<br>";
			return $registro;
		
		case "insertar" :
			return $acceso_db->ejecutar_acceso_db ( $cadena_sql );
		
		case "error" :
			return $acceso_db->obtener_error ();
	}
}

?>
