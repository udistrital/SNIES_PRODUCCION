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
	function consultarEstudianteAcademica($annio, $semestre) {
		return $this->miEstudiante->consultarEstudianteAcademica ( $annio, $semestre );
	}
	
	// ///PARTICIPANTE SNIES
	function cosultarParticipante($estudiante) {
		return $this->miEstudiante->cosultarParticipante ( $estudiante );
	}
	function actualizarParticipante($estudiante) {
		return $this->miEstudiante->actualizarParticipante ( $estudiante );
	}
	function registrarParticipante($estudiante) {
		return $this->miEstudiante->registrarParticipante ( $estudiante );
	}
	function borrarParticipanteEstudiante($estudiante) {
		return $this->miEstudiante->borrarParticipanteEstudiante ( $estudiante );
	}
	
	// //ESTUDIANTE SNIES
	function consultarEstudiante($estudiante) {
		return $this->miEstudiante->consultarEstudiante ( $estudiante );
	}
	function actualizarEstudiante($estudiante) {
		return $this->miEstudiante->actualizarEstudiante ( $estudiante );
	}
	function registrarEstudiante($estudiante) {
		return $this->miEstudiante->registrarEstudiante ( $estudiante );
	}
	
	// //ESTUDANTE_PROGRAMA SNIES
	function consultarEstudiantePrograma($estudiante) {
		return $this->miEstudiante->consultarEstudiantePrograma ( $estudiante );
	}
	function borrarEstudiantePrograma($annio, $semestre) {
		return $this->miEstudiante->borrarEstudiantePrograma ( $annio, $semestre );
	}
	function registrarEstudiantePrograma($estudiante) {
		return $this->miEstudiante->registrarEstudiantePrograma ( $estudiante );
	}
	
	// MATRICULADO
	function borrarMatriculado($annio, $semestre) {
		return $this->miEstudiante->borrarMatriculado ( $annio, $semestre );
	}
	function registrarMatriculado($estudiante, $annio, $semestre) {
		return $this->miEstudiante->registrarMatriculado ( $estudiante, $annio, $semestre );
	}
}

