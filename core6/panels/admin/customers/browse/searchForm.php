<?php
echo $this->makeInput (
/* type */
	'text',
/* attributes */
	array(
		'id'	=> 'search_for',
		'attr'		=> array(
			'size'	=> 12,
			),
		)
	);
?>
<?php 
$params = array();
$params[NTS_PARAM_VIEW_MODE] = $NTS_VIEW[NTS_PARAM_VIEW_MODE];
echo $this->makePostParams('-current-', 'search', $params);
?>
<INPUT TYPE="submit" class="btn btn-default" VALUE="<?php echo M('Search'); ?>">