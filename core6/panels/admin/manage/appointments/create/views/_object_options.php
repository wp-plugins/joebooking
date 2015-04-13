<?php
$col_style = '';
$row_class = '';
$col_class = '';

switch( $per_row ){
	case 0:
		$row_class = 'list-inline';
		$col_style = 'width: 6em;';
		break;
	case 6:
		$row_class = 'row';
		$col_class = 'col-lg-2 col-md-3 col-sm-6 col-xs-6';
		break;
	case 4:
	default:
		$row_class = 'row';
		$col_class = 'col-lg-3 col-md-4 col-sm-6';
		break;
}
if( $col_style ){
	$col_style = ' style="' . $col_style . '"';
}

$generic_child_file = dirname(__FILE__) . '/_object_child.php';
$my_child_file = dirname(__FILE__) . '/_' . $obj_class . '_child.php';
$child_file = file_exists($my_child_file) ? $my_child_file : $generic_child_file;
?>

<ul class="list-unstyled <?php echo $row_class; ?>">
<?php foreach( $objects as $obj ) : ?>
	<?php
	$this_id = is_object($obj) ? $obj->getId() : $obj;

	$alert_class = 'archive';
	$parent_alert_class = $alert_class;
	$my_errors = array();
	if( isset($available[$this_id]) )
	{
		if( is_array($available[$this_id]) && $available[$this_id] )
		{
			$alert_class = 'danger-o';
			$parent_alert_class = $alert_class;
			$my_errors = $available[$this_id];
		}
		elseif( $available[$this_id] )
		{
			$alert_class = 'success-o';
			if( in_array($obj_class, array('time')) )
				$parent_alert_class = 'available';
			else
				$parent_alert_class = $alert_class;
		}

	if( in_array($obj_class, array('time')) )
		$parent_alert_class .= ' alert-condensed';
	}
	?>
	<li class="<?php echo $col_class; ?>"<?php echo $col_style; ?>>
		<div class="alert alert-<?php echo $parent_alert_class; ?>">
			<?php
			$alert_class = preg_replace( '/\-o$/', '', $alert_class );

			echo $this->render_file(
				$child_file,
				array(
					'link'			=> 1,
					'obj'			=> $obj,
					'alert_class'	=> $alert_class,
					'errors'		=> $my_errors,
					'a'				=> $a,
					)
				);
			?>
		</div>
	</li>
<?php endforeach; ?>
</ul>