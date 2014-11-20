<?php
$plugin = 'limits';
$new = $_NTS['REQ']->getParam( 'new' );
?>

<?php
echo ntsForm::wrapInput(
	'Max number of appointments per customer',
	$this->buildInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'max',
			'attr'		=> array(
				'size'	=> 4,
				),
			'help'	=> 'Set to 0 to disable any restrictions'
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Required'),
				),
			)
		)
	)
?>

<?php
echo ntsForm::wrapInput(
	'During',
	$this->buildInput (
	/* type */
		'period/DayWeekMonthYear',
	/* attributes */
		array(
			'id'		=> 'per',
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Required'),
				),
			)
		)
	)
?>