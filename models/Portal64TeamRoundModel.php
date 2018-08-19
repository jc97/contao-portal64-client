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
 * Reads and writes rounds (Portal64)
 *
 * @property integer $id
 * @property integer $tstamp
 * @property integer $pid
 * @property integer $round
 * @property integer $start
 * @property boolean $isHome
 * @property string $opponent
 * @property integer eventId
 *
 * @method static \Portal64TeamRoundModel|null findById($id, $opt=array())
 * @method static \Portal64TeamRoundModel|null findByPk($id, $opt=array())
 * @method static \Portal64TeamRoundModel|null findOneBy($col, $val, $opt=array())
 * @method static \Portal64TeamRoundModel|null findOneByTstamp($val, $opt=array())
 * @method static \Portal64TeamRoundModel|null findOneByPid($val, $opt=array())
 * @method static \Portal64TeamRoundModel|null findOneByRound($val, $opt=array())
 * @method static \Portal64TeamRoundModel|null findOneByStart($val, $opt=array())
 * @method static \Portal64TeamRoundModel|null findOneByIsHome($val, $opt=array())
 * @method static \Portal64TeamRoundModel|null findOneByOpponent($val, $opt=array())
 * @method static \Portal64TeamRoundModel|null findOneByEventId($val, $opt=array())
 *
 * @method static \Portal64TeamRoundModel|null findByTstamp($val, $opt=array())
 * @method static \Portal64TeamRoundModel|null findByPid($val, $opt=array())
 * @method static \Portal64TeamRoundModel|null findByRound($val, $opt=array())
 * @method static \Portal64TeamRoundModel|null findByStart($val, $opt=array())
 * @method static \Portal64TeamRoundModel|null findByIsHome($val, $opt=array())
 * @method static \Portal64TeamRoundModel|null findByOpponent($val, $opt=array())
 * @method static \Portal64TeamRoundModel|null findByEventId($val, $opt=array())
 * @method static \Model\Collection|\Portal64TeamRoundModel[]|\Portal64TeamRoundModel|null findMultipleByIds($val, $opt=array())
 * @method static \Model\Collection|\Portal64TeamRoundModel[]|\Portal64TeamRoundModel|null findBy($col, $val, $opt=array())
 * @method static \Model\Collection|\Portal64TeamRoundModel[]|\Portal64TeamRoundModel|null findAll($opt=array())
 *
 * @method static integer countById($id, $opt=array())
 * @method static integer countByTstamp($val, $opt=array())
 * @method static integer countByPid($val, $opt=array())
 * @method static integer countByRound($val, $opt=array())
 * @method static integer countByStart($val, $opt=array())
 * @method static integer countByIsHome($val, $opt=array())
 * @method static integer countByOpponent($val, $opt=array())
 * @method static integer countByEventId($val, $opt=array())
 *
 * @author Julian Knorr
 */
class Portal64TeamRoundModel extends \Model
{
	/**
	 * Name of the table
	 *
	 * @var string
	 */
	protected static $strTable = 'tl_portal64_team_round';
	
	/**
	 * Find a round by team and number
	 *
	 * @param integer $idTeam
	 * @param integer $number
	 * @param array   $arrOptions
	 *
	 * @return \Portal64TeamPlayerModel|null The model or null if there is no player
	 */
	public static function findByTeamAndNumber($idTeam, $number, array $arrOptions = array())
	{
		$t = static::$strTable;
		
		$arrColumns = array("$t.pid=? AND $t.round=?");
		return static::findOneBy($arrColumns, array($idTeam, $number), $arrOptions);
	}
}

?>