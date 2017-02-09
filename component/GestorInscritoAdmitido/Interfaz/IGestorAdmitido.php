<?php

namespace sniesInscritoAdmitido;

interface IGestorAdmitido {
	function consultarAdmitidoSnies($annio, $semestre);
	function consultarAdmitidoPregradoAcademica($annio, $semestre);
	function consultarAdmitidoPostgradoAcademica($annio, $semestre);
	function borrarAdmitidoSnies($annio, $semestre);
	function insertarAdmitidoSnies($admitido);

}

?>