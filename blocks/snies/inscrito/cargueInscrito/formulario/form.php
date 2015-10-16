<?php
require_once ('component/GestorInscritoAdmitido/Componente.php');
require_once ('component/GestorEstudiante/Componente.php');

use snies\Componente as InscritoAdmitido;
use sniesEstudiante\Componente;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}
class Formulario {
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $annio;
	var $semestre;
	function __construct($lenguaje, $formulario) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->lenguaje = $lenguaje;
		
		$this->miFormulario = $formulario;
		
		$this->miComponente = new InscritoAdmitido ();
		
		$this->host = $this->miConfigurador->getVariableConfiguracion ( "host" );
		
		$this->site = $this->miConfigurador->getVariableConfiguracion ( "site" );
		
		$this->esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
		$this->urlImagenes = $this->miConfigurador->getVariableConfiguracion ( "rutaUrlBloque" );
	}
	function formulario() {
		
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
		$anoInicial=$annioActual-4;//presente solamente los últimos 5 años
		for($ano = $anoInicial; $ano <= $annioActual; $ano ++) {
			$periodo [$a] ['annio'] = $ano;
			$periodo [$a] ['semestre'] = '01';
			$periodo [$a] ['total'] = $this->miComponente->contarInscritos ( $ano, '01' );
			$a ++;
			
			if ($annioActual == $ano and $semestreActual == 1) {
			} else {
				$periodo [$a] ['annio'] = $ano;
				$periodo [$a] ['semestre'] = '02';
				$periodo [$a] ['total'] = $this->miComponente->contarInscritos ( $ano, '02' );
				$a ++;
			}
		}
		
		?>

<br>
<h3>INSCRITOS - ACTUALIZACIÓN SNIES CENTRAL</h3>
<br>
<table id="example" class="display" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th>Variable</th>
			<th>Año</th>
			<th>Período</th>
			<th>Total</th>
			<th>Errores</th>
			<th>Actualizar</th>
			<th>Última Actualización</th>
		</tr>
	</thead>

	<tbody>
	<?php
		foreach ( $periodo as $miPeriodo ) {
			?>
		<tr>
			<td><?php echo 'Inscrito';?></td>
			<td align="center"><?php echo $miPeriodo['annio']?></td>
			<td align="center"><?php echo $miPeriodo['semestre'];?></td>
			<td align="right"><?php echo $miPeriodo['total'];?></td>
			<td align="right">-</td>
			<td align="center"><a class=miEnlace
				href="<?php echo 'enlace';?>"><img
					src='<? echo $this->urlImagenes?>images/actualizar.png'
					width='30px'></a></td>
			<td align="center">-</td>
		</tr>
		<?php }?>
	</tbody>

</table>



<?
	}
	function mensaje() {
		
		// Si existe algun tipo de error en el login aparece el siguiente mensaje
		$mensaje = $this->miConfigurador->getVariableConfiguracion ( 'mostrarMensaje' );
		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', null );
		
		if ($mensaje) {
			
			$tipoMensaje = $this->miConfigurador->getVariableConfiguracion ( 'tipoMensaje' );
			
			if ($tipoMensaje == 'json') {
				
				$atributos ['mensaje'] = $mensaje;
				$atributos ['json'] = true;
			} else {
				$atributos ['mensaje'] = $this->lenguaje->getCadena ( $mensaje );
			}
			// -------------Control texto-----------------------
			$esteCampo = 'divMensaje';
			$atributos ['id'] = $esteCampo;
			$atributos ["tamanno"] = '';
			$atributos ["estilo"] = 'information';
			$atributos ['efecto'] = 'desvanecer';
			$atributos ["etiqueta"] = '';
			$atributos ["columnas"] = ''; // El control ocupa 47% del tamaño del formulario
			echo $this->miFormulario->campoMensaje ( $atributos );
			unset ( $atributos );
		}
		
		return true;
	}
	function enlaceActializarVariable($opcion) {
		$valorCodificado = "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
		$valorCodificado .= "&action=" . $this->esteBloque ["nombre"];
		$valorCodificado .= '&bloqueGrupo=' . $this->esteBloque ['grupo'];
		$valorCodificado .= "&opcion=" . $opcion;
		$valorCodificado .= "&annio=" . $this->annio;
		$valorCodificado .= "&semestre=" . $this->semestre;
		$valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );
		
		// Rescatar el parámetro enlace desde los datos de configuraión en la base de datos
		$variable = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
		$miEnlace = $this->host . $this->site . '/index.php?' . $variable . '=' . $valorCodificado;
		return $miEnlace;
	}
}

$miFormulario = new Formulario ( $this->lenguaje, $this->miFormulario );

$miFormulario->formulario ();
$miFormulario->mensaje ();

?>