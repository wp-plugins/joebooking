<?php
class ntsPager {
	var $_page;
	var $_ps;
	var $_shownPages;
	var $_count;
	var $_offset;

	function ntsPager( $count = 0, $pageSize = 5, $shownPages = 10 ){
		$this->reset();
		$this->setCount( $count );
		$this->setPagesize( $pageSize );
		$this->setShownPages( $shownPages );
		}

	function setCount( $c = 0 ){
		$this->_count = $c;
		}

	function reset(){
		$this->_page = 1;
		}

	function getDefaultPage(){
		return 1;
		}

	function getPagesize(){
		return $this->_ps;
		}
	function setPagesize( $ps ){
		$this->_ps = $ps;
		}

	function getShownPages(){
		return $this->_shownPages;
		}
	function setShownPages( $shownPages ){
		$this->_shownPages = $shownPages;
		}

	function getPage(){
		return $this->_page;
		}
	function setPage( $p ){
		if( ($p > $this->getNumPages()) || ($p <= 0) )
			$p = 1;
		$this->_page = $p;
		}

	function getNumPages(){
		if( ($this->_ps == 0) || ($this->_count == 0) )
			$pagesCount = 1;
		else
			$pagesCount = ceil( $this->_count / (float)$this->_ps );

		return $pagesCount;
		}

	function getPages(){
		$pages = array();

		// SETTING PAGES ARRAY
		$thisNumPages = $this->getNumPages();
		$shownPages = $this->getShownPages();
		$currentPage = $this->getPage();

		if( $thisNumPages < $shownPages ){
			for( $pn = 1; $pn <= $this->getNumPages(); $pn++ ){
				$pages[] = array(
					'number'	=> $pn,
					'title'	=> $pn
					);
				}
			}
		else {
			$slotsNumber = ceil( $thisNumPages / (float)$shownPages );
			$currentSlot = ceil( $currentPage /  (float)$shownPages );

			// ADD FIRST PAGE LINK
			if( $currentSlot > 2 ){
				$pages[] = array(
					'number'	=> 1,
					'title'	=> 1,
					);
				}

			// ADD PREVIOUS SLOT LINK
			if( $currentSlot > 1 ){
				$prevSlotStart = 1 + ($currentSlot - 2) * $shownPages;
				$prevSlotEnd = ($currentSlot - 1) * $shownPages;
				$prevSlotTitle = $prevSlotStart . '-' . $prevSlotEnd;

				$pages[] = array(
					'number'	=> $prevSlotEnd,
					'title'	=> $prevSlotTitle
					);
				}

			// ADD CURRENT SLOT LINKS
			for( $i = 1; $i <= $shownPages; $i++ ){
				$pn = $i + ($currentSlot - 1) * $shownPages;
				if( $pn > $thisNumPages )
					break;
				$pages[] = array(
					'number'	=> $pn,
					'title'	=> $pn
					);
				}

			// ADD NEXT SLOT LINK
			if( $currentSlot < $slotsNumber ){
				$nextSlotStart = 1 + $currentSlot * $shownPages;
				$nextSlotEnd = ($currentSlot + 1) * $shownPages;
				if( $nextSlotEnd > $thisNumPages )
					$nextSlotEnd = $thisNumPages;
				$nextSlotTitle = $nextSlotStart . '-' . $nextSlotEnd;

				$pages[] = array(
					'number'	=> $nextSlotStart,
					'title'	=> $nextSlotTitle
					);
				}

			// ADD LAST PAGE LINK
			if( $currentSlot  < ($slotsNumber - 1) ){
				$pages[] = array(
					'number'	=> $thisNumPages,
					'title'	=> $thisNumPages,
					);
				}

			}
		return $pages;
		}
	}
?>