document.writeln('<div class="hc" id="hc-container"></div>');
jQuery(document).ready(function(){
	jQuery.get( hc_target, function(data) {
		jQuery('#hc-container').html(data);
	});
});