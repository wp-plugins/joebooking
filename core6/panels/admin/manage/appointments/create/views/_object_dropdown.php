<?php
if( $link ){
	$dropdown = array();
	if( $errors ){
		foreach( $errors as $e ){
			$dropdown[] = '<i class="fa-fw fa fa-exclamation-circle text-danger"></i>' . $e;
		}
		$dropdown[] = '-divider-';
		$dropdown[] = array(
			'href'	=> $select_link,
			'title'	=> '<i class="fa fa-check"></i>' . M('Proceed Anyway'),
			'class'	=> 'nts-no-ajax',
			);
	}
}
?>