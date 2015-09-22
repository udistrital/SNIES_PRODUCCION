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
	 * Consulta el registro para un codigo_unico de estudiante
	 * @param unknown $estudiante
	 */
	function cosultarParticipante($estudiante);
	
	/**
	 * Registra los datos del estudiantes en la tabla PARTICIPANTE del SNIES
	 *
	 * @param array $estudiante        	
	 */
	function registrarParticipanteEstudiante($estudiante);
	
	/**
	 * Borra un registro de la tabla PARTICIPANTE del SNIES
	 *
	 * @param array $estudiante        	
	 */
	function borrarParticipanteEstudiante($estudiante);
	
	/**
	 * Consultar todos los estudiantes de la Universidad tipo documento y número
	 */
	function consultarEstudiante($annio, $semestre);
	/**
	 * Borra todos los registros de la tabla ESTUDIANTE del SNIES LOCAL
	 */
	function borrarEstudiante($unEstudiante);
	
	/**
	 *
	 * @param unknown $estudiante        	
	 */
	function consultarEstudianteSNIES($estudiante);
}

?>