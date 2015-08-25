<?php

namespace sniesEstudiante;

interface IGestorEstudiante {
	function contarMatriculados($periodo);
	/**
	 * Consulta los datos de los estudiantes requeridos en la
	 * tabla participante de SNIES LOCAL
	 *
	 * @param unknown $periodo        	
	 */
	function consultarParticipanteEstudiante($annio, $semestre);
	
	/**
	 * Registra los datos del estudiantes en la tabla PARTICIPANTE del SNIES
	 * @param array $estudiante
	 */
	function registrarParticipanteEstudiante($estudiante);
	
	/**
	 * Borra un registro de la tabla PARTICIPANTE del SNIES
	 * @param array $estudiante 
	 */
	function borrarParticipanteEstudiante($estudiante);
}

?>