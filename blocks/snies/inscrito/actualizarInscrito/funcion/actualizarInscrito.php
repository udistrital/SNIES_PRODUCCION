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
	 * Asegure de que todos los programas estén registrados en la tabla accra_snies
	 * para buscar los que no están registrados utilice la consulta
	 * SELECT * FROM ACCRA WHERE CRA_COD NOT IN (SELECT AS_CRA_COD FROM ACCRA_SNIES  )AND CRA_ESTADO='A'order by cra_cod asc
	 */
	function procesarFormulario() {
		$this -> annio = $_REQUEST['annio'];
		$this -> semestre = $_REQUEST['semestre'];

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

		$admitidosAcadémica = $this -> obtenerAdmitidosAcademica($inscritosAcademica);
		var_dump($admitidosAcadémica);exit;
		
		
		
		// INSERTAR, ACTUALIZAR Y/O BORRAR EN TABLA INSCRITO DEL SNIES
		$this -> registrarInscritos($inscritosAcademica);

		// INSERTAR, ACTUALIZAR Y/O BORRAR EN TABLA INSCRITO_PROGAMA
		$this -> registrarInscritoPrograma($inscritosAcademica);

		echo 'Proceso finalizado';

	}

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
			$inscritosPostgrado[$postgrado[$key]['ANO'] . $postgrado[$key]['SEMESTRE'] . $postgrado[$key]['ID_TIPO_DOCUMENTO'] . $postgrado[$key]['DOCUMENTO'] . "-" . $postgrado[$key]['PRO_CONSECUTIVO']] = $value;
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
			$inscritoUnicoAcademica[$inscrito[$key]['ANO'] . $inscrito[$key]['SEMESTRE'] . $inscrito[$key]['ID_TIPO_DOCUMENTO'] . $inscrito[$key]['DOCUMENTO']] = $value;
		}

		// CONSULTA LA TABLA INSCRITO SNIES
		$inscritoSnies = $this -> miComponente -> consultarInscritoSnies($this -> annio, $this -> semestre);

		//Coloca en el indice de cada arreglo de lo consultado en el SNIES ano||semestre||id_tipo_documento||documento
		if ($inscritoSnies != NULL) {
			foreach ($inscritoSnies as $key => $value) {
				$inscritoSniesClave[$inscritoSnies[$key]['ano'] . $inscritoSnies[$key]['semestre'] . $inscritoSnies[$key]['id_tipo_documento'] . $inscritoSnies[$key]['num_documento']] = $value;
			}

			//REGISTRA LOS NUEVOS EN SNIES
			$inscritoNuevo = array_diff_key($inscritoUnicoAcademica, $inscritoSniesClave);
			//Estan en académica y no en SNIES, INSERTAR
			foreach ($inscritoNuevo as $unInscritoNuevo) {
				$this -> miComponente -> insertarInscritoSnies($unInscritoNuevo);
			}
			echo 'Registros nuevos insertados en inscrito<br>';

			//ACTUALIZA LOS QUE YA ESTAN EN SNIES
			$inscritosActualizar = array_intersect_key($inscritoUnicoAcademica, $inscritoSniesClave);
			//aqui debería estar la función de actualizacion, por agilizar el tiempo de ejecución no se implementa en esta estapa
			echo 'Registros existentes actualizados en inscrito<br>';

			//BORRA LOS QUE NO DEBERÍAN ESTAR EN SNIES
			$inscritoError = array_diff_key($inscritoSniesClave, $inscritoUnicoAcademica);
			foreach ($inscritoError as $unInscritoError) {
				$this -> miComponente -> borrarInscritoSnies($unInscritoError);
			}
			echo 'Registros erroneos borrados en inscrito<br>';
		} else {
			$inscritoNuevo = $inscritoUnicoAcademica;
			//Estan en académica y no en SNIES, INSERTAR
			foreach ($inscritoNuevo as $unInscritoNuevo) {
				$this -> miComponente -> insertarInscritoSnies($unInscritoNuevo);
			}
			echo 'Registros nuevos insertados en inscrito<br>';
		}

	}

	function registrarInscritoPrograma($inscritoPrograma) {

		$inscritoProgSnies = $this -> miComponente -> consultarInscritoProgramaSnies($this -> annio, $this -> semestre);

		//Coloca en el indice de cada arreglo de lo consultado en el SNIES ano||semestre||id_tipo_documento||documento
		if ($inscritoProgSnies != NULL) {
			foreach ($inscritoProgSnies as $key => $value) {
				$inscritoSniesClave[$inscritoProgSnies[$key]['ano'] . $inscritoProgSnies[$key]['semestre'] . $inscritoProgSnies[$key]['id_tipo_documento'] . $inscritoProgSnies[$key]['num_documento'] . "-" . $inscritoProgSnies[$key]['pro_consecutivo']] = $value;
			}
			//REGISTRA INSCRITO_PROGRAMA NUEVO EN EL SNIES
			$inscritoProgramaNuevo = array_diff_key($inscritoPrograma, $inscritoSniesClave);
			var_dump($inscritoProgramaNuevo);
			exit ;
			foreach ($inscritoProgramaNuevo as $unInscritoProgramaNuevo) {
				$this -> miComponente -> insertarInscritoProgramaSnies($unInscritoProgramaNuevo);
			}
			echo 'Registros nuevos insertados en inscrito_programa<br>';

			//ACTUALIZA LOS QUE ESTAN EN INSCRITO_PROGRAMA DEL SNIES
			$inscritosProgramaActualizar = array_intersect_key($inscritoProgramaNuevo, $inscritoSniesClave);
			//aqui debería estar la función de actualizacion, por agilizar el tiempo de ejecución no se implementa en esta estapa
			echo 'Registros existentes actualizados<br>';

			//BORRA LOS QUE NO DEBERÍAN ESTAR EN INSCRITO_PROGRAMA DEL SNIES - es decir los que no estan en académica
			$inscritoError = array_diff_key($inscritoSniesClave, $inscritoProgramaNuevo);
			foreach ($inscritoError as $unInscritoError) {
				$this -> miComponente -> borrarInscritoSnies($unInscritoError);
			}
			echo 'Registros erroneos borrados en inscrito_programa<br>';
		} else {

			//Estan en académica y no en SNIES, INSERTAR
			foreach ($inscritoPrograma as $unInscritoPrograma) {
				$this -> miComponente -> insertarInscritoProgramaSnies($unInscritoPrograma);
			}
			echo 'Registros nuevos insertados en inscrito_programa<br>';
		}
	}

	function obtenerAdmitidosAcademica($inscritos) {
		foreach ($inscritos as $key => $value) {
			if (isset($inscritos[$key]['EAD_ESTADO']) and $inscritos[$key]['EAD_ESTADO'] == 'A') {
				$admitidos[$inscritos[$key]['ANO'] . $inscritos[$key]['SEMESTRE'] . $inscritos[$key]['ID_TIPO_DOCUMENTO'] . $inscritos[$key]['DOCUMENTO'] . "-" . $inscritos[$key]['PRO_CONSECUTIVO']] = $value;
			}
		}

		return $admitidos;

	}

}

$miProcesador = new FormProcessor($this -> lenguaje, $this -> sql);

$resultado = $miProcesador -> procesarFormulario();
