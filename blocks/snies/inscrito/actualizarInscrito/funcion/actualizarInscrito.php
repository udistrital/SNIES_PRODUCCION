<?php
include_once ('component/GestorInscritoAdmitido/Componente.php');
include_once ('blocks/snies/funcion/procesadorNombre.class.php');
include_once ('blocks/snies/funcion/procesadorExcepcion.class.php');

use sniesInscritoAdmitido\Componente;
use bloqueSnies\procesadorExcepcion;
use bloqueSnies\procesadorNombre;
class FormProcessor {
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miSql;
	var $conexion;
	function __construct($lenguaje, $sql) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->lenguaje = $lenguaje;
		$this->miSql = $sql;
		$this->miComponente = new Componente ();
		$this->host = $this->miConfigurador->getVariableConfiguracion ( "host" );
		$this->site = $this->miConfigurador->getVariableConfiguracion ( "site" );
		$this->esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
	}
	/**
	 * Esta función realiza las siguientes acciones
	 * 1.consulta en la académica inscritos pregrado
	 * 2.consulta en la académica inscritos postgrado
	 * 4.Borrar los registros para el año y periodo seleccionado en SNIES LOCAL tabla inscrito
	 * 2.Procesar los datos obtenidos, cambiar acentos.
	 * 3.Registrar errores de la fuente para reportarlos
	 * 5.Insertar los registros en el SNIES LOCAL
	 * 6.Redireccionar a lista de variables
	 */
	function procesarFormulario() {
		$annio = $_REQUEST ['annio'];
		$semestre = $_REQUEST ['semestre'];
		
		/**
		 * Asegure de que todos los programas estén registrados en la tabla accra_snies
		 * para buscar los que no están registrados utilice la consulta
		 * SELECT * FROM ACCRA WHERE CRA_COD NOT IN (SELECT AS_CRA_COD FROM ACCRA_SNIES  )AND CRA_ESTADO='A'order by cra_cod asc
		 */
		
		// CONSULTAS ACADEMICA
		$inscritosPregrado = $this->miComponente->consultarInscritoPregadoAcademica ( $annio, $semestre );
		$inscritosPostgrado = $this->miComponente->consultarInscritoPostgradoAcademica ( $annio, $semestre );
		
		// Si no realiza la consulta retorna a la pagina inicial
		if ($inscritosPregrado == false or $inscritosPostgrado == false) {
			$valorCodificado = "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
			$valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );
			
			// Rescatar el parámetro enlace desde los datos de configuraión en la base de datos
			$variable = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
			$miEnlace = $this->host . $this->site . '/index.php?' . $variable . '=' . $valorCodificado;
			
			header ( "Location:$miEnlace" );
		}
		// LIMPIAR LOS REGISTROS DEL AÑO Y SEMESTRE ESPECIFICADO
		$borrarInscritos = $this->miComponente->borrarInscritoSnies ( $annio, $semestre );
		
		// PARTE DE INSCRITOS DE PREGRADO
		
		$miProcesadorNombre = new procesadorNombre ();
		
		$inscritosPregrado = $miProcesadorNombre->quitarAcento ( $inscritosPregrado, 'APELLIDO' );
		$inscritosPregrado = $miProcesadorNombre->quitarAcento ( $inscritosPregrado, 'NOMBRE' );
		$inscritosPregrado = $miProcesadorNombre->quitarAcento ( $inscritosPregrado, 'PROG' );
		
		// descompone nombre y apellidos en sus partes y las agrega al final de cada registro
		foreach ( $inscritosPregrado as $clave => $valor ) {
			// echo $inscritosPregrado [$clave] ['DOCUMENTO'] . '<br>';
			
			// divide los apellidos compuestos en primer apellido y segundo apellido
			$apellido = $miProcesadorNombre->dividirApellidos ( $inscritosPregrado [$clave] ['APELLIDO'] );
			$inscritosPregrado [$clave] ['PRIMER_APELLIDO'] = $apellido ['primer_apellido'];
			$inscritosPregrado [$clave] ['SEGUNDO_APELLIDO'] = $apellido ['segundo_apellido'];
			
			// divide los nombres compuestos en primer nombre y segundo nombre
			$nombre = $miProcesadorNombre->dividirNombres ( $inscritosPregrado [$clave] ['NOMBRE'] );
			
			$inscritosPregrado [$clave] ['PRIMER_NOMBRE'] = $nombre ['primer_nombre'];
			$inscritosPregrado [$clave] ['SEGUNDO_NOMBRE'] = $nombre ['segundo_nombre'];
		}
		
		// Inserta uno a uno los registros de inscritos consultados en la académica
		foreach ( $inscritosPregrado as $inscrito ) {
			$insertarInscrito = $this->miComponente->insertarInscritoSnies ( $inscrito );
		}
		
		// PARTE DE INSCRITOS DE POSTGRADO
		/**
		 * Esta función realiza las siguientes acciones
		 * 1.consulta en la académica
		 * 2.Procesar los datos obtenidos, cambiar acentos.
		 * 3.Registrar errores de la fuente para reportarlos
		 * 4.Borrar los registros para el año y periodo seleccionado en SNIES LOCAL
		 * 5.Insertar los registros en el SNIES LOCAL
		 * 6.Redireccionar a lista de variables
		 */
		
		$miProcesadorNombre = new procesadorNombre ();
		
		$inscritosPostgrado = $miProcesadorNombre->quitarAcento ( $inscritosPostgrado, 'NOMBRE' );
		$inscritosPostgrado = $miProcesadorNombre->quitarAcento ( $inscritosPostgrado, 'PROG' );
		
		// descompone nombre completo en sus partes y las agrega al final de cada registro
		foreach ( $inscritosPostgrado as $clave => $valor ) {
			// echo $inscritosPostgrado [$clave] ['DOCUMENTO'] . '<br>';
			
			// divide los apellidos compuestos en primer apellido y segundo apellido
			$nombreCompleto = $miProcesadorNombre->dividirNombreCompleto ( $inscritosPostgrado [$clave] ['NOMBRE'] );
			$inscritosPostgrado [$clave] ['PRIMER_APELLIDO'] = $nombreCompleto ['primer_apellido'];
			$inscritosPostgrado [$clave] ['SEGUNDO_APELLIDO'] = $nombreCompleto ['segundo_apellido'];
			$inscritosPostgrado [$clave] ['PRIMER_NOMBRE'] = $nombreCompleto ['primer_nombre'];
			$inscritosPostgrado [$clave] ['SEGUNDO_NOMBRE'] = $nombreCompleto ['segundo_nombre'];
		}
		
		// Inserta uno a uno los registros de inscritos consultados en la académica
		foreach ( $inscritosPostgrado as $inscrito ) {
			$insertarInscrito = $this->miComponente->insertarInscritoSnies ( $inscrito );
		}
		
		echo 'Proceso finalizado';
		
	}
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );

$resultado = $miProcesador->procesarFormulario ();

