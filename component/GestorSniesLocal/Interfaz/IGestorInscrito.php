<?php

namespace snies;

interface IGestorInscrito {
	function contarInscritos($periodo);
	function insertarInscrito();
	function borrarInscrito();
	function actualizarInscrito();
}

?>