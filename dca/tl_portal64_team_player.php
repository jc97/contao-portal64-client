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
 * Table tl_portal64_team_player
 */
$GLOBALS['TL_DCA']['tl_portal64_team_player'] = array
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
			'fields'                => array('rank ASC'),
			'headerFields'          => array('internalName'),
			'child_record_callback' => array('tl_portal64_team_player', 'displayPlayer'),
			'disableGrouping'       => true,
			'filter'                => array(
				array('partOfTeam=?', '1')
			)
		)
	),
	
	// Fields
	'fields' => array
	(
		'id'           => array
		(
			'sql' => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid'          => array
		(
			'foreignKey' => 'tl_portal64_team.internalName',
			'sql'        => "int(10) unsigned NOT NULL default '0'",
			'relation'   => array('type' => 'belongsTo', 'load' => 'eager')
		),
		'tstamp'       => array
		(
			'sql' => "int(10) unsigned NOT NULL default '0'"
		),
		'rank'         => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_team_player']['rank'],
			'exclude'   => true,
			'inputType' => 'text',
			'sql'       => "int(10) unsigned NOT NULL default '0'",
			'eval'      => array('tl_class' => 'w50', 'readonly' => true)
		),
		'name'         => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_team_player']['name'],
			'exclude'   => true,
			'inputType' => 'text',
			'sql'       => "varchar(255) NOT NULL default ''",
			'eval'      => array('tl_class' => 'w50', 'readonly' => true)
		),
		'memberNumber' => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_team_player']['memberNumber'],
			'exclude'   => true,
			'inputType' => 'text',
			'sql'       => "int(10) unsigned NOT NULL default '0'",
			'eval'      => array('tl_class' => 'w50', 'readonly' => true)
		),
		'dwz'          => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_team_player']['dwz'],
			'exclude'   => true,
			'inputType' => 'text',
			'sql'       => "varchar(10) NOT NULL default ''",
			'eval'      => array('tl_class' => 'w50', 'readonly' => true)
		),
		'elo'          => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_team_player']['elo'],
			'exclude'   => true,
			'inputType' => 'text',
			'sql'       => "varchar(10) NOT NULL default ''",
			'eval'      => array('tl_class' => 'w50', 'readonly' => true)
		),
		'partOfTeam'   => array(
			'sql' => "char(1) NOT NULL default ''"
		)
	)
);

/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @author Julian Knorr
 */
class tl_portal64_team_player extends Backend
{
	
	/**
	 * Returns a view (row) of a player
	 *
	 * @param array $arrRow The row from database
	 *
	 * @return string The Html code
	 */
	public function displayPlayer($arrRow)
	{
		return '<div><span style="display: inline-block; width: 40px;">'.$arrRow['rank'].'</span> <span style="display: inline-block; width: 80px;">'.$arrRow['memberNumber'].'</span> <span style="display: inline-block; width: 300px;"><b>'.$arrRow['name'].'</b></span> <span style="display: inline-block; width: 50px;">'.$arrRow['dwz'].'</span> <span style="display: inline-block; width: 50px; margin-left: 50px;">'.$arrRow['elo'].'</span></div>';
	}
	
}

?>