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

/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'Portal64'
));

/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	//Classes
	'Portal64\Portal64'                    => 'system/modules/portal64.de_client/classes/Portal64.php',
	'Portal64\Portal64Automator'           => 'system/modules/portal64.de_client/classes/Portal64Automator.php',
	'Portal64\Portal64Manager'             => 'system/modules/portal64.de_client/classes/Portal64Manager.php',
	//Models
	'Portal64\ContentModel'                => 'system/modules/portal64.de_client/models/ContentModel.php',
	'Portal64\MemberModel'                 => 'system/modules/portal64.de_client/models/MemberModel.php',
	'Portal64\Portal64TeamModel'           => 'system/modules/portal64.de_client/models/Portal64TeamModel.php',
	'Portal64\Portal64TeamPlayerModel'     => 'system/modules/portal64.de_client/models/Portal64TeamPlayerModel.php',
	'Portal64\Portal64TeamRoundMatchModel' => 'system/modules/portal64.de_client/models/Portal64TeamRoundMatchModel.php',
	'Portal64\Portal64TeamRoundModel'      => 'system/modules/portal64.de_client/models/Portal64TeamRoundModel.php',
	'Portal64\Portal64TermModel'           => 'system/modules/portal64.de_client/models/Portal64TermModel.php',
	//Modules
	'Portal64\ContentPortal64'             => 'system/modules/portal64.de_client/elements/ContentPortal64.php',
	'Portal64\ContentTeam'                 => 'system/modules/portal64.de_client/elements/ContentTeam.php',
	'Portal64\ContentTeamArchive'          => 'system/modules/portal64.de_client/elements/ContentTeamArchive.php',
	'Portal64\ContentTeamHeadline'         => 'system/modules/portal64.de_client/elements/ContentTeamHeadline.php',
	'Portal64\ContentTeamResults'          => 'system/modules/portal64.de_client/elements/ContentTeamResults.php',
));

/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'ce_team'         => 'system/modules/portal64.de_client/templates/elements',
	'ce_team_archive' => 'system/modules/portal64.de_client/templates/elements',
	'ce_team_results' => 'system/modules/portal64.de_client/templates/elements',
));

?>