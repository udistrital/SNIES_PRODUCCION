<?php

namespace sniesDocente;

interface IGestorDocente {
	function consultarDocenteAcademica($annio, $semestre);
	function consultarVinculacionDocente($annio, $semestre);
	
	//PARTICIPANTE SNIES
	function consultarParticipante($docente);
	function actualizarParticipante($docente);
	function registrarParticipante($docente);
	function borrarParticipante($docente);
}

?>