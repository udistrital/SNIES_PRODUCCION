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
		$this -> miConfigurador = \Configurador::singleton();
		$this -> lenguaje = $lenguaje;
		$this -> miSql = $sql;
		$this -> miComponente = new Componente();
		$this -> host = $this -> miConfigurador -> getVariableConfiguracion("host");
		$this -> site = $this -> miConfigurador -> getVariableConfiguracion("site");
		$this -> esteBloque = $this -> miConfigurador -> getVariableConfiguracion("esteBloque");
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
		$annio = $_REQUEST['annio'];
		$semestre = $_REQUEST['semestre'];

		/**
		 * Asegure de que todos los programas estén registrados en la tabla accra_snies
		 * para buscar los que no están registrados utilice la consulta
		 * SELECT * FROM ACCRA WHERE CRA_COD NOT IN (SELECT AS_CRA_COD FROM ACCRA_SNIES  )AND CRA_ESTADO='A'order by cra_cod asc
		 */

		/**
		 * Consultar admitidos pregrado y prostgrado
		 */
		$admitidosPregrado = $this -> miComponente -> consultarAdmitidoPregradoAcademica($annio, $semestre);
		$admitidosPostgrado = $this -> miComponente -> consultarAdmitidoPostgradoAcademica($annio, $semestre);

		// borra admitido de pregrado y postgrado para el período indicado
		if ($admitidosPregrado == true and $admitidosPostgrado == true) {
			$borrarAdmitidos = $this -> miComponente -> borrarAdmitidoSnies($annio, $semestre);
		}

		$this -> procesarAdmitidosPregrado($admitidosPregrado);
		$this->procesarAdmitidosPostgrado ( $admitidosPostgrado );

		echo 'Proceso finalizado';

		//$valorCodificado = "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
		// $valorCodificado .= "&action=" . $this->esteBloque ["nombre"];
		// $valorCodificado .= '&bloqueGrupo='.$this->esteBloque ['grupo'];
		// $valorCodificado .= "&opcion=".$opcion;
		// $valorCodificado .= "&annio=".$this->annio;
		// $valorCodificado .= "&semestre=".$this->semestre;
		//$valorCodificado = $this -> miConfigurador -> fabricaConexiones -> crypto -> codificar($valorCodificado);

		// Rescatar el parámetro enlace desde los datos de configuraión en la base de datos
		//$variable = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
		//$miEnlace = $this->host . $this->site . '/index.php?' . $variable . '=' . $valorCodificado;

		//header ( "Location:$miEnlace" );
	}

	/**
	 * PROCESAR ADMITIDOS PREGRADO
	 * Esta función realiza las siguientes acciones
	 * 1.consulta en la académica
	 * 2.Procesar los datos obtenidos, cambiar acentos.
	 * 3.Registrar errores de la fuente para reportarlos
	 * 4.Borrar los registros para el año y periodo seleccionado en SNIES LOCAL
	 * 5.Insertar los registros en el SNIES LOCAL
	 * 6.Redireccionar a lista de variables
	 */
	function procesarAdmitidosPregrado($admitidosPregrado) {
		/**$miProcesadorNombre = new procesadorNombre();
		// Quitar acentos y caracteres especiales
		$admitidosPregrado = $miProcesadorNombre -> quitarAcento($admitidosPregrado, 'APELLIDO');
		$admitidosPregrado = $miProcesadorNombre -> quitarAcento($admitidosPregrado, 'NOMBRE');

		// descompone nombre y apellidos en sus partes y las agrega al final de cada registro
		foreach ($admitidosPregrado as $clave => $valor) {
			// echo $inscritosPregrado [$clave] ['DOCUMENTO'] . '<br>';

			// divide los apellidos compuestos en primer apellido y segundo apellido
			$apellido = $miProcesadorNombre -> dividirApellidos($admitidosPregrado[$clave]['APELLIDO']);
			$admitidosPregrado[$clave]['PRIMER_APELLIDO'] = $apellido['primer_apellido'];
			$admitidosPregrado[$clave]['SEGUNDO_APELLIDO'] = $apellido['segundo_apellido'];

			// divide los nombres compuestos en primer nombre y segundo nombre
			$nombre = $miProcesadorNombre -> dividirNombres($admitidosPregrado[$clave]['NOMBRE']);

			$admitidosPregrado[$clave]['PRIMER_NOMBRE'] = $nombre['primer_nombre'];
			$admitidosPregrado[$clave]['SEGUNDO_NOMBRE'] = $nombre['segundo_nombre'];
		}*/

		foreach ($admitidosPregrado as $admitido) {
			$this -> miComponente -> insertarAdmitido($admitido);
		}
	}

	/**
	 * PROCESAR ADMITIDOS POSTGRADO
	 * Esta función realiza las siguientes acciones
	 * 1.consulta en la académica
	 * 2.Procesar los datos obtenidos, cambiar acentos.
	 * 3.Registrar errores de la fuente para reportarlos
	 * 4.Borrar los registros para el año y periodo seleccionado en SNIES LOCAL
	 * 5.Insertar los registros en el SNIES LOCAL
	 * 6.Redireccionar a lista de variables
	 *
	 * @param unknown $admitidosPregrado
	 */
	function procesarAdmitidosPostgrado($admitidosPostgrado) {
		$miProcesadorNombre = new procesadorNombre();

		/**$admitidosPostgrado = $miProcesadorNombre -> quitarAcento($admitidosPostgrado, 'NOMBRE');

		// descompone nombre completo en sus partes y las agrega al final de cada registro
		foreach ($admitidosPostgrado as $clave => $valor) {
			// echo $admitidosPostgrado [$clave] ['DOCUMENTO'] . '<br>';

			// divide los apellidos compuestos en primer apellido y segundo apellido
			$nombreCompleto = $miProcesadorNombre -> dividirNombreCompleto($admitidosPostgrado[$clave]['NOMBRE']);
			$admitidosPostgrado[$clave]['PRIMER_APELLIDO'] = $nombreCompleto['primer_apellido'];
			$admitidosPostgrado[$clave]['SEGUNDO_APELLIDO'] = $nombreCompleto['segundo_apellido'];
			$admitidosPostgrado[$clave]['PRIMER_NOMBRE'] = $nombreCompleto['primer_nombre'];
			$admitidosPostgrado[$clave]['SEGUNDO_NOMBRE'] = $nombreCompleto['segundo_nombre'];
		}*/

		// Inserta uno a uno los registros de admitidos de postgrado consultados en la académica
		foreach ($admitidosPostgrado as $admitido) {
			// echo $admitidosPostgrado [$clave] ['DOCUMENTO'] . '<br>';
			$insertarAdmitido = $this -> miComponente -> insertarAdmitido($admitido);
		}
	}

}

$miProcesador = new FormProcessor($this -> lenguaje, $this -> sql);

$resultado = $miProcesador -> procesarFormulario();
