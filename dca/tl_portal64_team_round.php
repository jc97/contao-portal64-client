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
 * Table tl_portal64_team_round
 */
$GLOBALS['TL_DCA']['tl_portal64_team_round'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'     => 'Table',
		'enabledVersioning' => false,
		'closed'            => true,
		'notEditable'       => true,
		'notDeletable'      => true,
		'ptable'            => 'tl_portal64_team',
		'ctable'            => array('tl_portal64_team_round_match'),
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
		'sorting'    => array(
			'mode'                  => 4,
			'fields'                => array('round ASC'),
			'headerFields'          => array('internalName'),
			'child_record_callback' => array('tl_portal64_team_round', 'displayRound'),
			'disableGrouping'       => true
		),
		'operations' => array(
			'results' => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_portal64_team_round_match']['results'],
				'href'  => 'table=tl_portal64_team_round_match',
				'icon'  => 'show.gif'
			),
		)
	),
	
	// Fields
	'fields' => array
	(
		'id'       => array
		(
			'sql' => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid'      => array
		(
			'foreignKey' => 'tl_portal64_team.internalName',
			'sql'        => "int(10) unsigned NOT NULL default '0'",
			'relation'   => array('type' => 'belongsTo', 'load' => 'eager')
		),
		'tstamp'   => array
		(
			'sql' => "int(10) unsigned NOT NULL default '0'"
		),
		'round'    => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_team_round']['round'],
			'exclude'   => true,
			'inputType' => 'text',
			'sql'       => "int(10) unsigned NOT NULL default '0'",
			'eval'      => array('tl_class' => 'w50', 'readonly' => true)
		),
		'start'    => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_team_round']['start'],
			'exclude'   => true,
			'inputType' => 'text',
			'eval'      => array('rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard', 'readonly' => true),
			'sql'       => "varchar(10) NOT NULL default ''"
		),
		'isHome'   => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_team_round']['isHome'],
			'exclude'   => true,
			'inputType' => 'checkbox',
			'sql'       => "char(1) NOT NULL default ''",
			'eval'      => array('tl_class' => 'w50', 'readonly' => true)
		),
		'opponent' => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_team_round']['opponent'],
			'exclude'   => true,
			'inputType' => 'text',
			'sql'       => "varchar(255) NOT NULL default ''",
			'eval'      => array('tl_class' => 'w50', 'readonly' => true)
		),
		'eventId'  => array(
			'sql' => "int(10) unsigned NOT NULL default '0'"
		)
	)
);

/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @author Julian Knorr
 */
class tl_portal64_team_round extends Backend
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
	 * Returns a view (row) of a round
	 *
	 * @param array $arrRow The row from database
	 *
	 * @return string The Html code
	 */
	public function displayRound($arrRow)
	{
		$return = '<div>';
		$return .= '<b>'.$arrRow["round"].'. '.$GLOBALS['TL_LANG']['tl_portal64_team_round']['round'][0].'</b><br />';
		$return .= '<b>'.date("d.m.Y H:i", $arrRow["start"]).'</b><br />';
		$return .= $arrRow["opponent"].'<br />';
		if ($arrRow["isHome"] == "1") $return .= '<span style="padding-left:3px;color:#b3b3b3;">'.$GLOBALS['TL_LANG']['tl_portal64_team_round']['isHome'][0].'</span><br />';
		$return .= '<br />';
		
		$collectionMatches = \Portal64\Portal64TeamRoundMatchModel::findByPid($arrRow['id']);
		
		if ($collectionMatches !== null) {
			$thisTeamPoints = 0;
			$opponentTeamPoints = 0;
			while ($collectionMatches->next()) {
				if ($arrRow['isHome']) {
					$thisTeamPoints += Portal64\Portal64::htmlResultToPoints($collectionMatches->resultHome);
					$opponentTeamPoints += Portal64\Portal64::htmlResultToPoints($collectionMatches->resultGuest);
				} else {
					$thisTeamPoints += Portal64\Portal64::htmlResultToPoints($collectionMatches->resultGuest);
					$opponentTeamPoints += Portal64\Portal64::htmlResultToPoints($collectionMatches->resultHome);
				}
			}
			$return .= '<b>'.$thisTeamPoints.' - '.$opponentTeamPoints.'</b>';
		} else $return .= '<span style="padding-left:3px;color:#b3b3b3;">'.$GLOBALS['TL_LANG']['tl_portal64_team_round']['noresults'].'</span>';
		$return .= '</div>';
		return $return;
	}
	
}

?>