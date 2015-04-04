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

$btn = HC_Html_Factory::element('button')
	->add_attr('type', 'submit')
	->add_attr('class', array('btn', 'btn-default'))
	->add_attr('title', M('Search'))
	->add_child(HC_Html::icon('search'))
	;
echo $btn->render();
?>