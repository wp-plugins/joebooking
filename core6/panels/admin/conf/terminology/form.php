<?php
$defaults = $this->getDefaults();
reset( $defaults );
?>

<p class="text-italic">
<?php echo M('Please enter singular and plural forms for your business items'); ?>
</p>

<?php for( $ii = 0; $ii < count($defaults)/2; $ii++ ) : ?>
	<?php
	$da1 = $defaults[$ii*2];
	$da2 = $defaults[$ii*2+1];
	echo ntsForm::wrapInput(
		M($da1[0], array(), TRUE),
		array(
			'<ul class="list-inline list-separated" style="margin: 0 0;">',
				'<li title="' . M('Singular') . '">',
					$this->buildInput (
					/* type */
						'text',
					/* attributes */
						array(
							'id'		=> 'term-' . ($ii * 2 + 1),
							'attr'		=> array(
								'size'	=> 24,
								),
							'default'	=> $da1[1],
							),
					/* validators */
						array(
							array(
								'code'		=> 'notEmpty.php', 
								'error'		=> M('Required field'),
								),
							)
						),
				'</li>',
				'<li title="' . M('Plural') . '">',
					$this->buildInput (
					/* type */
						'text',
					/* attributes */
						array(
							'id'		=> 'term-' . ($ii * 2 + 2),
							'attr'		=> array(
								'size'	=> 24,
								),
							'default'	=> $da2[1],
							),
					/* validators */
						array(
							array(
								'code'		=> 'notEmpty.php', 
								'error'		=> M('Required field'),
								),
							)
						),
				'</li>',
			'</ul>',
			)
		)
	?>
<?php endfor; ?>


<?php echo $this->makePostParams('-current-', 'update'); ?>
<?php
echo ntsForm::wrapInput(
	'',
	'<input class="btn btn-default" type="submit" value="' . M('Save') . '">'
	);
?>