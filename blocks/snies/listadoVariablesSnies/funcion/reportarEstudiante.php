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
		 * 1.consulta estudiante de la académica
		 * 3.Registrar errores de la fuente para reportarlos
		 * 4.Borrar los registros consultados en estudainte SNIES LOCAL
		 * 5.Insertar los registros en el SNIES LOCAL
		 * 6.Redireccionar a lista de variables, es decir el fomulario de inicio
		 */
		
		/**
		 * LA TABLA PARTICIPANTE DEL SNIES LOCAL CONTIENE LOS DATOS BÁSICOS DE LOS ACTORES
		 * DE LA UNIVERSIDAD, SIN TENER EN CUENTA EL AÑO O PERIODO,
		 * EN ESTA CLASE SE ACTUALIZAR LOS ESTUDIANTES ACTIVOS PARA EL PRESENTE PERÍODO
		 */
		
		$estudianteAcademica = $this->miComponente->consultarEstudiante ();
		
		foreach ( $estudianteAcademica as $estudiante ) {
			// echo $estudiante['CODIGO_UNICO']." ".$estudiante['TIPO_DOC_UNICO'].'<br>';
			$resultado = $this->miComponente->consultarEstudianteSNIES ($estudiante['CODIGO_UNICO']);

		}
		exit ();
		// $estudianteSNIES = $this->miComponente->consultarEstudianteSnies ();
		
		var_dump ( $estudianteSNIES );
		
		exit ();
		
		// en el caso de que no se haga la consulta redirecciona
		if ($estudiante == false) {
			$valorCodificado = "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
			$valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );
			
			// Rescatar el parámetro enlace desde los datos de configuraión en la base de datos
			$variable = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
			$miEnlace = $this->host . $this->site . '/index.php?' . $variable . '=' . $valorCodificado;
			
			header ( "Location:$miEnlace" );
		}
		
		// insertar si no existe o actualizar si existe y es diferente
		$estudianteSNIES = 

		exit ();
		
		// borra todos los registros de la tabla estudiante del SNIES LOCAL
		$borrarEstudiantes = $this->miComponente->borrarEstudiante ();
		exit ();
		
		$valorCodificado = "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
		$valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );
		
		// Rescatar el parámetro enlace desde los datos de configuraión en la base de datos
		$variable = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
		$miEnlace = $this->host . $this->site . '/index.php?' . $variable . '=' . $valorCodificado;
		
		header ( "Location:$miEnlace" );
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );

$resultado = $miProcesador->procesarFormulario ();

