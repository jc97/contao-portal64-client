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

array_insert($GLOBALS['TL_DCA']['tl_settings'], 0, array
(
	'fields' => array
	(
		'portal64Link' => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_settings']['portal64Link'],
			'inputType' => 'text',
			'eval'      => array('rgxp' => 'url', 'nospace' => true, 'trailingSlash' => true)
		)
	)
));
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] = str_replace('{update_legend:hide}', '{portal64_legend},portal64Link;{update_legend:hide}', $GLOBALS['TL_DCA']['tl_settings']['palettes']['default']);

?>
