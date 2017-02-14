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

		// CONSULTAS ACADEMICA - PREGRADO Y POSTGRADO
		$inscritoAcademica = $this -> miComponente -> consultarInscritoAcademica($this -> annio, $this -> semestre);

		if (is_array($inscritoAcademica)) {
			$inscritoAcademica = $this -> procesarInscritoAcademica($inscritoAcademica);
		}

		// INSERTAR, ACTUALIZAR Y/O BORRAR EN TABLA INSCRITO DEL SNIES
		$this -> registrarInscritos($inscritoAcademica);

		// INSERTAR, ACTUALIZAR Y/O BORRAR EN TABLA INSCRITO_PROGAMA
		$this -> registrarInscritoPrograma($inscritoAcademica);
		exit ;
		// INSERTAR, ACTUALIZAR Y/O BORRAR EN TABLA INSCRITO_PROGAMA
		$admitidoAcademica = $this -> obtenerAdmitidosAcademica($inscritoAcademica);
		//se obtiene a partir del arreglo de inscritos
		$this -> registrarAdmitido($admitidoAcademica);

		echo 'Proceso finalizado';

	}

	/**
	 * Si el inscrito es estudiante el tipo y número de documento es el del estudiantes y no el del inscrito
	 * Separa los nombres en PRIMER_APELLIDO, SEGUNDO_APELLIDO, PRIMER_NOMBRE, SEGUNDO_NOMBRE
	 * Retorne el arreglo ajustado
	 */
	function procesarInscritoAcademica($inscrito) {

		$miProcesadorNombre = new procesadorNombre();
		$inscrito = $miProcesadorNombre -> quitarAcento($inscrito, 'APELLIDO');
		$inscrito = $miProcesadorNombre -> quitarAcento($inscrito, 'NOMBRE');

		foreach ($inscrito as $clave => $valor) {

			if ($inscrito[$clave]['NIVEL'] == 'PREGRADO') {

				if (isset($inscrito[$clave]['EST_TIPO_IDEN'])) {
					$inscrito[$clave]['ID_TIPO_DOCUMENTO'] = $inscrito[$clave]['EST_TIPO_IDEN'];
					$inscrito[$clave]['DOCUMENTO'] = $inscrito[$clave]['EST_NRO_IDEN'];
					//Quita del arreglo el tipo y numero de documento del estudiantes pera evitar confusión
					unset($inscrito[$clave]['EST_TIPO_IDEN']);
					unset($inscrito[$clave]['9']);
					unset($inscrito[$clave]['EST_NRO_IDEN']);
					unset($inscrito[$clave]['10']);
				} else {

				}

				// descompone nombre y apellidos en sus partes y las agrega al final de cada registro
				// echo $inscritosPregrado [$clave] ['DOCUMENTO'] . '<br>';
				// divide los apellidos compuestos en primer apellido y segundo apellido
				$apellido = $miProcesadorNombre -> dividirApellidos($inscrito[$clave]['APELLIDO']);
				$inscrito[$clave]['PRIMER_APELLIDO'] = $apellido['primer_apellido'];
				$inscrito[$clave]['SEGUNDO_APELLIDO'] = $apellido['segundo_apellido'];

				// divide los nombres compuestos en primer nombre y segundo nombre
				$nombre = $miProcesadorNombre -> dividirNombres($inscrito[$clave]['NOMBRE']);

				$inscrito[$clave]['PRIMER_NOMBRE'] = $nombre['primer_nombre'];
				$inscrito[$clave]['SEGUNDO_NOMBRE'] = $nombre['segundo_nombre'];

			} else {

				// descompone nombre completo en sus partes y las agrega al final de cada registro
				// echo $inscritosPostgrado [$clave] ['DOCUMENTO'] . '<br>';

				// divide los apellidos compuestos en primer apellido y segundo apellido
				$nombreCompleto = $miProcesadorNombre -> dividirNombreCompleto($inscrito[$clave]['NOMBRE']);
				$inscrito[$clave]['PRIMER_APELLIDO'] = $nombreCompleto['primer_apellido'];
				$inscrito[$clave]['SEGUNDO_APELLIDO'] = $nombreCompleto['segundo_apellido'];
				$inscrito[$clave]['PRIMER_NOMBRE'] = $nombreCompleto['primer_nombre'];
				$inscrito[$clave]['SEGUNDO_NOMBRE'] = $nombreCompleto['segundo_nombre'];
			}

		}

		return $inscrito;
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

	function registrarInscritoPrograma($inscrito) {

		foreach ($inscrito as $key => $value) {
			$inscritoAcademica[$inscrito[$key]['ANO'] . $inscrito[$key]['SEMESTRE'] . $inscrito[$key]['ID_TIPO_DOCUMENTO'] . $inscrito[$key]['DOCUMENTO'] . "-" . $inscrito[$key]['PRO_CONSECUTIVO']] = $value;
		}

		$inscritoProgSnies = $this -> miComponente -> consultarInscritoProgramaSnies($this -> annio, $this -> semestre);

		//Coloca en el indice de cada arreglo de lo consultado en el SNIES ano||semestre||id_tipo_documento||documento
		if ($inscritoProgSnies != NULL) {
			foreach ($inscritoProgSnies as $key => $value) {
				$inscritoSniesClave[$inscritoProgSnies[$key]['ano'] . $inscritoProgSnies[$key]['semestre'] . $inscritoProgSnies[$key]['id_tipo_documento'] . $inscritoProgSnies[$key]['num_documento'] . "-" . $inscritoProgSnies[$key]['pro_consecutivo']] = $value;
			}
			//REGISTRA INSCRITO_PROGRAMA NUEVO EN EL SNIES
			$inscritoProgramaNuevo = array_diff_key($inscritoAcademica, $inscritoSniesClave);

			foreach ($inscritoProgramaNuevo as $unInscritoProgramaNuevo) {
				$this -> miComponente -> insertarInscritoProgramaSnies($unInscritoProgramaNuevo);
			}
			echo 'Registros nuevos insertados en inscrito_programa<br>';

			//ACTUALIZA LOS QUE ESTAN EN INSCRITO_PROGRAMA DEL SNIES
			$inscritosProgramaActualizar = array_intersect_key($inscritoAcademica, $inscritoSniesClave);
			//aqui debería estar la función de actualizacion, por agilizar el tiempo de ejecución no se implementa en esta estapa
			echo 'Registros existentes actualizados<br>';

			//BORRA LOS QUE NO DEBERÍAN ESTAR EN INSCRITO_PROGRAMA DEL SNIES - es decir los que no estan en académica
			$inscritoError = array_diff_key($inscritoSniesClave, $inscritoAcademica);
			foreach ($inscritoError as $unInscritoError) {
				$this -> miComponente -> borrarInscritoSnies($unInscritoError);
			}
			echo 'Registros erroneos borrados en inscrito_programa<br>';
		} else {

			//Estan en académica y no en SNIES, INSERTAR
			foreach ($inscrito as $unInscritoPrograma) {
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

	function registrarAdmitido($admitido) {

		$admitidoSnies = $this -> miComponente -> consultarAdmitidoSnies($this -> annio, $this -> semestre);

		//Coloca en el indice de cada arreglo de lo consultado en el SNIES ano||semestre||id_tipo_documento||documento
		if ($admitidoSnies != NULL) {
			foreach ($admitidoSnies as $key => $value) {
				$admitidoSniesClave[$admitidoSnies[$key]['ano'] . $admitidoSnies[$key]['semestre'] . $admitidoSnies[$key]['id_tipo_documento'] . $admitidoSnies[$key]['num_documento'] . "-" . $admitidoSnies[$key]['pro_consecutivo']] = $value;
			}

			//REGISTRA ADMITIDO NUEVO EN EL SNIES
			$admitidoNuevo = array_diff_key($admitido, $admitidoSniesClave);

			foreach ($admitidoNuevo as $unAdmitidoNuevo) {
				$this -> miComponente -> insertarAdmitidoSnies($unAdmitidoNuevo);
			}
			echo 'Registros nuevos insertados en admitido<br>';

			//ACTUALIZA LOS QUE ESTAN EN INSCRITO_PROGRAMA DEL SNIES
			$admitidosActualizar = array_intersect_key($admitidoNuevo, $admitidoSniesClave);
			//aqui debería estar la función de actualizacion, por agilizar el tiempo de ejecución no se implementa en esta estapa
			echo 'Registros existentes actualizados<br>';

			//BORRA LOS QUE NO DEBERÍAN ESTAR EN INSCRITO_PROGRAMA DEL SNIES - es decir los que no estan en académica
			$admitidoError = array_diff_key($admitidoSniesClave, $admitidoNuevo);
			var_dump($admitidoError);
			exit ;
			foreach ($admitidoError as $unAdmitidoError) {
				$this -> miComponente -> borrarAdmitidoSnies($unAdmitidoError);
			}
			echo 'Registros erroneos borrados en admitido<br>';
		} else {

			//Estan en académica y no en SNIES, INSERTAR
			foreach ($admitido as $unAdmitido) {
				$this -> miComponente -> insertarAdmitidoSnies($unAdmitido);
			}
			echo 'Registros nuevos insertados en admitido<br>';
		}

	}

}

$miProcesador = new FormProcessor($this -> lenguaje, $this -> sql);

$resultado = $miProcesador -> procesarFormulario();
