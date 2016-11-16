<?php

namespace sniesInscritoAdmitido;

interface IGestorInscrito {
	function consultarInscritoPregadoAcademica($annio, $semestre);
	function consultarInscritoPostgradoAcademica($annio, $semestre);	
	function consultarInscritoSnies($annio, $semestre);
	function consultarInscritoProgramaSnies($annio, $semestre);
	function insertarInscritoSnies($inscrito);
	function insertarInscritoProgramaSnies($inscrito);
	function actualizarInscritoSnies(); // No se implementa en esta fase
	function borrarInscritoSnies($annio, $semestre);
	function borrarInscritoProgramaSnies($annio, $semestre);
	function contarInscritos($annio, $semestre);
}

?>



