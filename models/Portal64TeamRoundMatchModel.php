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
 * Reads and writes matches (Portal64)
 *
 * @property integer $id
 * @property integer $tstamp
 * @property integer $pid
 * @property integer $position
 * @property integer $homeRank
 * @property boolean $guestRank
 * @property string $opponentName
 * @property string $resultHome
 * @property string $resultGuest
 *
 * @method static \Portal64TeamRoundMatchModel|null findById($id, $opt=array())
 * @method static \Portal64TeamRoundMatchModel|null findByPk($id, $opt=array())
 * @method static \Portal64TeamRoundMatchModel|null findOneBy($col, $val, $opt=array())
 * @method static \Portal64TeamRoundMatchModel|null findOneByTstamp($val, $opt=array())
 * @method static \Portal64TeamRoundMatchModel|null findOneByPid($val, $opt=array())
 * @method static \Portal64TeamRoundMatchModel|null findOneByPosition($val, $opt=array())
 * @method static \Portal64TeamRoundMatchModel|null findOneByHomeRank($val, $opt=array())
 * @method static \Portal64TeamRoundMatchModel|null findOneByGuestRank($val, $opt=array())
 * @method static \Portal64TeamRoundMatchModel|null findOneByOpponentName($val, $opt=array())
 * @method static \Portal64TeamRoundMatchModel|null findOneByResultHome($val, $opt=array())
 * @method static \Portal64TeamRoundMatchModel|null findOneByResultGuest($val, $opt=array())
 *
 * @method static \Portal64TeamRoundMatchModel|null findByTstamp($val, $opt=array())
 * @method static \Portal64TeamRoundMatchModel|null findByPid($val, $opt=array())
 * @method static \Portal64TeamRoundMatchModel|null findByPosition($val, $opt=array())
 * @method static \Portal64TeamRoundMatchModel|null findByHomeRank($val, $opt=array())
 * @method static \Portal64TeamRoundMatchModel|null findByGuestRank($val, $opt=array())
 * @method static \Portal64TeamRoundMatchModel|null findByOpponentName($val, $opt=array())
 * @method static \Portal64TeamRoundMatchModel|null findByResultHome($val, $opt=array())
 * @method static \Portal64TeamRoundMatchModel|null findByResultGuest($val, $opt=array())
 * @method static \Portal64TeamRoundMatchModel|null findByPositionAndRound($position, $idRound, $opt=array())
 * @method static \Model\Collection|\Portal64TeamRoundMatchModel[]|\Portal64TeamRoundMatchModel|null findMultipleByIds($val, $opt=array())
 * @method static \Model\Collection|\Portal64TeamRoundMatchModel[]|\Portal64TeamRoundMatchModel|null findBy($col, $val, $opt=array())
 * @method static \Model\Collection|\Portal64TeamRoundMatchModel[]|\Portal64TeamRoundMatchModel|null findAll($opt=array())
 *
 * @method static integer countById($id, $opt=array())
 * @method static integer countByTstamp($val, $opt=array())
 * @method static integer countByPid($val, $opt=array())
 * @method static integer countByPosition($val, $opt=array())
 * @method static integer countByHomeRank($val, $opt=array())
 * @method static integer countByGuestRank($val, $opt=array())
 * @method static integer countByOpponentName($val, $opt=array())
 * @method static integer countByResultHome($val, $opt=array())
 * @method static integer countByResultGuest($val, $opt=array())
 *
 * @author Julian Knorr
 */
class Portal64TeamRoundMatchModel extends \Model
{
	/**
	 * Name of the table
	 *
	 * @var string
	 */
	protected static $strTable = 'tl_portal64_team_round_match';
	
	/**
	 * Find a match by position and round
	 *
	 * @param integer $position
	 * @param integer $idRound
	 * @param array   $arrOptions
	 *
	 * @return \Portal64TeamRoundMatchModel|null The model or null if there is no player
	 */
	public static function findByPositionAndRound($position, $idRound, array $arrOptions = array())
	{
		$t = static::$strTable;
		
		$arrColumns = array("$t.position=? AND $t.pid=?");
		return static::findOneBy($arrColumns, array($position, $idRound), $arrOptions);
	}
}

?>