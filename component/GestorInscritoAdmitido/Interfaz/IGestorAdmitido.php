<?php

namespace snies;

interface IGestorAdmitido {
	function contarAdmitidos($periodo);
	function consultarAdmitidoAcademica($annio, $semestre);
	function borrarAdmitidoSnies($annio, $semestre);
	function insertarAdmitidoSnies($admitido);

}

?>