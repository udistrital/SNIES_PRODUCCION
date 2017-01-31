<?php

namespace sniesInscritoAdmitido;

include_once 'component/Component.class.php';
use component\Component;

require_once ('component/GestorInscritoAdmitido/Sql.class.php');
require_once ('component/GestorInscritoAdmitido/Clase/GestorInscrito.class.php');
require_once ('component/GestorInscritoAdmitido/Interfaz/IGestorInscrito.php');
require_once ('component/GestorInscritoAdmitido/Clase/GestorAdmitido.class.php');
require_once ('component/GestorInscritoAdmitido/Interfaz/IGestorAdmitido.php');
class Componente extends Component implements IGestorInscrito, IGestorAdmitido {
	private $miSql;

	// El componente actua como Fachada

	/**
	 */
	public function __construct() {
		$this -> miGestorInscrito = new GestorInscrito();
		$this -> miGestorAdmitido = new GestorAdmitido();
	}

	// funciones inscritos
	function consultarInscritoPregadoAcademica($annio, $semestre) {
		return $this -> miGestorInscrito -> consultarInscritoPregadoAcademica($annio, $semestre);
	}

	function consultarInscritoPostgradoAcademica($annio, $semestre) {
		return $this -> miGestorInscrito -> consultarInscritoPostgradoAcademica($annio, $semestre);
	}

	function consultarInscritoSnies($annio, $semestre) {
		return $this -> miGestorInscrito -> consultarInscritoSnies($annio, $semestre);
	}

	function insertarInscritoSnies($inscrito) {
		return $this -> miGestorInscrito -> insertarInscritoSnies($inscrito);
	}

	function consultarInscritoProgramaSnies($annio, $semestre) {
		return $this -> miGestorInscrito -> consultarInscritoProgramaSnies($annio, $semestre);
	}

	function insertarInscritoProgramaSnies($inscrito) {
		return $this -> miGestorInscrito -> insertarInscritoProgramaSnies($inscrito);
	}

	function actualizarInscritoSnies() {
		return $this -> miGestorInscrito -> actualizarInscritoSnies($periodo);
	}

	function borrarInscritoSnies($inscrito) {
		return $this -> miGestorInscrito -> borrarInscritoSnies($inscrito);
	}

	function borrarInscritoProgramaSnies($annio, $semestre) {
		return $this -> miGestorInscrito -> borrarInscritoProgramaSnies($annio, $semestre);
	}

	function contarInscritos($annio, $semestre) {
		return $this -> miGestorInscrito -> contarInscritos($annio, $semestre);
	}

	// funciones admitidos
	function consultarAdmitidoPregradoAcademica($annio, $semestre) {
		return $this -> miGestorAdmitido -> consultarAdmitidoPregradoAcademica($annio, $semestre);
	}

	function consultarAdmitidoPostgradoAcademica($annio, $semestre) {
		return $this -> miGestorAdmitido -> consultarAdmitidoPostgradoAcademica($annio, $semestre);
	}

	function contarAdmitidos($periodo) {
		return $this -> miGestorAdmitido -> contarAdmitidos($periodo);
	}

	function borrarAdmitidoSnies($annio, $semestre) {
		return $this -> miGestorAdmitido -> borrarAdmitidoSnies($annio, $semestre);
	}

	function insertarAdmitido($admitido) {
		return $this -> miGestorAdmitido -> insertarAdmitido($admitido);
	}

}
