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
}

