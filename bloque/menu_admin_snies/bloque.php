<?
/*
 * ############################################################################
 * # UNIVERSIDAD DISTRITAL Francisco Jose de Caldas #
 * # Copyright: Vea el archivo EULA.txt que viene con la distribucion #
 * ############################################################################
 */
/**
 * *************************************************************************
 *
 * index.php
 *
 * Paulo Cesar Coronado
 * Copyright (C) 2001-2005
 *
 * ultima revision 04 de julio de 2008
 *
 * ****************************************************************************
 * ription Menu de administracion del SNIES
 * @usage
 * 
 * @subpackage
 *
 * @package bloques
 * @copyright
 *
 * @version 0.2
 * @author Paulo Cesar Coronado
 * @link N/D
 *       ***************************************************************************
 */
if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}

include_once ($configuracion ["raiz_documento"] . $configuracion ["clases"] . "/encriptar.class.php");

$indice = $configuracion ["host"] . $configuracion ["site"] . "/index.php?";
$cripto = new encriptar ();
?><table width="100%" align="center" border="0" cellpadding="10"
	cellspacing="0">
	<tbody>
		<tr>
			<td>
				<table align="center" border="0" cellpadding="5" cellspacing="0"
					class="bloquelateral_2" width="100%">
				<? 
/*
    * if(isset($_REQUEST["opcion"]) && $_REQUEST["opcion"]=="lista")
    * {
    * ?><tr class="centralcuerpo">
    * <td>
    * ::..
    * </td>
    * <td>
    * <a href="<?
    * $variable="pagina=registro_recibo";
    * $variable.="&accion=1";
    * $variable.="&hoja=1";
    * $variable.="&mostrar=nuevo";
    * $variable.="&xajax=datos_basicos";
    * $variable=$cripto->codificar_url($variable,$configuracion);
    * echo $indice.$variable;
    * ?>"> Agregar Solicitud</a>
    *
    * </td>
    * <td>
    * <a href="<?
    * $variable="pagina=registro_recibo_lote";
    * $variable.="&accion=1";
    * $variable.="&hoja=1";
    * $variable.="&mostrar=nuevo";
    * $variable=$cripto->codificar_url($variable,$configuracion);
    * echo $indice.$variable;
    * ?>"> Solicitud en Lote</a>
    *
    * </td>
    * <td>
    * <a href="<?
    * $variable="pagina=administar_recibo";
    * $variable.="&accion=1";
    * $variable.="&hoja=1";
    * $variable.="&mostrar=lista";
    * $variable=$cripto->codificar_url($variable,$configuracion);
    * echo $indice.$variable;
    * ?>"> Consolidados</a>
    *
    * </td>
    * </tr><?
    * }
    * else
    * {
    */
				?>
					<tr class="centralcuerpo">
						<td colspan="3"><b>::::..</b> Men&uacute;</td>
					</tr>

					<tr class="bloquelateralcuerpo">
						<td><a
							href="<?
							$variable = "pagina=administrar_inscrito"; // $variable="pagina=administrar_inscrito";
							$variable .= "&accion=1";
							$variable .= "&hoja=1";
							$variable .= "&mostrar=nuevo";
							$variable = $cripto->codificar_url ( $variable, $configuracion );
							$indice = $configuracion ["host"] . $configuracion ["site"] . "/index.php?";
							echo $indice . $variable;
							?>"> Inscritos</a></td>
					</tr>
					<tr class="bloquelateralcuerpo">
						<td><a
							href="<?
							$variable = "pagina=administrar_admitido";
							$variable .= "&accion=1";
							$variable .= "&hoja=1";
							$variable .= "&mostrar=nuevo";
							$variable = $cripto->codificar_url ( $variable, $configuracion );
							$indice = $configuracion ["host"] . $configuracion ["site"] . "/index.php?";
							echo $indice . $variable;
							?>"> Admitidos</a></td>
					</tr>
					<tr class="bloquelateralcuerpo">
						<td><a
							href="<?
							$variable = "pagina=administrar_primiparo";
							$variable .= "&accion=1";
							$variable .= "&hoja=1";
							$variable .= "&mostrar=nuevo";
							$variable = $cripto->codificar_url ( $variable, $configuracion );
							$indice = $configuracion ["host"] . $configuracion ["site"] . "/index.php?";
							echo $indice . $variable;
							?>"> Prim&iacute;paros</a>
							<hr class="hr_subtitulo"></td>
					</tr>

					<tr class="bloquelateralcuerpo">
						<td><a
							href="<?
							$variable = "pagina=administrar_matriculado";
							$variable .= "&accion=1";
							$variable .= "&hoja=1";
							$variable .= "&mostrar=nuevo";
							$variable = $cripto->codificar_url ( $variable, $configuracion );
							echo $indice . $variable;
							?>"> Matriculados</a>
							<hr class="hr_subtitulo"></td>
					</tr>


					<tr class="bloquelateralcuerpo">
						<td><a
							href="<?
							$variable = "pagina=administrar_egresado";
							$variable .= "&accion=1";
							$variable .= "&hoja=1";
							$variable .= "&mostrar=lista";
							$variable = $cripto->codificar_url ( $variable, $configuracion );
							echo $indice . $variable;
							?>"> Egresados(T. Materias)</a>
							<hr class="hr_subtitulo"></td>
					</tr
					
					
					<tr class="bloquelateralcuerpo">
						<td><a
							href="<?
							$variable = "pagina=administrar_graduado";
							$variable .= "&accion=1";
							$variable .= "&hoja=1";
							$variable .= "&mostrar=lista";
							$variable = $cripto->codificar_url ( $variable, $configuracion );
							echo $indice . $variable;
							?>"> Graduados</a>
							<hr class="hr_subtitulo"></td>
					</tr>
					<tr class="bloquelateralcuerpo">
						<td><a
							href="<?
							$variable = "pagina=administrar_docente";
							$variable .= "&accion=1";
							$variable .= "&hoja=1";
							$variable .= "&mostrar=lista";
							$variable = $cripto->codificar_url ( $variable, $configuracion );
							echo $indice . $variable;
							?>"> Docentes</a>
							<hr class="hr_subtitulo"></td>
					</tr>
					</tr>
					<tr class="bloquelateralcuerpo">
						<td><a
							href="<?
							$variable = "pagina=calcular_matricula";
							$variable .= "&accion=1";
							$variable .= "&hoja=1";
							$variable .= "&mostrar=lista";
							$variable = $cripto->codificar_url ( $variable, $configuracion );
							echo $indice . $variable;
							?>">Valor Matriculas</a>
							<hr class="hr_subtitulo"></td>
					</tr>
					</tr>
					<tr class="centralcuerpo">
						<td colspan="3"><b>::::..</b> Enlaces</td>
					</tr>

					<tr class="bloquelateralcuerpo">
						<td><a href="http://snies.mineducacion.gov.co/firmas"> Snies</a>
							<hr class="hr_subtitulo"></td>
					</tr>

					<tr class="bloquelateralcuerpo">
						<td><a
							href="http://www.elementool.com/Services/Common/quickview.aspx?usrname=IES_1301&accntname=DWSOPMEN&issueno=423">
								Mesa de Ayuda</a>
							<hr class="hr_subtitulo"></td>
					</tr>
					<tr class="centralcuerpo">
						<td colspan="3"><b>::::..</b>Calidad Datos</td>
					</tr>
					<tr class="bloquelateralcuerpo">
						<td><a
							href="<?
							$variable = "pagina=admin_calidadDatosinscrito"; // $variable="pagina=administrar_inscrito";
							$variable .= "&accion=1";
							$variable .= "&hoja=1";
							$variable .= "&mostrar=nuevo";
							$variable = $cripto->codificar_url ( $variable, $configuracion );
							$indice = $configuracion ["host"] . $configuracion ["site"] . "/index.php?";
							echo $indice . $variable;
							?>"> Calidad de datos Nombres y apellidos Inscritos</a></td>
					</tr>
							
							<? 
/*
							    * <tr class="bloquelateralcuerpo">
							    * <td>
							    * <a href="<?
							    * $variable="pagina=administrar_administrativo";
							    * $variable.="&accion=1";
							    * $variable.="&hoja=1";
							    * $variable.="&mostrar=nuevo";
							    * $variable=$cripto->codificar_url($variable,$configuracion);
							    * $indice=$configuracion["host"].$configuracion["site"]."/index.php?";
							    * echo $indice.$variable;
							    * ?>"> Administrativos</a>
							    * <hr class="hr_subtitulo">
							    * </td>
							    * </tr>
							    */
							?>
					
					
					
					<?
					?></table>
			</td>
		</tr>
	</tbody>
</table>