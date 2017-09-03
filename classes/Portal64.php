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
 * Retrives data of teams from Portal64.de
 *
 * This class supports importing and updating teams and their players, results and events.
 *
 * @author Julian Knorr
 */
class Portal64 extends \System
{
	
	const titleXPath = '//div[@id="col3_content"]/h2';
	const titleRegEx = '/^\s*(\w[\wÄÖÜäöüß\., -]+): Übersicht über Mannschaften/';
	const teamHeaderXPath = '//div[@id="col3_content"]/h3';
	const teamHeaderRegEx = '/^\s*Losnr.: (\d+): ([\wÄÖÜäöüß]+[\wÄÖÜäöüß \/-]*)[^\w ]+ VKZ: (\d+)\s*/';
	
	/**
	 * Cache at runtime for html data
	 *
	 * @var array
	 */
	protected $htmlCache = [];
	
	/**
	 * Cache at runtime for xml data
	 *
	 * @var array
	 */
	protected $xmlCache = [];
	
	/**
	 * Import the database object and config libxml
	 */
	public function __construct()
	{
		parent::__construct();
		libxml_use_internal_errors(true);
		if (filter_var($GLOBALS['TL_CONFIG']['portal64Link'], FILTER_VALIDATE_URL) === false) {
			throw new \Exception('No valid URL for portal64 is configured.');
		}
	}
	
	/**
	 * Calculate points from htmlencoded result.
	 *
	 * @param string $htmlResult The htmlencoded result of one player
	 *
	 * @return float|bool The points or false on invalid input
	 */
	public static function htmlResultToPoints($htmlResult)
	{
		switch ($htmlResult) {
			case '&frac12;':
				return 0.5;
			case '1':
			case '+':
				return 1;
			case '0':
			case '-':
			case '*':
				return 0;
			default:
				return false;
		}
	}
	
	/**
	 * Queries rounds of teams from the portal
	 *
	 * This function queries the following metadata of the round:
	 * Date and Time, opponent and indicates whether the match takes place at home
	 *
	 * @param int  $tid         The id of the tournament of the team
	 * @param int  $lotNumber   The lot number of the team
	 * @param bool $bypassCache (optional) Bypass the cache (runtime)
	 *
	 * @return array|bool The rounds or false on error
	 */
	public function getRoundsOfTeamFromPortal($tid, $teamName, $bypassCache = false)
	{
		$rounds = [];
		$teamName = self::innerTrim($teamName);
		
		$currentRound = 1;
		while (true) {
			$url = self::buildPortalUrl('round_xml', ['tid' => $tid, 'round' => $currentRound]);
			$roundXml = $this->getXmlDataFromPortal($url, [
				['expression' => '/ergebnisse', 'max' => 1],
				['expression' => '/ergebnisse/spieltag', 'max' => 1],
				['expression' => '/ergebnisse/spieltag/runde', 'max' => 1]
			]);
			if ($roundXml === false) return false;
			$xPath = new \DOMXPath($roundXml);
			
			$roundNumberNode = $xPath->query('/ergebnisse/spieltag/runde')->item(0);
			$roundNumber = intval($roundNumberNode->nodeValue);
			if ($roundNumber !== $currentRound) break;
			
			$roundDateNodes = $xPath->query('/ergebnisse/spieltag/datum');
			if ($roundDateNodes->length === 1) $roundDate = $roundDateNodes->item(0)->textContent;
			else $roundDate = null;
			$roundTimeNodes = $xPath->query('/ergebnisse/spieltag/uhrzeit');
			if ($roundTimeNodes->length === 1) $roundTime = $roundTimeNodes->item(0)->textContent;
			else $roundTime = null;
			
			$matchNodes = $xPath->query('/ergebnisse/begegnung');
			$matchCount = $matchNodes->length;
			if ($matchCount === 0 || !is_int($matchCount)) break;
			for ($match = 0; $match < $matchCount; $match++) {
				$matchXml = $matchNodes->item($match);
				$homeTeamNodes = $xPath->query('heimmannschaft/name', $matchXml);
				if ($homeTeamNodes->length !== 1) return false;
				$homeTeam = self::innerTrim($homeTeamNodes->item(0)->textContent);
				$guestTeamNodes = $xPath->query('gastmannschaft/name', $matchXml);
				if ($guestTeamNodes->length !== 1) return false;
				$guestTeam = self::innerTrim($guestTeamNodes->item(0)->textContent);
				if ($homeTeam === $teamName) {
					$guest = false;
					$opponentTeam = $guestTeam;
				} else if ($guestTeam === $teamName) {
					$guest = true;
					$opponentTeam = $homeTeam;
				} else continue;
				$round = [
					'roundNumber'  => $currentRound,
					'opponentTeam' => self::innerTrim($opponentTeam),
					'guest'        => $guest,
					'date'         => self::innerTrim($roundDate),
					'time'         => self::innerTrim($roundTime),
				];
				$gameNodes = $xPath->query('einzelergebnis', $matchXml);
				if ($gameNodes->length > 0) {
					$games = [];
					for ($game = 0; $game < $gameNodes->length; $game++) {
						$gameXml = $gameNodes->item($game);
						$positionNodes = $xPath->query("brettnr", $gameXml);
						if ($positionNodes->length !== 1) return false;
						$position = intval($positionNodes->item(0)->nodeValue);
						$rankHomeNodes = $xPath->query("RangnrHeim", $gameXml);
						if ($rankHomeNodes->length !== 1) return false;
						$rankHome = intval($rankHomeNodes->item(0)->nodeValue);
						$rankGuestNodes = $xPath->query("RangnrGast", $gameXml);
						if ($rankGuestNodes->length !== 1) return false;
						$rankGuest = intval($rankGuestNodes->item(0)->nodeValue);
						if ($guest) $opponentNameTag = "heimspieler";
						else $opponentNameTag = "gastspieler";
						$forenameNodes = $xPath->query($opponentNameTag.'/vorname', $gameXml);
						if ($forenameNodes->length !== 1) return false;
						$forename = self::innerTrim($forenameNodes->item(0)->textContent);
						$surenameNodes = $xPath->query($opponentNameTag.'/nachname', $gameXml);
						if ($surenameNodes->length !== 1) return false;
						$surename = self::innerTrim($surenameNodes->item(0)->textContent);
						if (strlen($surename) > 0 && strlen($forename) > 0) $opponentPlayer = $surename.", ".$forename;
						else if (strlen($surename) > 0) $opponentPlayer = $surename;
						else if (strlen($forename) > 0) $opponentPlayer = $forename;
						else $opponentPlayer = null;
						$gameArray = [
							'position'  => $position,
							'rankHome'  => $rankHome,
							'rankGuest' => $rankGuest,
							'opponent'  => $opponentPlayer
						];
						$resultNodes = $xPath->query("ergebnis", $gameXml);
						if ($resultNodes->length === 1) {
							$homeResultNodes = $xPath->query("ergebnis/heimergebnis", $gameXml);
							if ($homeResultNodes->length !== 1) return false;
							$guestResultNodes = $xPath->query("ergebnis/gastergebnis", $gameXml);
							if ($guestResultNodes->length !== 1) return false;
							$result = [
								'home'  => $homeResultNodes->item(0)->nodeValue,
								'guest' => $guestResultNodes->item(0)->nodeValue
							];
							$gameArray['result'] = $result;
						} else $gameArray['result'] = null;
						$games[] = $gameArray;
					}
					$round['games'] = $games;
				} else $round['games'] = null;
				$rounds[] = $round;
			}
			if (count($rounds) !== $currentRound) break;
			$currentRound++;
		}
		if (count($rounds) > 0) return $rounds;
		else return false;
	}
	
	/**
	 * Like trim(), but also replace all sequences of whitespaces inside the string, not only at the end.
	 *
	 * Caution: Also \t will be replaced by a single space.
	 *
	 * @param string $string The string to trim
	 *
	 * @return string The trimmed string
	 */
	protected static function innerTrim($string)
	{
		return preg_replace('/\s+/', ' ', $string);
	}
	
	/**
	 * Builds an url for a query to Portal64.
	 *
	 * @param string $request The type of request
	 * @param array|null (optional) $options The options corresponding to the type of request
	 *
	 * @return bool|string The url or false on error
	 */
	public static function buildPortalUrl($request, $options = [])
	{
		$url = $GLOBALS['TL_CONFIG']['portal64Link'];
		if (substr($url, -1) !== "/") $url .= "/";
		switch ($request) {
			case 'teams':
				if (!isset($options['tid'])) return false;
				$url .= 'ergebnisse/show/';
				$url .= isset($options['term']) ? $options['term'] : date('Y', time());
				$url .= '/'.$options['tid'];
				$url .= '/aufstellungen/';
				break;
			case 'round_xml':
				if (!isset($options['tid'])) return false;
				$url .= 'tools/export/runde.php?tid='.$options['tid'];
				if (isset($options['round'])) $url .= "&runde=".$options['round'];
				break;
			case 'league_home':
				if (!isset($options['tid'])) return false;
				if (!isset($options['term'])) return false;
				$url .= 'ergebnisse/show/'.$options['term'].'/'.$options['tid'].'/';
				break;
		}
		return $url;
	}
	
	/**
	 * Download, parse and verify XML output from Portal64.
	 *
	 * @param string     $url                The Url
	 * @param array|null $checkOptions       (optional) Null or an array of arrays containing XPath expressions,
	 *                                       optional a minimum length or maximum length for the result and optional a
	 *                                       RegEx for the node's value.
	 * @param bool       $preserveWhiteSpace (optional) Passed to DOMDocument
	 * @param bool       $bypassCache        (optional) Bypass the cache (runtime)
	 *
	 * @return bool|DOMDocument
	 */
	protected function getXmlDataFromPortal($url, $checkOptions = null, $preserveWhiteSpace = true,
											$bypassCache = false)
	{
		if (!$bypassCache && isset($this->htmlCache[$url])) {
			$rawXml = $this->xmlCache[$url];
		} else {
			$rawXml = file_get_contents($url);
			if (!$rawXml) {
				$this->log("Could not download $url", __METHOD__, TL_ERROR);
				return false;
			}
			$this->xmlCache[$url] = $rawXml;
		}
		$Xml = new \DOMDocument();
		$Xml->preserveWhiteSpace = $preserveWhiteSpace;
		if (!$Xml->loadXML($rawXml)) {
			$this->log("Could not parse $url", __METHOD__, TL_ERROR);
			return false;
		}
		if (is_array($checkOptions)) {
			if (!$this->checkDocument($Xml, $checkOptions)) return false;
		}
		return $Xml;
	}
	
	/**
	 * Verifies whether a xml document holds to some rules.
	 *
	 * @param \DOMDocument $document     The document to verify
	 * @param array        $checkOptions The rules
	 *
	 * @return bool Indicates whether the document is valid concerning the given rules.
	 */
	protected function checkDocument($document, $checkOptions)
	{
		if (!is_array($checkOptions)) return false;
		if (!$document instanceof \DOMDocument) return false;
		$xPath = new \DOMXPath($document);
		foreach ($checkOptions as $checkOption) {
			if (!isset($checkOption['expression'])) return false;
			$nodeList = $xPath->query($checkOption['expression']);
			if (isset($checkOption['min']) && $nodeList->length < $checkOption['min']) return false;
			else if (!isset($checkOption['min']) && $nodeList->length == 0) return false;
			if (isset($checkOption['max']) && $nodeList->length > $checkOption['max']) return false;
			if (isset($checkOption['regex'])) {
				if ($nodeList->length > 0) {
					for ($i = 0; $i < $nodeList->length; $i++) {
						$node = $nodeList->item($i)->nodeValue;
						if (preg_match($checkOption['regex'], $node) !== 1) return false;
					}
				}
			}
		}
		return true;
	}
	
	/**
	 * Queries metadata and players of teams from the portal
	 *
	 * This function queries the following metadata of the team:
	 * Official name of team, league of team, vkz of team's club, teamster
	 *
	 * @param int      $term        The term of the team
	 * @param int      $tid         The id of the tournament of the team
	 * @param int|null $lotNumber   (optional) The lot number of the team or null to get data of all teams
	 * @param bool     $bypassCache (optional) Bypass the cache (runtime)
	 *
	 * @return array|bool The metadata of the team or false on error
	 */
	public function getTeamsFromPortal($term, $tid, $lotNumber = null, $bypassCache = false)
	{
		$teams = [];
		$keys = ['teamname' => []];
		
		$url = self::buildPortalUrl('teams', ['term' => $term, 'tid' => $tid]);
		
		$html = $this->getHtmlDataFromPortal($url, [
			['expression' => self::titleXPath, 'max' => 1, 'regex' => self::titleRegEx],
			['expression' => self::teamHeaderXPath, 'min' => $lotNumber === null ? 1 : $lotNumber, 'regex' => self::teamHeaderRegEx]
		], $bypassCache);
		if (!$html) return false;
		$xPath = new \DOMXPath($html);
		
		$title = $xPath->query(self::titleXPath)->item(0)->nodeValue;
		$regexMatches = [];
		if (preg_match(self::titleRegEx, $title, $regexMatches) !== 1) return false;
		if (count($regexMatches) !== 2) return false;
		$league = $regexMatches[1];
		
		$teamsCount = $xPath->query(self::teamHeaderXPath)->length;
		for ($teamIndex = 0; $teamIndex < $teamsCount; $teamIndex++) {
			
			$teamHeader = $xPath->query(self::teamHeaderXPath)->item($teamIndex)->nodeValue;
			if (preg_match(self::teamHeaderRegEx, $teamHeader, $regexMatches) !== 1) return false;
			if (count($regexMatches) !== 4) return false;
			$currentLotNumber = intval($regexMatches[1]);
			if ($currentLotNumber == 0) return false;
			if ($lotNumber !== null && $currentLotNumber !== $lotNumber) continue;
			$teamname = trim($regexMatches[2]);
			$vkz = intval($regexMatches[3]);
			
			$teamContactsXPath = '(//div[@id="col3_content"]/table[@class="aufstellung"] | //div[@id="col3_content"]/table[@class="aufstellungElo"])['.($teamIndex + 1).']/tbody[@class="foot"]/tr/td';
			$teamContactsNodes = $xPath->query($teamContactsXPath);
			$teamster = null;
			$teamLocation = null;
			$players = [];
			
			//Get teamster and location
			for ($i = 0; $i < $teamContactsNodes->length; $i++) {
				$teamContactNode = $teamContactsNodes->item($i);
				$nodeValue = self::innerTrim($teamContactNode->nodeValue);
				if ($teamster === null && preg_match('/^\s*Mannschaftsführer: ([\wÄÖÜäöüß]+[\wAÖÜäöüß \.-]*),/', $nodeValue, $regexMatches) == 1) {
					if (count($regexMatches) == 2) {
						$teamster = trim($regexMatches[1]);
					}
				}
				if ($teamLocation === null && preg_match('/^\s*Sportstätte:\s+([\wÄÖÜäöüß]+[\wAÖÜäöüß ,\.-]*)/', $nodeValue, $regexMatches) == 1) {
					if (count($regexMatches) == 2) {
						$teamLocation = trim($regexMatches[1]);
					}
				}
			}
			
			//Get players
			$teamPlayersXPath = '(//div[@id="col3_content"]/table[@class="aufstellung"] | //div[@id="col3_content"]/table[@class="aufstellungElo"])['.($teamIndex + 1).']/tbody[1]/tr';
			$teamPlayersTrNodeList = $xPath->query($teamPlayersXPath);
			for ($i = 0; $i < $teamPlayersTrNodeList->length * 2; $i++) {
				$offset = $i >= $teamPlayersTrNodeList->length ? 1 : 0;
				$rowIndex = $i % $teamPlayersTrNodeList->length;
				$htmlTr = $teamPlayersTrNodeList->item($rowIndex);
				$player = $this->getPlayerFromTr($htmlTr, $offset);
				if (is_array($player)) {
					$players[] = $player;
				}
			}
			
			$teams[$currentLotNumber] = [
				'teamname'     => $teamname,
				'vkz'          => $vkz,
				'league'       => $league,
				'tid'          => $tid,
				'term'         => $term,
				'lotNumber'    => $currentLotNumber,
				'teamster'     => $teamster,
				'teamLocation' => $teamLocation,
				'players'      => $players,
				'index'        => $teamIndex,
			];
			$keys['teamname'][$teamname] = $currentLotNumber;
		}
		
		$teams['_keys'] = $keys;
		return count($teams) > 0 ? $teams : false;
	}
	
	/**
	 * Download, parse and verify HTML output from Portal64.
	 *
	 * @param string     $url                The Url
	 * @param array|null $checkOptions       (optional) Null or an array of arrays containing XPath expressions,
	 *                                       optional a minimum length or maximum length for the result and optional a
	 *                                       RegEx for the node's value.
	 * @param bool       $preserveWhiteSpace (optional) Passed to DOMDocument
	 * @param bool       $bypassCache        (optional) Bypass the cache (runtime)
	 *
	 * @return bool|DOMDocument
	 */
	protected function getHtmlDataFromPortal($url, $checkOptions = null, $preserveWhiteSpace = false,
											 $bypassCache = false)
	{
		if (!$bypassCache && isset($this->htmlCache[$url])) {
			$rawHtml = $this->htmlCache[$url];
		} else {
			$rawHtml = file_get_contents($url);
			if (!$rawHtml) {
				$this->log("Could not download $url", __METHOD__, TL_ERROR);
				return false;
			}
			$this->htmlCache[$url] = $rawHtml;
		}
		$Html = new \DOMDocument();
		$Html->preserveWhiteSpace = $preserveWhiteSpace;
		if (!$Html->loadHTML($rawHtml)) {
			$this->log("Could not parse $url", __METHOD__, TL_ERROR);
			return false;
		}
		if (is_array($checkOptions)) {
			if (!$this->checkDocument($Html, $checkOptions)) return false;
		}
		return $Html;
	}
	
	/**
	 * Extracts a player from a tr-Element
	 *
	 * @param \DOMNode $htmlTr The tr-Element
	 * @param int      $offset (optional) The offset, if multiple players saved in one row
	 *
	 * @return array|bool The player or false on error
	 */
	protected function getPlayerFromTr($htmlTr, $offset = 0)
	{
		$xPath = new \DOMXPath($htmlTr->ownerDocument);
		$tdNodeList = $xPath->query('td', $htmlTr);
		if ($tdNodeList->length === 10) $containsElo = true;
		else if ($tdNodeList->length === 8) $containsElo = false;
		else return false;
		
		$player = [];
		
		$tdOffset = $containsElo ? $offset * 5 : $offset * 4;
		
		$rankNode = $tdNodeList->item($tdOffset);
		$nameNode = $tdNodeList->item($tdOffset + 1);
		$memberNumberNode = $tdNodeList->item($tdOffset + 2);
		$dwzNode = $tdNodeList->item($tdOffset + 3);
		if ($containsElo) $eloNode = $tdNodeList->item($tdOffset + 4);
		
		if ($rankNode->nodeValue != '') {
			$player['rank'] = intval(trim($rankNode->nodeValue));
			$player['name'] = self::innerTrim($nameNode->nodeValue);
			$player['memberNumber'] = self::innerTrim($memberNumberNode->nodeValue);
			$player['dwz'] = self::innerTrim($dwzNode->nodeValue);
			if ($containsElo) $player['elo'] = self::innerTrim($eloNode->nodeValue);
			else $player['elo'] = null;
			return $player;
		}
		
		return false;
	}
}

?>