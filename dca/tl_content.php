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

/*
 * Table tl_content
 */
array_insert($GLOBALS['TL_DCA']['tl_content']['fields'], 0, array
(
	'autoHeadline'                  => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_content']['autoHeadline'],
		'exclude'   => true,
		'inputType' => 'checkbox',
		'sql'       => "char(1) NOT NULL default ''",
		'eval'      => array("tl_class" => "w50")
	),
	'team'                          => array
	(
		'label'            => &$GLOBALS['TL_LANG']['tl_content']['team'],
		'exclude'          => true,
		'inputType'        => 'select',
		'sql'              => "int(10) unsigned NOT NULL default '0'",
		'options_callback' => array("tl_content_portal64", "getTeams"),
		'eval'             => array("tl_class" => "clr w50 wizard", 'mandatory' => true, 'chosen' => true)
	),
	'updateTeam'                    => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_content']['updateTeam'],
		'exclude'   => true,
		'inputType' => 'checkbox',
		'sql'       => "char(1) NOT NULL default ''",
		'eval'      => array("tl_class" => "w50 clr")
	),
	'showSingleRound'               => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_content']['showSingleRound'],
		'exclude'   => true,
		'inputType' => 'checkbox',
		'sql'       => "char(1) NOT NULL default ''",
		'eval'      => array("tl_class" => "w50 clr", 'submitOnChange' => true)
	),
	'round'                         => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_content']['round'],
		'exclude'   => true,
		'inputType' => 'text',
		'sql'       => "int(10) unsigned NOT NULL default '1'",
		'default'   => 1,
		'eval'      => array("tl_class" => "w50", 'rgxp' => 'natural', 'minval' => 1, 'mandatory' => false)
	),
	'displayFIDEData'               => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_content']['displayFIDEData'],
		'exclude'   => true,
		'inputType' => 'checkbox',
		'sql'       => "char(1) NOT NULL default ''",
		'eval'      => array("tl_class" => "w50 clr")
	),
	'showGameResults'               => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_content']['showGameResults'],
		'exclude'   => true,
		'inputType' => 'checkbox',
		'sql'       => "char(1) NOT NULL default ''",
		'eval'      => array("tl_class" => "w50 clr")
	),
	'linkToPortal'                  => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_content']['linkToPortal'],
		'exclude'   => true,
		'inputType' => 'checkbox',
		'sql'       => "char(1) NOT NULL default ''",
		'eval'      => array("tl_class" => "w50 clr")
	),
	'showTeamster'                  => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_content']['showTeamster'],
		'exclude'   => true,
		'inputType' => 'checkbox',
		'sql'       => "char(1) NOT NULL default ''",
		'eval'      => array("tl_class" => "w50 clr", 'submitOnChange' => true)
	),
	'showTeamsterMail'              => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_content']['showTeamsterMail'],
		'exclude'   => true,
		'inputType' => 'checkbox',
		'sql'       => "char(1) NOT NULL default '0'",
		'eval'      => array("tl_class" => "w50")
	),
	'startTerm'                     => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_content']['startTerm'],
		'exclude'   => true,
		'inputType' => 'text',
		'sql'       => "int(10) unsigned NOT NULL default '0'",
		'default'   => 0,
		'eval'      => array("tl_class" => "w50 clr", 'rgxp' => 'natural', 'maxval' => 2025, 'mandatory' => false)
	),
	'endTerm'                       => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_content']['endTerm'],
		'exclude'   => true,
		'inputType' => 'text',
		'sql'       => "int(10) unsigned NOT NULL default '0'",
		'default'   => 0,
		'eval'      => array("tl_class" => "w50", 'rgxp' => 'natural', 'maxval' => 2025, 'mandatory' => false)
	),
	'excludeLatestTerm'             => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_content']['excludeLatestTerm'],
		'exclude'   => true,
		'inputType' => 'checkbox',
		'sql'       => "char(1) NOT NULL default '1'",
		'default'   => 1,
		'eval'      => array("tl_class" => "w50")
	),
	'showContentTeam'               => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_content']['showContentTeam'],
		'exclude'   => true,
		'inputType' => 'checkbox',
		'sql'       => "char(1) NOT NULL default '1'",
		'default'   => 1,
		'eval'      => array("tl_class" => "clr", 'submitOnChange' => true)
	),
	'ContentTeamHeadline'           => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_content']['ContentTeamHeadline'],
		'exclude'   => true,
		'search'    => true,
		'inputType' => 'inputUnit',
		'options'   => array('h1', 'h2', 'h3', 'h4', 'h5', 'h6'),
		'eval'      => array('tl_class' => 'w50 clr', 'maxlength' => 200),
		'sql'       => "varchar(255) NOT NULL default ''"
	),
	'teamTemplate'                  => array
	(
		'label'            => &$GLOBALS['TL_LANG']['tl_content']['teamTemplate'],
		'exclude'          => true,
		'inputType'        => 'select',
		'options_callback' => array('tl_content', 'getElementTemplates'),
		'eval'             => array('includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'clr w50'),
		'sql'              => "varchar(64) NOT NULL default ''"
	),
	'showContentTeamresults'        => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_content']['showContentTeamresults'],
		'exclude'   => true,
		'inputType' => 'checkbox',
		'sql'       => "char(1) NOT NULL default '1'",
		'default'   => 1,
		'eval'      => array("tl_class" => "clr w50", 'submitOnChange' => true)
	),
	'ContentTeamresultsHeadline'    => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_content']['ContentTeamresultsHeadline'],
		'exclude'   => true,
		'search'    => true,
		'inputType' => 'inputUnit',
		'options'   => array('h1', 'h2', 'h3', 'h4', 'h5', 'h6'),
		'eval'      => array('tl_class' => 'w50 clr', 'maxlength' => 200),
		'sql'       => "varchar(255) NOT NULL default ''"
	),
	'teamresultsTemplate'           => array
	(
		'label'            => &$GLOBALS['TL_LANG']['tl_content']['teamresultsTemplate'],
		'exclude'          => true,
		'inputType'        => 'select',
		'options_callback' => array('tl_content', 'getElementTemplates'),
		'eval'             => array('includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'clr w50'),
		'sql'              => "varchar(64) NOT NULL default ''"
	),
	'showRoundGameResultsInArchive' => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_content']['showGameResults'],
		'exclude'   => true,
		'inputType' => 'checkbox',
		'sql'       => "char(1) NOT NULL default ''",
		'eval'      => array("tl_class" => "w50 clr")
	),
));

$GLOBALS['TL_DCA']['tl_content']['palettes']['team'] = '{type_legend},type;{config_legend},headline,autoHeadline,team,updateTeam,displayFIDEData,showGameResults;{template_legend:hide},customTpl,linkToPortal,showTeamster;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';
$GLOBALS['TL_DCA']['tl_content']['palettes']['teamresults'] = '{type_legend},type;{config_legend},headline,autoHeadline,team,updateTeam,showSingleRound,showGameResults;{template_legend:hide},customTpl,linkToPortal,showTeamster;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';
$GLOBALS['TL_DCA']['tl_content']['palettes']['teamheadline'] = '{type_legend},type;{config_legend},headline,team,updateTeam;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';
$GLOBALS['TL_DCA']['tl_content']['palettes']['teamarchive'] = '{type_legend},type;{config_legend},headline,autoHeadline,team,updateTeam,startTerm,endTerm,excludeLatestTerm;{ceteam_legend},showContentTeam;{ceteamresults_legend},showContentTeamresults;{template_legend:hide},customTpl,linkToPortal,showTeamster;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';
$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'showTeamster';
$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'showSingleRound';
$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'showContentTeam';
$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'showContentTeamresults';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['showTeamster'] = 'showTeamsterMail';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['showSingleRound'] = 'round';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['showContentTeam'] = 'ContentTeamHeadline,showGameResults,displayFIDEData,teamTemplate';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['showContentTeamresults'] = 'ContentTeamresultsHeadline,showRoundGameResultsInArchive,teamresultsTemplate';

/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @author Julian Knorr
 */
class tl_content_portal64 extends tl_content
{
	
	/**
	 * Load the database object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import("Database");
	}
	
	/**
	 * Load the teams
	 *
	 * @return array The teams
	 */
	public function getTeams()
	{
		$teams = [];
		$collectionTerms = \Portal64\Portal64TermModel::findAll(['order' => 'year DESC']);
		while ($collectionTerms !== null && $collectionTerms->next()) {
			$collectionTeams = \Portal64\Portal64TeamModel::findByPid($collectionTerms->id, ['order' => 'internalName ASC']);
			while ($collectionTeams !== null && $collectionTeams->next()) {
				$teams[$collectionTeams->id] = $collectionTerms->year.": ".$collectionTeams->internalName;
			}
		}
		return $teams;
	}
}

?>