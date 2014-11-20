<?php
$conf =& ntsConf::getInstance();
$commonHeader = $conf->get('emailCommonHeader');
$commonFooter = $conf->get('emailCommonFooter');

$key = $this->getValue('key');

/* templates manager */
$tm =& ntsEmailTemplateManager::getInstance();

/* language options */
$lm =& ntsLanguageManager::getInstance();
$languageOptions = array();
$languages = $lm->getActiveLanguages();
foreach( $languages as $lo ){
	$lConf = $lm->getLanguageConf( $lo );
	if( $lo == 'en-builtin' ){
		$lo = 'en';
		$lConf['language'] = 'English';
		}
	$languageOptions[] = array( $lo, $lConf['language'] );
	}

/* tags */
$tags = $tm->getTags( $key );

/* service options */
$serviceOptions = array();

if( substr($key, 0, strlen('appointment-')) == 'appointment-' )
{
	// offer service options
	$services = ntsObjectFactory::getAll( 'service' );
	if( count($services) > 1 )
	{
		$serviceOptions[] = array( 0, ' - ' . M('All') . ' - ' );
		reset( $services );
		foreach( $services as $s )
		{
			$serviceTitle = ntsView::objectTitle($s);
			$tailoredKey = $key . '_' . $s->getId();
			// check if tailored version exists
			$tailoredTemplate = $tm->getTemplate( $NTS_VIEW['lang'], $tailoredKey );
			if( $tailoredTemplate )
			{
				$serviceTitle .= ' (*)';
			}
			$serviceOptions[] = array( $s->getId(), $serviceTitle );
		}
	}
}
?>

<?php if( $serviceOptions ) : ?>
	<?php
	echo ntsForm::wrapInput(
		M('Service'),
		$this->buildInput (
		/* type */
			'select',
		/* attributes */
			array(
				'id'		=> 'service',
				'options'	=> $serviceOptions,
				'attr'		=> array (
					'onChange'	=> "document.location.href='" . ntsLink::makeLink('-current-', '', array('key' => $this->getValue('key'), 'lang' => $this->getValue('lang')) ) . "&nts-service=' + this.value",
					),
				)
			)
		);
	?>
<?php endif; ?>

<?php if( count($languageOptions) > 1 ) : ?>
	<?php
	echo ntsForm::wrapInput(
		M('Language'),
		$this->buildInput (
		/* type */
			'select',
		/* attributes */
			array(
				'id'		=> 'lang',
				'options'	=> $languageOptions,
				'attr'		=> array (
					'onChange'	=> "document.location.href='" . ntsLink::makeLink('-current-', '', array('key' => $this->getValue('key')) ) . "&nts-lang=' + this.value",
					),
				)
			)
		);
	?>
<?php endif; ?>

<?php
echo ntsForm::wrapInput(
	M('Subject'),
	$this->buildInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'subject',
			'attr'		=> array(
				'size'	=> 48,
				),
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


<p>
<TABLE>

<tr>
	<TD>
	</TD>

<td style="vertical-align: top;" rowspan="3">
<?php foreach( $tags as $t ) : ?>
	<?php echo $t; ?><br>
<?php endforeach; ?>
</td>

</TR>

<TR>
	<TH style="vertical-align: top;"><?php echo M('Message'); ?> *</TH>
</tr>
<tr>
	<TD>
	<a href="<?php echo ntsLink::makeLink('admin/conf/email_settings'); ?>"><?php echo M('Header'); ?>: <?php echo M('Edit'); ?></a>
	<br>
	<?php echo $commonHeader; ?>
	<br>
	<?php
	echo $this->makeInput (
	/* type */
		'textarea',
	/* attributes */
		array(
			'id'		=> 'body',
			'attr'		=> array(
				'cols'	=> 56,
				'rows'	=> 16,
				),
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Required'),
				),
			)
		);
	?>
	<br>
	<?php echo nl2br($commonFooter); ?>
	<br>
	<a href="<?php echo ntsLink::makeLink('admin/conf/email_settings'); ?>"><?php echo M('Footer'); ?>: <?php echo M('Edit'); ?></a>
	</TD>	

</TR>

<tr>
<td colspan="2">
<?php echo $this->makePostParams('-current-', 'save', array('key' => $key)); ?>
<input class="btn btn-default" type="submit" value="<?php echo M('Save'); ?>">
&nbsp; <a href="<?php echo ntsLink::makeLink('-current-', 'reset', array('lang' => $NTS_VIEW['lang'], 'key' => $NTS_VIEW['key']) ); ?>"><?php echo M('Reset To Defaults'); ?></a>
</td>
</tr>
</table>