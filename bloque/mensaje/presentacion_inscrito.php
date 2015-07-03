<?/*
include_once($configuracion["raiz_documento"].$configuracion["clases"]."/html.class.php");

//Conectar con ORACLE
$acceso_db=new dbms($configuracion);
$enlace=$acceso_db->conectar_db('snies');

if (is_resource($enlace))
{
	//echo "Estamos en Oracle";
	echo $enlace;
	//echo $base;
	echo $base;
	//echo "casa";
	
	
	//$cadena_sql="DESC MNTAC.ACEST";
	//echo $acceso_db->dbsys;exit;
	//$acceso_db->registro_db($cadena_sql);
	//$registro=$acceso_db->obtener_registro_db();
	//foreach ($registro as $key => $val) {
    //  echo "<th>$key</th>\n";
   //}
	
	exit;
}


*/?>
<?include_once($configuracion["raiz_documento"].$configuracion["clases"]."/encriptar.class.php");
$cripto=new encriptar();
?>

<table width="100%" align="center" border="0" cellpadding="10" cellspacing="0" >
	<tbody>
		<tr>
			<td >
				<table border="0" cellpadding="5" cellspacing="0">
					<tbody>
						<tr class="bloquecentralcuerpo">
						<td>
						<table border="0" cellpadding="10" cellspacing="0" >
						<tbody>
						<tr class=texto_elegante >
						<td>
						<b>::::..</b>  SNIES Universidad Distrital - <span class="texto_negrita">INSCRITOS</span>
						<hr class=hr_subtitulo>
						</td>
						</tr>
						<tr class="bloquecentralcuerpo">
						<td>
						<p>El <span class="texto_negrita">Subsistema SNIES UD</span> del BackOffice CONDOR permite realizar la actualizaci&oacute;n de los Inscritos para cada per&iacute;odo.</p>
						<p>Para actualizar los registros de Inscritos en el SNIES:</p>
						<ul>
						<li>Seleccione el a&ntilde;o y per&iacute;odo que desea actualizar.
						<li>De Click en el boron actualizar.

							
						</ul>.
						</td>
						</tr>
						</tbody>
						</table>
						</td>
						</tr>											
					</tbody>
			</table>
		</td>
		</tr>
	</tbody>
</table>