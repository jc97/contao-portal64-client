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

$GLOBALS['TL_LANG']['tl_portal64_team']['player'] = array("Players", "Display the players of the team");
$GLOBALS['TL_LANG']['tl_portal64_team']['rounds'] = array("Rounds", "Display the rounds of the team");
$GLOBALS['TL_LANG']['tl_portal64_team']['editMetadata'] = array("edit", "Edit team");
$GLOBALS['TL_LANG']['tl_portal64_team']['copy'] = array("copy", "copy team");
$GLOBALS['TL_LANG']['tl_portal64_team']['delete'] = array("delete", "delete team (events wouldn't be deleted)");
$GLOBALS['TL_LANG']['tl_portal64_team']['new'] = array("New team", "Create a new team");

$GLOBALS['TL_LANG']['tl_portal64_team']['title_legend'] = "Name and league";
$GLOBALS['TL_LANG']['tl_portal64_team']['portal64_legend'] = "Portal64.de";
$GLOBALS['TL_LANG']['tl_portal64_team']['calendar_legend'] = "Calendar";
$GLOBALS['TL_LANG']['tl_portal64_team']['meta_legend'] = "Meta data";

$GLOBALS['TL_LANG']['tl_portal64_team']['officialName'] = array("Official name", "Official name of team, loaded from league management system");
$GLOBALS['TL_LANG']['tl_portal64_team']['internalName'] = array("Internal name", "Internal name of team");
$GLOBALS['TL_LANG']['tl_portal64_team']['league'] = array("League", "Loaded from league management system");
$GLOBALS['TL_LANG']['tl_portal64_team']['tid'] = array("Tournament id (tid)", "Can be determined from URLs to the league management system: (https://domain.of.portal/ergebnisse/show/2013/123/ => tournament id 123)");
$GLOBALS['TL_LANG']['tl_portal64_team']['lotNumber'] = array("Lot number", "The lot number of the team can be determined from the overview of all teams in the league management system");
$GLOBALS['TL_LANG']['tl_portal64_team']['lastUpdate'] = array("Last update", "The time of the last automatically update");
$GLOBALS['TL_LANG']['tl_portal64_team']['loadPlayersOfOpponentTeams'] = array("Save opponent players", "Indicates whether the data of players of opponent team should be saved in the database to make them useble for other extensions");
$GLOBALS['TL_LANG']['tl_portal64_team']['vkz'] = array("VKZ (Vereinskennziffer)", "Will be automatically determined");
$GLOBALS['TL_LANG']['tl_portal64_team']['teamster'] = array("Teamster", "Will be automatically determined");
$GLOBALS['TL_LANG']['tl_portal64_team']['disableUpdates'] = array("Disable updates", "Indicates whether updates are disabled for this team");
$GLOBALS['TL_LANG']['tl_portal64_team']['substituteTeams'] = array("Teams with stand-by players", "The set of teams from which stand-by players can be used");
$GLOBALS['TL_LANG']['tl_portal64_team']['manageCalendar'] = array("Manage a calendar", "Manage the events of the team automatically and update an internal calendar");
$GLOBALS['TL_LANG']['tl_portal64_team']['calendar'] = array("Calendar", "The calendar to manage and store the events");
$GLOBALS['TL_LANG']['tl_portal64_team']['eventTitle'] = array("Title of events", "The title of the events to create, therefor wildcards can be used (including _R_ = Round, _H_ = Home team, _G_ = Guest team, ... see documentation)");
$GLOBALS['TL_LANG']['tl_portal64_team']['eventAuthor'] = array("Author", "This user will be set as author of the created events");
$GLOBALS['TL_LANG']['tl_portal64_team']['eventDuration'] = array("Duration", "The duration of the events to create (in hours)");
$GLOBALS['TL_LANG']['tl_portal64_team']['homeLocation'] = array("Location of home matches", "The location determined from the league management system will be used if this option is empty");

$GLOBALS['TL_LANG']['tl_portal64_team']['mail'] = array("E-Mail", "Send an e-mail to all players of the team");

$GLOBALS['TL_LANG']['tl_portal64_team']['noemails'] = "This team has not any players with an e-mail";

?>