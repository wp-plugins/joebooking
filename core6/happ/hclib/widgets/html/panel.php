<?php
include_once( dirname(__FILE__) . '/container.php' );
class HC_Html_Widget_Panel extends HC_Html_Widget_Container
{
	protected $header = NULL;
	protected $body = NULL;
	protected $footer = NULL;

	protected $collapse = FALSE;
	private $default_in = FALSE;

	protected $ajax = NULL;
	protected $no_caret = FALSE;

	function set_header( $thing )
	{
		$this->header = $thing;
		return $this;
	}
	function header()
	{
		return $this->header;
	}

	function set_body( $thing )
	{
		$this->body = $thing;
		return $this;
	}
	function body()
	{
		return $this->body;
	}

	function set_collapse( $collapse )
	{
		$this->collapse = $collapse;
		return $this;
	}
	function collapse()
	{
		return $this->collapse;
	}

	function set_no_caret( $no_caret = TRUE )
	{
		$this->no_caret = $no_caret;
		return $this;
	}
	function no_caret()
	{
		return $this->no_caret;
	}

	function set_ajax( $ajax )
	{
		$this->ajax = $ajax;
		return $this;
	}
	function ajax()
	{
		return $this->ajax;
	}

	public function set_default_in( $default_in = TRUE )
	{
		$this->default_in = $default_in;
		return $this;
	}
	public function default_in()
	{
		return $this->default_in;
	}

	function set_footer( $thing)
	{
		$this->footer = $thing;
		return $this;
	}
	function footer()
	{
		return $this->footer;
	}

	function render()
	{
		$out = HC_Html_Factory::element('div')
			->add_attr('class', 'panel')
			;
		$collapse = $this->collapse();
		$ajax = $this->ajax();

		if( $collapse ){
			$out->add_attr('class', 'collapse-panel');
		}
		elseif( $ajax ){
			$out->add_attr('class', 'hc-ajax-parent');
		}

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$out->add_attr( $k, $v );
		}

		$header = $this->header();
		if( $header ){
			if( $collapse OR $ajax ){
			/* build trigger */
				if(
					is_object($header) &&
					( $header->tag() == 'a' )
				){
					$trigger = $header;
				}
				else {
					$full_title = $header;
					$title = strip_tags($header);
					$title = trim($title);

					$trigger = HC_Html_Factory::element('a')
						->add_attr('title', $title)
							->add_child( 
								$full_title
								)
						;
					if( $ajax ){
						$trigger
							->add_attr('href', $ajax)
							;
					}
				}
				$trigger
					->add_attr('class', 'display-block')
					;

				if( $collapse ){
					$trigger
						->add_attr('href', '#')
						->add_attr('class', 'hc-collapse-next')
						;
				}
				elseif( $ajax ){
					$trigger
						->add_attr('class', 'hc-ajax-loader')
						;
				}

				if( ! $this->no_caret() ){
					$trigger
						->add_child( ' ' )
						->add_child(
							HC_Html_Factory::element('b')
								->add_attr('class', 'caret')
							)
						;
				}

				$header = $trigger;
			}

			$out->add_child(
				HC_Html_Factory::element('div')
					->add_attr('class', 'panel-heading')
					->add_child($header)
				);
		}

		$body = $this->body();
		if( $body OR $ajax ){
			$body_item = HC_Html_Factory::element('div')
				->add_attr('class', 'panel-body')
				->add_child($body)
				;
			if( $collapse ){
				$body_item->add_attr('class', 'collapse');
				if( $this->default_in() ){
					$body_item->add_attr('class', 'in');
				}
			}
			elseif( $ajax ){
				$body_item
					->add_attr('class', 'hc-ajax-container')
					->add_attr('style', 'display: none;')
					;
			}
			$out->add_child( $body_item );
		}

		$footer = $this->footer();
		if( $footer ){
			$footer_item = HC_Html_Factory::element('div')
				->add_attr('class', 'panel-footer')
				->add_child($footer)
				;
			if( $collapse ){
				$footer_item->add_attr('class', 'collapse');
				if( $this->default_in() ){
					$footer_item->add_attr('class', 'in');
				}
			}
			$out->add_child( $footer_item );
		}

		$return = $out->render();
		return $return;
	}
}
?>