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
	 * 2. Ajusta formato de datos de inscritos pregrado
	 * 3.consulta en la académica inscritos postgrado
	 * 4. Ajusta los datos de postgrado
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

		// CONSULTAS ACADEMICA
		$inscritosPregradoAcademica = $this -> miComponente -> consultarInscritoPregadoAcademica($annio, $semestre);
		$inscritosPregradoAcademica = $this -> procesarInscritosPregrado($inscritosPregradoAcademica);

		$inscritosPostgradoAcademica = $this -> miComponente -> consultarInscritoPostgradoAcademica($annio, $semestre);
		$inscritosPostgradoAcademica = $this -> procesarInscritosPostgrado($inscritosPostgradoAcademica);

		$inscritoSnies = $this -> miComponente -> consultarInscritoSnies($annio, $semestre);

		$this -> registrarNuevosInscritos($inscritosPregradoAcademica, $inscritosPostgradoAcademica, $inscritoSnies);

		//ACTUALIZAR Buscar registros antiguos

		//BORRAR registros que ya no estan

		exit ;

		// LIMPIAR LOS REGISTROS DEL AÑO Y SEMESTRE ESPECIFICADO

		exit ;
		$borrarInscritoPrograma = $this -> miComponente -> borrarInscritoProgramaSnies($annio, $semestre);

		//La tabla inscrito acepta solo un registro por cada inscrito
		foreach ($inscritosPregradoAcademica as $key => $value) {
			$inscritosSinDuplicados[$value['DOCUMENTO']] = $value;
		}
		// Inserta uno a uno los registros sin duplicados de inscritos consultados en la académica

		// Inserta uno a uno los registros en la tabla inscrito_programa consultados en la académica
		foreach ($inscritosPregradoAcademica as $inscrito) {
			$this -> miComponente -> insertarInscritoProgramaSnies($inscrito);
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

		// Inserta uno a uno los registros de inscritos consultados en la académica
		foreach ($inscritosPostgrado as $inscrito) {
			$this -> miComponente -> insertarInscritoSnies($inscrito);
			$this -> miComponente -> insertarInscritoProgramaSnies($inscrito);
		}

		echo 'Proceso finalizado';

	}

	//procesarNuevosInscritos

	/**
	 * Si el inscrito es estudiante el tipo y número de documento es el del estudiantes y no el del inscrito
	 * Separa los nombres en PRIMER_APELLIDO, SEGUNDO_APELLIDO, PRIMER_NOMBRE, SEGUNDO_NOMBRE
	 * Retorne el arreglo ajustado
	 */
	function procesarInscritosPregrado($inscritosPregrado) {

		foreach ($inscritosPregrado as $clave => $valor) {
			if (isset($inscritosPregrado[$clave]['EST_TIPO_IDEN'])) {
				$inscritosPregrado[$clave]['ID_TIPO_DOCUMENTO'] = $inscritosPregrado[$clave]['EST_TIPO_IDEN'];
				$inscritosPregrado[$clave]['DOCUMENTO'] = $inscritosPregrado[$clave]['EST_NRO_IDEN'];
				//Quita del arreglo el tipo y numero de documanto del estudiantes pera evitar confusión
				unset($inscritosPregrado[$clave]['EST_TIPO_IDEN']);
				unset($inscritosPregrado[$clave]['9']);
				unset($inscritosPregrado[$clave]['EST_NRO_IDEN']);
				unset($inscritosPregrado[$clave]['10']);
			} else {

			}

		}

		$miProcesadorNombre = new procesadorNombre();

		$inscritosPregrado = $miProcesadorNombre -> quitarAcento($inscritosPregrado, 'APELLIDO');
		$inscritosPregrado = $miProcesadorNombre -> quitarAcento($inscritosPregrado, 'NOMBRE');

		// descompone nombre y apellidos en sus partes y las agrega al final de cada registro
		foreach ($inscritosPregrado as $clave => $valor) {
			// echo $inscritosPregrado [$clave] ['DOCUMENTO'] . '<br>';

			// divide los apellidos compuestos en primer apellido y segundo apellido
			$apellido = $miProcesadorNombre -> dividirApellidos($inscritosPregrado[$clave]['APELLIDO']);
			$inscritosPregrado[$clave]['PRIMER_APELLIDO'] = $apellido['primer_apellido'];
			$inscritosPregrado[$clave]['SEGUNDO_APELLIDO'] = $apellido['segundo_apellido'];

			// divide los nombres compuestos en primer nombre y segundo nombre
			$nombre = $miProcesadorNombre -> dividirNombres($inscritosPregrado[$clave]['NOMBRE']);

			$inscritosPregrado[$clave]['PRIMER_NOMBRE'] = $nombre['primer_nombre'];
			$inscritosPregrado[$clave]['SEGUNDO_NOMBRE'] = $nombre['segundo_nombre'];
		}

		return $inscritosPregrado;
	}

	function procesarInscritosPostgrado($inscritosPostgrado) {

		$miProcesadorNombre = new procesadorNombre();

		$inscritosPostgrado = $miProcesadorNombre -> quitarAcento($inscritosPostgrado, 'NOMBRE');

		// descompone nombre completo en sus partes y las agrega al final de cada registro
		foreach ($inscritosPostgrado as $clave => $valor) {
			// echo $inscritosPostgrado [$clave] ['DOCUMENTO'] . '<br>';

			// divide los apellidos compuestos en primer apellido y segundo apellido
			$nombreCompleto = $miProcesadorNombre -> dividirNombreCompleto($inscritosPostgrado[$clave]['NOMBRE']);
			$inscritosPostgrado[$clave]['PRIMER_APELLIDO'] = $nombreCompleto['primer_apellido'];
			$inscritosPostgrado[$clave]['SEGUNDO_APELLIDO'] = $nombreCompleto['segundo_apellido'];
			$inscritosPostgrado[$clave]['PRIMER_NOMBRE'] = $nombreCompleto['primer_nombre'];
			$inscritosPostgrado[$clave]['SEGUNDO_NOMBRE'] = $nombreCompleto['segundo_nombre'];
		}

		return $inscritosPostgrado;

	}

	//funcion que procesa los registros de inscritos
	function registrarNuevosInscritos($pregrado, $postgrado, $inscritoSnies) {

		//var_dump($inscritoSnies);exit;
		//Coloca en el indice de cada arreglo ano||semestre||id_tipo_documento||documento
		foreach ($pregrado as $key => $value) {
			$inscritosPregrado[$pregrado[$key]['ANO'] . $pregrado[$key]['SEMESTRE'] . $pregrado[$key]['ID_TIPO_DOCUMENTO'] . $pregrado[$key]['DOCUMENTO']] = $value;
		}

		//Coloca en el indice de cada arreglo ano||semestre||id_tipo_documento||documento
		foreach ($postgrado as $key => $value) {
			$inscritosPostgrado[$postgrado[$key]['ANO'] . $postgrado[$key]['SEMESTRE'] . $postgrado[$key]['ID_TIPO_DOCUMENTO'] . $postgrado[$key]['DOCUMENTO']] = $value;
		}

		//arreglo que incluye inscritos de pregrado y postgrado de la academica
		$inscritosAcademica = array_merge($inscritosPregrado, $inscritosPostgrado);

		//Coloca en el indice de cada arreglo de lo consultado en el SNIES ano||semestre||id_tipo_documento||documento

		if ($inscritoSnies != NULL) {
			foreach ($inscritoSnies as $key => $value) {
				$inscritoSniesClave[$inscritoSnies[$key]['ano'] . $inscritoSnies[$key]['semestre'] . $inscritoSnies[$key]['id_tipo_documento'] . $inscritoSnies[$key]['num_documento']] = $value;
				$inscritoNuevo = array_diff_key($inscritosAcademica, $inscritoSniesClave);
			}
		} else {
			$inscritoNuevo = $inscritosAcademica;
		}

		foreach ($inscritoNuevo as $unInscritoNuevo) {
			$this -> miComponente -> insertarInscritoSnies($unInscritoNuevo);
		}
		echo 'Registros nuevos insertados<br>';

		//Estan en académica y en Snies, deben actualizarse
		$inscritosActualizar = array_intersect_key($inscritosAcademica, $inscritoSniesClave);

		//Estan es Snies y no en Académica, deben borrarse
		$inscritoError = array_diff_key($inscritoSniesClave, $inscritosAcademica);

		foreach ($inscritoError as $unInscritoError) {
			$this -> miComponente -> borrarInscritoSnies($unInscritoError);
		}
		echo 'Registros erroneos borrados<br>';
	}

}

$miProcesador = new FormProcessor($this -> lenguaje, $this -> sql);

$resultado = $miProcesador -> procesarFormulario();
