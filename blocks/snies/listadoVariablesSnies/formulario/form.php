<?php
include_once ('component/GestorSniesLocal/Componente.php');
use snies\Componente;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}
class Formulario {
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	function __construct($lenguaje, $formulario) {
		$this->miConfigurador = \Configurador::singleton ();
		
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		
		$this->lenguaje = $lenguaje;
		
		$this->miFormulario = $formulario;
		
		$this->miComponente = new Componente ();
		
		$this->host = $this->miConfigurador->getVariableConfiguracion ( "host" );
		
		$this->site = $this->miConfigurador->getVariableConfiguracion ( "site" );
		
		$this->esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
		$this->urlImagenes = $this->miConfigurador->getVariableConfiguracion ( "rutaUrlBloque" );
	}
	function formulario() {
		
		// Obtener año y período actual basado en la fecha del sistema
		$fecha = getdate ();
		$annio = $fecha ['year'];
		if ($fecha ['mon'] <= 6) {
			$semestre = '01';
		} else {
			$semestre = '02';
		}
		
		// consultar la variable inscritos de la base de datos del SNIES LOCAL (postgres)
		
		$periodo ['annio'] = $annio;
		$periodo ['semestre'] = $semestre;
		
		$totalInscritos = 0;
		$totalAdmitidos = 0;
		$totalMatriculadosPrimerCurso = 0;
		$totalMatriculados = 0;
		
		$totalInscritos = $this->miComponente->contarInscritos ( $periodo );
		$totalAdmitidos = $this->miComponente->contarAdmitidos ( $periodo );
		$totalMatriculadosPrimerCurso = $this->miComponente->contarMatriculadosPrimerCurso ( $periodo );
		$totalMatriculados = $this->miComponente->contarMatriculados ( $periodo );
		// $inscritoAcademica=$this->miComponente->consultarInscritoAcademica($periodo);
		
		$variables = array (
				'0' => array (
						'nombre' => '1. Inscrito',
						'total' => $totalInscritos,
						'enlace' => $this->enlaceActializarVariable ('') 
				),
				'1' => array (
						'nombre' => '2. Admitido',
						'total' => $totalAdmitidos,
						'enlace' => $this->enlaceActializarVariable ('') 
				),
				'2' => array (
						'nombre' => '3. participante',
						'total' => $totalMatriculadosPrimerCurso,
						'enlace' => $this->enlaceActializarVariable ('actualizarParticipante') 
				),
				'3' => array (
						'nombre' => '4. Estudiante',
						'total' => $totalMatriculadosPrimerCurso,
						'enlace' => $this->enlaceActializarVariable ('') 
				),
				'4' => array (
						'nombre' => '5. Matrícula Primer Curso',
						'total' => $totalMatriculadosPrimerCurso,
						'enlace' => $this->enlaceActializarVariable ('') 
				),
				'5' => array (
						'nombre' => '6. Matrículado',
						'total' => $totalMatriculados,
						'enlace' => $this->enlaceActializarVariable ('') 
				),
				'6' => array (
						'nombre' => '7. Egresado',
						'total' => $totalMatriculadosPrimerCurso,
						'enlace' => $this->enlaceActializarVariable ('') 
				),
				'7' => array (
						'nombre' => '8. Graduado',
						'total' => $totalMatriculadosPrimerCurso,
						'enlace' => $this->enlaceActializarVariable ('') 
				) 
		);
		
		?>


<table id="example" class="display" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th>variable</th>
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
		foreach ( $variables as $valor ) {
			?>
		<tr>
			<td><?php echo $valor['nombre'];?></td>
			<td align="center"><?php echo $annio?></td>
			<td align="center"><?php echo $semestre?></td>
			<td align="right"><?php echo $valor['total'];?></td>
			<td align="right">-</td>
			<td align="center"><a href="<?php echo $valor['enlace']?>"><img
					src='<? echo $this->urlImagenes?>images/actualizar.png' width='30px'></a></td>
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
	function enlaceActializarVariable($pagina) {
		$valorCodificado = "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
		$valorCodificado .= "&action=" . $this->esteBloque ["nombre"];
		$valorCodificado .= '&bloqueGrupo='.$this->esteBloque ['grupo'];
		$valorCodificado .= "&opcion=actualizarParticipante"; // va a frontera
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