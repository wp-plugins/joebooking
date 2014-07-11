<?php
$apn =& ntsAdminPermissionsManager::getInstance();

switch( $inputAction ){
	case 'display':
		$keys = $apn->getPanelsDetailed();
		$blocks = array();
		$currentKey = '';
		reset( $keys );
		foreach( $keys as $ka )
		{
			if( isset($ka[2]) && $ka[2] )
			{
				$currentKey = $ka[0];
				$currentKey = str_replace( "/", '_', $currentKey );
				$blocks[$currentKey] = array( $ka[1], array() );
			}
			else
			{
				$blocks[$currentKey][1][] = $ka;
			}
		}
?>
<?php foreach( $blocks as $bak => $ba ) : ?>
	<div class="nts-toggle-container">
	<h3><?php echo $ba[0]; ?></h3>

<?php	foreach( $ba[1] as $ka ) : ?>
<?php
			$thisValue = in_array($ka[0], $conf['value']) ? 0 : 1;
			echo $this->makeInput(
				'checkbox',
				array(
					'id'	=> $conf['id'] . '-' . $ka[0],
					'value'	=> $thisValue,
					)
				);
?>
<?php		echo $ka[1]; ?>&nbsp;
<?php	endforeach; ?>

<?php	if( count($ba[1]) > 1 ) : ?>
<a href="#" class="nts-toggler"><?php echo M('Toggle All'); ?></a>
<?php	endif; ?>
	</div>
<?php endforeach; ?>

<?php
		break;

	case 'submit':
		$input = array();
		$keys = $apn->getPanels();
		foreach( $keys as $k ){
			$isAllowed = $_NTS['REQ']->getParam( $handle . '-' . $k );
			if( ! $isAllowed )
				$input[] = $k;
			}
		break;

	case 'check_submit':
		$input = true;
		break;
	}
?>
