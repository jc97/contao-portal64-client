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
 * Table tl_portal64_team
 */
$GLOBALS['TL_DCA']['tl_portal64_team'] = array
(
	// Config
	'config'      => array(
		'dataContainer'    => 'Table',
		'switchToEdit'     => true,
		'enableVersioning' => false,
		'doNotCopyRecords' => true,
		'ptable'           => 'tl_portal64_term',
		'ctable'           => array('tl_portal64_team_player', 'tl_portal64_team_round'),
		'sql'              => array(
			'keys' => array
			(
				'id'  => 'primary',
				'pid' => 'index'
			)
		)
	),
	
	// List
	'list'        => array(
		'sorting'           => array(
			'mode'                  => 4,
			'fields'                => array('internalName ASC'),
			'headerFields'          => array('year'),
			'child_record_callback' => array('tl_portal64_team', 'loadTeams'),
			'disableGrouping'       => true
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'       => 'act=select',
				'class'      => 'header_edit_all',
				'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			)
		),
		'operations'        => array(
			'mail'         => array
			(
				'label'           => &$GLOBALS['TL_LANG']['tl_portal64_team']['mail'],
				'icon'            => 'system/modules/newsletter/assets/icon.gif',
				'button_callback' => array('tl_portal64_team', 'mail')
			),
			'player'       => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_portal64_team']['player'],
				'href'  => 'table=tl_portal64_team_player',
				'icon'  => 'mgroup.gif'
			),
			'rounds'       => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_portal64_team']['rounds'],
				'href'  => 'table=tl_portal64_team_round',
				'icon'  => 'show.gif'
			),
			'editMetadata' => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_portal64_team']['editMetadata'],
				'href'  => 'act=edit',
				'icon'  => 'header.gif'
			),
			'copy'         => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_portal64_team']['copy'],
				'href'  => 'act=paste&amp;mode=copy',
				'icon'  => 'copy.gif'
			),
			'delete'       => array
			(
				'label'      => &$GLOBALS['TL_LANG']['tl_portal64_team']['delete'],
				'href'       => 'act=delete',
				'icon'       => 'delete.gif',
				'attributes' => 'onclick="if(!confirm(\''.$GLOBALS['TL_LANG']['MSC']['deleteConfirm'].'\'))return false;Backend.getScrollOffset()"'
			),
		)
	),
	
	// Palettes
	'palettes'    => array(
		'__selector__' => array('manageCalendar'),
		'default'      => '{title_legend},internalName,officialName,league;{portal64_legend},tid,lotNumber,substituteTeams,disableUpdates,lastUpdate,loadPlayersOfOpponentTeams;{meta_legend},teamster,vkz;{calendar_legend},manageCalendar'
	),
	
	// Subpalletes
	'subpalettes' => array(
		'manageCalendar' => 'calendar,eventTitle,eventAuthor,eventDuration,homeLocation'
	),
	
	// Fields
	'fields'      => array
	(
		'id'                         => array
		(
			'sql' => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid'                        => array
		(
			'foreignKey' => 'tl_portal64_term.year',
			'sql'        => "int(10) unsigned NOT NULL default '0'",
			'relation'   => array('type' => 'belongsTo', 'load' => 'eager')
		),
		'tstamp'                     => array
		(
			'sql' => "int(10) unsigned NOT NULL default '0'"
		),
		'internalName'               => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_team']['internalName'],
			'exclude'   => true,
			'inputType' => 'text',
			'sorting'   => true,
			'flag'      => 1,
			'sql'       => "varchar(255) NOT NULL default ''",
			'eval'      => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50')
		),
		'officialName'               => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_team']['officialName'],
			'exclude'   => true,
			'inputType' => 'text',
			'sorting'   => true,
			'flag'      => 1,
			'sql'       => "varchar(255) NOT NULL default ''",
			'eval'      => array('readonly' => true, 'tl_class' => 'w50')
		),
		'league'                     => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_team']['league'],
			'exclude'   => true,
			'inputType' => 'text',
			'sorting'   => true,
			'flag'      => 1,
			'sql'       => "varchar(255) NOT NULL default ''",
			'eval'      => array('readonly' => true, 'tl_class' => 'w50')
		),
		'tid'                        => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_team']['tid'],
			'exclude'   => true,
			'inputType' => 'text',
			'sql'       => "int(10) unsigned NOT NULL default '0'",
			'eval'      => array('rgxp' => 'digit', 'mandatory' => true, 'minlength' => 1, 'maxlength' => 7, 'tl_class' => 'w50')
		),
		'lotNumber'                  => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_team']['lotNumber'],
			'exclude'   => true,
			'inputType' => 'text',
			'sql'       => "int(10) unsigned NOT NULL default '0'",
			'eval'      => array('rgxp' => 'digit', 'mandatory' => true, 'minlength' => 1, 'maxlength' => 3, 'tl_class' => 'w50')
		),
		'lastUpdate'                 => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_team']['lastUpdate'],
			'exclude'   => true,
			'inputType' => 'text',
			'sorting'   => true,
			'flag'      => 1,
			'eval'      => array('rgxp' => 'datim', 'tl_class' => 'w50', 'readonly' => true),
			'sql'       => "varchar(10) NOT NULL default ''"
		),
		'loadPlayersOfOpponentTeams' => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_team']['loadPlayersOfOpponentTeams'],
			'exclude'   => true,
			'inputType' => 'checkbox',
			'sql'       => "char(1) NOT NULL default ''",
			'eval'      => array('tl_class' => 'w50 clr')
		),
		'vkz'                        => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_team']['vkz'],
			'exclude'   => true,
			'inputType' => 'text',
			'sql'       => "varchar(255) NOT NULL default ''",
			'eval'      => array('readonly' => true, 'tl_class' => 'w50')
		),
		'teamster'                   => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_team']['teamster'],
			'exclude'   => true,
			'inputType' => 'text',
			'sorting'   => true,
			'flag'      => 1,
			'sql'       => "varchar(255) NOT NULL default ''",
			'eval'      => array('readonly' => true, 'tl_class' => 'w50')
		),
		'disableUpdates'             => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_team']['disableUpdates'],
			'exclude'   => true,
			'inputType' => 'checkbox',
			'sql'       => "char(1) NOT NULL default ''",
			'eval'      => array('tl_class' => 'w50 clr')
		),
		'substituteTeams'            => array(
			'label'            => &$GLOBALS['TL_LANG']['tl_portal64_team']['substituteTeams'],
			'exclude'          => true,
			'inputType'        => 'checkboxWizard',
			'sql'              => "blob NULL",
			'eval'             => array('multiple' => true, 'tl_class' => 'w50 clr'),
			'options_callback' => array('tl_portal64_team', 'loadSubstituteTeams')
		),
		'manageCalendar'             => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_team']['manageCalendar'],
			'exclude'   => true,
			'inputType' => 'checkbox',
			'sql'       => "char(1) NOT NULL default ''",
			'eval'      => array('submitOnChange' => true)
		),
		'calendar'                   => array(
			'label'      => &$GLOBALS['TL_LANG']['tl_portal64_team']['calendar'],
			'exclude'    => true,
			'inputType'  => 'select',
			'sql'        => "int(10) unsigned NOT NULL default '0'",
			'foreignKey' => 'tl_calendar.title',
			'relation'   => array('type' => 'hasOne', 'load' => 'eager'),
			'eval'       => array('doNotCopy' => true, 'chosen' => true, 'mandatory' => true, 'tl_class' => 'w50'),
		),
		'eventTitle'                 => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_team']['eventTitle'],
			'exclude'   => true,
			'inputType' => 'text',
			'sql'       => "varchar(255) NOT NULL default ''",
			'eval'      => array('tl_class' => 'w50')
		),
		'eventAuthor'                => array(
			'label'      => &$GLOBALS['TL_LANG']['tl_portal64_team']['eventAuthor'],
			'default'    => BackendUser::getInstance()->id,
			'exclude'    => true,
			'inputType'  => 'select',
			'foreignKey' => 'tl_user.name',
			'eval'       => array('doNotCopy' => true, 'chosen' => true, 'mandatory' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'),
			'sql'        => "int(10) unsigned NOT NULL default '0'",
			'relation'   => array('type' => 'hasOne', 'load' => 'eager')
		),
		'eventDuration'              => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_team']['eventDuration'],
			'exclude'   => true,
			'default'   => '5',
			'inputType' => 'text',
			'sql'       => "varchar(12) NOT NULL default ''",
			'eval'      => array('rgxp' => 'digit', 'mandatory' => true, 'tl_class' => 'w50')
		),
		'homeLocation'               => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_team']['homeLocation'],
			'exclude'   => true,
			'inputType' => 'text',
			'eval'      => array('maxlength' => 255, 'tl_class' => 'long clr'),
			'sql'       => "varchar(255) NOT NULL default ''"
		)
	)
);

/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @author Julian Knorr
 */
class tl_portal64_team extends Backend
{
	
	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
		$this->import("Database");
	}
	
	/**
	 * Returns a view (row) of a team
	 *
	 * @param array $arrRow The row from database
	 *
	 * @return string The Html code
	 */
	public function loadTeams($arrRow)
	{
		$return = '<div><b>'.$arrRow['internalName'].'</b>';
		if (strlen($arrRow["officialName"]) > 0) $return .= ' <span style="padding-left:3px;color:#b3b3b3;">['.$arrRow['officialName'].']</span>';
		if (strlen($arrRow["league"]) > 0) $return .= '<br />'.$arrRow["league"];
		if (strlen($arrRow["teamster"]) > 0) $return .= '<br /><span style="padding-left:3px;color:#b3b3b3;">'.$GLOBALS['TL_LANG']['tl_portal64_team']['teamster'][0].': '.$arrRow["teamster"].'</span>';
		$return .= '</div>';
		return $return;
	}
	
	/**
	 * Returns an array of available substitute teams for a team
	 *
	 * @param DataContainer $dc
	 *
	 * @return array
	 */
	public function loadSubstituteTeams($dc)
	{
		$teams = array();
		$collectionTeams = \Portal64\Portal64TeamModel::findByPid($dc->activeRecord->pid);
		while ($collectionTeams->next()) {
			$team = $collectionTeams->row();
			if ($team["id"] <> $dc->activeRecord->id) $teams[$team["id"]] = $team["internalName"]." (".$team["officialName"].")";
		}
		return $teams;
	}
	
	/**
	 * Return a mailto-link to the team
	 *
	 * @param string $href
	 * @param string $label
	 * @param string $title
	 * @param string $class
	 * @param string $attributes
	 *
	 * @return string
	 */
	public function mail($row, $href, $label, $title, $icon, $attributes)
	{
		return ($this->User->isAdmin || $this->User->hasAccess('portal64', 'modules')) ? '<a href="'.$this->getMailTo($row['id'], $row).'" class="'.$class.'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : '';
	}
	
	/**
	 * Return href for a mailto-link to the team
	 *
	 * @param id    $team
	 * @param array $arrTeam
	 *
	 * @return string
	 */
	public function getMailTo($team, $arrTeam)
	{
		$players = \Portal64\Portal64TeamPlayerModel::findByTeam($team);
		
		$mails = array();
		while ($players !== null && $players->next()) {
			$member = $this->Database->prepare("SELECT * FROM tl_member WHERE CONCAT(lastname, ', ', firstname) = ? AND email <> ''")->execute($players->name);
			if (strlen($member->email) > 0) $mails[] = $member->email;
		}
		if (count($mails) > 0) {
			return "mailto:".implode(";", $mails)."?subject=".rawurlencode("[".$arrTeam['internalName']."]: ");
		} else {
			return "javascript:alert('".$GLOBALS['TL_LANG']['tl_portal64_team']['noemails']."');return false;";
		}
	}
	
}

?>