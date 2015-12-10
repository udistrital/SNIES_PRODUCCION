<?php

namespace sniesDocente;

interface IGestorDocente {
	function consultarDocenteAcademica($annio, $semestre);
	function consultarVinculacionDocente($annio, $semestre);
}

?>