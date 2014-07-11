<?php
require( NTS_APP_DIR . '/panels/admin/conf/email_templates/_keys.php' );

// find title
$keyTitle = '';
reset( $matrix );
foreach( $matrix as $class => $classArray ){
	reset( $classArray );
	foreach( $classArray as $to => $toArray ){
		reset( $toArray );
		foreach( $toArray as $keyArray ){
			if( $keyArray[0] == $NTS_VIEW['key'] ){
				$keyTitle = $keyArray[1];
				break;
				}
			}
		}
	}
?>
<div class="page-header">
	<h2>
		<small><?php echo M('Email Notifications Templates'); ?></small>
		<br><?php echo $keyTitle; ?>
	</h2>
</div>

<?php
$ff =& ntsFormFactory::getInstance();
$form =& $ff->makeForm( dirname(__FILE__) . '/form' );
$form->display();
?>