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
 * Table tl_calendar_events
 */
array_insert($GLOBALS['TL_DCA']['tl_calendar_events']['fields'], 0, array
(
	'createdByPortal64'        => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events']['createdByPortal64'],
		'exclude'   => true,
		'inputType' => 'checkbox',
		'search'    => true,
		'sql'       => "char(1) NOT NULL default ''",
		'eval'      => array('readonly' => true, 'disabled' => true, "tl_class" => "w50 clr", "doNotCopy" => true)
	),
	'disableUpdatesByPortal64' => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events']['disableUpdatesByPortal64'],
		'exclude'   => true,
		'inputType' => 'checkbox',
		'sql'       => "char(1) NOT NULL default ''",
		'eval'      => array("tl_class" => "w50")
	),
));

$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['default'] .= ';{portal64_legend:hide},createdByPortal64,disableUpdatesByPortal64';

?>