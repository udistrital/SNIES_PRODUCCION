<?php

namespace sniesInscritoAdmitido;

interface IGestorInscrito {
	function consultarInscritoPregadoAcademica($annio, $semestre);
	function consultarInscritoPostgradoAcademica($annio, $semestre);	
	function consultarInscritoSnies(); // No se implementa en esta fase
	function insertarInscritoSnies($inscrito);
	function insertarInscritoProgramaSnies($inscrito);
	function actualizarInscritoSnies(); // No se implementa en esta fase
	function borrarInscritoSnies($annio, $semestre);
	function borrarInscritoProgramaSnies($annio, $semestre);
	function contarInscritos($annio, $semestre);
}

?>



