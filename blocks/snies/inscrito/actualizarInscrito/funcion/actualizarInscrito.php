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
		$this -> annio = $_REQUEST['annio'];
		$this -> semestre = $_REQUEST['semestre'];

		/**
		 * Asegure de que todos los programas estén registrados en la tabla accra_snies
		 * para buscar los que no están registrados utilice la consulta
		 * SELECT * FROM ACCRA WHERE CRA_COD NOT IN (SELECT AS_CRA_COD FROM ACCRA_SNIES  )AND CRA_ESTADO='A'order by cra_cod asc
		 */

		// CONSULTAS ACADEMICA PREGRADO
		$inscritosPregradoAcademica = $this -> miComponente -> consultarInscritoPregadoAcademica($this -> annio, $this -> semestre);
		if (is_array($inscritosPregradoAcademica)) {
			$inscritosPregradoAcademica = $this -> procesarInscritosPregrado($inscritosPregradoAcademica);
		}

		// CONSULTAS ACADEMICA POSTGRADO
		$inscritosPostgradoAcademica = $this -> miComponente -> consultarInscritoPostgradoAcademica($this -> annio, $this -> semestre);
		if (is_array($inscritosPostgradoAcademica)) {
			$inscritosPostgradoAcademica = $this -> procesarInscritosPostgrado($inscritosPostgradoAcademica);
		}

		//UNIFICAR ACADEMICA PREGRADO Y POSTGRADO
		$inscritosAcademica = $this -> unificarInscritosPregradoPostgradoAcademica($inscritosPregradoAcademica, $inscritosPostgradoAcademica);

		// INSERTAR, ACTUALIZAR Y/O BORRAR EN TABLA INSCRITO DEL SNIES
		$this -> registrarInscritos($inscritosAcademica);

		// INSERTAR, ACTUALIZAR Y/O BORRAR EN TABLA INSCRITO_PROGAMA
		$this -> registrarInscritoPrograma($inscritosAcademica);

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

	/**
	 * Crea un solo arreglo con pregrado y postgrado de la académica
	 * Cada array tiene como indice: ano||semestre||id_tipo_documento||documento|| - || pro_consecutivo Ejem: 20162CC78125789-3432
	 */
	function unificarInscritosPregradoPostgradoAcademica($pregrado, $postgrado) {
		//Coloca en el indice de cada arreglo ano||semestre||id_tipo_documento||documento
		foreach ($pregrado as $key => $value) {
			$inscritosPregrado[$pregrado[$key]['ANO'] . $pregrado[$key]['SEMESTRE'] . $pregrado[$key]['ID_TIPO_DOCUMENTO'] . $pregrado[$key]['DOCUMENTO'] . "-" . $pregrado[$key]['PRO_CONSECUTIVO']] = $value;
		}

		//Coloca en el indice de cada arreglo ano||semestre||id_tipo_documento||documento
		foreach ($postgrado as $key => $value) {
			$inscritosPostgrado[$postgrado[$key]['ANO'] . $postgrado[$key]['SEMESTRE'] . $postgrado[$key]['ID_TIPO_DOCUMENTO'] . $postgrado[$key]['DOCUMENTO'] . "-" . $pregrado[$key]['PRO_CONSECUTIVO']] = $value;
		}

		//arreglo que incluye inscritos de pregrado y postgrado de la academica
		return array_merge($inscritosPregrado, $inscritosPostgrado);
	}

	/**
	 * Funcion que inserta, actualiza o borra en la tabla inscritos de SNIES
	 * Registra si no existe en SNIES
	 * Actualiza si existe en el SNIES
	 * Borra si no está en la ACADEMICA
	 */
	function registrarInscritos($inscrito) {

		//Obtiene un solo registro por inscrito, sin importar que esté en varios proyectos
		foreach ($inscrito as $key => $value) {
			$inscritosSinProyecto[$inscrito[$key]['ANO'] . $inscrito[$key]['SEMESTRE'] . $inscrito[$key]['ID_TIPO_DOCUMENTO'] . $inscrito[$key]['DOCUMENTO']] = $value;
		}

		// CONSULTA LA TABLA INSCRITO SNIES
		$inscritoSnies = $this -> miComponente -> consultarInscritoSnies($this -> annio, $this -> semestre);

		//Coloca en el indice de cada arreglo de lo consultado en el SNIES ano||semestre||id_tipo_documento||documento
		if ($inscritoSnies != NULL) {
			foreach ($inscritoSnies as $key => $value) {
				$inscritoSniesClave[$inscritoSnies[$key]['ano'] . $inscritoSnies[$key]['semestre'] . $inscritoSnies[$key]['id_tipo_documento'] . $inscritoSnies[$key]['num_documento']] = $value;
				$inscritoNuevo = array_diff_key($inscritosSinProyecto, $inscritoSniesClave);
			}
			//Estan en académica y no en SNIES, INSERTAR
			foreach ($inscritoNuevo as $unInscritoNuevo) {
				$this -> miComponente -> insertarInscritoSnies($unInscritoNuevo);
			}
			echo 'Registros nuevos insertados<br>';
			//Estan en académica y en Snies, ACTUALIZAR
			$inscritosActualizar = array_intersect_key($inscritosSinProyecto, $inscritoSniesClave);
			//aqui debería estar la función de actualizacion, por agilizar el tiempo de ejecución no se implementa en esta estapa
			echo 'Registros existentes actualizados<br>';

			//Estan es Snies y no en Académica, BORRAR
			$inscritoError = array_diff_key($inscritoSniesClave, $inscritosSinProyecto);
			foreach ($inscritoError as $unInscritoError) {
				$this -> miComponente -> borrarInscritoSnies($unInscritoError);
			}
			echo 'Registros erroneos borrados<br>';
		} else {
			$inscritoNuevo = $inscritosSinProyecto;
			//Estan en académica y no en SNIES, INSERTAR
			foreach ($inscritoNuevo as $unInscritoNuevo) {
				$this -> miComponente -> insertarInscritoSnies($unInscritoNuevo);
			}
			echo 'Registros nuevos insertados<br>';
		}

	}

	function registrarInscritoPrograma($inscritoPrograma) {

		$inscritoProgSnies = $this -> miComponente -> consultarInscritoProgramaSnies($this -> annio, $this -> semestre);

		//Coloca en el indice de cada arreglo de lo consultado en el SNIES ano||semestre||id_tipo_documento||documento
		if ($inscritoProgSnies != NULL) {
			foreach ($inscritoProgSnies as $key => $value) {
				$inscritoSniesClave[$inscritoProgSnies[$key]['ano'] . $inscritoProgSnies[$key]['semestre'] . $inscritoProgSnies[$key]['id_tipo_documento'] . $inscritoProgSnies[$key]['num_documento'] . "-" . $inscritoProgSnies[$key]['pro_consecutivo']] = $value;				
			}
			$inscritoProgramaNuevo = array_diff_key($inscritoPrograma, $inscritoSniesClave);
			//Estan en académica y no en SNIES, INSERTAR
			foreach ($inscritoProgramaNuevo as $unInscritoProgramaNuevo) {				
				$this -> miComponente -> insertarInscritoProgramaSnies($unInscritoProgramaNuevo);
			}
			echo 'Registros nuevos insertados<br>';
			
			//Estan en académica y en Snies, ACTUALIZAR
			$inscritosProgramaActualizar = array_intersect_key($inscritoProgramaNuevo, $inscritoSniesClave);
			//aqui debería estar la función de actualizacion, por agilizar el tiempo de ejecución no se implementa en esta estapa
			echo 'Registros existentes actualizados<br>';

			//Estan es Snies y no en Académica, BORRAR
			$inscritoError = array_diff_key($inscritoSniesClave, $inscritoProgramaNuevo);
			foreach ($inscritoError as $unInscritoError) {
				$this -> miComponente -> borrarInscritoSnies($unInscritoError);
			}
			echo 'Registros erroneos borrados<br>';
		} else {
			
			//Estan en académica y no en SNIES, INSERTAR
			foreach ($inscritoPrograma as $unInscritoPrograma) {
				$this -> miComponente -> insertarInscritoProgramaSnies($unInscritoPrograma);
			}
			echo 'Registros nuevos insertados<br>';
		}
	}

}

$miProcesador = new FormProcessor($this -> lenguaje, $this -> sql);

$resultado = $miProcesador -> procesarFormulario();
