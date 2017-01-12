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
	function registrarDocenteContrato($docente, $annio, $semestre);
	function borrarDocente_h($docente);
	function borrarDocenteContrato($annio, $semestre);
	function consultarDocenteDoctoradoMaestria($annio, $semestre);
	function borrarDocenteDoctoradoMaestriaTodos($annio, $semestre);
	function registrarDocenteDoctoradoMaestria($docente);
}

?>