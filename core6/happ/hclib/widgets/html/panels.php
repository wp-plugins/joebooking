<?php
class HC_Html_Widget_Panels extends HC_Html_Element
{
	protected $panels = array();
	protected $field = '';
	protected $active = '';
	protected $id = '';

	function __construct()
	{
		$this->id = hc_random();
	}

	function add_panel( $id, $label, $content )
	{
		$this->panels[ $id ] = array(
			'label'		=> $label,
			'content'	=> $content,
			);
	}

	function set_field( $field )
	{
		$this->field = $field;
	}
	function field()
	{
		return $this->field;
	}

	function set_active( $active )
	{
		$this->active = $active;
	}
	function active()
	{
		if( ! $this->active )
		{
			$options = array_keys( $this->panels );
			if( count($options) )
			{
				$this->active = $options[0];
			}
		}
		return $this->active;
	}

	function render_tabs()
	{
		return $this->render( array('tabs') );
	}
	function render_content()
	{
		return $this->render( array('content') );
	}

	function render( $what = array('tabs', 'content') )
	{
		if( ! is_array($what) )
		{
			$what = array( $what );
		}

		$active = $this->active();

		$tabs = HC_Html_Factory::element('ul');
		$tabs->add_attr( 'class', 'nav' );
		$tabs->add_attr( 'class', 'nav-tabs' );
		$tabs->add_attr( 'id', $this->id );

		$content = HC_Html_Factory::element( 'div' );
		$content->add_attr( 'class', 'tab-content' );
		$content->add_attr( 'style', 'overflow: visible' );

		reset( $this->panels );
		foreach( $this->panels as $pid => $p )
		{
		/* tab switch */
			$tab = HC_Html_Factory::element('li');
			if( $active == $pid )
			{
				$tab->add_attr( 'class', 'active' );
			}
			$a = HC_Html_Factory::element('a')
				->add_attr('href', '#' . $pid)
				->add_attr('data-toggle', 'tab')
				->add_attr('title', $p['label'])
				->add_child( $p['label'] )
				;
			$tab->add_child( $a );
			$tabs->add_child( $tab );

		/* tab content */
			$panel = HC_Html_Factory::element( 'div' );
			$panel->add_attr( 'class', 'tab-pane' );
			$panel->add_attr( 'id', $pid );
			if( $active == $pid )
			{
				$panel->add_attr( 'class', 'active' );
			}
			$panel->add_child( $p['content'] );

			$content->add_child( $panel );
		}

	/* addon */
		$addon = array();
		if( $field = $this->field() )
		{
			$addon[] = '<script language="JavaScript">';
			$addon[] = <<<EOT
jQuery('#$this->id a[data-toggle="tab"]').on('shown.bs.tab', function (e)
{
	var active_tab = e.target.hash.substr(1); // strip the starting #
	jQuery(this).closest('form').find('[name=$field]').val( active_tab );
});
EOT;

/*
				if( $shown )
				{
				$out[] = <<<EOT
		jQuery('#{$this->id}').closest('form').find('[name=$field]').val( "$shown" );
EOT;
				}
*/
			$addon[] = '</script>';
		}

	/* prepare output */
		$out = array();
		if( in_array('tabs', $what) )
		{
			$out[] = $tabs->render();
		}
		if( in_array('content', $what) )
		{
			$out[] = $content->render();
			$out[] = join( "\n", $addon );
		}

		$return = join( "\n", $out );
		return $return;
	}
}