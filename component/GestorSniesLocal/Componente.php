<?php

namespace snies;

include_once 'component/Component.class.php';
use component\Component;

require_once ('component/GestorSniesLocal/Sql.class.php');
require_once ('component/GestorSniesLocal/Clase/GestorInscrito.class.php');
require_once ('component/GestorSniesLocal/Interfaz/IGestorInscrito.php');
require_once ('component/GestorSniesLocal/Clase/GestorAdmitido.class.php');
require_once ('component/GestorSniesLocal/Interfaz/IGestorAdmitido.php');
require_once ('component/GestorSniesLocal/Clase/GestorMatriculaPrimerCurso.class.php');
require_once ('component/GestorSniesLocal/Interfaz/IGestorMatriculaPrimerCurso.php');
require_once ('component/GestorSniesLocal/Clase/GestorMatriculado.class.php');
require_once ('component/GestorSniesLocal/Interfaz/IGestorMatriculado.php');
class Componente extends Component implements IGestorInscrito, IGestorAdmitido, IGestorMatriculaPrimerCurso, IGestorMatriculado {
	private $miSql;
	
	// El componente actua como Fachada
	
	/**
	 */
	public function __construct() {
		$this->miGestorInscrito = new GestorInscrito ();
		$this->miGestorAdmitido = new GestorAdmitido ();
		$this->miGestorMatriculaPrimerCurso = new GestorMatriculaPrimerCurso ();
		$this->miGestorMatriculado = new GestorMatriculado ();
	}
	// funciones inscritos
	function consultarInscritoAcademica($annio, $semestre) {
		return $this->miGestorInscrito->consultarInscritoAcademica ( $annio, $semestre );
	}
	function consultarInscritoSnies() {
		return $this->miGestorInscrito->consultarInscritoSnies ( $periodo );
	}
	function insertarInscritoSnies($inscrito) {
		return $this->miGestorInscrito->insertarInscritoSnies ($inscrito );
	}
	function actualizarInscritoSnies() {
		return $this->miGestorInscrito->actualizarInscritoSnies ( $periodo );
	}
	function borrarInscritoSnies($annio, $semestre) {
		return $this->miGestorInscrito->borrarInscritoSnies ( $annio, $semestre );
	}
	function contarInscritos( $periodo ) {
		return $this->miGestorInscrito->contarInscritos ( $periodo );
	}
	
	// funciones admitidos
	function contarAdmitidos($periodo) {
		return $this->miGestorAdmitido->contarAdmitidos ( $periodo );
	}
	
	// funciones matriculados a primer curso
	function contarMatriculadosPrimerCurso($periodo) {
		return $this->miGestorMatriculaPrimerCurso->contarMatriculadosPrimerCurso ( $periodo );
	}
	
	// funciones matriculados
	function contarMatriculados($periodo) {
		return $this->miGestorMatriculado->contarMatriculados ( $periodo );
	}
}

