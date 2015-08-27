<?php
include_once ('component/GestorInscritoAdmitido/Componente.php');
include_once ('blocks/snies/listadoVariablesSnies/funcion/procesadorNombre.class.php');
use snies\Componente;
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
		 * 1.consulta en la académica
		 * 2.Procesar los datos obtenidos, cambiar acentos.
		 * 3.Registrar errores de la fuente para reportarlos
		 * 4.Borrar los registros para el año y periodo seleccionado en SNIES LOCAL
		 * 5.Insertar los registros en el SNIES LOCAL
		 * 6.Redireccionar a lista de variables
		 */
		
		//PARTE DE INSCRITOS DE PREGRADO
		$inscritosPregrado = $this->miComponente->consultarInscritoPregadoAcademica ( $annio, $semestre );

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
		
		// descompone nombre completo en sus partes y las agrega al final de cada registro
		foreach ( $inscritosPregrado as $clave => $valor ) {
			// echo $estudiante [$clave] ['CODIGO_UNICO'].'<br>';
			$nombreCompleto = $miProcesadorNombre->dividirNombre ( $inscritosPregrado [$clave] ['EST_NOMBRE'] );
			$inscritosPregrado [$clave] ['PRIMER_APELLIDO'] = $nombreCompleto ['primer_apellido'];
			$inscritosPregrado [$clave] ['SEGUNDO_APELLIDO'] = $nombreCompleto ['segundo_apellido'];
			$inscritosPregrado [$clave] ['PRIMER_NOMBRE'] = $nombreCompleto ['primer_nombre'];
			$inscritosPregrado [$clave] ['SEGUNDO_NOMBRE'] = $nombreCompleto ['segundo_nombre'];
		}
		
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

