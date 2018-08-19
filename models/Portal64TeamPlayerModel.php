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
 * Reads and writes players (Portal64)
 *
 * @property integer $id
 * @property integer $tstamp
 * @property integer $pid
 * @property string $internalName
 * @property string $officialName
 * @property string $league
 * @property integer $tid
 * @property integer $lotNumber
 * @property integer $lastUpdate
 * @property string $vkz
 * @property string $teamster
 * @property boolean $disableUpdates
 * @property string $substituteTeams
 * @property boolean $manageCalendar
 * @property integer $calendar
 * @property string $eventTitle
 * @property integer $eventAuthor
 * @property integer $eventDuration
 * @property string $homeLocation
 *
 * @method static \Portal64TeamPlayerModel|null findById($id, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findByPk($id, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findOneBy($col, $val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findOneByTstamp($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findOneByPid($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findOneByInternalName($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findOneByOfficialName($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findOneByLeague($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findOneByTid($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findOneByLotNumber($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findOneByLastUpdate($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findOneByVkz($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findOneByTeamster($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findOneByDisableUpdates($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findOneBySubstituteTeams($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findOneByCalendar($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findOneByManageCalendar($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findOneByEventTitle($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findOneByEventAuthor($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findOneByEventDuration($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findOneByHomeLocation($val, $opt=array())
 *
 * @method static \Portal64TeamPlayerModel|null findByTstamp($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findByPid($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findByInternalName($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findByOfficialName($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findByLeague($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findByTid($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findByLotNumber($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findByLastUpdate($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findByVkz($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findByTeamster($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findByDisableUpdates($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findBySubstituteTeams($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findByManageCalendar($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findByCalendar($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findByEventTitle($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findByEventAuthor($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findByEventDuration($val, $opt=array())
 * @method static \Portal64TeamPlayerModel|null findByHomeLocation($val, $opt=array())
 * @method static \Model\Collection|\Portal64TeamPlayerModel[]|\Portal64TeamPlayerModel|null findMultipleByIds($val, $opt=array())
 * @method static \Model\Collection|\Portal64TeamPlayerModel[]|\Portal64TeamPlayerModel|null findByTeam($idTeam, $opt=array())
 * @method static \Model\Collection|\Portal64TeamPlayerModel[]|\Portal64TeamPlayerModel|null findBy($col, $val, $opt=array())
 * @method static \Model\Collection|\Portal64TeamPlayerModel[]|\Portal64TeamPlayerModel|null findAll($opt=array())
 *
 * @method static integer countById($id, $opt=array())
 * @method static integer countByTstamp($val, $opt=array())
 * @method static integer countByPid($val, $opt=array())
 * @method static integer countByInternalName($val, $opt=array())
 * @method static integer countByOfficialName($val, $opt=array())
 * @method static integer countByLeague($val, $opt=array())
 * @method static integer countByTid($val, $opt=array())
 * @method static integer countByLotNumber($val, $opt=array())
 * @method static integer countByLastUpdate($val, $opt=array())
 * @method static integer countByVkz($val, $opt=array())
 * @method static integer countByTeamster($val, $opt=array())
 * @method static integer countByDisableUpdates($val, $opt=array())
 * @method static integer countBySubstituteTeams($val, $opt=array())
 * @method static integer countByManageCalendar($val, $opt=array())
 * @method static integer countByCalendar($val, $opt=array())
 * @method static integer countByEventTitle($val, $opt=array())
 * @method static integer countByEventAuthor($val, $opt=array())
 * @method static integer countByEventDuration($val, $opt=array())
 * @method static integer countByHomeLocation($val, $opt=array())
 *
 * @author Julian Knorr
 */
class Portal64TeamPlayerModel extends \Model
{
	/**
	 * Name of the table
	 *
	 * @var string
	 */
	protected static $strTable = 'tl_portal64_team_player';
	
	/**
	 * Find all players of a team
	 *
	 * @param id    $idTeam     The team
	 * @param array $arrOptions An optional options array
	 *
	 * @return \\Model\Collection|\Portal64TeamPlayerModel[]|\Portal64TeamPlayerModel|null The collection, a model or
	 *                                                                                     null if there is no player
	 */
	public static function findByTeam($idTeam, array $arrOptions = array())
	{
		$t = static::$strTable;
		
		$arrColumns = array("$t.partOfTeam=1 AND $t.pid=?");
		return static::findBy($arrColumns, array($idTeam), $arrOptions);
	}
	
	/**
	 * Find a player by team and rank
	 *
	 * @param integer $idTeam
	 * @param integer $rank
	 * @param array   $arrOptions
	 *
	 * @return \Portal64TeamPlayerModel|null The model or null if there is no player
	 */
	public static function findByTeamAndRank($idTeam, $rank, array $arrOptions = array())
	{
		$t = static::$strTable;
		
		$arrColumns = array("$t.partOfTeam=1 AND $t.pid=? AND $t.rank=?");
		return static::findOneBy($arrColumns, array($idTeam, $rank), $arrOptions);
	}
	
	/**
	 * Find an opponent player by managed team and name
	 *
	 * @param integer $idTeam
	 * @param string  $name
	 * @param array   $arrOptions
	 *
	 * @return \Portal64TeamPlayerModel|null The model or null if there is no player
	 */
	public static function findOpponentByTeamAndName($idTeam, $name, array $arrOptions = array())
	{
		$t = static::$strTable;
		
		$arrColumns = array("$t.partOfTeam=0 AND $t.name=? AND $t.pid=?");
		return static::findOneBy($arrColumns, array($name, $idTeam), $arrOptions);
	}
	
	/**
	 * Find a player by teams and rank
	 *
	 * @param array   $arrTeams
	 * @param integer $rank
	 * @param array   $arrOptions
	 *
	 * @return \Portal64TeamPlayerModel|null The model or null if there is no player
	 */
	public static function findByTeamsAndRank($arrTeams, $rank, array $arrOptions = array())
	{
		$t = static::$strTable;
		
		if (!is_array($arrTeams)) return null;
		
		$arrColumns = array("$t.partOfTeam=1 AND $t.pid IN(".implode(',', array_map('intval', $arrTeams)).") AND $t.rank=?");
		
		return static::findOneBy($arrColumns, array($rank), $arrOptions);
	}
}

?>