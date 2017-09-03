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
 * Reads and writes teams (Portal64)
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
 * @method static \Portal64TeamModel|null findById($id, $opt=array())
 * @method static \Portal64TeamModel|null findByPk($id, $opt=array())
 * @method static \Portal64TeamModel|null findOneBy($col, $val, $opt=array())
 * @method static \Portal64TeamModel|null findOneByTstamp($val, $opt=array())
 * @method static \Portal64TeamModel|null findOneByPid($val, $opt=array())
 * @method static \Portal64TeamModel|null findOneByInternalName($val, $opt=array())
 * @method static \Portal64TeamModel|null findOneByOfficialName($val, $opt=array())
 * @method static \Portal64TeamModel|null findOneByLeague($val, $opt=array())
 * @method static \Portal64TeamModel|null findOneByTid($val, $opt=array())
 * @method static \Portal64TeamModel|null findOneByLotNumber($val, $opt=array())
 * @method static \Portal64TeamModel|null findOneByLastUpdate($val, $opt=array())
 * @method static \Portal64TeamModel|null findOneByVkz($val, $opt=array())
 * @method static \Portal64TeamModel|null findOneByTeamster($val, $opt=array())
 * @method static \Portal64TeamModel|null findOneByDisableUpdates($val, $opt=array())
 * @method static \Portal64TeamModel|null findOneBySubstituteTeams($val, $opt=array())
 * @method static \Portal64TeamModel|null findOneByCalendar($val, $opt=array())
 * @method static \Portal64TeamModel|null findOneByManageCalendar($val, $opt=array())
 * @method static \Portal64TeamModel|null findOneByEventTitle($val, $opt=array())
 * @method static \Portal64TeamModel|null findOneByEventAuthor($val, $opt=array())
 * @method static \Portal64TeamModel|null findOneByEventDuration($val, $opt=array())
 * @method static \Portal64TeamModel|null findOneByHomeLocation($val, $opt=array())
 *
 * @method static \Portal64TeamModel|null findByTstamp($val, $opt=array())
 * @method static \Portal64TeamModel|null findByPid($val, $opt=array())
 * @method static \Portal64TeamModel|null findByInternalName($val, $opt=array())
 * @method static \Portal64TeamModel|null findByOfficialName($val, $opt=array())
 * @method static \Portal64TeamModel|null findByLeague($val, $opt=array())
 * @method static \Portal64TeamModel|null findByTid($val, $opt=array())
 * @method static \Portal64TeamModel|null findByLotNumber($val, $opt=array())
 * @method static \Portal64TeamModel|null findByLastUpdate($val, $opt=array())
 * @method static \Portal64TeamModel|null findByVkz($val, $opt=array())
 * @method static \Portal64TeamModel|null findByTeamster($val, $opt=array())
 * @method static \Portal64TeamModel|null findByDisableUpdates($val, $opt=array())
 * @method static \Portal64TeamModel|null findBySubstituteTeams($val, $opt=array())
 * @method static \Portal64TeamModel|null findByManageCalendar($val, $opt=array())
 * @method static \Portal64TeamModel|null findByCalendar($val, $opt=array())
 * @method static \Portal64TeamModel|null findByEventTitle($val, $opt=array())
 * @method static \Portal64TeamModel|null findByEventAuthor($val, $opt=array())
 * @method static \Portal64TeamModel|null findByEventDuration($val, $opt=array())
 * @method static \Portal64TeamModel|null findByHomeLocation($val, $opt=array())
 * @method static \Model\Collection|\Portal64TeamModel[]|\Portal64TeamModel|null findMultipleByIds($val, $opt=array())
 * @method static \Model\Collection|\Portal64TeamModel[]|\Portal64TeamModel|null findBy($col, $val, $opt=array())
 * @method static \Model\Collection|\Portal64TeamModel[]|\Portal64TeamModel|null findAll($opt=array())
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
class Portal64TeamModel extends \Model
{
	/**
	 * Name of the table
	 *
	 * @var string
	 */
	protected static $strTable = 'tl_portal64_team';
	
	/**
	 * Find the most outdated team of a term
	 *
	 * @param integer $idTerm
	 * @param array   $arrOptions
	 *
	 * @return \Portal64TeamModel|null The model or null if there is no team
	 */
	public static function findMostOutdatedByTerm($idTerm, array $arrOptions = array())
	{
		$t = static::$strTable;
		
		$arrColumns = array("$t.pid=?");
		$arrOptions['limit'] = 1;
		$arrOptions['order'] = "$t.lastUpdate ASC";
		$arrOptions['return'] = 'Model';
		return static::findOneBy($arrColumns, array($idTerm), $arrOptions);
	}
}

?>