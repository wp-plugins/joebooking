<?php
include_once( dirname(__FILE__) . '/container.php' );
class HC_Html_Widget_Dropdown_Menu extends HC_Html_Widget_Container
{
	protected $title = array(
		'title'	=> 'title',
		'icon'	=> ''
		);

	function set_title( $title )
	{
		$this->title = $title;
	}
	function title()
	{
		return $this->title;
	}

	function render()
	{
		$out = array();

	/* build trigger */
		$title = $this->title();
		if( 
			is_object($title) &&
			( $title->tag() == 'a' )
			)
		{
			$trigger = $title;
		}
		else
		{
			$set = array(
				'title'			=> 'title',
				'icon'			=> '',
				'icon_class'	=> ''
				);
			if( is_string($title) )
			{
				$set['title'] = $title;
			}
			elseif( is_array($title) )
			{
				if( isset($title['title']) )
					$set['title'] = $title['title'];
				if( isset($title['icon']) )
					$set['icon'] = $title['icon'];
				if( isset($title['icon_class']) )
					$set['icon_class'] = $title['icon_class'];
			}
			$set['title'] = strip_tags($set['title']);
			$set['title'] = trim($set['title']);

			$trigger = HC_Html_Factory::element('a')
				->add_attr('title', $set['title'])
					->add_child(
						HC_Html::icon($set['icon'])->add_attr('class', $set['icon_class']) . $set['title']
						)
				;
		}

		$trigger
			->add_attr('href', '#')
			->add_attr('class', 'dropdown-toggle')
			->add_attr('data-toggle', 'dropdown')
			->add_child(
				HC_Html_Factory::element('b')
					->add_attr('class', 'caret')
				)
			;

		$out[] = $trigger;
		$ul = HC_Html_Factory::element('ul')
			->add_attr('class', 'dropdown-menu')
			;

		$items = $this->items();
		foreach( $items as $k2 => $m2 )
		{
			$li = HC_Html_Factory::element('li');
			if( is_object($m2) && method_exists($m2, 'render') )
			{
				$li->add_child($m2);
			}
			elseif( is_array($m2) )
			{
				if( ! isset($m2['icon']) )
					$m2['icon'] = '';
				if( ! isset($m2['icon_class']) )
					$m2['icon_class'] = '';
				$m2['title'] = strip_tags($m2['title']);
				$m2['title'] = trim($m2['title']);

				$this_view = HC_Html::icon($m2['icon'])->add_attr('class', $m2['icon_class']) . $m2['title'];

				if( $m2['title'] == '-divider-' )
				{
					$li->add_attr('class', 'divider');
				}
				elseif( isset($m2['href']) && strlen($m2['href']) )
				{
					$a = HC_Html_Factory::element('a');
					$a->add_attr('href', $m2['href'] );
					$a->add_attr('title', $m2['title'] );
					if( isset($m2['target']) && strlen($m2['target']) )
					{
						$a->add_attr('target', $m2['target'] );
					}
					if( isset($m2['class']) && strlen($m2['class']) )
					{
						$a->add_attr('class', $m2['class'] );
						if( $m2['class'] == 'hc-ajax-loader' )
						{
							$li->add_attr('class', 'hc-ajax-parent');
						}
					}

					if( isset($m2['text-class']) )
					{
						$this_view = HC_Html_Factory::element('span')
							->add_attr('class', $m2['text-class'])
							->add_child( $this_view )
							;
					}

					$a->add_child( $this_view );
					$li->add_child( $a );
				}
				else
				{
					if( isset($m2['header']) && $m2['header'] )
					{
						$li->add_attr('class', 'dropdown-header');
					}
					else
					{
						$this_view = HC_Html_Factory::element('span')
							->add_attr('title', $m2['title'])
							->add_child( $this_view )
							;
					}
					$li->add_child( $this_view );
				}
			}
			else
			{
				if( $m2 == '-divider-' )
				{
					$li->add_attr('class', 'divider');
				}
			}

			$ul->add_child($li);
		}

		$out[] = $ul;

		$return = '';
		foreach( $out as $o )
		{
			$return .= $o->render();
		}
		return $return;
	}
}
?>