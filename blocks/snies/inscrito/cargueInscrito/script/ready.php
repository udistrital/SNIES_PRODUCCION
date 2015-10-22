
$('#example').DataTable({
        	  	"jQueryUI": true,
        	  	"order": [[ 1, "desc" ],[ 2, "desc" ]],//las columnas inician de cero
        	  	
				"language": {				
					"url":  "<?php echo $this->host.$this->site.'/plugin/scripts/javascript/datatables.esp.txt';?>" 
				
				
  				}
        	          	               
        	});