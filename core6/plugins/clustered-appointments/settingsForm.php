<?php
$plugin = 'clustered-appointments';
$new = $_NTS['REQ']->getParam( 'new' );
?>
<?php
$gapOptions = array();
$gapOptions[] = array(0, M('No'));
for( $ii = 1; $ii <= 20; $ii++ )
{
	$sec = $ii * NTS_TIME_UNIT * 60;
	$gapOptions[] = array($sec, ntsTime::formatPeriod( $sec ));
}
?>
<?php
echo ntsForm::wrapInput(
	'Max free time before an existing appointment',
	$this->buildInput(
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'gap_before',
			'options'	=> $gapOptions
			),
	/* validators */
		array(
			)
		)
	);
?>

<?php
echo ntsForm::wrapInput(
	'Max free time after an existing appointment',
	$this->buildInput(
	/* type */
		'select',
	/* attributes */
		array(
			'id'		=> 'gap_after',
			'options'	=> $gapOptions
			),
	/* validators */
		array(
			)
		)
	);
?>