<?php
class HC_Html_Widget_Main_Menu
{
	protected $menu = array();
	protected $disabled = array();
	protected $current = '';
	protected $engine = 'ci'; // can also be 'nts'

	public function __construct( $engine = 'ci' )
	{
		$this->set_engine( $engine );
	}

	public function set_engine( $engine )
	{
		$this->engine = $engine;
	}
	public function engine()
	{
		return $this->engine;
	}

	public function set_menu( $menu )
	{
		$this->menu = $menu;
	}

	public function set_disabled( $disabled = array() )
	{
		if( $disabled )
			$this->disabled = $disabled;
	}

	public function set_current( $current )
	{	
		$this->current = $current;
	}

	private function _prepare_menu()
	{
		if( ! ($this->menu && is_array($this->menu)) ){
			return;
		}

		$order = 1;
		$menu_keys = array_keys($this->menu);
		reset( $menu_keys );
		$active_k = '';
 
		foreach( $menu_keys as $k )
		{
			if( ! is_array($this->menu[$k]) ){
				$this->menu[$k] = array(
					'title'	=> $this->menu[$k]
					);
			}
			if( ! isset($this->menu[$k]['order']) ){
				$this->menu[$k]['order'] = $order++;
			}
			if( ! isset($this->menu[$k]['icon']) ){
				$this->menu[$k]['icon'] = '';
			}

			if( ! 
				(
					(isset($this->menu[$k]['external']) && $this->menu[$k]['external']) OR 
					(isset($this->menu[$k]['href']) && $this->menu[$k]['href'])
				)
				){
				switch( $this->engine() ){
					case 'ci':
						$this->menu[$k]['slug'] = $this->menu[$k]['link'];
						$this->menu[$k]['href'] = HC_Lib::link( $this->menu[$k]['link'] );
						break;
					case 'nts':
						if( ! isset($this->menu[$k]['panel']) ){
							$this->menu[$k]['panel'] = $k;
						}

						$this->menu[$k]['slug'] = $this->menu[$k]['panel'];
						$this->menu[$k]['href'] = ntsLink::makeLink( $this->menu[$k]['panel'], '', array(), FALSE, TRUE );
						break;
				}
			}

			if( $this->disabled && ( ! ( isset($this->menu[$k]['external']) && $this->menu[$k]['external'] ) ) ){
				$this_slug = $this->menu[$k]['slug'];
				if( in_array($this_slug, $this->disabled) ){
//					echo "DISABLE " . $this->menu[$k]['slug'] . '<br>';
					unset( $this->menu[$k] );
				}
				else {
					/* also check if a parent is disabled */
					foreach( $this->disabled as $ds ){
						if( substr($this_slug, 0, strlen($ds)) == $ds ){
							unset( $this->menu[$k] );
							break;
						}
					}
				}
			}

			/* check if current */
			if( $this->current && (! $active_k) ){
				$slug = isset($this->menu[$k]['slug']) ? $this->menu[$k]['slug'] : '';
				$current = $this->current;

				if(
					(
						($current == $slug)
					)
					OR
					( 
						( substr($current, 0, strlen($slug)) == $slug ) &&
						( substr($current, strlen($slug), 1) == '/' )
					)
					){
					$active_k = $k;
				}
			}
		}

	/* set current */
		if( $active_k ){
			reset( $menu_keys );
			foreach( $menu_keys as $k ){
				if( 
					( $k == $active_k )
					OR
					(
						( substr($active_k, 0, strlen($k)) == $k ) &&
						( substr($active_k, strlen($k), 1) == '/' )
					)
				){
					$this->menu[$k]['active'] = TRUE;
				}
			}
		}

		uasort( $this->menu, create_function('$a, $b', 'return ($a["order"] - $b["order"]);' ) );
	}

	private function _filter_menu( $root )
	{
		if( $this->menu && is_array($this->menu) ){
			$menu_keys = array_keys($this->menu);
			foreach( $menu_keys as $k ){
				if( substr($k, 0, strlen($root)) != $root ){
					unset( $this->menu[$k] );
				}
			}
		}
	}

	private function _get_menu( $root )
	{
		$this->_filter_menu( $root );
		$this->_prepare_menu();
		$return = array();

		if( ! ($this->menu && is_array($this->menu)) ){
			return $return;
		}

		$menu_keys = array_keys($this->menu);
		reset( $menu_keys );
		foreach( $menu_keys as $k ){
			$this_level = substr_count( $k, '/' );
			if( $this_level > 1 )
				continue;
			if( substr($k, 0, strlen($root)) != $root )
				continue;

			$this_m = $this->menu[$k];

			$children = array();
			$has_children = FALSE;
			reset( $menu_keys );
			foreach( $menu_keys as $k2 ){
				if( $k == $k2 )
					continue;
				if( substr($k2, 0, strlen($k)) == $k ){
					$their_level = substr_count( $k2, '/' );
					if( $their_level == ($this_level + 1) ){
						$has_children = TRUE;
						$their_m = $this->menu[$k2];
						$children[$k2] = $their_m;
					}
				}
			}

			if( $children ){
				if( count($children) == 1 ){
					$chkeys = array_keys($children);
					$this_m = $children[ $chkeys[0] ];
				}
				else {
					$this_m['children'] = $children;
				}
			}
			$return[ $k ] = $this_m;
		}
		return $return;
	}

	public function render( $root )
	{
		$menu = $this->_get_menu( $root );
		$return = '';
		if( ! $menu ){
			return $return;
		}

		$container = HC_Html_Factory::element('div')
			->add_attr('class', array('navbar', 'navbar-default'))
			->add_child(
				HC_Html_Factory::element('div')
					->add_attr('class', array('navbar-header'))
					->add_child(
						HC_Html_Factory::element('button')
							->add_attr('type', 'button')
							->add_attr('class', 'navbar-toggle')
							->add_attr('data-toggle', 'collapse')
							->add_attr('data-target', '.hc-navbar-collapse')
							->add_child(
								HC_Html_Factory::element('span')->add_child( 'Toggle Navigation' )
									->add_attr('class', 'sr-only')
								)
							->add_child(
								HC_Html_Factory::element('span')->add_child( HC_Html::icon('bars') )
								)
						)
					)
			;

		$nav_container = HC_Html_Factory::element('div')
			->add_attr('class', array('collapse', 'navbar-collapse', 'hc-navbar-collapse'))
			;

		$nav = HC_Html_Factory::widget('list')
			->add_attr('class', array('nav', 'navbar-nav'))
			;

		foreach( $menu as $mk => $m )
		{
			$li_attrs = array();
			if( isset($m['children']) && $m['children'] )
			{
				$item = HC_Html_Factory::widget('dropdown_menu');
				$item->set_items( $m['children'] );
				$item->set_title(
					array(
						'title'	=> $m['title'],
						'icon'	=> isset($m['icon']) ? $m['icon'] : '',
						)
					);
			}
			else
			{
				if( isset($m['external']) && $m['external'] )
				{
					$item = HC_Html_Factory::element('a')
						->add_attr('href', $m['link'])
						->add_attr('title', $m['title'])
						->add_attr('target', '_blank')
						->add_child( 
							HC_Html_Factory::element('span')
								->add_attr('class', array('alert', 'alert-success-o'))
								->add_child( $m['title'] )
							)
						;
				}
				else
				{
					$item = HC_Html_Factory::element('a')
						->add_attr('href', $m['href'])
						->add_attr('title', $m['title'])
						->add_child( HC_Html::icon($m['icon']) . $m['title'] )
						;
				}
			}

			$nav->add_item( 
				$mk,
				$item,
				$li_attrs
				);
		}
		$nav_container->add_child( $nav );
		$container->add_child( $nav_container );

		return $container->render();
	}
}
?>