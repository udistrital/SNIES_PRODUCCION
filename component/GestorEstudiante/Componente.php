<?php

namespace sniesEstudiante;

include_once 'component/Component.class.php';
use component\Component;

require_once ('component/GestorEstudiante/Sql.class.php');
require_once ('component/GestorEstudiante/Clase/GestorEstudianteAcademica.class.php');
require_once ('component/GestorEstudiante/Interfaz/IGestorEstudianteAcademica.php');
class Componente extends Component implements IGestorEstudiante {
	private $miSql;
	
	// El componente actua como Fachada
	
	/**
	 */
	public function __construct() {
		$this->miEstudiante = new estudiante ();
	}
	
	// funciones matriculados
	function contarMatriculados($periodo) {
		return $this->miEstudiante->contarMatriculados ( $periodo );
	}
	function consultarParticipanteEstudiante($annio, $semestre) {
		return $this->miEstudiante->consultarParticipanteEstudiante ( $annio, $semestre );
	}
	function registrarParticipanteEstudiante($estudiante) {
		return $this->miEstudiante->registrarParticipanteEstudiante ( $estudiante );
	}
	function borrarParticipanteEstudiante($estudiante) {
		return $this->miEstudiante->borrarParticipanteEstudiante ( $estudiante );
	}
	function consultarEstudiante() {
		return $this->miEstudiante->consultarEstudiante ();
	}
	function consultarEstudianteSNIES($estudiante) {
		return $this->miEstudiante->consultarEstudianteSNIES ($estudiante);
	}
	
	function borrarEstudiante() {
		return $this->miEstudiante->borrarEstudiante ();
	}
}

