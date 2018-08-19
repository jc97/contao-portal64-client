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
 * Content element "teamresults".
 *
 * @author Julian Knorr
 */
class ContentTeamResults extends ContentPortal64
{
	
	/**
	 * Template
	 *
	 * @var string
	 */
	protected $strTemplate = 'ce_team_results';
	
	/**
	 * Generate the content element
	 */
	protected function compile()
	{
		parent::compile();
		
		// remove rounds, when showSingleRound
		if ($this->showSingleRound) {
			$rounds = $this->Template->rounds;
			$roundNumberToShow = $this->round;
			$roundToShow = null;
			
			foreach ($rounds as $round) {
				if ($round['round'] == $roundNumberToShow) {
					$roundToShow = $round;
					break;
				}
			}
			
			if ($roundToShow === null) $this->Template->rounds = [];
			else $this->Template->rounds = [$roundToShow];
		}
	}
}

?>