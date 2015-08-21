<?php
include_once ('component/GestorEstudiante/Componente.php');
include_once ('blocks/snies/listadoVariablesSnies/funcion/procesadorNombre.class.php');
use sniesEstudiante\Componente;
use bloqueSnies\procesadorNombre;
class FormProcessor {
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miSql;
	var $conexion;
	function __construct($lenguaje, $sql) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->lenguaje = $lenguaje;
		$this->miSql = $sql;
		$this->miComponente = new Componente ();
		$this->host = $this->miConfigurador->getVariableConfiguracion ( "host" );
		$this->site = $this->miConfigurador->getVariableConfiguracion ( "site" );
		$this->esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
	}
	function procesarFormulario() {	
		
		$annio = $_REQUEST ['annio'];
		$semestre = $_REQUEST ['semestre'];
		
		/**
		 * Esta función realiza las siguientes acciones
		 * 1.consulta estudiante participante de la académica
		 * 2.Procesar los datos obtenidos, cambiar acentos.
		 * 3.Registrar errores de la fuente para reportarlos
		 * 4.Borrar los registros consultados en la tabla PARTICIPANTE del SNIES LOCAL
		 * 5.Insertar los registros en el SNIES LOCAL
		 * 6.Redireccionar a lista de variables, es decir el fomulario de inicio
		 */
		
		/**
		 * LA TABLA PARTICIPANTE DEL SNIES LOCAL CONTIENE LOS DATOS BÁSICOS DE LOS ACTORES
		 * DE LA UNIVERSIDAD, SIN TENER EN CUENTA EL AÑO O PERIODO,
		 * EN ESTA CLASE SE ACTUALIZAR LOS ESTUDIANTES ACTIVOS PARA EL PRESENTE PERÍODO
		 */
		
		$estudiante= $this->miComponente->consultarParticipanteEstudiante ( $annio, $semestre );
		echo count($estudiante);
		
		exit;
		
		if ($inscritosPregrado==false) {
			$valorCodificado = "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
			$valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );
			
			// Rescatar el parámetro enlace desde los datos de configuraión en la base de datos
			$variable = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
			$miEnlace = $this->host . $this->site . '/index.php?' . $variable . '=' . $valorCodificado;
			
			header ("Location:$miEnlace");
		}
		
		$miProcesadorNombre = new procesadorNombre ();
				
		$inscritosPregrado=$miProcesadorNombre->quitarAcento($inscritosPregrado, 'APELLIDO');
		$inscritosPregrado=$miProcesadorNombre->quitarAcento($inscritosPregrado, 'NOMBRE');
		$inscritosPregrado=$miProcesadorNombre->quitarAcento($inscritosPregrado, 'PROG');
		
		
		var_dump($inscritosPregrado);
		
		//PARTE DE INSCRITOS DE POSTGRADO
		
		
		exit;
		
		$borrarInscritos = $this->miComponente->borrarInscritoSnies ( $annio, $semestre );
		
		foreach ( $inscritosPregrado as $inscrito ) {
			
			$insertarInscrito = $this->miComponente->insertarInscritoSnies ($inscrito);
						
		}
		
		$valorCodificado = "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
		$valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );
		
		// Rescatar el parámetro enlace desde los datos de configuraión en la base de datos
		$variable = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
		$miEnlace = $this->host . $this->site . '/index.php?' . $variable . '=' . $valorCodificado;
		
		header ("Location:$miEnlace");
	
		
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );

$resultado = $miProcesador->procesarFormulario ();

