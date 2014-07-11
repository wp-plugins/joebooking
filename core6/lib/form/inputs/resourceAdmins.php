<?php
$fields = array('appointments_view', 'appointments_edit', 'appointments_notified', 'schedules_view', 'schedules_edit');

$uif =& ntsUserIntegratorFactory::getInstance();
$integrator =& $uif->getIntegrator();
$admins = $integrator->getAdmins();

switch( $inputAction ){
	case 'display':
		if( ! $conf['value'] )
			$conf['value'] = array();
		$input .= '<table class="table table-condensed table-striped">';
		$input .= '<tr><th></th><th colspan="3" style="text-align: center;">' . M('Appointments') . '</th><th colspan="2" style="text-align: center;">' . M('Schedules') . '</th></tr>';
		$input .= '<tr><td></td><td style="text-align: center;">' . M('View') . '</td><td style="text-align: center;">' . M('Edit') . '</td><td style="text-align: center;">' . M('Get Notified') . '</td><td style="text-align: center;">' . M('View') . '</td><td style="text-align: center;">' . M('Edit') . '</td></tr>';

		$count = 0;
		reset( $admins );
		foreach( $admins as $e ){
			$adminTitle = trim( ntsView::objectTitle($e) );
			$adminUsername = NTS_EMAIL_AS_USERNAME ? $e->getProp('email') : $e->getProp('username');

			$on = isset($conf['value'][$e->getId()]) ? TRUE : FALSE;
			if( $on )
				$adminTitle = '<strong>' . $adminTitle . '</strong>';

			$adminTitle .= '<br><small>' . $adminUsername . '</small>';

			$input .= '<tr';
			if( $on )
				$input .= ' class="success"';
			$input .= '>';
			$input .= '<td>' . $adminTitle . '</td>';

			reset( $fields );
			foreach( $fields as $f ){
				$input .= '<td style="text-align: center;">';
				$input .= $this->makeInput (
				/* type */
					'checkbox',
				/* attributes */
					array(
						'id'		=> $conf['id'] . '_' . $f . '_' . $e->getId(),
						'default'	=> isset($conf['value'][$e->getId()][$f]) ? $conf['value'][$e->getId()][$f] : 0,
						)
					);
				$input .= '</td>';
				}
			$input .= '</tr>';
			}
		$input .= '</table>';
		break;

	case 'submit':
		$input = array();
		reset( $admins );
		foreach( $admins as $e ){
			reset( $fields );
			foreach( $fields as $f ){
				$checkHandle = $handle . '_' . $f . '_' . $e->getId();
				$input[ $e->getId() ][ $f ] = ( $_NTS['REQ']->getParam($checkHandle) ) ? 1 : 0;
				}
			}
		break;

	case 'check_submit':
		$input = true;
		break;
	}
?>