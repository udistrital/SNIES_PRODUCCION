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
	
}

