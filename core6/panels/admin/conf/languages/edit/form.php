<ul class="list-unstyled list-separated">
	<li>
		<?php
		echo ntsForm::makeInput(
		/* type */
			'text',
		/* attributes */
			array(
				'id'		=> 'value',
				'attr'		=> array(
//					'size'	=> 60,
					'style'	=> 'width: 100%;',
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
	</li>

	<li>
		<?php
		echo $this->makePostParams(
			'-current-',
			'save',
			array(
				'id'		=> $this->getValue('id'),
				'language'	=> $this->getValue('lang'),
				'what'		=> 'edit',
				)
			);
		?>

		<?php
		$btn_label = M('Save');

		$buttons = array();
		$buttons[] = '<ul class="list-inline">';
		$buttons[] = '<li>';
		$buttons[] = '<INPUT class="btn btn-success" TYPE="submit" value="' . $btn_label . '">';
		$buttons[] = '</li>';

		if( $id )
		{
			$buttons[] = '<li class="divider"></li>';
			$buttons[] = '<li>';
			$reset_link = ntsLink::makeLink(
				'-current-',
				'reset',
				array(
					'id'		=> $this->getValue('id'),
					'language'	=> $this->getValue('lang'),
					'what'		=> 'edit',
					)
				);
			$buttons[] = '<a href="' . $reset_link . '" class="btn btn-sm btn-archive" TYPE="submit" title="' . M('Reset') . '">';
			$buttons[] = M('Reset');
			$buttons[] = '</a>';
			$buttons[] = '</li>';
		}
		$buttons[] = '</ul>';
		?>
		<?php
		echo join( '', $buttons );
		?>
	</li>
</ul>