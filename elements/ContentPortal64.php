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

/*
 * Base class for content elements
 *
 * @author Julian Knorr
 */
abstract class ContentPortal64 extends \ContentElement
{
	/**
	 * Check if the team is valid
	 *
	 * @return string
	 */
	public function generate()
	{
		if ($this->team == '') {
			return '### NO TEAM SELECTED ###';
		}
		
		$this->objTeam = Portal64TeamModel::findByPk($this->team);
		if ($this->objTeam === null) {
			return '### TEAM $this->team DOES NOT EXIST ###';
		}
		
		$this->objTerm = Portal64TermModel::findByPk($this->objTeam->pid);
		if ($this->objTerm === null) {
			return '### TERM $this->objTeam->pid DOES NOT EXIST ###';
		}
		
		return parent::generate();
	}
	
	/**
	 * Generate the content element
	 */
	protected function compile()
	{
		//Headline
		if ($this->autoHeadline) {
			$this->Template->headline = Portal64Manager::replaceWildcards($this->headline, $this->objTeam->id);
		}
		
		//Get member of teamster
		if (strlen($this->objTeam->teamster) > 0) {
			$possibleNames = Portal64Manager::splitName($this->objTeam->teamster);
			$objMember = null;
			foreach ($possibleNames as $splitting) {
				$collectionMember = MemberModel::findByFirstnameAndLastname($splitting['firstname'], $splitting['lastname']);
				if ($collectionMember !== null) {
					if ($collectionMember->count() > 1 || $objMember !== null) {
						//Name is not clear
						$objMember = null;
						break;
					}
					$objMember = $collectionMember->current();
				}
			}
			if ($objMember !== null) {
				$this->Template->arrTeamster = $objMember->row();
			} else $this->Template->arrTeamster = null;
		} else $this->showTeamster = '';
		
		//Get link to portal
		$this->Template->linkToPortal = Portal64::buildPortalUrl('league_home', ['tid' => $this->objTeam->tid, 'term' => $this->objTerm->year]);
		
		//Get players
		$players = [];
		$collectionPlayers = Portal64TeamPlayerModel::findByTeam($this->objTeam->id, ['order' => 'rank ASC']);
		while ($collectionPlayers->next()) {
			$players[$collectionPlayers->rank] = $collectionPlayers->row();
			$players[$collectionPlayers->rank]['_regular'] = true;
		}
		
		//Get rounds
		$rounds = [];
		$collectionRounds = Portal64TeamRoundModel::findByPid($this->objTeam->id, ['order' => 'round ASC']);
		while ($collectionRounds->next()) {
			$round = $collectionRounds->row();
			//Get results
			$round['_matches'] = [];
			$round['_ownPoints'] = 0;
			$round['_opponentPoints'] = 0;
			$round['_homeTeam'] = $collectionRounds->isHome ? $this->objTeam->officialName : $collectionRounds->opponent;
			$round['_guestTeam'] = !$collectionRounds->isHome ? $this->objTeam->officialName : $collectionRounds->opponent;
			$round['_hasResults'] = false;
			$collectionResults = Portal64TeamRoundMatchModel::findByPid($collectionRounds->id, ['order' => 'position ASC']);
			while ($collectionResults !== null && $collectionResults->next()) {
				$match = $collectionResults->row();
				$match['_ownRank'] = $collectionRounds->isHome ? $collectionResults->homeRank : $collectionResults->guestRank;
				$match['_ownResult'] = $collectionRounds->isHome ? $collectionResults->resultHome : $collectionResults->resultGuest;
				$match['_opponentRank'] = !$collectionRounds->isHome ? $collectionResults->homeRank : $collectionResults->guestRank;
				$match['_opponentResult'] = !$collectionRounds->isHome ? $collectionResults->resultHome : $collectionResults->resultGuest;
				$round['_ownPoints'] += Portal64::htmlResultToPoints($match['_ownResult']);
				$round['_opponentPoints'] += Portal64::htmlResultToPoints($match['_opponentResult']);
				$round['_hasResults'] = true;
				if (!isset($players[$match['_ownRank']])) {
					$objReplacement = Portal64TeamPlayerModel::findByTeamsAndRank(deserialize($this->objTeam->substituteTeams), $match['_ownRank']);
					if ($objReplacement !== null) {
						$players[$objReplacement->rank] = $objReplacement->row();
						$players[$objReplacement->rank]['_regular'] = false;
					}
				}
				$match['_homeName'] = $match['homeRank'] > 0 ? ($collectionRounds->isHome ? $players[$match['_ownRank']]['name'] : $collectionResults->opponentName) : null;
				$match['_guestName'] = $match['guestRank'] > 0 ? (!$collectionRounds->isHome ? $players[$match['_ownRank']]['name'] : $collectionResults->opponentName) : null;
				
				$round['_matches'][] = $match;
			}
			$round['_homePoints'] = $collectionRounds->isHome ? $round['_ownPoints'] : $round['_opponentPoints'];
			$round['_guestPoints'] = !$collectionRounds->isHome ? $round['_ownPoints'] : $round['_opponentPoints'];
			$rounds[] = $round;
		}
		
		//Additional data of players
		foreach ($players as $playerIndex => $player) {
			$extendedPlayer = Portal64Manager::getPlayerDataFromAdditionalSources($player);
			if (is_array($extendedPlayer)) $players[$playerIndex] = $extendedPlayer;
		}
		
		//Set team, term, players and rounds
		$this->Template->players = $players;
		$this->Template->rounds = $rounds;
		$this->Template->team = $this->objTeam->row();
		$this->Template->term = $this->objTerm->row();
		
		//Set configuration
		$this->Template->ContentElement = $this->arrData;
	}
}

?>