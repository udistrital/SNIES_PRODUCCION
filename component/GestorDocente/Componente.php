<?php

namespace sniesDocente;

include_once 'component/Component.class.php';
use component\Component;

require_once ('component/GestorDocente/Sql.class.php');
require_once ('component/GestorDocente/Clase/GestorDocente.class.php');
require_once ('component/GestorDocente/Interfaz/IGestorDocente.php');
class Componente extends Component implements IGestorDocente {
	private $miSql;
	
	// El componente actua como Fachada
	
	/**
	 */
	public function __construct() {
		$this->miDocente = new docente ();
	}
	function consultarDocenteAcademica($annio, $semestre) {
		return $this->miDocente->consultarDocenteAcademica ( $annio, $semestre );
	}
	function consultarVinculacionDocente($annio, $semestre) {
		return $this->miDocente->consultarVinculacionDocente ( $annio, $semestre );
	}
	
	// ///PARTICIPANTE SNIES
	function consultarParticipante($docente) {
		return $this->miDocente->consultarParticipante ( $docente );
	}
	function actualizarParticipante($docente) {
		return $this->miDocente->actualizarParticipante ( $docente );
	}
	function registrarParticipante($docente) {
		return $this->miDocente->registrarParticipante ( $docente );
	}
	function borrarParticipante($docente) {
		return $this->miDocente->borrarParticipante ( $docente );
	}
	
	// DECENTE SNIES
	function consultarDocente($docente) {
		return $this->miDocente->consultarDocente ( $docente );
	}
	function actualizarDocente($docente) {
		return $this->miDocente->actualizarDocente ( $docente );
	}
	function registrarDocente($docente) {
		return $this->miDocente->registrarDocente ( $docente );
	}
	function borrarDocente($docente) {
		return $this->miDocente->borrarDocente ( $docente );
	}
	// DECENTE SNIES
	function consultarDocente_h($docente) {
		return $this->miDocente->consultarDocente_h ( $docente );
	}
	function actualizarDocente_h($docente) {
		return $this->miDocente->actualizarDocente_h ( $docente );
	}
	function registrarDocente_h($docente, $annio, $semestre) {
		return $this->miDocente->registrarDocente_h ( $docente, $annio, $semestre );
	}
	function borrarDocente_h($docente) {
		return $this->miDocente->borrarDocente_h ( $docente );
	}
	function borrarDocente_hPeriodoTodos($annio, $semestre) {
		return $this->miDocente->borrarDocente_hPeriodoTodos ( $annio, $semestre );
	}
	function consultarDocenteDoctoradoMaestria($annio, $semestre) {
		return $this->miDocente->consultarDocenteDoctoradoMaestria ( $annio, $semestre );
	}
	function borrarDocenteDoctoradoMaestriaTodos($annio, $semestre) {
		return $this->miDocente->borrarDocenteDoctoradoMaestriaTodos ( $annio, $semestre );
	}
	function registrarDocenteDoctoradoMaestria($docente) {
		return $this->miDocente->registrarDocenteDoctoradoMaestria ( $docente );
	}
}

