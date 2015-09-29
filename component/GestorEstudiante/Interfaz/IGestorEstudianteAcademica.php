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
	function consultarEstudianteAcademica($annio, $semestre);
	
	/**
	 * Consulta el registro para un codigo_unico de estudiante
	 *
	 * @param unknown $estudiante        	
	 */
	function cosultarParticipante($estudiante);
	function actualizarParticipante($estudiante);
	
	/**
	 * Registra los datos del estudiantes en la tabla PARTICIPANTE del SNIES
	 *
	 * @param array $estudiante        	
	 */
	function registrarParticipante($estudiante);
	
	/**
	 * Borra un registro de la tabla PARTICIPANTE del SNIES
	 *
	 * @param array $estudiante        	
	 */
	function borrarParticipanteEstudiante($estudiante);
	
	// //ESTUDIANTE SNIES
	function consultarEstudiante($estudiante);
	function actualizarEstudiante($estudiante);
	function registrarEstudiante($estudiante);
	
	// ///ESTUDIANTE_PROGRAMA SNIES
	function consultarEstudiantePrograma($estudiante);
	function borrarEstudiantePrograma($annio, $semestre);
	function registrarEstudiantePrograma($estudiante);
	
	// MATRICULADO
	function borrarMatriculado($annio, $semestre);
	function registrarMatriculado($estudiante, $annio, $semestre);
}

?>