<?php

namespace snies;

interface IGestorMatriculado {
	function contarMatriculados($periodo);
	/**
	 * Consulta los datos de los estudiantes requeridos en la
	 * tabla participante de SNIES LOCAL
	 * @param unknown $periodo
	 */
	function consultarParticipanteEstudiante($annio, $semestre);
}

?>