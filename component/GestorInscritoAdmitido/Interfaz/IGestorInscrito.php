<?php

namespace sniesInscritoAdmitido;

interface IGestorInscrito {
	function consultarInscritoAcademica($annio, $semestre);
	function consultarInscritoPregadoAcademica($annio, $semestre);
	function consultarInscritoPostgradoAcademica($annio, $semestre);	
	function consultarInscritoSnies($annio, $semestre);
	function consultarInscritoProgramaSnies($annio, $semestre);
	function insertarInscritoSnies($inscrito);
	function insertarInscritoProgramaSnies($inscrito);
	function actualizarInscritoSnies(); // No se implementa en esta fase
	function borrarInscritoSnies($inscrito);
	function borrarInscritoProgramaSnies($annio, $semestre);
	function contarInscritos($annio, $semestre);
}

?>



