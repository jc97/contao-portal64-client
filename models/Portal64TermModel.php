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

namespace Portal64;

/*
 * Reads and writes terms (Portal64)
 *
 * @property integer id
 * @property integer tstamp
 * @property integer year
 *
 * @method static \Portal64TermModel|null findById($id, $opt=array())
 * @method static \Portal64TermModel|null findByPk($id, $opt=array())
 * @method static \Portal64TermModel|null findOneBy($col, $val, $opt=array())
 * @method static \Portal64TermModel|null findOneByTstamp($val, $opt=array())
 * @method static \Portal64TermModel|null findOneByYear($val, $opt=array())
 *
 * @method static \Model\Collection|\Portal64TermModel[]|\Portal64TermModel|null findByTstamp($val, $opt=array())
 * @method static \Model\Collection|\Portal64TermModel[]|\Portal64TermModel|null findByYear($val, $opt=array())
 * @method static \Model\Collection|\Portal64TermModel[]|\Portal64TermModel|null findMultipleByIds($val, $opt=array())
 * @method static \Model\Collection|\Portal64TermModel[]|\Portal64TermModel|null findBy($col, $val, $opt=array())
 * @method static \Model\Collection|\Portal64TermModel[]|\Portal64TermModel|null findAll($opt=array())
 *
 * @method static integer countById($id, $opt=array())
 * @method static integer countByYear($id, $opt=array())
 *
 * @author Julian Knorr
 */
class Portal64TermModel extends \Model
{
	/**
	 * Name of the table
	 *
	 * @var string
	 */
	protected static $strTable = 'tl_portal64_term';
	
	/**
	 * Find the current term.
	 *
	 * @return TermModel|null The model or null if there is no term
	 */
	public static function findLatest()
	{
		$t = static::$strTable;
		
		$arrOptions = array
		(
			'order'  => "$t.year DESC",
			'limit'  => '1',
			'return' => 'Model',
			''
		);
		
		return static::find($arrOptions);
	}
}

?>