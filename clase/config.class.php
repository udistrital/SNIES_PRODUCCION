<?
/***************************************************************************
*    Copyright (c) 2004 - 2006 :                                           *
*    UNIVERSIDAD DISTRITAL Francisco Jose de Caldas                        *
*    Comite Institucional de Acreditacion                                  *
*    siae@udistrital.edu.co                                                *
*    Paulo Cesar Coronado                                                  *
*    paulo_cesar@udistrital.edu.co                                         *
*                                                                          *
****************************************************************************
*                                                                          *
*                                                                          *
* SIAE es software libre. Puede redistribuirlo y/o modificarlo bajo los    *
* terminos de la Licencia Publica General GNU tal como la publica la       *
* Free Software Foundation en la versión 2 de la Licencia o, a su eleccion,*
* cualquier version posterior.                                             *
*                                                                          *
* SIAE se distribuye con la esperanza de que sea util, pero SIN NINGUNA    *
* GARANTIA. Incluso sin garantia implicita de COMERCIALIZACION o ADECUACION*
* PARA UN PROPOSITO PARTICULAR. Vea la Licencia Publica General GNU para   *
* mas detalles.                                                            *
*                                                                          *
* Deberia haber recibido una copia de la Licencia publica General GNU junto*
* con SIAE; si esto no ocurrio, escriba a la Free Software Foundation, Inc,*
* 59 Temple Place, Suite 330, Boston, MA 02111-1307, Estados Unidos de     *
* America                                                                  *
*                                                                          *
*                                                                          *
***************************************************************************/
/*Los caracteres tipograficos especificos del Espannol se han omitido      *
* deliberadamente para mantener la compatibilidad con editores que no      *
* soporten la codificacion                                                 *
****************************************************************************/
?><?
/***************************************************************************
     * @name          config.inc.php 
* @author        Paulo Cesar Coronado
* @revision      ultima revision 6 de septiembre de 2006
****************************************************************************
* @subpackage   
* @package	configuracion
* @copyright    
* @version      2.0.0.1
* @author      	Paulo Cesar Coronado
* @link		http://acreditacion.udistrital.edu.co
* @description  Administracion de Parametros globales de configuracion
*
****************************************************************************/

class config
{

	function config()
	{
	
	}

	function variable($ruta="")
	{
		include_once("encriptar.class.php");
		include_once("dbms.class.php");
		
		
		$this->cripto=new encriptar();
		
		$this->fp=fopen($ruta."configuracion/config.inc.php","r");
		if(!$this->fp)
		{
			return false;			
		}
		
		$this->i=0;
		while (!feof($this->fp)) 
		{
			$this->linea= $this->cripto->decodificar(fgets($this->fp, 4096),"");
			$this->i++;
			switch($this->i)
			{
				case 1:
					if($this->linea==1)
					{
						$this->configuracion["dbsys"]= 'mysql';
					}
					break;
				
				case 2:
					$this->configuracion["db_dns"]= $this->linea;
					break;
				case 3:
					$this->configuracion["db_nombre"]= $this->linea;
					break;
				case 4:
					$this->configuracion["db_usuario"]= $this->linea;
					break;	
				case 5:
					$this->configuracion["db_clave"]= $this->linea;
					break;
				case 6:
					$this->configuracion["prefijo"]= $this->linea;
					break;
					
			}			
		}
		fclose ($this->fp);
		
		$this->base=new dbms($this->configuracion);
		$this->enlace=$this->base->conectar_db();
		
		
		
		
		//exit;
		if (is_resource($this->enlace))
		{		
			
			$cadena_sql="SELECT ";
			$cadena_sql.=" `parametro`,  ";
			$cadena_sql.=" `valor`  ";
			$cadena_sql.="FROM ";
			$cadena_sql.=$this->configuracion["prefijo"]."configuracion ";
			//echo $cadena_sql;
			
			$this->total=$this->base->registro_db($cadena_sql,0);			
			if($this->total>0)
			{
				$this->registro=$this->base->obtener_registro_db();
				for($j=0;$j<$this->total;$j++)
				{
					$this->configuracion[$this->registro[$j][0]]=$this->registro[$j][1];
					//echo $this->configuracion[$this->registro[$j][0]]."<br>";
				}
				$this->configuracion["instalado"]=1;
			}
			else
			{
				echo "<h3>ERROR FATAL</h3><br>Imposible rescatar las variables de configuraci&oacute;n.";
				exit;
			
			}
		}
		
		return $this->configuracion;
	}


}


?>
