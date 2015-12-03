<?php

namespace sniesEstudiante;

interface IGestorEstudiante {
	function contarMatriculados($annio, $semestre);
	
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
	function consultarParticipante($estudiante);
	
	/**
	 * NO actualiza el número ni el tipo de documento
	 *
	 * @param unknown $estudiante        	
	 */
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
	function borrarParticipante($estudiante);
	
	// //ESTUDIANTE SNIES
	function consultarEstudiante($estudiante);
	function actualizarEstudiante($estudiante);
	function registrarEstudiante($estudiante);
	function borrarEstudiante($estudiante);
	
	// ///ESTUDIANTE_PROGRAMA SNIES
	function consultarEstudiantePrograma($estudiante);
	function borrarEstudiantePrograma($estudiante);
	function borrarEstudianteProgramaPeriodoTodos($annio, $semestre );
	function registrarEstudiantePrograma($estudiante);
	
	// MATRICULADO
	function borrarMatriculado($estudiante);
	function borrarMatriculadoPeriodoTodos( $annio, $semestre);
	function registrarMatriculado($estudiante, $annio, $semestre);
}

?>