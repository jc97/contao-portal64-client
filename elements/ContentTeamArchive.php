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
 * Content element "teamarchive".
 *
 * @author Julian Knorr
 */
class ContentTeamArchive extends ContentPortal64
{
	/**
	 * Template
	 *
	 * @var string
	 */
	protected $strTemplate = 'ce_team_archive';
	
	/**
	 * Generate the content element
	 */
	protected function compile()
	{
		//Headline
		if ($this->autoHeadline) {
			$this->Template->headline = Portal64Manager::replaceWildcards($this->headline, $this->objTeam->id);
		}
		
		$collectionTeams = Portal64TeamModel::findByInternalName($this->objTeam->internalName);
		$teams = [];
		$currentTerm = Portal64TermModel::findLatest();
		
		while ($currentTerm !== null && $collectionTeams !== null && $collectionTeams->next()) {
			$objTermOfTeam = Portal64TermModel::findById($collectionTeams->pid);
			if ($objTermOfTeam === null) continue;
			if ($objTermOfTeam->year < $this->startTerm && $this->startTerm > 0) continue;
			if ($objTermOfTeam->year > $this->endTerm && $this->endTerm > 0) continue;
			if ($objTermOfTeam->id == $currentTerm->id && $this->excludeLatestTerm) continue;
			$teams[$objTermOfTeam->year] = ['team' => $collectionTeams->current(), 'term' => $objTermOfTeam];
		}
		
		ksort($teams, SORT_NUMERIC);
		$teams = array_reverse($teams);
		$arrContent = [];
		
		foreach ($teams as $team) {
			if ($this->showContentTeam) {
				$objContent = new ContentModel();
				$objContent->type = 'team';
				$objContent->autoHeadline = $this->autoHeadline;
				$objContent->headline = $this->ContentTeamHeadline;
				$objContent->team = ($team['team'])->id;
				$objContent->displayFIDEData = $this->displayFIDEData;
				$objContent->showGameResults = $this->showGameResults;
				$objContent->linkToPortal = $this->showContentTeamresults ? 0 : $this->linkToPortal;
				$objContent->showTeamster = $this->showTeamster;
				$objContent->showTeamsterMail = $this->showTeamsterMail;
				$objContent->customTpl = $this->teamTemplate;
				$objElement = new ContentTeam($objContent, $this->strColumn);
				$arrContent[] = ['type' => 'team', 'team' => ($team['team'])->row(), 'term' => ($team['term'])->row(), 'string' => $objElement->generate()];
			}
			if ($this->showContentTeamresults) {
				$objContent = new ContentModel();
				$objContent->type = 'teamresults';
				$objContent->autoHeadline = $this->autoHeadline;
				$objContent->headline = $this->ContentTeamresultsHeadline;
				$objContent->team = ($team['team'])->id;
				$objContent->showGameResults = $this->showRoundGameResultsInArchive;
				$objContent->linkToPortal = $this->linkToPortal;
				$objContent->showTeamster = $this->showContentTeam ? 0 : $this->showTeamster;
				$objContent->showTeamsterMail = $this->showTeamsterMail;
				$objContent->showSingleRound = 0;
				$objContent->customTpl = $this->teamresultsTemplate;
				$objElement = new ContentTeamResults($objContent, $this->strColumn);
				$arrContent[] = ['type' => 'teamresults', 'team' => ($team['team'])->row(), 'term' => ($team['term'])->row(), 'string' => $objElement->generate()];
			}
		}
		
		$this->Template->ContentElements = $arrContent;
		
		//Set team and term
		$this->Template->team = $this->objTeam->row();
		$this->Template->term = $this->objTerm->row();
		
		//Set configuration
		$this->Template->ContentElement = $this->arrData;
	}
	
}

?>