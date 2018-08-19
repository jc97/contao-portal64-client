<?php
/**
 * Portal64.de Client
 * Extension for Contao Open Source CMS, Copyright (C) Leo Feyer
 *
 * Copyright (C) 2018 Julian Knorr
 *
 * @package			Portal64.de Client
 * @author			Julian Knorr
 * @copyright		Copyright (C) 2018 Julian Knorr
 * @date			2018
 */

namespace Portal64;

/**
 * Content element "teamheadline".
 *
 * @author Julian Knorr
 */
class ContentTeamHeadline extends ContentPortal64
{
	
	/**
	 * Template
	 *
	 * @var string
	 */
	protected $strTemplate = 'ce_headline';
	
	/**
	 * Generate the content element
	 */
	protected function compile()
	{
		//Headline
		$this->Template->headline = Portal64Manager::replaceWildcards($this->headline, $this->objTeam->id);
		
		//Set team and term
		$this->Template->team = $this->objTeam->row();
		$this->Template->term = $this->objTerm->row();
		
		//Set configuration
		$this->Template->ContentElement = $this->arrData;
	}
}

?>