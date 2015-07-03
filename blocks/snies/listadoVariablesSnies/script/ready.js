<?php
$_REQUEST ['tiempo'] = time ();
$this->site = $this->miConfigurador->getVariableConfiguracion ( "site" );

?>

$('#example').DataTable({
        	  	"jQueryUI": true,
        	  
				"language": {				
					"url":  "<?php echo $this->host.$this->site.'/plugin/scripts/javascript/datatables.esp.txt';?>"      																	    
  				}
        	          	               
        	});
        	        	
 