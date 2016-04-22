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
	function contarMatriculados($annio, $semestre) {
		return $this->miEstudiante->contarMatriculados ( $annio, $semestre );
	}
	function consultarEstudianteAcademica($annio, $semestre) {
		return $this->miEstudiante->consultarEstudianteAcademica ( $annio, $semestre );
	}
	function consultarEstudianteBpudc($annio, $semestre) {
		return $this->miEstudiante->consultarEstudianteBpudc ( $annio, $semestre );
	}
	
	// ///PARTICIPANTE SNIES
	function consultarParticipante($estudiante) {
		return $this->miEstudiante->consultarParticipante ( $estudiante );
	}
	function actualizarParticipante($estudiante) {
		return $this->miEstudiante->actualizarParticipante ( $estudiante );
	}
	function registrarParticipante($estudiante) {
		return $this->miEstudiante->registrarParticipante ( $estudiante );
	}
	function borrarParticipante($estudiante) {
		return $this->miEstudiante->borrarParticipante ( $estudiante );
	}
	
	// //ESTUDIANTE SNIES
	function consultarEstudiante($estudiante) {
		return $this->miEstudiante->consultarEstudiante ( $estudiante );
	}
	function consultarEstudianteTodos() {
		return $this->miEstudiante->consultarEstudianteTodos ( );
	}
	function actualizarEstudiante($estudiante) {
		return $this->miEstudiante->actualizarEstudiante ( $estudiante );
	}
	function registrarEstudiante($estudiante) {
		return $this->miEstudiante->registrarEstudiante ( $estudiante );
	}
	function borrarEstudiante($estudiante) {
		return $this->miEstudiante->borrarEstudiante ( $estudiante );
	}
	
	// //ESTUDANTE_PROGRAMA SNIES
	function consultarEstudiantePrograma($estudiante) {
		return $this->miEstudiante->consultarEstudiantePrograma ( $estudiante );
	}
	function borrarEstudiantePrograma($estudiante) {
		return $this->miEstudiante->borrarEstudiantePrograma ( $estudiante );
	}
	function borrarEstudianteProgramaPeriodoTodos($annio, $semestre) {
		return $this->miEstudiante->borrarEstudianteProgramaPeriodoTodos ( $annio, $semestre );
	}
	function registrarEstudiantePrograma($estudiante) {
		return $this->miEstudiante->registrarEstudiantePrograma ( $estudiante );
	}
	
	// MATRICULADO
	function borrarMatriculado($estudiante) {
		return $this->miEstudiante->borrarMatriculado ( $estudiante );
	}
	function borrarMatriculadoPeriodoTodos($annio, $semestre) {
		return $this->miEstudiante->borrarMatriculadoPeriodoTodos ( $annio, $semestre );
	}
	function registrarMatriculado($estudiante, $annio, $semestre) {
		return $this->miEstudiante->registrarMatriculado ( $estudiante, $annio, $semestre );
	}
	
	// EGRESADO SNIES
	function borrarEgresado($estudiante) {
		return $this->miEstudiante->borrarEgresado ( $estudiante );
	}
	
	// //GRADUADO SNIES
	function consultarGraduadoAcademica($annio, $semestre) {
		return $this->miEstudiante->consultarGraduadoAcademica ( $annio, $semestre );
	}
	function borrarGraduadoPeriodoTodos($annio, $semestre) {
		return $this->miEstudiante->borrarGraduadoPeriodoTodos ( $annio, $semestre );
	}
	function registrarGraduado($graduado) {
		return $this->miEstudiante->registrarGraduado ( $graduado );
	}
	function borrarGraduado($estudiante) {
		return $this->miEstudiante->borrarGraduado ( $estudiante );
	}
}

