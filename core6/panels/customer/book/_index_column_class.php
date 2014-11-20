<?php
switch( $per_row )
{
	case 0:
		$col_style = 'width: 6em;';
		break;
	case 6:
		$col_class = 'col-lg-2 col-md-3 col-sm-6 col-xs-6';
		break;
	case 3:
		$col_class = 'col-lg-4 col-md-6 col-sm-6 col-xs-12';
		break;
	case 2:
		$col_class = 'col-lg-6 col-md-6 col-sm-6 col-xs-12';
		break;
	case 4:
	default:
		$row_class = 'row';
		$col_class = 'col-lg-3 col-md-4 col-sm-6';
		break;
}
?>