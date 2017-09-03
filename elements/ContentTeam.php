<?php
/**
 * Portal64.de Client
 * Extension for Contao Open Source CMS, Copyright (C) Leo Feyer
 *
 * Copyright (C) 2017 Julian Knorr
 *
 * @package			Portal64.de Client
 * @author			Julian Knorr
 * @copytight		Copyright (C) 2017 Julian Knorr
 * @date			2017
 */

namespace Portal64;

/**
 * Content element "team".
 *
 * @author Julian Knorr
 */
class ContentTeam extends ContentPortal64
{
	
	/**
	 * Template
	 *
	 * @var string
	 */
	protected $strTemplate = 'ce_team';
	
	/**
	 * Generate the content element
	 */
	protected function compile()
	{
		parent::compile();
		// extend player by round results
		$players = $this->Template->players;
		foreach ($players as $playerIndex => $player) {
			$player['_round_matches'] = [];
			foreach ($this->Template->rounds as $round) {
				$roundNumber = $round['round'];
				foreach ($round['_matches'] as $match) {
					if ($match['_ownRank'] === $player['rank']) {
						$player['_round_matches'][$roundNumber] = $match;
					}
				}
			}
			$players[$playerIndex] = $player;
		}
		$this->Template->players = $players;
	}
}

?>