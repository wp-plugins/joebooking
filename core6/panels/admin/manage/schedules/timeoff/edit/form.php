<?php
$ress = ntsLib::getVar( 'admin::ress' );
$minStart = NTS_TIME_STARTS;
$maxEnd = NTS_TIME_ENDS;
?>

<?php if( $conflicts ) : ?>
	<dl class="dl-horizontal">
		<dt class="text-danger">
			<i class="fa fa-fw fa-exclamation-circle"></i><?php echo M('Appointments'); ?>
		</dt>
		<dd>
			<ul class="list-unstyled">
			<?php foreach( $conflicts as $c ) : ?>
				<li>
					<?php echo $c->statusLabel('&nbsp;'); ?>&nbsp;
					<a target="_blank" class="nts-no-ajax" href="<?php echo ntsLink::makeLink('admin/manage/appointments/edit/overview', '', array('_id' => $c->getId())); ?>">
						<?php echo ntsView::objectTitle($c); ?>
					</a>
				</li>
			<?php endforeach; ?>
			</ul>
		</dd>
	</dl>
	<hr>
<?php endif; ?>

<?php if( count($ress) > 1 ) : ?>
	<?php
	$obj = ntsObjectFactory::get( 'resource' );
	$obj->setId( $this->getValue('resource_id') );
	$objView = ntsView::objectTitle( $obj, TRUE );
	echo ntsForm::wrapInput(
		M('Bookable Resource'),
		$objView
		);
	?>
<?php endif; ?>

<?php
echo ntsForm::wrapInput(
	M('From'),
	$this->makeInput(
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
	/* type */
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

<?php echo $this->makePostParams('-current-', 'update'); ?>
<?php
$deleteLink = ntsLink::makeLink(
	'-current-/delete'
	);
echo ntsForm::wrapInput(
	'',
	array(
		'<ul class="list-inline">',
			'<li>',
				'<INPUT class="btn btn-default" TYPE="submit" VALUE="' . M('Update') . '">',
			'</li>',
			'<li class="divider">&nbsp;</li>',
			'<li>',
				'<a href="' . $deleteLink . '" class="btn btn-danger btn-sm" title="' . M('Delete') . '">' . M('Delete') . '</a>',
			'</li>',
		'</ul>'
		)
	);
?>