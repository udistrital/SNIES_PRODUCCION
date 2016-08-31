<?php

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}

include_once ("core/builder/InspectorHTML.class.php");
class registrarForm {
	var $miConfigurador;
	var $miInspectorHTML;
	var $lenguaje;
	var $miFormulario;
	var $miSql;
	function __construct($lenguaje, $formulario, $sql) {
		$this->miConfigurador = \Configurador::singleton ();

		$this->miInspectorHTML = \InspectorHTML::singleton ();

		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );

		$this->lenguaje = $lenguaje;

		$this->miFormulario = $formulario;

		$this->miSql = $sql;

		$this->urlImagenes = $this->miConfigurador->getVariableConfiguracion ( "rutaUrlBloque" );

		$this->host = $this->miConfigurador->getVariableConfiguracion ( "host" );

		$this->site = $this->miConfigurador->getVariableConfiguracion ( "site" );

		$this->esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
	}
	function miForm() {

		// Obtener año y período actual basado en la fecha del sistema
		$fecha = getdate ();
		$annioActual = $fecha ['year'];
		if ($fecha ['mon'] <= 6) {
			$semestreActual = '01';
		} else {
			$semestreActual = '02';
		}

		// crea un arreglo con todos los años y semestres desde 2000-1 hasta el presente semestre
		// contar la cantidad de registro para cada periodo(año, semestre)
		$a = 0;
		$anoInicial = $annioActual - 4; // presente solamente los últimos 5 años
		for($ano = $anoInicial; $ano <= $annioActual; $ano ++) {
			$periodo [$a] ['annio'] = $ano;
			$periodo [$a] ['semestre'] = '01';
			// $periodo [$a] ['total'] = $this->miComponente->contarMatriculados ( $ano, '01' );
			$a ++;

			if ($annioActual == $ano and $semestreActual == 1) {
			} else {
				$periodo [$a] ['annio'] = $ano;
				$periodo [$a] ['semestre'] = '02';
				// $periodo [$a] ['total'] = $this->miComponente->contarMatriculados ( $ano, '02' );
				$a ++;
			}
		}

		?>

<br>
<h3>ADMITIDO - GENERACIÓN DE PLANTILLA CSV</h3>
<br>
<table id="example" class="display" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th>Variable</th>
			<th>Año</th>
			<th>Período</th>
			<th>Generar CSV</th>
		</tr>
	</thead>

	<tbody>
			<?php

		foreach ( $periodo as $miPeriodo ) {
			$enlaceActualizarAdmitido = $this->enlaceActualizarVariable ( 'generarPlantillaAdmitido', $miPeriodo ['annio'], $miPeriodo ['semestre'] );
			?>
				<tr>
			<td><?php echo 'Admitido';?></td>
			<td align="center"><?php echo $miPeriodo['annio']?></td>
			<td align="center"><?php echo $miPeriodo['semestre'];?></td>
			<td align="center"><a class=miEnlace
				href="<?php echo $enlaceActualizarAdmitido;?>"><img
					src='<? echo $this->urlImagenes?>images/actualizar.png'
					width='30px'></a></td>
		</tr>
				<?php }?>
			</tbody>

</table>



<?
	}
	function enlaceActualizarVariable($opcion, $annio, $semestre) {
		$valorCodificado = "actionBloque=" . $this->esteBloque ["nombre"];
		$valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
		$valorCodificado .= "&bloque=" . $this->esteBloque ['nombre'];
		$valorCodificado .= "&bloqueGrupo=" . $this->esteBloque ["grupo"];
		$valorCodificado .= "&opcion=" . $opcion;
		$valorCodificado .= "&annio=" . $annio;
		$valorCodificado .= "&semestre=" . $semestre;
		$valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );

		// Rescatar el parámetro enlace desde los datos de configuraión en la base de datos
		$variable = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
		$miEnlace = $this->host . $this->site . '/index.php?' . $variable . '=' . $valorCodificado;
		return $miEnlace;
	}
}

$miSeleccionador = new registrarForm ( $this->lenguaje, $this->miFormulario, $this->sql );

$miSeleccionador->miForm ();
?>
