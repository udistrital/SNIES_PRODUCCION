<?php

namespace snies;

include_once 'component/Component.class.php';
use component\Component;

require_once ('component/GestorInscritoAdmitido/Sql.class.php');
require_once ('component/GestorInscritoAdmitido/Clase/GestorInscrito.class.php');
require_once ('component/GestorInscritoAdmitido/Interfaz/IGestorInscrito.php');
require_once ('component/GestorInscritoAdmitido/Clase/GestorAdmitido.class.php');
require_once ('component/GestorInscritoAdmitido/Interfaz/IGestorAdmitido.php');
require_once ('component/GestorInscritoAdmitido/Clase/GestorMatriculaPrimerCurso.class.php');
require_once ('component/GestorInscritoAdmitido/Interfaz/IGestorMatriculaPrimerCurso.php');
require_once ('component/GestorInscritoAdmitido/Clase/GestorMatriculado.class.php');
require_once ('component/GestorInscritoAdmitido/Interfaz/IGestorMatriculado.php');
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
	function consultarInscritoPregadoAcademica($annio, $semestre) {
		return $this->miGestorInscrito->consultarInscritoPregadoAcademica ( $annio, $semestre );
	}
	function consultarInscritoSnies() {
		return $this->miGestorInscrito->consultarInscritoSnies ( $periodo );
	}
	function insertarInscritoSnies($inscrito) {
		return $this->miGestorInscrito->insertarInscritoSnies ( $inscrito );
	}
	function actualizarInscritoSnies() {
		return $this->miGestorInscrito->actualizarInscritoSnies ( $periodo );
	}
	function borrarInscritoSnies($annio, $semestre) {
		return $this->miGestorInscrito->borrarInscritoSnies ( $annio, $semestre );
	}
	function contarInscritos($periodo) {
		return $this->miGestorInscrito->contarInscritos ( $periodo );
	}
	
	// funciones admitidos
	function consultarAdmitidoAcademica($annio, $semestre) {
		return $this->miGestorAdmitido->consultarAdmitidoAcademica ( $annio, $semestre );
	}
	function contarAdmitidos($periodo) {
		return $this->miGestorAdmitido->contarAdmitidos ( $periodo );
	}
	function borrarAdmitidoSnies($annio, $semestre) {
		return $this->miGestorAdmitido->borrarAdmitidoSnies ( $annio, $semestre );
	}
	
	function insertarAdmitidoSnies($admitido) {
		return $this->miGestorAdmitido->insertarAdmitidoSnies ( $admitido );
	}
	
	// funciones matriculados a primer curso
	function contarMatriculadosPrimerCurso($periodo) {
		return $this->miGestorMatriculaPrimerCurso->contarMatriculadosPrimerCurso ( $periodo );
	}
	
	// funciones matriculados
	function contarMatriculados($periodo) {
		return $this->miGestorMatriculado->contarMatriculados ( $periodo );
	}
	function consultarParticipanteEstudiante($annio, $semestre) {
		return $this->miGestorMatriculado->consultarParticipanteEstudiante ( $annio, $semestre );
	}
	
}

