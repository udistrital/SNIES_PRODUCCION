<?php

namespace sniesDocente;

interface IGestorDocente {
	function consultarDocenteAcademica($annio, $semestre);
	function consultarVinculacionDocente($annio, $semestre);
	
	// PARTICIPANTE SNIES
	function consultarParticipante($docente);
	function actualizarParticipante($docente);
	function registrarParticipante($docente);
	function borrarParticipante($docente);
	
	// DOCENTE SNIES
	function consultarDocente($docente);
	function actualizarDocente($docente);
	function registrarDocente($docente);
	function borrarDocente($docente);
	
	// DOCENTE_H SNIES
	function consultarDocente_h($docente);
	function actualizarDocente_h($docente);
	function registrarDocente_h($docente);
	function borrarDocente_h($docente);
}

?>