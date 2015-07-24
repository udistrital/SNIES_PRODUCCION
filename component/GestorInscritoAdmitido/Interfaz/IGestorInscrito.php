<?php

namespace snies;

interface IGestorInscrito {
	function consultarInscritoPregadoAcademica($annio, $semestre);
	function consultarInscritoSnies();//No se implementa en esta fase
	function insertarInscritoSnies($inscrito);
	function actualizarInscritoSnies();//No se implementa en esta fase
	function borrarInscritoSnies($annio, $semestre);
	function contarInscritos($periodo);
}

?>



