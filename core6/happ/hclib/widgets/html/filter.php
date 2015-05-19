<?php
include_once( dirname(__FILE__) . '/collapse.php' );

class HC_Html_Widget_Filter extends HC_Html_Widget_Collapse
{
	private $options = array();
	private $selected = array();
	private $param_name = 'param';
	private $link = NULL;
	private $allow_multiple = TRUE;

	function set_options( $options )
	{
		foreach( $options as $k => $v ){
			$this->set_option( $k, $v );
		}
		return $this;
	}
	function options()
	{
		return $this->options;
	}

	function set_option( $key, $value )
	{
		$this->options[ $key ] = $value;
		return $this;
	}
	function option( $key )
	{
		$return = NULL;
		if( isset($this->options[$key]) ){
			$return = $this->options[$key];
		}
		return $return;
	}

	function set_allow_multiple( $allow_multiple )
	{
		$this->allow_multiple = $allow_multiple;
		return $this;
	}
	function allow_multiple()
	{
		return $this->allow_multiple;
	}

	function set_param_name( $param_name )
	{
		$this->param_name = $param_name;
	}
	function param_name()
	{
		return $this->param_name;
	}

	function set_link( $link )
	{
		$this->link = $link;
	}
	function link()
	{
		return $this->link;
	}

	function set_selected( $selected )
	{
		if( ! is_array($selected) ){
			if( $selected !== NULL )
				$selected = array( $selected );
			else
				$selected = array();
		}
		$this->selected = $selected;
	}
	function selected()
	{
		return $this->selected;
	}

	function render()
	{
		if( ! $link = $this->link() ){
			return 'HC_Html_Widget_Filter: link is not set!';
		}

		$this->set_panel('default');

		$add_title = $this->title();
		$allow_multiple = $this->allow_multiple();

		$title = $add_title;
		$selected = $this->selected();
		if( $selected ){
			$title = array();
			foreach( $selected as $sel ){
				$title[] = $this->option($sel);
			}
			$title = join( ' ', $title );
		}

		$filter_icon = HC_Html_Factory::element('span')
			->add_attr('class', 'btn' )
			->add_attr('class', 'btn-default' )
			->add_child( HC_Html::icon('glass') )
			;
		$filter_icon = '';

		$title = $filter_icon . $title;
		$this->set_title( $title );

		$content = HC_Html_Factory::widget('list')
			->add_attr('class', 'list-inline')
			->add_attr('class', 'list-separated')
			->add_attr('class', 'list-separated-ver')
			// ->add_attr('class', 'list-unstyled')
			;

	/* remaining possible options */
		$remain_options = FALSE;
		if( (! $this->selected()) OR $allow_multiple ){
			foreach( $this->options() as $id => $label ){
				if( ! in_array($id, $this->selected()) ){
					$option_wrap = HC_Html_Factory::element('a')
						->add_attr('href', $link->url(array($this->param_name() . '+' => $id)))
						->add_attr('class', 'btn')
						->add_attr('class', 'btn-sm')
						->add_attr('class', 'btn-default')

						->add_attr('class', 'squeeze-in')
						->add_attr('class', 'text-left')
						->add_attr('style', 'width: 12em;')
						->add_attr('style', 'text-align: left;')
						;
					$option_wrap->add_child( $label );

					$content->add_item( $id, $option_wrap );
					$remain_options = TRUE;
				}
			}
		}
		$this->set_content( $content );

		$panel = $this->panel();

		$out = HC_Html_Factory::widget('list')
			->add_attr('class', 'list-unstyled')
			->add_attr('class', 'collapse-panel')
			;

		if( $panel ){
			$out->add_attr('class', array('panel', 'panel-' . $panel));
			$out->add_attr('class', array('panel-condensed'));
		}

	/* build trigger */
		$title = HC_Html_Factory::widget('list')
			->add_attr('class', 'list-separated')
			->add_attr('class', 'list-inline')
			;

		/* current selection */
		$selected = $this->selected();
		if( $selected ){
			foreach( $selected as $sel ){
				$option_wrap = HC_Html_Factory::element('div')
					->add_attr('class', 'btn')
					->add_attr('class', 'btn-sm')
					->add_attr('class', 'btn-default')

					->add_attr('class', 'btn')
					->add_attr('class', 'squeeze-in')
					->add_attr('class', 'text-left')
					->add_attr('style', 'width: 12em;')
					->add_attr('style', 'text-align: left;')
					;

				$option_wrap->add_child(
					HC_Html_Factory::element('a')
						->add_attr('href', $link->url(array($this->param_name() . '-' => $sel)))
						->add_attr('class', 'btn-close')
						->add_attr('class', 'btn')
						->add_attr('class', 'btn-danger-o')
						->add_child( HC_Html::icon('times') )
					);

				$option_label = $this->option($sel);
				$option_wrap->add_child( $option_label );

				$title->add_item( $sel, $option_wrap );
			}
		}

		if( $remain_options ){
			$trigger = HC_Html_Factory::element('a')
				->add_child( HC_Html::icon('plus') . lang('common_filter') . ': ' . $add_title )
				->add_attr('href', '#')
				->add_attr('class', 'hc-collapse-next')
				->add_attr('class', 'btn')
				->add_attr('class', 'btn-success-o')
				->add_attr('class', 'btn-sm')
				;
			$title->add_item( $trigger );
		}

		$out->add_item(	'header', $title );
		if( $panel ){
			$out->add_item_attr('header', 'class', 'panel-heading');
		}

		$out->add_item_attr('content', 'class', 'collapse');
		if( $panel ){
			$out->add_item_attr('content', 'class', 'panel-collapse');
			}
		if( $this->default_in() ){
			$out->add_item_attr('content', 'class', 'in');
		}

		if( $panel ){
			$out->add_item('content', 
				HC_Html_Factory::element('div')
					->add_attr('class', 'panel-body')
					->add_child( $this->content() )
				);
		}
		else {
			$out->add_item('content', $this->content());
		}

		return $out->render();

		return parent::render();
	}
}