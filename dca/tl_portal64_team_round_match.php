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

/*
 * Table tl_portal64_team_round_match
 */
$GLOBALS['TL_DCA']['tl_portal64_team_round_match'] = array(
	// Config
	'config' => array
	(
		'dataContainer'     => 'Table',
		'enabledVersioning' => false,
		'closed'            => true,
		'notEditable'       => true,
		'notDeletable'      => true,
		'ptable'            => 'tl_portal64_team_round',
		'sql'               => array(
			'keys' => array
			(
				'id'  => 'primary',
				'pid' => 'index'
			)
		)
	),
	
	// List
	'list'   => array(
		'sorting' => array(
			'mode'                  => 4,
			'fields'                => array('position ASC'),
			'headerFields'          => array('round', 'start'),
			'child_record_callback' => array('tl_portal64_team_round_match', 'displayMatch'),
			'disableGrouping'       => true,
		)
	),
	
	// Fields
	'fields' => array(
		'id'           => array
		(
			'sql' => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid'          => array
		(
			'foreignKey' => 'tl_portal64_team_round.round',
			'sql'        => "int(10) unsigned NOT NULL default '0'",
			'relation'   => array('type' => 'belongsTo', 'load' => 'eager')
		),
		'tstamp'       => array
		(
			'sql' => "int(10) unsigned NOT NULL default '0'"
		),
		'position'     => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_team_round_match']['position'],
			'exclude'   => true,
			'inputType' => 'text',
			'sql'       => "int(10) unsigned NOT NULL default '0'",
			'eval'      => array('tl_class' => 'w50', 'readonly' => true)
		),
		'homeRank'     => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_team_round_match']['homeRang'],
			'exclude'   => true,
			'inputType' => 'text',
			'sql'       => "int(10) unsigned NOT NULL default '0'",
			'eval'      => array('tl_class' => 'w50', 'readonly' => true)
		),
		'guestRank'    => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_team_round_match']['guestRank'],
			'exclude'   => true,
			'inputType' => 'text',
			'sql'       => "int(10) unsigned NOT NULL default '0'",
			'eval'      => array('tl_class' => 'w50', 'readonly' => true)
		),
		'opponentName' => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_team_round_match']['opponentName'],
			'exclude'   => true,
			'inputType' => 'text',
			'sql'       => "varchar(255) NOT NULL default ''",
			'eval'      => array('tl_class' => 'w50', 'readonly' => true)
		),
		'resultHome'   => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_team_round_match']['resultHome'],
			'exclude'   => true,
			'inputType' => 'text',
			'sql'       => "varchar(10) NOT NULL default ''",
			'eval'      => array('tl_class' => 'w50 clr', 'readonly' => true)
		),
		'resultGuest'  => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_team_round_match']['resultGuest'],
			'exclude'   => true,
			'inputType' => 'text',
			'sql'       => "varchar(10) NOT NULL default ''",
			'eval'      => array('tl_class' => 'w50', 'readonly' => true)
		),
	)
);

/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @author Julian Knorr
 */
class tl_portal64_team_round_match extends Backend
{
	
	/**
	 * Import the database object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('Database');
	}
	
	/**
	 * Returns a view (row) of a match
	 *
	 * @param array $arrRow The row from database
	 *
	 * @return string The Html code
	 */
	public function displayMatch($arrRow)
	{
		$objRound = \Portal64\Portal64TeamRoundModel::findByPk($arrRow['pid']);
		
		$return = '<div>';
		
		$return .= '<span style="display: inline-block; width: 40px;">'.$arrRow['homeRank'].'</span>';
		
		if ($objRound->isHome) {
			$objPlayer = \Portal64\Portal64TeamPlayerModel::findByTeamAndRank($objRound->pid, $arrRow['homeRank']);
			$return .= '<span style="display: inline-block; width: 200px;"><b>'.$objPlayer->name.'</b></span>';
		} else $return .= '<span style="display: inline-block; width: 200px;"><b>'.$arrRow["opponentName"].'</b></span>';
		
		$return .= ' - ';
		
		$return .= '<span style="display: inline-block; margin-left: 20px; width: 40px;">'.$arrRow['guestRank'].'</span>';
		
		if (!$objRound->isHome) {
			$objPlayer = \Portal64\Portal64TeamPlayerModel::findByTeamAndRank($objRound->pid, $arrRow['guestRank']);
			$return .= '<span style="display: inline-block; width: 200px;"><b>'.$objPlayer->name.'</b></span>';
		} else $return .= '<span style="display: inline-block; width: 200px;"><b>'.$arrRow["opponentName"].'</b></span>';
		
		$return .= '<b><span style="display: inline-block; width: 15px;">'.$arrRow["resultHome"].'</span>';
		$return .= ' - <span style="display: inline-block; width: 15px;">'.$arrRow["resultGuest"].'</span></b>';
		
		$return .= '</div>';
		return $return;
	}
	
}

?>