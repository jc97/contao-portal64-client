<?php
/**
 * Portal64.de Client
 * Extension for Contao Open Source CMS, Copyright (C) Leo Feyer
 *
 * Copyright (C) 2017 Julian Knorr
 *
 * @package            Portal64.de Client
 * @author             Julian Knorr
 * @copytight          Copyright (C) 2017 Julian Knorr
 * @date               2017
 */

/*
 * Content Elements
 */
$GLOBALS['TL_CTE']['includes']['team'] = 'ContentTeam';
$GLOBALS['TL_CTE']['includes']['teamarchive'] = 'ContentTeamArchive';
$GLOBALS['TL_CTE']['includes']['teamresults'] = 'ContentTeamResults';
$GLOBALS['TL_CTE']['texts']['teamheadline'] = 'ContentTeamHeadline';

/*
 * Back end modules
 */
$GLOBALS['BE_MOD']['content']['portal64'] = array(
	'tables'         => array('tl_portal64_term', 'tl_portal64_team', 'tl_portal64_team_player', 'tl_portal64_team_round', 'tl_portal64_team_round_match'),
	'portal64update' => array('Portal64\Portal64Manager', 'updateAllTeamsFromBackend'),
	'icon'           => 'system/themes/default/images/mgroup.gif',
);

/*
 * Cron
 */
array_insert($GLOBALS['TL_CRON'], 0, array(
	'hourly' => array
	(
		array('Portal64Automator', 'updateFromPortal64')
	)
));

?>