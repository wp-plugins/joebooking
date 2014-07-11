<?php
$per_row = 4;
$col_style = '';
$row_class = '';
$col_class = '';

switch( $per_row )
{
	case 0:
		$row_class = 'list-inline';
		$col_style = 'width: 6em;';
		break;
	case 6:
		$row_class = 'row';
		$col_class = 'col-lg-2 col-md-3 col-sm-6 col-xs-6';
		break;
	case 3:
		$row_class = 'row';
		$col_class = 'col-lg-4 col-md-6 col-sm-6 col-xs-12';
		break;
	case 4:
	default:
		$row_class = 'row';
		$col_class = 'col-lg-3 col-md-4 col-sm-6';
		break;
}

$to_select = array();
$to_show = array();

if( ! NTS_SINGLE_LOCATION )
{
	if( count($locations) > 1 )
		$to_select[] = 'location';
	else
		$to_show[] = 'location';
}

if( ! NTS_SINGLE_RESOURCE )
{
	if( count($resources) > 1 )
		$to_select[] = 'resource';
	else
		$to_show[] = 'resource';
}

if( count($services) > 1 )
	$to_select[] = 'service';
else
	$to_show[] = 'service';

if( $requested['time'] )
	$to_show[] = 'time';
else
	$to_select[] = 'time';
?>

<?php foreach( $to_show as $sh ) : ?>
	<?php require( dirname(__FILE__) . '/_index_selector_' . $sh . '.php' ); ?>
<?php endforeach; ?>

<?php for( $ii = 0; $ii < count($to_select); $ii++ ) : ?>
	<?php
	$sh = $to_select[$ii];
	$this_collapse = $ii ? '' : ' in';
	?>
	<?php require( dirname(__FILE__) . '/_index_selector_' . $sh . '.php' ); ?>
<?php endfor; ?>