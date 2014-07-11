<H2><?php echo ucfirst($NTS_VIEW['plugin']); ?> Settings</H2>
<?php
$plugin = $NTS_VIEW['plugin'];
$new = $NTS_VIEW['new'];

$plm =& ntsPluginManager::getInstance();
$defaults = $plm->getPluginSettings( $plugin );

$defaults['plugin'] = $plugin;
$defaults['new'] = $new;

$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form';
$form =& $ff->makeForm( $formFile, $defaults );

$form->display();
?>