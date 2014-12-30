<?php
class HC_Html_Widget_Collapse
{
	private $title = '';
	private $content = '';

	public function set_title( $title )
	{
		$this->title = $title;
		return $this;
	}
	public function title()
	{
		return $this->title;
	}

	public function set_content( $content )
	{
		$this->content = $content;
		return $this;
	}
	public function content()
	{
		return $this->content;
	}

	public function render()
	{
		$out = HC_Html_Factory::element( 'ul' )
			->add_attr('class', 'list-unstyled')
			->add_attr('class', 'collapse-panel')
			;

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
			$full_title = $title;
			$title = strip_tags($title);
			$title = trim($title);

			$trigger = HC_Html_Factory::element('a')
				->add_attr('title', $title)
					->add_child( 
						$full_title
						)
				;
		}

		$trigger
			->add_attr('href', '#')
			->add_attr('class', 'hc-collapse-next')
			->add_attr('class', 'display-block')
			;

		$out->add_child(
			HC_Html_Factory::element('li')
				->add_child( $trigger )
			);

		$out->add_child(
			HC_Html_Factory::element('li')
				->add_attr('class', 'collapse')
				->add_child( $this->content() )
			);

		return $out->render();
	}
}
?>