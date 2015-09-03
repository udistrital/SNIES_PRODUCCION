<?php

namespace snies;

interface IGestorAdmitido {
	function contarAdmitidos($periodo);
	function consultarAdmitidoPregradoAcademica($annio, $semestre);
	function consultarAdmitidoPostgradoAcademica($annio, $semestre);
	function borrarAdmitidoSnies($annio, $semestre);
	function insertarAdmitido($admitido);

}

?>