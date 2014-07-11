<?php
$id = $NTS_VIEW['id'];

$ff =& ntsFormFactory::getInstance();
$confirmForm =& $ff->makeForm( dirname(__FILE__) . '/confirmForm' );
?>
<H2><?php echo M('Are you sure?'); ?></H2>

<p>
<?php $confirmForm->display(); ?>
