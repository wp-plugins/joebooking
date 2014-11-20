<?php
$ress = ntsLib::getVar( 'admin::ress' );
$ress_archive = ntsLib::getVar( 'admin::ress_archive' );
if( $ress_archive )
{
	$ress = array_diff( $ress, $ress_archive );
	$ress = array_values( $ress );
}

$minStart = NTS_TIME_STARTS;
$maxEnd = NTS_TIME_ENDS;
?>

<?php if( count($ress) == 1 ) : ?>
	<?php
	echo $this->makeInput (
	/* type */
		'hidden',
	/* attributes */
		array(
			'id'	=> 'resource_id',
			'value'	=> $ress[0],
			)
		);
	?>
<?php else : ?>
	<?php
	$options = array();
	reset( $ress );
	foreach( $ress as $objId )
	{
		$obj = ntsObjectFactory::get( 'resource' );
		$obj->setId( $objId );
		$options[] = array( $objId, ntsView::objectTitle($obj) );
	}
	?>
	<?php
	echo ntsForm::wrapInput(
		M('Bookable Resource'),
		$this->buildInput(
		/* type */
			'select',
		/* attributes */
			array(
				'id'		=> 'resource_id',
				'options'	=> $options,
				)
			)
		);
	?>
<?php endif; ?>

<?php
echo ntsForm::wrapInput(
	M('From'),
	$this->makeInput (
	/* type */
		'date/Calendar',
	/* attributes */
		array(
			'id'		=> 'starts_at_date',
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Required'),
				),
			)
		) . ' ' . 
	$this->makeInput (
	/* type */
		'date/Time',
	/* attributes */
		array(
			'id'		=> 'starts_at_time',
			'conf'	=> array(
				'min'	=> $minStart,
				'max'	=> $maxEnd,
				),
			'default'	=> $minStart
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Required'),
				),
			)
		)
	);
?>

<?php
echo ntsForm::wrapInput(
	M('To'),
	$this->makeInput (
	/* type */
		'date/Calendar',
	/* attributes */
		array(
			'id'		=> 'ends_at_date',
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Required'),
				),
			array(
				'code'		=> 'greaterThan.php', 
				'error'		=> M('The end time should be after the start'),
				'params'	=> array(
					'compareFields'	=> array(
						array('ends_at_date', 'starts_at_date'),
						array('ends_at_time', 'starts_at_time'),
						)
					),
				),
			)
		) . ' ' . 
	$this->makeInput (
	/* type */
		'date/Time',
	/* attributes */
		array(
			'id'		=> 'ends_at_time',
			'conf'	=> array(
				'min'	=> $minStart,
				'max'	=> $maxEnd,
				),
			'default'	=> $maxEnd
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Required'),
				),
			)
		)
	);
?>

<?php
echo ntsForm::wrapInput(
	M('Description'),
	$this->buildInput(
		'textarea',
	/* attributes */
		array(
			'id'		=> 'description',
			'attr'		=> array(
				'cols'	=> 20,
				'rows'	=> 6,
				),
			),
	/* validators */
		array(
			)
		)
	);
?>

<?php echo $this->makePostParams('-current-', 'create' ); ?>
<?php
echo ntsForm::wrapInput(
	'',
	'<INPUT class="btn btn-default" TYPE="submit" VALUE="' . M('Add') . '">'
	);
?>