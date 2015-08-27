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


$(".miEnlace" ).click(function() {
	
	var progressbar = $( "#progressbar" ),
	progressLabel = $( ".progress-label" );
	progressbar.progressbar({
	value: false,	
	change: function() {
	progressLabel.text( progressbar.progressbar( "value" ) + "%" );
	},
	complete: function() {
	progressLabel.text( "Complete!" );
	}
	});
	
	function progress() {
		var val = progressbar.progressbar( "value" ) || 0;
		progressbar.progressbar( "value", val + 2 );
		if ( val < 99 ) {
			setTimeout( progress, 80 );
		}
	}
	setTimeout( progress, 2000 );
	
});