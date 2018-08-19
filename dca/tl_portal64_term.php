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
 * Table tl_portal64_term
 */
$GLOBALS['TL_DCA']['tl_portal64_term'] = array
(
	// Config
	'config'   => array(
		'dataContainer'    => 'Table',
		'switchToEdit'     => true,
		'label'            => $GLOBALS['TL_LANG']['MOD']['portal64'][0],
		'enableVersioning' => false,
		'notSortable'      => true,
		'notCopyable'      => true,
		'ctable'           => array('tl_portal64_team'),
		'sql'              => array(
			'keys' => array
			(
				'id'   => 'primary',
				'year' => 'unique'
			)
		)
	),
	
	// List
	'list'     => array(
		'sorting'           => array(
			'mode'   => 1,
			'flag'   => 2,
			'fields' => array('year')
		),
		'label'             => array
		(
			'fields'      => array('year'),
			'showColumns' => true,
		),
		'operations'        => array
		(
			'updateTerm' => array
			(
				'label'           => &$GLOBALS['TL_LANG']['tl_portal64_term']['updateTerm'],
				'href'            => 'key=portal64update&amp;term=%term',
				'icon'            => 'system/themes/default/images/reload.gif',
				'button_callback' => array('tl_portal64_term', 'updateTerm')
			),
			'delete'     => array
			(
				'label'      => &$GLOBALS['TL_LANG']['tl_portal64_term']['delete'],
				'href'       => 'act=delete',
				'icon'       => 'delete.gif',
				'attributes' => 'onclick="if(!confirm(\''.$GLOBALS['TL_LANG']['MSC']['deleteConfirm'].'\'))return false;Backend.getScrollOffset()"'
			),
			'edit'       => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_portal64_term']['edit'],
				'href'  => 'table=tl_portal64_team',
				'icon'  => 'edit.gif'
			)
		),
		'global_operations' => array
		(
			'update' => array
			(
				'label'           => &$GLOBALS['TL_LANG']['tl_portal64_term']['update'],
				'href'            => 'key=portal64update',
				'icon'            => 'system/themes/default/images/reload.gif',
				'button_callback' => array('tl_portal64_term', 'update')
			)
		)
	),
	
	// Palettes
	'palettes' => array
	(
		'default' => '{term_legend},year',
	),
	
	// Fields
	'fields'   => array(
		'id'     => array
		(
			'sql' => "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp' => array
		(
			'sql' => "int(10) unsigned NOT NULL default '0'"
		),
		'year'   => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_portal64_term']['year'],
			'exclude'   => true,
			'inputType' => 'text',
			'sql'       => "int(10) unsigned NOT NULL default '0'",
			'eval'      => array('mandatory' => true, 'maxlength' => 4, 'minlength' => 4, 'rgxp' => 'digit', 'doNotSaveEmpty' => true, 'unique' => true)
		)
	)
);

/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @author Julian Knorr
 */
class tl_portal64_term extends Backend
{
	
	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}
	
	/**
	 * Return the "update" link
	 *
	 * @param string $href
	 * @param string $label
	 * @param string $title
	 * @param string $class
	 * @param string $attributes
	 *
	 * @return string
	 */
	public function update($href, $label, $title, $class, $attributes)
	{
		return $this->User->isAdmin ? '<a href="'.$this->addToUrl($href).'" class="'.$class.'" title="'.specialchars($title).'"'.$attributes.'>'.$label.'</a> ' : '';
	}
	
	/**
	 * Return the update term button
	 *
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 *
	 * @return string
	 */
	public function updateTerm($row, $href, $label, $title, $icon, $attributes, $table)
	{
		$href = str_replace('%term', $row['id'], $href);
		return $this->User->isAdmin ? '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.gif$/i', '_.gif', $icon)).' ';
	}
}

?>