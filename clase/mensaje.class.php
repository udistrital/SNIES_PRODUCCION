<?	
class mensaje
{

	function mensaje($id_pagina,$configuracion)
	{
		
		$this->el_mensaje=strtolower($id_pagina);
				
		if(!file_exists ($configuracion["raiz_documento"].$configuracion["bloques"]."/mensaje/".$this->el_mensaje.".php"))
		{
			$this->el_mensaje="fallo_temporal";	
		}		

		include_once($configuracion["raiz_documento"].$configuracion["bloques"]."/mensaje/".$this->el_mensaje.".php");
	}
	
	
	
	
}	
?>
