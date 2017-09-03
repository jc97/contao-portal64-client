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

/*
 * Updates teams from Portal64.de
 *
 * @author Julian Knorr
 */
class Portal64Manager extends \System
{
	
	/**
	 * The time in seconds to wait between load data of teams from portal
	 *
	 * @const sleepDelay
	 */
	const sleepDelay = 2;
	
	/**
	 * Instance of Portal64
	 *
	 * @var Portal64
	 */
	protected $portal64;
	
	/**
	 * Initialize
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('Database');
		$this->import('Calendar');
		$this->portal64 = new Portal64();
	}
	
	/**
	 * Splits a name with multiple parts devided by space to first name and last name.
	 *
	 * @param $name The name
	 *
	 * @return array An array of all possible splittings. Each one is an array of firstname and lastname.
	 */
	public static function splitName($name)
	{
		$parts = explode(' ', $name);
		$results = [];
		if (count($parts) === 1) return [$parts];
		for ($i = 1; $i < count($parts); $i++) {
			$firstname = implode(' ', array_slice($parts, 0, $i));
			$lastname = implode(' ', array_slice($parts, $i));
			$results[] = ['firstname' => $firstname, 'lastname' => $lastname];
		}
		return $results;
	}
	
	/**
	 * Replace some wildcards in a string (e.g. for headline)
	 *
	 * @param $string The string
	 * @param $teamId The id of team
	 *
	 * @return bool|string The replaced string or false on error
	 */
	public static function replaceWildcards($string, $teamId)
	{
		$objTeam = Portal64TeamModel::findById($teamId);
		if ($objTeam === null) return false;
		$objTerm = Portal64TermModel::findById($objTeam->pid);
		if ($objTerm === null) return false;
		
		$string = str_replace('_N_', $objTeam->officialName, $string);
		$string = str_replace('_I_', $objTeam->internalName, $string);
		$string = str_replace('_L_', $objTeam->league, $string);
		$string = str_replace("_Y_", $objTerm->year, $string);
		$string = str_replace("_T_", self::yearToTerm($objTerm->year, true, true), $string);
		$string = str_replace("_LN_", $objTeam->lotNumber, $string);
		$string = str_replace("_TID_", $objTeam->tid, $string);
		
		return $string;
	}
	
	/**
	 * Converts a year to term as string (e.g. 2016 to 2016/17)
	 *
	 * @param      $year       The year
	 * @param bool $firstLong  (optional) Indicates whether the first year is printed with 4 digits instead of 2 digits
	 *                         (default true)
	 * @param bool $secondLong (optional) Indicates whether the second year is printed with 4 digits instead of 2
	 *                         digits (default true)
	 *
	 * @return string The term as string
	 */
	public static function yearToTerm($year, $firstLong = true, $secondLong = true)
	{
		$year = intval($year);
		$first = $firstLong ? "".$year : "".($year % 1000);
		if (strlen($first) == 1) $first = '0'.$first;
		else if (strlen($first) == 0) $first = '00';
		$secondYear = $year + 1;
		$second = $secondLong ? "".$secondYear : "".($secondYear % 1000);
		if (strlen($second) == 1) $second = '0'.$first;
		else if (strlen($second) == 0) $second = '00';
		return $first."/".$second;
	}
	
	/**
	 * Extend array of player data by some additional data from core and other extensions
	 *
	 * @param $arrPlayer The player
	 *
	 * @return array|bool The player with additional data or false on error
	 */
	public static function getPlayerDataFromAdditionalSources($arrPlayer)
	{
		if (!is_array($arrPlayer)) return false;
		
		//parts of name
		$lastName = substr($arrPlayer['name'], 0, strpos($arrPlayer['name'], ','));
		$firstName = substr($arrPlayer['name'], strpos($arrPlayer['name'], ',') + 2);
		$arrPlayer['_name']['lastname'] = $lastName;
		$arrPlayer['_name']['firstname'] = $firstName;
		
		//Core (member)
		$arrPlayer['_member'] = null;
		$collectionMember = MemberModel::findByFirstnameAndLastname($firstName, $lastName);
		if ($collectionMember !== null && $collectionMember->count() === 1) {
			$arrPlayer['_member'] = $collectionMember->row();
		}
		
		//Active extensions
		$objConfig = \Config::getInstance();
		$activeExtensions = $objConfig->getActiveModules();
		
		//Extension DWZ
		if (in_array('dwz', $activeExtensions)) {
			//ELO
			if (isset($arrPlayer['_member']['DSB_FIDE_Elo']) && strlen($arrPlayer['_member']['DSB_FIDE_Elo']) > 1) {
				$elo = $arrPlayer['_member']['DSB_FIDE_Elo'];
				if ($arrPlayer['elo'] == null) $arrPlayer['elo'] = $elo;
				$arrPlayer['_dwz']['elo'] = $elo;
			} else $arrPlayer['_dwz']['elo'] = null;
			
			//Title
			if (isset($arrPlayer['_member']['DSB_FIDE_Titel']) && strlen($arrPlayer['_member']['DSB_FIDE_Titel']) > 0) {
				$arrPlayer['_dwz']['title'] = $arrPlayer['_member']['DSB_FIDE_Titel'];
			} else $arrPlayer['_dwz']['title'] = null;
		}
		
		return $arrPlayer;
	}
	
	/**
	 * Updates all teams of latest term. Call from backend by &key=portal64update
	 */
	public function updateAllTeamsFromBackend()
	{
		if (\Input::get("index") == null) $index = 0;
		else if (!is_numeric(\Input::get("index"))) throw new \Exception('Invalid index.');
		else $index = \Input::get("index");
		$parameterTerm = \Input::get("term");
		if ($parameterTerm !== null) {
			$objTerm = Portal64TermModel::findByPk($parameterTerm);
		} else {
			$objTerm = Portal64TermModel::findLatest();
		}
		if ($objTerm === null) $this->returnInBackend();
		$collectionTeams = Portal64TeamModel::findByPid($objTerm->id);
		if ($collectionTeams === null) $this->returnInBackend();
		$numberOfTeams = $collectionTeams->count();
		if ($index < $numberOfTeams) {
			if ($index > 0) sleep(self::sleepDelay);
			$this->updateNextTeam($objTerm->id);
			$url = \Environment::get('request');
			if (preg_match('/[\?&]index=\d+/', $url)) {
				$url = preg_replace('/([\?&])index=\d+/', '${1}index='.($index + 1), $url);
			} else $url .= '&index='.($index + 1);
			$this->redirect($url);
			die();
		}
		$this->returnInBackend();
	}
	
	/**
	 * Return to menu in backend
	 */
	protected function returnInBackend()
	{
		$url = \Environment::get('request');
		$url = str_replace('&key=portal64update', '', $url);
		$url = str_replace('?key=portal64update', '', $url);
		$url = preg_replace('/&index=\d+/', '', $url);
		$url = preg_replace('/\?index=\d+/', '', $url);
		$url = preg_replace('/&term=\d+/', '', $url);
		$url = preg_replace('/\?term=\d+/', '', $url);
		$this->redirect($url);
		die();
	}
	
	/**
	 * Updates the most outdated team of a term
	 *
	 * @param int  $termId (optional) The id of the term
	 * @param bool $cron   (optional) Indicates whether the update is started by a cronjob (default false).
	 *
	 * @return bool Indicates whether the update was successfully completed
	 */
	public function updateNextTeam($termId = null, $cron = false)
	{
		if ($termId === null) $objTerm = Portal64TermModel::findLatest();
		else $objTerm = Portal64TermModel::findById($termId);
		if ($objTerm === null) return false;
		$objTeam = Portal64TeamModel::findMostOutdatedByTerm($objTerm->id);
		if ($objTeam === null) return false;
		if ($this->updateTeam($objTeam->id)) {
			$this->log('Update of team '.$objTeam->internalName.' ('.$objTeam->id.') was successfully completed.', __METHOD__, $cron ? TL_CRON : TL_GENERAL);
			return true;
		} else {
			$this->log('Update of team '.$objTeam->internalName.' ('.$objTeam->id.') failed'.($cron ? ' (executed by cronjob' : ''), __METHOD__, TL_ERROR);
			return false;
		}
	}
	
	/**
	 * Updates all data of a team and refresh lastUpdate
	 *
	 * @param      $teamId         The id of team to update
	 * @param bool $enforceUpdates (optional) Indicates whether an update of disabled teams and locked events should be
	 *                             enforced. (default false)
	 *
	 * @return bool Indicates whether the updates could successfully completed.
	 * @throws \Exception Throws an exception on some critical errors.
	 */
	public function updateTeam($teamId, $enforceUpdates = false)
	{
		$objTeam = Portal64TeamModel::findByPk($teamId);
		if ($objTeam === null) {
			throw new \Exception('Team $teamId does not exist');
		}
		if (!$this->updateMetadataOfTeam($teamId, $enforceUpdates)) return false;
		if (!$this->updatePlayersOfTeam($teamId, $enforceUpdates)) return false;
		if (!$this->updateRoundsOfTeam($teamId, true, $enforceUpdates, $enforceUpdates)) return false;
		$objTeam->lastUpdate = (new \Date())->tstamp;
		$objTeam->save();
		
		//Update content elements
		$objTerm = Portal64TermModel::findById($objTeam->pid);
		$objLatestTerm = Portal64TermModel::findLatest();
		if ($objTerm->id === $objLatestTerm->id && Portal64TeamPlayerModel::countByPid($objTeam->id) > 0) {
			$collectionPreviousTeams = Portal64TeamModel::findByInternalName($objTeam->internalName);
			if ($collectionPreviousTeams !== null) {
				$teams = $collectionPreviousTeams->fetchEach('id');
				$collectionElements = ContentModel::findUpdateblePortal64ElementByTeams($teams);
				while ($collectionElements !== null && $collectionElements->next()) {
					$objElementTeam = Portal64TeamModel::findById($collectionElements->team);
					if ($objElementTeam !== null && $objElementTeam->pid != $objTerm->id) {
						$collectionElements->team = $objTeam->id;
						$collectionElements->save();
					}
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Updates the metadata of a team.
	 *
	 * Updates name of team, teamster, league, vkz
	 *
	 * @param integer $idTeam        The id of the team to update
	 * @param bool    $enforceUpdate (optional) Indicates whether an update of disabled teams should be enforced.
	 *                               (default false)
	 *
	 * @return bool Indicates whether the update was successfully completed.
	 * @throws \Exception Throws an exception on some critical errors.
	 */
	public function updateMetadataOfTeam($idTeam, $enforceUpdate = false)
	{
		$objTeam = Portal64TeamModel::findByPk($idTeam);
		if (!$objTeam) throw new \Exception('Team $idTeam does not exist!');
		
		if ($objTeam->disableUpdates) {
			if ($enforceUpdate) {
				$this->log("Enforced update of metadata of disabled team $objTeam->internalName ($objTeam->id). Continue!", __METHOD__, TL_GENERAL);
			} else {
				$this->log("Abort update of metadata of disabled team $objTeam->internalName ($objTeam->id)", __METHOD__, TL_GENERAL);
				return false;
			}
		}
		
		$objTerm = Portal64TermModel::findByPk($objTeam->pid);
		if (!$objTerm) throw new \Exception('Term $objTeam->pid does not exist!');
		
		$portalTeams = $this->portal64->getTeamsFromPortal($objTerm->year, $objTeam->tid, intval($objTeam->lotNumber));
		if ($portalTeams === false || !isset($portalTeams[$objTeam->lotNumber])) {
			$this->log("Could not update metadata of team $objTeam->internalName ($objTeam->id) Aborted!", __METHOD__, TL_ERROR);
			throw new \Exception('Could not fetch team!');
		}
		
		$arrTeam = $portalTeams[$objTeam->lotNumber];
		
		if (strlen($arrTeam['teamname']) > 0) {
			$objTeam->officialName = $arrTeam['teamname'];
		} else {
			$this->log("Could not update name of team $objTeam->internalName ($objTeam->id) Try to continue...", __METHOD__, TL_ERROR);
		}
		
		if (strlen($arrTeam['league']) > 0) {
			$objTeam->league = $arrTeam['league'];
		} else {
			$this->log("Could not update league of team $objTeam->internalName ($objTeam->id) Try to continue...", __METHOD__, TL_ERROR);
		}
		
		if (strlen($arrTeam['vkz']) > 0) {
			$objTeam->vkz = $arrTeam['vkz'];
		} else {
			$this->log("Could not update vkz of team $objTeam->internalName ($objTeam->id) Try to continue...", __METHOD__, TL_ERROR);
		}
		
		if (strlen($arrTeam['teamster']) > 0) {
			$objTeam->teamster = $arrTeam['teamster'];
		} else {
			$this->log("Could not update teamster of team $objTeam->internalName ($objTeam->id) Try to continue...", __METHOD__, TL_ERROR);
		}
		
		$objTeam->save();
		
		$this->log("Update of metadata of team $objTeam->internalName ($objTeam->id) successfully completed.", __METHOD__, TL_GENERAL);
		return true;
	}
	
	/**
	 * Updates the players of a team
	 *
	 * @param integer $idTeam        The id of the team to update
	 * @param bool    $enforceUpdate (optional) Indicates whether an update of disabled teams should be enforced.
	 *                               (default false)
	 *
	 * @return bool Indicates whether the update was successfully completed.
	 * @throws \Exception Throws an exception on some critical errors.
	 */
	public function updatePlayersOfTeam($idTeam, $enforceUpdate = false)
	{
		$objTeam = Portal64TeamModel::findByPk($idTeam);
		if (!$objTeam) throw new \Exception('Team $idTeam does not exist!');
		
		if ($objTeam->disableUpdates) {
			if ($enforceUpdate) {
				$this->log("Enforced update of players of disabled team $objTeam->internalName ($objTeam->id). Continue!", __METHOD__, TL_GENERAL);
			} else {
				$this->log("Abort update of players of disabled team $objTeam->internalName ($objTeam->id)", __METHOD__, TL_GENERAL);
				return false;
			}
		}
		
		$objTerm = Portal64TermModel::findByPk($objTeam->pid);
		if (!$objTerm) throw new \Exception('Term $objTeam->pid does not exist!');
		
		$portalTeams = $this->portal64->getTeamsFromPortal($objTerm->year, $objTeam->tid, $objTeam->loadPlayersOfOpponentTeams ? null : intval($objTeam->lotNumber));
		
		if ($portalTeams === false || !isset($portalTeams[$objTeam->lotNumber])) {
			$this->log("Could not update players of team $objTeam->internalName ($objTeam->id) Aborted!", __METHOD__, TL_ERROR);
			throw new \Exception('Could not fetch team!');
		}
		
		$arrOpponentNames = [];
		
		//Update and insert players
		foreach ($portalTeams as $teamLotNumber => $arrTeam) {
			if (!preg_match('/\d+/', $teamLotNumber)) continue;
			$arrRanks = [];
			$ownTeam = ($teamLotNumber == $objTeam->lotNumber);
			foreach ($arrTeam['players'] as $indexOfPlayerFromPortal => $playerFromPortal) {
				$rank = $playerFromPortal['rank'];
				$haveToSave = false;
				if ($ownTeam) {
					$objPlayer = Portal64TeamPlayerModel::findByTeamAndRank($idTeam, $rank);
				} else {
					$objPlayer = Portal64TeamPlayerModel::findOpponentByTeamAndName($idTeam, $playerFromPortal['name']);
				}
				if ($objPlayer === null) {
					$haveToSave = true;
					$objPlayer = new Portal64TeamPlayerModel();
					$objPlayer->pid = $idTeam;
					$objPlayer->partOfTeam = $ownTeam;
					$objPlayer->rank = $rank;
				}
				if ($objPlayer->name !== $playerFromPortal['name']) {
					$objPlayer->name = $playerFromPortal['name'];
					$haveToSave = true;
				}
				if ($objPlayer->memberNumber !== $playerFromPortal['memberNumber']) {
					$objPlayer->memberNumber = $playerFromPortal['memberNumber'];
					$haveToSave = true;
				}
				if ($objPlayer->dwz !== $playerFromPortal['dwz']) {
					$objPlayer->dwz = $playerFromPortal['dwz'];
					$haveToSave = true;
				}
				if (intval($objPlayer->elo) != intval($playerFromPortal['elo'])) {
					$objPlayer->elo = $playerFromPortal['elo'];
					$haveToSave = true;
				}
				if (intval($objPlayer->rank) !== intval($playerFromPortal['rank'])) {
					$objPlayer->rank = $playerFromPortal['rank'];
					$haveToSave = true;
				}
				if ($haveToSave) {
					$objPlayer->tstamp = time();
					$objPlayer->save();
				}
				$arrRanks[] = $rank;
				if (!$ownTeam) $arrOpponentNames[] = $playerFromPortal['name'];
			}
			
			//Delete own players
			if ($ownTeam) {
				$collectionPlayers = Portal64TeamPlayerModel::findByTeam($idTeam);
				if ($collectionPlayers !== null) {
					while ($collectionPlayers->next()) {
						if (!in_array($collectionPlayers->rank, $arrRanks)) {
							$collectionPlayers->delete();
						}
					}
				}
			}
		}
		
		//Delete opponent players
		if ($objTeam->loadPlayersOfOpponentTeams) {
			$collectionPlayers = Portal64TeamPlayerModel::findByPid($idTeam);
			if ($collectionPlayers !== null) {
				while ($collectionPlayers->next()) {
					if ($collectionPlayers->partOfTeam == 0 && !in_array($collectionPlayers->name, $arrOpponentNames)) {
						$collectionPlayers->delete();
					}
				}
			}
		}
		
		$this->log("Update of players of team $objTeam->internalName ($objTeam->id) successfully completed.", __METHOD__, TL_GENERAL);
		return true;
	}
	
	/**
	 * Updates the rounds and results of a team
	 *
	 * @param      $idTeam                  The id of team to update
	 * @param bool $updateCalendar          (optional) Indicates whether the calender should be updated (default true)
	 * @param bool $enforceUpdateOfTeam     (optional) Indicates whether an update of disabled teams should be
	 *                                      enforced. (default false)
	 * @param bool $enforceUpdateOfCalendar (optional) Indicates whether an update of locked events should be enforced.
	 *                                      (default false)
	 *
	 * @return bool Indicates whether the update was successfully completed.
	 * @throws \Exception Throws an exception on some critical errors.
	 */
	public function updateRoundsOfTeam($idTeam, $updateCalendar = true, $enforceUpdateOfTeam = false,
									   $enforceUpdateOfCalendar = false)
	{
		$objTeam = Portal64TeamModel::findByPk($idTeam);
		if (!$objTeam) throw new \Exception('Team $idTeam does not exist!');
		
		if ($objTeam->disableUpdates) {
			if ($enforceUpdateOfTeam) {
				$this->log("Enforced update of rounds of disabled team $objTeam->internalName ($objTeam->id). Continue!", __METHOD__, TL_GENERAL);
			} else {
				$this->log("Abort update of rounds of disabled team $objTeam->internalName ($objTeam->id)", __METHOD__, TL_GENERAL);
				return false;
			}
		}
		
		$objTerm = Portal64TermModel::findByPk($objTeam->pid);
		if (!$objTerm) throw new \Exception('Term $objTeam->pid does not exist!');
		
		$portalTeams = $this->portal64->getTeamsFromPortal($objTerm->year, $objTeam->tid);
		if ($portalTeams === false || !isset($portalTeams[$objTeam->lotNumber])) {
			$this->log("Could not update rounds of team $objTeam->internalName ($objTeam->id) Aborted!", __METHOD__, TL_ERROR);
			throw new \Exception('Could not fetch teams!');
		}
		$portalRounds = $this->portal64->getRoundsOfTeamFromPortal($objTeam->tid, $objTeam->officialName);
		if ($portalRounds === false) {
			$this->log("Could not update rounds of team $objTeam->internalName ($objTeam->id) Aborted!", __METHOD__, TL_ERROR);
			throw new \Exception('Could not fetch rounds!');
		}
		
		if (!$objTeam->manageCalendar) $updateCalendar = false;
		if ($updateCalendar) {
			$objCalendar = \CalendarModel::findByPk($objTeam->calendar);
			if ($objCalendar === null) {
				$updateCalendar = false;
				$this->log("Can not update calendar for team $objTeam->internalName ($objTeam->id). Try to continue...", __METHOD__, TL_ERROR);
			}
		}
		
		$arrRoundNumbers = [];
		
		foreach ($portalRounds as $indexOfRoundFromPortal => $roundFromPortal) {
			$roundNumber = intval($roundFromPortal['roundNumber']);
			$arrRoundNumbers[] = $roundNumber;
			$objRound = Portal64TeamRoundModel::findByTeamAndNumber($idTeam, $roundNumber);
			$haveToSave = false;
			if ($objRound === null) {
				$objRound = new Portal64TeamRoundModel();
				$objRound->pid = $idTeam;
				$objRound->round = $roundNumber;
				$haveToSave = true;
			}
			if ($roundFromPortal['date'] === null || $roundFromPortal['time'] === null) {
				throw new \Exception('Date and time can not be null!');
			}
			$start = $this->convertTimeFromPortalToUnix($roundFromPortal['date'], $roundFromPortal['time']);
			if ($objRound->start != $start) {
				$objRound->start = $start;
				$haveToSave = true;
			}
			if ($objRound->isHome == $roundFromPortal['guest']) {
				$objRound->isHome = !$roundFromPortal['guest'];
				$haveToSave = true;
			}
			if ($objRound->opponent !== $roundFromPortal['opponentTeam']) {
				$objRound->opponent = $roundFromPortal['opponentTeam'];
				$haveToSave = true;
			}
			if ($updateCalendar) {
				//Update calendar
				$updateEvent = true;
				if ($objRound->eventId != null) {
					$objEvent = \CalendarEventsModel::findById($objRound->eventId);
					if ($objEvent === null || $objEvent->pid != $objTeam->calendar) $objRound->eventId = null;
					if ($objRound->eventId != null && $objEvent != null && $objEvent->disableUpdatesByPortal64) {
						if ($enforceUpdateOfCalendar) {
							$this->log("Enforced update of locked event; eventid $objEvent->id ; team $objTeam->internalName ($objTeam->id). Continue!", __METHOD__, TL_GENERAL);
						} else {
							$this->log("Skipped update of locked event; eventid $objEvent->id ; team $objTeam->internalName ($objTeam->id). Continue!", __METHOD__, TL_GENERAL);
							$updateEvent = false;
						}
					}
				}
				if ($updateEvent) {
					
					$title = $objTeam->eventTitle;
					$title = str_replace("_R_", $roundNumber, $title);
					$title = str_replace("_G_", $roundFromPortal['guest'] ? $objTeam->officialName : $roundFromPortal['opponentTeam'], $title);
					$title = str_replace("_H_", $roundFromPortal['guest'] ? $roundFromPortal['opponentTeam'] : $objTeam->officialName, $title);
					$title = str_replace("_L_", $objTeam->league, $title);
					$title = str_replace("_Y_", $objTerm->year, $title);
					$title = str_replace("_T_", self::yearToTerm($objTerm->year, true, true), $title);
					$title = str_replace("_N_", $objTeam->officialName, $title);
					$title = str_replace("_I_", $objTeam->internalName, $title);
					//backword compability:
					$title = str_replace("_S_", $objTeam->league, $title);
					
					$haveToSaveEvent = false;
					
					if ($objRound->eventId == null) {
						$objEvent = new \CalendarEventsModel();
						$objEvent->pid = $objTeam->calendar;
						$objEvent->published = 1;
						$alias = standardize(\StringUtil::restoreBasicEntities($title));
						if (\CalendarEventsModel::findByAlias($alias) !== null) $alias = $alias.'-p64-'.time();
						$objEvent->author = $objTeam->eventAuthor;
						$objEvent->alias = $alias;
						$haveToSaveEvent = true;
					}
					$startDate = $this->convertTimeFromPortalToUnix($roundFromPortal['date']);
					if ($objEvent->startDate !== $startDate) {
						$objEvent->startDate = $startDate;
						$haveToSaveEvent = true;
					}
					if ($haveToSave || $objEvent->startTime != $start) {
						$objEvent->startTime = $start;
						$haveToSaveEvent = true;
					}
					if (!$objEvent->addTime) {
						$objEvent->addTime = 1;
						$haveToSaveEvent = true;
					}
					$duration = $objTeam->eventDuration * 3600;
					$endTime = $start + $duration;
					if ((($start - $startDate) + $duration) > ($startDate + 86400)) $endDate = $startDate + ($duration % 86400);
					else $endDate = null;
					if ($objEvent->endTime != $endTime) {
						$objEvent->endTime = $endTime;
						$haveToSaveEvent = true;
					}
					if ($objEvent->endDate != $endDate) {
						$objEvent->endDate = $endDate;
						$haveToSaveEvent = true;
					}
					if (!$objEvent->createdByPortal64) {
						$objEvent->createdByPortal64 = 1;
						$haveToSaveEvent = true;
					}
					if ($title !== $objEvent->title) {
						$objEvent->title = $title;
						$haveToSaveEvent = true;
					}
					$location = $roundFromPortal['guest'] ? $portalTeams[$portalTeams['_keys']['teamname'][$roundFromPortal['opponentTeam']]]['teamLocation'] : (strlen($objTeam->homeLocation) > 0 ? $objTeam->homeLocation : $portalTeams[$objTeam->lotNumber]['teamLocation']);
					if ($objEvent->location !== $location) {
						$objEvent->location = $location;
						$haveToSaveEvent = true;
					}
					if ($haveToSaveEvent) {
						$objEvent->tstamp = time();
						$objEvent->save();
						if ($objEvent->id > 0) {
							if ($objRound->eventId !== $objEvent->id) {
								$objRound->eventId = $objEvent->id;
								$arrCalendarsEdited[] = $objEvent->pid;
								$haveToSave = true;
							}
						} else throw new \Exception('Could not save event!');
					}
				}
			}
			
			//Save?
			if ($haveToSave) {
				$objRound->tstamp = time();
				$objRound->save();
			}
			
			$arrGamePositions = [];
			//Add and update games
			if (is_array($roundFromPortal['games'])) {
				foreach ($roundFromPortal['games'] as $indexOfGameFromPortal => $gameFromPortal) {
					$positon = $gameFromPortal['position'];
					$arrGamePositions[] = $positon;
					$objGame = Portal64TeamRoundMatchModel::findByPositionAndRound($positon, $objRound->id);
					$haveToSaveGame = false;
					if ($objGame === null) {
						$haveToSaveGame = true;
						$objGame = new Portal64TeamRoundMatchModel();
						$objGame->pid = $objRound->id;
						$objGame->position = $positon;
					}
					if ($objGame->homeRank !== $gameFromPortal['rankHome']) {
						$objGame->homeRank = $gameFromPortal['rankHome'];
						$haveToSaveGame = true;
					}
					if ($objGame->guestRank !== $gameFromPortal['rankGuest']) {
						$objGame->guestRank = $gameFromPortal['rankGuest'];
						$haveToSaveGame = true;
					}
					if ($objGame->opponentName !== $gameFromPortal['opponent']) {
						$objGame->opponentName = $gameFromPortal['opponent'];
						$haveToSaveGame = true;
					}
					$resultHome = $gameFromPortal['result'] === null ? '*' : $gameFromPortal['result']['home'];
					$resultGuest = $gameFromPortal['result'] === null ? '*' : $gameFromPortal['result']['guest'];
					if ($objGame->resultHome != $resultHome) {
						$objGame->resultHome = $resultHome;
						$haveToSaveGame = true;
					}
					if ($objGame->resultGuest != $resultGuest) {
						$objGame->resultGuest = $resultGuest;
						$haveToSaveGame = true;
					}
					if ($haveToSaveGame) {
						$objGame->tstamp = time();
						$objGame->save();
					}
				}
			}
			//Delete games
			$collectionGames = Portal64TeamRoundMatchModel::findByPid($objRound->id);
			if ($collectionGames !== null) {
				while ($collectionGames->next()) {
					if (!in_array($collectionGames->position, $arrGamePositions)) $collectionGames->delete();
				}
			}
		}
		
		//Delete Rounds
		$collectionRounds = Portal64TeamRoundModel::findByPid($objTeam->id);
		if ($collectionRounds !== null) {
			while ($collectionRounds->next()) {
				if (!in_array(intval($collectionRounds->round), $arrRoundNumbers)) $collectionRounds->delete();
			}
		}
		
		//Trigger update of xml files
		if ($updateCalendar) {
			$this->Calendar->generateFeedsByCalendar($objTeam->calendar);
		}
		
		$this->log("Update of rounds of team $objTeam->internalName ($objTeam->id) successfully completed.", __METHOD__, TL_GENERAL);
		return true;
	}
	
	/**
	 * Converts date and time from string to timestamp
	 *
	 * @param string $date The date as String
	 * @param string $time The time as String
	 *
	 * @return int The timestamp
	 */
	protected function convertTimeFromPortalToUnix($date, $time = null)
	{
		if ($time !== null) return (new \Date($date." ".$time, 'd.m.Y H:i'))->tstamp;
		else return (new \Date($date, 'd.m.Y H:i'))->tstamp;
	}
	
	/**
	 * Updates all teams of latest term.
	 *
	 * @param bool $enforceUpdates Indicates whether an update of disabled teams and locked events should be enforced.
	 *                             (default false)
	 *
	 * @return int The amount of successfully updated teams
	 */
	public function updateAllTeams($enforceUpdates = false)
	{
		$objTerm = Portal64TermModel::findLatest();
		$count = 0;
		if ($objTerm !== null) {
			$collectionTeams = Portal64TeamModel::findByPid($objTerm->id);
			if ($collectionTeams !== null) {
				while ($collectionTeams->next()) {
					if ($enforceUpdates || !$collectionTeams->disableUpdates) {
						if ($this->updateTeam($collectionTeams->id, $enforceUpdates)) $count++;
					}
				}
			}
		}
		return $count;
	}
}

?>