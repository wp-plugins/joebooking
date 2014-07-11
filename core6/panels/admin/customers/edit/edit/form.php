<?php
$object = ntsLib::getVar( 'admin/customers/edit::OBJECT' );

$uif =& ntsUserIntegratorFactory::getInstance();
$integrator =& $uif->getIntegrator();

$id = $this->getValue('id');
/* form params - used later for validation */
$this->setParams(
	array(
		'myId'	=> $id,
		)
	);

$className = 'customer';

$om =& objectMapper::getInstance();
$fields = $om->getFields( $className, 'internal' );
reset( $fields );

/* status */
list( $alert, $cssClass, $message ) = $object->getStatus();
$class = $alert ? 'alert' : 'ok';
$restrictions = $object->getProp( '_restriction' );
?>

<?php foreach( $fields as $f ) : ?>
	<?php
	if( $f[0] == 'username' )
	{
		continue;
	}
	?>

	<?php $c = $om->getControl( 'customer', $f[0], false ); ?>
	<?php
	$ri = ntsLib::remoteIntegration();
	if( ($ri == 'wordpress') && ($c[2]['id'] == 'username') )
	{
		$c[1] = 'labelData';
	}

	if( NTS_ALLOW_NO_EMAIL && ($c[2]['id'] == 'email') )
	{
		$c[2]['after']	= '';
		$c[2]['after']	.= '<div class="checkbox">';
		$c[2]['after']		.= '<label>';
		$c[2]['after']		.= $this->makeInput (
								/* type */
									'checkbox',
								/* attributes */
									array(
										'id'	=> 'noEmail',
										)
									);
		$c[2]['after']		.= ' ' . M('No Email');
		$c[2]['after']		.= '</label>';
		$c[2]['after']	.= '</div>';
	}

	if( NTS_ALLOW_DUPLICATE_EMAILS && ($c[2]['id'] == 'email') )
	{
		// check if there're duplicates
		$checkEmail = $this->getValue('email');
		$countDuplicates = 0;
		if( strlen($checkEmail) )
		{
			$myWhere = array();
			$myWhere['email'] = array('=', $checkEmail);
			$myWhere['id'] = array('<>', $id);
			$countDuplicates = $integrator->countUsers( $myWhere );
		}
		if( $countDuplicates )
		{
			if( ! isset($c[2]['after']) )
				$c[2]['after'] = '';
			$c[2]['after']	.= '<div>';
			$c[2]['after']	.= 'Also <a class="nts-no-ajax" target="_blank" href=" ' . ntsLink::makeLink('admin/customers/browse', '', array('search' => $checkEmail) ) . '">';
			$c[2]['after']	.= $countDuplicates . ' other user(s)</a> with this email';
			$c[2]['after']	.= '</a>';
			$c[2]['after']	.= '</div>';
		}
	}
	?>
	<?php
	echo ntsForm::wrapInput(
		$c[0],
		$this->buildInput (
			$c[1],
			$c[2],
			$c[3]
			)
		);
	?>
<?php endforeach; ?>

<?php if( NTS_ENABLE_TIMEZONES > 0 ) : ?>
	<?php
	$timezoneOptions = ntsTime::getTimezones();
	?>
	<?php
	echo ntsForm::wrapInput(
		M('Timezone'),
		$this->buildInput (
		/* type */
			'select',
		/* attributes */
			array(
				'id'		=> '_timezone',
				'options'	=> $timezoneOptions,
				)
			)
		);
	?>
<?php endif; ?>

<?php
$lm =& ntsLanguageManager::getInstance();
$languages = $lm->getActiveLanguages();
$currentLanguage = $object->getLanguage();
?>
<?php if( count($languages) > 1 ) : ?>
	<?php
	$languageOptions = array();
	reset( $languages );
	foreach( $languages as $lng )
	{
		$languageOptions[] = array( $lng, $lng );
	}
	?>
	<?php
	echo ntsForm::wrapInput(
		M('Language'),
		$this->buildInput (
		/* type */
			'select',
		/* attributes */
			array(
				'id'		=> '_lang',
				'options'	=> $languageOptions,
				)
			)
		);
	?>
<?php endif; ?>

<?php echo $this->makePostParams('-current-', 'update' ); ?>

<?php
$buttons = array();
$buttons[] =	'<ul class="list-inline">';
$buttons[] =		'<li>';
$buttons[] =			'<INPUT class="btn btn-success" TYPE="submit" VALUE="' . M('Update') . '">';
$buttons[] =		'</li>';
//$buttons[] =		'<li class="divider">&nbsp;</li>';
$buttons[] =	'</ul>';

echo ntsForm::wrapInput(
	'',
	$buttons
	);
?>

<?php if( NTS_ALLOW_NO_EMAIL ) : ?>
<script language="JavaScript">
jQuery(document).ready( function(){
	if( jQuery("#<?php echo $this->getName(); ?>noEmail").is(":checked") ){
		jQuery("#<?php echo $this->getName(); ?>email").hide();
		}
	else {
		jQuery("#<?php echo $this->getName(); ?>email").show();
		}
	});
jQuery("#<?php echo $this->getName(); ?>noEmail").live( 'click', function(){
	jQuery("#<?php echo $this->getName(); ?>email").toggle();
	});
</script>
<?php endif; ?>